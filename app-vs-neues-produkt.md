# App vs. /neues-produkt — Nachzieh-Liste

`/neues-produkt` (`Impreza-child/page-produkt-hinzufuegen.php`) ist laut Absprache inaktiv — die Electron-App
(`Local Sites/PlantaphiliaApp`) ist die einzige aktiv genutzte Produktpflege. Diese Liste hält fest, was die App
seit dem Substrat/Dünger-Feature kann und `/neues-produkt` (noch) nicht, damit die Seite bei Bedarf nachgezogen
werden kann.

## Fehlt in /neues-produkt

- **Dritter Produkttyp „Dünger"**: Namensfeld + Dropdown mit 3 Typen (Langzeitdünger, Kalibetonter Dünger,
  Ausgeglichener Dünger). Pro Typ ist nur ein Produkt erlaubt (Eindeutigkeitsprüfung fehlt in `/neues-produkt`
  komplett).
- **Substrat-Komposition**: beliebig viele Zutaten-Zeilen (Prozent + Name) statt der bisherigen
  Pflegehinweise-Felder, plus „Verkaufe ich"-Checkbox.
- **Substrat-/Dünger-Verknüpfung bei Pflanzen**: Dropdown zur Auswahl eines existierenden Substrat-Produkts
  (mit Live-Vorschau der Zutatenliste) sowie Düngertyp- und Düngemenge-Auswahl (viel/mäßig/wenig) — Düngertyp ist
  auch ohne existierendes Dünger-Produkt wählbar.
- **Einheit „kg"**: dritte Option neben Stück/Liter, mit eigenem Inhalt-Feld (`weight_content`).
- **Kategorie-Beschreibungsfeld**: freies Textfeld pro Kategorie (analog zur bestehenden „Überschrift"), synct wie
  diese zur Website (`_pa_cat_description` Term-Meta) und wird auf der Shopseite unter der Kategorie-Überschrift
  angezeigt, sobald nach genau dieser einen Kategorie gefiltert wird.
- **Neue Meta-Description-Vorlage**: „Gattung Art 'Kultivar' im Xcm Topf für Y€, verfügbar: Kurzbeschreibung"
  (variabel: „ab Y€" statt „im Xcm Topf für Y€"), analoges Muster für Substrat/Dünger. Wird automatisch aus
  Preis/Bestand/Topfgröße generiert, nicht mehr frei getippt.

## Betroffene Datenbank-/Meta-Felder (zur Orientierung)

Neue `products`-Spalten (SQLite, App-seitig): `fertilizer_type`, `weight_content`, `composition`,
`sell_own_substrate`, `substrate_product_id`, `fertilizer_type_choice`, `fertilizer_amount`.
Neue `categories`-Spalte: `description`.

Neue WooCommerce-Postmeta (von der App gepusht, siehe `api.js`): `_pa_substrate_name`,
`_pa_substrate_composition`, `_pa_substrate_sell_own`, `_pa_substrate_wp_id`, `_pa_fertilizer_type_choice`,
`_pa_fertilizer_amount`, `_pa_fertilizer_type`, `_product_weight_kg`.
Neues `pa_category`-Term-Meta: `_pa_cat_description` (Push+Pull, analog zu `_pa_cat_heading`).

## Bereits auf der Website umgesetzt

- Produktseite: Substrat-Empfehlung/Kaufen-Link, Dünger-Auto-Link per Typ-Suche, live aktualisiertes
  Verfügbarkeits-Wort in der Yoast-Meta-Description
  (`woocommerce/content-single-product.php`, `functions.php`: `pa_find_fertilizer_product_id`,
  `pa_live_availability_in_metadesc`).
- Shopseite: Kategorie-Beschreibung unter der Kategorie-Überschrift bei aktivem Einzel-Kategorie-Filter
  (`woocommerce/archive-product.php`: `window.paCatDescriptions`, `#pa-shop-description`).

Nur die Eingabe-Oberfläche in `/neues-produkt` fehlt noch für alle oben genannten Punkte.
