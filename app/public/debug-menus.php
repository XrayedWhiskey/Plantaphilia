<?php
// Debug: Alle Menüs und ihre Namen anzeigen
require_once 'wp-load.php';

$menus = get_registered_nav_menus();
echo "Registrierte Menüs:\n";
print_r($menus);

echo "\n\nAlle Menü-Orte:\n";
$locations = get_nav_menu_locations();
print_r($locations);
?>
