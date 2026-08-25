<?php
/**
 * Template Name: Produkt hinzufügen (Admin)
 */

get_header();

if (!current_user_can('manage_options')) {
    echo '<div style="padding: 40px; text-align: center;">';
    echo '<h2>Zugriff verweigert</h2>';
    echo '<p>Diese Seite ist nur für Administratoren zugänglich.</p>';
    echo '<a href="' . home_url() . '" class="button">Zurück zur Startseite</a>';
    echo '</div>';
    get_footer();
    exit;
}

wp_enqueue_media();

// WooCommerce Steuer- und Versandklassen laden
$tax_classes = array_merge(['standard' => 'Standard (19%)'], WC_Tax::get_tax_classes() ? array_combine(
    array_map('sanitize_title', WC_Tax::get_tax_classes()),
    WC_Tax::get_tax_classes()
) : []);

$shipping_class_terms = get_terms(['taxonomy' => 'product_shipping_class', 'hide_empty' => false]);
$shipping_classes = [];
if (!is_wp_error($shipping_class_terms)) {
    foreach ($shipping_class_terms as $term) {
        $shipping_classes[$term->term_id] = $term->name;
    }
}
?>

<style>
.switch {
    position: relative;
    display: inline-block;
    width: 50px;
    height: 24px;
}
.switch input { opacity: 0; width: 0; height: 0; }
.slider {
    position: absolute;
    cursor: pointer;
    top: 0; left: 0; right: 0; bottom: 0;
    background-color: var(--bg-inky);
    transition: .3s;
    border-radius: 24px;
    border: 1px solid var(--border-thin);
}
.slider:before {
    position: absolute;
    content: "";
    height: 18px;
    width: 18px;
    left: 3px;
    bottom: 3px;
    background-color: var(--creme-dim);
    transition: .3s;
    border-radius: 50%;
}
input:checked + .slider { background-color: var(--plum); border-color: var(--plum-hot); }
input:checked + .slider:before { transform: translateX(26px); background-color: var(--creme); }

.form-row {
    display: flex;
    flex-direction: column;
    gap: 6px;
    margin-bottom: 20px;
}
.form-row label {
    font-size: 14px;
    font-weight: 500;
    color: var(--creme);
}
.form-row input[type="text"],
.form-row input[type="number"],
.form-row select,
.form-row textarea {
    padding: 10px 12px;
    font-size: 14px;
    border: 1px solid var(--border-thin);
    border-radius: 2px;
    width: 100%;
    box-sizing: border-box;
    background: var(--bg-surface);
    color: var(--creme);
}
.form-row input[type="text"]:focus,
.form-row input[type="number"]:focus,
.form-row select:focus {
    outline: none;
    border-color: var(--plum);
}
.form-section {
    background: var(--bg-surface);
    border: 1px solid var(--border-thin);
    border-radius: 2px;
    padding: 24px;
    margin-bottom: 20px;
}
.form-section h3 {
    margin: 0 0 20px 0;
    font-size: 15px;
    font-weight: 600;
    color: var(--creme);
    padding-bottom: 12px;
    border-bottom: 1px solid var(--border-hair);
}
.row-2col {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
}
.row-3col {
    display: grid;
    grid-template-columns: 1fr 1fr 1fr;
    gap: 16px;
}
.toggle-row {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 20px;
}
.toggle-row label { font-size: 14px; font-weight: 500; color: var(--creme); }
.toggle-label {
    font-size: 13px;
    color: var(--creme-dim);
    min-width: 50px;
}
.checkbox-row {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 14px;
}
.checkbox-row input[type="checkbox"] { width: 16px; height: 16px; cursor: pointer; }
.checkbox-row label { font-size: 14px; color: var(--creme); cursor: pointer; }
.tooltip-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 16px;
    height: 16px;
    background: var(--bg-raised);
    color: var(--creme);
    border-radius: 50%;
    font-size: 10px;
    cursor: help;
    position: relative;
}
.tooltip-icon:hover .tooltip-text {
    display: block;
}
.tooltip-text {
    display: none;
    position: absolute;
    bottom: 120%;
    left: 50%;
    transform: translateX(-50%);
    background: var(--bg-inky);
    color: var(--creme);
    font-size: 12px;
    padding: 6px 10px;
    border-radius: 3px;
    white-space: nowrap;
    z-index: 100;
    pointer-events: none;
    min-width: 220px;
    white-space: normal;
    line-height: 1.4;
}
.image-upload-area {
    border: 2px dashed var(--border-thin);
    border-radius: 2px;
    padding: 20px;
    text-align: center;
    cursor: pointer;
    transition: border-color .2s;
    background: var(--bg-raised);
    min-height: 100px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-direction: column;
    gap: 8px;
}
.image-upload-area:hover { border-color: var(--plum); }
.image-preview-grid {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-top: 10px;
}
.image-preview-item {
    position: relative;
    width: 80px;
    height: 80px;
}
.image-preview-item img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 2px;
    border: 1px solid var(--border-thin);
}
.image-preview-item .remove-img {
    position: absolute;
    top: -6px;
    right: -6px;
    width: 18px;
    height: 18px;
    background: var(--plum-hot);
    color: var(--creme);
    border: none;
    border-radius: 50%;
    font-size: 11px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    line-height: 1;
}
.rte-toolbar {
    display: flex;
    gap: 4px;
    padding: 8px;
    background: var(--bg-raised);
    border: 1px solid var(--border-thin);
    border-bottom: none;
    border-radius: 2px 2px 0 0;
    flex-wrap: wrap;
}
.rte-btn {
    padding: 4px 8px;
    background: var(--bg-surface);
    border: 1px solid var(--border-thin);
    border-radius: 2px;
    cursor: pointer;
    font-size: 13px;
    font-family: inherit;
    color: var(--creme);
}
.rte-btn:hover { background: var(--bg-deep); }
#product-description {
    min-height: 160px;
    padding: 12px;
    border: 1px solid var(--border-thin);
    border-radius: 0 0 2px 2px;
    font-size: 14px;
    outline: none;
    line-height: 1.5;
    background: var(--bg-surface);
    color: var(--creme);
}
.submit-row {
    display: flex;
    gap: 12px;
    align-items: center;
    margin-top: 10px;
}
.btn-primary {
    padding: 12px 28px;
    background: var(--plum);
    color: var(--creme);
    border: none;
    border-radius: 2px;
    cursor: pointer;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.14em;
    text-transform: uppercase;
}
.btn-primary:hover { background: var(--plum-hot); }
.btn-secondary {
    padding: 12px 20px;
    background: var(--bg-surface);
    color: var(--creme);
    border: 1px solid var(--border-thin);
    border-radius: 2px;
    cursor: pointer;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.14em;
    text-transform: uppercase;
}
.btn-secondary:hover { background: var(--bg-raised); }
#form-message {
    padding: 12px 16px;
    border-radius: 2px;
    font-size: 14px;
    display: none;
}
#form-message.success { background: var(--bg-raised); color: var(--creme); border: 1px solid var(--border-thin); }
#form-message.error   { background: var(--bg-raised); color: var(--creme); border: 1px solid var(--border-thin); }
/* ── Crop & Watermark Modal ────────────────────────────────────────────────── */
#cwm-overlay {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,.82);
    z-index: 99999;
    align-items: center;
    justify-content: center;
}
#cwm-overlay.open { display: flex; }
#cwm-dialog {
    background: var(--bg-surface);
    border-radius: 6px;
    padding: 24px;
    width: min(920px, 96vw);
    max-height: 94vh;
    overflow-y: auto;
    box-shadow: 0 8px 40px rgba(0,0,0,.4);
    border: 1px solid var(--border-thin);
}
#cwm-dialog h3 { margin: 0 0 18px; font-size: 16px; color: var(--creme); }
.cwm-body {
    display: flex;
    gap: 20px;
    align-items: flex-start;
}
#cwm-canvas {
    display: block;
    border-radius: 3px;
    cursor: grab;
    touch-action: none;
    flex-shrink: 0;
}
#cwm-canvas.dragging { cursor: grabbing; }
.cwm-controls {
    flex: 1;
    min-width: 180px;
}
.cwm-ctrl-label {
    font-size: 12px;
    font-weight: 600;
    color: var(--creme-dim);
    text-transform: uppercase;
    letter-spacing: .5px;
    margin: 0 0 8px;
}
.cwm-ctrl-label:not(:first-child) { margin-top: 18px; }
#cwm-zoom { width: 100%; margin: 4px 0 0; }
.cwm-checkbox-row {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 8px;
    font-size: 14px;
    color: var(--creme);
}
.cwm-hint {
    font-size: 12px;
    color: var(--creme-muted);
    line-height: 1.4;
    margin-top: 6px;
}
.cwm-btns {
    display: flex;
    gap: 10px;
    margin-top: 24px;
}
#cwm-upload-progress {
    font-size: 13px;
    color: var(--creme-dim);
    margin-top: 8px;
    display: none;
}
/* ── end CWM ─────────────────────────────────────────────────────────────── */

.bulk-upload-area {
    border: 2px dashed var(--border-thin);
    border-radius: 2px;
    padding: 30px 20px;
    text-align: center;
    cursor: pointer;
    background: var(--bg-raised);
}
.bulk-upload-area:hover { border-color: var(--plum); }
.tab-bar {
    display: flex;
    gap: 0;
    margin-bottom: 24px;
    border-bottom: 2px solid #e8e8e8;
}
.tab-btn {
    padding: 10px 20px;
    background: none;
    border: none;
    border-bottom: 2px solid transparent;
    cursor: pointer;
    font-size: 14px;
    font-weight: 500;
    color: #888;
    margin-bottom: -2px;
}
.tab-btn.active {
    color: #333;
    border-bottom-color: #333;
}
.tab-pane { display: none; }
.tab-pane.active { display: block; }

/* ── Tag Pool ──────────────────────────────────────────────────────────────── */
.tag-group { margin-bottom: 14px; }
.tag-group-label { font-size: 11px; font-weight: 700; color: #888; text-transform: uppercase; letter-spacing: .6px; margin: 0 0 7px; }
.tag-chips { display: flex; flex-wrap: wrap; gap: 5px; }
.tag-chip {
    padding: 4px 12px; background: #f4f4f4; border: 1.5px solid #ddd;
    border-radius: 20px; font-size: 13px; cursor: pointer; transition: all .12s; user-select: none;
    display: inline-flex; align-items: center; gap: 3px;
}
.tag-chip:hover { border-color: #aaa; background: #ebebeb; }
.pool-del-btn { background:none; border:none; padding:0; cursor:pointer; color:inherit; opacity:0.3; font-size:15px; line-height:1; transition:opacity .12s; flex-shrink:0; }
.pool-del-btn:hover { opacity:1; }
.tag-group-label { display:flex; align-items:center; gap:6px; }
.tag-chip.tag-selected { background: #222; color: #fff; border-color: #222; }
.tag-chip.tag-variation { border-color: #4a6fcc; color: #2c4fa0; }
.tag-chip.tag-variation:hover { background: #e8edfb; }
.tag-chip.tag-variation.tag-selected { background: #2c4fa0; color: #fff; border-color: #2c4fa0; }
.tag-chip.tag-category { border-color: #4a9e5c; color: #2a6e3a; }
.tag-chip.tag-category:hover { background: #e8f4eb; }
.tag-chip.tag-category.tag-selected { background: #2a6e3a; color: #fff; border-color: #2a6e3a; }

.selected-tag-pill {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 4px 10px; background: #333; color: #fff; border-radius: 20px; font-size: 12px;
}
.selected-tag-pill.pill-variation { background: #2c4fa0; }
.selected-tag-pill.pill-category { background: #2a6e3a; }
.selected-tag-pill button {
    background: none; border: none; color: rgba(255,255,255,.7); cursor: pointer;
    font-size: 14px; line-height: 1; padding: 0 0 0 2px;
}
.selected-tag-pill button:hover { color: #fff; }

#tag-create-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,.55); z-index:99999; align-items:center; justify-content:center; }
#tag-create-overlay.open { display:flex; }
#tag-create-dialog { background:#fff; border-radius:6px; padding:24px; width:min(440px,96vw); box-shadow:0 8px 40px rgba(0,0,0,.3); }
#tag-create-dialog h3 { margin:0 0 18px; font-size:15px; }

/* Variable-tag inline input */
.vtag-input-wrap { display:inline-flex; align-items:center; gap:4px; border:1.5px solid #4a6fcc; border-radius:20px; padding:2px 8px; background:#f0f4ff; }
.vtag-input-wrap input { border:none; outline:none; background:transparent; font-size:13px; color:#222; width:100px; }
.vtag-input-wrap button { background:none; border:none; cursor:pointer; font-size:14px; color:#2c4fa0; padding:0; line-height:1; }

.variant-row { display:grid; grid-template-columns:repeat(auto-fill,minmax(120px,1fr)) 120px 80px 36px; gap:8px; align-items:center; padding:10px 12px; background:#f8f9fa; border:1px solid #e8e8e8; border-radius:4px; margin-bottom:8px; }
/* ── Care Chips ──────────────────────────────────────────────────────────── */
.tag-chip.tag-care { border-color: #cc8a2e; color: #8a5c10; }
.tag-chip.tag-care:hover { background: #fef5e7; }
.tag-chip.tag-care.tag-selected { background: #cc8a2e; color: #fff; border-color: #cc8a2e; }
#species-create-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,.55); z-index:99999; align-items:center; justify-content:center; }
#species-create-overlay.open { display:flex; }
#species-create-dialog { background:#fff; border-radius:6px; padding:24px; width:min(520px,96vw); max-height:90vh; overflow-y:auto; box-shadow:0 8px 40px rgba(0,0,0,.3); }
#species-create-dialog h3 { margin:0 0 18px; font-size:15px; }
.species-care-group { margin-bottom: 14px; }
.species-care-group > label { display:block; font-size:11px; font-weight:700; color:#888; text-transform:uppercase; letter-spacing:.6px; margin:0 0 7px; }

/* ── pa-combo ── */
.pa-combo { position:relative; }
.pa-combo-trigger {
  display:flex; align-items:center; justify-content:space-between; gap:8px;
  padding:8px 10px; border:1px solid #ddd; border-radius:3px;
  background:#fff; cursor:pointer; font-size:14px; min-height:38px;
  transition:border-color .12s;
}
.pa-combo-trigger:hover { border-color:#999; }
.pa-combo-trigger.open { border-color:#555; }
.pa-combo-val { flex:1; color:#333; user-select:none; }
.pa-combo-val.pa-combo-placeholder { color:#aaa; }
.pa-combo-chev { flex-shrink:0; transition:transform .15s; color:#888; }
.pa-combo-trigger.open .pa-combo-chev { transform:rotate(180deg); }
.pa-combo-drop {
  position:absolute; top:calc(100% + 3px); left:0; right:0;
  background:#fff; border:1px solid #ddd; border-radius:4px;
  box-shadow:0 4px 16px rgba(0,0,0,.12); z-index:900;
  max-height:220px; overflow-y:auto;
}
.pa-combo-new-btn {
  display:flex; align-items:center; gap:6px;
  width:100%; padding:9px 12px;
  background:none; border:none; border-bottom:1px solid #eee;
  font-size:13px; font-weight:600; color:#2a6e3a; cursor:pointer; text-align:left;
}
.pa-combo-new-btn:hover { background:#f0f7f2; }
.pa-combo-option {
  display:block; width:100%; padding:9px 12px;
  background:none; border:none; text-align:left; font-size:13px; cursor:pointer;
}
.pa-combo-option:hover { background:#f5f5f5; }
.pa-combo-option.selected { background:#e8f0e8; font-weight:500; }
/* ── Blueprint/Spezifikation/Lieferzeit Modale ── */
.pa-modal-overlay {
  position:fixed; inset:0; background:rgba(0,0,0,.45); z-index:19999;
  display:flex; align-items:flex-start; justify-content:center;
  padding:30px 16px; overflow-y:auto;
}
.pa-modal-dialog {
  background:#fff; border-radius:6px; padding:24px 28px;
  width:560px; max-width:100%; box-shadow:0 8px 32px rgba(0,0,0,.22);
}
.pa-modal-dialog.pa-modal-narrow { width:380px; }
.pa-modal-dialog h4 { margin:0 0 4px; font-size:16px; font-weight:600; }
.pa-modal-dialog h5 { margin:18px 0 10px; font-size:12px; font-weight:600; text-transform:uppercase; letter-spacing:.04em; color:#888; border-top:1px solid #eee; padding-top:14px; }
.pa-modal-dialog h5:first-of-type { border-top:none; padding-top:0; margin-top:14px; }
.pa-modal-dialog input, .pa-modal-dialog select, .pa-modal-dialog textarea {
  width:100%; box-sizing:border-box; padding:9px 11px;
  border:1px solid #ddd; border-radius:3px; font-size:14px;
}
.pa-modal-dialog .form-row { margin-bottom:12px; }
.pa-modal-dialog-btns { display:flex; gap:10px; justify-content:flex-end; margin-top:18px; }
</style>

<div style="display: flex; justify-content: center; width: 100%;">
<div style="width: 100%; max-width: 900px; padding: 0 20px 60px;">

    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 30px; padding-top: 10px;">
        <div>
            <h1 style="margin: 0 0 6px 0;">Produkt hinzufügen</h1>
            <p style="color: #888; margin: 0; font-size: 14px;">Einzeln oder per CSV/Excel-Massenimport</p>
        </div>
        <a href="<?php echo get_permalink(get_page_by_path('produkt-liste')); ?>" style="color: #666; text-decoration: none; font-size: 14px;">← Produktliste</a>
    </div>

    <div class="tab-bar">
        <button class="tab-btn active" onclick="switchTab('single')">Einzelnes Produkt</button>
        <button class="tab-btn" onclick="switchTab('bulk')">CSV / Excel Import</button>
    </div>

    <!-- TAB: Einzelnes Produkt -->
    <div id="tab-single" class="tab-pane active">
    <div id="form-message"></div>

    <form id="add-product-form">
        <?php wp_nonce_field('add_product_nonce', 'add_product_nonce_field'); ?>

        <!-- Grundinfo -->
        <div class="form-section">
            <h3>Grundinformationen</h3>

            <div id="pflanze-taxonomy-fields">
                <div class="row-2col" style="align-items:end;">
                    <div class="form-row">
                        <label>Gattung *</label>
                        <div style="display:flex;gap:6px;align-items:center;">
                        <div class="pa-combo" id="combo-gattung" style="flex:1;">
                          <div class="pa-combo-trigger" onclick="_hfComboToggle('gattung')">
                            <span class="pa-combo-val pa-combo-placeholder" id="combo-gattung-lbl">z. B. Pelargonium</span>
                            <svg class="pa-combo-chev" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9l6 6 6-6"/></svg>
                          </div>
                          <div class="pa-combo-drop" id="combo-gattung-drop" style="display:none">
                            <button type="button" class="pa-combo-new-btn" onclick="event.stopPropagation();openBlueprintModal('gattung',null)">+ Neu hinzufügen</button>
                            <div class="pa-combo-options" id="combo-gattung-opts"></div>
                          </div>
                          <input type="hidden" id="product-gattung" name="gattung" value="">
                          <input type="hidden" id="product-gattung-bp-id" name="gattung_bp_id" value="">
                        </div>
                        <button type="button" class="btn-secondary" id="combo-gattung-edit-btn" style="display:none;padding:0 9px;" title="Gattung bearbeiten"
                          onclick="openBlueprintModal('gattung', parseInt(document.getElementById('product-gattung-bp-id').value,10))">✏️</button>
                        </div>
                    </div>
                    <div class="form-row">
                        <label>Art</label>
                        <div style="display:flex;gap:6px;align-items:center;">
                        <div class="pa-combo" id="combo-art" style="flex:1;">
                          <div class="pa-combo-trigger" onclick="_hfComboToggle('art')">
                            <span class="pa-combo-val pa-combo-placeholder" id="combo-art-lbl">z. B. zonale</span>
                            <svg class="pa-combo-chev" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9l6 6 6-6"/></svg>
                          </div>
                          <div class="pa-combo-drop" id="combo-art-drop" style="display:none">
                            <button type="button" class="pa-combo-new-btn" onclick="event.stopPropagation();openBlueprintModal('art',null)">+ Neu hinzufügen</button>
                            <div class="pa-combo-options" id="combo-art-opts"></div>
                          </div>
                          <input type="hidden" id="product-art" name="art" value="">
                          <input type="hidden" id="product-art-bp-id" name="art_bp_id" value="">
                        </div>
                        <button type="button" class="btn-secondary" id="combo-art-edit-btn" style="display:none;padding:0 9px;" title="Art bearbeiten"
                          onclick="openBlueprintModal('art', parseInt(document.getElementById('product-art-bp-id').value,10))">✏️</button>
                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <label for="product-kultivar">Kultivar <span style="font-weight:400;font-size:12px;color:#999;">(Sortenname, ohne Anführungszeichen)</span></label>
                    <input type="text" id="product-kultivar" name="kultivar" placeholder="z. B. Thai Constellation">
                </div>
            </div>

            <div class="form-row" id="substrat-name-field" style="display:none;">
                <label for="product-name">Name / Produkttitel *</label>
                <input type="text" id="product-name" name="product_name" placeholder="z. B. Bio-Blumenerde Premium">
            </div>

            <div class="row-2col">
                <div class="form-row">
                    <label for="product-sku">Artikelnummer (Art. Nr.)</label>
                    <input type="text" id="product-sku" name="product_sku" placeholder="z.B. MON-001">
                </div>
                <div class="form-row">
                    <label for="product-price">Preis (€) *</label>
                    <input type="number" id="product-price" name="product_price" step="0.01" min="0" placeholder="0.00" required>
                </div>
            </div>

            <!-- Produkt-Typ Toggle -->
            <div class="toggle-row">
                <label>Produkttyp:</label>
                <span class="toggle-label" id="type-label-left" style="color: #333; font-weight:600;">Pflanze</span>
                <label class="switch">
                    <input type="checkbox" id="product-type-toggle">
                    <span class="slider"></span>
                </label>
                <span class="toggle-label" id="type-label-right">Substrat</span>
            </div>

            <!-- Einheiten Toggle -->
            <div class="toggle-row">
                <label>Einheit:</label>
                <span class="toggle-label" id="unit-label-left" style="color: #333; font-weight:600;">Stück</span>
                <label class="switch">
                    <input type="checkbox" id="unit-toggle">
                    <span class="slider"></span>
                </label>
                <span class="toggle-label" id="unit-label-right">Liter</span>
            </div>

            <!-- Liter-Felder (nur bei Liter) -->
            <div id="liter-fields" style="display:none;">
                <div class="row-2col">
                    <div class="form-row">
                        <label for="product-liters">Inhalt (Liter) *</label>
                        <input type="number" id="product-liters" name="product_liters" step="0.1" min="0" placeholder="z.B. 10">
                    </div>
                    <div class="form-row">
                        <label>Grundpreis (automatisch)</label>
                        <input type="text" id="base-price-display" readonly style="background:#f5f5f5; color:#666;">
                    </div>
                </div>
            </div>
        </div>

        <!-- Steuerklasse & Versandklasse -->
        <div class="form-section">
            <h3>Steuer & Versand</h3>

            <!-- Steuerklasse -->
            <div class="form-row">
                <label>Steuerklasse</label>
                <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
                    <select id="tax-class-select" name="tax_class" style="flex:1; min-width:200px;">
                        <option value="standard">Standard (19%)</option>
                        <?php foreach (WC_Tax::get_tax_classes() as $class): ?>
                            <option value="<?php echo esc_attr(sanitize_title($class)); ?>"><?php echo esc_html($class); ?></option>
                        <?php endforeach; ?>
                        <option value="__new__">+ Neue Steuerklasse hinzufügen...</option>
                    </select>
                </div>
                <div id="new-tax-class-row" style="display:none; margin-top:8px; display:none;">
                    <div style="display: flex; gap: 8px;">
                        <input type="text" id="new-tax-class-name" placeholder="Name der neuen Steuerklasse" style="flex:1; padding:8px 12px; border:1px solid #ddd; border-radius:3px; font-size:14px;">
                        <input type="number" id="new-tax-class-rate" placeholder="%" step="0.01" min="0" max="100" style="width:80px; padding:8px 12px; border:1px solid #ddd; border-radius:3px; font-size:14px;">
                        <button type="button" onclick="addTaxClass()" style="padding:8px 14px; background:#333; color:white; border:none; border-radius:3px; cursor:pointer; font-size:13px;">Hinzufügen</button>
                        <button type="button" onclick="cancelNewTaxClass()" style="padding:8px 14px; background:#f5f5f5; border:1px solid #ddd; border-radius:3px; cursor:pointer; font-size:13px;">Abbrechen</button>
                    </div>
                </div>
            </div>

            <!-- Differenzbesteuerung -->
            <div class="checkbox-row">
                <input type="checkbox" id="differential-taxation" name="differential_taxation">
                <label for="differential-taxation">Differenzbesteuerung</label>
                <div class="tooltip-icon">?
                    <span class="tooltip-text">Differenzbesteuerung gilt für Wiederverkäufer (§ 25a UStG). Dabei wird die Mehrwertsteuer nur auf die Gewinnmarge berechnet, nicht auf den Gesamtverkaufspreis.</span>
                </div>
            </div>

            <!-- Versandklasse -->
            <div class="form-row">
                <label>Versandklasse</label>
                <div style="display: flex; align-items: center; gap: 10px; margin-bottom:8px;">
                    <label class="checkbox-row" style="margin-bottom:0;">
                        <input type="checkbox" id="no-shipping-class" name="no_shipping_class">
                        <span style="font-size:13px; color:#666;">Keine Versandklasse</span>
                    </label>
                </div>
                <select id="shipping-class-select" name="shipping_class" onchange="onShippingClassChange(this)">
                    <option value="">Standard (keine)</option>
                    <?php foreach ($shipping_classes as $term_id => $name): ?>
                        <option value="<?php echo esc_attr($term_id); ?>"><?php echo esc_html($name); ?></option>
                    <?php endforeach; ?>
                    <option value="__new__">+ Neue Versandklasse hinzufügen...</option>
                </select>

                <!-- Neue Versandklasse anlegen -->
                <div id="new-shipping-class-row" style="display:none; margin-top:10px;">
                    <div style="display:flex; gap:8px; flex-wrap:wrap;">
                        <input type="text" id="new-sc-name" placeholder="Name der Versandklasse" style="flex:1; min-width:160px; padding:8px 12px; border:1px solid #ddd; border-radius:3px; font-size:14px;">
                        <input type="text" id="new-sc-description" placeholder="Beschreibung (optional)" style="flex:2; min-width:160px; padding:8px 12px; border:1px solid #ddd; border-radius:3px; font-size:14px;">
                        <button type="button" onclick="addShippingClass()" style="padding:8px 14px; background:#333; color:white; border:none; border-radius:3px; cursor:pointer; font-size:13px;">Hinzufügen</button>
                        <button type="button" onclick="cancelNewShippingClass()" style="padding:8px 14px; background:#f5f5f5; border:1px solid #ddd; border-radius:3px; cursor:pointer; font-size:13px;">Abbrechen</button>
                    </div>
                </div>

                <!-- Parameter der gewählten Versandklasse -->
                <div id="shipping-class-params" style="display:none; margin-top:12px; padding:14px; background:#f8f9fa; border:1px solid #e8e8e8; border-radius:4px;">
                    <p style="margin:0 0 10px; font-size:12px; font-weight:600; color:#888; text-transform:uppercase; letter-spacing:.5px;">Parameter</p>
                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px;">
                        <div>
                            <label style="font-size:12px; color:#666; display:block; margin-bottom:4px;">Name</label>
                            <input type="text" id="sc-param-name" style="width:100%; padding:7px 10px; border:1px solid #ddd; border-radius:3px; font-size:13px; box-sizing:border-box;">
                        </div>
                        <div>
                            <label style="font-size:12px; color:#666; display:block; margin-bottom:4px;">Slug</label>
                            <input type="text" id="sc-param-slug" style="width:100%; padding:7px 10px; border:1px solid #ddd; border-radius:3px; font-size:13px; box-sizing:border-box; background:#f0f0f0;" readonly>
                        </div>
                    </div>
                    <div style="margin-top:10px;">
                        <label style="font-size:12px; color:#666; display:block; margin-bottom:4px;">Beschreibung</label>
                        <input type="text" id="sc-param-description" style="width:100%; padding:7px 10px; border:1px solid #ddd; border-radius:3px; font-size:13px; box-sizing:border-box;">
                    </div>
                    <div id="sc-flat-rate-costs" style="margin-top:10px;"></div>
                    <div style="margin-top:10px; display:flex; gap:8px; align-items:center;">
                        <button type="button" id="sc-save-btn" onclick="saveShippingClassParams()" style="padding:6px 14px; background:#333; color:white; border:none; border-radius:3px; cursor:pointer; font-size:13px;">Speichern</button>
                        <span id="sc-save-msg" style="font-size:12px; color:#1e7e34; display:none;">✓ Gespeichert</span>
                    </div>
                </div>
            </div>

            <!-- Lieferzeit -->
            <div class="form-row">
                <label for="delivery-time-select">Lieferzeit</label>
                <div style="display:flex;gap:6px;max-width:320px;">
                    <select id="delivery-time-select" name="delivery_time" style="flex:1;" onchange="_bpMarkManual('delivery_time')">
                        <option value="">— nicht angegeben —</option>
                    </select>
                    <button type="button" class="btn-secondary" onclick="openDeliveryTimeModal()">+ Neue</button>
                </div>
            </div>

            <!-- Nicht retournierbar (disabled) -->
            <div class="checkbox-row">
                <input type="checkbox" id="not-returnable" name="not_returnable" disabled>
                <label for="not-returnable" style="color:#aaa;">Nicht retournierbar</label>
                <span style="font-size:12px; color:#bbb;">(nicht aktivierbar)</span>
            </div>
        </div>

        <!-- Lager -->
        <div class="form-section">
            <h3>Lagerverwaltung</h3>
            <!-- Lagerverwaltung ist immer aktiv, Lieferrückstand immer NEIN – beides ausgeblendet -->
            <input type="hidden" name="manage_stock" value="1">
            <input type="hidden" name="backorders" value="no">

            <div class="form-row">
                <label for="product-stock">Lagerbestand (Stück)</label>
                <input type="number" id="product-stock" name="product_stock" value="0" min="0" style="max-width:160px;">
            </div>

            <!-- Schwellwert geringer Lagerbestand -->
            <div class="checkbox-row" style="margin-bottom:8px;">
                <input type="checkbox" id="custom-low-stock" name="custom_low_stock" onchange="toggleLowStock(this)">
                <label for="custom-low-stock">Individueller Schwellwert für geringen Lagerbestand</label>
            </div>
            <div id="low-stock-threshold-row" style="display:none; margin-left:26px; margin-bottom:14px;">
                <input type="number" id="low-stock-threshold" name="low_stock_threshold" value="5" min="1" style="max-width:100px; padding:8px 12px; border:1px solid #ddd; border-radius:3px; font-size:14px;">
                <span style="font-size:13px; color:#666; margin-left:8px;">Stück</span>
            </div>
            <div id="low-stock-default-note" style="margin-left:0; margin-bottom:14px; font-size:13px; color:#888;">
                Standard-Schwellwert: 5 Stück
            </div>
            <div class="checkbox-row">
                <input type="checkbox" id="never-low-stock" name="never_low_stock">
                <label for="never-low-stock">Nie als geringer Lagerbestand markieren</label>
            </div>
        </div>

        <!-- Spezifikation -->
        <div class="form-section">
            <h3>Spezifikation</h3>
            <p style="font-size:13px; color:#666; margin:0 0 14px;">Wiederverwendbare Topfgröße/Gewicht/Maße-Voreinstellung — füllt Gewicht &amp; Maße unten aus, danach weiter frei änderbar.</p>
            <div class="form-row">
                <label for="specification-select">Produktspezifikation</label>
                <div style="display:flex;gap:6px;max-width:420px;">
                    <select id="specification-select" name="specification_select" style="flex:1;" onchange="_specSelected(this.value)">
                        <option value="">— keine Vorgabe —</option>
                    </select>
                    <button type="button" class="btn-secondary" onclick="openSpecificationModal()">+ Neue</button>
                </div>
            </div>
        </div>

        <!-- Maße & Gewicht -->
        <div class="form-section">
            <h3>Maße & Gewicht</h3>
            <div class="row-2col">
                <div class="form-row">
                    <label for="product-weight">Gewicht (kg)</label>
                    <input type="number" id="product-weight" name="product_weight" step="0.001" min="0" placeholder="0.000">
                </div>
            </div>

            <p style="margin:0 0 10px; font-size:13px; font-weight:500; color:#555;">Produktmaße (cm)</p>
            <div class="row-3col">
                <div class="form-row">
                    <label for="product-length">Länge</label>
                    <input type="number" id="product-length" name="product_length" step="0.1" min="0" placeholder="0" oninput="syncShippingDims()">
                </div>
                <div class="form-row">
                    <label for="product-width">Breite</label>
                    <input type="number" id="product-width" name="product_width" step="0.1" min="0" placeholder="0" oninput="syncShippingDims()">
                </div>
                <div class="form-row">
                    <label for="product-height">Höhe</label>
                    <input type="number" id="product-height" name="product_height" step="0.1" min="0" placeholder="0" oninput="syncShippingDims()">
                </div>
            </div>

            <!-- Versandmaße -->
            <div style="margin-top:8px;">
                <div class="checkbox-row" style="margin-bottom:10px;">
                    <input type="checkbox" id="shipping-dims-same" checked onchange="toggleShippingDims(this)">
                    <label for="shipping-dims-same">Versandmaße gleich wie Produktmaße</label>
                </div>
                <div id="shipping-dims-fields" style="display:none;">
                    <p style="margin:0 0 10px; font-size:13px; font-weight:500; color:#555;">Versandmaße (cm)</p>
                    <div class="row-3col">
                        <div class="form-row">
                            <label for="shipping-length">Länge</label>
                            <input type="number" id="shipping-length" name="shipping_length" step="0.1" min="0" placeholder="0">
                        </div>
                        <div class="form-row">
                            <label for="shipping-width">Breite</label>
                            <input type="number" id="shipping-width" name="shipping_width" step="0.1" min="0" placeholder="0">
                        </div>
                        <div class="form-row">
                            <label for="shipping-height">Höhe</label>
                            <input type="number" id="shipping-height" name="shipping_height" step="0.1" min="0" placeholder="0">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bilder -->
        <div class="form-section">
            <h3>Produktbilder</h3>
            <input type="hidden" id="featured-image-id" name="featured_image_id" value="">
            <input type="hidden" id="gallery-image-ids" name="gallery_image_ids" value="">

            <div class="row-2col" style="align-items: start;">
                <div>
                    <p style="margin:0 0 8px; font-size:13px; font-weight:500; color:#555;">Titelbild</p>
                    <div class="image-upload-area" id="featured-image-area" onclick="openMediaPicker('featured')">
                        <div id="featured-placeholder">
                            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#ccc" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                            <span style="font-size:13px; color:#aaa;">Bild auswählen</span>
                        </div>
                        <div id="featured-preview" style="display:none; width:100%;">
                            <img id="featured-img-preview" src="" alt="" style="max-height:180px; max-width:100%; object-fit:contain; border-radius:3px;">
                            <button type="button" onclick="event.stopPropagation(); removeFeatured()" style="display:block; margin:8px auto 0; padding:4px 10px; background:#e00; color:white; border:none; border-radius:3px; cursor:pointer; font-size:12px;">Entfernen</button>
                        </div>
                    </div>
                </div>
                <div>
                    <p style="margin:0 0 8px; font-size:13px; font-weight:500; color:#555;">Weitere Bilder</p>
                    <div class="image-upload-area" id="gallery-upload-area" onclick="openMediaPicker('gallery')" style="min-height:60px;">
                        <span style="font-size:13px; color:#aaa;">+ Bilder hinzufügen</span>
                    </div>
                    <div class="image-preview-grid" id="gallery-preview"></div>
                </div>
            </div>
        </div>

        <!-- Kurzbeschreibung -->
        <div class="form-section">
            <h3>Kurzbeschreibung <span style="font-weight:400;font-size:13px;color:#999;">(1–2 Sätze, erscheint neben dem Produktbild)</span></h3>
            <textarea id="product-short-description" name="product_short_description"
                      rows="3" maxlength="500"
                      placeholder="Prägnante Beschreibung für die Produktseite…"
                      style="width:100%;box-sizing:border-box;padding:10px 12px;border:1px solid #ddd;border-radius:3px;font-size:14px;line-height:1.5;resize:vertical;font-family:inherit;"></textarea>
            <div style="text-align:right;font-size:11px;margin-top:4px;">
                <span id="short-desc-count" style="font-weight:500;">0</span>
                <span id="short-desc-limit-label" style="color:#bbb;"> / 160 Zeichen empfohlen</span>
            </div>
        </div>

        <!-- Beschreibung -->
        <div class="form-section">
            <h3>Produktbeschreibung</h3>
            <div class="rte-toolbar">
                <button type="button" class="rte-btn" onclick="rteCmd('bold')" title="Fett"><b>B</b></button>
                <button type="button" class="rte-btn" onclick="rteCmd('italic')" title="Kursiv"><i>I</i></button>
                <button type="button" class="rte-btn" onclick="rteCmd('underline')" title="Unterstrichen"><u>U</u></button>
                <button type="button" class="rte-btn" onclick="rteCmd('strikeThrough')" title="Durchgestrichen"><s>S</s></button>
                <span style="width:1px; background:#ddd; margin:2px 4px;"></span>
                <button type="button" class="rte-btn" onclick="rteCmd('insertUnorderedList')" title="Liste">&#8226; Liste</button>
                <button type="button" class="rte-btn" onclick="rteCmd('insertOrderedList')" title="Nummeriert">1. Liste</button>
                <span style="width:1px; background:#ddd; margin:2px 4px;"></span>
                <button type="button" class="rte-btn" onclick="rteHeading('h2')" title="Überschrift">H2</button>
                <button type="button" class="rte-btn" onclick="rteHeading('h3')" title="Unter-Überschrift">H3</button>
                <span style="width:1px; background:#ddd; margin:2px 4px;"></span>
                <button type="button" class="rte-btn" onclick="rteCmd('justifyLeft')" title="Links">⬅</button>
                <button type="button" class="rte-btn" onclick="rteCmd('justifyCenter')" title="Zentriert">↔</button>
                <button type="button" class="rte-btn" onclick="rteCmd('justifyRight')" title="Rechts">➡</button>
                <button type="button" class="rte-btn" onclick="rteLink()" title="Link">🔗</button>
            </div>
            <div id="product-description" contenteditable="true" style="min-height:160px; padding:12px; border:1px solid #ddd; border-radius:0 0 3px 3px; font-size:14px; outline:none; line-height:1.5;"></div>
        </div>

        <!-- Tags & Kategorien -->
        <div class="form-section">
            <h3>Tags & Kategorien</h3>

            <!-- Selected pills -->
            <div id="selected-tags-area" style="min-height:38px; padding:8px 10px; background:#fafafa; border:1px solid #e8e8e8; border-radius:3px; margin-bottom:14px; display:flex; flex-wrap:wrap; gap:6px; align-items:center;">
                <span id="no-tags-label" style="font-size:12px; color:#bbb; font-style:italic;">Keine Tags ausgewählt</span>
            </div>

            <!-- Tag Pool -->
            <div id="tag-pool-wrap" style="margin-bottom:14px;">
                <p style="color:#aaa; font-size:13px;">Lade Tag Pool...</p>
            </div>

            <button type="button" class="btn-secondary" onclick="openTagCreatePopup()" style="font-size:13px; padding:7px 14px;">+ Tag erstellen</button>

            <!-- Hidden fields -->
            <input type="hidden" id="h-fixed-tags"   name="product_tags_fixed"   value="">
            <input type="hidden" id="h-categories"    name="product_categories"   value="">
            <input type="hidden" id="h-variable-tags" name="product_variable_tags" value="[]">
            <input type="hidden" id="h-specification-id" name="specification_id" value="">
        </div>

        <!-- Pflege-Infos -->
<div class="form-section" id="care-section">
<h3>Pflege-Infos</h3>
<p style="font-size:13px; color:#666; margin:0 0 14px;">Pflegehinweise für die Produktdetailseite.</p>

<div class="species-care-group">
<label for="care-light">Licht (bevorzugt)</label>
<select id="care-light" name="care_light">
<option value="">— nicht angegeben —</option>
<option value="vollsonne">Vollsonne</option>
<option value="sonnig">Sonnig</option>
<option value="halbschatten">Halbschatten</option>
<option value="schatten">Schatten</option>
</select>
</div>

<div class="species-care-group">
<label for="care-light-min">Licht verträgt (von)</label>
<select id="care-light-min" name="care_light_tolerates_min">
<option value="">— wie bevorzugt —</option>
<option value="vollsonne">Vollsonne</option>
<option value="sonnig">Sonnig</option>
<option value="halbschatten">Halbschatten</option>
<option value="schatten">Schatten</option>
</select>
</div>

<div class="species-care-group">
<label for="care-light-max">Licht verträgt (bis)</label>
<select id="care-light-max" name="care_light_tolerates_max">
<option value="">— wie bevorzugt —</option>
<option value="vollsonne">Vollsonne</option>
<option value="sonnig">Sonnig</option>
<option value="halbschatten">Halbschatten</option>
<option value="schatten">Schatten</option>
</select>
</div>

<div class="species-care-group">
<label for="care-water">Wasser (bevorzugt)</label>
<select id="care-water" name="care_water">
<option value="">— nicht angegeben —</option>
<option value="viel">Viel (feucht halten)</option>
<option value="maessig">Mäßig</option>
<option value="wenig">Wenig (trocken halten)</option>
</select>
</div>

<div class="species-care-group">
<label for="care-water-min">Wasser verträgt (von)</label>
<select id="care-water-min" name="care_water_tolerates_min">
<option value="">— wie bevorzugt —</option>
<option value="viel">Viel (feucht halten)</option>
<option value="maessig">Mäßig</option>
<option value="wenig">Wenig (trocken halten)</option>
</select>
</div>

<div class="species-care-group">
<label for="care-water-max">Wasser verträgt (bis)</label>
<select id="care-water-max" name="care_water_tolerates_max">
<option value="">— wie bevorzugt —</option>
<option value="viel">Viel (feucht halten)</option>
<option value="maessig">Mäßig</option>
<option value="wenig">Wenig (trocken halten)</option>
</select>
</div>

<div class="species-care-group">
<label for="care-winter">Überwinterung (Freitext)</label>
<input type="text" id="care-winter" name="care_winter"
       placeholder="z. B. Frostfrei (>10 °C), Frostfest …"
       style="width:100%;box-sizing:border-box;padding:8px 10px;border:1px solid #ddd;border-radius:3px;font-size:14px;font-family:inherit;">
</div>

<div class="species-care-group">
<label for="care-winterhaerte">Winterhärte</label>
<select id="care-winterhaerte" name="care_winterhaerte">
<option value="">— nicht angegeben —</option>
<option value="nicht-wh">Nicht winterhart</option>
<option value="bedingt-wh">Bedingt winterhart (bis ca. −5 °C)</option>
<option value="winterhart">Winterhart (bis ca. −10 °C)</option>
<option value="sehr-wh">Sehr winterhart (bis ca. −15 °C)</option>
<option value="voll-wh">Vollwinterhart (bis −20 °C und kälter)</option>
</select>
</div>

<div class="row-2col">
<div class="species-care-group">
<label for="care-temp-min">Temperatur min (°C)</label>
<input type="number" id="care-temp-min" name="care_temp_min" step="1" placeholder="z.B. 5">
</div>
<div class="species-care-group">
<label for="care-temp-max">Temperatur max (°C)</label>
<input type="number" id="care-temp-max" name="care_temp_max" step="1" placeholder="z.B. 30">
</div>
</div>
</div>

<!-- Varianten (erscheint wenn Varianten-Tags gewählt) -->
        <div class="form-section" id="variants-section" style="display:none;">
            <h3>Produktvarianten</h3>
            <p style="font-size:13px; color:#666; margin:0 0 14px;">Definiere Preis und Lagerbestand pro Varianten-Kombination.</p>
            <div id="variants-table"></div>
            <button type="button" class="btn-secondary" onclick="addVariantRow()" style="margin-top:6px; font-size:13px; padding:7px 14px;">+ Variante hinzufügen</button>
            <input type="hidden" id="h-variants" name="product_variants" value="[]">
        </div>

        <div class="submit-row">
            <button type="submit" class="btn-primary" id="submit-btn">Produkt speichern</button>
            <button type="button" class="btn-secondary" onclick="resetForm()">Formular leeren</button>
            <span id="submit-spinner" style="display:none; color:#888; font-size:13px;">Speichert...</span>
        </div>
    </form>
    </div><!-- /tab-single -->

    <!-- TAB: Bulk Import -->
    <div id="tab-bulk" class="tab-pane">
        <div class="form-section">
            <h3>CSV / Excel Massenimport</h3>
            <p style="font-size:14px; color:#666; margin-bottom:20px;">
                Importiere mehrere Produkte auf einmal. Unterstützte Formate: <strong>.csv</strong>, <strong>.xlsx</strong>, <strong>.xls</strong>
            </p>

            <!-- Template Download -->
            <div style="margin-bottom:20px; padding:14px 16px; background:#f8f9fa; border:1px solid #e8e8e8; border-radius:4px; display:flex; align-items:center; justify-content:space-between;">
                <span style="font-size:14px;">Vorlage herunterladen (XLSX) — alle Felder, Zeile 2 = Beschreibungen, ab Zeile 3 = Daten</span>
                <button type="button" onclick="downloadTemplate()" style="padding:8px 16px; background:#333; color:white; border:none; border-radius:3px; cursor:pointer; font-size:13px;">Download Vorlage</button>
            </div>

            <!-- Upload Area -->
            <div class="bulk-upload-area" id="bulk-drop-area" onclick="document.getElementById('bulk-file-input').click()"
                 ondragover="event.preventDefault(); this.style.borderColor='#999';"
                 ondragleave="this.style.borderColor='#ddd';"
                 ondrop="handleBulkDrop(event)">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#ccc" stroke-width="1.5" style="margin-bottom:8px;"><polyline points="16 16 12 12 8 16"/><line x1="12" y1="12" x2="12" y2="21"/><path d="M20.39 18.39A5 5 0 0 0 18 9h-1.26A8 8 0 1 0 3 16.3"/></svg>
                <p style="margin:0; font-size:14px; color:#aaa;">Datei hier ablegen oder klicken</p>
                <p style="margin:4px 0 0; font-size:12px; color:#bbb;">.csv, .xlsx, .xls</p>
            </div>
            <input type="file" id="bulk-file-input" accept=".csv,.xlsx,.xls" style="display:none;" onchange="handleBulkFile(this.files[0])">

            <!-- Preview -->
            <div id="bulk-preview" style="display:none; margin-top:20px;">
                <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:12px;">
                    <div>
                        <strong id="bulk-file-name" style="font-size:14px;"></strong>
                        <span id="bulk-row-count" style="font-size:13px; color:#888; margin-left:8px;"></span>
                    </div>
                    <div style="display:flex; gap:8px;">
                        <button type="button" onclick="clearBulkFile()" class="btn-secondary" style="font-size:13px; padding:8px 14px;">Datei entfernen</button>
                        <button type="button" id="bulk-next-btn" onclick="openImageAssignModal()" class="btn-primary" style="font-size:13px; padding:8px 18px;">Weiter: Bilder zuweisen →</button>
                    </div>
                </div>
                <div style="overflow-x:auto; border:1px solid #e8e8e8; border-radius:4px;">
                    <table id="bulk-preview-table" style="width:100%; border-collapse:collapse; font-size:13px;">
                        <thead id="bulk-preview-head" style="background:#f5f5f5;"></thead>
                        <tbody id="bulk-preview-body"></tbody>
                    </table>
                </div>
            </div>

            <!-- Import Progress -->
            <div id="bulk-progress" style="display:none; margin-top:20px;">
                <p id="bulk-progress-text" style="font-size:14px; margin-bottom:8px;"></p>
                <div style="background:#e8e8e8; border-radius:4px; height:8px; overflow:hidden;">
                    <div id="bulk-progress-bar" style="background:#333; height:100%; width:0%; transition:width .3s;"></div>
                </div>
                <div id="bulk-results" style="margin-top:16px;"></div>
            </div>
        </div>
    </div><!-- /tab-bulk -->

</div>

<!-- Image Assignment Modal -->
<div id="ia-modal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.75);z-index:99999;align-items:flex-start;justify-content:center;overflow-y:auto;padding:40px 0;">
  <div style="background:#fff;border-radius:6px;width:min(1000px,96vw);padding:28px 24px;margin:auto;">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
      <h3 style="margin:0;font-size:17px;">Bilder zuweisen</h3>
      <button type="button" onclick="closeIaModal()" style="background:none;border:none;font-size:22px;cursor:pointer;color:#888;line-height:1;">✕</button>
    </div>
    <p style="margin:0 0 16px;font-size:13px;color:#888;">Weise jedem Produkt ein Hauptbild zu. Zweitbilder sind optional.</p>
    <div style="overflow-x:auto;max-height:55vh;overflow-y:auto;border:1px solid #e8e8e8;border-radius:4px;">
      <table style="width:100%;border-collapse:collapse;font-size:13px;">
        <thead><tr style="background:#f5f5f5;position:sticky;top:0;">
          <th style="padding:8px 12px;text-align:left;font-size:12px;color:#666;border-bottom:1px solid #e8e8e8;white-space:nowrap;">Gattung</th>
          <th style="padding:8px 12px;text-align:left;font-size:12px;color:#666;border-bottom:1px solid #e8e8e8;white-space:nowrap;">Art</th>
          <th style="padding:8px 12px;text-align:left;font-size:12px;color:#666;border-bottom:1px solid #e8e8e8;white-space:nowrap;">Kultivar</th>
          <th style="padding:8px 12px;text-align:left;font-size:12px;color:#666;border-bottom:1px solid #e8e8e8;white-space:nowrap;">SKU</th>
          <th style="padding:8px 12px;text-align:left;font-size:12px;color:#666;border-bottom:1px solid #e8e8e8;white-space:nowrap;">Hauptbild *</th>
          <th style="padding:8px 12px;text-align:left;font-size:12px;color:#666;border-bottom:1px solid #e8e8e8;white-space:nowrap;">Zweitbilder</th>
        </tr></thead>
        <tbody id="ia-tbody"></tbody>
      </table>
    </div>
    <div style="margin-top:14px;display:flex;align-items:center;gap:10px;">
      <label style="display:flex;align-items:center;gap:6px;font-size:13px;cursor:pointer;">
        <input type="checkbox" id="ia-skip-main" onchange="updateIaImportBtn()">
        Ohne Hauptbild importieren
      </label>
    </div>
    <div style="margin-top:16px;display:flex;gap:10px;align-items:center;">
      <button type="button" id="ia-import-btn" onclick="startBulkImport()" disabled class="btn-primary" style="font-size:13px;padding:10px 22px;">Import starten</button>
      <button type="button" onclick="closeIaModal()" style="padding:10px 16px;background:#e8e8e8;color:#333;border:none;border-radius:3px;cursor:pointer;font-size:13px;">Abbrechen</button>
      <span id="ia-import-status" style="font-size:13px;color:#888;"></span>
    </div>
  </div>
</div>
<input type="file" id="ia-file-input" accept="image/*" style="display:none;" onchange="iaHandleFile(this)">
</div>

<!-- Blueprint (Gattung/Art) erstellen/bearbeiten -->
<div id="pa-bp-overlay" class="pa-modal-overlay" style="display:none" onclick="if(event.target===this)closeBlueprintModal()">
  <div class="pa-modal-dialog">
    <h4 id="pa-bp-title">Neue Gattung</h4>
    <div class="form-row">
      <label for="bp-name">Name *</label>
      <input type="text" id="bp-name" placeholder="z. B. Pelargonium">
    </div>

    <h5>Einheit &amp; Typ</h5>
    <div class="row-2col">
      <div class="form-row">
        <label for="bp-unit-type">Einheit</label>
        <select id="bp-unit-type"><option value="">— keine Vorgabe —</option><option value="piece">Stück</option><option value="liter">Liter</option></select>
      </div>
      <div class="form-row">
        <label for="bp-product-type">Produkttyp</label>
        <select id="bp-product-type"><option value="">— keine Vorgabe —</option><option value="plant">🌿 Pflanze</option><option value="substrate">🪨 Substrat</option></select>
      </div>
    </div>

    <h5>Steuer &amp; Versand</h5>
    <div class="row-2col">
      <div class="form-row">
        <label for="bp-tax-class">Steuerklasse</label>
        <select id="bp-tax-class"><option value="">— keine Vorgabe —</option></select>
      </div>
      <div class="form-row">
        <label for="bp-delivery-time">Lieferzeit</label>
        <select id="bp-delivery-time"><option value="">— keine Vorgabe —</option></select>
      </div>
    </div>
    <div class="checkbox-row">
      <input type="checkbox" id="bp-differential-taxation">
      <label for="bp-differential-taxation">Differenzbesteuerung §25a UStG</label>
    </div>

    <h5>Lager</h5>
    <div class="row-2col">
      <div class="form-row">
        <label for="bp-stock">Lagerbestand</label>
        <input type="number" id="bp-stock" min="0" placeholder="— keine Vorgabe —">
      </div>
      <div class="form-row">
        <label for="bp-low-stock-threshold">Niedrig-Bestand Schwellwert</label>
        <input type="number" id="bp-low-stock-threshold" min="0" placeholder="— keine Vorgabe —">
      </div>
    </div>
    <div class="checkbox-row">
      <input type="checkbox" id="bp-never-low-stock">
      <label for="bp-never-low-stock">Nie als „Niedriger Bestand" markieren</label>
    </div>

    <h5>Spezifikation</h5>
    <div class="form-row">
      <div style="display:flex;gap:6px;">
        <select id="bp-specification" style="flex:1;"><option value="">— keine Vorgabe —</option></select>
        <button type="button" class="btn-secondary" onclick="openSpecificationModal()">+ Neue</button>
      </div>
    </div>

    <h5>Kurzbeschreibung &amp; Beschreibung</h5>
    <div class="form-row">
      <textarea id="bp-short-description" rows="2" maxlength="500" placeholder="Kurze Produktbeschreibung für Listings..."></textarea>
    </div>
    <div class="form-row">
      <textarea id="bp-description" rows="4" placeholder="Ausführliche Produktbeschreibung..."></textarea>
    </div>

    <h5>Pflegehinweise</h5>
    <div class="row-2col">
      <div class="form-row"><label>Licht (Bevorzugt)</label><select id="bp-care-light"></select></div>
      <div class="form-row"><label>Wasser (Bevorzugt)</label><select id="bp-care-water"></select></div>
    </div>
    <div class="row-2col">
      <div class="form-row"><label>Licht verträgt (von)</label><select id="bp-care-light-min"></select></div>
      <div class="form-row"><label>Licht verträgt (bis)</label><select id="bp-care-light-max"></select></div>
    </div>
    <div class="row-2col">
      <div class="form-row"><label>Wasser verträgt (von)</label><select id="bp-care-water-min"></select></div>
      <div class="form-row"><label>Wasser verträgt (bis)</label><select id="bp-care-water-max"></select></div>
    </div>
    <div class="row-2col">
      <div class="form-row"><label>Winterhärte</label><select id="bp-care-winterhaerte"></select></div>
      <div class="form-row"><label>Überwinterung</label><input type="text" id="bp-care-winter" placeholder="z. B. hell und kühl, min. 5 °C"></div>
    </div>
    <div class="row-2col">
      <div class="form-row"><label>Temp. min. (°C)</label><input type="number" id="bp-care-temp-min" placeholder="—"></div>
      <div class="form-row"><label>Temp. max. (°C)</label><input type="number" id="bp-care-temp-max" placeholder="—"></div>
    </div>

    <div class="pa-modal-dialog-btns">
      <button type="button" class="btn-secondary" onclick="closeBlueprintModal()">Abbrechen</button>
      <button type="button" class="btn-primary" onclick="saveBlueprintModal()">Speichern</button>
    </div>
  </div>
</div>

<!-- Spezifikation erstellen -->
<div id="pa-spec-overlay" class="pa-modal-overlay" style="display:none" onclick="if(event.target===this)closeSpecificationModal()">
  <div class="pa-modal-dialog pa-modal-narrow">
    <h4>Neue Spezifikation</h4>
    <p style="font-size:12px;color:#888;margin:0 0 14px;">Topfgröße, Form, Gewicht und Maße</p>
    <div class="form-row">
      <label for="spec-pot-size">Topfgröße (cm) *</label>
      <input type="number" id="spec-pot-size" step="0.5" min="0" placeholder="z. B. 12">
    </div>
    <div class="form-row">
      <label>Topfform *</label>
      <select id="spec-shape"><option value="round">⭘ Rund</option><option value="square">⬜ Eckig</option></select>
    </div>
    <div class="form-row">
      <label for="spec-weight">Gewicht *</label>
      <div style="display:flex;gap:6px;">
        <input type="number" id="spec-weight" step="0.1" min="0" placeholder="z. B. 250" style="flex:1;">
        <select id="spec-weight-unit" style="width:70px;"><option value="g">g</option><option value="kg">kg</option></select>
      </div>
    </div>
    <div class="form-row">
      <label>Maße (Höhe × Breite, cm)</label>
      <div style="display:flex;gap:6px;align-items:center;">
        <input type="number" id="spec-height" step="0.5" min="0" placeholder="Höhe">
        <span>×</span>
        <input type="number" id="spec-width" step="0.5" min="0" placeholder="Breite">
      </div>
    </div>
    <div class="pa-modal-dialog-btns">
      <button type="button" class="btn-secondary" onclick="closeSpecificationModal()">Abbrechen</button>
      <button type="button" class="btn-primary" onclick="saveSpecificationModal()">Speichern</button>
    </div>
  </div>
</div>

<!-- Lieferzeit erstellen -->
<div id="pa-dt-overlay" class="pa-modal-overlay" style="display:none" onclick="if(event.target===this)closeDeliveryTimeModal()">
  <div class="pa-modal-dialog pa-modal-narrow">
    <h4>Neue Lieferzeit</h4>
    <div class="form-row">
      <label for="dt-label">Bezeichnung *</label>
      <input type="text" id="dt-label" placeholder="z. B. 3–5 Werktage">
    </div>
    <div class="row-2col">
      <div class="form-row"><label for="dt-days-min">Tage (von)</label><input type="number" id="dt-days-min" min="0"></div>
      <div class="form-row"><label for="dt-days-max">Tage (bis)</label><input type="number" id="dt-days-max" min="0"></div>
    </div>
    <div class="pa-modal-dialog-btns">
      <button type="button" class="btn-secondary" onclick="closeDeliveryTimeModal()">Abbrechen</button>
      <button type="button" class="btn-primary" onclick="saveDeliveryTimeModal()">Speichern</button>
    </div>
  </div>
</div>

<div id="tag-create-overlay" onclick="if(event.target===this)closeTagCreatePopup()">
    <div id="tag-create-dialog">
        <h3>Neuen Tag erstellen</h3>
        <div class="form-row">
            <label for="tc-name" style="font-size:14px; font-weight:500;">Name *</label>
            <input type="text" id="tc-name" placeholder="z.B. Neu, Farbe, Pelargonium...">
        </div>
        <div class="form-row">
            <label for="tc-type" style="font-size:14px; font-weight:500;">Typ</label>
            <select id="tc-type" onchange="onTagTypeChange(this)" style="padding:10px 12px; font-size:14px; border:1px solid #ddd; border-radius:3px; width:100%; box-sizing:border-box;">
                <option value="fixed">Fester Tag (z.B. Neu, Raritaet)</option>
                <option value="variable_type">Variabler Tag-Typ (z.B. Farbe, Topfgröße)</option>
                <option value="category">Kategorie (Pflanzengattung)</option>
            </select>
        </div>
        <div id="tc-variation-row" class="checkbox-row" style="display:none; margin-top:2px;">
            <input type="checkbox" id="tc-is-variation">
            <label for="tc-is-variation" style="font-size:13px;">Ist Produktvariation (Dimension für WooCommerce-Varianten)</label>
        </div>
        <div style="display:flex; gap:10px; margin-top:20px;">
            <button type="button" class="btn-primary" onclick="createTag()" style="font-size:14px; padding:10px 20px;">Erstellen</button>
            <button type="button" class="btn-secondary" onclick="closeTagCreatePopup()">Abbrechen</button>
        </div>
        <div id="tc-msg" style="margin-top:10px; font-size:13px; display:none;"></div>
    </div>
</div>

<script>
const ajaxUrl = '<?php echo admin_url('admin-ajax.php'); ?>';
const addProductNonce = '<?php echo wp_create_nonce('add_product_nonce'); ?>';

// ── Tabs ──────────────────────────────────────────────────────────────────────
function switchTab(tab) {
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    document.querySelectorAll('.tab-pane').forEach(p => p.classList.remove('active'));
    document.getElementById('tab-' + tab).classList.add('active');
    event.target.classList.add('active');
}

// ── Produkt-Typ Toggle ────────────────────────────────────────────────────────
document.getElementById('product-type-toggle').addEventListener('change', function() {
    const l = document.getElementById('type-label-left');
    const r = document.getElementById('type-label-right');
    l.style.fontWeight = this.checked ? '400' : '600';
    l.style.color      = this.checked ? '#999' : '#333';
    r.style.fontWeight = this.checked ? '600' : '400';
    r.style.color      = this.checked ? '#333' : '#999';
    document.getElementById('pflanze-taxonomy-fields').style.display = this.checked ? 'none' : '';
    document.getElementById('substrat-name-field').style.display     = this.checked ? '' : 'none';
    recalcBasePrice();
});

// ── Einheiten Toggle ──────────────────────────────────────────────────────────
document.getElementById('unit-toggle').addEventListener('change', function() {
    const l = document.getElementById('unit-label-left');
    const r = document.getElementById('unit-label-right');
    l.style.fontWeight = this.checked ? '400' : '600';
    l.style.color      = this.checked ? '#999' : '#333';
    r.style.fontWeight = this.checked ? '600' : '400';
    r.style.color      = this.checked ? '#333' : '#999';
    document.getElementById('liter-fields').style.display = this.checked ? 'block' : 'none';
    recalcBasePrice();
});

document.getElementById('product-price').addEventListener('input', recalcBasePrice);
document.getElementById('product-liters').addEventListener('input', recalcBasePrice);

function recalcBasePrice() {
    const isLiter  = document.getElementById('unit-toggle').checked;
    const isSubstrat = document.getElementById('product-type-toggle').checked;
    if (!isLiter) return;
    const price  = parseFloat(document.getElementById('product-price').value) || 0;
    const liters = parseFloat(document.getElementById('product-liters').value) || 0;
    if (liters <= 0 || price <= 0) {
        document.getElementById('base-price-display').value = '';
        return;
    }
    const perLiter = price / liters;
    // Erde: Grundpreis per 10L; alle anderen: per 1L
    if (isSubstrat) {
        document.getElementById('base-price-display').value =
            (perLiter * 10).toFixed(2) + ' € / 10 L';
    } else {
        document.getElementById('base-price-display').value =
            perLiter.toFixed(2) + ' € / L';
    }
}

// ── Steuerklasse ──────────────────────────────────────────────────────────────
document.getElementById('tax-class-select').addEventListener('change', function() {
    const newRow = document.getElementById('new-tax-class-row');
    if (this.value === '__new__') {
        newRow.style.display = 'block';
        this.value = 'standard';
    }
});

function addTaxClass() {
    const name = document.getElementById('new-tax-class-name').value.trim();
    const rate = document.getElementById('new-tax-class-rate').value.trim();
    if (!name) { alert('Bitte Namen eingeben.'); return; }
    fetch(ajaxUrl, {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'action=add_custom_tax_class&nonce=' + addProductNonce +
              '&name=' + encodeURIComponent(name) + '&rate=' + encodeURIComponent(rate)
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            const sel = document.getElementById('tax-class-select');
            const opt = document.createElement('option');
            opt.value = data.data.slug;
            opt.textContent = name + (rate ? ' (' + rate + '%)' : '');
            opt.selected = true;
            sel.insertBefore(opt, sel.querySelector('option[value="__new__"]'));
            cancelNewTaxClass();
        } else {
            alert('Fehler: ' + (data.data || 'Unbekannter Fehler'));
        }
    });
}

function cancelNewTaxClass() {
    document.getElementById('new-tax-class-row').style.display = 'none';
    document.getElementById('new-tax-class-name').value = '';
    document.getElementById('new-tax-class-rate').value = '';
}

// ── Versandklasse ─────────────────────────────────────────────────────────────
document.getElementById('no-shipping-class').addEventListener('change', function() {
    const sel = document.getElementById('shipping-class-select');
    sel.disabled = this.checked;
    sel.style.opacity = this.checked ? '0.4' : '1';
    if (this.checked) {
        document.getElementById('shipping-class-params').style.display = 'none';
        document.getElementById('new-shipping-class-row').style.display = 'none';
    }
});

function onShippingClassChange(sel) {
    document.getElementById('new-shipping-class-row').style.display  = 'none';
    document.getElementById('shipping-class-params').style.display   = 'none';
    if (sel.value === '__new__') {
        sel.value = '';
        document.getElementById('new-shipping-class-row').style.display = 'block';
        document.getElementById('new-sc-name').focus();
        return;
    }
    if (sel.value) {
        loadShippingClassDetails(sel.value);
    }
}

function loadShippingClassDetails(termId) {
    fetch(ajaxUrl, {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'action=get_shipping_class_details&nonce=' + addProductNonce + '&term_id=' + termId
    })
    .then(r => r.json())
    .then(data => {
        if (!data.success) return;
        const d = data.data;
        document.getElementById('sc-param-name').value        = d.name        || '';
        document.getElementById('sc-param-slug').value        = d.slug        || '';
        document.getElementById('sc-param-description').value = d.description || '';
        // Flat-Rate-Kosten anzeigen
        const costBox = document.getElementById('sc-flat-rate-costs');
        if (d.flat_rate_costs && d.flat_rate_costs.length) {
            costBox.innerHTML = '<p style="margin:10px 0 6px; font-size:12px; color:#666; font-weight:600;">Kosten (Flat Rate)</p>' +
                d.flat_rate_costs.map(z =>
                    '<div style="display:flex; align-items:center; gap:8px; margin-bottom:6px;">' +
                    '<span style="font-size:13px; color:#555; min-width:120px;">' + escHtml(z.zone) + '</span>' +
                    '<input type="number" step="0.01" min="0" value="' + escHtml(String(z.cost)) + '" ' +
                    'data-zone-id="' + z.zone_instance_id + '" class="sc-flat-rate-input" ' +
                    'style="width:80px; padding:6px 8px; border:1px solid #ddd; border-radius:3px; font-size:13px;"> €' +
                    '</div>'
                ).join('');
        } else {
            costBox.innerHTML = '<p style="font-size:12px; color:#aaa; margin:8px 0 0;">Keine Flat-Rate-Zonen konfiguriert.</p>';
        }
        document.getElementById('sc-save-msg').style.display = 'none';
        document.getElementById('shipping-class-params').style.display = 'block';
        // term_id für Speichern merken
        document.getElementById('shipping-class-params').dataset.termId = termId;
    });
}

function addShippingClass() {
    const name = document.getElementById('new-sc-name').value.trim();
    const desc = document.getElementById('new-sc-description').value.trim();
    if (!name) { alert('Bitte Name eingeben.'); return; }
    fetch(ajaxUrl, {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'action=add_shipping_class&nonce=' + addProductNonce +
              '&name=' + encodeURIComponent(name) + '&description=' + encodeURIComponent(desc)
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            const sel = document.getElementById('shipping-class-select');
            const opt = document.createElement('option');
            opt.value = data.data.term_id;
            opt.textContent = name;
            opt.selected = true;
            sel.insertBefore(opt, sel.querySelector('option[value="__new__"]'));
            cancelNewShippingClass();
            loadShippingClassDetails(data.data.term_id);
        } else {
            alert('Fehler: ' + (data.data || 'Unbekannter Fehler'));
        }
    });
}

function cancelNewShippingClass() {
    document.getElementById('new-shipping-class-row').style.display = 'none';
    document.getElementById('new-sc-name').value = '';
    document.getElementById('new-sc-description').value = '';
}

function saveShippingClassParams() {
    const termId = document.getElementById('shipping-class-params').dataset.termId;
    const name   = document.getElementById('sc-param-name').value.trim();
    const desc   = document.getElementById('sc-param-description').value.trim();
    // Flat-Rate-Kosten sammeln
    const costs = [];
    document.querySelectorAll('.sc-flat-rate-input').forEach(inp => {
        costs.push({ zone_instance_id: inp.dataset.zoneId, cost: inp.value });
    });
    fetch(ajaxUrl, {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'action=update_shipping_class&nonce=' + addProductNonce +
              '&term_id=' + termId +
              '&name=' + encodeURIComponent(name) +
              '&description=' + encodeURIComponent(desc) +
              '&costs=' + encodeURIComponent(JSON.stringify(costs))
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            // Option-Text im Dropdown aktualisieren
            const opt = document.querySelector('#shipping-class-select option[value="' + termId + '"]');
            if (opt) opt.textContent = name;
            const msg = document.getElementById('sc-save-msg');
            msg.style.display = 'inline';
            setTimeout(() => { msg.style.display = 'none'; }, 2500);
        } else {
            alert('Fehler beim Speichern: ' + (data.data || ''));
        }
    });
}

// ── Versandmaße ───────────────────────────────────────────────────────────────
function toggleShippingDims(cb) {
    document.getElementById('shipping-dims-fields').style.display = cb.checked ? 'none' : 'block';
}
function syncShippingDims() {
    if (document.getElementById('shipping-dims-same').checked) return;
    // kein Auto-Sync wenn eigene Maße aktiv
}

// ── Lager-Schwellwert ─────────────────────────────────────────────────────────
function toggleLowStock(cb) {
    document.getElementById('low-stock-threshold-row').style.display = cb.checked ? 'block' : 'none';
    document.getElementById('low-stock-default-note').style.display  = cb.checked ? 'none'  : 'block';
}

// ── Rich Text Editor ──────────────────────────────────────────────────────────
function rteCmd(cmd) {
    document.getElementById('product-description').focus();
    document.execCommand(cmd, false, null);
}
function rteHeading(tag) {
    document.getElementById('product-description').focus();
    document.execCommand('formatBlock', false, tag);
}
function rteLink() {
    const url = prompt('URL eingeben:', 'https://');
    if (url) {
        document.getElementById('product-description').focus();
        document.execCommand('createLink', false, url);
    }
}

// ── Kurzbeschreibung Zeichenzähler + Soft-Limit ───────────────────────────────
var SHORT_DESC_SOFT_LIMIT = 160;
(function() {
    var ta      = document.getElementById('product-short-description');
    var counter = document.getElementById('short-desc-count');
    var label   = document.getElementById('short-desc-limit-label');
    if (!ta || !counter) return;
    function updateCount() {
        var len = ta.value.length;
        counter.textContent = len;
        if (len > SHORT_DESC_SOFT_LIMIT) {
            counter.style.color = '#c0392b';
            label.style.color   = '#c0392b';
        } else if (len > SHORT_DESC_SOFT_LIMIT * 0.85) {
            counter.style.color = '#e67e22';
            label.style.color   = '#e67e22';
        } else {
            counter.style.color = '';
            label.style.color   = '#bbb';
        }
    }
    ta.addEventListener('input', updateCount);
})();

function checkShortDescLimit(value) {
    if (value.length <= SHORT_DESC_SOFT_LIMIT) return true;
    return confirm(
        'Die Kurzbeschreibung ist ' + value.length + ' Zeichen lang (empfohlen: max. ' + SHORT_DESC_SOFT_LIMIT + ').\n\n' +
        'Du kannst trotzdem speichern – überprüfe aber vorher, ob es auf der Produktseite gut aussieht.\n\n' +
        'Trotzdem speichern?'
    );
}

// ── Medien-Uploader ───────────────────────────────────────────────────────────
let featuredFrame, galleryFrame;
let galleryIds = [];

// ── Media Picker (routes through Crop/Watermark modal) ────────────────────────
function openMediaPicker(type) {
    if (type === 'featured') {
        if (!featuredFrame) {
            featuredFrame = wp.media({
                title: 'Titelbild auswählen',
                button: { text: 'Titelbild festlegen' },
                multiple: false
            });
            featuredFrame.on('select', function() {
                const att = featuredFrame.state().get('selection').first().toJSON();
                const imgUrl = att.url;
                cwmOpen(imgUrl, function(newAttId, thumbUrl) {
                    document.getElementById('featured-image-id').value = newAttId;
                    document.getElementById('featured-img-preview').src = thumbUrl;
                    document.getElementById('featured-placeholder').style.display = 'none';
                    document.getElementById('featured-preview').style.display = 'block';
                });
            });
        }
        featuredFrame.open();
    } else {
        if (!galleryFrame) {
            galleryFrame = wp.media({
                title: 'Weitere Bilder auswählen',
                button: { text: 'Bilder hinzufügen' },
                multiple: true
            });
            galleryFrame.on('select', function() {
                const selected = galleryFrame.state().get('selection').toArray();
                const queue = selected
                    .map(a => a.toJSON())
                    .filter(j => !galleryIds.includes(j.id));
                cwmProcessQueue(queue);
            });
        }
        galleryFrame.open();
    }
}

function addGalleryPreview(att) {
    const grid = document.getElementById('gallery-preview');
    const item = document.createElement('div');
    item.className = 'image-preview-item';
    item.id = 'gallery-item-' + att.id;
    const thumb = att.sizes && att.sizes.thumbnail ? att.sizes.thumbnail.url : att.url;
    item.innerHTML = '<img src="' + thumb + '" alt=""><button type="button" class="remove-img" onclick="removeGalleryImage(' + att.id + ')">✕</button>';
    grid.appendChild(item);
}

function removeGalleryImage(id) {
    galleryIds = galleryIds.filter(i => i !== id);
    const el = document.getElementById('gallery-item-' + id);
    if (el) el.remove();
    updateGalleryInput();
}

function updateGalleryInput() {
    document.getElementById('gallery-image-ids').value = galleryIds.join(',');
}

function removeFeatured() {
    document.getElementById('featured-image-id').value = '';
    document.getElementById('featured-img-preview').src = '';
    document.getElementById('featured-placeholder').style.display = 'flex';
    document.getElementById('featured-preview').style.display = 'none';
}

// ── Formular absenden ─────────────────────────────────────────────────────────
document.getElementById('add-product-form').addEventListener('submit', function(e) {
    e.preventDefault();
    const shortDescVal = document.getElementById('product-short-description').value;
    if (!checkShortDescLimit(shortDescVal)) return;

    const isLiter    = document.getElementById('unit-toggle').checked;
    const isSubstrat = document.getElementById('product-type-toggle').checked;

    if (isSubstrat) {
        if (!document.getElementById('product-name').value.trim()) {
            alert('Bitte einen Namen/Produkttitel angeben.');
            return;
        }
    } else if (!document.getElementById('product-gattung').value.trim()
            && !document.getElementById('product-art').value.trim()
            && !document.getElementById('product-kultivar').value.trim()) {
        alert('Bitte Gattung, Art oder Kultivar angeben.');
        return;
    }

    const btn     = document.getElementById('submit-btn');
    const spinner = document.getElementById('submit-spinner');
    btn.disabled  = true;
    spinner.style.display = 'inline';

    const descriptionHtml = document.getElementById('product-description').innerHTML;

    const params = new URLSearchParams({
        action:                'add_product',
        nonce:                 addProductNonce,
        gattung:               isSubstrat ? '' : document.getElementById('product-gattung').value,
        art:                   isSubstrat ? '' : document.getElementById('product-art').value,
        kultivar:              isSubstrat ? '' : document.getElementById('product-kultivar').value,
        product_name:          isSubstrat ? document.getElementById('product-name').value : '',
        product_sku:           document.getElementById('product-sku').value,
        product_price:         document.getElementById('product-price').value,
        product_stock:         document.getElementById('product-stock').value,
        product_type:          isSubstrat ? 'substrat' : 'pflanze',
        unit_type:             isLiter ? 'liter' : 'stueck',
        product_liters:        isLiter ? document.getElementById('product-liters').value : '',
        tax_class:             document.getElementById('tax-class-select').value,
        differential_taxation: document.getElementById('differential-taxation').checked ? '1' : '0',
        shipping_class:        document.getElementById('no-shipping-class').checked ? '' : document.getElementById('shipping-class-select').value,
        delivery_time:         document.getElementById('delivery-time-select').value,
        gattung_bp_id:         document.getElementById('product-gattung-bp-id').value,
        art_bp_id:             document.getElementById('product-art-bp-id').value,
        specification_id:      document.getElementById('h-specification-id').value,
        blueprint_links:       JSON.stringify(bpLinks),
        custom_low_stock:      document.getElementById('custom-low-stock').checked ? '1' : '0',
        low_stock_threshold:   document.getElementById('custom-low-stock').checked ? document.getElementById('low-stock-threshold').value : '5',
        never_low_stock:       document.getElementById('never-low-stock').checked ? '1' : '0',
        product_weight:        document.getElementById('product-weight').value,
        product_length:        document.getElementById('product-length').value,
        product_width:         document.getElementById('product-width').value,
        product_height:        document.getElementById('product-height').value,
        shipping_dims_same:    document.getElementById('shipping-dims-same').checked ? '1' : '0',
        shipping_length:       document.getElementById('shipping-dims-same').checked ? document.getElementById('product-length').value : document.getElementById('shipping-length').value,
        shipping_width:        document.getElementById('shipping-dims-same').checked ? document.getElementById('product-width').value  : document.getElementById('shipping-width').value,
        shipping_height:       document.getElementById('shipping-dims-same').checked ? document.getElementById('product-height').value : document.getElementById('shipping-height').value,
                care_light: document.getElementById('care-light').value,
                care_light_tolerates_min: document.getElementById('care-light-min').value,
                care_light_tolerates_max: document.getElementById('care-light-max').value,
                care_water: document.getElementById('care-water').value,
                care_water_tolerates_min: document.getElementById('care-water-min').value,
                care_water_tolerates_max: document.getElementById('care-water-max').value,
                care_winter: document.getElementById('care-winter').value,
                care_winterhaerte: document.getElementById('care-winterhaerte').value,
                care_temp_min: document.getElementById('care-temp-min').value,
                care_temp_max: document.getElementById('care-temp-max').value,
        featured_image_id:     document.getElementById('featured-image-id').value,
        gallery_image_ids:     document.getElementById('gallery-image-ids').value,
        short_description:     document.getElementById('product-short-description').value,
        description:           descriptionHtml,
        product_tags_fixed:    document.getElementById('h-fixed-tags').value,
        product_categories:    document.getElementById('h-categories').value,
        product_variable_tags: document.getElementById('h-variable-tags').value,
        product_variants:      document.getElementById('h-variants').value,
    });

    fetch(ajaxUrl, {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: params.toString()
    })
    .then(r => r.json())
    .then(data => {
        btn.disabled = false;
        spinner.style.display = 'none';
        const msg = document.getElementById('form-message');
        msg.style.display = 'block';
        if (data.success) {
            msg.className = 'success';
            msg.textContent = '✓ Produkt "' + data.data.product_name + '" wurde erfolgreich erstellt.';
            resetForm();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        } else {
            msg.className = 'error';
            msg.textContent = '✗ Fehler: ' + (data.data || 'Unbekannter Fehler');
        }
    })
    .catch(() => {
        btn.disabled = false;
        spinner.style.display = 'none';
        const msg = document.getElementById('form-message');
        msg.style.display = 'block';
        msg.className = 'error';
        msg.textContent = '✗ Netzwerkfehler. Bitte erneut versuchen.';
    });
});

function resetForm() {
    document.getElementById('add-product-form').reset();
    document.getElementById('product-gattung').value = '';
    document.getElementById('product-art').value = '';
    document.getElementById('product-gattung-bp-id').value = '';
    document.getElementById('product-art-bp-id').value = '';
    document.getElementById('h-specification-id').value = '';
    document.getElementById('product-kultivar').value = '';
    document.getElementById('product-name').value = '';
    bpLinks = {};
    document.getElementById('pflanze-taxonomy-fields').style.display = '';
    document.getElementById('substrat-name-field').style.display = 'none';
    ['gattung','art'].forEach(f => {
        const lbl = document.getElementById('combo-'+f+'-lbl');
        const placeholders = { gattung: 'z. B. Pelargonium', art: 'z. B. zonale' };
        if (lbl) { lbl.textContent = placeholders[f]; lbl.classList.add('pa-combo-placeholder'); }
        const editBtn = document.getElementById('combo-'+f+'-edit-btn');
        if (editBtn) editBtn.style.display = 'none';
    });
    document.getElementById('product-short-description').value = '';
    document.getElementById('short-desc-count').textContent = '0';
    document.getElementById('product-description').innerHTML = '';
    document.getElementById('featured-image-id').value = '';
    document.getElementById('featured-img-preview').src = '';
    document.getElementById('featured-placeholder').style.display = 'flex';
    document.getElementById('featured-preview').style.display = 'none';
    galleryIds = [];
    document.getElementById('gallery-preview').innerHTML = '';
    document.getElementById('gallery-image-ids').value = '';
    document.getElementById('liter-fields').style.display = 'none';
    document.getElementById('low-stock-threshold-row').style.display = 'none';
    document.getElementById('low-stock-default-note').style.display = 'block';
    document.getElementById('shipping-dims-fields').style.display = 'none';
    document.getElementById('shipping-dims-same').checked = true;
    document.getElementById('shipping-class-select').disabled = false;
    document.getElementById('shipping-class-select').style.opacity = '1';
    document.getElementById('shipping-class-params').style.display = 'none';
    document.getElementById('new-shipping-class-row').style.display = 'none';
    resetTagSelection();
        document.getElementById('care-light').value = '';
        document.getElementById('care-water').value = '';
        document.getElementById('care-winter').value = '';
        document.getElementById('care-temp-min').value = '';
        document.getElementById('care-temp-max').value = '';
    // Reset toggle labels
    ['type-label-left','unit-label-left'].forEach(id => {
        const el = document.getElementById(id);
        el.style.fontWeight = '600';
        el.style.color = '#333';
    });
    ['type-label-right','unit-label-right'].forEach(id => {
        const el = document.getElementById(id);
        el.style.fontWeight = '400';
        el.style.color = '#999';
    });
}

// ── CSV/Excel Bulk Import ─────────────────────────────────────────────────────
let bulkData = [];
let bulkImageData = [];
let iaCurrentRow = null;
let iaIsMain = false;

function downloadTemplate() {
    window.location.href = ajaxUrl + '?action=pa_download_template_xlsx&nonce=' + encodeURIComponent(addProductNonce);
}

function handleBulkDrop(e) {
    e.preventDefault();
    document.getElementById('bulk-drop-area').style.borderColor = '#ddd';
    const file = e.dataTransfer.files[0];
    if (file) handleBulkFile(file);
}

function handleBulkFile(file) {
    if (!file) return;
    const ext = file.name.split('.').pop().toLowerCase();
    if (!['csv','xlsx','xls'].includes(ext)) {
        alert('Nur CSV, XLSX und XLS Dateien erlaubt.');
        return;
    }
    if (ext === 'csv') {
        const reader = new FileReader();
        reader.onload = function(e) { parseCSV(file.name, e.target.result); };
        reader.readAsText(file, 'UTF-8');
    } else {
        parseExcelServer(file);
    }
}

function parseExcelServer(file) {
    const previewEl  = document.getElementById('bulk-preview');
    const progressEl = document.getElementById('bulk-progress');
    const progressTxt = document.getElementById('bulk-progress-text');
    if (progressEl)  progressEl.style.display = 'block';
    if (previewEl)   previewEl.style.display  = 'none';
    if (progressTxt) progressTxt.textContent  = 'Excel-Datei wird analysiert…';
    const fd = new FormData();
    fd.append('action', 'pa_parse_excel');
    fd.append('nonce',  addProductNonce);
    fd.append('excel_file', file);
    fetch(ajaxUrl, { method: 'POST', body: fd })
        .then(r => r.json())
        .then(d => {
            if (progressEl) progressEl.style.display = 'none';
            if (!d.success) { showBulkError(d.data || 'Fehler beim Parsen der Excel-Datei.'); return; }
            bulkData = d.data.rows;
            document.getElementById('bulk-file-name').textContent = file.name;
            document.getElementById('bulk-row-count').textContent = bulkData.length + ' Produkte gefunden';
            renderBulkPreview(d.data.headers, bulkData.slice(0, 5));
            if (previewEl) previewEl.style.display = 'block';
        })
        .catch(() => { if (progressEl) progressEl.style.display = 'none'; showBulkError('Netzwerkfehler beim Hochladen der Excel-Datei.'); });
}

function parseCSV(filename, text) {
    const lines = text.trim().split('\n');
    if (lines.length < 3) { showBulkError('Die Datei enthält keine Daten (Zeile 1=Header, Zeile 2=Beschreibung, ab Zeile 3=Daten).'); return; }
    const headers = lines[0].split(',').map(h => h.trim().replace(/^"|"$/g, ''));
    // lines[1] = description row → skip
    bulkData = lines.slice(2).map(line => {
        const vals = line.match(/(".*?"|[^,]+)(?=,|$)/g) || [];
        const row = {};
        headers.forEach((h, i) => { row[h] = (vals[i] || '').replace(/^"|"$/g, '').trim(); });
        return row;
    }).filter(r => Object.values(r).some(v => v));
    document.getElementById('bulk-file-name').textContent = filename;
    document.getElementById('bulk-row-count').textContent = bulkData.length + ' Produkte gefunden';
    renderBulkPreview(headers, bulkData.slice(0, 5));
    document.getElementById('bulk-preview').style.display  = 'block';
    document.getElementById('bulk-progress').style.display = 'none';
}

function renderBulkPreview(headers, rows) {
    const thead = document.getElementById('bulk-preview-head');
    const tbody = document.getElementById('bulk-preview-body');
    thead.innerHTML = '<tr>' + headers.map(h => '<th style="padding:8px 12px; text-align:left; font-size:12px; color:#666; border-bottom:1px solid #e8e8e8;">' + escHtml(h) + '</th>').join('') + '</tr>';
    tbody.innerHTML = rows.map(row =>
        '<tr>' + headers.map(h => '<td style="padding:8px 12px; font-size:13px; border-bottom:1px solid #f0f0f0;">' + escHtml(row[h] || '') + '</td>').join('') + '</tr>'
    ).join('');
}

function clearBulkFile() {
    bulkData = [];
    bulkImageData = [];
    document.getElementById('bulk-file-input').value = '';
    document.getElementById('bulk-preview').style.display  = 'none';
    document.getElementById('bulk-progress').style.display = 'none';
}

// ── Image Assignment Modal ────────────────────────────────────────────────────
function openImageAssignModal() {
    if (!bulkData.length) return;
    bulkImageData = bulkData.map((_, i) => ({ rowIndex: i, mainImageId: null, mainImageUrl: null, extraImageIds: [], extraImageUrls: [] }));

    document.getElementById('ia-tbody').innerHTML = bulkData.map((row, i) => `
        <tr>
            <td style="padding:8px 12px;border-bottom:1px solid #f0f0f0;">${escHtml(row['Gattung'] || row['Name'] || '')}</td>
            <td style="padding:8px 12px;border-bottom:1px solid #f0f0f0;">${escHtml(row['Art'] || '')}</td>
            <td style="padding:8px 12px;border-bottom:1px solid #f0f0f0;">${escHtml(row['Kultivar'] || '')}</td>
            <td style="padding:8px 12px;border-bottom:1px solid #f0f0f0;font-family:monospace;font-size:12px;">${escHtml(row['SKU'] || '')}</td>
            <td class="ia-main-cell" data-row="${i}" style="padding:8px 12px;border-bottom:1px solid #f0f0f0;">
                <button type="button" onclick="iaSelectMain(${i})" style="padding:5px 10px;font-size:12px;background:#f5f5f5;border:1px solid #ddd;border-radius:3px;cursor:pointer;">Bild hinzufügen</button>
            </td>
            <td class="ia-extra-cell" data-row="${i}" style="padding:8px 12px;border-bottom:1px solid #f0f0f0;">
                <div id="ia-extra-${i}" style="display:flex;flex-wrap:wrap;gap:4px;margin-bottom:4px;"></div>
                <button type="button" onclick="iaSelectExtra(${i})" style="padding:5px 10px;font-size:12px;background:#f5f5f5;border:1px solid #ddd;border-radius:3px;cursor:pointer;">+ Bild</button>
            </td>
        </tr>`).join('');

    document.getElementById('ia-skip-main').checked = false;
    document.getElementById('ia-modal').style.display = 'flex';
    updateIaImportBtn();
}

function closeIaModal() { document.getElementById('ia-modal').style.display = 'none'; }

function iaSelectMain(rowIdx)  { iaCurrentRow = rowIdx; iaIsMain = true;  document.getElementById('ia-file-input').click(); }
function iaSelectExtra(rowIdx) { iaCurrentRow = rowIdx; iaIsMain = false; document.getElementById('ia-file-input').click(); }

function iaHandleFile(input) {
    if (!input.files[0] || iaCurrentRow === null) return;
    const reader = new FileReader();
    reader.onload = function(e) {
        cwmOpen(e.target.result, function(attachmentId, thumbUrl) {
            if (iaIsMain) {
                bulkImageData[iaCurrentRow].mainImageId  = attachmentId;
                bulkImageData[iaCurrentRow].mainImageUrl = thumbUrl;
                renderIaMainThumb(iaCurrentRow, thumbUrl);
            } else {
                bulkImageData[iaCurrentRow].extraImageIds.push(attachmentId);
                bulkImageData[iaCurrentRow].extraImageUrls.push(thumbUrl);
                renderIaExtraThumb(iaCurrentRow, thumbUrl, bulkImageData[iaCurrentRow].extraImageIds.length - 1);
            }
            updateIaImportBtn();
        });
    };
    reader.readAsDataURL(input.files[0]);
    input.value = '';
}

function renderIaMainThumb(rowIdx, url) {
    const cell = document.querySelector('.ia-main-cell[data-row="' + rowIdx + '"]');
    if (!cell) return;
    cell.innerHTML = `<div style="position:relative;display:inline-block;"><img src="${escHtml(url)}" style="width:60px;height:60px;object-fit:cover;border-radius:3px;display:block;"><button type="button" onclick="iaRemoveMain(${rowIdx})" style="position:absolute;top:-5px;right:-5px;background:#e74c3c;color:white;border:none;border-radius:50%;width:18px;height:18px;cursor:pointer;font-size:12px;line-height:18px;padding:0;text-align:center;">×</button></div>`;
}

function iaRemoveMain(rowIdx) {
    bulkImageData[rowIdx].mainImageId = null;
    bulkImageData[rowIdx].mainImageUrl = null;
    const cell = document.querySelector('.ia-main-cell[data-row="' + rowIdx + '"]');
    if (cell) cell.innerHTML = `<button type="button" onclick="iaSelectMain(${rowIdx})" style="padding:5px 10px;font-size:12px;background:#f5f5f5;border:1px solid #ddd;border-radius:3px;cursor:pointer;">Bild hinzufügen</button>`;
    updateIaImportBtn();
}

function renderIaExtraThumb(rowIdx, url, idx) {
    const wrap = document.getElementById('ia-extra-' + rowIdx);
    if (!wrap) return;
    const div = document.createElement('div');
    div.style.cssText = 'position:relative;display:inline-block;';
    div.dataset.idx = idx;
    div.innerHTML = `<img src="${escHtml(url)}" style="width:50px;height:50px;object-fit:cover;border-radius:3px;display:block;"><button type="button" onclick="iaRemoveExtra(${rowIdx},${idx})" style="position:absolute;top:-5px;right:-5px;background:#e74c3c;color:white;border:none;border-radius:50%;width:16px;height:16px;cursor:pointer;font-size:11px;line-height:16px;padding:0;text-align:center;">×</button>`;
    wrap.appendChild(div);
}

function iaRemoveExtra(rowIdx, idx) {
    bulkImageData[rowIdx].extraImageIds.splice(idx, 1);
    bulkImageData[rowIdx].extraImageUrls.splice(idx, 1);
    const wrap = document.getElementById('ia-extra-' + rowIdx);
    if (!wrap) return;
    wrap.innerHTML = '';
    bulkImageData[rowIdx].extraImageUrls.forEach((u, i) => renderIaExtraThumb(rowIdx, u, i));
}

function updateIaImportBtn() {
    const btn  = document.getElementById('ia-import-btn');
    const skip = document.getElementById('ia-skip-main').checked;
    const allHaveMain = bulkImageData.every(r => r.mainImageId !== null);
    btn.disabled = !skip && !allHaveMain;
    const missing = bulkImageData.filter(r => !r.mainImageId).length;
    document.getElementById('ia-import-status').textContent = (!skip && missing) ? missing + ' Produkt(e) ohne Hauptbild' : '';
}

// ── Import ────────────────────────────────────────────────────────────────────
async function startBulkImport() {
    if (!bulkData.length) return;
    const btn = document.getElementById('ia-import-btn');
    if (btn) btn.disabled = true;
    closeIaModal();
    document.getElementById('bulk-preview').style.display  = 'none';
    document.getElementById('bulk-progress').style.display = 'block';

    const results = [];
    for (let i = 0; i < bulkData.length; i++) {
        const row = bulkData[i];
        const img = bulkImageData[i] || { mainImageId: null, extraImageIds: [] };
        document.getElementById('bulk-progress-text').textContent = 'Importiere ' + (i + 1) + ' von ' + bulkData.length + '…';
        document.getElementById('bulk-progress-bar').style.width  = Math.round(((i + 1) / bulkData.length) * 100) + '%';

        const descRaw   = (row['Beschreibung'] || '').replace(/@@/g, '\n').replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>');
        const threshold  = row['Schwellwert_Lagerbestand'] || '';
        const hasShipDim = !!(row['Versandlaenge_cm'] || row['Versandbreite_cm'] || row['Versandhoehe_cm']);

        try {
            const res = await fetch(ajaxUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({
                    action:               'add_product',
                    nonce:                addProductNonce,
                    gattung:              row['Gattung']    || row['Name'] || '',
                    art:                  row['Art']        || '',
                    kultivar:             row['Kultivar']   || '',
                    product_sku:          row['SKU']        || '',
                    product_price:        row['Preis']      || '0',
                    product_stock:        row['Bestand']    || '0',
                    product_type:         (row['Produkttyp'] || '').toLowerCase() === 'substrat' ? 'substrat' : 'pflanze',
                    unit_type:            (row['Einheit']    || '').toLowerCase() === 'liter'    ? 'liter'    : 'stueck',
                    product_liters:       row['Liter']      || '',
                    tax_class:            row['Steuerklasse'] || 'standard',
                    differential_taxation: String(row['Differenzbesteuerung'] || '0') === '1' ? '1' : '0',
                    product_weight:       row['Gewicht_kg']  || '',
                    product_length:       row['Laenge_cm']   || '',
                    product_width:        row['Breite_cm']   || '',
                    product_height:       row['Hoehe_cm']    || '',
                    shipping_dims_same:   hasShipDim ? '0' : '1',
                    shipping_length:      row['Versandlaenge_cm'] || row['Laenge_cm'] || '',
                    shipping_width:       row['Versandbreite_cm'] || row['Breite_cm'] || '',
                    shipping_height:      row['Versandhoehe_cm']  || row['Hoehe_cm']  || '',
                    shipping_class_name:  row['Versandklasse']    || '',
                    delivery_time:        row['Lieferzeit_Tage']  || '7',
                    custom_low_stock:     threshold ? '1' : '0',
                    low_stock_threshold:  threshold || '5',
                    never_low_stock:      String(row['Nie_geringer_Lagerbestand'] || '0') === '1' ? '1' : '0',
                    short_description:    row['Kurzbeschreibung'] || '',
                    description:          descRaw,
                    tags_string:          row['Tags']          || '',
                    care_light:           row['Pflegelicht']   || '',
                    care_water:           row['Pflegewasser']  || '',
                    care_winter:          row['Pflegewinter']  || '',
                    care_temp_min:        row['PflegeTempMin'] || '',
                    care_temp_max:        row['PflegeTempMax'] || '',
                    featured_image_id:    img.mainImageId   ? String(img.mainImageId)         : '',
                    gallery_image_ids:    img.extraImageIds.length ? img.extraImageIds.join(',') : '',
                    manage_stock:         '1',
                    backorders:           'no',
                }).toString()
            });
            const data = await res.json();
            const label = row['Gattung'] || row['Name'] || row['SKU'] || ('Zeile ' + (i + 1));
            results.push({ name: label, ok: data.success, msg: data.success ? ('ID: ' + data.data.product_id) : (data.data || 'Fehler') });
        } catch (err) {
            results.push({ name: row['Gattung'] || ('Zeile ' + (i + 1)), ok: false, msg: 'Netzwerkfehler' });
        }
    }

    document.getElementById('bulk-progress-text').textContent = 'Import abgeschlossen.';
    const ok  = results.filter(r => r.ok).length;
    const bad = results.filter(r => !r.ok).length;
    document.getElementById('bulk-results').innerHTML =
        '<p style="font-size:14px;margin-bottom:12px;"><strong>' + ok + ' erfolgreich</strong>' +
        (bad ? ', <strong style="color:#c0392b;">' + bad + ' fehlgeschlagen</strong>' : '') + '</p>' +
        '<div style="max-height:200px;overflow-y:auto;font-size:13px;">' +
        results.map(r => '<div style="padding:4px 0;color:' + (r.ok ? '#1e7e34' : '#c0392b') + ';">' + (r.ok ? '✓' : '✗') + ' ' + escHtml(r.name) + ' — ' + escHtml(r.msg) + '</div>').join('') +
        '</div>';
    if (btn) btn.disabled = false;
}

function showBulkError(msg) { alert(msg); }

// ═══════════════════════════════════════════════════════════════════════════════
// CROP & WATERMARK ENGINE
// ═══════════════════════════════════════════════════════════════════════════════
const CWM_SIZE  = 480;   // canvas side length (square output)
const WM_HANDLE = 8;     // handle half-size in px
const LOGO_URL  = '<?php echo content_url("uploads/2022/01/Logo-Plantaphilia-1.svg"); ?>';

const CWM = {
    canvas: null, ctx: null,
    img: null,
    imgX: 0, imgY: 0, imgScale: 1, minScale: 1,
    wmX: 0, wmY: 0, wmW: 0,
    logoImg: null, logoAspect: 1,
    drag: null,   // null | 'image' | 'wm' | 'wm-tl'|'tr'|'bl'|'br' | 'bar-a' | 'bar-b'
    lastX: 0, lastY: 0,
    // Rechteckiges-Bild feature
    rectEnabled: false,
    rectOrientation: 'horizontal', // 'horizontal' | 'vertical'
    barA: 0,   // top (horizontal) or left (vertical) bar thickness in canvas px
    barB: 0,   // bottom / right bar thickness
    callback: null,
    galleryQueue: [],
};

// ── Init ──────────────────────────────────────────────────────────────────────
(function initCWM() {
    const logo = new Image();
    logo.crossOrigin = 'anonymous';
    logo.onload = () => { CWM.logoAspect = logo.naturalHeight / logo.naturalWidth; };
    logo.src = LOGO_URL;
    CWM.logoImg = logo;
})();

// ── Open / Close ──────────────────────────────────────────────────────────────
function cwmOpen(imgUrl, callback) {
    CWM.callback = callback;
    CWM.canvas   = document.getElementById('cwm-canvas');
    CWM.ctx      = CWM.canvas.getContext('2d');
    CWM.canvas.width  = CWM_SIZE;
    CWM.canvas.height = CWM_SIZE;

    const img = new Image();
    img.crossOrigin = 'anonymous';
    img.onload = () => {
        CWM.img = img;
        // Fit image to canvas (cover)
        const ms = Math.max(CWM_SIZE / img.naturalWidth, CWM_SIZE / img.naturalHeight);
        CWM.minScale  = ms;
        CWM.imgScale  = ms;
        CWM.imgX = (CWM_SIZE - img.naturalWidth  * ms) / 2;
        CWM.imgY = (CWM_SIZE - img.naturalHeight * ms) / 2;
        // Watermark: 32% width, bottom-right
        CWM.wmW = Math.round(CWM_SIZE * 0.32);
        CWM.wmX = CWM_SIZE - CWM.wmW - 14;
        CWM.wmY = CWM_SIZE - Math.round(CWM.wmW * CWM.logoAspect) - 14;
        // Zoom slider reset
        document.getElementById('cwm-zoom').value = 100;
        cwmRender();
    };
    img.src = imgUrl;

    document.getElementById('cwm-overlay').classList.add('open');
    cwmBindEvents();
}

function cwmCancel() {
    document.getElementById('cwm-overlay').classList.remove('open');
    CWM.callback = null;
    CWM.galleryQueue = [];
}

// ── Gallery queue (process one by one) ───────────────────────────────────────
function cwmProcessQueue(queue) {
    if (!queue.length) return;
    const att = queue[0];
    cwmOpen(att.url, function(newAttId, thumbUrl) {
        galleryIds.push(newAttId);
        addGalleryPreview({ id: newAttId, sizes: { thumbnail: { url: thumbUrl } }, url: thumbUrl });
        updateGalleryInput();
        const rest = queue.slice(1);
        if (rest.length) cwmProcessQueue(rest);
    });
}

// ── Render ────────────────────────────────────────────────────────────────────
// Zeichnet Layer-Stack (OHNE Handles) auf beliebigen Context.
// scale:     1 für Vorschau, (1042/CWM_SIZE) für Export
// forExport: true → bar-Bereiche transparent (clearRect), false → schwarz
function cwmRenderBase(ctx, scale, forExport) {
    const S = CWM_SIZE * scale;
    ctx.clearRect(0, 0, S, S);

    // Beim Export: Inhalt zentrieren — Offset = (barB - barA) / 2
    // (z.B. barA=100, barB=200 → offset=+50 → Bild 50px nach unten/rechts verschoben,
    //  dann gleich viel (150px) auf beiden Seiten transparent)
    let exportOffsetX = 0, exportOffsetY = 0;
    if (forExport && CWM.rectEnabled) {
        const off = (CWM.barB - CWM.barA) / 2 * scale;
        if (CWM.rectOrientation === 'horizontal') exportOffsetY = off;
        else                                       exportOffsetX = off;
    }

    // Layer 1 – Bild
    if (CWM.img) {
        ctx.drawImage(CWM.img,
            CWM.imgX * scale + exportOffsetX,
            CWM.imgY * scale + exportOffsetY,
            CWM.img.naturalWidth  * CWM.imgScale * scale,
            CWM.img.naturalHeight * CWM.imgScale * scale
        );
    }

    // Layer 2 – Entfernungsbereiche (schwarze Balken / transparent)
    if (CWM.rectEnabled) {
        const bA = CWM.barA * scale;
        const bB = CWM.barB * scale;
        if (forExport) {
            // Export: gleich große transparente Bereiche auf beiden Seiten
            const avg = (bA + bB) / 2;
            if (CWM.rectOrientation === 'horizontal') {
                ctx.clearRect(0, 0, S, avg);
                ctx.clearRect(0, S - avg, S, avg);
            } else {
                ctx.clearRect(0, 0, avg, S);
                ctx.clearRect(S - avg, 0, avg, S);
            }
        } else {
            // Vorschau: asymmetrische schwarze Balken (wie der User sie zieht)
            ctx.fillStyle = '#000';
            if (CWM.rectOrientation === 'horizontal') {
                ctx.fillRect(0, 0, S, bA);
                ctx.fillRect(0, S - bB, S, bB);
            } else {
                ctx.fillRect(0, 0, bA, S);
                ctx.fillRect(S - bB, 0, bB, S);
            }
        }
    }

    // Layer 3 – Wasserzeichen (immer über Balken, im Export ebenfalls zentriert)
    const showWm = document.getElementById('cwm-show-wm').checked;
    if (showWm && CWM.logoImg && CWM.logoImg.complete && CWM.logoImg.naturalWidth) {
        const wmW = CWM.wmW * scale;
        const wmH = Math.round(wmW * CWM.logoAspect);
        const wmX = CWM.wmX * scale + exportOffsetX;
        const wmY = CWM.wmY * scale + exportOffsetY;
        const pad = Math.round(wmW * 0.03);
        const r   = Math.round(Math.min(wmW, wmH) * 0.2);

        ctx.save();
        ctx.globalAlpha = 0.52;
        ctx.fillStyle   = '#7a7a7a';
        cwmRoundRect(ctx, wmX, wmY, wmW, wmH, r);
        ctx.fill();
        ctx.restore();

        ctx.save();
        if (document.getElementById('cwm-invert-wm').checked) {
            ctx.filter = 'invert(1)';
        }
        ctx.globalAlpha = 0.88;
        ctx.drawImage(CWM.logoImg, wmX + pad, wmY + pad, wmW - 2*pad, wmH - 2*pad);
        ctx.restore();
    }
}

// Vorschau: base + alle Handles obendrauf
function cwmRender() {
    if (!CWM.canvas || !CWM.img) return;
    cwmRenderBase(CWM.ctx, 1, false);
    const ctx = CWM.ctx;
    const S   = CWM_SIZE;

    // Wasserzeichen-Handles
    const showWm = document.getElementById('cwm-show-wm').checked;
    if (showWm) {
        ctx.fillStyle   = '#fff';
        ctx.strokeStyle = '#555';
        ctx.lineWidth   = 1.5;
        for (const h of cwmGetHandles()) {
            ctx.beginPath();
            ctx.rect(h.x - WM_HANDLE, h.y - WM_HANDLE, WM_HANDLE*2, WM_HANDLE*2);
            ctx.fill(); ctx.stroke();
        }
    }

    // Balken-Handles: gestrichelte Linie an der Innenkante + Grip-Markierung
    if (CWM.rectEnabled) {
        ctx.save();
        ctx.strokeStyle = 'rgba(255,255,255,0.85)';
        ctx.lineWidth   = 2;
        ctx.setLineDash([6, 4]);
        if (CWM.rectOrientation === 'horizontal') {
            const yA = CWM.barA, yB = S - CWM.barB;
            ctx.beginPath(); ctx.moveTo(0, yA); ctx.lineTo(S, yA); ctx.stroke();
            ctx.beginPath(); ctx.moveTo(0, yB); ctx.lineTo(S, yB); ctx.stroke();
            // Grip-Pillen
            ctx.setLineDash([]);
            cwmDrawGrip(ctx, S/2, yA, 'h');
            cwmDrawGrip(ctx, S/2, yB, 'h');
        } else {
            const xA = CWM.barA, xB = S - CWM.barB;
            ctx.beginPath(); ctx.moveTo(xA, 0); ctx.lineTo(xA, S); ctx.stroke();
            ctx.beginPath(); ctx.moveTo(xB, 0); ctx.lineTo(xB, S); ctx.stroke();
            ctx.setLineDash([]);
            cwmDrawGrip(ctx, xA, S/2, 'v');
            cwmDrawGrip(ctx, xB, S/2, 'v');
        }
        ctx.restore();
    }
}

function cwmDrawGrip(ctx, cx, cy, dir) {
    const w = dir === 'h' ? 28 : 8;
    const h = dir === 'h' ? 8  : 28;
    ctx.fillStyle = 'rgba(255,255,255,0.9)';
    ctx.strokeStyle = '#666';
    ctx.lineWidth = 1;
    ctx.beginPath();
    ctx.roundRect(cx - w/2, cy - h/2, w, h, 4);
    ctx.fill(); ctx.stroke();
    // 3 dots
    ctx.fillStyle = '#999';
    for (let i = -1; i <= 1; i++) {
        const ox = dir === 'h' ? i * 8 : 0;
        const oy = dir === 'v' ? i * 8 : 0;
        ctx.beginPath();
        ctx.arc(cx + ox, cy + oy, 1.5, 0, Math.PI * 2);
        ctx.fill();
    }
}

function cwmRoundRect(ctx, x, y, w, h, r) {
    ctx.beginPath();
    ctx.moveTo(x + r, y);
    ctx.lineTo(x + w - r, y); ctx.quadraticCurveTo(x + w, y, x + w, y + r);
    ctx.lineTo(x + w, y + h - r); ctx.quadraticCurveTo(x + w, y + h, x + w - r, y + h);
    ctx.lineTo(x + r, y + h); ctx.quadraticCurveTo(x, y + h, x, y + h - r);
    ctx.lineTo(x, y + r); ctx.quadraticCurveTo(x, y, x + r, y);
    ctx.closePath();
}

function cwmGetHandles() {
    const wmH = Math.round(CWM.wmW * CWM.logoAspect);
    return [
        { x: CWM.wmX,           y: CWM.wmY,           id: 'tl' },
        { x: CWM.wmX + CWM.wmW, y: CWM.wmY,           id: 'tr' },
        { x: CWM.wmX,           y: CWM.wmY + wmH,      id: 'bl' },
        { x: CWM.wmX + CWM.wmW, y: CWM.wmY + wmH,      id: 'br' },
    ];
}

// ── Hit test ──────────────────────────────────────────────────────────────────
const BAR_HIT = 12; // px around bar edge that counts as a hit

function cwmHitTest(x, y) {
    // Bars first (highest priority)
    if (CWM.rectEnabled) {
        if (CWM.rectOrientation === 'horizontal') {
            if (Math.abs(y - CWM.barA) <= BAR_HIT) return 'bar-a';
            if (Math.abs(y - (CWM_SIZE - CWM.barB)) <= BAR_HIT) return 'bar-b';
        } else {
            if (Math.abs(x - CWM.barA) <= BAR_HIT) return 'bar-a';
            if (Math.abs(x - (CWM_SIZE - CWM.barB)) <= BAR_HIT) return 'bar-b';
        }
    }
    // Watermark handles + body
    const showWm = document.getElementById('cwm-show-wm').checked;
    if (showWm) {
        for (const h of cwmGetHandles()) {
            if (Math.abs(x - h.x) <= WM_HANDLE + 2 && Math.abs(y - h.y) <= WM_HANDLE + 2) {
                return 'wm-' + h.id;
            }
        }
        const wmH = Math.round(CWM.wmW * CWM.logoAspect);
        if (x >= CWM.wmX && x <= CWM.wmX + CWM.wmW && y >= CWM.wmY && y <= CWM.wmY + wmH) {
            return 'wm';
        }
    }
    return 'image';
}

// ── Events ────────────────────────────────────────────────────────────────────
function cwmBindEvents() {
    const c = document.getElementById('cwm-canvas');
    c.onmousedown  = cwmDown;
    c.ontouchstart = (e) => { e.preventDefault(); cwmDown(e.touches[0]); };
    document.onmousemove  = cwmMove;
    document.ontouchmove  = (e) => { if (CWM.drag) { e.preventDefault(); cwmMove(e.touches[0]); } };
    document.onmouseup    = cwmUp;
    document.ontouchend   = cwmUp;
    c.onwheel = cwmWheel;
}

function cwmPos(e) {
    const r = CWM.canvas.getBoundingClientRect();
    const scaleX = CWM_SIZE / r.width;
    const scaleY = CWM_SIZE / r.height;
    return { x: (e.clientX - r.left) * scaleX, y: (e.clientY - r.top) * scaleY };
}

function cwmDown(e) {
    const { x, y } = cwmPos(e);
    CWM.drag  = cwmHitTest(x, y);
    CWM.lastX = x; CWM.lastY = y;
    CWM.canvas.classList.add('dragging');
}

function cwmMove(e) {
    const { x, y } = cwmPos(e);

    // Cursor-Update wenn kein Drag aktiv
    if (!CWM.drag) {
        const hit = cwmHitTest(x, y);
        if (!CWM.canvas) return;
        if (hit === 'bar-a' || hit === 'bar-b') {
            CWM.canvas.style.cursor = CWM.rectOrientation === 'horizontal' ? 'ns-resize' : 'ew-resize';
        } else if (hit === 'wm') {
            CWM.canvas.style.cursor = 'move';
        } else if (hit.startsWith('wm-')) {
            CWM.canvas.style.cursor = 'nwse-resize';
        } else {
            CWM.canvas.style.cursor = 'grab';
        }
        return;
    }

    const dx = x - CWM.lastX;
    const dy = y - CWM.lastY;
    CWM.lastX = x; CWM.lastY = y;
    const MAX_BAR = CWM_SIZE * 0.48;

    if (CWM.drag === 'image') {
        CWM.imgX += dx; CWM.imgY += dy;
        cwmClampImage();
    } else if (CWM.drag === 'wm') {
        CWM.wmX += dx; CWM.wmY += dy;
        cwmClampWatermark();
    } else if (CWM.drag.startsWith('wm-')) {
        const corner = CWM.drag.slice(3);
        const MIN_W  = 40;
        if (corner === 'br') {
            CWM.wmW = Math.max(MIN_W, CWM.wmW + dx);
        } else if (corner === 'bl') {
            const newW = Math.max(MIN_W, CWM.wmW - dx);
            CWM.wmX  += CWM.wmW - newW; CWM.wmW = newW;
        } else if (corner === 'tr') {
            CWM.wmW  = Math.max(MIN_W, CWM.wmW + dx);
            CWM.wmY += dy;
        } else if (corner === 'tl') {
            const newW = Math.max(MIN_W, CWM.wmW - dx);
            CWM.wmX  += CWM.wmW - newW; CWM.wmY += dy; CWM.wmW = newW;
        }
        cwmClampWatermark();
    } else if (CWM.drag === 'bar-a') {
        if (CWM.rectOrientation === 'horizontal') {
            CWM.barA = Math.max(0, Math.min(MAX_BAR, CWM.barA + dy));
        } else {
            CWM.barA = Math.max(0, Math.min(MAX_BAR, CWM.barA + dx));
        }
        cwmClampWatermark();
    } else if (CWM.drag === 'bar-b') {
        if (CWM.rectOrientation === 'horizontal') {
            CWM.barB = Math.max(0, Math.min(MAX_BAR, CWM.barB - dy));
        } else {
            CWM.barB = Math.max(0, Math.min(MAX_BAR, CWM.barB - dx));
        }
        cwmClampWatermark();
    }
    cwmRender();
}

function cwmUp() {
    CWM.drag = null;
    if (CWM.canvas) CWM.canvas.classList.remove('dragging');
}

function cwmWheel(e) {
    e.preventDefault();
    const delta = e.deltaY > 0 ? -0.08 : 0.08;
    const newScale = Math.max(CWM.minScale, CWM.imgScale * (1 + delta));
    // Zoom centered on canvas
    const cx = CWM_SIZE / 2;
    const cy = CWM_SIZE / 2;
    CWM.imgX = cx - (cx - CWM.imgX) * (newScale / CWM.imgScale);
    CWM.imgY = cy - (cy - CWM.imgY) * (newScale / CWM.imgScale);
    CWM.imgScale = newScale;
    cwmClampImage();
    // Sync slider
    const pct = Math.round(((newScale - CWM.minScale) / (CWM.minScale * 4)) * 400 + 100);
    document.getElementById('cwm-zoom').value = Math.min(500, Math.max(100, pct));
    cwmRender();
}

function cwmZoomSlider(val) {
    const factor  = (val - 100) / 400;        // 0–1
    const newScale = CWM.minScale * (1 + factor * 4);
    const cx = CWM_SIZE / 2; const cy = CWM_SIZE / 2;
    CWM.imgX = cx - (cx - CWM.imgX) * (newScale / CWM.imgScale);
    CWM.imgY = cy - (cy - CWM.imgY) * (newScale / CWM.imgScale);
    CWM.imgScale = newScale;
    cwmClampImage();
    cwmRender();
}

// Wasserzeichen auf den sichtbaren Bereich (zwischen den Balken) begrenzen
function cwmClampWatermark() {
    if (!CWM.rectEnabled) return;
    const wmH = Math.round(CWM.wmW * CWM.logoAspect);
    if (CWM.rectOrientation === 'horizontal') {
        const minY = CWM.barA;
        const maxY = CWM_SIZE - CWM.barB - wmH;
        if (maxY > minY) CWM.wmY = Math.max(minY, Math.min(maxY, CWM.wmY));
    } else {
        const minX = CWM.barA;
        const maxX = CWM_SIZE - CWM.barB - CWM.wmW;
        if (maxX > minX) CWM.wmX = Math.max(minX, Math.min(maxX, CWM.wmX));
    }
}

function cwmClampImage() {
    const iw = CWM.img.naturalWidth  * CWM.imgScale;
    const ih = CWM.img.naturalHeight * CWM.imgScale;
    if (CWM.imgX > 0)           CWM.imgX = 0;
    if (CWM.imgY > 0)           CWM.imgY = 0;
    if (CWM.imgX + iw < CWM_SIZE) CWM.imgX = CWM_SIZE - iw;
    if (CWM.imgY + ih < CWM_SIZE) CWM.imgY = CWM_SIZE - ih;
}

// ── Save ──────────────────────────────────────────────────────────────────────
async function cwmSave() {
    const btn  = document.getElementById('cwm-save-btn');
    const prog = document.getElementById('cwm-upload-progress');
    btn.disabled = true;
    prog.style.display = 'block';

    // Render handles-free at 1042×1042 for export
    const EXPORT    = 1042;
    const useRect   = CWM.rectEnabled;
    const offscreen = document.createElement('canvas');
    offscreen.width  = EXPORT;
    offscreen.height = EXPORT;
    cwmRenderBase(offscreen.getContext('2d'), EXPORT / CWM_SIZE, true);
    const mimeType = useRect ? 'image/png' : 'image/jpeg';
    const quality  = useRect ? undefined  : 0.93;
    const ext      = useRect ? '.png'     : '.jpg';
    const blob = await new Promise(resolve => offscreen.toBlob(resolve, mimeType, quality));

    const form = new FormData();
    form.append('action', 'upload_cropped_image');
    form.append('nonce',  addProductNonce);
    form.append('image',  blob, 'plantaphilia-' + Date.now() + ext);

    try {
        const res  = await fetch(ajaxUrl, { method: 'POST', body: form });
        const data = await res.json();
        if (data.success) {
            const cb = CWM.callback;
            document.getElementById('cwm-overlay').classList.remove('open');
            if (cb) cb(data.data.attachment_id, data.data.thumb_url);
        } else {
            alert('Upload fehlgeschlagen: ' + (data.data || 'Fehler'));
        }
    } catch (e) {
        alert('Netzwerkfehler beim Upload.');
    } finally {
        btn.disabled = false;
        prog.style.display = 'none';
    }
}
function cwmToggleRect() {
    CWM.rectEnabled = document.getElementById('cwm-rect-enable').checked;
    document.getElementById('cwm-rect-controls').style.display = CWM.rectEnabled ? 'block' : 'none';
    if (CWM.rectEnabled) {
        CWM.barA = Math.round(CWM_SIZE * 0.15);
        CWM.barB = Math.round(CWM_SIZE * 0.15);
        cwmClampWatermark();
    } else {
        CWM.barA = 0; CWM.barB = 0;
    }
    cwmRender();
}

function cwmToggleOrientation() {
    const isVert = document.getElementById('cwm-rect-toggle').checked;
    CWM.rectOrientation = isVert ? 'vertical' : 'horizontal';
    CWM.barA = Math.round(CWM_SIZE * 0.15);
    CWM.barB = Math.round(CWM_SIZE * 0.15);
    cwmClampWatermark();
    const lH = document.getElementById('cwm-orient-label-h');
    const lV = document.getElementById('cwm-orient-label-v');
    lH.style.fontWeight = isVert ? '400' : '600';
    lV.style.fontWeight = isVert ? '600' : '400';
    cwmRender();
}
// ═══════════════════════════════════════════════════════════════════════════════

function escHtml(s) {
    return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

// ══════════════════════════════════════════════════════════════════════════════
// TAG POOL
// ══════════════════════════════════════════════════════════════════════════════
let tagPoolData = { variable_types: [], variable_values: {}, fixed: [], categories: [] };

// State
const selFixed      = new Set();    // term_id numbers
const selCategories = new Set();    // term_id numbers
const selVariable   = {};           // { prefix: { value, isVariation } }

// Load on page init
(async function initTagPool() {
    try {
        const res  = await fetch(ajaxUrl, {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'action=pa_get_tag_pool&nonce=' + addProductNonce
        });
        const data = await res.json();
        if (data.success) {
            tagPoolData = data.data;
            renderTagPool();
        }
    } catch(e) {
        document.getElementById('tag-pool-wrap').innerHTML =
            '<p style="color:#c00; font-size:13px;">Tag Pool konnte nicht geladen werden.</p>';
    }
})();

// ── Gattung / Art Combo-Dropdown (Blueprint-gestützt) ─────────────────────────
// Gattung/Art sind jetzt Blueprints mit vererbbaren Feldern (wie in der App):
// Auswahl füllt leere Formularfelder, ein manueller Edit "gewinnt" danach immer.
var _bp = { gattungen: [], arten: [] };

async function paAjax(action, extra) {
    const params = new URLSearchParams(Object.assign({ action, nonce: addProductNonce }, extra || {}));
    const res = await fetch(ajaxUrl, { method: 'POST', headers: {'Content-Type':'application/x-www-form-urlencoded'}, body: params.toString() });
    return res.json();
}

(async function initBlueprints() {
    try {
        const data = await paAjax('pa_get_blueprints');
        if (data.success) { _bp = data.data; _hfRenderGattungOpts(); }
    } catch(e) {}
})();

function _hfRenderGattungOpts() {
    const opts = document.getElementById('combo-gattung-opts');
    if (!opts) return;
    const curId = document.getElementById('product-gattung-bp-id').value;
    opts.innerHTML = _bp.gattungen.map(g =>
        `<button type="button" class="pa-combo-option${String(g.id)===curId?' selected':''}" onclick="_hfComboSelect('gattung',${g.id})">${escHtml(g.name)}</button>`
    ).join('') || '<div style="padding:9px 12px;color:#aaa;font-size:12px;font-style:italic;">Noch keine Einträge</div>';
}

function _hfRenderArtOpts(gattungId) {
    const opts = document.getElementById('combo-art-opts');
    if (!opts) return;
    const curId = document.getElementById('product-art-bp-id').value;
    const arts = _bp.arten.filter(a => String(a.gattung_id) === String(gattungId));
    opts.innerHTML = arts.map(a =>
        `<button type="button" class="pa-combo-option${String(a.id)===curId?' selected':''}" onclick="_hfComboSelect('art',${a.id})">${escHtml(a.name)}</button>`
    ).join('') || '<div style="padding:9px 12px;color:#aaa;font-size:12px;font-style:italic;">Noch keine Einträge</div>';
}

function _hfComboToggle(field) {
    const drop = document.getElementById('combo-' + field + '-drop');
    const trig = drop.previousElementSibling;
    const isOpen = drop.style.display !== 'none';
    // Close all
    ['gattung','art'].forEach(f => {
        document.getElementById('combo-'+f+'-drop').style.display = 'none';
        document.getElementById('combo-'+f+'-drop').previousElementSibling.classList.remove('open');
    });
    if (!isOpen) {
        if (field === 'gattung') _hfRenderGattungOpts();
        if (field === 'art') _hfRenderArtOpts(document.getElementById('product-gattung-bp-id').value);
        drop.style.display = '';
        trig.classList.add('open');
    }
}

function _hfComboSelect(field, bpId) {
    const pool = field === 'gattung' ? _bp.gattungen : _bp.arten;
    const row = pool.find(x => x.id === bpId);
    if (!row) return;
    document.getElementById('product-' + field).value = row.name;
    document.getElementById('product-' + field + '-bp-id').value = row.id;
    const lbl = document.getElementById('combo-' + field + '-lbl');
    lbl.textContent = row.name;
    lbl.classList.remove('pa-combo-placeholder');
    document.getElementById('combo-' + field + '-drop').style.display = 'none';
    document.getElementById('combo-' + field + '-drop').previousElementSibling.classList.remove('open');
    document.getElementById('combo-' + field + '-edit-btn').style.display = '';
    if (field === 'gattung') {
        // Reset art when gattung changes
        document.getElementById('product-art').value = '';
        document.getElementById('product-art-bp-id').value = '';
        const artLbl = document.getElementById('combo-art-lbl');
        artLbl.textContent = 'z. B. zonale';
        artLbl.classList.add('pa-combo-placeholder');
        document.getElementById('combo-art-edit-btn').style.display = 'none';
        document.querySelectorAll('#combo-art-opts .pa-combo-option').forEach(b => b.classList.remove('selected'));
        bpApplyFields('gattung', row.fields || {});
    } else {
        bpApplyFields('art', row.fields || {});
    }
}

// Close on outside click
document.addEventListener('click', function(e) {
    if (!e.target.closest('.pa-combo')) {
        ['gattung','art'].forEach(f => {
            const drop = document.getElementById('combo-'+f+'-drop');
            const trig = drop && drop.previousElementSibling;
            if (drop) drop.style.display = 'none';
            if (trig) trig.classList.remove('open');
        });
    }
});

// ── Blueprint-Vererbung (Gattung/Art → Formularfelder) ────────────────────────
// Selbe Präzedenz wie in der App: 'manual' (Nutzer hat selbst editiert) blockt
// immer; 'gattung' verliert gegen bereits von 'art' belegte Felder.
var bpLinks = {};
var bpApplying = false;

var BP_FIELD_MAP = [
    { key:'liter_content', id:'product-liters', kind:'value' },
    { key:'tax_class', id:'tax-class-select', kind:'value' },
    { key:'differential_taxation', id:'differential-taxation', kind:'checked' },
    { key:'stock', id:'product-stock', kind:'value' },
    { key:'low_stock_threshold', id:'low-stock-threshold', kind:'low_stock' },
    { key:'never_low_stock', id:'never-low-stock', kind:'checked' },
    { key:'short_description', id:'product-short-description', kind:'value' },
    { key:'description', id:'product-description', kind:'html' },
    { key:'care_light', id:'care-light', kind:'value' },
    { key:'care_light_tolerates_min', id:'care-light-min', kind:'value' },
    { key:'care_light_tolerates_max', id:'care-light-max', kind:'value' },
    { key:'care_water', id:'care-water', kind:'value' },
    { key:'care_water_tolerates_min', id:'care-water-min', kind:'value' },
    { key:'care_water_tolerates_max', id:'care-water-max', kind:'value' },
    { key:'care_winter', id:'care-winter', kind:'value' },
    { key:'care_winterhaerte', id:'care-winterhaerte', kind:'value' },
    { key:'care_temp_min', id:'care-temp-min', kind:'value' },
    { key:'care_temp_max', id:'care-temp-max', kind:'value' },
];

function bpCanApply(currentOwner, sourceType) {
    if (currentOwner === 'manual') return false;
    if (currentOwner === 'art' && sourceType === 'gattung') return false;
    return true;
}

function _bpMarkManual(key) { if (!bpApplying) bpLinks[key] = 'manual'; }

function bpApplyFields(sourceType, fields) {
    bpApplying = true;
    Object.keys(fields).forEach(function(key) {
        if (!bpCanApply(bpLinks[key], sourceType)) return;
        const val = fields[key];
        if (key === 'unit_type') {
            document.getElementById('unit-toggle').checked = (val === 'liter');
            document.getElementById('unit-toggle').dispatchEvent(new Event('change'));
        } else if (key === 'product_type') {
            document.getElementById('product-type-toggle').checked = (val === 'substrate');
            document.getElementById('product-type-toggle').dispatchEvent(new Event('change'));
        } else if (key === 'specification_id') {
            _specApplyById(val);
        } else if (key === 'delivery_time') {
            document.getElementById('delivery-time-select').value = val;
        } else {
            const m = BP_FIELD_MAP.find(f => f.key === key);
            if (!m) return;
            const el = document.getElementById(m.id);
            if (!el) return;
            if (m.kind === 'checked') el.checked = !!val;
            else if (m.kind === 'html') el.innerHTML = val || '';
            else if (m.kind === 'low_stock') {
                document.getElementById('custom-low-stock').checked = true;
                toggleLowStock(document.getElementById('custom-low-stock'));
                el.value = val;
            } else el.value = val;
        }
        bpLinks[key] = sourceType;
    });
    bpApplying = false;
}

BP_FIELD_MAP.forEach(function(m) {
    const el = document.getElementById(m.id);
    if (!el) return;
    const evt = (m.kind === 'checked') ? 'change' : 'input';
    el.addEventListener(evt, function() { _bpMarkManual(m.key); });
});
['unit-toggle','product-type-toggle'].forEach(function(id) {
    const key = id === 'unit-toggle' ? 'unit_type' : 'product_type';
    document.getElementById(id).addEventListener('change', function() { _bpMarkManual(key); });
});

// ── Blueprint-Modal (Gattung/Art anlegen/bearbeiten) ──────────────────────────
var _bpModalState = null; // { type, id }

function openBlueprintModal(type, id) {
    _bpModalState = { type, id };
    document.getElementById('bp-tax-class').innerHTML = document.getElementById('tax-class-select').innerHTML;
    document.getElementById('bp-delivery-time').innerHTML = document.getElementById('delivery-time-select').innerHTML;
    document.getElementById('bp-care-light').innerHTML = document.getElementById('care-light').innerHTML;
    document.getElementById('bp-care-light-min').innerHTML = document.getElementById('care-light-min').innerHTML;
    document.getElementById('bp-care-light-max').innerHTML = document.getElementById('care-light-max').innerHTML;
    document.getElementById('bp-care-water').innerHTML = document.getElementById('care-water').innerHTML;
    document.getElementById('bp-care-water-min').innerHTML = document.getElementById('care-water-min').innerHTML;
    document.getElementById('bp-care-water-max').innerHTML = document.getElementById('care-water-max').innerHTML;
    document.getElementById('bp-care-winterhaerte').innerHTML = document.getElementById('care-winterhaerte').innerHTML;
    _specPopulateSelect(document.getElementById('bp-specification'));

    const row = id ? (type === 'gattung' ? _bp.gattungen : _bp.arten).find(x => x.id === id) : null;
    const f = (row && row.fields) || {};
    document.getElementById('bp-title').textContent = id
        ? `${type === 'art' ? 'Art' : 'Gattung'} bearbeiten`
        : `Neue ${type === 'art' ? 'Art' : 'Gattung'}`;
    document.getElementById('bp-name').value = row ? row.name : '';
    document.getElementById('bp-unit-type').value = f.unit_type || '';
    document.getElementById('bp-product-type').value = f.product_type || '';
    document.getElementById('bp-tax-class').value = f.tax_class || '';
    document.getElementById('bp-delivery-time').value = f.delivery_time || '';
    document.getElementById('bp-differential-taxation').checked = f.differential_taxation === true;
    document.getElementById('bp-stock').value = f.stock ?? '';
    document.getElementById('bp-low-stock-threshold').value = f.low_stock_threshold ?? '';
    document.getElementById('bp-never-low-stock').checked = f.never_low_stock === true;
    document.getElementById('bp-specification').value = f.specification_id || '';
    document.getElementById('bp-short-description').value = f.short_description || '';
    document.getElementById('bp-description').value = f.description || '';
    document.getElementById('bp-care-light').value = f.care_light || '';
    document.getElementById('bp-care-light-min').value = f.care_light_tolerates_min || '';
    document.getElementById('bp-care-light-max').value = f.care_light_tolerates_max || '';
    document.getElementById('bp-care-water').value = f.care_water || '';
    document.getElementById('bp-care-water-min').value = f.care_water_tolerates_min || '';
    document.getElementById('bp-care-water-max').value = f.care_water_tolerates_max || '';
    document.getElementById('bp-care-winterhaerte').value = f.care_winterhaerte || '';
    document.getElementById('bp-care-winter').value = f.care_winter || '';
    document.getElementById('bp-care-temp-min').value = f.care_temp_min ?? '';
    document.getElementById('bp-care-temp-max').value = f.care_temp_max ?? '';

    document.getElementById('pa-bp-overlay').style.display = 'flex';
    setTimeout(() => document.getElementById('bp-name').focus(), 40);
}

function closeBlueprintModal() {
    document.getElementById('pa-bp-overlay').style.display = 'none';
    _bpModalState = null;
}

async function saveBlueprintModal() {
    const name = document.getElementById('bp-name').value.trim();
    if (!name) { document.getElementById('bp-name').focus(); return; }
    const { type, id } = _bpModalState;
    const gattungName = type === 'art' ? document.getElementById('product-gattung').value : '';
    if (type === 'art' && !gattungName) { alert('Bitte zuerst eine Gattung wählen.'); return; }

    const fields = {};
    const sVal = eid => document.getElementById(eid).value;
    if (sVal('bp-unit-type')) fields.unit_type = sVal('bp-unit-type');
    if (sVal('bp-product-type')) fields.product_type = sVal('bp-product-type');
    if (sVal('bp-tax-class')) fields.tax_class = sVal('bp-tax-class');
    if (sVal('bp-delivery-time')) fields.delivery_time = sVal('bp-delivery-time');
    fields.differential_taxation = document.getElementById('bp-differential-taxation').checked;
    if (sVal('bp-stock') !== '') fields.stock = parseInt(sVal('bp-stock'), 10);
    if (sVal('bp-low-stock-threshold') !== '') fields.low_stock_threshold = parseInt(sVal('bp-low-stock-threshold'), 10);
    fields.never_low_stock = document.getElementById('bp-never-low-stock').checked;
    if (sVal('bp-specification')) fields.specification_id = parseInt(sVal('bp-specification'), 10);
    if (sVal('bp-short-description').trim()) fields.short_description = sVal('bp-short-description');
    if (sVal('bp-description').trim()) fields.description = sVal('bp-description');
    if (sVal('bp-care-light')) fields.care_light = sVal('bp-care-light');
    if (sVal('bp-care-light-min')) fields.care_light_tolerates_min = sVal('bp-care-light-min');
    if (sVal('bp-care-light-max')) fields.care_light_tolerates_max = sVal('bp-care-light-max');
    if (sVal('bp-care-water')) fields.care_water = sVal('bp-care-water');
    if (sVal('bp-care-water-min')) fields.care_water_tolerates_min = sVal('bp-care-water-min');
    if (sVal('bp-care-water-max')) fields.care_water_tolerates_max = sVal('bp-care-water-max');
    if (sVal('bp-care-winter').trim()) fields.care_winter = sVal('bp-care-winter');
    if (sVal('bp-care-winterhaerte')) fields.care_winterhaerte = sVal('bp-care-winterhaerte');
    if (sVal('bp-care-temp-min') !== '') fields.care_temp_min = parseFloat(sVal('bp-care-temp-min'));
    if (sVal('bp-care-temp-max') !== '') fields.care_temp_max = parseFloat(sVal('bp-care-temp-max'));

    const data = await paAjax('pa_save_blueprint', {
        id: id || '', type, name, gattung: gattungName, fields: JSON.stringify(fields)
    });
    if (!data.success) { alert(data.data || 'Fehler beim Speichern'); return; }

    const savedRow = { id: data.data.id, name: data.data.name, fields: data.data.fields, gattung_id: type === 'art' ? document.getElementById('product-gattung-bp-id').value : undefined };
    if (type === 'gattung') {
        const idx = _bp.gattungen.findIndex(g => g.id === savedRow.id);
        if (idx >= 0) _bp.gattungen[idx] = savedRow; else _bp.gattungen.push(savedRow);
        _bp.gattungen.sort((a,b) => a.name.localeCompare(b.name));
    } else {
        savedRow.gattung_id = document.getElementById('product-gattung-bp-id').value;
        const idx = _bp.arten.findIndex(a => a.id === savedRow.id);
        if (idx >= 0) _bp.arten[idx] = savedRow; else _bp.arten.push(savedRow);
        _bp.arten.sort((a,b) => a.name.localeCompare(b.name));
    }
    closeBlueprintModal();
    _hfComboSelect(type, savedRow.id);
}

// ── Spezifikations-Picker ──────────────────────────────────────────────────
var _specs = [];

(async function initSpecs() {
    try {
        const data = await paAjax('pa_get_specifications');
        if (data.success) { _specs = data.data; _specPopulateSelect(document.getElementById('specification-select')); }
    } catch(e) {}
})();

function _specLabel(s) {
    const parts = [s.pot_size_cm ? s.pot_size_cm + 'cm' : '', s.shape === 'square' ? 'eckig' : 'rund',
        s.weight ? s.weight + ' ' + (s.weight_unit || 'g') : '', (s.height_cm && s.width_cm) ? (s.height_cm + ' x ' + s.width_cm) : ''];
    return parts.filter(Boolean).join(' - ') || s.name;
}

function _specPopulateSelect(sel) {
    if (!sel) return;
    const cur = sel.value;
    sel.innerHTML = '<option value="">— keine Vorgabe —</option>' +
        _specs.map(s => `<option value="${s.id}">${escHtml(_specLabel(s))}</option>`).join('');
    sel.value = cur;
}

function _specSelected(id) {
    _bpMarkManual('specification_id');
    document.getElementById('h-specification-id').value = id || '';
    _specApplyById(id ? parseInt(id, 10) : null);
}

function _specApplyById(id) {
    document.getElementById('specification-select').value = id || '';
    document.getElementById('h-specification-id').value = id || '';
    const spec = _specs.find(s => s.id === id);
    if (!spec) return;
    if (spec.weight) {
        const kg = (spec.weight_unit === 'kg') ? spec.weight : spec.weight / 1000;
        document.getElementById('product-weight').value = kg;
    }
    if (spec.width_cm) { document.getElementById('product-length').value = spec.width_cm; document.getElementById('product-width').value = spec.width_cm; }
    if (spec.height_cm) document.getElementById('product-height').value = spec.height_cm;
    syncShippingDims();
}

function openSpecificationModal() {
    document.getElementById('spec-pot-size').value = '';
    document.getElementById('spec-shape').value = 'round';
    document.getElementById('spec-weight').value = '';
    document.getElementById('spec-weight-unit').value = 'g';
    document.getElementById('spec-height').value = '';
    document.getElementById('spec-width').value = '';
    document.getElementById('pa-spec-overlay').style.display = 'flex';
    setTimeout(() => document.getElementById('spec-pot-size').focus(), 40);
}

function closeSpecificationModal() { document.getElementById('pa-spec-overlay').style.display = 'none'; }

async function saveSpecificationModal() {
    const potSize = document.getElementById('spec-pot-size').value;
    const weight = document.getElementById('spec-weight').value;
    if (!potSize || !weight) { alert('Topfgröße und Gewicht sind Pflichtfelder'); return; }
    const heightCm = document.getElementById('spec-height').value;
    const widthCm = document.getElementById('spec-width').value;
    const shape = document.getElementById('spec-shape').value;
    const weightUnit = document.getElementById('spec-weight-unit').value;
    const name = _specLabel({ pot_size_cm: potSize, shape, weight, weight_unit: weightUnit, height_cm: heightCm, width_cm: widthCm });
    const data = await paAjax('pa_save_specification', {
        pot_size_cm: potSize, shape, weight, weight_unit: weightUnit, height_cm: heightCm, width_cm: widthCm, name
    });
    if (!data.success) { alert(data.data || 'Fehler beim Speichern'); return; }
    closeSpecificationModal();
    const saved = data.data;
    const idx = _specs.findIndex(s => s.id === saved.id);
    if (idx >= 0) _specs[idx] = saved; else _specs.push(saved);
    _specPopulateSelect(document.getElementById('specification-select'));
    _specPopulateSelect(document.getElementById('bp-specification'));
    if (document.getElementById('pa-bp-overlay').style.display === 'flex') {
        document.getElementById('bp-specification').value = saved.id;
    } else {
        _specSelected(saved.id);
    }
}

// ── Lieferzeit-Picker ─────────────────────────────────────────────────────
var _dts = [];

(async function initDeliveryTimes() {
    try {
        const data = await paAjax('pa_get_delivery_times');
        if (data.success) { _dts = data.data; _dtPopulateSelect(document.getElementById('delivery-time-select')); }
    } catch(e) {}
})();

function _dtPopulateSelect(sel) {
    if (!sel) return;
    const cur = sel.value;
    sel.innerHTML = '<option value="">— nicht angegeben —</option>' +
        _dts.map(t => `<option value="${escHtml(t.label)}">${escHtml(t.label)}</option>`).join('');
    sel.value = cur;
}

function openDeliveryTimeModal() {
    document.getElementById('dt-label').value = '';
    document.getElementById('dt-days-min').value = '';
    document.getElementById('dt-days-max').value = '';
    document.getElementById('pa-dt-overlay').style.display = 'flex';
    setTimeout(() => document.getElementById('dt-label').focus(), 40);
}

function closeDeliveryTimeModal() { document.getElementById('pa-dt-overlay').style.display = 'none'; }

async function saveDeliveryTimeModal() {
    const label = document.getElementById('dt-label').value.trim();
    if (!label) { document.getElementById('dt-label').focus(); return; }
    const data = await paAjax('pa_save_delivery_time', {
        label, days_min: document.getElementById('dt-days-min').value || 0, days_max: document.getElementById('dt-days-max').value || 0
    });
    if (!data.success) { alert(data.data || 'Fehler beim Speichern'); return; }
    closeDeliveryTimeModal();
    const idx = _dts.findIndex(t => t.id === data.data.id);
    if (idx >= 0) _dts[idx] = data.data; else _dts.push(data.data);
    _dtPopulateSelect(document.getElementById('delivery-time-select'));
    _dtPopulateSelect(document.getElementById('bp-delivery-time'));
    const targetSel = document.getElementById('pa-bp-overlay').style.display === 'flex' ? document.getElementById('bp-delivery-time') : document.getElementById('delivery-time-select');
    targetSel.value = label;
    if (targetSel.id === 'delivery-time-select') _bpMarkManual('delivery_time');
}

function renderTagPool() {
    const pool = tagPoolData;
    let html = '';

    // Variable type groups
    pool.variable_types.forEach(vt => {
        const vals = pool.variable_values[vt.name] || [];
        const cls  = vt.is_variation ? 'tag-chip tag-variation' : 'tag-chip';
        html += `<div class="tag-group">
            <p class="tag-group-label">
                <span>${escHtml(vt.name)}${vt.is_variation ? ' <span style="font-size:9px;color:#4a6fcc;">[Variation]</span>' : ''}</span>
                <button class="pool-del-btn" onclick="deletePoolTerm(${vt.term_id},'product_tag',event)" title="Typ + Werte löschen">×</button>
            </p>
            <div class="tag-chips">`;
        html += `<span class="${cls}" id="vtype-chip-${vt.term_id}" onclick="activateVariableInput(${vt.term_id}, '${escHtml(vt.name)}', ${vt.is_variation})">
                    + ${escHtml(vt.name)}...
                 </span>`;
        vals.forEach(v => {
            html += `<span class="${cls}" id="vtag-chip-${v.term_id}">
                        <span onclick="addVariableTag(${vt.term_id}, '${escHtml(vt.name)}', '${escHtml(v.name)}', ${vt.is_variation})">${escHtml(v.name)}</span>
                        <button class="pool-del-btn" onclick="deletePoolTerm(${v.term_id},'product_tag',event)" title="Löschen">×</button>
                     </span>`;
        });
        html += `<div id="vinput-${vt.term_id}" style="display:none;">
                     <div class="vtag-input-wrap">
                         <span style="font-size:12px;color:#666;">${escHtml(vt.name)}:</span>
                         <input type="text" id="vinput-val-${vt.term_id}" placeholder="Wert..." onkeydown="vtInputKey(event,${vt.term_id},'${escHtml(vt.name)}',${vt.is_variation})">
                         <button onclick="confirmVariableInput(${vt.term_id},'${escHtml(vt.name)}',${vt.is_variation})" title="Hinzufügen">✓</button>
                         <button onclick="cancelVariableInput(${vt.term_id})" title="Abbrechen">✕</button>
                     </div>
                 </div>`;
        html += `</div></div>`;
    });

    // Category group
    if (pool.categories.length) {
        html += `<div class="tag-group">
            <p class="tag-group-label">Kategorien</p>
            <div class="tag-chips">`;
        pool.categories.forEach(c => {
            html += `<span class="tag-chip tag-category" id="cat-chip-${c.term_id}">
                        <span onclick="toggleCategory(${c.term_id},'${escHtml(c.name)}')">${escHtml(c.name)}</span>
                        <button class="pool-del-btn" onclick="deletePoolTerm(${c.term_id},'product_cat',event)" title="Löschen">×</button>
                     </span>`;
        });
        html += `</div></div>`;
    }

    // Fixed tags group
    if (pool.fixed.length) {
        html += `<div class="tag-group">
            <p class="tag-group-label">Tags</p>
            <div class="tag-chips">`;
        pool.fixed.forEach(t => {
            html += `<span class="tag-chip" id="fixed-chip-${t.term_id}">
                        <span onclick="toggleFixed(${t.term_id},'${escHtml(t.name)}')">${escHtml(t.name)}</span>
                        <button class="pool-del-btn" onclick="deletePoolTerm(${t.term_id},'product_tag',event)" title="Löschen">×</button>
                     </span>`;
        });
        html += `</div></div>`;
    }

    if (!html) html = '<p style="color:#aaa; font-size:13px;">Noch keine Tags. Erstelle deinen ersten Tag.</p>';
    document.getElementById('tag-pool-wrap').innerHTML = html;
}

// ── Term aus Pool löschen ─────────────────────────────────────────────────────
async function deletePoolTerm(termId, taxonomy, ev) {
    if (ev) ev.stopPropagation();
    if (!confirm('Wirklich löschen? Der Eintrag wird von allen Produkten entfernt.')) return;
    try {
        const res  = await fetch(ajaxUrl, {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'action=pa_delete_term&nonce=' + addProductNonce
                + '&term_id=' + termId + '&taxonomy=' + taxonomy
        });
        const data = await res.json();
        if (!data.success) { alert('Fehler: ' + (data.data || 'Unbekannt')); return; }

        if (taxonomy === 'product_tag') {
            const vtIdx = tagPoolData.variable_types.findIndex(vt => vt.term_id === termId);
            if (vtIdx !== -1) {
                const typeName = tagPoolData.variable_types[vtIdx].name;
                tagPoolData.variable_types.splice(vtIdx, 1);
                delete tagPoolData.variable_values[typeName];
                delete selVariable[typeName];
            } else {
                const fIdx = tagPoolData.fixed.findIndex(t => t.term_id === termId);
                if (fIdx !== -1) { selFixed.delete(termId); tagPoolData.fixed.splice(fIdx, 1); }
                else {
                    for (const prefix in tagPoolData.variable_values) {
                        const vIdx = tagPoolData.variable_values[prefix].findIndex(v => v.term_id === termId);
                        if (vIdx !== -1) {
                            const deletedName = tagPoolData.variable_values[prefix][vIdx].name;
                            tagPoolData.variable_values[prefix].splice(vIdx, 1);
                            if (selVariable[prefix] === deletedName) delete selVariable[prefix];
                            break;
                        }
                    }
                }
            }
        } else {
            const cIdx = tagPoolData.categories.findIndex(c => c.term_id === termId);
            if (cIdx !== -1) { selCategories.delete(termId); tagPoolData.categories.splice(cIdx, 1); }
        }
        renderTagPool();
        updateHiddenFields();
        renderSelectedPills();
    } catch(e) { alert('Netzwerkfehler beim Löschen.'); }
}

// ── Toggle fixed tag ───────────────────────────────────────────────────────────
function toggleFixed(termId, name) {
    const chip = document.getElementById('fixed-chip-' + termId);
    if (selFixed.has(termId)) {
        selFixed.delete(termId);
        chip.classList.remove('tag-selected');
    } else {
        selFixed.add(termId);
        chip.classList.add('tag-selected');
    }
    updateHiddenFields();
    renderSelectedPills();
}

// ── Toggle category ────────────────────────────────────────────────────────────
function toggleCategory(termId, name) {
    const chip = document.getElementById('cat-chip-' + termId);
    if (selCategories.has(termId)) {
        selCategories.delete(termId);
        chip.classList.remove('tag-selected');
    } else {
        selCategories.add(termId);
        chip.classList.add('tag-selected');
    }
    updateHiddenFields();
    renderSelectedPills();
}

// ── Variable tag input ─────────────────────────────────────────────────────────
function activateVariableInput(typeId, prefix, isVariation) {
    document.getElementById('vinput-' + typeId).style.display = 'inline-flex';
    document.getElementById('vinput-val-' + typeId).focus();
}
function cancelVariableInput(typeId) {
    document.getElementById('vinput-' + typeId).style.display = 'none';
    document.getElementById('vinput-val-' + typeId).value = '';
}
function vtInputKey(e, typeId, prefix, isVariation) {
    if (e.key === 'Enter') { e.preventDefault(); confirmVariableInput(typeId, prefix, isVariation); }
    if (e.key === 'Escape') cancelVariableInput(typeId);
}
async function confirmVariableInput(typeId, prefix, isVariation) {
    const val = document.getElementById('vinput-val-' + typeId).value.trim();
    if (!val) return;
    cancelVariableInput(typeId);

    // Already in pool → just select
    const already = (tagPoolData.variable_values[prefix] || []).find(v => v.name === val);
    if (!already) {
        try {
            const res  = await fetch(ajaxUrl, {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'action=pa_create_tag&nonce=' + addProductNonce
                    + '&type=variable&prefix=' + encodeURIComponent(prefix)
                    + '&name=' + encodeURIComponent(val)
            });
            const data = await res.json();
            if (data.success) {
                if (!tagPoolData.variable_values[prefix]) tagPoolData.variable_values[prefix] = [];
                tagPoolData.variable_values[prefix].push({ term_id: data.data.term_id, name: val });
                renderTagPool();
            }
        } catch(e) {}
    }

    addVariableTag(typeId, prefix, val, isVariation);
}
function addVariableTag(typeId, prefix, value, isVariation) {
    // One value per prefix at a time (replace if already set)
    selVariable[prefix] = { value, isVariation: !!isVariation };
    updateHiddenFields();
    renderSelectedPills();
    updateVariantsSection();
}
function removeVariableTag(prefix) {
    delete selVariable[prefix];
    updateHiddenFields();
    renderSelectedPills();
    updateVariantsSection();
}

// ── Render selected pills ──────────────────────────────────────────────────────
function renderSelectedPills() {
    const area  = document.getElementById('selected-tags-area');
    const label = document.getElementById('no-tags-label');
    const total = selFixed.size + selCategories.size + Object.keys(selVariable).length;

    label.style.display = total ? 'none' : 'inline';
    // Remove old pills
    area.querySelectorAll('.selected-tag-pill').forEach(el => el.remove());

    selFixed.forEach(id => {
        const name = findTermName('fixed', id);
        area.appendChild(makePill(name, '', () => toggleFixed(id, name)));
    });
    selCategories.forEach(id => {
        const name = findTermName('category', id);
        area.appendChild(makePill(name, 'pill-category', () => toggleCategory(id, name)));
    });
    Object.entries(selVariable).forEach(([prefix, {value, isVariation}]) => {
        area.appendChild(makePill(prefix + ': ' + value, isVariation ? 'pill-variation' : '', () => removeVariableTag(prefix)));
    });
}

function makePill(label, extraClass, onRemove) {
    const pill = document.createElement('span');
    pill.className = 'selected-tag-pill' + (extraClass ? ' ' + extraClass : '');
    pill.innerHTML = escHtml(label) + '<button title="Entfernen">✕</button>';
    pill.querySelector('button').addEventListener('click', onRemove);
    return pill;
}

function findTermName(type, id) {
    if (type === 'fixed') {
        const t = tagPoolData.fixed.find(x => x.term_id === id);
        return t ? t.name : id;
    }
    if (type === 'category') {
        const t = tagPoolData.categories.find(x => x.term_id === id);
        return t ? t.name : id;
    }
    return id;
}

// ── Hidden field update ────────────────────────────────────────────────────────
function updateHiddenFields() {
    document.getElementById('h-fixed-tags').value   = [...selFixed].join(',');
    document.getElementById('h-categories').value   = [...selCategories].join(',');
    document.getElementById('h-variable-tags').value = JSON.stringify(
        Object.entries(selVariable).map(([prefix, {value}]) => ({ prefix, value }))
    );
}

// ── Variants section ───────────────────────────────────────────────────────────
let variantRows = []; // [{id, attributes:{prefix:value}, price:'', stock:''}]
let _variantIdSeq = 0;

function updateVariantsSection() {
    const hasVariation = Object.values(selVariable).some(v => v.isVariation);
    const sec = document.getElementById('variants-section');
    sec.style.display = hasVariation ? 'block' : 'none';
    if (!hasVariation) { variantRows = []; syncVariantsHidden(); return; }
    // Auto-add a row if empty
    if (!variantRows.length) addVariantRow();
    renderVariantRows();
}

function addVariantRow() {
    const variationAttrs = {};
    Object.entries(selVariable)
        .filter(([, v]) => v.isVariation)
        .forEach(([prefix, {value}]) => { variationAttrs[prefix] = value; });
    variantRows.push({ id: ++_variantIdSeq, attributes: { ...variationAttrs }, price: '', stock: '' });
    renderVariantRows();
}

function renderVariantRows() {
    const variationPrefixes = Object.entries(selVariable)
        .filter(([, v]) => v.isVariation)
        .map(([prefix]) => prefix);

    const table = document.getElementById('variants-table');
    if (!variantRows.length) { table.innerHTML = ''; syncVariantsHidden(); return; }

    table.innerHTML = variantRows.map(row => {
        const attrCols = variationPrefixes.map(prefix =>
            `<div>
                <div class="variant-attr-label">${escHtml(prefix)}</div>
                <input type="text" value="${escHtml(row.attributes[prefix] || '')}"
                       style="width:100%; padding:6px 8px; border:1px solid #ddd; border-radius:3px; font-size:13px; box-sizing:border-box;"
                       oninput="setVariantAttr(${row.id},'${prefix}',this.value)">
             </div>`
        ).join('');
        return `<div class="variant-row" style="grid-template-columns:${variationPrefixes.map(() => '1fr').join(' ')} 110px 80px 36px;">
            ${attrCols}
            <div>
                <div class="variant-attr-label">Preis (€)</div>
                <input type="number" step="0.01" min="0" value="${escHtml(row.price)}"
                       style="width:100%; padding:6px 8px; border:1px solid #ddd; border-radius:3px; font-size:13px; box-sizing:border-box;"
                       oninput="setVariantField(${row.id},'price',this.value)">
            </div>
            <div>
                <div class="variant-attr-label">Bestand</div>
                <input type="number" min="0" value="${escHtml(row.stock)}"
                       style="width:100%; padding:6px 8px; border:1px solid #ddd; border-radius:3px; font-size:13px; box-sizing:border-box;"
                       oninput="setVariantField(${row.id},'stock',this.value)">
            </div>
            <div style="padding-top:18px;">
                <button type="button" onclick="removeVariantRow(${row.id})"
                        style="padding:6px 8px; background:#f5f5f5; border:1px solid #ddd; border-radius:3px; cursor:pointer; font-size:13px; color:#999;">✕</button>
            </div>
        </div>`;
    }).join('');

    syncVariantsHidden();
}

function setVariantAttr(id, prefix, val) {
    const row = variantRows.find(r => r.id === id);
    if (row) { row.attributes[prefix] = val; syncVariantsHidden(); }
}
function setVariantField(id, field, val) {
    const row = variantRows.find(r => r.id === id);
    if (row) { row[field] = val; syncVariantsHidden(); }
}
function removeVariantRow(id) {
    variantRows = variantRows.filter(r => r.id !== id);
    renderVariantRows();
}
function syncVariantsHidden() {
    document.getElementById('h-variants').value = JSON.stringify(
        variantRows.map(({attributes, price, stock}) => ({ attributes, price, stock }))
    );
}

// ── Tag create popup ───────────────────────────────────────────────────────────
function openTagCreatePopup() {
    document.getElementById('tag-create-overlay').classList.add('open');
    document.getElementById('tc-name').focus();
    document.getElementById('tc-msg').style.display = 'none';
}
function closeTagCreatePopup() {
    document.getElementById('tag-create-overlay').classList.remove('open');
    document.getElementById('tc-name').value = '';
    document.getElementById('tc-type').value = 'fixed';
    document.getElementById('tc-is-variation').checked = false;
    document.getElementById('tc-variation-row').style.display = 'none';
    document.getElementById('tc-msg').style.display = 'none';
}
function onTagTypeChange(sel) {
    document.getElementById('tc-variation-row').style.display =
        sel.value === 'variable_type' ? 'flex' : 'none';
}

async function createTag() {
    const name = document.getElementById('tc-name').value.trim();
    const type = document.getElementById('tc-type').value;
    const isVariation = type === 'variable_type' && document.getElementById('tc-is-variation').checked ? 1 : 0;
    if (!name) { showTcMsg('Bitte Namen eingeben.', false); return; }

    const res  = await fetch(ajaxUrl, {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'action=pa_create_tag&nonce=' + addProductNonce +
              '&name=' + encodeURIComponent(name) +
              '&type=' + type +
              '&is_variation=' + isVariation
    });
    const data = await res.json();
    if (!data.success) { showTcMsg('Fehler: ' + data.data, false); return; }

    // Inject into pool data + re-render
    const t = data.data;
    if (t.type === 'category') {
        tagPoolData.categories.push({ term_id: t.term_id, name: t.name });
    } else if (t.type === 'variable_type') {
        tagPoolData.variable_types.push({ term_id: t.term_id, name: t.name, is_variation: !!isVariation });
        if (!tagPoolData.variable_values[t.name]) tagPoolData.variable_values[t.name] = [];
    } else {
        tagPoolData.fixed.push({ term_id: t.term_id, name: t.name });
    }
    renderTagPool();
    showTcMsg('✓ Tag erstellt!', true);
    setTimeout(closeTagCreatePopup, 800);
}

function showTcMsg(text, ok) {
    const el = document.getElementById('tc-msg');
    el.textContent = text;
    el.style.color = ok ? '#1e7e34' : '#c0392b';
    el.style.display = 'block';
}

function resetTagSelection() {
    selFixed.clear();
    selCategories.clear();
    for (const k in selVariable) delete selVariable[k];
    variantRows = [];
    updateHiddenFields();
    renderSelectedPills();
    document.getElementById('variants-section').style.display = 'none';
    // Re-render pool to remove selected states
    renderTagPool();
}
</script>

<!-- Crop & Watermark Modal -->
<div id="cwm-overlay">
    <div id="cwm-dialog">
        <h3>Bild zuschneiden & Wasserzeichen</h3>
        <div class="cwm-body">
            <canvas id="cwm-canvas" width="480" height="480"></canvas>
            <div class="cwm-controls">
                <p class="cwm-ctrl-label">Zoom</p>
                <input type="range" id="cwm-zoom" min="100" max="500" value="100" step="1"
                       oninput="cwmZoomSlider(this.value)">
                <p style="font-size:12px;color:#aaa;margin:4px 0 0;">
                    Scroll oder Slider zum Zoomen<br>Ziehen zum Verschieben
                </p>

                <p class="cwm-ctrl-label">Wasserzeichen</p>
                <div class="cwm-checkbox-row">
                    <input type="checkbox" id="cwm-show-wm" checked onchange="cwmRender()">
                    <label for="cwm-show-wm">Anzeigen</label>
                </div>
                <div class="cwm-checkbox-row">
                    <input type="checkbox" id="cwm-invert-wm" onchange="cwmRender()">
                    <label for="cwm-invert-wm">Farben invertieren</label>
                </div>
                <p class="cwm-hint">Wasserzeichen ziehen zum Verschieben.<br>Ecken ziehen zum Vergrößern/Verkleinern.</p>

                <p class="cwm-ctrl-label">Rechteckiges Bild</p>
                <div class="cwm-checkbox-row">
                    <input type="checkbox" id="cwm-rect-enable" onchange="cwmToggleRect()">
                    <label for="cwm-rect-enable">Aktivieren</label>
                </div>
                <div id="cwm-rect-controls" style="display:none;">
                    <div style="display:flex; align-items:center; gap:8px; margin-bottom:8px;">
                        <span style="font-size:13px;" id="cwm-orient-label-h" style="font-weight:600;">Horizontal</span>
                        <label class="switch" style="width:40px; height:20px;">
                            <input type="checkbox" id="cwm-rect-toggle" onchange="cwmToggleOrientation()">
                            <span class="slider"></span>
                        </label>
                        <span style="font-size:13px;" id="cwm-orient-label-v">Vertikal</span>
                    </div>
                    <p class="cwm-hint">Balken an den Kanten zum Einziehen ziehen.<br>Transparente Bereiche → PNG-Export.</p>
                </div>

                <div class="cwm-btns">
                    <button type="button" class="btn-primary" onclick="cwmSave()" id="cwm-save-btn">Speichern</button>
                    <button type="button" class="btn-secondary" onclick="cwmCancel()">Abbrechen</button>
                </div>
                <div id="cwm-upload-progress">Wird hochgeladen...</div>
            </div>
        </div>
    </div>
</div>

<?php get_footer(); ?>
