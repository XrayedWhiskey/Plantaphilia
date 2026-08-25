import React, { useState, useEffect } from 'react'

// Search-driven multi-select for the free (unbound) category tree — assigns
// a product (or, in bulk mode with no entityId, just tracks a local
// selection for the caller to apply itself) to arbitrary categories.
// Gattung/Art/Kultivar-bound and protected system categories are excluded:
// those are derived automatically, never manually assignable.
export default function CategoryAssignPicker({ entityId, initialIds, onChange }) {
  const [categories, setCategories] = useState([])
  const [selectedIds, setSelectedIds] = useState(initialIds || [])
  const [search, setSearch] = useState('')

  useEffect(() => { window.api.getCategories().then(cats => setCategories(cats || [])) }, [])

  // See TagPool.jsx for why onChange also fires here: callers that persist
  // the picker's selection on save need the already-assigned categories
  // reflected in their own state too, or an untouched picker would look
  // empty and wipe existing assignments.
  useEffect(() => {
    if (entityId) window.api.getProductCategories(entityId).then(cats => {
      const ids = cats.map(c => c.id)
      setSelectedIds(ids)
      onChange && onChange(ids)
    })
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [entityId])

  const byId = new Map(categories.map(c => [c.id, c]))
  const pathFor = (cat) => {
    const parts = []
    let cur = cat
    while (cur) {
      if (cur.name) parts.unshift(cur.name)
      cur = cur.parent_id ? byId.get(cur.parent_id) : null
    }
    return parts.join(' › ')
  }

  const assignable = categories.filter(c => !c.gattung_id && !c.art_id && !c.kultivar && !c.protected)
  const q = search.trim().toLowerCase()
  const results = q ? assignable.filter(c => pathFor(c).toLowerCase().includes(q)) : []

  const toggle = (id) => {
    const next = selectedIds.includes(id) ? selectedIds.filter(x => x !== id) : [...selectedIds, id]
    setSelectedIds(next)
    onChange && onChange(next)
    if (entityId) window.api.setProductCategories(entityId, next)
  }

  return (
    <div>
      <input
        className="input"
        placeholder="Kategorie suchen..."
        value={search}
        onChange={e => setSearch(e.target.value)}
      />

      {q && (
        <div style={{ marginTop: 6, maxHeight: 180, overflowY: 'auto', border: '1px solid rgba(155,111,208,0.15)', borderRadius: 4 }}>
          {results.length === 0 && (
            <div style={{ padding: 8, fontSize: 12, color: '#9CA59E', fontStyle: 'italic' }}>Keine Treffer</div>
          )}
          {results.map(c => {
            const active = selectedIds.includes(c.id)
            return (
              <div key={c.id} onClick={() => toggle(c.id)} style={{
                padding: '6px 10px', fontSize: 12, cursor: 'pointer',
                color: active ? '#B8A8D8' : '#C8C0AF',
                background: active ? 'rgba(155,111,208,0.15)' : 'transparent',
                borderBottom: '1px solid rgba(155,111,208,0.08)'
              }}>
                {active ? '✓ ' : ''}{pathFor(c)}
              </div>
            )
          })}
        </div>
      )}

      <div style={{ display: 'flex', flexWrap: 'wrap', gap: 6, marginTop: selectedIds.length > 0 ? 10 : 0 }}>
        {selectedIds.length === 0 && !q && (
          <span style={{ fontSize: 12, color: '#9CA59E', fontStyle: 'italic' }}>Keine Kategorien zugeordnet</span>
        )}
        {selectedIds.map(id => {
          const cat = byId.get(id)
          if (!cat) return null
          return (
            <span key={id} style={{
              display: 'inline-flex', alignItems: 'center', gap: 4, padding: '3px 8px 3px 10px',
              borderRadius: 20, fontSize: 11, background: 'rgba(155,111,208,0.2)', color: '#B8A8D8'
            }}>
              {pathFor(cat)}
              <span onClick={() => toggle(id)} style={{ cursor: 'pointer', opacity: 0.6, fontSize: 10 }}>✕</span>
            </span>
          )
        })}
      </div>
    </div>
  )
}
