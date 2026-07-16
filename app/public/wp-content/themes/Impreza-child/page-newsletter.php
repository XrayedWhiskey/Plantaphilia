<?php
/**
 * Template Name: Newsletter (Admin)
 */
get_header();

if (!current_user_can('manage_options')) {
    echo '<div style="padding:40px;text-align:center;"><h2>Zugriff verweigert</h2><a href="' . home_url() . '" class="button">Startseite</a></div>';
    get_footer(); exit;
}
?>
<style>
/* ── Reset / Base ──────────────────────────────────────────────────────────── */
#nl-page { display:flex; flex-direction:column; height:calc(100vh - 80px); min-height:600px; font-family:'Montserrat',Arial,sans-serif; background:var(--bg-deep); }
#nl-topbar { display:flex; align-items:center; gap:14px; padding:12px 20px; background:var(--bg-surface); border-bottom:1px solid var(--border-thin); flex-shrink:0; flex-wrap:wrap; }
#nl-topbar h1 { margin:0; font-size:17px; font-weight:700; color:var(--creme); white-space:nowrap; }
#nl-subject { flex:1; min-width:180px; padding:8px 12px; border:1px solid var(--border-thin); border-radius:2px; font-size:14px; font-family:inherit; background:var(--bg-deep); color:var(--creme); }
#nl-subject:focus { outline:none; border-color:var(--plum); }
.nl-toggle-wrap { display:flex; align-items:center; gap:8px; font-size:13px; white-space:nowrap; }
#nl-toggle-all   { font-weight:600; color:var(--creme); }
#nl-toggle-sub   { color:var(--creme-dim); }
.nl-toggle-wrap.subscribers #nl-toggle-all { font-weight:400; color:var(--creme-dim); }
.nl-toggle-wrap.subscribers #nl-toggle-sub { font-weight:600; color:var(--creme); }
#nl-recipient-count { font-size:12px; color:var(--creme-dim); background:var(--bg-raised); padding:3px 10px; border-radius:10px; }
#nl-send-btn { padding:9px 20px; background:var(--plum); color:var(--creme); border:none; border-radius:2px; cursor:pointer; font-size:11px; font-weight:700; letter-spacing:0.14em; text-transform:uppercase; white-space:nowrap; font-family:inherit; }
#nl-send-btn:hover { background:var(--plum-hot); }
#nl-send-btn:disabled { opacity:.5; cursor:default; }

/* ── Body (sidebar + editor) ─────────────────────────────────────────────── */
#nl-body { display:flex; flex:1; min-height:0; }

/* ── Sidebar ─────────────────────────────────────────────────────────────── */
#nl-sidebar { width:270px; min-width:220px; background:var(--bg-surface); border-right:1px solid var(--border-thin); display:flex; flex-direction:column; flex-shrink:0; }
#nl-sidebar-hdr { padding:14px 16px 10px; border-bottom:1px solid var(--border-hair); flex-shrink:0; }
#nl-sidebar-hdr h3 { margin:0 0 8px; font-size:13px; font-weight:700; color:var(--creme); text-transform:uppercase; letter-spacing:.5px; }
#nl-sidebar-search { width:100%; padding:7px 10px; border:1px solid var(--border-thin); border-radius:2px; font-size:13px; box-sizing:border-box; font-family:inherit; background:var(--bg-deep); color:var(--creme); }
#nl-sidebar-search:focus { outline:none; border-color:var(--plum); }
#nl-categories { flex:1; overflow-y:auto; padding:6px 0; }
.nl-cat { border-bottom:1px solid var(--border-hair); }
.nl-cat-hdr { display:flex; align-items:center; gap:8px; padding:10px 16px; cursor:pointer; user-select:none; font-size:13px; font-weight:600; color:var(--creme); }
.nl-cat-hdr:hover { background:var(--bg-raised); }
.nl-cat-arrow { font-size:10px; color:var(--creme-muted); transition:transform .2s; }
.nl-cat.open .nl-cat-arrow { transform:rotate(90deg); }
.nl-cat-count { margin-left:auto; font-size:11px; color:var(--creme-muted); font-weight:400; }
.nl-cat-items { display:none; padding:0 8px 6px; }
.nl-cat.open .nl-cat-items { display:block; }
.nl-item { display:flex; align-items:center; gap:8px; padding:6px 8px; border-radius:2px; cursor:grab; user-select:none; font-size:12px; color:var(--creme); }
.nl-item:hover { background:var(--bg-raised); }
.nl-item:active { cursor:grabbing; }
.nl-item.dragging { opacity:.4; }
.nl-item-thumb { width:36px; height:36px; object-fit:cover; border-radius:2px; flex-shrink:0; background:var(--bg-raised); }
.nl-item-thumb-icon { width:36px; height:36px; background:var(--bg-raised); border-radius:2px; display:flex; align-items:center; justify-content:center; font-size:18px; flex-shrink:0; }
.nl-item-info { flex:1; min-width:0; }
.nl-item-name { font-weight:600; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; display:block; }
.nl-item-sub  { color:var(--creme-dim); font-size:11px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; display:block; }
.nl-drag-handle { color:var(--creme-muted); font-size:14px; flex-shrink:0; }
#nl-sidebar-empty { text-align:center; color:var(--creme-muted); font-size:13px; padding:30px 16px; display:none; }

/* ── Editor ──────────────────────────────────────────────────────────────── */
#nl-editor-wrap { flex:1; overflow-y:auto; padding:24px; display:flex; flex-direction:column; align-items:center; }
#nl-blocks { width:100%; max-width:640px; }
#nl-add-text-btn { width:100%; max-width:640px; margin-top:10px; padding:10px; background:var(--bg-surface); border:2px dashed var(--border-thin); border-radius:2px; cursor:pointer; font-size:13px; color:var(--creme-dim); font-family:inherit; }
#nl-add-text-btn:hover { border-color:var(--plum); color:var(--creme); }

/* ── Drop zones ──────────────────────────────────────────────────────────── */
.nl-dropzone { height:20px; border-radius:2px; transition:height .15s, background .15s; position:relative; }
.nl-dropzone.drag-over { height:44px; background:var(--bg-raised); border:2px dashed var(--plum); }
.nl-dropzone.drag-over::after { content:'Hier einfügen'; position:absolute; inset:0; display:flex; align-items:center; justify-content:center; font-size:12px; color:var(--plum); font-weight:600; }

/* ── Blocks ──────────────────────────────────────────────────────────────── */
.nl-block { background:var(--bg-surface); border-radius:2px; box-shadow:0 1px 4px rgba(0,0,0,.08); margin-bottom:2px; position:relative; border:1px solid var(--border-thin); }
.nl-block-delete { position:absolute; top:8px; right:8px; width:24px; height:24px; background:var(--bg-raised); border:none; border-radius:50%; cursor:pointer; font-size:13px; color:var(--creme-dim); display:flex; align-items:center; justify-content:center; z-index:2; }
.nl-block-delete:hover { background:var(--plum-hot); color:var(--creme); }

/* Text block */
.nl-block-text { padding:0; }
.nl-text-toolbar { display:flex; gap:3px; flex-wrap:wrap; padding:6px 8px; border-bottom:1px solid var(--border-hair); background:var(--bg-raised); border-radius:2px 2px 0 0; }
.nl-text-toolbar button { padding:3px 7px; background:none; border:1px solid transparent; border-radius:2px; cursor:pointer; font-size:13px; color:var(--creme); font-family:inherit; }
.nl-text-toolbar button:hover { background:var(--bg-surface); border-color:var(--border-thin); }
.nl-text-body { padding:12px 14px; min-height:60px; font-size:14px; line-height:1.7; color:var(--creme); outline:none; border-radius:0 0 2px 2px; }
.nl-text-body:empty::before { content:attr(data-placeholder); color:var(--creme-muted); pointer-events:none; }

/* Product block */
.nl-block-product { padding:16px; }
.nl-product-preview { display:flex; gap:14px; align-items:flex-start; }
.nl-product-img { width:90px; height:90px; object-fit:cover; border-radius:2px; flex-shrink:0; background:var(--bg-raised); }
.nl-product-img-placeholder { width:90px; height:90px; background:var(--bg-raised); border-radius:2px; flex-shrink:0; display:flex; align-items:center; justify-content:center; font-size:28px; }
.nl-product-info { flex:1; }
.nl-product-name { font-size:15px; font-weight:700; color:var(--creme); margin:0 0 4px; }
.nl-product-excerpt { font-size:12px; color:var(--creme-dim); margin:0 0 8px; line-height:1.5; }
.nl-product-price { font-size:16px; font-weight:700; color:var(--creme); margin:0 0 8px; }
.nl-product-price .was { font-size:12px; color:var(--creme-muted); text-decoration:line-through; font-weight:400; margin-right:4px; }
.nl-product-price .now { color:var(--plum-hot); }
.nl-discount-badge { background:var(--plum-hot); color:var(--creme); padding:2px 7px; border-radius:10px; font-size:11px; font-weight:700; margin-left:5px; }
.nl-product-link { display:inline-block; padding:7px 16px; background:var(--plum); color:var(--creme); text-decoration:none; border-radius:2px; font-size:12px; font-weight:600; }

/* Sale block */
.nl-block-sale { padding:16px; }
.nl-sale-title { font-size:16px; font-weight:700; color:var(--creme); margin:0 0 12px; display:flex; align-items:center; gap:8px; }
.nl-sale-grid { display:grid; grid-template-columns:repeat(auto-fill, minmax(130px, 1fr)); gap:10px; }
.nl-sale-card { border:1px solid var(--border-thin); border-radius:2px; overflow:hidden; font-size:12px; }
.nl-sale-card img { width:100%; aspect-ratio:1; object-fit:cover; display:block; }
.nl-sale-card-info { padding:8px; }
.nl-sale-card-name { font-weight:600; color:var(--creme); margin-bottom:4px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.nl-sale-card-was  { color:var(--creme-muted); text-decoration:line-through; font-size:11px; }
.nl-sale-card-price { font-weight:700; color:var(--plum-hot); font-size:13px; }

/* ── Send modal ──────────────────────────────────────────────────────────── */
#nl-send-modal { display:none; position:fixed; inset:0; background:rgba(0,0,0,.5); z-index:99999; align-items:center; justify-content:center; }
#nl-send-modal.open { display:flex; }
#nl-send-dialog { background:var(--bg-surface); border-radius:8px; padding:30px; max-width:420px; width:92%; box-shadow:0 8px 40px rgba(0,0,0,.25); border:1px solid var(--border-thin); }
#nl-send-dialog h3 { margin:0 0 16px; font-size:17px; color:var(--creme); }
#nl-send-dialog p { margin:0 0 12px; font-size:14px; color:var(--creme-dim); line-height:1.5; }
#nl-send-result { margin-top:12px; font-size:14px; color:var(--creme); }
.nl-modal-btns { display:flex; gap:10px; margin-top:20px; }
.nl-modal-btns button { flex:1; padding:11px; border:none; border-radius:2px; cursor:pointer; font-size:11px; font-weight:700; letter-spacing:0.14em; text-transform:uppercase; font-family:inherit; }
#nl-confirm-btn  { background:var(--plum); color:var(--creme); }
#nl-confirm-btn:hover { background:var(--plum-hot); }
#nl-confirm-btn:disabled { opacity:.5; cursor:default; }
#nl-cancel-btn  { background:var(--bg-raised); color:var(--creme); border:1px solid var(--border-thin); }

/* ── Switch (reuse from other pages) ────────────────────────────────────── */
.switch { position:relative; display:inline-block; width:44px; height:22px; }
.switch input { opacity:0; width:0; height:0; }
.switch .slider { position:absolute; cursor:pointer; inset:0; background:var(--bg-inky); transition:.3s; border-radius:22px; border:1px solid var(--border-thin); }
.switch .slider:before { position:absolute; content:""; height:16px; width:16px; left:3px; bottom:3px; background:var(--creme-dim); transition:.3s; border-radius:50%; }
.switch input:checked + .slider { background:var(--plum); border-color:var(--plum-hot); }
.switch input:checked + .slider:before { transform:translateX(22px); background:var(--creme); }
</style>

<div id="nl-page">

  <!-- Top bar -->
  <div id="nl-topbar">
    <h1>📧 Newsletter</h1>
    <input type="text" id="nl-subject" placeholder="Betreff des Newsletters…">
    <div class="nl-toggle-wrap" id="nl-toggle-wrap">
      <span id="nl-toggle-all">Alle Kunden</span>
      <label class="switch"><input type="checkbox" id="nl-subscribers-only"><span class="slider"></span></label>
      <span id="nl-toggle-sub">Newsletter-Abonnenten</span>
      <span id="nl-recipient-count">… Empfänger</span>
    </div>
    <button id="nl-save-btn" onclick="saveDraft()" style="padding:9px 16px;background:var(--bg-raised);color:var(--creme);border:1px solid var(--border-thin);border-radius:2px;cursor:pointer;font-size:11px;font-weight:700;letter-spacing:0.14em;text-transform:uppercase;white-space:nowrap;font-family:inherit;">Entwurf speichern</button>
    <span id="nl-save-status" style="font-size:12px;color:var(--creme-muted);"></span>
    <button id="nl-send-btn" onclick="openSendModal()">Newsletter senden</button>
  </div>

  <!-- Body -->
  <div id="nl-body">

    <!-- Sidebar -->
    <div id="nl-sidebar">
      <div id="nl-sidebar-hdr">
        <h3>Produkte</h3>
        <input type="text" id="nl-sidebar-search" placeholder="Suchen…" oninput="filterSidebar(this.value)">
      </div>
      <div id="nl-categories">
        <div id="nl-sidebar-empty">Keine Einträge gefunden.</div>
        <div id="nl-categories-inner" style="color:#bbb;font-size:13px;padding:20px;text-align:center;">Laden…</div>
      </div>
    </div>

    <!-- Editor -->
    <div id="nl-editor-wrap">
      <div id="nl-blocks"></div>
      <button id="nl-add-text-btn" onclick="addTextBlock()">+ Text-Block hinzufügen</button>
    </div>

  </div>
</div>

<!-- Send modal -->
<div id="nl-send-modal">
  <div id="nl-send-dialog">
    <h3>Newsletter versenden</h3>
    <p id="nl-send-info">Soll der Newsletter an <strong id="nl-modal-count">?</strong> Empfänger gesendet werden?</p>
    <p style="font-size:12px;color:#aaa;">Diese Aktion kann nicht rückgängig gemacht werden.</p>
    <div id="nl-send-result"></div>
    <div class="nl-modal-btns">
      <button id="nl-confirm-btn" onclick="confirmSend()">Senden</button>
      <button id="nl-cancel-btn" onclick="closeSendModal()">Abbrechen</button>
    </div>
  </div>
</div>

<script>
var nlAjax   = '<?php echo admin_url('admin-ajax.php'); ?>';
var nlNonce  = '<?php echo wp_create_nonce('newsletter_nonce'); ?>';
var nlBlocks = [];
var nlCategories = []; // raw sidebar data from server
var nlDragData   = null; // currently dragged block data

// ── Init ──────────────────────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', function() {
    // Start with one empty text block (will be overwritten if draft exists)
    nlBlocks = [{ type: 'text', content: '' }];
    renderBlocks();
    loadSidebar();
    fetchRecipientCount();
    loadDraft();
    document.getElementById('nl-subscribers-only').addEventListener('change', function() {
        var wrap = document.getElementById('nl-toggle-wrap');
        wrap.classList.toggle('subscribers', this.checked);
        fetchRecipientCount();
    });
});

// ── Recipient count ───────────────────────────────────────────────────────────
function fetchRecipientCount() {
    var sub = document.getElementById('nl-subscribers-only').checked ? 1 : 0;
    fetch(nlAjax, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'action=get_newsletter_recipient_count&nonce=' + nlNonce + '&subscribers_only=' + sub
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        var count = (data.success && data.data) ? data.data.count : '?';
        document.getElementById('nl-recipient-count').textContent = count + ' Empfänger';
        document.getElementById('nl-modal-count').textContent = count;
    })
    .catch(function() {});
}

// ── Sidebar loading ───────────────────────────────────────────────────────────
function loadSidebar() {
    fetch(nlAjax, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'action=get_newsletter_products&nonce=' + nlNonce
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        var inner = document.getElementById('nl-categories-inner');
        if (!data.success || !data.data.length) {
            inner.textContent = 'Keine Produkte gefunden.';
            return;
        }
        nlCategories = data.data;
        inner.innerHTML = '';
        renderSidebar(nlCategories);
    })
    .catch(function() {
        document.getElementById('nl-categories-inner').textContent = 'Fehler beim Laden.';
    });
}

function renderSidebar(categories) {
    var inner = document.getElementById('nl-categories-inner');
    inner.innerHTML = '';
    var hasAny = false;
    categories.forEach(function(cat) {
        if (!cat.items.length) return;
        hasAny = true;
        var div = document.createElement('div');
        div.className = 'nl-cat';
        div.dataset.catKey = cat.key;
        div.innerHTML = '<div class="nl-cat-hdr" onclick="toggleCat(this)">'
            + '<span class="nl-cat-arrow">▶</span>'
            + '<span>' + escHtml(cat.label) + '</span>'
            + '<span class="nl-cat-count">' + cat.items.length + '</span>'
            + '</div>'
            + '<div class="nl-cat-items"></div>';
        var itemsDiv = div.querySelector('.nl-cat-items');
        cat.items.forEach(function(item) {
            itemsDiv.appendChild(buildSidebarItem(item));
        });
        inner.appendChild(div);
    });
    document.getElementById('nl-sidebar-empty').style.display = hasAny ? 'none' : 'block';
    // Open first category by default
    var first = inner.querySelector('.nl-cat');
    if (first) first.classList.add('open');
}

function buildSidebarItem(item) {
    var div = document.createElement('div');
    div.className = 'nl-item';
    div.draggable = true;
    div.dataset.itemKey = item.key;

    var thumbHtml = '';
    if (item.type === 'sale' || item.type === 'sale_group') {
        thumbHtml = '<div class="nl-item-thumb-icon">🏷</div>';
    } else if (item.thumb) {
        thumbHtml = '<img class="nl-item-thumb" src="' + escHtml(item.thumb) + '" alt="">';
    } else {
        thumbHtml = '<div class="nl-item-thumb-icon">🌿</div>';
    }

    div.innerHTML = thumbHtml
        + '<div class="nl-item-info">'
        +   '<span class="nl-item-name">' + escHtml(item.label) + '</span>'
        +   '<span class="nl-item-sub">' + escHtml(item.sub || '') + (item.product_count ? ' · ' + item.product_count + ' Produkte' : '') + '</span>'
        + '</div>'
        + '<span class="nl-drag-handle">⠿</span>';

    div.addEventListener('dragstart', function(e) {
        nlDragData = item.block_data;
        e.dataTransfer.effectAllowed = 'copy';
        e.dataTransfer.setData('text/plain', '');
        this.classList.add('dragging');
    });
    div.addEventListener('dragend', function() {
        this.classList.remove('dragging');
    });
    return div;
}

function toggleCat(hdr) {
    hdr.closest('.nl-cat').classList.toggle('open');
}

function filterSidebar(q) {
    q = q.toLowerCase().trim();
    if (!q) { renderSidebar(nlCategories); return; }
    var filtered = nlCategories.map(function(cat) {
        return Object.assign({}, cat, {
            items: cat.items.filter(function(i) {
                return i.label.toLowerCase().includes(q) || (i.sub || '').toLowerCase().includes(q);
            })
        });
    }).filter(function(cat) { return cat.items.length > 0; });
    renderSidebar(filtered);
    document.querySelectorAll('.nl-cat').forEach(function(c) { c.classList.add('open'); });
}

// ── Block rendering ───────────────────────────────────────────────────────────
function renderBlocks() {
    var container = document.getElementById('nl-blocks');
    var html = buildDropZone(0);
    for (var i = 0; i < nlBlocks.length; i++) {
        html += buildBlockHtml(nlBlocks[i], i);
        html += buildDropZone(i + 1);
    }
    container.innerHTML = html;
    bindDropZones();
    bindTextBodies();
}

function buildDropZone(idx) {
    return '<div class="nl-dropzone" data-idx="' + idx + '"></div>';
}

function buildBlockHtml(block, idx) {
    var del = '<button class="nl-block-delete" onclick="deleteBlock(' + idx + ')" title="Block löschen">✕</button>';
    if (block.type === 'text') {
        return '<div class="nl-block nl-block-text">'
            + del
            + buildTextToolbar(idx)
            + '<div class="nl-text-body" contenteditable="true" data-idx="' + idx + '" data-placeholder="Text eingeben…">'
            + (block.content || '') + '</div></div>';
    } else if (block.type === 'product') {
        return '<div class="nl-block nl-block-product">' + del + buildProductPreview(block.data) + '</div>';
    } else if (block.type === 'sale' || block.type === 'sale_group') {
        return '<div class="nl-block nl-block-sale">' + del + buildSalePreview(block.data) + '</div>';
    }
    return '';
}

function buildTextToolbar(idx) {
    var cmds = [
        ['bold','<b>B</b>'],['italic','<i>I</i>'],['underline','<u>U</u>'],
        ['strikeThrough','<s>S</s>'],['insertUnorderedList','• Liste'],['insertOrderedList','1. Liste'],
        ['|'], ['h2','H2'],['h3','H3'],
        ['|'], ['justifyLeft','⬅'],['justifyCenter','↔'],['justifyRight','➡'],
        ['link','🔗'],['removeFormat','∅'],
    ];
    var btns = '';
    cmds.forEach(function(c) {
        if (c[0] === '|') { btns += '<span style="width:1px;background:#e0e0e0;margin:0 3px;align-self:stretch;display:inline-block;"></span>'; return; }
        if (c[0] === 'h2' || c[0] === 'h3') {
            btns += '<button onmousedown="event.preventDefault()" onclick="nlHeading(\'' + c[0] + '\')">' + c[1] + '</button>';
        } else if (c[0] === 'link') {
            btns += '<button onmousedown="event.preventDefault()" onclick="nlLink()">' + c[1] + '</button>';
        } else {
            btns += '<button onmousedown="event.preventDefault()" onclick="nlCmd(\'' + c[0] + '\')">' + c[1] + '</button>';
        }
    });
    return '<div class="nl-text-toolbar">' + btns + '</div>';
}

function buildProductPreview(d) {
    if (!d) return '';
    var imgHtml = d.image_url
        ? '<img class="nl-product-img" src="' + escHtml(d.image_url) + '" alt="">'
        : '<div class="nl-product-img-placeholder">🌿</div>';
    var priceHtml = '';
    if (d.sale_price) {
        priceHtml = '<span class="was">' + fmtPrice(d.price) + '</span>'
            + '<span class="now">' + fmtPrice(d.sale_price) + '</span>';
        if (d.discount) priceHtml += '<span class="nl-discount-badge">' + escHtml(d.discount) + '</span>';
    } else {
        priceHtml = fmtPrice(d.price);
    }
    return '<div class="nl-product-preview">'
        + imgHtml
        + '<div class="nl-product-info">'
        +   '<p class="nl-product-name">' + escHtml(d.name || '') + '</p>'
        +   '<p class="nl-product-excerpt">' + escHtml(d.excerpt || '') + '</p>'
        +   '<p class="nl-product-price">' + priceHtml + '</p>'
        +   '<a class="nl-product-link" href="' + escHtml(d.product_url || '') + '" target="_blank">Jetzt ansehen →</a>'
        + '</div></div>';
}

function buildSalePreview(d) {
    if (!d) return '';
    var title = escHtml(d.title || d.sale_title || 'Sale');
    var discBadge = d.discount ? '<span class="nl-discount-badge">' + escHtml(d.discount) + '</span>' : '';
    var cards = '';
    (d.products || []).forEach(function(p) {
        var img = p.image_url ? '<img src="' + escHtml(p.image_url) + '" alt="">' : '<div style="background:#eee;aspect-ratio:1;display:flex;align-items:center;justify-content:center;font-size:24px;">🌿</div>';
        var price = p.sale_price
            ? '<div class="nl-sale-card-was">' + fmtPrice(p.price) + '</div><div class="nl-sale-card-price">' + fmtPrice(p.sale_price) + (p.discount ? ' ' + escHtml(p.discount) : '') + '</div>'
            : '<div class="nl-sale-card-price">' + fmtPrice(p.price) + '</div>';
        cards += '<div class="nl-sale-card">' + img + '<div class="nl-sale-card-info"><div class="nl-sale-card-name">' + escHtml(p.name || '') + '</div>' + price + '</div></div>';
    });
    return '<div class="nl-sale-title">🏷 ' + title + ' ' + discBadge + '</div>'
        + '<div class="nl-sale-grid">' + cards + '</div>';
}

// ── Drag and drop (sidebar → editor) ─────────────────────────────────────────
function bindDropZones() {
    document.querySelectorAll('.nl-dropzone').forEach(function(dz) {
        dz.addEventListener('dragover', function(e) {
            if (!nlDragData) return;
            e.preventDefault(); e.dataTransfer.dropEffect = 'copy';
            this.classList.add('drag-over');
        });
        dz.addEventListener('dragleave', function(e) {
            if (!e.relatedTarget || !this.contains(e.relatedTarget)) this.classList.remove('drag-over');
        });
        dz.addEventListener('drop', function(e) {
            e.preventDefault();
            this.classList.remove('drag-over');
            if (!nlDragData) return;
            var idx = parseInt(this.dataset.idx);
            nlBlocks.splice(idx, 0, JSON.parse(JSON.stringify(nlDragData)));
            nlDragData = null;
            renderBlocks();
        });
    });
}

function bindTextBodies() {
    document.querySelectorAll('.nl-text-body').forEach(function(el) {
        el.addEventListener('input', function() {
            var idx = parseInt(this.dataset.idx);
            if (nlBlocks[idx]) nlBlocks[idx].content = this.innerHTML;
        });
    });
}

// ── Block actions ─────────────────────────────────────────────────────────────
function deleteBlock(idx) {
    nlBlocks.splice(idx, 1);
    renderBlocks();
}

function addTextBlock() {
    nlBlocks.push({ type: 'text', content: '' });
    renderBlocks();
    // Focus new text body
    var bodies = document.querySelectorAll('.nl-text-body');
    if (bodies.length) bodies[bodies.length - 1].focus();
}

// ── Text editor commands ──────────────────────────────────────────────────────
function nlCmd(cmd) { document.execCommand(cmd, false, null); }
function nlHeading(tag) { document.execCommand('formatBlock', false, tag); }
function nlLink() {
    var url = prompt('URL:', 'https://');
    if (url) document.execCommand('createLink', false, url);
}

// ── Sync all text blocks before sending ──────────────────────────────────────
function syncAllTextBlocks() {
    document.querySelectorAll('.nl-text-body').forEach(function(el) {
        var idx = parseInt(el.dataset.idx);
        if (nlBlocks[idx]) nlBlocks[idx].content = el.innerHTML;
    });
}

// ── Send modal ────────────────────────────────────────────────────────────────
function openSendModal() {
    var subject = document.getElementById('nl-subject').value.trim();
    if (!subject) { alert('Bitte einen Betreff eingeben.'); return; }
    syncAllTextBlocks();
    fetchRecipientCount();
    document.getElementById('nl-send-result').textContent = '';
    document.getElementById('nl-confirm-btn').disabled = false;
    document.getElementById('nl-confirm-btn').textContent = 'Senden';
    document.getElementById('nl-cancel-btn').style.display = '';
    document.getElementById('nl-send-modal').classList.add('open');
}

function closeSendModal() {
    document.getElementById('nl-send-modal').classList.remove('open');
}

function confirmSend() {
    var subject = document.getElementById('nl-subject').value.trim();
    var subscribersOnly = document.getElementById('nl-subscribers-only').checked ? 1 : 0;
    syncAllTextBlocks();
    var btn = document.getElementById('nl-confirm-btn');
    btn.disabled = true;
    btn.textContent = 'Wird gesendet…';
    document.getElementById('nl-cancel-btn').style.display = 'none';
    document.getElementById('nl-send-result').textContent = '';

    fetch(nlAjax, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'action=send_newsletter&nonce=' + nlNonce
            + '&subject=' + encodeURIComponent(subject)
            + '&subscribers_only=' + subscribersOnly
            + '&blocks=' + encodeURIComponent(JSON.stringify(nlBlocks))
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        btn.textContent = 'Gesendet';
        document.getElementById('nl-cancel-btn').style.display = '';
        document.getElementById('nl-cancel-btn').textContent = 'Schließen';
        if (data.success) {
            var res = data.data;
            document.getElementById('nl-send-result').innerHTML =
                '<span style="color:#2a5c2a;font-weight:600;">✓ ' + res.sent + ' E-Mails gesendet</span>'
                + (res.failed ? '<br><span style="color:#c33;">✗ ' + res.failed + ' fehlgeschlagen</span>' : '');
        } else {
            document.getElementById('nl-send-result').innerHTML = '<span style="color:#c33;">Fehler: ' + escHtml(data.data || 'Unbekannt') + '</span>';
        }
    })
    .catch(function(e) {
        btn.disabled = false; btn.textContent = 'Senden';
        document.getElementById('nl-cancel-btn').style.display = '';
        document.getElementById('nl-send-result').innerHTML = '<span style="color:#c33;">Netzwerkfehler.</span>';
    });
}

// ── Draft save / load ─────────────────────────────────────────────────────────
function saveDraft() {
    syncAllTextBlocks();
    var subject = document.getElementById('nl-subject').value.trim();
    var status  = document.getElementById('nl-save-status');
    status.textContent = 'Speichern…';
    fetch(nlAjax, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'action=save_newsletter_draft&nonce=' + nlNonce
            + '&subject=' + encodeURIComponent(subject)
            + '&blocks='  + encodeURIComponent(JSON.stringify(nlBlocks))
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        if (data.success) {
            status.textContent = 'Gespeichert ✓';
            setTimeout(function() { status.textContent = ''; }, 3000);
        } else {
            status.textContent = 'Fehler beim Speichern.';
        }
    })
    .catch(function() { status.textContent = 'Netzwerkfehler.'; });
}

function loadDraft() {
    fetch(nlAjax, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'action=load_newsletter_draft&nonce=' + nlNonce
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        if (!data.success || !data.data) return;
        var d = data.data;
        if (d.subject) document.getElementById('nl-subject').value = d.subject;
        if (d.blocks && d.blocks.length) {
            nlBlocks = d.blocks;
            renderBlocks();
        }
        if (d.saved_at) {
            var status = document.getElementById('nl-save-status');
            status.textContent = 'Entwurf vom ' + new Date(d.saved_at * 1000).toLocaleString('de-DE') + ' geladen';
            setTimeout(function() { status.textContent = ''; }, 5000);
        }
    })
    .catch(function() {});
}

// ── Helpers ───────────────────────────────────────────────────────────────────
function fmtPrice(v) {
    return parseFloat(v || 0).toLocaleString('de-DE', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' €';
}
function escHtml(s) {
    return String(s || '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}
</script>

<?php get_footer(); ?>
