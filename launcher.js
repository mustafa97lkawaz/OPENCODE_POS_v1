const { spawn } = require('child_process');
const path = require('path');
const fs = require('fs');

const XAMPP_PATH = 'C:\\xampp';
const APP_PATH = __dirname;

function log(msg) {
    console.log(`[Launcher] ${msg}`);
    fs.appendFileSync(path.join(APP_PATH, 'launcher.log'), `${new Date().toISOString()} - ${msg}\n`);
}

function startXAMPP() {
    log('Starting XAMPP Control Panel...');
    
    const xamppControl = spawn('cmd.exe', ['/c', 'start', '""', `${XAMPP_PATH}\\xampp_control.exe`], {
        detached: true,
        stdio: 'ignore'
    });
    
    xamppControl.unref();
    log('XAMPP Control Panel started');
}

function startApache() {
    log('Starting Apache...');
    spawn('cmd.exe', ['/c', `${XAMPP_PATH}\\apache_start.bat`], {
        detached: true,
        stdio: 'ignore',
        shell: true
    }).unref();
}

function startMySQL() {
    log('Starting MySQL...');
    spawn('cmd.exe', ['/c', `${XAMPP_PATH}\\mysql_start.bat`], {
        detached: true,
        stdio: 'ignore',
        shell: true
    }).unref();
}

function startElectron() {
    log('Starting POS Electron App...');
    const electronPath = path.join(APP_PATH, 'node_modules', 'electron', 'dist', 'electron.exe');
    const mainPath = path.join(APP_PATH, 'electron', 'main.js');
    
    spawn('cmd.exe', ['/c', 'start', '""', `"${electronPath}"`, `"${mainPath}"`], {
        detached: true,
        stdio: 'ignore',
        shell: true
    }).unref();
    
    log('POS Desktop started');
}

function checkXAMPP() {
    const xamppExists = fs.existsSync(XAMPP_PATH);
    if (!xamppExists) {
        log('WARNING: XAMPP not found at ' + XAMPP_PATH);
        return false;
    }
    return true;
}

async function waitForServices() {
    log('Waiting for services to start...');
    await new Promise(resolve => setTimeout(resolve, 5000));
    log('Services should be ready');
}

async function main() {
    log('=== POS Launcher Started ===');
    
    if (checkXAMPP()) {
        startApache();
        startMySQL();
        await waitForServices();
    }
    
    startElectron();
    
    log('=== All services started ===');
}

main().catch(err => {
    log('Error: ' + err.message);
    process.exit(1);
});