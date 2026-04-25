const { app, BrowserWindow, Menu, Tray, ipcMain, nativeImage, shell, dialog } = require('electron');
const path = require('path');
const fs = require('fs');
const net = require('net');
const http = require('http');
const { spawn } = require('child_process');

let mainWindow = null;
let tray = null;
let isQuitting = false;
let phpServer = null;
let resolvedUrl = null;

const XAMPP_URL = 'http://localhost/pos_opencodee/public';

// app.getAppPath() works both in dev (project root) and packaged (asar root).
// In packaged builds, electron/php/** must be in asarUnpack so php.exe is on disk.
function projectRoot() {
    return app.isPackaged ? path.join(process.resourcesPath, 'app.asar.unpacked') : app.getAppPath();
}

function bundledPhpPath() {
    return path.join(projectRoot(), 'electron', 'php', 'php.exe');
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
    if (!fs.existsSync(phpExe)) {
        console.log('[PHP] No bundled PHP at', phpExe, '— falling back to XAMPP URL');
        return XAMPP_URL;
    }

    const port = await findFreePort(8123, 8200);
    const root = projectRoot();
    const url = `http://127.0.0.1:${port}`;
    console.log('[PHP] Spawning bundled PHP on', url);

    phpServer = spawn(phpExe, ['-S', `127.0.0.1:${port}`, '-t', 'public', 'server.php'], {
        cwd: root,
        windowsHide: true,
        env: {
            ...process.env,
            APP_URL: url,             // override .env so Laravel asset() URLs are correct
            APP_DEBUG: 'false',
        },
    });
    phpServer.stdout.on('data', d => console.log('[PHP]', d.toString().trimEnd()));
    phpServer.stderr.on('data', d => console.error('[PHP-err]', d.toString().trimEnd()));
    phpServer.on('exit', code => console.log('[PHP] exited with code', code));

    try {
        await waitForHttp(url + '/');
        return url;
    } catch (err) {
        console.error('[PHP]', err.message);
        if (phpServer) { phpServer.kill(); phpServer = null; }
        return XAMPP_URL; // graceful fallback
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
    resolvedUrl = await startPhpServer();
    createWindow(resolvedUrl);
    createTray();

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
    if (phpServer) {
        try { phpServer.kill(); } catch (e) { /* ignore */ }
        phpServer = null;
    }
});

app.setLoginItemSettings({
    openAtLogin: false,
    path: app.getPath('exe')
});