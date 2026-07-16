# Plantaphilia · How to design here

Quick rules. Read the full README.md for context.

## Before you build

1. Always import `colors_and_type.css` first.
2. For any storefront UI, copy `ui_kits/web/example.html` as your scaffold — don't compose from scratch.
3. Load Playfair Display from Google Fonts; Montserrat ships locally in `fonts/`.

## Token-picking shortcuts

- **Background?** `--bg-deep` (page) → `--bg-surface` (cards) → `--bg-raised` (hover) → `--bg-inky` (footer / topbar / modal scrims).
- **Accent?** `--plum-hot` for active/primary, `--plum` for resting fills, `--burgundy` for sold-out, `--amethyst` for icons & dividers, `--lavender` for italic latin names + links.
- **Text?** `--creme` body, `--creme-dim` secondary, `--creme-muted` meta. Never `#fff`.
- **Border?** `--border-hair` between rows, `--border-thin` around panels, `--border-gold` only at footer-top.
- **Radius?** `2px` (everything) or `999px` (chips & cart badge). Nothing else.
- **Shadow?** `--shadow-cabinet` for cards, `--shadow-specimen` for modals, `--shadow-inner-dark` for hero panels.

## Typography roles (don't invent new sizes)

- Headlines → `.h-display / .h1 / .h2 / .h3 / .h4` (Playfair, weight 500).
- Italic latin name → `.latin` (always lavender).
- Eyebrow caps → `.eyebrow` (Montserrat, +0.22em tracking).
- Body → `.p` (15px), `.p-small` (12px), `.meta` (11px).
- Price → `.price` (Playfair, plum-hot). Format: `€ 38,00`.

## Components (from `window.PA`)

`Header`, `NavPanel`, `CategoryRail`, `ProductCard`, `CartDrawer`, `Footer`, `Icon`. See `ui_kits/web/example.html` for wiring.

## Always / Never

✅ German copy first. Pair every trade name with a botanical name. Use real photography. Sharp corners. Outline icons.
❌ No emoji. No bright greens. No gradients (except the image veil + cart badge). No rounded-pill cards. No Inter / Roboto / Arial. No light mode.
