import React, { useState, useEffect, useRef, useContext } from 'react'
import RichTextEditor from './RichTextEditor.jsx'
import SpecificationDialog from './SpecificationDialog.jsx'
import TaxClassPicker from './TaxClassPicker.jsx'
import DeliveryTimePicker from './DeliveryTimePicker.jsx'
import TagPool from './TagPool.jsx'
import CategoryAssignPicker from './CategoryAssignPicker.jsx'
import { Section, Row, Field, Toggle } from './FormPrimitives.jsx'
import { ToastContext } from '../App.jsx'
import { CARE_LIGHT_OPTIONS, CARE_WATER_OPTIONS, CARE_FERTILIZER_OPTIONS, FERTILIZER_TYPES } from '../../shared/constants.js'

const EMPTY_STATE = {
  name: '', common_name: '',
  unit_type: '', liter_content: '', weight_content: '', product_type: '',
  substrate_display_text: '',
  tax_class: '', tax_class_id: null, delivery_time: '', differential_taxation: '',
  stock: '', low_stock_threshold: '', never_low_stock: '',
  short_description: '', description: '',
  specification_id: '',
  care_light: '', care_light_tolerates_min: '', care_light_tolerates_max: '',
  care_water: '', care_water_tolerates_min: '', care_water_tolerates_max: '',
  care_winter: '',
  care_temp_min: '', care_temp_max: '',
  care_temp_ausgepflanzt_min: '', care_temp_ausgepflanzt_max: '',
  fertilizer_type_choice: '', fertilizer_amount: '',
  category_ids: [],
}

const TRISTATE = [
  { value: '', label: '— keine Vorgabe —' },
  { value: 'yes', label: 'Ja' },
  { value: 'no', label: 'Nein' },
]

function unpackFields(fields) {
  const f = fields || {}
  return {
    common_name: f.common_name ?? '',
    unit_type: f.unit_type ?? '',
    liter_content: f.liter_content != null ? String(f.liter_content) : '',
    weight_content: f.weight_content != null ? String(f.weight_content) : '',
    product_type: f.product_type ?? '',
    substrate_display_text: f.substrate_display_text ?? '',
    tax_class: f.tax_class ?? '',
    tax_class_id: f.tax_class_id ?? null,
    delivery_time: f.delivery_time ?? '',
    differential_taxation: f.differential_taxation === true ? 'yes' : f.differential_taxation === false ? 'no' : '',
    stock: f.stock != null ? String(f.stock) : '',
    low_stock_threshold: f.low_stock_threshold != null ? String(f.low_stock_threshold) : '',
    never_low_stock: f.never_low_stock === true ? 'yes' : f.never_low_stock === false ? 'no' : '',
    short_description: f.short_description ?? '',
    description: f.description ?? '',
    specification_id: f.specification_id ?? '',
    care_light: f.care_light ?? '',
    care_light_tolerates_min: f.care_light_tolerates_min ?? '',
    care_light_tolerates_max: f.care_light_tolerates_max ?? '',
    care_water: f.care_water ?? '',
    care_water_tolerates_min: f.care_water_tolerates_min ?? '',
    care_water_tolerates_max: f.care_water_tolerates_max ?? '',
    care_winter: f.care_winter ?? '',
    care_temp_min: f.care_temp_min != null ? String(f.care_temp_min) : '',
    care_temp_max: f.care_temp_max != null ? String(f.care_temp_max) : '',
    care_temp_ausgepflanzt_min: f.care_temp_ausgepflanzt_min != null ? String(f.care_temp_ausgepflanzt_min) : '',
    care_temp_ausgepflanzt_max: f.care_temp_ausgepflanzt_max != null ? String(f.care_temp_ausgepflanzt_max) : '',
    fertilizer_type_choice: f.fertilizer_type_choice ?? '',
    fertilizer_amount: f.fertilizer_amount ?? '',
  }
}

// Only keys the user actually filled in end up in the saved blueprint —
// that sparseness is what lets a product distinguish "this blueprint has no
// opinion on this field" from "this blueprint explicitly sets it".
function buildFields(state, description) {
  const f = {}
  if (state.common_name.trim()) f.common_name = state.common_name
  if (state.unit_type) f.unit_type = state.unit_type
  if (state.unit_type === 'liter' && state.liter_content !== '') f.liter_content = parseFloat(state.liter_content)
  if (state.unit_type === 'kg' && state.weight_content !== '') f.weight_content = parseFloat(state.weight_content)
  if (state.product_type) f.product_type = state.product_type
  if (state.product_type === 'substrate' && state.substrate_display_text.trim()) f.substrate_display_text = state.substrate_display_text
  // tax_class_id only travels alongside a real (already WC-synced) tax_class
  // slug — persisting the id without it would let a blueprint apply an id
  // whose text half never actually reaches products.
  if (state.tax_class) {
    f.tax_class = state.tax_class
    if (state.tax_class_id != null) f.tax_class_id = state.tax_class_id
  }
  if (state.delivery_time) f.delivery_time = state.delivery_time
  if (state.differential_taxation !== '') f.differential_taxation = state.differential_taxation === 'yes'
  if (state.stock !== '') f.stock = parseInt(state.stock, 10)
  if (state.low_stock_threshold !== '') f.low_stock_threshold = parseInt(state.low_stock_threshold, 10)
  if (state.never_low_stock !== '') f.never_low_stock = state.never_low_stock === 'yes'
  if (state.short_description.trim()) f.short_description = state.short_description
  if (description && description.trim()) f.description = description
  if (state.specification_id) f.specification_id = parseInt(state.specification_id, 10)
  if (state.care_light) f.care_light = state.care_light
  if (state.care_light_tolerates_min) f.care_light_tolerates_min = state.care_light_tolerates_min
  if (state.care_light_tolerates_max) f.care_light_tolerates_max = state.care_light_tolerates_max
  if (state.care_water) f.care_water = state.care_water
  if (state.care_water_tolerates_min) f.care_water_tolerates_min = state.care_water_tolerates_min
  if (state.care_water_tolerates_max) f.care_water_tolerates_max = state.care_water_tolerates_max
  if (state.care_winter.trim()) f.care_winter = state.care_winter
  if (state.care_temp_min !== '') f.care_temp_min = parseFloat(state.care_temp_min)
  if (state.care_temp_max !== '') f.care_temp_max = parseFloat(state.care_temp_max)
  if (state.care_temp_ausgepflanzt_min !== '') f.care_temp_ausgepflanzt_min = parseFloat(state.care_temp_ausgepflanzt_min)
  if (state.care_temp_ausgepflanzt_max !== '') f.care_temp_ausgepflanzt_max = parseFloat(state.care_temp_ausgepflanzt_max)
  if (state.fertilizer_type_choice) f.fertilizer_type_choice = state.fertilizer_type_choice
  if (state.fertilizer_amount) f.fertilizer_amount = state.fertilizer_amount
  return f
}

export default function BlueprintForm({ type, id, parentGattungId, initialName, onSaved, onClose }) {
  const isArt = type === 'art'
  const [form, setForm] = useState({ ...EMPTY_STATE, name: initialName || '' })
  const [loading, setLoading] = useState(!!id)
  const [saving, setSaving] = useState(false)
  const [availableSpecs, setAvailableSpecs] = useState([])
  const [showSpecDialog, setShowSpecDialog] = useState(false)
  const [bulkTagIds, setBulkTagIds] = useState([])
  const [applyingTags, setApplyingTags] = useState(false)
  const descRef = useRef(null)
  const addToast = useContext(ToastContext)

  const set = (key, val) => setForm(f => ({ ...f, [key]: val }))

  useEffect(() => {
    async function load() {
      setAvailableSpecs(await window.api.getSpecifications() || [])

      if (id) {
        const row = isArt ? await window.api.getArt(id) : await window.api.getGattung(id)
        if (row) {
          setForm({ name: row.name, category_ids: row.category_ids || [], ...unpackFields(row.fields) })
          if (descRef.current) setTimeout(() => descRef.current?.setValue(row.fields?.description || ''), 50)
        }
      }
      setLoading(false)
    }
    load()
  }, [id, isArt])

  const handleSaveSpecification = async (spec) => {
    const savedId = await window.api.saveSpecification(spec)
    setAvailableSpecs(await window.api.getSpecifications() || [])
    set('specification_id', savedId)
    setShowSpecDialog(false)
    addToast('Spezifikation gespeichert', 'success')
  }

  // One-time bulk apply, not live inheritance — adds the selected tags to
  // every product currently linked to this Gattung/Art (additive, existing
  // tags on those products are kept).
  const applyTagsToLinkedProducts = async () => {
    if (!id || bulkTagIds.length === 0) return
    setApplyingTags(true)
    try {
      const productIds = await window.api.getProductIdsByBlueprint(type, id)
      for (const pid of productIds) {
        const existing = await window.api.getProductTags(pid)
        const merged = Array.from(new Set([...existing.map(t => t.id), ...bulkTagIds]))
        await window.api.setProductTags(pid, merged)
      }
      addToast(`Tags ${productIds.length} Produkt${productIds.length !== 1 ? 'en' : ''} zugeordnet`, 'success')
    } catch (e2) {
      addToast(`Fehler: ${e2.message}`, 'error')
    } finally {
      setApplyingTags(false)
    }
  }

  const handleSubmit = async (e) => {
    e.preventDefault()
    if (!form.name.trim()) { addToast('Name ist Pflichtfeld', 'error'); return }
    setSaving(true)
    try {
      const description = descRef.current?.getValue() || form.description || ''
      const fields = buildFields(form, description)
      const payload = isArt
        ? { id, gattung_id: parentGattungId, name: form.name.trim(), fields, category_ids: form.category_ids }
        : { id, name: form.name.trim(), fields, category_ids: form.category_ids }
      const savedId = isArt ? await window.api.saveArt(payload) : await window.api.saveGattung(payload)
      addToast(`${isArt ? 'Art' : 'Gattung'} gespeichert`, 'success')
      onSaved && onSaved({ id: savedId, name: form.name.trim(), fields })
    } catch (e2) {
      addToast(`Fehler: ${e2.message}`, 'error')
    } finally {
      setSaving(false)
    }
  }

  return (
    <div style={{
      position: 'fixed', inset: 0, background: 'rgba(0,0,0,0.6)', zIndex: 400,
      display: 'flex', alignItems: 'flex-start', justifyContent: 'center',
      paddingTop: 20, paddingBottom: 20, overflow: 'auto'
    }}>
      <div style={{
        background: '#25302B', border: '1px solid rgba(155,111,208,0.2)',
        borderRadius: 8, width: '100%', maxWidth: 680,
        boxShadow: '0 24px 80px rgba(0,0,0,0.7)', display: 'flex', flexDirection: 'column'
      }}>
        <div style={{
          padding: '14px 20px', background: '#1A231F',
          borderBottom: '1px solid rgba(155,111,208,0.15)',
          display: 'flex', justifyContent: 'space-between', alignItems: 'center',
          borderRadius: '8px 8px 0 0', position: 'sticky', top: 0, zIndex: 10
        }}>
          <h2 style={{ fontSize: 15, fontWeight: 700, color: '#EAE4D6' }}>
            {id ? `${isArt ? 'Art' : 'Gattung'} bearbeiten` : `Neue ${isArt ? 'Art' : 'Gattung'}`}
          </h2>
          <button type="button" onClick={onClose} style={{ background: 'none', border: 'none', color: '#9CA59E', cursor: 'pointer', fontSize: 18, lineHeight: 1 }}>✕</button>
        </div>

        {loading ? (
          <div style={{ padding: 40, textAlign: 'center', color: '#9CA59E', fontSize: 13 }}>Laden…</div>
        ) : (
          <form onSubmit={handleSubmit} style={{ padding: 20 }}>
            <Section title="Grundinfos">
              <Field label={isArt ? 'Art-Name' : 'Gattungs-Name'} full>
                <input className="input" value={form.name} onChange={e => set('name', e.target.value)}
                  placeholder={isArt ? 'graveolens' : 'Pelargonium'} required autoFocus />
              </Field>
              <Field label="Gängiger Name" full>
                <input className="input" value={form.common_name} onChange={e => set('common_name', e.target.value)}
                  placeholder="z. B. Duftgeranie" />
              </Field>
              <Row>
                <Field label="Einheit">
                  <Toggle
                    options={[{ value: '', label: '— keine Vorgabe —' }, { value: 'piece', label: 'Stück' }, { value: 'liter', label: 'Liter' }, { value: 'kg', label: 'kg' }]}
                    value={form.unit_type}
                    onChange={v => set('unit_type', v)}
                  />
                </Field>
                {form.unit_type === 'liter' && (
                  <Field label="Inhalt (Liter)">
                    <input className="input" type="number" step="0.1" min="0" value={form.liter_content} onChange={e => set('liter_content', e.target.value)} placeholder="1.0" />
                  </Field>
                )}
                {form.unit_type === 'kg' && (
                  <Field label="Inhalt (kg)">
                    <input className="input" type="number" step="0.1" min="0" value={form.weight_content} onChange={e => set('weight_content', e.target.value)} placeholder="5.0" />
                  </Field>
                )}
              </Row>
              <Row>
                <Field label="Produkttyp">
                  <Toggle
                    options={[{ value: '', label: '— keine Vorgabe —' }, { value: 'plant', label: '🌿 Pflanze' }, { value: 'substrate', label: '🪨 Substrat' }]}
                    value={form.product_type}
                    onChange={v => set('product_type', v)}
                  />
                </Field>
              </Row>
              {form.product_type === 'substrate' && (
                <Field label="Website-Text" full hint="wird nur angezeigt, wenn bei einem Substrat-Produkt oben Verkaufe ich aktiviert ist">
                  <textarea className="input" rows={3} value={form.substrate_display_text}
                    onChange={e => set('substrate_display_text', e.target.value)}
                    placeholder="z. B. Torffreie Bio-Erde speziell für Pelargonien..." />
                </Field>
              )}
            </Section>

            <Section title="Steuer & Versand" defaultOpen={false}>
              <Row>
                <TaxClassPicker value={form.tax_class_id} onChange={(taxId, wcSlug) => setForm(f => ({ ...f, tax_class_id: taxId, tax_class: wcSlug }))} />
                <DeliveryTimePicker value={form.delivery_time} onChange={v => set('delivery_time', v)} />
              </Row>
              <Row cols={1}>
                <Field label="Differenzbesteuerung §25a UStG">
                  <Toggle options={TRISTATE} value={form.differential_taxation} onChange={v => set('differential_taxation', v)} />
                </Field>
              </Row>
            </Section>

            <Section title="Lager" defaultOpen={false}>
              <Row>
                <Field label="Lagerbestand">
                  <input className="input" type="number" min="0" value={form.stock} onChange={e => set('stock', e.target.value)} placeholder="— keine Vorgabe —" />
                </Field>
                <Field label="Niedrig-Bestand Schwellwert">
                  <input className="input" type="number" min="0" value={form.low_stock_threshold} onChange={e => set('low_stock_threshold', e.target.value)} placeholder="— keine Vorgabe —" />
                </Field>
              </Row>
              <Field label={'Nie als „Niedriger Bestand" markieren'}>
                <Toggle options={TRISTATE} value={form.never_low_stock} onChange={v => set('never_low_stock', v)} />
              </Field>
            </Section>

            <Section title="Kurzbeschreibung" defaultOpen={false}>
              <Field full>
                <textarea className="input" rows={3} value={form.short_description}
                  onChange={e => set('short_description', e.target.value)}
                  placeholder="Kurze Produktbeschreibung für Listings..." maxLength={500} />
              </Field>
            </Section>

            <Section title="Produktbeschreibung" defaultOpen={false}>
              <RichTextEditor ref={descRef} value={form.description} onChange={v => set('description', v)}
                placeholder="Ausführliche Produktbeschreibung..." />
            </Section>

            <Section title="Spezifikationen" defaultOpen={false}>
              <div>
                <label className="label">Produktspezifikation</label>
                <div style={{ display: 'flex', gap: 8 }}>
                  <select className="input" value={form.specification_id || ''}
                    onChange={e => set('specification_id', e.target.value)} style={{ flex: 1 }}>
                    <option value="">— keine Vorgabe —</option>
                    {availableSpecs.map(spec => <option key={spec.id} value={spec.id}>{spec.name}</option>)}
                  </select>
                  <button type="button" className="btn-secondary" onClick={() => setShowSpecDialog(true)} style={{ whiteSpace: 'nowrap' }}>+ Neue</button>
                </div>
              </div>
            </Section>

            <Section title="Tags" defaultOpen={false}>
              <TagPool
                onChange={setBulkTagIds}
                gattungId={isArt ? parentGattungId : id}
                gattungName={isArt ? undefined : form.name}
                artId={isArt ? id : null}
                artName={isArt ? form.name : undefined}
              />
              {id ? (
                <button type="button" className="btn-secondary" style={{ marginTop: 10 }}
                  disabled={bulkTagIds.length === 0 || applyingTags}
                  onClick={applyTagsToLinkedProducts}>
                  {applyingTags ? 'Ordne zu…' : `Allen Produkten dieser ${isArt ? 'Art' : 'Gattung'} zuordnen`}
                </button>
              ) : (
                <div style={{ fontSize: 11, color: '#9CA59E', marginTop: 8, fontStyle: 'italic' }}>
                  Erst speichern, dann können ausgewählte Tags allen zugehörigen Produkten zugeordnet werden.
                </div>
              )}
            </Section>

            <Section title="Kategorien" defaultOpen={false}>
              <CategoryAssignPicker initialIds={form.category_ids} onChange={ids => set('category_ids', ids)} />
              <div style={{ fontSize: 11, color: '#9CA59E', marginTop: 8 }}>
                Produkte dieser {isArt ? 'Art' : 'Gattung'} gehören beim nächsten Push automatisch zu den hier gewählten Kategorien (und deren Überkategorien).
              </div>
            </Section>

            <Section title="Pflegehinweise" defaultOpen={false}>
              <Row>
                <Field label="Licht (Bevorzugt)">
                  <select className="input" value={form.care_light} onChange={e => set('care_light', e.target.value)}>
                    {CARE_LIGHT_OPTIONS.map(o => <option key={o.value} value={o.value}>{o.label}</option>)}
                  </select>
                </Field>
                <Field label="Wasser (Bevorzugt)">
                  <select className="input" value={form.care_water} onChange={e => set('care_water', e.target.value)}>
                    {CARE_WATER_OPTIONS.map(o => <option key={o.value} value={o.value}>{o.label}</option>)}
                  </select>
                </Field>
              </Row>
              <Row>
                <Field label="Licht verträgt (von)">
                  <select className="input" value={form.care_light_tolerates_min} onChange={e => set('care_light_tolerates_min', e.target.value)}>
                    {CARE_LIGHT_OPTIONS.map(o => <option key={o.value} value={o.value}>{o.label}</option>)}
                  </select>
                </Field>
                <Field label="Licht verträgt (bis)">
                  <select className="input" value={form.care_light_tolerates_max} onChange={e => set('care_light_tolerates_max', e.target.value)}>
                    {CARE_LIGHT_OPTIONS.map(o => <option key={o.value} value={o.value}>{o.label}</option>)}
                  </select>
                </Field>
              </Row>
              <Row>
                <Field label="Wasser verträgt (von)">
                  <select className="input" value={form.care_water_tolerates_min} onChange={e => set('care_water_tolerates_min', e.target.value)}>
                    {CARE_WATER_OPTIONS.map(o => <option key={o.value} value={o.value}>{o.label}</option>)}
                  </select>
                </Field>
                <Field label="Wasser verträgt (bis)">
                  <select className="input" value={form.care_water_tolerates_max} onChange={e => set('care_water_tolerates_max', e.target.value)}>
                    {CARE_WATER_OPTIONS.map(o => <option key={o.value} value={o.value}>{o.label}</option>)}
                  </select>
                </Field>
              </Row>
              <Field label="Pflegehinweise" full>
                <textarea className="input" rows={3} value={form.care_winter} onChange={e => set('care_winter', e.target.value)} placeholder="z. B. hell und kühl, min. 5 °C" />
              </Field>
              <Row>
                <Field label="Temp. min. (Topf, °C)">
                  <input className="input" type="number" step="1" value={form.care_temp_min} onChange={e => set('care_temp_min', e.target.value)} placeholder="—" />
                </Field>
                <Field label="Temp. max. (Topf, °C)">
                  <input className="input" type="number" step="1" value={form.care_temp_max} onChange={e => set('care_temp_max', e.target.value)} placeholder="—" />
                </Field>
              </Row>
              <Row>
                <Field label="Temp. min. (Ausgepflanzt, °C)">
                  <input className="input" type="number" step="1" value={form.care_temp_ausgepflanzt_min} onChange={e => set('care_temp_ausgepflanzt_min', e.target.value)} placeholder="—" />
                </Field>
                <Field label="Temp. max. (Ausgepflanzt, °C)">
                  <input className="input" type="number" step="1" value={form.care_temp_ausgepflanzt_max} onChange={e => set('care_temp_ausgepflanzt_max', e.target.value)} placeholder="—" />
                </Field>
              </Row>
              <Row>
                <Field label="Düngertyp">
                  <select className="input" value={form.fertilizer_type_choice} onChange={e => set('fertilizer_type_choice', e.target.value)}>
                    <option value="">— keine Vorgabe —</option>
                    {FERTILIZER_TYPES.map(t => <option key={t.value} value={t.value}>{t.label}</option>)}
                  </select>
                </Field>
                <Field label="Düngemenge">
                  <select className="input" value={form.fertilizer_amount} onChange={e => set('fertilizer_amount', e.target.value)}>
                    {CARE_FERTILIZER_OPTIONS.map(o => <option key={o.value} value={o.value}>{o.label}</option>)}
                  </select>
                </Field>
              </Row>
            </Section>

            <div style={{ display: 'flex', justifyContent: 'flex-end', gap: 10, marginTop: 8, paddingTop: 16, borderTop: '1px solid rgba(155,111,208,0.1)' }}>
              <button type="button" className="btn-secondary" onClick={onClose}>Abbrechen</button>
              <button type="submit" className="btn-primary" disabled={saving}>
                {saving ? 'Speichere...' : id ? 'Änderungen speichern' : `${isArt ? 'Art' : 'Gattung'} erstellen`}
              </button>
            </div>
          </form>
        )}
      </div>

      {showSpecDialog && (
        <SpecificationDialog onSave={handleSaveSpecification} onCancel={() => setShowSpecDialog(false)} />
      )}
    </div>
  )
}
