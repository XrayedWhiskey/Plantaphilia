import React, { useState, useEffect, useRef } from 'react'

// Hierarchical scope picker: Global, or a Gattung (expandable to reveal its
// Arten), or one specific Art. Lists ALL Gattungen/Arten regardless of any
// surrounding context — unlike the old context-limited buttons, a category
// can be scoped to any Gattung/Art in the catalog, not just the one the user
// happens to be looking at right now.
export default function TagScopePicker({ gattungId, artId, onChange }) {
  const [open, setOpen] = useState(false)
  const [gattungen, setGattungen] = useState([])
  const [arten, setArten] = useState([])
  const [expanded, setExpanded] = useState(() => new Set())
  const ref = useRef(null)

  useEffect(() => {
    Promise.all([window.api.getGattungen(), window.api.getArten()]).then(([g, a]) => {
      setGattungen(g)
      setArten(a)
    })
  }, [])

  useEffect(() => {
    const handler = (e) => { if (ref.current && !ref.current.contains(e.target)) setOpen(false) }
    document.addEventListener('mousedown', handler)
    return () => document.removeEventListener('mousedown', handler)
  }, [])

  // Auto-expand the currently selected Gattung so a selected Art stays visible.
  useEffect(() => {
    if (gattungId) setExpanded(prev => { const next = new Set(prev); next.add(gattungId); return next })
  }, [gattungId])

  const toggleExpanded = (id) => {
    setExpanded(prev => {
      const next = new Set(prev)
      next.has(id) ? next.delete(id) : next.add(id)
      return next
    })
  }

  const select = (g, a) => {
    onChange(g, a)
    setOpen(false)
  }

  const label = () => {
    if (!gattungId) return '🌐 Global'
    const g = gattungen.find(x => x.id === gattungId)
    if (!g) return '🌐 Global'
    if (!artId) return g.name
    const a = arten.find(x => x.id === artId)
    return a ? `${g.name} · ${a.name}` : g.name
  }

  return (
    <div ref={ref} style={{ position: 'relative', minWidth: 0 }}>
      <button type="button" onClick={() => setOpen(o => !o)} className="input"
        style={{ textAlign: 'left', cursor: 'pointer', display: 'flex', justifyContent: 'space-between', alignItems: 'center', width: '100%', minWidth: 0 }}>
        <span style={{ overflow: 'hidden', textOverflow: 'ellipsis', whiteSpace: 'nowrap' }}>{label()}</span>
        <span style={{ color: '#9CA59E', fontSize: 10, flexShrink: 0, marginLeft: 4 }}>▼</span>
      </button>

      {open && (
        <div style={{
          position: 'absolute', top: '100%', left: 0, right: 0, zIndex: 100,
          background: '#2F3B35', border: '1px solid rgba(155,111,208,0.3)',
          borderRadius: 4, boxShadow: '0 8px 24px rgba(0,0,0,0.5)', marginTop: 2,
          maxHeight: 260, overflowY: 'auto'
        }}>
          <div onClick={() => select(null, null)} style={{
            padding: '7px 12px', cursor: 'pointer', fontSize: 13,
            background: !gattungId ? 'rgba(155,111,208,0.15)' : 'transparent',
            color: !gattungId ? '#B8A8D8' : '#EAE4D6',
            borderBottom: '1px solid rgba(155,111,208,0.08)'
          }}>🌐 Global</div>

          {gattungen.map(g => {
            const childArten = arten.filter(a => a.gattung_id === g.id)
            const isExpanded = expanded.has(g.id)
            const isSelectedG = gattungId === g.id && !artId
            return (
              <div key={g.id}>
                <div style={{ display: 'flex', alignItems: 'center' }}>
                  <span onClick={(e) => { e.stopPropagation(); toggleExpanded(g.id) }} style={{
                    padding: '7px 4px 7px 10px', cursor: 'pointer', color: '#9CA59E', fontSize: 10, width: 20, flexShrink: 0
                  }}>{childArten.length > 0 ? (isExpanded ? '▾' : '▸') : ' '}</span>
                  <div onClick={() => select(g.id, null)} style={{
                    flex: 1, padding: '7px 8px 7px 2px', cursor: 'pointer', fontSize: 13,
                    background: isSelectedG ? 'rgba(155,111,208,0.15)' : 'transparent',
                    color: isSelectedG ? '#B8A8D8' : '#EAE4D6'
                  }}>{g.name}</div>
                </div>
                {isExpanded && childArten.map(a => {
                  const isSelectedA = gattungId === g.id && artId === a.id
                  return (
                    <div key={a.id} onClick={() => select(g.id, a.id)} style={{
                      padding: '6px 12px 6px 34px', cursor: 'pointer', fontSize: 12,
                      background: isSelectedA ? 'rgba(155,111,208,0.15)' : 'transparent',
                      color: isSelectedA ? '#B8A8D8' : '#C8C0AF'
                    }}>{a.name}</div>
                  )
                })}
              </div>
            )
          })}
          {gattungen.length === 0 && (
            <div style={{ padding: '7px 12px', color: '#9CA59E', fontSize: 12 }}>Keine Gattungen angelegt</div>
          )}
        </div>
      )}
    </div>
  )
}
