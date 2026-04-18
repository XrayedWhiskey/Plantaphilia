<?php
/**
 * Template Name: Produktliste (Admin)
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
?>

<div style="display: flex; justify-content: center; width: 100%;">
    <div style="width: 100%; max-width: 1200px; padding: 0 20px;">
        
        <div style="text-align: center; margin-bottom: 30px;">
            <h1 style="margin-bottom: 10px;">Produktliste</h1>
            <p style="color: #888; margin-bottom: 30px;">Übersicht aller Produkte mit Beständen und Preisen</p>
        </div>
        
        <!-- Fuzzy Search und Toggle -->
        <div style="margin-bottom: 30px; max-width: 700px; margin-left: auto; margin-right: auto; display: flex; gap: 20px; align-items: center;">
            <button id="bulk-sale-btn" style="padding: 12px 16px; background: #333; color: white; border: none; border-radius: 3px; cursor: pointer; font-size: 14px;">Sale einschalten</button>
            <input type="text" id="product-search" placeholder="Produkte suchen..." 
                   style="flex: 1; padding: 12px 16px; font-size: 14px; border: 1px solid #ddd; border-radius: 3px;">
            <div style="display: flex; align-items: center; gap: 10px;">
                <label style="font-size: 14px; font-weight: 500;">Nach Angeboten gruppieren</label>
                <label class="switch">
                    <input type="checkbox" id="group-by-offer-toggle">
                    <span class="slider"></span>
                </label>
            </div>
        </div>
        
        <style>
        .switch {
            position: relative;
            display: inline-block;
            width: 50px;
            height: 24px;
        }
        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }
        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .4s;
            border-radius: 24px;
        }
        .slider:before {
            position: absolute;
            content: "";
            height: 18px;
            width: 18px;
            left: 3px;
            bottom: 3px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }
        input:checked + .slider {
            background-color: #2196F3;
        }
        input:checked + .slider:before {
            transform: translateX(26px);
        }
        .sell-checkbox {
            position: relative;
            display: inline-block;
            width: 40px;
            height: 20px;
        }
        .sell-checkbox input {
            opacity: 0;
            width: 0;
            height: 0;
        }
        .sell-checkbox .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .4s;
            border-radius: 20px;
        }
        .sell-checkbox .slider:before {
            position: absolute;
            content: "";
            height: 14px;
            width: 14px;
            left: 3px;
            bottom: 3px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }
        .sell-checkbox input:checked + .slider {
            background-color: #4CAF50;
        }
        .sell-checkbox input:checked + .slider:before {
            transform: translateX(20px);
        }
        </style>
        
        <!-- Produktliste -->
        <div id="product-list-container">
            <table id="product-table" style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: #f5f5f5;">
                        <th data-sort="sellable" style="padding: 12px; text-align: left; border-bottom: 2px solid #e0e0e0; cursor: pointer;">Verkaufen <span class="sort-arrow"></span></th>
                        <th style="padding: 12px; text-align: left; border-bottom: 2px solid #e0e0e0;">Bild</th>
                        <th data-sort="name" style="padding: 12px; text-align: left; border-bottom: 2px solid #e0e0e0; cursor: pointer;">Name <span class="sort-arrow"></span></th>
                        <th data-sort="stock" style="padding: 12px; text-align: right; border-bottom: 2px solid #e0e0e0; cursor: pointer;">Bestand <span class="sort-arrow"></span></th>
                        <th data-sort="in_progress" style="padding: 12px; text-align: right; border-bottom: 2px solid #e0e0e0; cursor: pointer;">In Bearbeitung <span class="sort-arrow"></span></th>
                        <th data-sort="available" style="padding: 12px; text-align: right; border-bottom: 2px solid #e0e0e0; cursor: pointer;">Verfügbar <span class="sort-arrow"></span></th>
                        <th data-sort="price" style="padding: 12px; text-align: right; border-bottom: 2px solid #e0e0e0; cursor: pointer;">Preis <span class="sort-arrow"></span></th>
                        <th style="padding: 12px; text-align: left; border-bottom: 2px solid #e0e0e0;">Angebot</th>
                        <th style="padding: 12px; text-align: center; border-bottom: 2px solid #e0e0e0;">Bearbeiten</th>
                    </tr>
                </thead>
                <tbody id="product-table-body">
                    <tr>
                        <td colspan="9" style="padding: 40px; text-align: center; color: #888;">Lade Produkte...</td>
                    </tr>
                </tbody>
            </table>
        </div>
        
    </div>
</div>

<!-- Edit Modal -->
<div id="edit-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 10000; align-items: center; justify-content: center;">
    <div style="background: white; padding: 30px; border-radius: 8px; max-width: 500px; width: 90%; max-height: 90vh; overflow-y: auto;">
        <h3 style="margin-top: 0; margin-bottom: 20px;">Produkt bearbeiten</h3>
        <p style="color: #666; margin-bottom: 20px;" id="modal-product-name"></p>
        
        <div style="margin-bottom: 15px;">
            <label style="display: block; margin-bottom: 5px; font-weight: 500;">Stückzahl (Bestand)</label>
            <input type="number" id="modal-stock" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;" min="0">
        </div>
        
        <div style="margin-bottom: 15px;">
            <label style="display: block; margin-bottom: 5px; font-weight: 500;">Preis (€)</label>
            <input type="number" id="modal-price" step="0.01" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;" min="0">
        </div>
        
        <div style="margin-bottom: 15px;">
            <label style="display: block; margin-bottom: 5px; font-weight: 500;">
                <input type="checkbox" id="modal-has-offer"> Angebot aktiv
            </label>
        </div>
        
        <div id="offer-fields" style="display: none;">
            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px; font-weight: 500;">Preistyp</label>
                <div style="display: flex; align-items: center; gap: 10px;">
                    <span style="font-size: 14px;">€</span>
                    <label style="position: relative; display: inline-block; width: 50px; height: 26px;">
                        <input type="checkbox" id="modal-price-type-toggle" style="opacity: 0; width: 0; height: 0;">
                        <span style="position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #ccc; transition: .4s; border-radius: 34px;"></span>
                        <span style="position: absolute; content: ''; height: 20px; width: 20px; left: 3px; bottom: 3px; background-color: white; transition: .4s; border-radius: 50%;"></span>
                    </label>
                    <span style="font-size: 14px;">%</span>
                </div>
            </div>
            
            <div id="euro-price-field" style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px; font-weight: 500;">Angebotspreis (€)</label>
                <input type="number" id="modal-sale-price" step="0.01" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;" min="0">
            </div>
            
            <div id="percent-price-field" style="margin-bottom: 15px; display: none;">
                <label style="display: block; margin-bottom: 5px; font-weight: 500;">Rabatt (%)</label>
                <input type="number" id="modal-sale-percent" step="1" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;" min="0" max="100" placeholder="z.B. 20">
            </div>
            
            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px; font-weight: 500;">
                    <input type="checkbox" id="modal-show-old-price"> Alten Preis anzeigen (vorher X jetzt Y)
                </label>
            </div>
            
            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px; font-weight: 500;">Angebotsstart</label>
                <button type="button" id="modal-offer-start-btn" style="padding: 8px 12px; background: #f5f5f5; border: 1px solid #ddd; border-radius: 4px; cursor: pointer;">Startdatum setzen</button>
                <div id="modal-offer-start-display" style="margin-top: 5px; font-size: 13px; color: #666;"></div>
            </div>
            
            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px; font-weight: 500;">
                    <input type="checkbox" id="modal-time-limited"> Zeitlich begrenzt
                </label>
            </div>
            
            <div id="time-limit-fields" style="display: none; padding-left: 20px; margin-bottom: 15px;">
                <div style="margin-bottom: 15px;">
                    <label style="display: block; margin-bottom: 5px; font-weight: 500;">Begrenzungstyp</label>
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <span style="font-size: 14px;">Bis Datum</span>
                        <label style="position: relative; display: inline-block; width: 50px; height: 26px;">
                            <input type="checkbox" id="modal-time-limit-toggle" style="opacity: 0; width: 0; height: 0;">
                            <span style="position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #ccc; transition: .4s; border-radius: 34px;"></span>
                            <span style="position: absolute; content: ''; height: 20px; width: 20px; left: 3px; bottom: 3px; background-color: white; transition: .4s; border-radius: 50%;"></span>
                        </label>
                        <span style="font-size: 14px;">Für Zeit</span>
                    </div>
                </div>
                
                <div id="date-field" style="margin-bottom: 15px;">
                    <label style="display: block; margin-bottom: 5px; font-weight: 500;">Datum (TT.MM.JJJJ)</label>
                    <input type="text" id="modal-time-limit-date" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;" placeholder="z.B. 10.05.2026">
                    
                    <label style="display: block; margin-bottom: 5px; margin-top: 10px; font-weight: 500;">Uhrzeit (HH:MM)</label>
                    <input type="time" id="modal-time-limit-time" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                </div>
                
                <div id="duration-field" style="display: none; margin-bottom: 15px;">
                    <label style="display: block; margin-bottom: 5px; font-weight: 500;">Dauer</label>
                    <div style="display: flex; gap: 10px;">
                        <div style="flex: 1;">
                            <label style="display: block; margin-bottom: 3px; font-size: 12px; color: #666;">Tage</label>
                            <input type="number" id="modal-time-limit-days" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;" min="0" placeholder="0">
                        </div>
                        <div style="flex: 1;">
                            <label style="display: block; margin-bottom: 3px; font-size: 12px; color: #666;">Stunden</label>
                            <input type="number" id="modal-time-limit-hours" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;" min="0" placeholder="0">
                        </div>
                        <div style="flex: 1;">
                            <label style="display: block; margin-bottom: 3px; font-size: 12px; color: #666;">Minuten</label>
                            <input type="number" id="modal-time-limit-minutes" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;" min="0" placeholder="0">
                        </div>
                    </div>
                </div>
                
                <div style="margin-bottom: 10px;">
                    <label style="display: block; margin-bottom: 5px; font-weight: 500;">
                        <input type="checkbox" id="modal-show-end-date"> Dem Kunden anzeigen bis wann reduziert ist
                    </label>
                </div>
            </div>
        </div>
        
        <div style="display: flex; gap: 10px; margin-top: 20px;">
            <button id="save-edit" style="flex: 1; padding: 12px; background: #333; color: white; border: none; border-radius: 4px; cursor: pointer;">Speichern</button>
            <button id="cancel-edit" style="flex: 1; padding: 12px; background: #ddd; color: #333; border: none; border-radius: 4px; cursor: pointer;">Abbrechen</button>
        </div>
    </div>
</div>

<div id="offer-start-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 10000; align-items: center; justify-content: center;">
    <div style="background: white; padding: 30px; border-radius: 8px; max-width: 400px; width: 90%;">
        <h3 style="margin-top: 0;">Startdatum setzen</h3>
        <div style="margin-bottom: 15px;">
            <label style="display: block; margin-bottom: 5px; font-weight: 500;">Datum (TT.MM.JJJJ)</label>
            <input type="text" id="offer-start-date" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;" placeholder="z.B. 10.05.2026">
        </div>
        <div style="margin-bottom: 15px;">
            <label style="display: block; margin-bottom: 5px; font-weight: 500;">Uhrzeit (HH:MM)</label>
            <input type="time" id="offer-start-time" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
        </div>
        <div style="display: flex; gap: 10px;">
            <button id="save-offer-start" style="flex: 1; padding: 12px; background: #333; color: white; border: none; border-radius: 4px; cursor: pointer;">Speichern</button>
            <button id="cancel-offer-start" style="flex: 1; padding: 12px; background: #ddd; color: #333; border: none; border-radius: 4px; cursor: pointer;">Abbrechen</button>
        </div>
    </div>
</div>

<div id="bulk-sale-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 10000; align-items: center; justify-content: center;">
    <div style="background: white; padding: 30px; border-radius: 8px; max-width: 600px; width: 90%; max-height: 90vh; overflow-y: auto;">
        <h3 style="margin-top: 0; margin-bottom: 20px;">Bulk Rabatt Aktion</h3>
        
        <div style="margin-bottom: 15px;">
            <label style="display: block; margin-bottom: 5px; font-weight: 500;">Aktionsname</label>
            <input type="text" id="bulk-sale-title" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;" placeholder="z.B. Sommer Sale">
        </div>
        
        <div id="bulk-sale-date-section" style="margin-bottom: 20px;">
            <div style="display: flex; gap: 15px; margin-bottom: 15px;">
                <div style="flex: 1;">
                    <label style="display: block; margin-bottom: 5px; font-weight: 500;">Startdatum</label>
                    <button type="button" id="bulk-sale-start-btn" style="width: 100%; padding: 8px 12px; background: #f5f5f5; border: 1px solid #ddd; border-radius: 4px; cursor: pointer;">Startdatum setzen</button>
                    <div id="bulk-sale-start-display" style="margin-top: 5px; font-size: 13px; color: #666;"></div>
                </div>
                <div style="flex: 1;">
                    <label style="display: block; margin-bottom: 5px; font-weight: 500;">Enddatum</label>
                    <button type="button" id="bulk-sale-end-btn" style="width: 100%; padding: 8px 12px; background: #f5f5f5; border: 1px solid #ddd; border-radius: 4px; cursor: pointer;">Enddatum setzen</button>
                    <div id="bulk-sale-end-display" style="margin-top: 5px; font-size: 13px; color: #666;"></div>
                </div>
            </div>
            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px; font-weight: 500;">
                    <input type="checkbox" id="bulk-sale-show-end-date"> Dem Kunden anzeigen bis wann reduziert ist
                </label>
            </div>
            <div style="display: flex; gap: 10px;">
                <button type="button" id="bulk-sale-dates-ok" style="flex: 1; padding: 12px; background: #333; color: white; border: none; border-radius: 4px; cursor: pointer;">OK</button>
                <button type="button" id="bulk-sale-dates-cancel" style="flex: 1; padding: 12px; background: #ddd; color: #333; border: none; border-radius: 4px; cursor: pointer;">Abbrechen</button>
            </div>
        </div>
        
        <div id="bulk-sale-groups-section" style="display: none;">
            <div id="bulk-sale-groups" style="margin-bottom: 20px;"></div>
            <button type="button" id="add-bulk-sale-group" style="padding: 8px 12px; background: #f5f5f5; border: 1px solid #ddd; border-radius: 4px; cursor: pointer; margin-bottom: 15px;">+ Gruppe hinzufügen</button>
            
            <div style="display: flex; gap: 10px;">
                <button id="save-bulk-sale" style="flex: 1; padding: 12px; background: #333; color: white; border: none; border-radius: 4px; cursor: pointer;">Speichern</button>
                <button id="cancel-bulk-sale" style="flex: 1; padding: 12px; background: #ddd; color: #333; border: none; border-radius: 4px; cursor: pointer;">Abbrechen</button>
            </div>
        </div>
    </div>
</div>

<div id="bulk-sale-date-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 10001; align-items: center; justify-content: center;">
    <div style="background: white; padding: 30px; border-radius: 8px; max-width: 400px; width: 90%;">
        <h3 style="margin-top: 0;">Datum setzen</h3>
        <div style="margin-bottom: 15px;">
            <label style="display: block; margin-bottom: 5px; font-weight: 500;">Datum (TT.MM.JJJJ)</label>
            <input type="text" id="bulk-sale-date-input" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;" placeholder="z.B. 10.05.2026" maxlength="10">
        </div>
        <div style="margin-bottom: 15px;">
            <label style="display: block; margin-bottom: 5px; font-weight: 500;">Uhrzeit (HH:MM)</label>
            <input type="time" id="bulk-sale-time-input" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
        </div>
        <div style="display: flex; gap: 10px;">
            <button id="save-bulk-sale-date" style="flex: 1; padding: 12px; background: #333; color: white; border: none; border-radius: 4px; cursor: pointer;">Speichern</button>
            <button id="cancel-bulk-sale-date" style="flex: 1; padding: 12px; background: #ddd; color: #333; border: none; border-radius: 4px; cursor: pointer;">Abbrechen</button>
        </div>
    </div>
</div>

<div id="add-product-to-group-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 10001; align-items: center; justify-content: center;">
    <div style="background: white; padding: 30px; border-radius: 8px; max-width: 800px; width: 90%; max-height: 90vh; overflow-y: auto;">
        <h3 style="margin-top: 0; margin-bottom: 20px;">Artikel zu Gruppe hinzufügen</h3>
        <input type="text" id="add-product-search" placeholder="Produkte suchen..." style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; margin-bottom: 15px;">
        <div id="add-product-list" style="max-height: 400px; overflow-y: auto;"></div>
        <button id="close-add-product-modal" style="margin-top: 15px; padding: 10px 20px; background: #ddd; color: #333; border: none; border-radius: 4px; cursor: pointer;">Schließen</button>
    </div>
</div>

<div id="bulk-sale-details-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 10002; align-items: center; justify-content: center;">
    <div style="background: white; padding: 30px; border-radius: 8px; max-width: 900px; width: 90%; max-height: 90vh; overflow-y: auto;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h3 id="bulk-sale-details-title" style="margin-top: 0;">Bulk Aktion Details</h3>
            <button id="delete-bulk-sale-btn" style="padding: 8px 12px; background: #e74c3c; color: white; border: none; border-radius: 4px; cursor: pointer;">🗑️ Löschen</button>
        </div>
        <div id="bulk-sale-details-content"></div>
        <button id="close-bulk-sale-details" style="margin-top: 20px; padding: 10px 20px; background: #ddd; color: #333; border: none; border-radius: 4px; cursor: pointer;">Schließen</button>
    </div>
</div>

<style>
#product-search:focus {
    outline: none;
    border-color: #333;
}

#product-table {
    font-size: 14px;
}

#product-table tbody tr {
    border-bottom: 1px solid #eee;
}

#product-table tbody tr:hover {
    background-color: #fafafa;
}

#product-table td {
    padding: 12px;
    vertical-align: middle;
}

.product-img {
    width: 50px;
    height: 50px;
    object-fit: cover;
    border-radius: 3px;
}

.product-name-link {
    color: #333;
    text-decoration: none;
    font-weight: 500;
}

.product-name-link:hover {
    color: #666;
}

.stock-positive {
    color: #2e7d32;
    font-weight: 600;
}

.stock-negative {
    color: #c62828;
    font-weight: 600;
}

.stock-zero {
    color: #f57c00;
    font-weight: 600;
}

.price-cell {
    font-weight: 500;
    color: #333;
}

.offer-badge {
    background: #d32f2f;
    color: white;
    padding: 3px 8px;
    border-radius: 3px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    display: inline-block;
    margin-bottom: 4px;
}

.offer-info {
    font-size: 12px;
    color: #666;
    line-height: 1.3;
}

.offer-info .regular-price {
    text-decoration: line-through;
    color: #999;
    font-size: 11px;
}

.offer-info .sale-price {
    color: #d32f2f;
    font-weight: 600;
}

.offer-info .offer-end {
    color: #999;
    font-size: 11px;
}

.edit-btn {
    font-size: 18px;
    background: none;
    border: none;
    cursor: pointer;
    opacity: 0.6;
    transition: opacity 0.2s;
    padding: 5px;
}

.edit-btn:hover {
    opacity: 1;
}

.sell-checkbox {
    width: 18px;
    height: 18px;
    cursor: pointer;
}

#edit-modal button:hover {
    opacity: 0.9;
}

/* Toggle-Schalter Styling */
#modal-price-type-toggle:checked + span,
#modal-time-limit-toggle:checked + span {
    background-color: #333;
}

#modal-price-type-toggle:checked + span + span,
#modal-time-limit-toggle:checked + span + span {
    transform: translateX(24px);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Produkte laden
    loadProducts();
    
    // Search Event
    document.getElementById('product-search').addEventListener('input', function(e) {
        filterProducts(e.target.value);
    });
});

function loadProducts() {
    console.log('DEBUG: loadProducts aufgerufen');
    
    // Angebote mit Startzeitpunkt aktivieren
    fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'action=activate_offers_with_start_date&nonce=<?php echo wp_create_nonce('product_list_nonce'); ?>'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.data.activated_count > 0) {
            console.log('DEBUG: ' + data.data.activated_count + ' Angebote aktiviert');
        }
    })
    .catch(error => {
        console.error('DEBUG: Fehler beim Aktivieren von Angeboten:', error);
    });
    
    fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'action=get_product_list_data&nonce=<?php echo wp_create_nonce('product_list_nonce'); ?>'
    })
    .then(response => {
        console.log('DEBUG: Response status:', response.status);
        return response.json();
    })
    .then(data => {
        console.log('DEBUG: Response data:', data);
        if (data.success) {
            renderProducts(data.data);
        } else {
            console.error('DEBUG: Error:', data.data);
        }
    })
    .catch(error => {
        console.error('DEBUG: Fetch error:', error);
    });
}

function renderProducts(products) {
    const tbody = document.getElementById('product-table-body');
    tbody.innerHTML = '';
    
    if (products.length === 0) {
        tbody.innerHTML = '<tr><td colspan="9" style="padding: 40px; text-align: center; color: #666;"><div style="font-size: 48px; margin-bottom: 16px;">🔍</div>Keine Produkte gefunden.</td></tr>';
        return;
    }
    
    // Prüfen, ob Gruppierung aktiviert ist
    const groupByOffer = document.getElementById('group-by-offer-toggle').checked;
    
    if (groupByOffer) {
        // Produkte in Gruppen einteilen
        const activeOffers = products.filter(p => p.has_offer && !p.is_offer_expired && !p.part_of_sale);
        const expiredOffers = products.filter(p => p.is_offer_expired && p.offer_expired_read !== '1' && !p.part_of_sale);
        const readExpiredOffers = products.filter(p => p.is_offer_expired && p.offer_expired_read === '1' && !p.part_of_sale);
        const noOffers = products.filter(p => !p.has_offer && !p.is_offer_expired && !p.part_of_sale);
        const bulkSaleProducts = products.filter(p => p.part_of_sale);
        
        // Gruppen rendern
        if (activeOffers.length > 0) {
            renderGroup(tbody, 'Laufende Angebote', activeOffers);
        }
        if (bulkSaleProducts.length > 0) {
            renderBulkSalesGroup(tbody, bulkSaleProducts);
        }
        if (readExpiredOffers.length > 0) {
            renderGroup(tbody, 'Laufende Angebote (gelesen)', readExpiredOffers, false, true);
        }
        if (expiredOffers.length > 0) {
            renderGroup(tbody, 'Abgelaufene Angebote', expiredOffers, true);
        }
        if (noOffers.length > 0) {
            renderGroup(tbody, 'Produkte ohne Angebote', noOffers);
        }
    } else {
        // Normale Liste
        products.forEach(product => {
            const tr = document.createElement('tr');
            
            // Verfügbar berechnen
            const available = product.stock - product.in_progress;
            
            // Stock-Klasse
            let stockClass = 'stock-zero';
            if (available > 0) {
                stockClass = 'stock-positive';
        } else if (available < 0) {
            stockClass = 'stock-negative';
        }
        
        // Angebot HTML
        let offerHtml = '-';
        if (product.is_offer_expired) {
            // Abgelaufenes Angebot
            const expiredSince = product.offer_expired_since ? new Date(product.offer_expired_since * 1000).toLocaleString('de-DE') : 'Unbekannt';
            const isRead = product.offer_expired_read === '1';
            const reductionAmount = product.offer_reduction_amount || '';
            offerHtml = `
                <div style="padding: 8px; background: #fff3cd; border-left: 3px solid #ffc107; border-radius: 3px;">
                    <div style="font-weight: bold; color: #856404; margin-bottom: 4px;">Angebot abgelaufen ${reductionAmount}</div>
                    <div style="font-size: 12px; color: #856404; margin-bottom: 4px;">Abgelaufen seit: ${expiredSince}</div>
                    <label style="display: flex; align-items: center; gap: 5px; font-size: 12px; cursor: pointer;">
                        <input type="checkbox" class="expired-read-checkbox" data-product-id="${product.id}" ${isRead ? 'checked' : ''}>
                        Als gelesen markieren
                    </label>
                </div>
            `;
        } else if (product.has_offer) {
            let timeInfo = '';
            if (product.time_limited) {
                if (product.time_limit_type === 'date' && product.time_limit_date_only) {
                    let dateText = product.time_limit_date_only;
                    if (product.time_limit_time) {
                        dateText += ' ' + product.time_limit_time;
                    }
                    timeInfo = '<span class="offer-end">Bis: ' + dateText + '</span>';
                } else {
                    // Verbleibende Zeit berechnen und anzeigen
                    const now = Math.floor(Date.now() / 1000);
                    // offer_end ist jetzt ein Unix-Timestamp in Sekunden (UTC)
                    const dateTo = product.offer_end ? parseInt(product.offer_end) : 0;
                    const diff = dateTo - now;
                    
                    if (diff > 0) {
                        const days = Math.floor(diff / (24 * 60 * 60));
                        
                        if (days >= 1) {
                            // Mehr als 1 Tag: Datum anzeigen (mit Zeitzone-Korrektur)
                            const endDate = new Date(dateTo * 1000);
                            const localOffset = endDate.getTimezoneOffset() * 60 * 1000; // Offset in ms
                            const localDate = new Date(dateTo * 1000 + localOffset);
                            
                            const day = String(localDate.getDate()).padStart(2, '0');
                            const month = String(localDate.getMonth() + 1).padStart(2, '0');
                            const year = localDate.getFullYear();
                            const hours = String(localDate.getHours()).padStart(2, '0');
                            const minutes = String(localDate.getMinutes()).padStart(2, '0');
                            timeInfo = '<span class="offer-end" data-countdown="' + dateTo + '">Bis: ' + day + '.' + month + '.' + year + ' ' + hours + ':' + minutes + '</span>';
                        } else {
                            // Weniger als 1 Tag: Countdown in HH:MM:SS
                            const hours = Math.floor(diff / (60 * 60));
                            const minutes = Math.floor((diff % (60 * 60)) / 60);
                            const seconds = diff % 60;
                            
                            const h = String(hours).padStart(2, '0');
                            const m = String(minutes).padStart(2, '0');
                            const s = String(seconds).padStart(2, '0');
                            
                            timeInfo = '<span class="offer-end countdown" data-countdown="' + dateTo + '" data-product-id="' + product.id + '">' + h + ':' + m + ':' + s + '</span>';
                        }
                    } else {
                        // Angebot abgelaufen
                        timeInfo = '<span class="offer-end" style="color: #e74c3c;">Abgelaufen</span>';
                    }
                }
            }
            
            offerHtml = `
                <div>
                    <span class="offer-badge">Angebot</span>
                    <div class="offer-info">
                        ${product.show_old_price ? '<span class="regular-price">' + product.regular_price + ' €</span>' : ''}
                        <span class="sale-price">${product.sale_price} €</span>
                        <div>
                            ${product.offer_start_date ? '<span class="offer-begin">ab: ' + product.offer_start_date + '</span><br>' : ''}
                            ${timeInfo}
                        </div>
                    </div>
                </div>
            `;
        }
        
        tr.innerHTML = `
            <td style="text-align: center;">
                <input type="checkbox" class="sell-checkbox" data-product-id="${product.id}" ${product.is_sellable ? 'checked' : ''}>
            </td>
            <td>
                ${product.image ? `<img src="${product.image}" alt="${product.name}" class="product-img">` : '<div style="width:50px;height:50px;background:#f5f5f5;border-radius:3px;display:flex;align-items:center;justify-content:center;color:#999;">📷</div>'}
            </td>
            <td>
                <a href="${product.permalink}" target="_blank" class="product-name-link">${product.name}</a>
                ${product.was_out_of_stock ? '<span style="margin-left: 8px; background: #d4edda; color: #155724; padding: 2px 6px; border-radius: 3px; font-size: 11px; font-weight: 600;">Restocked</span>' : ''}
                ${product.was_low_stock ? '<span style="margin-left: 8px; background: #fff3cd; color: #856404; padding: 2px 6px; border-radius: 3px; font-size: 11px; font-weight: 600;">Low Stock Restocked</span>' : ''}
                ${product.was_restocked && !product.was_out_of_stock && !product.was_low_stock ? '<span style="margin-left: 8px; background: #e2e3e5; color: #383d41; padding: 2px 6px; border-radius: 3px; font-size: 11px; font-weight: 600;">Restocked</span>' : ''}
                ${product.is_new ? '<span style="margin-left: 8px; background: #cce5ff; color: #004085; padding: 2px 6px; border-radius: 3px; font-size: 11px; font-weight: 600;">Neu</span>' : ''}
                ${product.part_of_sale ? '<span style="margin-left: 8px; background: #d4edda; color: #155724; padding: 2px 6px; border-radius: 3px; font-size: 11px; font-weight: 600;">' + (product.sale_title || 'Aktion') + ' - ' + (product.group_name || 'Gruppe') + '</span>' : ''}
            </td>
            <td style="text-align: right;">${product.stock}</td>
            <td style="text-align: right;">${product.in_progress}</td>
            <td style="text-align: right;" class="${stockClass}">${available}</td>
            <td style="text-align: right;" class="price-cell">${product.price} €</td>
            <td>${offerHtml}</td>
            <td style="text-align: center;">
                <button class="edit-btn" data-product-id="${product.id}" data-product-name="${product.name}" data-product-sku="${product.sku || ''}" data-product-new="${product.is_new ? 'true' : 'false'}" data-stock="${product.stock}" data-price="${product.price}" data-regular-price="${product.regular_price}" data-sale-price="${product.sale_price}" data-has-offer="${product.has_offer}" data-show-old-price="${product.show_old_price}" data-time-limited="${product.time_limited}" data-time-limit-type="${product.time_limit_type}" data-time-limit-duration="${product.time_limit_duration}" data-time-limit-date="${product.time_limit_date}" data-show-end-date="${product.show_end_date}" data-time-limit-days="${product.time_limit_days}" data-time-limit-hours="${product.time_limit_hours}" data-time-limit-minutes="${product.time_limit_minutes}" data-time-limit-time="${product.time_limit_time}" data-time-limit-date-only="${product.time_limit_date_only}" data-offer-start-date="${product.offer_start_date || ''}" title="Bearbeiten">✏️</button>
            </td>
        `;
        
        tbody.appendChild(tr);
    });
    }
    
    // Checkbox-Event-Listener hinzufügen (nur für normale Liste)
    if (!groupByOffer) {
        document.querySelectorAll('.sell-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const productId = this.dataset.productId;
                const isSellable = this.checked;
                updateSellStatus(productId, isSellable);
            });
        });
        
        // Event-Listener für expired-read-checkbox hinzufügen
        document.querySelectorAll('.expired-read-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const productId = this.dataset.productId;
                const isRead = this.checked;
                markOfferAsRead(productId, isRead);
            });
        });
    }
    
    // Edit-Button Event-Listener neu setzen (nur wenn noch nicht vorhanden)
    document.querySelectorAll('.edit-btn:not(.event-attached)').forEach(btn => {
        btn.classList.add('event-attached');
        btn.addEventListener('click', function() {
            openEditModal(this);
        });
    });
}

function renderGroup(tbody, groupName, products, isExpired = false, isReadExpired = false) {
    // Gruppenüberschrift
    const groupHeader = document.createElement('tr');
    groupHeader.innerHTML = `
        <td colspan="9" style="padding: 20px 12px; background: #f9f9f9; font-weight: bold; font-size: 16px; color: #333; border-bottom: 2px solid #e0e0e0;">
            ${groupName}
        </td>
    `;
    tbody.appendChild(groupHeader);
    
    // Produkte rendern
    products.forEach(product => {
        const tr = document.createElement('tr');
        
        // Verfügbar berechnen
        const available = product.stock - product.in_progress;
        
        // Stock-Klasse
        let stockClass = 'stock-zero';
        if (available > 0) {
            stockClass = 'stock-positive';
        } else if (available < 0) {
            stockClass = 'stock-negative';
        }
        
        // Angebot HTML
        let offerHtml = '-';
        if (product.is_offer_expired) {
            // Abgelaufenes Angebot
            const expiredSince = product.offer_expired_since ? new Date(product.offer_expired_since * 1000).toLocaleString('de-DE') : 'Unbekannt';
            const isRead = product.offer_expired_read === '1';
            const reductionAmount = product.offer_reduction_amount || '';
            offerHtml = `
                <div style="padding: 8px; background: #fff3cd; border-left: 3px solid #ffc107; border-radius: 3px;">
                    <div style="font-weight: bold; color: #856404; margin-bottom: 4px;">Angebot abgelaufen ${reductionAmount}</div>
                    <div style="font-size: 12px; color: #856404; margin-bottom: 4px;">Abgelaufen seit: ${expiredSince}</div>
                    <label style="display: flex; align-items: center; gap: 5px; font-size: 12px; cursor: pointer;">
                        <input type="checkbox" class="expired-read-checkbox" data-product-id="${product.id}" ${isRead ? 'checked' : ''}>
                        Als gelesen markieren
                    </label>
                </div>
            `;
        } else if (product.has_offer) {
            // Laufendes Angebot
            let timeInfo = '';
            if (product.time_limited) {
                if (product.time_limit_type === 'date' && product.time_limit_date_only) {
                    let dateText = product.time_limit_date_only;
                    if (product.time_limit_time) {
                        dateText += ' ' + product.time_limit_time;
                    }
                    timeInfo = '<span class="offer-end">Bis: ' + dateText + '</span>';
                } else {
                    // Verbleibende Zeit berechnen und anzeigen
                    const now = Math.floor(Date.now() / 1000);
                    // offer_end ist jetzt ein Unix-Timestamp in Sekunden (UTC)
                    const diff = product.offer_end - now;
                    if (diff > 0) {
                        const days = Math.floor(diff / (24 * 60 * 60));
                        const hours = Math.floor((diff % (24 * 60 * 60)) / (60 * 60));
                        const minutes = Math.floor((diff % (60 * 60)) / 60);
                        
                        if (days > 0) {
                            timeInfo = '<span class="offer-end countdown" data-countdown="' + product.offer_end + '" data-product-id="' + product.id + '">' + days + 'd ' + hours + 'h ' + minutes + 'm</span>';
                        } else {
                            const h = String(hours).padStart(2, '0');
                            const m = String(minutes).padStart(2, '0');
                            const s = String(diff % 60).padStart(2, '0');
                            timeInfo = '<span class="offer-end countdown" data-countdown="' + product.offer_end + '" data-product-id="' + product.id + '">' + h + ':' + m + ':' + s + '</span>';
                        }
                    } else {
                        timeInfo = '<span class="offer-end">Abgelaufen</span>';
                    }
                }
            } else {
                timeInfo = '<span class="offer-end">Unbegrenzt</span>';
            }
            
            offerHtml = `
                <div>
                    <span class="offer-badge">Angebot</span>
                    <div class="offer-info">
                        ${product.show_old_price ? '<span class="regular-price">' + product.regular_price + ' €</span>' : ''}
                        <span class="sale-price">${product.sale_price} €</span>
                        ${timeInfo}
                    </div>
                </div>
            `;
        }
        
        tr.innerHTML = `
            <td style="text-align: center;">
                <input type="checkbox" class="sell-checkbox" data-product-id="${product.id}" ${product.is_sellable ? 'checked' : ''}>
            </td>
            <td>
                ${product.image ? `<img src="${product.image}" alt="${product.name}" class="product-img">` : '<div style="width:50px;height:50px;background:#f5f5f5;border-radius:3px;display:flex;align-items:center;justify-content:center;color:#999;">📷</div>'}
            </td>
            <td>
                <a href="${product.permalink}" target="_blank" class="product-name-link">${product.name}</a>
                ${product.was_out_of_stock ? '<span style="margin-left: 8px; background: #d4edda; color: #155724; padding: 2px 6px; border-radius: 3px; font-size: 11px; font-weight: 600;">Restocked</span>' : ''}
                ${product.was_low_stock ? '<span style="margin-left: 8px; background: #fff3cd; color: #856404; padding: 2px 6px; border-radius: 3px; font-size: 11px; font-weight: 600;">Low Stock Restocked</span>' : ''}
                ${product.was_restocked && !product.was_out_of_stock && !product.was_low_stock ? '<span style="margin-left: 8px; background: #e2e3e5; color: #383d41; padding: 2px 6px; border-radius: 3px; font-size: 11px; font-weight: 600;">Restocked</span>' : ''}
                ${product.is_new ? '<span style="margin-left: 8px; background: #cce5ff; color: #004085; padding: 2px 6px; border-radius: 3px; font-size: 11px; font-weight: 600;">Neu</span>' : ''}
                ${product.part_of_sale ? '<span style="margin-left: 8px; background: #d4edda; color: #155724; padding: 2px 6px; border-radius: 3px; font-size: 11px; font-weight: 600;">' + (product.sale_title || 'Aktion') + ' - ' + (product.group_name || 'Gruppe') + '</span>' : ''}
            </td>
            <td style="text-align: right;">${product.stock}</td>
            <td style="text-align: right;">${product.in_progress}</td>
            <td style="text-align: right;" class="${stockClass}">${available}</td>
            <td style="text-align: right;" class="price-cell">${product.price} €</td>
            <td>${offerHtml}</td>
            <td style="text-align: center;">
                <button class="edit-btn" data-product-id="${product.id}" data-product-name="${product.name}" data-product-sku="${product.sku || ''}" data-product-new="${product.is_new ? 'true' : 'false'}" data-stock="${product.stock}" data-price="${product.price}" data-regular-price="${product.regular_price}" data-sale-price="${product.sale_price}" data-has-offer="${product.has_offer}" data-show-old-price="${product.show_old_price}" data-time-limited="${product.time_limited}" data-time-limit-type="${product.time_limit_type}" data-time-limit-duration="${product.time_limit_duration}" data-time-limit-date="${product.time_limit_date}" data-show-end-date="${product.show_end_date}" data-time-limit-days="${product.time_limit_days}" data-time-limit-hours="${product.time_limit_hours}" data-time-limit-minutes="${product.time_limit_minutes}" data-time-limit-time="${product.time_limit_time}" data-time-limit-date-only="${product.time_limit_date_only}" data-offer-start-date="${product.offer_start_date || ''}" title="Bearbeiten">✏️</button>
            </td>
        `;
        
        tbody.appendChild(tr);
    });
    
    // Checkbox-Event-Listener für "Als gelesen markieren"
    if (isExpired || isReadExpired) {
        document.querySelectorAll('.expired-read-checkbox:not(.event-attached)').forEach(checkbox => {
            checkbox.classList.add('event-attached');
            checkbox.addEventListener('change', function() {
                const productId = this.dataset.productId;
                const isRead = this.checked;
                markOfferAsRead(productId, isRead);
            });
        });
    }
    
    // Edit-Button Event-Listener neu setzen (nur wenn noch nicht vorhanden)
    document.querySelectorAll('.edit-btn:not(.event-attached)').forEach(btn => {
        btn.classList.add('event-attached');
        btn.addEventListener('click', function() {
            openEditModal(this);
        });
    });
}

function parseGermanDate(dateString) {
    if (!dateString) return new Date();
    const parts = dateString.split(' ');
    if (parts.length === 2) {
        const dateParts = parts[0].split('.');
        const timeParts = parts[1].split(':');
        if (dateParts.length === 3 && timeParts.length === 2) {
            return new Date(dateParts[2], dateParts[1] - 1, dateParts[0], timeParts[0], timeParts[1]);
        }
    }
    return new Date();
}

function renderBulkSalesGroup(tbody, products) {
    try {
        // Produkte nach Sale ID gruppieren
        const sales = {};
        products.forEach(product => {
            const saleId = product.sale_id || 'unknown';
            if (!sales[saleId]) {
                sales[saleId] = {
                    title: product.sale_title || 'Aktion',
                    products: []
                };
            }
            sales[saleId].products.push(product);
        });
        
        // Für jeden Sale eine Gruppe erstellen
        Object.keys(sales).forEach(saleId => {
            const sale = sales[saleId];
            
            // Status des Angebots berechnen
            const now = Math.floor(Date.now() / 1000);
            let status = '';
            let statusColor = '#d4edda'; // Grün für aktiv
            let textColor = '#155724';
            let borderColor = '#c3e6cb';
            
            if (sale.products.length > 0) {
                const firstProduct = sale.products[0];
                const startDate = firstProduct.offer_start_date ? parseGermanDate(firstProduct.offer_start_date).getTime() / 1000 : 0;
                const endDate = firstProduct.offer_end_date ? parseGermanDate(firstProduct.offer_end_date).getTime() / 1000 : 0;
                
                if (endDate > 0 && endDate < now) {
                    status = ' (Abgelaufen seit: ' + firstProduct.offer_end_date + ')';
                    statusColor = '#f8d7da'; // Rot für abgelaufen
                    textColor = '#721c24';
                    borderColor = '#f5c6cb';
                } else if (startDate > 0 && startDate > now) {
                    status = ' (Startet in: ' + firstProduct.offer_start_date + ')';
                    statusColor = '#fff3cd'; // Gelb für startet in
                    textColor = '#856404';
                    borderColor = '#ffeeba';
                } else if (endDate > 0) {
                    status = ' (Aktiv - Bis: ' + firstProduct.offer_end_date + ')';
                } else {
                    status = ' (Aktiv)';
                }
            }
            
            // Sale-Überschrift
            const saleHeader = document.createElement('tr');
            saleHeader.innerHTML = `
                <td colspan="9" style="padding: 20px 12px; background: ${statusColor}; font-weight: bold; font-size: 16px; color: ${textColor}; border-bottom: 2px solid ${borderColor}; cursor: pointer;" onclick="openBulkSaleDetails('${saleId}', '${sale.title}')" data-sale-id="${saleId}">
                    ${sale.title} (Bulk Aktion)${status}
                </td>
            `;
            tbody.appendChild(saleHeader);
            
            // Produkte nach Gruppen innerhalb des Sales gruppieren
            const groups = {};
            sale.products.forEach(product => {
                const groupId = product.sale_group_id || 'unknown';
                const groupName = product.group_name || 'Gruppe';
                if (!groups[groupId]) {
                    groups[groupId] = {
                        name: groupName,
                        products: []
                    };
                }
                groups[groupId].products.push(product);
            });
            
            // Für jede Gruppe eine Gruppenüberschrift und Produkte rendern
            Object.keys(groups).forEach(groupId => {
                const group = groups[groupId];
                
                // Gruppenüberschrift
                const groupHeader = document.createElement('tr');
                groupHeader.innerHTML = `
                    <td colspan="9" style="padding: 10px 12px; background: #f8f9fa; font-weight: bold; font-size: 14px; color: #495057; border-bottom: 1px solid #dee2e6;">
                        ${group.name}
                    </td>
                `;
                tbody.appendChild(groupHeader);
                
                // Produkte rendern
                group.products.forEach(product => {
                    const tr = document.createElement('tr');
                    
                    // Verfügbar berechnen
                    const available = product.stock - product.in_progress;
                    
                    // Stock-Klasse
                    let stockClass = 'stock-zero';
                    if (available > 0) {
                        stockClass = 'stock-positive';
                    } else if (available < 0) {
                        stockClass = 'stock-negative';
                    }
                    
                    // Angebot HTML
                    let offerHtml = '-';
                    if (product.has_offer) {
                        offerHtml = `
                            <div>
                                <span class="offer-badge">Angebot</span>
                                <div class="offer-info">
                                    ${product.show_old_price ? '<span class="regular-price">' + product.regular_price + ' €</span>' : ''}
                                    <span class="sale-price">${product.sale_price} €</span>
                                    <div>
                                        ${product.offer_start_date ? '<span class="offer-begin">ab: ' + product.offer_start_date + '</span><br>' : ''}
                                        ${product.offer_end_date ? '<span class="offer-end">Bis: ' + product.offer_end_date + '</span>' : ''}
                                    </div>
                                </div>
                            </div>
                        `;
                    }
                    
                    tr.innerHTML = `
                        <td>
                            <input type="checkbox" data-product-id="${product.id}" ${product.sellable === '1' ? 'checked' : ''}>
                        </td>
                        <td>
                            ${product.image ? `<img src="${product.image}" alt="${product.name}" class="product-img">` : '<div style="width:50px;height:50px;background:#f5f5f5;border-radius:3px;display:flex;align-items:center;justify-content:center;color:#999;">📷</div>'}
                        </td>
                        <td>
                            <a href="${product.permalink}" target="_blank" class="product-name-link">${product.name}</a>
                            ${product.was_out_of_stock ? '<span style="margin-left: 8px; background: #d4edda; color: #155724; padding: 2px 6px; border-radius: 3px; font-size: 11px; font-weight: 600;">Restocked</span>' : ''}
                            ${product.was_low_stock ? '<span style="margin-left: 8px; background: #fff3cd; color: #856404; padding: 2px 6px; border-radius: 3px; font-size: 11px; font-weight: 600;">Low Stock Restocked</span>' : ''}
                            ${product.was_restocked && !product.was_out_of_stock && !product.was_low_stock ? '<span style="margin-left: 8px; background: #e2e3e5; color: #383d41; padding: 2px 6px; border-radius: 3px; font-size: 11px; font-weight: 600;">Restocked</span>' : ''}
                            ${product.is_new ? '<span style="margin-left: 8px; background: #cce5ff; color: #004085; padding: 2px 6px; border-radius: 3px; font-size: 11px; font-weight: 600;">Neu</span>' : ''}
                        </td>
                        <td style="text-align: right;">${product.stock}</td>
                        <td style="text-align: right;">${product.in_progress}</td>
                        <td style="text-align: right;" class="${stockClass}">${available}</td>
                        <td style="text-align: right;" class="price-cell">${product.price} €</td>
                        <td>${offerHtml}</td>
                        <td style="text-align: center;">
                            <button class="edit-btn" data-product-id="${product.id}" data-product-name="${product.name}" data-product-sku="${product.sku || ''}" data-product-new="${product.is_new ? 'true' : 'false'}" data-stock="${product.stock}" data-price="${product.price}" data-regular-price="${product.regular_price}" data-sale-price="${product.sale_price}" data-has-offer="${product.has_offer}" data-show-old-price="${product.show_old_price}" data-time-limited="${product.time_limited}" data-time-limit-type="${product.time_limit_type}" data-time-limit-duration="${product.time_limit_duration}" data-time-limit-date="${product.time_limit_date}" data-show-end-date="${product.show_end_date}" data-time-limit-days="${product.time_limit_days}" data-time-limit-hours="${product.time_limit_hours}" data-time-limit-minutes="${product.time_limit_minutes}" data-time-limit-time="${product.time_limit_time}" data-time-limit-date-only="${product.time_limit_date_only}" data-offer-start-date="${product.offer_start_date || ''}" title="Bearbeiten">✏️</button>
                        </td>
                    `;
                    
                    tbody.appendChild(tr);
                });
            });
        });
        
        // Checkbox-Event-Listener für Bulk-Sales
        document.querySelectorAll('input[type="checkbox"][data-product-id]:not(.event-attached)').forEach(checkbox => {
            checkbox.classList.add('event-attached');
            checkbox.addEventListener('change', function() {
                const productId = this.dataset.productId;
                const sellable = this.checked ? 1 : 0;
                updateSellStatus(productId, sellable);
            });
        });
        
        // Edit-Button Event-Listener für Bulk-Sales
        document.querySelectorAll('.edit-btn:not(.event-attached)').forEach(btn => {
            btn.classList.add('event-attached');
            btn.addEventListener('click', function() {
                openEditModal(this);
            });
        });
    } catch (error) {
        console.error('Fehler in renderBulkSalesGroup:', error);
    }
}

function openBulkSaleDetails(saleId, saleTitle) {
    document.getElementById('bulk-sale-details-title').textContent = saleTitle;
    document.getElementById('bulk-sale-details-title').dataset.saleId = saleId;
    document.getElementById('bulk-sale-details-content').innerHTML = '<div style="text-align: center; padding: 20px;">Laden...</div>';
    document.getElementById('bulk-sale-details-modal').style.display = 'flex';
    
    fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'action=get_bulk_sale_details&nonce=<?php echo wp_create_nonce('product_list_nonce'); ?>&sale_id=' + saleId
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            renderBulkSaleDetails(data.data);
        } else {
            document.getElementById('bulk-sale-details-content').innerHTML = '<div style="text-align: center; padding: 20px; color: #e74c3c;">Fehler beim Laden: ' + (data.data || 'Unbekannter Fehler') + '</div>';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        document.getElementById('bulk-sale-details-content').innerHTML = '<div style="text-align: center; padding: 20px; color: #e74c3c;">Fehler beim Laden</div>';
    });
}

function renderBulkSaleDetails(saleData) {
    const container = document.getElementById('bulk-sale-details-content');
    let html = '';
    
    // Start- und Enddatum
    if (saleData.start_date) {
        html += `<div style="margin-bottom: 15px;"><strong>Startdatum:</strong> ${saleData.start_date}</div>`;
    }
    if (saleData.end_date) {
        html += `<div style="margin-bottom: 15px;"><strong>Enddatum:</strong> ${saleData.end_date}</div>`;
    }
    if (saleData.show_end_date) {
        html += `<div style="margin-bottom: 15px;"><strong>Enddatum anzeigen:</strong> Ja</div>`;
    }
    
    html += '<hr style="margin: 20px 0; border: none; border-top: 1px solid #ddd;">';
    
    // Gruppen rendern
    if (saleData.groups && Object.keys(saleData.groups).length > 0) {
        Object.keys(saleData.groups).forEach(groupId => {
            const group = saleData.groups[groupId];
            html += `
                <div style="margin-bottom: 30px; padding: 20px; border: 1px solid #ddd; border-radius: 8px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                        <h4 style="margin: 0; font-size: 18px; color: #333;">${group.name}</h4>
                        ${group.has_offer ? '<span style="background: #d4edda; color: #155724; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 600;">Angebot aktiv</span>' : ''}
                    </div>
                    ${group.has_offer ? `
                        <div style="margin-bottom: 15px; padding: 10px; background: #f9f9f9; border-radius: 4px;">
                            <div style="margin-bottom: 5px;"><strong>Preistyp:</strong> ${group.discount_type === 'percent' ? 'Prozent' : 'Festbetrag'}</div>
                            <div style="margin-bottom: 5px;"><strong>Rabatt:</strong> ${group.discount_amount} ${group.discount_type === 'percent' ? '%' : '€'}</div>
                            ${group.show_old_price ? '<div style="margin-bottom: 5px;"><strong>Alter Preis anzeigen:</strong> Ja</div>' : ''}
                        </div>
                    ` : ''}
                    <div style="margin-bottom: 10px;"><strong>${group.products ? group.products.length : 0} Produkte in dieser Gruppe</strong></div>
                    <div style="max-height: 200px; overflow-y: auto; border: 1px solid #eee; border-radius: 4px;">
                        ${group.products && group.products.length > 0 ? renderProductList(group.products) : '<div style="padding: 20px; text-align: center; color: #888;">Keine Produkte</div>'}
                    </div>
                </div>
            `;
        });
    } else {
        html += '<div style="text-align: center; padding: 20px; color: #888;">Keine Gruppen</div>';
    }
    
    container.innerHTML = html;
}

function renderProductList(productIds) {
    let html = '';
    productIds.forEach(productId => {
        const product = allProducts.find(p => p.id == productId);
        if (product) {
            html += `
                <div style="padding: 10px; border-bottom: 1px solid #eee; display: flex; align-items: center;">
                    ${product.image ? `<img src="${product.image}" alt="${product.name}" style="width: 40px; height: 40px; object-fit: cover; margin-right: 10px; border-radius: 4px;">` : ''}
                    <div>
                        <div style="font-weight: 500;">${product.name}</div>
                        <div style="font-size: 12px; color: #666;">${product.price} €</div>
                    </div>
                </div>
            `;
        }
    });
    return html;
}

function deleteBulkSale(saleId) {
    fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'action=delete_bulk_sale&nonce=<?php echo wp_create_nonce('product_list_nonce'); ?>&sale_id=' + saleId
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('bulk-sale-details-modal').style.display = 'none';
            loadProducts();
        } else {
            alert('Fehler beim Löschen: ' + (data.data || 'Unbekannter Fehler'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Fehler beim Löschen');
    });
}

function markOfferAsRead(productId, isRead) {
    fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'action=mark_offer_as_read&nonce=<?php echo wp_create_nonce('product_list_nonce'); ?>&product_id=' + productId + '&is_read=' + (isRead ? 1 : 0)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            console.log('Angebot als gelesen markiert');
            // Liste neu laden
            renderProducts(allProducts);
        }
    })
    .catch(error => console.error('Error:', error));
}

// Edit-Button Event-Listener hinzufügen (initial)
document.querySelectorAll('.edit-btn:not(.event-attached)').forEach(btn => {
    btn.classList.add('event-attached');
    btn.addEventListener('click', function() {
        openEditModal(this);
    });
});

// Toggle-Switch Event-Listener hinzufügen
document.getElementById('group-by-offer-toggle').addEventListener('change', function() {
    renderProducts(allProducts);
});

function filterProducts(searchTerm) {
    const rows = document.querySelectorAll('#product-table-body tr');
    const term = searchTerm.toLowerCase();
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        const editBtn = row.querySelector('.edit-btn');
        const sku = editBtn ? (editBtn.dataset.productSku || '').toLowerCase() : '';
        const isNew = editBtn ? (editBtn.dataset.productNew === 'true') : false;
        
        let match = text.includes(term) || sku.includes(term);
        
        // Spezielle Suche nach "neu"
        if (term === 'neu' || term === 'new') {
            match = isNew;
        }
        
        if (match) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

function updateSellStatus(productId, isSellable) {
    fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'action=update_sell_status&nonce=<?php echo wp_create_nonce('product_list_nonce'); ?>&product_id=' + productId + '&is_sellable=' + (isSellable ? 1 : 0)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            console.log('Verkaufen-Status aktualisiert');
        }
    })
    .catch(error => console.error('Error:', error));
}

function openEditModal(btn) {
    const productId = btn.dataset.productId;
    const productName = btn.dataset.productName;
    const stock = btn.dataset.stock;
    const price = btn.dataset.price;
    const regularPrice = btn.dataset.regularPrice;
    const salePrice = btn.dataset.salePrice;
    const hasOffer = btn.dataset.hasOffer === 'true';
    const showOldPrice = btn.dataset.showOldPrice === '1';
    const timeLimited = btn.dataset.timeLimited === '1';
    const timeLimitType = btn.dataset.timeLimitType || 'days';
    const timeLimitDuration = btn.dataset.timeLimitDuration || '';
    const timeLimitDate = btn.dataset.timeLimitDate || '';
    const showEndDate = btn.dataset.showEndDate === '1';
    const timeLimitDays = btn.dataset.timeLimitDays || '';
    const timeLimitHours = btn.dataset.timeLimitHours || '';
    const timeLimitMinutes = btn.dataset.timeLimitMinutes || '';
    const timeLimitTime = btn.dataset.timeLimitTime || '';
    const timeLimitDateOnly = btn.dataset.timeLimitDateOnly || '';
    const offerStartDate = btn.dataset.offerStartDate || '';
    
    document.getElementById('modal-product-name').textContent = productName;
    document.getElementById('modal-stock').value = stock;
    // Immer den regulären Preis verwenden, nicht den Angebotspreis
    document.getElementById('modal-price').value = regularPrice || price;
    document.getElementById('modal-has-offer').checked = hasOffer;
    document.getElementById('modal-sale-price').value = salePrice;
    document.getElementById('modal-sale-percent').value = '';
    document.getElementById('modal-price-type-toggle').checked = false;
    document.getElementById('modal-show-old-price').checked = showOldPrice;
    document.getElementById('modal-time-limited').checked = timeLimited;
    document.getElementById('modal-time-limit-toggle').checked = (timeLimitType === 'date' ? false : true);
    document.getElementById('modal-time-limit-days').value = timeLimitDays;
    document.getElementById('modal-time-limit-hours').value = timeLimitHours;
    document.getElementById('modal-time-limit-minutes').value = timeLimitMinutes;
    document.getElementById('modal-time-limit-date').value = timeLimitDateOnly || timeLimitDate;
    document.getElementById('modal-time-limit-time').value = timeLimitTime;
    document.getElementById('modal-show-end-date').checked = showEndDate;
    
    // Startdatum setzen und anzeigen
    window.offerStartDate = offerStartDate;
    if (offerStartDate) {
        document.getElementById('modal-offer-start-display').textContent = 'Start: ' + offerStartDate;
    } else {
        document.getElementById('modal-offer-start-display').textContent = '';
    }
    
    document.getElementById('offer-fields').style.display = hasOffer ? 'block' : 'none';
    document.getElementById('time-limit-fields').style.display = (hasOffer && timeLimited) ? 'block' : 'none';
    document.getElementById('euro-price-field').style.display = 'block';
    document.getElementById('percent-price-field').style.display = 'none';
    
    // Zeitlimit-Felder basierend auf Toggle anzeigen
    if (timeLimitType === 'date') {
        document.getElementById('date-field').style.display = 'block';
        document.getElementById('duration-field').style.display = 'none';
    } else {
        document.getElementById('date-field').style.display = 'none';
        document.getElementById('duration-field').style.display = 'block';
    }
    
    document.getElementById('edit-modal').style.display = 'flex';
    
    // Speichern und Abbrechen Event-Listener
    document.getElementById('save-edit').onclick = function() {
        saveProductEdit(productId);
    };
    
    document.getElementById('cancel-edit').onclick = function() {
        closeEditModal();
    };
    
    // Angebot-Checkbox Event
    document.getElementById('modal-has-offer').onchange = function() {
        document.getElementById('offer-fields').style.display = this.checked ? 'block' : 'none';
        if (!this.checked) {
            document.getElementById('time-limit-fields').style.display = 'none';
        }
    };
    
    // Startdatum-Button Event
    window.offerStartDate = '';
    document.getElementById('modal-offer-start-btn').onclick = function() {
        document.getElementById('offer-start-modal').style.display = 'flex';
    };
    
    document.getElementById('cancel-offer-start').onclick = function() {
        document.getElementById('offer-start-modal').style.display = 'none';
    };
    
    document.getElementById('save-offer-start').onclick = function() {
        const date = document.getElementById('offer-start-date').value;
        const time = document.getElementById('offer-start-time').value;
        
        if (date && time) {
            window.offerStartDate = date + ' ' + time;
            document.getElementById('modal-offer-start-display').textContent = 'Start: ' + window.offerStartDate;
            document.getElementById('offer-start-modal').style.display = 'none';
        } else {
            alert('Bitte Datum und Uhrzeit eingeben');
        }
    };
    
    // Preis-Typ Toggle Event
    document.getElementById('modal-price-type-toggle').onchange = function() {
        if (this.checked) {
            // Prozent-Modus
            document.getElementById('euro-price-field').style.display = 'none';
            document.getElementById('percent-price-field').style.display = 'block';
        } else {
            // Euro-Modus
            document.getElementById('euro-price-field').style.display = 'block';
            document.getElementById('percent-price-field').style.display = 'none';
        }
    };
    
    // Prozent-Eingabe Event - automatische Preisberechnung
    document.getElementById('modal-sale-percent').oninput = function() {
        const percent = parseFloat(this.value);
        const regularPrice = parseFloat(document.getElementById('modal-price').value);
        
        if (!isNaN(percent) && !isNaN(regularPrice) && percent > 0) {
            const salePrice = regularPrice - (regularPrice * (percent / 100));
            document.getElementById('modal-sale-price').value = salePrice.toFixed(2);
        }
    };
    
    // Zeitlich begrenzt Checkbox Event
    document.getElementById('modal-time-limited').onchange = function() {
        document.getElementById('time-limit-fields').style.display = this.checked ? 'block' : 'none';
    };
    
    // Zeitlimit-Toggle Event
    document.getElementById('modal-time-limit-toggle').onchange = function() {
        if (this.checked) {
            // Für Zeit (Tage/Stunden)
            document.getElementById('date-field').style.display = 'none';
            document.getElementById('duration-field').style.display = 'block';
        } else {
            // Bis Datum
            document.getElementById('date-field').style.display = 'block';
            document.getElementById('duration-field').style.display = 'none';
        }
    };
}

function closeEditModal() {
    document.getElementById('edit-modal').style.display = 'none';
}

// Modal schließen bei Klick außerhalb
document.getElementById('edit-modal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeEditModal();
    }
});

// Dynamischer Countdown
let isReloading = false;

function updateCountdowns() {
    const countdowns = document.querySelectorAll('.countdown');
    const now = Math.floor(Date.now() / 1000);
    
    countdowns.forEach(function(countdown) {
        const dateTo = parseInt(countdown.dataset.countdown);
        const productId = countdown.dataset.productId;
        const diff = dateTo - now;
        
        if (diff > 0) {
            const hours = Math.floor(diff / (60 * 60));
            const minutes = Math.floor((diff % (60 * 60)) / 60);
            const seconds = diff % 60;
            
            const h = String(hours).padStart(2, '0');
            const m = String(minutes).padStart(2, '0');
            const s = String(seconds).padStart(2, '0');
            
            countdown.textContent = h + ':' + m + ':' + s;
        } else {
            countdown.textContent = 'Abgelaufen';
            countdown.style.color = '#e74c3c';
            countdown.classList.remove('countdown');
            // Zuerst AJAX-Call für Cleanup, dann Seite neu laden (nur einmal)
            if (!isReloading && productId) {
                isReloading = true;
                
                // AJAX-Call zum Bereinigen des abgelaufenen Angebots
                fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'action=cleanup_expired_sale&product_id=' + productId
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
    });
}

// Countdown jede Sekunde aktualisieren
setInterval(updateCountdowns, 1000);
updateCountdowns();

function saveProductEdit(productId) {
    const stock = document.getElementById('modal-stock').value;
    const price = document.getElementById('modal-price').value;
    const hasOffer = document.getElementById('modal-has-offer').checked;
    const salePrice = document.getElementById('modal-sale-price').value;
    const showOldPrice = document.getElementById('modal-show-old-price').checked;
    const timeLimited = document.getElementById('modal-time-limited').checked;
    const timeLimitToggle = document.getElementById('modal-time-limit-toggle').checked;
    const timeLimitDays = document.getElementById('modal-time-limit-days').value;
    const timeLimitHours = document.getElementById('modal-time-limit-hours').value;
    const timeLimitMinutes = document.getElementById('modal-time-limit-minutes').value;
    const timeLimitDate = document.getElementById('modal-time-limit-date').value;
    const timeLimitTime = document.getElementById('modal-time-limit-time').value;
    const showEndDate = document.getElementById('modal-show-end-date').checked;
    const priceTypeToggle = document.getElementById('modal-price-type-toggle').checked;
    
    // Validierung: Preis nur 2 Dezimalstellen
    const priceNum = parseFloat(price);
    if (isNaN(priceNum)) {
        alert('Bitte geben Sie einen gültigen Preis ein.');
        return;
    }
    const priceDecimals = (price.toString().split('.')[1] || '').length;
    if (priceDecimals > 2) {
        alert('Der Preis darf maximal 2 Dezimalstellen haben.');
        return;
    }
    
    // Validierung: Angebotspreis nur 2 Dezimalstellen und nicht unter 0.01€
    if (hasOffer) {
        const salePriceNum = parseFloat(salePrice);
        if (isNaN(salePriceNum)) {
            alert('Bitte geben Sie einen gültigen Angebotspreis ein.');
            return;
        }
        const salePriceDecimals = (salePrice.toString().split('.')[1] || '').length;
        if (salePriceDecimals > 2) {
            alert('Der Angebotspreis darf maximal 2 Dezimalstellen haben.');
            return;
        }
        if (salePriceNum < 0.01) {
            alert('Der Angebotspreis darf nicht unter 0.01€ liegen.');
            return;
        }
        
        // Validierung: Prozentsatz nur 2 Dezimalstellen
        if (priceTypeToggle) {
            const percentage = ((priceNum - salePriceNum) / priceNum) * 100;
            const percentageStr = percentage.toFixed(10); // Mit 10 Dezimalstellen formatieren
            const percentageDecimals = (percentageStr.split('.')[1] || '').replace(/0+$/, '').length;
            if (percentageDecimals > 2) {
                alert('Der Prozentsatz darf maximal 2 Dezimalstellen haben.');
                return;
            }
        }
    }
    
    let timeLimitType = 'date';
    let timeLimitDuration = '';
    
    if (timeLimitToggle) {
        // Für Zeit - prüfe ob Tage, Stunden und/oder Minuten angegeben
        const days = parseInt(timeLimitDays) || 0;
        const hours = parseInt(timeLimitHours) || 0;
        const minutes = parseInt(timeLimitMinutes) || 0;
        
        if (days > 0 || hours > 0 || minutes > 0) {
            // Berechne Gesamtminuten
            const totalMinutes = (days * 24 * 60) + (hours * 60) + minutes;
            timeLimitType = 'minutes';
            timeLimitDuration = totalMinutes;
        }
    }
    
    // Datum und Uhrzeit kombinieren
    let fullDateTime = timeLimitDate;
    if (timeLimitTime && !timeLimitToggle) {
        fullDateTime = timeLimitDate + ' ' + timeLimitTime;
    }
    
    let body = 'action=update_product_details&nonce=<?php echo wp_create_nonce('product_list_nonce'); ?>&product_id=' + productId + '&stock=' + stock + '&price=' + price + '&has_offer=' + (hasOffer ? 1 : 0) + '&sale_price=' + salePrice + '&show_old_price=' + (showOldPrice ? 1 : 0) + '&time_limited=' + (timeLimited ? 1 : 0) + '&time_limit_type=' + timeLimitType + '&time_limit_duration=' + timeLimitDuration + '&time_limit_date=' + fullDateTime + '&show_end_date=' + (showEndDate ? 1 : 0) + '&price_type_toggle=' + (priceTypeToggle ? 1 : 0) + '&offer_start_date=' + encodeURIComponent(window.offerStartDate || '');
    
    fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: body
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeEditModal();
            loadProducts(); // Neu laden
        } else {
            alert('Fehler beim Speichern: ' + (data.data || 'Unbekannter Fehler'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Fehler beim Speichern');
    });
}

// Sortierfunktion
let allProducts = [];
let currentSort = { column: null, direction: 'asc' };

function setupSorting() {
    const headers = document.querySelectorAll('#product-table th[data-sort]');
    headers.forEach(header => {
        header.addEventListener('click', () => {
            const column = header.dataset.sort;
            sortProducts(column);
        });
    });
}

function sortProducts(column) {
    // Sortierrichtung umschalten
    if (currentSort.column === column) {
        currentSort.direction = currentSort.direction === 'asc' ? 'desc' : 'asc';
    } else {
        currentSort.column = column;
        currentSort.direction = 'asc';
    }
    
    // Pfeile aktualisieren
    updateSortArrows();
    
    // Produkte sortieren
    allProducts.sort((a, b) => {
        let valueA, valueB;
        
        switch(column) {
            case 'sellable':
                // Pfeil nach oben (asc): nicht zu verkaufende oben (hidden = 1)
                // Pfeil nach unten (desc): zu verkaufende oben (visible = 0)
                valueA = a.catalog_visibility === 'visible' ? 0 : 1;
                valueB = b.catalog_visibility === 'visible' ? 0 : 1;
                break;
            case 'name':
                valueA = a.name.toLowerCase();
                valueB = b.name.toLowerCase();
                break;
            case 'stock':
                valueA = parseFloat(a.stock) || 0;
                valueB = parseFloat(b.stock) || 0;
                break;
            case 'in_progress':
                valueA = parseFloat(a.in_progress) || 0;
                valueB = parseFloat(b.in_progress) || 0;
                break;
            case 'available':
                valueA = (parseFloat(a.stock) || 0) - (parseFloat(a.in_progress) || 0);
                valueB = (parseFloat(b.stock) || 0) - (parseFloat(b.in_progress) || 0);
                break;
            case 'price':
                valueA = parseFloat(a.price) || 0;
                valueB = parseFloat(b.price) || 0;
                break;
            default:
                return 0;
        }
        
        if (valueA < valueB) return currentSort.direction === 'asc' ? -1 : 1;
        if (valueA > valueB) return currentSort.direction === 'asc' ? 1 : -1;
        return 0;
    });
    
    // Neu rendern
    renderProducts(allProducts);
}

function updateSortArrows() {
    const headers = document.querySelectorAll('#product-table th[data-sort]');
    headers.forEach(header => {
        const arrow = header.querySelector('.sort-arrow');
        if (header.dataset.sort === currentSort.column) {
            arrow.textContent = currentSort.direction === 'asc' ? '↑' : '↓';
        } else {
            arrow.textContent = '';
        }
    });
}

// renderProducts überschreiben, um Produkte zu speichern
const originalRenderProducts = window.renderProducts;
window.renderProducts = function(products) {
    allProducts = products;
    originalRenderProducts(products);
}

// Initialisierung
document.addEventListener('DOMContentLoaded', function() {
    setupSorting();
    
    // Bulk Sale Button Event
    document.getElementById('bulk-sale-btn').onclick = function() {
        openBulkSaleModal();
    };
    
    // Bulk Sale Modal Events
    document.getElementById('cancel-bulk-sale').onclick = function() {
        document.getElementById('bulk-sale-modal').style.display = 'none';
    };
    
    document.getElementById('save-bulk-sale').onclick = function() {
        saveBulkSale();
    };
    
    document.getElementById('add-bulk-sale-group').onclick = function() {
        addBulkSaleGroup();
    };
    
    // Bulk Sale Date Events
    let currentBulkSaleDateField = '';
    document.getElementById('bulk-sale-start-btn').onclick = function() {
        currentBulkSaleDateField = 'start';
        // Aktuelle Zeit und Datum vorbelegen
        const now = new Date();
        const day = String(now.getDate()).padStart(2, '0');
        const month = String(now.getMonth() + 1).padStart(2, '0');
        const year = now.getFullYear();
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        document.getElementById('bulk-sale-date-input').value = day + '.' + month + '.' + year;
        document.getElementById('bulk-sale-time-input').value = hours + ':' + minutes;
        document.getElementById('bulk-sale-date-modal').style.display = 'flex';
    };
    
    document.getElementById('bulk-sale-end-btn').onclick = function() {
        currentBulkSaleDateField = 'end';
        // Aktuelle Zeit und Datum vorbelegen
        const now = new Date();
        const day = String(now.getDate()).padStart(2, '0');
        const month = String(now.getMonth() + 1).padStart(2, '0');
        const year = now.getFullYear();
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        document.getElementById('bulk-sale-date-input').value = day + '.' + month + '.' + year;
        document.getElementById('bulk-sale-time-input').value = hours + ':' + minutes;
        document.getElementById('bulk-sale-date-modal').style.display = 'flex';
    };
    
    document.getElementById('cancel-bulk-sale-date').onclick = function() {
        document.getElementById('bulk-sale-date-modal').style.display = 'none';
    };
    
    // Automatische Punktsetzung im Datumsfeld
    document.getElementById('bulk-sale-date-input').oninput = function(e) {
        let value = e.target.value.replace(/\D/g, '');
        if (value.length > 2) {
            value = value.substring(0, 2) + '.' + value.substring(2);
        }
        if (value.length > 5) {
            value = value.substring(0, 5) + '.' + value.substring(5);
        }
        if (value.length > 10) {
            value = value.substring(0, 10);
        }
        e.target.value = value;
    };
    
    document.getElementById('save-bulk-sale-date').onclick = function() {
        const date = document.getElementById('bulk-sale-date-input').value;
        const time = document.getElementById('bulk-sale-time-input').value;
        
        if (date && time) {
            const dateTime = date + ' ' + time;
            if (currentBulkSaleDateField === 'start') {
                document.getElementById('bulk-sale-start-display').textContent = 'Start: ' + dateTime;
                document.getElementById('bulk-sale-start-display').dataset.value = dateTime;
            } else {
                document.getElementById('bulk-sale-end-display').textContent = 'End: ' + dateTime;
                document.getElementById('bulk-sale-end-display').dataset.value = dateTime;
            }
            document.getElementById('bulk-sale-date-modal').style.display = 'none';
        } else {
            alert('Bitte Datum und Uhrzeit eingeben');
        }
    };
    
    // OK Button für Datums-Sektion
    document.getElementById('bulk-sale-dates-ok').onclick = function() {
        const startDate = document.getElementById('bulk-sale-start-display').dataset.value;
        const endDate = document.getElementById('bulk-sale-end-display').dataset.value;
        
        if (!startDate) {
            alert('Bitte Startdatum setzen');
            return;
        }
        
        // Datums-Sektion ausblenden und Gruppen-Sektion einblenden
        document.getElementById('bulk-sale-date-section').style.display = 'none';
        document.getElementById('bulk-sale-groups-section').style.display = 'block';
        
        // Gruppe 1 automatisch generieren
        if (bulkSaleGroups.length === 0) {
            addBulkSaleGroup();
        }
    };
    
    // Abbrechen Button für Datums-Sektion
    document.getElementById('bulk-sale-dates-cancel').onclick = function() {
        document.getElementById('bulk-sale-modal').style.display = 'none';
    };
    
    // Add Product Modal Events
    document.getElementById('close-add-product-modal').onclick = function() {
        document.getElementById('add-product-to-group-modal').style.display = 'none';
    };
    
    document.getElementById('add-product-search').oninput = function() {
        filterAddProductList(this.value);
    };
    
    // Bulk Sale Details Modal Events
    document.getElementById('close-bulk-sale-details').onclick = function() {
        document.getElementById('bulk-sale-details-modal').style.display = 'none';
    };
    
    document.getElementById('delete-bulk-sale-btn').onclick = function() {
        const saleId = document.getElementById('bulk-sale-details-title').dataset.saleId;
        if (confirm('Möchten Sie diese Bulk-Aktion wirklich löschen?')) {
            deleteBulkSale(saleId);
        }
    };
});

let bulkSaleGroups = [];
let currentGroupId = '';

function openBulkSaleModal() {
    document.getElementById('bulk-sale-title').value = '';
    document.getElementById('bulk-sale-start-display').textContent = '';
    document.getElementById('bulk-sale-start-display').dataset.value = '';
    document.getElementById('bulk-sale-end-display').textContent = '';
    document.getElementById('bulk-sale-end-display').dataset.value = '';
    document.getElementById('bulk-sale-show-end-date').checked = false;
    bulkSaleGroups = [];
    document.getElementById('bulk-sale-groups').innerHTML = '';
    // Datums-Sektion einblenden und Gruppen-Sektion ausblenden
    document.getElementById('bulk-sale-date-section').style.display = 'block';
    document.getElementById('bulk-sale-groups-section').style.display = 'none';
    document.getElementById('bulk-sale-modal').style.display = 'flex';
}

function addBulkSaleGroup() {
    // Zuerst die Werte aus dem DOM auslesen und im Array aktualisieren
    bulkSaleGroups.forEach(group => {
        group.name = document.getElementById(`group-name-${group.id}`) ? document.getElementById(`group-name-${group.id}`).value : group.name;
        group.has_offer = document.getElementById(`group-offer-${group.id}`) ? document.getElementById(`group-offer-${group.id}`).checked : false;
        group.discount_type = document.getElementById(`group-price-toggle-${group.id}`) ? (document.getElementById(`group-price-toggle-${group.id}`).checked ? 'percent' : 'fixed') : 'fixed';
        group.discount_amount = document.getElementById(`group-price-toggle-${group.id}`) && document.getElementById(`group-price-toggle-${group.id}`).checked 
            ? (document.getElementById(`group-sale-percent-${group.id}`) ? document.getElementById(`group-sale-percent-${group.id}`).value : '')
            : (document.getElementById(`group-sale-price-${group.id}`) ? document.getElementById(`group-sale-price-${group.id}`).value : '');
        group.show_old_price = document.getElementById(`group-show-old-price-${group.id}`) ? document.getElementById(`group-show-old-price-${group.id}`).checked : false;
    });
    
    const groupId = 'group_' + Date.now();
    const group = {
        id: groupId,
        name: 'Gruppe ' + (bulkSaleGroups.length + 1),
        has_offer: false,
        discount_type: 'fixed',
        discount_amount: '',
        show_old_price: false,
        products: []
    };
    bulkSaleGroups.push(group);
    renderBulkSaleGroups();
}

function renderBulkSaleGroups() {
    const container = document.getElementById('bulk-sale-groups');
    container.innerHTML = '';
    
    bulkSaleGroups.forEach((group, index) => {
        const groupDiv = document.createElement('div');
        groupDiv.style.border = '1px solid #ddd';
        groupDiv.style.padding = '15px';
        groupDiv.style.marginBottom = '15px';
        groupDiv.style.borderRadius = '4px';
        
        groupDiv.innerHTML = `
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                <input type="text" id="group-name-${group.id}" value="${group.name}" onchange="updateGroupName('${group.id}', this.value)" style="font-size: 16px; font-weight: bold; padding: 5px; border: 1px solid #ddd; border-radius: 3px; width: 60%;">
                <button onclick="removeBulkSaleGroup('${group.id}')" style="padding: 5px 10px; background: #e74c3c; color: white; border: none; border-radius: 3px; cursor: pointer;">Gruppe löschen</button>
            </div>
            <div style="margin-bottom: 10px;">
                <button onclick="openAddProductModal('${group.id}')" style="padding: 8px 12px; background: #f5f5f5; border: 1px solid #ddd; border-radius: 4px; cursor: pointer;">Artikel zu Gruppe hinzufügen</button>
            </div>
            <div style="margin-bottom: 10px; font-size: 13px; color: #666;">
                ${group.products.length} Artikel in Gruppe
            </div>
            <div style="margin-bottom: 10px;">
                <label style="display: block; margin-bottom: 5px; font-weight: 500;">
                    <input type="checkbox" id="group-offer-${group.id}" ${group.has_offer ? 'checked' : ''} onchange="toggleGroupOffer('${group.id}')"> Angebot aktivieren
                </label>
            </div>
            <div id="group-offer-fields-${group.id}" style="display: ${group.has_offer ? 'block' : 'none'}; padding-left: 20px;">
                <div style="margin-bottom: 10px;">
                    <label style="display: block; margin-bottom: 5px; font-weight: 500;">Preistyp</label>
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <span style="font-size: 14px;">€</span>
                        <label style="position: relative; display: inline-block; width: 50px; height: 26px;">
                            <input type="checkbox" id="group-price-toggle-${group.id}" ${group.discount_type === 'percent' ? 'checked' : ''} onchange="toggleGroupPriceType('${group.id}')">
                            <span class="slider"></span>
                        </label>
                        <span style="font-size: 14px;">%</span>
                    </div>
                </div>
                <div id="group-euro-field-${group.id}" style="margin-bottom: 10px;">
                    <label style="display: block; margin-bottom: 5px; font-weight: 500;">Angebotspreis (€)</label>
                    <input type="number" id="group-sale-price-${group.id}" step="0.01" value="${group.discount_amount}" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;" min="0">
                </div>
                <div id="group-percent-field-${group.id}" style="margin-bottom: 10px; display: none;">
                    <label style="display: block; margin-bottom: 5px; font-weight: 500;">Rabatt (%)</label>
                    <input type="number" id="group-sale-percent-${group.id}" step="1" value="${group.discount_amount}" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;" min="0" max="100">
                </div>
                <div style="margin-bottom: 10px;">
                    <label style="display: block; margin-bottom: 5px; font-weight: 500;">
                        <input type="checkbox" id="group-show-old-price-${group.id}" ${group.show_old_price ? 'checked' : ''}> Alten Preis anzeigen
                    </label>
                </div>
            </div>
        `;
        
        container.appendChild(groupDiv);
    });
}

function removeBulkSaleGroup(groupId) {
    bulkSaleGroups = bulkSaleGroups.filter(g => g.id !== groupId);
    renderBulkSaleGroups();
}

function updateGroupName(groupId, newName) {
    const group = bulkSaleGroups.find(g => g.id === groupId);
    if (group) {
        group.name = newName;
    }
}

function toggleGroupOffer(groupId) {
    const group = bulkSaleGroups.find(g => g.id === groupId);
    if (group) {
        group.has_offer = !group.has_offer;
        document.getElementById(`group-offer-fields-${groupId}`).style.display = group.has_offer ? 'block' : 'none';
    }
}

function toggleGroupPriceType(groupId) {
    const group = bulkSaleGroups.find(g => g.id === groupId);
    if (group) {
        const isPercent = document.getElementById(`group-price-toggle-${groupId}`).checked;
        group.discount_type = isPercent ? 'percent' : 'fixed';
        document.getElementById(`group-euro-field-${groupId}`).style.display = isPercent ? 'none' : 'block';
        document.getElementById(`group-percent-field-${groupId}`).style.display = isPercent ? 'block' : 'none';
    }
}

function openAddProductModal(groupId) {
    currentGroupId = groupId;
    document.getElementById('add-product-to-group-modal').style.display = 'flex';
    document.getElementById('add-product-search').value = '';
    loadAvailableProducts();
}

function loadAvailableProducts() {
    fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'action=get_product_list_data&nonce=<?php echo wp_create_nonce('product_list_nonce'); ?>'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const products = data.data.filter(p => !p.part_of_sale && !p.has_offer);
            renderAddProductList(products);
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

function renderAddProductList(products) {
    const container = document.getElementById('add-product-list');
    container.innerHTML = '';
    
    products.forEach(product => {
        const productDiv = document.createElement('div');
        productDiv.style.display = 'flex';
        productDiv.style.alignItems = 'center';
        productDiv.style.padding = '10px';
        productDiv.style.borderBottom = '1px solid #eee';
        productDiv.dataset.productId = product.id;
        productDiv.dataset.productName = product.name.toLowerCase();
        productDiv.dataset.productSku = (product.sku || '').toLowerCase();
        
        productDiv.innerHTML = `
            <div style="width: 50px; height: 50px; margin-right: 15px;">
                ${product.image ? `<img src="${product.image}" alt="${product.name}" style="width: 100%; height: 100%; object-fit: cover;">` : '<div style="width:50px;height:50px;background:#f5f5f5;border-radius:3px;display:flex;align-items:center;justify-content:center;color:#999;">📷</div>'}
            </div>
            <div style="flex: 1;">
                <div style="font-weight: 500;">${product.name}</div>
                <div style="font-size: 13px; color: #666;">${product.sku ? 'SKU: ' + product.sku : ''} | ${product.price} €</div>
            </div>
            <button onclick="addProductToGroup(${product.id})" style="padding: 8px 12px; background: #333; color: white; border: none; border-radius: 4px; cursor: pointer;">Hinzufügen</button>
        `;
        
        container.appendChild(productDiv);
    });
}

function filterAddProductList(searchTerm) {
    const rows = document.querySelectorAll('#add-product-list > div');
    const term = searchTerm.toLowerCase();
    
    rows.forEach(row => {
        const name = row.dataset.productName || '';
        const sku = row.dataset.productSku || '';
        
        if (name.includes(term) || sku.includes(term)) {
            row.style.display = 'flex';
        } else {
            row.style.display = 'none';
        }
    });
}

function addProductToGroup(productId) {
    const group = bulkSaleGroups.find(g => g.id === currentGroupId);
    if (group) {
        group.products.push(productId);
        document.getElementById('add-product-to-group-modal').style.display = 'none';
        renderBulkSaleGroups();
    }
}

function saveBulkSale() {
    const title = document.getElementById('bulk-sale-title').value;
    const startDate = document.getElementById('bulk-sale-start-display').dataset.value || '';
    const endDate = document.getElementById('bulk-sale-end-display').dataset.value || '';
    const showEndDate = document.getElementById('bulk-sale-show-end-date').checked;
    
    if (!title) {
        alert('Bitte einen Aktionsnamen eingeben');
        return;
    }
    
    // Gruppen-Daten aktualisieren
    bulkSaleGroups.forEach(group => {
        group.name = document.getElementById(`group-name-${group.id}`) ? document.getElementById(`group-name-${group.id}`).value : group.name;
        group.has_offer = document.getElementById(`group-offer-${group.id}`) ? document.getElementById(`group-offer-${group.id}`).checked : false;
        group.discount_type = document.getElementById(`group-price-toggle-${group.id}`) ? (document.getElementById(`group-price-toggle-${group.id}`).checked ? 'percent' : 'fixed') : 'fixed';
        group.discount_amount = document.getElementById(`group-price-toggle-${group.id}`) && document.getElementById(`group-price-toggle-${group.id}`).checked 
            ? (document.getElementById(`group-sale-percent-${group.id}`) ? document.getElementById(`group-sale-percent-${group.id}`).value : '')
            : (document.getElementById(`group-sale-price-${group.id}`) ? document.getElementById(`group-sale-price-${group.id}`).value : '');
        group.show_old_price = document.getElementById(`group-show-old-price-${group.id}`) ? document.getElementById(`group-show-old-price-${group.id}`).checked : false;
    });
    
    console.log('Debug: Gruppen vor dem Speichern:', JSON.stringify(bulkSaleGroups, null, 2));
    
    const body = 'action=create_bulk_sale&nonce=<?php echo wp_create_nonce('product_list_nonce'); ?>&title=' + encodeURIComponent(title) + '&start_date=' + encodeURIComponent(startDate) + '&end_date=' + encodeURIComponent(endDate) + '&show_end_date=' + (showEndDate ? 1 : 0) + '&groups=' + encodeURIComponent(JSON.stringify(bulkSaleGroups));
    
    fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: body
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('bulk-sale-modal').style.display = 'none';
            loadProducts();
        } else {
            alert('Fehler beim Speichern: ' + (data.data || 'Unbekannter Fehler'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Fehler beim Speichern');
    });
}
</script>

<?php get_footer(); ?>
