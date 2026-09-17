/* ==========================================================================
   EcoLoop — Smart Sorting scanner
   A three-state machine: idle → scanning → detected (requirement R3.2), plus
   the 4-step preparation stepper (R3.5) and the detection toast (R3.6).
   Camera access is simulated; nothing touches getUserMedia.
   ========================================================================== */
(function () {
  'use strict';

  var scanner = document.querySelector('[data-scanner]');
  if (!scanner || !window.EcoLoop) return;

  var $ = window.EcoLoop.$;
  var $$ = window.EcoLoop.$$;
  var reduceMotion = window.EcoLoop.reduceMotion;

  var statusText = $('[data-status-text]', scanner);
  var progressBox = $('[data-scan-progress]', scanner);
  var progressFill = $('[data-scan-fill]', scanner);
  var confidence = $('[data-scanner-conf]', scanner);
  var idlePanel = $('[data-scanner-idle]', scanner);
  var resultPanel = $('[data-scanner-result]', scanner);
  var toast = $('[data-scanner-toast]', scanner);
  var shutters = $$('[data-scanner-shutter]', scanner);
  var resets = $$('[data-scanner-reset]', scanner);

  var timers = [];
  var later = function (fn, ms) { timers.push(window.setTimeout(fn, ms)); };
  var clearTimers = function () {
    timers.forEach(window.clearTimeout);
    timers = [];
  };

  var setState = function (state) {
    scanner.setAttribute('data-state', state);
  };

  /* ---------------------------------------------------------------------- */
  /* States                                                                 */
  /* ---------------------------------------------------------------------- */
  var toIdle = function () {
    clearTimers();
    setState('idle');
    if (statusText) statusText.textContent = 'Arahkan kamera ke kemasan sampah';
    if (progressBox) progressBox.hidden = true;
    if (progressFill) progressFill.style.width = '0%';
    if (confidence) confidence.hidden = true;
    if (resultPanel) resultPanel.hidden = true;
    if (idlePanel) idlePanel.hidden = false;
    if (toast) toast.hidden = true;
    setStep(1);
  };

  var toDetected = function () {
    setState('detected');
    if (statusText) statusText.textContent = 'Material terdeteksi — ikuti 4 langkah persiapan';
    if (progressBox) progressBox.hidden = true;
    if (confidence) confidence.hidden = false;
    if (idlePanel) idlePanel.hidden = true;
    if (resultPanel) {
      resultPanel.hidden = false;
      resultPanel.setAttribute('tabindex', '-1');
      resultPanel.focus({ preventScroll: true });
    }
    if (toast) toast.hidden = false;
    setStep(1);
  };

  var toScanning = function () {
    if (scanner.getAttribute('data-state') === 'scanning') return;
    clearTimers();
    setState('scanning');
    if (statusText) statusText.textContent = 'Menganalisis material…';
    if (idlePanel) idlePanel.hidden = true;
    if (resultPanel) resultPanel.hidden = true;
    if (toast) toast.hidden = true;
    if (confidence) confidence.hidden = true;

    if (progressBox) progressBox.hidden = false;

    if (reduceMotion) {
      if (progressFill) progressFill.style.width = '100%';
      later(toDetected, 320);
      return;
    }

    var pct = 0;
    var tick = function () {
      pct += 6 + Math.random() * 9;
      if (pct >= 100) {
        pct = 100;
        if (progressFill) progressFill.style.width = '100%';
        later(toDetected, 260);
        return;
      }
      if (progressFill) progressFill.style.width = pct.toFixed(1) + '%';
      later(tick, 90);
    };
    if (progressFill) progressFill.style.width = '0%';
    later(tick, 120);
  };

  shutters.forEach(function (btn) {
    btn.addEventListener('click', function () {
      if (scanner.getAttribute('data-state') === 'detected') { toIdle(); return; }
      toScanning();
    });
  });

  resets.forEach(function (btn) {
    btn.addEventListener('click', toIdle);
  });

  /* ---------------------------------------------------------------------- */
  /* 4-step preparation stepper (Kosongkan · Bilas · Lepas · Remas)          */
  /* ---------------------------------------------------------------------- */
  var steps = $$('[data-prep-step]', scanner);
  var prev = $('[data-prep-prev]', scanner);
  var next = $('[data-prep-next]', scanner);
  var counter = $('[data-prep-counter]', scanner);
  var current = 1;

  function setStep(no) {
    if (!steps.length) return;
    current = Math.max(1, Math.min(steps.length, no));

    steps.forEach(function (step) {
      var isCurrent = parseInt(step.getAttribute('data-prep-step'), 10) === current;
      step.classList.toggle('is-current', isCurrent);
      var toggle = step.querySelector('.prep__toggle');
      if (toggle) toggle.setAttribute('aria-expanded', isCurrent ? 'true' : 'false');
    });

    if (counter) counter.textContent = 'Langkah ' + current + ' dari ' + steps.length;
    if (prev) prev.disabled = current === 1;
    if (next) {
      next.disabled = false;
      next.classList.toggle('btn--gamify', current === steps.length);
      next.classList.toggle('btn--primary', current !== steps.length);
    }
  }

  steps.forEach(function (step) {
    var toggle = step.querySelector('.prep__toggle');
    if (!toggle) return;
    toggle.addEventListener('click', function () {
      setStep(parseInt(step.getAttribute('data-prep-step'), 10));
    });
  });

  if (prev) prev.addEventListener('click', function () { setStep(current - 1); });
  if (next) {
    next.addEventListener('click', function () {
      if (current === steps.length) { setStep(1); return; }
      setStep(current + 1);
    });
  }

  setStep(1);
}());
