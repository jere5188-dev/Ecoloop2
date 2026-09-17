# EcoLoop Web Platform — Design

## 1. Architecture Overview

A zero-dependency PHP application served through a single front controller. There is no
build step: PHP renders HTML, CSS is hand-authored with custom properties, JS is vanilla
ES2019 in IIFE modules.

```
Request  →  public/index.php (front controller)
              │
              ├─ app/bootstrap.php ......... constants, autoload-lite, helpers
              ├─ app/Router.php ............ path → route definition
              ├─ app/Controller.php ........ per-route data assembly + POST handling
              ├─ app/Data/*.php ............ mock data providers (arrays)
              └─ app/View.php .............. render(page, data) into layout
                        │
                        ├─ views/layouts/app.php
                        │     ├─ partials/head.php
                        │     ├─ partials/header.php
                        │     ├─ partials/sidebar.php        (tablet + desktop)
                        │     ├─ views/pages/<page>.php      (the route body)
                        │     ├─ partials/bottom-nav.php     (mobile)
                        │     └─ partials/footer.php
                        └─ views/components/*.php            (card, stat, badge, …)
```

### Directory layout

```
Ecoloop2/
├── .kiro/specs/ecoloop-ui/{requirements,design,tasks}.md
├── app/
│   ├── bootstrap.php
│   ├── Router.php
│   ├── View.php
│   ├── helpers.php               e(), asset(), url(), is_active(), fmt_kg(), fmt_pts()
│   ├── Controllers/
│   │   ├── PageController.php     dashboard, passbook, impact, sorting, gamify, profile
│   │   └── PickupController.php   multi-step wizard state machine
│   ├── Data/
│   │   ├── Nav.php               navigation registry
│   │   ├── User.php              profile, balance, streak
│   │   ├── Waste.php             passbook rows, transactions, categories
│   │   ├── Impact.php            CO₂ series, material breakdown, equivalences
│   │   ├── Sorting.php           detection fixtures + the 4 preparation steps
│   │   ├── Pickup.php            locations, waste types, time windows
│   │   └── Gamify.php            badges, rewards, leaderboard, competition, actions
│   └── views/
│       ├── layouts/app.php
│       ├── partials/{head,header,sidebar,bottom-nav,footer,flash}.php
│       ├── components/{card,stat-card,metric,progress,badge,chip-group,
│       │               table-passbook,step-indicator,icon,ring-gauge,
│       │               spark-chart,section-head,empty-state}.php
│       └── pages/{dashboard,passbook,impact,sorting,pickup,gamify,profile,404}.php
└── public/
    ├── index.php
    └── assets/
        ├── css/{tokens,base,layout,components,features,responsive}.css
        └── js/{app,passbook,impact,scanner,pickup,gamify}.js
```

`public/` is the document root. Application code sits outside it.

### Routing table

| Method | Path | Handler | Page |
| --- | --- | --- | --- |
| GET | `/` | `PageController::dashboard` | dashboard |
| GET | `/passbook` | `PageController::passbook` | Waste Passbook |
| GET | `/impact` | `PageController::impact` | Impact Metric |
| GET | `/sorting` | `PageController::sorting` | Smart Sorting |
| GET | `/pickup` | `PickupController::show` | Pickup wizard (step from session/query) |
| POST | `/pickup` | `PickupController::submit` | validate → advance / re-render |
| GET | `/pickup/reset` | `PickupController::reset` | clear session, back to step 1 |
| GET | `/competition` | `PageController::gamify` | EcoPoints & Gamify |
| GET | `/profile` | `PageController::profile` | Profile |
| * | any other | `View::notFound` | 404 |

The router normalizes the path (strip query, strip trailing slash, honor a sub-directory
base path) so the app works both at a domain root and under `php -S` in a subfolder.

### Templating contract

`View::render(string $page, array $data = [], array $meta = [])`

- extracts `$data` into the page scope,
- captures the page output into `$content`,
- includes `layouts/app.php`, which composes the partials around `$content`.

`component('stat-card', [...])` includes `views/components/stat-card.php` with the array
available as `$props`. Every component starts with a docblock listing its keys and uses
`$props['key'] ?? default` so partial calls never fatal.

### Pickup wizard state machine

State lives in `$_SESSION['pickup']` as `['step' => int, 'data' => [...], 'errors' => [...]]`.

```
step1 (location) ──POST valid──▶ step2 (waste type) ──POST valid──▶ step3 (schedule)
   ▲                                   ▲                                  │
   └───────── back ────────────────────┴──────── back ───────────────┐    │ POST valid
                                                                     ▼    ▼
                                                            step4 (review) ──confirm──▶ done
```

- Invalid POST → same step re-rendered with `errors` and the submitted values retained.
- `back` is a POST with `action=back` (so nothing is lost) plus a GET fallback link.
- On confirm, a request ID `ECO-<YYMMDD>-<4 rand>` is generated and the session data is
  moved to `$_SESSION['pickup_done']`, then a redirect to `?step=done` (POST-redirect-GET).

### Progressive enhancement

Everything renders and functions server-side. JS only adds: chip filtering, range tabs,
counter animation, scanner simulation, sidebar collapse memory, scroll-triggered progress
bars, and client-side pre-validation hints.

---

## 2. Design System

Sourced directly from the EcoLoop Web Styleguide.

### 2.1 Color tokens

| Token | Hex | Role |
| --- | --- | --- |
| `--c-forest` | `#2E7D32` | Primary — buttons, active nav, links |
| `--c-dark-green` | `#0F3D2E` | Dark surfaces, sidebar, hero, scanner chrome |
| `--c-leaf` | `#A7C957` | Accent — highlights, gauge tail, streak fill |
| `--c-sage` | `#6B8E71` | Muted accent — secondary borders, illustration |
| `--c-cream` | `#F8FAF7` | App background |
| `--c-surface` | `#FFFFFF` | Cards, sheets, table |
| `--c-text` | `#1B2E25` | Primary text |
| `--c-text-2` | `#4B5E56` | Secondary text |
| `--c-text-muted` | `#8BAA90` | Muted / captions / disabled |
| `--c-success` | `#22C55E` | Verified, positive delta |
| `--c-warning` | `#F59E0B` | Pending |
| `--c-error` | `#EF4444` | Rejected, validation error |
| `--c-info` | `#3B82F6` | Informational |

Derived: `--c-forest-tint` (8% forest over surface) for soft chips/badges, `--c-border`
(`#E4EDE6`), `--c-border-strong` (`#CFDED4`), plus per-material accents (PET → info,
Paper → warning, Cardboard → sage, Cans → sage-dark) used only for icon tiles.

Shadows: `--sh-1` (card rest), `--sh-2` (card hover), `--sh-3` (popover/toast). Radii:
`--r-sm 10px`, `--r-md 14px`, `--r-lg 20px`, `--r-xl 28px`, `--r-pill 999px`.

### 2.2 Typography

| Family | Google Font | Use |
| --- | --- | --- |
| Headings | **Plus Jakarta Sans** (600/700) | H1–H6 |
| Body | **Inter** (400/500/600) | paragraphs, labels, tables |
| Data / metrics | **Space Grotesk** (500/600) | KG, points, percentages |

Scale (desktop, from the styleguide) — implemented with `clamp()` so it scales fluidly and
lands on the styleguide value at ≥1024px:

| Style | size/line | weight |
| --- | --- | --- |
| H1 | 32/40 | Bold |
| H2 | 24/32 | Semibold |
| H3 | 20/28 | Semibold |
| H4 | 18/26 | Medium |
| H5 | 16/24 | Medium |
| H6 | 14/20 | Medium |
| Body Large | 16/24 | Regular |
| Body | 14/20 | Regular |
| Small | 12/16 | Medium |
| Caption | 11/14 | Regular |
| Data Large | 28/36 | Semibold |
| Data Medium | 20/28 | Semibold |
| Data Small | 16/24 | Medium |

Example: `--fs-h1: clamp(1.5rem, 1.2rem + 1.4vw, 2rem)` → 24px mobile … 32px desktop.

### 2.3 Spacing & layout grid

4px base scale: `--sp-1 4` … `--sp-12 64`. Gutters: 16px mobile, 24px tablet, 32px desktop.
Content max-width 1280px; sidebar 264px expanded / 76px rail.

The shell is a CSS Grid:

```css
.shell { display: grid; grid-template-areas: "main"; }                    /* mobile  */
@media (min-width:768px){ .shell{ grid-template-columns: var(--rail) 1fr;
                                  grid-template-areas: "side main"; } }   /* tablet+ */
```

Dashboard sections use `repeat(auto-fit, minmax(clamp(...), 1fr))` so the column count
falls out of available width — fluid rather than stepped.

### 2.4 Components

| Component | Spec |
| --- | --- |
| **Primary button** | forest bg, white text, 12px radius, 44px min height, hover → darker + 1px lift, arrow icon slides 2px |
| **Secondary button** | surface bg, 1px forest border, forest text, hover → forest tint |
| **Gamification button** | dark-green gradient bg, leaf-green icon, hover → leaf-tinted with shine sweep |
| **Icon button** | 40px circle; filled forest, tinted, or outline variants |
| **Toggle chip** | pill, 32px; inactive = tinted; active = dark-green bg + white text |
| **Switch** | 44×24 pill track, forest when on, 200ms knob translate |
| **Card** | white surface, 1px border, `--r-lg`, `--sh-1`; header row = title + action link |
| **Stat card** | icon tile (44px, tinted, material color) + label + data value + delta |
| **Ring gauge** | 2 SVG circles, `stroke-dasharray` progress, centered data value |
| **Progress bar** | 8px track, forest→leaf gradient fill, optional label row |
| **Badge** | hexagon-ish rounded tile with 2px ring, icon, title, requirement; locked = grayscale + 45% opacity + lock glyph |
| **Streak row** | 7 circular slots, 28px; done = forest fill + check, today = leaf ring pulse, future = dashed border |
| **Data table** | header row in small/muted caps, 1px row dividers, hover row tint; mobile → card-per-row via `display:block` + `data-label` pseudo-elements |
| **Step indicator** | 4 numbered circles joined by a 2px connector; done = forest + check, current = forest ring, future = muted |
| **Toast** | surface, `--sh-3`, success-tinted left edge, close button |
| **Bottom nav** | fixed, 5 items, 64px + safe-area inset, active item = forest icon + label + top indicator bar |

### 2.5 Iconography

Line-based, 24px grid, `stroke-width:1.75`, `currentColor`, rounded caps. Inline SVG via
`component('icon', ['name' => 'recycle'])`. Set: home, recycle, map, trophy, profile, leaf,
camera, calendar, location, settings, chart, bottle, paper, cardboard, can, flame, check,
lock, arrow-right, chevron-right, chevron-left, x, plus, gift, scan, clock, sparkle, tree,
info, alert, menu, filter, coin, weight.

---

## 3. Feature UI Design

### 3.1 Waste Passbook

```
┌ Summary strip ─────────────────────────────────────────┐
│ [12.4 KG total]  [2,450 pts]  [3 categories]  [+8 dep] │   4 → 2 → 1 col
└────────────────────────────────────────────────────────┘
┌ Passbook ledger ───────────────────────── View All ────┐
│ chips: All | PET | Paper | Cardboard | Cans            │
│ Kategori │ Volume │ Proporsi │ EcoPoints │ Status      │
│ ▣ PET    │ 6.5 KG │ ▓▓▓ 52.4%│ +260 Pts  │ ✓ Verified │
│ …                                                      │
│ TOTAL    │12.4 KG │  100%    │ +450 Pts  │            │
└────────────────────────────────────────────────────────┘
┌ Recent activity (timeline list) ───────────────────────┐
```

Mobile: each `<tr>` becomes a card; `<td>` gets `content: attr(data-label)` on `::before`
in a left column, value right-aligned. The `<thead>` is visually hidden.

### 3.2 Impact Metric

- Hero: dark-green gradient panel, ring gauge (CO₂ reduced vs goal) + 3 equivalence chips
  (trees, water, energy). Desktop = gauge left / chips right; mobile = stacked, centered.
- Range tabs (Week / Month / All) swap datasets held in `data-*` attributes.
- Trend: inline SVG polyline + area gradient + dot markers, `viewBox` scaled, `width:100%`.
- Material breakdown: label / bar / value rows in a 3-column grid collapsing to 2 on mobile.
- Counters animate with `requestAnimationFrame` and ease-out cubic.

### 3.3 Smart Sorting

```
Desktop (≥1024)                       Mobile (≤767)
┌───────────┬─────────────────┐       ┌───────────────┐
│ camera    │ Detected:       │       │    camera     │
│  ┌─────┐  │  PET Bottle     │       │   viewport    │
│  │reticle │ ─────────────── │       │  (portrait)   │
│  └─────┘  │ 1 Kosongkan     │       ├───────────────┤
│  ( ○ )    │ 2 Bilas         │       │ result sheet  │
│           │ 3 Lepas         │       │ 4 steps       │
└───────────┴─────────────────┘       └───────────────┘
```

- Viewport: `aspect-ratio: 3/4` mobile, `4/5` desktop, dark-green `#0A2A1F` base with a
  subtle radial vignette; a simulated bottle silhouette is drawn in SVG.
- Reticle: 4 absolutely-positioned corner brackets in leaf green; `@keyframes scan-sweep`
  moves a 2px gradient line down the frame during scanning.
- Shutter: 68px white ring with an inner forest disc; flanked by a gallery and flash button.
- The 4 steps are an ordered list with numbered forest discs; `aria-live="polite"` on the
  result region so a screen reader hears the detection.

### 3.4 Waste Pickup

- Card holds the step indicator, a `<form method="post">`, and a sticky footer action row
  (`Back` / `Continue`), so on mobile the primary action is always thumb-reachable.
- Location = styled radio cards (whole card clickable via `<label>`); "Other" reveals a
  detail input via `:has()` with a JS fallback toggle.
- Waste type = checkbox chips (`input` visually hidden, `label` styled as chip, `:checked`
  → dark-green).
- Schedule = native `date` + `time-window` radio grid (2 cols mobile / 4 desktop).
- Review = definition list of all answers with an "Edit" link per group, an estimated-points
  callout, and a required confirmation checkbox.
- Done = success panel + the 6-stage vertical timeline (horizontal on desktop).

### 3.5 EcoPoints & Gamify

- Balance card: dark-green gradient, large points value, Redeem button, streak row beneath.
- Campus Competition: `320 / 500 kg` with a 64% progress bar, days-left pill, and a
  faculty leaderboard (rank medal, name, KG, mini bar).
- Badges: `repeat(auto-fit, minmax(140px,1fr))`; unlocked badges get a hover lift +
  ring-glow + a rotating conic shine on desktop only.
- Earning actions: 2×2 grid of point-value tiles.
- Rewards: cards with cost pill; unaffordable → muted with "need N more pts".

---

## 4. Responsive Strategy

Mobile-first. Two `min-width` queries plus targeted refinements.

```css
/* base: ≤767 mobile */
@media (min-width: 768px)  { /* tablet: sidebar rail, 2-col */ }
@media (min-width: 1024px) { /* desktop: persistent sidebar, 3–4 col, hover FX */ }
@media (min-width: 1440px) { /* wide: larger gutters, 4-col dashboards */ }
@media (max-width: 767px)  { /* mobile-only: table→cards, bottom nav */ }
@media (hover: hover) and (pointer: fine) { /* hover animations only here */ }
@media (prefers-reduced-motion: reduce)   { /* kill all animation */ }
```

### Mobile (≤767px)

- Bottom nav fixed with `padding-bottom: env(safe-area-inset-bottom)`; `main` gets
  `padding-bottom: calc(64px + env(safe-area-inset-bottom) + 16px)` so nothing hides.
- Single-column stacks; `gap: var(--sp-4)`.
- Touch targets: `min-height:44px` everywhere, 48px for primary buttons, 64px+ shutter,
  chips 36px with 12px horizontal padding.
- Tables → cards. Long numbers use `Space Grotesk` at Data Medium to stay legible.
- Tall-screen tuning (Galaxy S26 Ultra class, ~1440×3200 CSS ≈ 480×1067):
  `@media (max-width:767px) and (min-height:820px)` increases hero and scanner viewport
  height so the fold isn't half-empty, and `dvh` units are used for the scanner so the
  browser chrome collapsing doesn't cause jumps.
- `-webkit-tap-highlight-color: transparent` plus explicit `:active` states.

### Tablet (768–1023px)

- Sidebar becomes a 76px icon rail; a toggle expands it to 264px (`localStorage`
  `ecoloop:sidebar`), overlaying content with a scrim when expanded.
- Dashboard grids: 2 columns (`minmax(320px, 1fr)`).
- Typography lands mid-clamp; H1 ≈ 28px.
- Bottom nav hidden; header keeps the sidebar toggle.

### Desktop (≥1024px)

- Sidebar persistent and always labelled; toggle hidden; `grid-template-columns: 264px 1fr`.
- Dashboards 3-col, wide screens 4-col; scanner and pickup use 2-col split panes.
- Hover animations: card lift (`translateY(-2px)` + `--sh-2`), badge ring glow, shine sweep
  on gamification buttons, nav item slide, table row tint, reward card scale.
- Content capped at 1280px and centered.

### Accessibility implementation notes

- `.skip-link` first in `<body>`, revealed on `:focus`.
- `:focus-visible { outline: 2px solid var(--c-forest); outline-offset: 2px; }`.
- Chips/tabs use `role="tablist"`/`aria-selected` with arrow-key handling.
- Progress bars: `role="progressbar" aria-valuenow aria-valuemin="0" aria-valuemax`.
- `.sr-only` utility for the visually-hidden table header and icon labels.

---

## 5. JavaScript Modules

| File | Responsibility |
| --- | --- |
| `app.js` | `EcoLoop` namespace, `$`/`$$` helpers, sidebar collapse + persistence, mobile drawer, toast dismissal, `IntersectionObserver` for progress/counter reveal, reduced-motion guard |
| `passbook.js` | Chip filtering of ledger rows + recomputed visible totals |
| `impact.js` | Range tab switching, counter tweening, SVG sparkline redraw |
| `scanner.js` | State machine idle → scanning → detected, progress simulation, step stepper, toast |
| `pickup.js` | Client-side hints: "Other" detail reveal, at-least-one-type check, min-date, review edit anchors |
| `gamify.js` | Progress bar reveal animation, badge hover FX gating, redeem affordability feedback |

Each file is an IIFE, feature-detects its root element and returns early if absent, so a
single bundle order is safe on every page.
