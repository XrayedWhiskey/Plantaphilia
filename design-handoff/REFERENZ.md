# Plantaphilia — Design- & Daten-Referenz für den Redesign-Handoff

Diese Datei richtet sich an den Design-Überarbeitungs-Prozess für die **Produktseite** und die **Shop-Seite**
(`app/public/wp-content/themes/Impreza-child/woocommerce/content-single-product.php` und
`woocommerce/archive-product.php`). Sie beschreibt: (A) das bestehende Design-System, (B) alle Daten, die die
Desktop-App (`desktop-app/`) erfasst und die auf diesen Seiten zur Verfügung stehen, (C) wo/wie diese Daten
heute platziert sind bzw. platziert werden sollen.

---

## A. Design-System — "Dark Botanical Gothic"

Einzige Farbquelle: `app/public/wp-content/themes/Impreza-child/farben.css`. Keine andere Datei definiert Farben
direkt — alles liest aus diesen Variablen.

### Primitive (6 Branding-Farben)

| Token | Hex | Verwendung |
|---|---|---|
| `--farbe-bg` / `--bg-deep` | `#25302B` | Body, große Sektionen, Footer |
| `--farbe-nav` / `--bg-inky` | `#1A231F` | Header, dunkelste Ebene |
| `--farbe-karte` / `--bg-surface` | `#2F3B35` | Produktkarten, Modals, Inputs |
| `--farbe-akzent` / `--plum-hot` | `#9B6FD0` | Buttons, Preise, Links — **sparsam einsetzen** |
| `--farbe-text` / `--creme` | `#EAE4D6` | Alle Überschriften & Lesetexte |
| `--farbe-text-meta` / `--creme-muted` | `#9CA59E` | Meta, Labels, Placeholder |

### Abgeleitet

- Hintergrund erhöht: `--bg-raised: #3A4840` (Hover-Feedback)
- Akzent-Skala: `--plum: #7D55B0` (tiefer/Hover), `--lavender: #C4B0E5` (heller Tint, kursiver Text)
- Body-Copy sekundär: `--creme-dim: #CABFAA`
- Ränder: `--border-thin: rgba(155,111,208,0.30)` (akzentbasiert), `--border-hair: rgba(234,228,214,0.08)`
  (kaum sichtbar, Standard-Trenner)
- Schatten: `--shadow-cabinet` (Produktkarten), `--shadow-specimen` (große Bilder)

### Typografie

- **Überschriften / Preise / botanische Namen**: `--serif-display: "Playfair Display"` — Gewicht 500 normal,
  Kultivar-/lateinische Namen oft *kursiv* in `--lavender`.
- **Fließtext / UI / Labels / Buttons**: `--sans-body: "Montserrat"` — Buttons/Badges typischerweise
  10–11px, 700 Gewicht, `letter-spacing: 0.16–0.28em`, GROSSBUCHSTABEN.
- Referenzgrößen: H1 Hero 64px/1.05, Produktkartenname 16px, Produktkarte-Preis 20px, Meta/Eyebrow 11px mit
  weitem Letter-Spacing.
- Barrierefreiheit: dritte Schriftart `OpenDyslexic2` als `--font-flow`-Option bereits vorbereitet.

### Bewegung

`--ease-botanical: cubic-bezier(.22,.61,.36,1)`, Timing `--t-fast: 120ms`, `--t-base: 220ms`, `--t-slow: 440ms`.
Karten heben sich beim Hover leicht an (`translateY(-3px)`), Farbübergänge statt harter Schnitte.

### Bestehende Komponenten-Muster (zur Orientierung, nicht 1:1 zu kopieren)

- **Produktkarte**: 1:1-Bild, Badge oben links (Sale/Neu/Ausverkauft), Favoriten-Button oben rechts, Name
  (Serif) + kursiver botanischer Name (Lavender) + Preis unten rechts.
- **Buttons**: Outline (`--plum-hot`-Rand, transparent) und Filled (`--plum-hot`-Fläche), beide Uppercase/
  Sans/kleine Schrift/breites Letter-Spacing.
- **Kategorie-Chips**: Pill-Form, Outline default, gefüllt wenn aktiv.

---

## B. Datenkatalog — was die App erfasst

Quelle: `desktop-app/src/main/database.js` (SQLite-Schema). Die App ist für alle Produkt-/Kategorie-Daten
führend, pusht per WP-REST-API an diese Website.

### Produkt — gemeinsame Felder (alle Typen)

Name/Slug, Preis (Normal-/Aktionspreis), SKU, Lagerbestand (+ Schwellwert für „Niedriger Bestand", Anzeige
exakt vs. vage), Steuerklasse, Versandklasse, Lieferzeit, Kurzbeschreibung, Langbeschreibung, Bildergalerie,
SEO-Titel/-Beschreibung/-Fokus-Keyword, Status (Entwurf/veröffentlicht), Tags, Kategorien.

**Einheit**: Stück / Liter (+ Liter-Inhalt) / kg (+ kg-Inhalt).
**Variable Produkte**: beliebig viele Varianten, je mit eigenem Preis/Bestand/SKU/Bildern; Variations-Attribut
heißt immer schlicht „Variante" (z. B. „Klein"/„Groß", „1 Liter"/„5 Liter").

### Drei Produkttypen

1. **Pflanze** (Standard): Gattung/Art/Kultivar (ersetzen den freien Produktnamen), gängiger Name, Pflegehinweise
   (siehe unten), Spezifikation (Topfgröße/Form/Gewicht/Maße — eine wiederverwendbare Voreinstellung, optional),
   Substrat-Empfehlung (siehe C), Dünger-Empfehlung (siehe C).
2. **Substrat**: freier Produktname statt Gattung/Art/Kultivar. **Komposition**: beliebig viele Zutaten-Zeilen
   (Prozent + Name), z. B. „60% Kokoshumus, 40% Perlite". **„Verkaufe ich"-Flag**: steuert, ob dieses Substrat
   selbst als kaufbares Produkt existiert oder nur eine Rezept-Empfehlung ist.
3. **Dünger**: freier Produktname + **Düngertyp** (genau einer von: Langzeitdünger, Kalibetonter Dünger,
   Ausgeglichener Dünger — pro Typ ist site-weit nur ein Produkt erlaubt). Keine Komposition, kein
   „Verkaufe ich"-Flag.

### Pflegehinweise (nur Pflanzen)

- Licht (bevorzugt): Vollsonne / Sonnig / Halbschatten / Schatten — plus optionaler Toleranz-Bereich (von/bis).
- Wasser (bevorzugt): Viel / Mäßig / Wenig — plus optionaler Toleranz-Bereich.
- Winterhärte-Text (frei) + Temperatur-Bereich (Topf, °C) + Temperatur-Bereich (ausgepflanzt, °C).
- **Substrat-Verknüpfung**: Dropdown-Auswahl eines existierenden Substrat-Produkts.
- **Düngertyp-Wahl**: einer der 3 Düngertypen — wählbar auch wenn (noch) kein passendes Produkt existiert.
- **Düngemenge**: Viel / Mäßig / Wenig.

### Kategorien

Baumstruktur (beliebig verschachtelt), pro Kategorie: Name (frei benannt **oder** an Gattung/Art/Kultivar
gebunden — Mitgliedschaft folgt dann automatisch den passenden Produkten), optionale „Überschrift im Shop"
(kurzer Text), optionale **Beschreibung** (längerer Text, „selten benötigt, aber z. B. um zu erklären was
Aronstabgewächse sind"), sichtbar/versteckt, auf der Startseite als Karussell zeigen.
5 zusätzliche System-Kategorien (Reduziert, Rabattaktionen, Neu, Beliebt, Alle Produkte) sind keine echten
Baum-Einträge, nur Sichtbarkeits-/Sortier-Einstellungen.

---

## C. Platzierungs-Vorgaben

### Bereits so implementiert (Ist-Zustand, `content-single-product.php`)

- **Preis**: eigene Zeile direkt unter Titel/Taxonomie-Zeile, groß, Serif, Akzentfarbe.
- **Lagerbestand-Indikator**: direkt unter dem Preis (Punkt + Text „Auf Lager"/„Nur noch N"/„Ausverkauft").
- **Warenkorb/Varianten-Auswahl**: direkt darunter.
- **Kurzbeschreibung**: unter dem Kaufbereich, noch innerhalb der "Hero"-Spalte (Bild+Info nebeneinander).
- **Pflege-Steckbrief**: eigene Box rechts neben der Langbeschreibung (zweispaltig, nur wenn mindestens ein
  Pflegewert gesetzt ist) — Licht/Wasser als Balken-Skala mit „Bevorzugt"-Markierung, Temperatur als Bereichs-
  Balken, Überwinterung/Winterhärte als Text.
- **Substrat-Empfehlung**: eigene Zeile *innerhalb* des Pflege-Steckbriefs. Ist das verknüpfte Substrat NICHT
  selbst verkäuflich → Überschrift „Empfohlenes Substrat nach Hausrezept" + Zutatenliste als Text. IST es
  verkäuflich → gleiche Zutatenliste + Link „Oder hier kaufen" zur Substrat-Produktseite.
- **Dünger-Empfehlung**: gleiche Logik, eigene Zeile im Pflege-Steckbrief — Düngertyp + Düngemenge als Text,
  plus Link „Oder hier kaufen" **nur falls** aktuell ein Produkt dieses Düngertyps existiert (wird automatisch
  zur Anzeigezeit gesucht, nicht fest verknüpft — sobald ein passendes Produkt angelegt wird, erscheint der
  Link ohne weiteres Zutun).
- **Meta-Description** (Google-Suchergebnis, nicht sichtbar auf der Seite selbst): „Gattung Art 'Kultivar' im
  Xcm Topf für Y€, verfügbar: Kurzbeschreibung" — bei variablen Produkten entfällt „im Xcm Topf", „für" wird zu
  „ab". Für Substrat/Dünger ohne Gattung/Art: „Name für/ab Y€, verfügbar: Kurzbeschreibung". Das
  Verfügbarkeits-Wort wird **live** aus dem tatsächlichen Bestand aktualisiert (nicht nur beim App-Push).

### Bereits so implementiert (Ist-Zustand, `archive-product.php` — Shop-Seite)

- Sidebar-Filter: Preis-Regler, Gattung & Art (verschachtelt), **Kategorien** (verschachtelt, eigene
  Filterdimension neben Gattung/Art), Winterhärte, Licht, Wasser, gruppierte + reguläre Tags — alle als
  Checkbox-Listen mit Live-Ergebniszahl pro Option.
- **Kategorie-Überschrift + -Beschreibung**: erscheinen zentriert über dem Produktgrid, aber nur wenn genau
  eine einzelne Kategorie aktiv gefiltert ist (nicht bei mehreren gleichzeitig, nicht ohne Filter).

### Explizit vorgegeben (aus diesem Chat, ggf. noch nicht 1:1 visuell umgesetzt)

- Substrat/Dünger-Empfehlung soll für Kunden sichtbar sein (nicht nur intern) — Kaufen-Link nur wenn ein
  passendes verkäufliches Produkt existiert, sonst reiner Rezept-/Empfehlungstext.
- Kategorie-Beschreibung ist bewusst ein *selten benötigtes* Feld — darf im Redesign unauffällig/klein sein,
  muss aber vorhanden sein, wenn gepflegt.
