import React, { useState, useEffect, useRef } from 'react'

// Like TagScopePicker, but one level deeper: Gattung → Art → Kultivar. Kultivar
// has no blueprint catalog (free text on products), so its "list" is just the
// distinct values already used under the selected Art, editable as free text.
export default function CategoryBindingPicker({ gattungId, artId, kultivar, onChange }) {
  const [open, setOpen] = useState(false)
  const [gattungen, setGattungen] = useState([])
  const [arten, setArten] = useState([])
  const [kultivars, setKultivars] = useState([])
  const [kultivarInput, setKultivarInput] = useState(kultivar || '')
  const [expandedGattung, setExpandedGattung] = useState(() => new Set())
  const [expandedArt, setExpandedArt] = useState(() => new Set())
  const ref = useRef(null)

  useEffect(() => {
    Promise.all([window.api.getGattungen(), window.api.getArten()]).then(([g, a]) => {
      setGattungen(g)
      setArten(a)
    })
  }, [])

  useEffect(() => {
    if (artId) window.api.getKultivarsForArt(artId).then(setKultivars)
    else setKultivars([])
  }, [artId])

  useEffect(() => {
    const handler = (e) => { if (ref.current && !ref.current.contains(e.target)) setOpen(false) }
    document.addEventListener('mousedown', handler)
    return () => document.removeEventListener('mousedown', handler)
  }, [])

  useEffect(() => {
    if (gattungId) setExpandedGattung(prev => { const next = new Set(prev); next.add(gattungId); return next })
    if (artId) setExpandedArt(prev => { const next = new Set(prev); next.add(artId); return next })
  }, [gattungId, artId])

  const toggle = (setFn, id) => setFn(prev => {
    const next = new Set(prev)
    next.has(id) ? next.delete(id) : next.add(id)
    return next
  })

  const select = (g, a, k) => {
    onChange(g, a, k)
    setKultivarInput(k || '')
    if (!k) setOpen(false)
  }

  const confirmKultivar = () => {
    if (!kultivarInput.trim()) return
    select(gattungId, artId, kultivarInput.trim())
    setOpen(false)
  }

  const label = () => {
    if (!gattungId) return '— Frei benannt —'
    const g = gattungen.find(x => x.id === gattungId)
    if (!g) return '— Frei benannt —'
    if (!artId) return g.name
    const a = arten.find(x => x.id === artId)
    if (!a) return g.name
    return kultivar ? `${g.name} · ${a.name} · ${kultivar}` : `${g.name} · ${a.name}`
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
          maxHeight: 320, overflowY: 'auto'
        }}>
          <div onClick={() => select(null, null, null)} style={{
            padding: '7px 12px', cursor: 'pointer', fontSize: 13,
            background: !gattungId ? 'rgba(155,111,208,0.15)' : 'transparent',
            color: !gattungId ? '#B8A8D8' : '#EAE4D6',
            borderBottom: '1px solid rgba(155,111,208,0.08)'
          }}>— Frei benannt —</div>

          {gattungen.map(g => {
            const childArten = arten.filter(a => a.gattung_id === g.id)
            const gExpanded = expandedGattung.has(g.id)
            const isSelectedG = gattungId === g.id && !artId
            return (
              <div key={g.id}>
                <div style={{ display: 'flex', alignItems: 'center' }}>
                  <span onClick={(e) => { e.stopPropagation(); toggle(setExpandedGattung, g.id) }} style={{
                    padding: '7px 4px 7px 10px', cursor: 'pointer', color: '#9CA59E', fontSize: 10, width: 20, flexShrink: 0
                  }}>{childArten.length > 0 ? (gExpanded ? '▾' : '▸') : ' '}</span>
                  <div onClick={() => select(g.id, null, null)} style={{
                    flex: 1, padding: '7px 8px 7px 2px', cursor: 'pointer', fontSize: 13,
                    background: isSelectedG ? 'rgba(155,111,208,0.15)' : 'transparent',
                    color: isSelectedG ? '#B8A8D8' : '#EAE4D6'
                  }}>{g.name}</div>
                </div>
                {gExpanded && childArten.map(a => {
                  const aExpanded = expandedArt.has(a.id)
                  const isSelectedA = gattungId === g.id && artId === a.id && !kultivar
                  const artKultivars = artId === a.id ? kultivars : []
                  return (
                    <div key={a.id}>
                      <div style={{ display: 'flex', alignItems: 'center' }}>
                        <span onClick={(e) => { e.stopPropagation(); select(g.id, a.id, null); toggle(setExpandedArt, a.id) }} style={{
                          padding: '6px 4px 6px 24px', cursor: 'pointer', color: '#9CA59E', fontSize: 10, width: 20, flexShrink: 0
                        }}>{aExpanded ? '▾' : '▸'}</span>
                        <div onClick={() => select(g.id, a.id, null)} style={{
                          flex: 1, padding: '6px 8px 6px 2px', cursor: 'pointer', fontSize: 12,
                          background: isSelectedA ? 'rgba(155,111,208,0.15)' : 'transparent',
                          color: isSelectedA ? '#B8A8D8' : '#C8C0AF'
                        }}>{a.name}</div>
                      </div>
                      {aExpanded && artId === a.id && (
                        <div style={{ padding: '6px 12px 8px 46px' }}>
                          {artKultivars.map(k => (
                            <div key={k} onClick={() => select(g.id, a.id, k)} style={{
                              padding: '4px 8px', cursor: 'pointer', fontSize: 12, borderRadius: 3,
                              background: kultivar === k ? 'rgba(155,111,208,0.15)' : 'transparent',
                              color: kultivar === k ? '#B8A8D8' : '#C8C0AF'
                            }}>{k}</div>
                          ))}
                          <div style={{ display: 'flex', gap: 6, marginTop: 4 }}>
                            <input
                              className="input"
                              style={{ fontSize: 12, flex: 1 }}
                              placeholder="Kultivar..."
                              value={kultivarInput}
                              onChange={e => setKultivarInput(e.target.value)}
                              onClick={e => e.stopPropagation()}
                              onKeyDown={e => { if (e.key === 'Enter') { e.preventDefault(); confirmKultivar() } }}
                            />
                            <button type="button" className="btn-primary" style={{ fontSize: 11, padding: '4px 10px' }}
                              onClick={(e) => { e.stopPropagation(); confirmKultivar() }}>✓</button>
                          </div>
                        </div>
                      )}
                    </div>
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
