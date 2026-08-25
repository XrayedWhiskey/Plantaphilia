import React, { useState } from 'react'

export function Section({ title, children, defaultOpen = true }) {
  const [open, setOpen] = useState(defaultOpen)
  return (
    // No overflow:hidden here — it used to double as corner-clipping for the header
    // button below, but that also clipped any dropdown (ComboSelect) opened inside
    // this section the moment it extended past the section's own bottom edge. The
    // button now rounds its own corners instead, so this can stay unclipped.
    <div style={{ marginBottom: 20, border: '1px solid rgba(155,111,208,0.12)', borderRadius: 6 }}>
      <button type="button" onClick={() => setOpen(o => !o)} style={{
        width: '100%', textAlign: 'left', padding: '10px 14px', background: '#1A231F',
        border: 'none', color: '#C8C0AF', cursor: 'pointer', display: 'flex', justifyContent: 'space-between',
        fontSize: 11, fontWeight: 700, textTransform: 'uppercase', letterSpacing: 1,
        borderRadius: open ? '6px 6px 0 0' : 6
      }}>
        {title}
        <span style={{ color: '#9CA59E' }}>{open ? '▲' : '▼'}</span>
      </button>
      {open && <div style={{ padding: '14px 14px' }}>{children}</div>}
    </div>
  )
}

export function Row({ children, cols = 2 }) {
  return (
    <div style={{ display: 'grid', gridTemplateColumns: `repeat(${cols}, 1fr)`, gap: 12, marginBottom: 12 }}>
      {children}
    </div>
  )
}

export function Field({ label, children, full, hint }) {
  return (
    <div style={full ? { marginBottom: 12, minWidth: 0 } : { minWidth: 0 }}>
      {label && (
        <label className="label">
          {label}
          {hint && <span style={{ color: '#9B6FD0', fontWeight: 400, textTransform: 'none', letterSpacing: 0 }}> {hint}</span>}
        </label>
      )}
      {children}
    </div>
  )
}

export function Toggle({ options, value, onChange }) {
  return (
    <div className="toggle-group">
      {options.map(o => (
        <button key={o.value} type="button"
          className={`toggle-btn${value === o.value ? ' active' : ''}`}
          onClick={() => onChange(o.value)}
        >{o.label}</button>
      ))}
    </div>
  )
}
