import React, { useState, useEffect } from 'react'

export default function SpecificationDialog({ onSave, onCancel, editSpec = null, specType = 'plant' }) {
  const isPackage = specType === 'package'
  const [potSize, setPotSize] = useState('')
  const [shape, setShape] = useState('round') // 'round' or 'square'
  const [weight, setWeight] = useState('')
  const [weightUnit, setWeightUnit] = useState('g') // 'g' or 'kg'
  const [height, setHeight] = useState('')
  const [width, setWidth] = useState('')
  const [saving, setSaving] = useState(false)

  // Pre-fill if editing
  useEffect(() => {
    if (editSpec) {
      setPotSize(editSpec.pot_size_cm?.toString() || '')
      setShape(editSpec.shape || 'round')
      setWeight(editSpec.weight?.toString() || '')
      setWeightUnit(editSpec.weight_unit || 'g')
      setHeight(editSpec.height_cm?.toString() || '')
      setWidth(editSpec.width_cm?.toString() || '')
    }
  }, [editSpec])

  // Generate name from fields
  const generateName = () => {
    const pot = !isPackage && potSize ? `${potSize}cm` : ''
    const shapeLabel = !isPackage ? (shape === 'round' ? 'rund' : 'eckig') : ''
    const weightLabel = weight ? `${weight} ${weightUnit}` : ''
    const dims = height && width ? `${height} x ${width}` : ''

    const parts = [pot, shapeLabel, weightLabel, dims].filter(Boolean)
    return parts.join(' - ')
  }

  const handleSubmit = async (e) => {
    e.preventDefault()
    if (!isPackage && !potSize) {
      alert('Topfgröße ist Pflichtfeld')
      return
    }
    if (!weight) {
      alert('Gewicht ist Pflichtfeld')
      return
    }

    setSaving(true)
    try {
      const spec = {
        id: editSpec?.id,
        name: generateName(),
        // Ohne Topfgröße (Substrat/Dünger-Presets) bleibt die NOT-NULL-Spalte
        // pot_size_cm mit 0 als nie angezeigtem Platzhalter befüllt.
        pot_size_cm: isPackage ? 0 : parseFloat(potSize),
        shape,
        weight: parseFloat(weight),
        weight_unit: weightUnit,
        height_cm: height ? parseFloat(height) : null,
        width_cm: width ? parseFloat(width) : null,
        spec_type: specType,
      }
      await onSave(spec)
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
        borderRadius: 8, width: 480, maxHeight: '85vh', display: 'flex', flexDirection: 'column',
        boxShadow: '0 24px 64px rgba(0,0,0,0.7)'
      }}>
        {/* Header */}
        <div style={{ padding: '16px 20px', background: '#1A231F', borderBottom: '1px solid rgba(155,111,208,0.12)' }}>
          <div style={{ fontSize: 14, fontWeight: 700, color: '#EAE4D6' }}>
            {editSpec ? 'Spezifikation bearbeiten' : 'Neue Spezifikation'}
          </div>
          <div style={{ fontSize: 11, color: '#9CA59E', marginTop: 2 }}>
            {isPackage ? 'Gewicht und Maße' : 'Topfgröße, Form, Gewicht und Maße'}
          </div>
        </div>

        {/* Body */}
        <form onSubmit={handleSubmit} style={{ padding: 20, display: 'flex', flexDirection: 'column', gap: 16 }}>
          {!isPackage && (
          <>
          {/* Topfgröße */}
          <div>
            <label className="label">Topfgröße (cm) *</label>
            <input
              className="input"
              type="number"
              step="0.5"
              min="0"
              value={potSize}
              onChange={e => setPotSize(e.target.value)}
              placeholder="z. B. 12"
              required
            />
          </div>

          {/* Form Toggle */}
          <div>
            <label className="label">Topfform *</label>
            <div style={{ display: 'flex', gap: 8 }}>
              <button
                type="button"
                onClick={() => setShape('round')}
                style={{
                  flex: 1,
                  padding: '10px',
                  background: shape === 'round' ? 'rgba(155,111,208,0.2)' : 'rgba(155,111,208,0.08)',
                  border: shape === 'round' ? '1px solid rgba(155,111,208,0.4)' : '1px solid rgba(155,111,208,0.2)',
                  borderRadius: 6,
                  color: shape === 'round' ? '#B8A8D8' : '#9CA59E',
                  cursor: 'pointer',
                  fontSize: 13,
                  fontWeight: 500
                }}
              >
                ⭘ Rund
              </button>
              <button
                type="button"
                onClick={() => setShape('square')}
                style={{
                  flex: 1,
                  padding: '10px',
                  background: shape === 'square' ? 'rgba(155,111,208,0.2)' : 'rgba(155,111,208,0.08)',
                  border: shape === 'square' ? '1px solid rgba(155,111,208,0.4)' : '1px solid rgba(155,111,208,0.2)',
                  borderRadius: 6,
                  color: shape === 'square' ? '#B8A8D8' : '#9CA59E',
                  cursor: 'pointer',
                  fontSize: 13,
                  fontWeight: 500
                }}
              >
                ⬜ Eckig
              </button>
            </div>
          </div>
          </>
          )}

          {/* Gewicht */}
          <div>
            <label className="label">Gewicht *</label>
            <div style={{ display: 'flex', gap: 8 }}>
              <input
                className="input"
                type="number"
                step="0.1"
                min="0"
                value={weight}
                onChange={e => setWeight(e.target.value)}
                placeholder="z. B. 250"
                required
                style={{ flex: 1 }}
              />
              <select
                className="input"
                value={weightUnit}
                onChange={e => setWeightUnit(e.target.value)}
                style={{ width: 80 }}
              >
                <option value="g">g</option>
                <option value="kg">kg</option>
              </select>
            </div>
          </div>

          {/* Maße */}
          <div>
            <label className="label">Maße (Höhe × Breite in cm)</label>
            <div style={{ display: 'flex', gap: 8, alignItems: 'center' }}>
              <input
                className="input"
                type="number"
                step="0.5"
                min="0"
                value={height}
                onChange={e => setHeight(e.target.value)}
                placeholder="Höhe"
                style={{ flex: 1 }}
              />
              <span style={{ color: '#9CA59E', fontSize: 14 }}>×</span>
              <input
                className="input"
                type="number"
                step="0.5"
                min="0"
                value={width}
                onChange={e => setWidth(e.target.value)}
                placeholder="Breite"
                style={{ flex: 1 }}
              />
            </div>
          </div>

          {/* Preview */}
          <div style={{
            padding: '12px',
            background: 'rgba(155,111,208,0.08)',
            border: '1px solid rgba(155,111,208,0.2)',
            borderRadius: 6,
            fontSize: 12,
            color: '#B8A8D8'
          }}>
            <div style={{ fontSize: 10, color: '#9CA59E', marginBottom: 4, textTransform: 'uppercase', letterSpacing: 1 }}>
              Vorschau Name
            </div>
            {generateName() || '-'}
          </div>
        </form>

        {/* Footer */}
        <div style={{ padding: '14px 20px', borderTop: '1px solid rgba(155,111,208,0.15)', display: 'flex', gap: 10, justifyContent: 'flex-end' }}>
          <button
            type="button"
            className="btn-secondary"
            onClick={onCancel}
            disabled={saving}
          >
            Abbrechen
          </button>
          <button
            type="submit"
            className="btn-primary"
            onClick={handleSubmit}
            disabled={saving}
          >
            {saving ? 'Speichere...' : 'Speichern'}
          </button>
        </div>
      </div>
    </div>
  )
}
