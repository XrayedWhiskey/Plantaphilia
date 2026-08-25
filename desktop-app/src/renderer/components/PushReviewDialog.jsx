import React, { useState } from 'react'

function fmt(v) {
  if (v === null || v === undefined) return '—'
  if (typeof v === 'object') return JSON.stringify(v)
  return String(v)
}

function ConflictFields({ productId, fields, resolutions, onChange }) {
  return (
    <div style={{ marginTop: 8, padding: '8px 10px', background: 'rgba(0,0,0,0.2)', borderRadius: 4 }}>
      {fields.map(f => (
        <div key={f.field} style={{ marginBottom: 10 }}>
          <div style={{ fontSize: 11, color: '#9CA59E', marginBottom: 4, fontWeight: 600 }}>{f.field}</div>
          {['local', 'online'].map(side => {
            const val = side === 'local' ? f.localValue : f.onlineValue
            const checked = resolutions[productId]?.[f.field]?.choice === side
            return (
              <label key={side} style={{ display: 'flex', gap: 8, fontSize: 12, cursor: 'pointer', marginBottom: 3, alignItems: 'flex-start' }}>
                <input type="radio" name={`${productId}-${f.field}`} checked={checked}
                  onChange={() => onChange(productId, f.field, { choice: side, value: val })}
                  style={{ marginTop: 2, flexShrink: 0 }} />
                <span>
                  <span style={{ color: side === 'local' ? '#B8A8D8' : '#4CAF7D' }}>{side === 'local' ? 'Lokal: ' : 'Online: '}</span>
                  <span style={{ color: '#EAE4D6' }}>{fmt(val)}</span>
                </span>
              </label>
            )
          })}
        </div>
      ))}
    </div>
  )
}

export default function PushReviewDialog({ data, onPush, onCancel }) {
  const { ready = [], conflicts = [], newProducts = [] } = data

  const allIds = [
    ...newProducts.map(p => p.id),
    ...ready.map(p => p.id),
    ...conflicts.map(c => c.productId),
  ]

  const [checked, setChecked] = useState(() => new Set(allIds))
  const [resolutions, setResolutions] = useState({})
  const [expanded, setExpanded] = useState({})

  const setRes = (productId, field, res) => {
    setResolutions(r => ({ ...r, [productId]: { ...(r[productId] || {}), [field]: res } }))
  }

  const isConflictResolved = (c) =>
    c.fields.every(f => resolutions[c.productId]?.[f.field]?.choice)

  const toggle = (id) => setChecked(s => {
    const n = new Set(s)
    n.has(id) ? n.delete(id) : n.add(id)
    return n
  })

  const totalChecked = checked.size
  const canPush = totalChecked > 0 &&
    conflicts.every(c => !checked.has(c.productId) || isConflictResolved(c))

  const handlePush = () => {
    onPush({ confirmedIds: [...checked], resolutions })
  }

  const sections = [
    {
      title: 'Neue Produkte',
      color: '#9B6FD0',
      items: newProducts.map(p => ({ id: p.id, name: p.name })),
    },
    {
      title: 'Lokal geändert',
      color: '#4CAF7D',
      items: ready.map(p => ({ id: p.id, name: p.name })),
    },
    {
      title: 'Konflikte',
      color: '#E57373',
      items: conflicts.map(c => ({ id: c.productId, name: c.productName, conflictData: c })),
      isConflict: true,
    },
  ].filter(s => s.items.length > 0)

  return (
    <div style={{ position: 'fixed', inset: 0, background: 'rgba(0,0,0,0.7)', zIndex: 500, display: 'flex', alignItems: 'center', justifyContent: 'center' }}>
      <div style={{ width: 580, maxHeight: '82vh', background: '#2F3B35', border: '1px solid rgba(155,111,208,0.3)', borderRadius: 8, display: 'flex', flexDirection: 'column', boxShadow: '0 20px 60px rgba(0,0,0,0.6)' }}>
        {/* Header */}
        <div style={{ padding: '16px 20px', borderBottom: '1px solid rgba(155,111,208,0.15)', background: '#1A231F', borderRadius: '8px 8px 0 0' }}>
          <div style={{ fontSize: 15, fontWeight: 700, color: '#EAE4D6' }}>Push-Vorschau</div>
          <div style={{ fontSize: 12, color: '#9CA59E', marginTop: 3 }}>
            {totalChecked} von {allIds.length} Produkt{allIds.length !== 1 ? 'en' : ''} ausgewählt
          </div>
        </div>

        {/* Body */}
        <div style={{ flex: 1, overflowY: 'auto', padding: '14px 20px' }}>
          {allIds.length === 0 ? (
            <div style={{ color: '#9CA59E', fontSize: 13, textAlign: 'center', padding: '24px 0' }}>
              Keine lokalen Änderungen vorhanden.
            </div>
          ) : (
            sections.map(section => (
              <div key={section.title} style={{ marginBottom: 18 }}>
                <div style={{ fontSize: 11, fontWeight: 700, color: section.color, textTransform: 'uppercase', letterSpacing: 1, marginBottom: 8 }}>
                  {section.title} ({section.items.length})
                </div>
                {section.items.map(item => {
                  const isConflict = !!item.conflictData
                  const resolved = isConflict ? isConflictResolved(item.conflictData) : true
                  const isExpanded = !!expanded[item.id]
                  const needsResolution = isConflict && checked.has(item.id) && !resolved

                  return (
                    <div key={item.id} style={{ marginBottom: 6, padding: '8px 10px', background: 'rgba(0,0,0,0.15)', borderRadius: 5, border: `1px solid ${needsResolution ? 'rgba(229,115,115,0.35)' : 'transparent'}` }}>
                      <div style={{ display: 'flex', alignItems: 'center', gap: 8 }}>
                        <input type="checkbox" checked={checked.has(item.id)} onChange={() => toggle(item.id)} />
                        <span style={{ fontSize: 13, color: '#EAE4D6', flex: 1 }}>{item.name}</span>
                        {isConflict && (
                          <button onClick={() => setExpanded(e => ({ ...e, [item.id]: !e[item.id] }))}
                            style={{ fontSize: 11, padding: '2px 8px', borderRadius: 3, cursor: 'pointer', background: 'transparent', border: `1px solid ${resolved ? 'rgba(76,175,125,0.4)' : 'rgba(229,115,115,0.4)'}`, color: resolved ? '#4CAF7D' : '#E57373' }}>
                            {resolved ? '✓ Gelöst' : `${item.conflictData.fields.length} Konflikt${item.conflictData.fields.length !== 1 ? 'e' : ''}`} {isExpanded ? '▲' : '▼'}
                          </button>
                        )}
                      </div>
                      {isConflict && isExpanded && (
                        <ConflictFields
                          productId={item.id}
                          fields={item.conflictData.fields}
                          resolutions={resolutions}
                          onChange={setRes}
                        />
                      )}
                    </div>
                  )
                })}
              </div>
            ))
          )}
        </div>

        {/* Footer */}
        <div style={{ padding: '12px 20px', borderTop: '1px solid rgba(155,111,208,0.15)', display: 'flex', gap: 8, justifyContent: 'flex-end', background: '#1A231F', borderRadius: '0 0 8px 8px' }}>
          <button className="btn-secondary" onClick={onCancel}>Abbrechen</button>
          <button className="btn-primary" onClick={handlePush} disabled={!canPush}>
            ↑ Pushen ({totalChecked})
          </button>
        </div>
      </div>
    </div>
  )
}
