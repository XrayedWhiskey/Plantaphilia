import React, { useState, useRef, useEffect } from 'react'

export default function ComboSelect({ options = [], value, onChange, placeholder = 'Auswählen...', onAddNew }) {
  const [open, setOpen] = useState(false)
  const [search, setSearch] = useState('')
  const [newValue, setNewValue] = useState('')
  const [addingNew, setAddingNew] = useState(false)
  const ref = useRef(null)

  useEffect(() => {
    const handler = (e) => { if (ref.current && !ref.current.contains(e.target)) setOpen(false) }
    document.addEventListener('mousedown', handler)
    return () => document.removeEventListener('mousedown', handler)
  }, [])

  const filtered = options.filter(o =>
    (typeof o === 'string' ? o : o.label).toLowerCase().includes(search.toLowerCase())
  )

  const getLabel = (v) => {
    const opt = options.find(o => (typeof o === 'string' ? o : o.value) === v)
    return opt ? (typeof opt === 'string' ? opt : opt.label) : v
  }

  const select = (v) => {
    onChange(v)
    setOpen(false)
    setSearch('')
  }

  const handleAddNew = () => {
    if (!newValue.trim()) return
    if (onAddNew) onAddNew(newValue.trim())
    onChange(newValue.trim())
    setNewValue('')
    setAddingNew(false)
    setOpen(false)
  }

  return (
    <div ref={ref} style={{ position: 'relative', minWidth: 0 }}>
      <button
        type="button"
        onClick={() => { setOpen(o => !o); setSearch('') }}
        className="input"
        style={{ textAlign: 'left', cursor: 'pointer', display: 'flex', justifyContent: 'space-between', alignItems: 'center', width: '100%', minWidth: 0, overflow: 'hidden' }}
      >
        <span style={{ color: value ? '#EAE4D6' : '#9CA59E', overflow: 'hidden', textOverflow: 'ellipsis', whiteSpace: 'nowrap', minWidth: 0 }}>{value ? getLabel(value) : placeholder}</span>
        <span style={{ color: '#9CA59E', fontSize: 10, flexShrink: 0, marginLeft: 4 }}>▼</span>
      </button>

      {open && (
        <div style={{
          position: 'absolute', top: '100%', left: 0, right: 0, zIndex: 100,
          background: '#2F3B35', border: '1px solid rgba(155,111,208,0.3)',
          borderRadius: 4, boxShadow: '0 8px 24px rgba(0,0,0,0.5)', marginTop: 2
        }}>
          <div style={{ padding: 8, borderBottom: '1px solid rgba(155,111,208,0.1)' }}>
            <input
              className="input"
              style={{ fontSize: 12 }}
              placeholder="Suchen..."
              value={search}
              onChange={e => setSearch(e.target.value)}
              onKeyDown={e => { if (e.key === 'Enter') e.preventDefault() }}
              autoFocus
            />
          </div>
          <div style={{ maxHeight: 200, overflowY: 'auto' }}>
            {value && (
              <div onClick={() => select('')} style={{
                padding: '7px 12px', cursor: 'pointer', color: '#9CA59E', fontSize: 12,
                borderBottom: '1px solid rgba(155,111,208,0.08)'
              }}>— Keine Auswahl —</div>
            )}
            {filtered.map((o, i) => {
              const v = typeof o === 'string' ? o : o.value
              const l = typeof o === 'string' ? o : o.label
              return (
                <div key={i} onClick={() => select(v)} style={{
                  padding: '7px 12px', cursor: 'pointer',
                  background: v === value ? 'rgba(155,111,208,0.15)' : 'transparent',
                  color: v === value ? '#B8A8D8' : '#EAE4D6', fontSize: 13,
                  transition: 'background 0.1s'
                }}
                  onMouseEnter={e => e.currentTarget.style.background = 'rgba(155,111,208,0.1)'}
                  onMouseLeave={e => e.currentTarget.style.background = v === value ? 'rgba(155,111,208,0.15)' : 'transparent'}
                >{l}</div>
              )
            })}
            {filtered.length === 0 && <div style={{ padding: '7px 12px', color: '#9CA59E', fontSize: 12 }}>Keine Einträge</div>}
          </div>
          {onAddNew && (
            <div style={{ padding: 8, borderTop: '1px solid rgba(155,111,208,0.1)' }}>
              {addingNew ? (
                <div style={{ display: 'flex', gap: 6 }}>
                  <input
                    className="input"
                    style={{ fontSize: 12, flex: 1 }}
                    placeholder="Neuer Wert..."
                    value={newValue}
                    onChange={e => setNewValue(e.target.value)}
                    onKeyDown={e => { if (e.key === 'Enter') { e.preventDefault(); handleAddNew() } }}
                    autoFocus
                  />
                  <button type="button" className="btn-primary" style={{ fontSize: 11, padding: '4px 10px' }} onClick={handleAddNew}>+</button>
                  <button type="button" className="btn-secondary" style={{ fontSize: 11, padding: '4px 10px' }} onClick={() => setAddingNew(false)}>✕</button>
                </div>
              ) : (
                <button type="button" onClick={() => setAddingNew(true)} style={{
                  background: 'none', border: 'none', color: '#9B6FD0', cursor: 'pointer',
                  fontSize: 12, width: '100%', textAlign: 'left', padding: '2px 4px'
                }}>+ Neu hinzufügen</button>
              )}
            </div>
          )}
        </div>
      )}
    </div>
  )
}
