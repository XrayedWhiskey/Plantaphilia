import React, { useState } from 'react'

function FieldConflict({ field, conflict, resolution, onChange }) {
  const { scenario, localValue, snapshotValue, onlineValue } = conflict

  const fmt = (v) => {
    if (v === null || v === undefined) return '—'
    if (typeof v === 'object') return JSON.stringify(v)
    return String(v)
  }

  if (scenario === 'C') {
    // Only online changed — checkbox to accept
    return (
      <div style={{ padding: '10px 0', borderBottom: '1px solid rgba(155,111,208,0.1)' }}>
        <div style={{ display: 'flex', alignItems: 'flex-start', gap: 10 }}>
          <input
            type="checkbox"
            id={`c-${field}`}
            checked={resolution?.accept !== false}
            onChange={e => onChange({ accept: e.target.checked, value: e.target.checked ? onlineValue : localValue })}
          />
          <label htmlFor={`c-${field}`} style={{ color: '#C8C0AF', fontSize: 12, cursor: 'pointer', lineHeight: 1.4 }}>
            <span style={{ color: '#9CA59E', display: 'block', marginBottom: 2 }}>{field}</span>
            <span style={{ color: '#9CA59E' }}>Online: </span>
            <span style={{ color: '#EAE4D6' }}>{fmt(onlineValue)}</span>
            <span style={{ color: '#9CA59E' }}> (vorher: {fmt(snapshotValue)})</span>
          </label>
        </div>
      </div>
    )
  }

  // Scenario D — both changed, radio choice
  return (
    <div style={{ padding: '10px 0', borderBottom: '1px solid rgba(155,111,208,0.1)' }}>
      <div style={{ fontSize: 11, color: '#9CA59E', marginBottom: 6 }}>{field} — Beide Seiten geändert</div>
      <div style={{ display: 'flex', flexDirection: 'column', gap: 4 }}>
        {[
          { id: 'snapshot', label: 'Snapshot (Basis)', value: snapshotValue },
          { id: 'local', label: 'Lokal (meine Version)', value: localValue },
          { id: 'online', label: 'Online (WordPress)', value: onlineValue },
        ].map(opt => (
          <label key={opt.id} style={{ display: 'flex', gap: 8, cursor: 'pointer', alignItems: 'flex-start' }}>
            <input
              type="radio"
              name={`field-${field}`}
              value={opt.id}
              checked={resolution?.choice === opt.id}
              onChange={() => onChange({ choice: opt.id, value: opt.value })}
              style={{ marginTop: 2 }}
            />
            <span style={{ fontSize: 12 }}>
              <span style={{ color: '#9CA59E' }}>{opt.label}: </span>
              <span style={{ color: '#EAE4D6' }}>{fmt(opt.value)}</span>
            </span>
          </label>
        ))}
      </div>
    </div>
  )
}

export default function SyncConflictDialog({ conflicts, onResolve, onClose }) {
  const [index, setIndex] = useState(0)
  const [resolutions, setResolutions] = useState({})

  const current = conflicts[index]
  if (!current) return null

  const setFieldResolution = (field, res) => {
    setResolutions(r => ({
      ...r,
      [current.productId]: {
        ...(r[current.productId] || {}),
        [field]: res
      }
    }))
  }

  const canAdvance = current.fields.every(f => {
    const res = resolutions[current.productId]?.[f.field]
    if (f.scenario === 'C') return true
    return res && res.choice
  })

  const confirm = () => {
    if (index < conflicts.length - 1) {
      setIndex(i => i + 1)
    } else {
      onResolve(resolutions)
    }
  }

  return (
    <div style={{
      position: 'fixed', inset: 0, background: 'rgba(0,0,0,0.65)', zIndex: 500,
      display: 'flex', alignItems: 'center', justifyContent: 'center'
    }}>
      <div style={{
        background: '#2F3B35', border: '1px solid rgba(155,111,208,0.3)',
        borderRadius: 8, width: 540, maxHeight: '80vh', display: 'flex', flexDirection: 'column',
        boxShadow: '0 20px 60px rgba(0,0,0,0.6)'
      }}>
        <div style={{ padding: '16px 20px', borderBottom: '1px solid rgba(155,111,208,0.15)' }}>
          <div style={{ fontSize: 11, color: '#9CA59E', marginBottom: 4 }}>
            Konflikt {index + 1} von {conflicts.length}
          </div>
          <h3 style={{ fontSize: 15, color: '#EAE4D6', fontWeight: 600 }}>
            {current.productName}
          </h3>
          <div style={{ fontSize: 12, color: '#9CA59E', marginTop: 2 }}>
            {current.fields.length} Feld{current.fields.length !== 1 ? 'er' : ''} mit Konflikten
          </div>
        </div>

        <div style={{ flex: 1, overflowY: 'auto', padding: '0 20px' }}>
          {current.fields.map(f => (
            <FieldConflict
              key={f.field}
              field={f.field}
              conflict={f}
              resolution={resolutions[current.productId]?.[f.field]}
              onChange={(res) => setFieldResolution(f.field, res)}
            />
          ))}
        </div>

        <div style={{ padding: '14px 20px', borderTop: '1px solid rgba(155,111,208,0.15)', display: 'flex', gap: 8, justifyContent: 'flex-end' }}>
          <button className="btn-secondary" onClick={onClose}>Abbrechen</button>
          <button className="btn-primary" onClick={confirm} disabled={!canAdvance}>
            {index < conflicts.length - 1 ? 'Weiter →' : 'Bestätigen & pushen'}
          </button>
        </div>
      </div>
    </div>
  )
}
