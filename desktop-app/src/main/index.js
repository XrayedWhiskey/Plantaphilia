import { app, BrowserWindow, session, globalShortcut } from 'electron'
import path from 'path'
import { fileURLToPath } from 'url'
import { initDatabase, getSetting } from './database.js'
import { registerIpcHandlers } from './ipc.js'

const __dirname = path.dirname(fileURLToPath(import.meta.url))
const isDev = !app.isPackaged

let mainWindow

function createWindow() {
  mainWindow = new BrowserWindow({
    width: 1280,
    height: 820,
    minWidth: 900,
    minHeight: 600,
    backgroundColor: '#25302B',
    webPreferences: {
      preload: path.join(__dirname, '../preload/preload.cjs'),
      contextIsolation: true,
      nodeIntegration: false,
      sandbox: false,
      webviewTag: true
    }
  })

  if (isDev) {
    mainWindow.loadURL('http://localhost:5173')
    mainWindow.webContents.openDevTools()
  } else {
    mainWindow.loadFile(path.join(__dirname, '../../dist/index.html'))
  }
}

app.whenReady().then(() => {
  session.defaultSession.setCertificateVerifyProc((req, cb) => {
    const h = req.hostname
    cb((h.endsWith('.local') || h === 'localhost' || h === '127.0.0.1') ? 0 : -2)
  })
  initDatabase(null)
  const projectFolder = getSetting('project_folder') || ''
  if (projectFolder) initDatabase(projectFolder)
  registerIpcHandlers()
  createWindow()
  
  // Register global shortcut for test mode (Ctrl+Shift+T)
  const ret = globalShortcut.register('CommandOrControl+Shift+T', () => {
    if (mainWindow) {
      mainWindow.webContents.send('open-test-mode-dialog')
    }
  })
  
  if (!ret) {
    console.error('Registration of global shortcut failed')
  }
  
  app.on('activate', () => {
    if (BrowserWindow.getAllWindows().length === 0) createWindow()
  })
})

app.on('window-all-closed', () => {
  if (process.platform !== 'darwin') app.quit()
})

app.on('will-quit', () => {
  // Unregister all shortcuts when app is quitting
  globalShortcut.unregisterAll()
})
