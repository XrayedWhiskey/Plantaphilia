# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Token-Effizienz (gilt für jede Session)
- Antworten immer so kurz wie möglich — kein Padding, keine Zusammenfassungen am Ende
- Keine Erklärungen was du tust, nur Ergebnisse
- Kein "Ich werde jetzt...", "Ich habe..."-Text
- Tools parallel aufrufen wenn unabhängig — niemals sequenziell wenn vermeidbar
- Dateien nur lesen wenn nötig, und nur den relevanten Abschnitt (offset/limit nutzen)
- Keine unnötigen Bestätigungen oder Nachfragen — direkt handeln wenn Absicht klar ist
- Bei Code: keine Kommentare ausser das WHY ist nicht offensichtlich

## Projekt
WordPress-Webshop für Pelargonien und Pflanzenzubehör. Lokale Entwicklung via **LocalWP** (kein Build-Step, kein npm, kein Composer nötig). Änderungen sofort wirksam.

## Entwicklung
- Site starten: LocalWP → Site "plantaphilia" → Start
- Admin: `http://plantaphilia.local/wp-admin`
- PHP-Fehler: `app/public/wp-content/debug.log` (wenn `WP_DEBUG_LOG` aktiv)
- DB-Credentials: `app/public/wp-config.php` (nicht im Repo)
- NVIDIA-API-Keys: `PLANTAPHILIA_NVAPI_KEYS` Konstante in wp-config.php

## Theme-Architektur

**Child-Theme:** `app/public/wp-content/themes/Impreza-child/`

### CSS-Ladereihenfolge (functions.php `pa_enqueue_design_assets`)
```
pa-design.css → ui-kit.css → product-page.css
```
CSS-Variablen (definiert in `pa-design.css` oder `farben.css`):
`--creme`, `--creme-dim`, `--plum`, `--plum-hot`, `--lavender`, `--bg-surface`, `--bg-deep`, `--bg-inky`, `--bg-raised`, `--border-thin`, `--border-hair`, `--serif-display`

### Template-Bypass-Pattern
Impreza's Page-Builder wird für bestimmte Seiten umgangen via `template_redirect` Hook (Priority 1) in `functions.php`:
- Einzelne Produktseiten → `single-product.php`
- Bestellbestätigung → `woocommerce/checkout/thankyou.php`
- Custom Page Templates werden geschützt durch Redirect auf Login wenn nicht eingeloggt/kein Admin

### Custom Page Templates (nur für Admins)
| Datei | URL-Pfad | Funktion |
|---|---|---|
| `page-produkt-liste.php` | `/produktliste` | Produktverwaltung mit Angeboten, Bulk-Sale, Rabattcodes, Social Deals |
| `page-bestellungen.php` | `/bestellungen` | Kanban-Board: Wartestellung → Bearbeitung → Versandt → Abgeschlossen |
| `page-produkt-hinzufuegen.php` | `/neues-produkt` | Produkt anlegen |
| `page-newsletter.php` | `/newsletter` | Newsletter-Verwaltung |
| `page-kontakt.php` | `/kontakt` | Kontaktseite |

## WooCommerce-Besonderheiten

### Bestellnummern-Schema
`PA-XXXXXXX` = `post_id + 10000`, linksbündig auf 7 Stellen. Rückrechnung: `(int)$numeric - 10000`.

### Stock-Logik (`_in_progress`)
Bestand wird **nicht** bei `pending`/`on-hold`/`processing` reduziert — nur bei `completed`. Stattdessen zählt `_in_progress` (post_meta) die Menge in offenen Bestellungen. Effektiv verfügbar = `stock - in_progress`. Hooks: `woocommerce_order_status_changed`, `woocommerce_new_order`.

### Custom Produkt-Metafelder
`_pa_gattung`, `_pa_art`, `_pa_kultivar`, `_pa_care_light`, `_pa_care_water`, `_pa_care_winter`, `_pa_care_temp_min`, `_pa_care_temp_max`

### Angebot-Metafelder
`_original_regular_price`, `_offer_history` (Array), `_offer_expired_since`, `_offer_expired_read`, `_part_of_sale`, `_sale_id`, `_sale_group_id`. Bulk-Sales werden als Option `_bulk_sales` (Array nach Sale-ID) gespeichert.

## AI-Chatbot (functions.php Sektion 7)

SSE-Streaming-Chatbot via WordPress AJAX (`pa_chatbot_stream`). Architektur:

1. **`pa_chatbot_stream_handler()`** — AJAX-Einstiegspunkt, Rate-Limiting (20/h, 60/Tag pro IP), Payload-Validierung
2. **`pa_chatbot_stream_loop()`** — Rekursive Schleife für Tool-Calls (max. Tiefe 4)
3. **`pa_chatbot_stream_api()`** — cURL-Streaming gegen NVIDIA NIM Endpoint
4. **`pa_chatbot_tools()`** — Tool-Definitionen: `send_email_to_admin`, `get_order_history`, `get_product_info`
5. **`pa_chatbot_get_api_key()`** — Key-Rotation über mehrere Keys (max. 40 req/min pro Key, transient-basiert)

API-Konstanten in wp-config.php: `PLANTAPHILIA_NVAPI_KEYS` (array), `PLANTAPHILIA_NVAPI_ENDPOINT`, `PLANTAPHILIA_NVAPI_MODEL`.

## Security-Maßnahmen (functions.php)
- WordPress-/WooCommerce-Version aus HTML entfernt, `ver=` Query-Params gestripped (außer eigene Assets)
- XML-RPC deaktiviert, User-Enumeration blockiert, REST `/users` gesperrt für Nicht-Admins
- Checkout: Honeypot-Feld, Rate-Limiting (5 Versuche/10 min per IP)
- Login: Brute-Force-Schutz (5 Fehlversuche/15 min), generische Fehlermeldung
- Admin: Session-Timeout 2h (Inaktivität), Login-Benachrichtigung per E-Mail, `X-Frame-Options: DENY`
- Datei-Editor im Admin deaktiviert (`DISALLOW_FILE_EDIT`)

## Branding & Assets
`app/public/wp-content/uploads/2022/01/` (einziger uploads-Ordner im Repo):
- Logo: `Logo-Plantaphilia-1.svg`
- Favicon: `favicon_Zeichenflaeche-1.png`
- Header-Banner: `Banner-Plantaphilia.jpg`
- Footer-Banner: `Footer-Banner-Plantaphilia.jpg`
- Schriftart: Montserrat (Regular 400, Medium 500, Bold 700)

Zusätzlich geladen: Playfair Display via Google Fonts (für Serif-Display-Elemente).

## Plugins (relevant)
- **WooCommerce** — Shop-Core
- **WooCommerce Waitlist** — Wartelisten für ausverkaufte Produkte
- **WooCommerce Advanced Bulk Edit** — Bulk-Produkt-Bearbeitung im WP-Admin
- **WPBakery Page Builder** (`js_composer`) — Impreza-Abhängigkeit, wird für eigene Templates bypassed
- **Code Snippets** — Kleine PHP-Snippets ohne Theme-Eingriff
- **TablePress** — Tabellen-Shortcodes
- **Contact Form 7** — Kontaktformulare
- **Export Media Library** — Media-Export
