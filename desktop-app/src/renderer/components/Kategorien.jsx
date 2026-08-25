import React, { useState, useEffect, useCallback } from 'react'
import CategoryTree from './CategoryTree.jsx'
import CategoryBindingPicker from './CategoryBindingPicker.jsx'
import ProductPicker from './ProductPicker.jsx'
import CategoryProductsModal from './CategoryProductsModal.jsx'

// Create/rename modal. Create: choose free name OR bind to Gattung/Art/Kultivar.
// Rename: only ever opened for free, non-protected categories (see CategoryTree),
// so it's just a name field there.
function CategoryModal({ parentId, editCat, onClose, onSaved }) {
  const isRename = !!editCat
  const [name, setName] = useState(editCat?.name || '')
  const [heading, setHeading] = useState(editCat?.heading || '')
  const [description, setDescription] = useState(editCat?.description || '')
  const [gattungId, setGattungId] = useState(null)
  const [artId, setArtId] = useState(null)
  const [kultivar, setKultivar] = useState(null)
  const [saving, setSaving] = useState(false)

  const bound = !!(gattungId || artId)

  const save = async () => {
    if (!bound && !name.trim()) return
    setSaving(true)
    try {
      const payload = isRename
        ? { ...editCat, name: name.trim(), heading: heading.trim(), description: description.trim() }
        : { parent_id: parentId, name: bound ? null : name.trim(), heading: heading.trim(), description: description.trim(), gattung_id: gattungId, art_id: artId, kultivar }
      await window.api.saveCategory(payload)
      onSaved()
    } finally {
      setSaving(false)
    }
  }

  return (
    <div style={{
      position: 'fixed', inset: 0, background: 'rgba(0,0,0,0.6)', zIndex: 400,
      display: 'flex', alignItems: 'center', justifyContent: 'center'
    }}>
      <div style={{
        background: '#25302B', border: '1px solid rgba(155,111,208,0.2)',
        borderRadius: 8, width: 420, boxShadow: '0 24px 80px rgba(0,0,0,0.7)'
      }}>
        <div style={{ padding: '14px 20px', background: '#1A231F', borderBottom: '1px solid rgba(155,111,208,0.15)', borderRadius: '8px 8px 0 0' }}>
          <h2 style={{ fontSize: 15, fontWeight: 700, color: '#EAE4D6' }}>
            {isRename ? 'Kategorie umbenennen' : 'Neue Kategorie'}
          </h2>
        </div>
        <div style={{ padding: 20, display: 'flex', flexDirection: 'column', gap: 12 }}>
          {isRename ? (
            <div>
              <label className="label">Name</label>
              <input className="input" value={name} onChange={e => setName(e.target.value)} autoFocus
                onKeyDown={e => { if (e.key === 'Enter') save() }} />
            </div>
          ) : (
            <>
              <div>
                <label className="label">Name (nur für frei benannte Kategorien)</label>
                <input className="input" value={name} onChange={e => setName(e.target.value)} disabled={bound}
                  placeholder="z. B. Zimmertaugliche" />
              </div>
              <div style={{ fontSize: 11, color: '#9CA59E', textAlign: 'center' }}>— oder —</div>
              <div>
                <label className="label">An Gattung/Art/Kultivar binden</label>
                <CategoryBindingPicker
                  gattungId={gattungId} artId={artId} kultivar={kultivar}
                  onChange={(g, a, k) => { setGattungId(g); setArtId(a); setKultivar(k) }}
                />
              </div>
            </>
          )}
          <div>
            <label className="label">Überschrift im Shop (leer = Kategoriename)</label>
            <input className="input" value={heading} onChange={e => setHeading(e.target.value)}
              placeholder="wird bei Filterung nach dieser Kategorie zentriert angezeigt" />
          </div>
          <div>
            <label className="label">Beschreibung (optional)</label>
            <textarea className="input" rows={3} value={description} onChange={e => setDescription(e.target.value)}
              placeholder="z. B. was diese Kategorie ausmacht — selten benötigt" />
          </div>
        </div>
        <div style={{ padding: '12px 20px', borderTop: '1px solid rgba(155,111,208,0.1)', display: 'flex', gap: 10, justifyContent: 'flex-end' }}>
          <button type="button" className="btn-secondary" onClick={onClose}>Abbrechen</button>
          <button type="button" className="btn-primary" disabled={saving || (!bound && !name.trim())} onClick={save}>
            {saving ? 'Speichere...' : isRename ? 'Speichern' : 'Erstellen'}
          </button>
        </div>
      </div>
    </div>
  )
}

export default function Kategorien() {
  const [categories, setCategories] = useState([])
  const [gattungen, setGattungen] = useState([])
  const [arten, setArten] = useState([])
  const [loading, setLoading] = useState(true)
  const [modalState, setModalState] = useState(null) // { parentId } | { editCat }
  const [productsFor, setProductsFor] = useState(null) // category row
  const [viewingProductsFor, setViewingProductsFor] = useState(null) // category row
  const [carouselView, setCarouselView] = useState(false)

  const load = useCallback(async () => {
    setLoading(true)
    try {
      const [cats, g, a] = await Promise.all([
        window.api.getCategories(), window.api.getGattungen(), window.api.getArten()
      ])
      setCategories(cats || [])
      setGattungen(g || [])
      setArten(a || [])
    } finally {
      setLoading(false)
    }
  }, [])

  useEffect(() => { load() }, [load])

  const gattungMap = new Map(gattungen.map(g => [g.id, g]))
  const artMap = new Map(arten.map(a => [a.id, a]))

  const categoryIdToLinks = new Map()
  for (const g of gattungen) {
    for (const cid of (g.category_ids || [])) {
      if (!categoryIdToLinks.has(cid)) categoryIdToLinks.set(cid, [])
      categoryIdToLinks.get(cid).push({ type: 'gattung', name: g.name })
    }
  }
  for (const a of arten) {
    for (const cid of (a.category_ids || [])) {
      if (!categoryIdToLinks.has(cid)) categoryIdToLinks.set(cid, [])
      categoryIdToLinks.get(cid).push({ type: 'art', name: a.name })
    }
  }

  const childrenByParent = new Map()
  for (const cat of categories) {
    const key = cat.parent_id ?? null
    if (!childrenByParent.has(key)) childrenByParent.set(key, [])
    childrenByParent.get(key).push(cat)
  }
  const rootNodes = childrenByParent.get(null) || []

  const carouselNodes = [...categories]
    .filter(c => c.mainpage_carousel)
    .sort((a, b) => a.carousel_sort_order - b.carousel_sort_order)

  const displayNameFor = (cat) => {
    if (cat.kultivar) return cat.kultivar
    if (cat.art_id) return artMap.get(cat.art_id)?.name || '(gelöschte Art)'
    if (cat.gattung_id) return gattungMap.get(cat.gattung_id)?.name || '(gelöschte Gattung)'
    return cat.name || '(unbenannt)'
  }

  return (
    <div style={{ padding: 20 }}>
      <div style={{ display: 'flex', gap: 10, marginBottom: 16, alignItems: 'center' }}>
        <h2 style={{ fontSize: 16, fontWeight: 700, color: '#EAE4D6', margin: 0 }}>Kategorien</h2>
        <span style={{ fontSize: 12, color: '#9CA59E' }}>
          Erscheinen als Filter im Shop — hierarchisch, mit Auf/Ab und Ein-/Ausgliedern.
        </span>
        <div style={{ flex: 1 }} />
        <label style={{ display: 'flex', alignItems: 'center', gap: 6, fontSize: 12, color: '#9CA59E', cursor: 'pointer' }}>
          <input type="checkbox" checked={carouselView} onChange={e => setCarouselView(e.target.checked)} />
          Nur Mainpage-Carousels (Reihenfolge auf der Startseite)
        </label>
        {!carouselView && (
          <button type="button" className="btn-primary" onClick={() => setModalState({ parentId: null })}>+ Überkategorie erstellen</button>
        )}
      </div>

      {loading ? (
        <div style={{ padding: 24, textAlign: 'center', color: '#9CA59E', fontSize: 13 }}>Lädt...</div>
      ) : carouselView ? (
        carouselNodes.length === 0 ? (
          <div style={{ padding: 24, textAlign: 'center', color: '#9CA59E', fontSize: 13 }}>Keine Kategorie ist als Mainpage-Carousel markiert.</div>
        ) : (
          <div style={{ border: '1px solid rgba(155,111,208,0.12)', borderRadius: 6, padding: 12 }}>
            <CategoryTree
              nodes={carouselNodes}
              childrenByParent={new Map()}
              gattungMap={gattungMap}
              artMap={artMap}
              depth={0}
              flatMode
              onReload={load}
              onOpenCreate={() => {}}
              onOpenRename={(cat) => setModalState({ editCat: cat })}
              onOpenProducts={(cat) => setProductsFor(cat)}
              onViewProducts={(cat) => setViewingProductsFor(cat)}
              categoryIdToLinks={categoryIdToLinks}
            />
          </div>
        )
      ) : rootNodes.length === 0 ? (
        <div style={{ padding: 24, textAlign: 'center', color: '#9CA59E', fontSize: 13 }}>Noch keine Kategorien angelegt.</div>
      ) : (
        <div style={{ border: '1px solid rgba(155,111,208,0.12)', borderRadius: 6, padding: 12 }}>
          <CategoryTree
            nodes={rootNodes}
            childrenByParent={childrenByParent}
            gattungMap={gattungMap}
            artMap={artMap}
            depth={0}
            onReload={load}
            onOpenCreate={(parentId) => setModalState({ parentId })}
            onOpenRename={(cat) => setModalState({ editCat: cat })}
            onOpenProducts={(cat) => setProductsFor(cat)}
            onViewProducts={(cat) => setViewingProductsFor(cat)}
            categoryIdToLinks={categoryIdToLinks}
          />
        </div>
      )}

      {modalState && (
        <CategoryModal
          parentId={modalState.parentId}
          editCat={modalState.editCat}
          onClose={() => setModalState(null)}
          onSaved={() => { setModalState(null); load() }}
        />
      )}
      {productsFor && (
        <ProductPicker
          categoryId={productsFor.id}
          categoryLabel={displayNameFor(productsFor)}
          onClose={() => setProductsFor(null)}
        />
      )}
      {viewingProductsFor && (
        <CategoryProductsModal
          categoryId={viewingProductsFor.id}
          categoryLabel={displayNameFor(viewingProductsFor)}
          onClose={() => setViewingProductsFor(null)}
        />
      )}
    </div>
  )
}
