import React, { useRef, useState, useImperativeHandle, forwardRef, useEffect } from 'react'

const TOOLS = [
  { cmd: 'bold', label: 'B', style: { fontWeight: 'bold' } },
  { cmd: 'italic', label: 'I', style: { fontStyle: 'italic' } },
  { cmd: 'underline', label: 'U', style: { textDecoration: 'underline' } },
  { cmd: 'strikeThrough', label: 'S', style: { textDecoration: 'line-through' } },
  { sep: true },
  { cmd: 'insertUnorderedList', label: '• Liste' },
  { cmd: 'insertOrderedList', label: '1. Liste' },
  { sep: true },
  { cmd: 'formatBlock', arg: 'h2', label: 'H2' },
  { cmd: 'formatBlock', arg: 'h3', label: 'H3' },
  { sep: true },
  { cmd: 'justifyLeft', label: '⬤L' },
  { cmd: 'justifyCenter', label: '⬤C' },
  { cmd: 'justifyRight', label: '⬤R' },
  { sep: true },
  { cmd: 'createLink', label: '🔗', prompt: true },
]

function escapeHtml(s) {
  return s.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;')
}

const RichTextEditor = forwardRef(function RichTextEditor({ value, onChange, placeholder }, ref) {
  const editorRef = useRef(null)
  const linkInputRef = useRef(null)
  // { range: Range|null } while the "insert link" popover is open, else null.
  // Native window.prompt() is a blocking dialog that steals OS-level focus —
  // in this Electron setup that reliably collapsed the editor's text
  // selection before execCommand ran, so createLink had nothing to linkify.
  // An in-app popover avoids that class of problem entirely and matches how
  // every other dialog in this app already works.
  const [linkPrompt, setLinkPrompt] = useState(null)
  const [linkUrl, setLinkUrl] = useState('https://')

  useImperativeHandle(ref, () => ({
    getValue: () => editorRef.current?.innerHTML || '',
    setValue: (html) => { if (editorRef.current) editorRef.current.innerHTML = html || '' }
  }))

  useEffect(() => {
    if (editorRef.current && value !== undefined && editorRef.current.innerHTML !== value) {
      editorRef.current.innerHTML = value || ''
    }
  }, [])

  useEffect(() => {
    if (linkPrompt) linkInputRef.current?.focus()
  }, [linkPrompt])

  const exec = (tool) => {
    editorRef.current?.focus()
    if (tool.prompt) {
      const selection = window.getSelection()
      const range = selection && selection.rangeCount > 0 && editorRef.current?.contains(selection.anchorNode)
        ? selection.getRangeAt(0).cloneRange()
        : null
      setLinkUrl('https://')
      setLinkPrompt({ range })
    } else if (tool.arg) {
      document.execCommand(tool.cmd, false, tool.arg)
    } else {
      document.execCommand(tool.cmd, false, null)
    }
  }

  const confirmLink = () => {
    const url = linkUrl.trim()
    const range = linkPrompt?.range
    setLinkPrompt(null)
    if (!url) return
    editorRef.current?.focus()
    const selection = window.getSelection()
    if (range) {
      selection.removeAllRanges()
      selection.addRange(range)
    }
    if (range && !range.collapsed) {
      document.execCommand('createLink', false, url)
    } else {
      // Nothing was selected — insert the URL itself as clickable link text
      // instead of doing nothing (createLink requires a non-collapsed selection).
      document.execCommand('insertHTML', false, `<a href="${escapeHtml(url)}">${escapeHtml(url)}</a>`)
    }
    onChange && onChange(editorRef.current?.innerHTML || '')
  }

  return (
    <div style={{ border: '1px solid rgba(155,111,208,0.2)', borderRadius: 4, overflow: 'hidden', position: 'relative' }}>
      {/* Toolbar */}
      <div style={{
        display: 'flex', flexWrap: 'wrap', gap: 2, padding: '6px 8px',
        background: '#1A231F', borderBottom: '1px solid rgba(155,111,208,0.1)'
      }}>
        {TOOLS.map((t, i) => t.sep
          ? <span key={i} style={{ width: 1, background: 'rgba(155,111,208,0.2)', margin: '0 4px' }} />
          : (
            <button key={i} type="button" onMouseDown={e => { e.preventDefault(); exec(t) }} style={{
              background: 'none', border: '1px solid transparent', borderRadius: 3,
              color: '#C8C0AF', cursor: 'pointer', fontSize: 11, padding: '2px 6px',
              fontFamily: 'inherit', ...t.style,
              transition: 'all 0.1s'
            }}
              onMouseEnter={e => { e.currentTarget.style.background = 'rgba(155,111,208,0.15)'; e.currentTarget.style.borderColor = 'rgba(155,111,208,0.3)' }}
              onMouseLeave={e => { e.currentTarget.style.background = 'none'; e.currentTarget.style.borderColor = 'transparent' }}
            >{t.label}</button>
          )
        )}
      </div>

      {/* Editable */}
      <div
        ref={editorRef}
        contentEditable
        suppressContentEditableWarning
        onInput={e => onChange && onChange(e.currentTarget.innerHTML)}
        data-placeholder={placeholder || 'Beschreibung eingeben...'}
        style={{
          minHeight: 160, padding: '10px 12px', outline: 'none',
          color: '#EAE4D6', fontSize: 13, lineHeight: 1.6,
          background: '#2F3B35'
        }}
      />

      {linkPrompt && (
        <div style={{
          position: 'absolute', top: 34, left: 8, zIndex: 20,
          background: '#2F3B35', border: '1px solid rgba(155,111,208,0.4)', borderRadius: 6,
          boxShadow: '0 8px 24px rgba(0,0,0,0.5)', padding: 8,
          display: 'flex', gap: 6, alignItems: 'center'
        }}>
          <input
            ref={linkInputRef}
            className="input"
            style={{ fontSize: 12, width: 220 }}
            value={linkUrl}
            onChange={e => setLinkUrl(e.target.value)}
            onKeyDown={e => {
              if (e.key === 'Enter') { e.preventDefault(); confirmLink() }
              else if (e.key === 'Escape') { e.preventDefault(); setLinkPrompt(null) }
            }}
            placeholder="https://..."
          />
          <button type="button" className="btn-primary" style={{ fontSize: 11, padding: '4px 10px' }} onClick={confirmLink}>OK</button>
          <button type="button" className="btn-secondary" style={{ fontSize: 11, padding: '4px 10px' }} onClick={() => setLinkPrompt(null)}>✕</button>
        </div>
      )}

      <style>{`
        [contenteditable]:empty:before { content: attr(data-placeholder); color: #9CA59E; pointer-events: none; }
        [contenteditable] h2 { font-size: 18px; font-weight: 700; margin: 8px 0 4px; }
        [contenteditable] h3 { font-size: 15px; font-weight: 600; margin: 6px 0 4px; }
        [contenteditable] ul { padding-left: 20px; }
        [contenteditable] ol { padding-left: 20px; }
        [contenteditable] a { color: #9B6FD0; }
      `}</style>
    </div>
  )
})

export default RichTextEditor
