import React, { useEffect, useState } from 'react'
import { generatePreviewHtml } from '../utils/generatePreviewHtml.js'

const DEVICES = [
  { id: 'desktop', label: '🖥 Desktop', width: null },
  { id: 'tablet', label: '⬜ Tablet', width: 768 },
  { id: 'mobile', label: '📱 Mobil', width: 390 },
]

export default function PreviewPanel({ product, onClose }) {
  const [device, setDevice] = useState('desktop')
  const [imageDataUrls, setImageDataUrls] = useState(null) // null = loading
  const [variants, setVariants] = useState([])
  const [potSize, setPotSize] = useState(null) // { pot_size_cm, shape } — simple products only

  useEffect(() => {
    let cancelled = false
    async function load() {
      setImageDataUrls(null)
      let urls = []
      let vars = []
      let pot = null
      if (product.id) {
        try {
          if (product.is_variable) {
            const [rawVariants, allSpecs] = await Promise.all([
              window.api.getVariants(product.id),
              window.api.getSpecifications(),
            ])
            const specById = new Map((allSpecs || []).map(s => [s.id, s]))
            const sortedVariants = (rawVariants || []).sort((a, b) => (a.sort_order || 0) - (b.sort_order || 0))
            // Base product carries no images of its own once it's variable —
            // every variant needs its own gallery loaded so switching bubbles
            // has something to swap to (not just the first one).
            vars = await Promise.all(sortedVariants.map(async v => {
              const sorted = (v.images || []).sort((a, b) => (a.sort_order || 0) - (b.sort_order || 0))
              const raw = await Promise.all(
                sorted.filter(i => i.local_path).map(i => window.api.readFileAsDataUrl(i.local_path))
              )
              const spec = v.specification_id ? specById.get(v.specification_id) : null
              return { ...v, imageDataUrls: raw.filter(Boolean), pot_size_cm: spec?.pot_size_cm ?? null, pot_shape: spec?.shape ?? null }
            }))
            urls = vars[0]?.imageDataUrls || []
          } else {
            const [imgs, specs] = await Promise.all([
              window.api.getProductImages(product.id),
              window.api.getProductSpecifications(product.id),
            ])
            const sorted = (imgs || []).sort((a, b) => (a.sort_order || 0) - (b.sort_order || 0))
            const raw = await Promise.all(
              sorted.filter(i => i.local_path).map(i => window.api.readFileAsDataUrl(i.local_path))
            )
            urls = raw.filter(Boolean)
            pot = specs?.[0] ? { pot_size_cm: specs[0].pot_size_cm, shape: specs[0].shape } : null
          }
        } catch (_) {}
      }
      if (!cancelled) { setImageDataUrls(urls); setVariants(vars); setPotSize(pot) }
    }
    load()
    return () => { cancelled = true }
  }, [product.id, product.is_variable])

  const previewProduct = potSize ? { ...product, pot_size_cm: potSize.pot_size_cm, pot_shape: potSize.shape } : product
  const html = imageDataUrls !== null
    ? generatePreviewHtml(previewProduct, imageDataUrls, device, variants)
    : null

  const current = DEVICES.find(d => d.id === device) || DEVICES[0]
  const title = product.kultivar
    ? `'${product.kultivar}'`
    : product.art || product.gattung || product.name || 'Produkt'

  const btn = (active) => ({
    background: active ? 'rgba(155,111,208,0.2)' : 'transparent',
    border: `1px solid ${active ? '#9B6FD0' : 'rgba(155,111,208,0.2)'}`,
    color: active ? '#C8A8F0' : '#9CA59E',
    borderRadius: 4, padding: '4px 12px', cursor: 'pointer', fontSize: 12,
  })

  return (
    <div style={{ position: 'fixed', inset: 0, zIndex: 400, display: 'flex', flexDirection: 'column', background: '#161E1A' }}>
      {/* Toolbar */}
      <div style={{ flexShrink: 0, display: 'flex', alignItems: 'center', gap: 8, padding: '8px 16px', background: '#25302B', borderBottom: '1px solid rgba(155,111,208,0.2)' }}>
        <button onClick={onClose} style={{ ...btn(false), color: '#EAE4D6', marginRight: 4 }}>
          ✕ Schließen
        </button>
        <span style={{ flex: 1, fontSize: 13, color: '#C8C0AF', fontStyle: 'italic', overflow: 'hidden', textOverflow: 'ellipsis', whiteSpace: 'nowrap' }}>
          {title}
          {product.status && product.status !== 'publish' && (
            <span style={{ marginLeft: 10, fontSize: 10, color: '#9CA59E', fontStyle: 'normal', letterSpacing: '0.1em', textTransform: 'uppercase' }}>
              {product.status}
            </span>
          )}
        </span>
        {DEVICES.map(d => (
          <button key={d.id} style={btn(device === d.id)} onClick={() => setDevice(d.id)}>
            {d.label}
          </button>
        ))}
      </div>

      {/* Viewport */}
      <div style={{ flex: 1, overflow: 'auto', display: 'flex', justifyContent: 'center', alignItems: 'flex-start', background: '#161E1A', padding: current.width ? '20px' : '0' }}>
        {!html ? (
          <div style={{ color: '#9CA59E', fontSize: 13, margin: 'auto' }}>Lade Vorschau…</div>
        ) : (
          <div style={{
            width: current.width ? `${current.width}px` : '100%',
            height: current.width ? 'auto' : '100%',
            minHeight: '100%',
            background: '#fff',
            boxShadow: current.width ? '0 8px 40px rgba(0,0,0,0.7)' : 'none',
            display: 'flex', flexShrink: 0,
          }}>
            <iframe
              key={device}
              srcDoc={html}
              style={{ flex: 1, border: 'none', minHeight: current.width ? 600 : '100%', width: '100%' }}
              title="Produktvorschau"
              sandbox="allow-scripts"
            />
          </div>
        )}
      </div>
    </div>
  )
}
