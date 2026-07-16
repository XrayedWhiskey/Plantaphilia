# Plantaphilia - Entwicklungsplan

## 1. Design & Layout

### 1.1 Mobile Optimierung
- [x] Jedes Layout gut handy kompatibel machen (Design-Richtlinien in design_proposal.md)
  - [x] Responsive Design für alle Seiten (Design-Richtlinien in design_proposal.md)
  - [x] Touch-optimierte Bedienelemente (Design-Richtlinien in design_proposal.md)
  - [x] Mobile Navigation anpassen (Design-Richtlinien in design_proposal.md)

### 1.2 Header Redesign
- [x] Header Elemente neu anordnen
  - [x] Logo "white space" anzeigen
  - [x] Shop Link/Button
  - [x] Konto Button
  - [x] Warenkorb Button
  - [x] Sprachen Dropdown
  - [x] Sandwich Menü entfernen
  - [x] Alle Elemente in einem Bereich
- [x] Responsive Header
  - [x] Bei schmalem Bildschirm: Nur Symbole, kein Text
  - [x] Icons für Shop, Konto, Warenkorb, Sprachen

### 1.3 Footer Redesign
- [x] Account Bereich entfernen
  - [x] Übersicht entfernen
  - [x] Bestellungen entfernen
  - [x] Andere Account-Infos entfernen
- [x] Abmelden Button in Header verschieben
- [x] "Sammlung · Winter 2026" Text entfernen
- [x] "Pelargonien aus europäischen Schattengärten — kuratiert, etikettiert, versandfertig." Text entfernen
- [x] "Zum Shop" Button beibehalten

### 1.4 Shop Seite (plantaphilia.local/shop/)
- [x] Produkt Carousels hinzufügen
  - [x] "Neu" Carousel
  - [x] "Rabattiert" Carousel
  - [x] "Meist verkauft" Carousel
  - [x] "Pflanzen Familien" Carousel
  - [x] Carousels nicht anzeigen wenn keine Produkte in Kategorie
- [x] Ansicht umschalten
  - [x] Button für Listenansicht
  - [x] Button für Kachelansicht
  - [x] Auswahl speichern (persistent)
- [x] Filter Sidebar (Laptop Ansicht)
  - [x] Links wie bei Amazon
  - [x] Expanding Headers (z.B. "Pflanzen Gattung" mit Pfeil nach unten)
  - [x] Checkboxes für Filter-Optionen
  - [x] Alle relevanten Parameter für Kunden
  - [x] Irrelevante Parameter ausblenden (z.B. Steuerklasse)

### 1.5 Konto Seite (plantaphilia.local/konto/)
- [x] Alle Account-Infos auf einer Seite konsolidieren
  - [x] Bestellungen anzeigen
  - [x] Adressen anzeigen
  - [x] Andere Account-Daten anzeigen
- [x] Bestellungen Anzeige
  - [x] Erste 3 Bestellungen anzeigen
  - [x] "Mehr anzeigen" Button unter den ersten 3
  - [x] Bei Klick: 5 weitere Bestellungen anzeigen
  - [x] Button ausblenden wenn alle Bestellungen sichtbar
- [x] Sticky Navigation
  - [x] Liste mit Buttons für verschiedene Bereiche
  - [x] Navigation scrollt mit
  - [x] Klick auf Button scrollt zum richtigen Bereich

### 1.6 Admin Seiten Design (plantaphilia.local/produkt-liste/)
- [x] Design für alle Admin Seiten verbessern
  - [x] Farben für bessere Lesbarkeit anpassen
  - [x] "Verkaufen" Checkbox besser erkennbar machen
  - [x] Konsistentes Design über:
    - [x] Produkt-Liste
    - [x] Bestellungen
    - [x] Neues Produkt
    - [x] Newsletter

## 2. Neue Funktionen

### 2.1 Produktbewertungen (plantaphilia.local/product/xyz)
- [x] Bewertungssystem implementieren
  - [x] Nur für angemeldete Benutzer
  - [x] Nur wenn Produkt bereits gekauft wurde
  - [x] Sterne-Bewertung (1-5)
  - [x] Text-Kommentar möglich
  - [x] Bewertungen auf Produktseite anzeigen

### 2.2 Newsletter Popup bei Anmeldung
- [x] Popup bei Login anzeigen
  - [x] Frage: "Möchten Sie unseren Newsletter abonnieren?"
  - [x] "Ja" Button
  - [x] "Nein" Button
  - [x] "Schließen" Button (wegdrückbar)
- [x] Popup Logik
  - [x] Bei jeder Anmeldung anzeigen (bis Ja/Nein geklickt)
  - [x] Nach Ja/Nein Auswahl nicht mehr anzeigen
  - [x] Auswahl speichern in Benutzerprofil
- [x] Konto Bereich Einstellung
  - [x] Newsletter-Präferenz im Konto änderbar
  - [x] Checkbox für Newsletter-Abonnement

### 2.3 Rabattcode System (plantaphilia.local/produkt-liste)
- [x] Rabattcode Erstellung
  - [x] Admin Seite für Rabattcode Management
  - [x] Rabattcode erstellen mit folgenden Parametern:
    - [x] Code Name/Bezeichnung
    - [x] Mindestbestellwert
    - [x] Gültig für bestimmte Produkte (muss gekauft werden)
    - [x] Gültig für bestimmte Kategorien
    - [x] Reduzierungs-Menge (Prozent oder Festbetrag)
    - [x] Wie oft verwendbar (pro User, insgesamt)
    - [x] Gültigkeitsdatum (Start/Ende)
    - [x] Mindestanzahl Produkte im Warenkorb
    - [x] Maximaler Rabattbetrag
    - [x] Nur für neue Kunden
    - [x] Ausschluss von bereits rabattierten Produkten
- [x] Rabattcode Anwendung
  - [x] Eingabefeld im Warenkorb/Kasse
  - [x] Validierung des Codes
  - [x] Rabatt automatisch berechnen
  - [x] Fehlermeldung bei ungültigem Code
- [x] Rabattcode Verwaltung
  - [x] Liste aller Rabattcodes
  - [x] Bearbeiten/Löschen von Codes
  - [x] Statistiken (wie oft verwendet, Gesamtrabatt)

### 2.4 Social Media Werbe-Deal System
- [x] Admin Konfiguration
  - [x] Admin-only Seite für Social Media Konfiguration
  - [x] Auswählbare Social Media Plattformen (Instagram, Facebook, TikTok, etc.)
  - [x] Pro Plattform:
    - [x] @ Account Handle eingeben
    - [x] Prozent-Bereich definieren (z.B. 1-20%)
    - [x] Aktivieren/Deaktivieren
- [x] Order-Received Seite (plantaphilia.local/kasse/order-received/.../?key=xyz)
  - [x] "Rabattcode verdienen" Button anzeigen nach Bestellung
  - [x] Popup mit Social Media Optionen
    - [x] Liste aller konfigurierten Plattformen links
    - [x] Pro Plattform:
      - [x] Möglicher Prozent-Bereich anzeigen
      - [x] Account Handle anzeigen (der getagged werden muss)
      - [x] Textfeld für User's eigenen @ eingeben
      - [x] Submit Button
- [x] Admin Deal-Verwaltung
  - [x] Liste aller User die dem Deal zugestimmt haben
  - [x] Pro User:
    - [x] Account Name
    - [x] Bestellte Produkte
    - [x] Gewählte Plattform
    - [x] User's @ Handle
    - [x] Textfeld für Prozent-Zahl eingeben
    - [x] Submit Button für Prozent-Zahl
- [x] Rabattcode Generierung
  - [x] Bei Prozent-Zahl Submission:
    - [x] Zufälliger Rabattcode generieren
    - [x] Code in Datenbank speichern
    - [x] Code nur einmal verwendbar
    - [x] Email an User senden mit Code
- [x] Konto Seite Integration
  - [x] Neben jeder Bestellung "Rabatt Deal" Button
    - [x] Nur für Bestellungen ohne abgeschlossenen Deal
  - [x] Popup öffnet bei Klick
    - [x] Gleiche Funktionalität wie auf Order-Received Seite
- [x] Bestellstatus
  - [x] "In Bearbeitung" Button für Bestellungen mit Deal
  - [x] Status-Anzeige ob Deal aktiv/pending/abgeschlossen

## 3. Produkt hinzufügen Seite (Custom statt wp-admin Redirect)
- [x] Button "Produkt hinzufügen" nicht zu wp-admin umleiten
  - [x] Eigene Seite für Produkt hinzufügen erstellen
  - [x] Routing konfigurieren
- [x] Bulk Produkt Upload via Excel/CSV
  - [x] Excel Datei Upload Funktion implementieren
  - [x] CSV Datei Upload Funktion implementieren
  - [x] Excel Parsing Logic
  - [x] CSV Parsing Logic
  - [x] Massenimport in WooCommerce
  - [x] CSV-Felder definieren (alle Produktformular-Felder außer Beschreibung und Bilder):
    - [x] Produktname
    - [x] Substrat Option (Pflanze / Substrat)
    - [x] Preis
    - [x] Steuerklasse
    - [x] Einheiten (Stück / Liter)
    - [x] Produkteinheiten (bei Liter)
    - [x] Differenzbesteuerung
    - [x] Art. Nr
    - [x] Gewicht (kg)
    - [x] Maße (cm) - Länge, Breite, Höhe
    - [x] Versandklasse
    - [x] Versandmaße (cm) - LxBxH
    - [x] Lieferzeiten
    - [x] Lagerverwaltung (Ja/Nein)
    - [x] Lieferrückstand erlauben (Ja/Nein)
    - [x] Schwellwert für geringer Lagerbestand
    - [x] Nie als geringer Lagerbestand markieren (Ja/Nein)

### 3.1 Produktformular Felder
- [x] Produktname (Textfeld)
- [x] Substrat Option (Toggle Switch: Pflanze / Substrat)
- [x] Preis (Zahlenfeld)
- [x] Steuerklassen System
  - [x] Standard: 19% voreingestellt
  - [x] Checkbox für benutzerdefinierte Steuerklasse
  - [x] Dropdown Menü bei Checkbox aktiv
  - [x] Option "Steuerklasse hinzufügen" im Dropdown
  - [x] Steuerklassen in Datenbank speichern für Wiederverwendung
- [x] Einheiten (Toggle Switch: Stück / Liter)
- [x] Produkteinheiten
  - [x] Nur sichtbar wenn Toggle auf "Liter"
  - [x] Eingabefeld für x Liter
- [x] Grundpreiseinheiten
  - [x] Automatisch berechnen (nicht als Eingabefeld)
  - [x] Spezielle Logik für Erde
- [x] Differenzbesteuerung
  - [x] Checkbox
  - [x] Erklärungstooltip hinzufügen
- [x] Art. Nr (Artikelnummer Feld)
- [x] Lagerverwaltung
  - [x] Standardmäßig auf "Ja" setzen
  - [x] Nicht als Option auf Seite anzeigen
- [x] Lieferrückstand erlauben?
  - [x] Standardmäßig auf "NEIN" setzen
- [x] Schwellwert für "geringer Lagerbestand"
  - [x] Checkbox für individuellen Schwellwert
  - [x] Wenn checkbox false: Feld nicht bearbeitbar, Wert = 5
  - [x] Wenn checkbox true: Feld bearbeitbar
  - [x] Checkbox "nie als geringer Lagerbestand markieren"
- [x] Gewicht (kg) (Eingabefeld)
- [x] Maße (cm) (Eingabefelder: Länge, Breite, Höhe)
- [x] Versandmaße (cm)
  - [x] Checkbox "Versandmaße gleich wie Produktmaße"
  - [x] Standardmäßig aktiv (checked)
  - [x] Bei Deaktivierung: 3 Felder für LxBxH erscheinen
  - [x] Bei Aktivierung: Felder ausgeblendet, Produktmaße verwenden
- [x] Versandklasse
  - [x] Voreinstellung System (ähnlich wie Steuerklasse)
  - [x] Checkbox zum Ausschalten der Option
  - [x] Option "Versandklasse hinzufügen" im Dropdown
  - [x] Versandklassen in Datenbank speichern für Wiederverwendung
  - [x] Parameter der Versandklasse unter Dropdown anzeigen
  - [x] Parameter-Felder für Versandklasse ausfüllbar machen
  - [x] Versandklassen-Parameter in Codebase recherchieren (WooCommerce)
- [x] Nicht retournierbar
  - [x] Standard: aus
  - [x] Nicht einschaltbar (disabled)
- [x] Lieferzeiten
  - [x] Standard: 7 Tage
  - [x] Editierbar
- [x] Bildupload
  - [x] Titelbild Upload
  - [x] Weitere Bilder Upload (mehrere)
  - [x] Bild-Crop & Wasserzeichen Popup
    - [x] Popup bei Bildupload erscheinen
    - [x] Zwangsmäßig quadratischer Crop
    - [x] Wasserzeichen mit Logo-Plantaphilia-1.svg
    - [x] Logo grau hinterlegt (durchsichtig)
    - [x] Checkbox "Logo-Farben invertieren"
    - [x] Graue Hinterlegung bleibt immer grau (auch bei Invertierung)
    - [x] Wasserzeichen durch Ziehen an Ecken in Größe veränderbar
    - [x] Wasserzeichen verschiebbar
    - [x] Crop-Bereich zoombar
    - [x] Crop-Bereich verschiebbar
    - [X] Rechteckiges Bild Feature
      - [X] Checkbox "Rechteckiges Bild" (horizontal oder vertikal)
      - [X] Bei Aktivierung: Toggle Switch mit Horizontal/Vertikal erscheint
      - [X] 2 schwarze Balken erscheinen bei Aktivierung
      - [X] Schwarze Balken an den Kanten ziehbar zum Ausfahren
      - [X] Markierte Bereiche werden beim Speichern entfernt
      - [X] Entfernte Bereiche werden mit transparenten Bereichen ersetzt
      - [X] Bild als PNG speichern (mit Transparenz)
      - [X] Bild bleibt quadratisch, sieht aber rechteckig aus
      - [X] Layer-Logik für Bild-Crop
        - [X] Layer unten: Original-Bild
        - [X] Layer mitte: Entfernungsbereiche (schwarze Balken / transparent)
        - [X] Layer oben: Wasserzeichen
        - [X] Wasserzeichen liegt immer über transparenten Bereichen
        - [X] Wasserzeichen wird nicht gecutted in Entfernungsbereichen
    - [x] Speichern Button
    - [x] Abbrechen Button
- [x] Produktbeschreibung
  - [x] Rich Text Editor (wie WP-admin)
  - [x] Buttons für Bold, Italic, Listen, etc.

## 4. Produktlistenseite - Sales Verbesserungen

### 4.1 Sale Gruppen Popup
- [x] Popup schließt nicht nach "Hinzufügen" Klick
  - [x] Nur Schließen-Button schließt Popup
- [x] Logik gegen doppelte Hinzufügung
  - [x] Produkt verschwindet aus Liste nach Hinzufügen zu Gruppe
  - [x] Produkt nicht mehr derselben Gruppe hinzufügbar
  - [x] Produkt nicht mehr anderen Gruppen hinzufügbar
  - [x] Produkte nur wieder hinzufügbar nach Sale-Ende oder Abbruch der Sale-Erstellung
- [x] Gruppen-Anzeige verbessern
  - [x] Statt "1Artikel in gruppe": Liste mit Artikeln anzeigen
  - [x] Mülleimer Icon pro Artikel zum Entfernen aus Gruppe

### 4.2 Produkte löschen
- [x] Löschen-Button oder Icon pro Produkt in der Liste
- [x] Nachfrage-Popup beim Löschen
  - [x] "Möchten Sie dieses Produkt wirklich löschen?"
  - [x] Ja/Nein oder Löschen/Abbrechen Buttons
- [x] Produkt wird aus WooCommerce entfernt
- [x] Produkt wird aus der Liste entfernt

## 5. Bestellungen Seite

### 5.1 Bestellungen Exportieren
- [x] "Bestellungen exportieren" Button
  - [x] Popup mit 4 Checkboxen erscheinen:
    - [x] "in Wartestellung"
    - [x] "in Bearbeitung"
    - [x] "Versandt"
    - [x] "Abgeschlossen"
  - [x] CSV Export der ausgewählten Bestellungen
- [x] "Abgeschlossene Bestellungen" Button

### 5.2 Bestellungen Anzeige Logik
- [x] Nach Account sortieren
- [x] Unter Account nach Adresse sortieren
  - [x] Bei "in Wartestellung": nach Rechnungsadresse
  - [x] Bei "in Bearbeitung": nach Lieferadresse
- [x] Anzeige pro Account-Adresse-Gruppe:
  - [x] Account Name
  - [x] Adresse (Rechnungs- oder Lieferadresse)
  - [x] Bestellungen innerhalb der Gruppe nach Datum sortiert
- [x] Anzeige pro Bestellung:
  - [x] Bestellnummer
  - [x] Datum (wann bestellt / auf In Bearbeitung / auf In Zustellung gesetzt)
  - [x] Was bestellt wurde (X x ArtNr|Name)
  - [x] Preis
- [x] Logik für gleiche Adresse:
  - [x] Wenn gleiche Adresse: Nur Adresse einmal oben anzeigen
  - [x] Danach alle Bestellungen mit:
    - [x] Was bestellt wurde
    - [x] Preis
    - [x] Bestellnummer
- [x] Logik für unterschiedliche Adressen:
  - [x] Wenn Adresse geändert: Neue Adresse anzeigen
  - [x] Danach Bestellungen mit:
    - [x] Was bestellt wurde
    - [x] Preis
    - [x] Bestellnummer

## 6. Bearbeiten Popup Überarbeitung

### 6.1 Listen-Layout
- [x] Alle Erstellungsinfos als Liste anzeigen
- [x] Rechtsbündig Stift-Icon pro Info
  - [x] Klick auf Stift aktiviert Edit-Mode
  - [x] X und Floppy Disc Buttons erscheinen
  - [x] Speichern Button speichert Änderung
  - [x] X Button bricht Änderung ab
- [x] "Bilder>" Label (ohne Stift-Icon)
  - [x] Klick öffnet Popup mit Bildern
  - [x] Bilder können entfernt werden
  - [x] Neue Bilder können hinzugefügt werden
- [x] "Beschreibung>" Label (ohne Stift-Icon)
  - [x] Klick öffnet Popup mit Beschreibung
  - [x] Rich Text Editor (gleich wie bei Erstellung)

### 6.2 Zusätzliche Buttons
- [x] "Angebot erstellen" Button
  - [x] Funktioniert wie aktuelle Bearbeiten-Funktion
- [x] "Vergangene Sales anschauen" Button
  - [x] Öffnet Popup mit Liste vergangener Sales
  - [x] Anzeige: Timeframe, Menge, verkaufte Anzahl während Sale

### 6.3 Stock Management
- [x] Stock erhöhen Feature
  - [x] "Stock um X erhöhen um Y" Funktionalität
  - [x] Beispiel: Stock um 20 in 1h erhöhen
  - [x] Geplante Stock-Erhöhungen speichern
  - [x] Automatische Ausführung zum geplanten Zeitpunkt

## 7. Newsletter Seite

### 7.1 Newsletter Editor
- [x] Rich Text Editor für Newsletter Inhalt
- [x] Produktliste auf linker Seite
  - [x] Sortiert nach Kategorien:
    - [x] "Sales" (Carousel Anzeige aller Produkte im Sale)
    - [x] "Sale Gruppen"
    - [x] "Artikel in Sales"
    - [x] "Angebote" (Artikel in Sales nicht inkludiert)
    - [x] "Neue Produkte"
    - [x] "restocked nach leerem stock"
    - [x] "restocked nach niedrigen stock"
    - [x] "restocked"
    - [x] "Produkte"
- [x] Drag and Drop Funktion
  - [x] Produkte aus Liste in Newsletter ziehen
- [x] Anzeige im Newsletter nach Drop:
  - [x] Bei Sales/Sale Gruppen: Produkt Carousel erstellen
    - [x] Zeigt Artikelbild, Name, Angebotspreis, Reduktion
    - [x] Carousel mit allen Artikeln innerhalb des Sales/der Salegruppe
  - [x] Bei neuen Artikeln: Einzelnes Produkt anzeigen
    - [x] Mit Link zur Produktseite
    - [x] Mehr Infos (z.B. Ausschnitt aus Beschreibung)

## 8. Technische Umsetzung

### 8.1 Datenbank
- [x] Steuerklassen Tabelle erstellen
- [x] Versandklassen Tabelle erstellen
- [x] Geplante Stock-Erhöhungen Tabelle erstellen
- [x] Sale Gruppen Produkt-Zuordnungen optimieren
- [x] Newsletter Produkt-Zuordnungen Tabelle

### 8.2 Frontend
- [x] Custom Produkt hinzufügen Seite entwickeln
- [x] Excel/CSV Upload UI erstellen
- [x] Toggle Switches implementieren
- [x] Tooltips implementieren
- [x] Popup Listen-Layout für Bearbeiten
- [x] Vergangene Sales Popup UI
- [x] Bestellungen Export Popup UI
- [x] Newsletter Seite entwickeln
- [x] Drag and Drop für Newsletter implementieren
- [x] Produkt Carousel für Sales anzeigen

### 8.3 Backend
- [x] Excel Parser implementieren
- [x] CSV Parser implementieren
- [x] Bulk Import API erstellen
- [x] Steuerklassen Management API
- [x] Versandklassen Management API
- [x] Sale Gruppen Logik überarbeiten
- [x] Stock Erhöhung Scheduler implementieren
- [x] Bestellungen CSV Export API
- [x] Newsletter Speichern API
- [x] Produkt Kategorisierung API für Newsletter

### 8.4 Testing
- [x] Produkt hinzufügen Tests
- [x] Excel/CSV Import Tests
- [x] Sale Gruppen Logik Tests
- [x] Bearbeiten Popup Tests
- [x] Stock Erhöhung Tests
- [x] Bestellungen Export Tests
- [x] Newsletter Drag and Drop Tests

## 9. Support Seite - AI Chatbot (ersetzt Kontaktformular)

### 9.1 AI Chatbot Integration
- [x] Bestehendes Kontaktformular durch AI Chatbot ersetzen
- [x] Tool Calls System einrichten
  - [x] "Email an Chef" Tool (bei schweren Fragen)
  - [x] "User Bestellhistorie prüfen" Tool
  - [x] Weitere Tools überlegen (z.B. Produktinformationen, Lagerbestand, Versandstatus, etc.)
- [x] API Key Management
  - [x] Key Rotation System implementieren
  - [x] Rate Limiting (40+ requests/minute trigger rotation)
  - [x] API Keys konfigurieren:
    - [x] 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
    - [x] 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
- [x] Chat UI erstellen
  - [x] Chat Eingabefeld
  - [x] Nachrichtenverlauf anzeigen
  - [x] Typing Indikator
  - [x] Tool Call Ergebnisse anzeigen
  - [x] Streaming Antworten anzeigen (chunk-by-chunk)
  - [x] Reasoning/Thinking Content optional anzeigen (z.B. grau/grün)

### 9.2 NVIDIA API Integration Details

**Base Configuration:**
- **Base URL:** `https://integrate.api.nvidia.com/v1`
- **Model:** `z-ai/glm-5.1`
- **Client:** OpenAI-kompatibel (OpenAI SDK verwenden)

**Request Parameters:**
- **Temperature:** 1
- **Top_p:** 1
- **Max Tokens:** 16384
- **Stream:** True (für Streaming-Antworten)
- **Extra Body:** `{"chat_template_kwargs":{"enable_thinking":True,"clear_thinking":False}}` (für Reasoning/Thinking)

**Response Handling:**
- **Reasoning Content:** Antwort kann `reasoning_content` enthalten (separat vom Haupt-Content)
- **Streaming:** Chunk-basierte Verarbeitung nötig
- **Delta Format:** `chunk.choices[0].delta` enthält `content` und optional `reasoning_content`
- **Thinking/Reasoning:** Kann angezeigt werden (z.B. grau/grün formatiert)

### 9.3 Backend
- [x] AI Chatbot API Endpoint erstellen
- [x] Tool Call Handler implementieren
- [x] Email Service Integration (für "Email an Chef" Tool)
- [x] WooCommerce Order History API Integration
- [x] API Key Rotation Logic
- [x] Rate Limiting Middleware
- [x] NVIDIA API Integration (OpenAI-kompatibel mit z-ai/glm-5.1)
- [x] Streaming Response Handler
- [x] Reasoning Content Verarbeitung

### 9.4 Testing
- [x] AI Chatbot Konversation Tests
- [x] Tool Call Funktionstests
- [x] Key Rotation Tests
- [x] Rate Limiting Tests
- [x] Streaming Response Tests
- [x] Reasoning Content Tests
- [x] NVIDIA API Integration Tests

## 10. Produkt Erstellungsseite - Tags & Varianten

### 10.1 Tag System
- [x] Tag Pool erstellen
  - [x] Alle existierenden Tags anzeigen
  - [x] "Tag erstellen" Button im Pool
  - [x] Popup für neue Tag Erstellung
  - [x] Neue Tags in Datenbank speichern
- [x] Tag Hinzufügen Popup
  - [x] Tag Typ Auswahl im Popup
  - [x] Variable Tags (z.B. "Farbe:") mit Toggle Switch
  - [x] Freie Eingabe bei variablem Tag
- [x] Tag Pool Sortierung
  - [x] Variabler Tag Typ 1: Tags dieses Typs
  - [x] Variabler Tag Typ 2: Tags dieses Typs
  - [x] Alle anderen Tags

### 10.2 Produkt Varianten
- [x] Variante erstellen
  - [x] "Ist Produkt Variation" Option bei Tag Erstellung
  - [x] Original-Produkt verbinden
  - [x] Varianten auf Produktseite änderbar
- [x] Kombinierte Varianten Logik
  - [x] Mehrere Varianten kombinieren (z.B. Farbe + Topfgröße)
  - [x] Nur verfügbare Kombinationen anzeigen
  - [x] Ungültige Kombinationen ausblenden (z.B. Rot in Topfgröße X wenn nicht verfügbar)

### 10.3 Produkt Kategorien
- [x] Kategorie Tags für Pflanzengattungen
- [x] Kategorie-Tag Verwaltung im Tag Pool

## 11. Admin Seiten Design

### 11.1 Design Vereinheitlichung
- [x] Admin Seiten Design an bestehende Seiten anpassen
- [x] Consistente UI/UX über alle Admin Bereiche

## 12. Mobile Optimierung

### 12.1 Handy Display
- [x] Responsive Design für mobile Geräte
- [x] Touch-optimierte Bedienelemente
- [x] Mobile Navigation anpassen

## 13. Shop & Main Page Design

### 13.1 Shop Seite (http://plantaphilia.local/shop/)
- [x] Layout Anpassungen
  - [x] Kategorien auf linker Seite
  - [x] Filter über den Produkten
  - [x] Sortierte Ansicht Button mit Popup
- [x] Ansicht Wechsel
  - [x] Button: Kachel ↔ Liste Ansicht
  - [x] Persistente Auswahl merken

### 13.2 Main Page
- [x] Produkt Karusselle
  - [x] Karussell 1: Neue Produkte
  - [x] Karussell 2: Beliebte Produkte (Meistgekauft)
  - [x] Karussell 3: (noch zu definieren - z.B. Empfohlen/Sale)
- [x] Karussell Navigation (Pfeile/Punkte)
- [x] Responsive Karussel für Mobile

## 14. Ideas Ordner Integration

### 14.1 Warenkorb (cart/cart.php)
- [ ] Add-ons Feature hinzufügen
  - [ ] Pflanzenpass · Geschenkverpackung (€4.50)
  - [ ] Pflegezettel · handgeschrieben (Kostenlos)
  - [ ] Bio-Pelargonien-Dünger · 250 ml (€8.90)
  - [ ] UI: Checkbox-Liste mit Beschreibungen und Preisen
- [ ] Notiz an die Gärtnerei hinzufügen
  - [ ] Textarea-Feld für optionale Notizen
  - [ ] Placeholder: "Optional · Wunschtermin, Pflege-Frage, Widmung für die Karte …"
- [ ] Wunschliste Button hinzufügen
  - [ ] Button neben "Entfernen" mit Herz-Icon
- [ ] Reservierungs-Timer anzeigen
  - [ ] "Reserviert · 60 Min" Anzeige im Header
  - [ ] Countdown-Logik implementieren
- [ ] CSS: cart-checkout.css Features integrieren
  - [ ] Add-ons Styling übernehmen
  - [ ] Notizfeld Styling übernehmen
  - [ ] Wunschliste Button Styling übernehmen

### 14.2 Kasse (checkout/form-checkout.php)
- [ ] Kontaktformular verbessern
  - [ ] Newsletter Checkbox hinzufügen
  - [ ] Text: "Vier Briefe im Jahr zur Jahreszeiten-Pflege und neuen Sammlerstücken. Jederzeit abbestellbar."
- [ ] Rechnungsadresse abweichend Toggle
  - [ ] Checkbox für abweichende Rechnungsadresse
  - [ ] Bei Aktivierung: Rechnungsadresse Felder anzeigen
- [ ] Versandoptionen verbessern
  - [ ] DHL · klimaneutral (Standard)
  - [ ] DHL Express · Pflanzen-Vorrang
  - [ ] Selbstabholung · Gewächshaus Freiburg
  - [ ] UI: Radio-Buttons mit Beschreibungen und Preisen
- [ ] Zahlungsoptionen verbessern
  - [ ] SEPA-Lastschrift mit Mandat
  - [ ] Rechnung (14 Tage)
  - [ ] PayPal
  - [ ] Klarna · Sofort oder Ratenkauf
  - [ ] Kreditkarte · Visa, Mastercard, Amex
  - [ ] UI: Radio-Buttons mit Beschreibungen und Inline-Formularen
- [ ] Trust-Badges hinzufügen
  - [ ] 14 Tage Pflanzengarantie
  - [ ] Versand frühestens Mo, 19. Mai
  - [ ] SSL · DSGVO
- [ ] CSS: cart-checkout.css Features integrieren
  - [ ] Verbesserte Formular-Styling
  - [ ] Trust-Badges Styling

### 14.3 Bestellbestätigung (checkout/thankyou.php)
- [ ] Share-for-discount Banner verbessern
  - [ ] Social Media Links mit Icons (Instagram, TikTok, Pinterest)
  - [ ] Tagge @plantaphilia und #plantaphilia Text
  - [ ] "Jetzt teilen" CTA Button
- [ ] Timeline verbessern
  - [ ] 4-Schritte Timeline mit Status-Indikatoren
  - [ ] Schritt 1: Bestellung aufgegeben (done)
  - [ ] Schritt 2: In der Gärtnerei (active)
  - [ ] Schritt 3: Verpackt & abgeholt
  - [ ] Schritt 4: Bei Ihnen
- [ ] Fußnoten-Sektion hinzufügen
  - [ ] Fragen zur Bestellung
  - [ ] Pflegehinweis
  - [ ] 14 Tage Pflanzengarantie
- [ ] CSS: cart-checkout.css Features integrieren
  - [ ] Banner Styling mit Gradient-Hintergrund
  - [ ] Timeline Styling
  - [ ] Fußnoten Styling

### 14.4 Produktseite (content-single-product.php)
- [ ] Pflege-Steckbrief Varianten hinzufügen
  - [ ] Icons-Variante mit Skalen (Licht, Wasser, Temperatur)
  - [ ] Tabelle-Variante mit Datenblatt-Layout
  - [ ] Plakette-Variante (existiert bereits)
  - [ ] UI: Toggle oder Auswahl zwischen Varianten
- [ ] Bewertungen verbessern
  - [ ] Bewertungsformular für Käufer
    - [ ] Sterne-Bewertung (1-5)
    - [ ] Text-Kommentar
    - [ ] Account-Info automatisch übernehmen
  - [ ] Login-Status Banner
    - [ ] Gast: "Schon einmal bestellt? Melden Sie sich an"
    - [ ] User: "Bewertungen nur für Käufer:innen"
    - [ ] Buyer: "Sie haben dieses Sammlerstück gekauft"
  - [ ] "Mehr anzeigen" Button für weitere Bewertungen
- [ ] Lore-Sektion verbessern
  - [ ] Pull-Quote mit Zitat-Styling
  - [ ] Mehrere Absätze mit Drop-Cap
- [ ] CSS: product-page.css Features integrieren
  - [ ] Icons-Skalen Styling
  - [ ] Tabelle-Styling
  - [ ] Bewertungsformular Styling

### 14.5 Design System Integration
- [ ] colors_and_type.css in pa-design.css integrieren
  - [ ] Color Tokens übernehmen
  - [ ] Typography Tokens übernehmen
  - [ ] Font-Familien definieren (Montserrat + Playfair Display)
- [ ] CSS Variablen standardisieren
  - [ ] --bg-deep, --bg-surface, --bg-raised, --bg-inky
  - [ ] --plum, --plum-hot, --plum-soft
  - [ ] --burgundy, --burgundy-hot
  - [ ] --amethyst, --amethyst-dim
  - [ ] --berry, --forest, --gold-dark
- [ ] UI Kits aus ui_kits/ übernehmen
  - [ ] Button-Komponenten
  - [ ] Formular-Komponenten
  - [ ] Card-Komponenten

### 14.6 Header & Footer
- [ ] Header und Footer so wie im Projekt belassen
  - [ ] Keine Änderungen an header.php
  - [ ] Keine Änderungen an footer.php
  - [ ] Bestehendes Design beibehalten
