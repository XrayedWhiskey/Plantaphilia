import React, { useState, useEffect, useCallback, useContext } from 'react'
import ProductForm from './ProductForm.jsx'
import BlueprintForm from './BlueprintForm.jsx'
import VariantDialog from './VariantDialog.jsx'
import TagScopePicker from './TagScopePicker.jsx'
import { finalizeImages } from '../utils/imageLifecycle.js'
import { ToastContext, PreviewContext } from '../App.jsx'

const STATUS_COLORS = {
  publish: { bg: 'rgba(44,101,56,0.3)', color: '#4CAF7D', label: 'Veröffentlicht' },
  draft: { bg: 'rgba(155,111,208,0.15)', color: '#B8A8D8', label: 'Entwurf' },
  private: { bg: 'rgba(180,120,0,0.2)', color: '#CDA832', label: 'Privat' },
  trash: { bg: 'rgba(90,26,46,0.3)', color: '#E57373', label: 'Papierkorb' },
}

export default function ProductList() {
  const [products, setProducts] = useState([])
  const [loading, setLoading] = useState(true)
  const [search, setSearch] = useState('')
  const [sortCol, setSortCol] = useState('updated_at')
  const [sortDir, setSortDir] = useState('desc')
  const [editId, setEditId] = useState(null)
  const [showNew, setShowNew] = useState(false)
  const openPreview = useContext(PreviewContext)
  const addToast = useContext(ToastContext)

  const [expandedVariable, setExpandedVariable] = useState(() => new Set())
  const [variantsByProduct, setVariantsByProduct] = useState({})
  const [availableSpecs, setAvailableSpecs] = useState([])
  const [variantDialog, setVariantDialog] = useState(null) // { product, index }

  useEffect(() => { window.api.getSpecifications().then(s => setAvailableSpecs(s || [])) }, [])

  const openVariantDialog = (product, index) => setVariantDialog({ product, index })

  // Every variant reachable here belongs to an already-saved product, so
  // this always persists immediately (mirrors ProductForm.jsx's
  // saveVariantFromDialog for its own already-saved-product branch).
  const saveVariantFromDialog = async (data) => {
    const { product, index } = variantDialog
    const folderName = product.image_folder || `produkt_${product.id}`
    const current = variantsByProduct[product.id] || []
    const staged = (data.images || []).some(img => img.isTemp)
    const finalizedImages = staged
      ? await finalizeImages(data.images, `${folderName}/varianten/${index}`, `variante-${index}`)
      : data.images
    const nextVariants = index < current.length
      ? current.map((existing, i) => i === index ? { ...existing, ...data, images: finalizedImages } : existing)
      : [...current, { ...data, images: finalizedImages }]
    await window.api.saveVariants(product.id, nextVariants)
    const fresh = await window.api.getVariants(product.id) || []
    setVariantsByProduct(v => ({ ...v, [product.id]: fresh }))
    setVariantDialog(null)
    addToast && addToast('Variante gespeichert', 'success')
  }

  const deleteVariant = async (product, index) => {
    if (!window.confirm('Diese Variante wirklich entfernen?')) return
    const current = variantsByProduct[product.id] || []
    const remaining = current.filter((_, i) => i !== index)
    await window.api.saveVariants(product.id, remaining)
    const fresh = await window.api.getVariants(product.id) || []
    setVariantsByProduct(v => ({ ...v, [product.id]: fresh }))
    addToast && addToast('Variante entfernt', 'success')
  }

  const toggleVariableExpanded = (id) => {
    setExpandedVariable(prev => {
      const next = new Set(prev)
      if (next.has(id)) { next.delete(id); return next }
      next.add(id)
      if (!variantsByProduct[id]) {
        window.api.getVariants(id).then(vars => {
          setVariantsByProduct(v => ({ ...v, [id]: vars || [] }))
        })
      }
      return next
    })
  }

  const [showBlueprints, setShowBlueprints] = useState(false)
  const [gattungen, setGattungen] = useState([])
  const [arten, setArten] = useState([])
  const [expandedGattungen, setExpandedGattungen] = useState(() => new Set())
  const [expandedArten, setExpandedArten] = useState(() => new Set())
  const [blueprintDialog, setBlueprintDialog] = useState(null) // { type, id, parentGattungId }
  const [tagCategories, setTagCategories] = useState([])
  const [allTags, setAllTags] = useState([])

  const load = useCallback(async (q) => {
    setLoading(true)
    try {
      const data = await window.api.getProducts(q || '')
      setProducts(data)
    } finally {
      setLoading(false)
    }
  }, [])

  const loadBlueprints = useCallback(async () => {
    const [g, a] = await Promise.all([window.api.getGattungen(), window.api.getArten()])
    setGattungen(g)
    setArten(a)
  }, [])

  const loadTags = useCallback(async () => {
    const [cats, tags] = await Promise.all([window.api.getTagCategories(), window.api.getTags()])
    setTagCategories(cats)
    setAllTags(tags)
  }, [])

  useEffect(() => { load('') }, [load])
  useEffect(() => { loadBlueprints() }, [loadBlueprints])
  useEffect(() => { loadTags() }, [loadTags])

  useEffect(() => {
    const t = setTimeout(() => load(search), 300)
    return () => clearTimeout(t)
  }, [search, load])

  const handleSort = (col) => {
    if (sortCol === col) {
      setSortDir(d => d === 'asc' ? 'desc' : 'asc')
    } else {
      setSortCol(col)
      setSortDir('asc')
    }
  }

  const sorted = [...products].sort((a, b) => {
    let av = a[sortCol], bv = b[sortCol]
    if (av === null || av === undefined) av = ''
    if (bv === null || bv === undefined) bv = ''
    if (typeof av === 'number') return sortDir === 'asc' ? av - bv : bv - av
    return sortDir === 'asc'
      ? String(av).localeCompare(String(bv), 'de')
      : String(bv).localeCompare(String(av), 'de')
  })

  const deleteProduct = async (id, name) => {
    if (!window.confirm(`Produkt „${name}" wirklich löschen?`)) return
    await window.api.deleteProduct(id)
    setProducts(p => p.filter(x => x.id !== id))
  }

  const toggleGattungExpanded = (id) => {
    setExpandedGattungen(prev => {
      const next = new Set(prev)
      next.has(id) ? next.delete(id) : next.add(id)
      return next
    })
  }

  const toggleArtExpanded = (id) => {
    setExpandedArten(prev => {
      const next = new Set(prev)
      next.has(id) ? next.delete(id) : next.add(id)
      return next
    })
  }

  const deleteGattungBlueprint = async (g) => {
    const artenUnderG = arten.filter(a => a.gattung_id === g.id)
    const affected = products.filter(p => p.gattung_id === g.id).length
      + artenUnderG.reduce((sum, a) => sum + products.filter(p => p.art_id === a.id).length, 0)
    const msg = affected > 0
      ? `Gattung „${g.name}" löschen? ${affected} Produkt${affected !== 1 ? 'e' : ''} behalten ihre aktuellen Werte, verlieren aber die Verknüpfung zur Blaupause.`
      : `Gattung „${g.name}" wirklich löschen?`
    if (!window.confirm(msg)) return
    await window.api.deleteGattung(g.id)
    await loadBlueprints()
    await load(search)
  }

  const deleteArtBlueprint = async (a) => {
    const affected = products.filter(p => p.art_id === a.id).length
    const msg = affected > 0
      ? `Art „${a.name}" löschen? ${affected} Produkt${affected !== 1 ? 'e' : ''} behalten ihre aktuellen Werte, verlieren aber die Verknüpfung zur Blaupause.`
      : `Art „${a.name}" wirklich löschen?`
    if (!window.confirm(msg)) return
    await window.api.deleteArt(a.id)
    await loadBlueprints()
    await load(search)
  }

  const closeBlueprintDialog = (didSave) => {
    setBlueprintDialog(null)
    loadBlueprints()
    if (didSave) load(search)
  }

  const fmtPrice = (p) => p != null ? `${Number(p).toFixed(2)} €` : '—'
  const fmtDate = (d) => d ? new Date(d).toLocaleDateString('de-DE', { day: '2-digit', month: '2-digit', year: '2-digit' }) : '—'

  const SortBtn = ({ col, label }) => (
    <span onClick={() => handleSort(col)} style={{ cursor: 'pointer', userSelect: 'none', display: 'inline-flex', alignItems: 'center', gap: 4 }}>
      {label}
      {sortCol === col && <span style={{ fontSize: 9, opacity: 0.7 }}>{sortDir === 'asc' ? '▲' : '▼'}</span>}
    </span>
  )

  return (
    <div style={{ padding: 20 }}>
      {/* Toolbar */}
      <div style={{ display: 'flex', gap: 10, marginBottom: 16, alignItems: 'center' }}>
        <input
          className="input"
          style={{ maxWidth: 320 }}
          placeholder="Suchen (Name, Gattung, Art, SKU...)"
          value={search}
          onChange={e => setSearch(e.target.value)}
        />
        <div style={{ flex: 1 }} />
        <span style={{ fontSize: 12, color: '#9CA59E' }}>{products.length} Produkt{products.length !== 1 ? 'e' : ''}</span>
        <button
          className={showBlueprints ? 'btn-primary' : 'btn-secondary'}
          onClick={() => setShowBlueprints(s => !s)}
        >{showBlueprints ? '✕ Blaupausen ausblenden' : '🧬 Gattungen & Arten'}</button>
        <button className="btn-primary" onClick={() => setShowNew(true)}>+ Neues Produkt</button>
      </div>

      <div style={{ display: 'flex', gap: 16, alignItems: 'flex-start' }}>
      {/* Table */}
      <div style={{ flex: showBlueprints ? '1 1 55%' : '1 1 100%', minWidth: 0, border: '1px solid rgba(155,111,208,0.12)', borderRadius: 6, overflow: 'hidden' }}>
        {/* Head */}
        <div style={{
          display: 'grid', gridTemplateColumns: '60px 1fr 120px 100px 100px 100px 100px 165px',
          background: '#1A231F', padding: '8px 12px',
          borderBottom: '1px solid rgba(155,111,208,0.12)',
          fontSize: 10, fontWeight: 700, color: '#9CA59E', textTransform: 'uppercase', letterSpacing: 0.8
        }}>
          <span>Bild</span>
          <SortBtn col="name" label="Name / Gattung" />
          <SortBtn col="sku" label="SKU" />
          <SortBtn col="stock" label="Bestand" />
          <SortBtn col="regular_price" label="Preis" />
          <SortBtn col="status" label="Status" />
          <SortBtn col="created_at" label="Erstellt" />
          <SortBtn col="updated_at" label="Geändert" />
        </div>

        {/* Rows */}
        {loading && (
          <div style={{ padding: 40, textAlign: 'center', color: '#9CA59E', fontSize: 13 }}>Laden…</div>
        )}
        {!loading && sorted.length === 0 && (
          <div style={{ padding: 40, textAlign: 'center', color: '#9CA59E', fontSize: 13 }}>
            {search ? 'Keine Produkte gefunden.' : 'Noch keine Produkte. Erstelle dein erstes Produkt oder führe einen Pull durch.'}
          </div>
        )}
        {!loading && sorted.map((p, idx) => {
          const status = STATUS_COLORS[p.status] || STATUS_COLORS.draft
          const variableExpanded = expandedVariable.has(p.id)
          return (
            <React.Fragment key={p.id}>
            <div style={{
              display: 'grid', gridTemplateColumns: '60px 1fr 120px 100px 100px 100px 100px 165px',
              padding: '10px 12px', alignItems: 'center',
              borderBottom: idx < sorted.length - 1 ? '1px solid rgba(155,111,208,0.07)' : 'none',
              background: idx % 2 === 0 ? 'transparent' : 'rgba(255,255,255,0.015)',
              transition: 'background 0.1s'
            }}
              onMouseEnter={e => e.currentTarget.style.background = 'rgba(155,111,208,0.06)'}
              onMouseLeave={e => e.currentTarget.style.background = idx % 2 === 0 ? 'transparent' : 'rgba(255,255,255,0.015)'}
            >
              {/* Thumbnail */}
              <div style={{ width: 44, height: 44, borderRadius: 4, background: '#1A231F', overflow: 'hidden', border: '1px solid rgba(155,111,208,0.15)' }}>
                <ProductThumb productId={p.id} />
              </div>

              {/* Name */}
              <div style={{ minWidth: 0, overflow: 'hidden' }}>
                <div style={{ fontWeight: 600, fontSize: 13, color: '#EAE4D6', marginBottom: 1, overflow: 'hidden', textOverflow: 'ellipsis', whiteSpace: 'nowrap', display: 'flex', alignItems: 'center', gap: 5 }}>
                  {p.is_variable ? (
                    <span onClick={() => toggleVariableExpanded(p.id)} style={{ cursor: 'pointer', display: 'flex', alignItems: 'center', gap: 5, minWidth: 0 }}>
                      <span style={{ color: '#9CA59E', fontSize: 10, flexShrink: 0 }}>{variableExpanded ? '▾' : '▸'}</span>
                      <span style={{ overflow: 'hidden', textOverflow: 'ellipsis', whiteSpace: 'nowrap' }}>
                        Variabel: "{p.name || 'Ohne Name'}"
                      </span>
                    </span>
                  ) : (
                    p.name || <span style={{ color: '#9CA59E', fontStyle: 'italic' }}>Ohne Name</span>
                  )}
                </div>
                <div style={{ fontSize: 11, color: '#9CA59E', overflow: 'hidden', textOverflow: 'ellipsis', whiteSpace: 'nowrap' }}>
                  {[p.gattung, p.art, p.kultivar].filter(Boolean).join(' ')}
                </div>
              </div>

              {/* SKU */}
              <div style={{ fontSize: 12, color: '#9CA59E', fontFamily: 'monospace', minWidth: 0, overflow: 'hidden', textOverflow: 'ellipsis', whiteSpace: 'nowrap' }}>{p.sku || '—'}</div>

              {/* Stock */}
              <div style={{ fontSize: 13, color: p.stock > 0 ? '#4CAF7D' : '#E57373', fontWeight: 600 }}>{p.stock ?? 0}</div>

              {/* Price */}
              <div>
                <div style={{ fontSize: 13, color: '#EAE4D6' }}>{fmtPrice(p.regular_price || p.price)}</div>
                {p.sale_price && <div style={{ fontSize: 11, color: '#9B6FD0' }}>{fmtPrice(p.sale_price)} Angebot</div>}
              </div>

              {/* Status */}
              <div>
                <span style={{
                  padding: '2px 8px', borderRadius: 20, fontSize: 10, fontWeight: 600,
                  background: status.bg, color: status.color
                }}>{status.label}</span>
              </div>

              {/* Created */}
              <div style={{ fontSize: 11, color: '#9CA59E' }}>{fmtDate(p.created_at)}</div>

              {/* Actions */}
              <div style={{ display: 'flex', gap: 5, alignItems: 'center' }}>
                <span style={{ fontSize: 11, color: '#9CA59E', flexShrink: 0 }}>{fmtDate(p.updated_at)}</span>
                <button onClick={() => openPreview({ product: p })} title="Vorschau" style={{
                  background: 'rgba(155,111,208,0.12)', border: '1px solid rgba(155,111,208,0.25)',
                  color: '#9CA59E', borderRadius: 3, padding: '3px 7px', fontSize: 11, cursor: 'pointer'
                }}>👁</button>
                <button onClick={() => setEditId(p.id)} style={{
                  background: 'rgba(155,111,208,0.15)', border: '1px solid rgba(155,111,208,0.3)',
                  color: '#B8A8D8', borderRadius: 3, padding: '3px 7px', fontSize: 11, cursor: 'pointer'
                }}>✏️</button>
                <button onClick={() => deleteProduct(p.id, p.name)} style={{
                  background: 'rgba(90,26,46,0.2)', border: '1px solid rgba(90,26,46,0.4)',
                  color: '#E57373', borderRadius: 3, padding: '3px 7px', fontSize: 11, cursor: 'pointer'
                }}>✕</button>
              </div>
            </div>
            {!!p.is_variable && variableExpanded && (
              <div style={{ padding: '8px 12px 12px 116px', background: 'rgba(255,255,255,0.02)', borderBottom: '1px solid rgba(155,111,208,0.07)' }}>
                <button onClick={() => openVariantDialog(p, (variantsByProduct[p.id] || []).length)} style={{
                  background: 'rgba(155,111,208,0.15)', border: '1px solid rgba(155,111,208,0.3)',
                  color: '#B8A8D8', borderRadius: 3, padding: '4px 10px', fontSize: 11, cursor: 'pointer', marginBottom: 8
                }}>+ Variante hinzufügen</button>
                {(variantsByProduct[p.id] || []).length === 0 ? (
                  <div style={{ fontSize: 12, color: '#9CA59E' }}>Noch keine Varianten.</div>
                ) : (
                  <div style={{ display: 'flex', flexDirection: 'column', gap: 4 }}>
                    {(variantsByProduct[p.id] || []).map((v, i) => (
                      <div key={v.id ?? i} style={{ display: 'flex', gap: 12, alignItems: 'center', fontSize: 12, color: '#C8C0AF' }}>
                        <span style={{ minWidth: 120 }}>{v.name || <em>ohne Namen</em>}</span>
                        <span style={{ color: '#9CA59E' }}>{v.sku || '—'}</span>
                        <span>{fmtPrice(v.regular_price || v.price)}</span>
                        <span style={{ color: v.stock > 0 ? '#4CAF7D' : '#E57373' }}>Bestand: {v.stock ?? 0}</span>
                        <span style={{ flex: 1 }} />
                        <button onClick={() => openVariantDialog(p, i)} title="Bearbeiten" style={{
                          background: 'rgba(155,111,208,0.15)', border: '1px solid rgba(155,111,208,0.3)',
                          color: '#B8A8D8', borderRadius: 3, padding: '2px 6px', fontSize: 10, cursor: 'pointer'
                        }}>✏️</button>
                        <button onClick={() => deleteVariant(p, i)} title="Entfernen" style={{
                          background: 'rgba(90,26,46,0.2)', border: '1px solid rgba(90,26,46,0.4)',
                          color: '#E57373', borderRadius: 3, padding: '2px 6px', fontSize: 10, cursor: 'pointer'
                        }}>✕</button>
                      </div>
                    ))}
                  </div>
                )}
              </div>
            )}
            </React.Fragment>
          )
        })}
      </div>

      {/* Blaupausen-Spalte */}
      {showBlueprints && (
        <div style={{ flex: '1 1 45%', minWidth: 280, border: '1px solid rgba(155,111,208,0.12)', borderRadius: 6, overflow: 'hidden' }}>
          <div style={{
            display: 'flex', justifyContent: 'space-between', alignItems: 'center',
            background: '#1A231F', padding: '8px 12px', borderBottom: '1px solid rgba(155,111,208,0.12)'
          }}>
            <span style={{ fontSize: 10, fontWeight: 700, color: '#9CA59E', textTransform: 'uppercase', letterSpacing: 0.8 }}>Gattungen &amp; Arten</span>
            <button className="btn-secondary" style={{ fontSize: 11, padding: '3px 9px' }}
              onClick={() => setBlueprintDialog({ type: 'gattung', id: null })}>+ Neue Gattung</button>
          </div>
          <div style={{ maxHeight: 560, overflowY: 'auto' }}>
            {gattungen.length === 0 && (
              <div style={{ padding: 24, textAlign: 'center', color: '#9CA59E', fontSize: 13 }}>Noch keine Gattungen angelegt.</div>
            )}
            {gattungen.map(g => {
              const childArten = arten.filter(a => a.gattung_id === g.id)
              const expanded = expandedGattungen.has(g.id)
              return (
                <div key={g.id} style={{ borderBottom: '1px solid rgba(155,111,208,0.07)' }}>
                  <div style={{ display: 'flex', alignItems: 'center', gap: 6, padding: '8px 12px' }}>
                    <span onClick={() => toggleGattungExpanded(g.id)} style={{ cursor: 'pointer', color: '#9CA59E', fontSize: 10, width: 12 }}>
                      {expanded ? '▾' : '▸'}
                    </span>
                    <span style={{ flex: 1, fontSize: 13, color: '#EAE4D6', fontWeight: 600, cursor: 'pointer' }} onClick={() => toggleGattungExpanded(g.id)}>{g.name}</span>
                    <button onClick={() => setBlueprintDialog({ type: 'art', id: null, parentGattungId: g.id })} title="Art hinzufügen" style={{
                      background: 'rgba(155,111,208,0.12)', border: '1px solid rgba(155,111,208,0.25)',
                      color: '#9CA59E', borderRadius: 3, padding: '2px 6px', fontSize: 10, cursor: 'pointer'
                    }}>+ Art</button>
                    <button onClick={() => setBlueprintDialog({ type: 'gattung', id: g.id })} style={{
                      background: 'rgba(155,111,208,0.15)', border: '1px solid rgba(155,111,208,0.3)',
                      color: '#B8A8D8', borderRadius: 3, padding: '2px 6px', fontSize: 10, cursor: 'pointer'
                    }}>✏️</button>
                    <button onClick={() => deleteGattungBlueprint(g)} style={{
                      background: 'rgba(90,26,46,0.2)', border: '1px solid rgba(90,26,46,0.4)',
                      color: '#E57373', borderRadius: 3, padding: '2px 6px', fontSize: 10, cursor: 'pointer'
                    }}>✕</button>
                  </div>
                  {expanded && childArten.map(a => {
                    const artExpanded = expandedArten.has(a.id)
                    return (
                    <div key={a.id}>
                    <div style={{ display: 'flex', alignItems: 'center', gap: 6, padding: '6px 12px 6px 34px', background: 'rgba(255,255,255,0.015)' }}>
                      <span onClick={() => toggleArtExpanded(a.id)} style={{ cursor: 'pointer', color: '#9CA59E', fontSize: 9, width: 10 }}>
                        {artExpanded ? '▾' : '▸'}
                      </span>
                      <span style={{ flex: 1, fontSize: 12, color: '#C8C0AF', cursor: 'pointer' }} onClick={() => toggleArtExpanded(a.id)}>{a.name}</span>
                      <button onClick={() => setBlueprintDialog({ type: 'art', id: a.id, parentGattungId: g.id })} style={{
                        background: 'rgba(155,111,208,0.15)', border: '1px solid rgba(155,111,208,0.3)',
                        color: '#B8A8D8', borderRadius: 3, padding: '2px 6px', fontSize: 10, cursor: 'pointer'
                      }}>✏️</button>
                      <button onClick={() => deleteArtBlueprint(a)} style={{
                        background: 'rgba(90,26,46,0.2)', border: '1px solid rgba(90,26,46,0.4)',
                        color: '#E57373', borderRadius: 3, padding: '2px 6px', fontSize: 10, cursor: 'pointer'
                      }}>✕</button>
                    </div>
                    {artExpanded && (
                      <TagCategorySection
                        gattungId={g.id} artId={a.id}
                        categories={tagCategories} allTags={allTags}
                        onCategoriesChange={setTagCategories} onTagsChange={setAllTags}
                      />
                    )}
                    </div>
                    )
                  })}
                  {expanded && (
                    <TagCategorySection
                      gattungId={g.id} artId={null}
                      categories={tagCategories} allTags={allTags}
                      onCategoriesChange={setTagCategories} onTagsChange={setAllTags}
                    />
                  )}
                </div>
              )
            })}
          </div>
        </div>
      )}
      </div>

      {/* Modals */}
      {(editId || showNew) && (
        <ProductForm
          productId={editId || null}
          onClose={() => { setEditId(null); setShowNew(false) }}
          onSaved={() => load(search)}
        />
      )}

      {variantDialog && (
        <VariantDialog
          variant={variantDialog.index < (variantsByProduct[variantDialog.product.id] || []).length
            ? (variantsByProduct[variantDialog.product.id] || [])[variantDialog.index] : null}
          index={variantDialog.index}
          defaultSpecificationId={null}
          availableSpecs={availableSpecs}
          onSave={saveVariantFromDialog}
          onCancel={() => setVariantDialog(null)}
          onSpecCreated={setAvailableSpecs}
          gattungId={variantDialog.product.gattung_id}
          gattungName={variantDialog.product.gattung}
          artId={variantDialog.product.art_id}
          artName={variantDialog.product.art}
        />
      )}

      {blueprintDialog && (
        <BlueprintForm
          type={blueprintDialog.type}
          id={blueprintDialog.id}
          parentGattungId={blueprintDialog.parentGattungId}
          onClose={() => setBlueprintDialog(null)}
          onSaved={() => closeBlueprintDialog(true)}
        />
      )}
    </div>
  )
}

// Manages Tag-Überkategorien (and their Tags) scoped to exactly this
// gattungId/artId pair (artId=null means the Gattung-level, not-Art-specific
// scope). Mirrors TagPool's category/tag CRUD but without product selection —
// this is pure catalog management, reachable right where the Gattung/Art
// itself lives so the two hierarchies visibly line up.
function TagCategorySection({ gattungId, artId, categories, allTags, onCategoriesChange, onTagsChange }) {
  const [addingCategory, setAddingCategory] = useState(false)
  const [newCategoryName, setNewCategoryName] = useState('')
  const [newScopeGattungId, setNewScopeGattungId] = useState(gattungId)
  const [newScopeArtId, setNewScopeArtId] = useState(artId || null)
  const [editingCategory, setEditingCategory] = useState(null)
  const [editName, setEditName] = useState('')
  const [editScopeGattungId, setEditScopeGattungId] = useState(null)
  const [editScopeArtId, setEditScopeArtId] = useState(null)
  const [addingTagFor, setAddingTagFor] = useState(null)
  const [newTagName, setNewTagName] = useState('')

  const scoped = categories.filter(c => c.gattung_id === gattungId && (artId ? c.art_id === artId : !c.art_id))

  const openAddCategory = () => {
    setNewScopeGattungId(gattungId)
    setNewScopeArtId(artId || null)
    setAddingCategory(true)
  }

  const createCategory = async () => {
    const name = newCategoryName.trim()
    if (!name) return
    const id = await window.api.saveTagCategory({ name, gattung_id: newScopeGattungId, art_id: newScopeArtId })
    onCategoriesChange(c => [...c, { id, name, gattung_id: newScopeGattungId, art_id: newScopeArtId }])
    setNewCategoryName('')
    setAddingCategory(false)
  }

  const openEditCategory = (cat) => {
    setEditingCategory(cat)
    setEditName(cat.name)
    setEditScopeGattungId(cat.gattung_id)
    setEditScopeArtId(cat.art_id)
  }

  const saveEditCategory = async () => {
    const name = editName.trim()
    if (!name) return
    await window.api.saveTagCategory({ id: editingCategory.id, name, gattung_id: editScopeGattungId, art_id: editScopeArtId })
    onCategoriesChange(c => c.map(x => x.id === editingCategory.id ? { ...x, name, gattung_id: editScopeGattungId, art_id: editScopeArtId } : x))
    setEditingCategory(null)
  }

  const deleteCategory = async (cat) => {
    const count = allTags.filter(t => t.category_id === cat.id).length
    const msg = count > 0
      ? `Kategorie „${cat.name}" löschen? ${count} Tag${count !== 1 ? 's' : ''} werden mitgelöscht.`
      : `Kategorie „${cat.name}" wirklich löschen?`
    if (!window.confirm(msg)) return
    try {
      await window.api.deleteTagCategory(cat.id)
    } catch (e) {
      window.alert(e.message)
      return
    }
    onCategoriesChange(c => c.filter(x => x.id !== cat.id))
    onTagsChange(t => t.filter(x => x.category_id !== cat.id))
  }

  const createTag = async (categoryId) => {
    const name = newTagName.trim()
    if (!name) return
    const id = await window.api.saveTag({ name, category_id: categoryId, slug: name.toLowerCase().replace(/\s+/g, '-') })
    onTagsChange(t => [...t, { id, name, category_id: categoryId }])
    setNewTagName('')
    setAddingTagFor(null)
  }

  const deleteTag = async (tag) => {
    if (!window.confirm(`Tag „${tag.name}" wirklich löschen?`)) return
    await window.api.deleteTag(tag.id)
    onTagsChange(t => t.filter(x => x.id !== tag.id))
  }

  return (
    <div style={{ padding: `4px 12px 8px ${artId ? 58 : 34}px`, background: 'rgba(155,111,208,0.02)' }}>
      {scoped.map(cat => {
        const isEditing = editingCategory?.id === cat.id
        return (
        <div key={cat.id} style={{ marginBottom: 6 }}>
          {isEditing ? (
            <div style={{ marginBottom: 4, display: 'flex', flexDirection: 'column', gap: 6 }}>
              <div style={{ display: 'flex', gap: 6 }}>
                <input className="input" style={{ fontSize: 11, flex: 1 }} value={editName}
                  onChange={e => setEditName(e.target.value)}
                  onKeyDown={e => { if (e.key === 'Enter') { e.preventDefault(); saveEditCategory() } }}
                  autoFocus />
                <div style={{ width: 150 }}>
                  <TagScopePicker gattungId={editScopeGattungId} artId={editScopeArtId}
                    onChange={(g, a) => { setEditScopeGattungId(g); setEditScopeArtId(a) }} />
                </div>
              </div>
              <div style={{ display: 'flex', gap: 6, justifyContent: 'flex-end' }}>
                <button type="button" className="btn-secondary" style={{ fontSize: 10, padding: '3px 8px' }} onClick={() => setEditingCategory(null)}>Abbrechen</button>
                <button type="button" className="btn-primary" style={{ fontSize: 10, padding: '3px 8px' }} onClick={saveEditCategory}>Speichern</button>
              </div>
            </div>
          ) : (
            <div style={{ display: 'flex', alignItems: 'center', gap: 6 }}>
              <span style={{ fontSize: 11, color: '#B8A8D8', flex: 1 }}>🏷️ {cat.name}</span>
              <button type="button" onClick={() => { setAddingTagFor(addingTagFor === cat.id ? null : cat.id); setNewTagName('') }} style={{
                background: 'none', border: '1px dashed rgba(155,111,208,0.3)', borderRadius: 3,
                color: '#9B6FD0', cursor: 'pointer', fontSize: 9, padding: '1px 6px'
              }}>+ Tag</button>
              <span onClick={() => openEditCategory(cat)} title="Kategorie bearbeiten" style={{
                cursor: 'pointer', fontSize: 10, color: '#9CA59E', opacity: 0.6, padding: '0 2px'
              }}>✏️</span>
              {!cat.protected && (
                <span onClick={() => deleteCategory(cat)} title="Kategorie löschen" style={{
                  cursor: 'pointer', fontSize: 10, color: '#9CA59E', opacity: 0.6, padding: '0 2px'
                }}>✕</span>
              )}
            </div>
          )}
          <div style={{ display: 'flex', flexWrap: 'wrap', gap: 4, marginTop: 4 }}>
            {allTags.filter(t => t.category_id === cat.id).map(tag => (
              <span key={tag.id} style={{
                display: 'inline-flex', alignItems: 'center', borderRadius: 20, fontSize: 11,
                background: 'rgba(155,111,208,0.08)', border: '1px solid rgba(155,111,208,0.2)', color: '#C8C0AF'
              }}>
                <span style={{ padding: '2px 4px 2px 8px' }}>{tag.name}</span>
                <span onClick={() => deleteTag(tag)} title="Tag löschen" style={{
                  cursor: 'pointer', fontSize: 9, opacity: 0.6, padding: '2px 6px 2px 2px'
                }}>✕</span>
              </span>
            ))}
            {allTags.filter(t => t.category_id === cat.id).length === 0 && (
              <span style={{ fontSize: 11, color: '#9CA59E', fontStyle: 'italic' }}>Keine Tags</span>
            )}
          </div>
          {addingTagFor === cat.id && (
            <div style={{ display: 'flex', gap: 6, marginTop: 4 }}>
              <input className="input" style={{ fontSize: 11, flex: 1 }} value={newTagName}
                onChange={e => setNewTagName(e.target.value)}
                onKeyDown={e => { if (e.key === 'Enter') { e.preventDefault(); createTag(cat.id) } }}
                autoFocus placeholder="Tag-Name..." />
              <button type="button" className="btn-primary" style={{ fontSize: 10, padding: '3px 8px' }} onClick={() => createTag(cat.id)}>+</button>
            </div>
          )}
        </div>
        )
      })}
      {addingCategory ? (
        <div style={{ display: 'flex', gap: 6 }}>
          <input className="input" style={{ fontSize: 11, flex: 1 }} value={newCategoryName}
            onChange={e => setNewCategoryName(e.target.value)}
            onKeyDown={e => { if (e.key === 'Enter') { e.preventDefault(); createCategory() } }}
            autoFocus placeholder="Kategorie-Name..." />
          <div style={{ width: 150 }}>
            <TagScopePicker gattungId={newScopeGattungId} artId={newScopeArtId}
              onChange={(g, a) => { setNewScopeGattungId(g); setNewScopeArtId(a) }} />
          </div>
          <button type="button" className="btn-primary" style={{ fontSize: 10, padding: '3px 8px' }} onClick={createCategory}>+</button>
          <button type="button" className="btn-secondary" style={{ fontSize: 10, padding: '3px 8px' }} onClick={() => setAddingCategory(false)}>✕</button>
        </div>
      ) : (
        <button type="button" onClick={openAddCategory} style={{
          background: 'none', border: '1px dashed rgba(155,111,208,0.3)', borderRadius: 3,
          color: '#9B6FD0', cursor: 'pointer', fontSize: 10, padding: '2px 8px'
        }}>+ Kategorie</button>
      )}
    </div>
  )
}

function ProductThumb({ productId }) {
  const [src, setSrc] = useState(null)

  useEffect(() => {
    if (!productId) return
    window.api.getProductImages(productId).then(imgs => {
      if (imgs && imgs.length > 0) {
        const img = imgs[0]
        setSrc(img.local_path ? `file://${img.local_path}` : img.remote_url || null)
      }
    })
  }, [productId])

  if (!src) return <div style={{ width: '100%', height: '100%', background: 'rgba(155,111,208,0.08)' }} />
  return <img src={src} alt="" style={{ width: '100%', height: '100%', objectFit: 'cover' }} />
}
