// cart-checkout.jsx — Plantaphilia Warenkorb & Kasse
// Exposes window.CC = { Steps, CartLines, Addons, Summary, MiniCart, MiniHead,
//                       ContactForm, AddressForm, ShippingOptions, PaymentOptions,
//                       ConfirmCard, LoginBar, Empty, SAMPLE_CART, ADDONS, fmt }

(function(){
  const {useState, useEffect, useMemo} = React;
  const e = React.createElement;
  const Icon = window.PA.Icon;

  // ── Currency ─────────────────────────────────────────────────
  const fmt = (n) => '€ ' + n.toFixed(2).replace('.', ',');

  // ── Sample cart (uses real PRODUCTS imagery) ─────────────────
  const SAMPLE_CART = [
    {
      key: 'lord-bute-14',
      name: 'Lord Bute',
      latin: 'Pelargonium × domesticum',
      sku: '2481',
      pot: '14 cm',
      collected: 'Sammlung · Winter 2026',
      priceN: 52,
      qty: 1,
      img: 'assets/banner.png',
      stock: 3,
      badge: 'Rarität',
    },
    {
      key: 'black-velvet-12',
      name: 'Black Velvet',
      latin: "Pelargonium 'Black Velvet'",
      sku: '2734',
      pot: '12 cm',
      collected: 'Sammlung · Frühjahr 2026',
      priceN: 28,
      qty: 2,
      img: 'assets/detail-bloom.png',
      stock: 7,
    },
    {
      key: 'mrs-pollock-12',
      name: 'Mrs. Pollock',
      latin: 'Pelargonium × hortorum',
      sku: '2519',
      pot: '12 cm',
      collected: 'Sammlung · Frühjahr 2026',
      priceN: 24,
      qty: 1,
      img: 'assets/detail-leaf.png',
      stock: 12,
      badge: 'Neu',
    },
  ];

  const ADDONS = [
    {
      key: 'gift',
      name: 'Pflanzenpass · Geschenkverpackung',
      desc: 'Tontopf in Seidenpapier, mit handgeschriebener Karte und gepresstem Blatt aus unserem Garten.',
      priceN: 4.50,
    },
    {
      key: 'care',
      name: 'Pflegezettel · handgeschrieben',
      desc: 'Sortenspezifischer Pflegehinweis, von unserer Gärtnerin von Hand notiert.',
      priceN: 0,
      free: true,
    },
    {
      key: 'fert',
      name: 'Bio-Pelargonien-Dünger · 250 ml',
      desc: 'Mineralisch-organisch, eigene Mischung für Edelpelargonien. Reicht für eine Saison.',
      priceN: 8.90,
    },
  ];

  // ── Step indicator ───────────────────────────────────────────
  function Steps({ current = 'cart' }){
    const order = ['cart','shipping','confirm'];
    const labels = { cart: 'Warenkorb', shipping: 'Versand & Zahlung', confirm: 'Bestätigung' };
    const ci = order.indexOf(current);
    return e('nav', { className: 'cc-steps', 'aria-label': 'Bestellschritte' },
      order.map((k, i) => {
        const state = i < ci ? 'done' : i === ci ? 'active' : '';
        return [
          e('div', { key: k, className: `cc-step ${state}` },
            e('span', { className: 'cc-step-num' }, state === 'done' ? '✓' : (i + 1)),
            e('span', { className: 'cc-step-label' }, labels[k])
          ),
          i < order.length - 1 && e('span', { key: k + '-r', className: 'cc-step-rule' })
        ];
      })
    );
  }

  // ── Cart line items ──────────────────────────────────────────
  function CartLines({ items, onQty, onRemove }){
    return e('div', { className: 'cc-lines' },
      items.map((it) => {
        const total = it.priceN * it.qty;
        const stockClass = it.stock <= 5 ? 'low' : '';
        const stockMsg = it.stock <= 5
          ? `Nur noch ${it.stock} Exemplar${it.stock === 1 ? '' : 'e'} im Schaubeet`
          : 'Versandfertig in 2 Werktagen';
        return e('article', { className: 'cc-line', key: it.key },
          e('div', { className: 'cc-line-img', style: { backgroundImage: `url(${it.img})` } },
            it.badge && e('span', { className: 'cc-line-badge' }, it.badge)
          ),
          e('div', { className: 'cc-line-body' },
            e('h3', { className: 'cc-line-name' }, it.name),
            e('div', { className: 'cc-line-latin' }, it.latin),
            e('div', { className: 'cc-line-meta' },
              e('span', null, `Art.-Nr. ${it.sku}`),
              e('span', { className: 'sep' }),
              e('span', null, `Topf ${it.pot}`),
              e('span', { className: 'sep' }),
              e('span', null, it.collected)
            ),
            e('div', { className: 'cc-line-controls' },
              e('div', { className: 'cc-qty' },
                e('button', {
                  onClick: () => onQty(it.key, Math.max(1, it.qty - 1)),
                  disabled: it.qty <= 1,
                  'aria-label': 'Weniger',
                }, '–'),
                e('span', { className: 'val' }, it.qty),
                e('button', {
                  onClick: () => onQty(it.key, Math.min(it.stock, it.qty + 1)),
                  disabled: it.qty >= it.stock,
                  'aria-label': 'Mehr',
                }, '+')
              ),
              e('button', { className: 'cc-line-link', onClick: () => onRemove(it.key) },
                e(Icon, { name: 'close', size: 12 }), 'Entfernen'
              ),
              e('button', { className: 'cc-line-link' },
                e(Icon, { name: 'heart', size: 12 }), 'Auf Wunschliste'
              )
            ),
            e('div', { className: `cc-line-stock ${stockClass}` }, '— ', stockMsg)
          ),
          e('div', { className: 'cc-line-right' },
            e('div', { className: 'cc-line-price' }, fmt(total)),
            it.qty > 1 && e('div', { className: 'cc-line-unit' }, `${it.qty} × ${fmt(it.priceN)}`)
          )
        );
      })
    );
  }

  // ── Add-ons (gift wrap, care card, fertilizer) ───────────────
  function Addons({ selected, onToggle, note, onNote }){
    return e('div', { className: 'cc-addons' },
      e('div', { className: 'cc-addons-h' }, '· Beilagen aus dem Gewächshaus ·'),
      ADDONS.map((a) => e('div', { className: 'cc-addon', key: a.key },
        e('button', {
          className: 'cc-addon-check' + (selected[a.key] ? ' on' : ''),
          onClick: () => onToggle(a.key),
          'aria-label': a.name,
        }, e(Icon, { name: 'arrow', size: 14 })),
        e('div', { className: 'cc-addon-body', onClick: () => onToggle(a.key) },
          e('div', { className: 'cc-addon-name' }, a.name.split(' · ')[0],
            a.name.includes(' · ') && e(React.Fragment, null, ' · ', e('em', null, a.name.split(' · ').slice(1).join(' · ')))
          ),
          e('div', { className: 'cc-addon-desc' }, a.desc)
        ),
        e('div', { className: `cc-addon-price ${a.free ? 'free' : ''}` },
          a.free ? 'Inklusive' : fmt(a.priceN)
        )
      )),
      e('div', { className: 'cc-note-wrap' },
        e('label', null, 'Notiz an die Gärtnerei'),
        e('textarea', {
          placeholder: 'Optional · Wunschtermin, Pflege-Frage, Widmung für die Karte …',
          value: note, onChange: (ev) => onNote(ev.target.value),
        })
      )
    );
  }

  // ── Order summary card ───────────────────────────────────────
  function Summary({ items, addons = {}, shippingN, voucher, onVoucher, ctaLabel, ctaHref, ctaOnClick, ctaDisabled, showExpress = true, showTrust = true, stamp = 'Tafel · Bestellung' }){
    const subtotal = items.reduce((s, i) => s + i.priceN * i.qty, 0);
    const addonsTotal = ADDONS.reduce((s, a) => s + (addons[a.key] && !a.free ? a.priceN : 0), 0);
    const discountN = voucher ? Math.round(subtotal * 0.10 * 100) / 100 : 0;
    const ship = (typeof shippingN === 'number') ? shippingN : (subtotal >= 80 ? 0 : 4.90);
    const total = subtotal + addonsTotal + ship - discountN;
    const tax = Math.round(total * 19 / 119 * 100) / 100;

    return e('aside', { className: 'cc-summary' },
      e('div', { className: 'cc-summary-stamp' }, stamp),
      e('div', { className: 'cc-summary-eyebrow' }, '· Zwischenrechnung ·'),
      e('h2', { className: 'cc-summary-h' }, 'Ihre ', e('em', null, 'Bestellung')),

      e('div', { className: 'cc-summary-row' },
        e('span', { className: 'lab' }, `Zwischensumme · ${items.reduce((s,i)=>s+i.qty,0)} Position${items.reduce((s,i)=>s+i.qty,0)===1?'':'en'}`),
        e('span', { className: 'val' }, fmt(subtotal))
      ),
      addonsTotal > 0 && e('div', { className: 'cc-summary-row' },
        e('span', { className: 'lab' }, 'Beilagen'),
        e('span', { className: 'val' }, fmt(addonsTotal))
      ),
      e('div', { className: 'cc-summary-row ' + (ship === 0 ? 'free' : '') },
        e('span', { className: 'lab' }, 'Versand · DHL klimaneutral'),
        e('span', { className: 'val' }, ship === 0 ? 'Frei' : fmt(ship))
      ),
      ship > 0 && subtotal < 80 && e('div', { className: 'cc-summary-row muted' },
        e('span', { className: 'lab', style:{fontStyle:'italic', fontFamily:'var(--serif-display)', letterSpacing:0, color:'var(--creme-muted)'} },
          'Noch ', fmt(80 - subtotal), ' bis zum versandkostenfreien Verschicken.'),
        e('span', { className: 'val' })
      ),
      discountN > 0 && e('div', { className: 'cc-summary-row discount' },
        e('span', { className: 'lab' }, `Gutschein · ${voucher.toUpperCase()}`),
        e('span', { className: 'val' }, '– ' + fmt(discountN))
      ),

      e('div', { className: 'cc-summary-divider' }),

      e('div', { className: 'cc-summary-total' },
        e('span', { className: 'lab' }, 'Gesamt'),
        e('span', { className: 'val' }, fmt(total))
      ),
      e('div', { className: 'cc-summary-tax' }, `inkl. ${fmt(tax)} MwSt. (19 %)`),

      ctaLabel && (ctaHref
        ? e('a', { className: 'cc-cta', href: ctaHref }, ctaLabel, e('span', { className: 'arrow' }, '→'))
        : e('button', { className: 'cc-cta', onClick: ctaOnClick, disabled: ctaDisabled },
            ctaLabel, e('span', { className: 'arrow' }, '→'))
      ),

      showExpress && e('div', { className: 'cc-express' },
        e('div', { className: 'cc-express-h' }, 'oder express'),
        e('button', { className: 'cc-express-btn' },
          e('span', { className: 'mark' }, 'P'), 'PayPal'),
        e('button', { className: 'cc-express-btn' },
          e('span', { className: 'mark' }, 'A'), 'Apple Pay')
      ),

      // Voucher field
      onVoucher && e(VoucherField, { value: voucher, onApply: onVoucher }),

      showTrust && e('div', { className: 'cc-trust' },
        e('div', { className: 'cc-trust-item' },
          e(Icon, { name: 'arrow', size: 14 }),
          e('span', null, e('b', { style:{fontStyle:'normal', fontFamily:'var(--sans-body)', fontSize:'10px', letterSpacing:'0.22em', textTransform:'uppercase', color:'var(--creme)'} }, 'Versand frei ab 80 €'),
            e('br'), 'Kuratiert, doppelt gepolstert, in eigener Tontopf-Verpackung.')
        ),
        e('div', { className: 'cc-trust-item' },
          e(Icon, { name: 'arrow', size: 14 }),
          e('span', null, e('b', { style:{fontStyle:'normal', fontFamily:'var(--sans-body)', fontSize:'10px', letterSpacing:'0.22em', textTransform:'uppercase', color:'var(--creme)'} }, 'Pflanzengarantie · 14 Tage'),
            e('br'), 'Sollte etwas nicht in voller Pracht ankommen, schicken wir Ersatz.')
        ),
        e('div', { className: 'cc-trust-item' },
          e(Icon, { name: 'arrow', size: 14 }),
          e('span', null, e('b', { style:{fontStyle:'normal', fontFamily:'var(--sans-body)', fontSize:'10px', letterSpacing:'0.22em', textTransform:'uppercase', color:'var(--creme)'} }, 'Versand Mo bis Mi'),
            e('br'), 'Damit Ihre Pelargonien das Wochenende nicht im Lager verbringen.')
        )
      )
    );
  }

  function VoucherField({ value, onApply }){
    const [v, setV] = useState('');
    const [open, setOpen] = useState(false);
    if (value) {
      return e('div', { className: 'cc-voucher' },
        e('div', { className: 'applied' },
          e('span', null, e('b', null, 'Gutschein'), value.toUpperCase(), ' · −10 %'),
          e('button', { onClick: () => onApply(''), style:{padding:'0 4px', border:0, color:'var(--creme-muted)'} }, '×')
        )
      );
    }
    if (!open) {
      return e('button', {
        className: 'cc-line-link', onClick: () => setOpen(true),
        style: { marginTop: 20, paddingTop: 20, borderTop: '1px solid var(--border-hair)', width:'100%', textAlign:'left', justifyContent:'flex-start' }
      }, '+ Gutschein- oder Geschenkcode einlösen');
    }
    return e('div', { className: 'cc-voucher' },
      e('input', {
        placeholder: 'Gutscheincode',
        value: v, onChange: (ev) => setV(ev.target.value.toUpperCase())
      }),
      e('button', { onClick: () => { if (v) onApply(v); } }, 'Einlösen')
    );
  }

  // ── Mini-cart for checkout summary ───────────────────────────
  function MiniCart({ items }){
    return e('div', { className: 'cc-mini' },
      items.map((it) => e('div', { className: 'cc-mini-row', key: it.key },
        e('div', { className: 'cc-mini-thumb', style: { backgroundImage: `url(${it.img})` } },
          e('span', { className: 'cc-mini-qty' }, it.qty)
        ),
        e('div', null,
          e('div', { className: 'cc-mini-name' }, it.name),
          e('div', { className: 'cc-mini-spec' }, `Topf ${it.pot} · Art. ${it.sku}`)
        ),
        e('div', { className: 'cc-mini-price' }, fmt(it.priceN * it.qty))
      ))
    );
  }

  // ── Minimal checkout header ──────────────────────────────────
  function MiniHead(){
    return e('header', { className: 'cc-minihead' },
      e('a', { className: 'cc-minihead-logo', href: 'Produktseite.html' },
        e('img', { src: 'assets/logo.svg', alt: 'Plantaphilia' })
      ),
      e('div', { className: 'cc-minihead-secure' },
        e(Icon, { name: 'arrow', size: 14 }),
        e('span', null, 'Sichere ', e('b', null, 'Kasse')),
        e('span', { style:{color:'var(--amethyst-dim)'} }, '·'),
        e('span', null, 'TLS · DSGVO')
      )
    );
  }

  // ── Forms ────────────────────────────────────────────────────
  function Field({ label, hint, span = 1, children, full }){
    return e('div', { className: 'cc-field' + (full ? ' col-2' : '') },
      e('label', null, label),
      children,
      hint && e('div', { className: 'cc-field-hint' }, hint)
    );
  }

  function ContactForm({ values, onChange }){
    return e('div', { className: 'cc-form-grid' },
      e(Field, { label: 'E-Mail-Adresse', hint: 'Wir senden Bestellbestätigung & Pflanzen-Bordkarte hierhin.', full: true },
        e('input', { type: 'email', placeholder: 'gartenfreundin@beispiel.de',
          value: values.email, onChange: (ev) => onChange('email', ev.target.value) })
      ),
      e('label', { className: 'cc-checkbox col-2', style:{gridColumn:'1 / -1'} },
        e('input', { type: 'checkbox', checked: !!values.newsletter,
          onChange: (ev) => onChange('newsletter', ev.target.checked) }),
        e('span', { className: 'box' }, e(Icon, { name: 'arrow', size: 12 })),
        e('span', null, 'Vier Briefe im Jahr zur ', e('em', null, 'Jahreszeiten-Pflege'), ' und neuen Sammlerstücken. Jederzeit abbestellbar.')
      )
    );
  }

  function AddressForm({ values, onChange, prefix = 'ship' }){
    const k = (key) => `${prefix}_${key}`;
    return e('div', { className: 'cc-form-grid' },
      e(Field, { label: 'Anrede' },
        e('select', { value: values[k('salutation')], onChange: (ev) => onChange(k('salutation'), ev.target.value) },
          e('option', { value: '' }, 'Bitte wählen'),
          e('option', { value: 'frau' }, 'Frau'),
          e('option', { value: 'herr' }, 'Herr'),
          e('option', { value: 'divers' }, 'Keine Anrede'),
        )
      ),
      e(Field, { label: 'Akademischer Titel (optional)' },
        e('input', { type: 'text', placeholder: 'Dr., Prof. …', value: values[k('title')] || '', onChange: (ev) => onChange(k('title'), ev.target.value) })
      ),
      e(Field, { label: 'Vorname' },
        e('input', { type: 'text', autoComplete: 'given-name', value: values[k('first')], onChange: (ev) => onChange(k('first'), ev.target.value) })
      ),
      e(Field, { label: 'Nachname' },
        e('input', { type: 'text', autoComplete: 'family-name', value: values[k('last')], onChange: (ev) => onChange(k('last'), ev.target.value) })
      ),
      e(Field, { label: 'Straße und Hausnummer', full: true },
        e('input', { type: 'text', autoComplete: 'street-address', placeholder: 'z. B. Friedrichstraße 21',
          value: values[k('street')], onChange: (ev) => onChange(k('street'), ev.target.value) })
      ),
      e(Field, { label: 'Adresszusatz (optional)', full: true },
        e('input', { type: 'text', placeholder: 'Hinterhaus, c/o, Etage …',
          value: values[k('extra')] || '', onChange: (ev) => onChange(k('extra'), ev.target.value) })
      ),
      e(Field, { label: 'Postleitzahl' },
        e('input', { type: 'text', autoComplete: 'postal-code', inputMode:'numeric', maxLength: 5,
          value: values[k('zip')], onChange: (ev) => onChange(k('zip'), ev.target.value) })
      ),
      e(Field, { label: 'Stadt' },
        e('input', { type: 'text', autoComplete: 'address-level2',
          value: values[k('city')], onChange: (ev) => onChange(k('city'), ev.target.value) })
      ),
      e(Field, { label: 'Land', full: true },
        e('select', { value: values[k('country')], onChange: (ev) => onChange(k('country'), ev.target.value) },
          e('option', { value: 'DE' }, 'Deutschland'),
          e('option', { value: 'AT' }, 'Österreich'),
          e('option', { value: 'CH' }, 'Schweiz'),
          e('option', { value: 'NL' }, 'Niederlande'),
          e('option', { value: 'BE' }, 'Belgien'),
          e('option', { value: 'LU' }, 'Luxemburg'),
          e('option', { value: 'DK' }, 'Dänemark'),
          e('option', { value: 'FR' }, 'Frankreich')
        )
      ),
      e(Field, { label: 'Telefon · für Rückfragen zur Lieferung', hint: 'Optional · DHL kontaktiert nur in Ausnahmefällen.', full: true },
        e('input', { type: 'tel', autoComplete: 'tel', placeholder: '+49 …',
          value: values[k('phone')] || '', onChange: (ev) => onChange(k('phone'), ev.target.value) })
      )
    );
  }

  // ── Shipping options ─────────────────────────────────────────
  const SHIPPING = [
    { id: 'standard', name: 'DHL · klimaneutral', desc: 'Versand Mo–Mi · Zustellung in 2–4 Werktagen · doppelt gepolstert', priceN: 4.90, free80: true },
    { id: 'express',  name: 'DHL Express · Pflanzen-Vorrang', desc: 'Versand am nächsten Werktag · für die ungeduldigen Sammler:innen', priceN: 14.90 },
    { id: 'pickup',   name: 'Selbstabholung · Gewächshaus Freiburg', desc: 'Wir reservieren 7 Tage. Voranmeldung per Mail oder Telefon.', priceN: 0, tag: 'Vor Ort' },
  ];

  function ShippingOptions({ selected, onSelect, subtotal }){
    return e('div', { className: 'cc-options' },
      SHIPPING.map((s) => {
        const isFree = (s.free80 && subtotal >= 80) || s.priceN === 0;
        return e('div', { key: s.id, className: 'cc-option' + (selected === s.id ? ' active' : ''),
          onClick: () => onSelect(s.id), role: 'button' },
          e('span', { className: 'cc-option-radio' }),
          e('div', { className: 'cc-option-body' },
            e('div', { className: 'cc-option-name' }, s.name,
              s.tag && e('span', { className: 'tag' }, s.tag)),
            e('div', { className: 'cc-option-desc' }, s.desc)
          ),
          e('div', { className: 'cc-option-price' },
            e('span', { className: 'pr' + (isFree ? ' free' : '') }, isFree ? 'Frei' : fmt(s.priceN)),
            s.free80 && subtotal < 80 && e('span', { className: 'sub' }, 'ab 80 € frei')
          )
        );
      })
    );
  }

  // ── Payment options ──────────────────────────────────────────
  const PAYMENTS = [
    { id: 'sepa', name: 'SEPA-Lastschrift', desc: 'Einzug 3 Werktage nach Versand. Vorausgefülltes Mandat im Konto.', form: 'sepa' },
    { id: 'invoice', name: 'Rechnung', desc: '14 Tage zahlbar nach Erhalt der Ware. Nur für Lieferungen innerhalb Deutschlands.', tag: 'Beliebt', form: null },
    { id: 'paypal', name: 'PayPal', desc: 'Weiterleitung im nächsten Schritt. PayPal-Käuferschutz inklusive.', form: null },
    { id: 'klarna', name: 'Klarna · Sofort oder Ratenkauf', desc: 'Bonitätsprüfung im nächsten Schritt. Auch in 3 zinsfreien Raten möglich.', form: null },
    { id: 'card', name: 'Kreditkarte · Visa, Mastercard, Amex', desc: 'Belastung erst beim Versand der Pflanzen.', form: 'card' },
  ];

  function PaymentOptions({ selected, onSelect, values, onChange }){
    return e('div', { className: 'cc-options' },
      PAYMENTS.map((p) => e('div', { key: p.id, className: 'cc-option' + (selected === p.id ? ' active' : ''),
        onClick: () => onSelect(p.id), role: 'button' },
        e('span', { className: 'cc-option-radio' }),
        e('div', { className: 'cc-option-body' },
          e('div', { className: 'cc-option-name' }, p.name,
            p.tag && e('span', { className: 'tag' }, p.tag)),
          e('div', { className: 'cc-option-desc' }, p.desc),
          selected === p.id && p.form === 'sepa' && e('div', { className: 'cc-option-extra', onClick: (ev) => ev.stopPropagation() },
            e('div', { className: 'cc-payform' },
              e(Field, { label: 'Kontoinhaber:in', full: true },
                e('input', { type: 'text', placeholder: 'Wie auf dem Konto',
                  value: values.sepa_name || '', onChange: (ev) => onChange('sepa_name', ev.target.value) })),
              e(Field, { label: 'IBAN', full: true },
                e('input', { type: 'text', placeholder: 'DE…',
                  value: values.sepa_iban || '', onChange: (ev) => onChange('sepa_iban', ev.target.value) })),
              e('label', { className: 'cc-checkbox col-2', style:{gridColumn:'1 / -1'} },
                e('input', { type: 'checkbox', checked: !!values.sepa_mandate,
                  onChange: (ev) => onChange('sepa_mandate', ev.target.checked) }),
                e('span', { className: 'box' }, e(Icon, { name: 'arrow', size: 12 })),
                e('span', null, 'Ich erteile das ', e('em', null, 'SEPA-Lastschriftmandat'), ' an Plantaphilia GmbH zur einmaligen Belastung.')
              )
            )
          ),
          selected === p.id && p.form === 'card' && e('div', { className: 'cc-option-extra', onClick: (ev) => ev.stopPropagation() },
            e('div', { className: 'cc-payform' },
              e(Field, { label: 'Karten-Nummer', full: true },
                e('input', { type: 'text', inputMode: 'numeric', placeholder: '•••• •••• •••• ••••',
                  value: values.card_num || '', onChange: (ev) => onChange('card_num', ev.target.value) })),
              e(Field, { label: 'Gültig bis (MM/JJ)' },
                e('input', { type: 'text', placeholder: 'MM / JJ',
                  value: values.card_exp || '', onChange: (ev) => onChange('card_exp', ev.target.value) })),
              e(Field, { label: 'Prüfnummer · CVC' },
                e('input', { type: 'text', inputMode: 'numeric', maxLength: 4, placeholder: '•••',
                  value: values.card_cvc || '', onChange: (ev) => onChange('card_cvc', ev.target.value) }))
            )
          )
        ),
        e('div', { className: 'cc-option-price' },
          p.id === 'klarna' && e('span', { className: 'sub' }, 'Ratenkauf möglich'),
          p.id === 'invoice' && e('span', { className: 'sub' }, '14 Tage Ziel'),
          p.id === 'sepa' && e('span', { className: 'sub' }, 'Mandat einmalig'),
        )
      ))
    );
  }

  // ── Final confirm card ───────────────────────────────────────
  function ConfirmCard({ values, onChange }){
    return e('div', { className: 'cc-confirm-card' },
      e('h3', null, 'Letzter Schritt'),
      e('label', { className: 'cc-checkbox' },
        e('input', { type: 'checkbox', checked: !!values.agb, onChange: (ev) => onChange('agb', ev.target.checked) }),
        e('span', { className: 'box' }, e(Icon, { name: 'arrow', size: 12 })),
        e('span', null, 'Ich stimme den ', e('a', { href: '#' }, 'AGB'), ' und der ',
          e('a', { href: '#' }, 'Widerrufsbelehrung'), ' zu. Bei lebenden Pflanzen ist das Widerrufsrecht eingeschränkt — Pflanzen sind ', e('em', null, 'verderbliche Ware'), '.')
      ),
      e('label', { className: 'cc-checkbox' },
        e('input', { type: 'checkbox', checked: !!values.privacy, onChange: (ev) => onChange('privacy', ev.target.checked) }),
        e('span', { className: 'box' }, e(Icon, { name: 'arrow', size: 12 })),
        e('span', null, 'Ich habe die ', e('a', { href: '#' }, 'Datenschutzerklärung'), ' gelesen.')
      )
    );
  }

  // ── Login bar (guest checkout) ───────────────────────────────
  function LoginBar(){
    return e('div', { className: 'cc-loginbar' },
      e('div', { className: 'cc-loginbar-text' },
        e('b', null, 'Schon Sammler:in bei uns?'),
        'Melden Sie sich an, damit Ihre Adresse und Bestellhistorie übernommen werden.'
      ),
      e('a', { href: '#' }, 'Anmelden →')
    );
  }

  // ── Empty cart state ─────────────────────────────────────────
  function Empty(){
    return e('div', { className: 'cc-empty' },
      e('div', { className: 'glyph' }, '✺'),
      e('h2', null, 'Der Warenkorb ist noch leer.'),
      e('p', null, 'Stöbern Sie im Schaubeet — wir haben in dieser Woche siebzehn neue Sorten aus dem Winter-Schnitt aufgenommen.'),
      e('a', { className: 'cc-cta', href: 'Produktseite.html', style: { display:'inline-flex', width:'auto', textDecoration:'none' } },
        'Zum Schaubeet', e('span', { className: 'arrow' }, '→'))
    );
  }

  window.CC = {
    Steps, CartLines, Addons, Summary, MiniCart, MiniHead,
    ContactForm, AddressForm, ShippingOptions, PaymentOptions,
    ConfirmCard, LoginBar, Empty, Field,
    SAMPLE_CART, ADDONS, SHIPPING, PAYMENTS, fmt,
  };
})();
