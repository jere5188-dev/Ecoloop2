/* ==========================================================================
   EcoLoop — EcoPoints & Gamify
   Redeem feedback and badge hover gating. Progress-bar reveal and counters are
   handled centrally in app.js (requirements R5.9, R5.10).
   ========================================================================== */
(function () {
  'use strict';

  if (!window.EcoLoop) return;
  var $$ = window.EcoLoop.$$;

  /* --- Redeem buttons ---------------------------------------------------- */
  var toast = document.querySelector('[data-redeem-toast]');
  var hideTimer = null;

  $$('[data-redeem]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      if (!toast) return;
      toast.hidden = false;
      if (hideTimer) window.clearTimeout(hideTimer);
      hideTimer = window.setTimeout(function () { toast.hidden = true; }, 4200);
    });
  });

  /* --- Touch devices: strip hover-only affordances ---------------------- */
  var fine = window.matchMedia('(hover: hover) and (pointer: fine)');
  if (!fine.matches) {
    document.body.classList.add('is-touch');
  }

  /* --- Locked badges announce their remaining progress ------------------ */
  $$('.badge-card.is-locked').forEach(function (card) {
    var bar = card.querySelector('[role="progressbar"]');
    if (!bar) return;
    var value = parseFloat(bar.getAttribute('aria-valuenow')) || 0;
    card.setAttribute('title', 'Belum terbuka — progres ' + value.toFixed(0) + '%');
  });
}());
