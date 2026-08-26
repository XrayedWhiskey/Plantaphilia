import https from 'https'
import http from 'http'
import fs from 'fs'
import path from 'path'
import { getSetting } from './database.js'

function getConfig() {
  return {
    baseUrl: (getSetting('wp_url') || '').trim().replace(/\/$/, ''),
    username: (getSetting('wp_username') || '').trim(),
    appPassword: (getSetting('wp_app_password') || '').trim(),
  }
}

function authHeader(username, appPassword) {
  return 'Basic ' + Buffer.from(`${username}:${appPassword}`).toString('base64')
}

async function wcRequest(method, endpoint, body, onProgress, namespace = 'wc/v3') {
  const { baseUrl, username, appPassword } = getConfig()
  const url = new URL(`${baseUrl}/wp-json/${namespace}/${endpoint}`)
  const isLocal = url.hostname.endsWith('.local') || url.hostname === 'localhost' || url.hostname === '127.0.0.1'

  return new Promise((resolve, reject) => {
    const lib = url.protocol === 'https:' ? https : http
    const options = {
      hostname: url.hostname,
      port: url.port || (url.protocol === 'https:' ? 443 : 80),
      path: url.pathname + url.search,
      method,
      rejectUnauthorized: !isLocal,
      headers: {
        'Authorization': authHeader(username, appPassword),
        'Content-Type': 'application/json',
      }
    }

    const req = lib.request(options, (res) => {
      let data = ''
      res.on('data', chunk => { data += chunk })
      res.on('end', () => {
        try {
          const parsed = JSON.parse(data)
          if (res.statusCode >= 400) {
            reject(new Error(parsed.message || `HTTP ${res.statusCode}`))
          } else {
            resolve({ data: parsed, headers: res.headers })
          }
        } catch {
          reject(new Error('Invalid JSON response'))
        }
      })
    })

    req.on('error', reject)
    if (body) req.write(JSON.stringify(body))
    req.end()
  })
}

export async function testConnection() {
  const { baseUrl, username, appPassword } = getConfig()
  if (!baseUrl || !username || !appPassword) throw new Error('Verbindungsdaten fehlen')
  const { data } = await wcRequest('GET', 'products?per_page=1')
  return Array.isArray(data)
}

export async function pullAllProducts(onProgress) {
  const products = []
  let page = 1
  let totalPages = 1

  while (page <= totalPages) {
    const { data, headers } = await wcRequest('GET', `products?per_page=100&page=${page}`)
    products.push(...data)
    totalPages = parseInt(headers['x-wp-totalpages'] || '1', 10)
    if (onProgress) onProgress(`Seite ${page}/${totalPages} geladen (${products.length} Produkte)`)
    page++
  }

  return products
}

export async function getOnlineProduct(wpId) {
  const { data } = await wcRequest('GET', `products/${wpId}`)
  return data
}

export async function createProduct(data) {
  const { data: created } = await wcRequest('POST', 'products', data)
  return created
}

export async function updateProduct(wpId, data) {
  const { data: updated } = await wcRequest('PUT', `products/${wpId}`, data)
  return updated
}

export async function createVariation(wpId, data) {
  const { data: created } = await wcRequest('POST', `products/${wpId}/variations`, data)
  return created
}

export async function updateVariation(wpId, variationId, data) {
  const { data: updated } = await wcRequest('PUT', `products/${wpId}/variations/${variationId}`, data)
  return updated
}

// Upserts product_tag terms + their Überkategorie/Gattung/Art scope on the
// WP side. tags: [{ name, category, gattung, art }] — category/gattung/art
// '' means unscoped ("Unkategorisiert" → flat filter instead of a grouped one).
// Returns { [name]: wpTermId }.
export async function syncTags(tags) {
  if (!tags.length) return {}
  const { data } = await wcRequest('POST', 'sync-tags', { tags }, null, 'pa/v1')
  return data.term_ids || {}
}

// Upserts Gattung/Art blueprint terms on the WP side (same JSON `fields` blob
// as stored locally — no reshaping needed). Returns
// { gattungen: {name: termId}, arten: {"gattung|art": termId} }.
export async function syncBlueprints(gattungen, arten) {
  if (!gattungen.length && !arten.length) return { gattungen: {}, arten: {} }
  const { data } = await wcRequest('POST', 'sync-blueprints', { gattungen, arten }, null, 'pa/v1')
  return { gattungen: data.gattungen || {}, arten: data.arten || {} }
}

export async function syncSpecifications(specifications) {
  if (!specifications.length) return {}
  const { data } = await wcRequest('POST', 'sync-specifications', { specifications }, null, 'pa/v1')
  return data.term_ids || {}
}

// Wendet Gewicht/Maße einer Spezifikation auf die nativen WooCommerce-Felder
// eines Produkts an (pa_apply_specification_to_product() in functions.php) —
// separat vom normalen Produkt-Push, der Produkte direkt über die native
// WC-REST-API sendet und dafür keinen eigenen pa/v1-Endpunkt hat.
export async function applySpecification(wpProductId, specTermId) {
  await wcRequest('POST', 'apply-specification', { productId: wpProductId, specTermId: specTermId || 0 }, null, 'pa/v1')
}

export async function syncDeliveryTimes(deliveryTimes) {
  if (!deliveryTimes.length) return {}
  const { data } = await wcRequest('POST', 'sync-delivery-times', { delivery_times: deliveryTimes }, null, 'pa/v1')
  return data.term_ids || {}
}

// Pushes the whole (non-system) category tree + resolved product membership
// in one go, plus the visible/order settings for the 5 system categories
// (Reduziert/Rabattaktionen/Neu/Beliebt/Alle Produkte, which aren't real
// pa_category terms). Returns localId -> wpTermId so the caller can persist
// wp_term_id locally — needed for the pull side to match terms back up.
export async function syncCategories(categories, specialSettings) {
  const { data } = await wcRequest('POST', 'sync-categories', { categories, special: specialSettings || {} }, null, 'pa/v1')
  return data.term_ids || {}
}

// Pull-side counterpart — full category tree + system-category settings, as
// currently stored on the website. Named distinctly from database.js's local
// getCategories() to keep the two unmistakably separate.
export async function pullCategoriesFromWp() {
  const { data } = await wcRequest('GET', 'get-categories', null, null, 'pa/v1')
  return { categories: data.categories || [], special: data.special || {} }
}

// Start-/Shopseiten-SEO — es gibt dafür keine Produkt-/Kategorie-Zeile, nur
// die 4 Textfelder aus den App-Einstellungen.
export async function syncSeoSettings(payload) {
  await wcRequest('POST', 'sync-seo-settings', payload, null, 'pa/v1')
}

// WooCommerce has no per-product tax percentage — tax is calculated from Tax
// Rates configured per (class, country) in the store's own tax settings.
// "Properly syncing" a Steuerklasse's percentage therefore means managing an
// actual class + rate there, not just the product's tax_class slug.
export async function getWcTaxClasses() {
  const { data } = await wcRequest('GET', 'taxes/classes')
  return data
}

export async function createWcTaxClass(name) {
  const { data } = await wcRequest('POST', 'taxes/classes', { name })
  return data
}

export async function createWcTaxRate(payload) {
  const { data } = await wcRequest('POST', 'taxes', payload)
  return data
}

export async function updateWcTaxRate(id, payload) {
  const { data } = await wcRequest('PUT', `taxes/${id}`, payload)
  return data
}

export async function uploadImage(localPath, filename, postId) {
  const { baseUrl, username, appPassword } = getConfig()
  const fileContent = fs.readFileSync(localPath)
  const ext = path.extname(filename).toLowerCase().replace('.', '')
  const mime = ext === 'png' ? 'image/png' : ext === 'gif' ? 'image/gif' : ext === 'avif' ? 'image/avif' : 'image/jpeg'

  const url = new URL(`${baseUrl}/wp-json/wp/v2/media`)
  if (postId) url.searchParams.set('post', String(postId))
  const isLocal = url.hostname.endsWith('.local') || url.hostname === 'localhost' || url.hostname === '127.0.0.1'

  return new Promise((resolve, reject) => {
    const lib = url.protocol === 'https:' ? https : http
    const options = {
      hostname: url.hostname,
      port: url.port || (url.protocol === 'https:' ? 443 : 80),
      path: url.pathname + url.search,
      method: 'POST',
      rejectUnauthorized: !isLocal,
      headers: {
        'Authorization': authHeader(username, appPassword),
        'Content-Disposition': `attachment; filename="${filename}"`,
        'Content-Type': mime,
        'Content-Length': fileContent.length,
      }
    }

    const req = lib.request(options, (res) => {
      let data = ''
      res.on('data', chunk => { data += chunk })
      res.on('end', () => {
        try {
          const parsed = JSON.parse(data)
          if (res.statusCode >= 400) {
            reject(new Error(parsed.message || `HTTP ${res.statusCode}`))
          } else {
            resolve(parsed)
          }
        } catch {
          reject(new Error('Invalid JSON response'))
        }
      })
    })

    req.on('error', reject)
    req.write(fileContent)
    req.end()
  })
}

// Alt text can't ride along on the raw-binary upload above (body is the file
// itself, not JSON) — a small follow-up PATCH on the core WP media endpoint.
// Best-effort: a missing alt text shouldn't fail the whole product push.
export async function updateMediaAltText(mediaId, altText) {
  if (!altText) return
  try {
    await wcRequest('PATCH', `media/${mediaId}`, { alt_text: altText }, null, 'wp/v2')
  } catch (e) {
    console.error('Alt-Text-Update fehlgeschlagen:', e.message)
  }
}

export async function downloadProductImages(wcProduct, projectFolder) {
  const savedImages = []
  const images = wcProduct.images || []

  for (const img of images) {
    try {
      const url = new URL(img.src)
      const filename = path.basename(url.pathname)
      const slug = wcProduct.slug || String(wcProduct.id)
      const destDir = path.join(projectFolder, 'images', slug)
      fs.mkdirSync(destDir, { recursive: true })
      const destPath = path.join(destDir, filename)

      const isLocalImg = url.hostname.endsWith('.local') || url.hostname === 'localhost' || url.hostname === '127.0.0.1'
      await new Promise((resolve, reject) => {
        const lib = url.protocol === 'https:' ? https : http
        const file = fs.createWriteStream(destPath)
        lib.get({
          hostname: url.hostname,
          port: url.port || (url.protocol === 'https:' ? 443 : 80),
          path: url.pathname + url.search,
          rejectUnauthorized: !isLocalImg
        }, (res) => {
          res.pipe(file)
          file.on('finish', () => { file.close(); resolve() })
        }).on('error', reject)
      })

      savedImages.push({ wp_id: img.id, local_path: destPath, remote_url: img.src, filename })
    } catch (e) {
      console.error('Image download failed:', e.message)
    }
  }

  return savedImages
}

export async function getProductVariations(wpId) {
  const { data } = await wcRequest('GET', `products/${wpId}/variations?per_page=100`)
  return data || []
}

// Mirrors downloadProductImages' download logic for exactly one image
// (WC variations carry a single `image`, never a gallery), but into a
// variant-scoped destination folder instead of the product's own — keeps
// variant images out of the base product's image folder entirely.
export async function downloadVariationImage(variation, projectFolder, destSlug) {
  const img = variation.image
  if (!img || !img.src) return null
  try {
    const url = new URL(img.src)
    const filename = path.basename(url.pathname)
    const destDir = path.join(projectFolder, 'images', destSlug)
    fs.mkdirSync(destDir, { recursive: true })
    const destPath = path.join(destDir, filename)

    const isLocalImg = url.hostname.endsWith('.local') || url.hostname === 'localhost' || url.hostname === '127.0.0.1'
    await new Promise((resolve, reject) => {
      const lib = url.protocol === 'https:' ? https : http
      const file = fs.createWriteStream(destPath)
      lib.get({
        hostname: url.hostname,
        port: url.port || (url.protocol === 'https:' ? 443 : 80),
        path: url.pathname + url.search,
        rejectUnauthorized: !isLocalImg
      }, (res) => {
        res.pipe(file)
        file.on('finish', () => { file.close(); resolve() })
      }).on('error', reject)
    })

    return { wp_id: img.id, local_path: destPath, remote_url: img.src, filename }
  } catch (e) {
    console.error('Variation image download failed:', e.message)
    return null
  }
}

export function wcProductToLocal(wc) {
  return {
    wp_id: wc.id,
    slug: wc.slug || '',
    name: wc.name || '',
    sku: wc.sku || '',
    price: parseFloat(wc.price) || 0,
    regular_price: parseFloat(wc.regular_price) || 0,
    sale_price: wc.sale_price ? parseFloat(wc.sale_price) : null,
    is_variable: wc.type === 'variable' ? 1 : 0,
    // Fall back to the old hardcoded defaults for products pushed before
    // these meta keys existed (never populated on the WC side yet).
    product_type: (wc.meta_data?.find(m => m.key === '_product_type_custom') || {}).value || 'plant',
    unit_type: (wc.meta_data?.find(m => m.key === '_unit_type') || {}).value || 'piece',
    liter_content: parseFloat((wc.meta_data?.find(m => m.key === '_product_liters') || {}).value) || null,
    weight_content: parseFloat((wc.meta_data?.find(m => m.key === '_product_weight_kg') || {}).value) || null,
    fertilizer_type: (wc.meta_data?.find(m => m.key === '_pa_fertilizer_type') || {}).value || '',
    fertilizer_type_choice: (wc.meta_data?.find(m => m.key === '_pa_fertilizer_type_choice') || {}).value || '',
    fertilizer_amount: (wc.meta_data?.find(m => m.key === '_pa_fertilizer_amount') || {}).value || '',
    sell_own_substrate: (wc.meta_data?.find(m => m.key === '_pa_substrate_sell_own') || {}).value === '1' ? 1 : 0,
    composition: (wc.meta_data?.find(m => m.key === '_pa_substrate_composition') || {}).value || '[]',
    substrate_display_text: (wc.meta_data?.find(m => m.key === '_pa_substrate_display_text') || {}).value || '',
    substrate_note: (wc.meta_data?.find(m => m.key === '_pa_substrate_note') || {}).value || '',
    differential_taxation: (wc.meta_data?.find(m => m.key === '_differential_taxation') || {}).value === '1' ? 1 : 0,
    low_stock_threshold: parseInt((wc.meta_data?.find(m => m.key === '_custom_low_stock_threshold') || {}).value, 10) || null,
    never_low_stock: (wc.meta_data?.find(m => m.key === '_never_low_stock') || {}).value === '1' ? 1 : 0,
    show_exact_stock: (wc.meta_data?.find(m => m.key === '_pa_show_exact_stock') || {}).value === '0' ? 0 : 1,
    tax_class: wc.tax_class || 'reduced-rate',
    shipping_class: wc.shipping_class || '',
    stock: wc.stock_quantity ?? 0,
    short_description: wc.short_description || '',
    description: wc.description || '',
    status: wc.status || 'draft',
    weight: parseFloat(wc.weight) || null,
    length: parseFloat(wc.dimensions?.length) || null,
    width: parseFloat(wc.dimensions?.width) || null,
    height: parseFloat(wc.dimensions?.height) || null,
    // Custom meta
    gattung: (wc.meta_data?.find(m => m.key === '_pa_gattung') || {}).value || '',
    art: (wc.meta_data?.find(m => m.key === '_pa_art') || {}).value || '',
    kultivar: (wc.meta_data?.find(m => m.key === '_pa_kultivar') || {}).value || '',
    common_name: (wc.meta_data?.find(m => m.key === '_pa_common_name') || {}).value || '',
    care_light: (wc.meta_data?.find(m => m.key === '_pa_care_light') || {}).value || '',
    care_light_tolerates_min: (wc.meta_data?.find(m => m.key === '_pa_care_light_tolerates_min') || {}).value || '',
    care_light_tolerates_max: (wc.meta_data?.find(m => m.key === '_pa_care_light_tolerates_max') || {}).value || '',
    care_water: (wc.meta_data?.find(m => m.key === '_pa_care_water') || {}).value || '',
    care_water_tolerates_min: (wc.meta_data?.find(m => m.key === '_pa_care_water_tolerates_min') || {}).value || '',
    care_water_tolerates_max: (wc.meta_data?.find(m => m.key === '_pa_care_water_tolerates_max') || {}).value || '',
    care_winter: (wc.meta_data?.find(m => m.key === '_pa_care_winter') || {}).value || '',
    care_temp_min: parseFloat((wc.meta_data?.find(m => m.key === '_pa_care_temp_min') || {}).value) || null,
    care_temp_max: parseFloat((wc.meta_data?.find(m => m.key === '_pa_care_temp_max') || {}).value) || null,
    care_temp_ausgepflanzt_min: parseFloat((wc.meta_data?.find(m => m.key === '_pa_care_temp_ausgepflanzt_min') || {}).value) || null,
    care_temp_ausgepflanzt_max: parseFloat((wc.meta_data?.find(m => m.key === '_pa_care_temp_ausgepflanzt_max') || {}).value) || null,
    delivery_time: (wc.meta_data?.find(m => m.key === '_pa_delivery_time') || {}).value || '',
  }
}

export function localProductToWc(local, wpTagIds = [], variants = []) {
  const meta_data = [
    { key: '_pa_gattung', value: local.gattung || '' },
    { key: '_pa_art', value: local.art || '' },
    { key: '_pa_kultivar', value: local.kultivar || '' },
    { key: '_pa_common_name', value: local.common_name || '' },
    { key: '_pa_care_light', value: local.care_light || '' },
    { key: '_pa_care_light_tolerates_min', value: local.care_light_tolerates_min || '' },
    { key: '_pa_care_light_tolerates_max', value: local.care_light_tolerates_max || '' },
    { key: '_pa_care_water', value: local.care_water || '' },
    { key: '_pa_care_water_tolerates_min', value: local.care_water_tolerates_min || '' },
    { key: '_pa_care_water_tolerates_max', value: local.care_water_tolerates_max || '' },
    { key: '_pa_care_winter', value: local.care_winter || '' },
    { key: '_pa_care_temp_min', value: String(local.care_temp_min ?? '') },
    { key: '_pa_care_temp_max', value: String(local.care_temp_max ?? '') },
    { key: '_pa_care_temp_ausgepflanzt_min', value: String(local.care_temp_ausgepflanzt_min ?? '') },
    { key: '_pa_care_temp_ausgepflanzt_max', value: String(local.care_temp_ausgepflanzt_max ?? '') },
    { key: '_pa_delivery_time', value: local.delivery_time || '' },
    // Substrat-Empfehlung + Dünger-Wahl innerhalb eines Pflanzenprodukts —
    // strukturierte Werte wie bei den übrigen _pa_care_*-Feldern, das
    // Rendering (inkl. "Oder hier kaufen"-Link) übernimmt das WP-Theme
    // (Impreza-child/woocommerce/content-single-product.php). Der Dünger-Link
    // wird dort per meta_query nach Typ aufgelöst, nicht hier fest verlinkt —
    // so verlinkt er automatisch, sobald ein Produkt dieses Typs existiert.
    { key: '_pa_substrate_name', value: local._substrate_name || '' },
    { key: '_pa_substrate_composition', value: local._substrate_composition || '[]' },
    { key: '_pa_substrate_display_text', value: local._substrate_display_text || '' },
    { key: '_pa_substrate_sell_own', value: local._substrate_sell_own ? '1' : '0' },
    { key: '_pa_substrate_wp_id', value: local._substrate_wp_id ? String(local._substrate_wp_id) : '' },
    { key: '_pa_substrate_note', value: local.substrate_note || '' },
    { key: '_pa_fertilizer_type_choice', value: local.fertilizer_type_choice || '' },
    { key: '_pa_fertilizer_amount', value: local.fertilizer_amount || '' },
    // Dünger-Produkt selbst (product_type='fertilizer'): eigener Typ, damit
    // andere Pflanzenprodukte ihn per meta_query finden.
    { key: '_pa_fertilizer_type', value: local.product_type === 'fertilizer' ? (local.fertilizer_type || '') : '' },
    // These meta keys are already read/written by the WordPress-side
    // blueprint cascade (pa_apply_blueprint_field_to_product in
    // functions.php) — reusing the exact same keys here so a plain
    // product-level push and a blueprint cascade agree on where each
    // field lives.
    { key: '_product_type_custom', value: local.product_type || '' },
    { key: '_unit_type', value: local.unit_type || '' },
    { key: '_product_liters', value: local.liter_content != null ? String(local.liter_content) : '' },
    { key: '_product_weight_kg', value: local.weight_content != null ? String(local.weight_content) : '' },
    { key: '_differential_taxation', value: local.differential_taxation ? '1' : '0' },
    { key: '_custom_low_stock_threshold', value: local.low_stock_threshold != null ? String(local.low_stock_threshold) : '' },
    { key: '_never_low_stock', value: local.never_low_stock ? '1' : '0' },
    { key: '_pa_show_exact_stock', value: local.show_exact_stock ? '1' : '0' },
    // Yoast SEO's own meta keys — once populated, Yoast handles sitemap
    // inclusion, OpenGraph/Twitter fallback, canonical tag and schema.org
    // Product data automatically, no theme code needed.
    { key: '_yoast_wpseo_title', value: local.seo_title || '' },
    { key: '_yoast_wpseo_metadesc', value: local.seo_description || '' },
    { key: '_yoast_wpseo_focuskw', value: local.seo_focus_keyword || '' },
  ]

  const base = {
    name: local.name,
    slug: local.slug,
    status: local.status,
    tax_class: local.tax_class || '',
    shipping_class: local.shipping_class || '',
    description: local.description || '',
    short_description: local.short_description || '',
    // Kein weight/dimensions hier — die alten products.weight/length/width/
    // height-Spalten haben seit App 1.1.2 kein UI mehr und sind immer leer.
    // Das echte Gewicht/Maße kommt aus der Spezifikation, per separatem
    // apply-specification-Aufruf in ipc.js (pa_apply_specification_to_product
    // in functions.php) — nicht hier, damit ein leerer Wert nie das dort
    // gerade richtig gesetzte Gewicht/Maße überschreibt.
    tags: wpTagIds.map(id => ({ id })),
    meta_data,
  }

  // Variable Produkte: SKU/Preis/Lager/Bilder leben auf den Variationen, nicht
  // auf dem Basisprodukt — WC braucht dafür ein "Variante"-Attribut mit
  // variation:true, dessen Optionen die Variationsnamen sind.
  if (local.is_variable) {
    return {
      ...base,
      type: 'variable',
      attributes: [{
        name: 'Variante',
        options: variants.map(v => v.name || '').filter(Boolean),
        visible: true,
        variation: true,
      }],
    }
  }

  return {
    ...base,
    sku: local.sku,
    regular_price: String(local.regular_price || 0),
    sale_price: local.sale_price ? String(local.sale_price) : '',
    stock_quantity: local.stock ?? 0,
    manage_stock: true,
  }
}
