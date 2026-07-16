# Bulk Import & Migration — TODO

---

## 1. Excel-Vorlage: Alle Produktfelder

**Aktueller Stand:** Nur 20 Spalten (Basisdaten). Fehlend: Pflanzendaten, Pflegeinfos, Tags, Beschreibungen, Versandklasse, Varianten.

### Neue Spalten-Reihenfolge (Zeile 1 = Feldname, Zeile 2 = Beschreibung/Regeln, ab Zeile 3 = Daten)

| Spalte | Feldname | Zeile-2-Beschreibung |
|--------|----------|----------------------|
| A | Name | Produktname, z.B. "Pelargonium 'Voodoo'" |
| B | SKU | Artikelnummer, z.B. PA-001 |
| C | Gattung | z.B. Pelargonium — muss existieren oder wird neu angelegt |
| D | Art | z.B. zonale — optional |
| E | Kultivar | Sortenname ohne Anführungszeichen, z.B. Voodoo |
| F | Preis | Dezimalzahl mit Punkt, z.B. 12.90 |
| G | Bestand | Ganzzahl, z.B. 5 |
| H | Produkttyp | Dropdown: Pflanze oder Substrat |
| I | Einheit | Dropdown: Stueck oder Liter |
| J | Liter | Nur bei Einheit=Liter, Dezimalzahl |
| K | Steuerklasse | standard oder Steuerklassen-Slug |
| L | Differenzbesteuerung | 0 = Nein, 1 = Ja — nur 0 oder 1 eingeben |
| M | Gewicht_kg | Dezimalzahl |
| N | Laenge_cm | Dezimalzahl |
| O | Breite_cm | Dezimalzahl |
| P | Hoehe_cm | Dezimalzahl |
| Q | Versandlaenge_cm | Dezimalzahl |
| R | Versandbreite_cm | Dezimalzahl |
| S | Versandhoehe_cm | Dezimalzahl |
| T | Versandklasse | Name der Versandklasse, leer = keine |
| U | Lieferzeit_Tage | Ganzzahl |
| V | Schwellwert_Lagerbestand | Ganzzahl, leer = Standard 5 |
| W | Nie_geringer_Lagerbestand | 0 = Nein, 1 = Ja — nur 0 oder 1 eingeben |
| X | Kurzbeschreibung | Max. 160 Zeichen. Kein HTML |
| Y | Beschre ibung | Fließtext. Zeilenumbruch = @@ — Excel-Fettformatierung wird als **text** interpretiert und in HTML umgewandelt |
| Z | Tags | Format: `kategorie:wert,kategorie2:wert2,freitag` — Trennzeichen: `,` — Kategorie und Wert getrennt durch `:` — kein `:` = regulärer Tag ohne Kategorie |
| AA | Pflegelicht | z.B. Vollsonne, Halbschatten |
| AB | Pflegewasser | z.B. mäßig, regelmäßig |
| AC | Pflegewinter | z.B. frostfrei, 5–10 °C |
| AD | PflegeTempMin | Ganzzahl (°C) |
| AE | PflegeTempMax | Ganzzahl (°C) |

### Template-Dateiformat
- Download als `.xlsx` (nicht mehr nur CSV) — mit vorformatierter Zeile 2 in kursiv/grau
- Spaltenbreite automatisch anpassen (AutoFit) beim Generieren serverseitig via PhpSpreadsheet
- Zeile 1 (Header): fett, Hintergrundfarbe
- Zeile 2 (Beschreibung): kursiv, grau, kleinere Schrift
- Zeile 3+: Daten (erste Zeile als Beispiel vorausfüllen)
- **0/1-Felder** (L, W): Excel-Datenvalidierung `Ganzzahl zwischen 0 und 1` (Fehlermeldung: „Nur 0 oder 1 erlaubt")
- **Dropdown-Felder** (H, I): Excel-Datenvalidierung `Liste` mit den erlaubten Werten

### Parser-Änderungen
- `parseCSV` / `pa_parse_excel` AJAX-Handler: Zeile 1 = Header, Zeile 2 überspringen (Beschreibungszeile), Zeile 3+ = Datensätze
- Tags parsen: `kategorie:wert` → variable Tag mit Kategorie; nur `wert` → fester Tag
- Beschreibung parsen: `@@` → `\n` (Zeilenumbrüche), Excel-Bold-Runs → `<strong>...</strong>`

---

## 2. Bild-Zuweisung nach Upload (Popup-Schritt 2)

Nach dem Hochladen und Parsen der Excel-Datei erscheint **statt sofortigem Import** ein Modal mit einer Tabelle.

### Tabellen-Spalten

| Spalte | Inhalt |
|--------|--------|
| 1 | Gattung (aus Spalte C) |
| 2 | Art (aus Spalte D) |
| 3 | Kultivar (aus Spalte E) |
| 4 | SKU (aus Spalte B) |
| 5 | Hauptbild |
| 6 | Zweitbilder |

### Spalte 3 — Hauptbild
- Startzustand: Button „Bild hinzufügen"
- Klick → öffnet bestehendes Bild-Upload-Popup (mit Wasserzeichen-Funktion)
- Nach Auswahl/Upload: Vorschau-Thumbnail (ca. 60×60 px) wird angezeigt, Button verschwindet
- Hover über Thumbnail: rotes `×` erscheint rechts oben im Thumbnail
- Klick `×`: Bild entfernt, Button erscheint wieder

### Spalte 4 — Zweitbilder
- Startzustand: Button „+ Bild hinzufügen" (bleibt immer sichtbar)
- Klick → öffnet Bild-Upload-Popup
- Nach Auswahl: Thumbnail erscheint unterhalb des Buttons (in einer Reihe, mehrere möglich)
- Hover über Thumbnail: rotes `×` rechts oben
- Klick `×`: einzelnes Bild entfernt, Button bleibt

### Datenstruktur im Frontend (pro Zeile)
```js
{
  rowIndex: 0,
  mainImageId: null,       // WP attachment ID oder null
  mainImageUrl: null,      // für Vorschau
  extraImageIds: [],       // Array von WP attachment IDs
  extraImageUrls: []       // Array für Vorschau
}
```

### Weiter-Button
- „Import starten" erst klickbar wenn alle Zeilen ein Hauptbild haben (oder optionale Override-Checkbox „ohne Hauptbild importieren")

---

## 3. Migrations-Export / -Import

Ziel: Vor einem Update alle variablen Daten exportieren, nach dem Update wieder importieren.

### Was exportiert wird (mehrere Dateien wegen Upload-Limits)

| Datei | Inhalt |
|-------|--------|
| `migration_products.json` | Alle Produkte: Post-Daten, Metafelder, Stock, Tags, Kategorien, Preise, Sale-Daten |
| `migration_orders.json` | Alle Bestellungen: Post-Daten, Order Items, Metafelder, Kundendaten |
| `migration_users.json` | Alle Kund:innen-Konten: user_data + usermeta (außer Passwort-Hashes → werden übernommen) |
| `migration_settings.json` | WooCommerce-Optionen, Theme-Optionen (nur pa_* Einträge), Newsletter-Liste |
| `migration_media_ids.json` | Mapping alte attachment_id → Dateiname (für Bild-Relinking nach Import) |

### Export-UI (in page-produkt-liste.php oder eigene Admin-Seite)
- Button „Migration exportieren" → Passwort-Eingabe-Dialog
- Passwort wird als SHA-256 Hash gespeichert / verglichen (kein Versand, nur lokal)
- Nach Bestätigung: ZIP mit allen JSON-Dateien wird heruntergeladen
- Dateiname: `plantaphilia_migration_YYYY-MM-DD.zip`

### Import-UI
- Upload-Bereich für ZIP-Datei
- Passwort-Eingabe
- Trocken-Lauf-Option: „Nur prüfen, nichts importieren" — zeigt Diff (was fehlt, was kollidiert)
- Eigentlicher Import: Schrittweise mit Fortschrittsanzeige
  1. Produkte anlegen/aktualisieren (anhand SKU als eindeutige ID)
  2. Bestellungen anlegen (anhand PA-Bestellnummer)
  3. User anlegen/aktualisieren (anhand E-Mail)
  4. Settings wiederherstellen
  5. Medien-IDs relinken (Bild-Zuweisungen per Dateinamen-Matching)

### Konfliktstrategie
- Produkt-SKU existiert bereits → **überschreiben** (Update)
- Bestellung existiert bereits → **überspringen** (nie doppelt anlegen)
- User-E-Mail existiert bereits → **überschreiben** (Metadaten aktualisieren, Passwort-Hash nicht anfassen)
- Settings → **überschreiben**

### Sicherheit
- Export-Passwort: mindestens 8 Zeichen, wird **nicht** in der Datenbank gespeichert — wird als AES-256-Schlüssel zum Verschlüsseln des ZIPs verwendet (z.B. via `ZipArchive` + Passwort oder PHP-Verschlüsselung der JSON-Dateien vor dem Zippen)
- Import nur für eingeloggte Admins (nonce + `current_user_can('manage_options')`)
- Hochgeladene Migration-ZIPs werden sofort nach dem Import gelöscht

---

## Reihenfolge der Umsetzung

1. [ ] PhpSpreadsheet einbinden (via `require_once` im Theme, kein Composer — manuell in `/vendor/` ablegen oder per Code Snippets Plugin laden)
2. [ ] Excel-Vorlage neu generieren (alle Spalten, Zeile 2 als Beschreibungszeile, AutoFit)
3. [ ] `pa_parse_excel` AJAX-Handler anpassen (Zeile 2 skipppen, neue Felder mappen)
4. [ ] Tag-Parser implementieren (`kategorie:wert` → variable Tags)
5. [ ] Beschreibungs-Parser (@@-Zeilenumbrüche, Bold-Erkennung)
6. [ ] Bild-Zuweisung Modal (Tabelle nach Excel-Upload, Hauptbild + Zweitbilder pro Zeile)
7. [ ] `startBulkImport()` anpassen: Bilder-IDs mit übergeben
8. [ ] Server-seitigen Import-Handler erweitern: alle neuen Felder schreiben
9. [ ] Migration: Export-Funktion (PHP AJAX, JSON-Generierung, ZIP-Download)
10. [ ] Migration: Import-Funktion (Upload, Passwort, Trocken-Lauf, schrittweiser Import)
11. [ ] Migration: UI in page-produkt-liste.php (eigener Abschnitt oder Unterseite)
