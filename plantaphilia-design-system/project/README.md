# Plantaphilia · Design System

> Rare pelargoniums & exotic curiosities — for a small German online nursery.
> Visual register: **Dark Botanical Gothic.** Victorian botanical illustration meets dark academia. Velvet, moss, blackberry, amethyst.

---

## What this is

A complete visual + component system for the Plantaphilia storefront. Use this when designing any new page, product, marketing piece, or campaign. **Do not start visual work without reading this file first.**

The system optimizes for two things:

1. **Reverence.** These are not houseplants from the supermarket — they are collected specimens. The UI should feel like a private cabinet, not a marketplace.
2. **Legibility for collectors.** Latin names, plant lore, provenance, and care notes are the *content*. Layouts must give them oxygen.

---

## Index

| File / folder | Purpose |
|---|---|
| `colors_and_type.css` | Single source of truth for color, type, spacing, shadows, motion. Import in every artifact. |
| `assets/` | Logo, banners, leaf textures, favicon. |
| `fonts/` | Montserrat woff2 (regular / medium / bold). Playfair Display loads from Google Fonts. |
| `preview/` | One small HTML card per token group. Used in the asset review pane. |
| `ui_kits/web/` | React/Babel component library + `styles.css` + `example.html` storefront. |
| `SKILL.md` | Quick-reference rules for picking tokens & components. |

---

## Content fundamentals

**Voice.** German first. Warm, knowledgeable, slightly old-fashioned. Never breezy, never salesy. We sound like a nursery owner who has read every monograph on the genus.

- ✅ "Gesammelt in den Schattengärten Europas."
- ✅ "Im Halbschatten kultiviert. Versandfertig in 12-cm-Töpfen."
- ❌ "Trending now! Grab yours before they're gone 🌿✨"

**Naming convention.** Always pair the **trade name** (display, serif) with the **botanical name** (italic, lavender) — Lord Bute *Pelargonium × domesticum*. Never one without the other.

**Numbers.** German formatting: `€ 38,00` (comma decimal, space after €). Article numbers: `Art.-Nr. 2481`. Pot size: `Topf 12 cm`.

**Dates.** Long German style for collection dates ("Januar 2026"); ISO for transactional UI.

---

## Visual foundations

### Color

The palette is **forest + berry**: deep mossy greens for surfaces, plum/burgundy/amethyst for accents, warm crème for text. **No bright greens, no pure black, no pure white anywhere.**

| Token | Hex | Use |
|---|---|---|
| `--bg-deep` | `#0f2419` | Page background, canvas |
| `--bg-surface` | `#143020` | Cards, panels, drawers |
| `--bg-raised` | `#1a3d29` | Hover surfaces, active nav |
| `--bg-inky` | `#07150d` | Footer, modal backdrops, topbar |
| `--plum` | `#6b2f5c` | Primary accent base |
| `--plum-hot` | `#9c3f7e` | Active accent — prices, primary CTAs, hover fills |
| `--burgundy` | `#5a1a2e` | Secondary accent, sold-out tags |
| `--amethyst` | `#7d5fa5` | Icons, dividers, subtle accents |
| `--lavender` | `#b9a3d4` | Italic latin names, links on dark |
| `--gold-dark` | `#8a6f3a` | Hairline dividers below hero sections |
| `--creme` | `#f2e8d5` | Primary body text |
| `--creme-dim` | `#d9cdb3` | Secondary body |
| `--creme-muted` | `#a89d84` | Captions, meta, disabled |

**Rules of thumb.**

- The page is dark by default. There is **no light mode.**
- `plum-hot` is the only color allowed for prices. Prices are always rendered in the display serif.
- Latin names are *always* `lavender`, *always* italic, *always* in the display serif.
- Use `gold-dark` sparingly — only as a hairline divider between major hero sections, never as a fill.
- Pure black (`#000`) and pure white (`#fff`) are forbidden. Use `bg-inky` and `creme`.

### Typography

Two families. No third faces, no decorative scripts.

- **Playfair Display** — serif display & editorial body. Headlines, prices, latin names, tagline italics, footer pull-quotes.
- **Montserrat** — sans body. Buttons, labels, navigation, eyebrow caps, meta, paragraph copy ≤14px.

The **italic of Playfair Display** is a load-bearing element of the brand: it carries every botanical name and every editorial voice quote. Treat italic as a first-class style, not an afterthought.

| Role | Family | Size | Weight | Notes |
|---|---|---|---|---|
| `.h-display` | Playfair | 104px | 400 | Hero only. One per page max. |
| `.h1` | Playfair | 72px | 500 | Page titles |
| `.h2` | Playfair | 48px | 500 | Section headers |
| `.h3` | Playfair | 32px | 500 | Sub-sections |
| `.h4` | Playfair | 22px | 500 | Card names |
| `.tagline` | Playfair italic | 22px | 400 | Hero supporting copy |
| `.latin` | Playfair italic | 14px | 400 | Botanical names — `--lavender` |
| `.eyebrow` | Montserrat | 11px / +0.22em / UPPER | 500 | Section labels |
| `.p` | Montserrat | 15px | 400 | Body |
| `.p-small` | Montserrat | 12px | 400 | Secondary body |
| `.meta` | Montserrat | 11px / +0.05em | 400 | Article numbers, dates |
| `.price` | Playfair | 22px | 500 | Always `--plum-hot` |

### Spacing

8 px base. `s-1 (4)`, `s-2 (8)`, `s-3 (12)`, `s-4 (16)`, `s-5 (24)`, `s-6 (32)`, `s-7 (48)`, `s-8 (64)`, `s-9 (96)`.

Card grid gutters: `28px 22px`. Page side padding: `32px` desktop, `20px` mobile. Section vertical rhythm: `80–96px`.

### Radii

**Sharp by default.** `--r-1` is `2px` and that's the maximum — applied to inputs, buttons, badges, cards. Only `--r-pill` (999px) is permitted, and only for category chips and the cart count badge. **No 8/12/16px rounded corners anywhere.** This is a serious thing about a serious nursery, not a SaaS dashboard.

### Shadows & borders

- `--shadow-cabinet` — default elevation for product cards. Soft, low, mostly downward.
- `--shadow-specimen` — heavier, for modals/lightboxes.
- `--shadow-inner-dark` — inset, used on hero panels to vignette imagery.
- `--border-thin` — 30% amethyst at 1px. Standard divider on dark surfaces.
- `--border-hair` — 8% crème at 1px. Almost-invisible row separators in lists.
- `--border-gold` — 35% gold at 1px. Reserved for footer top edge & special editorial dividers.

### Motion

`--ease-botanical` (`cubic-bezier(.22,.61,.36,1)`) — gentle ease-out, plant-like. Durations: `--t-fast 120ms` (button color), `--t-base 220ms` (hover, focus), `--t-slow 440ms` (drawers, nav panel).

No bounce, no spring. Plants don't bounce.

---

## Iconography

Outline-only, 1.5px strokes, 24×24 viewBox, rounded line caps & joins, color follows `currentColor`. The set lives inline in `ui_kits/web/components.jsx` under the `Icon` component — use it directly rather than reaching for an icon font.

Available glyphs: `user`, `cart`, `search`, `heart`, `menu`, `close`, `chevron`, `arrow`. Add new ones to that map; never mix in filled or different-stroke icons. Default size is `16`; topbar uses `14`; nav drawer close uses `22`.

---

## Imagery

Three image roles, all already shot in the same dark-botanical register:

- `assets/hero-bloom.png` — wide header / hero banner. Yellow-tinted bloom on deep green; vignette built in.
- `assets/banner.png` — dense floral macro on deep green; for product cards, category headers, editorial blocks.
- `assets/leaf-texture.png` — single-leaf macro; used as a moody backdrop for the footer pull-quote.

**Rules.** Always overlay imagery with the standard veil — `linear-gradient(180deg, rgba(7,21,13,0.35), rgba(7,21,13,0.85))` — so type stays legible. Hero images sit behind a 520px panel with the inner-dark inset shadow. Never crop subjects to the edge of the frame; leave breathing room above the hero copy.

---

## UI kit

`ui_kits/web/` is the canonical component library for the storefront.

- `components.jsx` — React components (no JSX file extension friction; loaded via Babel inline). Exports on `window.PA`: `Icon`, `TopBar`, `Header`, `NavPanel`, `CategoryRail`, `ProductCard`, `CartDrawer`, `Footer`.
- `styles.css` — all `pa-`-prefixed classes the components need. Pairs with the root `colors_and_type.css`.
- `example.html` — a complete storefront composed from the kit. Use as the starting point for any new page; copy and adapt.

Loading order in any host page:

1. `colors_and_type.css`
2. `ui_kits/web/styles.css`
3. Google Font for Playfair Display
4. React + ReactDOM + Babel (pinned versions)
5. `components.jsx` (`type="text/babel"`)
6. Your `type="text/babel"` script that mounts an `App`

---

## Rules to keep the system honest

1. **Never invent colors.** If a swatch isn't in the table above, you can't use it. Compose new tones with `oklch()` rooted in existing tokens, and add them to `colors_and_type.css` if they survive review.
2. **Never invent type sizes.** Use the role classes. Custom sizes need a documented role.
3. **No emoji, anywhere.** Botanical content is illustrated by botanical photography, not glyphs.
4. **No gradients except the image veil and the cart-badge linear-gradient.** Hard stops are honest, gradients are not.
5. **Latin names are never decorative.** They are the species, treat them as data.
6. **One display serif phrase per viewport.** Don't litter the page with italic flourishes — pick the moment.
7. **German copy is the source of truth.** English is a translation, not a co-equal.
