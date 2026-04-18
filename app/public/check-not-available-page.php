<?php
// Prüfe ob die Nicht verfügbar Seite existiert
require_once 'wp-load.php';

$page = get_page_by_path('nicht-verfuegbar');

if ($page) {
    echo "✓ Seite wurde gefunden!\n";
    echo "Seiten-ID: " . $page->ID . "\n";
    echo "Titel: " . $page->post_title . "\n";
    echo "Slug: " . $page->post_name . "\n";
    echo "URL: " . get_permalink($page->ID) . "\n";
} else {
    echo "✗ Seite nicht gefunden.\n";
    echo "Versuche alternative Slugs...\n";
    
    // Suche nach Seiten mit ähnlichen Namen
    $pages = get_pages(array('post_status' => 'publish'));
    foreach ($pages as $p) {
        if (stripos($p->post_name, 'nicht') !== false || stripos($p->post_title, 'nicht') !== false) {
            echo "Gefunden: " . $p->post_title . " (Slug: " . $p->post_name . ")\n";
        }
    }
}
?>
