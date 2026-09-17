# EcoLoop Web Platform — Implementation Tasks

Executed sequentially. Each task lists the requirements it satisfies.

## Phase 1 — Foundation

- [x] **1. Project skeleton & front controller**
  - Create `public/index.php`, `app/bootstrap.php`, `app/helpers.php`.
  - Session start, error reporting for dev, base-path detection.
  - _Requirements: R7.1, R7.9_

- [x] **2. Router**
  - `app/Router.php` with GET/POST registration, path normalization, 404 fallback.
  - Register all routes from the design routing table.
  - _Requirements: R7.2, R7.7_

- [x] **3. View engine & layout**
  - `app/View.php` (`render`, `component`, `notFound`), `views/layouts/app.php`,
    `partials/head.php`.
  - Google Fonts preconnect + link, CSS/JS includes, meta viewport with `viewport-fit=cover`.
  - _Requirements: R7.3, R7.4, N3.2_

- [x] **4. Mock data layer**
  - `app/Data/{Nav,User,Waste,Impact,Sorting,Pickup,Gamify}.php` returning typed arrays.
  - _Requirements: R7.5_

## Phase 2 — Design System

- [x] **5. `tokens.css`**
  - Color, typography (`clamp()` scale), spacing, radii, shadow, z-index, motion tokens.
  - _Requirements: N4.1, plus styleguide fidelity_

- [x] **6. `base.css`**
  - Reset, box-sizing, font families, heading/body/data classes, links, focus-visible,
    `.sr-only`, `.skip-link`, reduced-motion base.
  - _Requirements: N2.2, N2.3, N2.6, N2.7_

- [x] **7. Icon component**
  - `views/components/icon.php` — 35-icon inline SVG library, `currentColor`, `aria-hidden`.
  - _Requirements: R7.8, N2.4, N3.3_

- [x] **8. `components.css`**
  - Buttons (primary/secondary/gamification/icon), chips, switch, cards, stat cards,
    ring gauge, progress bars, badges, streak row, table, step indicator, toast, pills.
  - _Requirements: R1.4, R1.5, R5.2, R5.3, N4.2_

- [x] **9. Reusable PHP components**
  - `card`, `stat-card`, `metric`, `progress`, `badge`, `chip-group`, `ring-gauge`,
    `spark-chart`, `step-indicator`, `section-head`, `empty-state`, `table-passbook`.
  - Docblocks for every prop key; all output escaped.
  - _Requirements: R7.4, R7.6, N4.3_

## Phase 3 — App Shell

- [x] **10. Header**
  - Logo lockmark + wordmark + tagline, sidebar toggle (tablet), search, points pill,
    avatar.
  - _Requirements: R6.1, R6.5_

- [x] **11. Sidebar**
  - Nav registry render, active state + `aria-current`, rail/expanded variants, footer
    mini-card. Persistent ≥1024px, collapsible 768–1023px.
  - _Requirements: R6.2, R6.3, R6.4, R6.5_

- [x] **12. Bottom navigation (mobile)**
  - 5 primary destinations, active indicator, safe-area padding, ≥44px targets.
  - _Requirements: R6.6, N1 mobile_

- [x] **13. Footer + layout grid**
  - `.shell` grid areas, content max-width, gutters, skip link target.
  - _Requirements: R6.1, N1.2, N1.3_

## Phase 4 — Features

- [x] **14. Dashboard (home)**
  - Greeting hero, 4 stat cards, condensed passbook, impact ring, competition progress,
    streak, quick actions.
  - _Requirements: R6.7_

- [x] **15. Waste Passbook**
  - Summary strip, filter chips, full ledger table with proportion bars + status badges,
    totals row, transaction timeline. Mobile card transform.
  - _Requirements: R1.1–R1.8_

- [x] **16. Impact Metric**
  - Hero ring gauge + equivalences, range tabs, SVG trend chart, material breakdown,
    animated counters, milestone list.
  - _Requirements: R2.1–R2.6_

- [x] **17. Smart Sorting**
  - Camera viewport, reticle + sweep, shutter/controls, state machine, detection result
    panel, 4-step tooltip (Kosongkan/Bilas/Lepas/Remas), detection toast, material guide.
  - _Requirements: R3.1–R3.8_

- [x] **18. Waste Pickup wizard**
  - `PickupController` state machine, 4 steps, per-step server validation, inline errors,
    review + confirm, request ID, 6-stage timeline, no-JS operation.
  - _Requirements: R4.1–R4.10_

- [x] **19. EcoPoints & Gamify**
  - Balance card, 7-day streak, badge grid with locked states, 500 KG competition progress,
    leaderboard, earning actions, rewards catalog.
  - _Requirements: R5.1–R5.10_

- [x] **20. Profile + 404**
  - Profile summary, achievements, settings switches; styled 404.
  - _Requirements: R6.2, R7.7_

## Phase 5 — Responsive & Polish

- [x] **21. `responsive.css`**
  - Mobile base refinements, `@media (min-width:768px)`, `@media (min-width:1024px)`,
    `@media (min-width:1440px)`, mobile-only table transform, tall-screen tuning,
    landscape scanner, `hover: hover` gating, reduced-motion.
  - _Requirements: N1.1–N1.4, R1.7, R5.9, N2.7_

- [x] **22. JavaScript modules**
  - `app.js`, `passbook.js`, `impact.js`, `scanner.js`, `pickup.js`, `gamify.js`.
  - _Requirements: R1.6, R2.5, R2.6, R3.2, R3.5, R5.10, N3.1_

## Phase 6 — Verification

- [x] **23. Lint & smoke test**
  - `php -l` on every PHP file; `php -S` and curl every route asserting HTTP 200 and
    expected markers; exercise the pickup POST flow end-to-end including a validation error.
  - _Requirements: all_

- [x] **24. README + delivery**
  - Run instructions, structure, breakpoint table. Commit to a feature branch, push, open PR.
