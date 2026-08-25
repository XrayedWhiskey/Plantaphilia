---
description: Plantaphilia WordPress Shop – WooCommerce Custom Theme mit Admin-Tools & AI-Chatbot
---

# Plantaphilia WordPress Shop – WooCommerce Custom Theme mit Admin-Tools & AI-Chatbot

WordPress Child-Theme für Pelargonien-Shop mit Custom Templates, Admin-Verwaltung, AI-Support und Security-Features. Wichtige Einstiegspunkte: Theme-Loading [1a], Produktseite [2b], AI-Chatbot [3c], Admin-Security [4b], Stock-Management [5d], Shop-Filter [7b].

## Trace 1: Theme Loading & Template Routing

**Description:** WordPress Theme-System: Wie das Impreza Child-Theme geladen wird und Custom Templates für Produkte/Checkout routet

```
WordPress Theme Loading & Template Routing
├── wp_enqueue_scripts Hook <-- functions.php:6
│   └── pa_enqueue_design_assets() <-- 1a
│       ├── wp_enqueue_style('pa-design') <-- functions.php:9
│       ├── wp_enqueue_style('pa-uikit') <-- functions.php:15
│       └── wp_enqueue_style('pa-pdp') <-- functions.php:21
├── template_redirect Hook (Priority 1) <-- functions.php:36
│   └── Bypass-Funktion für Produkte <-- 1b
│       ├── is_product() prüfen <-- functions.php:37
│       └── include single-product.php <-- 1c
├── get_header() aufgerufen
│   └── header.php geladen <-- header.php:15
│       └── <body class="pa-storefront"> <-- 1d
└── get_footer() aufgerufen
    └── footer.php geladen <-- footer.php:1
        └── JavaScript Initialisierung <-- footer.php:167
            └── Cart Drawer Setup <-- 1e
```

### Location 1a: CSS Design-System laden
**Description:** Hook registriert pa-design.css, ui-kit.css, product-page.css  
**Path:** `c:/Users/raffa/Local Sites/plantaphilia/app/public/wp-content/themes/Impreza-child/functions.php:6`

### Location 1b: Template Bypass für Produktseiten
**Description:** Impreza Page-Builder umgehen, Priority 1  
**Path:** `c:/Users/raffa/Local Sites/plantaphilia/app/public/wp-content/themes/Impreza-child/functions.php:36`

### Location 1c: Custom single-product.php laden
**Description:** Direkter Include statt WordPress Template-Hierarchie  
**Path:** `c:/Users/raffa/Local Sites/plantaphilia/app/public/wp-content/themes/Impreza-child/functions.php:40`

### Location 1d: Body-Tag mit pa-storefront Klasse
**Description:** Globale CSS-Klasse für Design-System  
**Path:** `c:/Users/raffa/Local Sites/plantaphilia/app/public/wp-content/themes/Impreza-child/header.php:22`

### Location 1e: Cart Drawer JavaScript initialisieren
**Description:** Client-seitige Interaktionen für Warenkorb-Sidebar  
**Path:** `c:/Users/raffa/Local Sites/plantaphilia/app/public/wp-content/themes/Impreza-child/footer.php:184`

---

## Trace 2: Single Product Page Rendering

**Description:** WooCommerce Produktseite: Von Template-Bypass über Datensammlung bis zur Galerie-Ausgabe mit botanischer Taxonomie

```
WordPress Template System
├── template_redirect Hook (Priority 1) <-- 2a
│   └── Impreza Page-Builder Bypass <-- functions.php:37
│       └── include single-product.php <-- functions.php:40
│           └── get_header() <-- content-single-product.php:9
│               └── content-single-product.php
│                   ├── Daten sammeln
│                   │   ├── get_post_meta(_pa_gattung) <-- 2b
│                   │   ├── get_post_meta(_pa_art) <-- content-single-product.php:31
│                   │   ├── get_post_meta(_pa_kultivar) <-- content-single-product.php:32
│                   │   └── get_post_meta(_pa_care_*) <-- 2c
│                   └── HTML Rendering
│                       ├── <div pdp-main-img> <-- 2d
│                       │   ├── Galerie mit Thumbnails <-- content-single-product.php:97
│                       │   └── Badges (Sale/Neu/Ausverkauft) <-- content-single-product.php:127
│                       └── <div pdp-taxonomy> <-- 2e
│                           └── Gattung + Art ausgeben
```

### Location 2a: Single Product Template Entry
**Description:** Wird von template_redirect Hook geladen (Bypass Impreza)  
**Path:** `c:/Users/raffa/Local Sites/plantaphilia/app/public/wp-content/themes/Impreza-child/single-product.php:1`

### Location 2b: Botanische Taxonomie laden
**Description:** Custom Meta-Felder: Gattung, Art, Kultivar  
**Path:** `c:/Users/raffa/Local Sites/plantaphilia/app/public/wp-content/themes/Impreza-child/woocommerce/content-single-product.php:30`

### Location 2c: Pflegehinweise laden
**Description:** Licht, Wasser, Winter, Temperatur-Daten  
**Path:** `c:/Users/raffa/Local Sites/plantaphilia/app/public/wp-content/themes/Impreza-child/woocommerce/content-single-product.php:63`

### Location 2d: Produktgalerie rendern
**Description:** Hauptbild mit Thumbnails, Badges, Zoom-Hint  
**Path:** `c:/Users/raffa/Local Sites/plantaphilia/app/public/wp-content/themes/Impreza-child/woocommerce/content-single-product.php:111`

### Location 2e: Taxonomie-Anzeige
**Description:** Gattung + Art als wissenschaftlicher Name  
**Path:** `c:/Users/raffa/Local Sites/plantaphilia/app/public/wp-content/themes/Impreza-child/woocommerce/content-single-product.php:152`

---

## Trace 3: AI Chatbot Request Flow (NVIDIA NIM Streaming)

**Description:** Support-Chatbot: AJAX-Handler → Rate-Limiting → Streaming API-Call → Tool-Execution → SSE-Response an Client

```
AI Chatbot Request Flow (NVIDIA NIM)
├── WordPress AJAX System
│   ├── wp_ajax_pa_chatbot_stream Hook <-- 3a
│   └── pa_chatbot_stream_handler() <-- functions.php:568
│       ├── Nonce & Rate-Limit Check <-- 3b
│       ├── Payload validieren (max 50KB) <-- functions.php:603
│       └── pa_chatbot_stream_loop() <-- 3c
│           └── pa_chatbot_stream_api() <-- 3d
│               ├── cURL POST zu NVIDIA NIM <-- functions.php:459
│               ├── SSE-Chunks empfangen <-- functions.php:470
│               │   ├── Reasoning-Content streamen <-- functions.php:492
│               │   └── Tool-Calls akkumulieren <-- functions.php:502
│               └── Tool-Calls verarbeiten <-- functions.php:547
│                   ├── pa_chatbot_execute_tool() <-- 3e
│                   │   ├── send_email_to_admin <-- 3f
│                   │   ├── get_order_history <-- functions.php:709
│                   │   └── get_product_info <-- functions.php:770
│                   └── Rekursion (max Tiefe 4) <-- functions.php:563
└── Frontend (page-kontakt.php)
    └── Chat UI mit SSE EventSource <-- 3g
```

### Location 3a: AJAX Hook registrieren
**Description:** Für eingeloggte und nicht-eingeloggte User  
**Path:** `c:/Users/raffa/Local Sites/plantaphilia/app/public/wp-content/themes/Impreza-child/functions.php:403`

### Location 3b: Rate-Limiting prüfen
**Description:** 20 Anfragen/Stunde, 60/Tag pro IP  
**Path:** `c:/Users/raffa/Local Sites/plantaphilia/app/public/wp-content/themes/Impreza-child/functions.php:581`

### Location 3c: Streaming-Loop starten
**Description:** Rekursive Schleife für Tool-Calls (max. Tiefe 4)  
**Path:** `c:/Users/raffa/Local Sites/plantaphilia/app/public/wp-content/themes/Impreza-child/functions.php:625`

### Location 3d: NVIDIA API streamen
**Description:** cURL-Streaming mit SSE-Chunks, Reasoning-Content  
**Path:** `c:/Users/raffa/Local Sites/plantaphilia/app/public/wp-content/themes/Impreza-child/functions.php:532`

### Location 3e: Tool ausführen
**Description:** send_email_to_admin, get_order_history, get_product_info  
**Path:** `c:/Users/raffa/Local Sites/plantaphilia/app/public/wp-content/themes/Impreza-child/functions.php:552`

### Location 3f: Email an Admin senden
**Description:** Tool-Call: send_email_to_admin mit Rate-Limit 3/Tag  
**Path:** `c:/Users/raffa/Local Sites/plantaphilia/app/public/wp-content/themes/Impreza-child/functions.php:701`

### Location 3g: Chat UI rendern
**Description:** CSS für Messages, Reasoning-Blocks, Tool-Indicators  
**Path:** `c:/Users/raffa/Local Sites/plantaphilia/app/public/wp-content/themes/Impreza-child/page-kontakt.php:38`

---

## Trace 4: Admin Security & Session Management

**Description:** Admin-Bereich Sicherheit: Brute-Force-Schutz → Session-Timeout → Login-Benachrichtigung → Template-Redirect

```
WordPress Admin Security System
├── Login-Flow
│   ├── authenticate Filter Hook <-- 4a
│   │   ├── Transient-Check (pa_li_{ip_hash}) <-- functions.php:230
│   │   └── Fehlversuche >= 5? <-- 4b
│   │       ├── Ja → WP_Error zurückgeben <-- functions.php:233
│   │       └── Nein → User durchlassen <-- functions.php:238
│   ├── wp_login_failed Hook <-- functions.php:241
│   │   └── Transient Counter erhöhen <-- functions.php:244
│   └── wp_login Hook (Erfolg) <-- functions.php:247
│       └── Login-Email senden <-- 4d
├── Session-Management
│   ├── init Hook (jede Seite) <-- functions.php:311
│   │   ├── _pa_last_seen Meta laden <-- functions.php:317
│   │   ├── Timeout-Check (2h) <-- 4c
│   │   │   ├── Abgelaufen? → wp_logout() <-- functions.php:322
│   │   │   └── Aktiv? → Timestamp aktualisieren <-- functions.php:329
│   │   └── login_message Filter <-- functions.php:334
│   │       └── Timeout-Hinweis anzeigen <-- functions.php:336
│   └── auth_cookie_expiration Filter <-- functions.php:296
│       └── 2h für Admins setzen <-- functions.php:298
└── Template-Schutz
    ├── template_redirect Hook (Priority 1) <-- 4e
    │   ├── Protected Templates prüfen <-- functions.php:385
    │   ├── Nicht eingeloggt? <-- 4f
    │   │   └── wp_redirect(wp_login_url) <-- functions.php:389
    │   └── Kein Admin? <-- functions.php:393
    │       └── wp_redirect(home_url) <-- functions.php:394
    └── admin_init Hook <-- functions.php:366
        └── Security Headers (X-Frame-Options) <-- functions.php:368
```

### Location 4a: Login Brute-Force-Check
**Description:** Max. 5 Fehlversuche / 15 min per IP  
**Path:** `c:/Users/raffa/Local Sites/plantaphilia/app/public/wp-content/themes/Impreza-child/functions.php:224`

### Location 4b: Zu viele Login-Versuche blockieren
**Description:** WP_Error zurückgeben, Transient-basiert  
**Path:** `c:/Users/raffa/Local Sites/plantaphilia/app/public/wp-content/themes/Impreza-child/functions.php:232`

### Location 4c: Session-Timeout prüfen
**Description:** 2h Inaktivität → automatisch ausloggen  
**Path:** `c:/Users/raffa/Local Sites/plantaphilia/app/public/wp-content/themes/Impreza-child/functions.php:321`

### Location 4d: Login-Benachrichtigung senden
**Description:** Email an Admin mit IP, Browser, Zeit  
**Path:** `c:/Users/raffa/Local Sites/plantaphilia/app/public/wp-content/themes/Impreza-child/functions.php:352`

### Location 4e: Admin-Seiten schützen
**Description:** Redirect zu Login wenn nicht eingeloggt/kein Admin  
**Path:** `c:/Users/raffa/Local Sites/plantaphilia/app/public/wp-content/themes/Impreza-child/functions.php:376`

### Location 4f: Login-Redirect ausführen
**Description:** wp_login_url mit Permalink als Rücksprung  
**Path:** `c:/Users/raffa/Local Sites/plantaphilia/app/public/wp-content/themes/Impreza-child/functions.php:388`

---

## Trace 5: Product List & Stock Management (In-Progress System)

**Description:** Admin Produktliste: Stock-Berechnung aus offenen Bestellungen → AJAX-Response → Frontend-Anzeige mit Verfügbarkeit

```
Admin Produktliste & Stock-Management
├── AJAX Handler registriert <-- 5a
│   └── get_product_list_data() <-- functions.php:999
│       ├── calculate_in_progress_from_orders() <-- 5b
│       │   ├── wc_get_orders() offene Bestellungen <-- functions.php:837
│       │   ├── foreach order → foreach item <-- functions.php:839
│       │   │   └── Mengen akkumulieren <-- 5c
│       │   └── update_post_meta(_in_progress) <-- 5d
│       ├── Stock neu berechnen <-- 5e
│       ├── wc_get_products() alle Produkte <-- functions.php:1010
│       ├── foreach Produkt <-- functions.php:1017
│       │   ├── get_post_meta(_in_progress) <-- functions.php:1022
│       │   ├── Verfügbar = stock - in_progress
│       │   └── $product_data[] aufbauen <-- functions.php:1213
│       └── wp_send_json_success() <-- 5f
└── Frontend: Produkttabelle rendern <-- 5g
    └── JavaScript empfängt JSON
        └── Tabelle mit Bestand/Verfügbar füllen
```

### Location 5a: AJAX Handler registrieren
**Description:** Produktliste für Admin-Seite laden  
**Path:** `c:/Users/raffa/Local Sites/plantaphilia/app/public/wp-content/themes/Impreza-child/functions.php:819`

### Location 5b: In-Progress berechnen
**Description:** Summe aller Produkte in pending/on-hold/processing Orders  
**Path:** `c:/Users/raffa/Local Sites/plantaphilia/app/public/wp-content/themes/Impreza-child/functions.php:822`

### Location 5c: Mengen akkumulieren
**Description:** Pro Produkt-ID über alle offenen Bestellungen  
**Path:** `c:/Users/raffa/Local Sites/plantaphilia/app/public/wp-content/themes/Impreza-child/functions.php:854`

### Location 5d: In-Progress Meta speichern
**Description:** Wird bei Bestellstatus-Änderung aktualisiert  
**Path:** `c:/Users/raffa/Local Sites/plantaphilia/app/public/wp-content/themes/Impreza-child/functions.php:860`

### Location 5e: Stock vor AJAX-Response neu berechnen
**Description:** Aktuelle Werte für Produktliste  
**Path:** `c:/Users/raffa/Local Sites/plantaphilia/app/public/wp-content/themes/Impreza-child/functions.php:1008`

### Location 5f: Produktdaten als JSON zurückgeben
**Description:** Mit stock, in_progress, available, Angeboten, Meta  
**Path:** `c:/Users/raffa/Local Sites/plantaphilia/app/public/wp-content/themes/Impreza-child/functions.php:1272`

### Location 5g: Produkttabelle rendern
**Description:** Bestand, In Bearbeitung, Verfügbar, Preis, Angebot  
**Path:** `c:/Users/raffa/Local Sites/plantaphilia/app/public/wp-content/themes/Impreza-child/page-produkt-liste.php:165`

---

## Trace 6: Checkout Security & Order Creation

**Description:** WooCommerce Checkout: Honeypot-Check → Rate-Limiting → Order erstellen → Stock-Reduktion bei completed

```
WooCommerce Checkout Flow
├── Checkout Form Rendering
│   └── woocommerce_after_checkout_billing_form <-- functions.php:186
│       └── Honeypot-Feld einfügen <-- 6a
├── Checkout Validation <-- functions.php:201
│   ├── Honeypot prüfen <-- 6b
│   │   └── if (!empty($_POST['pa_hp'])) <-- functions.php:195
│   └── Rate-Limiting <-- 6c
│       └── Zu viele Versuche blockieren <-- 6d
│           └── wc_add_notice() Error <-- functions.php:207
├── Order Creation
│   └── woocommerce_checkout_order_created <-- functions.php:218
│       └── delete_transient() Reset <-- functions.php:220
└── Order Status Change Hook <-- 6e
    └── woocommerce_order_status_changed <-- functions.php:878
        ├── if (completed && old != completed) <-- functions.php:896
        │   └── Stock reduzieren <-- 6f
        │       └── wc_reduce_stock_levels() <-- functions.php:897
        └── calculate_in_progress_from_orders() <-- functions.php:901
            └── _in_progress Meta aktualisieren <-- functions.php:860
```

### Location 6a: Honeypot-Feld einfügen
**Description:** Unsichtbares Feld gegen Bots  
**Path:** `c:/Users/raffa/Local Sites/plantaphilia/app/public/wp-content/themes/Impreza-child/functions.php:186`

### Location 6b: Honeypot prüfen
**Description:** Bot-Erkennung: Feld muss leer sein  
**Path:** `c:/Users/raffa/Local Sites/plantaphilia/app/public/wp-content/themes/Impreza-child/functions.php:195`

### Location 6c: Checkout Rate-Limiting
**Description:** Max. 5 Versuche / 10 min pro IP  
**Path:** `c:/Users/raffa/Local Sites/plantaphilia/app/public/wp-content/themes/Impreza-child/functions.php:201`

### Location 6d: Zu viele Checkout-Versuche blockieren
**Description:** WooCommerce Notice anzeigen  
**Path:** `c:/Users/raffa/Local Sites/plantaphilia/app/public/wp-content/themes/Impreza-child/functions.php:206`

### Location 6e: Bestellstatus-Änderung Hook
**Description:** Stock-Reduktion nur bei completed  
**Path:** `c:/Users/raffa/Local Sites/plantaphilia/app/public/wp-content/themes/Impreza-child/functions.php:894`

### Location 6f: Stock reduzieren
**Description:** Nur wenn Status zu completed wechselt  
**Path:** `c:/Users/raffa/Local Sites/plantaphilia/app/public/wp-content/themes/Impreza-child/functions.php:897`

---

## Trace 7: Shop Filter System (Client-side)

**Description:** Shop-Seite: Alle Produkte vorladen → Taxonomie-Tree aufbauen → Client-seitige Filterung ohne Reload

```
Shop-Seite (archive-product.php) <-- archive-product.php:1
├── Server-seitige Vorbereitung
│   ├── wc_get_products() alle laden <-- 7a
│   ├── Taxonomie-Baum aufbauen
│   │   ├── foreach Produkt iterieren <-- 7b
│   │   ├── Gattung/Art-Hierarchie <-- 7c
│   │   └── Variable Tags gruppieren <-- 7d
│   └── JSON an Client übergeben <-- 7e
│       ├── window.paProducts
│       ├── window.paTaxTree <-- archive-product.php:115
│       └── window.paTagTree <-- archive-product.php:116
└── Client-seitige Filter-UI <-- archive-product.php:123
    ├── Suchfeld rendern <-- 7f
    ├── Filter-Sidebar (Gattung, Art, Tags) <-- archive-product.php:134
    └── JavaScript Filter-Logic
        ├── Fuzzy-Search über Namen
        ├── Taxonomie-Filter anwenden
        └── Produktgrid neu rendern
```

### Location 7a: Alle Produkte laden
**Description:** Komplette Produktliste für Client-seitige Filterung  
**Path:** `c:/Users/raffa/Local Sites/plantaphilia/app/public/wp-content/themes/Impreza-child/woocommerce/archive-product.php:9`

### Location 7b: Taxonomie-Tree aufbauen
**Description:** Gattung → Art Hierarchie, Tag-Gruppen  
**Path:** `c:/Users/raffa/Local Sites/plantaphilia/app/public/wp-content/themes/Impreza-child/woocommerce/archive-product.php:41`

### Location 7c: Gattung/Art-Baum füllen
**Description:** pa_tax_tree für Filter-Sidebar  
**Path:** `c:/Users/raffa/Local Sites/plantaphilia/app/public/wp-content/themes/Impreza-child/woocommerce/archive-product.php:48`

### Location 7d: Variable Tags gruppieren
**Description:** Blütenfarbe, Topfgröße etc. mit Prefix  
**Path:** `c:/Users/raffa/Local Sites/plantaphilia/app/public/wp-content/themes/Impreza-child/woocommerce/archive-product.php:60`

### Location 7e: Daten an JavaScript übergeben
**Description:** paProducts, paTaxTree, paTagTree für Filter-Logic  
**Path:** `c:/Users/raffa/Local Sites/plantaphilia/app/public/wp-content/themes/Impreza-child/woocommerce/archive-product.php:114`

### Location 7f: Suchfeld rendern
**Description:** Client-seitige Fuzzy-Search über Produktnamen  
**Path:** `c:/Users/raffa/Local Sites/plantaphilia/app/public/wp-content/themes/Impreza-child/woocommerce/archive-product.php:142`

---

## Trace 8: Migration Export Flow (Verschlüsselte ZIP)

**Description:** Daten-Migration: Produkte/Orders/Users/Settings/Media sammeln → AES-256 verschlüsseln → ZIP erstellen → Download

```
Migration Export Flow (Trace 8)
├── AJAX Handler <-- 8a
│   ├── Passwort-Validierung (min. 8 Zeichen) <-- functions.php:5695
│   ├── Memory & Timeout-Limits setzen <-- functions.php:5697
│   └── Daten sammeln
│       ├── pa_mig_export_media() <-- 8b
│       ├── pa_mig_export_products() <-- functions.php:5742
│       ├── pa_mig_export_orders() <-- functions.php:5769
│       ├── pa_mig_export_users() <-- functions.php:5791
│       └── pa_mig_export_settings() <-- functions.php:5805
├── ZIP-Archiv erstellen
│   ├── ZipArchive::open() <-- functions.php:5705
│   ├── JSON-Daten verschlüsseln
│   │   ├── pa_encrypt_payload() <-- 8e
│   │   │   ├── SHA-256 Key-Derivation <-- functions.php:5675
│   │   │   ├── Random IV generieren <-- functions.php:5676
│   │   │   └── AES-256-CBC Encryption <-- functions.php:5677
│   │   └── addFromString() <-- 8c
│   └── Media-Dateien verschlüsseln
│       ├── Für jedes Bild: file_get_contents() <-- functions.php:5721
│       ├── pa_encrypt_payload() <-- 8d
│       └── addFile() zu ZIP <-- functions.php:5726
└── Download ausliefern
    ├── Content-Type: application/zip Header <-- functions.php:5733
    ├── Content-Disposition: attachment Header <-- functions.php:5734
    └── readfile() <-- 8f
```

### Location 8a: Export AJAX Hook
**Description:** Admin-only Migration-Export  
**Path:** `c:/Users/raffa/Local Sites/plantaphilia/app/public/wp-content/themes/Impreza-child/functions.php:5689`

### Location 8b: Media-Metadaten sammeln
**Description:** Alle Attachments mit Pfaden, MIME-Types  
**Path:** `c:/Users/raffa/Local Sites/plantaphilia/app/public/wp-content/themes/Impreza-child/functions.php:5700`

### Location 8c: Produkte verschlüsseln & zu ZIP hinzufügen
**Description:** AES-256-CBC Verschlüsselung mit User-Passwort  
**Path:** `c:/Users/raffa/Local Sites/plantaphilia/app/public/wp-content/themes/Impreza-child/functions.php:5709`

### Location 8d: Bilder verschlüsseln
**Description:** Jedes Bild einzeln in Temp-Datei verschlüsseln  
**Path:** `c:/Users/raffa/Local Sites/plantaphilia/app/public/wp-content/themes/Impreza-child/functions.php:5724`

### Location 8e: Verschlüsselungs-Funktion
**Description:** SHA-256 Key-Derivation, Random IV, Base64-Encoding  
**Path:** `c:/Users/raffa/Local Sites/plantaphilia/app/public/wp-content/themes/Impreza-child/functions.php:5673`

### Location 8f: ZIP-Download starten
**Description:** Content-Disposition: attachment Header  
**Path:** `c:/Users/raffa/Local Sites/plantaphilia/app/public/wp-content/themes/Impreza-child/functions.php:5737`
