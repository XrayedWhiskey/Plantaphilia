# Plantaphilia Desktop App - TODO

## Projektübersicht
Desktop-App (Windows 10/11) für Offline-Verwaltung von Plantaphilia Produkten mit Sync zur WordPress/WooCommerce Webseite.

---

## Technologie-Stack

- **Framework:** Electron + React (Vite)
- **Styling:** TailwindCSS mit Plantaphilia Farbschema
- **Lokale Datenbank:** SQLite (better-sqlite3)
- **Authentifizierung:** WordPress REST API + Application Passwords
- **Rich Text Editor:** Quill.js (exakt wie auf Webseite)
- **Image Processing:** 
  - Cropper.js (Crop zu Quadrat)
  - Sharp (Wasserzeichen hinzufügen)
- **Build:** electron-builder (Windows EXE)
- **Verteilung:** GitHub Releases (ZIP Download)

---

## Projektstruktur

```
plantaphilia-app/
├── src/
│   ├── main/           # Electron Main Process
│   ├── renderer/       # React Frontend
│   └── shared/         # Shared Types & Utils
├── resources/          # Icons, Assets
├── package.json
└── electron-builder.yml
```

---

## Features

### 1. Startup-Dialog
- **Beim ersten Start:** Popup fragt nach:
  - Projektordner-Pfad (wo Daten lokal gespeichert werden)
  - Auto-Save Intervall (in Minuten)
- **Bei jedem Start:** Prüft ob Projektordner existiert, öffnet App direkt

### 2. Pull Button (Sync von Online zu Lokal)
- Lädt alle Produkte von plantaphilia.eu via WordPress REST API
- Lädt alle Produktbilder herunter
- Speichert Snapshot von online-Daten (timestamp + hash für jedes Produkt)
- Ordnet Bilder lokal:
  ```
  /projekt-ordner/
    /produkte/
      /pelargonium-zonal-thai-constellation/
        image-1.jpg
        image-2.jpg
      /monstera-deliciosa/
        image-1.jpg
  ```
- Bild-Benennung: `{slug}-{nummer}.jpg` (Gattung/Art/Kultivar können wegfallen, nur slug nutzen)
- Speichert alle Daten in SQLite Datenbank (`/projekt-ordner/data/plantaphilia.db`)

### 3. Produkt-Verwaltung (Lokal)
- **Alle Felder wie auf der Webseite:**
  - Grundinformationen:
    - Gattung (Dropdown mit Neu hinzufügen)
    - Art (Dropdown mit Neu hinzufügen)
    - Kultivar (Text)
    - Artikelnummer/SKU (Text)
    - Preis (€)
    - Produkttyp Toggle: Pflanze / Substrat
    - Einheit Toggle: Stück / Liter
    - Bei Liter: Inhalt (Liter), Grundpreis (auto)
  - Steuer & Versand:
    - Steuerklasse (Dropdown mit Neu hinzufügen)
    - Differenzbesteuerung (Checkbox)
    - Versandklasse (Dropdown mit Neu hinzufügen)
    - Lieferzeit (Tage)
    - Nicht retournierbar (disabled)
  - Lagerverwaltung:
    - Lagerbestand (Stück)
    - Individueller Schwellwert für geringen Lagerbestand
    - Nie als geringer Lagerbestand markieren
  - Maße & Gewicht:
    - Gewicht (kg)
    - Länge, Breite, Höhe (cm)
  - Bilder:
    - Upload mit Crop zu Quadrat
    - Wasserzeichen hinzufügen (wie auf Webseite)
    - Mehrere Bilder pro Produkt
    - Featured Image setzen
  - Beschreibung:
    - Rich Text Editor (Quill.js, exakt wie auf Webseite)
  - Tags:
    - Kategorien
    - Varianten
    - Pflegehinweise
  - Varianten (falls vorhanden)

### 4. Post/Sync Button (Sync von Lokal zu Online)
**Ablauf:**
1. Pull aktuellen online-Stand
2. Vergleich: Lokal vs. Online vs. Letzter Snapshot
3. Konflikt-Dialog für jedes Produkt mit Unterschieden

**Konflikt-Dialog Logik:**

Für jedes Feld mit Unterschieden:

**Szenario A: Lokal unverändert, Online verändert**
```
Lokal: 10€ → Online: 20€
[ ] Online-Version nutzen
```
- Checkbox default unchecked
- Wenn checked → Online-Version übernehmen
- Wenn unchecked → Lokale Version behalten

**Szenario B: Nur lokal verändert**
```
Lokal: 20€ → Online: 10€
[x] Lokale Version nutzen
```
- Checkbox auto-checked
- Wenn unchecked → Online-Version übernehmen

**Szenario C: Alle 3 unterschiedlich (Lokal, Online, Snapshot)**
```
Snapshot: 10€ → Lokal: 20€ → Online: 15€
( ) Snapshot nutzen (Rollback)
( ) Lokale Version nutzen
( ) Online-Version nutzen
```
- Radio-Buttons (nur einer wählbar)
- Wenn keine Auswahl → Rollback zu Snapshot
- Wenn eine Auswahl gewählt → andere auto-deselect

**Nach Bestätigung:**
- Upload aller gewählten Änderungen zur WordPress REST API
- Update lokaler Datenbank
- Update Snapshot

### 5. Offline-Funktionalität
- Alle Produkte lokal in SQLite
- Alle Bilder lokal auf Dateisystem
- Volle CRUD-Operationen offline möglich
- Auto-Save alle X Minuten (konfigurierbar)

### 6. Design
- Plantaphilia Farbschema (aus farben.css):
  - `--bg-deep: #25302B` (Body, große Sektionen)
  - `--bg-surface: #2F3B35` (Karten, Modals, Inputs)
  - `--bg-raised: #3A4840` (Hover)
  - `--bg-inky: #1A231F` (Header, Navigation)
  - `--plum: #7D55B0` (tiefer Akzent)
  - `--plum-hot: #9B6FD0` (Haupt-Akzent)
  - `--text: #EAE4D6` (Schrift primär)
  - `--text-meta: #9CA59E` (Schrift sekundär)
- Responsive Design
- Kleine, kompakte UI

---

## Implementierungs-Schritte

### Phase 1: Projekt-Setup
- [ ] Electron + React + Vite Projekt erstellen
- [ ] TailwindCSS installieren und konfigurieren
- [ ] Plantaphilia Farbschema in Tailwind config einbinden
- [ ] SQLite (better-sqlite3) installieren
- [ ] Quill.js installieren
- [ ] Cropper.js installieren
- [ ] Sharp installieren
- [ ] electron-builder konfigurieren
- [ ] Projektstruktur aufbauen

### Phase 2: Datenbank-Design
- [ ] SQLite Schema entwerfen:
  - products Tabelle (alle WooCommerce Felder)
  - product_images Tabelle
  - tags Tabelle
  - product_tags Tabelle (many-to-many)
  - snapshots Tabelle (für Sync-Vergleich)
  - settings Tabelle (Projektordner, Auto-Save Intervall)
- [ ] Database Helper Functions erstellen
- [ ] Migrations System implementieren

### Phase 3: Electron Main Process
- [ ] Main Window erstellen
- [ ] Startup-Dialog implementieren
- [ ] File System Access (Projektordner)
- [ ] IPC Handler für:
  - Datenbank-Operationen
  - File System Operationen
  - Image Processing (Sharp)
  - Auto-Save Timer

### Phase 4: WordPress REST API Integration
- [ ] API Client erstellen
- [ ] Authentifizierung mit Application Passwords
- [ ] Pull Products Endpoint implementieren
- [ ] Pull Images Endpoint implementieren
- [ ] Post Products Endpoint implementieren
- [ ] Post Images Endpoint implementieren
- [ ] Error Handling & Retry Logic

### Phase 5: React Frontend - Layout
- [ ] Main Layout (Pull Button, Produktliste, Sync Status)
- [ ] Produktliste mit:
  - Spalten-Auswahl Dropdown (Checkboxen für angezeigte Infos)
  - Suchleiste
  - Delete Button pro Produkt (mit "Bist du sicher" Popup)
  - Edit Button pro Produkt (öffnet Edit Popup)
  - **Vorschau Button** (öffnet Preview wie auf Webseite)
  - Produkt erstellen Button
  - **Reference:** `page-produkt-liste.php` Zeilen 164-185 (Produkt Tabelle)
- [ ] Edit Popup (alle Infos bearbeitbar)
  - **Reference:** `page-produkt-liste.php` Zeilen 397-491 (Edit Modal Multi-Panel)
- [ ] Produkt erstellen Popup (exakt wie auf der Webseite)
  - **Reference:** `page-produkt-hinzufuegen.php` Zeilen 530-966 (Ganzes Formular)
- [ ] Produkt Preview Component (wie Single Product Seite)
  - **Reference:** `single-product.php` (Template)
  - **Reference:** `content-single-product.php` (WooCommerce Template)
- [ ] Pull/Post Buttons
- [ ] Sync Status Indikator

### Phase 6: Produkt-Formular
- [ ] Grundinformationen Sektion
  - **Reference:** `page-produkt-hinzufuegen.php` Zeilen 537-621
  - Gattung/Art Dropdowns mit "Neu hinzufügen" (Zeilen 540-568)
  - Kultivar, SKU, Preis (Zeilen 570-584)
  - Produkttyp Toggle: Pflanze/Substrat (Zeilen 587-595)
  - Einheit Toggle: Stück/Liter (Zeilen 598-606)
  - Liter-Felder mit Grundpreis (Zeilen 609-620)
- [ ] Steuer & Versand Sektion
  - **Reference:** `page-produkt-hinzufuegen.php` Zeilen 624-722
  - Steuerklasse mit Neu hinzufügen (Zeilen 628-647)
  - Differenzbesteuerung (Zeilen 650-656)
  - Versandklasse mit Neu hinzufügen (Zeilen 659-708)
  - Lieferzeit (Zeilen 711-714)
  - Nicht retournierbar (disabled) (Zeilen 717-721)
- [ ] Lagerverwaltung Sektion
  - **Reference:** `page-produkt-hinzufuegen.php` Zeilen 725-752
  - Lagerbestand (Zeilen 731-734)
  - Individueller Schwellwert (Zeilen 737-747)
  - Nie als geringer Lagerbestand markieren (Zeilen 748-751)
- [ ] Maße & Gewicht Sektion
  - **Reference:** `page-produkt-hinzufuegen.php` Zeilen 755-804
  - Gewicht (Zeilen 758-761)
  - Produktmaße Länge/Breite/Höhe (Zeilen 765-778)
  - Versandmaße mit Toggle (Zeilen 781-803)
- [ ] Bilder Sektion
  - **Reference:** `page-produkt-hinzufuegen.php` Zeilen 807-834
- [ ] Kurzbeschreibung
  - **Reference:** `page-produkt-hinzufuegen.php` Zeilen 837-847
- [ ] Produktbeschreibung (Rich Text)
  - **Reference:** `page-produkt-hinzufuegen.php` Zeilen 850-870
- [ ] Pflege-Infos
  - **Reference:** `page-produkt-hinzufuegen.php` Zeilen 895-949
- [ ] Varianten
  - **Reference:** `page-produkt-hinzufuegen.php` Zeilen 952-958
- [ ] Form Validation

### Phase 7: Rich Text Editor
- [ ] Quill.js integrieren
- [ ] Toolbar konfigurieren (wie auf Webseite)
  - **Reference:** `page-produkt-hinzufuegen.php` Zeilen 852-868 (Toolbar Buttons)
- [ ] Styling an Plantaphilia Design anpassen
- [ ] Save/Load zu/from SQLite

### Phase 8: Image Upload & Processing
- [ ] Image Upload Component
  - **Reference:** `page-produkt-hinzufuegen.php` Zeilen 807-834 (Titelbild & Galerie)
- [ ] Cropper.js Integration (Quadrat Crop)
  - **Reference:** `page-produkt-hinzufuegen.php` Zeilen 299-372 (CWM Overlay CSS & HTML)
  - **Reference:** `page-produkt-hinzufuegen.php` Zeilen 2932-2980 (CWM Modal HTML)
  - **Reference:** `page-produkt-hinzufuegen.php` Zeilen 1900-1946 (cwmOpen, cwmCancel JS)
- [ ] Wasserzeichen mit Sharp (wie auf Webseite)
  - **Reference:** `page-produkt-hinzufuegen.php` Zeilen 2932-2980 (CWM Dialog Controls)
- [ ] Image Preview Grid
  - **Reference:** `page-produkt-hinzufuegen.php` Zeilen 191-226 (Image Preview CSS)
- [ ] Featured Image setzen
  - **Reference:** `page-produkt-hinzufuegen.php` Zeilen 814-824 (Featured Image Area)
- [ ] Lokale Speicherung in Ordnerstruktur
- [ ] Upload zu WordPress

### Phase 9: Tags System
- [ ] Tag Pool Component (wie auf Webseite)
  - **Reference:** `page-produkt-hinzufuegen.php` Zeilen 872-892 (Tags & Kategorien Sektion)
  - **Reference:** `page-produkt-hinzufuegen.php` Zeilen 882-884 (Tag Pool Wrap)
  - **Reference:** `page-produkt-hinzufuegen.php` Zeilen 2592-2596 (renderTagPool JS)
- [ ] Kategorien, Varianten, Pflegehinweise
  - **Reference:** `page-produkt-hinzufuegen.php` Zeilen 407-438 (Tag Pool CSS)
  - **Reference:** `page-produkt-hinzufuegen.php` Zeilen 895-949 (Pflege-Infos)
- [ ] Tag erstellen Modal
  - **Reference:** `page-produkt-hinzufuegen.php` Zeilen 1076-1101 (Tag Create Popup)
- [ ] Tag zu Produkt zuweisen
  - **Reference:** `page-produkt-hinzufuegen.php` Zeilen 876-879 (Selected Tags Area)

### Phase 10: Sync Logic
- [ ] Snapshot System implementieren
- [ ] Vergleichslogik (Lokal vs. Online vs. Snapshot)
- [ ] Konflikt-Dialog Component
- [ ] Radio-Button Logik (Szenario C)
- [ ] Checkbox Logik (Szenario A & B)
- [ ] Rollback zu Snapshot
- [ ] Upload gewählter Änderungen

### Phase 11: Auto-Save
- [ ] Auto-Save Timer implementieren
- [ ] Intervall aus Settings lesen
- [ ] Silent Save zu SQLite
- [ ] Status Indikator

### Phase 12: Design implementieren
- [ ] Plantaphilia Farbschema in Tailwind config einbinden
  - **Reference:** `farben.css` Zeilen 1-105 (Alle Farb-Tokens)
  - **Reference:** `pa-design.css` (Plantaphilia Design System CSS)
- [ ] Responsive Layout
- [ ] Komponenten-Styling

### Phase 13: Testing
- [ ] Unit Tests für Database Layer
- [ ] Unit Tests für API Client
- [ ] Integration Tests für Sync Logic
- [ ] Manual Testing auf Windows 10/11

### Phase 14: Build & Release
- [ ] electron-builder konfigurieren
- [ ] Windows EXE build
- [ ] GitHub Actions für automatische Builds
- [ ] Release auf GitHub (ZIP Download)

---

## Offene Fragen

1. **Wasserzeichen Design:** Welches Wasserzeichen soll verwendet werden? (Text, Logo, Position, Transparenz) - Genau wie auf der Webseite übernehmen
2. **WordPress API Endpoint:** Ist die WordPress REST API bereits aktiviert? Application Passwords aktiviert?
3. **CORS:** Muss CORS auf der WordPress Seite konfiguriert werden für lokale App?
4. **Performance:** Wie viele Produkte werden erwartet? (für Performance-Optimierung)
5. **Backup:** Soll es ein Backup-Feature geben (Export lokaler Daten)?

---

## Notizen

- App ist für einen einzelnen Nutzer konzipiert
- Offline-first Ansatz
- Sync bei Bedarf (Pull/Post Buttons)
- Bild-Ordnerstruktur: `/projekt-ordner/produkte/{slug}/`
- Bild-Benennung: `{slug}-{nummer}.jpg`
- Auto-Save Intervall konfigurierbar
- Windows 10/11 kompatibel
