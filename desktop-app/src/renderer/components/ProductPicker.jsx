import React, { useState, useEffect, useCallback } from 'react'

// Modal for assigning/removing products on a free-named category — the
// membership itself lives entirely in product_categories, addressed from the
// category side (per product design decision: no more per-product tag UI).
export default function ProductPicker({ categoryId, categoryLabel, onClose }) {
  const [assigned, setAssigned] = useState([])
  const [search, setSearch] = useState('')
  const [results, setResults] = useState([])
  const [loading, setLoading] = useState(false)

  const loadAssigned = useCallback(async () => {
    const rows = await window.api.getCategoryProducts(categoryId)
    setAssigned(rows || [])
  }, [categoryId])

  useEffect(() => { loadAssigned() }, [loadAssigned])

  useEffect(() => {
    const t = setTimeout(async () => {
      if (!search.trim()) { setResults([]); return }
      setLoading(true)
      try {
        const rows = await window.api.getProducts(search)
        setResults(rows || [])
      } finally { setLoading(false) }
    }, 250)
    return () => clearTimeout(t)
  }, [search])

  const assignedIds = new Set(assigned.map(p => p.id))

  const add = async (productId) => {
    await window.api.addProductToCategory(categoryId, productId)
    await loadAssigned()
  }
  const remove = async (productId) => {
    await window.api.removeProductFromCategory(categoryId, productId)
    await loadAssigned()
  }

  const productLabel = (p) => {
    const genus = [p.gattung, p.art].filter(Boolean).join(' ')
    return p.kultivar ? `'${p.kultivar}'${genus ? ' — ' + genus : ''}` : (p.name || genus || '(unbenannt)')
  }

  return (
    <div style={{
      position: 'fixed', inset: 0, background: 'rgba(0,0,0,0.6)', zIndex: 400,
      display: 'flex', alignItems: 'flex-start', justifyContent: 'center',
      paddingTop: 40, paddingBottom: 40, overflow: 'auto'
    }}>
      <div style={{
        background: '#25302B', border: '1px solid rgba(155,111,208,0.2)',
        borderRadius: 8, width: '100%', maxWidth: 560,
        boxShadow: '0 24px 80px rgba(0,0,0,0.7)', display: 'flex', flexDirection: 'column'
      }}>
        <div style={{
          padding: '14px 20px', background: '#1A231F',
          borderBottom: '1px solid rgba(155,111,208,0.15)',
          display: 'flex', justifyContent: 'space-between', alignItems: 'center',
          borderRadius: '8px 8px 0 0'
        }}>
          <h2 style={{ fontSize: 15, fontWeight: 700, color: '#EAE4D6' }}>Artikel — {categoryLabel}</h2>
          <button type="button" onClick={onClose} style={{ background: 'none', border: 'none', color: '#9CA59E', cursor: 'pointer', fontSize: 18, lineHeight: 1 }}>✕</button>
        </div>

        <div style={{ padding: 20 }}>
          <input
            className="input"
            placeholder="Produkt suchen (Name, Gattung, Art, SKU...)"
            value={search}
            onChange={e => setSearch(e.target.value)}
            autoFocus
          />

          {search.trim() && (
            <div style={{ marginTop: 10, maxHeight: 220, overflowY: 'auto', border: '1px solid rgba(155,111,208,0.12)', borderRadius: 4 }}>
              {loading && <div style={{ padding: 10, fontSize: 12, color: '#9CA59E' }}>Suche...</div>}
              {!loading && results.length === 0 && <div style={{ padding: 10, fontSize: 12, color: '#9CA59E' }}>Keine Treffer</div>}
              {!loading && results.map(p => (
                <div key={p.id} style={{
                  display: 'flex', justifyContent: 'space-between', alignItems: 'center',
                  padding: '7px 12px', borderBottom: '1px solid rgba(155,111,208,0.08)', fontSize: 13, color: '#EAE4D6'
                }}>
                  <span>{productLabel(p)}</span>
                  {assignedIds.has(p.id) ? (
                    <span style={{ fontSize: 11, color: '#4CAF7D' }}>✓ zugeordnet</span>
                  ) : (
                    <button type="button" className="btn-secondary" style={{ fontSize: 11, padding: '3px 10px' }} onClick={() => add(p.id)}>+ Hinzufügen</button>
                  )}
                </div>
              ))}
            </div>
          )}

          <div style={{ marginTop: 16, fontSize: 11, color: '#9CA59E', textTransform: 'uppercase', letterSpacing: 1 }}>
            Zugeordnet ({assigned.length})
          </div>
          <div style={{ marginTop: 8, display: 'flex', flexDirection: 'column', gap: 4 }}>
            {assigned.length === 0 && <div style={{ fontSize: 12, color: '#9CA59E', fontStyle: 'italic' }}>Noch keine Artikel zugeordnet</div>}
            {assigned.map(p => (
              <div key={p.id} style={{
                display: 'flex', justifyContent: 'space-between', alignItems: 'center',
                padding: '6px 10px', background: 'rgba(155,111,208,0.07)', borderRadius: 4, fontSize: 13, color: '#EAE4D6'
              }}>
                <span>{productLabel(p)}</span>
                <span onClick={() => remove(p.id)} title="Entfernen" style={{ cursor: 'pointer', color: '#9CA59E', opacity: 0.7, fontSize: 12 }}>✕</span>
              </div>
            ))}
          </div>
        </div>

        <div style={{ padding: '12px 20px', borderTop: '1px solid rgba(155,111,208,0.1)', display: 'flex', justifyContent: 'flex-end' }}>
          <button type="button" className="btn-primary" onClick={onClose}>Fertig</button>
        </div>
      </div>
    </div>
  )
}
