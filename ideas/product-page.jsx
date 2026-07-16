// product-page.jsx — Plantaphilia Product Detail Page
// Exposes window.PDP = { Crumb, Gallery, Info, Steckbrief, Lore, Reviews, Related, PRODUCTS }

(function(){
  const {useState, useEffect, useMemo} = React;
  const e = React.createElement;
  const Icon = window.PA.Icon;
  const ProductCard = window.PA.ProductCard;

  // ── Data ──────────────────────────────────────────────────────
  const PRODUCTS = {
    'lord-bute': {
      key: 'lord-bute',
      name: 'Lord Bute',
      latin: 'Pelargonium × domesticum',
      subtitle: "'Lord Bute'",
      cultivar: "'Lord Bute' Sweet, 1855",
      sku: 'Art.-Nr. 2481',
      collected: 'Sammlung · Winter 2026',
      collector: 'Aus dem Schaubeet im Mai 2025 vermehrt',
      pots: [
        { size: '12 cm', price: '€ 38,00', priceN: 38, available: true },
        { size: '14 cm', price: '€ 52,00', priceN: 52, available: true },
      ],
      stock: 3,
      lede: 'Tiefdunkler Burgunderton mit lavendelfarbenem Rand — eine viktorianische Edelpelargonie aus der Sammlung Sweet, kultiviert im Halbschatten.',
      images: [
        'assets/banner.png',
        'assets/detail-bloom.png',
        'assets/hero-bloom.png',
        'assets/detail-leaf.png',
      ],
      badges: ['Rarität'],
      care: {
        light: 'Halbschatten · hell',
        lightScale: 2,
        water: 'Mäßig · antrocknen lassen',
        waterScale: 2,
        tempMin: 5,
        tempMax: 28,
        winter: 'Nicht winterhart. Entweder kalt und dunkel ohne gießen — oder kalt und hell mit kaum gießen überwintern. Idealerweise 5–10 °C in einer frostfreien Garage oder im Treppenhaus. Rückschnitt im März, dann langsam an Licht gewöhnen.',
      },
      lore: {
        title: 'Eine viktorianische Erscheinung',
        body: [
          'Im Hochsommer 1854 entdeckte der Gärtner Robert Sweet im Schaubeet von Lord Bute in den Hochlandgärten Schottlands eine Pelargonium-Sämlingsabart von ungewöhnlich tiefdunkler Färbung. Was als Zufall begann, wurde durch sorgfältige Selektion zur Sorte — und zur Auszeichnung der königlichen Gärtnerei.',
          'Diese Edelpelargonie blüht im späten Frühjahr und wieder im Herbst. Die samtigen Petalen sind so dunkel, dass sie im Halbschatten beinahe schwarz wirken; erst das Streiflicht zeigt den weinroten Kern und den lavendelfarbenen Saum. Im Halbschatten kultiviert, in 12-cm-Tontöpfen versandfertig.',
        ],
        pull: '„Lord Bute zählt zu den wenigen Pelargonien, deren Blüten man im Halbdunkel ertastet, bevor man sie sieht."',
      },
      reviews: {
        avg: 4.8, count: 27,
        items: [
          { marks: '★★★★★', quote: 'Wirklich tiefschwarz im Schatten, mit dem lavendelfarbenen Saum, den man nur auf den Fotos der alten Kataloge findet. Drei Wochen nach Eintreffen die ersten Knospen.', by: 'Wiebke H.', city: 'Hamburg', date: '12. Mai 2026', bought: true },
          { marks: '★★★★★', quote: 'Hervorragend verpackt, mit handschriftlichem Pflegezettel. Steht jetzt im Ostfenster und blüht ununterbrochen. Werde im Herbst noch zwei weitere bestellen.', by: 'Friedrich K.', city: 'Leipzig', date: '04. Mai 2026', bought: true },
          { marks: '★★★★☆', quote: 'Etwas kleiner als erwartet, dafür kerngesund — und die Farbe ist tatsächlich so dunkel wie angekündigt. Bin sehr zufrieden.', by: 'Anneliese R.', city: 'Freiburg', date: '28. April 2026', bought: true },
          { marks: '★★★★★', quote: 'Wunderschöne Pflanze, sorgfältig kultiviert. Die Lieferung kam mit einem handgeschriebenen Pflegezettel — eine schöne Geste, die man heute kaum noch findet.', by: 'Dorothea S.', city: 'Berlin', date: '15. April 2026', bought: true },
          { marks: '★★★★☆', quote: 'Lord Bute ist tatsächlich der Star meiner Pelargonien-Sammlung geworden. Im Halbschatten genau so blühfreudig wie versprochen. Ein Stern Abzug nur, weil ich Versand bei einer 5°C-Nacht riskant fand.', by: 'Bernhard O.', city: 'Stuttgart', date: '02. April 2026', bought: true },
          { marks: '★★★★★', quote: 'Seit drei Jahren bestelle ich hier — diese Sorte ist eine der besten. Die Mutterpflanze blüht inzwischen üppig im großen Topf.', by: 'Hildegard P.', city: 'Dresden', date: '18. März 2026', bought: true },
          { marks: '★★★★★', quote: 'Die Beschreibung trifft genau zu. Die Petalen wirken im Streiflicht fast samtig.', by: 'Konstantin V.', city: 'Hannover', date: '01. März 2026', bought: true },
        ]
      },
      related: [
        { name:"Black Velvet",       latin:"Pelargonium 'Black Velvet'", price:"€ 28,00", priceN:28, img:"assets/banner.png" },
        { name:"Mrs. Pollock",       latin:"Pelargonium × hortorum", price:"€ 24,00", priceN:24, img:"assets/detail-bloom.png", badge:"Neu" },
        { name:"Attar of Roses",     latin:"Pelargonium capitatum", price:"€ 22,00", priceN:22, img:"assets/hero-bloom.png" },
        { name:"Crystal Palace Gem", latin:"Pelargonium × hortorum", price:"€ 32,00", priceN:32, img:"assets/banner.png", sold:true, badge:"Ausverkauft" },
      ]
    },
    'black-velvet': {
      key: 'black-velvet',
      name: 'Black Velvet',
      latin: 'Pelargonium × hortorum',
      subtitle: "'Black Velvet'",
      cultivar: "'Black Velvet Red' Goldsmith, 2002",
      sku: 'Art.-Nr. 2734',
      collected: 'Sammlung · Frühjahr 2026',
      collector: 'Stecklingsvermehrt aus Mutterpflanze 2018',
      pots: [
        { size: '12 cm', price: '€ 28,00', priceN: 28, available: true },
        { size: '14 cm', price: '€ 38,00', priceN: 38, available: true },
      ],
      stock: 7,
      lede: 'Kohleschwarzes Laub mit einem feurig roten Blütenkranz — eine moderne Zonale, geschaffen für den theatralischen Kontrast.',
      images: [
        'assets/detail-bloom.png',
        'assets/banner.png',
        'assets/detail-leaf.png',
        'assets/hero-bloom.png',
      ],
      badges: [],
      care: {
        light: 'Volle Sonne · hell',
        lightScale: 4,
        water: 'Regelmäßig · nicht stauend',
        waterScale: 3,
        tempMin: 7,
        tempMax: 30,
        winter: 'Frostfrei überwintern bei 7–12 °C. Im hellen Stand gießen Sie sparsam; im dunklen kalten Stand fast trocken halten. Pflanze treibt im Februar wieder an — dann Substrat erneuern und einkürzen.',
      },
      lore: {
        title: 'Schwarzes Laub, theatralisches Rot',
        body: [
          'Die Goldsmith-Züchtung von 2002 brachte erstmals tiefschwarzes Pelargonium-Laub mit verlässlicher Sortenkonstanz hervor. Black Velvet ist die Zonale-Variante davon — kompakter Wuchs, üppige Doldenblüten in feurigem Rot.',
          'Wir kultivieren unsere Bestände aus Stecklingen einer einzigen Mutterpflanze aus dem Jahr 2018; dadurch bleiben Habitus und Färbung berechenbar. Versandfertig in 12-cm-Tontöpfen.',
        ],
        pull: '„Schwarz, das eigentlich Tannengrün ist, mit Rot, das eigentlich Karmesin ist."',
      },
      reviews: {
        avg: 4.7, count: 41,
        items: [
          { marks: '★★★★★', quote: 'Auf dem Südbalkon eine Sensation — fast wie ein dunkler Edelstein. Blüht seit Mai durchgängig.', by: 'Margarethe L.', city: 'Wien' },
          { marks: '★★★★★', quote: 'Ich habe schon mehrere Black-Velvet-Sämlinge gehabt; dieser ist tatsächlich der dunkelste. Sehr zu empfehlen.', by: 'Hannes B.', city: 'München' },
          { marks: '★★★★☆', quote: 'Lieferung war perfekt verpackt, Pflanze gesund. Etwas Geduld nötig — sie wuchs erst nach drei Wochen sichtbar.', by: 'Karoline F.', city: 'Bremen' },
        ]
      },
      related: [
        { name:"Lord Bute",          latin:"Pelargonium × domesticum", price:"€ 38,00", priceN:38, img:"assets/banner.png", badge:"Rarität" },
        { name:"Mrs. Pollock",       latin:"Pelargonium × hortorum", price:"€ 24,00", priceN:24, img:"assets/detail-bloom.png", badge:"Neu" },
        { name:"Attar of Roses",     latin:"Pelargonium capitatum", price:"€ 22,00", priceN:22, img:"assets/hero-bloom.png" },
        { name:"Lemon Fancy",        latin:"Pelargonium crispum",     price:"€ 19,00", priceN:19, img:"assets/detail-leaf.png" },
      ]
    },
    'mrs-pollock': {
      key: 'mrs-pollock',
      name: 'Mrs. Pollock',
      latin: 'Pelargonium × hortorum',
      subtitle: "'Mrs. Pollock'",
      cultivar: "'Mrs. Pollock' Grieve, 1858",
      sku: 'Art.-Nr. 2519',
      collected: 'Sammlung · Frühjahr 2026',
      collector: 'Aus historischen Beständen, Kew Gardens',
      pots: [
        { size: '12 cm', price: '€ 24,00', priceN: 24, available: true },
        { size: '14 cm', price: '€ 34,00', priceN: 34, available: false },
      ],
      stock: 12,
      lede: 'Eine viktorianische Goldblattpelargonie mit dreifarbiger Zonenzeichnung — Bronze, Gelb und Grün auf einer einzigen Blattscheibe.',
      images: [
        'assets/detail-leaf.png',
        'assets/banner.png',
        'assets/hero-bloom.png',
        'assets/detail-bloom.png',
      ],
      badges: ['Neu'],
      care: {
        light: 'Hell · gefiltert',
        lightScale: 3,
        water: 'Sparsam · stets antrocknen',
        waterScale: 1,
        tempMin: 8,
        tempMax: 26,
        winter: 'Hell und kühl überwintern, ideal 10 °C. Bei vollkommen dunklem Stand verlieren die Blätter ihre Zeichnung — daher dunkle Überwinterung möglich, aber nicht ideal. Sehr sparsam gießen.',
      },
      lore: {
        title: 'Drei Farben auf einem Blatt',
        body: [
          'Mrs. Pollock wurde 1858 von Peter Grieve im Auftrag von Kew Gardens vorgestellt und galt während der gesamten viktorianischen Epoche als Schaustück. Die Blattzeichnung — Bronzeband, gelber Rand, grüner Kern — ist ein genetisches Mosaik, das sich nur durch Stecklingsvermehrung verlässlich erhalten lässt.',
          'Aus den historischen Beständen von Kew, behutsam an europäische Bedingungen angepasst.',
        ],
        pull: '„Eine Pflanze, deren Reiz nicht in der Blüte, sondern im Blatt liegt."',
      },
      reviews: {
        avg: 4.9, count: 18,
        items: [
          { marks: '★★★★★', quote: 'Die Blattzeichnung ist atemberaubend — wie ein Aquarell. Perfekt für die helle Fensterbank.', by: 'Sabine M.', city: 'Köln' },
          { marks: '★★★★★', quote: 'Eine echte Rarität, sehr sorgfältig kultiviert. Ich werde sicher weitere Sorten bestellen.', by: 'Theresa W.', city: 'Zürich' },
          { marks: '★★★★★', quote: 'Hat sich nach drei Monaten zu einem kompakten Buschwerk entwickelt. Sehr empfehlenswert.', by: 'Bernhard O.', city: 'Stuttgart' },
        ]
      },
      related: [
        { name:"Lord Bute",          latin:"Pelargonium × domesticum", price:"€ 38,00", priceN:38, img:"assets/banner.png", badge:"Rarität" },
        { name:"Black Velvet",       latin:"Pelargonium 'Black Velvet'", price:"€ 28,00", priceN:28, img:"assets/detail-bloom.png" },
        { name:"Attar of Roses",     latin:"Pelargonium capitatum", price:"€ 22,00", priceN:22, img:"assets/hero-bloom.png" },
        { name:"Crystal Palace Gem", latin:"Pelargonium × hortorum", price:"€ 32,00", priceN:32, img:"assets/banner.png", sold:true, badge:"Ausverkauft" },
      ]
    }
  };

  // ── Breadcrumb ───────────────────────────────────────────────
  function Crumb({ name }){
    return e('nav', { className: 'pdp-crumb' },
      e('a', { href: '#' }, 'Home'),
      e('span', { className: 'sep' }, '·'),
      e('a', { href: '#' }, 'Shop'),
      e('span', { className: 'sep' }, '·'),
      e('a', { href: '#' }, 'Edelpelargonien'),
      e('span', { className: 'sep' }, '·'),
      e('span', { className: 'cur' }, name)
    );
  }

  // ── Gallery ───────────────────────────────────────────────────
  function Gallery({ product, layout='vertical' }){
    const [active, setActive] = useState(0);
    const [zoomed, setZoomed] = useState(false);
    useEffect(() => { setActive(0); setZoomed(false); }, [product.key]);

    const main = product.images[active];

    if (layout === 'grid') {
      return e('div', { className: 'pdp-gallery', 'data-layout': 'grid' },
        product.images.slice(0,4).map((img, i) => e('div', {
          key: i,
          className: 'pdp-main-img',
          style: { aspectRatio: 1 }
        },
          e('div', { className: 'pdp-main-img-bg', style: { backgroundImage: `url(${img})` } }),
          i === 0 && product.badges.length > 0 && e('div', { className: 'pdp-badge-stack' },
            product.badges.map(b => e('span', { key: b, className: 'pa-badge' }, b))
          )
        ))
      );
    }

    const thumbs = e('div', { className: 'pdp-thumbs' },
      product.images.map((img, i) => e('button', {
        key: i,
        className: 'pdp-thumb' + (i === active ? ' active' : ''),
        style: { backgroundImage: `url(${img})` },
        onClick: () => { setActive(i); setZoomed(false); },
        'aria-label': `Bild ${i+1}`,
      }))
    );

    const main_img = e('div', {
      className: 'pdp-main-img' + (zoomed ? ' zoomed' : ''),
      onClick: () => setZoomed(z => !z),
    },
      e('div', { className: 'pdp-main-img-bg', style: { backgroundImage: `url(${main})` } }),
      product.badges.length > 0 && e('div', { className: 'pdp-badge-stack' },
        product.badges.map(b => e('span', { key: b, className: 'pa-badge' }, b))
      ),
      e('div', { className: 'pdp-zoom-hint' }, zoomed ? '— Klick zum Verkleinern' : '+ Klick zum Vergrößern')
    );

    return e('div', { className: 'pdp-gallery', 'data-layout': layout },
      layout === 'horizontal' ? main_img : thumbs,
      layout === 'horizontal' ? thumbs : main_img,
    );
  }

  // ── Buy-side info column ─────────────────────────────────────
  function Info({ product, accent }){
    const [potIdx, setPotIdx] = useState(0);
    const [qty, setQty] = useState(1);
    const [added, setAdded] = useState(false);
    useEffect(() => { setPotIdx(0); setQty(1); setAdded(false); }, [product.key]);
    const pot = product.pots[potIdx];

    const stockClass = product.stock === 0 ? 'out' : product.stock <= 5 ? 'low' : '';
    const stockText = product.stock === 0
      ? 'Ausverkauft'
      : product.stock <= 5
        ? `Nur noch ${product.stock} Stück`
        : 'Auf Lager';

    function handleAdd(){
      setAdded(true);
      window.dispatchEvent(new CustomEvent('pdp-add-to-cart', { detail: {
        name: product.name, latin: product.latin,
        price: pot.price, priceN: pot.priceN, img: product.images[0],
        pot: pot.size, qty,
      }}));
      setTimeout(() => setAdded(false), 1800);
    }

    return e('div', { className: 'pdp-info' },
      e('div', { className: 'pdp-eyebrow' }, product.collected),
      e('h1', { className: 'pdp-title' },
        product.name,
        e('em', null, product.latin)
      ),
      e('div', { className: 'pdp-meta-row' },
        e('span', null, product.sku),
        e('span', { className: 'sep' }),
        e('span', null, `Topf ${pot.size}`),
        e('span', { className: 'sep' }),
        e('span', null, 'Versandfertig')
      ),
      e('div', { className: 'pdp-price-row' },
        e('span', { className: 'pdp-price' }, pot.price),
        e('span', { className: 'pdp-price-tax' }, 'inkl. MwSt. · zzgl. Versand')
      ),
      e('div', { className: `pdp-stock ${stockClass}` },
        e('span', { className: 'dot' }),
        e('span', null, stockText)
      ),
      e('p', { className: 'pdp-lede' }, product.lede),

      e('div', null,
        e('div', { className: 'pdp-opt-label' }, 'Topfgröße ', e('span', { className: 'spec' }, pot.size)),
        e('div', { className: 'pdp-pots' },
          product.pots.map((p, i) => e('button', {
            key: p.size,
            className: 'pdp-pot' + (i === potIdx ? ' active' : ''),
            onClick: () => p.available && setPotIdx(i),
            disabled: !p.available,
            style: !p.available ? { opacity: 0.45, cursor: 'not-allowed' } : null,
          },
            e('span', { className: 'pot-size' }, `Topf ${p.size}`),
            e('span', { className: 'pot-price' }, p.available ? p.price : '— ausverkauft')
          ))
        )
      ),

      e('div', { className: 'pdp-buy-row' },
        e('div', { className: 'pdp-qty' },
          e('button', { onClick: () => setQty(q => Math.max(1, q-1)) }, '–'),
          e('span', { className: 'val' }, qty),
          e('button', { onClick: () => setQty(q => Math.min(product.stock, q+1)) }, '+')
        ),
        e('button', {
          className: 'pdp-cta' + (added ? ' added' : ''),
          onClick: handleAdd,
          disabled: product.stock === 0,
        },
          e('span', null, added ? 'Hinzugefügt ✓' : 'In den Warenkorb'),
          !added && e('span', { className: 'arrow' }, '→')
        )
      ),

      e('div', { className: 'pdp-aux' },
        e('span', null, e(Icon, { name: 'arrow', size: 14 }), 'Versand 4,90 €'),
        e('span', null, e(Icon, { name: 'heart', size: 14 }), 'Auf Wunschliste'),
        e('span', null, e(Icon, { name: 'user', size: 14 }), 'Pflegeberatung inkl.')
      )
    );
  }

  // ── Pflege-Steckbrief — Plakette (default) ───────────────────
  function SteckbriefPlakette({ product }){
    const c = product.care;
    return e('div', { className: 'pdp-steckbrief-wrap' },
      e('div', { className: 'pdp-steckbrief-heading' },
        e('span', { className: 'pdp-steckbrief-eyebrow' }, '· Pflege-Steckbrief ·'),
        e('h2', null, 'Im Schatten kultivieren')
      ),
      e('div', { className: 'pdp-plaque' },
        e('span', { className: 'corner-tr' }),
        e('span', { className: 'corner-bl' }),
        e('div', { className: 'pdp-plaque-title' },
          e('b', null, 'Tafel · I')
        ),

        e('div', { className: 'pdp-plaque-grid' },
          e('div', { className: 'pdp-plaque-col' },
            e('div', { className: 'pdp-field' },
              e('span', { className: 'label' }, 'Licht'),
              e('span', { className: 'value' }, c.light)
            ),
            e('div', { className: 'pdp-field' },
              e('span', { className: 'label' }, 'Temp. min'),
              e('span', { className: 'value' }, '+', c.tempMin, e('span', { className: 'unit' }, '°C'))
            )
          ),
          e('div', { className: 'vdiv' }),
          e('div', { className: 'pdp-plaque-col' },
            e('div', { className: 'pdp-field' },
              e('span', { className: 'label' }, 'Wasser'),
              e('span', { className: 'value' }, c.water)
            ),
            e('div', { className: 'pdp-field' },
              e('span', { className: 'label' }, 'Temp. max'),
              e('span', { className: 'value' }, '+', c.tempMax, e('span', { className: 'unit' }, '°C'))
            )
          )
        ),

        e('div', { className: 'pdp-plaque-wide' },
          e('div', { className: 'label' }, 'Überwinterung'),
          e('p', { className: 'value' }, c.winter)
        ),

        e('div', { className: 'pdp-plaque-foot' },
          e('span', null, 'Sub specimine — Plantaphilia'),
          e('span', { className: 'sigil' }, 'Sammlung № ', product.sku.replace('Art.-Nr. ',''))
        )
      )
    );
  }

  // ── Pflege-Steckbrief — Icon-Skalen ──────────────────────────
  // Light scale: 4 stages, left→right increasing brightness.
  const LIGHT_STAGES = ['Schatten', 'Halbschatten', 'Sonne', 'Vollsonne'];
  // Water scale: 3 stages, left→right increasing water.
  const WATER_STAGES = ['Wenig', 'Mäßig', 'Reichlich'];

  function SteckbriefIcons({ product }){
    const c = product.care;

    function renderScale(stages, active, kind){
      return e('div', { className: 'pdp-scale-block' },
        e('div', { className: 'pdp-scale' },
          stages.map((_, i) => e('span', {
            key: i,
            className: 'pip' + (i + 1 <= active ? ' on' : '') + (kind === 'water' ? ' water' : '')
          }))
        ),
        e('div', { className: 'pdp-scale-ends' },
          e('span', null, stages[0]),
          e('span', null, stages[stages.length - 1])
        )
      );
    }

    // Temperature bar 0–35°C
    const min = 0, max = 35;
    const leftPct = ((c.tempMin - min)/(max - min)) * 100;
    const widthPct = ((c.tempMax - c.tempMin)/(max - min)) * 100;

    return e('div', { className: 'pdp-steckbrief-wrap' },
      e('div', { className: 'pdp-steckbrief-heading' },
        e('span', { className: 'pdp-steckbrief-eyebrow' }, '· Pflege-Steckbrief ·'),
        e('h2', null, 'Im Schatten kultivieren')
      ),
      e('div', { className: 'pdp-iconscale' },
        e('div', { className: 'pdp-iconscale-grid' },
          e('div', { className: 'pdp-iconscale-cell' },
            e('div', { className: 'label' }, 'Licht'),
            e('div', { className: 'value' }, c.light),
            renderScale(LIGHT_STAGES, c.lightScale, 'light')
          ),
          e('div', { className: 'pdp-iconscale-cell' },
            e('div', { className: 'label' }, 'Wasser'),
            e('div', { className: 'value' }, c.water),
            renderScale(WATER_STAGES, c.waterScale, 'water')
          ),
          e('div', { className: 'pdp-iconscale-cell', style: { gridColumn: '1 / -1' } },
            e('div', { className: 'label' }, `Temperatur · ${c.tempMin}°C — ${c.tempMax}°C`),
            e('div', { className: 'pdp-tempbar' },
              e('div', { className: 'pdp-tempbar-track' },
                e('div', {
                  className: 'pdp-tempbar-fill',
                  style: { left: `${leftPct}%`, width: `${widthPct}%` }
                })
              ),
              e('div', { className: 'pdp-tempbar-labels' },
                e('span', null, '0°C'),
                e('span', null, 'Minimum ', e('b', null, `+${c.tempMin}°C`)),
                e('span', null, 'Maximum ', e('b', null, `+${c.tempMax}°C`)),
                e('span', null, '35°C')
              )
            )
          ),
          e('div', { className: 'pdp-iconscale-cell pdp-iconscale-wide' },
            e('div', { className: 'label' }, 'Überwinterung'),
            e('p', { className: 'value' }, c.winter)
          )
        )
      )
    );
  }

  // ── Pflege-Steckbrief — Tabelle ──────────────────────────────
  function SteckbriefTabelle({ product }){
    const c = product.care;
    return e('div', { className: 'pdp-steckbrief-wrap' },
      e('div', { className: 'pdp-steckbrief-heading' },
        e('span', { className: 'pdp-steckbrief-eyebrow' }, '· Pflege-Steckbrief ·'),
        e('h2', null, 'Im Schatten kultivieren')
      ),
      e('div', { className: 'pdp-table-wrap' },
        e('div', { className: 'pdp-table-h' },
          e('span', { className: 'nr' }, 'Datenblatt № ', product.sku.replace('Art.-Nr. ','')),
          e('span', { className: 'title' }, 'Kultivierung · Halbschatten'),
          e('span', { className: 'nr' }, 'Hortus Plantaphilia')
        ),
        e('div', { className: 'pdp-table' },
          e('div', { className: 'pdp-table-row' },
            e('span', { className: 'label' }, 'Licht'),
            e('span', { className: 'value' }, c.light)
          ),
          e('div', { className: 'pdp-table-row' },
            e('span', { className: 'label' }, 'Wasser'),
            e('span', { className: 'value' }, c.water)
          ),
          e('div', { className: 'pdp-table-row' },
            e('span', { className: 'label' }, 'Temperatur · Minimum'),
            e('span', { className: 'value' }, `+${c.tempMin} °C`)
          ),
          e('div', { className: 'pdp-table-row' },
            e('span', { className: 'label' }, 'Temperatur · Maximum'),
            e('span', { className: 'value' }, `+${c.tempMax} °C`)
          ),
          e('div', { className: 'pdp-table-row wide' },
            e('span', { className: 'label' }, 'Überwinterung'),
            e('span', { className: 'value' }, c.winter)
          )
        )
      )
    );
  }

  function Steckbrief({ product, variant }){
    if (variant === 'icons') return e(SteckbriefIcons, { product });
    if (variant === 'tabelle') return e(SteckbriefTabelle, { product });
    return e(SteckbriefPlakette, { product });
  }

  // ── Lore ─────────────────────────────────────────────────────
  function Lore({ product }){
    const l = product.lore;
    return e('section', { className: 'pdp-lore' },
      e('div', { className: 'pdp-lore-inner' },
        e('div', { className: 'pdp-lore-body' },
          e('div', { className: 'pdp-lore-eyebrow-inline' }, '· Eine Notiz zur Sorte ·'),
          e('h2', null, l.title.split(' ').slice(0,-1).join(' '), ' ', e('em', null, l.title.split(' ').slice(-1)[0])),
          e('p', { className: 'drop' }, l.body[0]),
          e('blockquote', { className: 'pdp-lore-pull' }, l.pull),
          l.body[1] && e('p', null, l.body[1])
        )
      )
    );
  }

  // ── Reviews ──────────────────────────────────────────────────
  // Account info pulled from the user's session — never asked in the form.
  const ACCOUNT = { name: 'Friedrich K.', city: 'Leipzig', orderDate: '14. Februar 2026' };

  function ReviewForm({ account, onSubmit }){
    const [rating, setRating] = useState(0);
    const [hover, setHover] = useState(0);
    const [text, setText] = useState('');
    const [submitted, setSubmitted] = useState(false);

    if (submitted) {
      return e('div', { className: 'pdp-review-form sent' },
        e('div', { className: 'pdp-review-form-marks' }, '·'),
        e('p', { className: 'pdp-review-thanks' },
          'Vielen Dank \u2014 Ihre Bewertung wurde \u00fcbermittelt und erscheint nach kurzer Sichtung.'
        )
      );
    }

    function submit(ev){
      ev.preventDefault();
      if (rating === 0 || !text.trim()) return;
      onSubmit && onSubmit({ rating, text });
      setSubmitted(true);
    }

    return e('form', { className: 'pdp-review-form', onSubmit: submit },
      e('div', { className: 'pdp-review-form-head' },
        e('div', { className: 'pdp-review-form-head-l' },
          e('span', { className: 'pdp-review-form-eyebrow' }, 'Ihre Bewertung'),
          e('span', { className: 'pdp-review-form-as' },
            'Wird ver\u00f6ffentlicht als ',
            e('b', null, account.name),
            ' \u00b7 ',
            e('span', { className: 'pdp-review-form-city' }, account.city)
          )
        ),
        e('span', { className: 'pdp-review-form-meta' }, `Bestellung vom ${account.orderDate}`)
      ),
      e('div', { className: 'pdp-review-form-stars' },
        [1,2,3,4,5].map((n) => e('button', {
          key: n, type: 'button',
          className: 'star' + ((hover || rating) >= n ? ' on' : ''),
          onMouseEnter: () => setHover(n),
          onMouseLeave: () => setHover(0),
          onClick: () => setRating(n),
          'aria-label': `${n} Stern${n>1?'e':''}`,
        }, '\u2605')),
        e('span', { className: 'pdp-review-form-hint' },
          rating === 0 ? 'Bitte Sterne w\u00e4hlen' : `${rating} von 5`
        )
      ),
      e('textarea', {
        className: 'pdp-review-form-text',
        placeholder: 'Wie hat sich Ihre Pflanze entwickelt? Was sollten andere Sammler:innen wissen?',
        value: text, onChange: (ev) => setText(ev.target.value),
        rows: 4, required: true,
      }),
      e('div', { className: 'pdp-review-form-foot' },
        e('span', { className: 'pdp-review-form-legal' },
          'Mit dem Absenden best\u00e4tigen Sie unsere ',
          e('a', { href: '#' }, 'Bewertungsrichtlinien'),
          '.'
        ),
        e('button', {
          type: 'submit', className: 'pdp-cta pdp-review-form-submit',
          disabled: rating === 0 || !text.trim(),
        }, 'Bewertung absenden', e('span', { className: 'arrow' }, '\u2192'))
      )
    );
  }

  function ReviewWriteBlock({ auth }){
    if (auth === 'buyer') {
      return e('div', { className: 'pdp-write-wrap' },
        e('div', { className: 'pdp-write-banner pdp-write-banner-buyer' },
          e('div', { className: 'dot' }),
          e('div', { className: 'pdp-write-banner-text' },
            e('b', null, 'Sie haben dieses Sammlerst\u00fcck gekauft.'),
            e('span', null, 'Teilen Sie Ihre Erfahrung mit der Community.')
          )
        ),
        e(ReviewForm, { account: ACCOUNT })
      );
    }
    if (auth === 'user') {
      return e('div', { className: 'pdp-write-wrap' },
        e('div', { className: 'pdp-write-banner pdp-write-banner-user' },
          e('div', { className: 'pdp-write-banner-text' },
            e('b', null, 'Bewertungen nur f\u00fcr K\u00e4ufer:innen.'),
            e('span', null, 'Sie haben diese Sorte (noch) nicht erworben. Nach dem Kauf k\u00f6nnen Sie hier eine Bewertung hinterlassen.')
          ),
          e('a', { className: 'pdp-write-banner-cta', href: '#' }, 'Zum Sammlerstück \u2192')
        )
      );
    }
    // guest
    return e('div', { className: 'pdp-write-wrap' },
      e('div', { className: 'pdp-write-banner pdp-write-banner-guest' },
        e('div', { className: 'pdp-write-banner-text' },
          e('b', null, 'Schon einmal bestellt?'),
          e('span', null, 'Melden Sie sich an, um eine Bewertung zu hinterlassen.')
        ),
        e('div', { className: 'pdp-write-banner-actions' },
          e('a', { className: 'pdp-write-banner-cta secondary', href: '#' }, 'Anmelden'),
          e('a', { className: 'pdp-write-banner-cta', href: '#' }, 'Konto erstellen')
        )
      )
    );
  }

  function Reviews({ product, auth='guest' }){
    const r = product.reviews;
    const PAGE = 4;
    const [shown, setShown] = useState(PAGE);
    useEffect(() => { setShown(PAGE); }, [product.key]);
    const items = r.items.slice(0, shown);
    const hasMore = shown < r.items.length;

    return e('section', { className: 'pdp-reviews' },
      e('div', { className: 'pdp-reviews-inner' },
        e('header', { className: 'pdp-reviews-head' },
          e('div', { className: 'pdp-reviews-head-l' },
            e('div', { className: 'eyebrow' }, 'Sammlerstimmen'),
            e('h2', null, 'Bewertungen')
          ),
          e('div', { className: 'pdp-reviews-head-r' },
            e('div', { className: 'pdp-stars' },
              e('span', { className: 'marks' }, '\u2605\u2605\u2605\u2605\u2605'),
              e('span', { className: 'num' }, r.avg.toFixed(1)),
              e('span', { className: 'sep' }, '·'),
              e('span', { className: 'count' }, `${r.count} Bewertungen`)
            )
          )
        ),

        e(ReviewWriteBlock, { auth }),

        e('ol', { className: 'pdp-review-list' },
          items.map((it, i) => e('li', { key: i, className: 'pdp-review-row' },
            e('div', { className: 'pdp-review-row-marks' },
              e('span', { className: 'marks' }, it.marks),
              it.bought && e('span', { className: 'verified' },
                e(Icon, { name: 'arrow', size: 12 }), 'Verifizierter Kauf'
              )
            ),
            e('p', { className: 'pdp-review-row-quote' }, '\u201E', it.quote, '\u201C'),
            e('div', { className: 'pdp-review-row-by' },
              e('span', { className: 'pdp-review-row-name' }, it.by),
              e('span', { className: 'pdp-review-row-city' }, it.city),
              e('span', { className: 'pdp-review-row-date' }, it.date)
            )
          ))
        ),

        hasMore && e('div', { className: 'pdp-reviews-more' },
          e('button', {
            className: 'pdp-more-btn',
            onClick: () => setShown(s => Math.min(s + PAGE, r.items.length)),
          },
            e('span', null, `Mehr anzeigen — ${r.items.length - shown} weitere`),
            e('span', { className: 'arrow' }, '\u2193')
          )
        )
      )
    );
  }

  // ── Related ──────────────────────────────────────────────────
  function Related({ product, onAdd }){
    return e('section', { className: 'pdp-related' },
      e('div', { className: 'pdp-related-head' },
        e('h2', null, 'Im selben ', e('em', null, 'Gewächshaus')),
        e('a', { href: '#' }, 'Alle Pelargonien', e(Icon, { name: 'arrow', size: 14 }))
      ),
      e('div', { className: 'pdp-related-grid' },
        product.related.map((p, i) => e(ProductCard, {
          key: i, p, onAdd, onOpen: () => {}
        }))
      )
    );
  }

  window.PDP = { Crumb, Gallery, Info, Steckbrief, Lore, Reviews, Related, PRODUCTS };
})();
