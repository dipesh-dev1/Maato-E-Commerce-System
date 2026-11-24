# Maato design system

Maato is a fictional homeware studio in Jhamsikhel, Lalitpur, selling stoneware, lighting, oak furniture, linen and small-batch apothecary from seven workshops across Nepal. The brand should feel like a well-edited shop with a person behind the counter: warm, unhurried, specific. It should not read as a marketplace or a template.

Tokens live in `public/assets/css/tokens.css`. Components use the **role** tokens (`--color-text`, `--radius-control`), never the raw values.

## Brand direction

- **Clay and paper.** A warm bone ground, near-black ink, and one accent: the terracotta of an unglazed pot.
- **Editorial, not promotional.** Large photography, a serif display face, small and confident body text, generous space. No banners shouting percentages.
- **Asymmetry with a grid underneath.** The hero sits off-centre with an inset photo breaking into the copy column; the category bento has one dominant tile; the featured grid opens with a two-column "studio pick" card.
- **Specific microcopy.** Name the place, the maker, the material. "Glazed in Thimi" beats "high quality". Never "Welcome to our website".

## Colour

| Role token | Value | Use |
|---|---|---|
| `--color-bg` | `#f4efe7` bone | Page ground |
| `--color-surface` | `#fbf8f3` paper | Cards, dialogs, inputs |
| `--color-sunk` | `#eae3d8` oat | Image wells, hover fills, alternate sections |
| `--color-line` | `#d9cfc1` sand | Dividers, table rules |
| `--color-line-strong` | `#b3a796` stone | Input borders, outlined buttons |
| `--color-text` | `#1e1a16` ink | Body and headings (≈15:1 on bone) |
| `--color-text-soft` | `#3d362f` | Secondary copy |
| `--color-text-muted` | `#625850` | Meta, hints (≥5.9:1 on bone, passes AA) |
| `--color-accent` | `#a4471f` clay | Primary call to action, sale prices, focus ring, the logo dot (≈7:1 on bone; white text on it ≈7:1) |
| `--color-accent-hover` | `#853816` | Hover/pressed accent |

**Semantic colours are separate from the accent** and always ship with a word or icon, never colour alone:
success `#2f6b45` on `#e2efe6`, warning `#85570a` on `#f6ebd3`, danger `#a3302a` on `#f7e1de`, info `#2d5c7a` on `#e0ebf2`.

Not allowed: gradients between hues, neon glows, glassmorphism, more than one accent on a screen.

## Typography

- **Display:** Fraunces (variable, optical size, `SOFT` and `WONK` axes on the wordmark). Weight 300–450 for headings, with italics for single words of emphasis ("Made slowly, *kept* for years").
- **Body and UI:** Instrument Sans 400–700.
- Loaded from Google Fonts with `display=swap`; fallbacks are Georgia and the system UI face.

| Token | Size | Use |
|---|---|---|
| `--text-4xl` | 48 → 96px fluid | Hero display |
| `--text-3xl` | 40 → 64px | Page titles (`.h1`) |
| `--text-2xl` | 32 → 44px | Section titles (`.h2`) |
| `--text-xl` | 28px | Panel titles, product names in feature cards |
| `--text-lg` | 22px | Dialog titles, prices on the product page |
| `--text-md` | 18px | Lede paragraphs |
| `--text-base` | 16px | Body |
| `--text-sm` | 14px | UI, cards, admin body |
| `--text-xs` / `--text-2xs` | 12 / 11px | Meta, eyebrows, badges |

- Large headings use tight tracking: `--tracking-display: -0.035em`, `--tracking-tight: -0.018em`. Eyebrows use `+0.12em` uppercase.
- Leading: 1.04 display, 1.2 headings, 1.6 body.
- **All prices and figures use tabular lining numerals** (`.num`, `.price-line`, tables), so columns align.
- Money is formatted with Nepali grouping: `Rs. 1,25,000`. The same rule runs in PHP (`money()`).

## Spacing

A 4px base: `--s-1` 4 · `--s-2` 8 · `--s-3` 12 · `--s-4` 16 · `--s-5` 24 · `--s-6` 32 · `--s-7` 48 · `--s-8` 64 · `--s-9` 96 · `--s-10` 128.
Sections breathe at 56–120px (fluid). The container is 1360px with a 16–40px fluid gutter.

## Radius by role

Radius signals what something *is*, so it is assigned by role and nowhere else:

| Token | Value | Role |
|---|---|---|
| `--radius-media` | 2px | Photography and product cards: nearly square, editorial |
| `--radius-control` | 6px | Inputs, selects, quantity steppers, toasts, notices |
| `--radius-panel` | 14px | Dialogs, account panels, admin cards, empty states |
| `--radius-pill` | 999px | Buttons, badges, chips, swatches, the bento labels |

## Elevation

Only two shadows: `--shadow-raised` (inset photo, floating buttons) and `--shadow-overlay` (drawers, modals, toasts). Everything else separates with lines and surface changes.

## Motion

- Durations: `--dur-fast` 140ms (hover, colour), `--dur-base` 240ms (dialogs, menus), `--dur-slow` 480ms (drawers, image swaps, reveal).
- Easing: `--ease-out` for anything entering; `--ease-in-out` for exits.
- Purposeful only: hover image swap on product cards, cart drawer slide, quick-view rise, skeleton shimmer while loading, toast in/out, once-only section reveal, a bump on the bag count.
- `prefers-reduced-motion: reduce` sets every duration to 0, removes the shimmer and the reveal offset, and switches smooth scrolling off.

## Iconography

One hand-drawn SVG sprite (`public/assets/icons.svg`): 24px grid, 1.5px stroke, round caps and joins, `currentColor`. Used via `icon('name')` in PHP. No emoji, no icon fonts. Decorative icons are `aria-hidden`; icon-only buttons carry an `aria-label`.

## Imagery

Photography is natural light, warm, uncluttered, shot in rooms rather than on white. Product images are cropped 4:5 portrait; the second image is a styled in-room shot shown on hover. Images come from Unsplash (free licence) and are stored locally under `storage/uploads` and `public/assets/img`.

## Components (storefront)

- **Header:** sticky, condenses from 72px to 56px after 40px of scroll and gains a hairline. "Shop" opens a mega menu (hover with intent delay on pointer devices, click/Enter/ArrowDown on keyboard, Escape closes and returns focus).
- **Product card:** 4:5 image well, badge stack (sale %, sold out, new), save heart, quick-view button revealed on hover/focus (hidden on touch, where the card links straight through). Variants: `default`, `feature` (two columns, serif title, summary), `row` (compact horizontal for lists).
- **Bento:** one dominant category tile with a panel label, the rest with pill labels.
- **Drawers and modals:** native `<dialog>` (focus trap, Escape and inert background for free) with exit animations. Cart drawer, mobile nav, size guide, quick view, search, confirm.
- **Empty states:** icon in a circle, a human sentence, one or two next steps.
- **Focus:** a 2px bone gap plus 2px clay ring (`--focus-ring`) on every interactive element.

## Back office

The same tokens at a denser scale: 14px body, 36px controls, a light sidebar with a dark active state, cards on a slightly warmer ground (`#f7f4ef`). Data tables use sticky headers, tabular figures, sortable column headers with `aria-sort`, row hover and selection tint, and a dark bulk-action bar. Forms open in right-hand slide-overs; destructive actions always pass through a confirmation dialog. Status badges reuse the semantic palette: pending = warning, confirmed/packed = info, shipped = accent, delivered = success, cancelled = neutral.

The sales chart is one series: a 2px clay line with a 10% area fill, a recessive grid, a crosshair and tooltip on hover and arrow keys, a 7/30/90-day switch and a table view underneath.
