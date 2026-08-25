import React, { useState, useEffect, useContext } from 'react'
import { ToastContext } from '../App.jsx'

function composeLabel(min, max) {
  const a = min !== '' ? parseInt(min, 10) : null
  const b = max !== '' ? parseInt(max, 10) : null
  if (a == null && b == null) return ''
  if (a != null && b != null && a !== b) return `${a}–${b} Werktage`
  const single = a ?? b
  return `${single} Werktag${single === 1 ? '' : 'e'}`
}

export default function DeliveryTimeDialog({ id, onClose, onSaved }) {
  const [daysMin, setDaysMin] = useState('')
  const [daysMax, setDaysMax] = useState('')
  const [loading, setLoading] = useState(!!id)
  const [saving, setSaving] = useState(false)
  const addToast = useContext(ToastContext)

  useEffect(() => {
    if (!id) return
    window.api.getDeliveryTime(id).then(row => {
      if (row) {
        setDaysMin(row.days_min != null ? String(row.days_min) : '')
        setDaysMax(row.days_max != null ? String(row.days_max) : '')
      }
      setLoading(false)
    })
  }, [id])

  const label = composeLabel(daysMin, daysMax)

  const handleSubmit = async (e) => {
    e.preventDefault()
    if (!label) { addToast('Bitte mindestens „von" oder „bis" angeben', 'error'); return }
    setSaving(true)
    try {
      const saved = await window.api.saveDeliveryTime({
        id,
        days_min: daysMin !== '' ? parseInt(daysMin, 10) : null,
        days_max: daysMax !== '' ? parseInt(daysMax, 10) : null,
        label,
      })
      addToast('Lieferzeit gespeichert', 'success')
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
            {id ? 'Lieferzeit bearbeiten' : 'Neue Lieferzeit'}
          </div>
          <div style={{ fontSize: 11, color: '#9CA59E', marginTop: 2 }}>Von–bis Zeitraum in Werktagen</div>
        </div>

        {loading ? (
          <div style={{ padding: 40, textAlign: 'center', color: '#9CA59E', fontSize: 13 }}>Laden…</div>
        ) : (
          <form onSubmit={handleSubmit} style={{ padding: 20, display: 'flex', flexDirection: 'column', gap: 16 }}>
            <div style={{ display: 'flex', gap: 8, alignItems: 'center' }}>
              <div style={{ flex: 1 }}>
                <label className="label">Von (Werktage)</label>
                <input className="input" type="number" step="1" min="0" value={daysMin} onChange={e => setDaysMin(e.target.value)} placeholder="2" />
              </div>
              <span style={{ color: '#9CA59E', fontSize: 14, marginTop: 18 }}>–</span>
              <div style={{ flex: 1 }}>
                <label className="label">Bis (Werktage)</label>
                <input className="input" type="number" step="1" min="0" value={daysMax} onChange={e => setDaysMax(e.target.value)} placeholder="5" />
              </div>
            </div>

            <div style={{ padding: '12px', background: 'rgba(155,111,208,0.08)', border: '1px solid rgba(155,111,208,0.2)', borderRadius: 6, fontSize: 12, color: '#B8A8D8' }}>
              <div style={{ fontSize: 10, color: '#9CA59E', marginBottom: 4, textTransform: 'uppercase', letterSpacing: 1 }}>Vorschau</div>
              {label || '-'}
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
