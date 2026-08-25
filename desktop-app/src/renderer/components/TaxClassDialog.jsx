import React, { useState, useEffect, useContext } from 'react'
import { ToastContext } from '../App.jsx'

export default function TaxClassDialog({ id, onClose, onSaved }) {
  const [name, setName] = useState('')
  const [rate, setRate] = useState('')
  const [loading, setLoading] = useState(!!id)
  const [saving, setSaving] = useState(false)
  const addToast = useContext(ToastContext)

  useEffect(() => {
    if (!id) return
    window.api.getTaxClass(id).then(row => {
      if (row) {
        setName(row.name)
        setRate(String(row.rate))
      }
      setLoading(false)
    })
  }, [id])

  const handleSubmit = async (e) => {
    e.preventDefault()
    if (!name.trim() || rate === '') { addToast('Name und Steuersatz sind Pflichtfelder', 'error'); return }
    setSaving(true)
    try {
      const saved = await window.api.saveTaxClass({ id, name: name.trim(), rate: parseFloat(rate) })
      if (saved.wcError) {
        addToast(`Lokal gespeichert, aber WooCommerce-Sync fehlgeschlagen: ${saved.wcError}`, 'error')
      } else {
        addToast('Steuerklasse gespeichert', 'success')
      }
      onSaved && onSaved(saved)
    } catch (e2) {
      addToast(`Fehler: ${e2.message}`, 'error')
    } finally {
      setSaving(false)
    }
  }

  return (
    <div style={{
      position: 'fixed', inset: 0, background: 'rgba(0,0,0,0.7)', zIndex: 500,
      display: 'flex', alignItems: 'center', justifyContent: 'center'
    }}>
      <div style={{
        background: '#2F3B35', border: '1px solid rgba(155,111,208,0.3)',
        borderRadius: 8, width: 420, maxHeight: '85vh', display: 'flex', flexDirection: 'column',
        boxShadow: '0 24px 64px rgba(0,0,0,0.7)'
      }}>
        <div style={{ padding: '16px 20px', background: '#1A231F', borderBottom: '1px solid rgba(155,111,208,0.12)' }}>
          <div style={{ fontSize: 14, fontWeight: 700, color: '#EAE4D6' }}>
            {id ? 'Steuerklasse bearbeiten' : 'Neue Steuerklasse'}
          </div>
          <div style={{ fontSize: 11, color: '#9CA59E', marginTop: 2 }}>
            Name und Steuersatz — wird nach Deutschland (DE) an WooCommerce übertragen
          </div>
        </div>

        {loading ? (
          <div style={{ padding: 40, textAlign: 'center', color: '#9CA59E', fontSize: 13 }}>Laden…</div>
        ) : (
          <form onSubmit={handleSubmit} style={{ padding: 20, display: 'flex', flexDirection: 'column', gap: 16 }}>
            <div>
              <label className="label">Name *</label>
              <input
                className="input"
                value={name}
                onChange={e => setName(e.target.value)}
                placeholder="z. B. Ermäßigt"
                required
                autoFocus
              />
            </div>
            <div>
              <label className="label">Steuersatz (%) *</label>
              <input
                className="input"
                type="number"
                step="0.01"
                min="0"
                max="100"
                value={rate}
                onChange={e => setRate(e.target.value)}
                placeholder="z. B. 7"
                required
              />
            </div>
          </form>
        )}

        <div style={{ padding: '14px 20px', borderTop: '1px solid rgba(155,111,208,0.15)', display: 'flex', gap: 10, justifyContent: 'flex-end' }}>
          <button type="button" className="btn-secondary" onClick={onClose} disabled={saving}>Abbrechen</button>
          <button type="submit" className="btn-primary" onClick={handleSubmit} disabled={saving || loading}>
            {saving ? 'Speichere...' : 'Speichern'}
          </button>
        </div>
      </div>
    </div>
  )
}
