import React, { useState, useEffect, useCallback } from 'react'
import ProductForm from './ProductForm.jsx'

// Read-only view of a category's products, including all descendant
// categories' products and derived (Gattung/Art/Kultivar-bound) membership —
// see getCategoryProductsRecursive in database.js. Unlike ProductPicker
// (assignment UI for a single free-named category), this is just for looking.
export default function CategoryProductsModal({ categoryId, categoryLabel, onClose }) {
  const [products, setProducts] = useState([])
  const [loading, setLoading] = useState(true)
  const [editingId, setEditingId] = useState(null)

  const load = useCallback(async () => {
    setLoading(true)
    try {
      const rows = await window.api.getCategoryProductsRecursive(categoryId)
      setProducts(rows || [])
    } finally {
      setLoading(false)
    }
  }, [categoryId])

  useEffect(() => { load() }, [load])

  const productLabel = (p) => {
    const genus = [p.gattung, p.art].filter(Boolean).join(' ')
    return p.kultivar ? `'${p.kultivar}'${genus ? ' — ' + genus : ''}` : (p.name || genus || '(unbenannt)')
  }

  if (editingId) {
    return (
      <ProductForm
        productId={editingId}
        onClose={() => setEditingId(null)}
        onSaved={() => { setEditingId(null); load() }}
      />
    )
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
          <h2 style={{ fontSize: 15, fontWeight: 700, color: '#EAE4D6' }}>Produkte — {categoryLabel}</h2>
          <button type="button" onClick={onClose} style={{ background: 'none', border: 'none', color: '#9CA59E', cursor: 'pointer', fontSize: 18, lineHeight: 1 }}>✕</button>
        </div>

        <div style={{ padding: 20 }}>
          <div style={{ fontSize: 11, color: '#9CA59E', textTransform: 'uppercase', letterSpacing: 1, marginBottom: 8 }}>
            {loading ? 'Lädt...' : `${products.length} Produkt${products.length === 1 ? '' : 'e'} (inkl. Unterkategorien)`}
          </div>
          <div style={{ display: 'flex', flexDirection: 'column', gap: 4, maxHeight: 420, overflowY: 'auto' }}>
            {!loading && products.length === 0 && (
              <div style={{ fontSize: 12, color: '#9CA59E', fontStyle: 'italic' }}>Keine Produkte in dieser Kategorie.</div>
            )}
            {products.map(p => (
              <div key={p.id} onClick={() => setEditingId(p.id)} style={{
                padding: '7px 10px', background: 'rgba(155,111,208,0.07)', borderRadius: 4,
                fontSize: 13, color: '#EAE4D6', cursor: 'pointer'
              }}>
                {productLabel(p)}
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
