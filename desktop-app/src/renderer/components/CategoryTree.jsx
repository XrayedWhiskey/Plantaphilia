import React from 'react'

function displayName(cat, gattungMap, artMap) {
  if (cat.kultivar) return cat.kultivar
  if (cat.art_id) return artMap.get(cat.art_id)?.name || '(gelöschte Art)'
  if (cat.gattung_id) return gattungMap.get(cat.gattung_id)?.name || '(gelöschte Gattung)'
  return cat.name || '(unbenannt)'
}

// Recursive outliner row. All mutations go straight to the IPC layer (which
// also re-syncs to the website) and then trigger a full reload from the
// parent — the tree has no local optimistic state, simplicity over snappiness.
export default function CategoryTree({ nodes, childrenByParent, gattungMap, artMap, depth, onReload, onOpenCreate, onOpenRename, onOpenProducts, onViewProducts, categoryIdToLinks, flatMode }) {
  const move = async (id, direction) => {
    try {
      if (flatMode) await window.api.moveCarouselCategory(id, direction)
      else await window.api.moveCategory(id, direction)
      onReload()
    } catch (e) {
      window.alert(e.message)
    }
  }

  const toggleFlag = async (cat, key) => {
    await window.api.saveCategory({ ...cat, [key]: !cat[key] })
    onReload()
  }

  const remove = async (cat) => {
    const label = displayName(cat, gattungMap, artMap)
    if (!window.confirm(`"${label}" wirklich löschen? Unterkategorien werden mitgelöscht (gebundene Gattungen/Arten bleiben unangetastet).`)) return
    try {
      await window.api.deleteCategory(cat.id)
      onReload()
    } catch (e) {
      window.alert(e.message)
    }
  }

  const isBound = (cat) => !!(cat.gattung_id || cat.art_id)

  return (
    <div>
      {nodes.map((cat, i) => {
        const children = childrenByParent.get(cat.id) || []
        const bound = isBound(cat)
        const canRename = !bound && !cat.protected
        const canAddProducts = !bound && !cat.protected
        const canDelete = !cat.protected
        const canCreateSub = !cat.protected
        const prevSibling = i > 0 ? nodes[i - 1] : null
        const indentBlocked = !prevSibling || prevSibling.protected
        return (
          <div key={cat.id}>
            <div style={{
              display: 'flex', alignItems: 'center', gap: 6,
              padding: '6px 8px', marginLeft: depth * 22,
              borderRadius: 4, background: depth === 0 ? 'rgba(155,111,208,0.05)' : 'transparent',
            }}>
              <span onClick={() => onViewProducts(cat)} title="Alle Produkte dieser Kategorie anzeigen" style={{
                flex: 1, fontSize: depth === 0 ? 13 : 12.5, color: (cat.hidden && !flatMode) ? '#6B756F' : '#EAE4D6',
                fontStyle: (cat.hidden && !flatMode) ? 'italic' : 'normal', display: 'flex', alignItems: 'center', gap: 6, minWidth: 0,
                overflow: 'hidden', textOverflow: 'ellipsis', whiteSpace: 'nowrap', cursor: 'pointer'
              }}>
                {displayName(cat, gattungMap, artMap)}
                {!!cat.protected && <span style={{ fontSize: 9, color: '#9B6FD0', textTransform: 'uppercase', letterSpacing: 0.5 }}>System</span>}
                {bound && <span style={{ fontSize: 9, color: '#9CA59E', textTransform: 'uppercase', letterSpacing: 0.5 }}>{cat.kultivar ? 'Kultivar' : cat.art_id ? 'Art' : 'Gattung'}</span>}
                {!bound && (categoryIdToLinks?.get(cat.id) || []).map((l, i) => (
                  <span key={i} style={{ fontSize: 9, color: '#9CA59E', textTransform: 'uppercase', letterSpacing: 0.5 }}>
                    {l.type === 'art' ? 'Art' : 'Gattung'}: {l.name}
                  </span>
                ))}
              </span>

              <span title="Nach oben" onClick={() => move(cat.id, 'up')} style={{ cursor: 'pointer', color: '#9CA59E', fontSize: 11, padding: '0 3px' }}>▲</span>
              <span title="Nach unten" onClick={() => move(cat.id, 'down')} style={{ cursor: 'pointer', color: '#9CA59E', fontSize: 11, padding: '0 3px' }}>▼</span>
              {!flatMode && (
                <>
                  <span title="Ausgliedern" onClick={() => move(cat.id, 'outdent')} style={{ cursor: 'pointer', color: '#9CA59E', fontSize: 11, padding: '0 3px', opacity: cat.parent_id ? 1 : 0.3 }}>◀</span>
                  <span title="Eingliedern" onClick={() => { if (!indentBlocked) move(cat.id, 'indent') }} style={{ cursor: indentBlocked ? 'default' : 'pointer', color: '#9CA59E', fontSize: 11, padding: '0 3px', opacity: indentBlocked ? 0.3 : 1 }}>▶</span>
                </>
              )}

              <label title="Ausklappbar" style={{ display: 'flex', alignItems: 'center', gap: 3, fontSize: 10, color: '#9CA59E', cursor: 'pointer' }}>
                <input type="checkbox" checked={!!cat.expandable} onChange={() => toggleFlag(cat, 'expandable')} /> Ausklappbar
              </label>
              <label title={flatMode ? 'Ausblenden (gilt nur für den Shop-Filter, hier bewusst nicht angehakt gezeigt — erscheint trotzdem im Mainpage-Carousel)' : 'Ausblenden'} style={{ display: 'flex', alignItems: 'center', gap: 3, fontSize: 10, color: '#9CA59E', cursor: 'pointer' }}>
                <input type="checkbox" checked={flatMode ? false : !!cat.hidden} onChange={() => toggleFlag(cat, 'hidden')} /> Ausblenden
              </label>
              <label title="Mainpage-Carousel" style={{ display: 'flex', alignItems: 'center', gap: 3, fontSize: 10, color: '#9CA59E', cursor: 'pointer' }}>
                <input type="checkbox" checked={!!cat.mainpage_carousel} onChange={() => toggleFlag(cat, 'mainpage_carousel')} /> Mainpage-Carousel
              </label>

              {!flatMode && canCreateSub && (
                <button type="button" onClick={() => onOpenCreate(cat.id)} title="Unterkategorie hinzufügen"
                  style={{ background: 'none', border: '1px dashed rgba(155,111,208,0.3)', borderRadius: 3, color: '#9B6FD0', cursor: 'pointer', fontSize: 10, padding: '2px 6px' }}>+ Unter</button>
              )}
              {canAddProducts && (
                <button type="button" onClick={() => onOpenProducts(cat)} title="Artikel zuordnen"
                  style={{ background: 'none', border: '1px dashed rgba(155,111,208,0.3)', borderRadius: 3, color: '#9B6FD0', cursor: 'pointer', fontSize: 10, padding: '2px 6px' }}>+ Artikel</button>
              )}
              {canRename && (
                <span onClick={() => onOpenRename(cat)} title="Umbenennen" style={{ cursor: 'pointer', fontSize: 11, color: '#9CA59E', opacity: 0.7 }}>✏️</span>
              )}
              {canDelete && (
                <span onClick={() => remove(cat)} title="Löschen" style={{ cursor: 'pointer', fontSize: 11, color: '#9CA59E', opacity: 0.7 }}>✕</span>
              )}
            </div>

            {!flatMode && children.length > 0 && (
              <CategoryTree
                nodes={children}
                childrenByParent={childrenByParent}
                gattungMap={gattungMap}
                artMap={artMap}
                depth={depth + 1}
                onReload={onReload}
                onOpenCreate={onOpenCreate}
                onOpenRename={onOpenRename}
                onOpenProducts={onOpenProducts}
                onViewProducts={onViewProducts}
                categoryIdToLinks={categoryIdToLinks}
              />
            )}
          </div>
        )
      })}
    </div>
  )
}
