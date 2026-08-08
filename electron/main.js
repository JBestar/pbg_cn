const { app, BrowserWindow, ipcMain } = require('electron');
const path = require('path');
const fs = require('fs');

const configPath = path.join(__dirname, 'config.json');
const defaultConfig = {
  machineId: 'm01',
  // Local XAMPP URL for the Chinese terminal page
  appUrl: 'http://localhost/pbg_cn/public/index.html',
  fullscreen: true,
  width: 1080,
  height: 1920,
};

function loadConfig() {
  try {
    if (fs.existsSync(configPath)) {
      return Object.assign({}, defaultConfig, JSON.parse(fs.readFileSync(configPath, 'utf8')));
    }
  } catch (e) {
    console.warn('config read failed', e);
  }
  return { ...defaultConfig };
}

function createWindow() {
  const cfg = loadConfig();
  const win = new BrowserWindow({
    width: cfg.width,
    height: cfg.height,
    fullscreen: !!cfg.fullscreen,
    autoHideMenuBar: true,
    webPreferences: {
      preload: path.join(__dirname, 'preload.js'),
      contextIsolation: true,
      nodeIntegration: false,
    },
  });

  const url = cfg.appUrl + (cfg.appUrl.includes('?') ? '&' : '?') + 'mid=' + encodeURIComponent(cfg.machineId);
  win.loadURL(url);

  // Main-process key capture (works even if page focus quirks)
  win.webContents.on('before-input-event', (event, input) => {
    if (input.type !== 'keyDown') return;
    const code = input.code;
    const watch = new Set([
      'KeyA','KeyB','KeyC','KeyD','KeyE','KeyF','KeyG','KeyH',
      'KeyO','KeyP','KeyQ','KeyR','KeyS','KeyT','KeyU','KeyV',
      'KeyW','KeyX','KeyY','KeyZ','KeyI','Escape',
    ]);
    if (!watch.has(code)) return;
    event.preventDefault();
    win.webContents.send('pbg-key', {
      code,
      key: input.key,
      ctrlKey: !!input.control,
      metaKey: !!input.meta,
    });
  });
}

app.whenReady().then(createWindow);

app.on('window-all-closed', () => {
  if (process.platform !== 'darwin') app.quit();
});

app.on('activate', () => {
  if (BrowserWindow.getAllWindows().length === 0) createWindow();
});

ipcMain.handle('pbg-get-config', () => loadConfig());
