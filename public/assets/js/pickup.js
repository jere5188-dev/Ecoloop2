/* ==========================================================================
   EcoLoop — Waste Pickup wizard: client-side hints only.
   All real validation and state transitions happen server-side in PHP, so the
   form still works with JavaScript disabled (requirement R4.10).
   ========================================================================== */
(function () {
  'use strict';

  var root = document.querySelector('[data-pickup]');
  if (!root || !window.EcoLoop) return;

  var $ = window.EcoLoop.$;
  var $$ = window.EcoLoop.$$;

  /* --- Step 1: reveal the detail field when "Lokasi Lain" is chosen ----- */
  (function locationDetail() {
    var group = $('[data-pickup-locations]', root);
    var detailWrap = $('[data-pickup-detail]', root);
    if (!group || !detailWrap) return;

    var input = detailWrap.querySelector('input');
    var hint = detailWrap.querySelector('.field__hint');
    var radios = $$('input[name="location"]', group);

    var sync = function () {
      var picked = radios.filter(function (r) { return r.checked; })[0];
      var isOther = picked && picked.value === 'other';
      if (input) {
        input.required = !!isOther;
        input.placeholder = isOther
          ? 'Wajib: tulis titik temu selengkap mungkin'
          : 'Contoh: Asrama Dahlia blok D2, depan pos jaga';
      }
      if (hint) {
        hint.textContent = isOther
          ? 'Wajib diisi karena kamu memilih “Lokasi Lain”.'
          : 'Opsional — membantu petugas menemukan lokasi lebih cepat.';
      }
      detailWrap.classList.toggle('is-required', !!isOther);
    };

    radios.forEach(function (radio) { radio.addEventListener('change', sync); });
    sync();
  }());

  /* --- Step 2: require at least one waste type, live estimate ----------- */
  (function wasteTypes() {
    var group = $('[data-pickup-types]', root);
    if (!group) return;

    var boxes = $$('input[type="checkbox"]', group);
    var form = root.querySelector('form');
    if (!form) return;

    form.addEventListener('submit', function (ev) {
      var action = ev.submitter ? ev.submitter.value : 'next';
      if (action === 'back' || action === 'edit') return;
      var any = boxes.some(function (b) { return b.checked; });
      if (any) return;

      ev.preventDefault();
      var existing = group.parentNode.querySelector('.field__error');
      if (!existing) {
        var msg = document.createElement('p');
        msg.className = 'field__error';
        msg.setAttribute('role', 'alert');
        msg.textContent = 'Pilih minimal satu jenis sampah.';
        group.parentNode.appendChild(msg);
      }
      boxes[0].focus();
    });
  }());

  /* --- Step 3: keep the date within the allowed window ------------------ */
  (function dateGuard() {
    var date = $('#date', root);
    if (!date) return;
    date.addEventListener('change', function () {
      if (date.min && date.value && date.value < date.min) date.value = date.min;
      if (date.max && date.value && date.value > date.max) date.value = date.max;
    });
  }());

  /* --- Focus the first invalid field after a server-side rejection ------ */
  (function focusError() {
    var invalid = root.querySelector('[aria-invalid="true"]');
    if (invalid && typeof invalid.focus === 'function') {
      invalid.focus({ preventScroll: false });
    }
  }());
}());
