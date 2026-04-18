# Plantaphilia – WordPress Pflanzenprojekt

## Ziel
Der Nutzer möchte, dass du folgendes beachtest:

> **Erstelle ein Design-Mockup für die Plantaphilia-Website.**
> Die Website ist ein WordPress-Webshop für Pflanzen und Pflanzenzubehör.
> Das Mockup soll das bestehende Branding aufgreifen und ein modernes, ansprechendes Design vorschlagen –
> mit Fokus auf Übersichtlichkeit, Pflanzen-Ästhetik und einer guten User Experience im Shop.

## Branding & Assets
- **Logo:** `app/public/wp-content/uploads/2022/01/Logo-Plantaphilia-1.svg`
- **Favicon:** `app/public/wp-content/uploads/2022/01/favicon_Zeichenflaeche-1.png`
- **Header-Banner:** `app/public/wp-content/uploads/2022/01/Banner-Plantaphilia.jpg`
- **Footer-Banner:** `app/public/wp-content/uploads/2022/01/Footer-Banner-Plantaphilia.jpg`
- **Schriftart:** Montserrat (Regular 400, Medium 500, Bold 700) – Dateien im selben Ordner

## Theme
- Basis: **Impreza** (UpSolution) mit Child-Theme unter `app/public/wp-content/themes/Impreza-child/`
- Theme-Einstellungen (Farben, Layout, Widgets) sind in der Datenbank gespeichert: `app/sql/local.sql`
- Eigene PHP-Templates: `page-produkt-liste.php`, `page-bestellungen.php`

## Projektstruktur
- `app/public/` – WordPress-Installation
- `app/public/wp-content/themes/Impreza-child/` – Eigenes Child-Theme
- `app/public/wp-content/plugins/` – Installierte Plugins
- `app/sql/local.sql` – Datenbank-Dump (Inhalte, Produkte, Einstellungen)
- `app/public/wp-config.php` – **Nicht im Repo** (enthält DB-Credentials)

## Wichtige Hinweise
- `wp-content/uploads/` ist ausgeschlossen (1,5 GB) – nur `uploads/2022/01/` ist im Repo (Branding-Assets)
- Lokale Entwicklung läuft über LocalWP
