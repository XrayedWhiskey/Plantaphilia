/**
 * ============================================================================
 * PLANTAPHILIA VERSION REGISTRY - COMPREHENSIVE DEVELOPER GUIDE
 * ============================================================================
 * 
 * Dieses System verwaltet die Versionierung von Produktdaten und ermöglicht
 * Migrationen zwischen verschiedenen App-Versionen. Es ist kritisch für die
 * Datenintegrität bei Updates der App.
 * 
 * ========================================================================
 * FÜR ENTWICKLER: WIE MAN EINE NEUE VERSION HINZUFÜGT
 * ========================================================================
 * 
 * 1. VERSION IN package.json AKTUALISIEREN:
 *    - Erhöhe die Version in app/package.json (z.B. "1.2.0" → "1.3.0")
 *    - Aktualisiere auch APP_VERSION in dieser Datei
 * 
 * 2. NEUE VERSION IM VERSIONS OBJEKT HINZUFÜGEN:
 *    - Füge einen neuen Eintrag am Anfang des VERSIONS Objekts hinzu
 *    - Definiere keywords: Welche Systeme/Felder haben sich geändert?
 *    - Definiere description: Was hat sich geändert?
 *    - Definiere compatible_with: Welche Versionen sind kompatibel?
 *    - Definiere migrations: Wie werden alte Daten migriert?
 * 
 * 3. KEYWORDS DEFINIEREN:
 *    - Keywords beschreiben die geänderten Systeme/Felder
 *    - Beispiele: 'product_specs', 'care_temp_min', 'shipping_dimensions'
 *    - Keywords werden im Migration-UI angezeigt
 * 
 * 4. MIGRATIONEN IMPLEMENTIEREN:
 *    - Für jede ältere Version, die migriert werden muss
 *    - Die Migrationsfunktion empfängt das alte Produktobjekt
 *    - Gibt das migrierte Produktobjekt zurück
 *    - Setze app_version auf die neue Version
 * 
 * 5. KOMPATIBILITÄT DEFINIEREN:
 *    - compatible_with listet Versionen, die ohne Migration funktionieren
 *    - Wenn eine Version nicht kompatibel ist, wird eine Migration benötigt
 * 
 * ========================================================================
 * BEISPIEL: NEUE VERSION 1.3.0 MIT NEUEM FELD
 * ========================================================================
 * 
 * export const VERSIONS = {
 *   '1.3.0': {
 *     keywords: ['new_field'],
 *     description: 'Neues Feld "custom_field" hinzugefügt',
 *     compatible_with: ['1.2.0', '1.1.2', '1.1.1', '1.1.0'],
 *     migrations: {
 *       '1.2.0': (product) => ({
 *         ...product,
 *         custom_field: product.custom_field ?? null // Default für alte Produkte
 *       }),
 *       '1.1.2': (product) => ({ ...product, custom_field: null }),
 *       // ... weitere Migrationen
 *     }
 *   },
 *   // ... ältere Versionen
 * }
 * 
 * ========================================================================
 * DATENBANK-ÄNDERUNGEN
 * ========================================================================
 * 
 * Wenn du neue Felder zur Datenbank hinzufügst:
 * 1. Füge die Felder zum CREATE TABLE Statement in database.js hinzu
 * 2. Verwende DEFAULT-Werte für existierende Daten
 * 3. Aktualisiere saveProduct() um die neuen Felder zu speichern
 * 4. Füge Migrationslogik im versionRegistry hinzu
 * 
 * ========================================================================
 * TESTEN DES VERSIONSSYSTEMS
 * ========================================================================
 * 
 * 1. Test-Modus aktivieren: Ctrl+Shift+T
 * 2. Wähle eine Version aus der Vergangenheit (z.B. 1.1.2)
 * 3. Ein Test-Ordner wird mit Produkten aus dieser Version erstellt
 * 4. Eine zweite App-Instanz öffnet sich mit dem Test-Ordner
 * 5. Teste das Migration-UI und die Migrationen
 * 6. Beende den Test über das Banner in der Haupt-App
 * 
 * ========================================================================
 */

export const APP_VERSION = '2.2.0' // Aktuelle Version aus package.json

/**
 * Version Registry - alle Versionen mit ihren Änderungen
 * keywords: Beschreibt welche Systeme/Felder sich geändert haben
 * compatible_with: Liste von Versionen, mit denen diese Version kompatibel ist
 * migrations: Funktionen zum Migrieren von Daten von älteren Versionen
 */
export const VERSIONS = {
  '2.2.0': {
    keywords: ['grundpreis_display', 'delivery_time_display', 'spec_weight_dimensions_push', 'variant_menu_order'],
    description: 'Vollständigkeits-Audit: Grundpreis (Liter) und Lieferzeit werden jetzt auf der Produktseite angezeigt, Gewicht/Maße aus der Spezifikation werden korrekt an WooCommerce übergeben (Versandberechnung), Varianten-Reihenfolge wird korrekt gepusht.',
    details: {
      before: 'Einheit/Literangabe und Lieferzeit wurden gepusht, aber nirgends angezeigt; WooCommerce bekam nie ein echtes Gewicht/Maße für die Versandberechnung (App pushte leere Alt-Spalten statt der Spezifikationsdaten); Varianten-Reihenfolge auf der Website konnte von der App-Konfiguration abweichen.',
      after: 'Produktseite zeigt bei literbasierten Produkten Inhalt + Grundpreis pro Liter unter dem Preis, sowie die Lieferzeit in der Meta-Zeile. Ein neuer schlanker Endpunkt wendet die Spezifikations-Maße korrekt auf die nativen WooCommerce-Felder an. Varianten werden in der konfigurierten Reihenfolge gepusht.'
    },
    // Rein additive Anzeige-Ergänzungen + ein Push-Korrektur-Endpunkt, keine
    // Schema-Änderung — bestehende Produkte brauchen keine Migration.
    compatible_with: ['2.1.1', '2.1.0', '2.0.2', '2.0.1', '2.0.0', '1.3.4', '1.3.3', '1.3.2', '1.3.1', '1.3.0', '1.2.1', '1.2.0', '1.1.2', '1.1.1', '1.1.0'],
    migrations: {}
  },
  '2.1.1': {
    keywords: ['product_list_actions_column_width'],
    description: 'Produktliste: Aktionen-Spalte (Löschen-Button) war zu schmal und wurde rechts abgeschnitten.',
    details: {
      before: 'Letzte Tabellenspalte (Geändert-Datum + Vorschau/Bearbeiten/Löschen-Buttons) war auf 110px fixiert — zu schmal, Löschen-Button ragte über den sichtbaren Tabellenrand hinaus und wurde abgeschnitten.',
      after: 'Aktionen-Spalte auf 165px verbreitert (Erstellt-Spalte im Gegenzug leicht verschmälert), alle drei Buttons vollständig sichtbar.'
    },
    // Rein CSS-Breiten-Fix, keine Daten-/Schema-Änderung.
    compatible_with: ['2.1.0', '2.0.2', '2.0.1', '2.0.0', '1.3.4', '1.3.3', '1.3.2', '1.3.1', '1.3.0', '1.2.1', '1.2.0', '1.1.2', '1.1.1', '1.1.0'],
    migrations: {}
  },
  '2.1.0': {
    keywords: ['seo_title_wording', 'blueprint_category_link', 'variant_low_stock', 'stock_display_toggle'],
    description: 'SEO-Titel ohne "online", Gattung/Art können dauerhaft Kategorien zugeordnet werden, Varianten haben einen eigenen Niedrig-Bestand-Schwellwert, und ein Umschalter steuert bei Produkt und Variante ob niedriger Bestand als genaue Stückzahl oder nur vage angezeigt wird.',
    details: {
      before: 'SEO-Titel endete auf "online kaufen"; Kategorien für eine Gattung/Art ließen sich nur einmalig auf aktuell verlinkte Produkte anwenden (keine Vererbung auf neue Produkte); Varianten hatten keinen eigenen Niedrig-Bestand-Schwellwert; die Produktseite zeigte bei niedrigem Bestand immer hart die exakte Stückzahl (Schwellwert fix 5).',
      after: 'SEO-Titel endet auf "kaufen"; Gattung/Art-Kategorie-Verknüpfung ist dauerhaft (neue Produkte erben automatisch, auch Überkategorien), in der Kategorien-Übersicht sichtbar; Varianten haben eigenen Schwellwert + "nie niedrig"-Flag; ein neuer Umschalter (Produkt und Variante) entscheidet zwischen exakter Stückzahl und vagem "Niedriger Bestand"-Hinweis, Default bewahrt das bisherige Verhalten.'
    },
    // Neue Spalten (products.show_exact_stock, variants.low_stock_threshold/
    // never_low_stock/show_exact_stock, gattungen/arten.category_ids) sind
    // additiv mit sicheren Defaults, per reconcileSchema() automatisch
    // nachgezogen — bestehende Daten brauchen keine Migration.
    compatible_with: ['2.0.2', '2.0.1', '2.0.0', '1.3.4', '1.3.3', '1.3.2', '1.3.1', '1.3.0', '1.2.1', '1.2.0', '1.1.2', '1.1.1', '1.1.0'],
    migrations: {}
  },
  '2.0.2': {
    keywords: ['carousel_view_hidden_display'],
    description: 'In der "Nur Mainpage-Carousels"-Ansicht wird das Ausblenden-Häkchen nicht angehakt und der Name nicht kursiv/ausgegraut angezeigt.',
    details: {
      before: 'Eine Kategorie mit gesetztem "Ausblenden" wirkte auch in der Mainpage-Carousel-Ansicht kursiv/ausgegraut mit angehaktem Häkchen, obwohl sie dort (anders als im Shop-Filter) weiterhin sichtbar ist — verwirrend.',
      after: 'In der Mainpage-Carousel-Ansicht wird "Ausblenden" nie als angehakt/kursiv/ausgegraut dargestellt, da es dort ohnehin nur den Shop-Filter betrifft — visuelle Bestätigung, dass die Kategorie auf der Startseite erscheint.'
    },
    // Rein visuelle Darstellungs-Änderung, keine Daten-/Schema-Änderung.
    compatible_with: ['2.0.1', '2.0.0', '1.3.4', '1.3.3', '1.3.2', '1.3.1', '1.3.0', '1.2.1', '1.2.0', '1.1.2', '1.1.1', '1.1.0'],
    migrations: {}
  },
  '2.0.1': {
    keywords: ['carousel_push_recompute', 'alle_produkte_random', 'category_click_products'],
    description: 'Kategorie-Carousel-Cache aktualisiert sich sofort beim Push, "Alle Produkte" zeigt 100 zufällige Produkte pro Seitenaufruf, Klick auf eine Kategorie in der App zeigt alle (auch verschachtelten/gebundenen) Produkte.',
    details: {
      before: 'Hidden+Mainpage-Carousel-Kombination brauchte bis zu 24h, um auf der Startseite sichtbar zu werden; "Alle Produkte" zeigte eine feste Top-30-nach-Verkäufen-Auswahl; Klick auf eine Kategorie in der App tat nichts.',
      after: 'Kategorie-Push aktualisiert das Startseiten-Carousel sofort; "Alle Produkte" zeigt bei jedem Laden 100 frisch zufällig gewählte Produkte ohne Duplikate; Klick auf eine Kategorie öffnet eine Liste aller zugehörigen Produkte (inkl. Unterkategorien und Gattung/Art/Kultivar-gebundener Kategorien), klickbar zum Bearbeiten.'
    },
    // Keine Schema-Änderung — Cache-Timing, Zufallsauswahl und eine neue,
    // rein lesende Ansicht. Bestehende Produkte/Kategorien brauchen keine Migration.
    compatible_with: ['2.0.0', '1.3.4', '1.3.3', '1.3.2', '1.3.1', '1.3.0', '1.2.1', '1.2.0', '1.1.2', '1.1.1', '1.1.0'],
    migrations: {}
  },
  '2.0.0': {
    keywords: ['seo_length_warning'],
    description: 'Warnung beim Speichern, wenn SEO-Titel/-Beschreibung zu lang für Google sind.',
    details: {
      before: 'Zeichenzähler bei den SEO-Feldern waren rein informativ, zu lange Werte konnten ohne Hinweis gespeichert werden.',
      after: 'Beim Erstellen/Speichern erscheint eine Warnung, wenn SEO-Titel (>60 Zeichen) oder -Beschreibung (>160 Zeichen) zu lang sind; ein zweiter Klick auf denselben Wert ignoriert die Warnung und speichert trotzdem, eine Änderung des Feldes lässt die Warnung erneut erscheinen.'
    },
    // Rein UI-seitige Validierung, keine Schema-Änderung — bestehende
    // Produkte brauchen keine Migration.
    compatible_with: ['1.3.4', '1.3.3', '1.3.2', '1.3.1', '1.3.0', '1.2.1', '1.2.0', '1.1.2', '1.1.1', '1.1.0'],
    migrations: {}
  },
  '1.3.4': {
    keywords: ['seo_auto_sync', 'category_heading', 'variant_gallery_fix'],
    description: 'SEO-Titel/-Beschreibung/Fokus-Keyword bleiben bis zur manuellen Bearbeitung live mit Gattung/Art/Kultivar synchron, Kategorien haben ein eigenes Überschrift-Feld für den Shop, Varianten-Bild-Galerie zeigt jetzt korrekt allgemeine + spezifische Bilder statt eines Duplikats.',
    details: {
      before: 'SEO-Titel/-Beschreibung wurden nur einmalig automatisch vorgeschlagen und aktualisierten sich nicht mehr, wenn z. B. der Kultivar erst später ergänzt wurde; Fokus-Keyword war rein manuell; Kategorien hatten keine separate Shop-Überschrift; Varianten ohne eigenes Bild zeigten das allgemeine Hauptbild fälschlich doppelt.',
      after: 'SEO-Felder bleiben automatisch synchron, solange sie nicht manuell verändert wurden; Fokus-Keyword wird automatisch als "Gattung Art kaufen" vorgeschlagen; Kategorien haben ein Überschrift-Feld (Default = Kategoriename), das im Shop bei Filterung zentriert in der Toolbar erscheint; Varianten zeigen zuverlässig ihre eigenen Bilder plus die allgemeine Galerie, ohne Duplikate.'
    },
    // Neue Kategorie-Spalte "heading" ist additiv (sicherer Default, per
    // reconcileSchema() automatisch nachgezogen) — bestehende Kategorien/
    // Produkte brauchen keine Migration.
    compatible_with: ['1.3.3', '1.3.2', '1.3.1', '1.3.0', '1.2.1', '1.2.0', '1.1.2', '1.1.1', '1.1.0'],
    migrations: {}
  },
  '1.3.3': {
    keywords: ['bar_image_edit_fix', 'thumb_avif_removed'],
    description: 'Balken-Bild-Bearbeitung zeigt wieder die rohen Balkenwerte (Zentrierung nur noch beim Export), separates 300x300-AVIF-Vorschaubild entfernt.',
    details: {
      before: 'Beim Ziehen eines Balkens bewegte sich der andere optisch mit, da die Zentrierung bereits in der Live-Bearbeitung angewendet wurde. Zusätzlich wurde pro Produkt ein separates 300x300-AVIF-Vorschaubild erzeugt und gepusht.',
      after: 'Die Bearbeitungsansicht zeigt wieder unverändert die rohen Balkenwerte, nur der Export zentriert die Ränder. Das 300x300-AVIF-Vorschaubild wird nicht mehr erzeugt; Produktkarten auf der Website nutzen die native WordPress/WooCommerce-Thumbnail-Größe als Fallback.'
    },
    // Keine Schema-Änderung — Rendering-Fix und Entfernen einer abgeleiteten,
    // nie zwingend benötigten Bild-Variante. Bestehende Produkte brauchen
    // keine Migration.
    compatible_with: ['1.3.2', '1.3.1', '1.3.0', '1.2.1', '1.2.0', '1.1.2', '1.1.1', '1.1.0'],
    migrations: {}
  },
  '1.3.2': {
    keywords: ['bar_image_centering', 'push_completeness', 'variant_management_ui'],
    description: 'Balken-Bilder werden zentriert (symmetrische Ränder bei asymmetrischem Zuschnitt), mehrere bisher nicht gepushte Produktfelder ergänzt, Varianten-Verwaltung nur noch in der Produktübersicht.',
    details: {
      before: 'Balken-Ränder waren bei ungleichen Balkenwerten außermittig; Produkttyp/Einheit/Literangabe/Differenzbesteuerung/Niedrig-Bestand-Einstellungen wurden nie an die Website gepusht; der Varianten-Button existierte sowohl im Produktformular als auch in der Produktübersicht.',
      after: 'Balken-Ränder sind immer symmetrisch, unabhängig von den einzelnen Balkenwerten. Die 6 genannten Felder werden jetzt mitgepusht (und beim Pull zurückgelesen). Varianten werden ausschließlich in der Produktübersicht verwaltet.'
    },
    // Keine Schema-Änderung — nur Rendering-Verhalten, Push-Vollständigkeit
    // und UI-Umbau bestehender Felder. Bestehende Produkte brauchen keine
    // Migration.
    compatible_with: ['1.3.1', '1.3.0', '1.2.1', '1.2.0', '1.1.2', '1.1.1', '1.1.0'],
    migrations: {}
  },
  '1.3.1': {
    keywords: ['common_name', 'pot_size_hint'],
    description: 'Gängiger Name für Produkte/Gattungen/Arten, Topfgrößen-Hinweis auf der Produktseite, diverse Vorschau/Website-Angleichungen.',
    details: {
      before: 'Kein Feld für den gängigen (deutschen) Pflanzennamen; kein Topfgrößen-Hinweis auf der Produktseite; App-Vorschau wich in mehreren Punkten (Temperatur-Skala, Licht/Wasser-Anzeige, Titel-Formatierung) von der echten Produktseite ab.',
      after: 'Neues Feld "Gängiger Name" (kaskadierbar wie Pflegehinweise) wird unter dem Titel angezeigt. Zwischen Preis und Warenkorb erscheint ein Topfgrößen-Hinweis aus der Produkt-/Varianten-Spezifikation. App-Vorschau wurde an die echte Produktseite angeglichen.'
    },
    // Rein additiv (neue Spalte mit sicherem Default, per reconcileSchema()
    // automatisch nachgezogen) — bestehende Produkte brauchen keine
    // Feld-für-Feld-Migration, daher kompatibel statt migrations-Einträge.
    compatible_with: ['1.3.0', '1.2.1', '1.2.0', '1.1.2', '1.1.1', '1.1.0'],
    migrations: {}
  },
  '1.3.0': {
    keywords: ['mainpage_carousel', 'beliebt', 'alle_produkte', 'is_variable', 'variants', 'category_pull'],
    description: 'Homepage-Carousels, automatische "Beliebt"/"Alle Produkte"-Kategorien, variable Produkte mit eigenen Varianten, voller Pull für Kategorien und Varianten.',
    details: {
      before: 'Kategorien-Sync war eine reine Push-Einbahnstraße (App → Website), die Startseite zeigte 3 fest codierte Carousels, und ein Produkt hatte immer genau einen Preis/SKU/Lagerbestand/Bilder-Satz.',
      after: 'Kategorien lassen sich als "Mainpage-Carousel" markieren und erscheinen auf der Startseite (inkl. automatisch berechneter "Beliebt"- und "Alle Produkte"-Kategorie); Kategorien UND Varianten lassen sich jetzt auch von der Website zurückpullen (wp_term_id-Abgleich); Produkte können als "Variabel" markiert werden — SKU, Preis, Lagerbestand und Bilder wandern dann auf einzelne, frei benannte Varianten mit eigenem Bild-Ordner.'
    },
    // Rein additiv (neue Spalten mit sicheren Defaults, per reconcileSchema()
    // automatisch nachgezogen) — bestehende Produkte brauchen keine
    // Feld-für-Feld-Migration, daher kompatibel statt migrations-Einträge.
    compatible_with: ['1.2.1', '1.2.0', '1.1.2', '1.1.1', '1.1.0'],
    migrations: {}
  },
  '1.2.1': {
    keywords: ['gattung_art_blueprints'],
    description: 'Gattung und Art sind jetzt wiederverwendbare Blueprint-Entitäten mit vererbbaren Feldern',
    details: {
      before: 'Gattung und Art waren reiner Freitext ohne Verknüpfung zwischen Produkten.',
      after: 'Gattung und Art können als eigene Datensätze mit Feldern (Einheit, Steuer, Lager, Pflege, Beschreibungen) angelegt werden. Produkte verknüpfen sich mit einer Gattung/Art und übernehmen deren ausgefüllte Felder, bleiben aber frei editierbar. Bearbeitet man die Blaupause später, werden verknüpfte, nicht überschriebene Felder automatisch mit aktualisiert.'
    },
    compatible_with: ['1.2.0', '1.1.2', '1.1.1', '1.1.0'],
    migrations: {
      '1.2.0': (product) => ({
        ...product,
        gattung_id: product.gattung_id ?? null,
        art_id: product.art_id ?? null,
        blueprint_links: product.blueprint_links ?? '{}'
      }),
      '1.1.2': (product) => ({ ...product, gattung_id: null, art_id: null, blueprint_links: '{}' }),
      '1.1.1': (product) => ({ ...product, gattung_id: null, art_id: null, blueprint_links: '{}' }),
      '1.1.0': (product) => ({ ...product, gattung_id: null, art_id: null, blueprint_links: '{}' }),
    }
  },
  '1.2.0': {
    keywords: ['product_specs'],
    description: 'Produktspezifikationen als wiederverwendbare Einheiten (ähnlich Tags)',
    details: {
      before: 'Größe und Gewicht waren direkt in der products Tabelle als einzelne Felder (weight, length, width, height) gespeichert.',
      after: 'Spezifikationen sind nun wiederverwendbare Einheiten in einer separaten Tabelle mit Topfgröße, Form (rund/eckig), Gewicht (g/kg) und Maßen (H×B). Produkte können mehrere Spezifikationen haben, ähnlich wie Tags.'
    },
    compatible_with: ['1.1.2', '1.1.1', '1.1.0'],
    migrations: {
      '1.1.2': (product) => {
        // Alte weight/length/width/height Felder bleiben erhalten für Kompatibilität
        // Neue Spezifikationen werden separat verwaltet
        return product
      },
      '1.1.1': (product) => product,
      '1.1.0': (product) => product
    }
  },
  '1.1.2': {
    keywords: ['initial_version'],
    description: 'Initiale Version mit vollem Produktschema',
    details: {
      before: 'Keine vorherige Version.',
      after: 'Volles Produktschema mit allen Feldern für Pflanzen, Substrate, Pflegehinweise, Versandmaße, etc. app_version Feld wurde zur Nachverfolgung hinzugefügt.'
    },
    compatible_with: ['1.1.1', '1.1.0'],
    // Migration von 1.1.1 zu 1.1.2 - keine Änderungen, nur Kompatibilität
    migrations: {
      '1.1.1': (product) => product, // Keine Änderungen nötig
      '1.1.0': (product) => product
    }
  },
  '1.1.1': {
    keywords: ['care_temp_min', 'care_temp_max'],
    description: 'Temperaturbereiche für Pflegehinweise hinzugefügt',
    details: {
      before: 'Pflegehinweise hatten nur Licht, Wasser, Winter und Winterhärte.',
      after: 'Neue Felder care_temp_min und care_temp_max für Temperaturbereiche hinzugefügt.'
    },
    compatible_with: ['1.1.0'],
    migrations: {
      '1.1.0': (product) => {
        // care_temp_min und care_temp_max wurden neu hinzugefügt
        // Alte Produkte haben diese Felder nicht - leer lassen
        return {
          ...product,
          care_temp_min: product.care_temp_min ?? null,
          care_temp_max: product.care_temp_max ?? null
        }
      }
    }
  },
  '1.1.0': {
    keywords: ['shipping_dimensions'],
    description: 'Versandmaße separat von Produktmaßen',
    details: {
      before: 'Nur Produktmaße (length, width, height) vorhanden.',
      after: 'Versandmaße (shipping_length, shipping_width, shipping_height) separat von Produktmaßen hinzugefügt.'
    },
    compatible_with: ['1.0.0'],
    migrations: {
      '1.0.0': (product) => {
        // Versandmaße wurden von Produktmaßen getrennt
        // Alte Produkte: shipping_* Felder auf length/width/height kopieren
        return {
          ...product,
          shipping_length: product.shipping_length ?? product.length ?? null,
          shipping_width: product.shipping_width ?? product.width ?? null,
          shipping_height: product.shipping_height ?? product.height ?? null
        }
      }
    }
  },
  '1.0.0': {
    keywords: ['base_schema'],
    description: 'Basis-Schema ohne Versionierung',
    details: {
      before: 'Keine vorherige Version.',
      after: 'Basis-Schema mit grundlegenden Produktfeldern ohne Versionierungssystem.'
    },
    compatible_with: [],
    migrations: {}
  }
}

/**
 * Prüft ob zwei Versionen kompatibel sind
 */
export function areVersionsCompatible(fromVersion, toVersion) {
  if (fromVersion === toVersion) return true
  
  const toVersionData = VERSIONS[toVersion]
  if (!toVersionData) return false
  
  return toVersionData.compatible_with?.includes(fromVersion) || false
}

/**
 * Findet alle Produkte die migriert werden müssen
 * Gibt eine Liste von Produkten mit den geänderten Feldern zurück
 */
export function detectMigrationNeeded(products, currentVersion) {
  const migrationsNeeded = []
  
  for (const product of products) {
    const productVersion = product.app_version || '1.0.0' // Default wenn nicht gesetzt
    
    if (productVersion === currentVersion) continue
    
    // Prüfe ob Kompatibilität besteht
    if (areVersionsCompatible(productVersion, currentVersion)) {
      continue // Kompatibel, keine Migration nötig
    }
    
    // Finde die geänderten Felder
    const changes = getVersionChanges(productVersion, currentVersion)
    if (changes.length > 0) {
      migrationsNeeded.push({
        product,
        fromVersion: productVersion,
        toVersion: currentVersion,
        changes,
        canMigrate: hasMigrationPath(productVersion, currentVersion)
      })
    }
  }
  
  return migrationsNeeded
}

/**
 * Gibt die Liste der geänderten Keywords zwischen zwei Versionen zurück
 */
export function getVersionChanges(fromVersion, toVersion) {
  const fromData = VERSIONS[fromVersion]
  const toData = VERSIONS[toVersion]
  
  if (!fromData || !toData) return []
  
  // Alle Keywords der Zielversion, die nicht in der Quellversion sind
  const fromKeywords = new Set(fromData.keywords || [])
  const newKeywords = (toData.keywords || []).filter(k => !fromKeywords.has(k))
  
  return newKeywords
}

/**
 * Prüft ob ein Migrationspfad zwischen zwei Versionen existiert
 */
export function hasMigrationPath(fromVersion, toVersion) {
  const toData = VERSIONS[toVersion]
  if (!toData) return false
  
  return !!(toData.migrations && toData.migrations[fromVersion])
}

/**
 * Migriert ein Produkt von einer Version zu einer anderen
 */
export function migrateProduct(product, fromVersion, toVersion) {
  const toData = VERSIONS[toVersion]
  if (!toData || !toData.migrations) {
    return product // Keine Migration verfügbar
  }
  
  const migrationFn = toData.migrations[fromVersion]
  if (!migrationFn) {
    return product // Keine spezifische Migration
  }
  
  try {
    const migrated = migrationFn(product)
    return {
      ...migrated,
      app_version: toVersion
    }
  } catch (e) {
    console.error(`Migration fehlgeschlagen von ${fromVersion} zu ${toVersion}:`, e)
    return product // Original bei Fehler zurückgeben
  }
}

/**
 * Gibt die aktuelle App-Version aus package.json zurück
 */
export function getCurrentAppVersion() {
  return APP_VERSION
}
