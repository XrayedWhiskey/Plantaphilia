import React, { useState, useEffect } from 'react'
import ImageUpload from './ImageUpload.jsx'
import TagPool from './TagPool.jsx'
import SpecificationDialog from './SpecificationDialog.jsx'
import { copyImagesToTemp } from '../utils/imageLifecycle.js'

// Popup for creating/editing a single variant of a "Variabel" product —
// mirrors SpecificationDialog.jsx's overlay/header/body/footer structure.
// `index` scopes this session's temp staging (images/temp/variant-N) so two
// variants (or the base product's own temp/) never fight over the same
// "Bild 1" filename while several are being edited across one session.
export default function VariantDialog({ variant, index, defaultSpecificationId = null, availableSpecs, onSave, onCancel, onSpecCreated, gattungId, gattungName, artId, artName }) {
  const isEdit = !!variant
  const [name, setName] = useState(variant?.name || '')
  const [sku, setSku] = useState(variant?.sku || '')
  const [regularPrice, setRegularPrice] = useState(variant?.regular_price ?? variant?.price ?? '')
  const [stock, setStock] = useState(variant?.stock ?? 0)
  const [lowStockThreshold, setLowStockThreshold] = useState(variant?.low_stock_threshold ?? '')
  const [neverLowStock, setNeverLowStock] = useState(!!variant?.never_low_stock)
  const [showExactStock, setShowExactStock] = useState(variant ? !!variant.show_exact_stock : true)
  const [specs, setSpecs] = useState(availableSpecs)
  const [specificationId, setSpecificationId] = useState(variant?.specification_id ?? defaultSpecificationId)
  const [showSpecDialog, setShowSpecDialog] = useState(false)
  const [tagIds, setTagIds] = useState(variant?.tag_ids || [])
  const [images, setImages] = useState([])
  const [saving, setSaving] = useState(false)

  const handleSaveSpecification = async (spec) => {
    const savedId = await window.api.saveSpecification(spec)
    const fresh = await window.api.getSpecifications() || []
    setSpecs(fresh)
    setSpecificationId(savedId)
    setShowSpecDialog(false)
    onSpecCreated && onSpecCreated(fresh)
  }

  // Existing (already-finalized, non-temp) variant images must be re-staged
  // into temp for this editing session — ImageUpload assumes everything it's
  // handed lives in temp and is safe to crop/replace/delete.
  useEffect(() => {
    let cancelled = false
    if (isEdit && (variant.images || []).length > 0) {
      copyImagesToTemp(variant.images, `temp/variant-${index}`).then(staged => {
        if (!cancelled) setImages(staged)
      })
    }
    return () => { cancelled = true }
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [])

  const handleSubmit = async (e) => {
    e.preventDefault()
    if (!name.trim()) return
    setSaving(true)
    try {
      await onSave({
        ...(variant || {}),
        name: name.trim(),
        sku,
        regular_price: regularPrice,
        price: regularPrice,
        stock,
        // Leer + "nie niedrig" nicht angehakt → Standard 3 (gleiche Logik wie ProductForm.jsx).
        low_stock_threshold: neverLowStock ? null : (lowStockThreshold ? parseInt(lowStockThreshold, 10) : 3),
        never_low_stock: neverLowStock,
        show_exact_stock: showExactStock,
        specification_id: specificationId,
        tag_ids: tagIds,
        images,
      })
    } finally {
      setSaving(false)
    }
  }

  return (
    <div style={{
      position: 'fixed', inset: 0, background: 'rgba(0,0,0,0.7)', zIndex: 500,
      display: 'flex', alignItems: 'center', justifyContent: 'center'
    }}>
      <div style={{
        background: '#2F3B35', border: '1px solid rgba(155,111,208,0.3)',
        borderRadius: 8, width: 480, maxHeight: '85vh', overflowY: 'auto', display: 'flex', flexDirection: 'column',
        boxShadow: '0 24px 64px rgba(0,0,0,0.7)'
      }}>
        <div style={{ padding: '16px 20px', background: '#1A231F', borderBottom: '1px solid rgba(155,111,208,0.12)' }}>
          <div style={{ fontSize: 14, fontWeight: 700, color: '#EAE4D6' }}>
            {isEdit ? 'Variante bearbeiten' : 'Neue Variante'}
          </div>
          <div style={{ fontSize: 11, color: '#9CA59E', marginTop: 2 }}>
            Name, SKU, Preis, Lager, Spezifikation und Bilder — eigenständig pro Variante
          </div>
        </div>

        <form onSubmit={handleSubmit} style={{ padding: 20, display: 'flex', flexDirection: 'column', gap: 16 }}>
          <div>
            <label className="label">Variationsname *</label>
            <input className="input" value={name} onChange={e => setName(e.target.value)} placeholder="z. B. Klein" autoFocus required />
          </div>

          <div style={{ display: 'flex', gap: 12 }}>
            <div style={{ flex: 1 }}>
              <label className="label">SKU / Artikelnummer</label>
              <input className="input" value={sku} onChange={e => setSku(e.target.value)} placeholder="PA-00001" />
            </div>
            <div style={{ flex: 1 }}>
              <label className="label">Preis (€)</label>
              <input className="input" type="number" step="0.01" min="0" value={regularPrice}
                onChange={e => setRegularPrice(e.target.value)} placeholder="9.99" />
            </div>
          </div>

          <div style={{ display: 'flex', gap: 12 }}>
            <div style={{ flex: 1 }}>
              <label className="label">Lagerbestand</label>
              <input className="input" type="number" min="0" value={stock} onChange={e => setStock(e.target.value)} placeholder="0" />
            </div>
            <div style={{ flex: 1 }}>
              <label className="label">Niedrig-Bestand Schwellwert</label>
              <input className="input" type="number" min="0" value={lowStockThreshold} onChange={e => setLowStockThreshold(e.target.value)} placeholder="Standard (3)" disabled={neverLowStock} />
            </div>
          </div>
          <label style={{ display: 'flex', gap: 8, alignItems: 'center', cursor: 'pointer' }}>
            <input type="checkbox" checked={neverLowStock} onChange={e => setNeverLowStock(e.target.checked)} />
            <span style={{ fontSize: 12, color: '#C8C0AF' }}>Nie als „Niedriger Bestand" markieren</span>
          </label>
          <label style={{ display: 'flex', gap: 8, alignItems: 'center', cursor: 'pointer', marginTop: -8 }}>
            <input type="checkbox" checked={showExactStock} onChange={e => setShowExactStock(e.target.checked)} />
            <span style={{ fontSize: 12, color: '#C8C0AF' }}>Genaue Stückzahl bei niedrigem Bestand anzeigen (sonst nur „Niedriger Bestand")</span>
          </label>

          <div>
            <label className="label">Produktspezifikation</label>
            <div style={{ display: 'flex', gap: 8 }}>
              <select className="input" value={specificationId || ''} style={{ flex: 1 }}
                onChange={e => setSpecificationId(e.target.value ? parseInt(e.target.value, 10) : null)}>
                <option value="">— keine —</option>
                {specs.map(s => <option key={s.id} value={s.id}>{s.name}</option>)}
              </select>
              <button type="button" className="btn-secondary" onClick={() => setShowSpecDialog(true)} style={{ whiteSpace: 'nowrap' }}>
                + Neue
              </button>
            </div>
          </div>

          <div>
            <label className="label">Tags</label>
            <TagPool
              entityType="variant"
              entityId={variant?.id ?? null}
              onChange={setTagIds}
              gattungId={gattungId}
              gattungName={gattungName}
              artId={artId}
              artName={artName}
            />
          </div>

          <div>
            <label className="label">Bilder</label>
            <ImageUpload images={images} setImages={setImages} />
          </div>
        </form>

        <div style={{ padding: '14px 20px', borderTop: '1px solid rgba(155,111,208,0.15)', display: 'flex', gap: 10, justifyContent: 'flex-end' }}>
          <button type="button" className="btn-secondary" onClick={onCancel} disabled={saving}>Abbrechen</button>
          <button type="submit" className="btn-primary" onClick={handleSubmit} disabled={saving || !name.trim()}>
            {saving ? 'Speichere...' : 'Speichern'}
          </button>
        </div>
      </div>

      {showSpecDialog && (
        <SpecificationDialog
          onSave={handleSaveSpecification}
          onCancel={() => setShowSpecDialog(false)}
        />
      )}
    </div>
  )
}
