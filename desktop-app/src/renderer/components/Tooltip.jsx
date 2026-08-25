import React, { useState } from 'react'

// Small hover info-icon + popover, for explaining exactly what belongs in a
// field (used by the SEO fields — distinct from FormPrimitives' `hint`,
// which is always-visible inline text, not a hover popover).
export default function Tooltip({ text }) {
  const [open, setOpen] = useState(false)

  return (
    <span
      style={{ position: 'relative', display: 'inline-flex', marginLeft: 6 }}
      onMouseEnter={() => setOpen(true)}
      onMouseLeave={() => setOpen(false)}
    >
      <span style={{
        display: 'inline-flex', alignItems: 'center', justifyContent: 'center',
        width: 14, height: 14, borderRadius: '50%', border: '1px solid #9CA59E',
        color: '#9CA59E', fontSize: 10, fontWeight: 700, cursor: 'help', lineHeight: 1,
      }}>
        i
      </span>
      {open && (
        <div style={{
          position: 'absolute', bottom: '135%', left: '50%', transform: 'translateX(-50%)',
          width: 260, padding: '10px 12px', background: '#1A231F',
          border: '1px solid rgba(155,111,208,0.3)', borderRadius: 6,
          boxShadow: '0 12px 32px rgba(0,0,0,0.6)', zIndex: 50,
          fontSize: 11, lineHeight: 1.5, color: '#C8C0AF', fontWeight: 400,
          textTransform: 'none', letterSpacing: 'normal', whiteSpace: 'normal',
        }}>
          {text}
        </div>
      )}
    </span>
  )
}
