import React, { useState, useEffect, useCallback } from 'react'
import TagScopePicker from './TagScopePicker.jsx'

// gattungId/artId identify the CURRENT product's Gattung/Art (if any) — used
// to filter which categories are relevant here (global, or scoped to this
// Gattung / this exact Art) and as the default scope when creating a new
// category (still overridable via the picker, which lists everything).
export default function TagPool({ entityType = 'product', entityId, onChange, gattungId, gattungName, artId, artName }) {
  const [categories, setCategories] = useState([])
  const [allTags, setAllTags] = useState([])
  const [selectedIds, setSelectedIds] = useState([])
  const [newCategoryName, setNewCategoryName] = useState('')
  const [newScopeGattungId, setNewScopeGattungId] = useState(null)
  const [newScopeArtId, setNewScopeArtId] = useState(null)
  const [addingCategory, setAddingCategory] = useState(false)
  const [editingCategory, setEditingCategory] = useState(null) // category object being renamed/rescoped
  const [editName, setEditName] = useState('')
  const [editScopeGattungId, setEditScopeGattungId] = useState(null)
  const [editScopeArtId, setEditScopeArtId] = useState(null)
  const [newTagByCategory, setNewTagByCategory] = useState({})
  const [addingTagFor, setAddingTagFor] = useState(null)

  const load = useCallback(async () => {
    const [cats, tags] = await Promise.all([window.api.getTagCategories(), window.api.getTags()])
    setCategories(cats)
    setAllTags(tags)
  }, [])

  useEffect(() => { load() }, [load])

  const getEntityTags = entityType === 'variant' ? window.api.getVariantTags : window.api.getProductTags
  const setEntityTags = entityType === 'variant' ? window.api.setVariantTags : window.api.setProductTags

  // onChange also fires after this initial load (not just on user toggles) —
  // callers that persist the pool's selection on save (see ProductForm.jsx,
  // VariantDialog.jsx) need the already-existing tags reflected in their own
  // state too, or an untouched Tags section would look empty and wipe them.
  useEffect(() => {
    if (entityId) getEntityTags(entityId).then(tags => {
      const ids = tags.map(t => t.id)
      setSelectedIds(ids)
      onChange && onChange(ids)
    })
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [entityId, entityType])

  // Global categories always show; Gattung-scoped ones show only under that
  // same Gattung; Art-scoped ones only under that exact Art.
  const visibleCategories = categories.filter(cat => {
    if (!cat.gattung_id) return true
    if (cat.gattung_id !== gattungId) return false
    if (cat.art_id && cat.art_id !== artId) return false
    return true
  })

  const scopeLabel = (cat) => {
    if (!cat.gattung_id) return null
    if (cat.art_id) return artName
    return gattungName
  }

  const toggle = (id) => {
    const next = selectedIds.includes(id)
      ? selectedIds.filter(x => x !== id)
      : [...selectedIds, id]
    setSelectedIds(next)
    onChange && onChange(next)
    if (entityId) setEntityTags(entityId, next)
  }

  const openAddCategory = () => {
    setNewScopeGattungId(gattungId || null)
    setNewScopeArtId(artId || null)
    setAddingCategory(true)
  }

  const createCategory = async () => {
    const name = newCategoryName.trim()
    if (!name) return
    const id = await window.api.saveTagCategory({ name, gattung_id: newScopeGattungId, art_id: newScopeArtId })
    setCategories(c => [...c, { id, name, gattung_id: newScopeGattungId, art_id: newScopeArtId }])
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
    setCategories(c => c.map(x => x.id === editingCategory.id ? { ...x, name, gattung_id: editScopeGattungId, art_id: editScopeArtId } : x))
    setEditingCategory(null)
  }

  const deleteCategory = async (cat) => {
    const count = allTags.filter(t => t.category_id === cat.id).length
    const msg = count > 0
      ? `Überkategorie „${cat.name}" löschen? ${count} Tag${count !== 1 ? 's' : ''} werden mitgelöscht.`
      : `Überkategorie „${cat.name}" wirklich löschen?`
    if (!window.confirm(msg)) return
    const removedIds = new Set(allTags.filter(t => t.category_id === cat.id).map(t => t.id))
    try {
      await window.api.deleteTagCategory(cat.id)
    } catch (e) {
      window.alert(e.message)
      return
    }
    setCategories(c => c.filter(x => x.id !== cat.id))
    setAllTags(t => t.filter(x => x.category_id !== cat.id))
    if (removedIds.size > 0) {
      const next = selectedIds.filter(id => !removedIds.has(id))
      if (next.length !== selectedIds.length) {
        setSelectedIds(next)
        onChange && onChange(next)
        if (entityId) setEntityTags(entityId, next)
      }
    }
  }

  const createTag = async (categoryId) => {
    const name = (newTagByCategory[categoryId] || '').trim()
    if (!name) return
    const id = await window.api.saveTag({ name, category_id: categoryId, slug: name.toLowerCase().replace(/\s+/g, '-') })
    setAllTags(t => [...t, { id, name, category_id: categoryId }])
    setNewTagByCategory(m => ({ ...m, [categoryId]: '' }))
    setAddingTagFor(null)
    toggle(id)
  }

  const deleteTag = async (tag) => {
    if (!window.confirm(`Tag „${tag.name}" wirklich löschen?`)) return
    await window.api.deleteTag(tag.id)
    setAllTags(t => t.filter(x => x.id !== tag.id))
    if (selectedIds.includes(tag.id)) toggle(tag.id)
  }

  return (
    <div>
      {visibleCategories.length === 0 && (
        <div style={{ fontSize: 12, color: '#9CA59E', fontStyle: 'italic', marginBottom: 10 }}>
          Noch keine Überkategorien angelegt.
        </div>
      )}

      {visibleCategories.map(cat => {
        const tagsInCat = allTags.filter(t => t.category_id === cat.id)
        const label = scopeLabel(cat)
        const isEditing = editingCategory?.id === cat.id
        return (
          <div key={cat.id} style={{ marginBottom: 14 }}>
            {isEditing ? (
              <div style={{ marginBottom: 6, display: 'flex', flexDirection: 'column', gap: 6 }}>
                <div style={{ display: 'flex', gap: 6 }}>
                  <input className="input" style={{ fontSize: 12, flex: 1 }} value={editName}
                    onChange={e => setEditName(e.target.value)}
                    onKeyDown={e => { if (e.key === 'Enter') { e.preventDefault(); saveEditCategory() } }}
                    autoFocus />
                  <div style={{ width: 160 }}>
                    <TagScopePicker gattungId={editScopeGattungId} artId={editScopeArtId}
                      onChange={(g, a) => { setEditScopeGattungId(g); setEditScopeArtId(a) }} />
                  </div>
                </div>
                <div style={{ display: 'flex', gap: 6, justifyContent: 'flex-end' }}>
                  <button type="button" className="btn-secondary" style={{ fontSize: 11, padding: '4px 10px' }} onClick={() => setEditingCategory(null)}>Abbrechen</button>
                  <button type="button" className="btn-primary" style={{ fontSize: 11, padding: '4px 10px' }} onClick={saveEditCategory}>Speichern</button>
                </div>
              </div>
            ) : (
              <div style={{ display: 'flex', alignItems: 'center', gap: 6, marginBottom: 6 }}>
                <span style={{ fontSize: 10, color: '#9CA59E', textTransform: 'uppercase', letterSpacing: 1, flex: 1 }}>
                  {cat.name}
                  {label && <span style={{ color: '#9B6FD0', textTransform: 'none', letterSpacing: 0 }}> · {label}</span>}
                </span>
                <button type="button" onClick={() => setAddingTagFor(addingTagFor === cat.id ? null : cat.id)} style={{
                  background: 'none', border: '1px dashed rgba(155,111,208,0.3)', borderRadius: 3,
                  color: '#9B6FD0', cursor: 'pointer', fontSize: 10, padding: '2px 7px'
                }}>+ Tag</button>
                <span onClick={() => openEditCategory(cat)} title="Überkategorie bearbeiten" style={{
                  cursor: 'pointer', fontSize: 11, color: '#9CA59E', opacity: 0.6, padding: '0 2px'
                }}>✏️</span>
                {!cat.protected && (
                  <span onClick={() => deleteCategory(cat)} title="Überkategorie löschen" style={{
                    cursor: 'pointer', fontSize: 11, color: '#9CA59E', opacity: 0.6, padding: '0 2px'
                  }}>✕</span>
                )}
              </div>
            )}
            <div style={{ display: 'flex', flexWrap: 'wrap', gap: 6 }}>
              {tagsInCat.length === 0 && (
                <span style={{ fontSize: 12, color: '#9CA59E', fontStyle: 'italic' }}>Keine Tags</span>
              )}
              {tagsInCat.map(tag => {
                const active = selectedIds.includes(tag.id)
                return (
                  <span key={tag.id} style={{
                    display: 'inline-flex', alignItems: 'center', borderRadius: 20,
                    background: active ? 'rgba(155,111,208,0.25)' : 'rgba(155,111,208,0.06)',
                    border: active ? '1px solid rgba(155,111,208,0.6)' : '1px solid rgba(155,111,208,0.15)',
                  }}>
                    <button type="button" onClick={() => toggle(tag.id)} style={{
                      padding: '4px 4px 4px 10px', borderRadius: '20px 0 0 20px', fontSize: 12, cursor: 'pointer',
                      background: 'none', border: 'none',
                      color: active ? '#B8A8D8' : '#9CA59E', transition: 'all 0.15s'
                    }}>{tag.name}</button>
                    <span onClick={() => deleteTag(tag)} title="Tag löschen" style={{
                      cursor: 'pointer', fontSize: 10, color: '#9CA59E', opacity: 0.6, padding: '4px 8px 4px 2px'
                    }}>✕</span>
                  </span>
                )
              })}
            </div>
            {addingTagFor === cat.id && (
              <div style={{ display: 'flex', gap: 6, marginTop: 6 }}>
                <input
                  className="input"
                  style={{ fontSize: 12, flex: 1 }}
                  placeholder="Tag-Name..."
                  value={newTagByCategory[cat.id] || ''}
                  onChange={e => setNewTagByCategory(m => ({ ...m, [cat.id]: e.target.value }))}
                  onKeyDown={e => { if (e.key === 'Enter') { e.preventDefault(); createTag(cat.id) } }}
                  autoFocus
                />
                <button type="button" className="btn-primary" style={{ fontSize: 11, padding: '4px 10px' }} onClick={() => createTag(cat.id)}>+</button>
              </div>
            )}
          </div>
        )
      })}

      {/* Selected pills summary */}
      {selectedIds.length > 0 && (
        <div style={{ marginTop: 8, padding: '8px 10px', background: 'rgba(155,111,208,0.07)', borderRadius: 4 }}>
          <div style={{ fontSize: 10, color: '#9CA59E', marginBottom: 4 }}>Ausgewählt ({selectedIds.length})</div>
          <div style={{ display: 'flex', flexWrap: 'wrap', gap: 4 }}>
            {selectedIds.map(id => {
              const tag = allTags.find(t => t.id === id)
              if (!tag) return null
              return (
                <span key={id} style={{
                  padding: '2px 8px', borderRadius: 20, fontSize: 11,
                  background: 'rgba(155,111,208,0.2)', color: '#B8A8D8',
                  display: 'inline-flex', alignItems: 'center', gap: 4
                }}>
                  {tag.name}
                  <span onClick={() => toggle(id)} style={{ cursor: 'pointer', opacity: 0.6, fontSize: 10 }}>✕</span>
                </span>
              )
            })}
          </div>
        </div>
      )}

      {/* Add new category */}
      <div style={{ marginTop: 10 }}>
        {addingCategory ? (
          <div style={{ display: 'flex', gap: 6, alignItems: 'center' }}>
            <input
              className="input"
              style={{ fontSize: 12, flex: 1 }}
              placeholder="Überkategorie-Name..."
              value={newCategoryName}
              onChange={e => setNewCategoryName(e.target.value)}
              onKeyDown={e => { if (e.key === 'Enter') { e.preventDefault(); createCategory() } }}
              autoFocus
            />
            <div style={{ width: 160 }}>
              <TagScopePicker gattungId={newScopeGattungId} artId={newScopeArtId}
                onChange={(g, a) => { setNewScopeGattungId(g); setNewScopeArtId(a) }} />
            </div>
            <button type="button" className="btn-primary" style={{ fontSize: 11, padding: '6px 10px' }} onClick={createCategory}>+</button>
            <button type="button" className="btn-secondary" style={{ fontSize: 11, padding: '6px 10px' }} onClick={() => setAddingCategory(false)}>✕</button>
          </div>
        ) : (
          <button type="button" onClick={openAddCategory} style={{
            background: 'none', border: '1px dashed rgba(155,111,208,0.3)', borderRadius: 4,
            color: '#9B6FD0', cursor: 'pointer', fontSize: 12, padding: '4px 12px'
          }}>+ Überkategorie erstellen</button>
        )}
      </div>
    </div>
  )
}
