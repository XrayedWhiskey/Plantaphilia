// Ableitung der SEO-Meta-Description. Liegt in shared/ statt nur im
// ProductForm.jsx-Renderer, weil saveVariants() (database.js, Hauptprozess)
// die Beschreibung nach jeder Varianten-Preis-/Bestandsänderung neu
// berechnen muss (siehe ProductList.jsx-Workflow für variable Produkte).

export function composeProductName(gattung, art, kultivar) {
  const g = (gattung || '').trim()
  const a = (art || '').trim()
  const k = (kultivar || '').trim()
  let name = [g, a].filter(Boolean).join(' ')
  if (k) name += (name ? ' ' : '') + `'${k}'`
  return name
}

function stripHtml(html) {
  return String(html || '').replace(/<[^>]*>/g, '').replace(/\s+/g, ' ').trim()
}

function truncate(text, max) {
  return text.length > max ? text.slice(0, max - 3).trim() + '...' : text
}

function formatPrice(n) {
  return Number(n).toFixed(2).replace('.', ',')
}

// data: { product_type, is_variable, gattung, art, kultivar, name,
//   common_name, short_description, price, regular_price,
//   variants: [{ price, regular_price }] }
//
// Immer die gleiche Bauweise (Name[, gängiger Name][, Preis-Segment]: Kurzbeschreibung),
// nur die vorhandenen Teile — kein Sonderfall-Fließtext mehr, damit die Ausgabe
// über alle Produkte hinweg einheitlich aussieht.
export function composeSeoDescription(data) {
  const isPlant = (data.product_type || 'plant') === 'plant'
  const productName = isPlant
    ? composeProductName(data.gattung, data.art, data.kultivar)
    : (data.name || '').trim()
  if (!productName) return ''

  const shortDesc = stripHtml(data.short_description)
  const commonName = isPlant ? (data.common_name || '').trim() : ''

  let priceSegment = null
  if (data.is_variable) {
    const prices = (data.variants || [])
      .map(v => Number(v.price || v.regular_price || 0))
      .filter(p => p > 0)
    if (prices.length > 0) priceSegment = `ab ${formatPrice(Math.min(...prices))}€ kaufen`
  } else {
    const price = Number(data.price || data.regular_price || 0)
    if (price > 0) priceSegment = `für ${formatPrice(price)}€ kaufen`
  }

  const head = [productName, commonName, priceSegment].filter(Boolean).join(', ')
  return truncate(shortDesc ? `${head}: ${shortDesc}` : head, 160)
}
