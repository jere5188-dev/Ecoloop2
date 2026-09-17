# EcoLoop Web Platform — Requirements

## 1. Introduction

EcoLoop is a digital platform and gamification layer that drives sustainable consumption
and waste management on university campuses. Tagline: **"Sort. Collect. Reward. Repeat."**

This spec covers the **frontend UI/UX implementation** of the EcoLoop web platform, built
with PHP (server-side logic and templating), HTML5, CSS3, and vanilla JavaScript. No
framework, no build step, no external runtime dependencies.

### Users

| Role | Description | Needs |
| --- | --- | --- |
| **Primary — Mahasiswa (Student)** | Daily waste producer on campus | Fast, mobile-first sorting and deposit flow; visible rewards |
| **Secondary — Kampus & Bank Sampah** | Facility / ESG managers | Real-time tonnage data, pickup fleet scheduling, reporting |

The interfaces in this scope target the **primary user (student)**, with aggregate campus
data surfaced through the Campus Competition views.

### Design theme

"Green and Dark Green Minimalist — Restoring The Forest". Natural, calm, focused. Minimal
and clean, content over chrome. Accessible and inclusive. Purpose-driven.

---

## 2. Functional Requirements

### R1 — Waste Passbook (Buku Tabungan Digital)

**User story:** As a student, I want to see a ledger of every kilogram of waste I have
sorted, so that I trust the platform is recording my contribution accurately.

**Acceptance criteria:**

1. R1.1 — The page SHALL display aggregate summary tiles: total waste recycled (KG) and
   total EcoPoints accumulated.
2. R1.2 — The page SHALL render a data table with one row per waste category, with the
   columns: Kategori Sampah, Volume (KG), Proporsi (%), EcoPoints Diperoleh, Status
   Verifikasi.
3. R1.3 — Categories SHALL cover at minimum: Botol Plastik (PET), Kertas & Kardus,
   Kaleng & Logam.
4. R1.4 — The Proporsi column SHALL include a visual inline bar in addition to the numeric
   percentage.
5. R1.5 — Verification status SHALL use semantic status colors: verified = success,
   pending = warning, rejected = error.
6. R1.6 — The table SHALL be filterable by category via toggle chips (All / PET / Paper /
   Cardboard / Cans) without a page reload.
7. R1.7 — On mobile (≤767px) the table SHALL transform into stacked cards where each cell
   is labelled, so no horizontal scrolling is required to read a row.
8. R1.8 — A transaction history list SHALL show recent deposits with date, location,
   weight and points.

### R2 — Impact Metric

**User story:** As a student, I want to see the carbon emissions I have prevented, so that
I feel my individual action has measurable value.

**Acceptance criteria:**

1. R2.1 — A hero metric SHALL display CO₂ reduced in KG using the data/metrics typeface,
   rendered inside a circular progress gauge against the monthly goal.
2. R2.2 — The dashboard SHALL display supporting metrics: waste diverted from landfill
   (KG), tree equivalence, and water/energy saved.
3. R2.3 — A 7-point trend chart SHALL visualize CO₂ reduction over recent weeks, drawn
   with inline SVG (no charting library).
4. R2.4 — A per-material breakdown SHALL show each material's share of the total CO₂
   saving with a horizontal bar.
5. R2.5 — A time-range selector (This Week / This Month / All Time) SHALL be present and
   SHALL update the visible figures client-side.
6. R2.6 — All numeric metrics SHALL animate from 0 to their final value on first paint,
   and SHALL respect `prefers-reduced-motion`.

### R3 — Smart Sorting (AI Camera Scanner)

**User story:** As a student who is unsure which bin a package belongs in, I want to point
my camera at it and be told the material and how to prepare it.

**Acceptance criteria:**

1. R3.1 — The UI SHALL present a camera viewport with a dark surface, a scan reticle with
   animated corner brackets, and a shutter control.
2. R3.2 — The scanner SHALL have three visible states: idle, scanning (with progress bar
   and "Scanning..." label), and detected.
3. R3.3 — On detection, a result panel SHALL show the detected material name, its
   technical name, recyclability, estimated weight, and confidence.
4. R3.4 — The result SHALL include the **4-step preparation instruction tooltip** in
   order: **1. Kosongkan** (empty), **2. Bilas** (rinse), **3. Lepas** (remove cap/label),
   **4. Remas** (crush).
5. R3.5 — Each step SHALL be a numbered item with a title, an Indonesian description, and
   an English gloss, and SHALL be expandable/steppable.
6. R3.6 — A dismissible material-detection toast SHALL confirm the detection with the
   estimated weight range.
7. R3.7 — The viewport SHALL be portrait-dominant on mobile and side-by-side with the
   result panel on desktop.
8. R3.8 — Controls SHALL be reachable with a thumb on mobile: shutter ≥64px, secondary
   controls ≥44px.

### R4 — Waste Pickup (Multi-step Form)

**User story:** As a student with a bulk load of recyclables, I want to schedule a
collection at my dorm or faculty building.

**Acceptance criteria:**

1. R4.1 — The flow SHALL be a **4-step wizard** with a numbered step indicator: (1)
   Location, (2) Waste Type, (3) Schedule, (4) Review.
2. R4.2 — Each step SHALL be submitted to the server via PHP `POST`; state SHALL persist
   in the PHP session across steps.
3. R4.3 — Server-side validation SHALL run per step, and errors SHALL be rendered inline
   next to the offending field with an error-colored message and `aria-invalid`.
4. R4.4 — Step 1 SHALL offer radio location options (Dormitory, Faculty Building, Student
   Association Room, Other) with a conditional free-text detail field.
5. R4.5 — Step 2 SHALL offer multi-select waste-type chips (PET, Paper, Cardboard, Cans,
   Mixed) plus an estimated weight input; at least one type SHALL be required.
6. R4.6 — Step 3 SHALL collect preferred date, a time-window selection, and optional
   notes; the date SHALL NOT be in the past.
7. R4.7 — Step 4 SHALL summarize all entries, show estimated EcoPoints, and require
   confirmation before submitting.
8. R4.8 — On success a confirmation view SHALL display a generated request ID and the
   6-stage service timeline (Request → Jenis → Jadwal → Jemput → Timbang → Poin).
9. R4.9 — Back navigation SHALL be possible without losing entered data.
10. R4.10 — The form SHALL work with JavaScript disabled (progressive enhancement).

### R5 — EcoPoints & Gamification

**User story:** As a student, I want streaks, badges and a campus competition, so that
sorting waste becomes a habit rather than a chore.

**Acceptance criteria:**

1. R5.1 — An EcoPoints balance card SHALL display the current balance and a Redeem action.
2. R5.2 — A **daily streak** row SHALL show 7 day-slots, with completed days filled and
   the current day highlighted, plus a flame indicator and day count.
3. R5.3 — A **badge grid** SHALL show at least 4 badges (First Sort, Eco Champion,
   Recycling Hero, Campus Leader) with title, requirement, and locked/unlocked state.
4. R5.4 — Locked badges SHALL be visually de-emphasized and SHALL show progress toward
   unlock.
5. R5.5 — A **Campus Competition** card SHALL show a progress bar toward the **500 KG
   monthly target**, with the current KG, the percentage, and the days remaining.
6. R5.6 — A faculty leaderboard SHALL rank the top teams with their KG and progress.
7. R5.7 — An earning-actions list SHALL show point values (Smart Scan +100, Weekly Mission
   +50, Recycle 1 KG +20, Drop-off Box +30).
8. R5.8 — A rewards catalog SHALL show redeemable items with their point cost and an
   affordability state.
9. R5.9 — On desktop, badges and reward cards SHALL have hover-state animations (lift,
   glow, shine sweep). These SHALL be suppressed on touch devices and under
   `prefers-reduced-motion`.
10. R5.10 — Progress bars SHALL animate to their target width on scroll-into-view.

### R6 — Application Shell & Navigation

1. R6.1 — A single PHP layout SHALL wrap every page; header, sidebar, bottom nav and
   footer SHALL be separate reusable partials.
2. R6.2 — Navigation destinations: Home, Passbook, Impact, Sorting, Pickup, Competition,
   Profile.
3. R6.3 — The active route SHALL be visually indicated and SHALL carry
   `aria-current="page"`.
4. R6.4 — Desktop (≥1024px): a **persistent** left sidebar.
5. R6.5 — Tablet (768–1023px): a **collapsible** sidebar — icon-rail by default,
   expandable to a labelled drawer, with the state remembered in `localStorage`.
6. R6.6 — Mobile (≤767px): the sidebar SHALL be hidden and replaced by a fixed **bottom
   navigation bar** of 5 primary destinations.
7. R6.7 — A dashboard/home page SHALL aggregate condensed widgets from all 5 features.

### R7 — Server-side Architecture

1. R7.1 — A single front controller SHALL route all requests.
2. R7.2 — Routing SHALL map clean paths (`/passbook`, `/impact`, …) to page views.
3. R7.3 — Templating SHALL be modular: `layouts/`, `partials/`, `components/`, `pages/`.
4. R7.4 — Reusable UI pieces (stat card, progress bar, badge, chip, section header, icon)
   SHALL be PHP components invoked with an argument array.
5. R7.5 — All mock data SHALL live in a dedicated data layer, keeping views free of literals.
6. R7.6 — Every dynamic value echoed into HTML SHALL be escaped.
7. R7.7 — Unknown routes SHALL return HTTP 404 with a styled error page.
8. R7.8 — Icons SHALL be an inline SVG component library — no icon font, no image requests.
9. R7.9 — The app SHALL run on `php -S` with zero configuration and zero Composer
   dependencies.

---

## 3. Non-functional Requirements

### N1 — Responsive design (crucial)

Three breakpoints, implemented with CSS Grid and Flexbox, mobile-first:

| Form factor | Range | Requirements |
| --- | --- | --- |
| **Mobile** | ≤767px | Bottom navigation bar; single-column stacked cards; touch targets ≥44px (primary actions ≥48px); tables collapse to labelled cards; fluid type; safe-area insets honored; optimized for tall high-resolution screens (e.g. Galaxy S26 Ultra, ~19.5:9) |
| **Tablet** | 768–1023px | Collapsible sidebar; two-column dashboard grids; typography scaled between mobile and desktop; bottom nav hidden |
| **Desktop** | ≥1024px | Persistent sidebar; multi-column (3–4 col) layouts; hover-state animations on gamification elements; max content width with centered gutters |

Additional:

1. N1.1 — No horizontal page scrolling at any viewport ≥320px wide.
2. N1.2 — Layout SHALL be **fluid** between breakpoints (`clamp()`, `minmax()`,
   `auto-fit`), not merely three fixed layouts.
3. N1.3 — A 1440px+ wide desktop SHALL not stretch text lines beyond a readable measure.
4. N1.4 — Landscape mobile SHALL remain usable (scanner viewport reflows).

### N2 — Accessibility

1. N2.1 — Semantic landmarks: `header`, `nav`, `main`, `aside`, `footer`.
2. N2.2 — Visible focus rings on all interactive elements; keyboard-operable wizard,
   chips, and tabs.
3. N2.3 — Text contrast ≥4.5:1 against its background for body copy.
4. N2.4 — Decorative SVG marked `aria-hidden`; meaningful icons labelled.
5. N2.5 — Progress bars expose `role="progressbar"` with `aria-valuenow/min/max`.
6. N2.6 — A skip-to-content link SHALL be the first focusable element.
7. N2.7 — All animation SHALL be disabled under `prefers-reduced-motion: reduce`.

### N3 — Performance

1. N3.1 — No JS framework; total custom JS under ~30KB unminified.
2. N3.2 — Google Fonts loaded with `preconnect` + `display=swap`; system-font fallback stack.
3. N3.3 — All iconography and charts as inline SVG — zero image HTTP requests for UI chrome.
4. N3.4 — CSS split into token/base/layout/component/responsive files for maintainability.

### N4 — Maintainability

1. N4.1 — Design tokens defined once as CSS custom properties; no hard-coded hex values in
   component CSS.
2. N4.2 — BEM-ish class naming (`block__element--modifier`).
3. N4.3 — PHP components documented with a docblock listing their expected keys.

---

## 4. Out of Scope

- Authentication, real database persistence, real AI inference, real camera capture
  (`getUserMedia` is simulated), payment/redemption processing, the campus/admin ESG
  dashboard, push notifications, and native mobile apps.
