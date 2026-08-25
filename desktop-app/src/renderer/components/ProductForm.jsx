import React, { useState, useEffect, useRef, useContext } from 'react'
import ComboSelect from './ComboSelect.jsx'
import RichTextEditor from './RichTextEditor.jsx'
import ImageUpload from './ImageUpload.jsx'
import SpecificationDialog from './SpecificationDialog.jsx'
import TagPool from './TagPool.jsx'
import CategoryAssignPicker from './CategoryAssignPicker.jsx'
import Tooltip from './Tooltip.jsx'
import BlueprintForm from './BlueprintForm.jsx'
import TaxClassPicker from './TaxClassPicker.jsx'
import DeliveryTimePicker from './DeliveryTimePicker.jsx'
import { Section, Row, Field, Toggle } from './FormPrimitives.jsx'
import { ToastContext, PreviewContext } from '../App.jsx'
import { TAX_CLASSES, CARE_LIGHT_OPTIONS, CARE_WATER_OPTIONS, CARE_FERTILIZER_OPTIONS, FERTILIZER_TYPES, DELIVERY_TIMES, PRODUCT_STATUSES } from '../../shared/constants.js'
import { canApplyField } from '../../shared/blueprintFields.js'
import { composeProductName, composeSeoDescription } from '../../shared/composeSeoDescription.js'
import { useAutoSave } from '../hooks/useAutoSave.js'
import { copyImagesToTemp, finalizeImages } from '../utils/imageLifecycle.js'

const EMPTY = {
  name: '', gattung: '', art: '', kultivar: '', common_name: '', sku: '',
  gattung_id: null, art_id: null, blueprint_links: {},
  price: '', regular_price: '',
  product_type: 'plant', unit_type: 'piece', liter_content: '', weight_content: '',
  fertilizer_type: '', composition: [], sell_own_substrate: false,
  substrate_product_id: null, fertilizer_type_choice: '', fertilizer_amount: '',
  tax_class: 'reduced-rate', tax_class_id: null, differential_taxation: false,
  delivery_time: '',
  stock: '', low_stock_threshold: '', never_low_stock: false, show_exact_stock: true,
  short_description: '', description: '',
  care_light: '', care_light_tolerates_min: '', care_light_tolerates_max: '',
  care_water: '', care_water_tolerates_min: '', care_water_tolerates_max: '',
  care_winter: '',
  care_temp_min: '', care_temp_max: '',
  care_temp_ausgepflanzt_min: '', care_temp_ausgepflanzt_max: '',
  status: 'draft', is_variable: false,
  slug: '', seo_title: '', seo_description: '', seo_focus_keyword: '',
}

// Deutsche Umlaute korrekt transliterieren statt sie stumpf wegzufiltern
// (WordPress' eigene sanitize_title() macht das nicht sauber — ä würde zu
// "a" statt "ae"). Nur zur ERSTBEFÜLLUNG gedacht (siehe save(), slug wird
// nie überschrieben sobald einmal gesetzt — sonst bricht das die URL).
function slugify(text) {
  return String(text || '')
    .toLowerCase()
    .replace(/ä/g, 'ae').replace(/ö/g, 'oe').replace(/ü/g, 'ue').replace(/ß/g, 'ss')
    .normalize('NFKD').replace(/\p{Diacritic}/gu, '')
    .replace(/'/g, '')
    .replace(/[^a-z0-9]+/g, '-')
    .replace(/^-+|-+$/g, '')
}

// SEO-Titel/-Beschreibung: Vorschlag aus vorhandenen Produktdaten, nur
// solange das Feld noch leer ist (siehe die beiden useEffects unten) — sobald
// der Nutzer etwas einträgt, wird nie wieder automatisch überschrieben.
const isRecipeType = (productType) => productType === 'substrate' || productType === 'fertilizer'

function composeSeoTitle(data) {
  const productName = isRecipeType(data.product_type) ? data.name : composeProductName(data.gattung, data.art, data.kultivar)
  if (!productName?.trim()) return ''
  return `${productName} kaufen | Plantaphilia`
}

// Fokus-Keyword: botanischer Name (Gattung + Art, ohne Kultivar) + "kaufen"
function composeSeoFocusKeyword(data) {
  if (isRecipeType(data.product_type)) return ''
  const botanical = [data.gattung, data.art].filter(Boolean).join(' ').trim()
  return botanical ? `${botanical} kaufen` : ''
}

// Bildordner-Name: gattung_art_kultivar (nur die vorhandenen Teile), bzw. der freie
// Name bei Substrat-Produkten. Reiner Text-Slug ohne ID — wird zusätzlich zur
// tatsächlichen Migrations-Erkennung gebraucht (siehe generateFolderName).
function folderNameFor(data) {
  const isRecipe = isRecipeType(data.product_type)
  const gattung = isRecipe ? (data.name || 'unbekannt') : (data.gattung || 'unbekannt')
  const art = isRecipe ? '' : (data.art || '')
  const kultivar = isRecipe ? '' : (data.kultivar || '')
  const parts = [gattung]
  if (art) parts.push(art)
  if (kultivar) parts.push(kultivar)
  return parts.join('_').toLowerCase().replace(/\s+/g, '-').replace(/[^a-z0-9_-]/g, '')
}

export default function ProductForm({ productId, onClose, onSaved }) {
  const [form, setForm] = useState(EMPTY)
  const [gattungen, setGattungen] = useState([])
  const [arten, setArten] = useState([])
  const [selectedSpecId, setSelectedSpecId] = useState(null)
  const [saving, setSaving] = useState(false)
  const descRef = useRef(null)
  // Tracks the value each SEO field's auto-suggest effect last set — as long as
  // the field still equals its own last auto-suggestion, the effect keeps it in
  // sync with Gattung/Art/Kultivar; the moment the user types something else,
  // the field diverges from the ref and the effect never touches it again.
  const autoSeoTitleRef = useRef('')
  const autoSeoDescRef = useRef('')
  const autoSeoFocusRef = useRef('')
  // Remembers the exact field value the user was last warned about being too
  // long for Google — clicking Erstellen/Speichern again on that SAME value
  // ignores the warning and saves anyway; editing the field (even if still
  // too long) makes it diverge from this ref, so the warning fires once more.
  const warnedSeoTitleRef = useRef(null)
  const warnedSeoDescRef = useRef(null)
  const addToast = useContext(ToastContext)
  const openPreview = useContext(PreviewContext)
  const [autosaveMs, setAutosaveMs] = useState(60000)
  const [images, setImages] = useState([]) // temp-staged images for this editing session
  const originalImagesRef = useRef([]) // {id, filename} of images present when editing started, to detect removals/renames on save
  const originalFolderRef = useRef(null) // image folder name at load time, to detect Gattung/Art/Kultivar renames
  const [showSpecDialog, setShowSpecDialog] = useState(false)
  const [availableSpecs, setAvailableSpecs] = useState([])
  const [allProducts, setAllProducts] = useState([]) // für Substrat-Dropdown + Dünger-Auto-Link in "Pflegehinweise"
  const [selectedTagIds, setSelectedTagIds] = useState([])
  const [selectedCategoryIds, setSelectedCategoryIds] = useState([])
  const [blueprintDialog, setBlueprintDialog] = useState(null) // { type, id, parentGattungId }
  // Tracks the id of the row this session is actually writing to. Starts as productId,
  // but a new product only gets one once the first save (autosave or submit) creates it —
  // every later save must reuse that id or it inserts a fresh blank row each time.
  const currentIdRef = useRef(productId)
  useEffect(() => { currentIdRef.current = productId }, [productId])

  // form.id only exists once an already-persisted product was loaded — a
  // brand-new product's id only becomes known from save()'s return value,
  // ID suffix guarantees two products with identical Gattung/Art/Kultivar
  // never collide on the same disk folder — form.id isn't reliably set for a
  // brand-new product (only save()'s return value is), so callers must pass
  // the id explicitly rather than relying on form.id.
  const generateFolderName = (id) => `${folderNameFor(form)}_${id ?? form.id}`

  useEffect(() => {
    window.api.getSettings().then(s => {
      const interval = parseInt(s.autosave_interval || '60', 10) * 1000
      setAutosaveMs(interval)
    })
  }, [])

  useEffect(() => {
    window.api.getProducts().then(l => setAllProducts(l || []))
  }, [])

  useEffect(() => {
    async function load() {
      if (productId) {
        const p = await window.api.getProduct(productId)
        if (p) {
          let blueprintLinks = {}
          try { blueprintLinks = p.blueprint_links ? JSON.parse(p.blueprint_links) : {} } catch { blueprintLinks = {} }
          setForm({
            ...EMPTY, ...p,
            price: p.price ?? '',
            regular_price: p.regular_price ?? '',
            stock: p.stock ?? '',
            low_stock_threshold: p.low_stock_threshold ?? '',
            care_temp_min: p.care_temp_min ?? '',
            care_temp_max: p.care_temp_max ?? '',
            care_temp_ausgepflanzt_min: p.care_temp_ausgepflanzt_min ?? '',
            care_temp_ausgepflanzt_max: p.care_temp_ausgepflanzt_max ?? '',
            liter_content: p.liter_content ?? '',
            weight_content: p.weight_content ?? '',
            slug: p.slug ?? '',
            seo_title: p.seo_title ?? '',
            seo_description: p.seo_description ?? '',
            seo_focus_keyword: p.seo_focus_keyword ?? '',
            differential_taxation: !!p.differential_taxation,
            never_low_stock: !!p.never_low_stock,
            show_exact_stock: !!p.show_exact_stock,
            is_variable: !!p.is_variable,
            sell_own_substrate: !!p.sell_own_substrate,
            composition: (() => { try { return p.composition ? JSON.parse(p.composition) : [] } catch { return [] } })(),
            blueprint_links: blueprintLinks,
          })
          autoSeoTitleRef.current = p.seo_title ?? ''
          autoSeoDescRef.current = p.seo_description ?? ''
          autoSeoFocusRef.current = p.seo_focus_keyword ?? ''
          // Load specification
          const specs = await window.api.getProductSpecifications(productId)
          setSelectedSpecId(specs.length > 0 ? specs[0].id : null)
          
          if (p.description && descRef.current) {
            setTimeout(() => descRef.current?.setValue(p.description), 50)
          }

          // Copy existing images into temp for editing — originals stay untouched in
          // their GAK folder until the edit is actually saved (so cancelling loses nothing).
          const dbImages = await window.api.getProductImages(productId)
          // p.image_folder is the actual on-disk folder name as of the last save —
          // authoritative once set. Falls back to the plain text slug (no id
          // suffix) for products saved before that column existed, so the very
          // next save correctly detects a "rename" onto the new id-suffixed
          // scheme instead of silently drifting out of sync with the real folder.
          originalFolderRef.current = p.image_folder || folderNameFor(p)
          if (dbImages && dbImages.length > 0) {
            originalImagesRef.current = dbImages.map(img => ({ id: img.id, filename: img.filename }))
            const staged = await copyImagesToTemp(dbImages, 'temp')
            setImages(staged)
          }
        }
      }
      // Load Gattung blueprints; Arten are loaded per-selected-Gattung by a
      // separate effect below (they're nested under exactly one Gattung).
      setGattungen(await window.api.getGattungen())

      // Load available specifications
      const allSpecs = await window.api.getSpecifications()
      setAvailableSpecs(allSpecs || [])
    }
    load()
  }, [productId])

  // Art is nested under exactly one Gattung — refetch its options whenever the
  // selected Gattung changes (including to none, which empties the list).
  useEffect(() => {
    if (form.gattung_id) {
      window.api.getArten(form.gattung_id).then(list => setArten(list || []))
    } else {
      setArten([])
    }
  }, [form.gattung_id])

  const set = (key, val) => setForm(f => ({ ...f, [key]: val }))

  // Blueprint-eligible fields go through this instead of `set`: any direct user
  // edit permanently unlinks the field from whatever Gattung/Art it was
  // following, so a later blueprint edit never silently overwrites it again.
  const setField = (key, val) => {
    setForm(f => ({ ...f, [key]: val, blueprint_links: { ...f.blueprint_links, [key]: 'manual' } }))
  }

  const linkHint = (key) => {
    const owner = form.blueprint_links?.[key]
    if (owner === 'gattung') return '· folgt Gattung'
    if (owner === 'art') return '· folgt Art'
    return undefined
  }

  const handleSpecChange = (val) => {
    const specId = val ? parseInt(val, 10) : null
    setSelectedSpecId(specId)
    setForm(f => ({ ...f, blueprint_links: { ...f.blueprint_links, specification_id: 'manual' } }))
  }

  // Steuerklasse needs both the local catalog id (for TaxClassPicker's own
  // selection) and the resolved WC slug (the actual value that gets synced,
  // stored in `tax_class` like before) updated together. Both link entries
  // must be marked 'manual' together too — otherwise a later blueprint
  // refresh could reapply a stale tax_class_id whose text no longer matches
  // what the user just picked here, desyncing the picker from the WC slug.
  const handleTaxClassChange = (id, wcSlug) => {
    setForm(f => ({
      ...f, tax_class_id: id, tax_class: wcSlug,
      blueprint_links: { ...f.blueprint_links, tax_class: 'manual', tax_class_id: 'manual' }
    }))
  }

  const isBlank = (v) => v === '' || v === null || v === undefined || v === false

  // Drops every `sourceType`-owned link. A field left with a real value gets
  // marked 'manual' instead of just dropped, so it can't be silently
  // overwritten by picking a *different* Gattung/Art afterwards — only an
  // untouched/blank field is safe to leave free for a future blueprint to fill.
  const dropOrProtectLinksFor = (formState, sourceType, specId) => {
    const links = { ...formState.blueprint_links }
    for (const key of Object.keys(links)) {
      if (links[key] !== sourceType) continue
      const currentValue = key === 'specification_id' ? specId : formState[key]
      if (isBlank(currentValue)) delete links[key]
      else links[key] = 'manual'
    }
    return { ...formState, blueprint_links: links }
  }

  // Applies a blueprint's filled-in fields to form state, honoring the same
  // precedence rule the server-side cascade uses (canApplyField): never
  // touches a manually-overridden field, and Gattung never overwrites a field
  // Art already owns. specification_id isn't part of `form` (it's tracked via
  // selectedSpecId) so it's reported back separately instead of patched in.
  const applyBlueprintFieldsToForm = (formState, sourceType, fields) => {
    const links = { ...formState.blueprint_links }
    const patch = {}
    let specApplied
    for (const [key, value] of Object.entries(fields || {})) {
      if (!canApplyField(links[key], sourceType)) continue
      if (key === 'specification_id') { specApplied = value; links[key] = sourceType; continue }
      patch[key] = value
      links[key] = sourceType
    }
    return { form: { ...formState, ...patch, blueprint_links: links }, specApplied }
  }

  const applyGattung = (g) => {
    setForm(f => {
      let next = dropOrProtectLinksFor(f, 'art', selectedSpecId)
      next = { ...next, art_id: null, art: '', gattung_id: g.id, gattung: g.name }
      const { form: applied, specApplied } = applyBlueprintFieldsToForm(next, 'gattung', g.fields)
      if (specApplied !== undefined) setSelectedSpecId(specApplied || null)
      return applied
    })
  }

  // Used when the *currently selected* Gattung's blueprint was just edited
  // (not switched to a different one) — refreshes its field values without
  // touching the already-selected Art. applyGattung always clears Art since a
  // genuine reselection implies a different Gattung; that must not happen
  // here or editing a Gattung's blueprint mid-product-edit silently drops the
  // Art (and with it half the composed name).
  const refreshGattungFields = (g) => {
    setForm(f => {
      const next = { ...f, gattung: g.name }
      const { form: applied, specApplied } = applyBlueprintFieldsToForm(next, 'gattung', g.fields)
      if (specApplied !== undefined) setSelectedSpecId(specApplied || null)
      return applied
    })
  }

  const applyArt = (a) => {
    setForm(f => {
      const next = { ...f, art_id: a.id, art: a.name }
      const { form: applied, specApplied } = applyBlueprintFieldsToForm(next, 'art', a.fields)
      if (specApplied !== undefined) setSelectedSpecId(specApplied || null)
      return applied
    })
  }

  const handleSelectGattung = (idVal) => {
    const gId = idVal ? parseInt(idVal, 10) : null
    if (gId === form.gattung_id) return
    if (!gId) {
      setForm(f => {
        let next = dropOrProtectLinksFor(f, 'art', selectedSpecId)
        next = dropOrProtectLinksFor(next, 'gattung', selectedSpecId)
        return { ...next, gattung_id: null, gattung: '', art_id: null, art: '' }
      })
      return
    }
    const g = gattungen.find(x => x.id === gId)
    if (g) applyGattung(g)
  }

  const handleSelectArt = (idVal) => {
    const aId = idVal ? parseInt(idVal, 10) : null
    if (aId === form.art_id) return
    if (!aId) {
      setForm(f => dropOrProtectLinksFor({ ...f, art_id: null, art: '' }, 'art', selectedSpecId))
      return
    }
    const a = arten.find(x => x.id === aId)
    if (a) applyArt(a)
  }

  const handleBlueprintSaved = ({ id, name, fields }) => {
    const type = blueprintDialog.type
    const gattungIdForArtRefresh = form.gattung_id
    setBlueprintDialog(null)
    if (type === 'gattung') {
      window.api.getGattungen().then(setGattungen)
      // Editing the already-selected Gattung's blueprint must not clear the
      // Art that's currently selected under it — only a genuine switch to a
      // different (or newly created) Gattung should do that.
      if (id === form.gattung_id) refreshGattungFields({ id, name, fields })
      else applyGattung({ id, name, fields })
    } else {
      if (gattungIdForArtRefresh) window.api.getArten(gattungIdForArtRefresh).then(list => setArten(list || []))
      applyArt({ id, name, fields })
    }
  }

  const handleSaveSpecification = async (spec) => {
    try {
      const savedId = await window.api.saveSpecification(spec)
      // Refresh available specs
      const allSpecs = await window.api.getSpecifications()
      setAvailableSpecs(allSpecs || [])
      // Set as selected
      handleSpecChange(String(savedId))
      setShowSpecDialog(false)
      addToast('Spezifikation gespeichert', 'success')
    } catch (e) {
      addToast(`Fehler: ${e.message}`, 'error')
    }
  }

  // Pflanzen: Name live aus Gattung/Art/Kultivar ableiten (kein freies Titelfeld)
  useEffect(() => {
    if (isRecipeType(form.product_type)) return
    const composed = composeProductName(form.gattung, form.art, form.kultivar)
    if (composed !== form.name) set('name', composed)
  }, [form.product_type, form.gattung, form.art, form.kultivar])

  // SEO-Titel/-Beschreibung/Fokus-Keyword: auto-befüllen und bei Gattung/Art/
  // Kultivar-Änderungen live synchron halten, SOLANGE der Nutzer das Feld seit
  // der letzten Auto-Vorschlag nicht selbst editiert hat (siehe autoSeo*Ref
  // oben) — tippt der Nutzer etwas Abweichendes, greift der Effekt nie wieder ein.
  useEffect(() => {
    const auto = composeSeoTitle(form)
    if (!auto || auto === form.seo_title) return
    if (form.seo_title === autoSeoTitleRef.current) {
      autoSeoTitleRef.current = auto
      set('seo_title', auto)
    }
  }, [form.gattung, form.art, form.kultivar, form.name, form.product_type])

  useEffect(() => {
    const potSizeCm = availableSpecs.find(s => s.id === selectedSpecId)?.pot_size_cm ?? null
    const auto = composeSeoDescription({ ...form, potSizeCm })
    if (!auto || auto === form.seo_description) return
    if (form.seo_description === autoSeoDescRef.current) {
      autoSeoDescRef.current = auto
      set('seo_description', auto)
    }
  }, [form.gattung, form.art, form.kultivar, form.name, form.product_type, form.short_description,
      form.care_light, form.care_water, form.price, form.regular_price, form.stock, form.is_variable, selectedSpecId, availableSpecs])

  useEffect(() => {
    const auto = composeSeoFocusKeyword(form)
    if (!auto || auto === form.seo_focus_keyword) return
    if (form.seo_focus_keyword === autoSeoFocusRef.current) {
      autoSeoFocusRef.current = auto
      set('seo_focus_keyword', auto)
    }
  }, [form.gattung, form.art, form.product_type])

  const save = async (formData) => {
    const data = formData || form
    const desc = descRef.current?.getValue() || data.description || ''
    const isRecipe = isRecipeType(data.product_type)
    const toSave = {
      ...data,
      name: isRecipe ? data.name : composeProductName(data.gattung, data.art, data.kultivar),
      gattung: isRecipe ? '' : data.gattung,
      art: isRecipe ? '' : data.art,
      kultivar: isRecipe ? '' : data.kultivar,
      description: desc,
      id: currentIdRef.current || undefined,
      price: parseFloat(data.price) || 0,
      regular_price: parseFloat(data.regular_price) || 0,
      stock: parseInt(data.stock) || 0,
      // Blank + "nie niedriger Bestand" not checked → defaults to 3, not left empty.
      low_stock_threshold: data.never_low_stock ? null : (data.low_stock_threshold ? parseInt(data.low_stock_threshold) : 3),
      care_temp_min: data.care_temp_min ? parseFloat(data.care_temp_min) : null,
      care_temp_max: data.care_temp_max ? parseFloat(data.care_temp_max) : null,
      care_temp_ausgepflanzt_min: data.care_temp_ausgepflanzt_min ? parseFloat(data.care_temp_ausgepflanzt_min) : null,
      care_temp_ausgepflanzt_max: data.care_temp_ausgepflanzt_max ? parseFloat(data.care_temp_ausgepflanzt_max) : null,
      liter_content: data.liter_content ? parseFloat(data.liter_content) : null,
      weight_content: data.weight_content ? parseFloat(data.weight_content) : null,
      differential_taxation: data.differential_taxation ? 1 : 0,
      never_low_stock: data.never_low_stock ? 1 : 0,
      show_exact_stock: data.show_exact_stock ? 1 : 0,
      is_variable: data.is_variable ? 1 : 0,
      sell_own_substrate: data.sell_own_substrate ? 1 : 0,
      composition: JSON.stringify(data.composition ?? []),
      // Nur einmalig erzeugen (nie überschreiben) — sonst bricht jede spätere
      // Speicherung die bereits veröffentlichte URL.
      slug: data.slug || slugify(isRecipe ? data.name : composeProductName(data.gattung, data.art, data.kultivar)),
    }
    const id = await window.api.saveProduct(toSave)
    currentIdRef.current = id
    return id
  }

  const hasContent = (data) => isRecipeType(data.product_type)
    ? !!data.name?.trim()
    : !!(data.gattung?.trim() || data.art?.trim() || data.kultivar?.trim())

  useAutoSave(form, (data) => {
    // Don't create a row for a still-empty new-product draft — only ever touch
    // an existing row, or a new one that already has a name to show for it.
    if (!currentIdRef.current && !hasContent(data)) return
    save(data).catch(() => {})
  }, autosaveMs)

  const saveRef = useRef(save)
  useEffect(() => { saveRef.current = save }, [save])

  useEffect(() => {
    const handler = async () => {
      if (!currentIdRef.current && !hasContent(form)) return
      try {
        await saveRef.current()
        addToast('Gespeichert', 'success')
      } catch (e) {
        addToast(`Fehler: ${e.message}`, 'error')
      }
    }
    window.addEventListener('manual-save', handler)
    return () => window.removeEventListener('manual-save', handler)
  }, [])

  const handleSubmit = async (e) => {
    e.preventDefault()
    if (isRecipeType(form.product_type)) {
      if (!form.name.trim()) { addToast('Name / Produkttitel ist Pflichtfeld', 'error'); return }
    } else if (!form.gattung.trim() && !form.art.trim() && !form.kultivar.trim()) {
      addToast('Bitte Gattung, Art oder Kultivar angeben', 'error'); return
    }
    if (form.seo_title.length > 60 && warnedSeoTitleRef.current !== form.seo_title) {
      warnedSeoTitleRef.current = form.seo_title
      addToast(`SEO-Titel hat zu viele Zeichen (${form.seo_title.length}/60) — Google schneidet ihn sonst ab. Nochmal klicken zum Ignorieren.`, 'error')
      return
    }
    if (form.seo_description.length > 160 && warnedSeoDescRef.current !== form.seo_description) {
      warnedSeoDescRef.current = form.seo_description
      addToast(`SEO-Beschreibung hat zu viele Zeichen (${form.seo_description.length}/160) — Google schneidet sie sonst ab. Nochmal klicken zum Ignorieren.`, 'error')
      return
    }
    setSaving(true)
    try {
      const id = await save()
      if (selectedSpecId && id) {
        await window.api.setProductSpecifications(id, [selectedSpecId])
      }
      if (id) await window.api.setProductTags(id, selectedTagIds)
      if (id) await window.api.setProductCategories(id, selectedCategoryIds)

      const folderName = id ? generateFolderName(id) : null
      let touchedTemp = false

      // Rename the whole product image folder (base images + any variant
      // subfolders it already contains) up front, decoupled from whether this
      // particular session touches base-product images — a variable product
      // may have zero base images (that section is hidden) but still have
      // variant subfolders that need to move along with a Gattung/Art/
      // Kultivar rename.
      if (id && originalFolderRef.current && originalFolderRef.current !== folderName) {
        await window.api.renameFolder(originalFolderRef.current, folderName)
      }
      if (id) await window.api.setProductImageFolder(id, folderName)

      // Variants are managed exclusively in ProductList.jsx now (added,
      // edited, and persisted there, immediately, per-variant) — the only
      // thing left to do here is clean up if a product got switched off
      // "Variabel", so no orphaned variant rows linger in the DB.
      if (id && !form.is_variable) {
        await window.api.saveVariants(id, [])
      }

      // Finalize base-product images. The GAK folder may contain files the app doesn't
      // track (dropped in manually via Explorer) — those must survive untouched. So
      // instead of wiping the folder, we only: (1) delete the specific old filenames of
      // tracked images being replaced/removed, (2) write the current temp set under the
      // fresh _Vorschaubild/_BildN names.
      if (id && (images.length > 0 || originalImagesRef.current.length > 0)) {
        touchedTemp = true

        // Old tracked filenames are now stale (superseded by a fresh name below, or the
        // image was removed entirely) — remove just those, nothing else in the folder.
        const keptIds = new Set(images.map(img => img.id).filter(Boolean))
        for (const orig of originalImagesRef.current) {
          if (orig.filename) await window.api.deleteFileInFolder(folderName, orig.filename)
          if (!keptIds.has(orig.id)) await window.api.deleteImage(orig.id)
        }

        const finalizedBaseImages = await finalizeImages(images, folderName, folderName)
        for (const img of finalizedBaseImages) {
          // Save to DB with product_id (pass id through so existing images are updated, not duplicated)
          await window.api.saveImage({
            id: img.id,
            product_id: id,
            local_path: img.local_path,
            avif_local_path: img.avif_local_path,
            filename: img.filename,
            sort_order: img.sort_order,
            watermark: img.watermark
          })
        }
      }

      // Only now, after both base and variant images have been moved out of
      // it, is it safe to wipe the temp folder.
      if (touchedTemp) {
        await window.api.clearTempFolder()
        setImages([])
        originalImagesRef.current = []
        originalFolderRef.current = null
      }
      addToast('Produkt gespeichert', 'success')
      onSaved && onSaved()
      onClose()
    } catch (e) {
      addToast(`Fehler: ${e.message}`, 'error')
    } finally {
      setSaving(false)
    }
  }

  const handleClose = async () => {
    // Existing images are only ever copied into temp, never moved —
    // originals are untouched until save, so cancelling just needs to
    // throw the temp copies away.
    if (images.length > 0) {
      await window.api.clearTempFolder()
      setImages([])
      originalImagesRef.current = []
      originalFolderRef.current = null
    }
    onClose()
  }

  return (
    <div style={{
      position: 'fixed', inset: 0, background: 'rgba(0,0,0,0.6)', zIndex: 300,
      display: 'flex', alignItems: 'flex-start', justifyContent: 'center',
      paddingTop: 20, paddingBottom: 20, overflow: 'auto'
    }}>
      <div style={{
        background: '#25302B', border: '1px solid rgba(155,111,208,0.2)',
        borderRadius: 8, width: '100%', maxWidth: 740,
        boxShadow: '0 24px 80px rgba(0,0,0,0.7)', display: 'flex', flexDirection: 'column'
      }}>
        {/* Modal header */}
        <div style={{
          padding: '14px 20px', background: '#1A231F',
          borderBottom: '1px solid rgba(155,111,208,0.15)',
          display: 'flex', justifyContent: 'space-between', alignItems: 'center',
          borderRadius: '8px 8px 0 0', position: 'sticky', top: 0, zIndex: 10
        }}>
          <h2 style={{ fontSize: 15, fontWeight: 700, color: '#EAE4D6' }}>
            {productId ? 'Produkt bearbeiten' : 'Neues Produkt'}
          </h2>
          <div style={{ display: 'flex', gap: 8, alignItems: 'center' }}>
            <div>
              <label className="label" style={{ display: 'inline', marginRight: 6 }}>Status</label>
              <select
                value={form.status}
                onChange={e => set('status', e.target.value)}
                style={{ background: '#2F3B35', border: '1px solid rgba(155,111,208,0.2)', color: '#EAE4D6', borderRadius: 4, padding: '4px 8px', fontSize: 12 }}
              >
                {PRODUCT_STATUSES.map(s => <option key={s.value} value={s.value}>{s.label}</option>)}
              </select>
            </div>
            <button type="button"
              onClick={() => openPreview({ product: { ...form, id: productId } })}
              style={{ background: 'rgba(155,111,208,0.12)', border: '1px solid rgba(155,111,208,0.3)', color: '#B8A8D8', borderRadius: 4, padding: '4px 10px', fontSize: 12, cursor: 'pointer' }}
            >
              👁 Vorschau
            </button>
            <button type="button" onClick={handleClose} style={{ background: 'none', border: 'none', color: '#9CA59E', cursor: 'pointer', fontSize: 18, lineHeight: 1 }}>✕</button>
          </div>
        </div>

        <form onSubmit={handleSubmit} style={{ padding: 20 }}>
          {/* 1. Grundinfos */}
          <Section title="Grundinfos">
            {isRecipeType(form.product_type) ? (
              <Row cols={form.product_type === 'fertilizer' ? 2 : 1}>
                <Field label="Name / Produkttitel">
                  <input className="input" value={form.name} onChange={e => set('name', e.target.value)} placeholder="z. B. Bio-Blumenerde Premium" required />
                </Field>
                {form.product_type === 'fertilizer' && (
                  <Field label="Düngertyp">
                    <select className="input" value={form.fertilizer_type} onChange={e => set('fertilizer_type', e.target.value)} required>
                      <option value="">— wählen —</option>
                      {FERTILIZER_TYPES.map(t => <option key={t.value} value={t.value}>{t.label}</option>)}
                    </select>
                  </Field>
                )}
              </Row>
            ) : (
              <Row cols={3}>
                <Field label="Gattung">
                  <div style={{ display: 'flex', gap: 6, minWidth: 0 }}>
                    <div style={{ flex: 1, minWidth: 0 }}>
                      <ComboSelect
                        options={gattungen.map(g => ({ value: g.id, label: g.name }))}
                        value={form.gattung_id ?? ''}
                        onChange={handleSelectGattung}
                        placeholder="Pelargonium"
                      />
                    </div>
                    <button type="button" className="btn-secondary" style={{ padding: '0 9px' }} title="Neue Gattung anlegen"
                      onClick={() => setBlueprintDialog({ type: 'gattung', id: null })}>+</button>
                    {form.gattung_id && (
                      <button type="button" className="btn-secondary" style={{ padding: '0 9px' }} title="Gattung bearbeiten"
                        onClick={() => setBlueprintDialog({ type: 'gattung', id: form.gattung_id })}>✏️</button>
                    )}
                  </div>
                </Field>
                <Field label="Art">
                  <div style={{ display: 'flex', gap: 6, minWidth: 0 }}>
                    <div style={{ flex: 1, minWidth: 0 }}>
                      <ComboSelect
                        options={arten.map(a => ({ value: a.id, label: a.name }))}
                        value={form.art_id ?? ''}
                        onChange={handleSelectArt}
                        placeholder={form.gattung_id ? 'graveolens' : 'Zuerst Gattung wählen'}
                      />
                    </div>
                    <button type="button" className="btn-secondary" style={{ padding: '0 9px' }} title="Neue Art anlegen"
                      disabled={!form.gattung_id}
                      onClick={() => setBlueprintDialog({ type: 'art', id: null, parentGattungId: form.gattung_id })}>+</button>
                    {form.art_id && (
                      <button type="button" className="btn-secondary" style={{ padding: '0 9px' }} title="Art bearbeiten"
                        onClick={() => setBlueprintDialog({ type: 'art', id: form.art_id, parentGattungId: form.gattung_id })}>✏️</button>
                    )}
                  </div>
                </Field>
                <Field label="Kultivar">
                  <input className="input" value={form.kultivar} onChange={e => set('kultivar', e.target.value)} placeholder="'Rose'" />
                </Field>
              </Row>
            )}
            {!isRecipeType(form.product_type) && (
              <Row>
                <Field label="Gängiger Name" hint={linkHint('common_name')}>
                  <input className="input" value={form.common_name} onChange={e => setField('common_name', e.target.value)} placeholder="z. B. Duftgeranie" />
                </Field>
              </Row>
            )}
            <Row>
              {!form.is_variable && (
                <Field label="SKU / Artikelnummer">
                  <input className="input" value={form.sku} onChange={e => set('sku', e.target.value)} placeholder="PA-00001" />
                </Field>
              )}
              <Field label="Produkttyp" hint={linkHint('product_type')}>
                <Toggle
                  options={[{ value: 'plant', label: '🌿 Pflanze' }, { value: 'substrate', label: '🪨 Substrat' }, { value: 'fertilizer', label: '🌱 Dünger' }]}
                  value={form.product_type}
                  onChange={v => setField('product_type', v)}
                />
              </Field>
            </Row>
            <label style={{ display: 'flex', gap: 8, alignItems: 'center', cursor: 'pointer' }}>
              <input type="checkbox" checked={form.is_variable} onChange={e => set('is_variable', e.target.checked)} />
              <span style={{ fontSize: 12, color: '#C8C0AF' }}>Variabel (SKU, Preis, Lager und Bilder werden pro Variante festgelegt)</span>
            </label>
            <Row>
              <Field label="Einheit" hint={linkHint('unit_type')}>
                <Toggle
                  options={[{ value: 'piece', label: 'Stück' }, { value: 'liter', label: 'Liter' }, { value: 'kg', label: 'kg' }]}
                  value={form.unit_type}
                  onChange={v => setField('unit_type', v)}
                />
              </Field>
              {form.unit_type === 'liter' && (
                <Field label="Inhalt (Liter)" hint={linkHint('liter_content')}>
                  <input className="input" type="number" step="0.1" min="0" value={form.liter_content} onChange={e => setField('liter_content', e.target.value)} placeholder="1.0" />
                </Field>
              )}
              {form.unit_type === 'kg' && (
                <Field label="Inhalt (kg)" hint={linkHint('weight_content')}>
                  <input className="input" type="number" step="0.1" min="0" value={form.weight_content} onChange={e => setField('weight_content', e.target.value)} placeholder="5.0" />
                </Field>
              )}
            </Row>
            {!form.is_variable && (
              <Row>
                <Field label="Preis (€)">
                  <input className="input" type="number" step="0.01" min="0" value={form.regular_price} onChange={e => { set('regular_price', e.target.value); set('price', e.target.value) }} placeholder="9.99" />
                </Field>
              </Row>
            )}
          </Section>

          {/* 2. Steuer & Versand */}
          <Section title="Steuer & Versand" defaultOpen={false}>
            <Row>
              <TaxClassPicker value={form.tax_class_id} onChange={handleTaxClassChange} hint={linkHint('tax_class')} />
              <DeliveryTimePicker value={form.delivery_time} onChange={v => setField('delivery_time', v)} hint={linkHint('delivery_time')} />
            </Row>
            <Row>
              <Field label=" ">
                <label style={{ display: 'flex', gap: 8, alignItems: 'center', marginTop: 22, cursor: 'pointer' }}>
                  <input type="checkbox" checked={form.differential_taxation} onChange={e => setField('differential_taxation', e.target.checked)} />
                  <span style={{ fontSize: 12, color: '#C8C0AF' }}>Differenzbesteuerung §25a UStG {linkHint('differential_taxation')}</span>
                </label>
              </Field>
            </Row>
          </Section>

          {/* 3. Lager */}
          {!form.is_variable && (
          <Section title="Lager" defaultOpen={false}>
            <Row>
              <Field label="Lagerbestand" hint={linkHint('stock')}>
                <input className="input" type="number" min="0" value={form.stock} onChange={e => setField('stock', e.target.value)} placeholder="0" />
              </Field>
              <Field label="Niedrig-Bestand Schwellwert" hint={linkHint('low_stock_threshold')}>
                <input className="input" type="number" min="0" value={form.low_stock_threshold} onChange={e => setField('low_stock_threshold', e.target.value)} placeholder="Standard (3)" disabled={form.never_low_stock} />
              </Field>
            </Row>
            <label style={{ display: 'flex', gap: 8, alignItems: 'center', cursor: 'pointer' }}>
              <input type="checkbox" checked={form.never_low_stock} onChange={e => setField('never_low_stock', e.target.checked)} />
              <span style={{ fontSize: 12, color: '#C8C0AF' }}>Nie als „Niedriger Bestand" markieren {linkHint('never_low_stock')}</span>
            </label>
            <label style={{ display: 'flex', gap: 8, alignItems: 'center', cursor: 'pointer', marginTop: 6 }}>
              <input type="checkbox" checked={form.show_exact_stock} onChange={e => setField('show_exact_stock', e.target.checked)} />
              <span style={{ fontSize: 12, color: '#C8C0AF' }}>Genaue Stückzahl bei niedrigem Bestand anzeigen (sonst nur „Niedriger Bestand")</span>
            </label>
          </Section>
          )}

          {/* 5. Bilder */}
          <Section title="Bilder" defaultOpen={false}>
            {form.is_variable && (
              <div style={{ fontSize: 11, color: '#9CA59E', marginBottom: 10 }}>
                Diese Bilder werden als allgemeines Vorschaubild verwendet und zusätzlich zu den Varianten-Bildern angezeigt.
              </div>
            )}
            <ImageUpload images={images} setImages={setImages} />
          </Section>

          {/* Varianten werden ausschließlich in der Produktübersicht
              (ProductList.jsx) angelegt/bearbeitet — hier nur ein Hinweis. */}
          {form.is_variable && (
            <div style={{ margin: '4px 0 16px', fontSize: 11, color: '#9CA59E' }}>
              Varianten werden nach dem Speichern in der Produktübersicht verwaltet.
            </div>
          )}

          {/* 6. Kurzbeschreibung */}
          <Section title="Kurzbeschreibung" defaultOpen={false}>
            <Field full hint={linkHint('short_description')}>
              <textarea
                className="input"
                rows={3}
                value={form.short_description}
                onChange={e => setField('short_description', e.target.value)}
                placeholder="Kurze Produktbeschreibung für Listings..."
                maxLength={500}
              />
              <div style={{ fontSize: 11, color: '#9CA59E', textAlign: 'right', marginTop: 3 }}>
                {(form.short_description || '').length}/500
              </div>
            </Field>
          </Section>

          {/* 7. Beschreibung */}
          <Section title="Produktbeschreibung" defaultOpen={false}>
            {linkHint('description') && (
              <div style={{ fontSize: 11, color: '#9B6FD0', marginBottom: 6 }}>{linkHint('description')}</div>
            )}
            <RichTextEditor
              ref={descRef}
              value={form.description}
              onChange={v => setField('description', v)}
              placeholder="Ausführliche Produktbeschreibung..."
            />
          </Section>

          {/* 8. SEO */}
          <Section title="SEO" defaultOpen={false}>
            <div style={{ display: 'flex', flexDirection: 'column', gap: 14 }}>
              <div>
                <label className="label" style={{ display: 'flex', alignItems: 'center' }}>
                  Meta-Titel
                  <Tooltip text="Wird als klickbare Überschrift bei Google angezeigt. Automatisch aus Gattung/Art/Kultivar vorbefüllt — bei Bedarf anpassen. Richtwert: ca. 50–60 Zeichen, sonst wird er in den Suchergebnissen abgeschnitten." />
                </label>
                <input className="input" value={form.seo_title} onChange={e => setField('seo_title', e.target.value)} placeholder="wird automatisch vorgeschlagen" />
                <div style={{ fontSize: 11, color: '#9CA59E', textAlign: 'right', marginTop: 3 }}>{form.seo_title.length}/60</div>
              </div>
              <div>
                <label className="label" style={{ display: 'flex', alignItems: 'center' }}>
                  Meta-Beschreibung
                  <Tooltip text="Der Beschreibungstext unter dem Titel bei Google. Automatisch aus Kurzbeschreibung bzw. Pflegehinweisen vorbefüllt — für eine bessere Klickrate lohnt sich eine von Hand geschriebene, einladende Formulierung. Richtwert: ca. 150–160 Zeichen." />
                </label>
                <textarea className="input" rows={2} value={form.seo_description} onChange={e => setField('seo_description', e.target.value)} placeholder="wird automatisch vorgeschlagen" maxLength={300} />
                <div style={{ fontSize: 11, color: '#9CA59E', textAlign: 'right', marginTop: 3 }}>{form.seo_description.length}/160</div>
              </div>
              <div>
                <label className="label" style={{ display: 'flex', alignItems: 'center' }}>
                  Fokus-Keyword
                  <Tooltip text="Wonach würde jemand bei Google suchen, um genau dieses Produkt zu finden? Z. B. 'Pelargonium graveolens kaufen'. Das lässt sich nicht automatisch ermitteln — kurz überlegen, welcher Suchbegriff am besten passt." />
                </label>
                <input className="input" value={form.seo_focus_keyword} onChange={e => setField('seo_focus_keyword', e.target.value)} placeholder="z. B. Pelargonium graveolens kaufen" />
              </div>
            </div>
          </Section>

          {/* 9. Spezifikationen */}
          <Section title="Spezifikationen" defaultOpen={false}>
            <div>
              <label className="label">Produktspezifikation {linkHint('specification_id')}</label>
              <div style={{ display: 'flex', gap: 8 }}>
                <select
                  className="input"
                  value={selectedSpecId || ''}
                  onChange={(e) => handleSpecChange(e.target.value)}
                  style={{ flex: 1 }}
                >
                  <option value="">Keine Spezifikation</option>
                  {availableSpecs.map(spec => (
                    <option key={spec.id} value={spec.id}>{spec.name}</option>
                  ))}
                </select>
                <button
                  type="button"
                  className="btn-secondary"
                  onClick={() => setShowSpecDialog(true)}
                  style={{ whiteSpace: 'nowrap' }}
                >
                  + Neue
                </button>
              </div>
              
              {/* Selected specification details */}
              {selectedSpecId && (() => {
                const spec = availableSpecs.find(s => s.id === selectedSpecId)
                if (!spec) return null
                return (
                  <div style={{ marginTop: 12, padding: '10px 14px', background: 'rgba(155,111,208,0.08)', border: '1px solid rgba(155,111,208,0.2)', borderRadius: 4 }}>
                    <div style={{ fontSize: 12, color: '#B8A8D8', marginBottom: 4 }}>{spec.name}</div>
                    <div style={{ fontSize: 11, color: '#9CA59E' }}>
                      {spec.shape === 'round' ? 'Rund' : 'Eckig'} • {spec.pot_size}cm • {spec.weight}{spec.weight_unit === 'g' ? 'g' : 'kg'} • {spec.height}×{spec.width}cm
                    </div>
                  </div>
                )
              })()}
            </div>
          </Section>

          {/* Tags */}
          <Section title="Tags" defaultOpen={false}>
            <TagPool
              entityType="product"
              entityId={productId}
              onChange={setSelectedTagIds}
              gattungId={form.gattung_id}
              gattungName={form.gattung}
              artId={form.art_id}
              artName={form.art}
            />
          </Section>

          {/* Kategorien */}
          <Section title="Kategorien" defaultOpen={false}>
            <CategoryAssignPicker entityId={productId} onChange={setSelectedCategoryIds} />
          </Section>

          {/* 10. Pflege */}
          {form.product_type === 'plant' && (
          <Section title="Pflegehinweise" defaultOpen={false}>
            <Row>
              <Field label="Licht (Bevorzugt)" hint={linkHint('care_light')}>
                <select className="input" value={form.care_light} onChange={e => setField('care_light', e.target.value)}>
                  {CARE_LIGHT_OPTIONS.map(o => <option key={o.value} value={o.value}>{o.label}</option>)}
                </select>
              </Field>
              <Field label="Wasser (Bevorzugt)" hint={linkHint('care_water')}>
                <select className="input" value={form.care_water} onChange={e => setField('care_water', e.target.value)}>
                  {CARE_WATER_OPTIONS.map(o => <option key={o.value} value={o.value}>{o.label}</option>)}
                </select>
              </Field>
            </Row>
            <Row>
              <Field label="Licht verträgt (von)" hint={linkHint('care_light_tolerates_min')}>
                <select className="input" value={form.care_light_tolerates_min} onChange={e => setField('care_light_tolerates_min', e.target.value)}>
                  {CARE_LIGHT_OPTIONS.map(o => <option key={o.value} value={o.value}>{o.label}</option>)}
                </select>
              </Field>
              <Field label="Licht verträgt (bis)" hint={linkHint('care_light_tolerates_max')}>
                <select className="input" value={form.care_light_tolerates_max} onChange={e => setField('care_light_tolerates_max', e.target.value)}>
                  {CARE_LIGHT_OPTIONS.map(o => <option key={o.value} value={o.value}>{o.label}</option>)}
                </select>
              </Field>
            </Row>
            <Row>
              <Field label="Wasser verträgt (von)" hint={linkHint('care_water_tolerates_min')}>
                <select className="input" value={form.care_water_tolerates_min} onChange={e => setField('care_water_tolerates_min', e.target.value)}>
                  {CARE_WATER_OPTIONS.map(o => <option key={o.value} value={o.value}>{o.label}</option>)}
                </select>
              </Field>
              <Field label="Wasser verträgt (bis)" hint={linkHint('care_water_tolerates_max')}>
                <select className="input" value={form.care_water_tolerates_max} onChange={e => setField('care_water_tolerates_max', e.target.value)}>
                  {CARE_WATER_OPTIONS.map(o => <option key={o.value} value={o.value}>{o.label}</option>)}
                </select>
              </Field>
            </Row>
            <Row>
              <Field label="Pflegehinweise" hint={linkHint('care_winter')}>
                <input className="input" value={form.care_winter} onChange={e => setField('care_winter', e.target.value)} placeholder="z. B. hell und kühl, min. 5 °C" />
              </Field>
            </Row>
            <Row>
              <Field label="Temp. min. (Topf, °C)" hint={linkHint('care_temp_min')}>
                <input className="input" type="number" step="1" value={form.care_temp_min} onChange={e => setField('care_temp_min', e.target.value)} placeholder="-5" />
              </Field>
              <Field label="Temp. max. (Topf, °C)" hint={linkHint('care_temp_max')}>
                <input className="input" type="number" step="1" value={form.care_temp_max} onChange={e => setField('care_temp_max', e.target.value)} placeholder="35" />
              </Field>
            </Row>
            <Row>
              <Field label="Temp. min. (Ausgepflanzt, °C)" hint={linkHint('care_temp_ausgepflanzt_min')}>
                <input className="input" type="number" step="1" value={form.care_temp_ausgepflanzt_min} onChange={e => setField('care_temp_ausgepflanzt_min', e.target.value)} placeholder="-15" />
              </Field>
              <Field label="Temp. max. (Ausgepflanzt, °C)" hint={linkHint('care_temp_ausgepflanzt_max')}>
                <input className="input" type="number" step="1" value={form.care_temp_ausgepflanzt_max} onChange={e => setField('care_temp_ausgepflanzt_max', e.target.value)} placeholder="40" />
              </Field>
            </Row>
            <Row>
              <Field label="Substrat" hint={linkHint('substrate_product_id')}>
                <select className="input" value={form.substrate_product_id ?? ''} onChange={e => setField('substrate_product_id', e.target.value ? parseInt(e.target.value, 10) : null)}>
                  <option value="">— keins —</option>
                  {allProducts.filter(p => p.product_type === 'substrate').map(p => <option key={p.id} value={p.id}>{p.name}</option>)}
                </select>
              </Field>
            </Row>
            {form.substrate_product_id && (() => {
              const sub = allProducts.find(p => p.id === form.substrate_product_id)
              if (!sub) return null
              let ingredients = []
              try { ingredients = JSON.parse(sub.composition || '[]') } catch { ingredients = [] }
              return (
                <div style={{ marginTop: -4, marginBottom: 12, padding: '10px 14px', background: 'rgba(155,111,208,0.08)', border: '1px solid rgba(155,111,208,0.2)', borderRadius: 4, fontSize: 12, color: '#C8C0AF' }}>
                  <div style={{ color: '#B8A8D8', marginBottom: 4 }}>
                    {sub.sell_own_substrate ? sub.name : 'Empfohlenes Substrat nach Hausrezept'}
                  </div>
                  {ingredients.map((ing, i) => <div key={i}>{ing.percent}% {ing.name}</div>)}
                  {sub.sell_own_substrate && (
                    <div style={{ marginTop: 4, color: '#9CA59E' }}>Wird zusätzlich als eigenes Produkt verkauft — „Oder hier kaufen"-Link erscheint automatisch auf der Live-Produktseite.</div>
                  )}
                </div>
              )
            })()}
            <Row>
              <Field label="Düngertyp" hint={linkHint('fertilizer_type_choice')}>
                <select className="input" value={form.fertilizer_type_choice} onChange={e => setField('fertilizer_type_choice', e.target.value)}>
                  <option value="">— keiner —</option>
                  {FERTILIZER_TYPES.map(t => <option key={t.value} value={t.value}>{t.label}</option>)}
                </select>
              </Field>
              <Field label="Düngemenge" hint={linkHint('fertilizer_amount')}>
                <select className="input" value={form.fertilizer_amount} onChange={e => setField('fertilizer_amount', e.target.value)}>
                  {CARE_FERTILIZER_OPTIONS.map(o => <option key={o.value} value={o.value}>{o.label}</option>)}
                </select>
              </Field>
            </Row>
          </Section>
          )}

          {/* Komposition (nur Substrat) */}
          {form.product_type === 'substrate' && (
          <Section title="Komposition" defaultOpen={false}>
            {form.composition.map((row, i) => (
              <Row key={i} cols={3}>
                <Field label={i === 0 ? 'Prozent' : undefined}>
                  <input className="input" type="number" step="1" min="0" max="100" value={row.percent}
                    onChange={e => set('composition', form.composition.map((r, j) => j === i ? { ...r, percent: e.target.value } : r))}
                    placeholder="30" />
                </Field>
                <Field label={i === 0 ? 'Zutat' : undefined}>
                  <input className="input" value={row.name}
                    onChange={e => set('composition', form.composition.map((r, j) => j === i ? { ...r, name: e.target.value } : r))}
                    placeholder="z. B. Kokoshumus" />
                </Field>
                <div style={{ display: 'flex', alignItems: i === 0 ? 'flex-end' : 'center', paddingBottom: i === 0 ? 2 : 0 }}>
                  <button type="button" className="btn-secondary" onClick={() => set('composition', form.composition.filter((_, j) => j !== i))}>✕</button>
                </div>
              </Row>
            ))}
            <button type="button" className="btn-secondary" onClick={() => set('composition', [...form.composition, { percent: '', name: '' }])}>
              + Zutat hinzufügen
            </button>
            <label style={{ display: 'flex', gap: 8, alignItems: 'center', cursor: 'pointer', marginTop: 14 }}>
              <input type="checkbox" checked={form.sell_own_substrate} onChange={e => set('sell_own_substrate', e.target.checked)} />
              <span style={{ fontSize: 12, color: '#C8C0AF' }}>Verkaufe ich als eigenes Produkt</span>
            </label>
          </Section>
          )}

          {/* Actions */}
          <div style={{ display: 'flex', justifyContent: 'flex-end', gap: 10, marginTop: 8, paddingTop: 16, borderTop: '1px solid rgba(155,111,208,0.1)' }}>
            <button type="button" className="btn-secondary" onClick={handleClose}>Abbrechen</button>
            <button type="submit" className="btn-primary" disabled={saving}>
              {saving ? 'Speichere...' : productId ? 'Änderungen speichern' : 'Produkt erstellen'}
            </button>
          </div>
        </form>
      </div>
      
      {showSpecDialog && (
        <SpecificationDialog
          onSave={handleSaveSpecification}
          onCancel={() => setShowSpecDialog(false)}
        />
      )}

      {blueprintDialog && (
        <BlueprintForm
          type={blueprintDialog.type}
          id={blueprintDialog.id}
          parentGattungId={blueprintDialog.parentGattungId}
          onClose={() => setBlueprintDialog(null)}
          onSaved={handleBlueprintSaved}
        />
      )}
    </div>
  )
}
