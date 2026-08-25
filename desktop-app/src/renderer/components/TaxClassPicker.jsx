import React, { useState, useEffect } from 'react'
import ComboSelect from './ComboSelect.jsx'
import TaxClassDialog from './TaxClassDialog.jsx'
import { Field } from './FormPrimitives.jsx'

// value = local tax_classes.id (not the WC slug) — stays a stable, unambiguous
// identity even before a class has synced to WooCommerce (wc_slug still ''),
// which matters for local-only mode where it may never sync at all.
// onChange(id, wcSlugText) reports both: the id for re-selecting this field,
// the resolved WC slug text for the product's actual tax_class column.
export default function TaxClassPicker({ value, onChange, hint }) {
  const [taxClasses, setTaxClasses] = useState([])
  const [dialogState, setDialogState] = useState(null) // null | { id: number|null }

  const load = () => window.api.getTaxClasses().then(setTaxClasses)
  useEffect(() => { load() }, [])

  const selected = taxClasses.find(t => t.id === value)

  const handleSelect = (idVal) => {
    const id = idVal ? parseInt(idVal, 10) : null
    const row = id ? taxClasses.find(t => t.id === id) : null
    onChange(row ? row.id : null, row ? (row.wc_slug || '') : '')
  }

  return (
    <Field label="Steuerklasse" hint={hint}>
      <div style={{ display: 'flex', gap: 6, minWidth: 0 }}>
        <div style={{ flex: 1, minWidth: 0 }}>
          <ComboSelect
            options={taxClasses.map(t => ({ value: t.id, label: `${t.name} (${t.rate}%)` }))}
            value={value ?? ''}
            onChange={handleSelect}
            placeholder="Ermäßigt (7%)"
          />
        </div>
        <button type="button" className="btn-secondary" style={{ padding: '0 9px' }} title="Neue Steuerklasse anlegen"
          onClick={() => setDialogState({ id: null })}>+</button>
        {selected && (
          <button type="button" className="btn-secondary" style={{ padding: '0 9px' }} title="Steuerklasse bearbeiten"
            onClick={() => setDialogState({ id: selected.id })}>✏️</button>
        )}
      </div>
      {dialogState && (
        <TaxClassDialog
          id={dialogState.id}
          onClose={() => setDialogState(null)}
          onSaved={(saved) => {
            setDialogState(null)
            load()
            onChange(saved.id, saved.wc_slug || '')
          }}
        />
      )}
    </Field>
  )
}
