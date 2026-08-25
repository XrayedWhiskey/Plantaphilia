<?php
/**
 * Template Name: Produktliste (Admin)
 */

get_header();

// Nur für Admins zugänglich
if (!current_user_can('manage_options')) {
    echo '<div style="padding: 40px; text-align: center;">';
    echo '<h2>Zugriff verweigert</h2>';
    echo '<p>Diese Seite ist nur für Administratoren zugänglich.</p>';
    echo '<a href="' . home_url() . '" class="button">Zurück zur Startseite</a>';
    echo '</div>';
    get_footer();
    exit;
}
?>

<div style="display: flex; justify-content: center; width: 100%;">
    <div style="width: 100%; max-width: 1200px; padding: 0 20px;">
        
        <div style="display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 30px;">
            <div>
                <h1 style="margin: 0 0 6px 0; color: var(--creme); font-family: var(--serif-display); font-size: 48px;">Produktliste</h1>
                <p style="color: var(--creme-dim); margin: 0; font-size: 14px;">Übersicht aller Produkte mit Beständen und Preisen</p>
            </div>
            <div style="display:flex;gap:10px;align-items:center;">
                <a href="<?php echo get_permalink(get_page_by_path('newsletter')); ?>" style="padding:10px 16px;background:var(--plum);color:var(--creme);text-decoration:none;border-radius:2px;font-size:11px;font-weight:700;letter-spacing:0.14em;text-transform:uppercase;white-space:nowrap;">📧 Newsletter</a>
                <a href="<?php echo get_permalink(get_page_by_path('neues-produkt')); ?>" style="padding:12px 20px;background:var(--plum-hot);color:var(--creme);text-decoration:none;border-radius:2px;font-size:11px;font-weight:700;letter-spacing:0.14em;text-transform:uppercase;white-space:nowrap;">+ Produkt hinzufügen</a>
            </div>
        </div>

        <!-- ── Page Tabs ──────────────────────────────────────────────── -->
        <div style="display:flex;gap:0;border-bottom:2px solid var(--border-thin);margin-top:12px;margin-bottom:0;">
            <button class="pa-ptab active" onclick="showPageTab('produkte')" id="pptab-produkte">Produktliste</button>
            <button class="pa-ptab" onclick="showPageTab('rabattcodes')" id="pptab-rabattcodes">Rabattcodes</button>
            <button class="pa-ptab" onclick="showPageTab('social-deals')" id="pptab-social-deals">Social Deals</button>
            <button class="pa-ptab" onclick="showPageTab('migration')" id="pptab-migration">Migration</button>
        </div>

        <!-- ── Tab: Produktliste ────────────────────────────────────────── -->
        <div id="pa-tc-produkte" class="pa-tab-content">

        <!-- Fuzzy Search und Toggle -->
        <div style="margin-bottom: 30px; max-width: 700px; margin-left: auto; margin-right: auto; display: flex; gap: 20px; align-items: center;">
            <button id="bulk-sale-btn" style="padding: 12px 16px; background: var(--bg-surface); color: var(--creme); border: 1px solid var(--border-thin); border-radius: 2px; cursor: pointer; font-size: 11px; font-weight: 700; letter-spacing: 0.14em; text-transform: uppercase;">Sale einschalten</button>
            <input type="text" id="product-search" placeholder="Produkte suchen..." 
                   style="flex: 1; padding: 12px 16px; font-size: 14px; border: 1px solid var(--border-thin); border-radius: 2px; background: var(--bg-surface); color: var(--creme);">
            <div style="display: flex; align-items: center; gap: 10px;">
                <label style="font-size: 14px; font-weight: 500; color: var(--creme-dim);">Nach Angeboten gruppieren</label>
                <label class="switch">
                    <input type="checkbox" id="group-by-offer-toggle">
                    <span class="slider"></span>
                </label>
            </div>
        </div>
        
        <style>
        .switch {
            position: relative;
            display: inline-block;
            width: 50px;
            height: 24px;
        }
        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }
        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: var(--bg-inky);
            transition: .4s;
            border-radius: 24px;
            border: 1px solid var(--border-thin);
        }
        .slider:before {
            position: absolute;
            content: "";
            height: 18px;
            width: 18px;
            left: 3px;
            bottom: 3px;
            background-color: var(--creme-dim);
            transition: .4s;
            border-radius: 50%;
        }
        input:checked + .slider {
            background-color: var(--plum);
            border-color: var(--plum-hot);
        }
        input:checked + .slider:before {
            transform: translateX(26px);
            background-color: var(--creme);
        }
        .sell-checkbox {
            position: relative;
            display: inline-block;
            width: 40px;
            height: 20px;
        }
        .sell-checkbox input {
            opacity: 0;
            width: 0;
            height: 0;
        }
        .sell-checkbox .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: var(--bg-inky);
            transition: .4s;
            border-radius: 20px;
            border: 1px solid var(--border-thin);
        }
        .sell-checkbox .slider:before {
            position: absolute;
            content: "";
            height: 14px;
            width: 14px;
            left: 3px;
            bottom: 3px;
            background-color: var(--creme-dim);
            transition: .4s;
            border-radius: 50%;
        }
        .sell-checkbox input:checked + .slider {
            background-color: var(--plum-hot);
            border-color: var(--plum);
        }
        .sell-checkbox input:checked + .slider:before {
            transform: translateX(20px);
            background-color: var(--creme);
        }

        /* ── Page Tabs ── */
        .pa-ptab { padding:10px 20px; background:none; border:none; border-bottom:3px solid transparent; color:var(--creme-dim); font-size:11px; font-weight:700; letter-spacing:.12em; text-transform:uppercase; cursor:pointer; transition:color .15s,border-color .15s; margin-bottom:-2px; }
        .pa-ptab:hover { color:var(--creme); }
        .pa-ptab.active { color:var(--creme); border-bottom-color:var(--plum-hot); }
        .pa-tab-content { padding-top:24px; }

        /* ── Shared table / form styles (Rabattcodes + Social Deals) ── */
        .rc-th { padding:10px 12px; text-align:left; border-bottom:2px solid var(--border-thin); color:var(--creme-dim); font-size:11px; font-weight:700; letter-spacing:.08em; text-transform:uppercase; }
        .rc-label { display:block; font-size:11px; font-weight:700; color:var(--creme-dim); margin-bottom:5px; letter-spacing:.06em; text-transform:uppercase; }
        .rc-inp { width:100%; padding:8px 10px; border:1px solid var(--border-thin); border-radius:2px; background:var(--bg-inky); color:var(--creme); font-size:13px; box-sizing:border-box; }
        .rc-inp:focus { outline:none; border-color:var(--plum); }
        select.rc-inp { appearance:auto; }

        /* ── Social deal filter buttons ── */
        .sd-filter { padding:6px 14px; background:var(--bg-surface); color:var(--creme-dim); border:1px solid var(--border-thin); border-radius:2px; cursor:pointer; font-size:11px; font-weight:700; letter-spacing:.1em; text-transform:uppercase; transition:all .15s; }
        .sd-filter.active { background:var(--plum); color:var(--creme); border-color:var(--plum); }
        </style>
        
        <!-- Produktliste -->
        <div id="product-list-container">
            <table id="product-table" style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: var(--bg-surface);">
                        <th data-sort="sellable" style="padding: 12px; text-align: left; border-bottom: 2px solid var(--border-thin); cursor: pointer; color: var(--creme);">Verkaufen <span class="sort-arrow"></span></th>
                        <th style="padding: 12px; text-align: left; border-bottom: 2px solid var(--border-thin); color: var(--creme);">Bild</th>
                        <th data-sort="name" style="padding: 12px; text-align: left; border-bottom: 2px solid var(--border-thin); cursor: pointer; color: var(--creme);">Name <span class="sort-arrow"></span></th>
                        <th data-sort="stock" style="padding: 12px; text-align: right; border-bottom: 2px solid var(--border-thin); cursor: pointer; color: var(--creme);">Bestand <span class="sort-arrow"></span></th>
                        <th data-sort="in_progress" style="padding: 12px; text-align: right; border-bottom: 2px solid var(--border-thin); cursor: pointer; color: var(--creme);">In Bearbeitung <span class="sort-arrow"></span></th>
                        <th data-sort="available" style="padding: 12px; text-align: right; border-bottom: 2px solid var(--border-thin); cursor: pointer; color: var(--creme);">Verfügbar <span class="sort-arrow"></span></th>
                        <th data-sort="price" style="padding: 12px; text-align: right; border-bottom: 2px solid var(--border-thin); cursor: pointer; color: var(--creme);">Preis <span class="sort-arrow"></span></th>
                        <th style="padding: 12px; text-align: left; border-bottom: 2px solid var(--border-thin); color: var(--creme);">Angebot</th>
                        <th style="padding: 12px; text-align: center; border-bottom: 2px solid var(--border-thin); color: var(--creme);">Aktionen</th>
                    </tr>
                </thead>
                <tbody id="product-table-body">
                    <tr>
                        <td colspan="9" style="padding: 40px; text-align: center; color: var(--creme-dim);">Lade Produkte...</td>
                    </tr>
                </tbody>
            </table>
        </div>
        </div><!-- /pa-tc-produkte -->

        <!-- ── Tab: Rabattcodes ──────────────────────────────────────────── -->
        <div id="pa-tc-rabattcodes" class="pa-tab-content" style="display:none;padding-top:24px;">

            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;">
                <div>
                    <h2 style="margin:0 0 4px;color:var(--creme);font-family:var(--serif-display);font-size:30px;">Rabattcodes</h2>
                    <p style="margin:0;color:var(--creme-dim);font-size:13px;">WooCommerce Coupons verwalten</p>
                </div>
                <button onclick="rcOpenForm(0)" style="padding:10px 18px;background:var(--plum-hot);color:var(--creme);border:none;border-radius:2px;cursor:pointer;font-size:11px;font-weight:700;letter-spacing:.14em;text-transform:uppercase;">+ Neuer Code</button>
            </div>

            <!-- Coupon Form -->
            <div id="rc-form-wrap" style="display:none;background:var(--bg-surface);border:1px solid var(--border-thin);border-radius:4px;padding:24px;margin-bottom:24px;">
                <h3 style="margin:0 0 20px;color:var(--creme);font-size:16px;" id="rc-form-title">Neuer Rabattcode</h3>
                <input type="hidden" id="rc-coupon-id" value="0">
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                    <div>
                        <label class="rc-label">Code</label>
                        <input type="text" id="rc-code" class="rc-inp" placeholder="z.B. SOMMER20" style="text-transform:uppercase;">
                    </div>
                    <div>
                        <label class="rc-label">Rabatt-Typ</label>
                        <select id="rc-type" class="rc-inp">
                            <option value="percent">Prozent (%)</option>
                            <option value="fixed_cart">Fixer Betrag – Warenkorb (€)</option>
                            <option value="fixed_product">Fixer Betrag – pro Produkt (€)</option>
                        </select>
                    </div>
                    <div>
                        <label class="rc-label">Betrag</label>
                        <input type="number" id="rc-amount" min="0" step="0.01" class="rc-inp" placeholder="z.B. 10">
                    </div>
                    <div>
                        <label class="rc-label">Max. Rabattbetrag (€) <span style="font-weight:400;text-transform:none;">(0 = kein Limit)</span></label>
                        <input type="number" id="rc-max-amount" min="0" step="0.01" class="rc-inp" placeholder="0">
                    </div>
                    <div>
                        <label class="rc-label">Mindestbestellwert (€) <span style="font-weight:400;text-transform:none;">(0 = kein Minimum)</span></label>
                        <input type="number" id="rc-min-amount" min="0" step="0.01" class="rc-inp" placeholder="0">
                    </div>
                    <div>
                        <label class="rc-label">Mindestanzahl Artikel <span style="font-weight:400;text-transform:none;">(0 = kein Minimum)</span></label>
                        <input type="number" id="rc-min-qty" min="0" step="1" class="rc-inp" placeholder="0">
                    </div>
                    <div>
                        <label class="rc-label">Gesamt verwendbar <span style="font-weight:400;text-transform:none;">(0 = unbegrenzt)</span></label>
                        <input type="number" id="rc-usage-limit" min="0" step="1" class="rc-inp" placeholder="0">
                    </div>
                    <div>
                        <label class="rc-label">Pro Benutzer verwendbar <span style="font-weight:400;text-transform:none;">(0 = unbegrenzt)</span></label>
                        <input type="number" id="rc-usage-per-user" min="0" step="1" class="rc-inp" placeholder="1">
                    </div>
                    <div>
                        <label class="rc-label">Gültig ab</label>
                        <input type="date" id="rc-date-start" class="rc-inp">
                    </div>
                    <div>
                        <label class="rc-label">Gültig bis <span style="font-weight:400;text-transform:none;">(leer = kein Ablaufdatum)</span></label>
                        <input type="date" id="rc-date-expires" class="rc-inp">
                    </div>
                </div>
                <div style="margin-top:16px;display:grid;grid-template-columns:1fr;gap:12px;">
                    <div>
                        <label class="rc-label">Nur für Produkt-IDs <span style="font-weight:400;text-transform:none;">(komma-getrennt, leer = alle Produkte)</span></label>
                        <input type="text" id="rc-products" class="rc-inp" placeholder="z.B. 123, 456">
                    </div>
                    <div>
                        <label class="rc-label">Nur für Kategorie-IDs <span style="font-weight:400;text-transform:none;">(komma-getrennt, leer = alle Kategorien)</span></label>
                        <input type="text" id="rc-categories" class="rc-inp" placeholder="z.B. 7, 12">
                    </div>
                </div>
                <div style="margin-top:16px;display:flex;flex-wrap:wrap;gap:20px;">
                    <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;color:var(--creme);">
                        <input type="checkbox" id="rc-exclude-sale"> Rabattierte Produkte ausschließen
                    </label>
                    <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;color:var(--creme);">
                        <input type="checkbox" id="rc-new-customers"> Nur für Neukunden
                    </label>
                    <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;color:var(--creme);">
                        <input type="checkbox" id="rc-individual"> Nicht kombinierbar mit anderen Codes
                    </label>
                    <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;color:var(--creme);">
                        <input type="checkbox" id="rc-free-shipping"> Kostenloser Versand
                    </label>
                </div>
                <div id="rc-form-error" style="display:none;margin-top:12px;padding:8px 12px;background:#3a1515;color:#ff9090;border-radius:3px;font-size:13px;"></div>
                <div style="margin-top:20px;display:flex;gap:10px;">
                    <button onclick="rcSave()" id="rc-save-btn" style="padding:10px 20px;background:var(--plum);color:var(--creme);border:none;border-radius:2px;cursor:pointer;font-size:12px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;">Speichern</button>
                    <button onclick="rcCloseForm()" style="padding:10px 20px;background:var(--bg-raised);color:var(--creme-dim);border:1px solid var(--border-thin);border-radius:2px;cursor:pointer;font-size:12px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;">Abbrechen</button>
                </div>
            </div>

            <!-- Coupons table -->
            <table style="width:100%;border-collapse:collapse;">
                <thead>
                    <tr style="background:var(--bg-surface);">
                        <th class="rc-th">Code</th>
                        <th class="rc-th">Typ</th>
                        <th class="rc-th">Betrag</th>
                        <th class="rc-th">Gültig bis</th>
                        <th class="rc-th">Verwendet / Limit</th>
                        <th class="rc-th">Aktionen</th>
                    </tr>
                </thead>
                <tbody id="rc-table-body">
                    <tr><td colspan="6" style="padding:30px;text-align:center;color:var(--creme-dim);">Lade Rabattcodes…</td></tr>
                </tbody>
            </table>

        </div><!-- /pa-tc-rabattcodes -->

        <!-- ── Tab: Social Deals ──────────────────────────────────────────── -->
        <div id="pa-tc-social-deals" class="pa-tab-content" style="display:none;padding-top:24px;">

            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;">
                <div>
                    <h2 style="margin:0 0 4px;color:var(--creme);font-family:var(--serif-display);font-size:30px;">Social Deals</h2>
                    <p style="margin:0;color:var(--creme-dim);font-size:13px;">Kunden posten, erhalten Rabattcodes</p>
                </div>
                <button onclick="sdToggleConfig()" style="padding:10px 18px;background:var(--bg-surface);color:var(--creme);border:1px solid var(--border-thin);border-radius:2px;cursor:pointer;font-size:11px;font-weight:700;letter-spacing:.14em;text-transform:uppercase;">Plattformen konfigurieren</button>
            </div>

            <!-- Platform config (hidden) -->
            <div id="sd-config-wrap" style="display:none;background:var(--bg-surface);border:1px solid var(--border-thin);border-radius:4px;padding:24px;margin-bottom:24px;">
                <h3 style="margin:0 0 4px;color:var(--creme);font-size:16px;">Plattform-Konfiguration</h3>
                <p style="margin:0 0 16px;color:var(--creme-dim);font-size:12px;">Handle = der Account der getagged werden muss. Prozent-Bereich = möglicher Rabatt den du zuweisen kannst.</p>
                <div style="display:grid;grid-template-columns:140px 1fr 220px;gap:0;margin-bottom:8px;padding:0 0 6px;border-bottom:1px solid var(--border-thin);">
                    <span style="font-size:11px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:var(--creme-dim);">Plattform</span>
                    <span style="font-size:11px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:var(--creme-dim);">@ Handle</span>
                    <span style="font-size:11px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:var(--creme-dim);">% Bereich (min – max)</span>
                </div>
                <div id="sd-platforms-config"></div>
                <div id="sd-config-error" style="display:none;margin-top:10px;padding:8px 12px;background:#3a1515;color:#ff9090;border-radius:3px;font-size:13px;"></div>
                <div style="margin-top:18px;display:flex;gap:10px;">
                    <button onclick="sdSaveConfig()" style="padding:10px 20px;background:var(--plum);color:var(--creme);border:none;border-radius:2px;cursor:pointer;font-size:12px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;">Speichern</button>
                    <button onclick="sdToggleConfig()" style="padding:10px 20px;background:var(--bg-raised);color:var(--creme-dim);border:1px solid var(--border-thin);border-radius:2px;cursor:pointer;font-size:12px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;">Schließen</button>
                </div>
            </div>

            <!-- Deals filter -->
            <div style="display:flex;gap:6px;margin-bottom:16px;">
                <button class="sd-filter active" data-st="" onclick="sdSetFilter(this,'')">Alle</button>
                <button class="sd-filter" data-st="pending" onclick="sdSetFilter(this,'pending')">Offen</button>
                <button class="sd-filter" data-st="approved" onclick="sdSetFilter(this,'approved')">Genehmigt</button>
                <button class="sd-filter" data-st="rejected" onclick="sdSetFilter(this,'rejected')">Abgelehnt</button>
            </div>

            <!-- Deals table -->
            <table style="width:100%;border-collapse:collapse;">
                <thead>
                    <tr style="background:var(--bg-surface);">
                        <th class="rc-th">Benutzer</th>
                        <th class="rc-th">Bestellung</th>
                        <th class="rc-th">Plattform</th>
                        <th class="rc-th">@ Handle</th>
                        <th class="rc-th">Status</th>
                        <th class="rc-th">Aktionen</th>
                    </tr>
                </thead>
                <tbody id="sd-table-body">
                    <tr><td colspan="6" style="padding:30px;text-align:center;color:var(--creme-dim);">Lade Deals…</td></tr>
                </tbody>
            </table>

        </div><!-- /pa-tc-social-deals -->

        <!-- ── Tab: Migration ─────────────────────────────────────────────── -->
        <div id="pa-tc-migration" class="pa-tab-content" style="display:none;padding-top:24px;max-width:700px;">

            <!-- Export -->
            <div style="margin-bottom:32px;">
                <h3 style="margin:0 0 6px;font-size:16px;">Export</h3>
                <p style="margin:0 0 16px;font-size:13px;color:var(--creme-dim);">Alle Produkte, Bestellungen, Kund:innen und Einstellungen in eine verschlüsselte ZIP-Datei exportieren.</p>
                <div style="display:flex;gap:10px;align-items:flex-end;flex-wrap:wrap;">
                    <div>
                        <label style="display:block;font-size:12px;font-weight:600;color:var(--creme-dim);margin-bottom:4px;text-transform:uppercase;letter-spacing:.05em;">Passwort (mind. 8 Zeichen)</label>
                        <input type="password" id="mig-export-pw" placeholder="Exportpasswort" style="padding:9px 12px;font-size:13px;border:1px solid var(--border-thin);background:var(--bg-surface);color:var(--creme);border-radius:2px;width:220px;">
                    </div>
                    <button type="button" onclick="migDoExport()" style="padding:9px 18px;font-size:13px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;background:var(--plum);color:var(--creme);border:none;border-radius:2px;cursor:pointer;">Export starten</button>
                </div>
                <div id="mig-export-status" style="margin-top:10px;font-size:13px;color:var(--creme-dim);display:none;"></div>
            </div>

            <!-- Import -->
            <div>
                <h3 style="margin:0 0 6px;font-size:16px;">Import</h3>
                <p style="margin:0 0 16px;font-size:13px;color:var(--creme-dim);">Exportierte ZIP-Datei wieder einspielen. Führe erst einen Trocken-Lauf durch, um Konflikte zu prüfen.</p>
                <div style="display:flex;flex-direction:column;gap:10px;max-width:420px;">
                    <div>
                        <label style="display:block;font-size:12px;font-weight:600;color:var(--creme-dim);margin-bottom:4px;text-transform:uppercase;letter-spacing:.05em;">ZIP-Datei</label>
                        <input type="file" id="mig-import-file" accept=".zip" style="font-size:13px;color:var(--creme);">
                    </div>
                    <div>
                        <label style="display:block;font-size:12px;font-weight:600;color:var(--creme-dim);margin-bottom:4px;text-transform:uppercase;letter-spacing:.05em;">Passwort</label>
                        <input type="password" id="mig-import-pw" placeholder="Passwort aus dem Export" style="padding:9px 12px;font-size:13px;border:1px solid var(--border-thin);background:var(--bg-surface);color:var(--creme);border-radius:2px;width:220px;">
                    </div>
                    <div style="display:flex;gap:10px;align-items:center;">
                        <button type="button" onclick="migDryRun()" style="padding:9px 18px;font-size:13px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;background:var(--bg-surface);color:var(--creme);border:1px solid var(--border-thin);border-radius:2px;cursor:pointer;">Trocken-Lauf</button>
                        <button type="button" onclick="migDoImport()" style="padding:9px 18px;font-size:13px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;background:var(--plum);color:var(--creme);border:none;border-radius:2px;cursor:pointer;">Import starten</button>
                    </div>
                </div>
                <div id="mig-import-status" style="margin-top:12px;font-size:13px;color:var(--creme-dim);display:none;white-space:pre-wrap;"></div>
            </div>

        </div><!-- /pa-tc-migration -->

    </div>
</div>

<!-- Edit Modal -->
<!-- ── Bearbeiten Modal (Multi-Panel) ──────────────────────────────────────── -->
<style>
.em-panel { display: none; flex-direction: column; }
.em-panel.active { display: flex; flex: 1; min-height: 0; }
.em-panel-hdr { display: flex; align-items: center; gap: 10px; padding: 16px 22px; border-bottom: 1px solid var(--border-thin); flex-shrink: 0; background: var(--bg-surface); }
.em-back { background: none; border: none; font-size: 20px; cursor: pointer; color: var(--creme-dim); padding: 0; line-height: 1; }
.em-panel-hdr h3 { margin: 0; font-size: 16px; flex: 1; color: var(--creme); }
.em-body { overflow-y: auto; flex: 1; min-height: 0; padding: 0 22px; background: var(--bg-deep); }
.ef-row { display: flex; align-items: center; padding: 11px 0; border-bottom: 1px solid var(--border-hair); gap: 8px; }
.ef-label { min-width: 155px; font-size: 13px; color: var(--creme-dim); font-weight: 500; flex-shrink: 0; }
.ef-display { font-size: 13px; color: var(--creme); flex: 1; }
.ef-input-wrap { display: none; flex: 1; align-items: center; gap: 5px; flex-wrap: wrap; }
.ef-input-wrap.active { display: flex; }
.ef-pencil { background: none; border: 1px solid var(--border-thin); border-radius: 2px; padding: 3px 7px; cursor: pointer; font-size: 12px; color: var(--creme-dim); flex-shrink: 0; }
.ef-pencil:hover { background: var(--bg-raised); }
.ef-inp { padding: 5px 8px; border: 1px solid var(--border-thin); border-radius: 2px; font-size: 13px; background: var(--bg-surface); color: var(--creme); }
.ef-save-btn { padding: 4px 8px; background: var(--plum); color: var(--creme); border: none; border-radius: 2px; cursor: pointer; font-size: 13px; }
.ef-cancel-btn { padding: 4px 8px; background: var(--bg-surface); color: var(--creme); border: 1px solid var(--border-thin); border-radius: 2px; cursor: pointer; font-size: 13px; }
.ef-divider { margin: 10px 0 2px; padding: 6px 0 5px; font-size: 11px; font-weight: 700; color: var(--creme-muted); text-transform: uppercase; letter-spacing: .6px; border-top: 1px solid var(--border-hair); }
.em-nav-row { display: flex; justify-content: space-between; align-items: center; padding: 13px 0; cursor: pointer; border-bottom: 1px solid var(--border-hair); user-select: none; }
.em-nav-row:hover { background: var(--bg-raised); }
.em-nav-row span:first-child { font-size: 14px; font-weight: 500; color: var(--creme); }
.em-nav-row span:last-child { color: var(--creme-muted); font-size: 20px; }
.em-footer { padding: 14px 22px; border-top: 1px solid var(--border-thin); display: flex; gap: 10px; flex-shrink: 0; background: var(--bg-surface); }
.em-img-thumb { position: relative; width: 90px; height: 90px; border-radius: 4px; overflow: hidden; border: 1px solid var(--border-thin); }
.em-img-thumb img { width: 100%; height: 100%; object-fit: cover; }
.em-img-del { position: absolute; top: 3px; right: 3px; background: rgba(0,0,0,.55); color: white; border: none; border-radius: 50%; width: 20px; height: 20px; cursor: pointer; font-size: 11px; display: flex; align-items: center; justify-content: center; padding: 0; }
.em-img-featured { position: absolute; bottom: 3px; left: 3px; background: rgba(0,0,0,.55); color: white; font-size: 9px; padding: 1px 4px; border-radius: 2px; }
.desc-toolbar button { padding: 4px 8px; background: var(--bg-surface); border: 1px solid var(--border-thin); border-radius: 2px; cursor: pointer; font-size: 12px; color: var(--creme); }
.desc-toolbar button:hover { background: var(--bg-raised); }

/* ── CWM ─────────────────────────────────────────────────────────────────── */
#cwm-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,.82); z-index:999999; align-items:center; justify-content:center; }
#cwm-overlay.open { display:flex; }
#cwm-dialog { background:var(--bg-surface); border-radius:6px; padding:24px; width:min(920px,96vw); max-height:94vh; overflow-y:auto; box-shadow:0 8px 40px rgba(0,0,0,.4); border:1px solid var(--border-thin); }
#cwm-dialog h3 { margin:0 0 18px; font-size:16px; color:var(--creme); }
.cwm-body { display:flex; gap:20px; align-items:flex-start; }
#cwm-canvas { display:block; border-radius:3px; cursor:grab; touch-action:none; flex-shrink:0; }
#cwm-canvas.dragging { cursor:grabbing; }
.cwm-controls { flex:1; min-width:180px; }
.cwm-ctrl-label { font-size:12px; font-weight:600; color:var(--creme-dim); text-transform:uppercase; letter-spacing:.5px; margin:0 0 8px; }
.cwm-ctrl-label:not(:first-child) { margin-top:18px; }
#cwm-zoom { width:100%; margin:4px 0 0; }
.cwm-checkbox-row { display:flex; align-items:center; gap:8px; margin-bottom:8px; font-size:14px; color:var(--creme); }
.cwm-hint { font-size:12px; color:var(--creme-muted); line-height:1.4; margin-top:6px; }
.cwm-btns { display:flex; gap:10px; margin-top:24px; }
#cwm-upload-progress { font-size:13px; color:var(--creme-dim); margin-top:8px; display:none; }
</style>

<div id="edit-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); z-index: 10000; align-items: center; justify-content: center;">
    <div style="background: var(--bg-surface); border-radius: 8px; width: 92%; max-width: 520px; height: 88vh; max-height: 700px; display: flex; flex-direction: column; overflow: hidden; border: 1px solid var(--border-thin);">

        <!-- PANEL: Hauptansicht -->
        <div id="edit-panel-main" class="em-panel active">
            <div class="em-panel-hdr" style="justify-content: space-between;">
                <h3 id="edit-modal-title" style="margin:0;font-size:17px;color:var(--creme);">Produkt bearbeiten</h3>
                <button onclick="closeEditModal()" style="background:none;border:none;font-size:22px;cursor:pointer;color:var(--creme-dim);line-height:1;">✕</button>
            </div>
            <div class="em-body" style="padding-top: 4px;">
                <div id="edit-modal-loading" style="padding:30px;text-align:center;color:var(--creme-dim);">Laden…</div>
                <div id="edit-fields-list" style="display:none;"></div>
                <div id="edit-modal-nav" style="display:none; border-top: 1px solid var(--border-thin); margin-top: 4px;">
                    <div class="em-nav-row" onclick="openImagesPanel()">
                        <span>Bilder</span><span>›</span>
                    </div>
                    <div class="em-nav-row" onclick="openDescriptionPanel()">
                        <span>Beschreibung</span><span>›</span>
                    </div>
                    <div class="em-nav-row" onclick="openStockPanel()">
                        <span>Stock verwalten</span><span>›</span>
                    </div>
                    <div class="em-nav-row" onclick="openVariantPanel()">
                        <span>Varianten verbinden</span><span>›</span>
                    </div>
                    <div class="em-nav-row" onclick="openTagsPanel()">
                        <span>Tags</span><span>›</span>
                    </div>
                </div>
            </div>
            <div id="edit-modal-actions" class="em-footer" style="display:none;">
                <button onclick="openOfferPanel()" style="flex:1;padding:10px;background:var(--plum);color:var(--creme);border:none;border-radius:2px;cursor:pointer;font-size:11px;font-weight:700;letter-spacing:0.14em;text-transform:uppercase;">Angebot erstellen</button>
                <button onclick="openPastSalesPanel()" style="flex:1;padding:10px;background:var(--bg-raised);color:var(--creme);border:1px solid var(--border-thin);border-radius:2px;cursor:pointer;font-size:11px;font-weight:700;letter-spacing:0.14em;text-transform:uppercase;">Vergangene Sales</button>
            </div>
        </div>

        <!-- PANEL: Angebot erstellen (gleiche IDs wie bisher) -->
        <div id="edit-panel-offer" class="em-panel">
            <div class="em-panel-hdr">
                <button class="em-back" onclick="switchEditPanel('main')">‹</button>
                <h3>Angebot erstellen</h3>
            </div>
            <div class="em-body" style="padding-top:10px;padding-bottom:10px;">
                <div style="margin-bottom:14px;">
                    <label style="display:block;margin-bottom:5px;font-weight:500;font-size:13px;color:var(--creme);">Bestand</label>
                    <input type="number" id="modal-stock" style="width:100%;padding:9px;border:1px solid var(--border-thin);border-radius:2px;box-sizing:border-box;background:var(--bg-surface);color:var(--creme);" min="0">
                </div>
                <div style="margin-bottom:14px;">
                    <label style="display:block;margin-bottom:5px;font-weight:500;font-size:13px;color:var(--creme);">Preis (€)</label>
                    <input type="number" id="modal-price" step="0.01" style="width:100%;padding:9px;border:1px solid var(--border-thin);border-radius:2px;box-sizing:border-box;background:var(--bg-surface);color:var(--creme);" min="0">
                </div>
                <div style="margin-bottom:14px;">
                    <label style="font-size:13px;color:var(--creme);"><input type="checkbox" id="modal-has-offer"> Angebot aktiv</label>
                </div>
                <div id="offer-fields" style="display:none;">
                    <div style="margin-bottom:14px;">
                        <label style="display:block;margin-bottom:5px;font-weight:500;font-size:13px;">Preistyp</label>
                        <div style="display:flex;align-items:center;gap:10px;">
                            <span style="font-size:13px;">€</span>
                            <label style="position:relative;display:inline-block;width:46px;height:24px;">
                                <input type="checkbox" id="modal-price-type-toggle" style="opacity:0;width:0;height:0;">
                                <span style="position:absolute;cursor:pointer;top:0;left:0;right:0;bottom:0;background:#ccc;transition:.3s;border-radius:24px;"></span>
                                <span style="position:absolute;height:18px;width:18px;left:3px;bottom:3px;background:white;transition:.3s;border-radius:50%;"></span>
                            </label>
                            <span style="font-size:13px;">%</span>
                        </div>
                    </div>
                    <div id="euro-price-field" style="margin-bottom:14px;">
                        <label style="display:block;margin-bottom:5px;font-weight:500;font-size:13px;">Angebotspreis (€)</label>
                        <input type="number" id="modal-sale-price" step="0.01" style="width:100%;padding:9px;border:1px solid #ddd;border-radius:4px;box-sizing:border-box;" min="0">
                    </div>
                    <div id="percent-price-field" style="margin-bottom:14px;display:none;">
                        <label style="display:block;margin-bottom:5px;font-weight:500;font-size:13px;">Rabatt (%)</label>
                        <input type="number" id="modal-sale-percent" step="1" style="width:100%;padding:9px;border:1px solid #ddd;border-radius:4px;box-sizing:border-box;" min="0" max="100" placeholder="z.B. 20">
                    </div>
                    <div style="margin-bottom:14px;">
                        <label style="font-size:13px;"><input type="checkbox" id="modal-show-old-price"> Alten Preis anzeigen</label>
                    </div>
                    <div style="margin-bottom:14px;">
                        <label style="display:block;margin-bottom:5px;font-weight:500;font-size:13px;">Angebotsstart</label>
                        <button type="button" id="modal-offer-start-btn" style="padding:7px 12px;background:#f5f5f5;border:1px solid #ddd;border-radius:4px;cursor:pointer;font-size:13px;">Startdatum setzen</button>
                        <div id="modal-offer-start-display" style="margin-top:5px;font-size:12px;color:#666;"></div>
                    </div>
                    <div style="margin-bottom:14px;">
                        <label style="font-size:13px;"><input type="checkbox" id="modal-time-limited"> Zeitlich begrenzt</label>
                    </div>
                    <div id="time-limit-fields" style="display:none;padding-left:18px;margin-bottom:14px;">
                        <div style="margin-bottom:12px;">
                            <label style="display:block;margin-bottom:5px;font-weight:500;font-size:13px;">Begrenzungstyp</label>
                            <div style="display:flex;align-items:center;gap:10px;">
                                <span style="font-size:13px;">Bis Datum</span>
                                <label style="position:relative;display:inline-block;width:46px;height:24px;">
                                    <input type="checkbox" id="modal-time-limit-toggle" style="opacity:0;width:0;height:0;">
                                    <span style="position:absolute;cursor:pointer;top:0;left:0;right:0;bottom:0;background:#ccc;transition:.3s;border-radius:24px;"></span>
                                    <span style="position:absolute;height:18px;width:18px;left:3px;bottom:3px;background:white;transition:.3s;border-radius:50%;"></span>
                                </label>
                                <span style="font-size:13px;">Für Zeit</span>
                            </div>
                        </div>
                        <div id="date-field" style="margin-bottom:12px;">
                            <label style="display:block;margin-bottom:4px;font-weight:500;font-size:13px;">Datum (TT.MM.JJJJ)</label>
                            <input type="text" id="modal-time-limit-date" style="width:100%;padding:9px;border:1px solid #ddd;border-radius:4px;box-sizing:border-box;" placeholder="z.B. 10.05.2026">
                            <label style="display:block;margin:8px 0 4px;font-weight:500;font-size:13px;">Uhrzeit (HH:MM)</label>
                            <input type="time" id="modal-time-limit-time" style="width:100%;padding:9px;border:1px solid #ddd;border-radius:4px;box-sizing:border-box;">
                        </div>
                        <div id="duration-field" style="display:none;margin-bottom:12px;">
                            <label style="display:block;margin-bottom:4px;font-weight:500;font-size:13px;">Dauer</label>
                            <div style="display:flex;gap:8px;">
                                <div style="flex:1;"><label style="display:block;font-size:11px;color:#888;margin-bottom:3px;">Tage</label><input type="number" id="modal-time-limit-days" style="width:100%;padding:8px;border:1px solid #ddd;border-radius:4px;box-sizing:border-box;" min="0" placeholder="0"></div>
                                <div style="flex:1;"><label style="display:block;font-size:11px;color:#888;margin-bottom:3px;">Stunden</label><input type="number" id="modal-time-limit-hours" style="width:100%;padding:8px;border:1px solid #ddd;border-radius:4px;box-sizing:border-box;" min="0" placeholder="0"></div>
                                <div style="flex:1;"><label style="display:block;font-size:11px;color:#888;margin-bottom:3px;">Minuten</label><input type="number" id="modal-time-limit-minutes" style="width:100%;padding:8px;border:1px solid #ddd;border-radius:4px;box-sizing:border-box;" min="0" placeholder="0"></div>
                            </div>
                        </div>
                        <label style="font-size:13px;"><input type="checkbox" id="modal-show-end-date"> Dem Kunden anzeigen bis wann reduziert ist</label>
                    </div>
                </div>
                <div style="display:flex;gap:10px;margin-top:16px;padding-bottom:4px;">
                    <button id="save-edit" style="flex:1;padding:11px;background:#333;color:white;border:none;border-radius:4px;cursor:pointer;font-size:13px;">Speichern</button>
                    <button id="cancel-edit" style="flex:1;padding:11px;background:#ddd;color:#333;border:none;border-radius:4px;cursor:pointer;font-size:13px;">Abbrechen</button>
                </div>
            </div>
        </div>

        <!-- PANEL: Bilder -->
        <div id="edit-panel-images" class="em-panel">
            <div class="em-panel-hdr">
                <button class="em-back" onclick="switchEditPanel('main')">‹</button>
                <h3>Bilder</h3>
            </div>
            <div class="em-body" style="padding-top:16px;padding-bottom:16px;">
                <div id="images-loading" style="text-align:center;color:#aaa;padding:20px;">Laden…</div>
                <div id="images-grid" style="display:flex;flex-wrap:wrap;gap:12px;margin-bottom:20px;"></div>
                <label style="display:inline-flex;align-items:center;gap:8px;padding:9px 16px;background:#333;color:white;border-radius:4px;cursor:pointer;font-size:13px;">
                    + Bild hinzufügen
                    <input type="file" id="image-upload-input" accept="image/*" style="display:none;" onchange="uploadProductImage(this)">
                </label>
                <div id="image-upload-status" style="margin-top:10px;font-size:13px;color:#666;"></div>
            </div>
        </div>

        <!-- PANEL: Beschreibung -->
        <div id="edit-panel-description" class="em-panel">
            <div class="em-panel-hdr">
                <button class="em-back" onclick="switchEditPanel('main')">‹</button>
                <h3>Beschreibung</h3>
            </div>
            <div class="em-body" style="padding: 12px 22px; overflow-y: auto; display: flex; flex-direction: column; gap: 16px; min-height: 0;">
                <!-- Kurzbeschreibung -->
                <div>
                    <label style="display:block;font-size:11px;font-weight:600;letter-spacing:.06em;text-transform:uppercase;color:#888;margin-bottom:6px;">
                        Kurzbeschreibung <span style="font-weight:400;text-transform:none;letter-spacing:0;">(erscheint neben dem Produktbild)</span>
                    </label>
                    <textarea id="edit-short-desc" rows="3" maxlength="500"
                              placeholder="1–2 prägnante Sätze…"
                              style="width:100%;box-sizing:border-box;padding:10px 12px;border:1px solid #ddd;border-radius:4px;font-size:13px;line-height:1.5;resize:vertical;font-family:inherit;"></textarea>
                    <div style="text-align:right;font-size:11px;margin-top:3px;">
                        <span id="edit-short-desc-count" style="font-weight:500;">0</span>
                        <span id="edit-short-desc-limit-label" style="color:#bbb;"> / 160 Zeichen empfohlen</span>
                    </div>
                </div>
                <!-- Lange Beschreibung -->
                <div style="flex:1;display:flex;flex-direction:column;min-height:200px;">
                    <label style="display:block;font-size:11px;font-weight:600;letter-spacing:.06em;text-transform:uppercase;color:#888;margin-bottom:6px;">Lange Beschreibung</label>
                    <div class="desc-toolbar" style="display:flex;gap:4px;flex-wrap:wrap;padding:6px;background:#f8f8f8;border:1px solid #ddd;border-bottom:none;border-radius:4px 4px 0 0;">
                        <button type="button" onclick="descCmd('bold')"><b>B</b></button>
                        <button type="button" onclick="descCmd('italic')"><i>I</i></button>
                        <button type="button" onclick="descCmd('underline')" style="text-decoration:underline;">U</button>
                        <button type="button" onclick="descCmd('strikeThrough')" style="text-decoration:line-through;">S</button>
                        <button type="button" onclick="descCmd('insertUnorderedList')">• Liste</button>
                        <button type="button" onclick="descCmd('insertOrderedList')">1. Liste</button>
                        <button type="button" onclick="descHeading('h2')">H2</button>
                        <button type="button" onclick="descHeading('h3')">H3</button>
                        <button type="button" onclick="descCmd('justifyLeft')">⬅</button>
                        <button type="button" onclick="descCmd('justifyCenter')">↔</button>
                        <button type="button" onclick="descCmd('justifyRight')">➡</button>
                        <button type="button" onclick="descLink()">🔗</button>
                        <button type="button" onclick="descCmd('removeFormat')">∅</button>
                    </div>
                    <div id="desc-editor" contenteditable="true" style="flex:1;padding:12px;border:1px solid #ddd;border-radius:0 0 4px 4px;font-size:14px;line-height:1.6;outline:none;overflow-y:auto;min-height:160px;"></div>
                </div>
            </div>
            <div class="em-footer">
                <button onclick="saveDescription()" style="flex:1;padding:10px;background:#333;color:white;border:none;border-radius:4px;cursor:pointer;font-size:13px;">Speichern</button>
            </div>
        </div>

        <!-- PANEL: Vergangene Sales -->
        <div id="edit-panel-past-sales" class="em-panel">
            <div class="em-panel-hdr">
                <button class="em-back" onclick="switchEditPanel('main')">‹</button>
                <h3>Vergangene Sales</h3>
            </div>
            <div class="em-body" style="padding-top:16px;padding-bottom:16px;">
                <div id="past-sales-loading" style="text-align:center;color:#aaa;padding:20px;">Laden…</div>
                <div id="past-sales-list" style="display:none;"></div>
            </div>
        </div>

        <!-- PANEL: Stock verwalten -->
        <div id="edit-panel-stock" class="em-panel">
            <div class="em-panel-hdr">
                <button class="em-back" onclick="switchEditPanel('main')">‹</button>
                <h3>Stock verwalten</h3>
            </div>
            <div class="em-body" style="padding-top:16px;padding-bottom:16px;">
                <div id="stock-schedule-loading" style="text-align:center;color:#aaa;padding:20px;">Laden…</div>
                <div id="stock-schedule-list" style="display:none;margin-bottom:8px;"></div>
                <div style="border-top:1px solid #eee;padding-top:16px;margin-top:4px;">
                    <h4 style="margin:0 0 12px;font-size:14px;font-weight:600;">Neue Änderung planen</h4>
                    <div style="margin-bottom:12px;">
                        <label style="font-size:13px;font-weight:500;display:block;margin-bottom:6px;">Typ</label>
                        <div style="display:flex;border:1px solid #ddd;border-radius:4px;overflow:hidden;">
                            <button type="button" id="stock-type-increase" onclick="setStockType('increase')" style="flex:1;padding:8px;border:none;background:#333;color:white;cursor:pointer;font-size:13px;">Erhöhen um</button>
                            <button type="button" id="stock-type-set" onclick="setStockType('set')" style="flex:1;padding:8px;border:none;background:#f5f5f5;color:#333;cursor:pointer;font-size:13px;">Auf setzen</button>
                        </div>
                    </div>
                    <div style="margin-bottom:12px;">
                        <label style="font-size:13px;font-weight:500;display:block;margin-bottom:6px;">Menge</label>
                        <input type="number" id="stock-change-amount" min="1" style="width:100%;padding:9px;border:1px solid #ddd;border-radius:4px;box-sizing:border-box;">
                    </div>
                    <div style="margin-bottom:16px;">
                        <label style="font-size:13px;font-weight:500;display:block;margin-bottom:6px;">Datum &amp; Uhrzeit</label>
                        <input type="datetime-local" id="stock-change-datetime" style="width:100%;padding:9px;border:1px solid #ddd;border-radius:4px;box-sizing:border-box;">
                    </div>
                    <button onclick="addStockSchedule()" style="width:100%;padding:10px;background:#333;color:white;border:none;border-radius:4px;cursor:pointer;font-size:13px;">Planen</button>
                    <div id="stock-schedule-status" style="margin-top:10px;font-size:13px;color:#666;min-height:18px;"></div>
                </div>
            </div>
        </div>

        <!-- PANEL: Varianten verbinden -->
        <div id="edit-panel-variants" class="em-panel">
            <div class="em-panel-hdr">
                <button class="em-back" onclick="switchEditPanel('main')">‹</button>
                <h3>Varianten verbinden</h3>
            </div>
            <div class="em-body" style="padding-top:20px;padding-bottom:20px;">
                <p style="font-size:13px;color:var(--creme-dim);margin:0 0 18px;line-height:1.6;">
                    Verbinde dieses Produkt mit einem anderen, das eine Variante davon ist (z.B. andere Farbe oder Topfgröße).
                    Das verbundene Produkt wird auf der Produktseite als auswählbare Variante angezeigt.
                </p>
                <div style="margin-bottom:16px;">
                    <label style="font-size:11px;font-weight:700;color:var(--creme-dim);text-transform:uppercase;letter-spacing:.08em;display:block;margin-bottom:8px;">
                        Original-Produkt (Eltern-Produkt)
                    </label>
                    <div style="position:relative;">
                        <input type="text" id="variant-parent-search" placeholder="Produktname suchen…"
                               style="width:100%;padding:9px 12px;border:1px solid var(--border-thin);border-radius:2px;background:var(--bg-inky);color:var(--creme);font-size:13px;box-sizing:border-box;"
                               oninput="variantSearchProducts(this.value)">
                        <div id="variant-parent-results" style="display:none;position:absolute;top:100%;left:0;right:0;background:var(--bg-surface);border:1px solid var(--border-thin);z-index:50;max-height:220px;overflow-y:auto;"></div>
                    </div>
                    <div id="variant-parent-selected" style="margin-top:10px;padding:10px 12px;background:var(--bg-raised);border-radius:2px;display:none;align-items:center;justify-content:space-between;gap:8px;">
                        <span id="variant-parent-name" style="font-size:13px;color:var(--creme);"></span>
                        <button onclick="variantClearParent()" style="background:none;border:none;color:var(--creme-muted);cursor:pointer;font-size:16px;line-height:1;padding:0;">✕</button>
                    </div>
                </div>
                <button onclick="variantSaveParent()" class="pa-btn-filled" style="width:100%;padding:11px;">Verbindung speichern</button>
                <div id="variant-save-status" style="margin-top:10px;font-size:13px;color:var(--plum-hot);min-height:16px;"></div>
            </div>
        </div>

        <!-- PANEL: Tags bearbeiten -->
        <div id="edit-panel-tags" class="em-panel">
            <div class="em-panel-hdr">
                <button class="em-back" onclick="switchEditPanel('main')">‹</button>
                <h3>Tags bearbeiten</h3>
            </div>
            <div class="em-body" id="edit-panel-tags-body" style="padding-top:16px;">
                <div style="padding:20px;text-align:center;color:var(--creme-dim);">Lade Tags…</div>
            </div>
            <div class="em-footer" style="align-items:center;">
                <button id="tags-save-btn" onclick="saveProductTags()" style="flex:1;padding:10px;background:var(--plum);color:var(--creme);border:none;border-radius:2px;cursor:pointer;font-size:12px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;">Speichern</button>
                <button onclick="switchEditPanel('main')" style="padding:10px 20px;background:var(--bg-raised);color:var(--creme);border:1px solid var(--border-thin);border-radius:2px;cursor:pointer;font-size:12px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;">Abbrechen</button>
                <span id="tags-save-status" style="font-size:13px;color:var(--creme-dim);min-width:80px;"></span>
            </div>
        </div>

    </div>
</div>

<!-- Crop & Watermark Modal -->
<div id="cwm-overlay">
    <div id="cwm-dialog">
        <h3>Bild zuschneiden &amp; Wasserzeichen</h3>
        <div class="cwm-body">
            <canvas id="cwm-canvas" width="480" height="480"></canvas>
            <div class="cwm-controls">
                <p class="cwm-ctrl-label">Zoom</p>
                <input type="range" id="cwm-zoom" min="100" max="500" value="100" step="1" oninput="cwmZoomSlider(this.value)">
                <p style="font-size:12px;color:#aaa;margin:4px 0 0;">Scroll oder Slider zum Zoomen<br>Ziehen zum Verschieben</p>
                <p class="cwm-ctrl-label">Wasserzeichen</p>
                <div class="cwm-checkbox-row">
                    <input type="checkbox" id="cwm-show-wm" checked onchange="cwmRender()">
                    <label for="cwm-show-wm">Anzeigen</label>
                </div>
                <div class="cwm-checkbox-row">
                    <input type="checkbox" id="cwm-invert-wm" onchange="cwmRender()">
                    <label for="cwm-invert-wm">Farben invertieren</label>
                </div>
                <p class="cwm-hint">Wasserzeichen ziehen zum Verschieben.<br>Ecken ziehen zum Vergrößern/Verkleinern.</p>
                <p class="cwm-ctrl-label">Rechteckiges Bild</p>
                <div class="cwm-checkbox-row">
                    <input type="checkbox" id="cwm-rect-enable" onchange="cwmToggleRect()">
                    <label for="cwm-rect-enable">Aktivieren</label>
                </div>
                <div id="cwm-rect-controls" style="display:none;">
                    <div style="display:flex;align-items:center;gap:8px;margin-bottom:8px;">
                        <span style="font-size:13px;font-weight:600;" id="cwm-orient-label-h">Horizontal</span>
                        <label class="switch" style="width:40px;height:20px;">
                            <input type="checkbox" id="cwm-rect-toggle" onchange="cwmToggleOrientation()">
                            <span class="slider"></span>
                        </label>
                        <span style="font-size:13px;" id="cwm-orient-label-v">Vertikal</span>
                    </div>
                    <p class="cwm-hint">Balken an den Kanten zum Einziehen ziehen.<br>Transparente Bereiche → PNG-Export.</p>
                </div>
                <div class="cwm-btns">
                    <button type="button" style="padding:10px 18px;background:#333;color:white;border:none;border-radius:4px;cursor:pointer;" onclick="cwmSave()" id="cwm-save-btn">Speichern</button>
                    <button type="button" style="padding:10px 18px;background:#f0f0f0;color:#333;border:none;border-radius:4px;cursor:pointer;" onclick="cwmCancel()">Abbrechen</button>
                </div>
                <div id="cwm-upload-progress">Wird hochgeladen...</div>
            </div>
        </div>
    </div>
</div>

<div id="offer-start-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 10000; align-items: center; justify-content: center;">
    <div style="background: white; padding: 30px; border-radius: 8px; max-width: 400px; width: 90%;">
        <h3 style="margin-top: 0;">Startdatum setzen</h3>
        <div style="margin-bottom: 15px;">
            <label style="display: block; margin-bottom: 5px; font-weight: 500;">Datum (TT.MM.JJJJ)</label>
            <input type="text" id="offer-start-date" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;" placeholder="z.B. 10.05.2026">
        </div>
        <div style="margin-bottom: 15px;">
            <label style="display: block; margin-bottom: 5px; font-weight: 500;">Uhrzeit (HH:MM)</label>
            <input type="time" id="offer-start-time" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
        </div>
        <div style="display: flex; gap: 10px;">
            <button id="save-offer-start" style="flex: 1; padding: 12px; background: #333; color: white; border: none; border-radius: 4px; cursor: pointer;">Speichern</button>
            <button id="cancel-offer-start" style="flex: 1; padding: 12px; background: #ddd; color: #333; border: none; border-radius: 4px; cursor: pointer;">Abbrechen</button>
        </div>
    </div>
</div>

<div id="bulk-sale-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 10000; align-items: center; justify-content: center;">
    <div style="background: white; padding: 30px; border-radius: 8px; max-width: 600px; width: 90%; max-height: 90vh; overflow-y: auto;">
        <h3 style="margin-top: 0; margin-bottom: 20px;">Bulk Rabatt Aktion</h3>
        
        <div style="margin-bottom: 15px;">
            <label style="display: block; margin-bottom: 5px; font-weight: 500;">Aktionsname</label>
            <input type="text" id="bulk-sale-title" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;" placeholder="z.B. Sommer Sale">
        </div>
        
        <div id="bulk-sale-date-section" style="margin-bottom: 20px;">
            <div style="display: flex; gap: 15px; margin-bottom: 15px;">
                <div style="flex: 1;">
                    <label style="display: block; margin-bottom: 5px; font-weight: 500;">Startdatum</label>
                    <button type="button" id="bulk-sale-start-btn" style="width: 100%; padding: 8px 12px; background: #f5f5f5; border: 1px solid #ddd; border-radius: 4px; cursor: pointer;">Startdatum setzen</button>
                    <div id="bulk-sale-start-display" style="margin-top: 5px; font-size: 13px; color: #666;"></div>
                </div>
                <div style="flex: 1;">
                    <label style="display: block; margin-bottom: 5px; font-weight: 500;">Enddatum</label>
                    <button type="button" id="bulk-sale-end-btn" style="width: 100%; padding: 8px 12px; background: #f5f5f5; border: 1px solid #ddd; border-radius: 4px; cursor: pointer;">Enddatum setzen</button>
                    <div id="bulk-sale-end-display" style="margin-top: 5px; font-size: 13px; color: #666;"></div>
                </div>
            </div>
            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px; font-weight: 500;">
                    <input type="checkbox" id="bulk-sale-show-end-date"> Dem Kunden anzeigen bis wann reduziert ist
                </label>
            </div>
            <div style="display: flex; gap: 10px;">
                <button type="button" id="bulk-sale-dates-ok" style="flex: 1; padding: 12px; background: #333; color: white; border: none; border-radius: 4px; cursor: pointer;">OK</button>
                <button type="button" id="bulk-sale-dates-cancel" style="flex: 1; padding: 12px; background: #ddd; color: #333; border: none; border-radius: 4px; cursor: pointer;">Abbrechen</button>
            </div>
        </div>
        
        <div id="bulk-sale-groups-section" style="display: none;">
            <div id="bulk-sale-groups" style="margin-bottom: 20px;"></div>
            <button type="button" id="add-bulk-sale-group" style="padding: 8px 12px; background: #f5f5f5; border: 1px solid #ddd; border-radius: 4px; cursor: pointer; margin-bottom: 15px;">+ Gruppe hinzufügen</button>
            
            <div style="display: flex; gap: 10px;">
                <button id="save-bulk-sale" style="flex: 1; padding: 12px; background: #333; color: white; border: none; border-radius: 4px; cursor: pointer;">Speichern</button>
                <button id="cancel-bulk-sale" style="flex: 1; padding: 12px; background: #ddd; color: #333; border: none; border-radius: 4px; cursor: pointer;">Abbrechen</button>
            </div>
        </div>
    </div>
</div>

<div id="bulk-sale-date-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 10001; align-items: center; justify-content: center;">
    <div style="background: white; padding: 30px; border-radius: 8px; max-width: 400px; width: 90%;">
        <h3 style="margin-top: 0;">Datum setzen</h3>
        <div style="margin-bottom: 15px;">
            <label style="display: block; margin-bottom: 5px; font-weight: 500;">Datum (TT.MM.JJJJ)</label>
            <input type="text" id="bulk-sale-date-input" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;" placeholder="z.B. 10.05.2026" maxlength="10">
        </div>
        <div style="margin-bottom: 15px;">
            <label style="display: block; margin-bottom: 5px; font-weight: 500;">Uhrzeit (HH:MM)</label>
            <input type="time" id="bulk-sale-time-input" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
        </div>
        <div style="display: flex; gap: 10px;">
            <button id="save-bulk-sale-date" style="flex: 1; padding: 12px; background: #333; color: white; border: none; border-radius: 4px; cursor: pointer;">Speichern</button>
            <button id="cancel-bulk-sale-date" style="flex: 1; padding: 12px; background: #ddd; color: #333; border: none; border-radius: 4px; cursor: pointer;">Abbrechen</button>
        </div>
    </div>
</div>

<div id="add-product-to-group-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 10001; align-items: center; justify-content: center;">
    <div style="background: white; padding: 30px; border-radius: 8px; max-width: 800px; width: 90%; max-height: 90vh; overflow-y: auto;">
        <h3 style="margin-top: 0; margin-bottom: 20px;">Artikel zu Gruppe hinzufügen</h3>
        <input type="text" id="add-product-search" placeholder="Produkte suchen..." style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; margin-bottom: 15px;">
        <div id="add-product-list" style="max-height: 400px; overflow-y: auto;"></div>
        <button id="close-add-product-modal" style="margin-top: 15px; padding: 10px 20px; background: #ddd; color: #333; border: none; border-radius: 4px; cursor: pointer;">Schließen</button>
    </div>
</div>

<div id="delete-confirm-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 10000; align-items: center; justify-content: center;">
    <div style="background: white; padding: 30px; border-radius: 8px; max-width: 420px; width: 90%;">
        <h3 style="margin-top: 0; margin-bottom: 15px;">Produkt löschen</h3>
        <p id="delete-confirm-text" style="color: #666; margin-bottom: 25px;">Möchten Sie dieses Produkt wirklich löschen?</p>
        <div style="display: flex; gap: 10px;">
            <button id="confirm-delete-btn" style="flex: 1; padding: 12px; background: #e74c3c; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 14px;">Löschen</button>
            <button id="cancel-delete-btn" style="flex: 1; padding: 12px; background: #ddd; color: #333; border: none; border-radius: 4px; cursor: pointer; font-size: 14px;">Abbrechen</button>
        </div>
    </div>
</div>

<div id="bulk-sale-details-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 10002; align-items: center; justify-content: center;">
    <div style="background: white; padding: 30px; border-radius: 8px; max-width: 900px; width: 90%; max-height: 90vh; overflow-y: auto;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h3 id="bulk-sale-details-title" style="margin-top: 0;">Bulk Aktion Details</h3>
            <button id="delete-bulk-sale-btn" style="padding: 8px 12px; background: #e74c3c; color: white; border: none; border-radius: 4px; cursor: pointer;">🗑️ Löschen</button>
        </div>
        <div id="bulk-sale-details-content"></div>
        <button id="close-bulk-sale-details" style="margin-top: 20px; padding: 10px 20px; background: #ddd; color: #333; border: none; border-radius: 4px; cursor: pointer;">Schließen</button>
    </div>
</div>

<style>
#product-search:focus {
    outline: none;
    border-color: #333;
}

#product-table {
    font-size: 14px;
}

#product-table tbody tr {
    border-bottom: 1px solid #eee;
}

#product-table tbody tr:hover {
    background-color: #fafafa;
}

#product-table td {
    padding: 12px;
    vertical-align: middle;
}

.product-img {
    width: 50px;
    height: 50px;
    object-fit: cover;
    border-radius: 3px;
}

.product-name-link {
    color: #333;
    text-decoration: none;
    font-weight: 500;
}

.product-name-link:hover {
    color: #666;
}

.stock-positive {
    color: #2e7d32;
    font-weight: 600;
}

.stock-negative {
    color: #c62828;
    font-weight: 600;
}

.stock-zero {
    color: #f57c00;
    font-weight: 600;
}

.price-cell {
    font-weight: 500;
    color: #333;
}

.offer-badge {
    background: #d32f2f;
    color: white;
    padding: 3px 8px;
    border-radius: 3px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    display: inline-block;
    margin-bottom: 4px;
}

.offer-info {
    font-size: 12px;
    color: #666;
    line-height: 1.3;
}

.offer-info .regular-price {
    text-decoration: line-through;
    color: #999;
    font-size: 11px;
}

.offer-info .sale-price {
    color: #d32f2f;
    font-weight: 600;
}

.offer-info .offer-end {
    color: #999;
    font-size: 11px;
}

.edit-btn {
    font-size: 18px;
    background: none;
    border: none;
    cursor: pointer;
    opacity: 0.6;
    transition: opacity 0.2s;
    padding: 5px;
}

.edit-btn:hover {
    opacity: 1;
}

.delete-btn {
    font-size: 18px;
    background: none;
    border: none;
    cursor: pointer;
    opacity: 0.5;
    transition: opacity 0.2s;
    padding: 5px;
    color: #e74c3c;
}

.delete-btn:hover {
    opacity: 1;
}

.sell-checkbox {
    width: 18px;
    height: 18px;
    cursor: pointer;
}

#edit-modal button:hover {
    opacity: 0.9;
}

/* Toggle-Schalter Styling */
#modal-price-type-toggle:checked + span,
#modal-time-limit-toggle:checked + span {
    background-color: #333;
}

#modal-price-type-toggle:checked + span + span,
#modal-time-limit-toggle:checked + span + span {
    transform: translateX(24px);
}

/* ── Tags panel ── */
.ep-tag-group { margin-bottom:18px; }
.ep-tag-group-label { display:flex;align-items:center;gap:6px;font-size:11px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:var(--creme-dim);margin:0 0 8px; }
.ep-tag-chips { display:flex;flex-wrap:wrap;gap:6px; }
.ep-tag-chip { padding:5px 12px;background:var(--bg-inky);border:1px solid var(--border-thin);border-radius:14px;font-size:12px;color:var(--creme-dim);cursor:pointer;transition:border-color .12s,color .12s,background .12s;user-select:none;display:inline-flex;align-items:center;gap:4px; }
.ep-tag-chip:hover { border-color:var(--plum);color:var(--creme); }
.ep-tag-chip.ep-selected { background:var(--plum);border-color:var(--plum-hot);color:var(--creme); }
.ep-tag-chip.ep-new-btn { border-style:dashed; }
.ep-vinput-wrap { display:flex;gap:6px;align-items:center;margin-top:8px;flex-wrap:wrap; }
.ep-vinput { padding:5px 9px;border:1px solid var(--border-thin);border-radius:2px;background:var(--bg-inky);color:var(--creme);font-size:12px;min-width:120px; }
.ep-vinput:focus { outline:none;border-color:var(--plum); }
.ep-del-btn { background:none;border:none;padding:0;cursor:pointer;color:inherit;opacity:0.35;font-size:15px;line-height:1;transition:opacity .12s;flex-shrink:0; }
.ep-del-btn:hover { opacity:1; }

/* ── pa-combo (edit modal) ── */
.pa-combo { position:relative; }
.pa-combo-trigger {
  display:flex; align-items:center; justify-content:space-between; gap:8px;
  padding:7px 10px; border:1px solid #ddd; border-radius:3px;
  background:#fff; cursor:pointer; font-size:13px; min-height:34px;
  transition:border-color .12s; flex:1;
}
.pa-combo-trigger:hover { border-color:#999; }
.pa-combo-trigger.open { border-color:#555; }
.pa-combo-val { flex:1; color:#333; user-select:none; }
.pa-combo-val.pa-combo-placeholder { color:#aaa; }
.pa-combo-chev { flex-shrink:0; transition:transform .15s; color:#888; }
.pa-combo-trigger.open .pa-combo-chev { transform:rotate(180deg); }
.pa-combo-drop {
  position:absolute; top:calc(100% + 3px); left:0; right:0;
  background:#fff; border:1px solid #ddd; border-radius:4px;
  box-shadow:0 4px 16px rgba(0,0,0,.14); z-index:9000;
  max-height:220px; overflow-y:auto;
}
.pa-combo-new-btn {
  display:flex; align-items:center; gap:6px; width:100%;
  padding:8px 11px; background:none; border:none; border-bottom:1px solid #eee;
  font-size:12px; font-weight:600; color:#2a6e3a; cursor:pointer; text-align:left;
}
.pa-combo-new-btn:hover { background:#f0f7f2; }
.pa-combo-option {
  display:block; width:100%; padding:8px 11px;
  background:none; border:none; text-align:left; font-size:13px; cursor:pointer;
}
.pa-combo-option:hover { background:#f5f5f5; }
.pa-combo-option.selected { background:#e8f0e8; font-weight:500; }
#pa-combo-overlay {
  position:fixed; inset:0; background:rgba(0,0,0,.45); z-index:19999;
  display:flex; align-items:center; justify-content:center;
}
#pa-combo-dialog {
  background:#fff; border-radius:6px; padding:24px 28px;
  width:340px; max-width:92vw; box-shadow:0 8px 32px rgba(0,0,0,.22);
}
#pa-combo-dialog h4 { margin:0 0 14px; font-size:15px; font-weight:600; }
#pa-combo-dialog-input {
  width:100%; box-sizing:border-box; padding:9px 11px;
  border:1px solid #ddd; border-radius:3px; font-size:14px; margin-bottom:16px;
}
#pa-combo-dialog-input:focus { outline:none; border-color:#555; }
.pa-combo-dialog-btns { display:flex; gap:10px; justify-content:flex-end; }
.pa-combo-dialog-btns button {
  padding:8px 18px; border-radius:3px; font-size:13px; cursor:pointer; border:1px solid #ddd;
}
.pa-combo-dialog-btns button:first-child { background:#333; color:#fff; border-color:#333; }
.pa-combo-dialog-btns button:first-child:hover { background:#111; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Produkte laden
    loadProducts();
    
    // Search Event
    document.getElementById('product-search').addEventListener('input', function(e) {
        filterProducts(e.target.value);
    });
});

function loadProducts() {
    console.log('DEBUG: loadProducts aufgerufen');
    
    // Angebote mit Startzeitpunkt aktivieren
    fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'action=activate_offers_with_start_date&nonce=<?php echo wp_create_nonce('product_list_nonce'); ?>'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.data.activated_count > 0) {
            console.log('DEBUG: ' + data.data.activated_count + ' Angebote aktiviert');
        }
    })
    .catch(error => {
        console.error('DEBUG: Fehler beim Aktivieren von Angeboten:', error);
    });
    
    fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'action=get_product_list_data&nonce=<?php echo wp_create_nonce('product_list_nonce'); ?>'
    })
    .then(response => {
        console.log('DEBUG: Response status:', response.status);
        return response.json();
    })
    .then(data => {
        console.log('DEBUG: Response data:', data);
        if (data.success) {
            renderProducts(data.data);
        } else {
            console.error('DEBUG: Error:', data.data);
        }
    })
    .catch(error => {
        console.error('DEBUG: Fetch error:', error);
    });
}

function renderProducts(products) {
    const tbody = document.getElementById('product-table-body');
    tbody.innerHTML = '';
    
    if (products.length === 0) {
        tbody.innerHTML = '<tr><td colspan="9" style="padding: 40px; text-align: center; color: #666;"><div style="font-size: 48px; margin-bottom: 16px;">🔍</div>Keine Produkte gefunden.</td></tr>';
        return;
    }
    
    // Prüfen, ob Gruppierung aktiviert ist
    const groupByOffer = document.getElementById('group-by-offer-toggle').checked;
    
    if (groupByOffer) {
        // Produkte in Gruppen einteilen
        const activeOffers = products.filter(p => p.has_offer && !p.is_offer_expired && !p.part_of_sale);
        const expiredOffers = products.filter(p => p.is_offer_expired && p.offer_expired_read !== '1' && !p.part_of_sale);
        const readExpiredOffers = products.filter(p => p.is_offer_expired && p.offer_expired_read === '1' && !p.part_of_sale);
        const noOffers = products.filter(p => !p.has_offer && !p.is_offer_expired && !p.part_of_sale);
        const bulkSaleProducts = products.filter(p => p.part_of_sale);
        
        // Gruppen rendern
        if (activeOffers.length > 0) {
            renderGroup(tbody, 'Laufende Angebote', activeOffers);
        }
        if (bulkSaleProducts.length > 0) {
            renderBulkSalesGroup(tbody, bulkSaleProducts);
        }
        if (readExpiredOffers.length > 0) {
            renderGroup(tbody, 'Laufende Angebote (gelesen)', readExpiredOffers, false, true);
        }
        if (expiredOffers.length > 0) {
            renderGroup(tbody, 'Abgelaufene Angebote', expiredOffers, true);
        }
        if (noOffers.length > 0) {
            renderGroup(tbody, 'Produkte ohne Angebote', noOffers);
        }
    } else {
        // Normale Liste
        products.forEach(product => {
            const tr = document.createElement('tr');
            
            // Verfügbar berechnen
            const available = product.stock - product.in_progress;
            
            // Stock-Klasse
            let stockClass = 'stock-zero';
            if (available > 0) {
                stockClass = 'stock-positive';
        } else if (available < 0) {
            stockClass = 'stock-negative';
        }
        
        // Angebot HTML
        let offerHtml = '-';
        if (product.is_offer_expired) {
            // Abgelaufenes Angebot
            const expiredSince = product.offer_expired_since ? new Date(product.offer_expired_since * 1000).toLocaleString('de-DE') : 'Unbekannt';
            const isRead = product.offer_expired_read === '1';
            const reductionAmount = product.offer_reduction_amount || '';
            offerHtml = `
                <div style="padding: 8px; background: #fff3cd; border-left: 3px solid #ffc107; border-radius: 3px;">
                    <div style="font-weight: bold; color: #856404; margin-bottom: 4px;">Angebot abgelaufen ${reductionAmount}</div>
                    <div style="font-size: 12px; color: #856404; margin-bottom: 4px;">Abgelaufen seit: ${expiredSince}</div>
                    <label style="display: flex; align-items: center; gap: 5px; font-size: 12px; cursor: pointer;">
                        <input type="checkbox" class="expired-read-checkbox" data-product-id="${product.id}" ${isRead ? 'checked' : ''}>
                        Als gelesen markieren
                    </label>
                </div>
            `;
        } else if (product.has_offer) {
            let timeInfo = '';
            if (product.time_limited) {
                if (product.time_limit_type === 'date' && product.time_limit_date_only) {
                    let dateText = product.time_limit_date_only;
                    if (product.time_limit_time) {
                        dateText += ' ' + product.time_limit_time;
                    }
                    timeInfo = '<span class="offer-end">Bis: ' + dateText + '</span>';
                } else {
                    // Verbleibende Zeit berechnen und anzeigen
                    const now = Math.floor(Date.now() / 1000);
                    // offer_end ist jetzt ein Unix-Timestamp in Sekunden (UTC)
                    const dateTo = product.offer_end ? parseInt(product.offer_end) : 0;
                    const diff = dateTo - now;
                    
                    if (diff > 0) {
                        const days = Math.floor(diff / (24 * 60 * 60));
                        
                        if (days >= 1) {
                            // Mehr als 1 Tag: Datum anzeigen (mit Zeitzone-Korrektur)
                            const endDate = new Date(dateTo * 1000);
                            const localOffset = endDate.getTimezoneOffset() * 60 * 1000; // Offset in ms
                            const localDate = new Date(dateTo * 1000 + localOffset);
                            
                            const day = String(localDate.getDate()).padStart(2, '0');
                            const month = String(localDate.getMonth() + 1).padStart(2, '0');
                            const year = localDate.getFullYear();
                            const hours = String(localDate.getHours()).padStart(2, '0');
                            const minutes = String(localDate.getMinutes()).padStart(2, '0');
                            timeInfo = '<span class="offer-end" data-countdown="' + dateTo + '">Bis: ' + day + '.' + month + '.' + year + ' ' + hours + ':' + minutes + '</span>';
                        } else {
                            // Weniger als 1 Tag: Countdown in HH:MM:SS
                            const hours = Math.floor(diff / (60 * 60));
                            const minutes = Math.floor((diff % (60 * 60)) / 60);
                            const seconds = diff % 60;
                            
                            const h = String(hours).padStart(2, '0');
                            const m = String(minutes).padStart(2, '0');
                            const s = String(seconds).padStart(2, '0');
                            
                            timeInfo = '<span class="offer-end countdown" data-countdown="' + dateTo + '" data-product-id="' + product.id + '">' + h + ':' + m + ':' + s + '</span>';
                        }
                    } else {
                        // Angebot abgelaufen
                        timeInfo = '<span class="offer-end" style="color: #e74c3c;">Abgelaufen</span>';
                    }
                }
            }
            
            offerHtml = `
                <div>
                    <span class="offer-badge">Angebot</span>
                    <div class="offer-info">
                        ${product.show_old_price ? '<span class="regular-price">' + product.regular_price + ' €</span>' : ''}
                        <span class="sale-price">${product.sale_price} €</span>
                        <div>
                            ${product.offer_start_date ? '<span class="offer-begin">ab: ' + product.offer_start_date + '</span><br>' : ''}
                            ${timeInfo}
                        </div>
                    </div>
                </div>
            `;
        }
        
        tr.innerHTML = `
            <td style="text-align: center;">
                <input type="checkbox" class="sell-checkbox" data-product-id="${product.id}" ${product.is_sellable ? 'checked' : ''}>
            </td>
            <td>
                ${product.image ? `<img src="${product.image}" alt="${product.name}" class="product-img">` : '<div style="width:50px;height:50px;background:#f5f5f5;border-radius:3px;display:flex;align-items:center;justify-content:center;color:#999;">📷</div>'}
            </td>
            <td>
                ${paProductTitle(product)}
                ${product.was_out_of_stock ? '<span style="margin-left: 8px; background: #d4edda; color: #155724; padding: 2px 6px; border-radius: 3px; font-size: 11px; font-weight: 600;">Restocked</span>' : ''}
                ${product.was_low_stock ? '<span style="margin-left: 8px; background: #fff3cd; color: #856404; padding: 2px 6px; border-radius: 3px; font-size: 11px; font-weight: 600;">Low Stock Restocked</span>' : ''}
                ${product.was_restocked && !product.was_out_of_stock && !product.was_low_stock ? '<span style="margin-left: 8px; background: #e2e3e5; color: #383d41; padding: 2px 6px; border-radius: 3px; font-size: 11px; font-weight: 600;">Restocked</span>' : ''}
                ${product.is_new ? '<span style="margin-left: 8px; background: #cce5ff; color: #004085; padding: 2px 6px; border-radius: 3px; font-size: 11px; font-weight: 600;">Neu</span>' : ''}
                ${product.part_of_sale ? '<span style="margin-left: 8px; background: #d4edda; color: #155724; padding: 2px 6px; border-radius: 3px; font-size: 11px; font-weight: 600;">' + (product.sale_title || 'Aktion') + ' - ' + (product.group_name || 'Gruppe') + '</span>' : ''}
            </td>
            <td style="text-align: right;">${product.stock}</td>
            <td style="text-align: right;">${product.in_progress}</td>
            <td style="text-align: right;" class="${stockClass}">${available}</td>
            <td style="text-align: right;" class="price-cell">${product.price} €</td>
            <td>${offerHtml}</td>
            <td style="text-align: center;">
                <button class="edit-btn" data-product-id="${product.id}" data-product-name="${product.name}" data-product-sku="${product.sku || ''}" data-product-new="${product.is_new ? 'true' : 'false'}" data-stock="${product.stock}" data-price="${product.price}" data-regular-price="${product.regular_price}" data-sale-price="${product.sale_price}" data-has-offer="${product.has_offer}" data-show-old-price="${product.show_old_price}" data-time-limited="${product.time_limited}" data-time-limit-type="${product.time_limit_type}" data-time-limit-duration="${product.time_limit_duration}" data-time-limit-date="${product.time_limit_date}" data-show-end-date="${product.show_end_date}" data-time-limit-days="${product.time_limit_days}" data-time-limit-hours="${product.time_limit_hours}" data-time-limit-minutes="${product.time_limit_minutes}" data-time-limit-time="${product.time_limit_time}" data-time-limit-date-only="${product.time_limit_date_only}" data-offer-start-date="${product.offer_start_date || ''}" title="Bearbeiten">✏️</button>
                <button class="delete-btn" data-product-id="${product.id}" data-product-name="${product.name}" title="Löschen">🗑️</button>
            </td>
        `;

        tbody.appendChild(tr);
    });
    }

    // Checkbox-Event-Listener hinzufügen (nur für normale Liste)
    if (!groupByOffer) {
        document.querySelectorAll('.sell-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const productId = this.dataset.productId;
                const isSellable = this.checked;
                updateSellStatus(productId, isSellable);
            });
        });
        
        // Event-Listener für expired-read-checkbox hinzufügen
        document.querySelectorAll('.expired-read-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const productId = this.dataset.productId;
                const isRead = this.checked;
                markOfferAsRead(productId, isRead);
            });
        });
    }
    
    // Edit-Button Event-Listener neu setzen (nur wenn noch nicht vorhanden)
    document.querySelectorAll('.edit-btn:not(.event-attached)').forEach(btn => {
        btn.classList.add('event-attached');
        btn.addEventListener('click', function() {
            openEditModal(this);
        });
    });

    document.querySelectorAll('.delete-btn:not(.event-attached)').forEach(btn => {
        btn.classList.add('event-attached');
        btn.addEventListener('click', function() {
            openDeleteConfirm(this.dataset.productId, this.dataset.productName);
        });
    });
}

function renderGroup(tbody, groupName, products, isExpired = false, isReadExpired = false) {
    // Gruppenüberschrift
    const groupHeader = document.createElement('tr');
    groupHeader.innerHTML = `
        <td colspan="9" style="padding: 20px 12px; background: #f9f9f9; font-weight: bold; font-size: 16px; color: #333; border-bottom: 2px solid #e0e0e0;">
            ${groupName}
        </td>
    `;
    tbody.appendChild(groupHeader);
    
    // Produkte rendern
    products.forEach(product => {
        const tr = document.createElement('tr');
        
        // Verfügbar berechnen
        const available = product.stock - product.in_progress;
        
        // Stock-Klasse
        let stockClass = 'stock-zero';
        if (available > 0) {
            stockClass = 'stock-positive';
        } else if (available < 0) {
            stockClass = 'stock-negative';
        }
        
        // Angebot HTML
        let offerHtml = '-';
        if (product.is_offer_expired) {
            // Abgelaufenes Angebot
            const expiredSince = product.offer_expired_since ? new Date(product.offer_expired_since * 1000).toLocaleString('de-DE') : 'Unbekannt';
            const isRead = product.offer_expired_read === '1';
            const reductionAmount = product.offer_reduction_amount || '';
            offerHtml = `
                <div style="padding: 8px; background: #fff3cd; border-left: 3px solid #ffc107; border-radius: 3px;">
                    <div style="font-weight: bold; color: #856404; margin-bottom: 4px;">Angebot abgelaufen ${reductionAmount}</div>
                    <div style="font-size: 12px; color: #856404; margin-bottom: 4px;">Abgelaufen seit: ${expiredSince}</div>
                    <label style="display: flex; align-items: center; gap: 5px; font-size: 12px; cursor: pointer;">
                        <input type="checkbox" class="expired-read-checkbox" data-product-id="${product.id}" ${isRead ? 'checked' : ''}>
                        Als gelesen markieren
                    </label>
                </div>
            `;
        } else if (product.has_offer) {
            // Laufendes Angebot
            let timeInfo = '';
            if (product.time_limited) {
                if (product.time_limit_type === 'date' && product.time_limit_date_only) {
                    let dateText = product.time_limit_date_only;
                    if (product.time_limit_time) {
                        dateText += ' ' + product.time_limit_time;
                    }
                    timeInfo = '<span class="offer-end">Bis: ' + dateText + '</span>';
                } else {
                    // Verbleibende Zeit berechnen und anzeigen
                    const now = Math.floor(Date.now() / 1000);
                    // offer_end ist jetzt ein Unix-Timestamp in Sekunden (UTC)
                    const diff = product.offer_end - now;
                    if (diff > 0) {
                        const days = Math.floor(diff / (24 * 60 * 60));
                        const hours = Math.floor((diff % (24 * 60 * 60)) / (60 * 60));
                        const minutes = Math.floor((diff % (60 * 60)) / 60);
                        
                        if (days > 0) {
                            timeInfo = '<span class="offer-end countdown" data-countdown="' + product.offer_end + '" data-product-id="' + product.id + '">' + days + 'd ' + hours + 'h ' + minutes + 'm</span>';
                        } else {
                            const h = String(hours).padStart(2, '0');
                            const m = String(minutes).padStart(2, '0');
                            const s = String(diff % 60).padStart(2, '0');
                            timeInfo = '<span class="offer-end countdown" data-countdown="' + product.offer_end + '" data-product-id="' + product.id + '">' + h + ':' + m + ':' + s + '</span>';
                        }
                    } else {
                        timeInfo = '<span class="offer-end">Abgelaufen</span>';
                    }
                }
            } else {
                timeInfo = '<span class="offer-end">Unbegrenzt</span>';
            }
            
            offerHtml = `
                <div>
                    <span class="offer-badge">Angebot</span>
                    <div class="offer-info">
                        ${product.show_old_price ? '<span class="regular-price">' + product.regular_price + ' €</span>' : ''}
                        <span class="sale-price">${product.sale_price} €</span>
                        ${timeInfo}
                    </div>
                </div>
            `;
        }
        
        tr.innerHTML = `
            <td style="text-align: center;">
                <input type="checkbox" class="sell-checkbox" data-product-id="${product.id}" ${product.is_sellable ? 'checked' : ''}>
            </td>
            <td>
                ${product.image ? `<img src="${product.image}" alt="${product.name}" class="product-img">` : '<div style="width:50px;height:50px;background:#f5f5f5;border-radius:3px;display:flex;align-items:center;justify-content:center;color:#999;">📷</div>'}
            </td>
            <td>
                ${paProductTitle(product)}
                ${product.was_out_of_stock ? '<span style="margin-left: 8px; background: #d4edda; color: #155724; padding: 2px 6px; border-radius: 3px; font-size: 11px; font-weight: 600;">Restocked</span>' : ''}
                ${product.was_low_stock ? '<span style="margin-left: 8px; background: #fff3cd; color: #856404; padding: 2px 6px; border-radius: 3px; font-size: 11px; font-weight: 600;">Low Stock Restocked</span>' : ''}
                ${product.was_restocked && !product.was_out_of_stock && !product.was_low_stock ? '<span style="margin-left: 8px; background: #e2e3e5; color: #383d41; padding: 2px 6px; border-radius: 3px; font-size: 11px; font-weight: 600;">Restocked</span>' : ''}
                ${product.is_new ? '<span style="margin-left: 8px; background: #cce5ff; color: #004085; padding: 2px 6px; border-radius: 3px; font-size: 11px; font-weight: 600;">Neu</span>' : ''}
                ${product.part_of_sale ? '<span style="margin-left: 8px; background: #d4edda; color: #155724; padding: 2px 6px; border-radius: 3px; font-size: 11px; font-weight: 600;">' + (product.sale_title || 'Aktion') + ' - ' + (product.group_name || 'Gruppe') + '</span>' : ''}
            </td>
            <td style="text-align: right;">${product.stock}</td>
            <td style="text-align: right;">${product.in_progress}</td>
            <td style="text-align: right;" class="${stockClass}">${available}</td>
            <td style="text-align: right;" class="price-cell">${product.price} €</td>
            <td>${offerHtml}</td>
            <td style="text-align: center;">
                <button class="edit-btn" data-product-id="${product.id}" data-product-name="${product.name}" data-product-sku="${product.sku || ''}" data-product-new="${product.is_new ? 'true' : 'false'}" data-stock="${product.stock}" data-price="${product.price}" data-regular-price="${product.regular_price}" data-sale-price="${product.sale_price}" data-has-offer="${product.has_offer}" data-show-old-price="${product.show_old_price}" data-time-limited="${product.time_limited}" data-time-limit-type="${product.time_limit_type}" data-time-limit-duration="${product.time_limit_duration}" data-time-limit-date="${product.time_limit_date}" data-show-end-date="${product.show_end_date}" data-time-limit-days="${product.time_limit_days}" data-time-limit-hours="${product.time_limit_hours}" data-time-limit-minutes="${product.time_limit_minutes}" data-time-limit-time="${product.time_limit_time}" data-time-limit-date-only="${product.time_limit_date_only}" data-offer-start-date="${product.offer_start_date || ''}" title="Bearbeiten">✏️</button>
                <button class="delete-btn" data-product-id="${product.id}" data-product-name="${product.name}" title="Löschen">🗑️</button>
            </td>
        `;

        tbody.appendChild(tr);
    });

    // Checkbox-Event-Listener für "Als gelesen markieren"
    if (isExpired || isReadExpired) {
        document.querySelectorAll('.expired-read-checkbox:not(.event-attached)').forEach(checkbox => {
            checkbox.classList.add('event-attached');
            checkbox.addEventListener('change', function() {
                const productId = this.dataset.productId;
                const isRead = this.checked;
                markOfferAsRead(productId, isRead);
            });
        });
    }
    
    // Edit-Button Event-Listener neu setzen (nur wenn noch nicht vorhanden)
    document.querySelectorAll('.edit-btn:not(.event-attached)').forEach(btn => {
        btn.classList.add('event-attached');
        btn.addEventListener('click', function() {
            openEditModal(this);
        });
    });

    document.querySelectorAll('.delete-btn:not(.event-attached)').forEach(btn => {
        btn.classList.add('event-attached');
        btn.addEventListener('click', function() {
            openDeleteConfirm(this.dataset.productId, this.dataset.productName);
        });
    });
}

function parseGermanDate(dateString) {
    if (!dateString) return new Date();
    const parts = dateString.split(' ');
    if (parts.length === 2) {
        const dateParts = parts[0].split('.');
        const timeParts = parts[1].split(':');
        if (dateParts.length === 3 && timeParts.length === 2) {
            return new Date(dateParts[2], dateParts[1] - 1, dateParts[0], timeParts[0], timeParts[1]);
        }
    }
    return new Date();
}

function renderBulkSalesGroup(tbody, products) {
    try {
        // Produkte nach Sale ID gruppieren
        const sales = {};
        products.forEach(product => {
            const saleId = product.sale_id || 'unknown';
            if (!sales[saleId]) {
                sales[saleId] = {
                    title: product.sale_title || 'Aktion',
                    products: []
                };
            }
            sales[saleId].products.push(product);
        });
        
        // Für jeden Sale eine Gruppe erstellen
        Object.keys(sales).forEach(saleId => {
            const sale = sales[saleId];
            
            // Status des Angebots berechnen
            const now = Math.floor(Date.now() / 1000);
            let status = '';
            let statusColor = '#d4edda'; // Grün für aktiv
            let textColor = '#155724';
            let borderColor = '#c3e6cb';
            
            if (sale.products.length > 0) {
                const firstProduct = sale.products[0];
                const startDate = firstProduct.offer_start_date ? parseGermanDate(firstProduct.offer_start_date).getTime() / 1000 : 0;
                const endDate = firstProduct.offer_end_date ? parseGermanDate(firstProduct.offer_end_date).getTime() / 1000 : 0;
                
                if (endDate > 0 && endDate < now) {
                    status = ' (Abgelaufen seit: ' + firstProduct.offer_end_date + ')';
                    statusColor = '#f8d7da'; // Rot für abgelaufen
                    textColor = '#721c24';
                    borderColor = '#f5c6cb';
                } else if (startDate > 0 && startDate > now) {
                    status = ' (Startet in: ' + firstProduct.offer_start_date + ')';
                    statusColor = '#fff3cd'; // Gelb für startet in
                    textColor = '#856404';
                    borderColor = '#ffeeba';
                } else if (endDate > 0) {
                    status = ' (Aktiv - Bis: ' + firstProduct.offer_end_date + ')';
                } else {
                    status = ' (Aktiv)';
                }
            }
            
            // Sale-Überschrift
            const saleHeader = document.createElement('tr');
            saleHeader.innerHTML = `
                <td colspan="9" style="padding: 20px 12px; background: ${statusColor}; font-weight: bold; font-size: 16px; color: ${textColor}; border-bottom: 2px solid ${borderColor}; cursor: pointer;" onclick="openBulkSaleDetails('${saleId}', '${sale.title}')" data-sale-id="${saleId}">
                    ${sale.title} (Bulk Aktion)${status}
                </td>
            `;
            tbody.appendChild(saleHeader);
            
            // Produkte nach Gruppen innerhalb des Sales gruppieren
            const groups = {};
            sale.products.forEach(product => {
                const groupId = product.sale_group_id || 'unknown';
                const groupName = product.group_name || 'Gruppe';
                if (!groups[groupId]) {
                    groups[groupId] = {
                        name: groupName,
                        products: []
                    };
                }
                groups[groupId].products.push(product);
            });
            
            // Für jede Gruppe eine Gruppenüberschrift und Produkte rendern
            Object.keys(groups).forEach(groupId => {
                const group = groups[groupId];
                
                // Gruppenüberschrift
                const groupHeader = document.createElement('tr');
                groupHeader.innerHTML = `
                    <td colspan="9" style="padding: 10px 12px; background: #f8f9fa; font-weight: bold; font-size: 14px; color: #495057; border-bottom: 1px solid #dee2e6;">
                        ${group.name}
                    </td>
                `;
                tbody.appendChild(groupHeader);
                
                // Produkte rendern
                group.products.forEach(product => {
                    const tr = document.createElement('tr');
                    
                    // Verfügbar berechnen
                    const available = product.stock - product.in_progress;
                    
                    // Stock-Klasse
                    let stockClass = 'stock-zero';
                    if (available > 0) {
                        stockClass = 'stock-positive';
                    } else if (available < 0) {
                        stockClass = 'stock-negative';
                    }
                    
                    // Angebot HTML
                    let offerHtml = '-';
                    if (product.has_offer) {
                        offerHtml = `
                            <div>
                                <span class="offer-badge">Angebot</span>
                                <div class="offer-info">
                                    ${product.show_old_price ? '<span class="regular-price">' + product.regular_price + ' €</span>' : ''}
                                    <span class="sale-price">${product.sale_price} €</span>
                                    <div>
                                        ${product.offer_start_date ? '<span class="offer-begin">ab: ' + product.offer_start_date + '</span><br>' : ''}
                                        ${product.offer_end_date ? '<span class="offer-end">Bis: ' + product.offer_end_date + '</span>' : ''}
                                    </div>
                                </div>
                            </div>
                        `;
                    }
                    
                    tr.innerHTML = `
                        <td>
                            <input type="checkbox" data-product-id="${product.id}" ${product.sellable === '1' ? 'checked' : ''}>
                        </td>
                        <td>
                            ${product.image ? `<img src="${product.image}" alt="${product.name}" class="product-img">` : '<div style="width:50px;height:50px;background:#f5f5f5;border-radius:3px;display:flex;align-items:center;justify-content:center;color:#999;">📷</div>'}
                        </td>
                        <td>
                            ${paProductTitle(product)}
                            ${product.was_out_of_stock ? '<span style="margin-left: 8px; background: #d4edda; color: #155724; padding: 2px 6px; border-radius: 3px; font-size: 11px; font-weight: 600;">Restocked</span>' : ''}
                            ${product.was_low_stock ? '<span style="margin-left: 8px; background: #fff3cd; color: #856404; padding: 2px 6px; border-radius: 3px; font-size: 11px; font-weight: 600;">Low Stock Restocked</span>' : ''}
                            ${product.was_restocked && !product.was_out_of_stock && !product.was_low_stock ? '<span style="margin-left: 8px; background: #e2e3e5; color: #383d41; padding: 2px 6px; border-radius: 3px; font-size: 11px; font-weight: 600;">Restocked</span>' : ''}
                            ${product.is_new ? '<span style="margin-left: 8px; background: #cce5ff; color: #004085; padding: 2px 6px; border-radius: 3px; font-size: 11px; font-weight: 600;">Neu</span>' : ''}
                        </td>
                        <td style="text-align: right;">${product.stock}</td>
                        <td style="text-align: right;">${product.in_progress}</td>
                        <td style="text-align: right;" class="${stockClass}">${available}</td>
                        <td style="text-align: right;" class="price-cell">${product.price} €</td>
                        <td>${offerHtml}</td>
                        <td style="text-align: center;">
                            <button class="edit-btn" data-product-id="${product.id}" data-product-name="${product.name}" data-product-sku="${product.sku || ''}" data-product-new="${product.is_new ? 'true' : 'false'}" data-stock="${product.stock}" data-price="${product.price}" data-regular-price="${product.regular_price}" data-sale-price="${product.sale_price}" data-has-offer="${product.has_offer}" data-show-old-price="${product.show_old_price}" data-time-limited="${product.time_limited}" data-time-limit-type="${product.time_limit_type}" data-time-limit-duration="${product.time_limit_duration}" data-time-limit-date="${product.time_limit_date}" data-show-end-date="${product.show_end_date}" data-time-limit-days="${product.time_limit_days}" data-time-limit-hours="${product.time_limit_hours}" data-time-limit-minutes="${product.time_limit_minutes}" data-time-limit-time="${product.time_limit_time}" data-time-limit-date-only="${product.time_limit_date_only}" data-offer-start-date="${product.offer_start_date || ''}" title="Bearbeiten">✏️</button>
                            <button class="delete-btn" data-product-id="${product.id}" data-product-name="${product.name}" title="Löschen">🗑️</button>
                        </td>
                    `;

                    tbody.appendChild(tr);
                });
            });
        });
        
        // Checkbox-Event-Listener für Bulk-Sales
        document.querySelectorAll('input[type="checkbox"][data-product-id]:not(.event-attached)').forEach(checkbox => {
            checkbox.classList.add('event-attached');
            checkbox.addEventListener('change', function() {
                const productId = this.dataset.productId;
                const sellable = this.checked ? 1 : 0;
                updateSellStatus(productId, sellable);
            });
        });
        
        // Edit-Button Event-Listener für Bulk-Sales
        document.querySelectorAll('.edit-btn:not(.event-attached)').forEach(btn => {
            btn.classList.add('event-attached');
            btn.addEventListener('click', function() {
                openEditModal(this);
            });
        });

        document.querySelectorAll('.delete-btn:not(.event-attached)').forEach(btn => {
            btn.classList.add('event-attached');
            btn.addEventListener('click', function() {
                openDeleteConfirm(this.dataset.productId, this.dataset.productName);
            });
        });
    } catch (error) {
        console.error('Fehler in renderBulkSalesGroup:', error);
    }
}

function openBulkSaleDetails(saleId, saleTitle) {
    document.getElementById('bulk-sale-details-title').textContent = saleTitle;
    document.getElementById('bulk-sale-details-title').dataset.saleId = saleId;
    document.getElementById('bulk-sale-details-content').innerHTML = '<div style="text-align: center; padding: 20px;">Laden...</div>';
    document.getElementById('bulk-sale-details-modal').style.display = 'flex';
    
    fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'action=get_bulk_sale_details&nonce=<?php echo wp_create_nonce('product_list_nonce'); ?>&sale_id=' + saleId
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            renderBulkSaleDetails(data.data);
        } else {
            document.getElementById('bulk-sale-details-content').innerHTML = '<div style="text-align: center; padding: 20px; color: #e74c3c;">Fehler beim Laden: ' + (data.data || 'Unbekannter Fehler') + '</div>';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        document.getElementById('bulk-sale-details-content').innerHTML = '<div style="text-align: center; padding: 20px; color: #e74c3c;">Fehler beim Laden</div>';
    });
}

function renderBulkSaleDetails(saleData) {
    const container = document.getElementById('bulk-sale-details-content');
    let html = '';
    
    // Start- und Enddatum
    if (saleData.start_date) {
        html += `<div style="margin-bottom: 15px;"><strong>Startdatum:</strong> ${saleData.start_date}</div>`;
    }
    if (saleData.end_date) {
        html += `<div style="margin-bottom: 15px;"><strong>Enddatum:</strong> ${saleData.end_date}</div>`;
    }
    if (saleData.show_end_date) {
        html += `<div style="margin-bottom: 15px;"><strong>Enddatum anzeigen:</strong> Ja</div>`;
    }
    
    html += '<hr style="margin: 20px 0; border: none; border-top: 1px solid #ddd;">';
    
    // Gruppen rendern
    if (saleData.groups && Object.keys(saleData.groups).length > 0) {
        Object.keys(saleData.groups).forEach(groupId => {
            const group = saleData.groups[groupId];
            html += `
                <div style="margin-bottom: 30px; padding: 20px; border: 1px solid #ddd; border-radius: 8px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                        <h4 style="margin: 0; font-size: 18px; color: #333;">${group.name}</h4>
                        ${group.has_offer ? '<span style="background: #d4edda; color: #155724; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 600;">Angebot aktiv</span>' : ''}
                    </div>
                    ${group.has_offer ? `
                        <div style="margin-bottom: 15px; padding: 10px; background: #f9f9f9; border-radius: 4px;">
                            <div style="margin-bottom: 5px;"><strong>Preistyp:</strong> ${group.discount_type === 'percent' ? 'Prozent' : 'Festbetrag'}</div>
                            <div style="margin-bottom: 5px;"><strong>Rabatt:</strong> ${group.discount_amount} ${group.discount_type === 'percent' ? '%' : '€'}</div>
                            ${group.show_old_price ? '<div style="margin-bottom: 5px;"><strong>Alter Preis anzeigen:</strong> Ja</div>' : ''}
                        </div>
                    ` : ''}
                    <div style="margin-bottom: 10px;"><strong>${group.products ? group.products.length : 0} Produkte in dieser Gruppe</strong></div>
                    <div style="max-height: 200px; overflow-y: auto; border: 1px solid #eee; border-radius: 4px;">
                        ${group.products && group.products.length > 0 ? renderProductList(group.products) : '<div style="padding: 20px; text-align: center; color: #888;">Keine Produkte</div>'}
                    </div>
                </div>
            `;
        });
    } else {
        html += '<div style="text-align: center; padding: 20px; color: #888;">Keine Gruppen</div>';
    }
    
    container.innerHTML = html;
}

function renderProductList(productIds) {
    let html = '';
    productIds.forEach(productId => {
        const product = allProducts.find(p => p.id == productId);
        if (product) {
            html += `
                <div style="padding: 10px; border-bottom: 1px solid #eee; display: flex; align-items: center;">
                    ${product.image ? `<img src="${product.image}" alt="${product.name}" style="width: 40px; height: 40px; object-fit: cover; margin-right: 10px; border-radius: 4px;">` : ''}
                    <div>
                        <div style="font-weight: 500;">${product.name}</div>
                        <div style="font-size: 12px; color: #666;">${product.price} €</div>
                    </div>
                </div>
            `;
        }
    });
    return html;
}

function deleteBulkSale(saleId) {
    fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'action=delete_bulk_sale&nonce=<?php echo wp_create_nonce('product_list_nonce'); ?>&sale_id=' + saleId
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('bulk-sale-details-modal').style.display = 'none';
            loadProducts();
        } else {
            alert('Fehler beim Löschen: ' + (data.data || 'Unbekannter Fehler'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Fehler beim Löschen');
    });
}

function markOfferAsRead(productId, isRead) {
    fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'action=mark_offer_as_read&nonce=<?php echo wp_create_nonce('product_list_nonce'); ?>&product_id=' + productId + '&is_read=' + (isRead ? 1 : 0)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            console.log('Angebot als gelesen markiert');
            // Liste neu laden
            renderProducts(allProducts);
        }
    })
    .catch(error => console.error('Error:', error));
}

// Edit-Button Event-Listener hinzufügen (initial)
document.querySelectorAll('.edit-btn:not(.event-attached)').forEach(btn => {
    btn.classList.add('event-attached');
    btn.addEventListener('click', function() {
        openEditModal(this);
    });
});

// Toggle-Switch Event-Listener hinzufügen
document.getElementById('group-by-offer-toggle').addEventListener('change', function() {
    renderProducts(allProducts);
});

function filterProducts(searchTerm) {
    const rows = document.querySelectorAll('#product-table-body tr');
    const term = searchTerm.toLowerCase();
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        const editBtn = row.querySelector('.edit-btn');
        const sku = editBtn ? (editBtn.dataset.productSku || '').toLowerCase() : '';
        const isNew = editBtn ? (editBtn.dataset.productNew === 'true') : false;
        
        let match = text.includes(term) || sku.includes(term);
        
        // Spezielle Suche nach "neu"
        if (term === 'neu' || term === 'new') {
            match = isNew;
        }
        
        if (match) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

function updateSellStatus(productId, isSellable) {
    fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'action=update_sell_status&nonce=<?php echo wp_create_nonce('product_list_nonce'); ?>&product_id=' + productId + '&is_sellable=' + (isSellable ? 1 : 0)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            console.log('Verkaufen-Status aktualisiert');
        }
    })
    .catch(error => console.error('Error:', error));
}

// ── Globale Variablen für das Bearbeiten-Modal ───────────────────────────────
var _plAjax  = '<?php echo admin_url('admin-ajax.php'); ?>';
var _plNonce = '<?php echo wp_create_nonce('product_list_nonce'); ?>';
var _editProductId     = null;
var _editProductDetail = null;
var _editBtnData       = null; // Offer-Daten vom Edit-Button

// ── Botanischer Produkttitel (zwei Zeilen: Gattung+Art klein, Kultivar groß) ──
function paProductTitle(product) {
    var g = product.gattung || '', a = product.art || '', k = product.kultivar || '';
    var genus = [g, a].filter(Boolean).join(' ');
    if (!g && !a && !k) {
        return '<a href="' + product.permalink + '" target="_blank" class="product-name-link">' + _rcEsc(product.name) + '</a>';
    }
    var main = k ? '‘' + _rcEsc(k) + '’' : (_rcEsc(genus) || _rcEsc(product.name));
    return (genus ? '<div style="font-size:10px;color:var(--creme-muted);font-style:italic;margin-bottom:1px;">' + _rcEsc(genus) + '</div>' : '')
        + '<a href="' + product.permalink + '" target="_blank" class="product-name-link">' + main + '</a>';
}

// ── Felddefinitionen ─────────────────────────────────────────────────────────
var EDIT_FIELDS = [
    { key:'gattung',                label:'Gattung',                 type:'combobox', placeholder:'z. B. Pelargonium' },
    { key:'art',                    label:'Art',                     type:'combobox', placeholder:'z. B. zonale' },
    { key:'kultivar',               label:'Kultivar',                type:'text', placeholder:'z. B. Thai Constellation' },
    { key:'sku',                    label:'Art. Nr',                 type:'text' },
    { key:'price',                  label:'Preis',                   type:'number', step:'0.01', min:'0', suffix:'€' },
    { key:'stock',                  label:'Bestand',                 type:'number', min:'0' },
    { key:'tax_class',              label:'Steuerklasse',            type:'select', optKey:'tax_class_options',      valKey:'tax_class',         lblKey:'name', valAttr:'slug' },
    { key:'product_type',           label:'Typ',                     type:'select', staticOpts:[{v:'pflanze',l:'Pflanze'},{v:'substrat',l:'Substrat'}] },
    { key:'unit_type',              label:'Einheit',                 type:'select', staticOpts:[{v:'stueck',l:'Stück'},{v:'liter',l:'Liter'}] },
    { key:'product_liters',         label:'Litermenge',              type:'number', step:'0.1', min:'0', suffix:'L', showWhen:{k:'unit_type',v:'liter'} },
    { key:'differential_taxation',  label:'Differenzbesteuerung',   type:'bool' },
    { key:'backorders',             label:'Lieferrückstand',         type:'select', staticOpts:[{v:'no',l:'Nein'},{v:'yes',l:'Ja'},{v:'notify',l:'Benachrichtigen'}] },
    { key:'low_stock_threshold',    label:'Lager-Schwellwert',       type:'number', min:'0' },
    { key:'never_low_stock',        label:'Nie geringer Lagerbestand', type:'bool' },
    { key:'weight',                 label:'Gewicht',                 type:'number', step:'0.001', min:'0', suffix:'kg' },
    { key:'dimensions',             label:'Maße (L×B×H)',            type:'dimensions' },
    { key:'shipping_class',         label:'Versandklasse',           type:'select', optKey:'shipping_class_options', valKey:'shipping_class_id', lblKey:'name', valAttr:'id' },
    { key:'delivery_time',          label:'Lieferzeit',              type:'text', placeholder:'z. B. 3–5 Werktage' },
    { key:'_care_header',           label:'Pflege-Infos',            type:'divider' },
    { key:'care_light',             label:'Licht (bevorzugt)',       type:'select', staticOpts:[{v:'',l:'— nicht angegeben —'},{v:'vollsonne',l:'Vollsonne'},{v:'sonnig',l:'Sonnig'},{v:'halbschatten',l:'Halbschatten'},{v:'schatten',l:'Schatten'}] },
    { key:'care_light_tolerates_min', label:'Licht verträgt (von)',  type:'select', staticOpts:[{v:'',l:'— wie bevorzugt —'},{v:'vollsonne',l:'Vollsonne'},{v:'sonnig',l:'Sonnig'},{v:'halbschatten',l:'Halbschatten'},{v:'schatten',l:'Schatten'}] },
    { key:'care_light_tolerates_max', label:'Licht verträgt (bis)',  type:'select', staticOpts:[{v:'',l:'— wie bevorzugt —'},{v:'vollsonne',l:'Vollsonne'},{v:'sonnig',l:'Sonnig'},{v:'halbschatten',l:'Halbschatten'},{v:'schatten',l:'Schatten'}] },
    { key:'care_water',             label:'Wasser (bevorzugt)',      type:'select', staticOpts:[{v:'',l:'— nicht angegeben —'},{v:'viel',l:'Viel (feucht halten)'},{v:'maessig',l:'Mäßig'},{v:'wenig',l:'Wenig (trocken halten)'}] },
    { key:'care_water_tolerates_min', label:'Wasser verträgt (von)', type:'select', staticOpts:[{v:'',l:'— wie bevorzugt —'},{v:'viel',l:'Viel (feucht halten)'},{v:'maessig',l:'Mäßig'},{v:'wenig',l:'Wenig (trocken halten)'}] },
    { key:'care_water_tolerates_max', label:'Wasser verträgt (bis)', type:'select', staticOpts:[{v:'',l:'— wie bevorzugt —'},{v:'viel',l:'Viel (feucht halten)'},{v:'maessig',l:'Mäßig'},{v:'wenig',l:'Wenig (trocken halten)'}] },
    { key:'care_winter',            label:'Überwinterung',           type:'text', placeholder:'z. B. Frostfrei (>10 °C), Frostfest …' },
    { key:'care_winterhaerte',      label:'Winterhärte',             type:'select', staticOpts:[{v:'',l:'— nicht angegeben —'},{v:'nicht-wh',l:'Nicht winterhart'},{v:'bedingt-wh',l:'Bedingt winterhart (bis ca. −5 °C)'},{v:'winterhart',l:'Winterhart (bis ca. −10 °C)'},{v:'sehr-wh',l:'Sehr winterhart (bis ca. −15 °C)'},{v:'voll-wh',l:'Vollwinterhart (bis −20 °C und kälter)'}] },
    { key:'care_temp_min',          label:'Temp. min',               type:'number', min:'-40', suffix:'°C' },
    { key:'care_temp_max',          label:'Temp. max',               type:'number', min:'-40', suffix:'°C' },
];

function _efEsc(s) {
    return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

function _efDisplayVal(f, p) {
    var val;
    switch (f.type) {
        case 'text': case 'number': case 'combobox':
            val = p[f.key];
            if (val === '' || val === null || val === undefined) return '–';
            return String(val) + (f.suffix ? ' ' + f.suffix : '');
        case 'bool':
            return p[f.key] == 1 ? 'Ja' : 'Nein';
        case 'select':
            var opts   = f.optKey ? (p[f.optKey] || []) : (f.staticOpts || []);
            var curVal = p[f.valKey || f.key];
            for (var i = 0; i < opts.length; i++) {
                var o    = opts[i];
                var oVal = f.valAttr ? o[f.valAttr] : (o.v !== undefined ? o.v : o[f.valAttr]);
                if (oVal === undefined) oVal = o.v;
                if (String(oVal) === String(curVal)) return o[f.lblKey || 'l'] || o.l || o.name || oVal;
            }
            return curVal !== undefined && curVal !== '' ? String(curVal) : '–';
        case 'dimensions':
            var l = p.length, w = p.width, h = p.height;
            if (!l && !w && !h) return '–';
            return (l||'?') + ' × ' + (w||'?') + ' × ' + (h||'?') + ' cm';
        default:
            return p[f.key] || '–';
    }
}

function _efInputHtml(f, p) {
    var inp = '';
    var suf = f.suffix ? '<span style="font-size:12px;color:#888;margin-left:4px;">'+f.suffix+'</span>' : '';
    switch (f.type) {
        case 'combobox':
            var _cbVal  = p[f.key] || '';
            var _cbPh   = f.placeholder || '';
            var _cbOpts = f.key === 'gattung'
                ? (_plTaxPool.gattungen || [])
                : (_plTaxPool.arts[p.gattung || ''] || []);
            var _cbOptHtml = '<button type="button" class="pa-combo-new-btn" onclick="_plComboOpenAdd(\''+f.key+'\',event)">+ Neu hinzufügen</button>'
                + '<div class="pa-combo-options" id="pl-combo-opts-'+f.key+'">'
                + _cbOpts.map(function(o){
                    return '<button type="button" class="pa-combo-option'+(_cbVal===o?' selected':'')+'" data-val="'+_efEsc(o)+'" onclick="_plComboSelect(\''+f.key+'\',\''+_efEsc(o)+'\')">'+_efEsc(o)+'</button>';
                  }).join('')
                + '</div>';
            return '<div class="pa-combo" id="pl-combo-'+f.key+'" style="flex:1;">'
                 + '<div class="pa-combo-trigger" onclick="_plComboToggle(\''+f.key+'\')">'
                 + '<span class="pa-combo-val'+(_cbVal?'':' pa-combo-placeholder')+'" id="pl-combo-lbl-'+f.key+'">'
                 + (_cbVal ? _efEsc(_cbVal) : _efEsc(_cbPh)) + '</span>'
                 + '<svg class="pa-combo-chev" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9l6 6 6-6"/></svg>'
                 + '</div>'
                 + '<div class="pa-combo-drop" id="pl-combo-drop-'+f.key+'" style="display:none">' + _cbOptHtml + '</div>'
                 + '<input type="hidden" id="efi-'+f.key+'" value="'+_efEsc(_cbVal)+'">'
                 + '</div>';
        case 'text':
            inp = '<input type="text" id="efi-'+f.key+'" class="ef-inp" value="'+_efEsc(p[f.key]||'')+'" style="flex:1;"'+(f.placeholder?' placeholder="'+_efEsc(f.placeholder)+'"':'')+'>';
            return inp;
        case 'number':
            inp = '<input type="number" id="efi-'+f.key+'" class="ef-inp" value="'+_efEsc(p[f.key]||'')+'"'
                + (f.step ? ' step="'+f.step+'"' : '') + (f.min ? ' min="'+f.min+'"' : '')
                + ' style="width:90px;">';
            return inp + suf;
        case 'bool':
            return '<label style="display:flex;align-items:center;gap:6px;font-size:13px;cursor:pointer;">'
                 + '<input type="checkbox" id="efi-'+f.key+'"'+(p[f.key]==1?' checked':'')+'>Ja</label>';
        case 'select':
            var opts   = f.optKey ? (p[f.optKey] || []) : (f.staticOpts || []);
            var curVal = p[f.valKey || f.key];
            inp = '<select id="efi-'+f.key+'" class="ef-inp">';
            opts.forEach(function(o) {
                var oVal = f.valAttr ? o[f.valAttr] : (o.v !== undefined ? o.v : undefined);
                if (oVal === undefined) oVal = o.v;
                var oLbl = o[f.lblKey || 'l'] || o.l || o.name || oVal;
                inp += '<option value="'+_efEsc(String(oVal))+'"'+(String(oVal)===String(curVal)?' selected':'')+'>'+_efEsc(String(oLbl))+'</option>';
            });
            inp += '</select>';
            return inp;
        case 'dimensions':
            return '<input type="number" id="efi-length" placeholder="L" value="'+_efEsc(p.length||'')+'" step="0.1" min="0" class="ef-inp" style="width:55px;">'
                 + '<span style="font-size:12px;color:#888;margin:0 2px;">×</span>'
                 + '<input type="number" id="efi-width"  placeholder="B" value="'+_efEsc(p.width||'')+'"  step="0.1" min="0" class="ef-inp" style="width:55px;">'
                 + '<span style="font-size:12px;color:#888;margin:0 2px;">×</span>'
                 + '<input type="number" id="efi-height" placeholder="H" value="'+_efEsc(p.height||'')+'" step="0.1" min="0" class="ef-inp" style="width:55px;">'
                 + '<span style="font-size:12px;color:#888;margin-left:4px;">cm</span>';
        default:
            return '<input type="text" id="efi-'+f.key+'" class="ef-inp" value="'+_efEsc(p[f.key]||'')+'" style="flex:1;">';
    }
}

function _efGetSaveVal(f) {
    if (f.type === 'bool') {
        return document.getElementById('efi-'+f.key).checked ? 1 : 0;
    } else if (f.type === 'dimensions') {
        return {
            length: document.getElementById('efi-length').value,
            width:  document.getElementById('efi-width').value,
            height: document.getElementById('efi-height').value
        };
    } else {
        return document.getElementById('efi-'+f.key).value;
    }
}

function renderEditFields(p) {
    var html = '';
    EDIT_FIELDS.forEach(function(f) {
        if (f.type === 'divider') {
            html += '<div class="ef-divider">'+_efEsc(f.label)+'</div>';
            return;
        }
        if (f.showWhen && p[f.showWhen.k] !== f.showWhen.v) return;
        var disp = _efDisplayVal(f, p);
        html += '<div class="ef-row" data-field="'+f.key+'">'
             + '<span class="ef-label">'+f.label+'</span>'
             + '<div style="flex:1;display:flex;align-items:center;gap:5px;">'
             +   '<span class="ef-display">'+_efEsc(String(disp))+'</span>'
             +   '<div class="ef-input-wrap">'+_efInputHtml(f,p)
             +     '<button class="ef-cancel-btn" onclick="cancelFieldEdit(\''+f.key+'\')">✕</button>'
             +     '<button class="ef-save-btn"   onclick="saveField(\''+f.key+'\')">💾</button>'
             +   '</div>'
             + '</div>'
             + '<button class="ef-pencil" onclick="startFieldEdit(\''+f.key+'\')">✏</button>'
             + '</div>';
    });
    document.getElementById('edit-fields-list').innerHTML = html;
}

function startFieldEdit(key) {
    // Alle anderen Edits abbrechen
    document.querySelectorAll('.ef-row').forEach(function(row) {
        if (row.dataset.field !== key) _cancelRowEdit(row);
    });
    var row = document.querySelector('.ef-row[data-field="'+key+'"]');
    if (!row) return;
    row.querySelector('.ef-display').style.display   = 'none';
    row.querySelector('.ef-input-wrap').classList.add('active');
    row.querySelector('.ef-pencil').style.display    = 'none';
    var first = row.querySelector('input, select');
    if (first) first.focus();
}

function cancelFieldEdit(key) {
    var row = document.querySelector('.ef-row[data-field="'+key+'"]');
    if (row) _cancelRowEdit(row);
}

function _cancelRowEdit(row) {
    if (!row) return;
    row.querySelector('.ef-display').style.display = '';
    var iw = row.querySelector('.ef-input-wrap');
    if (iw) iw.classList.remove('active');
    var pen = row.querySelector('.ef-pencil');
    if (pen) pen.style.display = '';
}

function saveField(key) {
    var f = EDIT_FIELDS.find(function(x){ return x.key === key; });
    if (!f || !_editProductId) return;
    var val = _efGetSaveVal(f);
    var saveBtn = document.querySelector('.ef-row[data-field="'+key+'"] .ef-save-btn');
    if (saveBtn) { saveBtn.disabled = true; saveBtn.textContent = '…'; }

    fetch(_plAjax, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'action=update_product_field&nonce='+_plNonce
            + '&product_id='+_editProductId
            + '&field='+encodeURIComponent(key)
            + '&value='+encodeURIComponent(JSON.stringify(val))
    })
    .then(function(r){ return r.json(); })
    .then(function(data) {
        if (data.success) {
            // Lokalen Cache aktualisieren
            if (f.type === 'dimensions') {
                _editProductDetail.length = val.length;
                _editProductDetail.width  = val.width;
                _editProductDetail.height = val.height;
            } else if (f.valKey) {
                _editProductDetail[f.valKey] = val;
            } else {
                _editProductDetail[key] = (f.type === 'bool') ? (val ? 1 : 0) : val;
            }
            // Anzeigewert aktualisieren
            var row = document.querySelector('.ef-row[data-field="'+key+'"]');
            if (row) {
                var newDisp = _efDisplayVal(f, _editProductDetail);
                row.querySelector('.ef-display').textContent = String(newDisp);
            }
            cancelFieldEdit(key);
            loadProducts(); // Produktliste im Hintergrund aktualisieren
        } else {
            alert('Fehler: ' + (data.data || 'Unbekannt'));
            if (saveBtn) { saveBtn.disabled = false; saveBtn.textContent = '💾'; }
        }
    })
    .catch(function() {
        alert('Fehler beim Speichern');
        if (saveBtn) { saveBtn.disabled = false; saveBtn.textContent = '💾'; }
    });
}

// ── Panel-Navigation ──────────────────────────────────────────────────────────
function switchEditPanel(name) {
    ['main','offer','images','description','past-sales','stock'].forEach(function(p) {
        var el = document.getElementById('edit-panel-' + p);
        if (!el) return;
        el.classList.remove('active');
    });
    var active = document.getElementById('edit-panel-' + name);
    if (active) active.classList.add('active');
}

// ── Angebot-Panel öffnen ──────────────────────────────────────────────────────
function openOfferPanel() {
    if (!_editBtnData) return;
    var d = _editBtnData;
    document.getElementById('modal-stock').value            = d.stock;
    document.getElementById('modal-price').value            = d.regularPrice || d.price;
    document.getElementById('modal-has-offer').checked      = d.hasOffer;
    document.getElementById('modal-sale-price').value       = d.salePrice || '';
    document.getElementById('modal-sale-percent').value     = '';
    document.getElementById('modal-price-type-toggle').checked = false;
    document.getElementById('modal-show-old-price').checked = d.showOldPrice;
    document.getElementById('modal-time-limited').checked   = d.timeLimited;
    document.getElementById('modal-time-limit-toggle').checked = (d.timeLimitType !== 'date');
    document.getElementById('modal-time-limit-days').value  = d.timeLimitDays || '';
    document.getElementById('modal-time-limit-hours').value = d.timeLimitHours || '';
    document.getElementById('modal-time-limit-minutes').value = d.timeLimitMinutes || '';
    document.getElementById('modal-time-limit-date').value  = d.timeLimitDateOnly || d.timeLimitDate || '';
    document.getElementById('modal-time-limit-time').value  = d.timeLimitTime || '';
    document.getElementById('modal-show-end-date').checked  = d.showEndDate;

    window.offerStartDate = d.offerStartDate || '';
    document.getElementById('modal-offer-start-display').textContent = d.offerStartDate ? 'Start: ' + d.offerStartDate : '';

    document.getElementById('offer-fields').style.display      = d.hasOffer ? 'block' : 'none';
    document.getElementById('time-limit-fields').style.display = (d.hasOffer && d.timeLimited) ? 'block' : 'none';
    document.getElementById('euro-price-field').style.display  = 'block';
    document.getElementById('percent-price-field').style.display = 'none';
    document.getElementById('date-field').style.display         = (d.timeLimitType === 'date') ? 'block' : 'none';
    document.getElementById('duration-field').style.display     = (d.timeLimitType !== 'date') ? 'block' : 'none';

    var pid = _editProductId;
    document.getElementById('save-edit').onclick   = function() { saveProductEdit(pid); };
    document.getElementById('cancel-edit').onclick = function() { switchEditPanel('main'); };

    document.getElementById('modal-has-offer').onchange = function() {
        document.getElementById('offer-fields').style.display = this.checked ? 'block' : 'none';
        if (!this.checked) document.getElementById('time-limit-fields').style.display = 'none';
    };
    document.getElementById('modal-offer-start-btn').onclick = function() {
        document.getElementById('offer-start-modal').style.display = 'flex';
    };
    document.getElementById('modal-price-type-toggle').onchange = function() {
        document.getElementById('euro-price-field').style.display    = this.checked ? 'none' : 'block';
        document.getElementById('percent-price-field').style.display = this.checked ? 'block' : 'none';
    };
    document.getElementById('modal-sale-percent').oninput = function() {
        var pct = parseFloat(this.value), reg = parseFloat(document.getElementById('modal-price').value);
        if (!isNaN(pct) && !isNaN(reg) && pct > 0)
            document.getElementById('modal-sale-price').value = (reg - reg * pct / 100).toFixed(2);
    };
    document.getElementById('modal-time-limited').onchange = function() {
        document.getElementById('time-limit-fields').style.display = this.checked ? 'block' : 'none';
    };
    document.getElementById('modal-time-limit-toggle').onchange = function() {
        document.getElementById('date-field').style.display     = this.checked ? 'none'  : 'block';
        document.getElementById('duration-field').style.display = this.checked ? 'block' : 'none';
    };

    switchEditPanel('offer');
}

// ── Bilder-Panel öffnen ───────────────────────────────────────────────────────
function openImagesPanel() {
    switchEditPanel('images');
    document.getElementById('images-loading').style.display = 'block';
    document.getElementById('images-grid').innerHTML        = '';
    fetch(_plAjax, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'action=get_product_detail&nonce='+_plNonce+'&product_id='+_editProductId
    })
    .then(function(r){ return r.json(); })
    .then(function(data) {
        document.getElementById('images-loading').style.display = 'none';
        if (!data.success) return;
        var d     = data.data;
        var grid  = document.getElementById('images-grid');
        var imgs  = [];
        if (d.featured_image && d.featured_image.id) {
            imgs.push({ id: d.featured_image.id, url: d.featured_image.url, featured: true });
        }
        (d.gallery || []).forEach(function(g) { imgs.push({ id: g.id, url: g.url, featured: false }); });

        if (imgs.length === 0) {
            grid.innerHTML = '<p style="color:#aaa;font-size:13px;">Keine Bilder vorhanden.</p>';
        } else {
            imgs.forEach(function(img) {
                var el = document.createElement('div');
                el.className = 'em-img-thumb';
                el.innerHTML = '<img src="'+_efEsc(img.url)+'" alt="">'
                             + (img.featured ? '<span class="em-img-featured">Titelbild</span>' : '')
                             + '<button class="em-img-del" onclick="deleteProductImage('+img.id+','+img.featured+')">✕</button>';
                grid.appendChild(el);
            });
        }
    });
}

function deleteProductImage(imageId, isFeatured) {
    if (!confirm('Bild wirklich entfernen?')) return;
    fetch(_plAjax, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'action=delete_product_image&nonce='+_plNonce+'&product_id='+_editProductId+'&image_id='+imageId
    })
    .then(function(r){ return r.json(); })
    .then(function(data) {
        if (data.success) { openImagesPanel(); loadProducts(); }
        else alert('Fehler: ' + (data.data || 'Unbekannt'));
    });
}

function uploadProductImage(input) {
    if (!input.files || !input.files[0]) return;
    var file = input.files[0];
    input.value = '';
    var reader = new FileReader();
    reader.onload = function(e) {
        cwmOpen(e.target.result, function(attachmentId, thumbUrl) {
            openImagesPanel();
            loadProducts();
        });
    };
    reader.readAsDataURL(file);
}

// ── Beschreibungs-Panel öffnen ────────────────────────────────────────────────
function openDescriptionPanel() {
    switchEditPanel('description');
    var desc      = (_editProductDetail && _editProductDetail.description)       ? _editProductDetail.description       : '';
    var shortDesc = (_editProductDetail && _editProductDetail.short_description) ? _editProductDetail.short_description : '';
    document.getElementById('desc-editor').innerHTML    = desc;
    document.getElementById('edit-short-desc').value   = shortDesc;
    var cnt = document.getElementById('edit-short-desc-count');
    var lbl = document.getElementById('edit-short-desc-limit-label');
    cnt.textContent = shortDesc.length;
    var col = shortDesc.length > _EDIT_SHORT_LIMIT ? '#c0392b' : (shortDesc.length > _EDIT_SHORT_LIMIT * 0.85 ? '#e67e22' : '');
    cnt.style.color = col;
    if (lbl) lbl.style.color = col || '#bbb';
}

var _EDIT_SHORT_LIMIT = 160;
(function() {
    var ta      = document.getElementById('edit-short-desc');
    var counter = document.getElementById('edit-short-desc-count');
    var label   = document.getElementById('edit-short-desc-limit-label');
    if (!ta || !counter) return;
    function updateEditCount() {
        var len = ta.value.length;
        counter.textContent = len;
        if (len > _EDIT_SHORT_LIMIT) {
            counter.style.color = '#c0392b';
            if (label) label.style.color = '#c0392b';
        } else if (len > _EDIT_SHORT_LIMIT * 0.85) {
            counter.style.color = '#e67e22';
            if (label) label.style.color = '#e67e22';
        } else {
            counter.style.color = '';
            if (label) label.style.color = '#bbb';
        }
    }
    ta.addEventListener('input', updateEditCount);
})();

function descCmd(cmd) {
    document.getElementById('desc-editor').focus();
    document.execCommand(cmd, false, null);
}
function descHeading(tag) {
    document.getElementById('desc-editor').focus();
    document.execCommand('formatBlock', false, tag);
}
function descLink() {
    var url = prompt('URL eingeben:', 'https://');
    if (url) {
        document.getElementById('desc-editor').focus();
        document.execCommand('createLink', false, url);
    }
}

function saveDescription() {
    var html      = document.getElementById('desc-editor').innerHTML;
    var shortDesc = document.getElementById('edit-short-desc').value;
    if (shortDesc.length > _EDIT_SHORT_LIMIT) {
        var ok = confirm(
            'Die Kurzbeschreibung ist ' + shortDesc.length + ' Zeichen lang (empfohlen: max. ' + _EDIT_SHORT_LIMIT + ').\n\n' +
            'Du kannst trotzdem speichern – überprüfe aber vorher, ob es auf der Produktseite gut aussieht.\n\n' +
            'Trotzdem speichern?'
        );
        if (!ok) return;
    }

    function doSave(field, value) {
        return fetch(_plAjax, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'action=update_product_field&nonce='+_plNonce
                + '&product_id='+_editProductId
                + '&field='+field
                + '&value='+encodeURIComponent(JSON.stringify(value))
        }).then(function(r){ return r.json(); });
    }

    Promise.all([doSave('description', html), doSave('short_description', shortDesc)])
        .then(function(results) {
            if (results.every(function(d){ return d.success; })) {
                if (_editProductDetail) {
                    _editProductDetail.description       = html;
                    _editProductDetail.short_description = shortDesc;
                }
                switchEditPanel('main');
            } else {
                alert('Fehler beim Speichern der Beschreibung.');
            }
        });
}

// ── Vergangene-Sales-Panel öffnen ─────────────────────────────────────────────
function openPastSalesPanel() {
    switchEditPanel('past-sales');
    document.getElementById('past-sales-loading').style.display = 'block';
    document.getElementById('past-sales-list').style.display    = 'none';
    document.getElementById('past-sales-list').innerHTML        = '';
    fetch(_plAjax, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'action=get_product_past_sales&nonce='+_plNonce+'&product_id='+_editProductId
    })
    .then(function(r){ return r.json(); })
    .then(function(data) {
        document.getElementById('past-sales-loading').style.display = 'none';
        var list = document.getElementById('past-sales-list');
        list.style.display = 'block';
        if (!data.success || !data.data.length) {
            list.innerHTML = '<p style="color:#aaa;font-size:13px;">Keine vergangenen Sales gefunden.</p>';
            return;
        }
        var html = '';
        data.data.forEach(function(s) {
            var badge = s.is_past
                ? '<span style="font-size:11px;background:#eee;color:#666;padding:1px 6px;border-radius:10px;margin-left:6px;">abgeschlossen</span>'
                : '<span style="font-size:11px;background:#d4edda;color:#155724;padding:1px 6px;border-radius:10px;margin-left:6px;">aktiv</span>';
            html += '<div style="padding:14px 0;border-bottom:1px solid #f0f0f0;">'
                  +   '<div style="font-weight:600;font-size:14px;margin-bottom:4px;">'+_efEsc(s.title)+badge+'</div>'
                  +   '<div style="font-size:12px;color:#888;display:flex;gap:16px;flex-wrap:wrap;">'
                  +     (s.start_date ? '<span>📅 '+_efEsc(s.start_date)+' – '+_efEsc(s.end_date||'?')+'</span>' : '')
                  +     (s.discount   ? '<span>🏷 '+_efEsc(s.discount)+'</span>'   : '')
                  +     '<span>✓ '+s.sold+' verkauft</span>'
                  +   '</div>'
                  + '</div>';
        });
        list.innerHTML = html;
    });
}

// ── Haupt-openEditModal ───────────────────────────────────────────────────────
function openEditModal(btn) {
    _editProductId = btn.dataset.productId;
    _editBtnData   = {
        stock:          btn.dataset.stock,
        price:          btn.dataset.price,
        regularPrice:   btn.dataset.regularPrice,
        salePrice:      btn.dataset.salePrice,
        hasOffer:       btn.dataset.hasOffer === 'true',
        showOldPrice:   btn.dataset.showOldPrice === '1',
        timeLimited:    btn.dataset.timeLimited === '1',
        timeLimitType:  btn.dataset.timeLimitType  || 'days',
        timeLimitDate:  btn.dataset.timeLimitDate  || '',
        timeLimitDateOnly: btn.dataset.timeLimitDateOnly || '',
        timeLimitTime:  btn.dataset.timeLimitTime  || '',
        timeLimitDays:  btn.dataset.timeLimitDays  || '',
        timeLimitHours: btn.dataset.timeLimitHours || '',
        timeLimitMinutes: btn.dataset.timeLimitMinutes || '',
        showEndDate:    btn.dataset.showEndDate === '1',
        offerStartDate: btn.dataset.offerStartDate || '',
    };

    document.getElementById('edit-modal-title').textContent = btn.dataset.productName || 'Produkt bearbeiten';
    document.getElementById('edit-modal-loading').style.display  = 'block';
    document.getElementById('edit-fields-list').style.display    = 'none';
    document.getElementById('edit-modal-nav').style.display      = 'none';
    document.getElementById('edit-modal-actions').style.display  = 'none';
    switchEditPanel('main');
    document.getElementById('edit-modal').style.display = 'flex';

    fetch(_plAjax, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'action=get_product_detail&nonce='+_plNonce+'&product_id='+_editProductId
    })
    .then(function(r){ return r.json(); })
    .then(function(data) {
        if (data.success) {
            _editProductDetail = data.data;
            renderEditFields(_editProductDetail);
            document.getElementById('edit-modal-loading').style.display  = 'none';
            document.getElementById('edit-fields-list').style.display    = 'block';
            document.getElementById('edit-modal-nav').style.display      = 'block';
            document.getElementById('edit-modal-actions').style.display  = 'flex';
        } else {
            document.getElementById('edit-modal-loading').textContent = 'Fehler beim Laden';
        }
    })
    .catch(function() {
        document.getElementById('edit-modal-loading').textContent = 'Fehler beim Laden';
    });

    // offer-start-modal Events einrichten
    document.getElementById('cancel-offer-start').onclick = function() {
        document.getElementById('offer-start-modal').style.display = 'none';
    };
    document.getElementById('save-offer-start').onclick = function() {
        var date = document.getElementById('offer-start-date').value;
        var time = document.getElementById('offer-start-time').value;
        if (date && time) {
            window.offerStartDate = date + ' ' + time;
            document.getElementById('modal-offer-start-display').textContent = 'Start: ' + window.offerStartDate;
            document.getElementById('offer-start-modal').style.display = 'none';
        } else {
            alert('Bitte Datum und Uhrzeit eingeben');
        }
    };
}

function closeEditModal() {
    document.getElementById('edit-modal').style.display = 'none';
}

// Modal schließen bei Klick außerhalb
document.getElementById('edit-modal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeEditModal();
    }
});

// Dynamischer Countdown
let isReloading = false;

function updateCountdowns() {
    const countdowns = document.querySelectorAll('.countdown');
    const now = Math.floor(Date.now() / 1000);
    
    countdowns.forEach(function(countdown) {
        const dateTo = parseInt(countdown.dataset.countdown);
        const productId = countdown.dataset.productId;
        const diff = dateTo - now;
        
        if (diff > 0) {
            const hours = Math.floor(diff / (60 * 60));
            const minutes = Math.floor((diff % (60 * 60)) / 60);
            const seconds = diff % 60;
            
            const h = String(hours).padStart(2, '0');
            const m = String(minutes).padStart(2, '0');
            const s = String(seconds).padStart(2, '0');
            
            countdown.textContent = h + ':' + m + ':' + s;
        } else {
            countdown.textContent = 'Abgelaufen';
            countdown.style.color = '#e74c3c';
            countdown.classList.remove('countdown');
            // Zuerst AJAX-Call für Cleanup, dann Seite neu laden (nur einmal)
            if (!isReloading && productId) {
                isReloading = true;
                
                // AJAX-Call zum Bereinigen des abgelaufenen Angebots
                fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'action=cleanup_expired_sale&product_id=' + productId
                })
                .then(response => response.json())
                .then(data => {
                    console.log('Cleanup result:', data);
                    // Seite neu laden nach erfolgreichem Cleanup
                    location.reload();
                })
                .catch(error => {
                    console.error('Cleanup error:', error);
                    // Trotzdem neu laden bei Fehler
                    location.reload();
                });
            }
        }
    });
}

// Countdown jede Sekunde aktualisieren
setInterval(updateCountdowns, 1000);
updateCountdowns();

function saveProductEdit(productId) {
    const stock = document.getElementById('modal-stock').value;
    const price = document.getElementById('modal-price').value;
    const hasOffer = document.getElementById('modal-has-offer').checked;
    const salePrice = document.getElementById('modal-sale-price').value;
    const showOldPrice = document.getElementById('modal-show-old-price').checked;
    const timeLimited = document.getElementById('modal-time-limited').checked;
    const timeLimitToggle = document.getElementById('modal-time-limit-toggle').checked;
    const timeLimitDays = document.getElementById('modal-time-limit-days').value;
    const timeLimitHours = document.getElementById('modal-time-limit-hours').value;
    const timeLimitMinutes = document.getElementById('modal-time-limit-minutes').value;
    const timeLimitDate = document.getElementById('modal-time-limit-date').value;
    const timeLimitTime = document.getElementById('modal-time-limit-time').value;
    const showEndDate = document.getElementById('modal-show-end-date').checked;
    const priceTypeToggle = document.getElementById('modal-price-type-toggle').checked;
    
    // Validierung: Preis nur 2 Dezimalstellen
    const priceNum = parseFloat(price);
    if (isNaN(priceNum)) {
        alert('Bitte geben Sie einen gültigen Preis ein.');
        return;
    }
    const priceDecimals = (price.toString().split('.')[1] || '').length;
    if (priceDecimals > 2) {
        alert('Der Preis darf maximal 2 Dezimalstellen haben.');
        return;
    }
    
    // Validierung: Angebotspreis nur 2 Dezimalstellen und nicht unter 0.01€
    if (hasOffer) {
        const salePriceNum = parseFloat(salePrice);
        if (isNaN(salePriceNum)) {
            alert('Bitte geben Sie einen gültigen Angebotspreis ein.');
            return;
        }
        const salePriceDecimals = (salePrice.toString().split('.')[1] || '').length;
        if (salePriceDecimals > 2) {
            alert('Der Angebotspreis darf maximal 2 Dezimalstellen haben.');
            return;
        }
        if (salePriceNum < 0.01) {
            alert('Der Angebotspreis darf nicht unter 0.01€ liegen.');
            return;
        }
        
        // Validierung: Prozentsatz nur 2 Dezimalstellen
        if (priceTypeToggle) {
            const percentage = ((priceNum - salePriceNum) / priceNum) * 100;
            const percentageStr = percentage.toFixed(10); // Mit 10 Dezimalstellen formatieren
            const percentageDecimals = (percentageStr.split('.')[1] || '').replace(/0+$/, '').length;
            if (percentageDecimals > 2) {
                alert('Der Prozentsatz darf maximal 2 Dezimalstellen haben.');
                return;
            }
        }
    }
    
    let timeLimitType = 'date';
    let timeLimitDuration = '';
    
    if (timeLimitToggle) {
        // Für Zeit - prüfe ob Tage, Stunden und/oder Minuten angegeben
        const days = parseInt(timeLimitDays) || 0;
        const hours = parseInt(timeLimitHours) || 0;
        const minutes = parseInt(timeLimitMinutes) || 0;
        
        if (days > 0 || hours > 0 || minutes > 0) {
            // Berechne Gesamtminuten
            const totalMinutes = (days * 24 * 60) + (hours * 60) + minutes;
            timeLimitType = 'minutes';
            timeLimitDuration = totalMinutes;
        }
    }
    
    // Datum und Uhrzeit kombinieren
    let fullDateTime = timeLimitDate;
    if (timeLimitTime && !timeLimitToggle) {
        fullDateTime = timeLimitDate + ' ' + timeLimitTime;
    }
    
    let body = 'action=update_product_details&nonce=<?php echo wp_create_nonce('product_list_nonce'); ?>&product_id=' + productId + '&stock=' + stock + '&price=' + price + '&has_offer=' + (hasOffer ? 1 : 0) + '&sale_price=' + salePrice + '&show_old_price=' + (showOldPrice ? 1 : 0) + '&time_limited=' + (timeLimited ? 1 : 0) + '&time_limit_type=' + timeLimitType + '&time_limit_duration=' + timeLimitDuration + '&time_limit_date=' + fullDateTime + '&show_end_date=' + (showEndDate ? 1 : 0) + '&price_type_toggle=' + (priceTypeToggle ? 1 : 0) + '&offer_start_date=' + encodeURIComponent(window.offerStartDate || '');
    
    fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: body
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeEditModal();
            loadProducts(); // Neu laden
        } else {
            alert('Fehler beim Speichern: ' + (data.data || 'Unbekannter Fehler'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Fehler beim Speichern');
    });
}

// Sortierfunktion
let allProducts = [];
let currentSort = { column: null, direction: 'asc' };

function setupSorting() {
    const headers = document.querySelectorAll('#product-table th[data-sort]');
    headers.forEach(header => {
        header.addEventListener('click', () => {
            const column = header.dataset.sort;
            sortProducts(column);
        });
    });
}

function sortProducts(column) {
    // Sortierrichtung umschalten
    if (currentSort.column === column) {
        currentSort.direction = currentSort.direction === 'asc' ? 'desc' : 'asc';
    } else {
        currentSort.column = column;
        currentSort.direction = 'asc';
    }
    
    // Pfeile aktualisieren
    updateSortArrows();
    
    // Produkte sortieren
    allProducts.sort((a, b) => {
        let valueA, valueB;
        
        switch(column) {
            case 'sellable':
                // Pfeil nach oben (asc): nicht zu verkaufende oben (hidden = 1)
                // Pfeil nach unten (desc): zu verkaufende oben (visible = 0)
                valueA = a.catalog_visibility === 'visible' ? 0 : 1;
                valueB = b.catalog_visibility === 'visible' ? 0 : 1;
                break;
            case 'name':
                valueA = a.name.toLowerCase();
                valueB = b.name.toLowerCase();
                break;
            case 'stock':
                valueA = parseFloat(a.stock) || 0;
                valueB = parseFloat(b.stock) || 0;
                break;
            case 'in_progress':
                valueA = parseFloat(a.in_progress) || 0;
                valueB = parseFloat(b.in_progress) || 0;
                break;
            case 'available':
                valueA = (parseFloat(a.stock) || 0) - (parseFloat(a.in_progress) || 0);
                valueB = (parseFloat(b.stock) || 0) - (parseFloat(b.in_progress) || 0);
                break;
            case 'price':
                valueA = parseFloat(a.price) || 0;
                valueB = parseFloat(b.price) || 0;
                break;
            default:
                return 0;
        }
        
        if (valueA < valueB) return currentSort.direction === 'asc' ? -1 : 1;
        if (valueA > valueB) return currentSort.direction === 'asc' ? 1 : -1;
        return 0;
    });
    
    // Neu rendern
    renderProducts(allProducts);
}

function updateSortArrows() {
    const headers = document.querySelectorAll('#product-table th[data-sort]');
    headers.forEach(header => {
        const arrow = header.querySelector('.sort-arrow');
        if (header.dataset.sort === currentSort.column) {
            arrow.textContent = currentSort.direction === 'asc' ? '↑' : '↓';
        } else {
            arrow.textContent = '';
        }
    });
}

// renderProducts überschreiben, um Produkte zu speichern
const originalRenderProducts = window.renderProducts;
window.renderProducts = function(products) {
    allProducts = products;
    originalRenderProducts(products);
}

// Initialisierung
document.addEventListener('DOMContentLoaded', function() {
    setupSorting();
    
    // Bulk Sale Button Event
    document.getElementById('bulk-sale-btn').onclick = function() {
        openBulkSaleModal();
    };
    
    // Bulk Sale Modal Events
    document.getElementById('cancel-bulk-sale').onclick = function() {
        document.getElementById('bulk-sale-modal').style.display = 'none';
    };
    
    document.getElementById('save-bulk-sale').onclick = function() {
        saveBulkSale();
    };
    
    document.getElementById('add-bulk-sale-group').onclick = function() {
        addBulkSaleGroup();
    };
    
    // Bulk Sale Date Events
    let currentBulkSaleDateField = '';
    document.getElementById('bulk-sale-start-btn').onclick = function() {
        currentBulkSaleDateField = 'start';
        // Aktuelle Zeit und Datum vorbelegen
        const now = new Date();
        const day = String(now.getDate()).padStart(2, '0');
        const month = String(now.getMonth() + 1).padStart(2, '0');
        const year = now.getFullYear();
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        document.getElementById('bulk-sale-date-input').value = day + '.' + month + '.' + year;
        document.getElementById('bulk-sale-time-input').value = hours + ':' + minutes;
        document.getElementById('bulk-sale-date-modal').style.display = 'flex';
    };
    
    document.getElementById('bulk-sale-end-btn').onclick = function() {
        currentBulkSaleDateField = 'end';
        // Aktuelle Zeit und Datum vorbelegen
        const now = new Date();
        const day = String(now.getDate()).padStart(2, '0');
        const month = String(now.getMonth() + 1).padStart(2, '0');
        const year = now.getFullYear();
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        document.getElementById('bulk-sale-date-input').value = day + '.' + month + '.' + year;
        document.getElementById('bulk-sale-time-input').value = hours + ':' + minutes;
        document.getElementById('bulk-sale-date-modal').style.display = 'flex';
    };
    
    document.getElementById('cancel-bulk-sale-date').onclick = function() {
        document.getElementById('bulk-sale-date-modal').style.display = 'none';
    };
    
    // Automatische Punktsetzung im Datumsfeld
    document.getElementById('bulk-sale-date-input').oninput = function(e) {
        let value = e.target.value.replace(/\D/g, '');
        if (value.length > 2) {
            value = value.substring(0, 2) + '.' + value.substring(2);
        }
        if (value.length > 5) {
            value = value.substring(0, 5) + '.' + value.substring(5);
        }
        if (value.length > 10) {
            value = value.substring(0, 10);
        }
        e.target.value = value;
    };
    
    document.getElementById('save-bulk-sale-date').onclick = function() {
        const date = document.getElementById('bulk-sale-date-input').value;
        const time = document.getElementById('bulk-sale-time-input').value;
        
        if (date && time) {
            const dateTime = date + ' ' + time;
            if (currentBulkSaleDateField === 'start') {
                document.getElementById('bulk-sale-start-display').textContent = 'Start: ' + dateTime;
                document.getElementById('bulk-sale-start-display').dataset.value = dateTime;
            } else {
                document.getElementById('bulk-sale-end-display').textContent = 'End: ' + dateTime;
                document.getElementById('bulk-sale-end-display').dataset.value = dateTime;
            }
            document.getElementById('bulk-sale-date-modal').style.display = 'none';
        } else {
            alert('Bitte Datum und Uhrzeit eingeben');
        }
    };
    
    // OK Button für Datums-Sektion
    document.getElementById('bulk-sale-dates-ok').onclick = function() {
        const startDate = document.getElementById('bulk-sale-start-display').dataset.value;
        const endDate = document.getElementById('bulk-sale-end-display').dataset.value;
        
        if (!startDate) {
            alert('Bitte Startdatum setzen');
            return;
        }
        
        // Datums-Sektion ausblenden und Gruppen-Sektion einblenden
        document.getElementById('bulk-sale-date-section').style.display = 'none';
        document.getElementById('bulk-sale-groups-section').style.display = 'block';
        
        // Gruppe 1 automatisch generieren
        if (bulkSaleGroups.length === 0) {
            addBulkSaleGroup();
        }
    };
    
    // Abbrechen Button für Datums-Sektion
    document.getElementById('bulk-sale-dates-cancel').onclick = function() {
        document.getElementById('bulk-sale-modal').style.display = 'none';
    };
    
    // Add Product Modal Events
    document.getElementById('close-add-product-modal').onclick = function() {
        document.getElementById('add-product-to-group-modal').style.display = 'none';
    };
    
    document.getElementById('add-product-search').oninput = function() {
        filterAddProductList(this.value);
    };
    
    // Bulk Sale Details Modal Events
    document.getElementById('close-bulk-sale-details').onclick = function() {
        document.getElementById('bulk-sale-details-modal').style.display = 'none';
    };
    
    document.getElementById('delete-bulk-sale-btn').onclick = function() {
        const saleId = document.getElementById('bulk-sale-details-title').dataset.saleId;
        if (confirm('Möchten Sie diese Bulk-Aktion wirklich löschen?')) {
            deleteBulkSale(saleId);
        }
    };
});

let bulkSaleGroups = [];
let currentGroupId = '';

function openBulkSaleModal() {
    document.getElementById('bulk-sale-title').value = '';
    document.getElementById('bulk-sale-start-display').textContent = '';
    document.getElementById('bulk-sale-start-display').dataset.value = '';
    document.getElementById('bulk-sale-end-display').textContent = '';
    document.getElementById('bulk-sale-end-display').dataset.value = '';
    document.getElementById('bulk-sale-show-end-date').checked = false;
    bulkSaleGroups = [];
    document.getElementById('bulk-sale-groups').innerHTML = '';
    // Datums-Sektion einblenden und Gruppen-Sektion ausblenden
    document.getElementById('bulk-sale-date-section').style.display = 'block';
    document.getElementById('bulk-sale-groups-section').style.display = 'none';
    document.getElementById('bulk-sale-modal').style.display = 'flex';
}

function addBulkSaleGroup() {
    // Zuerst die Werte aus dem DOM auslesen und im Array aktualisieren
    bulkSaleGroups.forEach(group => {
        group.name = document.getElementById(`group-name-${group.id}`) ? document.getElementById(`group-name-${group.id}`).value : group.name;
        group.has_offer = document.getElementById(`group-offer-${group.id}`) ? document.getElementById(`group-offer-${group.id}`).checked : false;
        group.discount_type = document.getElementById(`group-price-toggle-${group.id}`) ? (document.getElementById(`group-price-toggle-${group.id}`).checked ? 'percent' : 'fixed') : 'fixed';
        group.discount_amount = document.getElementById(`group-price-toggle-${group.id}`) && document.getElementById(`group-price-toggle-${group.id}`).checked 
            ? (document.getElementById(`group-sale-percent-${group.id}`) ? document.getElementById(`group-sale-percent-${group.id}`).value : '')
            : (document.getElementById(`group-sale-price-${group.id}`) ? document.getElementById(`group-sale-price-${group.id}`).value : '');
        group.show_old_price = document.getElementById(`group-show-old-price-${group.id}`) ? document.getElementById(`group-show-old-price-${group.id}`).checked : false;
    });
    
    const groupId = 'group_' + Date.now();
    const group = {
        id: groupId,
        name: 'Gruppe ' + (bulkSaleGroups.length + 1),
        has_offer: false,
        discount_type: 'fixed',
        discount_amount: '',
        show_old_price: false,
        products: []
    };
    bulkSaleGroups.push(group);
    renderBulkSaleGroups();
}

function renderBulkSaleGroups() {
    const container = document.getElementById('bulk-sale-groups');
    container.innerHTML = '';
    
    bulkSaleGroups.forEach((group, index) => {
        const groupDiv = document.createElement('div');
        groupDiv.style.border = '1px solid #ddd';
        groupDiv.style.padding = '15px';
        groupDiv.style.marginBottom = '15px';
        groupDiv.style.borderRadius = '4px';
        
        groupDiv.innerHTML = `
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                <input type="text" id="group-name-${group.id}" value="${group.name}" onchange="updateGroupName('${group.id}', this.value)" style="font-size: 16px; font-weight: bold; padding: 5px; border: 1px solid #ddd; border-radius: 3px; width: 60%;">
                <button onclick="removeBulkSaleGroup('${group.id}')" style="padding: 5px 10px; background: #e74c3c; color: white; border: none; border-radius: 3px; cursor: pointer;">Gruppe löschen</button>
            </div>
            <div style="margin-bottom: 10px;">
                <button onclick="openAddProductModal('${group.id}')" style="padding: 8px 12px; background: #f5f5f5; border: 1px solid #ddd; border-radius: 4px; cursor: pointer;">Artikel zu Gruppe hinzufügen</button>
            </div>
            <div style="margin-bottom: 10px; border: 1px solid #eee; border-radius: 4px; overflow: hidden; font-size: 13px;">
                ${group.products.length === 0
                    ? '<div style="padding: 10px; color: #999;">Keine Artikel in Gruppe</div>'
                    : group.products.map(function(productId) {
                        var p = allProducts.find(function(x) { return x.id == productId; });
                        if (!p) return '<div style="padding: 8px 10px; color: #999; border-bottom: 1px solid #eee;">Produkt #' + productId + '</div>';
                        return '<div style="display:flex;align-items:center;gap:8px;padding:8px 10px;border-bottom:1px solid #eee;background:#fff;">' +
                            (p.image ? '<img src="' + p.image + '" style="width:30px;height:30px;object-fit:cover;border-radius:3px;flex-shrink:0;">' : '<div style="width:30px;height:30px;background:#f5f5f5;border-radius:3px;flex-shrink:0;"></div>') +
                            '<span style="flex:1;">' + p.name + '</span>' +
                            '<button onclick="removeProductFromGroup(\'' + group.id + '\',' + productId + ')" style="background:none;border:none;cursor:pointer;font-size:15px;color:#e74c3c;padding:2px;opacity:0.7;line-height:1;" title="Entfernen">&#x1F5D1;&#xFE0F;</button>' +
                        '</div>';
                    }).join('')
                }
            </div>
            <div style="margin-bottom: 10px;">
                <label style="display: block; margin-bottom: 5px; font-weight: 500;">
                    <input type="checkbox" id="group-offer-${group.id}" ${group.has_offer ? 'checked' : ''} onchange="toggleGroupOffer('${group.id}')"> Angebot aktivieren
                </label>
            </div>
            <div id="group-offer-fields-${group.id}" style="display: ${group.has_offer ? 'block' : 'none'}; padding-left: 20px;">
                <div style="margin-bottom: 10px;">
                    <label style="display: block; margin-bottom: 5px; font-weight: 500;">Preistyp</label>
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <span style="font-size: 14px;">€</span>
                        <label style="position: relative; display: inline-block; width: 50px; height: 26px;">
                            <input type="checkbox" id="group-price-toggle-${group.id}" ${group.discount_type === 'percent' ? 'checked' : ''} onchange="toggleGroupPriceType('${group.id}')">
                            <span class="slider"></span>
                        </label>
                        <span style="font-size: 14px;">%</span>
                    </div>
                </div>
                <div id="group-euro-field-${group.id}" style="margin-bottom: 10px;">
                    <label style="display: block; margin-bottom: 5px; font-weight: 500;">Angebotspreis (€)</label>
                    <input type="number" id="group-sale-price-${group.id}" step="0.01" value="${group.discount_amount}" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;" min="0">
                </div>
                <div id="group-percent-field-${group.id}" style="margin-bottom: 10px; display: none;">
                    <label style="display: block; margin-bottom: 5px; font-weight: 500;">Rabatt (%)</label>
                    <input type="number" id="group-sale-percent-${group.id}" step="1" value="${group.discount_amount}" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;" min="0" max="100">
                </div>
                <div style="margin-bottom: 10px;">
                    <label style="display: block; margin-bottom: 5px; font-weight: 500;">
                        <input type="checkbox" id="group-show-old-price-${group.id}" ${group.show_old_price ? 'checked' : ''}> Alten Preis anzeigen
                    </label>
                </div>
            </div>
        `;
        
        container.appendChild(groupDiv);
    });
}

function removeBulkSaleGroup(groupId) {
    bulkSaleGroups = bulkSaleGroups.filter(g => g.id !== groupId);
    renderBulkSaleGroups();
}

function removeProductFromGroup(groupId, productId) {
    const group = bulkSaleGroups.find(g => g.id === groupId);
    if (group) {
        group.products = group.products.filter(id => id != productId);
        renderBulkSaleGroups();
    }
}

function updateGroupName(groupId, newName) {
    const group = bulkSaleGroups.find(g => g.id === groupId);
    if (group) {
        group.name = newName;
    }
}

function toggleGroupOffer(groupId) {
    const group = bulkSaleGroups.find(g => g.id === groupId);
    if (group) {
        group.has_offer = !group.has_offer;
        document.getElementById(`group-offer-fields-${groupId}`).style.display = group.has_offer ? 'block' : 'none';
    }
}

function toggleGroupPriceType(groupId) {
    const group = bulkSaleGroups.find(g => g.id === groupId);
    if (group) {
        const isPercent = document.getElementById(`group-price-toggle-${groupId}`).checked;
        group.discount_type = isPercent ? 'percent' : 'fixed';
        document.getElementById(`group-euro-field-${groupId}`).style.display = isPercent ? 'none' : 'block';
        document.getElementById(`group-percent-field-${groupId}`).style.display = isPercent ? 'block' : 'none';
    }
}

function openAddProductModal(groupId) {
    currentGroupId = groupId;
    document.getElementById('add-product-to-group-modal').style.display = 'flex';
    document.getElementById('add-product-search').value = '';
    loadAvailableProducts();
}

function loadAvailableProducts() {
    const alreadyAdded = new Set();
    bulkSaleGroups.forEach(g => g.products.forEach(id => alreadyAdded.add(id)));

    fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'action=get_product_list_data&nonce=<?php echo wp_create_nonce('product_list_nonce'); ?>'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const products = data.data.filter(p => !p.part_of_sale && !p.has_offer && !alreadyAdded.has(p.id));
            renderAddProductList(products);
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

function renderAddProductList(products) {
    const container = document.getElementById('add-product-list');
    container.innerHTML = '';
    
    products.forEach(product => {
        const productDiv = document.createElement('div');
        productDiv.style.display = 'flex';
        productDiv.style.alignItems = 'center';
        productDiv.style.padding = '10px';
        productDiv.style.borderBottom = '1px solid #eee';
        productDiv.dataset.productId = product.id;
        productDiv.dataset.productName = product.name.toLowerCase();
        productDiv.dataset.productSku = (product.sku || '').toLowerCase();
        
        productDiv.innerHTML = `
            <div style="width: 50px; height: 50px; margin-right: 15px;">
                ${product.image ? `<img src="${product.image}" alt="${product.name}" style="width: 100%; height: 100%; object-fit: cover;">` : '<div style="width:50px;height:50px;background:#f5f5f5;border-radius:3px;display:flex;align-items:center;justify-content:center;color:#999;">📷</div>'}
            </div>
            <div style="flex: 1;">
                <div style="font-weight: 500;">${product.name}</div>
                <div style="font-size: 13px; color: #666;">${product.sku ? 'SKU: ' + product.sku : ''} | ${product.price} €</div>
            </div>
            <button onclick="addProductToGroup(${product.id})" style="padding: 8px 12px; background: #333; color: white; border: none; border-radius: 4px; cursor: pointer;">Hinzufügen</button>
        `;
        
        container.appendChild(productDiv);
    });
}

function filterAddProductList(searchTerm) {
    const rows = document.querySelectorAll('#add-product-list > div');
    const term = searchTerm.toLowerCase();
    
    rows.forEach(row => {
        const name = row.dataset.productName || '';
        const sku = row.dataset.productSku || '';
        
        if (name.includes(term) || sku.includes(term)) {
            row.style.display = 'flex';
        } else {
            row.style.display = 'none';
        }
    });
}

function addProductToGroup(productId) {
    const group = bulkSaleGroups.find(g => g.id === currentGroupId);
    if (group && !group.products.includes(productId)) {
        group.products.push(productId);
        const row = document.querySelector('#add-product-list > div[data-product-id="' + productId + '"]');
        if (row) row.remove();
        renderBulkSaleGroups();
    }
}

function saveBulkSale() {
    const title = document.getElementById('bulk-sale-title').value;
    const startDate = document.getElementById('bulk-sale-start-display').dataset.value || '';
    const endDate = document.getElementById('bulk-sale-end-display').dataset.value || '';
    const showEndDate = document.getElementById('bulk-sale-show-end-date').checked;
    
    if (!title) {
        alert('Bitte einen Aktionsnamen eingeben');
        return;
    }
    
    // Gruppen-Daten aktualisieren
    bulkSaleGroups.forEach(group => {
        group.name = document.getElementById(`group-name-${group.id}`) ? document.getElementById(`group-name-${group.id}`).value : group.name;
        group.has_offer = document.getElementById(`group-offer-${group.id}`) ? document.getElementById(`group-offer-${group.id}`).checked : false;
        group.discount_type = document.getElementById(`group-price-toggle-${group.id}`) ? (document.getElementById(`group-price-toggle-${group.id}`).checked ? 'percent' : 'fixed') : 'fixed';
        group.discount_amount = document.getElementById(`group-price-toggle-${group.id}`) && document.getElementById(`group-price-toggle-${group.id}`).checked 
            ? (document.getElementById(`group-sale-percent-${group.id}`) ? document.getElementById(`group-sale-percent-${group.id}`).value : '')
            : (document.getElementById(`group-sale-price-${group.id}`) ? document.getElementById(`group-sale-price-${group.id}`).value : '');
        group.show_old_price = document.getElementById(`group-show-old-price-${group.id}`) ? document.getElementById(`group-show-old-price-${group.id}`).checked : false;
    });
    
    console.log('Debug: Gruppen vor dem Speichern:', JSON.stringify(bulkSaleGroups, null, 2));
    
    const body = 'action=create_bulk_sale&nonce=<?php echo wp_create_nonce('product_list_nonce'); ?>&title=' + encodeURIComponent(title) + '&start_date=' + encodeURIComponent(startDate) + '&end_date=' + encodeURIComponent(endDate) + '&show_end_date=' + (showEndDate ? 1 : 0) + '&groups=' + encodeURIComponent(JSON.stringify(bulkSaleGroups));
    
    fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: body
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('bulk-sale-modal').style.display = 'none';
            loadProducts();
        } else {
            alert('Fehler beim Speichern: ' + (data.data || 'Unbekannter Fehler'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Fehler beim Speichern');
    });
}

function openDeleteConfirm(productId, productName) {
    document.getElementById('delete-confirm-text').textContent = 'Möchten Sie "' + productName + '" wirklich löschen? Diese Aktion kann nicht rückgängig gemacht werden.';
    document.getElementById('delete-confirm-modal').style.display = 'flex';
    document.getElementById('confirm-delete-btn').onclick = function() {
        deleteProduct(productId);
    };
    document.getElementById('cancel-delete-btn').onclick = function() {
        document.getElementById('delete-confirm-modal').style.display = 'none';
    };
}

function deleteProduct(productId) {
    document.getElementById('confirm-delete-btn').disabled = true;
    document.getElementById('confirm-delete-btn').textContent = 'Löschen...';

    fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'action=delete_product&nonce=<?php echo wp_create_nonce('product_list_nonce'); ?>&product_id=' + productId
    })
    .then(response => response.json())
    .then(data => {
        document.getElementById('delete-confirm-modal').style.display = 'none';
        document.getElementById('confirm-delete-btn').disabled = false;
        document.getElementById('confirm-delete-btn').textContent = 'Löschen';
        if (data.success) {
            allProducts = allProducts.filter(p => p.id != productId);
            renderProducts(allProducts);
        } else {
            alert('Fehler beim Löschen: ' + (data.data || 'Unbekannter Fehler'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        document.getElementById('delete-confirm-modal').style.display = 'none';
        document.getElementById('confirm-delete-btn').disabled = false;
        document.getElementById('confirm-delete-btn').textContent = 'Löschen';
        alert('Fehler beim Löschen');
    });
}

// ── Stock-Verwaltung ──────────────────────────────────────────────────────────
var _currentStockType = 'increase';

function setStockType(type) {
    _currentStockType = type;
    document.getElementById('stock-type-increase').style.background = type === 'increase' ? '#333' : '#f5f5f5';
    document.getElementById('stock-type-increase').style.color      = type === 'increase' ? 'white' : '#333';
    document.getElementById('stock-type-set').style.background      = type === 'set'      ? '#333' : '#f5f5f5';
    document.getElementById('stock-type-set').style.color           = type === 'set'      ? 'white' : '#333';
}

function openStockPanel() {
    switchEditPanel('stock');
    _currentStockType = 'increase';
    setStockType('increase');
    document.getElementById('stock-schedule-loading').style.display = 'block';
    document.getElementById('stock-schedule-list').style.display    = 'none';
    document.getElementById('stock-schedule-list').innerHTML        = '';
    document.getElementById('stock-change-amount').value            = '';
    document.getElementById('stock-change-datetime').value          = '';
    document.getElementById('stock-schedule-status').textContent    = '';
    fetch(_plAjax, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'action=get_stock_schedule&nonce=' + _plNonce + '&product_id=' + _editProductId
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        document.getElementById('stock-schedule-loading').style.display = 'none';
        var list = document.getElementById('stock-schedule-list');
        list.style.display = 'block';
        if (!data.success || !data.data.length) {
            list.innerHTML = '<p style="color:#aaa;font-size:13px;margin:0 0 4px;">Keine geplanten Änderungen.</p>';
            return;
        }
        var html = '';
        data.data.forEach(function(e) {
            var d = new Date(e.scheduled_at * 1000);
            var dateStr = d.toLocaleDateString('de-DE') + ' ' + d.toLocaleTimeString('de-DE', { hour: '2-digit', minute: '2-digit' });
            var typeLabel = e.type === 'increase' ? 'Erhöhen um' : 'Auf setzen';
            html += '<div style="display:flex;align-items:center;justify-content:space-between;padding:10px 0;border-bottom:1px solid #f0f0f0;">'
                  +   '<div>'
                  +     '<div style="font-size:13px;font-weight:500;">' + _efEsc(typeLabel) + ' ' + e.amount + '</div>'
                  +     '<div style="font-size:12px;color:#888;">' + dateStr + '</div>'
                  +   '</div>'
                  +   '<button onclick="deleteStockSchedule(\'' + _efEsc(e.id) + '\')" style="background:none;border:none;cursor:pointer;color:#dc3545;font-size:18px;padding:4px 8px;">✕</button>'
                  + '</div>';
        });
        list.innerHTML = html;
    });
}

function addStockSchedule() {
    var amount   = parseInt(document.getElementById('stock-change-amount').value, 10);
    var dtVal    = document.getElementById('stock-change-datetime').value;
    var status   = document.getElementById('stock-schedule-status');
    if (!amount || amount <= 0) { status.textContent = 'Bitte eine gültige Menge eingeben.'; return; }
    if (!dtVal) { status.textContent = 'Bitte ein Datum und eine Uhrzeit eingeben.'; return; }
    var scheduledAt = Math.floor(new Date(dtVal).getTime() / 1000);
    if (scheduledAt <= Math.floor(Date.now() / 1000)) { status.textContent = 'Das Datum muss in der Zukunft liegen.'; return; }
    status.textContent = 'Wird geplant…';
    fetch(_plAjax, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'action=add_stock_schedule&nonce=' + _plNonce
            + '&product_id=' + _editProductId
            + '&type=' + _currentStockType
            + '&amount=' + amount
            + '&scheduled_at=' + scheduledAt
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        if (data.success) { status.textContent = 'Erfolgreich geplant.'; openStockPanel(); }
        else { status.textContent = 'Fehler: ' + (data.data || 'Unbekannt'); }
    });
}

function deleteStockSchedule(entryId) {
    if (!confirm('Geplante Änderung löschen?')) return;
    fetch(_plAjax, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'action=delete_stock_schedule&nonce=' + _plNonce + '&entry_id=' + encodeURIComponent(entryId)
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        if (data.success) openStockPanel();
        else alert('Fehler beim Löschen.');
    });
}

// ═══════════════════════════════════════════════════════════════════════════════
// CROP & WATERMARK ENGINE (Produktliste-Version)
// ═══════════════════════════════════════════════════════════════════════════════
const CWM_SIZE  = 480;
const WM_HANDLE = 8;
const LOGO_URL  = '<?php echo content_url("uploads/2022/01/Logo-Plantaphilia-1.svg"); ?>';

const CWM = {
    canvas: null, ctx: null,
    img: null,
    imgX: 0, imgY: 0, imgScale: 1, minScale: 1,
    wmX: 0, wmY: 0, wmW: 0,
    logoImg: null, logoAspect: 1,
    drag: null,
    lastX: 0, lastY: 0,
    rectEnabled: false,
    rectOrientation: 'horizontal',
    barA: 0, barB: 0,
    callback: null,
};

(function initCWM() {
    const logo = new Image();
    logo.crossOrigin = 'anonymous';
    logo.onload = () => { CWM.logoAspect = logo.naturalHeight / logo.naturalWidth; };
    logo.src = LOGO_URL;
    CWM.logoImg = logo;
})();

function cwmOpen(imgUrl, callback) {
    CWM.callback = callback;
    CWM.canvas   = document.getElementById('cwm-canvas');
    CWM.ctx      = CWM.canvas.getContext('2d');
    CWM.canvas.width  = CWM_SIZE;
    CWM.canvas.height = CWM_SIZE;
    const img = new Image();
    img.crossOrigin = 'anonymous';
    img.onload = () => {
        CWM.img = img;
        const ms = Math.max(CWM_SIZE / img.naturalWidth, CWM_SIZE / img.naturalHeight);
        CWM.minScale = ms; CWM.imgScale = ms;
        CWM.imgX = (CWM_SIZE - img.naturalWidth  * ms) / 2;
        CWM.imgY = (CWM_SIZE - img.naturalHeight * ms) / 2;
        CWM.wmW = Math.round(CWM_SIZE * 0.32);
        CWM.wmX = CWM_SIZE - CWM.wmW - 14;
        CWM.wmY = CWM_SIZE - Math.round(CWM.wmW * CWM.logoAspect) - 14;
        document.getElementById('cwm-zoom').value = 100;
        cwmRender();
    };
    img.src = imgUrl;
    document.getElementById('cwm-overlay').classList.add('open');
    cwmBindEvents();
}

function cwmCancel() {
    document.getElementById('cwm-overlay').classList.remove('open');
    CWM.callback = null;
}

function cwmRenderBase(ctx, scale, forExport) {
    const S = CWM_SIZE * scale;
    ctx.clearRect(0, 0, S, S);
    let exportOffsetX = 0, exportOffsetY = 0;
    if (forExport && CWM.rectEnabled) {
        const off = (CWM.barB - CWM.barA) / 2 * scale;
        if (CWM.rectOrientation === 'horizontal') exportOffsetY = off;
        else exportOffsetX = off;
    }
    ctx.drawImage(CWM.img,
        CWM.imgX * scale + exportOffsetX,
        CWM.imgY * scale + exportOffsetY,
        CWM.img.naturalWidth  * CWM.imgScale * scale,
        CWM.img.naturalHeight * CWM.imgScale * scale);
    if (CWM.rectEnabled) {
        if (forExport) {
            ctx.clearRect(0, 0, S, CWM.barA * scale);
            ctx.clearRect(0, (CWM_SIZE - CWM.barB) * scale, S, CWM.barB * scale);
            if (CWM.rectOrientation === 'vertical') {
                ctx.clearRect(0, 0, CWM.barA * scale, S);
                ctx.clearRect((CWM_SIZE - CWM.barB) * scale, 0, CWM.barB * scale, S);
            }
        } else {
            ctx.fillStyle = 'rgba(0,0,0,0.85)';
            if (CWM.rectOrientation === 'horizontal') {
                ctx.fillRect(0, 0, S, CWM.barA * scale);
                ctx.fillRect(0, (CWM_SIZE - CWM.barB) * scale, S, CWM.barB * scale);
            } else {
                ctx.fillRect(0, 0, CWM.barA * scale, S);
                ctx.fillRect((CWM_SIZE - CWM.barB) * scale, 0, CWM.barB * scale, S);
            }
        }
    }
    const showWm = document.getElementById('cwm-show-wm').checked;
    const invertWm = document.getElementById('cwm-invert-wm').checked;
    if (showWm && CWM.logoImg && CWM.wmW > 0) {
        const wmH = Math.round(CWM.wmW * CWM.logoAspect);
        ctx.save();
        ctx.globalAlpha = 0.55;
        if (invertWm) { ctx.filter = 'invert(1)'; }
        ctx.drawImage(CWM.logoImg, CWM.wmX * scale, CWM.wmY * scale, CWM.wmW * scale, wmH * scale);
        ctx.restore();
    }
}

function cwmRender() {
    if (!CWM.canvas || !CWM.img) return;
    cwmRenderBase(CWM.ctx, 1, false);
    const ctx = CWM.ctx; const S = CWM_SIZE;
    const showWm = document.getElementById('cwm-show-wm').checked;
    if (showWm) {
        ctx.fillStyle = '#fff'; ctx.strokeStyle = '#555'; ctx.lineWidth = 1.5;
        for (const h of cwmGetHandles()) {
            ctx.beginPath(); ctx.rect(h.x - WM_HANDLE, h.y - WM_HANDLE, WM_HANDLE*2, WM_HANDLE*2);
            ctx.fill(); ctx.stroke();
        }
    }
    if (CWM.rectEnabled) {
        ctx.save(); ctx.strokeStyle = 'rgba(255,255,255,0.85)'; ctx.lineWidth = 2; ctx.setLineDash([6,4]);
        if (CWM.rectOrientation === 'horizontal') {
            ctx.beginPath(); ctx.moveTo(0, CWM.barA); ctx.lineTo(S, CWM.barA); ctx.stroke();
            ctx.beginPath(); ctx.moveTo(0, S - CWM.barB); ctx.lineTo(S, S - CWM.barB); ctx.stroke();
            ctx.setLineDash([]); cwmDrawGrip(ctx, S/2, CWM.barA, 'h'); cwmDrawGrip(ctx, S/2, S - CWM.barB, 'h');
        } else {
            ctx.beginPath(); ctx.moveTo(CWM.barA, 0); ctx.lineTo(CWM.barA, S); ctx.stroke();
            ctx.beginPath(); ctx.moveTo(S - CWM.barB, 0); ctx.lineTo(S - CWM.barB, S); ctx.stroke();
            ctx.setLineDash([]); cwmDrawGrip(ctx, CWM.barA, S/2, 'v'); cwmDrawGrip(ctx, S - CWM.barB, S/2, 'v');
        }
        ctx.restore();
    }
}

function cwmDrawGrip(ctx, cx, cy, dir) {
    const w = dir === 'h' ? 28 : 8, h = dir === 'h' ? 8 : 28;
    ctx.fillStyle = 'rgba(255,255,255,0.9)'; ctx.strokeStyle = '#666'; ctx.lineWidth = 1;
    ctx.beginPath(); ctx.roundRect(cx - w/2, cy - h/2, w, h, 4); ctx.fill(); ctx.stroke();
    ctx.fillStyle = '#999';
    for (let i = -1; i <= 1; i++) {
        ctx.beginPath();
        ctx.arc(cx + (dir === 'h' ? i*8 : 0), cy + (dir === 'v' ? i*8 : 0), 1.5, 0, Math.PI*2);
        ctx.fill();
    }
}

function cwmGetHandles() {
    const wmH = Math.round(CWM.wmW * CWM.logoAspect);
    return [
        { x: CWM.wmX,           y: CWM.wmY,      id: 'tl' },
        { x: CWM.wmX + CWM.wmW, y: CWM.wmY,      id: 'tr' },
        { x: CWM.wmX,           y: CWM.wmY + wmH, id: 'bl' },
        { x: CWM.wmX + CWM.wmW, y: CWM.wmY + wmH, id: 'br' },
    ];
}

const BAR_HIT = 12;
function cwmHitTest(x, y) {
    if (CWM.rectEnabled) {
        if (CWM.rectOrientation === 'horizontal') {
            if (Math.abs(y - CWM.barA) <= BAR_HIT) return 'bar-a';
            if (Math.abs(y - (CWM_SIZE - CWM.barB)) <= BAR_HIT) return 'bar-b';
        } else {
            if (Math.abs(x - CWM.barA) <= BAR_HIT) return 'bar-a';
            if (Math.abs(x - (CWM_SIZE - CWM.barB)) <= BAR_HIT) return 'bar-b';
        }
    }
    const showWm = document.getElementById('cwm-show-wm').checked;
    if (showWm) {
        for (const h of cwmGetHandles()) {
            if (Math.abs(x - h.x) <= WM_HANDLE+2 && Math.abs(y - h.y) <= WM_HANDLE+2) return 'wm-' + h.id;
        }
        const wmH = Math.round(CWM.wmW * CWM.logoAspect);
        if (x >= CWM.wmX && x <= CWM.wmX + CWM.wmW && y >= CWM.wmY && y <= CWM.wmY + wmH) return 'wm';
    }
    return 'image';
}

function cwmBindEvents() {
    const c = document.getElementById('cwm-canvas');
    c.onmousedown  = cwmDown;
    c.ontouchstart = (e) => { e.preventDefault(); cwmDown(e.touches[0]); };
    document.onmousemove  = cwmMove;
    document.ontouchmove  = (e) => { if (CWM.drag) { e.preventDefault(); cwmMove(e.touches[0]); } };
    document.onmouseup    = cwmUp;
    document.ontouchend   = cwmUp;
    c.onwheel = cwmWheel;
}

function cwmPos(e) {
    const r = CWM.canvas.getBoundingClientRect();
    return { x: (e.clientX - r.left) * CWM_SIZE / r.width, y: (e.clientY - r.top) * CWM_SIZE / r.height };
}

function cwmDown(e) {
    const { x, y } = cwmPos(e);
    CWM.drag = cwmHitTest(x, y); CWM.lastX = x; CWM.lastY = y;
    CWM.canvas.classList.add('dragging');
}

function cwmMove(e) {
    const { x, y } = cwmPos(e);
    if (!CWM.drag) {
        const hit = cwmHitTest(x, y);
        if (!CWM.canvas) return;
        if (hit === 'bar-a' || hit === 'bar-b') CWM.canvas.style.cursor = CWM.rectOrientation === 'horizontal' ? 'ns-resize' : 'ew-resize';
        else if (hit === 'wm') CWM.canvas.style.cursor = 'move';
        else if (hit.startsWith('wm-')) CWM.canvas.style.cursor = 'nwse-resize';
        else CWM.canvas.style.cursor = 'grab';
        return;
    }
    const dx = x - CWM.lastX, dy = y - CWM.lastY;
    CWM.lastX = x; CWM.lastY = y;
    const MAX_BAR = CWM_SIZE * 0.48;
    if (CWM.drag === 'image') { CWM.imgX += dx; CWM.imgY += dy; cwmClampImage(); }
    else if (CWM.drag === 'wm') { CWM.wmX += dx; CWM.wmY += dy; cwmClampWatermark(); }
    else if (CWM.drag.startsWith('wm-')) {
        const corner = CWM.drag.slice(3), MIN_W = 40;
        if (corner === 'br') { CWM.wmW = Math.max(MIN_W, CWM.wmW + dx); }
        else if (corner === 'bl') { const nw = Math.max(MIN_W, CWM.wmW - dx); CWM.wmX += CWM.wmW - nw; CWM.wmW = nw; }
        else if (corner === 'tr') { CWM.wmW = Math.max(MIN_W, CWM.wmW + dx); CWM.wmY += dy; }
        else if (corner === 'tl') { const nw = Math.max(MIN_W, CWM.wmW - dx); CWM.wmX += CWM.wmW - nw; CWM.wmY += dy; CWM.wmW = nw; }
        cwmClampWatermark();
    } else if (CWM.drag === 'bar-a') {
        CWM.barA = Math.max(0, Math.min(MAX_BAR, CWM.barA + (CWM.rectOrientation === 'horizontal' ? dy : dx)));
        cwmClampWatermark();
    } else if (CWM.drag === 'bar-b') {
        CWM.barB = Math.max(0, Math.min(MAX_BAR, CWM.barB - (CWM.rectOrientation === 'horizontal' ? dy : dx)));
        cwmClampWatermark();
    }
    cwmRender();
}

function cwmUp() { CWM.drag = null; if (CWM.canvas) CWM.canvas.classList.remove('dragging'); }

function cwmWheel(e) {
    e.preventDefault();
    const delta = e.deltaY > 0 ? -0.08 : 0.08;
    const ns = Math.max(CWM.minScale, CWM.imgScale * (1 + delta));
    const cx = CWM_SIZE / 2, cy = CWM_SIZE / 2;
    CWM.imgX = cx - (cx - CWM.imgX) * (ns / CWM.imgScale);
    CWM.imgY = cy - (cy - CWM.imgY) * (ns / CWM.imgScale);
    CWM.imgScale = ns; cwmClampImage();
    const pct = Math.round(((ns - CWM.minScale) / (CWM.minScale * 4)) * 400 + 100);
    document.getElementById('cwm-zoom').value = Math.min(500, Math.max(100, pct));
    cwmRender();
}

function cwmZoomSlider(val) {
    const factor = (val - 100) / 400;
    const ns = CWM.minScale * (1 + factor * 4);
    const cx = CWM_SIZE / 2, cy = CWM_SIZE / 2;
    CWM.imgX = cx - (cx - CWM.imgX) * (ns / CWM.imgScale);
    CWM.imgY = cy - (cy - CWM.imgY) * (ns / CWM.imgScale);
    CWM.imgScale = ns; cwmClampImage(); cwmRender();
}

function cwmClampWatermark() {
    if (!CWM.rectEnabled) return;
    const wmH = Math.round(CWM.wmW * CWM.logoAspect);
    if (CWM.rectOrientation === 'horizontal') {
        const maxY = CWM_SIZE - CWM.barB - wmH;
        if (maxY > CWM.barA) CWM.wmY = Math.max(CWM.barA, Math.min(maxY, CWM.wmY));
    } else {
        const maxX = CWM_SIZE - CWM.barB - CWM.wmW;
        if (maxX > CWM.barA) CWM.wmX = Math.max(CWM.barA, Math.min(maxX, CWM.wmX));
    }
}

function cwmClampImage() {
    const iw = CWM.img.naturalWidth  * CWM.imgScale;
    const ih = CWM.img.naturalHeight * CWM.imgScale;
    if (CWM.imgX > 0) CWM.imgX = 0;
    if (CWM.imgY > 0) CWM.imgY = 0;
    if (CWM.imgX + iw < CWM_SIZE) CWM.imgX = CWM_SIZE - iw;
    if (CWM.imgY + ih < CWM_SIZE) CWM.imgY = CWM_SIZE - ih;
}

async function cwmSave() {
    const btn  = document.getElementById('cwm-save-btn');
    const prog = document.getElementById('cwm-upload-progress');
    btn.disabled = true; prog.style.display = 'block';
    const EXPORT  = 1042;
    const useRect = CWM.rectEnabled;
    const offscreen = document.createElement('canvas');
    offscreen.width  = EXPORT; offscreen.height = EXPORT;
    cwmRenderBase(offscreen.getContext('2d'), EXPORT / CWM_SIZE, true);
    const mimeType = useRect ? 'image/png'  : 'image/jpeg';
    const quality  = useRect ? undefined    : 0.93;
    const ext      = useRect ? '.png'       : '.jpg';
    const blob = await new Promise(resolve => offscreen.toBlob(resolve, mimeType, quality));
    const form = new FormData();
    form.append('action',     'upload_cropped_image_for_edit');
    form.append('nonce',      _plNonce);
    form.append('product_id', _editProductId);
    form.append('image',      blob, 'plantaphilia-' + Date.now() + ext);
    try {
        const res  = await fetch(_plAjax, { method: 'POST', body: form });
        const data = await res.json();
        if (data.success) {
            const cb = CWM.callback;
            document.getElementById('cwm-overlay').classList.remove('open');
            if (cb) cb(data.data.attachment_id, data.data.thumb_url);
        } else {
            alert('Upload fehlgeschlagen: ' + (data.data || 'Fehler'));
        }
    } catch (e) {
        alert('Netzwerkfehler beim Upload.');
    } finally {
        btn.disabled = false; prog.style.display = 'none';
    }
}

function cwmToggleRect() {
    CWM.rectEnabled = document.getElementById('cwm-rect-enable').checked;
    document.getElementById('cwm-rect-controls').style.display = CWM.rectEnabled ? 'block' : 'none';
    if (CWM.rectEnabled) {
        CWM.barA = Math.round(CWM_SIZE * 0.15); CWM.barB = Math.round(CWM_SIZE * 0.15);
        cwmClampWatermark();
    } else { CWM.barA = 0; CWM.barB = 0; }
    cwmRender();
}

function cwmToggleOrientation() {
    const isVert = document.getElementById('cwm-rect-toggle').checked;
    CWM.rectOrientation = isVert ? 'vertical' : 'horizontal';
    CWM.barA = Math.round(CWM_SIZE * 0.15); CWM.barB = Math.round(CWM_SIZE * 0.15);
    cwmClampWatermark();
    document.getElementById('cwm-orient-label-h').style.fontWeight = isVert ? '400' : '600';
    document.getElementById('cwm-orient-label-v').style.fontWeight = isVert ? '600' : '400';
    cwmRender();
}
// ═══════════════════════════════════════════════════════════════════════════════

// ── Page Tab Navigation ──────────────────────────────────────────────────────
function showPageTab(name) {
    document.querySelectorAll('.pa-tab-content').forEach(function(el) {
        el.style.display = 'none';
    });
    document.querySelectorAll('.pa-ptab').forEach(function(el) {
        el.classList.remove('active');
    });
    var tc = document.getElementById('pa-tc-' + name);
    if (tc) tc.style.display = 'block';
    var btn = document.getElementById('pptab-' + name);
    if (btn) btn.classList.add('active');
    if (name === 'rabattcodes')  loadCoupons();
    if (name === 'social-deals') { loadSmConfig(); loadDeals(''); }
}

// ── Rabattcode System ────────────────────────────────────────────────────────
var _rcCoupons = [];

function _rcEsc(s) {
    return String(s == null ? '' : s)
        .replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

function loadCoupons() {
    var tbody = document.getElementById('rc-table-body');
    tbody.innerHTML = '<tr><td colspan="6" style="padding:30px;text-align:center;color:var(--creme-dim);">Lade…</td></tr>';
    fetch(_plAjax, {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'action=pa_get_coupons&nonce=' + _plNonce
    })
    .then(function(r) { return r.json(); })
    .then(function(d) {
        if (!d.success) return;
        _rcCoupons = d.data;
        renderCouponsTable(d.data);
    });
}

function renderCouponsTable(coupons) {
    var tbody = document.getElementById('rc-table-body');
    if (!coupons.length) {
        tbody.innerHTML = '<tr><td colspan="6" style="padding:30px;text-align:center;color:var(--creme-dim);">Keine Rabattcodes vorhanden.</td></tr>';
        return;
    }
    var typeLabels = {percent: 'Prozent', fixed_cart: 'Festbetrag', fixed_product: 'Festbetrag/Produkt'};
    var html = '';
    coupons.forEach(function(c) {
        var typeLabel = typeLabels[c.discount_type] || c.discount_type;
        var amount    = c.discount_type === 'percent' ? c.amount + '%' : c.amount.toFixed(2) + ' €';
        var expires   = c.date_expires ? new Date(c.date_expires + 'T00:00:00').toLocaleDateString('de-DE') : '—';
        var usageLim  = c.usage_limit ? ' / ' + c.usage_limit : '';
        var usageStr  = c.usage_count + usageLim;
        html += '<tr style="border-bottom:1px solid var(--border-hair);">'
            + '<td style="padding:10px 12px;color:var(--creme);font-family:monospace;font-weight:600;">' + _rcEsc(c.code.toUpperCase()) + '</td>'
            + '<td style="padding:10px 12px;color:var(--creme-dim);font-size:12px;">' + _rcEsc(typeLabel) + '</td>'
            + '<td style="padding:10px 12px;color:var(--creme);">' + _rcEsc(amount) + '</td>'
            + '<td style="padding:10px 12px;color:var(--creme-dim);font-size:12px;">' + _rcEsc(expires) + '</td>'
            + '<td style="padding:10px 12px;color:var(--creme-dim);font-size:12px;">' + _rcEsc(usageStr) + '</td>'
            + '<td style="padding:10px 12px;">'
            +   '<button onclick="rcOpenForm(' + c.id + ')" style="padding:4px 10px;background:var(--bg-raised);color:var(--creme);border:1px solid var(--border-thin);border-radius:2px;cursor:pointer;font-size:11px;margin-right:6px;">Bearbeiten</button>'
            +   '<button onclick="rcDelete(' + c.id + ',\'' + _rcEsc(c.code) + '\')" style="padding:4px 10px;background:transparent;color:#ff7070;border:1px solid rgba(255,112,112,.3);border-radius:2px;cursor:pointer;font-size:11px;">L\xf6schen</button>'
            + '</td>'
            + '</tr>';
    });
    tbody.innerHTML = html;
}

function rcOpenForm(id) {
    document.getElementById('rc-form-error').style.display = 'none';
    if (id === 0) {
        document.getElementById('rc-form-title').textContent = 'Neuer Rabattcode';
        document.getElementById('rc-coupon-id').value = '0';
        ['rc-code','rc-amount','rc-max-amount','rc-min-amount','rc-min-qty','rc-usage-limit','rc-date-start','rc-date-expires','rc-products','rc-categories'].forEach(function(id) {
            document.getElementById(id).value = '';
        });
        document.getElementById('rc-type').value = 'percent';
        document.getElementById('rc-usage-per-user').value = '1';
        ['rc-exclude-sale','rc-new-customers','rc-individual','rc-free-shipping'].forEach(function(id) {
            document.getElementById(id).checked = false;
        });
    } else {
        var c = _rcCoupons.find(function(x) { return x.id === id; });
        if (!c) return;
        document.getElementById('rc-form-title').textContent = 'Rabattcode bearbeiten';
        document.getElementById('rc-coupon-id').value = c.id;
        document.getElementById('rc-code').value = c.code.toUpperCase();
        document.getElementById('rc-type').value = c.discount_type;
        document.getElementById('rc-amount').value = c.amount;
        document.getElementById('rc-max-amount').value = c.maximum_amount || '';
        document.getElementById('rc-min-amount').value = c.minimum_amount || '';
        document.getElementById('rc-min-qty').value = c.min_cart_quantity || '';
        document.getElementById('rc-usage-limit').value = c.usage_limit || '';
        document.getElementById('rc-usage-per-user').value = c.usage_limit_per_user || '';
        document.getElementById('rc-date-start').value = c.date_start || '';
        document.getElementById('rc-date-expires').value = c.date_expires || '';
        document.getElementById('rc-products').value = (c.product_ids || []).join(', ');
        document.getElementById('rc-categories').value = (c.product_categories || []).join(', ');
        document.getElementById('rc-exclude-sale').checked  = !!c.exclude_sale_items;
        document.getElementById('rc-new-customers').checked = !!c.new_customers_only;
        document.getElementById('rc-individual').checked    = !!c.individual_use;
        document.getElementById('rc-free-shipping').checked = !!c.free_shipping;
    }
    var wrap = document.getElementById('rc-form-wrap');
    wrap.style.display = 'block';
    wrap.scrollIntoView({behavior: 'smooth', block: 'start'});
}

function rcCloseForm() {
    document.getElementById('rc-form-wrap').style.display = 'none';
}

function rcSave() {
    var btn   = document.getElementById('rc-save-btn');
    var errEl = document.getElementById('rc-form-error');
    errEl.style.display = 'none';

    var code   = document.getElementById('rc-code').value.trim();
    var amount = parseFloat(document.getElementById('rc-amount').value) || 0;
    if (!code)     { errEl.textContent = 'Code darf nicht leer sein.'; errEl.style.display = 'block'; return; }
    if (amount<=0) { errEl.textContent = 'Betrag muss gr\xf6\xdfer als 0 sein.'; errEl.style.display = 'block'; return; }

    btn.disabled = true; btn.textContent = 'Speichern…';

    var body = 'action=pa_save_coupon&nonce=' + _plNonce
        + '&coupon_id='          + encodeURIComponent(document.getElementById('rc-coupon-id').value)
        + '&code='               + encodeURIComponent(code)
        + '&discount_type='      + encodeURIComponent(document.getElementById('rc-type').value)
        + '&amount='             + encodeURIComponent(amount)
        + '&maximum_amount='     + encodeURIComponent(document.getElementById('rc-max-amount').value || '0')
        + '&minimum_amount='     + encodeURIComponent(document.getElementById('rc-min-amount').value || '0')
        + '&min_cart_quantity='  + encodeURIComponent(document.getElementById('rc-min-qty').value || '0')
        + '&usage_limit='        + encodeURIComponent(document.getElementById('rc-usage-limit').value || '0')
        + '&usage_limit_per_user='+ encodeURIComponent(document.getElementById('rc-usage-per-user').value || '0')
        + '&date_start='         + encodeURIComponent(document.getElementById('rc-date-start').value)
        + '&date_expires='       + encodeURIComponent(document.getElementById('rc-date-expires').value)
        + '&product_ids='        + encodeURIComponent(document.getElementById('rc-products').value)
        + '&product_categories=' + encodeURIComponent(document.getElementById('rc-categories').value)
        + '&exclude_sale_items=' + (document.getElementById('rc-exclude-sale').checked ? '1' : '0')
        + '&new_customers_only=' + (document.getElementById('rc-new-customers').checked ? '1' : '0')
        + '&individual_use='     + (document.getElementById('rc-individual').checked ? '1' : '0')
        + '&free_shipping='      + (document.getElementById('rc-free-shipping').checked ? '1' : '0');

    fetch(_plAjax, {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: body
    })
    .then(function(r) { return r.json(); })
    .then(function(d) {
        if (d.success) { rcCloseForm(); loadCoupons(); }
        else { errEl.textContent = d.data || 'Fehler beim Speichern.'; errEl.style.display = 'block'; }
        btn.disabled = false; btn.textContent = 'Speichern';
    })
    .catch(function() {
        errEl.textContent = 'Netzwerkfehler.'; errEl.style.display = 'block';
        btn.disabled = false; btn.textContent = 'Speichern';
    });
}

function rcDelete(id, code) {
    if (!confirm('Rabattcode "' + code.toUpperCase() + '" wirklich l\xf6schen?')) return;
    fetch(_plAjax, {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'action=pa_delete_coupon&nonce=' + _plNonce + '&coupon_id=' + id
    })
    .then(function(r) { return r.json(); })
    .then(function(d) {
        if (d.success) loadCoupons();
        else alert(d.data || 'Fehler beim L\xf6schen.');
    });
}

// ── Social Deals System ──────────────────────────────────────────────────────
var _sdConfig = [];
var SD_PLATFORMS = [
    {key:'instagram', label:'Instagram'},
    {key:'facebook',  label:'Facebook'},
    {key:'tiktok',    label:'TikTok'},
    {key:'pinterest', label:'Pinterest'},
    {key:'youtube',   label:'YouTube'},
    {key:'twitter',   label:'X / Twitter'}
];

function loadSmConfig() {
    fetch(_plAjax, {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'action=pa_get_sm_config&nonce=' + _plNonce
    })
    .then(function(r) { return r.json(); })
    .then(function(d) {
        if (!d.success) return;
        _sdConfig = d.data;
    });
}

function renderSmConfigForm() {
    var wrap = document.getElementById('sd-platforms-config');
    var html = '';
    SD_PLATFORMS.forEach(function(p) {
        var saved   = _sdConfig.find(function(x) { return x.key === p.key; }) || {};
        var active  = saved.active  ? 'checked' : '';
        var handle  = saved.handle  || '';
        var pct_min = saved.pct_min != null ? saved.pct_min : 5;
        var pct_max = saved.pct_max != null ? saved.pct_max : 20;
        html += '<div style="display:grid;grid-template-columns:140px 1fr 220px;gap:12px;align-items:center;padding:10px 0;border-bottom:1px solid var(--border-hair);">'
            + '<label style="display:flex;align-items:center;gap:8px;cursor:pointer;">'
            +   '<input type="checkbox" class="sd-plat-active" data-key="' + p.key + '" ' + active + '>'
            +   '<span style="color:var(--creme);font-size:13px;font-weight:600;">' + p.label + '</span>'
            + '</label>'
            + '<input type="text" class="sd-plat-handle rc-inp" data-key="' + p.key + '" value="' + _rcEsc(handle) + '" placeholder="@account_handle">'
            + '<div style="display:flex;align-items:center;gap:6px;">'
            +   '<input type="number" class="sd-plat-min rc-inp" data-key="' + p.key + '" value="' + pct_min + '" min="1" max="100" style="width:64px;" placeholder="min">'
            +   '<span style="color:var(--creme-dim);font-size:13px;">–</span>'
            +   '<input type="number" class="sd-plat-max rc-inp" data-key="' + p.key + '" value="' + pct_max + '" min="1" max="100" style="width:64px;" placeholder="max">'
            +   '<span style="color:var(--creme-dim);font-size:12px;">%</span>'
            + '</div>'
            + '</div>';
    });
    wrap.innerHTML = html;
}

function sdToggleConfig() {
    var wrap = document.getElementById('sd-config-wrap');
    if (wrap.style.display === 'none' || !wrap.style.display) {
        renderSmConfigForm();
        wrap.style.display = 'block';
    } else {
        wrap.style.display = 'none';
    }
}

function sdSaveConfig() {
    var platforms = [];
    SD_PLATFORMS.forEach(function(p) {
        var activeEl = document.querySelector('.sd-plat-active[data-key="' + p.key + '"]');
        var handleEl = document.querySelector('.sd-plat-handle[data-key="' + p.key + '"]');
        var minEl    = document.querySelector('.sd-plat-min[data-key="' + p.key + '"]');
        var maxEl    = document.querySelector('.sd-plat-max[data-key="' + p.key + '"]');
        platforms.push({
            key:     p.key,
            label:   p.label,
            handle:  handleEl  ? handleEl.value.trim()  : '',
            pct_min: minEl     ? parseInt(minEl.value)  : 5,
            pct_max: maxEl     ? parseInt(maxEl.value)  : 20,
            active:  activeEl  ? activeEl.checked       : false
        });
    });
    var errEl = document.getElementById('sd-config-error');
    errEl.style.display = 'none';
    fetch(_plAjax, {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'action=pa_save_sm_config&nonce=' + _plNonce + '&platforms=' + encodeURIComponent(JSON.stringify(platforms))
    })
    .then(function(r) { return r.json(); })
    .then(function(d) {
        if (d.success) {
            _sdConfig = platforms;
            document.getElementById('sd-config-wrap').style.display = 'none';
        } else {
            errEl.textContent = d.data || 'Fehler beim Speichern.';
            errEl.style.display = 'block';
        }
    });
}

function sdSetFilter(btn, status) {
    document.querySelectorAll('.sd-filter').forEach(function(b) { b.classList.remove('active'); });
    btn.classList.add('active');
    loadDeals(status);
}

function loadDeals(status) {
    var tbody = document.getElementById('sd-table-body');
    tbody.innerHTML = '<tr><td colspan="6" style="padding:30px;text-align:center;color:var(--creme-dim);">Lade…</td></tr>';
    fetch(_plAjax, {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'action=pa_get_deals&nonce=' + _plNonce + (status ? '&status=' + encodeURIComponent(status) : '')
    })
    .then(function(r) { return r.json(); })
    .then(function(d) {
        if (!d.success) return;
        renderDealsTable(d.data);
    });
}

function renderDealsTable(deals) {
    var tbody = document.getElementById('sd-table-body');
    if (!deals.length) {
        tbody.innerHTML = '<tr><td colspan="6" style="padding:30px;text-align:center;color:var(--creme-dim);">Keine Deals vorhanden.</td></tr>';
        return;
    }
    var sColors = {pending:'var(--plum-hot)', approved:'#3a9e6b', rejected:'#666'};
    var sLabels = {pending:'Offen', approved:'Genehmigt', rejected:'Abgelehnt'};
    var platLabels = {};
    SD_PLATFORMS.forEach(function(p) { platLabels[p.key] = p.label; });
    var html = '';
    deals.forEach(function(d) {
        var sc  = sColors[d.status] || '#666';
        var sl  = sLabels[d.status] || d.status;
        var pl  = platLabels[d.platform] || d.platform;
        var actions = '';
        if (d.status === 'pending') {
            actions = '<div style="display:flex;align-items:center;gap:6px;flex-wrap:wrap;">'
                + '<input type="number" id="sd-pct-' + d.id + '" min="1" max="100" step="0.1" placeholder="%" '
                + 'style="width:58px;padding:4px 6px;border:1px solid var(--border-thin);border-radius:2px;background:var(--bg-surface);color:var(--creme);font-size:12px;">'
                + '<button onclick="sdApprove(' + d.id + ')" style="padding:4px 10px;background:var(--plum);color:var(--creme);border:none;border-radius:2px;cursor:pointer;font-size:11px;white-space:nowrap;">Genehmigen</button>'
                + '<button onclick="sdReject(' + d.id + ')" style="padding:4px 8px;background:transparent;color:#ff7070;border:1px solid rgba(255,112,112,.3);border-radius:2px;cursor:pointer;font-size:11px;">Ablehnen</button>'
                + '</div>';
        } else if (d.status === 'approved' && d.coupon_code) {
            actions = '<div style="font-family:monospace;font-size:12px;color:var(--creme);">' + _rcEsc(d.coupon_code) + '</div>'
                + '<div style="font-size:11px;color:var(--creme-dim);margin-top:2px;">' + d.assigned_pct + '%</div>';
        }
        html += '<tr style="border-bottom:1px solid var(--border-hair);">'
            + '<td style="padding:10px 12px;">'
            +   '<div style="font-size:13px;color:var(--creme);">' + _rcEsc(d.user_name) + '</div>'
            +   '<div style="font-size:11px;color:var(--creme-dim);margin-top:2px;">' + _rcEsc(d.user_email) + '</div>'
            + '</td>'
            + '<td style="padding:10px 12px;">'
            +   '<div style="font-size:13px;color:var(--creme);">' + _rcEsc(d.order_number) + '</div>'
            +   '<div style="font-size:11px;color:var(--creme-dim);margin-top:2px;max-width:200px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">' + _rcEsc(d.products) + '</div>'
            + '</td>'
            + '<td style="padding:10px 12px;font-size:13px;color:var(--creme);">' + _rcEsc(pl) + '</td>'
            + '<td style="padding:10px 12px;font-family:monospace;font-size:13px;color:var(--creme);">' + _rcEsc(d.handle) + '</td>'
            + '<td style="padding:10px 12px;">'
            +   '<span style="display:inline-block;padding:2px 8px;border-radius:10px;background:' + sc + '22;color:' + sc + ';font-size:11px;font-weight:700;">' + sl + '</span>'
            + '</td>'
            + '<td style="padding:10px 12px;">' + actions + '</td>'
            + '</tr>';
    });
    tbody.innerHTML = html;
}

function sdApprove(dealId) {
    var pct = parseFloat(document.getElementById('sd-pct-' + dealId)?.value) || 0;
    if (pct <= 0 || pct > 100) { alert('Bitte einen g\xfcltigen Prozentwert (1–100) eingeben.'); return; }
    if (!confirm('Deal genehmigen mit ' + pct + '%?')) return;
    fetch(_plAjax, {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'action=pa_approve_deal&nonce=' + _plNonce + '&deal_id=' + dealId + '&percent=' + pct
    })
    .then(function(r) { return r.json(); })
    .then(function(d) {
        if (d.success) {
            var filterActive = document.querySelector('.sd-filter.active');
            loadDeals(filterActive ? filterActive.dataset.st : '');
        } else {
            alert(d.data || 'Fehler beim Genehmigen.');
        }
    });
}

function sdReject(dealId) {
    if (!confirm('Deal ablehnen?')) return;
    fetch(_plAjax, {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'action=pa_reject_deal&nonce=' + _plNonce + '&deal_id=' + dealId
    })
    .then(function(r) { return r.json(); })
    .then(function(d) {
        if (d.success) {
            var filterActive = document.querySelector('.sd-filter.active');
            loadDeals(filterActive ? filterActive.dataset.st : '');
        } else {
            alert(d.data || 'Fehler beim Ablehnen.');
        }
    });
}

// ═══════════════════════════════════════════════════════════════════════════════
// VARIANTEN VERBINDEN
// ═══════════════════════════════════════════════════════════════════════════════

var _variantSelectedParentId = 0;
var _variantSearchTimer = null;

function openVariantPanel() {
    switchEditPanel('variants');
    _variantSelectedParentId = 0;
    document.getElementById('variant-parent-search').value = '';
    document.getElementById('variant-parent-results').style.display = 'none';
    document.getElementById('variant-parent-selected').style.display = 'none';
    document.getElementById('variant-save-status').textContent = '';
    // Load current parent if set
    fetch(ajaxUrl, {
        method: 'POST',
        headers: {'Content-Type':'application/x-www-form-urlencoded'},
        body: new URLSearchParams({action:'get_product_detail', nonce:productListNonce, product_id:_editProductId}).toString()
    })
    .then(r => r.json())
    .then(d => {
        if (d.success && d.data && d.data.variant_parent_id) {
            _variantSelectedParentId = d.data.variant_parent_id;
            document.getElementById('variant-parent-name').textContent = d.data.variant_parent_name || ('Produkt #' + _variantSelectedParentId);
            document.getElementById('variant-parent-selected').style.display = 'flex';
        }
    })
    .catch(() => {});
}

function variantSearchProducts(q) {
    clearTimeout(_variantSearchTimer);
    var res = document.getElementById('variant-parent-results');
    if (!q || q.length < 2) { res.style.display = 'none'; return; }
    _variantSearchTimer = setTimeout(function() {
        fetch(ajaxUrl, {
            method: 'POST',
            headers: {'Content-Type':'application/x-www-form-urlencoded'},
            body: new URLSearchParams({action:'pa_search_products', nonce:productListNonce, q:q}).toString()
        })
        .then(r => r.json())
        .then(d => {
            if (!d.success || !d.data.length) { res.style.display = 'none'; return; }
            res.innerHTML = d.data.map(function(p) {
                return '<div onclick="variantSelectParent(' + p.id + ', ' + JSON.stringify(p.name) + ')" style="padding:9px 14px;cursor:pointer;font-size:13px;color:var(--creme);border-bottom:1px solid var(--border-hair);" onmouseover="this.style.background=\'var(--bg-raised)\'" onmouseout="this.style.background=\'\'">' + escHtml(p.name) + ' <span style="color:var(--creme-muted);font-size:12px;">' + escHtml(p.sku || '') + '</span></div>';
            }).join('');
            res.style.display = 'block';
        })
        .catch(() => { res.style.display = 'none'; });
    }, 300);
}

function variantSelectParent(id, name) {
    _variantSelectedParentId = id;
    document.getElementById('variant-parent-name').textContent = name;
    document.getElementById('variant-parent-selected').style.display = 'flex';
    document.getElementById('variant-parent-results').style.display = 'none';
    document.getElementById('variant-parent-search').value = '';
}

function variantClearParent() {
    _variantSelectedParentId = 0;
    document.getElementById('variant-parent-selected').style.display = 'none';
}

function variantSaveParent() {
    var status = document.getElementById('variant-save-status');
    status.textContent = 'Speichern…';
    fetch(ajaxUrl, {
        method: 'POST',
        headers: {'Content-Type':'application/x-www-form-urlencoded'},
        body: new URLSearchParams({
            action:     'pa_set_variant_parent',
            nonce:      productListNonce,
            product_id: _editProductId,
            parent_id:  _variantSelectedParentId
        }).toString()
    })
    .then(r => r.json())
    .then(d => {
        status.textContent = d.success ? 'Gespeichert.' : ('Fehler: ' + (d.data || ''));
        if (d.success) setTimeout(() => { status.textContent = ''; switchEditPanel('main'); }, 1200);
    })
    .catch(() => { status.textContent = 'Netzwerkfehler.'; });
}

// ═══════════════════════════════════════════════════════════════════════════════
// TAGS PANEL
// ═══════════════════════════════════════════════════════════════════════════════

var _tagPanelPool = null;
var _tagPanelSel  = { fixed: new Set(), variable: {} };
var _tagPanelPid  = 0;

function openTagsPanel() {
    switchEditPanel('tags');
    document.getElementById('tags-save-status').textContent = '';
    document.getElementById('edit-panel-tags-body').innerHTML =
        '<div style="padding:24px;text-align:center;color:var(--creme-dim);">Lade…</div>';

    _tagPanelPid = _editProductId;
    _tagPanelSel = { fixed: new Set(), variable: {} };

    Promise.all([
        fetch(_plAjax, { method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'},
            body:'action=pa_get_tag_pool&nonce='+_plNonce }).then(function(r){return r.json();}),
        fetch(_plAjax, { method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'},
            body:'action=get_product_detail&nonce='+_plNonce+'&product_id='+_editProductId }).then(function(r){return r.json();})
    ]).then(function(results) {
        var poolData    = results[0];
        var productData = results[1];
        if (!poolData.success || !productData.success) {
            document.getElementById('edit-panel-tags-body').innerHTML =
                '<div style="padding:20px;color:#e06060;">Fehler beim Laden der Tags.</div>';
            return;
        }
        _tagPanelPool = poolData.data;
        (productData.data.tags_fixed || []).forEach(function(t) { _tagPanelSel.fixed.add(t.term_id); });
        (productData.data.tags_variable || []).forEach(function(t) { _tagPanelSel.variable[t.prefix] = t.name; });
        _tagPanelRender();
    }).catch(function() {
        document.getElementById('edit-panel-tags-body').innerHTML =
            '<div style="padding:20px;color:#e06060;">Netzwerkfehler.</div>';
    });
}

function _tagPanelRender() {
    var pool = _tagPanelPool;
    var sel  = _tagPanelSel;
    var html = '';

    (pool.variable_types || []).forEach(function(vt) {
        var vals   = pool.variable_values[vt.name] || [];
        var curVal = sel.variable[vt.name] || null;
        html += '<div class="ep-tag-group">'
              + '<p class="ep-tag-group-label"><span>' + _efEsc(vt.name) + '</span>'
              + '<button class="ep-del-btn" onclick="_tpDeleteTerm(' + vt.term_id + ',\'product_tag\',event)" title="Typ + Werte löschen">×</button></p>'
              + '<div class="ep-tag-chips">';
        vals.forEach(function(v) {
            var s = curVal === v.name ? ' ep-selected' : '';
            html += '<span class="ep-tag-chip' + s + '">'
                  + '<span onclick="_tpToggleVar(\'' + _efEsc(vt.name) + '\',\'' + _efEsc(v.name) + '\')">' + _efEsc(v.name) + '</span>'
                  + '<button class="ep-del-btn" onclick="_tpDeleteTerm(' + v.term_id + ',\'product_tag\',event)" title="Löschen">×</button>'
                  + '</span>';
        });
        html += '<span class="ep-tag-chip ep-new-btn" onclick="_tpShowInput(\'' + _efEsc(vt.name) + '\',event)">+ Neu</span>'
              + '</div>'
              + '<div id="ep-vinput-wrap-' + _efEsc(vt.name) + '" class="ep-vinput-wrap" style="display:none;">'
              + '<input type="text" class="ep-vinput" id="ep-vinput-' + _efEsc(vt.name) + '" placeholder="Wert…"'
              + ' onkeydown="_tpVKey(event,\'' + _efEsc(vt.name) + '\')">'
              + '<button onclick="_tpConfirmInput(\'' + _efEsc(vt.name) + '\')" style="padding:4px 9px;background:var(--plum);color:var(--creme);border:none;border-radius:2px;cursor:pointer;font-size:12px;">✓</button>'
              + '<button onclick="_tpHideInput(\'' + _efEsc(vt.name) + '\')" style="padding:4px 9px;background:var(--bg-surface);color:var(--creme-dim);border:1px solid var(--border-thin);border-radius:2px;cursor:pointer;font-size:12px;">✕</button>'
              + '</div>'
              + '</div>';
    });

    if ((pool.fixed || []).length) {
        html += '<div class="ep-tag-group"><p class="ep-tag-group-label">Tags</p><div class="ep-tag-chips">';
        pool.fixed.forEach(function(t) {
            var s = sel.fixed.has(t.term_id) ? ' ep-selected' : '';
            html += '<span class="ep-tag-chip' + s + '">'
                  + '<span onclick="_tpToggleFixed(' + t.term_id + ')">' + _efEsc(t.name) + '</span>'
                  + '<button class="ep-del-btn" onclick="_tpDeleteTerm(' + t.term_id + ',\'product_tag\',event)" title="Löschen">×</button>'
                  + '</span>';
        });
        html += '</div></div>';
    }

    if (!html) {
        html = '<p style="color:var(--creme-dim);font-size:13px;">Keine Tags vorhanden. Tags können auf der Produkterstellungsseite angelegt werden.</p>';
    }

    document.getElementById('edit-panel-tags-body').innerHTML = html;
}

function _tpDeleteTerm(termId, taxonomy, ev) {
    if (ev) ev.stopPropagation();
    if (!confirm('Wirklich löschen? Dieser Eintrag wird von allen Produkten entfernt.')) return;
    fetch(_plAjax, {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'action=pa_delete_term&nonce=' + _plNonce + '&term_id=' + termId + '&taxonomy=' + taxonomy
    })
    .then(function(r) { return r.json(); })
    .then(function(d) {
        if (!d.success) { alert('Fehler: ' + (d.data || '')); return; }
        if (taxonomy === 'product_tag') {
            var vtIdx = (_tagPanelPool.variable_types || []).findIndex(function(vt) { return vt.term_id === termId; });
            if (vtIdx !== -1) {
                var typeName = _tagPanelPool.variable_types[vtIdx].name;
                _tagPanelPool.variable_types.splice(vtIdx, 1);
                delete _tagPanelPool.variable_values[typeName];
                delete _tagPanelSel.variable[typeName];
            } else {
                _tagPanelSel.fixed.delete(termId);
                _tagPanelPool.fixed = (_tagPanelPool.fixed || []).filter(function(t) { return t.term_id !== termId; });
                for (var prefix in _tagPanelPool.variable_values) {
                    var before = _tagPanelPool.variable_values[prefix].length;
                    _tagPanelPool.variable_values[prefix] = _tagPanelPool.variable_values[prefix].filter(function(v) { return v.term_id !== termId; });
                    if (_tagPanelPool.variable_values[prefix].length < before) break;
                }
            }
        }
        _tagPanelRender();
    })
    .catch(function() { alert('Netzwerkfehler.'); });
}

function _tpToggleFixed(termId) {
    if (_tagPanelSel.fixed.has(termId)) _tagPanelSel.fixed.delete(termId);
    else _tagPanelSel.fixed.add(termId);
    _tagPanelRender();
}

function _tpToggleVar(prefix, val) {
    if (_tagPanelSel.variable[prefix] === val) delete _tagPanelSel.variable[prefix];
    else _tagPanelSel.variable[prefix] = val;
    _tagPanelRender();
}

function _tpShowInput(prefix, ev) {
    if (ev) ev.stopPropagation();
    var wrap = document.getElementById('ep-vinput-wrap-' + prefix);
    var inp  = document.getElementById('ep-vinput-' + prefix);
    if (wrap) { wrap.style.display = 'flex'; }
    if (inp)  { inp.value = ''; inp.focus(); }
}

function _tpHideInput(prefix) {
    var wrap = document.getElementById('ep-vinput-wrap-' + prefix);
    if (wrap) wrap.style.display = 'none';
}

function _tpVKey(ev, prefix) {
    if (ev.key === 'Enter')  _tpConfirmInput(prefix);
    if (ev.key === 'Escape') _tpHideInput(prefix);
}

function _tpConfirmInput(prefix) {
    var inp = document.getElementById('ep-vinput-' + prefix);
    var val = inp ? inp.value.trim() : '';
    if (!val) return;
    _tpHideInput(prefix);

    var already = (_tagPanelPool.variable_values[prefix] || []).find(function(v) { return v.name === val; });
    if (already) {
        _tagPanelSel.variable[prefix] = val;
        _tagPanelRender();
        return;
    }

    fetch(_plAjax, {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'action=pa_create_tag&nonce=' + _plNonce
            + '&type=variable&prefix=' + encodeURIComponent(prefix)
            + '&name=' + encodeURIComponent(val)
    })
    .then(function(r) { return r.json(); })
    .then(function(d) {
        if (d.success) {
            if (!_tagPanelPool.variable_values[prefix]) _tagPanelPool.variable_values[prefix] = [];
            _tagPanelPool.variable_values[prefix].push({ term_id: d.data.term_id, name: val });
        }
        _tagPanelSel.variable[prefix] = val;
        _tagPanelRender();
    })
    .catch(function() {
        _tagPanelSel.variable[prefix] = val;
        _tagPanelRender();
    });
}

function saveProductTags() {
    var btn    = document.getElementById('tags-save-btn');
    var status = document.getElementById('tags-save-status');
    btn.disabled = true;
    status.textContent = 'Speichern…';

    var fixedIds = Array.from(_tagPanelSel.fixed).join(',');
    var varTags  = Object.keys(_tagPanelSel.variable).map(function(prefix) {
        return { prefix: prefix, value: _tagPanelSel.variable[prefix] };
    });

    fetch(_plAjax, {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'action=pa_save_product_tags&nonce=' + _plNonce
            + '&product_id=' + _tagPanelPid
            + '&fixed_ids=' + encodeURIComponent(fixedIds)
            + '&variable_tags=' + encodeURIComponent(JSON.stringify(varTags))
    })
    .then(function(r) { return r.json(); })
    .then(function(d) {
        btn.disabled = false;
        if (d.success) {
            status.textContent = '✓ Gespeichert';
            setTimeout(function() { status.textContent = ''; switchEditPanel('main'); }, 1000);
        } else {
            status.textContent = 'Fehler: ' + (d.data || '');
        }
    })
    .catch(function() {
        btn.disabled = false;
        status.textContent = 'Netzwerkfehler.';
    });
}

// ── Gattung / Art Dropdown-Pool ───────────────────────────────────────────────
var _plTaxPool = { gattungen: [], arts: {} };
var _plComboCurrentKey = '';

(async function() {
    try {
        const res  = await fetch(_plAjax, {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'action=pa_get_taxonomy_values&nonce=' + _plNonce
        });
        const data = await res.json();
        if (data.success) _plTaxPool = data.data;
    } catch(e) {}
})();

function _plComboToggle(key) {
    var drop = document.getElementById('pl-combo-drop-' + key);
    var trig = document.querySelector('#pl-combo-' + key + ' .pa-combo-trigger');
    if (!drop) return;
    var isOpen = drop.style.display !== 'none';
    ['gattung', 'art'].forEach(function(k) {
        var d = document.getElementById('pl-combo-drop-' + k);
        var t = document.querySelector('#pl-combo-' + k + ' .pa-combo-trigger');
        if (d) d.style.display = 'none';
        if (t) t.classList.remove('open');
    });
    if (!isOpen) {
        drop.style.display = 'block';
        if (trig) trig.classList.add('open');
    }
}

function _plComboSelect(key, val) {
    var hi   = document.getElementById('efi-' + key);
    var lbl  = document.getElementById('pl-combo-lbl-' + key);
    var drop = document.getElementById('pl-combo-drop-' + key);
    var trig = document.querySelector('#pl-combo-' + key + ' .pa-combo-trigger');
    if (hi)  hi.value = val;
    if (lbl) { lbl.textContent = val; lbl.classList.remove('pa-combo-placeholder'); }
    document.querySelectorAll('#pl-combo-opts-' + key + ' .pa-combo-option').forEach(function(btn) {
        btn.classList.toggle('selected', btn.dataset.val === val);
    });
    if (drop) drop.style.display = 'none';
    if (trig) trig.classList.remove('open');
    if (key === 'gattung') _plRefreshArtCombo(val);
}

function _plRefreshArtCombo(gattung) {
    var arts = _plTaxPool.arts[gattung] || [];
    if (!arts.length) {
        var all = new Set();
        Object.values(_plTaxPool.arts).forEach(function(arr) { arr.forEach(function(a) { all.add(a); }); });
        arts = Array.from(all).sort();
    }
    var artHi  = document.getElementById('efi-art');
    var artLbl = document.getElementById('pl-combo-lbl-art');
    if (artHi)  artHi.value = '';
    if (artLbl) { artLbl.textContent = 'z. B. zonale'; artLbl.classList.add('pa-combo-placeholder'); }
    var optsWrap = document.getElementById('pl-combo-opts-art');
    if (!optsWrap) return;
    optsWrap.innerHTML = arts.map(function(o) {
        return '<button type="button" class="pa-combo-option" data-val="' + _efEsc(o) + '" onclick="_plComboSelect(\'art\',\'' + _efEsc(o) + '\')">' + _efEsc(o) + '</button>';
    }).join('');
}

function _plComboOpenAdd(key, ev) {
    if (ev) ev.stopPropagation();
    _plComboCurrentKey = key;
    var labels = { gattung: 'Neue Gattung hinzufügen', art: 'Neue Art hinzufügen' };
    var placeholders = { gattung: 'z. B. Pelargonium', art: 'z. B. zonale' };
    var title = document.getElementById('pa-combo-dialog-title');
    var inp   = document.getElementById('pa-combo-dialog-input');
    if (title) title.textContent = labels[key] || 'Neuer Eintrag';
    if (inp)   { inp.value = ''; inp.placeholder = placeholders[key] || ''; }
    var overlay = document.getElementById('pa-combo-overlay');
    if (overlay) overlay.style.display = 'flex';
    setTimeout(function() { if (inp) inp.focus(); }, 50);
}

function paComboConfirmAdd() {
    var inp = document.getElementById('pa-combo-dialog-input');
    var val = inp ? inp.value.trim() : '';
    if (!val) { if (inp) inp.focus(); return; }
    var key = _plComboCurrentKey;
    if (key === 'gattung') {
        if (_plTaxPool.gattungen.indexOf(val) === -1) _plTaxPool.gattungen.push(val);
        if (!_plTaxPool.arts[val]) _plTaxPool.arts[val] = [];
    } else if (key === 'art') {
        var curGattung = (document.getElementById('efi-gattung') || {value:''}).value || '';
        if (!_plTaxPool.arts[curGattung]) _plTaxPool.arts[curGattung] = [];
        if (_plTaxPool.arts[curGattung].indexOf(val) === -1) _plTaxPool.arts[curGattung].push(val);
    }
    paComboCloseOverlay();
    var optsWrap = document.getElementById('pl-combo-opts-' + key);
    if (optsWrap) {
        var curG  = (document.getElementById('efi-gattung') || {value:''}).value || '';
        var list  = key === 'gattung' ? _plTaxPool.gattungen : (_plTaxPool.arts[curG] || []);
        optsWrap.innerHTML = list.map(function(o) {
            return '<button type="button" class="pa-combo-option" data-val="' + _efEsc(o) + '" onclick="_plComboSelect(\'' + key + '\',\'' + _efEsc(o) + '\')">' + _efEsc(o) + '</button>';
        }).join('');
    }
    _plComboSelect(key, val);
}

function paComboCloseOverlay() {
    var overlay = document.getElementById('pa-combo-overlay');
    if (overlay) overlay.style.display = 'none';
}

// ═══════════════════════════════════════════════════════════════════════════════

// ── Migration ─────────────────────────────────────────────────────────────────
const migNonce = '<?php echo wp_create_nonce('product_list_nonce'); ?>';
const migAjax  = '<?php echo admin_url('admin-ajax.php'); ?>';

function migDoExport() {
    const pw = document.getElementById('mig-export-pw').value;
    if (pw.length < 8) { alert('Passwort muss mindestens 8 Zeichen lang sein.'); return; }
    const status = document.getElementById('mig-export-status');
    status.textContent = 'Export läuft…';
    status.style.display = 'block';

    const form = document.createElement('form');
    form.method = 'POST';
    form.action = migAjax;
    form.style.display = 'none';
    [['action','pa_migration_export'],['nonce',migNonce],['password',pw]].forEach(([k,v]) => {
        const inp = document.createElement('input');
        inp.type = 'hidden'; inp.name = k; inp.value = v;
        form.appendChild(inp);
    });
    document.body.appendChild(form);
    form.submit();
    document.body.removeChild(form);
    setTimeout(() => { status.textContent = 'Download gestartet.'; }, 800);
}

function migSendImport(dryRun) {
    const file = document.getElementById('mig-import-file').files[0];
    const pw   = document.getElementById('mig-import-pw').value;
    if (!file) { alert('Bitte eine ZIP-Datei auswählen.'); return; }
    if (!pw)   { alert('Bitte Passwort eingeben.'); return; }

    const status = document.getElementById('mig-import-status');
    status.textContent = dryRun ? 'Trocken-Lauf läuft…' : 'Import läuft…';
    status.style.display = 'block';

    const fd = new FormData();
    fd.append('action',        'pa_migration_import');
    fd.append('nonce',         migNonce);
    fd.append('password',      pw);
    fd.append('dry_run',       dryRun ? '1' : '0');
    fd.append('migration_zip', file);

    fetch(migAjax, { method: 'POST', body: fd })
        .then(r => r.json())
        .then(d => {
            if (!d.success) { status.textContent = 'Fehler: ' + (d.data || 'Unbekannter Fehler'); return; }
            if (d.data.dry_run) {
                const diff = d.data.diff;
                let text = '── Trocken-Lauf ──\n';
                text += 'Produkte:    ' + diff.products.total + ' gesamt | ' + diff.products.new + ' neu, ' + diff.products.update + ' aktualisieren\n';
                text += 'Bestellungen:' + diff.orders.total   + ' gesamt | ' + diff.orders.new   + ' neu, ' + diff.orders.skip    + ' überspringen\n';
                text += 'Kund:innen:  ' + diff.users.total    + ' gesamt | ' + diff.users.new    + ' neu, ' + diff.users.update   + ' aktualisieren\n';
                text += 'Einstellungen: ' + diff.settings.total + ' Optionen\n';
                text += 'Medien-Metadaten: ' + diff.media.total + ' Einträge\n';
                text += 'Medien-Dateien: ' + (diff.media.files ?? '?') + ' Dateien im ZIP\n';
                text += '\nKeine Daten wurden verändert.';
                status.textContent = text;
            } else {
                const r = d.data.results;
                let text = '── Import abgeschlossen ──\n';
                if (r.media_files) text += 'Medien-Dateien: ' + r.media_files.restored + ' wiederhergestellt, ' + r.media_files.skipped + ' übersprungen, ' + r.media_files.errors.length + ' Fehler\n';
                if (r.products)    text += 'Produkte:       ' + r.products.created + ' erstellt, ' + r.products.updated + ' aktualisiert, ' + r.products.errors.length + ' Fehler\n';
                if (r.orders)      text += 'Bestellungen:   ' + r.orders.created   + ' erstellt, ' + r.orders.skipped   + ' übersprungen, ' + r.orders.errors.length  + ' Fehler\n';
                if (r.users)       text += 'Kund:innen:     ' + r.users.created    + ' erstellt, ' + r.users.updated    + ' aktualisiert, ' + r.users.errors.length   + ' Fehler\n';
                if (r.settings)    text += 'Einstellungen:  ' + r.settings.restored + ' wiederhergestellt\n';
                if (r.media_relink) text += 'Medien-Relink:  ' + r.media_relink.relinked + ' Produkte aktualisiert (' + r.media_relink.mapped + ' IDs gemappt)\n';
                status.textContent = text;
            }
        })
        .catch(() => { status.textContent = 'Netzwerkfehler.'; });
}

function migDryRun()    { migSendImport(true);  }
function migDoImport()  { migSendImport(false); }

</script>

<!-- pa-combo "Neu hinzufügen" popup overlay -->
<div id="pa-combo-overlay" style="display:none;" onclick="paComboCloseOverlay()">
    <div id="pa-combo-dialog" onclick="event.stopPropagation()">
        <h4 id="pa-combo-dialog-title">Neuer Eintrag</h4>
        <input type="text" id="pa-combo-dialog-input" placeholder=""
               onkeydown="if(event.key==='Enter')paComboConfirmAdd(); if(event.key==='Escape')paComboCloseOverlay();">
        <div class="pa-combo-dialog-btns">
            <button onclick="paComboConfirmAdd()">Hinzufügen</button>
            <button onclick="paComboCloseOverlay()">Abbrechen</button>
        </div>
    </div>
</div>

<?php get_footer(); ?>
