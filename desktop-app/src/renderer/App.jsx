import React, { useState, useEffect, createContext } from 'react'
import StartupDialog from './components/StartupDialog.jsx'
import Layout from './components/Layout.jsx'
import PreviewPanel from './components/PreviewPanel.jsx'
import VersionMigrationDialog from './components/VersionMigrationDialog.jsx'
import TestModeDialog from './components/TestModeDialog.jsx'

export const ToastContext = createContext(null)
export const PreviewContext = createContext(null)

function ToastProvider({ children }) {
  const [toasts, setToasts] = useState([])

  const addToast = (msg, type = 'info') => {
    const id = Date.now() + Math.random()
    setToasts(t => [...t, { id, msg, type }])
    setTimeout(() => setToasts(t => t.filter(x => x.id !== id)), 4000)
  }

  return (
    <ToastContext.Provider value={addToast}>
      {children}
      <div style={{ position: 'fixed', bottom: 20, right: 20, zIndex: 9999, display: 'flex', flexDirection: 'column', gap: 8 }}>
        {toasts.map(t => (
          <div key={t.id} style={{
            padding: '10px 16px', borderRadius: 4, fontSize: 13, maxWidth: 340,
            background: t.type === 'error' ? '#5A1A2E' : t.type === 'success' ? '#1A3B2E' : '#2F3B35',
            border: `1px solid ${t.type === 'error' ? '#7A2840' : t.type === 'success' ? '#2A6B4A' : 'rgba(155,111,208,0.3)'}`,
            color: '#EAE4D6', boxShadow: '0 4px 12px rgba(0,0,0,0.4)',
            animation: 'fadeIn 0.2s ease'
          }}>{t.msg}</div>
        ))}
      </div>
      <style>{`@keyframes fadeIn { from { opacity:0; transform:translateY(8px) } to { opacity:1; transform:none } }`}</style>
    </ToastContext.Provider>
  )
}

function PreviewProvider({ children }) {
  const [preview, setPreview] = useState(null) // { url, title }
  return (
    <PreviewContext.Provider value={setPreview}>
      {children}
      {preview && <PreviewPanel product={preview.product} onClose={() => setPreview(null)} />}
    </PreviewContext.Provider>
  )
}

export default function App() {
  const [ready, setReady] = useState(false)
  const [setupDone, setSetupDone] = useState(false)
  const [migrationData, setMigrationData] = useState(null)
  const [showTestDialog, setShowTestDialog] = useState(false)
  const [testModeActive, setTestModeActive] = useState(false)
  const addToast = React.useContext(ToastContext)

  useEffect(() => {
    window.api.getSettings().then(s => {
      setSetupDone(!!(s.project_folder && (s.local_only === '1' || s.wp_url)))
      setReady(true)
    })
  }, [])

  // Check for migrations when setup is done
  useEffect(() => {
    if (setupDone) {
      checkMigrations()
    }
  }, [setupDone])

  const checkMigrations = async () => {
    try {
      const data = await window.api.checkMigrations()
      if (data && data.hasMigrations) {
        setMigrationData(data)
      }
    } catch (e) {
      console.error('Failed to check migrations:', e)
    }
  }

  const handleMigrate = async (productIds) => {
    try {
      const results = await window.api.migrateProducts(productIds)
      const successCount = results.filter(r => r.success).length
      addToast(`${successCount} Produkt${successCount !== 1 ? 'e' : ''} erfolgreich migriert`, 'success')
      setMigrationData(null)
      // Refresh products list
      window.dispatchEvent(new CustomEvent('products-refresh'))
    } catch (e) {
      addToast(`Migration fehlgeschlagen: ${e.message}`, 'error')
    }
  }

  const handleIgnoreMigration = () => {
    setMigrationData(null)
    addToast('Migration ignoriert — Produkte behalten alte Version', 'info')
  }

  const handleStartTest = async (version) => {
    try {
      const result = await window.api.startTestMode(version)
      if (result.success) {
        setTestModeActive(true)
        setShowTestDialog(false)
        addToast(`Test-Modus aktiviert für Version ${version}`, 'success')
      }
    } catch (e) {
      addToast(`Fehler beim Starten des Test-Modus: ${e.message}`, 'error')
    }
  }

  const handleEndTest = async () => {
    try {
      await window.api.endTestMode()
      setTestModeActive(false)
      addToast('Test-Modus beendet, Test-Ordner gelöscht', 'success')
    } catch (e) {
      addToast(`Fehler beim Beenden des Test-Modus: ${e.message}`, 'error')
    }
  }

  // Listen for pull events to recheck migrations
  useEffect(() => {
    const handlePullComplete = () => {
      checkMigrations()
    }
    window.addEventListener('pull-complete', handlePullComplete)
    return () => window.removeEventListener('pull-complete', handlePullComplete)
  }, [])

  // Listen for test mode dialog trigger (Ctrl+Shift+T)
  useEffect(() => {
    const handleOpenTestDialog = () => {
      setShowTestDialog(true)
    }
    
    // Listen to IPC event from main process
    if (window.electronAPI?.onTestModeDialog) {
      window.electronAPI.onTestModeDialog(handleOpenTestDialog)
    }
    
    // Also listen to custom event for testing
    window.addEventListener('open-test-mode-dialog', handleOpenTestDialog)
    
    return () => {
      window.removeEventListener('open-test-mode-dialog', handleOpenTestDialog)
    }
  }, [])

  if (!ready) return (
    <div style={{ display: 'flex', alignItems: 'center', justifyContent: 'center', height: '100vh', color: '#9CA59E', fontSize: 14 }}>
      Laden…
    </div>
  )

  return (
    <ToastProvider>
      <PreviewProvider>
        {setupDone
          ? (
            <>
              {testModeActive && (
                <div style={{
                  position: 'fixed',
                  top: 0,
                  left: 0,
                  right: 0,
                  height: 40,
                  background: 'rgba(229, 115, 73, 0.95)',
                  borderBottom: '1px solid rgba(255, 255, 255, 0.2)',
                  zIndex: 1000,
                  display: 'flex',
                  alignItems: 'center',
                  justifyContent: 'center',
                  gap: 16
                }}>
                  <span style={{ color: '#fff', fontSize: 13, fontWeight: 600 }}>
                    ⚠️ Test-Modus aktiv — Test-Ordner wird verwendet
                  </span>
                  <button
                    onClick={handleEndTest}
                    style={{
                      padding: '6px 14px',
                      background: 'rgba(255, 255, 255, 0.2)',
                      border: '1px solid rgba(255, 255, 255, 0.4)',
                      borderRadius: 4,
                      color: '#fff',
                      fontSize: 12,
                      cursor: 'pointer',
                      fontWeight: 600
                    }}
                  >
                    Test beenden & Ordner löschen
                  </button>
                </div>
              )}
              <Layout style={{ marginTop: testModeActive ? 40 : 0 }} />
            </>
          )
          : <StartupDialog onDone={() => setSetupDone(true)} />
        }
        {migrationData && (
          <VersionMigrationDialog
            migrationData={migrationData}
            onMigrate={handleMigrate}
            onIgnore={handleIgnoreMigration}
            onClose={() => setMigrationData(null)}
          />
        )}
        {showTestDialog && (
          <TestModeDialog
            onStartTest={handleStartTest}
            onClose={() => setShowTestDialog(false)}
          />
        )}
      </PreviewProvider>
    </ToastProvider>
  )
}
