import { ipcMain, dialog, BrowserWindow, app, shell } from 'electron'
import path from 'path'
import fs from 'fs'
import sharp from 'sharp'
import {
  getAllSettings, getSetting, setSetting, getProducts, getProduct, saveProduct, deleteProduct,
  getProductImages, saveImage, deleteImage,
  getTagCategories, saveTagCategory, deleteTagCategory,
  getTags, saveTag, deleteTag, getProductTags, setProductTags,
  getVariantTags, setVariantTags, getProductIdsByBlueprint,
  getCategories, saveCategory, deleteCategory, moveCategory, moveCarouselCategory,
  getProductCategories, setProductCategories, getBoundCategoryProductIds, getCategoryProductIds,
  getProductIdsForLinkedBlueprints,
  getCategoryProducts, getCategoryProductsRecursive, addProductToCategory, removeProductFromCategory,
  setCategoryWpTermId, reconcileCategoriesFromPull,
  getSnapshot, saveSnapshot, getVariants, saveVariants,
  getProductByWpId, updateProductSyncedAt, updateProductImageFolder, initDatabase, getProjectFolder,
  getModifiedProducts, getSpecifications, saveSpecification, deleteSpecification,
  getProductSpecifications, setProductSpecifications,
  getGattungen, getGattung, saveGattung, deleteGattung,
  getArten, getArt, saveArt, deleteArt, getKultivarsForArt,
  getTaxClasses, getTaxClass, saveTaxClass, deleteTaxClass,
  getDeliveryTimes, getDeliveryTime, saveDeliveryTime, deleteDeliveryTime
} from './database.js'
import {
  testConnection, pullAllProducts, getOnlineProduct, getProductVariations, downloadVariationImage,
  createProduct, updateProduct, createVariation, updateVariation, uploadImage, updateMediaAltText, downloadProductImages,
  wcProductToLocal, localProductToWc, syncTags,
  syncBlueprints, syncSpecifications, applySpecification, syncDeliveryTimes, syncCategories, pullCategoriesFromWp, syncSeoSettings,
  getWcTaxClasses, createWcTaxClass, createWcTaxRate, updateWcTaxRate
} from './api.js'
import {
  getCurrentAppVersion, detectMigrationNeeded, migrateProduct,
  getVersionChanges, areVersionsCompatible
} from '../shared/versionRegistry.js'

function sendProgress(msg) {
  for (const win of BrowserWindow.getAllWindows()) {
    win.webContents.send('api:progress', msg)
  }
}

// ponytail: fixed quality instead of a target-size search — AVIF q50 reads as
// visually lossless for product photos at a fraction of the PNG/JPG size;
// couple to a target size later if a specific image family needs it.
// Verified root cause of the reported yellow-cast bug: sharp's AVIF output
// had NO embedded color profile at all (metadata().hasProfile === false) —
// pixel values round-trip correctly through sharp itself, but a decoder that
// doesn't assume sRGB by default (browsers, other viewers) has nothing to
// tell it what color space the bytes are in, and can render a visible tint.
// withMetadata() embeds an explicit sRGB profile (source has none, so sharp
// falls back to sRGB) — confirmed via metadata().hasProfile flipping to true.
// chromaSubsampling '4:4:4' kept alongside as cheap insurance for real
// photos' fine chroma detail (a flat test patch can't exercise that).
async function toAvifBuffer(buffer, resizeTo) {
  let img = sharp(buffer)
  if (resizeTo) img = img.resize(resizeTo, resizeTo)
  return img.avif({ quality: 50, chromaSubsampling: '4:4:4' }).withMetadata().toBuffer()
}

// Real lossless recompression for the PNG (crop-bars/transparency) path —
// browser Canvas PNG export has no compression effort behind it. AVIF now
// carries the "loads fast" job; this is just best-effort for the fallback.
async function compressPngBuffer(buffer) {
  return sharp(buffer).png({ compressionLevel: 9, effort: 10 }).toBuffer()
}

function avifPathFor(destPath) {
  const dir = path.dirname(destPath)
  const base = path.basename(destPath, path.extname(destPath))
  return path.join(dir, 'avif', `${base}.avif`)
}

// gattung_id/art_id/blueprint_links/tax_class_id are local-only bookkeeping —
// WooCommerce never has them (wcProductToLocal doesn't produce them), so
// diffing them here would permanently look like an unresolvable online change
// on every pull/push.
function compareProducts(local, snapshot, online) {
  const fields = Object.keys(local).filter(k => !['id', 'created_at', 'updated_at', 'synced_at', 'gattung_id', 'art_id', 'blueprint_links', 'tax_class_id'].includes(k))
  const conflicts = []
  for (const field of fields) {
    const lv = local[field]
    const sv = snapshot ? snapshot[field] : undefined
    const ov = online[field]
    const localChanged = JSON.stringify(lv) !== JSON.stringify(sv)
    const onlineChanged = JSON.stringify(ov) !== JSON.stringify(sv)
    if (!localChanged && !onlineChanged) continue
    if (localChanged && !onlineChanged) continue
    if (!localChanged && onlineChanged) { conflicts.push({ field, scenario: 'C', localValue: lv, snapshotValue: sv, onlineValue: ov }); continue }
    if (JSON.stringify(lv) === JSON.stringify(ov)) continue
    conflicts.push({ field, scenario: 'D', localValue: lv, snapshotValue: sv, onlineValue: ov })
  }
  return conflicts
}

// Syncs a product's local tags (with their Überkategorie + Gattung/Art scope)
// to WordPress and returns the WP term ids to assign on the product. Caches
// the resulting wp_id on each local tag so re-pushes don't re-create terms.
async function pushProductTags(productId) {
  const productTags = getProductTags(productId)
  // WooCommerce has no product_tag field on variations — merge each
  // variant's own (locally-tracked) tags up into the parent product's set.
  const seen = new Set(productTags.map(t => t.id))
  const localTags = [...productTags]
  for (const v of getVariants(productId)) {
    for (const tag of getVariantTags(v.id)) {
      if (!seen.has(tag.id)) { seen.add(tag.id); localTags.push(tag) }
    }
  }
  if (localTags.length === 0) return []

  const categories = getTagCategories()
  const catById = new Map(categories.map(c => [c.id, c]))
  const payload = localTags.map(tag => {
    const cat = catById.get(tag.category_id)
    const gattung = cat?.gattung_id ? (getGattung(cat.gattung_id)?.name || '') : ''
    const art = cat?.art_id ? (getArt(cat.art_id)?.name || '') : ''
    return { name: tag.name, category: (cat && !cat.protected) ? cat.name : '', gattung, art }
  })

  const termIds = await syncTags(payload)
  const wpIds = []
  for (const tag of localTags) {
    const wpId = termIds[tag.name]
    if (wpId) {
      if (tag.wp_id !== wpId) saveTag({ ...tag, wp_id: wpId })
      wpIds.push(wpId)
    } else if (tag.wp_id) {
      wpIds.push(tag.wp_id)
    }
  }
  return wpIds
}

// Maps WC tags ([{id,name}]) back onto local tags for a pulled product —
// matched by wp_id first (tags this app already pushed), then by name.
// Unknown tags are created locally under the protected "Unkategorisiert"
// category, matching the app's default for scope-less tags.
function pullProductTags(localProductId, wcTags) {
  if (!wcTags || wcTags.length === 0) return
  const localTags = getTags()
  const uncategorized = getTagCategories().find(c => c.protected)
  const tagIds = []
  for (const wt of wcTags) {
    let match = localTags.find(t => t.wp_id === wt.id) || localTags.find(t => t.name === wt.name)
    if (!match) {
      const newId = saveTag({ name: wt.name, wp_id: wt.id, category_id: uncategorized?.id ?? null })
      match = { id: newId, name: wt.name, wp_id: wt.id, category_id: uncategorized?.id ?? null }
      localTags.push(match)
    } else if (match.wp_id !== wt.id) {
      saveTag({ ...match, wp_id: wt.id })
    }
    tagIds.push(match.id)
  }
  setProductTags(localProductId, tagIds)
}

// Pushes the whole category tree to WordPress. Membership for Gattung/Art/
// Kultivar-gebundene categories is resolved locally (getBoundCategoryProductIds)
// so the website only ever has to call wp_set_object_terms(), nothing to
// derive at render time. System categories (Reduziert/Rabattaktionen/Neu)
// are never sent as real categories — the website computes their content
// live from data it already has (sale prices, bulk-sale campaigns, product
// age); only their visible/order settings travel across.
async function pushCategories() {
  const cats = getCategories()
  const gattungCache = new Map()
  const artCache = new Map()

  const payload = cats.filter(c => !c.system_type).map(cat => {
    let gattungName = '', artName = ''
    if (cat.gattung_id) {
      if (!gattungCache.has(cat.gattung_id)) gattungCache.set(cat.gattung_id, getGattung(cat.gattung_id))
      gattungName = gattungCache.get(cat.gattung_id)?.name || ''
    }
    if (cat.art_id) {
      if (!artCache.has(cat.art_id)) artCache.set(cat.art_id, getArt(cat.art_id))
      artName = artCache.get(cat.art_id)?.name || ''
    }

    const localProductIds = (cat.gattung_id || cat.art_id)
      ? getBoundCategoryProductIds(cat)
      : Array.from(new Set([...getCategoryProductIds(cat.id), ...getProductIdsForLinkedBlueprints(cat.id)]))
    const wpProductIds = localProductIds.map(pid => getProduct(pid)?.wp_id).filter(Boolean)

    return {
      localId: cat.id,
      parentLocalId: cat.parent_id,
      name: cat.name || '',
      heading: cat.heading || '',
      description: cat.description || '',
      gattung: gattungName,
      art: artName,
      kultivar: cat.kultivar || '',
      sortOrder: cat.sort_order,
      expandable: !!cat.expandable,
      hidden: !!cat.hidden,
      mainpageCarousel: !!cat.mainpage_carousel,
      carouselSortOrder: cat.carousel_sort_order,
      productIds: wpProductIds,
    }
  })

  const specialSettings = {}
  for (const cat of cats) {
    if (cat.system_type) {
      specialSettings[cat.system_type] = {
        hidden: !!cat.hidden, sortOrder: cat.sort_order,
        mainpageCarousel: !!cat.mainpage_carousel, carouselSortOrder: cat.carousel_sort_order,
      }
    }
  }

  try {
    const wpTermIds = await syncCategories(payload, specialSettings)
    for (const cat of payload) {
      const wpTermId = wpTermIds[cat.localId]
      if (wpTermId) setCategoryWpTermId(cat.localId, wpTermId)
    }
  } catch (e) {
    console.error('Kategorien-Sync fehlgeschlagen:', e.message)
  }
}

// Syncs a product's linked Gattung/Art blueprint, Spezifikation and
// Lieferzeit to WordPress and returns the extra meta_data entries to merge
// into the product payload (WP term ids + the blueprint_links ownership map,
// so the website's own cascade-on-blueprint-edit can address this product).
async function pushProductBlueprints(product, variants = []) {
  const gattungRow = product.gattung_id ? getGattung(product.gattung_id) : null
  const artRow = product.art_id ? getArt(product.art_id) : null
  const specRows = getProductSpecifications(product.id)
  const dtRow = product.delivery_time ? getDeliveryTimes().find(d => d.label === product.delivery_time) : null

  // A variant can use a specification never attached to the base product —
  // WooCommerce variations carry their own pot size, pushed separately as
  // _pa_specification meta on the variation (see the variant loop below) —
  // so it needs to be synced here too, alongside the product-level one(s).
  const specById = new Map(getSpecifications().map(s => [s.id, s]))
  const allSpecRows = new Map(specRows.map(s => [s.id, s]))
  for (const v of variants) {
    if (v.specification_id && !allSpecRows.has(v.specification_id) && specById.has(v.specification_id)) {
      allSpecRows.set(v.specification_id, specById.get(v.specification_id))
    }
  }

  const gattungen = gattungRow ? [{ name: gattungRow.name, fields: gattungRow.fields }] : []
  const arten = artRow ? [{ name: artRow.name, gattung: gattungRow ? gattungRow.name : '', fields: artRow.fields }] : []
  const specifications = [...allSpecRows.values()].map(s => ({
    name: s.name,
    fields: { pot_size_cm: s.pot_size_cm, shape: s.shape, weight: s.weight, weight_unit: s.weight_unit, height_cm: s.height_cm, width_cm: s.width_cm }
  }))
  const deliveryTimes = dtRow
    ? [{ label: dtRow.label, days_min: dtRow.days_min, days_max: dtRow.days_max }]
    : (product.delivery_time ? [{ label: product.delivery_time, days_min: 0, days_max: 0 }] : [])

  // Lieferzeiten-Katalog wird mitgesynct (für die Website-eigene Verwaltung),
  // das Produkt selbst trägt weiterhin nur das Label in _pa_delivery_time.
  const [bp, specIds] = await Promise.all([
    syncBlueprints(gattungen, arten),
    syncSpecifications(specifications),
    syncDeliveryTimes(deliveryTimes),
  ])

  const specTermIdByLocalId = new Map()
  for (const s of allSpecRows.values()) {
    const wpId = specIds[s.name]
    if (wpId) specTermIdByLocalId.set(s.id, wpId)
  }

  return {
    meta: [
      { key: '_pa_gattung_bp', value: gattungRow ? (bp.gattungen[gattungRow.name] || '') : '' },
      { key: '_pa_art_bp', value: artRow ? (bp.arten[(gattungRow ? gattungRow.name : '') + '|' + artRow.name] || '') : '' },
      { key: '_pa_specification', value: specRows[0] ? (specIds[specRows[0].name] || '') : '' },
      { key: '_pa_blueprint_links', value: product.blueprint_links || '{}' },
    ],
    specTermIdByLocalId,
    productSpecTermId: specRows[0] ? (specTermIdByLocalId.get(specRows[0].id) || null) : null,
  }
}

async function doPushProduct(product, resolutions) {
  let productToSave = { ...product }
  if (resolutions && resolutions[product.id]) {
    for (const [field, res] of Object.entries(resolutions[product.id])) {
      if (res.value !== undefined) productToSave[field] = res.value
    }
    saveProduct(productToSave)
  }

  // Substrat-Referenz ist ein lokaler FK (products.substrate_product_id) —
  // das WP-Theme kann damit nichts anfangen, braucht die WP-Post-ID des
  // Substrat-Produkts für get_permalink(). Auflösung deshalb hier, wo die DB
  // erreichbar ist (localProductToWc in api.js ist ein reiner Mapper ohne
  // DB-Zugriff).
  if (productToSave.substrate_product_id) {
    const sub = getProduct(productToSave.substrate_product_id)
    if (sub) {
      productToSave._substrate_name = sub.name
      productToSave._substrate_composition = sub.composition
      productToSave._substrate_sell_own = sub.sell_own_substrate
      productToSave._substrate_wp_id = sub.wp_id
    }
  }

  const wpTagIds = await pushProductTags(product.id)
  const variants = product.is_variable ? getVariants(product.id) : []
  const wcData = localProductToWc(productToSave, wpTagIds, variants)
  const { meta: bpMeta, specTermIdByLocalId, productSpecTermId } = await pushProductBlueprints(productToSave, variants)
  wcData.meta_data.push(...bpMeta)
  let wpId = product.wp_id
  if (wpId) {
    await updateProduct(wpId, wcData)
  } else {
    const created = await createProduct(wcData)
    wpId = created.id
    saveProduct({ ...productToSave, wp_id: wpId })
  }

  // Echtes Gewicht/Maße kommt aus der Spezifikation, nicht aus dem
  // Produkt-Push selbst (siehe Kommentar in localProductToWc) — separater
  // Aufruf, damit die bereits korrekte, getestete Umrechnung in
  // pa_apply_specification_to_product() (functions.php) genutzt wird statt
  // sie hier in JS zu duplizieren.
  if (productSpecTermId) {
    try {
      await applySpecification(wpId, productSpecTermId)
    } catch (e) {
      sendProgress(`Spezifikations-Maße-Fehler: ${e.message}`)
    }
  }

  const allImgs = getProductImages(product.id).sort((a, b) => a.sort_order - b.sort_order)
  const wpImageIds = []
  const altTextBase = product.name || [product.gattung, product.art].filter(Boolean).join(' ')
  // AVIF variants aren't part of the WC gallery (WooCommerce allows exactly one
  // image per gallery slot, no format variants) — they go up as their own WP
  // media attachments (parented to the product), keyed by the main image's WP
  // id so the website can pair "gallery image N" with "its AVIF" unambiguously.
  const avifGallery = {}
  for (const img of allImgs) {
    let row = img
    if (row.wp_id) {
      wpImageIds.push(row.wp_id)
    } else if (row.local_path) {
      try {
        const uploaded = await uploadImage(row.local_path, row.filename || path.basename(row.local_path), wpId)
        saveImage({ ...row, wp_id: uploaded.id })
        row = { ...row, wp_id: uploaded.id }
        wpImageIds.push(uploaded.id)
        if (altTextBase) {
          const suffix = allImgs.length > 1 ? ` – Foto ${row.sort_order + 1}` : ''
          await updateMediaAltText(uploaded.id, `${altTextBase}${suffix}`)
        }
        sendProgress(`Bild hochgeladen: ${row.filename}`)
      } catch (e) {
        sendProgress(`Bild-Fehler "${row.filename}": ${e.message}`)
      }
    }

    if (row.avif_local_path && !row.avif_wp_id) {
      try {
        const uploaded = await uploadImage(row.avif_local_path, path.basename(row.avif_local_path), wpId)
        row = { ...row, avif_wp_id: uploaded.id, avif_remote_url: uploaded.source_url || '' }
        saveImage(row)
      } catch (e) {
        sendProgress(`AVIF-Fehler "${row.filename}": ${e.message}`)
      }
    }
    if (row.wp_id && row.avif_remote_url) avifGallery[row.wp_id] = row.avif_remote_url
  }
  if (wpImageIds.length > 0) {
    await updateProduct(wpId, { images: wpImageIds.map(id => ({ id })) })
  }
  if (Object.keys(avifGallery).length > 0) {
    await updateProduct(wpId, {
      meta_data: [
        { key: '_pa_avif_gallery', value: JSON.stringify(avifGallery) },
      ]
    })
  }

  // Varianten als echte WC-Variationen pushen — jede mit eigenem Bild/SKU/
  // Preis/Lager. ponytail: kein AVIF-Pipeline-Duplikat pro Variante, nur das
  // Rohbild; bei Bedarf später wie bei den Basisprodukt-Bildern nachrüsten.
  if (product.is_variable) {
    const updatedVariants = []
    for (const [variantIndex, v] of variants.entries()) {
      const updatedImages = []
      for (const img of (v.images || [])) {
        let row = img
        // ponytail: remote_url only gets captured for images actually uploaded
        // THIS push (fresh wp_id) — a variant image already pushed before this
        // gallery feature existed stays without one until the user re-touches
        // it. Fine for this shop's scale; backfill via a media-lookup call if
        // that ever becomes a real gap.
        if (!row.wp_id && row.local_path) {
          try {
            const uploaded = await uploadImage(row.local_path, row.filename || path.basename(row.local_path), wpId)
            row = { ...row, wp_id: uploaded.id, remote_url: uploaded.source_url || row.remote_url || '' }
            if (altTextBase) await updateMediaAltText(uploaded.id, `${altTextBase} - ${v.name || 'Variante'}`)
            sendProgress(`Variantenbild hochgeladen: ${row.filename}`)
          } catch (e) {
            sendProgress(`Varianten-Bild-Fehler "${row.filename}": ${e.message}`)
          }
        }
        updatedImages.push(row)
      }
      const titleImage = updatedImages.find(i => i.sort_order === 0 && i.wp_id) || updatedImages.find(i => i.wp_id)
      // WooCommerce variations natively carry exactly one image (set below as
      // variationData.image, kept for e.g. the admin variation thumbnail) —
      // the frontend gallery-swap code doesn't read that field at all though,
      // since WC transparently falls back to the PARENT product's image
      // there whenever a variation has none of its own, making "has own
      // image" and "WC fell back" indistinguishable from that field alone.
      // Instead ALL of this variant's own images (titleImage included) ride
      // along as this one unambiguous postmeta JSON array — empty means
      // "no images of its own", full stop (see the woocommerce_available_
      // variation filter in functions.php and the found_variation handler
      // in content-single-product.php).
      const galleryUrls = updatedImages
        .filter(i => i.remote_url)
        .map(i => i.remote_url)
      const variantSpecWpId = v.specification_id ? specTermIdByLocalId.get(v.specification_id) : null

      const variationData = {
        regular_price: String(v.regular_price || v.price || 0),
        sku: v.sku || '',
        manage_stock: true,
        stock_quantity: v.stock ?? 0,
        menu_order: variantIndex,
        attributes: [{ name: 'Variante', option: v.name || '' }],
        meta_data: [
          { key: '_pa_variant_gallery', value: JSON.stringify(galleryUrls) },
          { key: '_pa_specification', value: variantSpecWpId ?? '' },
          // Own explicit stock number — WooCommerce's variation JSON doesn't
          // reliably expose stock_quantity to the frontend, same reasoning
          // as _pa_variant_gallery above.
          { key: '_pa_variant_stock', value: String(v.stock ?? 0) },
          { key: '_pa_low_stock_threshold', value: v.low_stock_threshold != null ? String(v.low_stock_threshold) : '' },
          { key: '_pa_never_low_stock', value: v.never_low_stock ? '1' : '0' },
          { key: '_pa_show_exact_stock', value: v.show_exact_stock ? '1' : '0' },
        ],
        // Always set explicitly (never just omitted) so removing a variant's
        // last image actually clears a previously-set WC variation image
        // instead of leaving it stale on re-push.
        image: titleImage ? { id: titleImage.wp_id } : { id: '' },
      }

      try {
        let variationWpId = v.wp_id
        if (variationWpId) await updateVariation(wpId, variationWpId, variationData)
        else variationWpId = (await createVariation(wpId, variationData)).id
        updatedVariants.push({ ...v, wp_id: variationWpId, images: updatedImages })
      } catch (e) {
        sendProgress(`Varianten-Fehler "${v.name}": ${e.message}`)
        updatedVariants.push({ ...v, images: updatedImages })
      }
    }
    saveVariants(product.id, updatedVariants)
  }

  updateProductSyncedAt(product.id)
  saveSnapshot(product.id, productToSave)
}

export function registerIpcHandlers() {
  ipcMain.handle('db:getSettings', () => getAllSettings())
  ipcMain.handle('db:setSetting', (_, key, value) => {
    setSetting(key, value)
    if (key === 'project_folder') {
      try { initDatabase(value); setSetting(key, value) } catch (_) {}
    }
    return true
  })

  ipcMain.handle('db:getProducts', (_, search) => getProducts(search))
  ipcMain.handle('db:getProduct', (_, id) => getProduct(id))
  ipcMain.handle('db:saveProduct', (_, product) => saveProduct(product))
  ipcMain.handle('db:deleteProduct', (_, id) => deleteProduct(id))
  ipcMain.handle('db:setProductImageFolder', (_, id, folderName) => updateProductImageFolder(id, folderName))

  ipcMain.handle('db:getProductImages', (_, productId) => getProductImages(productId))
  ipcMain.handle('db:saveImage', (_, image) => saveImage(image))
  ipcMain.handle('db:deleteImage', (_, id) => deleteImage(id))

  ipcMain.handle('db:getTagCategories', () => getTagCategories())
  ipcMain.handle('db:saveTagCategory', (_, cat) => saveTagCategory(cat))
  ipcMain.handle('db:deleteTagCategory', (_, id) => deleteTagCategory(id))

  ipcMain.handle('db:getCategories', () => getCategories())
  ipcMain.handle('db:saveCategory', async (_, cat) => {
    const id = saveCategory(cat)
    await pushCategories()
    return id
  })
  ipcMain.handle('db:deleteCategory', async (_, id) => {
    deleteCategory(id)
    await pushCategories()
    return true
  })
  ipcMain.handle('db:moveCategory', async (_, id, direction) => {
    moveCategory(id, direction)
    await pushCategories()
    return true
  })
  ipcMain.handle('db:moveCarouselCategory', async (_, id, direction) => {
    moveCarouselCategory(id, direction)
    await pushCategories()
    return true
  })
  ipcMain.handle('db:getProductCategories', (_, productId) => getProductCategories(productId))
  ipcMain.handle('db:setProductCategories', async (_, productId, categoryIds) => {
    setProductCategories(productId, categoryIds)
    await pushCategories()
    return true
  })
  ipcMain.handle('db:getCategoryProducts', (_, categoryId) => getCategoryProducts(categoryId))
  ipcMain.handle('db:getCategoryProductsRecursive', (_, categoryId) => getCategoryProductsRecursive(categoryId))
  ipcMain.handle('db:addProductToCategory', async (_, categoryId, productId) => {
    addProductToCategory(categoryId, productId)
    await pushCategories()
    return true
  })
  ipcMain.handle('db:removeProductFromCategory', async (_, categoryId, productId) => {
    removeProductFromCategory(categoryId, productId)
    await pushCategories()
    return true
  })
  ipcMain.handle('api:pushCategories', () => pushCategories())
  ipcMain.handle('db:getTags', () => getTags())
  ipcMain.handle('db:saveTag', (_, tag) => saveTag(tag))
  ipcMain.handle('db:deleteTag', (_, id) => deleteTag(id))
  ipcMain.handle('db:getProductTags', (_, productId) => getProductTags(productId))
  ipcMain.handle('db:setProductTags', (_, productId, tagIds) => setProductTags(productId, tagIds))
  ipcMain.handle('db:getVariantTags', (_, variantId) => getVariantTags(variantId))
  ipcMain.handle('db:setVariantTags', (_, variantId, tagIds) => setVariantTags(variantId, tagIds))
  ipcMain.handle('db:getProductIdsByBlueprint', (_, type, id) => getProductIdsByBlueprint(type, id))

  ipcMain.handle('db:getSnapshot', (_, productId) => getSnapshot(productId))
  ipcMain.handle('db:saveSnapshot', (_, productId, data) => saveSnapshot(productId, data))

  ipcMain.handle('db:getVariants', (_, productId) => getVariants(productId))
  ipcMain.handle('db:saveVariants', (_, productId, variants) => saveVariants(productId, variants))

  ipcMain.handle('db:getSpecifications', () => getSpecifications())
  ipcMain.handle('db:saveSpecification', (_, spec) => saveSpecification(spec))
  ipcMain.handle('db:deleteSpecification', (_, id) => deleteSpecification(id))
  ipcMain.handle('db:getProductSpecifications', (_, productId) => getProductSpecifications(productId))
  ipcMain.handle('db:setProductSpecifications', (_, productId, specIds) => setProductSpecifications(productId, specIds))

  ipcMain.handle('db:getGattungen', () => getGattungen())
  ipcMain.handle('db:getGattung', (_, id) => getGattung(id))
  ipcMain.handle('db:saveGattung', (_, gattung) => saveGattung(gattung))
  ipcMain.handle('db:deleteGattung', (_, id) => deleteGattung(id))
  ipcMain.handle('db:getArten', (_, gattungId) => getArten(gattungId))
  ipcMain.handle('db:getArt', (_, id) => getArt(id))
  ipcMain.handle('db:saveArt', (_, art) => saveArt(art))
  ipcMain.handle('db:deleteArt', (_, id) => deleteArt(id))
  ipcMain.handle('db:getKultivarsForArt', (_, artId) => getKultivarsForArt(artId))

  ipcMain.handle('db:getTaxClasses', () => getTaxClasses())
  ipcMain.handle('db:getTaxClass', (_, id) => getTaxClass(id))
  // Saves locally first (so the class is always usable offline), then — unless
  // in local-only mode or WP isn't configured — creates/updates the matching
  // WooCommerce tax class + rate (country DE) so the percentage actually takes
  // effect there, not just the slug. A WC failure doesn't lose the local save;
  // it's reported back on the returned row as `wcError` for the UI to surface.
  ipcMain.handle('db:saveTaxClass', async (_, taxClass) => {
    const id = saveTaxClass(taxClass)
    const row = getTaxClass(id)
    const localOnly = getSetting('local_only') === '1'
    const wpConfigured = !!getSetting('wp_url')
    if (localOnly || !wpConfigured) return row

    try {
      let wcSlug = row.wc_slug
      if (!wcSlug) {
        const cls = await createWcTaxClass(row.name)
        wcSlug = cls.slug
      }
      const ratePayload = {
        country: 'DE', rate: String(row.rate), name: row.name,
        class: wcSlug, priority: 1, compound: false, shipping: true, order: 0
      }
      let rateId = row.wc_rate_id
      if (rateId) {
        await updateWcTaxRate(rateId, ratePayload)
      } else {
        const created = await createWcTaxRate(ratePayload)
        rateId = created.id
      }
      saveTaxClass({ id, name: row.name, rate: row.rate, wc_slug: wcSlug, wc_rate_id: rateId })
      return getTaxClass(id)
    } catch (e) {
      return { ...row, wcError: e.message }
    }
  })
  ipcMain.handle('db:deleteTaxClass', (_, id) => deleteTaxClass(id))

  ipcMain.handle('db:getDeliveryTimes', () => getDeliveryTimes())
  ipcMain.handle('db:getDeliveryTime', (_, id) => getDeliveryTime(id))
  ipcMain.handle('db:saveDeliveryTime', (_, dt) => getDeliveryTime(saveDeliveryTime(dt)))
  ipcMain.handle('db:deleteDeliveryTime', (_, id) => deleteDeliveryTime(id))

  ipcMain.handle('api:testConnection', async () => testConnection())

  ipcMain.handle('api:pushSeoSettings', async (_, payload) => {
    await syncSeoSettings(payload)
    return true
  })

  ipcMain.handle('api:pull', async () => {
    sendProgress('Verbinde mit WordPress...')
    const wcProducts = await pullAllProducts((msg) => sendProgress(msg))
    const conflicts = []

    for (const wc of wcProducts) {
      const local = getProductByWpId(wc.id)
      const onlineLocal = wcProductToLocal(wc)

      if (!local) {
        const newId = saveProduct(onlineLocal)
        pullProductTags(newId, wc.tags)
        saveSnapshot(newId, onlineLocal)
        updateProductSyncedAt(newId)
        sendProgress(`Neu: ${wc.name}`)
        const pf = getProjectFolder() || getSetting('project_folder') || ''
        if (pf && wc.images && wc.images.length > 0) {
          try {
            const imgs = await downloadProductImages(wc, pf)
            for (let i = 0; i < imgs.length; i++) {
              saveImage({ ...imgs[i], product_id: newId, sort_order: i })
            }
          } catch (e) {
            sendProgress(`Bilder-Fehler "${wc.name}": ${e.message}`)
          }
        }
        if (onlineLocal.is_variable) await pullVariants(newId, wc)
      } else {
        const snap = getSnapshot(local.id)
        const productConflicts = compareProducts(local, snap, onlineLocal)
        if (productConflicts.length === 0) {
          pullProductTags(local.id, wc.tags)
          saveSnapshot(local.id, onlineLocal)
          updateProductSyncedAt(local.id)
          if (onlineLocal.is_variable) await pullVariants(local.id, wc)
        } else {
          conflicts.push({ productId: local.id, productName: local.name, fields: productConflicts })
        }
      }
    }

    sendProgress('Kategorien werden synchronisiert...')
    try {
      const { categories: pulledCategories, special } = await pullCategoriesFromWp()
      reconcileCategoriesFromPull(pulledCategories, special)
    } catch (e) {
      sendProgress(`Kategorien-Pull-Fehler: ${e.message}`)
    }

    sendProgress(`Pull abgeschlossen: ${wcProducts.length} Produkte`)
    return { ok: true, conflicts, count: wcProducts.length }
  })

  // Rebuilds local variants (SKU/Preis/Lager/Bild/Name, je eine WC-Variation)
  // für ein als variable gepulltes Produkt. Produktspezifikation-pro-Variante
  // wird bewusst nicht zurückgemappt (nicht angefragt, niedrige Priorität).
  async function pullVariants(localProductId, wc) {
    try {
      const variations = await getProductVariations(wc.id)
      const pf = getProjectFolder() || getSetting('project_folder') || ''
      const destSlug = `${wc.slug || wc.id}_variante`
      const mapped = []
      for (const variation of variations) {
        const attr = (variation.attributes || []).find(a => /variante/i.test(a.name || ''))
        let images = []
        if (pf) {
          try {
            const img = await downloadVariationImage(variation, pf, destSlug)
            if (img) images = [{ ...img, sort_order: 0 }]
          } catch (e) {
            sendProgress(`Variantenbild-Fehler "${wc.name}": ${e.message}`)
          }
        }
        mapped.push({
          wp_id: variation.id,
          name: attr ? attr.option : '',
          sku: variation.sku || '',
          regular_price: parseFloat(variation.regular_price) || parseFloat(variation.price) || 0,
          price: parseFloat(variation.price) || 0,
          stock: variation.stock_quantity ?? 0,
          images,
        })
      }
      saveVariants(localProductId, mapped)
    } catch (e) {
      sendProgress(`Varianten-Pull-Fehler "${wc.name}": ${e.message}`)
    }
  }

  // Apply pull-conflict resolutions to local DB (does NOT push)
  ipcMain.handle('api:applyPullResolutions', (_, resolutions) => {
    for (const [productId, fieldResolutions] of Object.entries(resolutions || {})) {
      const product = getProduct(parseInt(productId))
      if (!product) continue
      const updates = {}
      for (const [field, res] of Object.entries(fieldResolutions)) {
        if (res.value !== undefined) updates[field] = res.value
      }
      if (Object.keys(updates).length > 0) {
        const toSave = { ...product, ...updates }
        // gattung/art text changed via conflict resolution — drop the (now
        // possibly stale) id so saveProduct re-resolves the blueprint link
        // for the new text instead of keeping it pointed at the old one.
        if ('gattung' in updates || 'art' in updates) {
          delete toSave.gattung_id
          delete toSave.art_id
        }
        saveProduct(toSave)
      }
    }
    return true
  })

  // Analyse locally-modified products against WP — returns preview data for PushReviewDialog
  ipcMain.handle('api:preparePush', async () => {
    const modified = getModifiedProducts()
    if (modified.length === 0) return { ready: [], conflicts: [], newProducts: [] }

    const ready = []
    const conflicts = []
    const newProducts = []

    for (const product of modified) {
      sendProgress(`Prüfe: ${product.name}`)
      if (!product.wp_id) {
        newProducts.push(product)
        continue
      }
      try {
        const wcOnline = await getOnlineProduct(product.wp_id)
        const online = wcProductToLocal(wcOnline)
        const snap = getSnapshot(product.id)
        const conflictFields = compareProducts(product, snap, online)
        const dConflicts = conflictFields.filter(f => f.scenario === 'D')
        if (dConflicts.length > 0) {
          conflicts.push({ productId: product.id, productName: product.name, fields: dConflicts })
        } else {
          ready.push(product)
        }
      } catch (_) {
        newProducts.push(product)
      }
    }

    sendProgress('')
    return { ready, conflicts, newProducts }
  })

  // Push only confirmed products
  ipcMain.handle('api:push', async (_, { confirmedIds, resolutions } = {}) => {
    // Catches product-level membership changes (e.g. a product's Gattung/Art
    // changed) for bound categories without requiring an explicit category edit.
    await pushCategories()

    const modified = getModifiedProducts()
    const toUse = confirmedIds
      ? modified.filter(p => confirmedIds.includes(p.id))
      : modified
    let pushed = 0, errors = 0

    for (const product of toUse) {
      try {
        await doPushProduct(product, resolutions)
        pushed++
        sendProgress(`Gespeichert: ${product.name}`)
      } catch (e) {
        errors++
        sendProgress(`Fehler bei ${product.name}: ${e.message}`)
      }
    }

    return { ok: true, pushed, errors }
  })

  ipcMain.handle('api:getConflicts', () => [])

  // Writes the main JPG/PNG export and, alongside it, an AVIF counterpart in
  // an avif/ subfolder — every image saved through here gets both, so the
  // website can serve the (much smaller) AVIF with a JPG/PNG fallback.
  ipcMain.handle('image:saveBlob', async (_, buffer, slug, filename) => {
    const projectFolder = getProjectFolder() || getSetting('project_folder') || ''
    if (!projectFolder) throw new Error('Projektordner nicht gesetzt')
    const destDir = path.join(projectFolder, 'images', slug)
    fs.mkdirSync(destDir, { recursive: true })
    const destPath = path.join(destDir, filename)
    const isPng = /\.png$/i.test(filename)
    const inputBuffer = Buffer.from(buffer)
    const bytes = isPng ? await compressPngBuffer(inputBuffer) : inputBuffer
    fs.writeFileSync(destPath, bytes)

    const avifDestPath = avifPathFor(destPath)
    fs.mkdirSync(path.dirname(avifDestPath), { recursive: true })
    fs.writeFileSync(avifDestPath, await toAvifBuffer(inputBuffer))

    return { path: destPath, avifPath: avifDestPath }
  })

  ipcMain.handle('fs:readFileBase64', (_, filePath) => {
    try {
      const data = fs.readFileSync(filePath)
      const ext = path.extname(filePath).toLowerCase()
      const mimes = { '.jpg': 'image/jpeg', '.jpeg': 'image/jpeg', '.png': 'image/png', '.gif': 'image/gif', '.webp': 'image/webp', '.avif': 'image/avif' }
      return `data:${mimes[ext] || 'image/jpeg'};base64,${data.toString('base64')}`
    } catch { return null }
  })

  ipcMain.handle('dialog:openFolder', async () => {
    const result = await dialog.showOpenDialog({ properties: ['openDirectory'] })
    return result.canceled ? null : result.filePaths[0]
  })

  ipcMain.handle('dialog:openFile', async () => {
    const result = await dialog.showOpenDialog({
      properties: ['openFile'],
      filters: [{ name: 'Bilder', extensions: ['jpg', 'jpeg', 'png', 'gif', 'webp'] }]
    })
    return result.canceled ? null : result.filePaths[0]
  })

  ipcMain.handle('fs:deleteFile', async (_, filePath) => {
    try {
      if (fs.existsSync(filePath)) {
        fs.unlinkSync(filePath)
      }
      const avifPath = avifPathFor(filePath)
      if (fs.existsSync(avifPath)) fs.unlinkSync(avifPath)
      return true
    } catch (e) {
      console.error('Failed to delete file:', e)
      return false
    }
  })

  ipcMain.handle('fs:moveFile', async (_, srcPath, destPath) => {
    try {
      // Ensure destination directory exists
      const destDir = path.dirname(destPath)
      if (!fs.existsSync(destDir)) {
        fs.mkdirSync(destDir, { recursive: true })
      }
      fs.renameSync(srcPath, destPath)
      return true
    } catch (e) {
      console.error('Failed to move file:', e)
      return false
    }
  })

  // Moves a file into images/<folderSlug>/<filename ?? original name>, using OS-native
  // path joining (renderer-side string replacement on '/images/...' breaks on Windows
  // backslash paths).
  ipcMain.handle('fs:moveToFolder', async (_, srcPath, folderSlug, filename) => {
    const projectFolder = getProjectFolder() || getSetting('project_folder') || ''
    if (!projectFolder) throw new Error('Projektordner nicht gesetzt')
    const destDir = path.join(projectFolder, 'images', folderSlug)
    fs.mkdirSync(destDir, { recursive: true })
    const destPath = path.join(destDir, filename || path.basename(srcPath))
    if (path.resolve(srcPath) !== path.resolve(destPath)) {
      fs.renameSync(srcPath, destPath)
    }
    return destPath
  })

  // Copies (does not move) a file into images/<folderSlug>/<filename ?? original name>.
  ipcMain.handle('fs:copyToFolder', async (_, srcPath, folderSlug, filename) => {
    const projectFolder = getProjectFolder() || getSetting('project_folder') || ''
    if (!projectFolder) throw new Error('Projektordner nicht gesetzt')
    const destDir = path.join(projectFolder, 'images', folderSlug)
    fs.mkdirSync(destDir, { recursive: true })
    const destPath = path.join(destDir, filename || path.basename(srcPath))
    if (path.resolve(srcPath) !== path.resolve(destPath)) {
      fs.copyFileSync(srcPath, destPath)
    }
    return destPath
  })

  // Renames images/<oldSlug>/ to images/<newSlug>/ wholesale — used when Gattung/Art/
  // Kultivar changes rename a product's folder. Carries over EVERY file, including ones
  // the user dropped in manually outside the app, not just the ones tracked in the DB.
  ipcMain.handle('fs:renameFolder', async (_, oldSlug, newSlug) => {
    const projectFolder = getProjectFolder() || getSetting('project_folder') || ''
    if (!projectFolder || !oldSlug || oldSlug === newSlug) return true
    // Slugs are meant to be plain generated names (see folderNameFor() in
    // ProductForm.jsx) — reject anything that could escape images/ via path
    // traversal (e.g. "..") before it ever reaches the filesystem.
    const safeSlug = /^[a-zA-Z0-9_-]+$/
    if (!safeSlug.test(oldSlug) || !safeSlug.test(newSlug)) return true
    const imagesRoot = path.resolve(path.join(projectFolder, 'images'))
    const oldDir = path.resolve(path.join(imagesRoot, oldSlug))
    const newDir = path.resolve(path.join(imagesRoot, newSlug))
    if (!oldDir.startsWith(imagesRoot + path.sep) || !newDir.startsWith(imagesRoot + path.sep)) return true
    if (!fs.existsSync(oldDir)) return true
    try {
      if (fs.existsSync(newDir)) {
        // Target folder already has files (e.g. two products landed on the same name) —
        // merge instead of clobbering. One level of recursion for directory entries
        // (namely avif/) so a same-named subfolder on both sides gets merged too,
        // instead of the old-side one being silently dropped by rmSync below.
        for (const f of fs.readdirSync(oldDir)) {
          const src = path.join(oldDir, f)
          const dest = path.join(newDir, f)
          if (fs.statSync(src).isDirectory()) {
            fs.mkdirSync(dest, { recursive: true })
            for (const sub of fs.readdirSync(src)) {
              const subDest = path.join(dest, sub)
              if (!fs.existsSync(subDest)) fs.renameSync(path.join(src, sub), subDest)
            }
          } else if (!fs.existsSync(dest)) {
            fs.renameSync(src, dest)
          }
        }
        fs.rmSync(oldDir, { recursive: true, force: true })
      } else {
        fs.mkdirSync(path.dirname(newDir), { recursive: true })
        fs.renameSync(oldDir, newDir)
      }
      return true
    } catch (e) {
      console.error('Failed to rename folder:', e)
      return false
    }
  })

  // Deletes a single named file from images/<folderSlug>/ without touching anything
  // else in that folder (e.g. manually-added reference images the app doesn't track).
  // Also cleans up its avif/ counterpart(s) — single fix point so every caller (current
  // and future) gets AVIF hygiene for free instead of having to remember it.
  ipcMain.handle('fs:deleteFileInFolder', async (_, folderSlug, filename) => {
    const projectFolder = getProjectFolder() || getSetting('project_folder') || ''
    if (!projectFolder || !folderSlug || !filename) return true
    const dir = path.join(projectFolder, 'images', folderSlug)
    const filePath = path.join(dir, filename)
    const base = path.basename(filename, path.extname(filename))
    const siblings = [filePath, path.join(dir, 'avif', `${base}.avif`), path.join(dir, 'avif', `${base}_300.avif`)]
    try {
      for (const p of siblings) {
        if (fs.existsSync(p)) fs.unlinkSync(p)
      }
      return true
    } catch (e) {
      console.error('Failed to delete file:', e)
      return false
    }
  })

  ipcMain.handle('fs:clearTempFolder', async () => {
    const projectFolder = getProjectFolder() || getSetting('project_folder') || ''
    if (!projectFolder) return true
    const tempDir = path.join(projectFolder, 'images', 'temp')
    try {
      if (fs.existsSync(tempDir)) {
        fs.rmSync(tempDir, { recursive: true, force: true })
      }
      return true
    } catch (e) {
      console.error('Failed to clear temp folder:', e)
      return false
    }
  })

  // Version Management Handlers
  ipcMain.handle('version:getCurrent', () => getCurrentAppVersion())

  ipcMain.handle('version:checkMigrations', () => {
    const currentVersion = getCurrentAppVersion()
    const allProducts = getProducts('')
    const migrationsNeeded = detectMigrationNeeded(allProducts, currentVersion)
    
    // Gruppiere nach geänderten Systemen
    const groupedByChange = {}
    for (const item of migrationsNeeded) {
      for (const change of item.changes) {
        if (!groupedByChange[change]) {
          groupedByChange[change] = []
        }
        groupedByChange[change].push({
          productId: item.product.id,
          productName: item.product.name,
          fromVersion: item.fromVersion,
          toVersion: item.toVersion,
          canMigrate: item.canMigrate
        })
      }
    }
    
    return {
      currentVersion,
      hasMigrations: migrationsNeeded.length > 0,
      changes: groupedByChange,
      totalProducts: migrationsNeeded.length
    }
  })

  ipcMain.handle('version:migrateProducts', async (_, productIds) => {
    const currentVersion = getCurrentAppVersion()
    const results = []
    
    for (const productId of productIds) {
      const product = getProduct(productId)
      if (!product) continue
      
      const fromVersion = product.app_version || '1.0.0'
      if (fromVersion === currentVersion) {
        results.push({ productId, success: true, alreadyCurrent: true })
        continue
      }
      
      const migrated = migrateProduct(product, fromVersion, currentVersion)
      saveProduct(migrated)
      results.push({ productId, success: true, fromVersion, toVersion: currentVersion })
    }
    
    return results
  })

  // Test Mode Handlers
  let testModeFolder = null

  ipcMain.handle('test:start', async (_, version) => {
    try {
      const baseFolder = getSetting('project_folder') || path.join(app.getPath('home'), 'Plantaphilia')
      
      // Create test folder
      const timestamp = Date.now()
      testModeFolder = path.join(path.dirname(baseFolder), `Plantaphilia_Test_${timestamp}`)
      fs.mkdirSync(testModeFolder, { recursive: true })
      fs.mkdirSync(path.join(testModeFolder, 'images'), { recursive: true })
      
      // Initialize database in test folder
      initDatabase(testModeFolder)
      
      // Create test products with the selected version
      const testProducts = [
        {
          name: 'Monstera deliciosa',
          gattung: 'Monstera',
          art: 'deliciosa',
          kultivar: '',
          price: 29.99,
          regular_price: 29.99,
          product_type: 'plant',
          unit_type: 'piece',
          tax_class: 'reduced-rate',
          differential_taxation: 0,
          shipping_class: '',
          delivery_time: '2-3 Tage',
          stock: 10,
          weight: 500,
          length: 30,
          width: 30,
          height: 40,
          short_description: 'Testprodukt für Version ' + version,
          description: 'Dies ist ein Testprodukt, das simuliert, als ob es mit Version ' + version + ' erstellt wurde.',
          care_light: 'indirect',
          care_water: 'moderate',
          care_winter: 'indoor',
          status: 'draft',
          app_version: version
        },
        {
          name: 'Ficus lyrata',
          gattung: 'Ficus',
          art: 'lyrata',
          kultivar: '',
          price: 39.99,
          regular_price: 39.99,
          product_type: 'plant',
          unit_type: 'piece',
          tax_class: 'reduced-rate',
          differential_taxation: 0,
          shipping_class: '',
          delivery_time: '2-3 Tage',
          stock: 5,
          weight: 800,
          length: 40,
          width: 40,
          height: 60,
          short_description: 'Testprodukt für Version ' + version,
          description: 'Dies ist ein Testprodukt, das simuliert, als ob es mit Version ' + version + ' erstellt wurde.',
          care_light: 'bright',
          care_water: 'moderate',
          care_winter: 'indoor',
          status: 'draft',
          app_version: version
        }
      ]
      
      for (const product of testProducts) {
        saveProduct(product)
      }
      
      // Save test folder path to settings
      setSetting('test_mode_folder', testModeFolder)
      
      // Open test folder in file explorer for user to manually open second instance
      // (Simpler than spawning a second Electron instance)
      shell.openPath(testModeFolder)
      
      return { success: true, testFolder: testFolder, manualOpen: true }
    } catch (e) {
      console.error('Failed to start test mode:', e)
      return { success: false, error: e.message }
    }
  })

  ipcMain.handle('test:end', async () => {
    try {
      // Delete test folder
      if (testModeFolder && fs.existsSync(testModeFolder)) {
        fs.rmSync(testModeFolder, { recursive: true, force: true })
      }
      
      testModeFolder = null
      setSetting('test_mode_folder', '')
      
      return { success: true }
    } catch (e) {
      console.error('Failed to end test mode:', e)
      return { success: false, error: e.message }
    }
  })
}
