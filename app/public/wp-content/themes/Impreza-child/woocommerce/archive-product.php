<?php
/**
 * Plantaphilia – Shop Archive (vollständig client-seitig, kein Reload)
 */
defined('ABSPATH') or die();
get_header();

// ── Alle Produkte laden ───────────────────────────────────────────────────────
$_raw = wc_get_products(['limit' => -1, 'status' => 'publish', 'orderby' => 'date', 'order' => 'DESC']);

// ── Tag-Taxonomie vorladen (1 Abfrage statt N) ────────────────────────────────
$_all_tag_terms = get_terms(['taxonomy' => 'product_tag', 'hide_empty' => false]);
$_tag_meta = []; // term_id => { type, prefix, name }
$pa_tag_tree  = []; // { 'Blütenfarbe' => ['Rot','Weiß'] }    – gruppierte Tags
$pa_fixed_tags = []; // ['Rarität','Neu',…]                    – reguläre Tags

foreach ($_all_tag_terms as $_tt) {
    $_type   = get_term_meta($_tt->term_id, '_pa_tag_type', true);
    $_prefix = get_term_meta($_tt->term_id, '_pa_variable_prefix', true);
    $_tag_meta[$_tt->term_id] = ['type' => $_type, 'prefix' => $_prefix, 'name' => $_tt->name];
    if ($_type === 'variable_type') {
        if (!isset($pa_tag_tree[$_tt->name])) $pa_tag_tree[$_tt->name] = [];
    } elseif ($_type === 'fixed' || $_type === '') {
        $pa_fixed_tags[] = $_tt->name;
    }
}
sort($pa_fixed_tags);

// ── Produkt-Daten aufbauen ────────────────────────────────────────────────────
$pa_products  = [];
$pa_tax_tree  = []; // { 'Monstera' => ['adansonii','deliciosa'] }

$_wh_labels = [
    'nicht-wh'   => 'Nicht winterhart',
    'bedingt-wh' => 'Bedingt winterhart (bis ca. −5 °C)',
    'winterhart'  => 'Winterhart (bis ca. −10 °C)',
    'sehr-wh'    => 'Sehr winterhart (bis ca. −15 °C)',
    'voll-wh'    => 'Vollwinterhart (bis −20 °C+)',
];

foreach ($_raw as $prod) {
    $pid      = $prod->get_id();
    $gattung  = (string) get_post_meta($pid, '_pa_gattung',  true);
    $art      = (string) get_post_meta($pid, '_pa_art',      true);
    $kultivar = (string) get_post_meta($pid, '_pa_kultivar', true);
    $wh       = (string) get_post_meta($pid, '_pa_winterhaerte', true);

    if ($gattung !== '') {
        if (!isset($pa_tax_tree[$gattung])) $pa_tax_tree[$gattung] = [];
        if ($art !== '' && !in_array($art, $pa_tax_tree[$gattung], true))
            $pa_tax_tree[$gattung][] = $art;
    }

    $p_tags       = []; // [{type:'Blütenfarbe', value:'Rot'}, …]
    $p_fixed_tags = []; // ['Rarität',…]
    $raw_tags = get_the_terms($pid, 'product_tag');
    if ($raw_tags && !is_wp_error($raw_tags)) {
        foreach ($raw_tags as $_tag) {
            $m = $_tag_meta[$_tag->term_id] ?? ['type' => '', 'prefix' => '', 'name' => $_tag->name];
            if ($m['type'] === 'variable') {
                $pfx = $m['prefix'];
                if ($pfx) {
                    if (!isset($pa_tag_tree[$pfx])) $pa_tag_tree[$pfx] = [];
                    if (!in_array($_tag->name, $pa_tag_tree[$pfx], true))
                        $pa_tag_tree[$pfx][] = $_tag->name;
                    $p_tags[] = ['type' => $pfx, 'value' => $_tag->name];
                }
            } elseif ($m['type'] === 'fixed' || $m['type'] === '') {
                $p_fixed_tags[] = $_tag->name;
            }
        }
    }

    $do = $prod->get_date_created();
    $pa_products[] = [
        'id'           => $pid,
        'name'         => $prod->get_name(),
        'gattung'      => $gattung,
        'art'          => $art,
        'kultivar'     => $kultivar,
        'price'        => (float)($prod->get_price() ?: 0),
        'priceHtml'    => $prod->get_price_html(),
        'img'          => esc_url(get_the_post_thumbnail_url($pid, 'woocommerce_thumbnail') ?: ''),
        'url'          => $prod->get_permalink(),
        'inStock'      => $prod->is_in_stock(),
        'onSale'       => $prod->is_on_sale(),
        'date'         => $do ? $do->getTimestamp() : 0,
        'rating'       => (float) $prod->get_average_rating(),
        'sales'        => (int) get_post_meta($pid, 'total_sales', true),
        'tags'         => $p_tags,
        'fixedTags'    => $p_fixed_tags,
        'winterhaerte' => $wh,
    ];
}

ksort($pa_tax_tree);
foreach ($pa_tax_tree as &$_ar) sort($_ar); unset($_ar);
ksort($pa_tag_tree);
foreach ($pa_tag_tree as &$_tv) sort($_tv); unset($_tv);

// ── Preisbereich ──────────────────────────────────────────────────────────────
$_all_prices = array_column($pa_products, 'price');
$pa_price_min = $_all_prices ? (int) floor(min($_all_prices)) : 0;
$pa_price_max = $_all_prices ? (int) ceil(max($_all_prices))  : 200;
if ($pa_price_max === $pa_price_min) $pa_price_max = $pa_price_min + 1;

// ── Winterhärte-Werte die tatsächlich vorkommen ───────────────────────────────
$pa_wh_present = array_values(array_unique(array_filter(array_column($pa_products, 'winterhaerte'))));
$pa_wh_order   = ['nicht-wh','bedingt-wh','winterhart','sehr-wh','voll-wh'];
usort($pa_wh_present, fn($a,$b) => array_search($a,$pa_wh_order) <=> array_search($b,$pa_wh_order));
?>

<script>
window.paProducts  = <?php echo wp_json_encode($pa_products); ?>;
window.paTaxTree   = <?php echo wp_json_encode($pa_tax_tree); ?>;
window.paTagTree   = <?php echo wp_json_encode($pa_tag_tree); ?>;
window.paFixedTags = <?php echo wp_json_encode($pa_fixed_tags); ?>;
window.paPriceMin  = <?php echo (int)$pa_price_min; ?>;
window.paPriceMax  = <?php echo (int)$pa_price_max; ?>;
window.paWHLabels  = <?php echo wp_json_encode($_wh_labels); ?>;
</script>

<main class="pa-shop-page">
<div class="pa-shop-main">

  <div class="pa-shop-header">
    <h1 class="pa-shop-title">Shop<em>.</em></h1>
    <p class="pa-shop-subtitle">Seltene Pelargonien &amp; botanische Raritäten</p>
  </div>

  <div class="pa-shop-layout">

    <!-- ── Sidebar ──────────────────────────────────────────────────────── -->
    <aside class="pa-shop-sidebar" id="pa-shop-sidebar">
      <div class="pa-sidebar-inner">

        <!-- Suche -->
        <div class="pa-search-wrap">
          <svg class="pa-search-ico" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
          </svg>
          <input type="text" id="pa-search" class="pa-search-inp" placeholder="Suchen…" autocomplete="off" spellcheck="false">
        </div>

        <div class="pa-sidebar-head">
          <span class="pa-sidebar-title">Filter</span>
          <button id="pa-clear-all" class="pa-clear-filters" style="display:none">Zurücksetzen</button>
        </div>

        <!-- Preis -->
        <div class="pa-filter-section">
          <h4 class="pa-filter-section-title">Preis</h4>
          <div class="pa-price-labels">
            <span id="pa-pdisplay-min"><?php echo $pa_price_min; ?> €</span>
            <span id="pa-pdisplay-max"><?php echo $pa_price_max; ?> €</span>
          </div>
          <div class="pa-slider-outer">
            <div class="pa-slider-track"><div class="pa-slider-fill" id="pa-slider-fill"></div></div>
            <div class="pa-slider-inputs">
              <input type="range" id="pa-prange-min" class="pa-prange" value="<?php echo $pa_price_min; ?>"
                     min="<?php echo $pa_price_min; ?>" max="<?php echo $pa_price_max; ?>" step="1">
              <input type="range" id="pa-prange-max" class="pa-prange" value="<?php echo $pa_price_max; ?>"
                     min="<?php echo $pa_price_min; ?>" max="<?php echo $pa_price_max; ?>" step="1">
            </div>
          </div>
        </div>

        <!-- Gattung & Art -->
        <?php if (!empty($pa_tax_tree)): ?>
        <div class="pa-filter-section">
          <h4 class="pa-filter-section-title">Gattung &amp; Art</h4>
          <?php foreach ($pa_tax_tree as $gattung => $arten):
            $g_slug = sanitize_html_class(strtolower($gattung));
          ?>
          <div class="pa-genus-wrap">
            <div class="pa-genus-row">
              <label class="pa-filter-label">
                <input type="checkbox" class="pa-g-cb"
                       data-gattung="<?php echo esc_attr($gattung); ?>"
                       data-slug="<?php echo esc_attr($g_slug); ?>">
                <span class="pa-filter-lbl"><?php echo esc_html($gattung); ?></span>
                <span class="pa-filter-badge">0</span>
              </label>
              <?php if (!empty($arten)): ?>
              <button type="button" class="pa-expand-btn"
                      data-target="pa-arts-<?php echo esc_attr($g_slug); ?>"
                      aria-label="Arten anzeigen">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9l6 6 6-6"/></svg>
              </button>
              <?php endif; ?>
            </div>
            <?php if (!empty($arten)): ?>
            <div class="pa-arts-panel" id="pa-arts-<?php echo esc_attr($g_slug); ?>" style="display:none">
              <?php foreach ($arten as $art): ?>
              <label class="pa-filter-label pa-art-label">
                <input type="checkbox" class="pa-a-cb"
                       data-gattung="<?php echo esc_attr($gattung); ?>"
                       data-art="<?php echo esc_attr($art); ?>">
                <span class="pa-filter-lbl"><em><?php echo esc_html($art); ?></em></span>
                <span class="pa-filter-badge">0</span>
              </label>
              <?php endforeach; ?>
            </div>
            <?php endif; ?>
          </div>
          <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <!-- Winterhärte -->
        <?php if (!empty($pa_wh_present)): ?>
        <div class="pa-filter-section">
          <div class="pa-filter-section-head">
            <h4 class="pa-filter-section-title">Winterhärte</h4>
            <button type="button" class="pa-expand-btn" data-target="pa-wh-panel" aria-label="Winterhärte anzeigen">
              <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9l6 6 6-6"/></svg>
            </button>
          </div>
          <div class="pa-arts-panel" id="pa-wh-panel" style="display:none">
            <?php foreach ($pa_wh_present as $wh_key): ?>
            <label class="pa-filter-label pa-art-label">
              <input type="checkbox" class="pa-wh-cb" data-wh="<?php echo esc_attr($wh_key); ?>">
              <span class="pa-filter-lbl"><?php echo esc_html($_wh_labels[$wh_key] ?? $wh_key); ?></span>
              <span class="pa-filter-badge">0</span>
            </label>
            <?php endforeach; ?>
          </div>
        </div>
        <?php endif; ?>

        <!-- Tag-Kategorien (expandierbar) -->
        <?php foreach ($pa_tag_tree as $tag_type => $tag_vals):
          $t_slug = sanitize_html_class(strtolower($tag_type));
        ?>
        <div class="pa-filter-section">
          <div class="pa-filter-section-head">
            <h4 class="pa-filter-section-title"><?php echo esc_html($tag_type); ?></h4>
            <button type="button" class="pa-expand-btn"
                    data-target="pa-tags-<?php echo esc_attr($t_slug); ?>"
                    aria-label="<?php echo esc_attr($tag_type); ?> anzeigen">
              <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9l6 6 6-6"/></svg>
            </button>
          </div>
          <div class="pa-arts-panel" id="pa-tags-<?php echo esc_attr($t_slug); ?>" style="display:none">
            <?php foreach ($tag_vals as $tv): ?>
            <label class="pa-filter-label pa-art-label">
              <input type="checkbox" class="pa-tag-cb"
                     data-type="<?php echo esc_attr($tag_type); ?>"
                     data-value="<?php echo esc_attr($tv); ?>">
              <span class="pa-filter-lbl"><?php echo esc_html($tv); ?></span>
              <span class="pa-filter-badge">0</span>
            </label>
            <?php endforeach; ?>
          </div>
        </div>
        <?php endforeach; ?>

        <!-- Reguläre Tags (flache Liste) -->
        <?php if (!empty($pa_fixed_tags)): ?>
        <div class="pa-filter-section">
          <div class="pa-filter-section-head">
            <h4 class="pa-filter-section-title">Tags</h4>
            <button type="button" class="pa-expand-btn" data-target="pa-fixed-tags-panel" aria-label="Tags anzeigen">
              <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9l6 6 6-6"/></svg>
            </button>
          </div>
          <div class="pa-arts-panel" id="pa-fixed-tags-panel" style="display:none">
            <?php foreach ($pa_fixed_tags as $ft): ?>
            <label class="pa-filter-label pa-art-label">
              <input type="checkbox" class="pa-ft-cb" data-tag="<?php echo esc_attr($ft); ?>">
              <span class="pa-filter-lbl"><?php echo esc_html($ft); ?></span>
              <span class="pa-filter-badge">0</span>
            </label>
            <?php endforeach; ?>
          </div>
        </div>
        <?php endif; ?>

      </div>
    </aside><!-- /sidebar -->

    <!-- ── Produktbereich ────────────────────────────────────────────────── -->
    <div class="pa-shop-content-wrap">

      <div class="pa-shop-toolbar">
        <div style="display:flex;align-items:center;gap:12px;flex-wrap:wrap;">
          <button class="pa-filter-toggle" id="pa-filter-toggle">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
              <line x1="4" y1="21" x2="4" y2="14"/><line x1="4" y1="10" x2="4" y2="3"/>
              <line x1="12" y1="21" x2="12" y2="12"/><line x1="12" y1="8" x2="12" y2="3"/>
              <line x1="20" y1="21" x2="20" y2="16"/><line x1="20" y1="12" x2="20" y2="3"/>
              <line x1="1" y1="14" x2="7" y2="14"/><line x1="9" y1="8" x2="15" y2="8"/><line x1="17" y1="16" x2="23" y2="16"/>
            </svg>
            Filter
            <span class="pa-filter-badge" id="pa-active-count" style="display:none">0</span>
          </button>
          <span class="pa-result-count" id="pa-result-count"></span>
        </div>
        <div style="display:flex;align-items:center;gap:8px;">
          <select class="pa-sort-select" id="pa-sort-select" aria-label="Sortierung">
            <option value="">Standard</option>
            <option value="date">Neueste zuerst</option>
            <option value="price">Preis aufsteigend</option>
            <option value="price-desc">Preis absteigend</option>
            <option value="name">Name A → Z</option>
            <option value="name-desc">Name Z → A</option>
            <option value="popularity">Beliebtheit</option>
            <option value="rating">Bewertung</option>
          </select>
          <div class="pa-shop-view-toggle">
            <button class="pa-view-btn active" id="pa-view-grid" aria-label="Kachelansicht">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                <rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/>
                <rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/>
              </svg>
            </button>
            <button class="pa-view-btn" id="pa-view-list" aria-label="Listenansicht">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                <line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/>
                <line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/>
                <line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/>
              </svg>
            </button>
          </div>
        </div>
      </div>

      <!-- Aktive Filter-Chips -->
      <div id="pa-active-chips" class="pa-active-filters" style="display:none"></div>

      <!-- Produkt-Grid (JS-gerendert) -->
      <div class="pa-grid" id="pa-product-grid"></div>

    </div><!-- /content-wrap -->
  </div><!-- /layout -->
</div><!-- /shop-main -->
</main>

<script>
(function () {
'use strict';

var ALL      = window.paProducts  || [];
var TAX      = window.paTaxTree   || {};
var TAGS     = window.paTagTree   || {};
var FIXED    = window.paFixedTags || [];
var WH_LABELS= window.paWHLabels  || {};
var P_MIN    = window.paPriceMin  || 0;
var P_MAX    = window.paPriceMax  || 200;

var state = {
  search:       '',
  gattung:      [],
  art:          [],
  tags:         {},   // { 'Blütenfarbe': ['Rot'] }
  fixedTags:    [],   // ['Rarität']
  winterhaerte: [],   // ['nicht-wh']
  priceMin:     P_MIN,
  priceMax:     P_MAX,
  sort:         '',
  view:         localStorage.getItem('pa-shop-view') || 'grid'
};

// ── Helpers ────────────────────────────────────────────────────────────────
function esc(s) {
  return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}
function has(arr, val) { return arr.indexOf(val) >= 0; }

// ── Filter ─────────────────────────────────────────────────────────────────
function applySearch(prods) {
  if (!state.search) return prods;
  var q = state.search.toLowerCase();
  return prods.filter(function(p) {
    return p.gattung.toLowerCase().indexOf(q) >= 0 ||
           p.art.toLowerCase().indexOf(q) >= 0 ||
           p.kultivar.toLowerCase().indexOf(q) >= 0 ||
           p.name.toLowerCase().indexOf(q) >= 0;
  });
}
function applyTaxonomy(prods) {
  if (!state.gattung.length && !state.art.length) return prods;
  return prods.filter(function(p) {
    return has(state.gattung, p.gattung) || has(state.art, p.art);
  });
}
function applyTags(prods, exceptType) {
  var types = Object.keys(state.tags).filter(function(t) {
    return t !== exceptType && state.tags[t] && state.tags[t].length;
  });
  return types.reduce(function(acc, type) {
    return acc.filter(function(p) {
      return p.tags.some(function(t) { return t.type === type && has(state.tags[type], t.value); });
    });
  }, prods);
}
function applyFixedTags(prods) {
  if (!state.fixedTags.length) return prods;
  return prods.filter(function(p) {
    return state.fixedTags.some(function(ft) { return has(p.fixedTags, ft); });
  });
}
function applyWinterhaerte(prods) {
  if (!state.winterhaerte.length) return prods;
  return prods.filter(function(p) { return has(state.winterhaerte, p.winterhaerte); });
}
function applyPrice(prods) {
  return prods.filter(function(p) {
    return p.price >= state.priceMin && p.price <= state.priceMax;
  });
}

function getFiltered() {
  return applyPrice(applyWinterhaerte(applyFixedTags(applyTags(applyTaxonomy(applySearch(ALL))))));
}

// ── Sort ───────────────────────────────────────────────────────────────────
function sorted(prods) {
  var s = prods.slice();
  switch (state.sort) {
    case 'price':      return s.sort(function(a,b){return a.price-b.price;});
    case 'price-desc': return s.sort(function(a,b){return b.price-a.price;});
    case 'name':       return s.sort(function(a,b){return a.name.localeCompare(b.name,'de');});
    case 'name-desc':  return s.sort(function(a,b){return b.name.localeCompare(a.name,'de');});
    case 'date':       return s.sort(function(a,b){return b.date-a.date;});
    case 'rating':     return s.sort(function(a,b){return b.rating-a.rating;});
    case 'popularity': return s.sort(function(a,b){return b.sales-a.sales;});
    default:           return s;
  }
}

// ── Card rendering ─────────────────────────────────────────────────────────
function cardHtml(p) {
  var genus = [p.gattung, p.art].filter(Boolean).join(' ');
  var main  = p.kultivar ? '‘' + esc(p.kultivar) + '’'
                         : esc(p.art || p.gattung || p.name);
  var badges = (p.onSale  ? '<span class="pa-badge">Sale</span>' : '') +
               (!p.inStock ? '<span class="pa-badge out">Ausverkauft</span>' : '');
  return (
    '<a href="' + esc(p.url) + '" class="pa-card">' +
      '<div class="pa-card-img" style="background-image:url(' + esc(p.img) + ')">' + badges + '</div>' +
      '<div class="pa-card-body">' +
        (genus ? '<div class="pa-card-taxonomy">' + esc(genus) + '</div>' : '') +
        '<div class="pa-card-name">' + main + '</div>' +
        '<div class="pa-card-foot"><span class="pa-card-price">' + p.priceHtml + '</span></div>' +
      '</div>' +
    '</a>'
  );
}

// ── Render products ────────────────────────────────────────────────────────
function render() {
  var filtered = getFiltered();
  var result   = sorted(filtered);
  var grid = document.getElementById('pa-product-grid');

  grid.innerHTML = result.length
    ? result.map(cardHtml).join('')
    : '<p class="pa-no-results">Keine Produkte gefunden.</p>';

  var ce = document.getElementById('pa-result-count');
  if (ce) ce.textContent = result.length + (result.length === 1 ? ' Produkt' : ' Produkte');

  var total = state.gattung.length + state.art.length + state.fixedTags.length + state.winterhaerte.length +
              Object.keys(state.tags).reduce(function(n,t){return n+(state.tags[t]||[]).length;},0) +
              (state.search ? 1 : 0) +
              (state.priceMin > P_MIN || state.priceMax < P_MAX ? 1 : 0);
  var ab = document.getElementById('pa-active-count');
  if (ab) { ab.textContent = total; ab.style.display = total ? '' : 'none'; }
  var cb = document.getElementById('pa-clear-all');
  if (cb) cb.style.display = total ? '' : 'none';

  updateCounts();
  renderChips();
  updatePriceSlider();
}

// ── Preisregler ────────────────────────────────────────────────────────────
function updatePriceSlider() {
  if (P_MAX === P_MIN) return;
  var minPct = (state.priceMin - P_MIN) / (P_MAX - P_MIN) * 100;
  var maxPct = (state.priceMax - P_MIN) / (P_MAX - P_MIN) * 100;
  var fill = document.getElementById('pa-slider-fill');
  if (fill) { fill.style.left = minPct + '%'; fill.style.width = (maxPct - minPct) + '%'; }
  var dm = document.getElementById('pa-pdisplay-min');
  var dx = document.getElementById('pa-pdisplay-max');
  if (dm) dm.textContent = state.priceMin.toFixed(0) + ' €';
  if (dx) dx.textContent = state.priceMax.toFixed(0) + ' €';
}

var rMin = document.getElementById('pa-prange-min');
var rMax = document.getElementById('pa-prange-max');
if (rMin) rMin.addEventListener('input', function() {
  var v = parseFloat(this.value);
  if (v > state.priceMax) { this.value = state.priceMax; v = state.priceMax; }
  state.priceMin = v; render();
});
if (rMax) rMax.addEventListener('input', function() {
  var v = parseFloat(this.value);
  if (v < state.priceMin) { this.value = state.priceMin; v = state.priceMin; }
  state.priceMax = v; render();
});

// ── Update counts ──────────────────────────────────────────────────────────
function baseForTaxonomy() {
  return applyPrice(applyWinterhaerte(applyFixedTags(applyTags(applySearch(ALL)))));
}

function updateCounts() {
  var base = baseForTaxonomy();

  document.querySelectorAll('.pa-g-cb').forEach(function(cb) {
    var g = cb.dataset.gattung;
    var n = base.filter(function(p){return p.gattung===g;}).length;
    var badge = cb.closest('.pa-filter-label').querySelector('.pa-filter-badge');
    if (badge) badge.textContent = n;
  });

  document.querySelectorAll('.pa-a-cb').forEach(function(cb) {
    var g = cb.dataset.gattung, a = cb.dataset.art;
    var n = base.filter(function(p){return p.gattung===g && p.art===a;}).length;
    var badge = cb.closest('.pa-filter-label').querySelector('.pa-filter-badge');
    if (badge) badge.textContent = n;
  });

  // Gruppierte Tags
  document.querySelectorAll('.pa-tag-cb').forEach(function(cb) {
    var type = cb.dataset.type, val = cb.dataset.value;
    var base2 = applyPrice(applyWinterhaerte(applyFixedTags(applyTags(applyTaxonomy(applySearch(ALL)), type))));
    var n = base2.filter(function(p){
      return p.tags.some(function(t){return t.type===type && t.value===val;});
    }).length;
    var badge = cb.closest('.pa-filter-label').querySelector('.pa-filter-badge');
    if (badge) badge.textContent = n;
  });

  // Reguläre Tags
  document.querySelectorAll('.pa-ft-cb').forEach(function(cb) {
    var tag = cb.dataset.tag;
    var base3 = applyPrice(applyWinterhaerte(applyTags(applyTaxonomy(applySearch(ALL)))));
    var n = base3.filter(function(p){ return has(p.fixedTags, tag); }).length;
    var badge = cb.closest('.pa-filter-label').querySelector('.pa-filter-badge');
    if (badge) badge.textContent = n;
  });

  // Winterhärte
  document.querySelectorAll('.pa-wh-cb').forEach(function(cb) {
    var wh = cb.dataset.wh;
    var base4 = applyPrice(applyFixedTags(applyTags(applyTaxonomy(applySearch(ALL)))));
    var n = base4.filter(function(p){ return p.winterhaerte === wh; }).length;
    var badge = cb.closest('.pa-filter-label').querySelector('.pa-filter-badge');
    if (badge) badge.textContent = n;
  });
}

// ── Chips ──────────────────────────────────────────────────────────────────
function renderChips() {
  var el = document.getElementById('pa-active-chips');
  if (!el) return;
  var html = '';

  state.gattung.forEach(function(g) {
    html += '<button class="pa-filter-chip" data-rem="g:'+esc(g)+'">'+esc(g)+'<span class="pa-chip-x">×</span></button>';
  });
  state.art.forEach(function(a) {
    html += '<button class="pa-filter-chip" data-rem="a:'+esc(a)+'"><em>'+esc(a)+'</em><span class="pa-chip-x">×</span></button>';
  });
  Object.keys(state.tags).forEach(function(type) {
    (state.tags[type]||[]).forEach(function(v) {
      html += '<button class="pa-filter-chip" data-rem="t:'+esc(type)+':'+esc(v)+'">'+esc(type)+': '+esc(v)+'<span class="pa-chip-x">×</span></button>';
    });
  });
  state.fixedTags.forEach(function(ft) {
    html += '<button class="pa-filter-chip" data-rem="ft:'+esc(ft)+'">'+esc(ft)+'<span class="pa-chip-x">×</span></button>';
  });
  state.winterhaerte.forEach(function(wh) {
    var label = WH_LABELS[wh] || wh;
    html += '<button class="pa-filter-chip" data-rem="wh:'+esc(wh)+'">❄ '+esc(label)+'<span class="pa-chip-x">×</span></button>';
  });
  if (state.search) {
    html += '<button class="pa-filter-chip" data-rem="s">„'+esc(state.search)+'“<span class="pa-chip-x">×</span></button>';
  }
  if (state.priceMin > P_MIN || state.priceMax < P_MAX) {
    html += '<button class="pa-filter-chip" data-rem="p">'+state.priceMin+' €–'+state.priceMax+' €<span class="pa-chip-x">×</span></button>';
  }

  el.innerHTML = html;
  el.style.display = html ? 'flex' : 'none';

  el.querySelectorAll('.pa-filter-chip').forEach(function(chip) {
    chip.addEventListener('click', function() {
      removeFilter(chip.dataset.rem);
      render();
    });
  });
}

function removeFilter(rem) {
  if (rem === 's') {
    state.search = '';
    var si = document.getElementById('pa-search'); if (si) si.value = '';
  } else if (rem === 'p') {
    state.priceMin = P_MIN; state.priceMax = P_MAX;
    var rn = document.getElementById('pa-prange-min'); if (rn) rn.value = P_MIN;
    var rx = document.getElementById('pa-prange-max'); if (rx) rx.value = P_MAX;
  } else if (rem.startsWith('g:')) {
    var g = rem.slice(2);
    state.gattung = state.gattung.filter(function(x){return x!==g;});
    var gcb = document.querySelector('.pa-g-cb[data-gattung="'+g+'"]');
    if (gcb) { gcb.checked=false; gcb.indeterminate=false; }
    document.querySelectorAll('.pa-a-cb[data-gattung="'+g+'"]').forEach(function(a){a.checked=false;});
    state.art = state.art.filter(function(a){return !(TAX[g]||[]).includes(a);});
  } else if (rem.startsWith('a:')) {
    var a = rem.slice(2);
    state.art = state.art.filter(function(x){return x!==a;});
    var acb = document.querySelector('.pa-a-cb[data-art="'+a+'"]');
    if (acb) { acb.checked=false; syncGattungCb(acb.dataset.gattung); }
  } else if (rem.startsWith('t:')) {
    var p2 = rem.split(':'), tt = p2[1], tv = p2[2];
    if (state.tags[tt]) state.tags[tt] = state.tags[tt].filter(function(x){return x!==tv;});
    var tcb = document.querySelector('.pa-tag-cb[data-type="'+tt+'"][data-value="'+tv+'"]');
    if (tcb) tcb.checked = false;
  } else if (rem.startsWith('ft:')) {
    var ft = rem.slice(3);
    state.fixedTags = state.fixedTags.filter(function(x){return x!==ft;});
    var ftcb = document.querySelector('.pa-ft-cb[data-tag="'+ft+'"]');
    if (ftcb) ftcb.checked = false;
  } else if (rem.startsWith('wh:')) {
    var wh = rem.slice(3);
    state.winterhaerte = state.winterhaerte.filter(function(x){return x!==wh;});
    var whcb = document.querySelector('.pa-wh-cb[data-wh="'+wh+'"]');
    if (whcb) whcb.checked = false;
  }
}

// ── Sync gattung checkbox ──────────────────────────────────────────────────
function syncGattungCb(g) {
  if (!g) return;
  var gcb  = document.querySelector('.pa-g-cb[data-gattung="'+g+'"]'); if (!gcb) return;
  var arts = Array.from(document.querySelectorAll('.pa-a-cb[data-gattung="'+g+'"]'));
  var all  = arts.every(function(c){return c.checked;});
  var some = arts.some(function(c){return c.checked;});
  gcb.checked = all; gcb.indeterminate = !all && some;
  if (all) {
    if (!has(state.gattung,g)) state.gattung.push(g);
    state.art = state.art.filter(function(a){return !(TAX[g]||[]).includes(a);});
  } else {
    state.gattung = state.gattung.filter(function(x){return x!==g;});
  }
}

// ── Event handlers ─────────────────────────────────────────────────────────
var _stimer;
var sinp = document.getElementById('pa-search');
if (sinp) sinp.addEventListener('input', function() {
  clearTimeout(_stimer);
  _stimer = setTimeout(function(){ state.search = sinp.value.trim(); render(); }, 150);
});

document.querySelectorAll('.pa-g-cb').forEach(function(cb) {
  cb.addEventListener('change', function() {
    var g = cb.dataset.gattung, slug = cb.dataset.slug;
    if (cb.checked) {
      if (!has(state.gattung,g)) state.gattung.push(g);
      state.art = state.art.filter(function(a){return !(TAX[g]||[]).includes(a);});
      document.querySelectorAll('.pa-a-cb[data-gattung="'+g+'"]').forEach(function(a){a.checked=true;});
      var panel = document.getElementById('pa-arts-'+slug);
      var btn   = document.querySelector('.pa-expand-btn[data-target="pa-arts-'+slug+'"]');
      if (panel && panel.style.display==='none') panel.style.display='';
      if (btn)   btn.classList.add('open');
    } else {
      state.gattung = state.gattung.filter(function(x){return x!==g;});
      state.art = state.art.filter(function(a){return !(TAX[g]||[]).includes(a);});
      document.querySelectorAll('.pa-a-cb[data-gattung="'+g+'"]').forEach(function(a){a.checked=false;});
    }
    cb.indeterminate = false;
    render();
  });
});

document.querySelectorAll('.pa-a-cb').forEach(function(cb) {
  cb.addEventListener('change', function() {
    var a = cb.dataset.art, g = cb.dataset.gattung;
    if (cb.checked) { if (!has(state.art,a)) state.art.push(a); }
    else            { state.art = state.art.filter(function(x){return x!==a;}); }
    syncGattungCb(g);
    render();
  });
});

document.querySelectorAll('.pa-tag-cb').forEach(function(cb) {
  cb.addEventListener('change', function() {
    var type = cb.dataset.type, val = cb.dataset.value;
    if (!state.tags[type]) state.tags[type] = [];
    if (cb.checked) { if (!has(state.tags[type],val)) state.tags[type].push(val); }
    else            { state.tags[type] = state.tags[type].filter(function(x){return x!==val;}); }
    render();
  });
});

document.querySelectorAll('.pa-ft-cb').forEach(function(cb) {
  cb.addEventListener('change', function() {
    var tag = cb.dataset.tag;
    if (cb.checked) { if (!has(state.fixedTags,tag)) state.fixedTags.push(tag); }
    else            { state.fixedTags = state.fixedTags.filter(function(x){return x!==tag;}); }
    render();
  });
});

document.querySelectorAll('.pa-wh-cb').forEach(function(cb) {
  cb.addEventListener('change', function() {
    var wh = cb.dataset.wh;
    if (cb.checked) { if (!has(state.winterhaerte,wh)) state.winterhaerte.push(wh); }
    else            { state.winterhaerte = state.winterhaerte.filter(function(x){return x!==wh;}); }
    render();
  });
});

document.querySelectorAll('.pa-expand-btn').forEach(function(btn) {
  btn.addEventListener('click', function() {
    var panel = document.getElementById(btn.dataset.target); if (!panel) return;
    var open  = panel.style.display !== 'none';
    panel.style.display = open ? 'none' : '';
    btn.classList.toggle('open', !open);
  });
});

var sortEl = document.getElementById('pa-sort-select');
if (sortEl) sortEl.addEventListener('change', function(){ state.sort = this.value; render(); });

var clearBtn = document.getElementById('pa-clear-all');
if (clearBtn) clearBtn.addEventListener('click', function() {
  state.search=''; state.gattung=[]; state.art=[]; state.tags={}; state.fixedTags=[]; state.winterhaerte=[];
  state.priceMin=P_MIN; state.priceMax=P_MAX;
  document.querySelectorAll('.pa-g-cb,.pa-a-cb,.pa-tag-cb,.pa-ft-cb,.pa-wh-cb').forEach(function(cb){cb.checked=false;cb.indeterminate=false;});
  var si = document.getElementById('pa-search'); if (si) si.value='';
  var rn = document.getElementById('pa-prange-min'); if (rn) rn.value = P_MIN;
  var rx = document.getElementById('pa-prange-max'); if (rx) rx.value = P_MAX;
  render();
});

var gbtn = document.getElementById('pa-view-grid');
var lbtn = document.getElementById('pa-view-list');
var pgrid= document.getElementById('pa-product-grid');
if (state.view==='list') { pgrid.classList.add('pa-view-list'); if(lbtn)lbtn.classList.add('active'); if(gbtn)gbtn.classList.remove('active'); }
if (gbtn) gbtn.addEventListener('click', function(){
  pgrid.classList.remove('pa-view-list');
  gbtn.classList.add('active'); lbtn.classList.remove('active');
  state.view='grid'; localStorage.setItem('pa-shop-view','grid');
});
if (lbtn) lbtn.addEventListener('click', function(){
  pgrid.classList.add('pa-view-list');
  lbtn.classList.add('active'); gbtn.classList.remove('active');
  state.view='list'; localStorage.setItem('pa-shop-view','list');
});

var ftoggle = document.getElementById('pa-filter-toggle');
var fbar    = document.getElementById('pa-shop-sidebar');
if (ftoggle) ftoggle.addEventListener('click', function(){ fbar.classList.toggle('open'); });

render();
})();
</script>

<?php get_footer(); ?>
