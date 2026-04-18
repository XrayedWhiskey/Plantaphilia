<?php
/* Custom functions code goes here. */

// AJAX Handler für Produktliste
add_action('wp_ajax_get_product_list_data', 'get_product_list_data');
add_action('wp_ajax_nopriv_get_product_list_data', 'get_product_list_data');

// Funktion zum Berechnen von in_progress basierend auf offenen Bestellungen
function calculate_in_progress_from_orders() {
    // Array zum Speichern der Summen pro Produkt
    $product_quantities = array();
    
    // pending, on-hold und processing Bestellungen zählen
    // Bestand wird erst bei completed reduziert
    $order_statuses = array('pending', 'on-hold', 'processing');
    
    // Alle Bestellungen mit offenen Statusse abrufen
    $args = array(
        'status' => $order_statuses,
        'limit' => -1,
        'return' => 'ids'
    );
    
    $order_ids = wc_get_orders($args);
    
    foreach ($order_ids as $order_id) {
        $order = wc_get_order($order_id);
        if (!$order) continue;
        
        // Alle Items der Bestellung durchgehen
        $items = $order->get_items();
        
        foreach ($items as $item) {
            $product_id = $item->get_product_id();
            $quantity = $item->get_quantity();
            
            if (!isset($product_quantities[$product_id])) {
                $product_quantities[$product_id] = 0;
            }
            
            $product_quantities[$product_id] += $quantity;
        }
    }
    
    // Ergebnisse in _in_progress speichern
    foreach ($product_quantities as $product_id => $quantity) {
        update_post_meta($product_id, '_in_progress', $quantity);
    }
    
    // Für Produkte ohne offene Bestellungen, in_progress auf 0 setzen
    $all_product_ids = get_posts(array(
        'post_type' => 'product',
        'numberposts' => -1,
        'fields' => 'ids'
    ));
    
    foreach ($all_product_ids as $product_id) {
        if (!isset($product_quantities[$product_id])) {
            update_post_meta($product_id, '_in_progress', 0);
        }
    }
}

// Hooks für automatische Aktualisierung bei Bestellungsänderungen
add_action('woocommerce_order_status_changed', 'update_in_progress_on_order_status_change', 10, 4);
add_action('woocommerce_new_order', 'update_in_progress_on_new_order', 10, 1);
add_action('woocommerce_delete_order_item', 'update_in_progress_on_order_item_change', 10, 1);
add_action('woocommerce_add_order_item', 'update_in_progress_on_order_item_change', 10, 1);
add_filter('woocommerce_can_reduce_order_stock', 'prevent_stock_reduction_for_non_pending', 10, 2);

function prevent_stock_reduction_for_non_pending($can_reduce, $order) {
    $status = $order->get_status();
    // Bestand NICHT für pending, on-hold und processing reduzieren, nur für completed
    // pending, on-hold und processing werden in "In Bearbeitung" gezählt
    if ($status === 'pending' || $status === 'on-hold' || $status === 'processing') {
        return false;
    }
    return $can_reduce;
}

function update_in_progress_on_order_status_change($order_id, $old_status, $new_status, $order) {
    // Wenn Status zu completed wechselt, Bestand reduzieren
    if ($new_status === 'completed' && $old_status !== 'completed') {
        wc_reduce_stock_levels($order_id);
    }
    
    // Berechnung neu ausführen, da sich der Status geändert hat
    calculate_in_progress_from_orders();
}

function update_in_progress_on_new_order($order_id) {
    // Berechnung neu ausführen, da eine neue Bestellung erstellt wurde
    calculate_in_progress_from_orders();
}

function update_in_progress_on_order_item_change($item_id) {
    // Berechnung neu ausführen, da ein Item hinzugefügt oder gelöscht wurde
    calculate_in_progress_from_orders();
}

// Filter für verfügbare Menge basierend auf in_progress
add_filter('woocommerce_get_availability', 'adjust_availability_based_on_in_progress', 10, 2);
add_filter('woocommerce_add_to_cart_validation', 'validate_cart_quantity_based_on_in_progress', 10, 4);
add_filter('woocommerce_quantity_input_args', 'adjust_quantity_input_args_based_on_in_progress', 10, 2);
add_filter('woocommerce_cart_item_quantity', 'validate_cart_item_quantity_based_on_in_progress', 10, 3);
add_filter('woocommerce_is_sold_individually', '__return_false', 10, 2);

function validate_cart_quantity_based_on_in_progress($passed, $product_id, $quantity, $variation_id = 0) {
    $product = wc_get_product($product_id);
    if (!$product) return $passed;
    
    $stock = $product->get_stock_quantity();
    $in_progress = get_post_meta($product_id, '_in_progress', true);
    $in_progress = $in_progress ? intval($in_progress) : 0;
    
    $available = $stock - $in_progress;
    
    // Prüfen, ob bereits im Warenkorb
    $cart_quantity = 0;
    foreach (WC()->cart->get_cart() as $cart_item) {
        if ($cart_item['product_id'] == $product_id) {
            $cart_quantity += $cart_item['quantity'];
        }
    }
    
    $total_quantity = $cart_quantity + $quantity;
    
    if ($total_quantity > $available) {
        wc_add_notice(sprintf(__('Du kannst nur %d Stück dieses Produkts bestellen. Aktuell im Warenkorb: %d', 'woocommerce'), $available, $cart_quantity), 'error');
        return false;
    }
    
    return $passed;
}

function validate_cart_item_quantity_based_on_in_progress($quantity, $cart_item_key, $cart_item) {
    $product_id = $cart_item['product_id'];
    $product = wc_get_product($product_id);
    if (!$product) return $quantity;
    
    $stock = $product->get_stock_quantity();
    $in_progress = get_post_meta($product_id, '_in_progress', true);
    $in_progress = $in_progress ? intval($in_progress) : 0;
    
    $available = $stock - $in_progress;
    
    // Wenn der Benutzer versucht, mehr als verfügbar zu wählen, aber nicht blockieren
    // Die Validierung erfolgt beim Checkout
    return $quantity;
}

function adjust_quantity_input_args_based_on_in_progress($args, $product) {
    $stock = $product->get_stock_quantity();
    $in_progress = get_post_meta($product->get_id(), '_in_progress', true);
    $in_progress = $in_progress ? intval($in_progress) : 0;
    
    $available = $stock - $in_progress;
    
    $args['max_value'] = $available > 0 ? $available : 0;
    
    return $args;
}

function adjust_availability_based_on_in_progress($availability, $product) {
    $stock = $product->get_stock_quantity();
    $in_progress = get_post_meta($product->get_id(), '_in_progress', true);
    $in_progress = $in_progress ? intval($in_progress) : 0;
    
    $available = $stock - $in_progress;
    
    if ($product->is_in_stock()) {
        if ($available <= 0) {
            $availability['availability'] = __('Nicht verfügbar', 'woocommerce');
            $availability['class'] = 'out-of-stock';
        } elseif ($available < 5) {
            $availability['availability'] = __('Nur noch ' . $available . ' verfügbar', 'woocommerce');
            $availability['class'] = 'low-stock';
        } else {
            $availability['availability'] = __('Auf Lager', 'woocommerce');
        }
    }
    
    return $availability;
}

function get_product_list_data() {
    error_log('DEBUG: get_product_list_data aufgerufen');
    check_ajax_referer('product_list_nonce', 'nonce');
    
    if (!current_user_can('manage_options')) {
        wp_send_json_error('Unauthorized');
    }
    
    // Zuerst in_progress für alle Produkte basierend auf offenen Bestellungen berechnen
    calculate_in_progress_from_orders();
    
    $products = wc_get_products(array(
        'limit' => -1,
        'status' => 'publish'
    ));
    
    $product_data = array();
    
    foreach ($products as $product) {
        $image_id = $product->get_image_id();
        $image_url = $image_id ? wp_get_attachment_image_url($image_id, 'thumbnail') : '';
        
        $stock = $product->get_stock_quantity();
        $in_progress = get_post_meta($product->get_id(), '_in_progress', true);
        $in_progress = $in_progress ? intval($in_progress) : 0;
        
        $regular_price = $product->get_regular_price();
        $sale_price = $product->get_sale_price();
        $price = $product->get_price();
        
        // Prüfen, ob Angebot abgelaufen ist und bereinigen
        if (!empty($sale_price)) {
            $date_to = $product->get_date_on_sale_to();
            if ($date_to) {
                $now = current_time('timestamp', true);
                $end_time = $date_to->getTimestamp();
                
                // Wenn das Angebot abgelaufen ist
                if ($end_time < $now) {
                    // Zuerst _original_regular_price prüfen (der Preis VOR dem Angebot)
                    $saved_regular_price = get_post_meta($product->get_id(), '_original_regular_price', true);
                    
                    // Falls nicht vorhanden, _saved_regular_price prüfen
                    if (!$saved_regular_price) {
                        $saved_regular_price = get_post_meta($product->get_id(), '_saved_regular_price', true);
                    }
                    
                    // Fallback: Falls nichts existiert, den aktuellen regular_price verwenden
                    if (!$saved_regular_price) {
                        $saved_regular_price = $product->get_regular_price();
                    }
                    
                    if ($saved_regular_price) {
                        // Angebot im WooCommerce-Produkt löschen
                        $product->set_sale_price('');
                        $product->set_date_on_sale_to('');
                        $product->set_date_on_sale_from('');
                        $product->set_regular_price($saved_regular_price);
                        $product->save();
                        
                        // Angebots-Meta-Daten löschen
                        update_post_meta($product->get_id(), '_sale_price', '');
                        update_post_meta($product->get_id(), '_sale_price_dates_from', '');
                        update_post_meta($product->get_id(), '_sale_price_dates_to', '');
                        
                        // Umfassendes Cache-Leeren
                        wc_delete_product_transients($product->get_id());
                        wp_cache_delete($product->get_id(), 'products');
                        wp_cache_flush();
                        delete_transient('wc_products_onsale');
                        
                        // Werte neu laden
                        $sale_price = $product->get_sale_price();
                        $price = $product->get_price();
                        $regular_price = $product->get_regular_price();
                    }
                }
            }
        }
        
        $has_offer = !empty($sale_price) && $sale_price < $regular_price;
        $offer_end = '';
        
        if ($has_offer) {
            $date_from = $product->get_date_on_sale_from();
            $date_to = $product->get_date_on_sale_to();
            if ($date_to) {
                // Als Unix-Timestamp senden (in Sekunden)
                $offer_end = $date_to->getTimestamp();
            }
        }
        
        // Verkaufen-Status prüfen (catalog_visibility)
        $is_sellable = $product->get_catalog_visibility() !== 'hidden';
        
        // Meta-Daten für Angebot
        $show_old_price = get_post_meta($product->get_id(), '_show_old_price', true);
        $time_limited = get_post_meta($product->get_id(), '_time_limited', true);
        $time_limit_type = get_post_meta($product->get_id(), '_time_limit_type', true);
        $time_limit_duration = get_post_meta($product->get_id(), '_time_limit_duration', true);
        $time_limit_date = get_post_meta($product->get_id(), '_time_limit_date', true);
        $show_end_date = get_post_meta($product->get_id(), '_show_end_date', true);
        $time_limit_days = get_post_meta($product->get_id(), '_time_limit_days', true);
        $time_limit_hours = get_post_meta($product->get_id(), '_time_limit_hours', true);
        $time_limit_minutes = get_post_meta($product->get_id(), '_time_limit_minutes', true);
        $time_limit_time = get_post_meta($product->get_id(), '_time_limit_time', true);
        $time_limit_date_only = get_post_meta($product->get_id(), '_time_limit_date_only', true);
        $offer_start_date = get_post_meta($product->get_id(), '_offer_start_date', true);
        
        // Startdatum aus WooCommerce Produkt abrufen
        $offer_start = 0;
        if ($date_from) {
            $offer_start = $date_from->getTimestamp();
        }
        
        // Meta-Daten für abgelaufene Angebote
        $offer_expired_read = get_post_meta($product->get_id(), '_offer_expired_read', true);
        $offer_expired_since = get_post_meta($product->get_id(), '_offer_expired_since', true);
        
        // Meta-Daten für Restock-Status
        $was_out_of_stock = get_post_meta($product->get_id(), '_was_out_of_stock', true);
        $was_low_stock = get_post_meta($product->get_id(), '_was_low_stock', true);
        $was_restocked = get_post_meta($product->get_id(), '_was_restocked', true);
        $out_of_stock_since = get_post_meta($product->get_id(), '_out_of_stock_since', true);
        $low_stock_since = get_post_meta($product->get_id(), '_low_stock_since', true);
        $restocked_since = get_post_meta($product->get_id(), '_restocked_since', true);
        
        // Meta-Daten für Bulk-Aktionen
        $part_of_sale = get_post_meta($product->get_id(), '_part_of_sale', true);
        $sale_id = get_post_meta($product->get_id(), '_sale_id', true);
        $sale_group_id = get_post_meta($product->get_id(), '_sale_group_id', true);
        $sale_title = '';
        $group_name = '';
        $bulk_sale_start_date = '';
        $bulk_sale_end_date = '';
        
        if ($part_of_sale && $sale_id) {
            $bulk_sales = get_option('_bulk_sales', array());
            if (isset($bulk_sales[$sale_id])) {
                $sale_title = $bulk_sales[$sale_id]['title'];
                $bulk_sale_start_date = isset($bulk_sales[$sale_id]['start_date']) ? $bulk_sales[$sale_id]['start_date'] : '';
                $bulk_sale_end_date = isset($bulk_sales[$sale_id]['end_date']) ? $bulk_sales[$sale_id]['end_date'] : '';
                if (isset($bulk_sales[$sale_id]['groups'][$sale_group_id])) {
                    $group_name = $bulk_sales[$sale_id]['groups'][$sale_group_id]['name'];
                }
            }
        }
        
        // Prüfen, ob Angebot abgelaufen ist
        $is_offer_expired = false;
        if ($has_offer && $time_limited && $offer_end) {
            $now = current_time('timestamp', true);
            if ($offer_end < $now) {
                $is_offer_expired = true;
                // Wenn noch kein abgelaufen_seit gesetzt, setzen
                if (!$offer_expired_since) {
                    $offer_expired_since = $now;
                    update_post_meta($product->get_id(), '_offer_expired_since', $offer_expired_since);
                }
            }
        }
        
        // Auch prüfen, ob das Produkt ein abgelaufenes Angebot hatte (bereinigt)
        if (!$is_offer_expired && $offer_expired_since && !$has_offer) {
            $is_offer_expired = true;
        }
        
        // Prüfen, ob der Artikel neu ist (weniger als 7 Tage alt)
        $is_new = false;
        $date_created = $product->get_date_created();
        if ($date_created) {
            $created_timestamp = $date_created->getTimestamp();
            $now = current_time('timestamp', true);
            $days_since_creation = ($now - $created_timestamp) / (24 * 60 * 60);
            if ($days_since_creation < 7) {
                $is_new = true;
            }
        }
        
        // Rabatttyp und Rabattbetrag berechnen oder aus Meta-Daten abrufen
        $offer_reduction_type = '';
        $offer_reduction_amount = '';
        if ($has_offer && $regular_price && $sale_price && $sale_price < $regular_price) {
            $reduction = $regular_price - $sale_price;
            $percentage = ($reduction / $regular_price) * 100;
            // Prüfen, ob der Rabatttyp in den Meta-Daten gespeichert ist
            $price_type_toggle = get_post_meta($product->get_id(), '_price_type_toggle', true);
            if ($price_type_toggle === '1') {
                // Prozentualer Rabatt - Anzahl der Dezimalstellen basierend auf der Differenz berechnen
                $decimal_places = 0;
                $fractional_part = abs($percentage) - floor(abs($percentage));
                if ($fractional_part > 0) {
                    // Dezimalstellen zählen
                    $decimal_str = number_format($fractional_part, 10, '.', '');
                    $decimal_str = rtrim($decimal_str, '0');
                    $decimal_places = strlen(substr($decimal_str, strpos($decimal_str, '.') + 1));
                    // Maximal 2 Dezimalstellen
                    $decimal_places = min($decimal_places, 2);
                }
                $offer_reduction_type = 'percent';
                $offer_reduction_amount = '-' . number_format($percentage, $decimal_places) . '%';
            } else {
                // Fester Rabatt in Euro
                $offer_reduction_type = 'fixed';
                $offer_reduction_amount = '-' . number_format($reduction, 2) . '€';
            }
        } elseif (!$has_offer && $is_offer_expired) {
            // Wenn Angebot abgelaufen und bereinigt, gespeicherte Werte abrufen
            $offer_reduction_type = get_post_meta($product->get_id(), '_offer_reduction_type', true);
            $offer_reduction_amount = get_post_meta($product->get_id(), '_offer_reduction_amount', true);
        }
        
        error_log('DEBUG: Produkt ' . $product->get_id() . ' - has_offer=' . ($has_offer ? 'yes' : 'no') . ', is_offer_expired=' . ($is_offer_expired ? 'yes' : 'no') . ', offer_expired_since=' . $offer_expired_since);
        
        $product_data[] = array(
            'id' => $product->get_id(),
            'name' => $product->get_name(),
            'sku' => $product->get_sku(),
            'image' => $image_url,
            'stock' => $stock ? $stock : 0,
            'in_progress' => $in_progress,
            'price' => $price,
            'regular_price' => $regular_price,
            'sale_price' => $sale_price,
            'has_offer' => $has_offer,
            'offer_end' => $offer_end,
            'offer_start' => $offer_start,
            'permalink' => $product->get_permalink(),
            'edit_link' => get_edit_post_link($product->get_id()),
            'is_sellable' => $is_sellable,
            'catalog_visibility' => $product->get_catalog_visibility(),
            'show_old_price' => $show_old_price,
            'time_limited' => $time_limited,
            'time_limit_type' => $time_limit_type,
            'time_limit_duration' => $time_limit_duration,
            'time_limit_date' => $time_limit_date,
            'show_end_date' => $show_end_date,
            'is_offer_expired' => $is_offer_expired,
            'offer_expired_read' => $offer_expired_read,
            'offer_expired_since' => $offer_expired_since,
            'offer_reduction_type' => $offer_reduction_type,
            'offer_reduction_amount' => $offer_reduction_amount,
            'time_limit_days' => $time_limit_days,
            'time_limit_hours' => $time_limit_hours,
            'time_limit_minutes' => $time_limit_minutes,
            'time_limit_time' => $time_limit_time,
            'time_limit_date_only' => $time_limit_date_only,
            'offer_start_date' => $offer_start_date,
            'is_new' => $is_new,
            'was_out_of_stock' => $was_out_of_stock,
            'was_low_stock' => $was_low_stock,
            'was_restocked' => $was_restocked,
            'out_of_stock_since' => $out_of_stock_since,
            'low_stock_since' => $low_stock_since,
            'restocked_since' => $restocked_since,
            'part_of_sale' => $part_of_sale,
            'sale_id' => $sale_id,
            'sale_group_id' => $sale_group_id,
            'sale_title' => $sale_title,
            'group_name' => $group_name,
            'offer_start_date' => $part_of_sale ? $bulk_sale_start_date : $offer_start_date,
            'offer_end_date' => $part_of_sale ? $bulk_sale_end_date : ($time_limited && $offer_end ? date('d.m.Y H:i', $offer_end) : '')
        );
    }
    
    wp_send_json_success($product_data);
}

// AJAX Handler für Verkaufen-Status
add_action('wp_ajax_update_sell_status', 'update_sell_status');
add_action('wp_ajax_nopriv_update_sell_status', 'update_sell_status');

function update_sell_status() {
    check_ajax_referer('product_list_nonce', 'nonce');
    
    if (!current_user_can('manage_options')) {
        wp_send_json_error('Unauthorized');
    }
    
    $product_id = intval($_POST['product_id']);
    $is_sellable = intval($_POST['is_sellable']);
    
    $product = wc_get_product($product_id);
    if (!$product) {
        wp_send_json_error('Product not found');
    }
    
    // Catalog Visibility setzen
    $visibility = $is_sellable ? 'visible' : 'hidden';
    $product->set_catalog_visibility($visibility);
    
    // Sellable Meta-Wert speichern
    update_post_meta($product_id, 'sellable', $is_sellable ? '1' : '0');
    
    $product->save();
    
    wp_send_json_success(array(
        'product_id' => $product_id,
        'is_sellable' => $is_sellable
    ));
}

// AJAX Handler für Produkt-Details-Update
add_action('wp_ajax_update_product_details', 'update_product_details');
add_action('wp_ajax_nopriv_update_product_details', 'update_product_details');

// AJAX Handler für Cleanup abgelaufener Angebote
add_action('wp_ajax_cleanup_expired_sale', 'cleanup_expired_sale');
add_action('wp_ajax_nopriv_cleanup_expired_sale', 'cleanup_expired_sale');

// AJAX Handler für Markieren als gelesen
add_action('wp_ajax_mark_offer_as_read', 'mark_offer_as_read');
add_action('wp_ajax_nopriv_mark_offer_as_read', 'mark_offer_as_read');

function mark_offer_as_read() {
    $product_id = intval($_POST['product_id']);
    $is_read = intval($_POST['is_read']);
    
    update_post_meta($product_id, '_offer_expired_read', $is_read);
    
    if ($is_read) {
        // Wenn als gelesen markiert, is_offer_expired und offer_expired_since zurücksetzen
        delete_post_meta($product_id, '_offer_expired_since');
    }
    
    wp_send_json_success();
}

function cleanup_expired_sale() {
    $product_id = intval($_POST['product_id']);
    
    $product = wc_get_product($product_id);
    if (!$product) {
        wp_send_json_error('Product not found');
    }
    
    // Prüfen, ob ein gespeicherter Original-Preis existiert, der vom aktuellen regular_price abweicht
    $saved_regular_price = get_post_meta($product_id, '_original_regular_price', true);
    if (!$saved_regular_price) {
        $saved_regular_price = get_post_meta($product_id, '_saved_regular_price', true);
    }
    
    // Prüfen, ob ein sale_price existiert (auch wenn is_on_sale false ist)
    $current_sale_price = $product->get_sale_price();
    $date_to = $product->get_date_on_sale_to();
    
    $needs_cleanup = false;
    
    // Wenn ein gespeicherter Preis existiert und vom aktuellen regular_price abweicht, wiederherstellen
    if ($saved_regular_price && $saved_regular_price != $product->get_regular_price()) {
        $product->set_regular_price($saved_regular_price);
        $needs_cleanup = true;
    }
    
    // Wenn ein sale_price existiert, löschen
    if ($current_sale_price) {
        $product->set_sale_price('');
        $needs_cleanup = true;
    }
    
    // Wenn ein date_to existiert, löschen
    if ($date_to) {
        $product->set_date_on_sale_to('');
        $product->set_date_on_sale_from('');
        $needs_cleanup = true;
    }
    
    if ($needs_cleanup) {
        $product->save();
        
        // Angebots-Meta-Daten löschen
        update_post_meta($product_id, '_sale_price', '');
        update_post_meta($product_id, '_sale_price_dates_from', '');
        update_post_meta($product_id, '_sale_price_dates_to', '');
        
        // offer_expired_since setzen, wenn noch nicht gesetzt
        $offer_expired_since = get_post_meta($product_id, '_offer_expired_since', true);
        if (!$offer_expired_since) {
            $offer_expired_since = current_time('timestamp', true);
            update_post_meta($product_id, '_offer_expired_since', $offer_expired_since);
        }
        
        // Umfassendes Cache-Leeren
        wc_delete_product_transients($product_id);
        wp_cache_delete($product_id, 'products');
        wp_cache_flush();
        delete_transient('wc_products_onsale');
        
        wp_send_json_success(array(
            'regular_price' => $product->get_regular_price(),
            'sale_price' => $product->get_sale_price()
        ));
    }
    
    wp_send_json_error('No price restoration needed');
}

function update_product_details() {
    check_ajax_referer('product_list_nonce', 'nonce');
    
    if (!current_user_can('manage_options')) {
        wp_send_json_error('Unauthorized');
    }
    
    $product_id = intval($_POST['product_id']);
    $stock = intval($_POST['stock']);
    $price = floatval($_POST['price']);
    $has_offer = intval($_POST['has_offer']);
    $sale_price = floatval($_POST['sale_price']);
    $show_old_price = intval($_POST['show_old_price']);
    $time_limited = intval($_POST['time_limited']);
    $time_limit_type = sanitize_text_field($_POST['time_limit_type']);
    $time_limit_duration = intval($_POST['time_limit_duration']);
    $time_limit_date = sanitize_text_field($_POST['time_limit_date']);
    $show_end_date = intval($_POST['show_end_date']);
    $offer_start_date = sanitize_text_field($_POST['offer_start_date']);
    
    $product = wc_get_product($product_id);
    if (!$product) {
        wp_send_json_error('Product not found');
    }
    
    // Aktuellen Stock vor dem Update abrufen
    $current_stock = $product->get_stock_quantity();
    $current_stock = $current_stock ? $current_stock : 0;
    
    // Stock setzen
    $product->set_stock_quantity($stock);
    $product->set_manage_stock(true);
    
    // Restock-Erkennung
    $was_out_of_stock = false;
    $was_low_stock = false;
    $was_restocked = false;
    
    // Prüfen, ob ausverkauft war und jetzt gerestocked wurde
    if ($current_stock == 0 && $stock > 0) {
        $was_out_of_stock = true;
        $was_restocked = true;
        update_post_meta($product_id, '_was_out_of_stock', true);
        update_post_meta($product_id, '_out_of_stock_since', current_time('timestamp', true));
    }
    
    // Prüfen, ob low stock war (weniger als 5) und jetzt gerestocked wurde
    if ($current_stock > 0 && $current_stock < 5 && $stock >= 5) {
        $was_low_stock = true;
        $was_restocked = true;
        update_post_meta($product_id, '_was_low_stock', true);
        update_post_meta($product_id, '_low_stock_since', current_time('timestamp', true));
    }
    
    // Prüfen, ob allgemein gerestocked wurde (Stock erhöht)
    if ($stock > $current_stock && $current_stock > 0) {
        $was_restocked = true;
        update_post_meta($product_id, '_was_restocked', true);
        update_post_meta($product_id, '_restocked_since', current_time('timestamp', true));
    }
    
    // Wenn Stock reduziert wurde, Restock-Flags zurücksetzen
    if ($stock < $current_stock) {
        delete_post_meta($product_id, '_was_out_of_stock');
        delete_post_meta($product_id, '_was_low_stock');
        delete_post_meta($product_id, '_was_restocked');
    }
    
    // Angebot setzen
    if ($has_offer) {
        // VOR dem Setzen des Angebots den aktuellen regular_price speichern
        $current_regular_price = $product->get_regular_price();
        
        if ($current_regular_price && $current_regular_price != $price) {
            // Nur speichern, wenn sich der Preis geändert hat
            update_post_meta($product_id, '_original_regular_price', $current_regular_price);
        }
        
        // offer_expired_since zurücksetzen, da ein neues Angebot gesetzt wird
        delete_post_meta($product_id, '_offer_expired_since');
        delete_post_meta($product_id, '_offer_expired_read');
        
        // offer_start_activated zurücksetzen, da ein neues Angebot gesetzt wird
        delete_post_meta($product_id, '_offer_start_activated');
        
        // Rabatttyp und Rabattbetrag berechnen und speichern
        $reduction = $price - $sale_price;
        $percentage = ($reduction / $price) * 100;
        $price_type_toggle = isset($_POST['price_type_toggle']) ? intval($_POST['price_type_toggle']) : 0;
        if ($price_type_toggle === 1) {
            // Prozentualer Rabatt - Anzahl der Dezimalstellen basierend auf der Differenz berechnen
            $decimal_places = 0;
            $fractional_part = abs($percentage) - floor(abs($percentage));
            if ($fractional_part > 0) {
                // Dezimalstellen zählen
                $decimal_str = number_format($fractional_part, 10, '.', '');
                $decimal_str = rtrim($decimal_str, '0');
                $decimal_places = strlen(substr($decimal_str, strpos($decimal_str, '.') + 1));
                // Maximal 2 Dezimalstellen
                $decimal_places = min($decimal_places, 2);
            }
            update_post_meta($product_id, '_offer_reduction_type', 'percent');
            update_post_meta($product_id, '_offer_reduction_amount', '-' . number_format($percentage, $decimal_places) . '%');
        } else {
            // Fester Rabatt in Euro
            update_post_meta($product_id, '_offer_reduction_type', 'fixed');
            update_post_meta($product_id, '_offer_reduction_amount', '-' . number_format($reduction, 2) . '€');
        }
        
        $product->set_regular_price($price);
        $product->set_sale_price($sale_price);
        
        // Zeitliche Begrenzung berechnen
        if ($time_limited) {
            // Startdatum verwenden oder aktuelle Zeit
            if (!empty($offer_start_date)) {
                // TT.MM.JJJJ HH:MM Format parsen
                $date_parts = explode(' ', $offer_start_date);
                if (count($date_parts) === 2) {
                    $date = $date_parts[0];
                    $time = $date_parts[1];
                    $date_parts = explode('.', $date);
                    if (count($date_parts) === 3) {
                        $day = intval($date_parts[0]);
                        $month = intval($date_parts[1]);
                        $year = intval($date_parts[2]);
                        $start_time = strtotime($year . '-' . $month . '-' . $day . ' ' . $time . ' Europe/Berlin');
                    } else {
                        $start_time = current_time('timestamp', true);
                    }
                } else {
                    $start_time = current_time('timestamp', true);
                }
            } else {
                $start_time = current_time('timestamp', true);
            }
            
            if ($time_limit_type === 'days') {
                $end_time = strtotime('+' . $time_limit_duration . ' days', $start_time);
            } elseif ($time_limit_type === 'hours') {
                $end_time = strtotime('+' . $time_limit_duration . ' hours', $start_time);
            } elseif ($time_limit_type === 'minutes') {
                $end_time = strtotime('+' . $time_limit_duration . ' minutes', $start_time);
            } elseif ($time_limit_type === 'date' && !empty($time_limit_date)) {
                // TT.MM.JJJJ HH:MM Format parsen
                $date_parts = explode(' ', $time_limit_date);
                if (count($date_parts) === 2) {
                    $date = $date_parts[0];
                    $time = $date_parts[1];
                    $date_parts = explode('.', $date);
                    if (count($date_parts) === 3) {
                        $day = intval($date_parts[0]);
                        $month = intval($date_parts[1]);
                        $year = intval($date_parts[2]);
                        $end_time = strtotime($year . '-' . $month . '-' . $day . ' ' . $time . ' Europe/Berlin');
                    } else {
                        $end_time = strtotime('+7 days', $start_time); // Fallback
                    }
                } else {
                    // Nur Datum ohne Uhrzeit
                    $date_parts = explode('.', $time_limit_date);
                    if (count($date_parts) === 3) {
                        $day = intval($date_parts[0]);
                        $month = intval($date_parts[1]);
                        $year = intval($date_parts[2]);
                        $end_time = strtotime($year . '-' . $month . '-' . $day . ' 23:59:59 Europe/Berlin');
                    } else {
                        $end_time = strtotime('+7 days', $start_time); // Fallback
                    }
                }
            } else {
                $end_time = strtotime('+7 days', $start_time); // Default: 7 Tage
            }
            
            $product->set_date_on_sale_to($end_time);
            $product->set_date_on_sale_from($start_time);
        } else {
            // Keine zeitliche Begrenzung - Angebot läuft unendlich
            $product->set_date_on_sale_to('');
            $product->set_date_on_sale_from('');
        }
    } else {
        // Kein Angebot - regulären Preis setzen und Angebot löschen
        $product->set_regular_price($price);
        $product->set_sale_price('');
        $product->set_date_on_sale_to('');
        $product->set_date_on_sale_from('');
    }
    
    // WooCommerce Cache leeren
    wc_delete_product_transients($product_id);
    wp_cache_delete($product_id, 'products');
    
    // Meta-Felder speichern
    update_post_meta($product_id, '_show_old_price', $show_old_price);
    update_post_meta($product_id, '_time_limited', $time_limited);
    update_post_meta($product_id, '_time_limit_type', $time_limit_type);
    update_post_meta($product_id, '_time_limit_duration', $time_limit_duration);
    update_post_meta($product_id, '_time_limit_date', $time_limit_date);
    update_post_meta($product_id, '_show_end_date', $show_end_date);
    update_post_meta($product_id, '_offer_start_date', $offer_start_date);
    
    // Regulären Preis IMMER separat speichern (für Wiederherstellung nach Ablauf)
    update_post_meta($product_id, '_saved_regular_price', $price);
    
    // Uhrzeit speichern
    if ($time_limit_type === 'date' && !empty($time_limit_date)) {
        $date_parts = explode(' ', $time_limit_date);
        if (count($date_parts) === 2) {
            update_post_meta($product_id, '_time_limit_time', $date_parts[1]);
            update_post_meta($product_id, '_time_limit_date_only', $date_parts[0]);
        } else {
            update_post_meta($product_id, '_time_limit_date_only', $time_limit_date);
        }
    }
    
    // Tage, Stunden und Minuten separat speichern
    $time_limit_days = 0;
    $time_limit_hours = 0;
    $time_limit_minutes = 0;
    
    if ($time_limit_type === 'days') {
        $time_limit_days = $time_limit_duration;
    } elseif ($time_limit_type === 'hours') {
        // Stunden in Tage und Stunden aufteilen
        $time_limit_days = floor($time_limit_duration / 24);
        $time_limit_hours = $time_limit_duration % 24;
    } elseif ($time_limit_type === 'minutes') {
        // Minuten in Tage, Stunden und Minuten aufteilen
        $total_minutes = $time_limit_duration;
        $time_limit_days = floor($total_minutes / (24 * 60));
        $remaining_minutes = $total_minutes % (24 * 60);
        $time_limit_hours = floor($remaining_minutes / 60);
        $time_limit_minutes = $remaining_minutes % 60;
    }
    
    update_post_meta($product_id, '_time_limit_days', $time_limit_days);
    update_post_meta($product_id, '_time_limit_hours', $time_limit_hours);
    update_post_meta($product_id, '_time_limit_minutes', $time_limit_minutes);
    
    $product->save();
    
    wp_send_json_success(array(
        'product_id' => $product_id,
        'stock' => $stock,
        'price' => $price,
        'has_offer' => $has_offer,
        'sale_price' => $sale_price
    ));
}

// AJAX Handler für Bulk-Aktionen
add_action('wp_ajax_create_bulk_sale', 'create_bulk_sale');
add_action('wp_ajax_nopriv_create_bulk_sale', 'create_bulk_sale');

function create_bulk_sale() {
    check_ajax_referer('product_list_nonce', 'nonce');
    
    if (!current_user_can('manage_options')) {
        wp_send_json_error('Unauthorized');
    }
    
    $title = sanitize_text_field($_POST['title']);
    $start_date = sanitize_text_field($_POST['start_date']);
    $end_date = sanitize_text_field($_POST['end_date']);
    $show_end_date = isset($_POST['show_end_date']) ? intval($_POST['show_end_date']) : 0;
    $groups = isset($_POST['groups']) ? json_decode(stripslashes($_POST['groups']), true) : array();
    
    error_log('DEBUG: create_bulk_sale - Gruppen: ' . print_r($groups, true));
    
    // Bulk-Sales aus Optionen laden
    $bulk_sales = get_option('_bulk_sales', array());
    
    // Neue ID generieren
    $sale_id = uniqid('sale_');
    
    // Neue Bulk-Aktion erstellen
    $bulk_sales[$sale_id] = array(
        'id' => $sale_id,
        'title' => $title,
        'start_date' => $start_date,
        'end_date' => $end_date,
        'show_end_date' => $show_end_date,
        'groups' => array()
    );
    
    // Gruppen verarbeiten und Meta-Daten für Produkte setzen
    foreach ($groups as $group) {
        $group_id = $group['id'];
        $bulk_sales[$sale_id]['groups'][$group_id] = array(
            'id' => $group_id,
            'name' => $group['name'],
            'has_offer' => isset($group['has_offer']) ? $group['has_offer'] : false,
            'discount_type' => isset($group['discount_type']) ? $group['discount_type'] : 'fixed',
            'discount_amount' => isset($group['discount_amount']) ? $group['discount_amount'] : '',
            'show_old_price' => isset($group['show_old_price']) ? $group['show_old_price'] : false,
            'products' => isset($group['products']) ? $group['products'] : array()
        );
        
        // Meta-Daten für Produkte setzen
        if (isset($group['products']) && is_array($group['products'])) {
            foreach ($group['products'] as $product_id) {
                update_post_meta($product_id, '_part_of_sale', true);
                update_post_meta($product_id, '_sale_id', $sale_id);
                update_post_meta($product_id, '_sale_group_id', $group_id);
                
                // Wenn Gruppe ein Angebot hat, WooCommerce Angebotsdaten setzen
                if (isset($group['has_offer']) && $group['has_offer']) {
                    $product = wc_get_product($product_id);
                    if ($product) {
                        $regular_price = $product->get_regular_price();
                        $discount_amount = isset($group['discount_amount']) ? floatval($group['discount_amount']) : 0;
                        $discount_type = isset($group['discount_type']) ? $group['discount_type'] : 'fixed';
                        
                        // Angebotspreis berechnen
                        if ($discount_type === 'percent') {
                            $sale_price = $regular_price * (1 - ($discount_amount / 100));
                        } else {
                            $sale_price = $regular_price - $discount_amount;
                        }
                        
                        // Angebotspreis darf nicht negativ sein
                        if ($sale_price < 0) {
                            $sale_price = 0;
                        }
                        
                        // Originalen Preis speichern
                        update_post_meta($product_id, '_original_regular_price', $regular_price);
                        
                        // WooCommerce Angebotsdaten setzen
                        $product->set_sale_price($sale_price);
                        
                        // Start- und Enddatum setzen
                        if ($start_date) {
                            $date_parts = explode(' ', $start_date);
                            if (count($date_parts) === 2) {
                                $date_parts_date = explode('.', $date_parts[0]);
                                if (count($date_parts_date) === 3) {
                                    $day = $date_parts_date[0];
                                    $month = $date_parts_date[1];
                                    $year = $date_parts_date[2];
                                    $time = $date_parts[1];
                                    $start_time = strtotime($year . '-' . $month . '-' . $day . ' ' . $time . ' Europe/Berlin');
                                    $product->set_date_on_sale_from($start_time);
                                }
                            }
                        }
                        
                        if ($end_date) {
                            $date_parts = explode(' ', $end_date);
                            if (count($date_parts) === 2) {
                                $date_parts_date = explode('.', $date_parts[0]);
                                if (count($date_parts_date) === 3) {
                                    $day = $date_parts_date[0];
                                    $month = $date_parts_date[1];
                                    $year = $date_parts_date[2];
                                    $time = $date_parts[1];
                                    $end_time = strtotime($year . '-' . $month . '-' . $day . ' ' . $time . ' Europe/Berlin');
                                    $product->set_date_on_sale_to($end_time);
                                }
                            }
                        }
                        
                        $product->save();
                        
                        // Meta-Daten für Angebot setzen
                        update_post_meta($product_id, '_show_old_price', isset($group['show_old_price']) ? $group['show_old_price'] : false);
                        update_post_meta($product_id, '_time_limited', !empty($end_date));
                        update_post_meta($product_id, '_time_limit_type', 'date');
                        update_post_meta($product_id, '_time_limit_date_only', $end_date);
                        update_post_meta($product_id, '_time_limit_time', isset(explode(' ', $end_date)[1]) ? explode(' ', $end_date)[1] : '');
                        update_post_meta($product_id, '_offer_start_date', $start_date);
                        update_post_meta($product_id, '_has_offer', '1');
                        update_post_meta($product_id, '_offer_start_activated', '1'); // Da Angebot sofort aktiv ist
                        
                        // Rabatttyp speichern
                        update_post_meta($product_id, '_price_type_toggle', $discount_type === 'percent' ? '1' : '0');
                        
                        // Cache leeren
                        wc_delete_product_transients($product_id);
                        wp_cache_delete($product_id, 'products');
                    }
                }
            }
        }
    }
    
    // Speichern
    update_option('_bulk_sales', $bulk_sales);
    
    wp_send_json_success(array('sale_id' => $sale_id));
}

add_action('wp_ajax_get_bulk_sales', 'get_bulk_sales');
add_action('wp_ajax_nopriv_get_bulk_sales', 'get_bulk_sales');

function get_bulk_sales() {
    check_ajax_referer('product_list_nonce', 'nonce');
    
    if (!current_user_can('manage_options')) {
        wp_send_json_error('Unauthorized');
    }
    
    $bulk_sales = get_option('_bulk_sales', array());
    wp_send_json_success($bulk_sales);
}

add_action('wp_ajax_get_bulk_sale_details', 'get_bulk_sale_details');
add_action('wp_ajax_nopriv_get_bulk_sale_details', 'get_bulk_sale_details');

function get_bulk_sale_details() {
    check_ajax_referer('product_list_nonce', 'nonce');
    
    if (!current_user_can('manage_options')) {
        wp_send_json_error('Unauthorized');
    }
    
    $sale_id = sanitize_text_field($_POST['sale_id']);
    $bulk_sales = get_option('_bulk_sales', array());
    
    if (isset($bulk_sales[$sale_id])) {
        wp_send_json_success($bulk_sales[$sale_id]);
    } else {
        wp_send_json_error('Sale not found');
    }
}

add_action('wp_ajax_update_bulk_sale', 'update_bulk_sale');
add_action('wp_ajax_nopriv_update_bulk_sale', 'update_bulk_sale');

function update_bulk_sale() {
    check_ajax_referer('product_list_nonce', 'nonce');
    
    if (!current_user_can('manage_options')) {
        wp_send_json_error('Unauthorized');
    }
    
    $sale_id = sanitize_text_field($_POST['sale_id']);
    $title = sanitize_text_field($_POST['title']);
    $start_date = sanitize_text_field($_POST['start_date']);
    $end_date = sanitize_text_field($_POST['end_date']);
    $show_end_date = isset($_POST['show_end_date']) ? intval($_POST['show_end_date']) : 0;
    $groups = isset($_POST['groups']) ? json_decode(stripslashes($_POST['groups']), true) : array();
    
    $bulk_sales = get_option('_bulk_sales', array());
    
    if (isset($bulk_sales[$sale_id])) {
        $bulk_sales[$sale_id]['title'] = $title;
        $bulk_sales[$sale_id]['start_date'] = $start_date;
        $bulk_sales[$sale_id]['end_date'] = $end_date;
        $bulk_sales[$sale_id]['show_end_date'] = $show_end_date;
        $bulk_sales[$sale_id]['groups'] = $groups;
        
        // Alle Produkte aus dem alten Sale entfernen (um Konflikte zu vermeiden)
        foreach ($bulk_sales[$sale_id]['groups'] as $old_group) {
            if (isset($old_group['products'])) {
                foreach ($old_group['products'] as $product_id) {
                    delete_post_meta($product_id, '_part_of_sale');
                    delete_post_meta($product_id, '_sale_id');
                    delete_post_meta($product_id, '_sale_group_id');
                    
                    // WooCommerce Angebotsdaten entfernen
                    $product = wc_get_product($product_id);
                    if ($product) {
                        $saved_regular_price = get_post_meta($product_id, '_original_regular_price', true);
                        if ($saved_regular_price) {
                            $product->set_sale_price('');
                            $product->set_date_on_sale_from('');
                            $product->set_date_on_sale_to('');
                            $product->set_regular_price($saved_regular_price);
                            $product->save();
                        }
                        
                        // Angebots-Meta-Daten entfernen
                        delete_post_meta($product_id, '_has_offer');
                        delete_post_meta($product_id, '_show_old_price');
                        delete_post_meta($product_id, '_time_limited');
                        delete_post_meta($product_id, '_offer_start_date');
                        
                        // Cache leeren
                        wc_delete_product_transients($product_id);
                        wp_cache_delete($product_id, 'products');
                    }
                }
            }
        }
        
        // Neue Gruppen verarbeiten und Meta-Daten für Produkte setzen
        foreach ($groups as $group) {
            $group_id = $group['id'];
            
            // Meta-Daten für Produkte setzen
            if (isset($group['products']) && is_array($group['products'])) {
                foreach ($group['products'] as $product_id) {
                    update_post_meta($product_id, '_part_of_sale', true);
                    update_post_meta($product_id, '_sale_id', $sale_id);
                    update_post_meta($product_id, '_sale_group_id', $group_id);
                    
                    // Wenn Gruppe ein Angebot hat, WooCommerce Angebotsdaten setzen
                    if (isset($group['has_offer']) && $group['has_offer']) {
                        $product = wc_get_product($product_id);
                        if ($product) {
                            $regular_price = $product->get_regular_price();
                            $discount_amount = isset($group['discount_amount']) ? floatval($group['discount_amount']) : 0;
                            $discount_type = isset($group['discount_type']) ? $group['discount_type'] : 'fixed';
                            
                            // Angebotspreis berechnen
                            if ($discount_type === 'percent') {
                                $sale_price = $regular_price * (1 - ($discount_amount / 100));
                            } else {
                                $sale_price = $regular_price - $discount_amount;
                            }
                            
                            // Angebotspreis darf nicht negativ sein
                            if ($sale_price < 0) {
                                $sale_price = 0;
                            }
                            
                            // Originalen Preis speichern
                            update_post_meta($product_id, '_original_regular_price', $regular_price);
                            
                            // WooCommerce Angebotsdaten setzen
                            $product->set_sale_price($sale_price);
                            
                            // Start- und Enddatum setzen
                            if ($start_date) {
                                $date_parts = explode(' ', $start_date);
                                if (count($date_parts) === 2) {
                                    $date_parts_date = explode('.', $date_parts[0]);
                                    if (count($date_parts_date) === 3) {
                                        $day = $date_parts_date[0];
                                        $month = $date_parts_date[1];
                                        $year = $date_parts_date[2];
                                        $time = $date_parts[1];
                                        $start_time = strtotime($year . '-' . $month . '-' . $day . ' ' . $time . ' Europe/Berlin');
                                        $product->set_date_on_sale_from($start_time);
                                    }
                                }
                            }
                            
                            if ($end_date) {
                                $date_parts = explode(' ', $end_date);
                                if (count($date_parts) === 2) {
                                    $date_parts_date = explode('.', $date_parts[0]);
                                    if (count($date_parts_date) === 3) {
                                        $day = $date_parts_date[0];
                                        $month = $date_parts_date[1];
                                        $year = $date_parts_date[2];
                                        $time = $date_parts[1];
                                        $end_time = strtotime($year . '-' . $month . '-' . $day . ' ' . $time . ' Europe/Berlin');
                                        $product->set_date_on_sale_to($end_time);
                                    }
                                }
                            }
                            
                            $product->save();
                            
                            // Meta-Daten für Angebot setzen
                            update_post_meta($product_id, '_show_old_price', isset($group['show_old_price']) ? $group['show_old_price'] : false);
                            update_post_meta($product_id, '_time_limited', !empty($end_date));
                            update_post_meta($product_id, '_time_limit_type', 'date');
                            update_post_meta($product_id, '_time_limit_date_only', $end_date);
                            update_post_meta($product_id, '_time_limit_time', isset(explode(' ', $end_date)[1]) ? explode(' ', $end_date)[1] : '');
                            update_post_meta($product_id, '_offer_start_date', $start_date);
                            update_post_meta($product_id, '_has_offer', '1');
                            update_post_meta($product_id, '_offer_start_activated', '1');
                            
                            // Rabatttyp speichern
                            update_post_meta($product_id, '_price_type_toggle', $discount_type === 'percent' ? '1' : '0');
                            
                            // Cache leeren
                            wc_delete_product_transients($product_id);
                            wp_cache_delete($product_id, 'products');
                        }
                    }
                }
            }
        }
        
        update_option('_bulk_sales', $bulk_sales);
        wp_send_json_success(array('sale_id' => $sale_id));
    } else {
        wp_send_json_error('Sale not found');
    }
}

add_action('wp_ajax_delete_bulk_sale', 'delete_bulk_sale');
add_action('wp_ajax_nopriv_delete_bulk_sale', 'delete_bulk_sale');

function delete_bulk_sale() {
    check_ajax_referer('product_list_nonce', 'nonce');
    
    if (!current_user_can('manage_options')) {
        wp_send_json_error('Unauthorized');
    }
    
    $sale_id = sanitize_text_field($_POST['sale_id']);
    $bulk_sales = get_option('_bulk_sales', array());
    
    if (isset($bulk_sales[$sale_id])) {
        // Produkte aus der Aktion entfernen
        foreach ($bulk_sales[$sale_id]['groups'] as $group) {
            if (isset($group['products'])) {
                foreach ($group['products'] as $product_id) {
                    delete_post_meta($product_id, '_part_of_sale');
                    delete_post_meta($product_id, '_sale_id');
                    delete_post_meta($product_id, '_sale_group_id');
                    
                    // WooCommerce Angebotsdaten entfernen
                    $product = wc_get_product($product_id);
                    if ($product) {
                        $saved_regular_price = get_post_meta($product_id, '_original_regular_price', true);
                        if ($saved_regular_price) {
                            $product->set_sale_price('');
                            $product->set_date_on_sale_from('');
                            $product->set_date_on_sale_to('');
                            $product->set_regular_price($saved_regular_price);
                            $product->save();
                        }
                        
                        // Angebots-Meta-Daten entfernen
                        delete_post_meta($product_id, '_has_offer');
                        delete_post_meta($product_id, '_show_old_price');
                        delete_post_meta($product_id, '_time_limited');
                        delete_post_meta($product_id, '_offer_start_date');
                        
                        // Cache leeren
                        wc_delete_product_transients($product_id);
                        wp_cache_delete($product_id, 'products');
                    }
                }
            }
        }
        
        unset($bulk_sales[$sale_id]);
        update_option('_bulk_sales', $bulk_sales);
        wp_send_json_success();
    } else {
        wp_send_json_error('Sale not found');
    }
}

add_action('wp_ajax_add_product_to_sale_group', 'add_product_to_sale_group');
add_action('wp_ajax_nopriv_add_product_to_sale_group', 'add_product_to_sale_group');

function add_product_to_sale_group() {
    check_ajax_referer('product_list_nonce', 'nonce');
    
    if (!current_user_can('manage_options')) {
        wp_send_json_error('Unauthorized');
    }
    
    $sale_id = sanitize_text_field($_POST['sale_id']);
    $group_id = sanitize_text_field($_POST['group_id']);
    $product_id = intval($_POST['product_id']);
    
    // Prüfen, ob Produkt bereits Teil einer Aktion ist
    $existing_sale_id = get_post_meta($product_id, '_sale_id', true);
    if ($existing_sale_id) {
        wp_send_json_error('Produkt ist bereits Teil einer Aktion');
    }
    
    // Meta-Daten setzen
    update_post_meta($product_id, '_part_of_sale', true);
    update_post_meta($product_id, '_sale_id', $sale_id);
    update_post_meta($product_id, '_sale_group_id', $group_id);
    
    wp_send_json_success();
}

add_action('wp_ajax_remove_product_from_sale_group', 'remove_product_from_sale_group');
add_action('wp_ajax_nopriv_remove_product_from_sale_group', 'remove_product_from_sale_group');

function remove_product_from_sale_group() {
    check_ajax_referer('product_list_nonce', 'nonce');
    
    if (!current_user_can('manage_options')) {
        wp_send_json_error('Unauthorized');
    }
    
    $product_id = intval($_POST['product_id']);
    
    // Meta-Daten löschen
    delete_post_meta($product_id, '_part_of_sale');
    delete_post_meta($product_id, '_sale_id');
    delete_post_meta($product_id, '_sale_group_id');
    
    wp_send_json_success();
}

// Funktion zum automatischen Aktivieren von Angeboten mit Startzeitpunkt
add_action('wp_ajax_activate_offers_with_start_date', 'activate_offers_with_start_date');
add_action('wp_ajax_nopriv_activate_offers_with_start_date', 'activate_offers_with_start_date');

function activate_offers_with_start_date() {
    check_ajax_referer('product_list_nonce', 'nonce');
    
    if (!current_user_can('manage_options')) {
        wp_send_json_error('Unauthorized');
    }
    
    $now = current_time('timestamp', true);
    $activated_count = 0;
    
    // Alle Produkte mit Angeboten und Startdatum durchlaufen
    $args = array(
        'post_type' => 'product',
        'posts_per_page' => -1,
        'meta_query' => array(
            'relation' => 'AND',
            array(
                'key' => '_has_offer',
                'value' => '1',
                'compare' => '='
            ),
            array(
                'key' => '_offer_start_date',
                'compare' => 'EXISTS'
            ),
            array(
                'key' => '_offer_start_activated',
                'compare' => 'NOT EXISTS'
            )
        )
    );
    
    $products = get_posts($args);
    
    foreach ($products as $post) {
        $product = wc_get_product($post->ID);
        if (!$product) {
            continue;
        }
        
        $offer_start_date = get_post_meta($product->get_id(), '_offer_start_date', true);
        
        if ($offer_start_date) {
            // TT.MM.JJJJ HH:MM Format parsen
            $date_parts = explode(' ', $offer_start_date);
            if (count($date_parts) === 2) {
                $date = $date_parts[0];
                $time = $date_parts[1];
                $date_parts = explode('.', $date);
                if (count($date_parts) === 3) {
                    $day = intval($date_parts[0]);
                    $month = intval($date_parts[1]);
                    $year = intval($date_parts[2]);
                    $start_time = strtotime($year . '-' . $month . '-' . $day . ' ' . $time . ' Europe/Berlin');
                    
                    // Prüfen, ob Startzeitpunkt erreicht ist
                    if ($start_time && $start_time <= $now) {
                        // Angebot aktivieren
                        $sale_price = $product->get_sale_price();
                        if ($sale_price) {
                            $product->set_date_on_sale_from($start_time);
                            $product->save();
                            
                            // Als aktiviert markieren
                            update_post_meta($product->get_id(), '_offer_start_activated', true);
                            $activated_count++;
                        }
                    }
                }
            }
        }
    }
    
    wp_send_json_success(array('activated_count' => $activated_count));
}

// Redirect für nicht verkäufliche Produkte
add_action('template_redirect', 'redirect_hidden_products');

// Abgelaufene Angebote beim Laden der Produktseite bereinigen
add_action('template_redirect', 'cleanup_expired_sales_on_product_page');

function cleanup_expired_sales_on_product_page() {
    // Nur für Produkt-Seiten
    if (!is_product()) {
        return;
    }
    
    $product_id = get_queried_object_id();
    if (!$product_id) {
        return;
    }
    
    $product = wc_get_product($product_id);
    if (!$product) {
        return;
    }
    
    // Prüfen, ob Angebot abgelaufen ist und bereinigen
    if ($product->is_on_sale()) {
        $date_to = $product->get_date_on_sale_to();
        if ($date_to) {
            $now = current_time('timestamp', true);
            $end_time = $date_to->getTimestamp();
            
            // Wenn das Angebot abgelaufen ist
            if ($end_time < $now) {
                // Zuerst _original_regular_price prüfen (der Preis VOR dem Angebot)
                $saved_regular_price = get_post_meta($product->get_id(), '_original_regular_price', true);
                
                // Falls nicht vorhanden, _saved_regular_price prüfen
                if (!$saved_regular_price) {
                    $saved_regular_price = get_post_meta($product->get_id(), '_saved_regular_price', true);
                }
                
                // Fallback: Falls nichts existiert, den aktuellen regular_price verwenden
                if (!$saved_regular_price) {
                    $saved_regular_price = $product->get_regular_price();
                }
                
                if ($saved_regular_price) {
                    // Angebot im WooCommerce-Produkt löschen
                    $product->set_sale_price('');
                    $product->set_date_on_sale_to('');
                    $product->set_date_on_sale_from('');
                    $product->set_regular_price($saved_regular_price);
                    $product->save();
                    
                    error_log('DEBUG: Nachher - regular_price=' . $product->get_regular_price() . ', sale_price=' . $product->get_sale_price());
                    
                    // Angebots-Meta-Daten löschen
                    update_post_meta($product->get_id(), '_sale_price', '');
                    update_post_meta($product->get_id(), '_sale_price_dates_from', '');
                    update_post_meta($product->get_id(), '_sale_price_dates_to', '');
                    
                    // Umfassendes Cache-Leeren
                    wc_delete_product_transients($product->get_id());
                    wp_cache_delete($product->get_id(), 'products');
                    wp_cache_flush();
                    delete_transient('wc_products_onsale');
                }
            }
        }
    }
}

// "Vorher X jetzt Y" Anzeige auf Produktseite
add_filter('woocommerce_get_price_html', 'custom_price_html', 10, 2);

function custom_price_html($price, $product) {
    // Prüfen, ob Angebot abgelaufen ist und bereinigen
    if ($product->is_on_sale()) {
        $date_to = $product->get_date_on_sale_to();
        if ($date_to) {
            $now = current_time('timestamp', true);
            $end_time = $date_to->getTimestamp();
            
            // Wenn das Angebot abgelaufen ist
            if ($end_time < $now) {
                // Zuerst _original_regular_price prüfen (der Preis VOR dem Angebot)
                $saved_regular_price = get_post_meta($product->get_id(), '_original_regular_price', true);
                
                // Falls nicht vorhanden, _saved_regular_price prüfen
                if (!$saved_regular_price) {
                    $saved_regular_price = get_post_meta($product->get_id(), '_saved_regular_price', true);
                }
                
                // Fallback: Falls nichts existiert, den aktuellen regular_price verwenden
                if (!$saved_regular_price) {
                    $saved_regular_price = $product->get_regular_price();
                }
                
                if ($saved_regular_price) {
                    // Angebot im WooCommerce-Produkt löschen
                    $product->set_sale_price('');
                    $product->set_date_on_sale_to('');
                    $product->set_date_on_sale_from('');
                    $product->set_regular_price($saved_regular_price);
                    $product->save();
                    
                    // Angebots-Meta-Daten löschen
                    update_post_meta($product->get_id(), '_sale_price', '');
                    update_post_meta($product->get_id(), '_sale_price_dates_from', '');
                    update_post_meta($product->get_id(), '_sale_price_dates_to', '');
                    
                    // Umfassendes Cache-Leeren
                    wc_delete_product_transients($product->get_id());
                    wp_cache_delete($product->get_id(), 'products');
                    wp_cache_flush();
                    delete_transient('wc_products_onsale');
                    
                    // Produkt neu laden
                    $product = wc_get_product($product->get_id());
                }
            }
        }
    }
    
    $show_old_price = get_post_meta($product->get_id(), '_show_old_price', true);
    
    if ($product->is_on_sale() && $show_old_price) {
        $regular_price = $product->get_regular_price();
        $sale_price = $product->get_sale_price();
        
        $price = '<del>' . wc_price($regular_price) . '</del> <ins>' . wc_price($sale_price) . '</ins>';
    }
    
    return $price;
}

// Produktnamen durchstreichen, wenn nicht zum Verkauf ausstehend (nur für Admins)
add_action('wp_footer', 'add_strikethrough_script_for_not_sellable_products');

function add_strikethrough_script_for_not_sellable_products() {
    if (!is_product() || !current_user_can('manage_options')) {
        return;
    }
    
    global $product;
    if (!$product) {
        return;
    }
    
    $product_id = $product->get_id();
    $sellable = get_post_meta($product_id, 'sellable', true);
    error_log('DEBUG: Produkt ID: ' . $product_id);
    error_log('DEBUG: sellable Wert: ' . var_export($sellable, true));
    
    // Nur wenn sellable explizit auf '0' gesetzt ist, ist das Produkt nicht zum Verkauf ausstehend
    if ($sellable === '0') {
        error_log('DEBUG: JavaScript wird hinzugefügt');
        ?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var titleElements = document.querySelectorAll('.product_title, .woocommerce-product-title, .entry-title, h1.product_title');
                titleElements.forEach(function(element) {
                    element.style.textDecoration = 'line-through';
                    element.style.color = '#999';
                });
            });
        </script>
        <?php
    } else {
        error_log('DEBUG: JavaScript wird nicht hinzugefügt, sellable ist: ' . $sellable);
    }
}

// Angebotsende auf Produktseite anzeigen
add_action('woocommerce_single_product_summary', 'display_offer_end_date', 25);

function display_offer_end_date() {
    global $product;
    
    if (!$product) {
        return;
    }
    
    // Prüfen, ob Angebot abgelaufen ist und bereinigen
    if ($product->is_on_sale()) {
        $date_to = $product->get_date_on_sale_to();
        if ($date_to) {
            $now = current_time('timestamp', true);
            $end_time = $date_to->getTimestamp();
            
            // Wenn das Angebot abgelaufen ist
            if ($end_time < $now) {
                // Gespeicherten Original-Preis abrufen
                $saved_regular_price = get_post_meta($product->get_id(), '_original_regular_price', true);
                if (!$saved_regular_price) {
                    $saved_regular_price = get_post_meta($product->get_id(), '_saved_regular_price', true);
                }
                
                if (!$saved_regular_price) {
                    // Fallback: aktuellen regular_price verwenden
                    $saved_regular_price = $product->get_regular_price();
                }
                
                if ($saved_regular_price) {
                    // Angebot im WooCommerce-Produkt löschen
                    $product->set_sale_price('');
                    $product->set_date_on_sale_to('');
                    $product->set_date_on_sale_from('');
                    $product->set_regular_price($saved_regular_price);
                    $product->save();
                    
                    // Angebots-Meta-Daten löschen
                    update_post_meta($product->get_id(), '_sale_price', '');
                    update_post_meta($product->get_id(), '_sale_price_dates_from', '');
                    update_post_meta($product->get_id(), '_sale_price_dates_to', '');
                    
                    // Umfassendes Cache-Leeren
                    wc_delete_product_transients($product->get_id());
                    wp_cache_delete($product->get_id(), 'products');
                    wp_cache_flush();
                    delete_transient('wc_products_onsale');
                    
                    // Produkt neu laden
                    $product = wc_get_product($product->get_id());
                }
            }
        }
    }
    
    if (!$product->is_on_sale()) {
        return;
    }
    
    $show_end_date = get_post_meta($product->get_id(), '_show_end_date', true);
    $time_limited = get_post_meta($product->get_id(), '_time_limited', true);
    
    if ($show_end_date && $time_limited) {
        $date_to = $product->get_date_on_sale_to();
        if ($date_to) {
            $now = current_time('timestamp', true);
            $end_time = $date_to->getTimestamp();
            $diff = $end_time - $now;
            
            if ($diff > 0) {
                $days = floor($diff / (24 * 60 * 60));
                
                if ($days >= 1) {
                    // Mehr als 1 Tag: Datum anzeigen (in lokale Zeit konvertieren)
                    $end_date_local = get_date_from_gmt(date('Y-m-d H:i:s', $end_time), 'd.m.Y H:i');
                    echo '<div class="offer-end-display" style="margin-top: 10px; color: #666; font-size: 14px;">';
                    echo '<span style="background: #e74c3c; color: white; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: bold;">Angebot endet am: ' . $end_date_local . '</span>';
                    echo '</div>';
                } else {
                    // Weniger als 1 Tag: Countdown in HH:MM:SS
                    $hours = floor($diff / (60 * 60));
                    $minutes = floor(($diff % (60 * 60)) / 60);
                    $seconds = $diff % 60;
                    
                    $h = str_pad($hours, 2, '0', STR_PAD_LEFT);
                    $m = str_pad($minutes, 2, '0', STR_PAD_LEFT);
                    $s = str_pad($seconds, 2, '0', STR_PAD_LEFT);
                    
                    echo '<div class="offer-end-display" style="margin-top: 10px; color: #666; font-size: 14px;">';
                    echo '<span class="countdown" data-countdown="' . $end_time . '" style="background: #e74c3c; color: white; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: bold;">Angebot endet in ' . $h . ':' . $m . ':' . $s . '</span>';
                    echo '</div>';
                }
            }
        }
    }
}

// JavaScript für Countdown auf Produktseite
add_action('wp_footer', 'add_countdown_script');

function add_countdown_script() {
    if (!is_product()) {
        return;
    }
    
    global $product;
    if (!$product) return;
    
    $product_id = $product->get_id();
    ?>
    <script>
    let isReloading = false;
    
    function updateCountdown() {
        const countdown = document.querySelector('.countdown');
        if (!countdown) return;
        
        const dateTo = parseInt(countdown.dataset.countdown);
        const now = Math.floor(Date.now() / 1000);
        const diff = dateTo - now;
        
        if (diff > 0) {
            const hours = Math.floor(diff / (60 * 60));
            const minutes = Math.floor((diff % (60 * 60)) / 60);
            const seconds = diff % 60;
            
            const h = String(hours).padStart(2, '0');
            const m = String(minutes).padStart(2, '0');
            const s = String(seconds).padStart(2, '0');
            
            countdown.textContent = 'Angebot endet in ' + h + ':' + m + ':' + s;
        } else {
            countdown.textContent = 'Angebot abgelaufen';
            countdown.style.color = '#e74c3c';
            // Zuerst AJAX-Call für Cleanup, dann Seite neu laden (nur einmal)
            if (!isReloading) {
                isReloading = true;
                
                // AJAX-Call zum Bereinigen des abgelaufenen Angebots
                fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'action=cleanup_expired_sale&product_id=<?php echo $product_id; ?>'
                })
                .then(response => response.json())
                .then(data => {
                    console.log('Cleanup result:', data);
                    // Seite neu laden nach erfolgreichem Cleanup
                    location.reload();
                })
                .catch(error => {
                    console.error('Cleanup error:', error);
                    // Trotzdem neu laden bei Fehler
                    location.reload();
                });
            }
        }
    }
    
    setInterval(updateCountdown, 1000);
    updateCountdown();
    </script>
    <?php
}

function redirect_hidden_products() {
    // Nur für Produkt-Seiten und nicht für Admins
    if (!is_product() || is_admin() || current_user_can('manage_options')) {
        return;
    }
    
    global $product;
    if (!$product || !is_object($product)) {
        $product_id = get_queried_object_id();
        $product = wc_get_product($product_id);
    }
    
    if ($product && is_object($product)) {
        $visibility = $product->get_catalog_visibility();
        
        if ($visibility === 'hidden') {
            // Redirect zur "Nicht verfügbar" Seite
            $not_available_page = get_page_by_path('nicht-verfuegbar');
            if ($not_available_page) {
                wp_redirect(get_permalink($not_available_page->ID));
                exit;
            }
        }
    }
}

// Admin-Buttons nach Abmelden-Link im Footer einfügen
add_action('wp_footer', 'add_admin_buttons_after_logout', 999);

function add_admin_buttons_after_logout() {
    // Nur für Admins
    if (!current_user_can('manage_options')) {
        return;
    }
    
    $add_product_url = admin_url('post-new.php?post_type=product');
    $product_list_url = home_url('/produkt-liste/');
    
    ?>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Suche nach Abmelden-Link im Footer
        var logoutLinks = document.querySelectorAll('a[href*="logout"]');
        logoutLinks.forEach(function(link) {
            if (link.textContent.includes('Abmelden') || link.textContent.includes('Logout')) {
                // Finde das w-iconbox Element
                var iconbox = link.closest('.w-iconbox');
                if (iconbox) {
                    // Erstelle w-iconbox für Produkt hinzufügen
                    var addIconbox = document.createElement('div');
                    addIconbox.className = 'w-iconbox iconpos_left style_default color_contrast align_left no_text';
                    
                    addIconbox.innerHTML = `
                        <a href='<?php echo esc_url($add_product_url); ?>' class='w-iconbox-link'>
                            <div class='w-iconbox-icon' style='font-size:1.2rem;'>
                                <i class='far fa-plus'></i>
                            </div>
                        </a>
                        <div class='w-iconbox-meta'>
                            <a href='<?php echo esc_url($add_product_url); ?>' class='w-iconbox-link'>
                                <div class='w-iconbox-title'>Produkt hinzufügen</div>
                            </a>
                        </div>
                    `;
                    
                    // Erstelle w-iconbox für Produktliste
                    var listIconbox = document.createElement('div');
                    listIconbox.className = 'w-iconbox iconpos_left style_default color_contrast align_left no_text';
                    
                    listIconbox.innerHTML = `
                        <a href='<?php echo esc_url($product_list_url); ?>' class='w-iconbox-link'>
                            <div class='w-iconbox-icon' style='font-size:1.2rem;'>
                                <i class='far fa-list'></i>
                            </div>
                        </a>
                        <div class='w-iconbox-meta'>
                            <a href='<?php echo esc_url($product_list_url); ?>' class='w-iconbox-link'>
                                <div class='w-iconbox-title'>Produktliste</div>
                            </a>
                        </div>
                    `;
                    
                    // Separatoren hinzufügen
                    var separator1 = document.createElement('div');
                    separator1.className = 'w-separator size_custom';
                    separator1.style.height = '5px';
                    
                    var separator2 = document.createElement('div');
                    separator2.className = 'w-separator size_custom';
                    separator2.style.height = '5px';
                    
                    // Nach dem Abmelden-iconbox einfügen
                    iconbox.parentNode.insertBefore(separator1, iconbox.nextSibling);
                    separator1.parentNode.insertBefore(addIconbox, separator1.nextSibling);
                    addIconbox.parentNode.insertBefore(separator2, addIconbox.nextSibling);
                    separator2.parentNode.insertBefore(listIconbox, separator2.nextSibling);
                    
                    // Erstelle w-iconbox für Bestellungen
                    var ordersIconbox = document.createElement('div');
                    ordersIconbox.className = 'w-iconbox iconpos_left style_default color_contrast align_left no_text';
                    
                    ordersIconbox.innerHTML = `
                        <a href='<?php echo esc_url(home_url('/bestellungen/')); ?>' class='w-iconbox-link'>
                            <div class='w-iconbox-icon' style='font-size:1.2rem;'>
                                <i class='far fa-shopping-cart'></i>
                            </div>
                        </a>
                        <div class='w-iconbox-meta'>
                            <a href='<?php echo esc_url(home_url('/bestellungen/')); ?>' class='w-iconbox-link'>
                                <div class='w-iconbox-title'>Bestellungen</div>
                            </a>
                        </div>
                    `;
                    
                    var separator3 = document.createElement('div');
                    separator3.className = 'w-separator size_custom';
                    separator3.style.height = '5px';
                    
                    // Nach der Produktliste einfügen
                    listIconbox.parentNode.insertBefore(separator3, listIconbox.nextSibling);
                    separator3.parentNode.insertBefore(ordersIconbox, separator3.nextSibling);
                }
            }
        });
    });
    </script>
    <?php
}

// AJAX Handler für Bestellungsübersicht
add_action('wp_ajax_get_orders_overview', 'get_orders_overview');
add_action('wp_ajax_nopriv_get_orders_overview', 'get_orders_overview');

function get_orders_overview() {
    check_ajax_referer('orders_overview_nonce', 'nonce');
    
    if (!current_user_can('manage_options')) {
        wp_send_json_error('Unauthorized');
    }
    
    // Germanized Shipment Plugin prüfen - mehrere Methoden
    $has_shipments = false;
    if (class_exists('WooCommerce_Germanized_Shipments')) {
        $has_shipments = true;
    } elseif (function_exists('wc_gzd_get_shipment')) {
        $has_shipments = true;
    } elseif (defined('WC_GZD_SHIPMENT_VERSION')) {
        $has_shipments = true;
    }
    
    if (is_plugin_active('woocommerce-germanized/woocommerce-germanized.php') || is_plugin_active('woocommerce-germanized-pro/woocommerce-germanized-pro.php')) {
        $has_shipments = true;
    }
    
    // Bestellungen abrufen
    $args = array(
        'status' => array('pending', 'processing', 'on-hold', 'completed', 'cancelled', 'refunded', 'failed'),
        'limit' => -1,
        'orderby' => 'date',
        'order' => 'DESC'
    );
    
    $orders = wc_get_orders($args);
    
    $pending_orders = array();
    $processing_orders = array();
    $shipped_orders = array();
    
    foreach ($orders as $order) {
        $order_id = $order->get_id();
        $status = $order->get_status();
        
        // Überspringe Rückerstattungen (refunds haben keine get_order_number Methode)
        if (method_exists($order, 'get_type') && $order->get_type() === 'shop_order_refund') {
            continue;
        }
        
        // Überspringe stornierte und abgeschlossene Bestellungen
        if (in_array($status, array('cancelled', 'completed'))) {
            continue;
        }
        
        $order_data = array(
            'id' => $order_id,
            'number' => $order->get_order_number(),
            'date' => $order->get_date_created()->date_i18n('d.m.Y H:i'),
            'total' => $order->get_formatted_order_total(),
            'user_id' => $order->get_user_id(),
            'user_name' => $order->get_formatted_billing_full_name(),
            'comment' => $order->get_customer_note(),
            'items' => array()
        );
        
        // Adresse formatieren
        $address = array();
        $address[] = $order->get_billing_first_name() . ' ' . $order->get_billing_last_name();
        $address[] = $order->get_billing_postcode() . ' ' . $order->get_billing_city();
        $address[] = $order->get_billing_address_1();
        if ($order->get_billing_address_2()) {
            $address[] = $order->get_billing_address_2();
        }
        if ($order->get_billing_phone()) {
            $address[] = '📞 ' . $order->get_billing_phone();
        }
        $order_data['address'] = implode(', ', $address);
        
        // Items
        foreach ($order->get_items() as $item) {
            $order_data['items'][] = array(
                'name' => $item->get_name(),
                'quantity' => $item->get_quantity(),
                'total' => $order->get_formatted_line_subtotal($item)
            );
        }
        
        // Status-Mapping
        if ($status === 'pending') {
            $pending_orders[] = $order_data;
        } elseif ($status === 'on-hold') {
            // on-hord auch in Wartestellung anzeigen
            $pending_orders[] = $order_data;
        } elseif ($status === 'processing') {
            // Prüfen, ob bereits versandt wurde (Germanized Shipment)
            $is_shipped = false;
            $shipped_date = '';
            
            error_log('DEBUG: Order ' . $order_id . ' is processing, checking shipments. has_shipments: ' . ($has_shipments ? 'yes' : 'no'));
            
            if ($has_shipments) {
                $shipments = wc_gzd_get_shipments_by_order($order_id);
                error_log('DEBUG: Found ' . count($shipments) . ' shipments for order ' . $order_id);
                foreach ($shipments as $shipment) {
                    $shipment_status = $shipment->get_status();
                    error_log('DEBUG: Shipment ID ' . $shipment->get_id() . ' has status: ' . $shipment_status);
                    if ($shipment_status === 'shipped' || $shipment_status === 'delivered') {
                        $is_shipped = true;
                        $shipped_date = $shipment->get_date_sent()->date_i18n('d.m.Y H:i');
                        error_log('DEBUG: Order ' . $order_id . ' is shipped, date: ' . $shipped_date);
                        break;
                    }
                }
            }
            
            // Fallback: Prüfe Meta für lokalen Versandstatus
            if (!$is_shipped) {
                $is_shipped = get_post_meta($order_id, '_is_shipped', true) === '1';
                $shipped_date = get_post_meta($order_id, '_shipped_date', true);
                if ($shipped_date) {
                    $shipped_date = date('d.m.Y H:i', strtotime($shipped_date));
                }
                error_log('DEBUG: Order ' . $order_id . ' fallback check - is_shipped: ' . ($is_shipped ? 'yes' : 'no'));
            }
            
            if ($is_shipped) {
                $order_data['shipped_date'] = $shipped_date;
                $shipped_orders[] = $order_data;
                error_log('DEBUG: Added order ' . $order_id . ' to shipped orders');
            } else {
                // "In Bearbeitung seit" berechnen
                $processing_date = $order->get_date_created();
                $order_data['processing_since'] = $processing_date->date_i18n('d.m.Y H:i');
                $processing_orders[] = $order_data;
                error_log('DEBUG: Added order ' . $order_id . ' to processing orders');
            }
        }
    }
    
    // Sortierung
    // Wartestellung: Neueste oben
    usort($pending_orders, function($a, $b) {
        return strtotime($b['date']) - strtotime($a['date']);
    });
    
    // In Bearbeitung: Am längsten in Bearbeitung oben
    usort($processing_orders, function($a, $b) {
        return strtotime($a['processing_since']) - strtotime($b['processing_since']);
    });
    
    // In Zustellung: Neueste oben
    usort($shipped_orders, function($a, $b) {
        return strtotime($b['shipped_date']) - strtotime($a['shipped_date']);
    });
    
    // Nach Benutzer gruppieren
    $pending_orders = group_orders_by_user($pending_orders);
    $processing_orders = group_orders_by_user($processing_orders);
    $shipped_orders = group_orders_by_user($shipped_orders);
    
    wp_send_json_success(array(
        'pending' => $pending_orders,
        'processing' => $processing_orders,
        'shipped' => $shipped_orders
    ));
}

function group_orders_by_user($orders) {
    $grouped = array();
    
    foreach ($orders as $order) {
        $user_key = $order['user_id'] ? 'user_' . $order['user_id'] : 'guest_' . md5($order['user_name']);
        
        if (!isset($grouped[$user_key])) {
            $grouped[$user_key] = array(
                'user_name' => $order['user_name'],
                'orders' => array()
            );
        }
        
        $grouped[$user_key]['orders'][] = $order;
    }
    
    // Flatt zurückgeben (für einfache Darstellung)
    $result = array();
    foreach ($grouped as $group) {
        $result = array_merge($result, $group['orders']);
    }
    
    return $result;
}

// AJAX Handler für Status-Update
add_action('wp_ajax_update_order_status', 'update_order_status_ajax');
add_action('wp_ajax_nopriv_update_order_status', 'update_order_status_ajax');

function update_order_status_ajax() {
    check_ajax_referer('orders_overview_nonce', 'nonce');
    
    if (!current_user_can('manage_options')) {
        wp_send_json_error('Unauthorized');
    }
    
    $order_id = intval($_POST['order_id']);
    $new_status = sanitize_text_field($_POST['status']);
    
    error_log('DEBUG: update_order_status_ajax called - order_id: ' . $order_id . ', new_status: ' . $new_status);
    
    $order = wc_get_order($order_id);
    if (!$order) {
        wp_send_json_error('Bestellung nicht gefunden');
    }
    
    // Status-Mapping
    $wc_status = '';
    switch ($new_status) {
        case 'cancelled':
            $wc_status = 'cancelled';
            break;
        case 'pending':
            $wc_status = 'pending';
            break;
        case 'processing':
            // Wenn von shipped zurück zu processing, Shipment-Status zurücksetzen
            if (function_exists('wc_gzd_get_shipments_by_order')) {
                $shipments = wc_gzd_get_shipments_by_order($order_id);
                foreach ($shipments as $shipment) {
                    if ($shipment->get_status() === 'shipped' || $shipment->get_status() === 'delivered') {
                        $shipment->update_status('dispatched');
                        $shipment->save();
                    }
                }
            }
            // Meta-Daten zurücksetzen
            delete_post_meta($order_id, '_is_shipped');
            delete_post_meta($order_id, '_shipped_date');
            $wc_status = 'processing';
            break;
        case 'shipped':
            // Prüfen, ob Germanized Shipment aktiv ist
            $has_shipments = false;
            
            // Mehrere Prüfungen für das Plugin
            if (class_exists('WooCommerce_Germanized_Shipments')) {
                $has_shipments = true;
            } elseif (function_exists('wc_gzd_get_shipment')) {
                $has_shipments = true;
            } elseif (defined('WC_GZD_SHIPMENT_VERSION')) {
                $has_shipments = true;
            }
            
            // Prüfen, ob das Plugin aktiv ist
            if (is_plugin_active('woocommerce-germanized/woocommerce-germanized.php') || is_plugin_active('woocommerce-germanized-pro/woocommerce-germanized-pro.php')) {
                $has_shipments = true;
            }
            
            if ($has_shipments && function_exists('wc_gzd_get_shipments_by_order')) {
                // Shipment erstellen oder aktualisieren
                $shipments = wc_gzd_get_shipments_by_order($order_id);
                error_log('DEBUG: Found ' . count($shipments) . ' shipments for order ' . $order_id);
                
                if (empty($shipments)) {
                    // Neues Shipment erstellen
                    try {
                        error_log('DEBUG: Creating new shipment for order ' . $order_id);
                        $shipment = wc_gzd_create_shipment($order);
                        if ($shipment) {
                            $shipment->update_status('shipped');
                            $shipment->save();
                            error_log('DEBUG: Shipment created and set to shipped, ID: ' . $shipment->get_id());
                        } else {
                            error_log('DEBUG: Failed to create shipment');
                            $has_shipments = false;
                        }
                    } catch (Exception $e) {
                        error_log('DEBUG: Exception creating shipment: ' . $e->getMessage());
                        $has_shipments = false;
                    }
                } else {
                    // Erstes Shipment auf shipped setzen
                    $shipment = reset($shipments);
                    error_log('DEBUG: Updating shipment ID ' . $shipment->get_id() . ' from status ' . $shipment->get_status() . ' to shipped');
                    $shipment->update_status('shipped');
                    $shipment->save();
                    error_log('DEBUG: Shipment saved, new status: ' . $shipment->get_status());
                }
                
                // WICHTIG: WooCommerce-Status auf processing zurücksetzen, da Germanized ihn automatisch ändert
                $wc_status = 'processing';
            }
            
            if (!$has_shipments) {
                // Fallback: Meta-Daten für lokalen Versandstatus setzen
                update_post_meta($order_id, '_is_shipped', '1');
                update_post_meta($order_id, '_shipped_date', current_time('mysql'));
                $wc_status = 'processing';
            }
            break;
        case 'completed':
            $wc_status = 'completed';
            break;
        default:
            wp_send_json_error('Ungültiger Status');
    }
    
    error_log('DEBUG: Final wc_status: ' . $wc_status);
    
    if ($wc_status) {
        $order->update_status($wc_status);
        error_log('DEBUG: Order status updated to: ' . $wc_status);
        
        // Prüfen, ob der Status wirklich gesetzt wurde
        $order_after = wc_get_order($order_id);
        $actual_status = $order_after->get_status();
        error_log('DEBUG: Actual order status after update: ' . $actual_status);
    }
    
    wp_send_json_success();
}
