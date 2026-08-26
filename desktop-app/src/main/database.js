import Database from 'better-sqlite3'
import path from 'path'
import fs from 'fs'
import { app } from 'electron'
import { getCurrentAppVersion } from '../shared/versionRegistry.js'
import { canApplyField } from '../shared/blueprintFields.js'
import { composeSeoDescription } from '../shared/composeSeoDescription.js'

let db = null
let currentProjectFolder = null

const SCHEMA = `
CREATE TABLE IF NOT EXISTS settings (
  key TEXT PRIMARY KEY,
  value TEXT
);

CREATE TABLE IF NOT EXISTS products (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  wp_id INTEGER,
  slug TEXT,
  name TEXT NOT NULL DEFAULT '',
  gattung TEXT DEFAULT '',
  art TEXT DEFAULT '',
  kultivar TEXT DEFAULT '',
  common_name TEXT DEFAULT '',
  sku TEXT DEFAULT '',
  price REAL DEFAULT 0,
  regular_price REAL DEFAULT 0,
  sale_price REAL,
  product_type TEXT DEFAULT 'plant',
  unit_type TEXT DEFAULT 'piece',
  liter_content REAL,
  weight_content REAL,
  fertilizer_type TEXT DEFAULT '',
  composition TEXT DEFAULT '[]',
  sell_own_substrate INTEGER DEFAULT 0,
  substrate_display_text TEXT DEFAULT '',
  substrate_product_id INTEGER,
  substrate_note TEXT DEFAULT '',
  fertilizer_type_choice TEXT DEFAULT '',
  fertilizer_amount TEXT DEFAULT '',
  tax_class TEXT DEFAULT 'reduced-rate',
  differential_taxation INTEGER DEFAULT 0,
  shipping_class TEXT DEFAULT '',
  delivery_time TEXT DEFAULT '',
  stock INTEGER DEFAULT 0,
  low_stock_threshold INTEGER,
  never_low_stock INTEGER DEFAULT 0,
  show_exact_stock INTEGER DEFAULT 1,
  weight REAL,
  length REAL,
  width REAL,
  height REAL,
  shipping_length REAL,
  shipping_width REAL,
  shipping_height REAL,
  short_description TEXT DEFAULT '',
  description TEXT DEFAULT '',
  care_light TEXT DEFAULT '',
  care_light_tolerates_min TEXT DEFAULT '',
  care_light_tolerates_max TEXT DEFAULT '',
  care_water TEXT DEFAULT '',
  care_water_tolerates_min TEXT DEFAULT '',
  care_water_tolerates_max TEXT DEFAULT '',
  care_winter TEXT DEFAULT '',
  care_winterhaerte TEXT DEFAULT '',
  care_temp_min REAL,
  care_temp_max REAL,
  care_temp_ausgepflanzt_min REAL,
  care_temp_ausgepflanzt_max REAL,
  status TEXT DEFAULT 'draft',
  is_variable INTEGER DEFAULT 0,
  image_folder TEXT,
  seo_title TEXT,
  seo_description TEXT,
  seo_focus_keyword TEXT,
  app_version TEXT DEFAULT '1.3.0',
  gattung_id INTEGER,
  art_id INTEGER,
  blueprint_links TEXT DEFAULT '{}',
  tax_class_id INTEGER,
  created_at TEXT DEFAULT (datetime('now')),
  updated_at TEXT DEFAULT (datetime('now')),
  synced_at TEXT
);

CREATE TABLE IF NOT EXISTS product_images (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  product_id INTEGER NOT NULL,
  wp_id INTEGER,
  local_path TEXT,
  remote_url TEXT,
  filename TEXT,
  sort_order INTEGER DEFAULT 0,
  watermark INTEGER DEFAULT 0,
  avif_local_path TEXT,
  avif_wp_id INTEGER,
  avif_remote_url TEXT,
  thumb_avif_local_path TEXT,
  thumb_avif_wp_id INTEGER,
  thumb_avif_remote_url TEXT,
  FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS tag_categories (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  name TEXT NOT NULL,
  gattung_id INTEGER,
  art_id INTEGER,
  protected INTEGER DEFAULT 0,
  created_at TEXT DEFAULT (datetime('now'))
);

CREATE TABLE IF NOT EXISTS tags (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  wp_id INTEGER,
  name TEXT NOT NULL,
  slug TEXT,
  category_id INTEGER
);

CREATE TABLE IF NOT EXISTS product_tags (
  product_id INTEGER NOT NULL,
  tag_id INTEGER NOT NULL,
  PRIMARY KEY (product_id, tag_id),
  FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
  FOREIGN KEY (tag_id) REFERENCES tags(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS categories (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  parent_id INTEGER,
  name TEXT,
  heading TEXT DEFAULT '',
  description TEXT DEFAULT '',
  gattung_id INTEGER,
  art_id INTEGER,
  kultivar TEXT,
  sort_order INTEGER DEFAULT 0,
  expandable INTEGER DEFAULT 1,
  hidden INTEGER DEFAULT 0,
  protected INTEGER DEFAULT 0,
  system_type TEXT,
  mainpage_carousel INTEGER DEFAULT 0,
  carousel_sort_order INTEGER DEFAULT 0,
  wp_term_id INTEGER,
  created_at TEXT DEFAULT (datetime('now')),
  FOREIGN KEY (parent_id) REFERENCES categories(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS product_categories (
  product_id INTEGER NOT NULL,
  category_id INTEGER NOT NULL,
  PRIMARY KEY (product_id, category_id),
  FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
  FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS snapshots (
  product_id INTEGER PRIMARY KEY,
  data TEXT NOT NULL,
  snapped_at TEXT DEFAULT (datetime('now')),
  FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS variants (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  product_id INTEGER NOT NULL,
  wp_id INTEGER,
  name TEXT DEFAULT '',
  sku TEXT DEFAULT '',
  price REAL DEFAULT 0,
  regular_price REAL DEFAULT 0,
  sale_price REAL,
  stock INTEGER DEFAULT 0,
  low_stock_threshold INTEGER,
  never_low_stock INTEGER DEFAULT 0,
  show_exact_stock INTEGER DEFAULT 1,
  sort_order INTEGER DEFAULT 0,
  unit_type TEXT DEFAULT '',
  liter_content REAL,
  weight_content REAL,
  FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- 1:1-Spiegel von product_images, aber pro Variante statt pro Produkt (Feature
-- "Variable Produkte" — jede Variante hat ihre eigene Bildergalerie).
CREATE TABLE IF NOT EXISTS variant_images (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  variant_id INTEGER NOT NULL,
  wp_id INTEGER,
  local_path TEXT,
  remote_url TEXT,
  filename TEXT,
  sort_order INTEGER DEFAULT 0,
  watermark INTEGER DEFAULT 0,
  avif_local_path TEXT,
  avif_wp_id INTEGER,
  avif_remote_url TEXT,
  thumb_avif_local_path TEXT,
  thumb_avif_wp_id INTEGER,
  thumb_avif_remote_url TEXT,
  FOREIGN KEY (variant_id) REFERENCES variants(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS variant_specifications (
  variant_id INTEGER NOT NULL,
  specification_id INTEGER NOT NULL,
  PRIMARY KEY (variant_id, specification_id),
  FOREIGN KEY (variant_id) REFERENCES variants(id) ON DELETE CASCADE,
  FOREIGN KEY (specification_id) REFERENCES specifications(id) ON DELETE CASCADE
);

-- WooCommerce hat kein product_tag-Feld auf Variations-Ebene — diese Tags
-- bleiben lokal und werden beim Push zusätzlich in die Tag-Menge des
-- übergeordneten Produkts gemerged (siehe pushProductTags in ipc.js).
CREATE TABLE IF NOT EXISTS variant_tags (
  variant_id INTEGER NOT NULL,
  tag_id INTEGER NOT NULL,
  PRIMARY KEY (variant_id, tag_id),
  FOREIGN KEY (variant_id) REFERENCES variants(id) ON DELETE CASCADE,
  FOREIGN KEY (tag_id) REFERENCES tags(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS specifications (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  name TEXT NOT NULL,
  pot_size_cm REAL NOT NULL,
  shape TEXT DEFAULT 'round', -- 'round' or 'square'
  weight REAL NOT NULL,
  weight_unit TEXT DEFAULT 'g', -- 'g' or 'kg'
  height_cm REAL,
  width_cm REAL,
  spec_type TEXT DEFAULT 'plant', -- 'plant' (Topf-Preset) or 'package' (Substrat/Dünger, ohne Topfgröße)
  created_at TEXT DEFAULT (datetime('now'))
);

CREATE TABLE IF NOT EXISTS product_specifications (
  product_id INTEGER NOT NULL,
  specification_id INTEGER NOT NULL,
  PRIMARY KEY (product_id, specification_id),
  FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
  FOREIGN KEY (specification_id) REFERENCES specifications(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS gattungen (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  name TEXT NOT NULL,
  fields TEXT NOT NULL DEFAULT '{}',
  category_ids TEXT NOT NULL DEFAULT '[]',
  created_at TEXT DEFAULT (datetime('now')),
  updated_at TEXT DEFAULT (datetime('now'))
);

CREATE TABLE IF NOT EXISTS arten (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  gattung_id INTEGER NOT NULL,
  name TEXT NOT NULL,
  fields TEXT NOT NULL DEFAULT '{}',
  category_ids TEXT NOT NULL DEFAULT '[]',
  created_at TEXT DEFAULT (datetime('now')),
  updated_at TEXT DEFAULT (datetime('now')),
  FOREIGN KEY (gattung_id) REFERENCES gattungen(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS tax_classes (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  name TEXT NOT NULL,
  rate REAL NOT NULL,
  wc_slug TEXT DEFAULT '',
  wc_rate_id INTEGER,
  created_at TEXT DEFAULT (datetime('now')),
  updated_at TEXT DEFAULT (datetime('now'))
);

CREATE TABLE IF NOT EXISTS delivery_times (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  days_min INTEGER,
  days_max INTEGER,
  label TEXT NOT NULL,
  created_at TEXT DEFAULT (datetime('now'))
);
`

export function getProjectFolder() {
  return currentProjectFolder
}

export function getModifiedProducts() {
  return getDb().prepare(`
    SELECT * FROM products
    WHERE synced_at IS NULL OR updated_at > synced_at
    ORDER BY updated_at DESC
  `).all()
}

export function initDatabase(projectFolder) {
  let dbPath
  if (projectFolder && fs.existsSync(projectFolder)) {
    dbPath = path.join(projectFolder, 'plantaphilia.db')
    currentProjectFolder = projectFolder
  } else {
    currentProjectFolder = null
    const userData = app.getPath('userData')
    dbPath = path.join(userData, 'plantaphilia.db')
  }

  if (db) {
    try { db.close() } catch (_) {}
  }

  db = new Database(dbPath)
  db.pragma('journal_mode = WAL')
  db.pragma('foreign_keys = ON')
  db.exec(SCHEMA)
  migrateTagsToCategories(db)
  reconcileSchema(db)
  seedUncategorizedTagCategory(db)
  seedSystemCategories(db)
  reconcileCarouselSortOrder(db)

  backfillGattungArtLinks(db)
}

// Renumbers carousel_sort_order for every mainpage_carousel=1 row to 0..n-1,
// gap-free — cheap to run every startup, and fixes any rows that collided at
// the same order (e.g. the column default 0) before the saveCategory fix above.
function reconcileCarouselSortOrder(d) {
  const rows = d.prepare('SELECT id FROM categories WHERE mainpage_carousel = 1 ORDER BY carousel_sort_order, id').all()
  const update = d.prepare('UPDATE categories SET carousel_sort_order = ? WHERE id = ?')
  rows.forEach((r, i) => update.run(i, r.id))
}

// One-time, idempotent: guarantees a permanent, global "Unkategorisiert"
// bucket always exists so users aren't forced to invent a category name for
// casual tags. protected=1 blocks deletion (see deleteTagCategory).
function seedUncategorizedTagCategory(d) {
  const existing = d.prepare('SELECT id FROM tag_categories WHERE protected = 1').get()
  if (existing) return
  d.prepare("INSERT INTO tag_categories (name, gattung_id, art_id, protected) VALUES ('Unkategorisiert', NULL, NULL, 1)").run()
}

// Root-level, non-deletable categories whose membership is computed entirely
// on the website at render time (sale prices / bulk-sale campaigns / recent
// products) — the app only carries their name/order/expandable/hidden state.
function seedSystemCategories(d) {
  const existing = d.prepare("SELECT system_type FROM categories WHERE protected = 1").all().map(r => r.system_type)
  const systemCats = [
    { name: 'Reduziert', system_type: 'reduziert', expandable: 0, sort_order: 1000, mainpage_carousel: 0 },
    { name: 'Rabattaktionen', system_type: 'rabattaktionen', expandable: 1, sort_order: 1001, mainpage_carousel: 0 },
    { name: 'Neu', system_type: 'neu', expandable: 0, sort_order: 1002, mainpage_carousel: 0 },
    { name: 'Beliebt', system_type: 'beliebt', expandable: 0, sort_order: 1003, mainpage_carousel: 1 },
    { name: 'Alle Produkte', system_type: 'alle_produkte', expandable: 1, sort_order: 1004, mainpage_carousel: 0 },
  ]
  const insert = d.prepare(`
    INSERT INTO categories (name, system_type, protected, expandable, sort_order, mainpage_carousel)
    VALUES (?, ?, 1, ?, ?, ?)
  `)
  for (const cat of systemCats) {
    if (existing.includes(cat.system_type)) continue
    insert.run(cat.name, cat.system_type, cat.expandable, cat.sort_order, cat.mainpage_carousel)
  }
}

// One-time: tags used to be flat with a hardcoded 3-way group_type bucket
// (category/variant/care). Replaced with free-form Überkategorien (like
// Gattung/Art). Per explicit user decision, old tags and their product
// assignments are discarded rather than migrated — group_type carried no
// reusable category identity to map onto the new nested model. Gated on
// category_id's absence so this runs exactly once, not on every launch.
function migrateTagsToCategories(d) {
  const cols = d.prepare("PRAGMA table_info(tags)").all().map(c => c.name)
  if (cols.includes('category_id')) return
  d.exec('DELETE FROM product_tags')
  d.exec('DELETE FROM tags')
  d.exec('ALTER TABLE tags ADD COLUMN category_id INTEGER')
}

// Any column added to a table's CREATE TABLE text after that table already
// existed on disk never actually reaches it — CREATE TABLE IF NOT EXISTS is a
// no-op once the table is there, so a real, already-populated DB file (e.g.
// the developer's own project folder, upgraded across many versions) can be
// missing columns that were added years apart (app_version, care_temp_min,
// shipping_length, and now gattung_id/art_id/blueprint_links all landed this
// way at different times). Diffs every table in SCHEMA against PRAGMA
// table_info and ALTER TABLE ADD COLUMN's whatever is missing, so upgrading
// never throws "no such column" again — for any table, not just products.
// Splits "TYPE DEFAULT <expr>" into { base: "TYPE", defaultExpr }, correctly
// handling a parenthesized expr with nested parens (e.g. DEFAULT (datetime('now')))
// which a single non-recursive regex can't balance — needed because ALTER TABLE
// ADD COLUMN rejects non-constant defaults and the caller falls back to adding
// the column bare, then backfilling defaultExpr via UPDATE.
function splitColumnDefault(colDef) {
  const m = /DEFAULT\s*/i.exec(colDef)
  if (!m) return { base: colDef, defaultExpr: null }
  const start = m.index
  let i = start + m[0].length
  let defaultExpr
  if (colDef[i] === '(') {
    let depth = 0, j = i
    for (; j < colDef.length; j++) {
      if (colDef[j] === '(') depth++
      else if (colDef[j] === ')') { depth--; if (depth === 0) { j++; break } }
    }
    defaultExpr = colDef.slice(i, j); i = j
  } else if (colDef[i] === "'") {
    let j = i + 1
    while (j < colDef.length) {
      if (colDef[j] === "'" && colDef[j + 1] === "'") { j += 2; continue }
      if (colDef[j] === "'") { j++; break }
      j++
    }
    defaultExpr = colDef.slice(i, j); i = j
  } else {
    const rest = /^\S+/.exec(colDef.slice(i))
    defaultExpr = rest ? rest[0] : ''
    i += defaultExpr.length
  }
  const base = (colDef.slice(0, start) + colDef.slice(i)).replace(/\s+/g, ' ').trim()
  return { base, defaultExpr }
}

function reconcileSchema(d) {
  for (const [, table, body] of SCHEMA.matchAll(/CREATE TABLE IF NOT EXISTS (\w+) \(([\s\S]*?)\n\);/g)) {
    const existingCols = new Set(d.prepare(`PRAGMA table_info(${table})`).all().map(c => c.name))
    for (const rawLine of body.split('\n')) {
      const line = rawLine.replace(/--.*$/, '').trim().replace(/,$/, '')
      if (!line) continue
      const upper = line.toUpperCase()
      if (upper.startsWith('PRIMARY KEY') || upper.startsWith('FOREIGN KEY') || upper.startsWith('UNIQUE') || upper.startsWith('CHECK')) continue
      const match = line.match(/^(\w+)\s+(.+)$/)
      if (!match) continue
      const [, colName, colDef] = match
      if (existingCols.has(colName)) continue
      try {
        d.exec(`ALTER TABLE ${table} ADD COLUMN ${colName} ${colDef}`)
      } catch (e) {
        // SQLite disallows a non-constant default (e.g. DEFAULT (datetime('now')))
        // in ALTER TABLE ADD COLUMN — retry without the default, then backfill
        // existing rows with it once via UPDATE, instead of just giving up.
        const { base: strippedDef, defaultExpr } = splitColumnDefault(colDef)
        if (defaultExpr != null && strippedDef !== colDef) {
          try {
            d.exec(`ALTER TABLE ${table} ADD COLUMN ${colName} ${strippedDef}`)
            d.exec(`UPDATE ${table} SET ${colName} = ${defaultExpr} WHERE ${colName} IS NULL`)
          } catch (e2) {
            console.error(`[schema] Failed to add column ${table}.${colName}:`, e2.message)
          }
        } else {
          console.error(`[schema] Failed to add column ${table}.${colName}:`, e.message)
        }
      }
    }
  }
}

// Finds (or silently creates an empty, name-only) Gattung/Art blueprint
// matching this text pair. Used both for the one-time backfill below and for
// auto-linking products that arrive with gattung/art text but no id — e.g.
// products coming in from a WordPress pull, which has no concept of
// blueprints and only ever sends plain text.
function resolveGattungArtIds(d, gattungText, artText) {
  const gName = (gattungText || '').trim()
  if (!gName) return { gattungId: null, artId: null }

  const existingG = d.prepare('SELECT id FROM gattungen WHERE name = ?').get(gName)
  const gattungId = existingG ? existingG.id : d.prepare("INSERT INTO gattungen (name, fields) VALUES (?, '{}')").run(gName).lastInsertRowid

  let artId = null
  const aName = (artText || '').trim()
  if (aName) {
    const existingA = d.prepare('SELECT id FROM arten WHERE gattung_id = ? AND name = ?').get(gattungId, aName)
    artId = existingA ? existingA.id : d.prepare("INSERT INTO arten (gattung_id, name, fields) VALUES (?, ?, '{}')").run(gattungId, aName).lastInsertRowid
  }

  return { gattungId, artId }
}

// One-time, idempotent: existing products only ever had gattung/art as free
// text. Without this, every pre-1.2.1 product would show an empty Gattung/Art
// picker despite having real data. Creates empty (name-only) blueprint rows
// for distinct text values and links products to them by exact match — never
// touches product field values, only the id linkage.
function backfillGattungArtLinks(d) {
  const candidates = d.prepare(`
    SELECT id, gattung, art FROM products
    WHERE gattung_id IS NULL AND TRIM(COALESCE(gattung, '')) != ''
  `).all()
  for (const p of candidates) {
    const { gattungId, artId } = resolveGattungArtIds(d, p.gattung, p.art)
    d.prepare('UPDATE products SET gattung_id = ?, art_id = ? WHERE id = ?').run(gattungId, artId, p.id)
  }
}

export function getDb() {
  if (!db) throw new Error('Database not initialized')
  return db
}

export function getSetting(key) {
  if (!db) return null
  const row = db.prepare('SELECT value FROM settings WHERE key = ?').get(key)
  return row ? row.value : null
}

export function getAllSettings() {
  if (!db) return {}
  const rows = db.prepare('SELECT key, value FROM settings').all()
  return Object.fromEntries(rows.map(r => [r.key, r.value]))
}

export function setSetting(key, value) {
  const d = getDb()
  d.prepare('INSERT OR REPLACE INTO settings (key, value) VALUES (?, ?)').run(key, String(value ?? ''))
}

export function getProducts(search) {
  const d = getDb()
  if (search && search.trim()) {
    const q = `%${search.trim()}%`
    return d.prepare(`
      SELECT * FROM products
      WHERE name LIKE ? OR gattung LIKE ? OR art LIKE ? OR kultivar LIKE ? OR sku LIKE ?
      ORDER BY updated_at DESC
    `).all(q, q, q, q, q)
  }
  return d.prepare('SELECT * FROM products ORDER BY updated_at DESC').all()
}

export function getProduct(id) {
  return getDb().prepare('SELECT * FROM products WHERE id = ?').get(id)
}

export function getProductByWpId(wpId) {
  return getDb().prepare('SELECT * FROM products WHERE wp_id = ?').get(wpId)
}

export function saveProduct(product) {
  const d = getDb()
  const now = new Date().toISOString()
  const currentVersion = getCurrentAppVersion()

  // Callers that manage blueprint linkage themselves (ProductForm, the
  // cascade updater) always pass gattung_id as a real id or an explicit null.
  // A caller that has never heard of blueprints — chiefly wcProductToLocal,
  // used when pulling products from WordPress — simply omits the key. Treat
  // that as "please link this by name", so pulled products stay linked to
  // (or create) a matching blueprint instead of landing with no link at all.
  if (product.gattung_id === undefined) {
    const resolved = resolveGattungArtIds(d, product.gattung, product.art)
    product = { ...product, gattung_id: resolved.gattungId, art_id: resolved.artId }
  }

  // Nur ein Produkt pro Düngertyp — der Auto-Link von Pflanzen zu "dem"
  // Dünger dieses Typs (siehe ProductForm.jsx Pflegehinweise) setzt das voraus.
  if (product.product_type === 'fertilizer' && product.fertilizer_type) {
    const dupe = d.prepare('SELECT id FROM products WHERE product_type=? AND fertilizer_type=? AND id != ?')
      .get('fertilizer', product.fertilizer_type, product.id ?? 0)
    if (dupe) throw new Error('Für diesen Düngertyp existiert bereits ein Produkt.')
  }

  if (product.id) {
    d.prepare(`
      UPDATE products SET
        wp_id=?, slug=?, name=?, gattung=?, art=?, kultivar=?, common_name=?, sku=?,
        price=?, regular_price=?, sale_price=?, product_type=?, unit_type=?,
        liter_content=?, weight_content=?, fertilizer_type=?, composition=?,
        sell_own_substrate=?, substrate_display_text=?, substrate_product_id=?, substrate_note=?, fertilizer_type_choice=?, fertilizer_amount=?,
        tax_class=?, differential_taxation=?, shipping_class=?,
        delivery_time=?, stock=?, low_stock_threshold=?, never_low_stock=?, show_exact_stock=?,
        weight=?, length=?, width=?, height=?,
        shipping_length=?, shipping_width=?, shipping_height=?,
        short_description=?, description=?,
        care_light=?, care_light_tolerates_min=?, care_light_tolerates_max=?,
        care_water=?, care_water_tolerates_min=?, care_water_tolerates_max=?,
        care_winter=?,
        care_temp_min=?, care_temp_max=?,
        care_temp_ausgepflanzt_min=?, care_temp_ausgepflanzt_max=?, status=?, app_version=?, updated_at=?,
        gattung_id=?, art_id=?, blueprint_links=?, tax_class_id=?, is_variable=?,
        seo_title=?, seo_description=?, seo_focus_keyword=?
      WHERE id=?
    `).run(
      product.wp_id ?? null, product.slug ?? '', product.name ?? '',
      product.gattung ?? '', product.art ?? '', product.kultivar ?? '', product.common_name ?? '', product.sku ?? '',
      product.price ?? 0, product.regular_price ?? 0, product.sale_price ?? null,
      product.product_type ?? 'plant', product.unit_type ?? 'piece',
      product.liter_content ?? null, product.weight_content ?? null,
      product.fertilizer_type ?? '',
      typeof product.composition === 'string' ? product.composition : JSON.stringify(product.composition ?? []),
      product.sell_own_substrate ? 1 : 0, product.substrate_display_text ?? '', product.substrate_product_id ?? null, product.substrate_note ?? '',
      product.fertilizer_type_choice ?? '', product.fertilizer_amount ?? '',
      product.tax_class ?? 'reduced-rate',
      product.differential_taxation ? 1 : 0, product.shipping_class ?? '',
      product.delivery_time ?? '', product.stock ?? 0,
      product.low_stock_threshold ?? null, product.never_low_stock ? 1 : 0, product.show_exact_stock === false ? 0 : 1,
      product.weight ?? null, product.length ?? null, product.width ?? null, product.height ?? null,
      product.shipping_length ?? null, product.shipping_width ?? null, product.shipping_height ?? null,
      product.short_description ?? '', product.description ?? '',
      product.care_light ?? '', product.care_light_tolerates_min ?? '', product.care_light_tolerates_max ?? '',
      product.care_water ?? '', product.care_water_tolerates_min ?? '', product.care_water_tolerates_max ?? '',
      product.care_winter ?? '',
      product.care_temp_min ?? null, product.care_temp_max ?? null,
      product.care_temp_ausgepflanzt_min ?? null, product.care_temp_ausgepflanzt_max ?? null,
      product.status ?? 'draft', product.app_version ?? currentVersion, now,
      product.gattung_id ?? null, product.art_id ?? null,
      typeof product.blueprint_links === 'string' ? product.blueprint_links : JSON.stringify(product.blueprint_links ?? {}),
      product.tax_class_id ?? null, product.is_variable ? 1 : 0,
      product.seo_title ?? null, product.seo_description ?? null, product.seo_focus_keyword ?? null,
      product.id
    )
    return product.id
  } else {
    const info = d.prepare(`
      INSERT INTO products (
        wp_id, slug, name, gattung, art, kultivar, common_name, sku,
        price, regular_price, sale_price, product_type, unit_type,
        liter_content, weight_content, fertilizer_type, composition,
        sell_own_substrate, substrate_display_text, substrate_product_id, substrate_note, fertilizer_type_choice, fertilizer_amount,
        tax_class, differential_taxation, shipping_class,
        delivery_time, stock, low_stock_threshold, never_low_stock, show_exact_stock,
        weight, length, width, height,
        shipping_length, shipping_width, shipping_height,
        short_description, description,
        care_light, care_light_tolerates_min, care_light_tolerates_max,
        care_water, care_water_tolerates_min, care_water_tolerates_max,
        care_winter,
        care_temp_min, care_temp_max, care_temp_ausgepflanzt_min, care_temp_ausgepflanzt_max,
        status, app_version, created_at, updated_at,
        gattung_id, art_id, blueprint_links, tax_class_id, is_variable,
        seo_title, seo_description, seo_focus_keyword
      ) VALUES (
        ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?
      )
    `).run(
      product.wp_id ?? null, product.slug ?? '', product.name ?? '',
      product.gattung ?? '', product.art ?? '', product.kultivar ?? '', product.common_name ?? '', product.sku ?? '',
      product.price ?? 0, product.regular_price ?? 0, product.sale_price ?? null,
      product.product_type ?? 'plant', product.unit_type ?? 'piece',
      product.liter_content ?? null, product.weight_content ?? null,
      product.fertilizer_type ?? '',
      typeof product.composition === 'string' ? product.composition : JSON.stringify(product.composition ?? []),
      product.sell_own_substrate ? 1 : 0, product.substrate_display_text ?? '', product.substrate_product_id ?? null, product.substrate_note ?? '',
      product.fertilizer_type_choice ?? '', product.fertilizer_amount ?? '',
      product.tax_class ?? 'reduced-rate',
      product.differential_taxation ? 1 : 0, product.shipping_class ?? '',
      product.delivery_time ?? '', product.stock ?? 0,
      product.low_stock_threshold ?? null, product.never_low_stock ? 1 : 0, product.show_exact_stock === false ? 0 : 1,
      product.weight ?? null, product.length ?? null, product.width ?? null, product.height ?? null,
      product.shipping_length ?? null, product.shipping_width ?? null, product.shipping_height ?? null,
      product.short_description ?? '', product.description ?? '',
      product.care_light ?? '', product.care_light_tolerates_min ?? '', product.care_light_tolerates_max ?? '',
      product.care_water ?? '', product.care_water_tolerates_min ?? '', product.care_water_tolerates_max ?? '',
      product.care_winter ?? '',
      product.care_temp_min ?? null, product.care_temp_max ?? null,
      product.care_temp_ausgepflanzt_min ?? null, product.care_temp_ausgepflanzt_max ?? null,
      product.status ?? 'draft', product.app_version ?? currentVersion, now, now,
      product.gattung_id ?? null, product.art_id ?? null,
      typeof product.blueprint_links === 'string' ? product.blueprint_links : JSON.stringify(product.blueprint_links ?? {}),
      product.tax_class_id ?? null, product.is_variable ? 1 : 0,
      product.seo_title ?? null, product.seo_description ?? null, product.seo_focus_keyword ?? null
    )
    return info.lastInsertRowid
  }
}

export function deleteProduct(id) {
  getDb().prepare('DELETE FROM products WHERE id = ?').run(id)
}

export function updateProductSyncedAt(id) {
  const now = new Date().toISOString()
  getDb().prepare('UPDATE products SET synced_at = ? WHERE id = ?').run(now, id)
}

// Tracks the actual on-disk image folder name as of the last save — lets the
// renderer detect "the naming scheme/text changed since last time" without
// re-deriving (and potentially guessing wrong) from Gattung/Art/Kultivar text.
export function updateProductImageFolder(id, folderName) {
  getDb().prepare('UPDATE products SET image_folder = ? WHERE id = ?').run(folderName, id)
}

export function getProductImages(productId) {
  return getDb().prepare('SELECT * FROM product_images WHERE product_id = ? ORDER BY sort_order').all(productId)
}

export function saveImage(image) {
  const d = getDb()
  if (image.id) {
    d.prepare(`
      UPDATE product_images SET wp_id=?, local_path=?, remote_url=?, filename=?, sort_order=?, watermark=?,
        avif_local_path=?, avif_wp_id=?, avif_remote_url=?
      WHERE id=?
    `).run(
      image.wp_id ?? null, image.local_path ?? '', image.remote_url ?? '', image.filename ?? '', image.sort_order ?? 0, image.watermark ? 1 : 0,
      image.avif_local_path ?? null, image.avif_wp_id ?? null, image.avif_remote_url ?? null,
      image.id
    )
    return image.id
  } else {
    const info = d.prepare(`
      INSERT INTO product_images (
        product_id, wp_id, local_path, remote_url, filename, sort_order, watermark,
        avif_local_path, avif_wp_id, avif_remote_url
      )
      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    `).run(
      image.product_id, image.wp_id ?? null, image.local_path ?? '', image.remote_url ?? '', image.filename ?? '', image.sort_order ?? 0, image.watermark ? 1 : 0,
      image.avif_local_path ?? null, image.avif_wp_id ?? null, image.avif_remote_url ?? null
    )
    return info.lastInsertRowid
  }
}

export function deleteImage(id) {
  getDb().prepare('DELETE FROM product_images WHERE id = ?').run(id)
}

export function getTagCategories() {
  return getDb().prepare('SELECT * FROM tag_categories ORDER BY name').all()
}

// gattung_id/art_id scope a category to that Gattung (any Art) or that exact
// Art; both null means global (shown/available for every product).
export function saveTagCategory(cat) {
  const d = getDb()
  if (cat.id) {
    d.prepare('UPDATE tag_categories SET name=?, gattung_id=?, art_id=? WHERE id=?')
      .run(cat.name, cat.gattung_id ?? null, cat.art_id ?? null, cat.id)
    return cat.id
  }
  const info = d.prepare('INSERT INTO tag_categories (name, gattung_id, art_id) VALUES (?, ?, ?)')
    .run(cat.name, cat.gattung_id ?? null, cat.art_id ?? null)
  return info.lastInsertRowid
}

// No hard FK on tags.category_id (same reasoning as gattung_id/art_id/tax_class_id
// on products) — cleanup runs explicitly here instead.
export function deleteTagCategory(id) {
  const d = getDb()
  const row = d.prepare('SELECT protected FROM tag_categories WHERE id = ?').get(id)
  if (row && row.protected) throw new Error('Diese Überkategorie ist geschützt und kann nicht gelöscht werden.')
  const tagIds = d.prepare('SELECT id FROM tags WHERE category_id = ?').all(id).map(r => r.id)
  if (tagIds.length > 0) {
    const placeholders = tagIds.map(() => '?').join(',')
    d.prepare(`DELETE FROM product_tags WHERE tag_id IN (${placeholders})`).run(...tagIds)
    d.prepare('DELETE FROM tags WHERE category_id = ?').run(id)
  }
  d.prepare('DELETE FROM tag_categories WHERE id = ?').run(id)
}

// ── Kategorien-Baum ────────────────────────────────────────────────────────
// Frei benannte Knoten haben `name`; an Gattung/Art/Kultivar gebundene Knoten
// haben `name=NULL` und ihren Anzeigenamen implizit über die Bindung (der
// Aufrufer löst das per getGattung/getArt auf). Mitgliedschaft gebundener
// Knoten wird nicht gespeichert, sondern per getBoundCategoryProductIds()
// aus den Produktdaten abgeleitet.
export function getCategories() {
  return getDb().prepare('SELECT * FROM categories ORDER BY parent_id, sort_order, id').all()
}

// Records the WP term id a local category was last pushed as — the pull
// side matches terms back up by this, not by local id (which is meaningless
// across a fresh install / different machine's SQLite file).
export function setCategoryWpTermId(id, wpTermId) {
  getDb().prepare('UPDATE categories SET wp_term_id = ? WHERE id = ?').run(wpTermId, id)
}

// Pull-side counterpart to pushCategories() — reconciles the local
// categories table against what the website currently has, matched by
// wp_term_id. Rebuilds the tree on a fresh install / second machine; safe to
// re-run (idempotent upsert). Protected system categories are never pushed
// as real pa_category terms, so they never appear in `pulled` and this never
// touches/nests under them.
export function reconcileCategoriesFromPull(pulled, special) {
  const d = getDb()
  const wpTermIdToLocalId = {}

  // Pass 1: upsert each term by wp_term_id (parent unresolved so far — every
  // term needs a local id to exist before parent linking can happen).
  for (const cat of pulled) {
    const { gattungId, artId } = resolveGattungArtIds(d, cat.gattung || '', cat.art || '')
    const kultivar = cat.kultivar || null
    const name = cat.type === 'free' ? (cat.name || null) : null

    const existing = d.prepare('SELECT id FROM categories WHERE wp_term_id = ?').get(cat.wpTermId)
    if (existing) {
      d.prepare(`
        UPDATE categories SET name=?, heading=?, description=?, gattung_id=?, art_id=?, kultivar=?,
          expandable=?, hidden=?, sort_order=?, mainpage_carousel=?, carousel_sort_order=?
        WHERE id=?
      `).run(
        name, cat.heading ?? '', cat.description ?? '', gattungId, artId, kultivar, cat.expandable ? 1 : 0, cat.hidden ? 1 : 0,
        cat.sortOrder ?? 0, cat.mainpageCarousel ? 1 : 0, cat.carouselSortOrder ?? 0, existing.id
      )
      wpTermIdToLocalId[cat.wpTermId] = existing.id
    } else {
      const info = d.prepare(`
        INSERT INTO categories (parent_id, name, heading, description, gattung_id, art_id, kultivar, sort_order, expandable, hidden, mainpage_carousel, carousel_sort_order, wp_term_id)
        VALUES (NULL, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
      `).run(
        name, cat.heading ?? '', cat.description ?? '', gattungId, artId, kultivar, cat.sortOrder ?? 0, cat.expandable ? 1 : 0, cat.hidden ? 1 : 0,
        cat.mainpageCarousel ? 1 : 0, cat.carouselSortOrder ?? 0, cat.wpTermId
      )
      wpTermIdToLocalId[cat.wpTermId] = info.lastInsertRowid
    }
  }

  // Pass 2: parent linking, now that every pulled term has a local id.
  const updateParent = d.prepare('UPDATE categories SET parent_id = ? WHERE id = ?')
  for (const cat of pulled) {
    const localId = wpTermIdToLocalId[cat.wpTermId]
    const parentLocalId = cat.parentWpTermId ? (wpTermIdToLocalId[cat.parentWpTermId] ?? null) : null
    updateParent.run(parentLocalId, localId)
  }

  // Product membership — only for free-named categories (bound categories'
  // membership is derived from products.gattung_id/art_id/kultivar, never stored).
  for (const cat of pulled) {
    if (cat.type !== 'free') continue
    const localId = wpTermIdToLocalId[cat.wpTermId]
    for (const wpId of (cat.productWpIds || [])) {
      const product = d.prepare('SELECT id FROM products WHERE wp_id = ?').get(wpId)
      if (product) addProductToCategory(localId, product.id)
    }
  }

  // System-category settings (Reduziert/Rabattaktionen/Neu/Beliebt/Alle Produkte).
  if (special) {
    const updateSpecial = d.prepare(`
      UPDATE categories SET hidden=?, sort_order=?, mainpage_carousel=?, carousel_sort_order=?
      WHERE system_type = ?
    `)
    for (const [systemType, settings] of Object.entries(special)) {
      updateSpecial.run(
        settings.hidden ? 1 : 0, settings.sortOrder ?? 0,
        settings.mainpageCarousel ? 1 : 0, settings.carouselSortOrder ?? 0, systemType
      )
    }
  }
}

// System categories (protected=1) are always root nodes — nothing may ever
// be nested under one, neither via create-with-parent nor indent.
function assertParentNotProtected(d, parentId) {
  if (parentId == null) return
  const parent = d.prepare('SELECT protected FROM categories WHERE id = ?').get(parentId)
  if (parent && parent.protected) throw new Error('Systemkategorien können keine Unterkategorien haben.')
}

export function saveCategory(cat) {
  const d = getDb()
  assertParentNotProtected(d, cat.parent_id ?? null)
  if (cat.id) {
    // carousel_sort_order only gets assigned here on the transition into being
    // flagged — otherwise every checkbox-toggle-via-UPDATE left it at the column
    // default (0), so any two categories flagged this way collided at the same
    // order and up/down (which swaps carousel_sort_order between two rows)
    // silently no-op'd when both sides were 0.
    const already = d.prepare('SELECT mainpage_carousel, carousel_sort_order FROM categories WHERE id = ?').get(cat.id)
    const nowFlagged = cat.mainpage_carousel ? 1 : 0
    let carouselSortOrder = already ? already.carousel_sort_order : 0
    if (nowFlagged && !(already && already.mainpage_carousel)) {
      const maxOrder = d.prepare('SELECT COALESCE(MAX(carousel_sort_order), -1) AS m FROM categories WHERE mainpage_carousel = 1').get().m
      carouselSortOrder = maxOrder + 1
    }
    d.prepare(`
      UPDATE categories SET parent_id=?, name=?, heading=?, description=?, gattung_id=?, art_id=?, kultivar=?,
        expandable=?, hidden=?, mainpage_carousel=?, carousel_sort_order=?
      WHERE id=?
    `).run(
      cat.parent_id ?? null, cat.name ?? null, cat.heading ?? '', cat.description ?? '', cat.gattung_id ?? null, cat.art_id ?? null, cat.kultivar ?? null,
      cat.expandable === false ? 0 : 1, cat.hidden ? 1 : 0, nowFlagged, carouselSortOrder,
      cat.id
    )
    return cat.id
  }
  const maxOrder = d.prepare('SELECT COALESCE(MAX(sort_order), -1) AS m FROM categories WHERE parent_id IS ?').get(cat.parent_id ?? null).m
  const maxCarouselOrder = d.prepare('SELECT COALESCE(MAX(carousel_sort_order), -1) AS m FROM categories').get().m
  const info = d.prepare(`
    INSERT INTO categories (parent_id, name, heading, description, gattung_id, art_id, kultivar, sort_order, expandable, hidden, mainpage_carousel, carousel_sort_order)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
  `).run(
    cat.parent_id ?? null, cat.name ?? null, cat.heading ?? '', cat.description ?? '', cat.gattung_id ?? null, cat.art_id ?? null, cat.kultivar ?? null,
    maxOrder + 1, cat.expandable === false ? 0 : 1, cat.hidden ? 1 : 0, cat.mainpage_carousel ? 1 : 0, maxCarouselOrder + 1
  )
  return info.lastInsertRowid
}

// Flat reorder scoped to mainpage_carousel=1 rows only — these aren't tree
// siblings in the usual sense (they can live at any depth/parent), so they
// get their own independent ordering column instead of reusing sort_order.
export function moveCarouselCategory(id, direction) {
  const d = getDb()
  const rows = d.prepare('SELECT id, carousel_sort_order FROM categories WHERE mainpage_carousel = 1 ORDER BY carousel_sort_order, id').all()
  const idx = rows.findIndex(r => r.id === id)
  const swapIdx = direction === 'up' ? idx - 1 : idx + 1
  if (idx === -1 || swapIdx < 0 || swapIdx >= rows.length) return
  const other = rows[swapIdx]
  const mine = rows[idx].carousel_sort_order
  d.prepare('UPDATE categories SET carousel_sort_order=? WHERE id=?').run(other.carousel_sort_order, id)
  d.prepare('UPDATE categories SET carousel_sort_order=? WHERE id=?').run(mine, other.id)
}

// Kaskadiert per FK (ON DELETE CASCADE) auf Unterkategorien und
// product_categories-Zeilen — löscht also einen ganzen Teilbaum.
export function deleteCategory(id) {
  const d = getDb()
  const row = d.prepare('SELECT protected FROM categories WHERE id = ?').get(id)
  if (row && row.protected) throw new Error('Diese Kategorie ist geschützt und kann nicht gelöscht werden.')
  d.prepare('DELETE FROM categories WHERE id = ?').run(id)
}

// Outliner-Bewegungen: up/down tauscht sort_order mit dem Nachbar-Geschwister;
// outdent macht den Knoten zum Geschwister seines bisherigen Elternknotens;
// indent macht ihn zum letzten Kind seines direkten vorherigen Geschwisters
// (kein vorheriges Geschwister vorhanden → nichts zu tun).
export function moveCategory(id, direction) {
  const d = getDb()
  const cat = d.prepare('SELECT * FROM categories WHERE id = ?').get(id)
  if (!cat) return

  if (direction === 'up' || direction === 'down') {
    const siblings = d.prepare('SELECT id, sort_order FROM categories WHERE parent_id IS ? ORDER BY sort_order, id').all(cat.parent_id)
    const idx = siblings.findIndex(s => s.id === id)
    const swapIdx = direction === 'up' ? idx - 1 : idx + 1
    if (idx === -1 || swapIdx < 0 || swapIdx >= siblings.length) return
    const other = siblings[swapIdx]
    const mine = siblings[idx].sort_order
    d.prepare('UPDATE categories SET sort_order=? WHERE id=?').run(other.sort_order, id)
    d.prepare('UPDATE categories SET sort_order=? WHERE id=?').run(mine, other.id)
  } else if (direction === 'outdent') {
    if (cat.parent_id === null) return
    const parent = d.prepare('SELECT * FROM categories WHERE id = ?').get(cat.parent_id)
    if (!parent) return
    const maxOrder = d.prepare('SELECT COALESCE(MAX(sort_order), -1) AS m FROM categories WHERE parent_id IS ?').get(parent.parent_id).m
    d.prepare('UPDATE categories SET parent_id=?, sort_order=? WHERE id=?').run(parent.parent_id, maxOrder + 1, id)
  } else if (direction === 'indent') {
    const siblings = d.prepare('SELECT id FROM categories WHERE parent_id IS ? ORDER BY sort_order, id').all(cat.parent_id)
    const idx = siblings.findIndex(s => s.id === id)
    if (idx <= 0) return
    const newParentId = siblings[idx - 1].id
    assertParentNotProtected(d, newParentId)
    const maxOrder = d.prepare('SELECT COALESCE(MAX(sort_order), -1) AS m FROM categories WHERE parent_id = ?').get(newParentId).m
    d.prepare('UPDATE categories SET parent_id=?, sort_order=? WHERE id=?').run(newParentId, maxOrder + 1, id)
  }
}

export function getProductCategories(productId) {
  return getDb().prepare(`
    SELECT c.* FROM categories c
    JOIN product_categories pc ON pc.category_id = c.id
    WHERE pc.product_id = ?
  `).all(productId)
}

export function setProductCategories(productId, categoryIds) {
  const d = getDb()
  d.prepare('DELETE FROM product_categories WHERE product_id = ?').run(productId)
  const insert = d.prepare('INSERT OR IGNORE INTO product_categories (product_id, category_id) VALUES (?, ?)')
  for (const catId of (categoryIds || [])) insert.run(productId, catId)
}

// Reverse of getProductCategories — used when pushing a (frei benannte)
// Kategorie's product membership to the website.
export function getCategoryProductIds(categoryId) {
  return getDb().prepare('SELECT product_id FROM product_categories WHERE category_id = ?')
    .all(categoryId).map(r => r.product_id)
}

// Full product rows for the "+ Artikel hinzufügen"-Picker-Anzeige.
export function getCategoryProducts(categoryId) {
  return getDb().prepare(`
    SELECT p.* FROM products p
    JOIN product_categories pc ON pc.product_id = p.id
    WHERE pc.category_id = ?
    ORDER BY p.name
  `).all(categoryId)
}

// Kategorie-zentrierte Einzel-Zuordnung (statt setProductCategories, das die
// GESAMTE Kategorie-Liste eines Produkts ersetzt — hier würde das versehentlich
// andere Zuordnungen des Produkts überschreiben).
export function addProductToCategory(categoryId, productId) {
  getDb().prepare('INSERT OR IGNORE INTO product_categories (product_id, category_id) VALUES (?, ?)').run(productId, categoryId)
}

export function removeProductFromCategory(categoryId, productId) {
  getDb().prepare('DELETE FROM product_categories WHERE product_id = ? AND category_id = ?').run(productId, categoryId)
}

// Mitgliedschaft einer an Gattung/Art/Kultivar gebundenen Kategorie — abgeleitet,
// nicht gespeichert. Kultivar wird trimmed/case-insensitive verglichen (Freitext).
export function getBoundCategoryProductIds(cat) {
  const d = getDb()
  if (cat.kultivar) {
    return d.prepare('SELECT id FROM products WHERE art_id = ? AND lower(trim(kultivar)) = lower(trim(?))')
      .all(cat.art_id, cat.kultivar).map(r => r.id)
  }
  if (cat.art_id) {
    return d.prepare('SELECT id FROM products WHERE art_id = ?').all(cat.art_id).map(r => r.id)
  }
  if (cat.gattung_id) {
    return d.prepare('SELECT id FROM products WHERE gattung_id = ?').all(cat.gattung_id).map(r => r.id)
  }
  return []
}

// Produkte aller Gattungen/Arten, die diese Kategorie in ihrer category_ids-
// Liste verlinkt haben (BlueprintForm "Kategorien"-Feld) — zusätzlich zur
// normalen freien Zuordnung (product_categories); Duplikate werden vom
// Aufrufer dedupliziert (ein Produkt manuell UND per Gattung/Art derselben
// Kategorie zugeordnet ist redundant, nicht falsch).
export function getProductIdsForLinkedBlueprints(categoryId) {
  const d = getDb()
  const ids = new Set()
  for (const g of d.prepare('SELECT id, category_ids FROM gattungen').all()) {
    if (parseIds(g.category_ids).includes(categoryId)) {
      getProductIdsByBlueprint('gattung', g.id).forEach(id => ids.add(id))
    }
  }
  for (const a of d.prepare('SELECT id, category_ids FROM arten').all()) {
    if (parseIds(a.category_ids).includes(categoryId)) {
      getProductIdsByBlueprint('art', a.id).forEach(id => ids.add(id))
    }
  }
  return Array.from(ids)
}

// Alle Produkte einer Kategorie UND all ihrer Unterkategorien (rekursiv,
// beliebig tief), unter Berücksichtigung sowohl frei zugeordneter
// (product_categories) als auch an Gattung/Art/Kultivar gebundener
// Kategorien (abgeleitete Mitgliedschaft, siehe getBoundCategoryProductIds)
// — für den Klick auf eine (Über-)Kategorie in der App.
export function getCategoryProductsRecursive(categoryId) {
  const d = getDb()
  const all = d.prepare('SELECT * FROM categories').all()
  const byId = new Map(all.map(c => [c.id, c]))
  const root = byId.get(categoryId)
  if (!root) return []
  const subtree = []
  const stack = [root]
  while (stack.length) {
    const cat = stack.pop()
    subtree.push(cat)
    stack.push(...all.filter(c => c.parent_id === cat.id))
  }
  const productIds = new Set()
  for (const cat of subtree) {
    if (cat.gattung_id || cat.art_id || cat.kultivar) {
      getBoundCategoryProductIds(cat).forEach(id => productIds.add(id))
    } else {
      d.prepare('SELECT product_id FROM product_categories WHERE category_id = ?')
        .all(cat.id).forEach(r => productIds.add(r.product_id))
      getProductIdsForLinkedBlueprints(cat.id).forEach(id => productIds.add(id))
    }
  }
  if (!productIds.size) return []
  const placeholders = Array.from(productIds).map(() => '?').join(',')
  return d.prepare(`SELECT * FROM products WHERE id IN (${placeholders}) ORDER BY name`).all(...productIds)
}

export function getTags() {
  return getDb().prepare('SELECT * FROM tags ORDER BY name').all()
}

export function saveTag(tag) {
  const d = getDb()
  if (tag.id) {
    d.prepare('UPDATE tags SET wp_id=?, name=?, slug=?, category_id=? WHERE id=?')
      .run(tag.wp_id ?? null, tag.name, tag.slug ?? '', tag.category_id ?? null, tag.id)
    return tag.id
  } else {
    const info = d.prepare('INSERT INTO tags (wp_id, name, slug, category_id) VALUES (?, ?, ?, ?)')
      .run(tag.wp_id ?? null, tag.name, tag.slug ?? '', tag.category_id ?? null)
    return info.lastInsertRowid
  }
}

export function deleteTag(id) {
  const d = getDb()
  d.prepare('DELETE FROM product_tags WHERE tag_id = ?').run(id)
  d.prepare('DELETE FROM variant_tags WHERE tag_id = ?').run(id)
  d.prepare('DELETE FROM tags WHERE id = ?').run(id)
}

export function getProductTags(productId) {
  return getDb().prepare(`
    SELECT t.* FROM tags t
    JOIN product_tags pt ON pt.tag_id = t.id
    WHERE pt.product_id = ?
  `).all(productId)
}

export function setProductTags(productId, tagIds) {
  const d = getDb()
  d.prepare('DELETE FROM product_tags WHERE product_id = ?').run(productId)
  const insert = d.prepare('INSERT OR IGNORE INTO product_tags (product_id, tag_id) VALUES (?, ?)')
  for (const tagId of (tagIds || [])) {
    insert.run(productId, tagId)
  }
}

export function getVariantTags(variantId) {
  return getDb().prepare(`
    SELECT t.* FROM tags t
    JOIN variant_tags vt ON vt.tag_id = t.id
    WHERE vt.variant_id = ?
  `).all(variantId)
}

export function setVariantTags(variantId, tagIds) {
  const d = getDb()
  d.prepare('DELETE FROM variant_tags WHERE variant_id = ?').run(variantId)
  const insert = d.prepare('INSERT OR IGNORE INTO variant_tags (variant_id, tag_id) VALUES (?, ?)')
  for (const tagId of (tagIds || [])) {
    insert.run(variantId, tagId)
  }
}

export function getSnapshot(productId) {
  const row = getDb().prepare('SELECT data FROM snapshots WHERE product_id = ?').get(productId)
  return row ? JSON.parse(row.data) : null
}

export function saveSnapshot(productId, data) {
  const d = getDb()
  const json = JSON.stringify(data)
  const now = new Date().toISOString()
  d.prepare('INSERT OR REPLACE INTO snapshots (product_id, data, snapped_at) VALUES (?, ?, ?)').run(productId, json, now)
}

// Jede Variante kommt hydriert mit ihren eigenen Bildern + Spezifikation
// zurück — ein Aufruf statt drei Roundtrips vom Renderer aus.
export function getVariants(productId) {
  const d = getDb()
  const rows = d.prepare('SELECT * FROM variants WHERE product_id = ? ORDER BY sort_order').all(productId)
  const getImages = d.prepare('SELECT * FROM variant_images WHERE variant_id = ? ORDER BY sort_order')
  const getSpec = d.prepare('SELECT specification_id FROM variant_specifications WHERE variant_id = ?')
  const getTagIds = d.prepare('SELECT tag_id FROM variant_tags WHERE variant_id = ?')
  return rows.map(v => ({
    ...v,
    images: getImages.all(v.id),
    specification_id: getSpec.get(v.id)?.specification_id ?? null,
    tag_ids: getTagIds.all(v.id).map(r => r.tag_id),
  }))
}

// Delete-then-reinsert für den ganzen Varianten-Teilbaum (Variante + ihre
// Bilder + ihre Spezifikation-Verknüpfung) in einer Funktion — kaskadiert
// per FK, deshalb reicht das Löschen der variants-Zeilen.
// Bei variablen Produkten kommt die App (ProductForm.jsx) nie an die Varianten-
// Preise/-Bestände heran (die werden ausschließlich hier, per ProductList.jsx,
// gepflegt) — die auto-generierte Meta-Description für "ab X€, verfügbar" kann
// also nur hier neu berechnet werden, nicht im ProductForm-useEffect. Gleiche
// "nur überschreiben solange noch auto-generiert"-Regel wie dort, nur per
// DB-Vergleich statt React-Ref (überlebt den Wechsel zwischen den Editoren).
function recomputeVariableSeoDescription(d, productId, newVariants) {
  const product = d.prepare('SELECT * FROM products WHERE id = ?').get(productId)
  if (!product) return
  const oldVariants = d.prepare('SELECT price, regular_price, stock FROM variants WHERE product_id = ?').all(productId)
  const oldAuto = composeSeoDescription({ ...product, is_variable: true, variants: oldVariants })
  if (product.seo_description && product.seo_description !== oldAuto) return
  const newAuto = composeSeoDescription({ ...product, is_variable: true, variants: newVariants || [] })
  if (newAuto && newAuto !== product.seo_description) {
    d.prepare('UPDATE products SET seo_description = ? WHERE id = ?').run(newAuto, productId)
  }
}

export function saveVariants(productId, variants) {
  const d = getDb()
  recomputeVariableSeoDescription(d, productId, variants)
  d.prepare('DELETE FROM variants WHERE product_id = ?').run(productId)
  const insert = d.prepare(`
    INSERT INTO variants (product_id, wp_id, name, sku, price, regular_price, sale_price, stock, low_stock_threshold, never_low_stock, show_exact_stock, sort_order, unit_type, liter_content, weight_content)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
  `)
  const insertImage = d.prepare(`
    INSERT INTO variant_images (
      variant_id, wp_id, local_path, remote_url, filename, sort_order, watermark,
      avif_local_path, avif_wp_id, avif_remote_url
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
  `)
  const insertSpec = d.prepare('INSERT OR IGNORE INTO variant_specifications (variant_id, specification_id) VALUES (?, ?)')
  const insertTag = d.prepare('INSERT OR IGNORE INTO variant_tags (variant_id, tag_id) VALUES (?, ?)')
  for (let i = 0; i < (variants || []).length; i++) {
    const v = variants[i]
    const info = insert.run(
      productId, v.wp_id ?? null, v.name ?? '', v.sku ?? '', v.price ?? 0, v.regular_price ?? 0, v.sale_price ?? null, v.stock ?? 0,
      v.low_stock_threshold ?? null, v.never_low_stock ? 1 : 0, v.show_exact_stock === false ? 0 : 1, i,
      v.unit_type ?? '', v.liter_content ?? null, v.weight_content ?? null
    )
    const variantId = info.lastInsertRowid
    const images = v.images || []
    for (let j = 0; j < images.length; j++) {
      const img = images[j]
      insertImage.run(
        variantId, img.wp_id ?? null, img.local_path ?? '', img.remote_url ?? '', img.filename ?? '', j, img.watermark ? 1 : 0,
        img.avif_local_path ?? null, img.avif_wp_id ?? null, img.avif_remote_url ?? null
      )
    }
    if (v.specification_id) insertSpec.run(variantId, v.specification_id)
    for (const tagId of (v.tag_ids || [])) insertTag.run(variantId, tagId)
  }
}

export function getSpecifications() {
  return getDb().prepare('SELECT * FROM specifications ORDER BY name').all()
}

export function saveSpecification(spec) {
  const d = getDb()
  if (spec.id) {
    d.prepare(`
      UPDATE specifications SET
        name=?, pot_size_cm=?, shape=?, weight=?, weight_unit=?, height_cm=?, width_cm=?, spec_type=?
      WHERE id=?
    `).run(
      spec.name, spec.pot_size_cm, spec.shape, spec.weight, spec.weight_unit,
      spec.height_cm, spec.width_cm, spec.spec_type ?? 'plant', spec.id
    )
    return spec.id
  } else {
    const info = d.prepare(`
      INSERT INTO specifications (name, pot_size_cm, shape, weight, weight_unit, height_cm, width_cm, spec_type)
      VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    `).run(
      spec.name, spec.pot_size_cm, spec.shape, spec.weight, spec.weight_unit,
      spec.height_cm, spec.width_cm, spec.spec_type ?? 'plant'
    )
    return info.lastInsertRowid
  }
}

export function deleteSpecification(id) {
  getDb().prepare('DELETE FROM specifications WHERE id = ?').run(id)
}

export function getProductSpecifications(productId) {
  return getDb().prepare(`
    SELECT s.* FROM specifications s
    JOIN product_specifications ps ON ps.specification_id = s.id
    WHERE ps.product_id = ?
  `).all(productId)
}

export function setProductSpecifications(productId, specificationIds) {
  const d = getDb()
  d.prepare('DELETE FROM product_specifications WHERE product_id = ?').run(productId)
  const insert = d.prepare('INSERT OR IGNORE INTO product_specifications (product_id, specification_id) VALUES (?, ?)')
  for (const specId of (specificationIds || [])) {
    insert.run(productId, specId)
  }
}

function parseFields(json) {
  try { return JSON.parse(json || '{}') } catch { return {} }
}

function parseIds(json) {
  try { return JSON.parse(json || '[]') } catch { return [] }
}

export function getGattungen() {
  return getDb().prepare('SELECT * FROM gattungen ORDER BY name').all()
    .map(g => ({ ...g, fields: parseFields(g.fields), category_ids: parseIds(g.category_ids) }))
}

export function getGattung(id) {
  const g = getDb().prepare('SELECT * FROM gattungen WHERE id = ?').get(id)
  return g ? { ...g, fields: parseFields(g.fields), category_ids: parseIds(g.category_ids) } : null
}

export function saveGattung(gattung) {
  const d = getDb()
  const now = new Date().toISOString()
  const fieldsJson = JSON.stringify(gattung.fields ?? {})
  const categoryIdsJson = JSON.stringify(gattung.category_ids ?? [])
  let id = gattung.id
  if (id) {
    d.prepare('UPDATE gattungen SET name=?, fields=?, category_ids=?, updated_at=? WHERE id=?').run(gattung.name, fieldsJson, categoryIdsJson, now, id)
  } else {
    const info = d.prepare('INSERT INTO gattungen (name, fields, category_ids) VALUES (?, ?, ?)').run(gattung.name, fieldsJson, categoryIdsJson)
    id = info.lastInsertRowid
  }
  applyBlueprintToLinkedProducts('gattung', id, gattung.fields ?? {})
  return id
}

export function deleteGattung(id) {
  const d = getDb()
  const artenIds = d.prepare('SELECT id FROM arten WHERE gattung_id = ?').all(id).map(r => r.id)
  const affectedByGattung = d.prepare('SELECT * FROM products WHERE gattung_id = ?').all(id)
  for (const p of affectedByGattung) unlinkBlueprintFromProduct(p, 'gattung')
  if (artenIds.length > 0) {
    const placeholders = artenIds.map(() => '?').join(',')
    const affectedByArt = d.prepare(`SELECT * FROM products WHERE art_id IN (${placeholders})`).all(...artenIds)
    for (const p of affectedByArt) unlinkBlueprintFromProduct(p, 'art')
  }
  // Tag categories scoped to this Gattung (or any Art under it) lose their
  // scope entirely rather than being deleted — their tags and any existing
  // product assignments stay intact, just no longer Gattung/Art-specific.
  d.prepare('UPDATE tag_categories SET gattung_id = NULL, art_id = NULL WHERE gattung_id = ?').run(id)
  d.prepare('DELETE FROM gattungen WHERE id = ?').run(id) // cascades arten via FK
}

export function getArten(gattungId) {
  const d = getDb()
  const rows = gattungId
    ? d.prepare('SELECT * FROM arten WHERE gattung_id = ? ORDER BY name').all(gattungId)
    : d.prepare('SELECT * FROM arten ORDER BY name').all()
  return rows.map(a => ({ ...a, fields: parseFields(a.fields), category_ids: parseIds(a.category_ids) }))
}

export function getArt(id) {
  const a = getDb().prepare('SELECT * FROM arten WHERE id = ?').get(id)
  return a ? { ...a, fields: parseFields(a.fields), category_ids: parseIds(a.category_ids) } : null
}

// Kultivar hat keinen eigenen Blueprint-Katalog (nur Freitext auf Produkten) —
// für die Kultivar-Bindung einer Kategorie schlagen wir die tatsächlich schon
// verwendeten Werte unter der gewählten Art vor.
// Products currently linked to a Gattung/Art — used by the renderer's
// "allen Produkten zuordnen" bulk-apply actions (Tags, Kategorien). Not a
// live-inheritance mechanism, just a one-time snapshot of who's linked now.
export function getProductIdsByBlueprint(type, id) {
  const d = getDb()
  return type === 'art'
    ? d.prepare('SELECT id FROM products WHERE art_id = ?').all(id).map(r => r.id)
    : d.prepare('SELECT id FROM products WHERE gattung_id = ?').all(id).map(r => r.id)
}

export function getKultivarsForArt(artId) {
  return getDb().prepare(`
    SELECT DISTINCT kultivar FROM products
    WHERE art_id = ? AND kultivar IS NOT NULL AND trim(kultivar) != ''
    ORDER BY kultivar
  `).all(artId).map(r => r.kultivar)
}

export function saveArt(art) {
  const d = getDb()
  const now = new Date().toISOString()
  const fieldsJson = JSON.stringify(art.fields ?? {})
  const categoryIdsJson = JSON.stringify(art.category_ids ?? [])
  let id = art.id
  if (id) {
    d.prepare('UPDATE arten SET gattung_id=?, name=?, fields=?, category_ids=?, updated_at=? WHERE id=?')
      .run(art.gattung_id, art.name, fieldsJson, categoryIdsJson, now, id)
  } else {
    const info = d.prepare('INSERT INTO arten (gattung_id, name, fields, category_ids) VALUES (?, ?, ?, ?)').run(art.gattung_id, art.name, fieldsJson, categoryIdsJson)
    id = info.lastInsertRowid
  }
  applyBlueprintToLinkedProducts('art', id, art.fields ?? {})
  return id
}

export function deleteArt(id) {
  const d = getDb()
  const affected = d.prepare('SELECT * FROM products WHERE art_id = ?').all(id)
  for (const p of affected) unlinkBlueprintFromProduct(p, 'art')
  // Art-scoped categories fall back to their Gattung's scope (still a valid,
  // useful grouping), not all the way to global.
  d.prepare('UPDATE tag_categories SET art_id = NULL WHERE art_id = ?').run(id)
  d.prepare('DELETE FROM arten WHERE id = ?').run(id)
}

// Removes every `sourceType`-owned key from a product's blueprint_links and
// nulls the matching FK — used when a blueprint is deleted. Product field
// values are left exactly as they were, just no longer tracked as linked.
function unlinkBlueprintFromProduct(product, sourceType) {
  const d = getDb()
  const fkCol = sourceType === 'gattung' ? 'gattung_id' : 'art_id'
  const links = parseFields(product.blueprint_links)
  for (const key of Object.keys(links)) {
    if (links[key] === sourceType) delete links[key]
  }
  d.prepare(`UPDATE products SET ${fkCol} = NULL, blueprint_links = ? WHERE id = ?`)
    .run(JSON.stringify(links), product.id)
}

// Pushes a blueprint's current field values into every product still linked
// to it, but only for fields that product hasn't overridden ('manual') or
// isn't currently owned by the higher-precedence source ('art' beats
// 'gattung'). Always re-loads the full product row before writing — saveProduct
// is a full-row UPDATE, so patching without the rest of the row would wipe it.
function applyBlueprintToLinkedProducts(sourceType, sourceId, fields) {
  const d = getDb()
  const fkCol = sourceType === 'gattung' ? 'gattung_id' : 'art_id'
  const linked = d.prepare(`SELECT * FROM products WHERE ${fkCol} = ?`).all(sourceId)

  for (const row of linked) {
    const links = parseFields(row.blueprint_links)
    const patch = {}
    let specToApply

    for (const [key, value] of Object.entries(fields)) {
      if (!canApplyField(links[key], sourceType)) continue
      if (key === 'specification_id') { specToApply = value; links[key] = sourceType; continue }
      patch[key] = value
      links[key] = sourceType
    }

    // Fields this blueprint no longer defines: drop the link (not 'manual')
    // so a later re-fill on the blueprint can flow again; leave the product's
    // last-known value in place.
    for (const key of Object.keys(links)) {
      if (links[key] === sourceType && !(key in fields)) delete links[key]
    }

    if (Object.keys(patch).length > 0) {
      const full = getProduct(row.id)
      saveProduct({ ...full, ...patch, blueprint_links: JSON.stringify(links) })
    } else {
      d.prepare('UPDATE products SET blueprint_links = ? WHERE id = ?').run(JSON.stringify(links), row.id)
    }

    if (specToApply !== undefined) {
      setProductSpecifications(row.id, specToApply ? [specToApply] : [])
    }
  }
}

export function getTaxClasses() {
  return getDb().prepare('SELECT * FROM tax_classes ORDER BY name').all()
}

export function getTaxClass(id) {
  return getDb().prepare('SELECT * FROM tax_classes WHERE id = ?').get(id)
}

// Purely local bookkeeping — wc_slug/wc_rate_id start out null/empty and are
// filled in by ipc.js after a successful WooCommerce sync. Editing later just
// upserts whatever the caller passes, so ipc.js can call this twice: once to
// persist name/rate immediately, once more after sync to record the slug/id.
export function saveTaxClass(t) {
  const d = getDb()
  const now = new Date().toISOString()
  if (t.id) {
    d.prepare('UPDATE tax_classes SET name=?, rate=?, wc_slug=?, wc_rate_id=?, updated_at=? WHERE id=?')
      .run(t.name, t.rate, t.wc_slug ?? '', t.wc_rate_id ?? null, now, t.id)
    return t.id
  }
  const info = d.prepare('INSERT INTO tax_classes (name, rate, wc_slug, wc_rate_id) VALUES (?, ?, ?, ?)')
    .run(t.name, t.rate, t.wc_slug ?? '', t.wc_rate_id ?? null)
  return info.lastInsertRowid
}

export function deleteTaxClass(id) {
  getDb().prepare('DELETE FROM tax_classes WHERE id = ?').run(id)
}

export function getDeliveryTimes() {
  return getDb().prepare('SELECT * FROM delivery_times ORDER BY days_min, days_max').all()
}

export function getDeliveryTime(id) {
  return getDb().prepare('SELECT * FROM delivery_times WHERE id = ?').get(id)
}

export function saveDeliveryTime(t) {
  const d = getDb()
  if (t.id) {
    d.prepare('UPDATE delivery_times SET days_min=?, days_max=?, label=? WHERE id=?')
      .run(t.days_min ?? null, t.days_max ?? null, t.label, t.id)
    return t.id
  }
  const info = d.prepare('INSERT INTO delivery_times (days_min, days_max, label) VALUES (?, ?, ?)')
    .run(t.days_min ?? null, t.days_max ?? null, t.label)
  return info.lastInsertRowid
}

export function deleteDeliveryTime(id) {
  getDb().prepare('DELETE FROM delivery_times WHERE id = ?').run(id)
}
