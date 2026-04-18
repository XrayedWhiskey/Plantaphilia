<?php
/**
 * Template Name: Bestellungen (Admin)
 */

get_header();

// Nur für Admins zugänglich
if (!current_user_can('manage_options')) {
    echo '<div style="padding: 40px; text-align: center;">';
    echo '<h2>Zugriff verweigert</h2>';
    echo '<p>Diese Seite ist nur für Administratoren zugänglich.</p>';
    echo '<a href="' . home_url() . '" class="button">Zurück zur Startseite</a>';
    echo '</div>';
    get_footer();
    exit;
}

// Germanized Shipment Plugin prüfen
$has_germanized_shipments = class_exists('WooCommerce_Germanized_Shipments');
?>

<div style="display: flex; justify-content: center; width: 100%;">
    <div style="width: 100%; max-width: 1400px; padding: 0 20px;">
        
        <div style="text-align: center; margin-bottom: 30px;">
            <h1 style="margin-bottom: 10px;">Bestellungsübersicht</h1>
            <p style="color: #888; margin-bottom: 30px;">Verwalte alle Bestellungen in drei Spalten</p>
        </div>
        
        <div id="orders-container" style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px;">
            <!-- Wartestellung -->
            <div class="order-column" id="column-pending">
                <div style="background: #fff3cd; padding: 15px; border-radius: 5px; border-left: 4px solid #ffc107; margin-bottom: 20px;">
                    <h2 style="margin: 0; font-size: 18px; color: #856404;">Wartestellung</h2>
                    <span class="order-count" style="color: #856404; font-size: 14px;">0 Bestellungen</span>
                </div>
                <div class="orders-list" id="orders-pending"></div>
            </div>
            
            <!-- In Bearbeitung -->
            <div class="order-column" id="column-processing">
                <div style="background: #cce5ff; padding: 15px; border-radius: 5px; border-left: 4px solid #007bff; margin-bottom: 20px;">
                    <h2 style="margin: 0; font-size: 18px; color: #004085;">In Bearbeitung</h2>
                    <span class="order-count" style="color: #004085; font-size: 14px;">0 Bestellungen</span>
                </div>
                <div class="orders-list" id="orders-processing"></div>
            </div>
            
            <!-- In Zustellung -->
            <div class="order-column" id="column-shipped">
                <div style="background: #d4edda; padding: 15px; border-radius: 5px; border-left: 4px solid #28a745; margin-bottom: 20px;">
                    <h2 style="margin: 0; font-size: 18px; color: #155724;">In Zustellung</h2>
                    <span class="order-count" style="color: #155724; font-size: 14px;">0 Bestellungen</span>
                </div>
                <div class="orders-list" id="orders-shipped"></div>
            </div>
        </div>
    </div>
</div>

<style>
.order-column {
    background: #f9f9f9;
    border-radius: 8px;
    padding: 15px;
    min-height: 400px;
}

.orders-list {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.order-card {
    background: white;
    border-radius: 6px;
    padding: 15px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    border-left: 3px solid #ddd;
    transition: box-shadow 0.2s;
}

.order-card:hover {
    box-shadow: 0 2px 8px rgba(0,0,0,0.15);
}

.order-card.status-pending {
    border-left-color: #ffc107;
}

.order-card.status-processing {
    border-left-color: #007bff;
}

.order-card.status-shipped {
    border-left-color: #28a745;
}

.order-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 10px;
    padding-bottom: 10px;
    border-bottom: 1px solid #eee;
}

.order-number {
    font-weight: 600;
    color: #333;
    font-size: 14px;
}

.order-date {
    font-size: 12px;
    color: #888;
}

.order-user {
    font-weight: 500;
    color: #555;
    margin-bottom: 8px;
    font-size: 13px;
}

.order-address {
    font-size: 12px;
    color: #666;
    margin-bottom: 8px;
    line-height: 1.4;
}

.order-comment {
    font-size: 12px;
    color: #666;
    background: #f5f5f5;
    padding: 8px;
    border-radius: 4px;
    margin-bottom: 10px;
    font-style: italic;
}

.order-items {
    font-size: 12px;
    color: #555;
    margin-bottom: 10px;
}

.order-item {
    display: flex;
    justify-content: space-between;
    padding: 4px 0;
    border-bottom: 1px dashed #eee;
}

.order-total {
    font-weight: 600;
    color: #333;
    text-align: right;
    font-size: 14px;
    margin-bottom: 12px;
}

.order-actions {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}

.order-actions button {
    padding: 6px 12px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 12px;
    font-weight: 500;
    transition: opacity 0.2s;
}

.order-actions button:hover {
    opacity: 0.9;
}

.btn-cancel {
    background: #dc3545;
    color: white;
}

.btn-move-to-processing {
    background: #ffc107;
    color: #333;
}

.btn-back-to-pending {
    background: #6c757d;
    color: white;
}

.btn-generate-label {
    background: #007bff;
    color: white;
}

.btn-move-to-shipped {
    background: #28a745;
    color: white;
}

.btn-complete {
    background: #17a2b8;
    color: white;
}

.btn-view-order {
    background: #333;
    color: white;
}

.processing-since {
    font-size: 11px;
    color: #007bff;
    font-weight: 500;
    margin-bottom: 5px;
}

.shipped-date {
    font-size: 11px;
    color: #28a745;
    font-weight: 500;
    margin-bottom: 5px;
}

.loading {
    text-align: center;
    padding: 40px;
    color: #888;
}

.no-orders {
    text-align: center;
    padding: 30px;
    color: #999;
    font-size: 13px;
}

@media (max-width: 1024px) {
    #orders-container {
        grid-template-columns: repeat(2, 1fr);
    }
    
    #column-shipped {
        grid-column: span 2;
    }
}

@media (max-width: 768px) {
    #orders-container {
        grid-template-columns: 1fr;
    }
    
    #column-shipped {
        grid-column: span 1;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    loadOrders();
});

function loadOrders() {
    const columns = document.querySelectorAll('.orders-list');
    columns.forEach(col => {
        col.innerHTML = '<div class="loading">Lade Bestellungen...</div>';
    });
    
    fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'action=get_orders_overview&nonce=<?php echo wp_create_nonce('orders_overview_nonce'); ?>'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            renderOrders(data.data);
        } else {
            console.error('Error:', data.data);
            columns.forEach(col => {
                col.innerHTML = '<div class="no-orders" style="color: red;">Fehler: ' + data.data + '</div>';
            });
        }
    })
    .catch(error => {
        console.error('Fetch error:', error);
        columns.forEach(col => {
            col.innerHTML = '<div class="no-orders" style="color: red;">Fehler: ' + error.message + '</div>';
        });
    });
}

function renderOrders(orders) {
    const pendingContainer = document.getElementById('orders-pending');
    const processingContainer = document.getElementById('orders-processing');
    const shippedContainer = document.getElementById('orders-shipped');
    
    pendingContainer.innerHTML = '';
    processingContainer.innerHTML = '';
    shippedContainer.innerHTML = '';
    
    // Update counts
    document.querySelector('#column-pending .order-count').textContent = orders.pending.length + ' Bestellungen';
    document.querySelector('#column-processing .order-count').textContent = orders.processing.length + ' Bestellungen';
    document.querySelector('#column-shipped .order-count').textContent = orders.shipped.length + ' Bestellungen';
    
    if (orders.pending.length === 0) {
        pendingContainer.innerHTML = '<div class="no-orders">Keine Bestellungen</div>';
    } else {
        orders.pending.forEach(order => {
            pendingContainer.appendChild(createOrderCard(order, 'pending'));
        });
    }
    
    if (orders.processing.length === 0) {
        processingContainer.innerHTML = '<div class="no-orders">Keine Bestellungen</div>';
    } else {
        orders.processing.forEach(order => {
            processingContainer.appendChild(createOrderCard(order, 'processing'));
        });
    }
    
    if (orders.shipped.length === 0) {
        shippedContainer.innerHTML = '<div class="no-orders">Keine Bestellungen</div>';
    } else {
        orders.shipped.forEach(order => {
            shippedContainer.appendChild(createOrderCard(order, 'shipped'));
        });
    }
}

function createOrderCard(order, status) {
    const card = document.createElement('div');
    card.className = 'order-card status-' + status;
    
    let extraInfo = '';
    if (status === 'processing' && order.processing_since) {
        extraInfo = '<div class="processing-since">In Bearbeitung seit: ' + order.processing_since + '</div>';
    } else if (status === 'shipped' && order.shipped_date) {
        extraInfo = '<div class="shipped-date">Abgeschickt am: ' + order.shipped_date + '</div>';
    }
    
    let commentHtml = '';
    if (order.comment) {
        commentHtml = '<div class="order-comment">💬 ' + order.comment + '</div>';
    }
    
    let itemsHtml = '';
    order.items.forEach(item => {
        itemsHtml += `
            <div class="order-item">
                <span>${item.quantity}x ${item.name}</span>
                <span>${item.total}</span>
            </div>
        `;
    });
    
    let actionsHtml = '';
    if (status === 'pending') {
        actionsHtml = `
            <button class="btn-cancel" onclick="cancelOrder(${order.id})">Stornieren</button>
            <button class="btn-move-to-processing" onclick="moveToProcessing(${order.id})">→ In Bearbeitung</button>
        `;
    } else if (status === 'processing') {
        actionsHtml = `
            <button class="btn-back-to-pending" onclick="moveToPending(${order.id})">← Zurück</button>
            <button class="btn-generate-label" onclick="generateLabel(${order.id})">🏷️ Label</button>
            <button class="btn-move-to-shipped" onclick="moveToShipped(${order.id})">→ In Zustellung</button>
        `;
    } else if (status === 'shipped') {
        actionsHtml = `
            <button class="btn-back-to-pending" onclick="moveToProcessing(${order.id})">← Zurück</button>
            <button class="btn-complete" onclick="completeOrder(${order.id})">✓ Abschließen</button>
        `;
    }
    
    actionsHtml += `<button class="btn-view-order" onclick="viewOrder(${order.id})">👁️ Ansehen</button>`;
    
    card.innerHTML = `
        <div class="order-header">
            <div>
                <div class="order-number">#${order.number}</div>
                <div class="order-date">${order.date}</div>
            </div>
            <div class="order-total">${order.total}</div>
        </div>
        ${extraInfo}
        <div class="order-user">👤 ${order.user_name}</div>
        <div class="order-address">📍 ${order.address}</div>
        ${commentHtml}
        <div class="order-items">
            ${itemsHtml}
        </div>
        <div class="order-actions">
            ${actionsHtml}
        </div>
    `;
    
    return card;
}

function cancelOrder(orderId) {
    if (!confirm('Bestellung wirklich stornieren?')) return;
    
    updateOrderStatus(orderId, 'cancelled');
}

function moveToProcessing(orderId) {
    updateOrderStatus(orderId, 'processing');
}

function moveToPending(orderId) {
    updateOrderStatus(orderId, 'pending');
}

function moveToShipped(orderId) {
    updateOrderStatus(orderId, 'shipped');
}

function completeOrder(orderId) {
    updateOrderStatus(orderId, 'completed');
}

function generateLabel(orderId) {
    // Öffne die Bestellung im Admin-Panel für DHL Label Generierung
    window.open('<?php echo admin_url('post.php?post='); ?>' + orderId + '&action=edit', '_blank');
}

function viewOrder(orderId) {
    window.open('<?php echo admin_url('post.php?post='); ?>' + orderId + '&action=edit', '_blank');
}

function updateOrderStatus(orderId, newStatus) {
    fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'action=update_order_status&order_id=' + orderId + '&status=' + newStatus + '&nonce=<?php echo wp_create_nonce('orders_overview_nonce'); ?>'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            loadOrders();
        } else {
            alert('Fehler: ' + data.data);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Fehler beim Aktualisieren des Status');
    });
}
</script>

<?php get_footer(); ?>
