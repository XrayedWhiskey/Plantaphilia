import React, { useState } from 'react'
import { VERSIONS } from '../../shared/versionRegistry.js'

export default function TestModeDialog({ onStartTest, onClose }) {
  const [selectedVersion, setSelectedVersion] = useState('')
  const [creating, setCreating] = useState(false)

  // Filter versions from 1.1.2 onwards
  const availableVersions = Object.keys(VERSIONS)
    .filter(v => {
      const [major, minor, patch] = v.split('.').map(Number)
      return major > 1 || (major === 1 && minor > 1) || (major === 1 && minor === 1 && patch >= 2)
    })
    .sort((a, b) => {
      const [amajor, aminor, apatch] = a.split('.').map(Number)
      const [bmajor, bminor, bpatch] = b.split('.').map(Number)
      if (amajor !== bmajor) return bmajor - amajor
      if (aminor !== bminor) return bminor - aminor
      return (bpatch || 0) - (apatch || 0)
    })

  const handleStart = async () => {
    if (!selectedVersion) return
    setCreating(true)
    try {
      await onStartTest(selectedVersion)
    } finally {
      setCreating(false)
    }
  }

  return (
    <div style={{
      position: 'fixed', inset: 0, background: 'rgba(0,0,0,0.7)', zIndex: 600,
      display: 'flex', alignItems: 'center', justifyContent: 'center'
    }}>
      <div style={{
        background: '#2F3B35', border: '1px solid rgba(155,111,208,0.3)',
        borderRadius: 8, width: 500, maxHeight: '85vh', display: 'flex', flexDirection: 'column',
        boxShadow: '0 24px 80px rgba(0,0,0,0.7)'
      }}>
        {/* Header */}
        <div style={{ padding: '18px 24px', borderBottom: '1px solid rgba(155,111,208,0.15)' }}>
          <div style={{ fontSize: 11, color: '#9B6FD0', fontWeight: 700, letterSpacing: 1.5, textTransform: 'uppercase', marginBottom: 6 }}>
            Version-Test-Modus
          </div>
          <h3 style={{ fontSize: 16, color: '#EAE4D6', fontWeight: 600, margin: 0 }}>
            Test-Migration für alte Versionen
          </h3>
          <div style={{ fontSize: 12, color: '#9CA59E', marginTop: 4 }}>
            Erstelle Test-Produkte aus einer alten Version und teste das Migrationssystem
          </div>
        </div>

        {/* Body */}
        <div style={{ flex: 1, overflowY: 'auto', padding: '0 24px 24px' }}>
          <div style={{ padding: '16px 0' }}>
            <label className="label">Version auswählen</label>
            <div style={{ fontSize: 11, color: '#9CA59E', marginBottom: 8 }}>
              Wähle eine Version ab 1.1.2, um Test-Produkte aus dieser Zeit zu erstellen
            </div>
            <select
              className="input"
              value={selectedVersion}
              onChange={e => setSelectedVersion(e.target.value)}
            >
              <option value="">-- Version wählen --</option>
              {availableVersions.map(v => (
                <option key={v} value={v}>{v} - {VERSIONS[v].description}</option>
              ))}
            </select>

            {selectedVersion && VERSIONS[selectedVersion] && (
              <div style={{
                marginTop: 16,
                padding: '12px 16px',
                background: 'rgba(155,111,208,0.08)',
                border: '1px solid rgba(155,111,208,0.2)',
                borderRadius: 6
              }}>
                <div style={{ fontSize: 11, color: '#9B6FD0', fontWeight: 700, marginBottom: 8, textTransform: 'uppercase', letterSpacing: 1 }}>
                  Version {selectedVersion}
                </div>
                <div style={{ fontSize: 12, color: '#C8C0AF', marginBottom: 8 }}>
                  {VERSIONS[selectedVersion].description}
                </div>
                {VERSIONS[selectedVersion].details && (
                  <>
                    <div style={{ fontSize: 11, color: '#9CA59E', marginBottom: 4, fontWeight: 600 }}>
                      Vorher:
                    </div>
                    <div style={{ fontSize: 12, color: '#EAE4D6', marginBottom: 8, paddingLeft: 8 }}>
                      {VERSIONS[selectedVersion].details.before}
                    </div>
                    <div style={{ fontSize: 11, color: '#9CA59E', marginBottom: 4, fontWeight: 600 }}>
                      Jetzt:
                    </div>
                    <div style={{ fontSize: 12, color: '#EAE4D6', paddingLeft: 8 }}>
                      {VERSIONS[selectedVersion].details.after}
                    </div>
                  </>
                )}
              </div>
            )}

            <div style={{
              marginTop: 16,
              padding: '12px 16px',
              background: 'rgba(76, 175, 125, 0.08)',
              border: '1px solid rgba(76, 175, 125, 0.2)',
              borderRadius: 6,
              fontSize: 12,
              color: '#4CAF7D'
            }}>
              <strong>Was passiert:</strong>
              <ul style={{ margin: '8px 0 0 16px', paddingLeft: 16 }}>
                <li>Ein Test-Ordner wird erstellt</li>
                <li>Test-Produkte mit der gewählten Version werden erstellt</li>
                <li>Der Test-Ordner wird im Explorer geöffnet</li>
                <li>Öffne die App mit diesem Ordner als Projektordner</li>
                <li>Du kannst das Migration-UI testen</li>
                <li>Beende den Test über das Banner in der Haupt-App</li>
              </ul>
            </div>
          </div>
        </div>

        {/* Footer */}
        <div style={{ padding: '16px 24px', borderTop: '1px solid rgba(155,111,208,0.15)', display: 'flex', gap: 10, justifyContent: 'flex-end' }}>
          <button
            className="btn-secondary"
            onClick={onClose}
            disabled={creating}
          >
            Abbrechen
          </button>
          <button
            className="btn-primary"
            onClick={handleStart}
            disabled={!selectedVersion || creating}
          >
            {creating ? 'Erstelle Test...' : 'Test starten'}
          </button>
        </div>
      </div>
    </div>
  )
}
