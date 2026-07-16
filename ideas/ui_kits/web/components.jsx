(function(){
  const {useState} = React;
  const e = React.createElement;

  function Icon({name, size=16}){
    const s = size;
    const paths = {
      user: e('g',{},e('circle',{cx:12,cy:8,r:4}),e('path',{d:'M4 21c0-4.5 3.5-8 8-8s8 3.5 8 8'})),
      cart: e('g',{},e('circle',{cx:9,cy:20,r:1.5}),e('circle',{cx:18,cy:20,r:1.5}),e('path',{d:'M3 4h3l2.7 11.5a2 2 0 0 0 2 1.5h7.6a2 2 0 0 0 2-1.5L22 8H6'})),
      search: e('g',{},e('circle',{cx:11,cy:11,r:7}),e('path',{d:'m20 20-3.5-3.5'})),
      heart: e('path',{d:'M12 21s-7-4.5-9-9a5 5 0 0 1 9-3 5 5 0 0 1 9 3c-2 4.5-9 9-9 9z'}),
      menu: e('g',{},e('path',{d:'M3 7h18'}),e('path',{d:'M3 12h18'}),e('path',{d:'M3 17h18'})),
      close: e('g',{},e('path',{d:'M6 6l12 12'}),e('path',{d:'M18 6L6 18'})),
      chevron: e('path',{d:'M9 6l6 6-6 6'}),
      arrow: e('g',{},e('path',{d:'M5 12h14'}),e('path',{d:'M13 6l6 6-6 6'})),
    };
    return e('svg',{width:s,height:s,viewBox:'0 0 24 24',fill:'none',stroke:'currentColor',strokeWidth:1.5,strokeLinecap:'round',strokeLinejoin:'round'},paths[name]);
  }

  function TopBar({cartCount, onCart, onSearch}){
    return e('div',{className:'pa-topbar'},
      e('div',{className:'pa-topbar-l'}, e('span',null,'DE / EN')),
      e('div',{className:'pa-topbar-r'},
        e('button',{className:'pa-iconbtn', onClick:onSearch}, e(Icon,{name:'search',size:14}), e('span',null,'Suchen')),
        e('button',{className:'pa-iconbtn'}, e(Icon,{name:'user',size:14}), e('span',null,'Konto')),
        e('button',{className:'pa-iconbtn', onClick:onCart}, e(Icon,{name:'cart',size:14}), e('span',null,'Warenkorb'),
          cartCount>0 && e('em',{className:'pa-cart-badge'}, cartCount))
      )
    );
  }

  function Header({onMenu, cartCount, onCart, onSearch}){
    return e('header',{className:'pa-header'},
      e(TopBar,{cartCount, onCart, onSearch}),
      e('div',{className:'pa-midbar'},
        e('a',{href:'#', className:'pa-logo-link'},
          e('img',{className:'pa-logo', src:'assets/logo.svg', alt:'Plantaphilia'})
        ),
        e('button',{className:'pa-hamb', onClick:onMenu},
          e('span'), e('span'), e('span')
        )
      ),
      e('div',{className:'pa-divider'})
    );
  }

  function NavPanel({open, onClose}){
    const [expanded, setExpanded] = useState(true);
    const subcats = ['Angel','Arthybriden','Duftpelargonien','Edelpelargonien','Finger-Pelargonien','Goldblattpelargonien','Hängepelargonien','Kaktusblütige','Miniatur','Nelkenblütige','Rosenknospige','Schmuckblatt','Stellar','Tulpenblütige','Unique','Wildpelargonien','Zonale einfach','Zonale gefüllt','Zonartic','Zwerg'];
    return e('div',{className:'pa-nav ' + (open?'open':'')},
      e('div',{className:'pa-nav-inner'},
        e('button',{className:'pa-nav-close', onClick:onClose}, e(Icon,{name:'close',size:22})),
        e('div',{className:'pa-nav-content'},
          e('div',{className:'pa-nav-eyebrow'}, 'Navigation'),
          e('a',{className:'pa-nav-item', href:'#', onClick:onClose}, 'Home'),
          e('div',{className:'pa-nav-item pa-nav-group'},
            e('div',{className:'pa-nav-row', onClick:()=>setExpanded(!expanded)},
              e('span',null,'Shop · Pelargonium'),
              e('span',{className:'pa-nav-chev ' + (expanded?'ex':'')}, e(Icon,{name:'chevron',size:16}))
            ),
            expanded && e('ul',{className:'pa-subnav'}, subcats.map(s => e('li',{key:s}, e('a',{href:'#', onClick:onClose}, s))))
          ),
          e('a',{className:'pa-nav-item', href:'#', onClick:onClose}, 'Kontakt'),
          e('div',{className:'pa-nav-foot'},
            e('span',{className:'pa-nav-tag'}, 'Kuratiert · versandfertig aus Deutschland')
          )
        )
      )
    );
  }

  function CategoryRail({active, onSelect}){
    const cats = ['Alle','Angel','Edelpelargonien','Duftpelargonien','Finger-Pelargonien','Rosenknospige','Stellar','Miniatur','Wildpelargonien','Zonartic'];
    return e('div',{className:'pa-rail'},
      cats.map(c => e('button',{key:c, className:'pa-chip ' + (active===c?'active':''), onClick:()=>onSelect(c)}, c))
    );
  }

  function ProductCard({p, onAdd, onOpen}){
    return e('article',{className:'pa-card'},
      e('div',{className:'pa-card-img', style:{backgroundImage:`url(${p.img})`}, onClick:onOpen},
        p.badge && e('span',{className:'pa-badge ' + (p.badge==='Ausverkauft'?'out':'')}, p.badge),
        e('button',{className:'pa-fav', onClick:(ev)=>{ev.stopPropagation();}}, e(Icon,{name:'heart',size:14}))
      ),
      e('div',{className:'pa-card-body'},
        e('h3',{className:'pa-card-name'}, p.name),
        e('p',{className:'pa-card-latin'}, p.latin),
        e('div',{className:'pa-card-foot'},
          e('span',{className:'pa-card-price'}, p.price),
          p.sold ? e('span',{className:'pa-sold'}, 'Ausverkauft')
                 : e('button',{className:'pa-btn-out', onClick:(ev)=>{ev.stopPropagation(); onAdd(p);}}, 'In den Warenkorb')
        )
      )
    );
  }

  function CartDrawer({open, items, onClose, onRemove}){
    const total = items.reduce((s,i)=>s+i.priceN,0);
    return e('div',{className:'pa-cart ' + (open?'open':'')},
      e('div',{className:'pa-cart-head'},
        e('span',{className:'pa-cart-title'}, 'Warenkorb'),
        e('button',{className:'pa-cart-close', onClick:onClose}, e(Icon,{name:'close',size:18}))
      ),
      e('div',{className:'pa-cart-body'},
        items.length===0 ? e('p',{className:'pa-cart-empty'}, 'Noch leer. Wählen Sie Ihr erstes Sammlerstück.')
        : items.map((it,i) => e('div',{key:i,className:'pa-cart-row'},
            e('div',{className:'pa-cart-thumb',style:{backgroundImage:`url(${it.img})`}}),
            e('div',{className:'pa-cart-info'},
              e('div',{className:'pa-cart-name'}, it.name),
              e('div',{className:'pa-cart-latin'}, it.latin),
              e('div',{className:'pa-cart-pr'}, it.price)
            ),
            e('button',{className:'pa-cart-x', onClick:()=>onRemove(i)}, e(Icon,{name:'close',size:14}))
          ))
      ),
      items.length>0 && e('div',{className:'pa-cart-foot'},
        e('div',{className:'pa-cart-total'},
          e('span',null,'Zwischensumme'),
          e('span',{className:'pa-cart-sum'}, '€ '+total.toFixed(2).replace('.',','))),
        e('button',{className:'pa-btn-filled wide'}, 'Zur Kasse →')
      )
    );
  }

  function Footer(){
    return e('footer',{className:'pa-footer'},
      e('div',{className:'pa-footer-top', style:{backgroundImage:'url(assets/leaf-texture.png)'}},
        e('div',{className:'pa-footer-veil'},
          e('div',{className:'pa-footer-quote'}, '„Die selteneren Pelargonien wachsen im Halbschatten — und in guter Gesellschaft."')
        )
      ),
      e('div',{className:'pa-footer-main'},
        e('div',{className:'pa-footer-col'},
          e('img',{className:'pa-footer-logo', src:'assets/logo.svg', alt:''}),
          e('p',{className:'pa-footer-p'}, 'Seltene Pelargonien & exotische Raritäten, seit 2019 aus Leidenschaft gesammelt.')
        ),
        e('div',{className:'pa-footer-col'},
          e('div',{className:'pa-footer-h'}, 'Shop'),
          e('a',{href:'#'},'Pelargonien'),e('a',{href:'#'},'Raritäten'),e('a',{href:'#'},'Geschenkgutscheine')),
        e('div',{className:'pa-footer-col'},
          e('div',{className:'pa-footer-h'}, 'Service'),
          e('a',{href:'#'},'Versand & Pflege'),e('a',{href:'#'},'Kontakt'),e('a',{href:'#'},'Bestellungen')),
        e('div',{className:'pa-footer-col'},
          e('div',{className:'pa-footer-h'}, 'Rechtliches'),
          e('a',{href:'#'},'Impressum'),e('a',{href:'#'},'AGB'),e('a',{href:'#'},'Datenschutz'))
      ),
      e('div',{className:'pa-footer-bottom'},
        e('span',null,'© 2026 Plantaphilia · Ein kleines Gewächshaus in Freiburg'),
        e('span',null,'kontakt@plantaphilia.eu'))
    );
  }

  window.PA = {Icon, TopBar, Header, NavPanel, CategoryRail, ProductCard, CartDrawer, Footer};
})();
