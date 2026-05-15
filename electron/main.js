const { app, BrowserWindow, Menu, Tray, ipcMain, nativeImage, shell, dialog } = require('electron');
const path = require('path');
const fs = require('fs');
const os = require('os');
const net = require('net');
const http = require('http');
const { spawn } = require('child_process');

let mainWindow = null;
let tray = null;
let isQuitting = false;
let phpServer = null;
let queueWorker = null;
let resolvedUrl = null;

const XAMPP_URL = 'http://localhost/pos_opencodee/public';

// ---- File logging (GUI Electron has no console) ----
// os.tmpdir() needs no app readiness and always exists — safe at module load.
const LOG_FILE = path.join(os.tmpdir(), 'pos-desktop.log');
function flog(...args) {
    const line = `[${new Date().toISOString()}] ` + args.map(a =>
        typeof a === 'object' ? JSON.stringify(a) : String(a)).join(' ');
    try { fs.appendFileSync(LOG_FILE, line + '\n'); } catch (e) { /* ignore */ }
    console.log(line);
}
process.on('uncaughtException', err => {
    flog('UNCAUGHT EXCEPTION:', err && err.stack ? err.stack : err);
});
process.on('unhandledRejection', reason => {
    flog('UNHANDLED REJECTION:', reason && reason.stack ? reason.stack : reason);
});
try {
    fs.writeFileSync(LOG_FILE, ''); // truncate per launch
    flog('=== POS Desktop starting ===', 'isPackaged=' + app.isPackaged, 'appPath=' + app.getAppPath(), 'argv=' + JSON.stringify(process.argv));
} catch (e) { /* ignore */ }

// Build uses "asar": false, so app.getAppPath() returns the real Laravel root
// (resources/app in packaged builds, project dir in dev) — PHP can read every file.
function projectRoot() {
    return app.getAppPath();
}

function bundledPhpPath() {
    return path.join(projectRoot(), 'electron', 'php', 'php.exe');
}

// The install dir is read-only (or wiped on update), so the DB and the writable
// storage tree live in %APPDATA%\<app>. Seeds them from the bundle on first run.
// Returns the per-user { dbPath, storagePath }. No-op-ish in dev (just ensures dirs).
function prepareUserData() {
    const root    = projectRoot();
    const userDir = app.getPath('userData');               // %APPDATA%\POS Desktop
    const dbPath  = path.join(userDir, 'database.sqlite');
    const storage = path.join(userDir, 'storage');

    try {
        // First run: copy the seeded DB out of the bundle so the user keeps data
        // across reinstalls / auto-updates.
        if (!fs.existsSync(dbPath)) {
            const seed = path.join(root, 'database', 'database.sqlite');
            if (fs.existsSync(seed)) {
                fs.copyFileSync(seed, dbPath);
                flog('[userdata] seeded DB →', dbPath);
            } else {
                fs.writeFileSync(dbPath, '');
                flog('[userdata] created empty DB →', dbPath);
            }
        }
    } catch (e) {
        flog('[userdata] DB prepare FAILED:', e.message);
    }

    // Laravel's writable storage skeleton (contents excluded from build).
    for (const d of [
        'framework/views',
        'framework/cache/data',
        'framework/sessions',
        'logs',
        'app/public',
    ]) {
        const full = path.join(storage, ...d.split('/'));
        try {
            if (!fs.existsSync(full)) fs.mkdirSync(full, { recursive: true });
        } catch (e) {
            flog('[userdata] mkdir FAILED', full, '-', e.message);
        }
    }
    flog('[userdata] dbPath=' + dbPath, 'storagePath=' + storage);
    return { dbPath, storagePath: storage };
}

// public/storage symlink can't be shipped (Windows blocks symlink copy) and must
// point at the relocated AppData storage. Recreate as a directory JUNCTION
// (junctions need no admin rights). Install dir is per-user (NSIS perMachine:false).
function ensureStorageLink(storagePath) {
    const linkPath = path.join(projectRoot(), 'public', 'storage');
    const target   = path.join(storagePath, 'app', 'public');
    try {
        if (!fs.existsSync(target)) fs.mkdirSync(target, { recursive: true });
        if (fs.existsSync(linkPath)) return; // already linked
        fs.symlinkSync(target, linkPath, 'junction');
        flog('[storage] junction created:', linkPath, '->', target);
    } catch (e) {
        flog('[storage] could not create junction:', e.message);
    }
}

// Auto-update — only meaningful in a packaged build that has a publish feed
// (GitHub releases via electron-builder). Fully non-fatal: a missing/!configured
// update server must never block app launch.
function maybeCheckForUpdates() {
    if (!app.isPackaged) return;
    try {
        const { autoUpdater } = require('electron-updater');
        autoUpdater.autoDownload = true;
        autoUpdater.logger = { info: m => flog('[update]', m), warn: m => flog('[update]', m), error: m => flog('[update-err]', m), debug: () => {} };
        autoUpdater.on('error', e => flog('[update-err]', e && e.message ? e.message : e));
        autoUpdater.on('update-downloaded', () => flog('[update] downloaded — will install on quit'));
        autoUpdater.checkForUpdatesAndNotify().catch(e => flog('[update] check skipped:', e && e.message ? e.message : e));
    } catch (e) {
        flog('[update] electron-updater unavailable:', e && e.message ? e.message : e);
    }
}

function findFreePort(start = 8123, end = 8200) {
    return new Promise((resolve, reject) => {
        const tryPort = (p) => {
            if (p > end) return reject(new Error('No free port in ' + start + '-' + end));
            const srv = net.createServer();
            srv.once('error', () => tryPort(p + 1));
            srv.once('listening', () => srv.close(() => resolve(p)));
            srv.listen(p, '127.0.0.1');
        };
        tryPort(start);
    });
}

function waitForHttp(url, timeoutMs = 15000) {
    const start = Date.now();
    return new Promise((resolve, reject) => {
        const ping = () => {
            const req = http.get(url, (res) => {
                res.resume();
                resolve();
            });
            req.on('error', () => {
                if (Date.now() - start > timeoutMs) return reject(new Error('PHP server never came up at ' + url));
                setTimeout(ping, 250);
            });
            req.setTimeout(1000, () => req.destroy());
        };
        ping();
    });
}

async function startPhpServer() {
    const phpExe = bundledPhpPath();
    flog('[PHP] looking for bundled PHP at', phpExe, 'exists=' + fs.existsSync(phpExe));
    if (!fs.existsSync(phpExe)) {
        flog('[PHP] No bundled PHP — falling back to XAMPP URL');
        return XAMPP_URL;
    }

    const { dbPath, storagePath } = prepareUserData();
    ensureStorageLink(storagePath);

    const root = projectRoot();
    const serverPhp = path.join(root, 'server.php');
    const publicDir = path.join(root, 'public');
    flog('[PHP] root=' + root, 'server.php exists=' + fs.existsSync(serverPhp), 'public exists=' + fs.existsSync(publicDir));

    const port = await findFreePort(8123, 8200);
    const url = `http://127.0.0.1:${port}`;
    const phpEnv = {
        ...process.env,
        APP_URL: url,                    // correct asset() URLs
        APP_DEBUG: 'false',
        DB_CONNECTION: 'sqlite',
        DB_DATABASE: dbPath,             // %APPDATA% — survives reinstall/update
        LARAVEL_STORAGE_PATH: storagePath, // bootstrap/app.php → useStoragePath()
        QUEUE_CONNECTION: 'database',
    };
    flog('[PHP] Spawning bundled PHP on', url, 'cwd=' + root, 'db=' + dbPath, 'dbExists=' + fs.existsSync(dbPath));

    phpServer = spawn(phpExe, ['-S', `127.0.0.1:${port}`, '-t', 'public', 'server.php'], {
        cwd: root,
        windowsHide: true,
        env: phpEnv,
    });
    phpServer.stdout.on('data', d => flog('[PHP]', d.toString().trimEnd()));
    phpServer.stderr.on('data', d => flog('[PHP-err]', d.toString().trimEnd()));
    phpServer.on('error', e => flog('[PHP] spawn error:', e.message));
    phpServer.on('exit', code => flog('[PHP] exited with code', code));

    try {
        await waitForHttp(url + '/');
        flog('[PHP] server is up at', url);
        startQueueWorker(phpExe, root, phpEnv);
        return url;
    } catch (err) {
        flog('[PHP] ERROR:', err.message);
        if (phpServer) { phpServer.kill(); phpServer = null; }
        return XAMPP_URL; // graceful fallback
    }
}

// Background queue worker: drains the `jobs` table (printing runs here so a
// slow/offline thermal printer never blocks the cashier's sale request).
function startQueueWorker(phpExe, root, phpEnv) {
    try {
        queueWorker = spawn(
            phpExe,
            ['artisan', 'queue:work', '--queue=default', '--sleep=2', '--tries=3', '--backoff=5', '--timeout=60'],
            {
                cwd: root,
                windowsHide: true,
                env: phpEnv, // same DB + storage path as the web process
            }
        );
        queueWorker.stdout.on('data', d => flog('[queue]', d.toString().trimEnd()));
        queueWorker.stderr.on('data', d => flog('[queue-err]', d.toString().trimEnd()));
        queueWorker.on('error', e => flog('[queue] spawn error:', e.message));
        queueWorker.on('exit', code => {
            flog('[queue] worker exited with code', code);
            queueWorker = null;
        });
        flog('[queue] worker started');
    } catch (e) {
        flog('[queue] failed to start worker:', e.message);
    }
}

function createWindow(loadUrl) {
    mainWindow = new BrowserWindow({
        width: 1280,
        height: 800,
        minWidth: 1024,
        minHeight: 700,
        webPreferences: {
            nodeIntegration: false,
            contextIsolation: true,
            preload: path.join(__dirname, 'preload.js')
        },
        icon: path.join(__dirname, '../public/favicon.ico'),
        show: false
    });

    mainWindow.loadURL(loadUrl);

    mainWindow.once('ready-to-show', () => {
        mainWindow.show();
        console.log('POS Desktop started successfully');
    });

    mainWindow.on('close', (event) => {
        if (!isQuitting) {
            event.preventDefault();
            mainWindow.hide();
            return false;
        }
    });

    mainWindow.on('closed', () => {
        mainWindow = null;
    });

    createMenu();
}

function createMenu() {
    const template = [
        {
            label: 'File',
            submenu: [
                {
                    label: 'Refresh',
                    accelerator: 'CmdOrCtrl+R',
                    click: () => {
                        if (mainWindow) mainWindow.reload();
                    }
                },
                { type: 'separator' },
                {
                    label: 'Open in Browser',
                    click: () => {
                        if (resolvedUrl) shell.openExternal(resolvedUrl);
                    }
                },
                { type: 'separator' },
                {
                    label: 'Exit',
                    accelerator: 'Alt+F4',
                    click: () => {
                        isQuitting = true;
                        app.quit();
                    }
                }
            ]
        },
        {
            label: 'Edit',
            submenu: [
                { role: 'undo' },
                { role: 'redo' },
                { type: 'separator' },
                { role: 'cut' },
                { role: 'copy' },
                { role: 'paste' },
                { role: 'selectAll' }
            ]
        },
        {
            label: 'View',
            submenu: [
                { role: 'reload' },
                { role: 'forceReload' },
                { role: 'toggleDevTools' },
                { type: 'separator' },
                { role: 'resetZoom' },
                { role: 'zoomIn' },
                { role: 'zoomOut' },
                { type: 'separator' },
                { role: 'togglefullscreen' }
            ]
        },
        {
            label: 'Window',
            submenu: [
                { role: 'minimize' },
                { role: 'zoom' },
                { type: 'separator' },
                {
                    label: 'Always on Top',
                    type: 'checkbox',
                    checked: false,
                    click: (menuItem) => {
                        if (mainWindow) {
                            mainWindow.setAlwaysOnTop(menuItem.checked);
                        }
                    }
                },
                { type: 'separator' },
                { role: 'close' }
            ]
        },
        {
            label: 'Help',
            submenu: [
                {
                    label: 'About POS Desktop',
                    click: () => {
                        const { dialog } = require('electron');
                        dialog.showMessageBox(mainWindow, {
                            type: 'info',
                            title: 'About POS Desktop',
                            message: 'POS Desktop',
                            detail: 'Version 1.0.0\nElectron Desktop App for Laravel POS System'
                        });
                    }
                },
                {
                    label: 'Open Laravel Server',
                    click: () => {
                        if (resolvedUrl) shell.openExternal(resolvedUrl);
                    }
                }
            ]
        }
    ];

    const menu = Menu.buildFromTemplate(template);
    Menu.setApplicationMenu(menu);
}

function createTray() {
    const iconPath = path.join(__dirname, '../public/favicon.ico');
    let trayIcon;
    
    try {
        trayIcon = nativeImage.createFromPath(iconPath);
        if (trayIcon.isEmpty()) {
            trayIcon = nativeImage.createEmpty();
        }
    } catch (e) {
        trayIcon = nativeImage.createEmpty();
    }

    tray = new Tray(trayIcon);

    const contextMenu = Menu.buildFromTemplate([
        {
            label: 'Open POS',
            click: () => {
                if (mainWindow) {
                    mainWindow.show();
                    mainWindow.focus();
                }
            }
        },
        {
            label: 'Refresh',
            click: () => {
                if (mainWindow) mainWindow.reload();
            }
        },
        { type: 'separator' },
        {
            label: 'Exit',
            click: () => {
                isQuitting = true;
                app.quit();
            }
        }
    ]);

    tray.setToolTip('POS Desktop');
    tray.setContextMenu(contextMenu);

    tray.on('double-click', () => {
        if (mainWindow) {
            mainWindow.show();
            mainWindow.focus();
        }
    });
}

app.whenReady().then(async () => {
    flog('app ready, log file at', LOG_FILE);
    try {
        resolvedUrl = await startPhpServer();
        flog('resolvedUrl =', resolvedUrl);
        createWindow(resolvedUrl);
        createTray();
        flog('window + tray created');
        maybeCheckForUpdates();
    } catch (e) {
        flog('FATAL in whenReady:', e && e.stack ? e.stack : e);
    }

    ipcMain.on('window-minimize', () => {
        if (mainWindow) mainWindow.minimize();
    });

    ipcMain.on('window-maximize', () => {
        if (mainWindow) {
            if (mainWindow.isMaximized()) {
                mainWindow.unmaximize();
            } else {
                mainWindow.maximize();
            }
        }
    });

    ipcMain.on('window-close', () => {
        if (mainWindow) mainWindow.hide();
    });

    ipcMain.handle('window-is-maximized', () => {
        return mainWindow ? mainWindow.isMaximized() : false;
    });

    app.on('activate', () => {
        if (BrowserWindow.getAllWindows().length === 0) {
            createWindow(resolvedUrl);
        }
    });
});

app.on('window-all-closed', () => {
    if (process.platform !== 'darwin') {
        app.quit();
    }
});

app.on('before-quit', () => {
    isQuitting = true;
    if (queueWorker) {
        try { queueWorker.kill(); } catch (e) { /* ignore */ }
        queueWorker = null;
    }
    if (phpServer) {
        try { phpServer.kill(); } catch (e) { /* ignore */ }
        phpServer = null;
    }
});

app.setLoginItemSettings({
    openAtLogin: false,
    path: app.getPath('exe')
});