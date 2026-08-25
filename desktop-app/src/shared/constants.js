export const TAX_CLASSES = [
  { value: 'standard', label: 'Standard (19%)' },
  { value: 'reduced-rate', label: 'Ermäßigt (7%)' },
  { value: 'zero-rate', label: 'Steuerfrei (0%)' },
]

export const CARE_LIGHT_OPTIONS = [
  { value: '', label: '— nicht angegeben —' },
  { value: 'vollsonne', label: 'Vollsonne' },
  { value: 'sonnig', label: 'Sonnig' },
  { value: 'halbschatten', label: 'Halbschatten' },
  { value: 'schatten', label: 'Schatten' },
]

export const CARE_WATER_OPTIONS = [
  { value: '', label: '— nicht angegeben —' },
  { value: 'viel', label: 'Viel (feucht halten)' },
  { value: 'maessig', label: 'Mäßig' },
  { value: 'wenig', label: 'Wenig (trocken halten)' },
]

export const CARE_FERTILIZER_OPTIONS = [
  { value: '', label: '— nicht angegeben —' },
  { value: 'viel', label: 'Viel' },
  { value: 'maessig', label: 'Mäßig' },
  { value: 'wenig', label: 'Wenig' },
]

export const FERTILIZER_TYPES = [
  { value: 'langzeit', label: 'Langzeitdünger' },
  { value: 'kalibetont', label: 'Kalibetonter Dünger' },
  { value: 'ausgeglichen', label: 'Ausgeglichener Dünger' },
]

export const SHIPPING_CLASSES = [
  { value: '', label: '— Standard —' },
  { value: 'klein', label: 'Klein' },
  { value: 'mittel', label: 'Mittel' },
  { value: 'gross', label: 'Groß' },
  { value: 'xxl', label: 'XXL' },
]

export const DELIVERY_TIMES = [
  { value: '', label: '— nicht angegeben —' },
  { value: '1-3', label: '1–3 Werktage' },
  { value: '3-5', label: '3–5 Werktage' },
  { value: '5-7', label: '5–7 Werktage' },
  { value: '1-2w', label: '1–2 Wochen' },
  { value: 'saisonal', label: 'Saisonal / Vorbestellung' },
]

export const PRODUCT_STATUSES = [
  { value: 'publish', label: 'Veröffentlicht' },
  { value: 'draft', label: 'Entwurf' },
  { value: 'private', label: 'Privat' },
]
