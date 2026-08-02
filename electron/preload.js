const { contextBridge, ipcRenderer } = require('electron');

contextBridge.exposeInMainWorld('pbgElectron', {
  onKey(callback) {
    ipcRenderer.on('pbg-key', (_evt, payload) => callback(payload));
  },
  getConfig() {
    return ipcRenderer.invoke('pbg-get-config');
  },
});
