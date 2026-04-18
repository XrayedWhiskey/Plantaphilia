<?php
// Produktliste-Seite automatisch erstellen
require_once 'wp-load.php';

// Prüfen ob Seite schon existiert
$existing_page = get_page_by_path('produkt-liste');

if (!$existing_page) {
    $page_data = array(
        'post_title'    => 'Produktliste',
        'post_content'  => '',
        'post_status'   => 'publish',
        'post_type'     => 'page',
        'post_name'     => 'produkt-liste',
        'page_template' => 'page-produkt-liste.php'
    );
    
    $page_id = wp_insert_post($page_data);
    
    if ($page_id) {
        echo "✓ Produktliste-Seite wurde erfolgreich erstellt!\n";
        echo "Seite-ID: " . $page_id . "\n";
        echo "URL: " . get_permalink($page_id) . "\n";
    } else {
        echo "✗ Fehler beim Erstellen der Seite.\n";
    }
} else {
    echo "✓ Seite existiert bereits.\n";
    echo "URL: " . get_permalink($existing_page->ID) . "\n";
    
    // Template aktualisieren falls nötig
    update_post_meta($existing_page->ID, '_wp_page_template', 'page-produkt-liste.php');
    echo "✓ Template wurde aktualisiert.\n";
}
?>
