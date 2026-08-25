import farbenCss from '../assets/preview/farben.css?raw'
import pdpCss from '../assets/preview/pdp.css?raw'

const LIGHT_MAP = { schatten: 1, halbschatten: 2, sonne: 3, sonnig: 3, vollsonne: 4 }
const WATER_MAP = { wenig: 1, maessig: 2, 'mäßig': 2, mittel: 2, viel: 3, reichlich: 3 }

function esc(s) {
  return String(s ?? '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;')
}

// Variant name/sku are free text the user can type — JSON.stringify alone
// doesn't escape "<", so a value containing "</script>" could break out of
// the inline <script> block below. <-escaping keeps it inert.
function toScriptJson(v) {
  return JSON.stringify(v).replace(/</g, '\\u003c')
}

// Muss zur PHP-Entsprechung pa_pot_size_text() in functions.php passen.
function formatPotSizeText(potSizeCm, shape) {
  if (!potSizeCm) return ''
  const n = Number(potSizeCm)
  const size = n === Math.trunc(n) ? String(Math.trunc(n)) : String(potSizeCm).replace('.', ',')
  const shapeLabel = shape === 'square' ? 'eckigen' : 'runden'
  return `Pflanze im ${size}cm ${shapeLabel} Topf`
}

// Muss zur PHP-Entsprechung in content-single-product.php passen (Range-Bar
// von tolerates_min bis tolerates_max mit Strich bei der bevorzugten Stufe).
function careBarHtml(scale, minScale, maxScale, maxStep, endLabels, prefValue) {
  const lp = (scale - 1) / maxStep * 100
  const l0 = (Math.min(minScale, maxScale) - 1) / maxStep * 100
  const l1 = (Math.max(minScale, maxScale) - 1) / maxStep * 100
  return `
    <div class="pdp-carebar">
      <div class="pdp-carebar-track">
        <div class="pdp-carebar-range" style="left:${l0.toFixed(1)}%;width:${(l1 - l0).toFixed(1)}%"></div>
        <div class="pdp-carebar-pref" style="left:${lp.toFixed(1)}%"></div>
      </div>
      <div class="pdp-carebar-ends"><span>${esc(endLabels[0])}</span><span>${esc(endLabels[1])}</span></div>
      <div class="pdp-carebar-pref-label">Bevorzugt: ${esc(prefValue)}</div>
    </div>`
}

// Muss zur PHP-Entsprechung $pa_tempbar() in content-single-product.php
// passen — Skala -20°C bis 50°C, Minimum/Maximum immer mit Vorzeichen.
function formatSignedTemp(n) {
  const t = Math.trunc(n)
  return (t >= 0 ? '+' : '') + t + '°C'
}
function tempBarHtml(label, min, max) {
  if (min == null || max == null) return ''
  const scaleMin = -20, scaleMax = 50, span = scaleMax - scaleMin
  const leftPct = Math.max(0, Math.min(100, (min - scaleMin) / span * 100))
  const widthPct = Math.max(0, Math.min(100 - leftPct, (max - min) / span * 100))
  return `
    <div class="pdp-iconscale-cell" style="grid-column:1/-1">
      <div class="label">${esc(label)} · ${Math.trunc(min)}°C — ${Math.trunc(max)}°C</div>
      <div class="pdp-tempbar">
        <div class="pdp-tempbar-track">
          <div class="pdp-tempbar-fill" style="left:${leftPct.toFixed(1)}%;width:${widthPct.toFixed(1)}%"></div>
        </div>
        <div class="pdp-tempbar-labels">
          <span>${scaleMin}°C</span>
          <span>Minimum <b>${formatSignedTemp(min)}</b></span>
          <span>Maximum <b>${formatSignedTemp(max)}</b></span>
          <span>${scaleMax}°C</span>
        </div>
      </div>
    </div>`
}

export function generatePreviewHtml(product, imageDataUrls = [], viewport = 'desktop', variants = []) {
  const {
    name = '', gattung = '', art = '', kultivar = '', common_name = '', sku = '',
    price, regular_price, sale_price, stock,
    pot_size_cm = null, pot_shape = null,
    short_description = '', description = '',
    care_light = '', care_light_tolerates_min = '', care_light_tolerates_max = '',
    care_water = '', care_water_tolerates_min = '', care_water_tolerates_max = '',
    care_winter = '',
    care_temp_min, care_temp_max,
    care_temp_ausgepflanzt_min, care_temp_ausgepflanzt_max,
  } = product

  const genusLine = [gattung, art].filter(Boolean).join(' ')
  const lightScale = LIGHT_MAP[(care_light || '').toLowerCase()] || 0
  const waterScale = WATER_MAP[(care_water || '').toLowerCase()] || 0
  const lightMinScale = LIGHT_MAP[(care_light_tolerates_min || '').toLowerCase()] || lightScale
  const lightMaxScale = LIGHT_MAP[(care_light_tolerates_max || '').toLowerCase()] || lightScale
  const waterMinScale = WATER_MAP[(care_water_tolerates_min || '').toLowerCase()] || waterScale
  const waterMaxScale = WATER_MAP[(care_water_tolerates_max || '').toLowerCase()] || waterScale
  const tempMin = care_temp_min != null && care_temp_min !== '' ? parseFloat(care_temp_min) : null
  const tempMax = care_temp_max != null && care_temp_max !== '' ? parseFloat(care_temp_max) : null
  const tempAusgepflanztMin = care_temp_ausgepflanzt_min != null && care_temp_ausgepflanzt_min !== '' ? parseFloat(care_temp_ausgepflanzt_min) : null
  const tempAusgepflanztMax = care_temp_ausgepflanzt_max != null && care_temp_ausgepflanzt_max !== '' ? parseFloat(care_temp_ausgepflanzt_max) : null
  // Deckt sich mit $has_care in content-single-product.php — Verträgt-Werte
  // allein lösen die Karte dort nicht aus, nur der bevorzugte Wert tut das.
  const hasCare = !!(care_light || care_water || care_winter || tempMin != null || tempAusgepflanztMin != null)

  const fmt = v => (v ? Number(v).toFixed(2).replace('.', ',') + ' €' : '—')
  // Matcht WooCommerce's eigene wc_price()-Markup-Struktur (.woocommerce-
  // Price-amount.amount > <bdi>) für den Hauptpreis — die App hat keinen
  // WC-Renderer zur Hand, baut die gleiche DOM-Form aber von Hand nach.
  const wcPriceHtml = v => v
    ? `<span class="woocommerce-Price-amount amount"><bdi>${Number(v).toFixed(2).replace('.', ',')}&nbsp;<span class="woocommerce-Price-currencySymbol">€</span></bdi></span>`
    : '—'

  // Variable Produkte tragen Preis/SKU/Lager/Bilder nicht mehr auf sich
  // selbst, sondern pro Variante. Die erste Variante ist standardmäßig
  // "ausgewählt" — genau ihre Werte werden serverseitig ins initiale HTML
  // gerendert, damit der erste Bildschirm schon korrekt aussieht; die
  // Bubbles darunter sind klickbar und schalten clientseitig (im iframe) um.
  const isVariable = !!product.is_variable && variants.length > 0
  const variantsForJs = isVariable
    ? variants.map(v => ({
        name: v.name || '',
        sku: v.sku || '',
        price: Number(v.regular_price ?? v.price ?? 0),
        stock: parseInt(v.stock) || 0,
        images: v.imageDataUrls || [],
        pot_size_cm: v.pot_size_cm ?? null,
        pot_shape: v.pot_shape ?? null,
      }))
    : []
  const active = isVariable ? variantsForJs[0] : null

  const stockNum = isVariable ? active.stock : (parseInt(stock) || 0)
  const stockClass = stockNum === 0 ? 'out' : stockNum <= 5 ? 'low' : ''
  const stockText = stockNum === 0 ? 'Ausverkauft' : stockNum <= 5 ? `Nur noch ${stockNum} Stück` : 'Auf Lager'

  let priceHtml
  if (isVariable) {
    priceHtml = wcPriceHtml(active.price)
  } else {
    const regPrice = regular_price || price
    const showSale = sale_price && sale_price != regPrice
    priceHtml = showSale
      ? `<del aria-hidden="true">${wcPriceHtml(regPrice)}</del> <ins>${wcPriceHtml(sale_price)}</ins>`
      : wcPriceHtml(regPrice)
  }

  const skuForDisplay = isVariable ? active.sku : sku
  const metaRowHtml =
    (skuForDisplay ? `<span>Art.-Nr. ${esc(skuForDisplay)}</span><span class="sep"></span>` : '') +
    (stockNum > 0 ? `<span>Versandfertig</span>` : '')

  const potSizeText = formatPotSizeText(isVariable ? active.pot_size_cm : pot_size_cm, isVariable ? active.pot_shape : pot_shape)
  const potSizeHtml = `<div class="pdp-pot-size-hint" id="pdp-pot-size-hint"${potSizeText ? '' : ' style="display:none"'}>${esc(potSizeText)}</div>`

  const variantBubblesHtml = isVariable
    ? `<div class="pdp-opt-label">Variante</div>
       <div class="pdp-pots" id="pdp-variant-bubbles">${variantsForJs.map((v, i) =>
          `<button type="button" class="pdp-pot${i === 0 ? ' active' : ''}" data-variant-index="${i}">` +
          `<span class="pot-size">${esc(v.name || '—')}</span>` +
          `<span class="pot-price">${fmt(v.price)}</span>` +
          `</button>`
        ).join('')}</div>`
    : ''

  const galleryImages = isVariable ? active.images : imageDataUrls
  const galleryCount = galleryImages.length
  const thumbsHtml = galleryCount > 1
    ? galleryImages.map((src, i) =>
        `<button class="pdp-thumb${i === 0 ? ' active' : ''}" style="background-image:url('${src}')" data-thumb="${i}" aria-label="Bild ${i + 1}"></button>`
      ).join('')
    : ''
  const mainBgStyle = galleryCount > 0 ? `background-image:url('${galleryImages[0]}')` : 'background:#2F3B35;'
  const galleryLayout = galleryCount > 1 ? 'vertical' : 'single'

  // "Neu" (is_featured) bleibt außen vor — kein entsprechendes Feld im
  // App-Datenmodell (siehe content-single-product.php für den Vergleich).
  const isOnSale = isVariable
    ? variants.some(v => {
        const reg = Number(v.regular_price ?? v.price ?? 0)
        const sp = v.sale_price != null && v.sale_price !== '' ? Number(v.sale_price) : null
        return !!sp && sp !== reg
      })
    : !!(sale_price && sale_price != (regular_price || price))
  const badges = []
  if (isOnSale) badges.push('Sale')
  if (stockNum === 0) badges.push('Ausverkauft')
  const badgeStackHtml = badges.length
    ? `<div class="pdp-badge-stack">${badges.map(b => `<span class="pa-badge">${esc(b)}</span>`).join('')}</div>`
    : ''

  // Kein echtes Ziel in der lokalen Vorschau (Sandbox-iframe) — Links sind
  // rein optisch (# statt einer echten Shop-URL wie auf der Website).
  const crumbs = [{ label: 'Shop', href: '#' }]
  if (gattung) crumbs.push({ label: gattung, href: '#' })
  if (art) crumbs.push({ label: art, href: '#' })
  if (kultivar) crumbs.push({ label: `‘${kultivar}’`, href: '#' })
  if (crumbs.length === 1) crumbs.push({ label: name || 'Neues Produkt', href: '#' })
  crumbs[crumbs.length - 1].href = null
  const crumbHtml = `<nav class="pdp-crumb" aria-label="Breadcrumb">${crumbs.map((c, i) =>
    (i > 0 ? '<span class="sep">/</span>' : '') +
    (c.href ? `<a href="${c.href}">${esc(c.label)}</a>` : `<span class="cur">${esc(c.label)}</span>`)
  ).join('')}</nav>`

  let titleHtml
  if (gattung || art || kultivar) {
    const taxonomyHtml = genusLine ? `<div class="pdp-taxonomy">${esc(genusLine)}</div>` : ''
    const h1Inner = kultivar ? `‘${esc(kultivar)}’` : art ? esc(art) : esc(gattung)
    titleHtml = `${taxonomyHtml}<h1 class="pdp-title">${h1Inner}</h1>`
  } else {
    titleHtml = `<h1 class="pdp-title">${esc(name || 'Neues Produkt')}</h1>`
  }
  if (common_name) {
    titleHtml += `<div class="pdp-common-name">${esc(common_name)}</div>`
  }

  let careHtml = ''
  if (hasCare) {
    // Ein Balken pro Kategorie (Topf/Ausgepflanzt) — je eigenständig nur
    // sichtbar, wenn für genau diese Kategorie beide Werte gepflegt sind.
    const tempSection = tempBarHtml('Im Topf', tempMin, tempMax) + tempBarHtml('Ausgepflanzt', tempAusgepflanztMin, tempAusgepflanztMax)

    careHtml = `
      <div class="pdp-iconscale">
        <div class="pdp-iconscale-grid">
          ${care_light ? `<div class="pdp-iconscale-cell">
            <div class="label">Licht</div>
            ${lightScale > 0
              ? careBarHtml(lightScale, lightMinScale, lightMaxScale, 3, ['Schatten', 'Vollsonne'], care_light)
              : `<div class="value">${esc(care_light)}</div>`}
          </div>` : ''}
          ${care_water ? `<div class="pdp-iconscale-cell">
            <div class="label">Wasser</div>
            ${waterScale > 0
              ? careBarHtml(waterScale, waterMinScale, waterMaxScale, 2, ['Wenig', 'Viel'], care_water)
              : `<div class="value">${esc(care_water)}</div>`}
          </div>` : ''}
          ${tempSection}
          ${care_winter ? `<div class="pdp-iconscale-cell pdp-iconscale-wide">
            <div class="label">Pflegehinweise</div>
            <p class="value">${esc(care_winter.replace(/_/g, ' ').replace(/^./, c => c.toUpperCase()))}</p>
          </div>` : ''}
        </div>
      </div>`
  }

  const isSplit = !!(description && hasCare)
  const detailColsHtml = (description || hasCare) ? `
    <div class="pdp-detail-cols${isSplit ? ' is-split' : ''}">
      ${description ? `<div class="pdp-detail-main">
        <div class="pdp-steckbrief-heading">
          <span class="pdp-steckbrief-eyebrow">· Beschreibung ·</span>
          <h2>${esc(kultivar || art || gattung || name)}</h2>
        </div>
        <div class="pdp-description-body">${description}</div>
      </div>` : ''}
      ${hasCare ? `<div class="pdp-detail-side">
        <div class="pdp-detail-sticky">
          <div class="pdp-steckbrief-heading">
            <span class="pdp-steckbrief-eyebrow">· Pflege-Steckbrief ·</span>
            <h2>${esc(kultivar || art || gattung || name)} kultivieren</h2>
          </div>
          ${careHtml}
        </div>
      </div>` : ''}
    </div>` : ''

  const dataViewport = viewport === 'mobile' ? ' data-viewport="mobile"' : ''

  return `<!DOCTYPE html>
<html lang="de"${dataViewport}>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,500;0,600;1,400;1,500&family=Montserrat:wght@400;500;700&display=swap" rel="stylesheet">
<style>
${farbenCss}
:root {
  --serif-display: "Playfair Display", Georgia, serif;
  --sans-body: "Montserrat", -apple-system, sans-serif;
  --ease-botanical: cubic-bezier(.22,.61,.36,1);
  --t-fast: 120ms; --t-base: 220ms; --t-slow: 440ms;
  --pdp-pad-y: 80px; --pdp-gap: 64px;
  --accent: var(--plum-hot); --accent-deep: var(--plum); --accent-soft: var(--plum-soft);
  --shadow-cabinet: 0 24px 60px -30px rgba(0,0,0,0.85), 0 2px 0 rgba(155,111,208,0.06) inset;
  --shadow-specimen: 0 40px 80px -40px rgba(0,0,0,0.9);
  --shadow-inner-dark: inset 0 0 120px rgba(0,0,0,0.6);
}
*, *::before, *::after { box-sizing: border-box; }
body { margin: 0; padding: 0; background: var(--bg-deep); color: var(--creme); font-family: var(--sans-body); -webkit-font-smoothing: antialiased; }
h1, h2, h3, h4 { font-family: var(--serif-display); color: var(--creme); margin: 0; }
p, li { color: var(--creme-dim); font-family: var(--sans-body); }
a { color: var(--lavender); }
button { font-family: inherit; }
del { color: var(--creme-muted); text-decoration: line-through; }
ins { text-decoration: none; }

${pdpCss}
</style>
</head>
<body>
<div id="product-${product.id || 0}">

${crumbHtml}

<div class="pdp-hero">
  <div class="pdp-gallery" data-layout="${galleryLayout}" id="pdp-gallery">
    <div class="pdp-thumbs" id="pdp-thumbs">${thumbsHtml}</div>
    <div class="pdp-main-img" id="pdp-main-img" role="img" aria-label="Produktbild">
      <div class="pdp-main-img-bg" id="pdp-main-bg" style="${mainBgStyle}"></div>
      <div id="pdp-no-image" style="display:${galleryCount === 0 ? 'flex' : 'none'};position:absolute;inset:0;align-items:center;justify-content:center;color:var(--creme-muted);font-size:11px;letter-spacing:0.12em;text-transform:uppercase;">Kein Bild vorhanden</div>
      ${badgeStackHtml}
      <div class="pdp-zoom-hint">+ Klick zum Vergrößern</div>
    </div>
  </div>

  <div class="pdp-info">
    ${titleHtml}
    <div class="pdp-meta-row" id="pdp-meta-row">${metaRowHtml}</div>
    <div class="pdp-price-row">
      <span class="pdp-price" id="pdp-price-text">${priceHtml}</span>
      <span class="pdp-price-tax">inkl. MwSt. · zzgl. Versand</span>
    </div>
    <div class="pdp-stock ${stockClass}" id="pdp-stock-block">
      <span class="dot"></span>
      <span id="pdp-stock-text">${stockText}</span>
    </div>
    ${potSizeHtml}
    <div class="pdp-buy-row">
      <div class="pdp-qty">
        <button type="button" class="pdp-qty-dec" aria-label="Weniger">–</button>
        <span class="val" id="pdp-qty-display">1</span>
        <button type="button" class="pdp-qty-inc" aria-label="Mehr">+</button>
      </div>
      <button class="pdp-cta" id="pdp-cta-btn"${stockNum === 0 ? ' disabled style="opacity:0.5;cursor:not-allowed;"' : ''}>
        <span>${stockNum === 0 ? 'Ausverkauft' : 'In den Warenkorb'}</span>
        <span class="arrow">→</span>
      </button>
    </div>
    ${variantBubblesHtml}
    ${short_description ? `<div class="pdp-short-desc">${short_description}</div>` : ''}
  </div>
</div>

${detailColsHtml}

</div>
<script>
(function(){
  var variants = ${toScriptJson(variantsForJs)};
  var imgs = ${toScriptJson(galleryImages)};
  var mainBg = document.getElementById('pdp-main-bg');
  var mainImg = document.getElementById('pdp-main-img');
  var noImageEl = document.getElementById('pdp-no-image');
  var galleryEl = document.getElementById('pdp-gallery');
  var thumbsWrap = document.getElementById('pdp-thumbs');
  var zoomHint = mainImg && mainImg.querySelector('.pdp-zoom-hint');
  var zoomed = false;

  function escHtml(s){ return String(s == null ? '' : s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;'); }
  function fmtPrice(v){
    if (!v) return '—';
    return '<span class="woocommerce-Price-amount amount"><bdi>' + Number(v).toFixed(2).replace('.', ',') +
      '&nbsp;<span class="woocommerce-Price-currencySymbol">€</span></bdi></span>';
  }

  function bindThumbs(){
    thumbsWrap.querySelectorAll('.pdp-thumb').forEach(function(btn){
      btn.addEventListener('click', function(){
        var idx = parseInt(this.dataset.thumb) || 0;
        if (mainBg && imgs[idx]) mainBg.style.backgroundImage = "url('" + imgs[idx] + "')";
        thumbsWrap.querySelectorAll('.pdp-thumb').forEach(function(t){ t.classList.remove('active'); });
        this.classList.add('active');
        zoomed = false;
        if (mainBg) mainBg.style.transform = '';
      });
    });
  }

  if (mainImg) {
    mainImg.addEventListener('click', function(){
      zoomed = !zoomed;
      if (mainBg) mainBg.style.transform = zoomed ? 'scale(1.6)' : '';
      mainImg.style.cursor = zoomed ? 'zoom-out' : 'zoom-in';
      if (zoomHint) zoomHint.textContent = zoomed ? '— Klick zum Verkleinern' : '+ Klick zum Vergrößern';
    });
  }

  var dec = document.querySelector('.pdp-qty-dec');
  var inc = document.querySelector('.pdp-qty-inc');
  var disp = document.getElementById('pdp-qty-display');
  var qty = 1;
  function syncQty(v){ qty = v; if(disp) disp.textContent = v; if(dec) dec.disabled = v<=1; }
  if (dec) dec.addEventListener('click', function(){ syncQty(Math.max(1, qty-1)); });
  if (inc) inc.addEventListener('click', function(){ syncQty(qty+1); });

  // Variante wählen — aktualisiert Preis/Art.-Nr./Lager/CTA/Galerie im
  // gleichen HTML wie der Server-Render, nur clientseitig im Vorschau-iframe.
  var priceEl = document.getElementById('pdp-price-text');
  var metaEl = document.getElementById('pdp-meta-row');
  var stockBlock = document.getElementById('pdp-stock-block');
  var stockTextEl = document.getElementById('pdp-stock-text');
  var ctaBtn = document.getElementById('pdp-cta-btn');
  var potSizeHintEl = document.getElementById('pdp-pot-size-hint');

  function formatPotSizeText(potSizeCm, shape){
    if (!potSizeCm) return '';
    var size = (potSizeCm === Math.trunc(potSizeCm)) ? String(Math.trunc(potSizeCm)) : String(potSizeCm).replace('.', ',');
    var shapeLabel = shape === 'square' ? 'eckigen' : 'runden';
    return 'Pflanze im ' + size + 'cm ' + shapeLabel + ' Topf';
  }

  function applyVariant(idx){
    var v = variants[idx]; if (!v) return;
    if (priceEl) priceEl.innerHTML = fmtPrice(v.price);
    if (metaEl) {
      metaEl.innerHTML = (v.sku ? '<span>Art.-Nr. ' + escHtml(v.sku) + '</span><span class="sep"></span>' : '') +
        (v.stock > 0 ? '<span>Versandfertig</span>' : '');
    }
    var stockNum = v.stock || 0;
    if (stockBlock) stockBlock.className = 'pdp-stock' + (stockNum === 0 ? ' out' : stockNum <= 5 ? ' low' : '');
    if (stockTextEl) stockTextEl.textContent = stockNum === 0 ? 'Ausverkauft' : (stockNum <= 5 ? ('Nur noch ' + stockNum + ' Stück') : 'Auf Lager');
    if (ctaBtn) {
      if (stockNum === 0) { ctaBtn.setAttribute('disabled', ''); ctaBtn.style.opacity = '0.5'; ctaBtn.style.cursor = 'not-allowed'; }
      else { ctaBtn.removeAttribute('disabled'); ctaBtn.style.opacity = ''; ctaBtn.style.cursor = ''; }
      var ctaLabel = ctaBtn.querySelector('span');
      if (ctaLabel) ctaLabel.textContent = stockNum === 0 ? 'Ausverkauft' : 'In den Warenkorb';
    }
    if (potSizeHintEl) {
      var potText = formatPotSizeText(v.pot_size_cm, v.pot_shape);
      potSizeHintEl.textContent = potText;
      potSizeHintEl.style.display = potText ? '' : 'none';
    }
    imgs = v.images || [];
    if (thumbsWrap) {
      thumbsWrap.innerHTML = imgs.length > 1 ? imgs.map(function(src, i){
        return '<button class="pdp-thumb' + (i === 0 ? ' active' : '') + '" style="background-image:url(\\'' + src + '\\')" data-thumb="' + i + '" aria-label="Bild ' + (i + 1) + '"></button>';
      }).join('') : '';
      bindThumbs();
    }
    if (mainBg) mainBg.style.backgroundImage = imgs.length ? "url('" + imgs[0] + "')" : '';
    if (galleryEl) galleryEl.setAttribute('data-layout', imgs.length > 1 ? 'vertical' : 'single');
    if (noImageEl) noImageEl.style.display = imgs.length ? 'none' : 'flex';
    zoomed = false;
    if (mainBg) mainBg.style.transform = '';
    document.querySelectorAll('.pdp-pot').forEach(function(b){ b.classList.remove('active'); });
    var activeBubble = document.querySelector('.pdp-pot[data-variant-index="' + idx + '"]');
    if (activeBubble) activeBubble.classList.add('active');
  }

  document.querySelectorAll('.pdp-pot').forEach(function(btn){
    btn.addEventListener('click', function(){
      applyVariant(parseInt(this.dataset.variantIndex, 10));
    });
  });

  bindThumbs();
})();
</script>
</body>
</html>`
}
