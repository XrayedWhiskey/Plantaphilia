<?php
// Nicht verfügbar Seite mit Inhalt füllen
require_once 'wp-load.php';

$page = get_page_by_path('nicht-verfuegbar');

if ($page) {
    $content = '
<div class="w-container">
    <div class="vc_row">
        <div class="vc_col-sm-12">
            <div class="wpb_text_column">
                <div class="wpb_wrapper">
                    <h1 style="text-align: center; margin-bottom: 30px;">Produkt nicht verfügbar</h1>
                    <p style="text-align: center; font-size: 18px; color: #666; margin-bottom: 40px;">
                        Schön, dass du hier bist! Dieses Produkt steht aktuell nicht mehr zum Verkauf bereit.
                    </p>
                    <div style="text-align: center;">
                        <a href="' . home_url() . '" class="w-btn us-btn-style_1" style="display: inline-block;">Zurück zur Startseite</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>';
    
    $page_data = array(
        'ID' => $page->ID,
        'post_content' => $content
    );
    
    $result = wp_update_post($page_data);
    
    if ($result) {
        echo "✓ Seite wurde erfolgreich aktualisiert!\n";
        echo "URL: " . get_permalink($page->ID) . "\n";
    } else {
        echo "✗ Fehler beim Aktualisieren der Seite.\n";
    }
} else {
    echo "✗ Seite nicht gefunden. Bitte stelle sicher, dass die Seite mit dem Slug 'nicht-verfuegbar' existiert.\n";
}
?>
