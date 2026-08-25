import React, { useState } from 'react'

const STEPS = ['Projektordner', 'WordPress', 'Einstellungen']

export default function StartupDialog({ onDone }) {
  const [step, setStep] = useState(0)
  const [folder, setFolder] = useState('')
  const [localOnly, setLocalOnly] = useState(false)
  const [wpUrl, setWpUrl] = useState('')
  const [wpUser, setWpUser] = useState('')
  const [wpPass, setWpPass] = useState('')
  const [autosave, setAutosave] = useState('60')
  const [testing, setTesting] = useState(false)
  const [testResult, setTestResult] = useState(null)
  const [saving, setSaving] = useState(false)

  const pickFolder = async () => {
    const f = await window.api.openFolder()
    if (f) setFolder(f)
  }

  const testConn = async () => {
    setTesting(true)
    setTestResult(null)
    await window.api.setSetting('wp_url', wpUrl)
    await window.api.setSetting('wp_username', wpUser)
    await window.api.setSetting('wp_app_password', wpPass)
    try {
      await window.api.testConnection()
      setTestResult({ ok: true, msg: 'Verbindung erfolgreich!' })
    } catch (e) {
      setTestResult({ ok: false, msg: e.message })
    } finally {
      setTesting(false)
    }
  }

  const chooseLocalOnly = () => {
    setLocalOnly(true)
    setTestResult(null)
    setStep(2)
  }

  const finish = async () => {
    setSaving(true)
    await window.api.setSetting('project_folder', folder)
    await window.api.setSetting('autosave_interval', autosave)
    if (localOnly) {
      await window.api.setSetting('local_only', '1')
    } else {
      await window.api.setSetting('local_only', '')
      await window.api.setSetting('wp_url', wpUrl)
      await window.api.setSetting('wp_username', wpUser)
      await window.api.setSetting('wp_app_password', wpPass)
    }
    setSaving(false)
    onDone()
  }

  const canNext = [
    folder.trim().length > 0,
    localOnly || (wpUrl.trim().length > 0 && wpUser.trim().length > 0 && wpPass.trim().length > 0),
    autosave.trim().length > 0
  ][step]

  return (
    <div style={{ position: 'fixed', inset: 0, background: '#1A231F', display: 'flex', alignItems: 'center', justifyContent: 'center' }}>
      <div style={{ width: 440, background: '#2F3B35', border: '1px solid rgba(155,111,208,0.25)', borderRadius: 10, overflow: 'hidden', boxShadow: '0 24px 64px rgba(0,0,0,0.6)' }}>
        {/* Header */}
        <div style={{ padding: '20px 24px 16px', borderBottom: '1px solid rgba(155,111,208,0.1)' }}>
          <div style={{ fontSize: 11, color: '#9B6FD0', fontWeight: 700, letterSpacing: 2, textTransform: 'uppercase', marginBottom: 6 }}>
            Plantaphilia — Ersteinrichtung
          </div>
          <div style={{ display: 'flex', gap: 8, alignItems: 'center' }}>
            {STEPS.map((s, i) => (
              <React.Fragment key={i}>
                <div style={{ fontSize: 11, fontWeight: 600, color: i === step ? '#EAE4D6' : i < step ? '#4CAF7D' : '#9CA59E', display: 'flex', alignItems: 'center', gap: 4 }}>
                  <span style={{ width: 18, height: 18, borderRadius: '50%', display: 'inline-flex', alignItems: 'center', justifyContent: 'center', fontSize: 10, background: i === step ? '#9B6FD0' : i < step ? '#2A6B4A' : 'rgba(155,111,208,0.15)', color: i <= step ? '#fff' : '#9CA59E' }}>
                    {i < step ? '✓' : i + 1}
                  </span>
                  {s}
                </div>
                {i < STEPS.length - 1 && <span style={{ color: 'rgba(155,111,208,0.3)', fontSize: 10 }}>→</span>}
              </React.Fragment>
            ))}
          </div>
        </div>

        {/* Body */}
        <div style={{ padding: 24 }}>
          {step === 0 && (
            <div>
              <h2 style={{ fontSize: 16, color: '#EAE4D6', marginBottom: 8 }}>Projektordner wählen</h2>
              <p style={{ fontSize: 12, color: '#9CA59E', marginBottom: 20, lineHeight: 1.5 }}>
                Wähle einen Ordner auf deinem Computer, in dem Bilder und die lokale Datenbank gespeichert werden.
              </p>
              <label className="label">Ordner</label>
              <div style={{ display: 'flex', gap: 8 }}>
                <input className="input" value={folder} readOnly placeholder="Noch kein Ordner gewählt..." style={{ color: folder ? '#EAE4D6' : '#9CA59E' }} />
                <button className="btn-secondary" onClick={pickFolder} style={{ whiteSpace: 'nowrap' }}>Wählen</button>
              </div>
            </div>
          )}

          {step === 1 && (
            <div>
              <h2 style={{ fontSize: 16, color: '#EAE4D6', marginBottom: 8 }}>WordPress-Verbindung</h2>
              <p style={{ fontSize: 12, color: '#9CA59E', marginBottom: 16, lineHeight: 1.5 }}>
                Erstelle ein App-Passwort unter <em>WP-Admin → Benutzer → Profil → Anwendungspasswörter</em>.
              </p>
              <div style={{ display: 'flex', flexDirection: 'column', gap: 12 }}>
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
              </div>
              <div style={{ marginTop: 16, display: 'flex', gap: 10, alignItems: 'center' }}>
                <button className="btn-secondary" onClick={testConn} disabled={testing || !wpUrl || !wpUser || !wpPass}>
                  {testing ? 'Teste...' : 'Verbindung testen'}
                </button>
                {testResult && (
                  <span style={{ fontSize: 12, color: testResult.ok ? '#4CAF7D' : '#E57373' }}>{testResult.msg}</span>
                )}
              </div>
              <div style={{ marginTop: 20, paddingTop: 16, borderTop: '1px solid rgba(155,111,208,0.1)' }}>
                <button onClick={chooseLocalOnly} style={{ width: '100%', padding: '10px 0', background: 'transparent', border: '1px dashed rgba(155,111,208,0.3)', borderRadius: 6, color: '#9CA59E', fontSize: 12, cursor: 'pointer', transition: 'all 0.15s' }}
                  onMouseEnter={e => { e.currentTarget.style.borderColor = 'rgba(155,111,208,0.6)'; e.currentTarget.style.color = '#C8C0AF' }}
                  onMouseLeave={e => { e.currentTarget.style.borderColor = 'rgba(155,111,208,0.3)'; e.currentTarget.style.color = '#9CA59E' }}>
                  Nur lokal verwenden (kein WordPress)
                </button>
              </div>
            </div>
          )}

          {step === 2 && (
            <div>
              <h2 style={{ fontSize: 16, color: '#EAE4D6', marginBottom: 8 }}>App-Einstellungen</h2>
              {localOnly && (
                <div style={{ marginBottom: 16, padding: '8px 12px', background: 'rgba(155,111,208,0.08)', border: '1px solid rgba(155,111,208,0.2)', borderRadius: 6, fontSize: 12, color: '#B8A8D8' }}>
                  Modus: Nur lokal — Push/Pull zu WordPress ist deaktiviert.
                </div>
              )}
              <div style={{ display: 'flex', flexDirection: 'column', gap: 12 }}>
                <div>
                  <label className="label">Auto-Save Intervall (Sekunden)</label>
                  <input className="input" type="number" min="10" max="3600" value={autosave} onChange={e => setAutosave(e.target.value)} />
                  <div style={{ fontSize: 11, color: '#9CA59E', marginTop: 4 }}>Produkte werden automatisch alle {autosave}s lokal gespeichert.</div>
                </div>
              </div>
            </div>
          )}
        </div>

        {/* Footer */}
        <div style={{ padding: '14px 24px', borderTop: '1px solid rgba(155,111,208,0.1)', display: 'flex', justifyContent: 'space-between', alignItems: 'center' }}>
          <button className="btn-secondary" onClick={() => { if (localOnly && step === 2) { setLocalOnly(false); setStep(1) } else { setStep(s => s - 1) } }} disabled={step === 0} style={{ opacity: step === 0 ? 0.4 : 1 }}>← Zurück</button>
          {step < STEPS.length - 1 ? (
            <button className="btn-primary" onClick={() => setStep(s => s + 1)} disabled={!canNext}>Weiter →</button>
          ) : (
            <button className="btn-primary" onClick={finish} disabled={!canNext || saving}>{saving ? 'Speichere...' : 'Fertig — App starten'}</button>
          )}
        </div>
      </div>
    </div>
  )
}
