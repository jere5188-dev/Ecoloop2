# EcoLoop — Web Platform UI

> **Sort. Collect. Reward. Repeat.**
> A digital platform and gamification layer for sustainable consumption and waste
> management on university campuses. Supports **SDG 12 — Responsible Consumption
> & Production**.

A responsive frontend implementation built with **PHP 8**, **HTML5**, **CSS3** and
**vanilla JavaScript**. No framework, no build step, no Composer dependencies.

Theme: *"Green and Dark Green Minimalist — Restoring The Forest"*, following the
EcoLoop Web Styleguide for palette, typography, components and iconography.

---

## Run it

```bash
php -S localhost:8000 -t public
# then open http://localhost:8000
```

Requires PHP 8.1+ (uses `never` return types, `readonly`-era syntax and `match`).
Nothing else — no `composer install`, no `npm install`.

A convenience script is included:

```bash
bash tools/serve.sh 8000      # start detached
bash tools/smoke.sh           # lint + render every route + walk the pickup wizard
```

---

## The five core features

| Feature | Route | What it does |
| --- | --- | --- |
| **Waste Passbook** | `/passbook` | Digital ledger of sorted waste per category (PET, paper/cardboard, cans) in a responsive data table with proportion bars, EcoPoints earned and verification status. Filterable by category. |
| **Impact Metric** | `/impact` | Personal dashboard visualising CO₂ reduction: ring gauge against the monthly goal, inline-SVG trend chart, per-material contribution, real-world equivalences, milestones. |
| **Smart Sorting** | `/sorting` | AI camera scanner overlay with reticle, scan sweep and shutter, plus the mandatory **4-step instruction tooltip — Kosongkan · Bilas · Lepas · Remas**. |
| **Waste Pickup** | `/pickup` | Multi-step PHP form (Location → Waste Type → Schedule → Review) with server-side validation, session state and a confirmation screen showing the 6-stage service timeline. |
| **EcoPoints & Gamify** | `/competition` | Daily streak, achievement badges with locked states, rewards catalog, and progress bars tracking the **500 KG monthly Campus Competition Challenge**. |

Plus `/` (aggregate dashboard) and `/profile`.

---

## Responsive design

Mobile-first, built with CSS Grid and Flexbox. Layouts are **fluid** between
breakpoints (`clamp()`, `minmax()`, `auto-fit`) rather than three fixed states.

| Form factor | Range | Behaviour |
| --- | --- | --- |
| **Mobile** | ≤ 767px | Fixed **bottom navigation bar** (5 destinations, safe-area aware); single-column stacked cards; touch targets ≥44px (primary ≥48px, camera shutter 68px); the passbook table collapses into self-labelling cards via `data-label` + `::before`; extra tuning at `min-height: 820px` for tall high-resolution screens (Galaxy S26 Ultra class) |
| **Tablet** | 768–1023px | **Collapsible sidebar** — 76px icon rail by default, expands to a 264px labelled drawer over a scrim, remembered in `localStorage`; two-column dashboard grids; mid-clamp typography; bottom nav hidden |
| **Desktop** | ≥ 1024px | **Persistent sidebar** (264px, always labelled); 3–4 column layouts; two-column split panes for the scanner and pickup wizard; **hover-state animations** on gamification elements (badge lift + ring glow, reward card lift, shine sweep on gamification buttons) gated behind `@media (hover: hover) and (pointer: fine)` |
| **Wide** | ≥ 1440px | Larger gutters, wider content split, prose capped to a readable measure |

All motion is disabled under `prefers-reduced-motion: reduce`.

---

## Architecture

```
Ecoloop2/
├── .kiro/specs/ecoloop-ui/       requirements.md · design.md · tasks.md
├── app/
│   ├── bootstrap.php             paths, session, autoloader
│   ├── Router.php                path → handler, 404/405 fallback
│   ├── View.php                  render(page, data) into the layout
│   ├── helpers.php               e() url() asset() fmt_kg() csrf_field() …
│   ├── Controllers/
│   │   ├── PageController.php    read-only pages
│   │   └── PickupController.php  wizard state machine + validation
│   ├── Data/                     Nav · User · Waste · Impact · Sorting · Pickup · Gamify
│   └── views/
│       ├── layouts/app.php
│       ├── partials/             head · header · sidebar · bottom-nav · page-head · footer
│       ├── components/           icon · stat-card · progress · ring-gauge · spark-chart ·
│       │                         badge · streak · step-indicator · table-passbook · …
│       └── pages/                dashboard · passbook · impact · sorting · pickup ·
│                                 gamify · profile · 404
├── public/                       ← document root
│   ├── index.php                 front controller
│   └── assets/
│       ├── css/  tokens · base · layout · components · features · responsive
│       └── js/   app · passbook · impact · scanner · pickup · gamify
└── tools/                        request.php (CLI harness) · smoke.sh · serve.sh
```

**Templating.** One layout wraps every page. `component('stat-card', [...])` and
`partial('header')` include files with an explicit props array, so nothing leaks
from the surrounding scope. Every component carries a docblock listing its keys,
and every dynamic value is escaped through `e()`.

**Design tokens.** All colour, type, spacing, radius, shadow and motion values are
declared once in `tokens.css` as custom properties. No other stylesheet contains a
raw hex value.

**Iconography.** 60+ line-based icons as inline SVG on a 24px grid, inheriting
`currentColor`. Zero image requests for UI chrome; the charts and gauges are
hand-drawn SVG too — no charting library.

**Progressive enhancement.** Every page renders and functions server-side. The
pickup wizard is real PHP `POST` with server-side validation and session state, so
it works with JavaScript disabled. JS only adds chip filtering, range tabs, counter
tweening, the scanner simulation, sidebar memory and scroll-triggered progress bars.

---

## Accessibility

- Semantic landmarks, skip-to-content link, visible `:focus-visible` rings
- `aria-current="page"` on the active nav item, `aria-current="step"` in the wizard
- `role="progressbar"` with `aria-valuenow/min/max` on every progress bar
- Inline validation errors tied to fields via `aria-invalid` + `aria-describedby`
- The passbook table keeps a real `<thead>` (visually hidden in card mode) so screen
  readers still hear column names
- Decorative SVG is `aria-hidden`; meaningful icons are labelled

---

## Verification

`bash tools/smoke.sh` runs 105 assertions: `php -l` on every PHP file, renders every
route through a CLI request harness checking status codes and required markup, walks
the pickup wizard end-to-end (including four validation-failure paths, back
navigation, the POST-redirect-GET confirmation and a CSRF rejection), and greps the
stylesheets for the required breakpoint coverage.

The UI was also verified visually in a headless browser at mobile (412×915 and
480×1067), tablet (900×1100) and desktop (1440×900) viewports.

---

## Scope

This is the **frontend** implementation. Authentication, database persistence, real
AI inference, live camera capture (`getUserMedia` is simulated), reward fulfilment
and the campus/ESG admin dashboard are out of scope; all data comes from the fixtures
in `app/Data/`.
