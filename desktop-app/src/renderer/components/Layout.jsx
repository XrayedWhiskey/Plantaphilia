import React, { useState, useEffect } from 'react'
import ProductList from './ProductList.jsx'
import Kategorien from './Kategorien.jsx'
import SyncConflictDialog from './SyncConflictDialog.jsx'
import PushReviewDialog from './PushReviewDialog.jsx'
import TestModeDialog from './TestModeDialog.jsx'
import { useSync } from '../hooks/useSync.js'

const NAV = [
  { id: 'products', label: 'Produkte', icon: '🌿' },
  { id: 'kategorien', label: 'Kategorien', icon: '🏷️' },
  { id: 'settings', label: 'Einstellungen', icon: '⚙️' },
]

function OnlineModal({ onSave, onCancel }) {
  const [wpUrl, setWpUrl] = useState('')
  const [wpUser, setWpUser] = useState('')
  const [wpPass, setWpPass] = useState('')
  const [testing, setTesting] = useState(false)
  const [testResult, setTestResult] = useState(null)
  const [saving, setSaving] = useState(false)

  const testConn = async () => {
    setTesting(true); setTestResult(null)
    await window.api.setSetting('wp_url', wpUrl)
    await window.api.setSetting('wp_username', wpUser)
    await window.api.setSetting('wp_app_password', wpPass)
    try {
      await window.api.testConnection()
      setTestResult({ ok: true, msg: 'Verbindung erfolgreich!' })
    } catch (e) {
      setTestResult({ ok: false, msg: e.message })
    } finally { setTesting(false) }
  }

  const handleSave = async () => {
    setSaving(true)
    await window.api.setSetting('wp_url', wpUrl)
    await window.api.setSetting('wp_username', wpUser)
    await window.api.setSetting('wp_app_password', wpPass)
    await window.api.setSetting('local_only', '')
    setSaving(false)
    onSave()
  }

  const canSave = wpUrl.trim() && wpUser.trim() && wpPass.trim()

  return (
    <div style={{ position: 'fixed', inset: 0, background: 'rgba(0,0,0,0.7)', zIndex: 500, display: 'flex', alignItems: 'center', justifyContent: 'center' }}>
      <div style={{ width: 400, background: '#2F3B35', border: '1px solid rgba(155,111,208,0.3)', borderRadius: 8, overflow: 'hidden', boxShadow: '0 24px 64px rgba(0,0,0,0.6)' }}>
        <div style={{ padding: '16px 20px', background: '#1A231F', borderBottom: '1px solid rgba(155,111,208,0.12)' }}>
          <div style={{ fontSize: 14, fontWeight: 700, color: '#EAE4D6' }}>Zu online wechseln</div>
          <div style={{ fontSize: 11, color: '#9CA59E', marginTop: 2 }}>WordPress-Verbindungsdaten eingeben</div>
        </div>
        <div style={{ padding: 20, display: 'flex', flexDirection: 'column', gap: 12 }}>
          <div>
            <label className="label">WordPress URL</label>
            <input className="input" value={wpUrl} onChange={e => setWpUrl(e.target.value)} placeholder="https://plantaphilia.local" />
          </div>
          <div>
            <label className="label">Benutzername</label>
            <input className="input" value={wpUser} onChange={e => setWpUser(e.target.value)} placeholder="admin" autoComplete="off" />
          </div>
          <div>
            <label className="label">App-Passwort</label>
            <input className="input" type="password" value={wpPass} onChange={e => setWpPass(e.target.value)} placeholder="xxxx xxxx xxxx xxxx xxxx xxxx" />
          </div>
          <div style={{ display: 'flex', gap: 10, alignItems: 'center' }}>
            <button className="btn-secondary" onClick={testConn} disabled={testing || !wpUrl || !wpUser || !wpPass} style={{ fontSize: 12, padding: '5px 12px' }}>
              {testing ? 'Teste...' : 'Verbindung testen'}
            </button>
            {testResult && <span style={{ fontSize: 12, color: testResult.ok ? '#4CAF7D' : '#E57373' }}>{testResult.msg}</span>}
          </div>
        </div>
        <div style={{ padding: '12px 20px', borderTop: '1px solid rgba(155,111,208,0.1)', display: 'flex', gap: 8, justifyContent: 'flex-end' }}>
          <button className="btn-secondary" onClick={onCancel}>Abbrechen</button>
          <button className="btn-primary" onClick={handleSave} disabled={!canSave || saving}>
            {saving ? 'Speichere...' : 'Online verknüpfen'}
          </button>
        </div>
      </div>
    </div>
  )
}

function LocalConfirm({ onConfirm, onCancel }) {
  return (
    <div style={{ position: 'fixed', inset: 0, background: 'rgba(0,0,0,0.7)', zIndex: 500, display: 'flex', alignItems: 'center', justifyContent: 'center' }}>
      <div style={{ width: 360, background: '#2F3B35', border: '1px solid rgba(155,111,208,0.3)', borderRadius: 8, overflow: 'hidden', boxShadow: '0 24px 64px rgba(0,0,0,0.6)' }}>
        <div style={{ padding: '16px 20px', background: '#1A231F', borderBottom: '1px solid rgba(155,111,208,0.12)' }}>
          <div style={{ fontSize: 14, fontWeight: 700, color: '#EAE4D6' }}>Zu lokal only wechseln?</div>
        </div>
        <div style={{ padding: 20 }}>
          <p style={{ fontSize: 13, color: '#C8C0AF', lineHeight: 1.6, margin: 0 }}>
            Push und Pull werden deaktiviert. Die WordPress-Zugangsdaten bleiben gespeichert und können jederzeit wieder aktiviert werden.
          </p>
        </div>
        <div style={{ padding: '12px 20px', borderTop: '1px solid rgba(155,111,208,0.1)', display: 'flex', gap: 8, justifyContent: 'flex-end' }}>
          <button className="btn-secondary" onClick={onCancel}>Abbrechen</button>
          <button onClick={onConfirm} style={{ padding: '7px 16px', borderRadius: 5, border: 'none', background: '#5A1A2E', color: '#fff', fontSize: 13, cursor: 'pointer', fontWeight: 600 }}>
            Zu lokal wechseln
          </button>
        </div>
      </div>
    </div>
  )
}

function SettingsView({ localOnly, onLocalOnlyChange }) {
  const [settings, setSettings] = useState({})
  const [saved, setSaved] = useState(false)
  const [showOnlineModal, setShowOnlineModal] = useState(false)
  const [showLocalConfirm, setShowLocalConfirm] = useState(false)
  const [testing, setTesting] = useState(false)
  const [testResult, setTestResult] = useState(null)
  const [showTestDialog, setShowTestDialog] = useState(false)
  const [pushingSeo, setPushingSeo] = useState(false)
  const [seoPushResult, setSeoPushResult] = useState(null)

  useEffect(() => {
    window.api.getSettings().then(setSettings)
  }, [])

  const save = async (key, value) => {
    await window.api.setSetting(key, value)
    setSettings(s => ({ ...s, [key]: value }))
    setSaved(true)
    setTimeout(() => setSaved(false), 2000)
  }

  const testConn = async () => {
    setTesting(true); setTestResult(null)
    try {
      await window.api.testConnection()
      setTestResult({ ok: true, msg: 'Verbindung erfolgreich!' })
    } catch (e) {
      setTestResult({ ok: false, msg: e.message })
    } finally { setTesting(false) }
  }

  const switchToLocal = async () => {
    await window.api.setSetting('local_only', '1')
    setSettings(s => ({ ...s, local_only: '1' }))
    setShowLocalConfirm(false)
    onLocalOnlyChange(true)
  }

  const switchToOnline = () => {
    setShowOnlineModal(false)
    onLocalOnlyChange(false)
    window.api.getSettings().then(setSettings)
  }

  const nonWpFields = [
    { key: 'project_folder', label: 'Projektordner', placeholder: 'C:\\Projekte\\Plantaphilia', isFolder: true },
    { key: 'autosave_interval', label: 'Auto-Save Intervall (Sekunden)', placeholder: '60' },
  ]

  const seoFields = [
    { key: 'seo_home_title', label: 'Startseite – Meta-Titel', placeholder: 'Plantaphilia – Seltene Pelargonien & botanische Raritäten' },
    { key: 'seo_home_description', label: 'Startseite – Meta-Beschreibung', textarea: true, placeholder: 'Kurzer, einladender Beschreibungstext für die Google-Trefferliste (ca. 150–160 Zeichen).' },
    { key: 'seo_shop_title', label: 'Shopseite – Meta-Titel', placeholder: 'Shop | Plantaphilia' },
    { key: 'seo_shop_description', label: 'Shopseite – Meta-Beschreibung', textarea: true, placeholder: 'Kurzer, einladender Beschreibungstext für die Google-Trefferliste (ca. 150–160 Zeichen).' },
  ]

  const pushSeo = async () => {
    setPushingSeo(true); setSeoPushResult(null)
    try {
      await window.api.pushSeoSettings({
        home: { title: settings.seo_home_title || '', description: settings.seo_home_description || '' },
        shop: { title: settings.seo_shop_title || '', description: settings.seo_shop_description || '' },
      })
      setSeoPushResult({ ok: true, msg: 'Übertragen!' })
    } catch (e) {
      setSeoPushResult({ ok: false, msg: e.message })
    } finally {
      setPushingSeo(false)
    }
  }

  const wpFields = [
    { key: 'wp_url', label: 'WordPress URL', placeholder: 'https://plantaphilia.local' },
    { key: 'wp_username', label: 'Benutzername', placeholder: 'admin' },
    { key: 'wp_app_password', label: 'App-Passwort', placeholder: 'xxxx xxxx xxxx xxxx', type: 'password' },
  ]

  return (
    <div style={{ padding: 32, maxWidth: 540 }}>
      <h2 style={{ fontSize: 18, fontWeight: 700, color: '#EAE4D6', marginBottom: 24 }}>Einstellungen</h2>

      {/* Connection mode section */}
      <div style={{ marginBottom: 24, padding: 16, background: '#1A231F', borderRadius: 8, border: '1px solid rgba(155,111,208,0.12)' }}>
        <div style={{ fontSize: 11, fontWeight: 700, color: '#9CA59E', textTransform: 'uppercase', letterSpacing: 1, marginBottom: 10 }}>Verbindungsmodus</div>
        <div style={{ display: 'flex', alignItems: 'center', justifyContent: 'space-between' }}>
          <div>
            <div style={{ display: 'flex', alignItems: 'center', gap: 8 }}>
              <span style={{ display: 'inline-block', width: 8, height: 8, borderRadius: '50%', background: localOnly ? '#9CA59E' : '#4CAF7D' }} />
              <span style={{ fontSize: 13, color: '#EAE4D6', fontWeight: 600 }}>
                {localOnly ? 'Nur lokal' : `Online — ${settings.wp_url || '(keine URL)'}`}
              </span>
            </div>
            {!localOnly && (
              <div style={{ marginTop: 6, display: 'flex', gap: 8, alignItems: 'center' }}>
                <button className="btn-secondary" onClick={testConn} disabled={testing} style={{ fontSize: 11, padding: '4px 10px' }}>
                  {testing ? 'Teste...' : 'Verbindung testen'}
                </button>
                {testResult && <span style={{ fontSize: 11, color: testResult.ok ? '#4CAF7D' : '#E57373' }}>{testResult.msg}</span>}
              </div>
            )}
          </div>
          {localOnly ? (
            <button className="btn-primary" onClick={() => setShowOnlineModal(true)} style={{ fontSize: 12, padding: '6px 14px', whiteSpace: 'nowrap' }}>
              Zu online wechseln
            </button>
          ) : (
            <button className="btn-secondary" onClick={() => setShowLocalConfirm(true)} style={{ fontSize: 12, padding: '6px 14px', whiteSpace: 'nowrap' }}>
              Zu lokal only wechseln
            </button>
          )}
        </div>
      </div>

      {/* WP fields (only when online) */}
      {!localOnly && (
        <div style={{ display: 'flex', flexDirection: 'column', gap: 16, marginBottom: 24 }}>
          <div style={{ fontSize: 11, fontWeight: 700, color: '#9CA59E', textTransform: 'uppercase', letterSpacing: 1 }}>WordPress</div>
          {wpFields.map(f => (
            <div key={f.key}>
              <label className="label">{f.label}</label>
              <input className="input" type={f.type || 'text'} value={settings[f.key] || ''} placeholder={f.placeholder}
                onChange={e => setSettings(s => ({ ...s, [f.key]: e.target.value }))}
                onBlur={e => save(f.key, e.target.value)} />
            </div>
          ))}
        </div>
      )}

      {/* Non-WP fields */}
      <div style={{ display: 'flex', flexDirection: 'column', gap: 16 }}>
        <div style={{ fontSize: 11, fontWeight: 700, color: '#9CA59E', textTransform: 'uppercase', letterSpacing: 1 }}>Allgemein</div>
        {nonWpFields.map(f => (
          <div key={f.key}>
            <label className="label">{f.label}</label>
            <div style={{ display: 'flex', gap: 8 }}>
              <input className="input" value={settings[f.key] || ''} placeholder={f.placeholder}
                onChange={e => setSettings(s => ({ ...s, [f.key]: e.target.value }))}
                onBlur={e => save(f.key, e.target.value)} />
              {f.isFolder && (
                <button className="btn-secondary" style={{ whiteSpace: 'nowrap' }} onClick={async () => {
                  const folder = await window.api.openFolder()
                  if (folder) save(f.key, folder)
                }}>Wählen</button>
              )}
            </div>
          </div>
        ))}
      </div>

      {saved && <div style={{ marginTop: 16, fontSize: 13, color: '#4CAF7D' }}>Gespeichert</div>}

      {/* SEO: Start-/Shopseite (Produkte haben ihr eigenes SEO-Feld im Produktformular).
          Immer sichtbar/editierbar (auch lokal-only) — nur das Übertragen braucht eine
          Verbindung, das Vorbereiten der Texte nicht. */}
      <div style={{ marginTop: 24, display: 'flex', flexDirection: 'column', gap: 16 }}>
        <div style={{ fontSize: 11, fontWeight: 700, color: '#9CA59E', textTransform: 'uppercase', letterSpacing: 1 }}>SEO — Start-/Shopseite</div>
        {seoFields.map(f => (
          <div key={f.key}>
            <label className="label">{f.label}</label>
            {f.textarea ? (
              <textarea className="input" rows={2} value={settings[f.key] || ''} placeholder={f.placeholder}
                onChange={e => setSettings(s => ({ ...s, [f.key]: e.target.value }))}
                onBlur={e => save(f.key, e.target.value)} />
            ) : (
              <input className="input" value={settings[f.key] || ''} placeholder={f.placeholder}
                onChange={e => setSettings(s => ({ ...s, [f.key]: e.target.value }))}
                onBlur={e => save(f.key, e.target.value)} />
            )}
          </div>
        ))}
        <div style={{ display: 'flex', gap: 10, alignItems: 'center' }}>
          <button className="btn-primary" onClick={pushSeo} disabled={pushingSeo || localOnly} title={localOnly ? 'Erst online wechseln, um zu übertragen' : ''} style={{ fontSize: 12, padding: '6px 14px' }}>
            {pushingSeo ? 'Überträgt...' : 'SEO speichern & übertragen'}
          </button>
          {localOnly && <span style={{ fontSize: 12, color: '#9CA59E' }}>Nur lokal gespeichert — zum Übertragen erst online wechseln.</span>}
          {seoPushResult && <span style={{ fontSize: 12, color: seoPushResult.ok ? '#4CAF7D' : '#E57373' }}>{seoPushResult.msg}</span>}
        </div>
      </div>

      {/* Test Mode Button */}
      <div style={{ marginTop: 24, padding: '16px', background: 'rgba(155,111,208,0.08)', border: '1px solid rgba(155,111,208,0.2)', borderRadius: 8 }}>
        <div style={{ fontSize: 11, fontWeight: 700, color: '#9B6FD0', textTransform: 'uppercase', letterSpacing: 1, marginBottom: 8 }}>
          Entwickler-Tools
        </div>
        <button
          onClick={() => setShowTestDialog(true)}
          style={{
            width: '100%',
            padding: '10px',
            background: 'rgba(155,111,208,0.1)',
            border: '1px dashed rgba(155,111,208,0.4)',
            borderRadius: 6,
            color: '#B8A8D8',
            fontSize: 12,
            cursor: 'pointer',
            transition: 'all 0.15s'
          }}
        >
          🧪 Test-Modus starten (Version-Migration testen)
        </button>
      </div>

      {showOnlineModal && <OnlineModal onSave={switchToOnline} onCancel={() => setShowOnlineModal(false)} />}
      {showLocalConfirm && <LocalConfirm onConfirm={switchToLocal} onCancel={() => setShowLocalConfirm(false)} />}
      {showTestDialog && (
        <TestModeDialog
          onStartTest={async (version) => {
            const result = await window.api.startTestMode(version)
            if (result.success) {
              setShowTestDialog(false)
              alert(`Test-Ordner erstellt: ${result.testFolder}\nOrdner wurde im Explorer geöffnet. Öffne die App mit diesem Ordner als Projektordner.`)
            }
          }}
          onClose={() => setShowTestDialog(false)}
        />
      )}
    </div>
  )
}

export default function Layout() {
  const [activeNav, setActiveNav] = useState('products')
  const [localOnly, setLocalOnly] = useState(false)
  const [saveFeedback, setSaveFeedback] = useState(false)
  const sync = useSync()

  useEffect(() => {
    window.api.getSettings().then(s => {
      setLocalOnly(s.local_only === '1')
    })
  }, [])

  const fmtTime = (d) => d ? d.toLocaleTimeString('de-DE', { hour: '2-digit', minute: '2-digit' }) : '—'

  const triggerManualSave = () => {
    window.dispatchEvent(new CustomEvent('manual-save'))
    setSaveFeedback(true)
    setTimeout(() => setSaveFeedback(false), 1800)
  }

  const syncBtnStyle = (disabled) => ({
    fontSize: 12, padding: '6px 14px',
    opacity: disabled ? 0.35 : 1,
    cursor: disabled ? 'not-allowed' : 'pointer',
  })

  return (
    <div style={{ display: 'flex', height: '100vh', overflow: 'hidden' }}>
      {/* Sidebar */}
      <aside style={{ width: 200, background: '#1A231F', borderRight: '1px solid rgba(155,111,208,0.12)', display: 'flex', flexDirection: 'column', flexShrink: 0 }}>
        <div style={{ padding: '20px 16px 16px', borderBottom: '1px solid rgba(155,111,208,0.1)' }}>
          <div style={{ fontSize: 13, fontWeight: 700, color: '#9B6FD0', letterSpacing: 2, textTransform: 'uppercase' }}>Plantaphilia</div>
          <div style={{ fontSize: 10, color: '#9CA59E', marginTop: 2 }}>Produktverwaltung</div>
          {localOnly && (
            <div style={{ marginTop: 6, fontSize: 10, color: '#B8A8D8', background: 'rgba(155,111,208,0.12)', borderRadius: 3, padding: '2px 6px', display: 'inline-block' }}>Lokal</div>
          )}
        </div>
        <nav style={{ flex: 1, padding: '12px 8px' }}>
          {NAV.map(n => (
            <button key={n.id} onClick={() => setActiveNav(n.id)} style={{
              width: '100%', textAlign: 'left', padding: '9px 12px', borderRadius: 6,
              background: activeNav === n.id ? 'rgba(155,111,208,0.15)' : 'transparent',
              color: activeNav === n.id ? '#B8A8D8' : '#9CA59E',
              border: 'none', cursor: 'pointer', fontSize: 13, fontWeight: 500,
              display: 'flex', alignItems: 'center', gap: 8, transition: 'all 0.15s'
            }}>
              <span>{n.icon}</span> {n.label}
            </button>
          ))}
        </nav>
      </aside>

      {/* Main */}
      <div style={{ flex: 1, display: 'flex', flexDirection: 'column', overflow: 'hidden' }}>
        {/* Header */}
        <header style={{ height: 52, background: '#1A231F', borderBottom: '1px solid rgba(155,111,208,0.12)', display: 'flex', alignItems: 'center', justifyContent: 'flex-end', padding: '0 20px', gap: 10, flexShrink: 0 }}>
          {sync.progress && (
            <span style={{ fontSize: 12, color: '#9CA59E', maxWidth: 300, overflow: 'hidden', textOverflow: 'ellipsis', whiteSpace: 'nowrap' }}>{sync.progress}</span>
          )}
          {sync.lastSynced && !sync.progress && (
            <span style={{ fontSize: 12, color: '#9CA59E' }}>Sync: {fmtTime(sync.lastSynced)}</span>
          )}

          <button onClick={triggerManualSave} style={{ fontSize: 12, padding: '6px 14px', background: saveFeedback ? 'rgba(76,175,125,0.15)' : 'rgba(155,111,208,0.08)', border: `1px solid ${saveFeedback ? 'rgba(76,175,125,0.4)' : 'rgba(155,111,208,0.25)'}`, color: saveFeedback ? '#4CAF7D' : '#B8A8D8', borderRadius: 5, cursor: 'pointer', transition: 'all 0.2s', fontWeight: 500 }}>
            {saveFeedback ? '✓ Gespeichert' : '💾 Speichern'}
          </button>

          <button className="btn-secondary" onClick={sync.pull}
            disabled={localOnly || sync.pulling || sync.pushing || sync.preparing}
            style={syncBtnStyle(localOnly || sync.pulling || sync.pushing || sync.preparing)}
            title={localOnly ? 'Im Lokal-Modus nicht verfügbar' : undefined}>
            {sync.pulling ? '↓ Laden...' : '↓ Pull'}
          </button>
          <button className="btn-primary" onClick={sync.preparePush}
            disabled={localOnly || sync.pulling || sync.pushing || sync.preparing}
            style={syncBtnStyle(localOnly || sync.pulling || sync.pushing || sync.preparing)}
            title={localOnly ? 'Im Lokal-Modus nicht verfügbar' : undefined}>
            {sync.preparing ? '↑ Analysiere...' : '↑ Push'}
          </button>
        </header>

        {/* Content */}
        <main style={{ flex: 1, overflow: 'auto', background: '#25302B' }}>
          {activeNav === 'products' && <ProductList />}
          {activeNav === 'kategorien' && <Kategorien />}
          {activeNav === 'settings' && <SettingsView localOnly={localOnly} onLocalOnlyChange={setLocalOnly} />}
        </main>
      </div>

      {sync.pullConflicts.length > 0 && (
        <SyncConflictDialog
          conflicts={sync.pullConflicts}
          onResolve={(resolved) => sync.applyPullResolutions(resolved)}
          onClose={() => sync.setPullConflicts([])}
        />
      )}
      {sync.pushPreview && (
        <PushReviewDialog
          data={sync.pushPreview}
          onPush={(payload) => sync.push(payload)}
          onCancel={() => sync.setPushPreview(null)}
        />
      )}
    </div>
  )
}
