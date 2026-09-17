#!/usr/bin/env bash
# ============================================================================
# EcoLoop — smoke test
#
# Renders every route through the CLI harness and asserts that the response has
# the expected status code and contains the markers each feature must render.
# Also walks the Waste Pickup wizard end-to-end, including a validation failure.
#
#   bash tools/smoke.sh
# ============================================================================
set -uo pipefail

cd "$(dirname "$0")/.."

PASS=0
FAIL=0
TMP=$(mktemp -d)
trap 'rm -rf "$TMP"' EXIT

# render METHOD URI [BODY] -> body on stdout, meta (STATUS/REDIRECT) in $TMP/meta
render() {
  php tools/request.php "$1" "$2" "${3:-}" 2>"$TMP/meta"
}

status_of() { grep '^STATUS ' "$TMP/meta" | tail -1 | awk '{print $2}'; }
redirect_of() { grep '^REDIRECT ' "$TMP/meta" | tail -1 | awk '{print $2}'; }

check() { # check <label> <condition-result 0/1>
  if [ "$2" -eq 0 ]; then
    printf '  \033[32mPASS\033[0m  %s\n' "$1"; PASS=$((PASS + 1))
  else
    printf '  \033[31mFAIL\033[0m  %s\n' "$1"; FAIL=$((FAIL + 1))
  fi
}

expect_status() { # expect_status <label> <expected>
  local got; got=$(status_of)
  [ "$got" = "$2" ]; check "$1 → HTTP $2 (got ${got:-none})" $?
}

expect_contains() { # expect_contains <file> <needle> <label>
  grep -qF -- "$2" "$1"; check "$3" $?
}

expect_absent() {
  ! grep -qiE 'Fatal error|Parse error|Warning:|Notice:|Deprecated:|missing component|missing partial' "$1"
  check "$2" $?
}

echo
echo "── PHP lint ──────────────────────────────────────────────────────────"
LINT=$(find . -name '*.php' -not -path './.git/*' -exec php -l {} \; 2>&1 | grep -v 'No syntax errors' || true)
[ -z "$LINT" ]; check "all PHP files parse" $?
[ -n "$LINT" ] && echo "$LINT"

echo
echo "── Route: / (Dashboard) ──────────────────────────────────────────────"
render GET / > "$TMP/home"
expect_status "GET /" 200
expect_absent "$TMP/home" "no PHP diagnostics in output"
expect_contains "$TMP/home" 'Sort. Collect. Reward. Repeat.' "brand tagline present"
expect_contains "$TMP/home" 'class="bottom-nav"' "mobile bottom navigation rendered"
expect_contains "$TMP/home" 'id="app-sidebar"' "sidebar rendered"
expect_contains "$TMP/home" 'Waste Passbook' "passbook widget"
expect_contains "$TMP/home" 'Impact Metric' "impact widget"
expect_contains "$TMP/home" 'Campus Competition' "competition widget"
expect_contains "$TMP/home" 'Daily Streak' "streak widget"
expect_contains "$TMP/home" 'skip-link' "skip-to-content link"
expect_contains "$TMP/home" 'Plus+Jakarta+Sans' "Google Fonts loaded"
expect_contains "$TMP/home" 'Space+Grotesk' "data typeface loaded"

echo
echo "── Route: /passbook (Waste Passbook) ─────────────────────────────────"
render GET /passbook > "$TMP/pb"
expect_status "GET /passbook" 200
expect_absent "$TMP/pb" "no PHP diagnostics in output"
expect_contains "$TMP/pb" 'Kategori Sampah' "table column: Kategori Sampah"
expect_contains "$TMP/pb" 'Volume (KG)' "table column: Volume (KG)"
expect_contains "$TMP/pb" 'Proporsi' "table column: Proporsi"
expect_contains "$TMP/pb" 'EcoPoints Diperoleh' "table column: EcoPoints Diperoleh"
expect_contains "$TMP/pb" 'Status Verifikasi' "table column: Status Verifikasi"
expect_contains "$TMP/pb" 'Botol Plastik (PET)' "PET row"
expect_contains "$TMP/pb" 'Kertas &amp; Kardus' "paper row"
expect_contains "$TMP/pb" 'Kaleng &amp; Logam' "cans row"
expect_contains "$TMP/pb" 'data-label="Volume"' "cells carry data-label for the mobile card transform"
expect_contains "$TMP/pb" 'data-filter="pet"' "category filter chips"
expect_contains "$TMP/pb" '12.4 KG' "aggregate total volume"
expect_contains "$TMP/pb" 'Riwayat Transaksi' "transaction history"

echo
echo "── Route: /impact (Impact Metric) ────────────────────────────────────"
render GET /impact > "$TMP/im"
expect_status "GET /impact" 200
expect_absent "$TMP/im" "no PHP diagnostics in output"
expect_contains "$TMP/im" 'CO₂ Reduced' "CO2 hero metric"
expect_contains "$TMP/im" 'gauge__fill' "ring gauge rendered"
expect_contains "$TMP/im" 'spark__line' "inline SVG trend chart"
expect_contains "$TMP/im" 'data-range="month"' "range selector"
expect_contains "$TMP/im" 'Kontribusi per Material' "per-material breakdown"
expect_contains "$TMP/im" 'role="progressbar"' "accessible progress bars"

echo
echo "── Route: /sorting (Smart Sorting) ───────────────────────────────────"
render GET /sorting > "$TMP/so"
expect_status "GET /sorting" 200
expect_absent "$TMP/so" "no PHP diagnostics in output"
expect_contains "$TMP/so" 'data-scanner' "scanner root"
expect_contains "$TMP/so" 'reticle__corner' "scan reticle brackets"
expect_contains "$TMP/so" 'class="shutter"' "shutter control"
expect_contains "$TMP/so" 'Kosongkan' "step 1 Kosongkan"
expect_contains "$TMP/so" 'Bilas' "step 2 Bilas"
expect_contains "$TMP/so" 'Lepas' "step 3 Lepas"
expect_contains "$TMP/so" 'Remas' "step 4 Remas"
expect_contains "$TMP/so" 'data-prep-step="4"' "all four steps enumerated"
expect_contains "$TMP/so" 'data-scanner-toast' "material detection toast"
expect_contains "$TMP/so" 'Scanning' "scanning state label"

echo
echo "── Route: /competition (EcoPoints & Gamify) ──────────────────────────"
render GET /competition > "$TMP/gm"
expect_status "GET /competition" 200
expect_absent "$TMP/gm" "no PHP diagnostics in output"
expect_contains "$TMP/gm" 'EcoPoints Balance' "balance card"
expect_contains "$TMP/gm" 'Daily Streak' "daily streak"
expect_contains "$TMP/gm" 'First Sort' "badge: First Sort"
expect_contains "$TMP/gm" 'Eco Champion' "badge: Eco Champion"
expect_contains "$TMP/gm" 'Recycling Hero' "badge: Recycling Hero"
expect_contains "$TMP/gm" 'Campus Leader' "badge: Campus Leader"
expect_contains "$TMP/gm" 'is-locked' "locked badge state"
expect_contains "$TMP/gm" '500 kg' "500 KG monthly target"
expect_contains "$TMP/gm" 'aria-valuenow="64"' "competition progress at 64%"
expect_contains "$TMP/gm" 'Fakultas Teknik' "leaderboard"
expect_contains "$TMP/gm" 'Claim Reward' "rewards catalog"

echo
echo "── Route: /profile and 404 ───────────────────────────────────────────"
render GET /profile > "$TMP/pr"
expect_status "GET /profile" 200
expect_absent "$TMP/pr" "no PHP diagnostics in output"
render GET /does-not-exist > "$TMP/nf"
expect_status "GET /does-not-exist" 404
expect_contains "$TMP/nf" 'tidak ditemukan' "styled 404 page"

echo
echo "── Waste Pickup wizard (multi-step POST flow) ────────────────────────"
export ECOLOOP_SID="smoke$$"
rm -f "/tmp/sess_$ECOLOOP_SID"

render GET /pickup > "$TMP/w1"
expect_status "GET /pickup (step 1)" 200
expect_absent "$TMP/w1" "no PHP diagnostics in output"
expect_contains "$TMP/w1" 'Select Location' "step 1 renders the location chooser"
expect_contains "$TMP/w1" 'class="steps"' "4-step indicator"
expect_contains "$TMP/w1" 'aria-current="step"' "current step marked for assistive tech"
CSRF=$(grep -o 'name="_csrf" value="[a-f0-9]*"' "$TMP/w1" | head -1 | sed 's/.*value="\(.*\)"/\1/')
[ -n "$CSRF" ]; check "CSRF token issued" $?

# --- validation failure path -------------------------------------------------
render POST /pickup "_csrf=$CSRF&step=1&action=next&location=&contact=" > "$TMP/e1"
expect_contains "$TMP/e1" 'Pilih salah satu lokasi penjemputan.' "step 1 rejects a missing location"
expect_contains "$TMP/e1" 'Nomor WhatsApp diperlukan' "step 1 rejects a missing contact"
expect_contains "$TMP/e1" 'aria-invalid="true"' "invalid fields flagged with aria-invalid"
expect_contains "$TMP/e1" 'Select Location' "invalid submit re-renders the same step"

# --- step 1 valid ------------------------------------------------------------
render POST /pickup "_csrf=$CSRF&step=1&action=next&location=dormitory&location_detail=Asrama+Dahlia+D2&contact=081234567890" > "$TMP/w2"
expect_contains "$TMP/w2" 'Waste Type' "advances to step 2"
expect_contains "$TMP/w2" 'name="types[]"' "waste-type multi-select"

# --- step 2 invalid: no type, weight below minimum ---------------------------
render POST /pickup "_csrf=$CSRF&step=2&action=next&weight=1" > "$TMP/e2"
expect_contains "$TMP/e2" 'Pilih minimal satu jenis sampah.' "step 2 requires at least one waste type"
expect_contains "$TMP/e2" 'mulai dari 2 kg' "step 2 enforces the 2 kg minimum"

# --- step 2 valid ------------------------------------------------------------
render POST /pickup "_csrf=$CSRF&step=2&action=next&types[]=pet&types[]=cardboard&weight=14.5&container=karung" > "$TMP/w3"
expect_contains "$TMP/w3" 'Preferred Date' "advances to step 3"
expect_contains "$TMP/w3" 'name="window"' "time-window selection"

# --- step 3 invalid: past date ----------------------------------------------
render POST /pickup "_csrf=$CSRF&step=3&action=next&date=2020-01-01&window=08-10" > "$TMP/e3"
expect_contains "$TMP/e3" 'Tanggal tidak boleh di masa lalu.' "step 3 rejects a past date"

# --- step 3 valid ------------------------------------------------------------
TOMORROW=$(date -d '+1 day' +%Y-%m-%d 2>/dev/null || date -v+1d +%Y-%m-%d)
render POST /pickup "_csrf=$CSRF&step=3&action=next&date=$TOMORROW&window=13-15&notes=Hubungi+dulu" > "$TMP/w4"
expect_contains "$TMP/w4" 'Periksa kembali permintaanmu' "advances to step 4 review"
expect_contains "$TMP/w4" 'Asrama Dahlia D2' "review retains the step 1 answer"
expect_contains "$TMP/w4" '14.5 KG' "review retains the step 2 weight"
expect_contains "$TMP/w4" 'Estimasi EcoPoints' "review shows the estimated points"

# --- step 4 without confirmation --------------------------------------------
render POST /pickup "_csrf=$CSRF&step=4&action=next" > "$TMP/e4"
expect_contains "$TMP/e4" 'Centang konfirmasi' "step 4 requires the confirmation checkbox"

# --- back navigation preserves data -----------------------------------------
render POST /pickup "_csrf=$CSRF&step=4&action=back" > "$TMP/b3"
expect_contains "$TMP/b3" "value=\"$TOMORROW\"" "back navigation preserves the chosen date"
render POST /pickup "_csrf=$CSRF&step=3&action=next&date=$TOMORROW&window=13-15&notes=Hubungi+dulu" > /dev/null

# --- confirm -> redirect -> confirmation screen ------------------------------
render POST /pickup "_csrf=$CSRF&step=4&action=next&confirm=yes" > "$TMP/done1"
REDIR=$(redirect_of)
[ "$REDIR" = "/pickup?step=done" ]; check "confirming redirects to the done screen (got ${REDIR:-none})" $?

render GET "/pickup?step=done" > "$TMP/done"
expect_status "GET /pickup?step=done" 200
expect_absent "$TMP/done" "no PHP diagnostics in output"
expect_contains "$TMP/done" 'Permintaan pickup terkirim' "confirmation headline"
grep -qE 'ECO-[0-9]{6}-[0-9]{4}' "$TMP/done"; check "generated request ID" $?
expect_contains "$TMP/done" 'Timbang Digital' "6-stage service timeline"
expect_contains "$TMP/done" 'Poin Masuk' "final timeline stage"

# --- CSRF rejection ---------------------------------------------------------
render POST /pickup "_csrf=bogus&step=1&action=next&location=dormitory&contact=081234567890" > "$TMP/csrf"
expect_contains "$TMP/csrf" 'Sesi telah kedaluwarsa' "bad CSRF token is rejected"

echo
echo "── Responsive CSS coverage ───────────────────────────────────────────"
CSS=public/assets/css/responsive.css
grep -q 'max-width: 767px' "$CSS"; check "mobile breakpoint (max-width: 767px)" $?
grep -q 'min-width: 768px' "$CSS"; check "tablet breakpoint (min-width: 768px)" $?
grep -q 'min-width: 1024px' "$CSS"; check "desktop breakpoint (min-width: 1024px)" $?
grep -q 'min-width: 1440px' "$CSS"; check "wide-desktop refinement (min-width: 1440px)" $?
grep -q 'hover: hover' "$CSS"; check "hover animations gated to fine pointers" $?
grep -q 'min-height: 820px' "$CSS"; check "tall-screen tuning for high-res phones" $?
grep -q 'orientation: landscape' "$CSS"; check "landscape scanner reflow" $?
grep -q 'attr(data-label)' "$CSS"; check "mobile table-to-card transform" $?
grep -q 'safe-area-inset-bottom' public/assets/css/layout.css; check "safe-area inset honoured" $?
grep -q 'prefers-reduced-motion' public/assets/css/base.css; check "reduced-motion kill switch" $?

echo
echo "──────────────────────────────────────────────────────────────────────"
printf 'PASS: %d   FAIL: %d\n' "$PASS" "$FAIL"
echo
[ "$FAIL" -eq 0 ] || exit 1
