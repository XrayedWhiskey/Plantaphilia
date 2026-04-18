<?php
// Site-URL nach Migration korrigieren für LocalWP
require_once 'wp-load.php';

global $wpdb;

// Hole aktuelle Site-URLs
$home_url = get_option('home');
$site_url = get_option('siteurl');

echo "Aktuelle URLs:\n";
echo "  home: $home_url\n";
echo "  siteurl: $site_url\n\n";

// Korrigiere auf LocalWP-URL
$new_url = 'http://plantaphilia.local';

if ($home_url !== $new_url || $site_url !== $new_url) {
    update_option('home', $new_url);
    update_option('siteurl', $new_url);
    echo "✓ URLs aktualisiert auf: $new_url\n";
} else {
    echo "✓ URLs sind bereits korrekt\n";
}

// Flush rewrite rules
flush_rewrite_rules();
echo "✓ Rewrite rules geflushed\n";

echo "\nDone! Site sollte jetzt erreichbar sein.\n";
?>
