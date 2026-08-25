import React, { useState, useEffect } from 'react'
import ComboSelect from './ComboSelect.jsx'
import DeliveryTimeDialog from './DeliveryTimeDialog.jsx'
import { Field } from './FormPrimitives.jsx'

// value = the composed label text itself (e.g. "2–5 Werktage") — delivery
// time has no WooCommerce-native field to keep in sync (it's plain product
// meta either way), so unlike TaxClassPicker there's no id/slug split needed.
export default function DeliveryTimePicker({ value, onChange, hint }) {
  const [deliveryTimes, setDeliveryTimes] = useState([])
  const [dialogState, setDialogState] = useState(null) // null | { id: number|null }

  const load = () => window.api.getDeliveryTimes().then(setDeliveryTimes)
  useEffect(() => { load() }, [])

  const selected = deliveryTimes.find(d => d.label === value)

  return (
    <Field label="Lieferzeit" hint={hint}>
      <div style={{ display: 'flex', gap: 6, minWidth: 0 }}>
        <div style={{ flex: 1, minWidth: 0 }}>
          <ComboSelect
            options={deliveryTimes.map(d => ({ value: d.label, label: d.label }))}
            value={value || ''}
            onChange={onChange}
            placeholder="2–5 Werktage"
          />
        </div>
        <button type="button" className="btn-secondary" style={{ padding: '0 9px' }} title="Neue Lieferzeit anlegen"
          onClick={() => setDialogState({ id: null })}>+</button>
        {selected && (
          <button type="button" className="btn-secondary" style={{ padding: '0 9px' }} title="Lieferzeit bearbeiten"
            onClick={() => setDialogState({ id: selected.id })}>✏️</button>
        )}
      </div>
      {dialogState && (
        <DeliveryTimeDialog
          id={dialogState.id}
          onClose={() => setDialogState(null)}
          onSaved={(saved) => {
            setDialogState(null)
            load()
            onChange(saved.label)
          }}
        />
      )}
    </Field>
  )
}
