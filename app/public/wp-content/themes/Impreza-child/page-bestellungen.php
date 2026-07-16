<?php
/**
 * Template Name: Bestellungen (Admin)
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
?>

<div style="display: flex; justify-content: center; width: 100%;">
    <div style="width: 100%; max-width: 1400px; padding: 0 20px;">

        <div style="margin-bottom: 24px;">
            <h1 style="margin: 0 0 6px 0; color: var(--creme); font-family: var(--serif-display); font-size: 48px;">Bestellungsübersicht</h1>
            <p style="color: var(--creme-dim); margin: 0 0 14px 0; font-size: 14px;">Verwalte alle aktiven Bestellungen</p>
            <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                <button id="btn-completed-orders" style="padding: 10px 18px; background: var(--bg-raised); color: var(--creme); border: 1px solid var(--border-thin); border-radius: 2px; cursor: pointer; font-size: 11px; font-weight: 700; letter-spacing: 0.14em; text-transform: uppercase;">✓ Abgeschlossene Bestellungen</button>
                <button id="btn-export-orders" style="padding: 10px 18px; background: var(--plum); color: var(--creme); border: none; border-radius: 2px; cursor: pointer; font-size: 11px; font-weight: 700; letter-spacing: 0.14em; text-transform: uppercase;">⬇ Bestellungen exportieren</button>
            </div>
        </div>

        <div id="orders-container" style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px;">
            <div class="order-column" id="column-pending">
                <div style="background: var(--bg-surface); padding: 12px 15px; border-radius: 2px; border-left: 4px solid var(--plum-hot); margin-bottom: 15px; display: flex; justify-content: space-between; align-items: center; border: 1px solid var(--border-thin);">
                    <h2 style="margin: 0; font-size: 16px; color: var(--creme);">Wartestellung</h2>
                    <span id="count-pending" style="color: var(--creme-dim); font-size: 13px; font-weight: 600;">0 Bestellungen</span>
                </div>
                <div class="orders-list" id="orders-pending"></div>
            </div>

            <div class="order-column" id="column-processing">
                <div style="background: var(--bg-surface); padding: 12px 15px; border-radius: 2px; border-left: 4px solid var(--plum); margin-bottom: 15px; display: flex; justify-content: space-between; align-items: center; border: 1px solid var(--border-thin);">
                    <h2 style="margin: 0; font-size: 16px; color: var(--creme);">In Bearbeitung</h2>
                    <span id="count-processing" style="color: var(--creme-dim); font-size: 13px; font-weight: 600;">0 Bestellungen</span>
                </div>
                <div class="orders-list" id="orders-processing"></div>
            </div>

            <div class="order-column" id="column-shipped">
                <div style="background: var(--bg-surface); padding: 12px 15px; border-radius: 2px; border-left: 4px solid var(--lavender); margin-bottom: 15px; display: flex; justify-content: space-between; align-items: center; border: 1px solid var(--border-thin);">
                    <h2 style="margin: 0; font-size: 16px; color: var(--creme);">Versandt</h2>
                    <span id="count-shipped" style="color: var(--creme-dim); font-size: 13px; font-weight: 600;">0 Bestellungen</span>
                </div>
                <div class="orders-list" id="orders-shipped"></div>
            </div>
        </div>
    </div>
</div>

<!-- Export Modal -->
<div id="export-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 10000; align-items: center; justify-content: center;">
    <div style="background: var(--bg-surface); padding: 30px; border-radius: 8px; max-width: 420px; width: 90%; border: 1px solid var(--border-thin);">
        <h3 style="margin-top: 0; margin-bottom: 20px; color: var(--creme);">Bestellungen exportieren</h3>
        <p style="color: var(--creme-dim); font-size: 14px; margin-bottom: 20px;">Welche Bestellungen sollen exportiert werden?</p>
        <div style="display: flex; flex-direction: column; gap: 12px; margin-bottom: 25px;">
            <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; font-size: 14px; color: var(--creme);">
                <input type="checkbox" id="export-pending" checked style="width: 16px; height: 16px;"> In Wartestellung
            </label>
            <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; font-size: 14px; color: var(--creme);">
                <input type="checkbox" id="export-processing" checked style="width: 16px; height: 16px;"> In Bearbeitung
            </label>
            <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; font-size: 14px; color: var(--creme);">
                <input type="checkbox" id="export-shipped" checked style="width: 16px; height: 16px;"> Versandt
            </label>
            <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; font-size: 14px; color: var(--creme);">
                <input type="checkbox" id="export-completed" style="width: 16px; height: 16px;"> Abgeschlossen
            </label>
        </div>
        <div style="display: flex; gap: 10px;">
            <button id="btn-do-export" style="flex: 1; padding: 12px; background: var(--plum); color: var(--creme); border: none; border-radius: 2px; cursor: pointer; font-size: 11px; font-weight: 700; letter-spacing: 0.14em; text-transform: uppercase;">Exportieren</button>
            <button id="btn-cancel-export" style="flex: 1; padding: 12px; background: var(--bg-raised); color: var(--creme); border: 1px solid var(--border-thin); border-radius: 2px; cursor: pointer; font-size: 11px; font-weight: 700; letter-spacing: 0.14em; text-transform: uppercase;">Abbrechen</button>
        </div>
    </div>
</div>

<!-- Abgeschlossene Bestellungen Modal -->
<div id="completed-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 10000; align-items: center; justify-content: center;">
    <div style="background: var(--bg-surface); padding: 30px; border-radius: 8px; max-width: 960px; width: 90%; max-height: 85vh; overflow-y: auto; border: 1px solid var(--border-thin);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h3 style="margin: 0; color: var(--creme);">Abgeschlossene Bestellungen</h3>
            <button id="btn-close-completed" style="padding: 8px 16px; background: var(--bg-raised); color: var(--creme); border: 1px solid var(--border-thin); border-radius: 2px; cursor: pointer; font-size: 11px; font-weight: 700; letter-spacing: 0.14em; text-transform: uppercase;">Schließen</button>
        </div>
        <div id="completed-orders-list">
            <div style="text-align: center; padding: 40px; color: var(--creme-dim);">Lade...</div>
        </div>
    </div>
</div>

<style>
.order-column {
    background: var(--bg-deep);
    border-radius: 8px;
    padding: 15px;
    min-height: 300px;
}

.orders-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.user-group {
    background: var(--bg-surface);
    border-radius: 6px;
    border: 1px solid var(--border-thin);
    overflow: hidden;
}

.user-header {
    padding: 9px 12px;
    background: var(--bg-raised);
    font-weight: 600;
    font-size: 13px;
    color: var(--creme);
    border-bottom: 1px solid var(--border-thin);
}

.address-group {
    border-bottom: 2px solid var(--border-hair);
}

.address-group:last-child {
    border-bottom: none;
}

.address-header {
    padding: 7px 12px;
    background: var(--bg-deep);
    font-size: 12px;
    color: var(--creme-dim);
    border-bottom: 1px solid var(--border-hair);
}

.order-row {
    padding: 9px 12px;
    border-bottom: 1px dashed var(--border-hair);
}

.order-row:last-child {
    border-bottom: none;
}

.order-row-date {
    font-size: 11px;
    color: #999;
    display: block;
    margin-bottom: 3px;
}

.order-row-items {
    font-size: 13px;
    color: #333;
    margin-bottom: 5px;
    line-height: 1.4;
}

.order-row-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 7px;
}

.order-row-total {
    font-weight: 600;
    font-size: 13px;
    color: #333;
}

.order-row-number {
    font-size: 12px;
    color: #999;
}

.order-comment {
    font-size: 12px;
    color: #666;
    background: #fff8e1;
    padding: 5px 8px;
    border-radius: 3px;
    margin-bottom: 6px;
    font-style: italic;
}

.order-actions {
    display: flex;
    gap: 5px;
    flex-wrap: wrap;
}

.order-actions button {
    padding: 4px 9px;
    border: none;
    border-radius: 3px;
    cursor: pointer;
    font-size: 11px;
    font-weight: 500;
    transition: opacity 0.2s;
}

.order-actions button:hover { opacity: 0.85; }

.btn-cancel               { background: #dc3545; color: white; }
.btn-move-to-processing   { background: #ffc107; color: #333; }
.btn-back-to-pending      { background: #6c757d; color: white; }
.btn-generate-label       { background: #007bff; color: white; }
.btn-move-to-shipped      { background: #28a745; color: white; }
.btn-complete             { background: #17a2b8; color: white; }
.btn-view-order           { background: #555; color: white; }

.no-orders {
    text-align: center;
    padding: 30px;
    color: #999;
    font-size: 13px;
}

@media (max-width: 1024px) {
    #orders-container { grid-template-columns: repeat(2, 1fr); }
    #column-shipped   { grid-column: span 2; }
}
@media (max-width: 768px) {
    #orders-container { grid-template-columns: 1fr; }
    #column-shipped   { grid-column: span 1; }
}
</style>

<script>
var ajaxUrl = '<?php echo admin_url('admin-ajax.php'); ?>';
var nonce   = '<?php echo wp_create_nonce('orders_overview_nonce'); ?>';
var allOrdersData = null;

document.addEventListener('DOMContentLoaded', function () {
    loadOrders();

    document.getElementById('btn-export-orders').onclick = function () {
        document.getElementById('export-modal').style.display = 'flex';
    };
    document.getElementById('btn-cancel-export').onclick = function () {
        document.getElementById('export-modal').style.display = 'none';
    };
    document.getElementById('btn-do-export').onclick = exportOrders;

    document.getElementById('btn-completed-orders').onclick = function () {
        document.getElementById('completed-modal').style.display = 'flex';
        renderCompletedOrders();
    };
    document.getElementById('btn-close-completed').onclick = function () {
        document.getElementById('completed-modal').style.display = 'none';
    };
});

function loadOrders() {
    ['orders-pending', 'orders-processing', 'orders-shipped'].forEach(function (id) {
        document.getElementById(id).innerHTML = '<div class="no-orders">Lade Bestellungen...</div>';
    });

    fetch(ajaxUrl, {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'action=get_orders_overview&nonce=' + nonce
    })
    .then(function (r) { return r.json(); })
    .then(function (data) {
        if (data.success) {
            allOrdersData = data.data;
            renderOrders(data.data);
        } else {
            ['orders-pending', 'orders-processing', 'orders-shipped'].forEach(function (id) {
                document.getElementById(id).innerHTML = '<div class="no-orders" style="color:red;">Fehler: ' + (data.data || '') + '</div>';
            });
        }
    })
    .catch(function (err) {
        ['orders-pending', 'orders-processing', 'orders-shipped'].forEach(function (id) {
            document.getElementById(id).innerHTML = '<div class="no-orders" style="color:red;">Fehler: ' + err.message + '</div>';
        });
    });
}

function countOrders(groups) {
    return (groups || []).reduce(function (sum, ug) {
        return sum + ug.address_groups.reduce(function (s2, ag) { return s2 + ag.orders.length; }, 0);
    }, 0);
}

function renderOrders(data) {
    var cols = {
        pending:    {listId: 'orders-pending',    countId: 'count-pending'},
        processing: {listId: 'orders-processing', countId: 'count-processing'},
        shipped:    {listId: 'orders-shipped',    countId: 'count-shipped'}
    };

    Object.keys(cols).forEach(function (status) {
        var groups = data[status] || [];
        var count  = countOrders(groups);
        document.getElementById(cols[status].countId).textContent = count + (count === 1 ? ' Bestellung' : ' Bestellungen');

        var list = document.getElementById(cols[status].listId);
        list.innerHTML = '';

        if (groups.length === 0) {
            list.innerHTML = '<div class="no-orders">Keine Bestellungen</div>';
            return;
        }

        groups.forEach(function (userGroup) {
            list.appendChild(createUserGroupEl(userGroup, status));
        });
    });
}

function createUserGroupEl(userGroup, status) {
    var div = document.createElement('div');
    div.className = 'user-group';

    var html = '<div class="user-header">👤 ' + escHtml(userGroup.user_name) + '</div>';

    (userGroup.address_groups || []).forEach(function (addrGroup) {
        html += '<div class="address-group">';
        html += '<div class="address-header">📍 ' + escHtml(addrGroup.address) + '</div>';
        (addrGroup.orders || []).forEach(function (order) {
            html += createOrderRowHtml(order, status);
        });
        html += '</div>';
    });

    div.innerHTML = html;
    return div;
}

function createOrderRowHtml(order, status) {
    var priceRow = 'display:flex;justify-content:space-between;align-items:baseline;padding:2px 0;';

    var itemsHtml = (order.items || []).map(function (item) {
        var label = item.sku ? (item.quantity + 'x ' + item.sku + '|' + item.name)
                             : (item.quantity + 'x ' + item.name);
        return '<div style="' + priceRow + '">' +
            '<span style="flex:1;margin-right:8px;font-size:12px;">' + escHtml(label) + '</span>' +
            '<span style="white-space:nowrap;font-size:12px;font-weight:500;">' + escHtml(item.total) + '</span>' +
        '</div>';
    }).join('');

    var taxHtml = '';
    (order.taxes || []).forEach(function (tax) {
        taxHtml += '<div style="' + priceRow + 'font-size:11px;color:#888;">' +
            '<span>' + escHtml(tax.label) + '</span>' +
            '<span>' + escHtml(tax.amount) + '</span>' +
        '</div>';
    });

    var footerHtml = '<div style="border-top:1px solid #ddd;margin-top:5px;padding-top:5px;">' +
        taxHtml +
        '<div style="' + priceRow + 'font-weight:600;font-size:13px;">' +
            '<span>Gesamt</span>' +
            '<span>' + escHtml(order.total) + '</span>' +
        '</div>' +
    '</div>';

    var actions = '';
    if (status === 'pending') {
        actions = '<button class="btn-cancel" onclick="cancelOrder(' + order.id + ')">Stornieren</button>' +
                  '<button class="btn-move-to-processing" onclick="moveToProcessing(' + order.id + ')">→ In Bearbeitung</button>';
    } else if (status === 'processing') {
        actions = '<button class="btn-back-to-pending" onclick="moveToPending(' + order.id + ')">← Zurück</button>' +
                  '<button class="btn-generate-label" onclick="generateLabel(' + order.id + ')">🏷️ Label</button>' +
                  '<button class="btn-move-to-shipped" onclick="moveToShipped(' + order.id + ')">→ Versandt</button>';
    } else if (status === 'shipped') {
        actions = '<button class="btn-back-to-pending" onclick="moveToProcessing(' + order.id + ')">← Zurück</button>' +
                  '<button class="btn-complete" onclick="completeOrder(' + order.id + ')">✓ Abschließen</button>';
    }
    actions += '<button class="btn-view-order" onclick="viewOrder(' + order.id + ')">👁️</button>';

    var commentHtml = order.comment
        ? '<div class="order-comment">💬 ' + escHtml(order.comment) + '</div>'
        : '';

    return '<div class="order-row">' +
        commentHtml +
        '<span class="order-row-date">' + escHtml(order.display_date) + '</span>' +
        '<div class="order-row-number" style="font-size:12px;color:#999;margin-bottom:6px;">#' + escHtml(String(order.number)) + '</div>' +
        '<div style="border-top:1px solid #eee;padding-top:6px;">' + itemsHtml + '</div>' +
        footerHtml +
        '<div class="order-actions" style="margin-top:8px;">' + actions + '</div>' +
    '</div>';
}

function renderCompletedOrders() {
    var container = document.getElementById('completed-orders-list');
    if (!allOrdersData) {
        container.innerHTML = '<div class="no-orders">Daten noch nicht geladen. Bitte warten.</div>';
        return;
    }
    var groups = allOrdersData.completed || [];
    if (groups.length === 0) {
        container.innerHTML = '<div class="no-orders">Keine abgeschlossenen Bestellungen</div>';
        return;
    }
    container.innerHTML = '';
    var wrapper = document.createElement('div');
    wrapper.style.cssText = 'display:flex;flex-direction:column;gap:12px;';
    groups.forEach(function (userGroup) {
        wrapper.appendChild(createUserGroupEl(userGroup, 'completed'));
    });
    container.appendChild(wrapper);
}

function exportOrders() {
    var columns = [];
    if (document.getElementById('export-pending').checked)    columns.push('pending');
    if (document.getElementById('export-processing').checked) columns.push('processing');
    if (document.getElementById('export-shipped').checked)    columns.push('shipped');
    if (document.getElementById('export-completed').checked)  columns.push('completed');

    if (columns.length === 0) {
        alert('Bitte mindestens eine Kategorie auswählen.');
        return;
    }

    var btn = document.getElementById('btn-do-export');
    btn.disabled    = true;
    btn.textContent = 'Exportiere...';

    var bodyParts = ['action=export_orders_csv', 'nonce=' + nonce];
    columns.forEach(function (c) { bodyParts.push('columns[]=' + encodeURIComponent(c)); });

    fetch(ajaxUrl, {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: bodyParts.join('&')
    })
    .then(function (r) { return r.json(); })
    .then(function (data) {
        btn.disabled    = false;
        btn.textContent = 'Exportieren';
        if (data.success) {
            var blob = new Blob([data.data.csv], {type: 'text/csv;charset=utf-8;'});
            var url  = URL.createObjectURL(blob);
            var a    = document.createElement('a');
            a.href   = url;
            a.download = 'bestellungen_' + new Date().toISOString().split('T')[0] + '.csv';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            URL.revokeObjectURL(url);
            document.getElementById('export-modal').style.display = 'none';
        } else {
            alert('Fehler: ' + (data.data || 'Unbekannter Fehler'));
        }
    })
    .catch(function (err) {
        btn.disabled    = false;
        btn.textContent = 'Exportieren';
        alert('Fehler: ' + err.message);
    });
}

function escHtml(str) {
    if (str === null || str === undefined) return '';
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;');
}

function cancelOrder(orderId) {
    if (!confirm('Bestellung wirklich stornieren?')) return;
    updateOrderStatus(orderId, 'cancelled');
}
function moveToProcessing(orderId) { updateOrderStatus(orderId, 'processing'); }
function moveToPending(orderId)    { updateOrderStatus(orderId, 'pending'); }
function moveToShipped(orderId)    { updateOrderStatus(orderId, 'shipped'); }
function completeOrder(orderId)    { updateOrderStatus(orderId, 'completed'); }

function generateLabel(orderId) {
    window.open('<?php echo admin_url('post.php?post='); ?>' + orderId + '&action=edit', '_blank');
}
function viewOrder(orderId) {
    window.open('<?php echo admin_url('post.php?post='); ?>' + orderId + '&action=edit', '_blank');
}

function updateOrderStatus(orderId, newStatus) {
    fetch(ajaxUrl, {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'action=update_order_status&order_id=' + orderId + '&status=' + newStatus + '&nonce=' + nonce
    })
    .then(function (r) { return r.json(); })
    .then(function (data) {
        if (data.success) {
            loadOrders();
        } else {
            alert('Fehler: ' + (data.data || 'Unbekannter Fehler'));
        }
    })
    .catch(function (err) { alert('Fehler: ' + err.message); });
}
</script>

<?php get_footer(); ?>
