import React, { useState, useRef, useEffect, useContext, useCallback } from 'react'
import { ToastContext } from '../App.jsx'
import logoUrl from '../assets/logo.svg?url'

const CWM_SIZE = 320
const EXPORT_SIZE = 1024
const PAD_COLOR = '#FF0033'
// Watermark has a fixed HEIGHT relative to the exported image, not
// user-resizable. Expressed as a ratio (not an absolute export pixel count)
// so it comes out correctly proportioned no matter what the actual export
// canvas size ends up being — a fixed px count only stayed correct as long
// as it was kept in manual lockstep with EXPORT_SIZE, which silently broke
// the moment the two drifted apart (e.g. a differently-sized export).
const WM_HEIGHT_RATIO = 0.125 // was 128px at the current 1024px export size

function cwmRoundRect(ctx, x, y, w, h, r) {
  ctx.beginPath()
  ctx.moveTo(x + r, y)
  ctx.lineTo(x + w - r, y)
  ctx.arcTo(x + w, y, x + w, y + r, r)
  ctx.lineTo(x + w, y + h - r)
  ctx.arcTo(x + w, y + h, x + w - r, y + h, r)
  ctx.lineTo(x + r, y + h)
  ctx.arcTo(x, y + h, x, y + h - r, r)
  ctx.lineTo(x, y + r)
  ctx.arcTo(x, y, x + r, y, r)
  ctx.closePath()
}

function drawGrip(ctx, cx, cy, dir) {
  const w = dir === 'h' ? 28 : 8
  const h = dir === 'h' ? 8 : 28
  ctx.fillStyle = 'rgba(255,255,255,0.9)'
  ctx.strokeStyle = '#666'
  ctx.lineWidth = 1
  ctx.beginPath()
  ctx.roundRect(cx - w/2, cy - h/2, w, h, 4)
  ctx.fill(); ctx.stroke()
  // 3 dots
  ctx.fillStyle = '#999'
  for (let i = -1; i <= 1; i++) {
    const ox = dir === 'h' ? i * 8 : 0
    const oy = dir === 'v' ? i * 8 : 0
    ctx.beginPath()
    ctx.arc(cx + ox, cy + oy, 1.5, 0, Math.PI * 2)
    ctx.fill()
  }
}

function CropModal({ imagePath, onConfirm, onCancel }) {
  const canvasRef = useRef(null)
  const s = useRef({
    img: null,
    imgX: 0, imgY: 0, imgScale: 1, minScale: 1,
    wmX: 0, wmY: 0, wmW: 0, logoAspect: 1,
    drag: null, lastX: 0, lastY: 0,
    rectEnabled: false, rectOrientation: 'horizontal',
    barA: 0, barB: 0,
  })
  const logoRef = useRef(null)
  const showWmRef = useRef(true)
  const invertWmRef = useRef(false)
  const [showWm, setShowWm] = useState(true)
  const [invertWm, setInvertWm] = useState(false)
  const [rectEnabled, setRectEnabled] = useState(false)
  const [orientation, setOrientation] = useState('horizontal')
  const [zoom, setZoom] = useState(100)
  const [saving, setSaving] = useState(false)
  const rafRef = useRef(null)

  const renderBase = useCallback((ctx, scale, forExport) => {
    const c = s.current
    const S = CWM_SIZE * scale
    ctx.clearRect(0, 0, S, S)
    ctx.fillStyle = PAD_COLOR
    ctx.fillRect(0, 0, S, S)

    // Bars stay independently draggable (asymmetric crop input — "cut 100px
    // off the left, 200px off the right") and the LIVE EDITING VIEW always
    // shows them exactly as dragged (raw barA/barB, no shift) — nothing
    // should appear to move just because the other bar was touched. Only
    // the actual EXPORT (forExport, rendered once on a separate offscreen
    // canvas in handleSave(), never shown interactively) gets recentered:
    // both margins become the average, and the photo is shifted by
    // (avg - barA) so exactly the same crop content stays visible, just
    // centered — the visible window width is unchanged
    // (S - barA - barB === S - 2*avg), only its position moves.
    let shiftX = 0, shiftY = 0, barLeft = 0, barRight = 0
    if (c.rectEnabled) {
      if (forExport) {
        const avg = (c.barA + c.barB) / 2
        const shift = (c.barB - c.barA) / 2
        barLeft = avg * scale; barRight = avg * scale
        if (c.rectOrientation === 'horizontal') shiftY = shift * scale
        else shiftX = shift * scale
      } else {
        barLeft = c.barA * scale; barRight = c.barB * scale
      }
    }

    if (c.img) {
      let drawX = c.imgX * scale + shiftX
      let drawY = c.imgY * scale + shiftY
      const w = c.img.naturalWidth * c.imgScale * scale
      const h = c.img.naturalHeight * c.imgScale * scale
      // Export-only safety net: if an extreme bar asymmetry combined with
      // zero zoom slack would push the shifted photo short of covering the
      // canvas, clamp just this render's draw position (never the live
      // c.imgX/c.imgY state) so the export can't show a gap at the edge.
      // Only applies on an axis the photo actually covers (w/h >= S) — for
      // a letterboxed photo (the whole point of the bars feature) the shift
      // is already correct and must not be clamped back toward 0.
      if (forExport) {
        if (shiftX !== 0 && w >= S) drawX = Math.max(Math.min(0, S - w), Math.min(0, drawX))
        if (shiftY !== 0 && h >= S) drawY = Math.max(Math.min(0, S - h), Math.min(0, drawY))
      }
      ctx.drawImage(c.img, drawX, drawY, w, h)
    }
    if (c.rectEnabled) {
      if (forExport) {
        if (c.rectOrientation === 'horizontal') {
          ctx.clearRect(0, 0, S, barLeft); ctx.clearRect(0, S - barRight, S, barRight)
        } else {
          ctx.clearRect(0, 0, barLeft, S); ctx.clearRect(S - barRight, 0, barRight, S)
        }
      } else {
        ctx.fillStyle = '#000'
        if (c.rectOrientation === 'horizontal') {
          ctx.fillRect(0, 0, S, barLeft); ctx.fillRect(0, S - barRight, S, barRight)
        } else {
          ctx.fillRect(0, 0, barLeft, S); ctx.fillRect(S - barRight, 0, barRight, S)
        }
      }
    }
    const logo = logoRef.current
    if (showWmRef.current && logo && logo.complete && logo.naturalWidth) {
      const wmW = c.wmW * scale
      const wmH = Math.round(wmW * c.logoAspect)
      // Export shifts the watermark by the same amount as the photo — the
      // clamp already in effect during editing ([barA, S-barB-wmH]) maps
      // exactly onto the new centered window ([avg, S-avg-wmH]) under this
      // shift, so no separate re-clamping is needed here.
      const wmX = c.wmX * scale + shiftX
      const wmY = c.wmY * scale + shiftY
      const pad = Math.round(wmW * 0.03)
      const r = Math.round(Math.min(wmW, wmH) * 0.2)
      ctx.save(); ctx.globalAlpha = 0.37; ctx.fillStyle = '#7a7a7a'
      cwmRoundRect(ctx, wmX, wmY, wmW, wmH, r); ctx.fill(); ctx.restore()
      ctx.save()
      if (invertWmRef.current) ctx.filter = 'invert(1)'
      ctx.globalAlpha = 0.88
      ctx.drawImage(logo, wmX + pad, wmY + pad, wmW - 2 * pad, wmH - 2 * pad)
      ctx.restore()
    }
  }, [])

  const render = useCallback(() => {
    if (rafRef.current) cancelAnimationFrame(rafRef.current)
    rafRef.current = requestAnimationFrame(() => {
      const canvas = canvasRef.current
      if (!canvas) return
      const ctx = canvas.getContext('2d')
      renderBase(ctx, 1, false)
      const c = s.current
      const S = CWM_SIZE
      // Watermark size is fixed (WM_HEIGHT_RATIO) — only position is draggable,
      // so no resize handles are drawn; the move cursor (see hitTarget) is the
      // only affordance needed.
      // Improved bar handles with dashed lines and grip pills
      if (c.rectEnabled) {
        ctx.save()
        ctx.strokeStyle = 'rgba(255,255,255,0.85)'
        ctx.lineWidth = 2
        ctx.setLineDash([6, 4])
        if (c.rectOrientation === 'horizontal') {
          const yA = c.barA, yB = S - c.barB
          ctx.beginPath(); ctx.moveTo(0, yA); ctx.lineTo(S, yA); ctx.stroke()
          ctx.beginPath(); ctx.moveTo(0, yB); ctx.lineTo(S, yB); ctx.stroke()
          ctx.setLineDash([])
          drawGrip(ctx, S/2, yA, 'h')
          drawGrip(ctx, S/2, yB, 'h')
        } else {
          const xA = c.barA, xB = S - c.barB
          ctx.beginPath(); ctx.moveTo(xA, 0); ctx.lineTo(xA, S); ctx.stroke()
          ctx.beginPath(); ctx.moveTo(xB, 0); ctx.lineTo(xB, S); ctx.stroke()
          ctx.setLineDash([])
          drawGrip(ctx, xA, S/2, 'v')
          drawGrip(ctx, xB, S/2, 'v')
        }
        ctx.restore()
      }
    })
  }, [renderBase])

  useEffect(() => {
    const logo = new Image()
    logo.onload = () => {
      s.current.logoAspect = logo.naturalHeight / logo.naturalWidth
      logoRef.current = logo; render()
    }
    logo.src = logoUrl
  }, [render])

  useEffect(() => {
    if (!imagePath) return
    const img = new Image()
    img.onload = () => {
      const c = s.current
      const ms = Math.min(CWM_SIZE / img.naturalWidth, CWM_SIZE / img.naturalHeight)
      c.img = img; c.minScale = ms; c.imgScale = ms
      c.imgX = (CWM_SIZE - img.naturalWidth * ms) / 2
      c.imgY = (CWM_SIZE - img.naturalHeight * ms) / 2
      const pad = Math.round(CWM_SIZE * 0.02)
      recomputeWatermarkSize()
      c.wmX = CWM_SIZE - c.wmW - pad
      c.wmY = CWM_SIZE - Math.round(c.wmW * c.logoAspect) - pad
      render()
    }
    img.src = `file://${imagePath}`
  }, [imagePath, render])

  useEffect(() => {
    const canvas = canvasRef.current; if (!canvas) return
    const onWheel = (e) => {
      e.preventDefault()
      const c = s.current; if (!c.img) return
      const rect = canvas.getBoundingClientRect()
      const px = (e.clientX - rect.left) * (CWM_SIZE / rect.width)
      const py = (e.clientY - rect.top) * (CWM_SIZE / rect.height)
      const delta = e.deltaY > 0 ? -0.08 : 0.08
      const newScale = Math.max(c.minScale, c.imgScale * (1 + delta))
      c.imgX = px - (px - c.imgX) * (newScale / c.imgScale)
      c.imgY = py - (py - c.imgY) * (newScale / c.imgScale)
      c.imgScale = newScale
      clampImage()
      // Sync slider
      const pct = Math.round(((newScale - c.minScale) / (c.minScale * 4)) * 400 + 100)
      setZoom(Math.min(500, Math.max(100, pct)))
      render()
    }
    canvas.addEventListener('wheel', onWheel, { passive: false })
    return () => canvas.removeEventListener('wheel', onWheel)
  }, [render])

  function getPos(e) {
    const canvas = canvasRef.current
    const rect = canvas.getBoundingClientRect()
    const cx = e.touches ? e.touches[0].clientX : e.clientX
    const cy = e.touches ? e.touches[0].clientY : e.clientY
    return { x: (cx - rect.left) * (CWM_SIZE / rect.width), y: (cy - rect.top) * (CWM_SIZE / rect.height) }
  }

  function hitTarget(x, y) {
    const c = s.current; if (!c.img) return null
    const wmH = Math.round(c.wmW * c.logoAspect)
    const BAR_HIT = 12
    // Bars first (highest priority)
    if (c.rectEnabled) {
      if (c.rectOrientation === 'horizontal') {
        if (Math.abs(y - c.barA) <= BAR_HIT) return 'bar-a'
        if (Math.abs(y - (CWM_SIZE - c.barB)) <= BAR_HIT) return 'bar-b'
      } else {
        if (Math.abs(x - c.barA) <= BAR_HIT) return 'bar-a'
        if (Math.abs(x - (CWM_SIZE - c.barB)) <= BAR_HIT) return 'bar-b'
      }
    }
    // Watermark body — position only, size is fixed (no resize handles).
    if (showWmRef.current) {
      if (x >= c.wmX && x <= c.wmX + c.wmW && y >= c.wmY && y <= c.wmY + wmH) return 'wm'
    }
    return 'image'
  }

  function onMouseDown(e) {
    e.preventDefault()
    const pos = getPos(e)
    s.current.drag = hitTarget(pos.x, pos.y)
    s.current.lastX = pos.x; s.current.lastY = pos.y
    if (s.current.drag) canvasRef.current.style.cursor = 'grabbing'
  }

  function onMouseMove(e) {
    e.preventDefault()
    const c = s.current
    const pos = getPos(e)

    // Cursor update when no drag active
    if (!c.drag) {
      const hit = hitTarget(pos.x, pos.y)
      if (!canvasRef.current) return
      if (hit === 'bar-a' || hit === 'bar-b') {
        canvasRef.current.style.cursor = c.rectOrientation === 'horizontal' ? 'ns-resize' : 'ew-resize'
      } else if (hit === 'wm') {
        canvasRef.current.style.cursor = 'move'
      } else {
        canvasRef.current.style.cursor = 'grab'
      }
      return
    }

    const dx = pos.x - c.lastX, dy = pos.y - c.lastY
    c.lastX = pos.x; c.lastY = pos.y
    const MAX_BAR = CWM_SIZE * 0.48
    if (c.drag === 'image') { c.imgX += dx; c.imgY += dy; clampImage() }
    else if (c.drag === 'wm') { c.wmX += dx; c.wmY += dy; clampWatermark() }
    else if (c.drag === 'bar-a') {
      if (c.rectOrientation === 'horizontal') c.barA = Math.max(0, Math.min(MAX_BAR, c.barA + dy))
      else c.barA = Math.max(0, Math.min(MAX_BAR, c.barA + dx))
      recomputeWatermarkSize(); clampWatermark()
    } else if (c.drag === 'bar-b') {
      if (c.rectOrientation === 'horizontal') c.barB = Math.max(0, Math.min(MAX_BAR, c.barB - dy))
      else c.barB = Math.max(0, Math.min(MAX_BAR, c.barB - dx))
      recomputeWatermarkSize(); clampWatermark()
    }
    render()
  }

  // Keeps the photo fully covering the crop square after panning/zooming —
  // without this, dragging could leave blank canvas showing at an edge.
  // Purely a function of zoom/pan — bars no longer affect this at all, since
  // the export-only recentering shift (see renderBase) is applied and
  // safety-clamped locally at export time, never to this live state.
  function clampImage() {
    const c = s.current
    if (!c.img) return
    const w = c.img.naturalWidth * c.imgScale
    const h = c.img.naturalHeight * c.imgScale
    c.imgX = w >= CWM_SIZE
      ? Math.max(CWM_SIZE - w, Math.min(0, c.imgX))
      : Math.max(0, Math.min(CWM_SIZE - w, c.imgX))
    c.imgY = h >= CWM_SIZE
      ? Math.max(CWM_SIZE - h, Math.min(0, c.imgY))
      : Math.max(0, Math.min(CWM_SIZE - h, c.imgY))
  }

  // Fixed ratio of the full CWM_SIZE canvas, regardless of bars — matches
  // what vertical bars already did (their axis never fed into this at all).
  // Horizontal bars used to shrink this with the visible strip, which made
  // the watermark keep shrinking the more of the image got covered; now
  // size stays constant and clampWatermark() alone keeps it positioned
  // inside the visible strip.
  function recomputeWatermarkSize() {
    const c = s.current
    const wmHCwm = WM_HEIGHT_RATIO * CWM_SIZE
    c.wmW = Math.round(wmHCwm / c.logoAspect)
  }

  function clampWatermark() {
    const c = s.current
    if (!c.rectEnabled) return
    const wmH = Math.round(c.wmW * c.logoAspect)
    if (c.rectOrientation === 'horizontal') {
      const minY = c.barA
      const maxY = CWM_SIZE - c.barB - wmH
      if (maxY > minY) c.wmY = Math.max(minY, Math.min(maxY, c.wmY))
    } else {
      const minX = c.barA
      const maxX = CWM_SIZE - c.barB - c.wmW
      if (maxX > minX) c.wmX = Math.max(minX, Math.min(maxX, c.wmX))
    }
  }

  function snapToPicture() {
    const c = s.current
    if (!c.img || !c.rectEnabled) return
    const MAX_BAR = CWM_SIZE * 0.48
    if (c.rectOrientation === 'horizontal') {
      const h = c.img.naturalHeight * c.imgScale
      c.barA = Math.max(0, Math.min(MAX_BAR, c.imgY))
      c.barB = Math.max(0, Math.min(MAX_BAR, CWM_SIZE - (c.imgY + h)))
    } else {
      const w = c.img.naturalWidth * c.imgScale
      c.barA = Math.max(0, Math.min(MAX_BAR, c.imgX))
      c.barB = Math.max(0, Math.min(MAX_BAR, CWM_SIZE - (c.imgX + w)))
    }
    recomputeWatermarkSize(); clampWatermark(); render()
  }

  function onMouseUp() { s.current.drag = null; if (canvasRef.current) canvasRef.current.style.cursor = 'grab' }

  function handleZoomSlider(val) {
    const c = s.current
    if (!c.img) return
    const factor = (val - 100) / 400
    const newScale = c.minScale * (1 + factor * 4)
    const cx = CWM_SIZE / 2
    const cy = CWM_SIZE / 2
    c.imgX = cx - (cx - c.imgX) * (newScale / c.imgScale)
    c.imgY = cy - (cy - c.imgY) * (newScale / c.imgScale)
    c.imgScale = newScale
    clampImage()
    render()
  }

  async function handleSave() {
    setSaving(true)
    try {
      const c = s.current
      const offscreen = document.createElement('canvas')
      offscreen.width = EXPORT_SIZE; offscreen.height = EXPORT_SIZE
      renderBase(offscreen.getContext('2d'), EXPORT_SIZE / CWM_SIZE, true)
      const useRect = c.rectEnabled
      const mimeType = useRect ? 'image/png' : 'image/jpeg'
      const quality = useRect ? undefined : 0.93
      const ext = useRect ? '.png' : '.jpg'
      const blob = await new Promise(resolve => offscreen.toBlob(resolve, mimeType, quality))
      const arrayBuffer = await blob.arrayBuffer()
      onConfirm(arrayBuffer, ext)
    } finally { setSaving(false) }
  }

  return (
    <div style={{ position: 'fixed', inset: 0, background: 'rgba(0,0,0,0.8)', zIndex: 200, display: 'flex', alignItems: 'center', justifyContent: 'center' }}>
      <div style={{ background: '#2F3B35', border: '1px solid rgba(155,111,208,0.3)', borderRadius: 8, padding: 24, display: 'flex', gap: 20, alignItems: 'flex-start', maxWidth: '90vw' }}>
        <canvas ref={canvasRef} width={CWM_SIZE} height={CWM_SIZE}
          style={{ borderRadius: 4, cursor: 'grab', flexShrink: 0, userSelect: 'none', touchAction: 'none' }}
          onMouseDown={onMouseDown} onMouseMove={onMouseMove} onMouseUp={onMouseUp} onMouseLeave={onMouseUp} />
        <div style={{ minWidth: 180, display: 'flex', flexDirection: 'column', gap: 14 }}>
          <h3 style={{ color: '#EAE4D6', margin: 0, fontSize: 15 }}>Bild zuschneiden</h3>
          <div>
            <div style={{ fontSize: 11, fontWeight: 600, color: '#9CA59E', textTransform: 'uppercase', letterSpacing: 1, marginBottom: 6 }}>Wasserzeichen</div>
            <label style={{ display: 'flex', alignItems: 'center', gap: 7, fontSize: 13, color: '#C8C0AF', cursor: 'pointer' }}>
              <input type="checkbox" checked={showWm} onChange={e => { showWmRef.current = e.target.checked; setShowWm(e.target.checked); render() }} />
              Logo anzeigen
            </label>
            <label style={{ display: 'flex', alignItems: 'center', gap: 7, fontSize: 13, color: '#C8C0AF', cursor: 'pointer', marginTop: 6 }}>
              <input type="checkbox" checked={invertWm} onChange={e => { invertWmRef.current = e.target.checked; setInvertWm(e.target.checked); render() }} />
              Farben invertieren
            </label>
            <div style={{ fontSize: 11, color: '#9CA59E', marginTop: 4 }}>Logo per Drag verschieben (feste Größe, Höhe = 12,5% der Bildhöhe).</div>
          </div>
          <div>
            <div style={{ fontSize: 11, fontWeight: 600, color: '#9CA59E', textTransform: 'uppercase', letterSpacing: 1, marginBottom: 6 }}>Zoom</div>
            <input
              type="range"
              min="100"
              max="500"
              value={zoom}
              onChange={e => { setZoom(parseInt(e.target.value)); handleZoomSlider(parseInt(e.target.value)) }}
              style={{ width: '100%', cursor: 'pointer' }}
            />
            <div style={{ fontSize: 11, color: '#9CA59E', marginTop: 4, textAlign: 'center' }}>{zoom}%</div>
          </div>
          <div>
            <div style={{ fontSize: 11, fontWeight: 600, color: '#9CA59E', textTransform: 'uppercase', letterSpacing: 1, marginBottom: 6 }}>Balken</div>
            <label style={{ display: 'flex', alignItems: 'center', gap: 7, fontSize: 13, color: '#C8C0AF', cursor: 'pointer' }}>
              <input type="checkbox" checked={rectEnabled} onChange={e => {
                const v = e.target.checked; s.current.rectEnabled = v
                if (v) { s.current.barA = Math.round(CWM_SIZE * 0.12); s.current.barB = Math.round(CWM_SIZE * 0.12) }
                else { s.current.barA = 0; s.current.barB = 0 }
                recomputeWatermarkSize(); clampWatermark()
                setRectEnabled(v); render()
              }} /> Balken einblenden
            </label>
            {rectEnabled && (
              <div style={{ marginTop: 8, display: 'flex', gap: 6 }}>
                {['horizontal', 'vertical'].map(o => (
                  <button key={o} type="button" onClick={() => { s.current.rectOrientation = o; recomputeWatermarkSize(); clampWatermark(); setOrientation(o); render() }}
                    style={{ fontSize: 11, padding: '3px 10px', borderRadius: 4, cursor: 'pointer',
                      background: orientation === o ? '#9B6FD0' : 'transparent',
                      border: '1px solid rgba(155,111,208,0.4)',
                      color: orientation === o ? '#fff' : '#C8C0AF' }}>
                    {o === 'horizontal' ? 'Hor.' : 'Vert.'}
                  </button>
                ))}
                <button type="button" onClick={snapToPicture}
                  style={{ fontSize: 11, padding: '3px 10px', borderRadius: 4, cursor: 'pointer',
                    background: 'transparent', border: '1px solid rgba(155,111,208,0.4)', color: '#C8C0AF' }}>
                  An Bild anpassen
                </button>
              </div>
            )}
            {rectEnabled && <div style={{ fontSize: 11, color: '#9CA59E', marginTop: 4 }}>Balken an Kanten ziehen → PNG-Export.</div>}
          </div>
          <div style={{ fontSize: 11, color: '#9CA59E' }}>Mausrad = Zoom · Drag = Verschieben</div>
          <div style={{ display: 'flex', gap: 8, marginTop: 4 }}>
            <button type="button" className="btn-secondary" onClick={onCancel} style={{ flex: 1 }}>Abbrechen</button>
            <button type="button" className="btn-primary" onClick={handleSave} disabled={saving} style={{ flex: 1 }}>
              {saving ? 'Speichere…' : 'Übernehmen'}
            </button>
          </div>
        </div>
      </div>
    </div>
  )
}

// Images always live in images/temp/ for the whole editing session — a product's real
// GAK folder is only touched once, atomically, when ProductForm actually saves.
export default function ImageUpload({ images, setImages }) {
  const addToast = useContext(ToastContext)
  const [cropTarget, setCropTarget] = useState(null)
  const [loading, setLoading] = useState(false)

  const openFile = async () => {
    const filePath = await window.api.openFile()
    if (filePath) setCropTarget(filePath)
  }

  const handleCropConfirm = async (arrayBuffer, ext) => {
    setCropTarget(null)
    setLoading(true)
    try {
      // Timestamp suffix guarantees uniqueness even if an earlier "Bild N" was deleted
      // in this session and the count would otherwise collide with a surviving file.
      const filename = `Bild ${images.length + 1}_${Date.now()}${ext}`
      const { path: savedPath, avifPath } = await window.api.saveImageBlob(arrayBuffer, 'temp', filename)
      setImages(imgs => [...imgs, { local_path: savedPath, avif_local_path: avifPath, filename, sort_order: imgs.length, watermark: 1, isTemp: true }])
    } catch (e) {
      addToast('Bild-Fehler: ' + (e?.message || e), 'error')
    } finally { setLoading(false) }
  }

  const deleteImage = async (img) => {
    try {
      await window.api.deleteFile(img.local_path)
    } catch (e) {
      // Ignore file deletion errors
    }
    setImages(imgs => imgs.filter(i => i !== img))
  }

  const handleDrop = async (e) => {
    e.preventDefault()
    const file = e.dataTransfer.files[0]
    if (file?.path) setCropTarget(file.path)
  }

  return (
    <div>
      <div onDrop={handleDrop} onDragOver={e => e.preventDefault()} onClick={openFile}
        style={{ border: '2px dashed rgba(155,111,208,0.3)', borderRadius: 6, padding: 20,
          textAlign: 'center', cursor: 'pointer', marginBottom: 12,
          background: 'rgba(155,111,208,0.05)', color: '#9CA59E', fontSize: 13 }}
        onMouseEnter={e => e.currentTarget.style.borderColor = 'rgba(155,111,208,0.6)'}
        onMouseLeave={e => e.currentTarget.style.borderColor = 'rgba(155,111,208,0.3)'}>
        {loading ? 'Verarbeite Bild…' : '+ Bild hinzufügen (klicken oder per Drag & Drop)'}
      </div>
      {images.length > 0 && (
        <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fill, minmax(90px, 1fr))', gap: 8 }}>
          {images.map((img, idx) => (
            <div key={img.local_path} style={{ position: 'relative', borderRadius: 4, overflow: 'hidden',
              border: idx === 0 ? '2px solid #9B6FD0' : '2px solid rgba(155,111,208,0.2)' }}>
              <img src={img.local_path ? `file://${img.local_path}` : img.remote_url} alt=""
                style={{ width: '100%', aspectRatio: '1', objectFit: 'cover', display: 'block' }} />
              {idx === 0 && <div style={{ position: 'absolute', bottom: 0, left: 0, right: 0,
                background: 'rgba(155,111,208,0.7)', color: '#fff', fontSize: 9, textAlign: 'center', padding: '2px 0' }}>Featured</div>}
              <button type="button" onClick={() => deleteImage(img)} style={{ position: 'absolute', top: 3, right: 3,
                background: 'rgba(90,26,46,0.85)', border: 'none', color: '#fff', borderRadius: '50%',
                width: 20, height: 20, fontSize: 10, cursor: 'pointer',
                display: 'flex', alignItems: 'center', justifyContent: 'center' }}>✕</button>
            </div>
          ))}
        </div>
      )}
      {cropTarget && <CropModal imagePath={cropTarget} onConfirm={handleCropConfirm} onCancel={() => setCropTarget(null)} />}
    </div>
  )
}
