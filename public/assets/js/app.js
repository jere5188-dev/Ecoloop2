/* ==========================================================================
   EcoLoop — core behaviour
   Vanilla ES2019, no dependencies. Everything here is progressive
   enhancement: the server-rendered page is already complete and usable.
   ========================================================================== */
(function () {
  'use strict';

  var $ = function (sel, root) { return (root || document).querySelector(sel); };
  var $$ = function (sel, root) {
    return Array.prototype.slice.call((root || document).querySelectorAll(sel));
  };

  var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  /* Public namespace consumed by the feature modules. */
  var EcoLoop = {
    $: $,
    $$: $$,
    reduceMotion: reduceMotion,

    /** Tween a number into an element. Respects reduced-motion. */
    countTo: function (el, target, decimals, duration) {
      if (!el) return;
      var suffixNode = el.querySelector('span');
      var suffix = suffixNode ? suffixNode.outerHTML : '';
      var dec = typeof decimals === 'number' ? decimals : (target % 1 === 0 ? 0 : 1);

      var write = function (value) {
        el.innerHTML = EcoLoop.formatNumber(value, dec) + suffix;
      };

      if (reduceMotion || !window.requestAnimationFrame) {
        write(target);
        return;
      }

      var dur = duration || 900;
      var start = null;
      var step = function (ts) {
        if (start === null) start = ts;
        var p = Math.min((ts - start) / dur, 1);
        // ease-out cubic
        var eased = 1 - Math.pow(1 - p, 3);
        write(target * eased);
        if (p < 1) window.requestAnimationFrame(step);
      };
      window.requestAnimationFrame(step);
    },

    formatNumber: function (value, decimals) {
      return Number(value).toLocaleString('en-US', {
        minimumFractionDigits: decimals,
        maximumFractionDigits: decimals
      });
    },

    /** Run a callback the first time an element scrolls into view. */
    onReveal: function (el, cb) {
      if (!('IntersectionObserver' in window)) { cb(el); return; }
      var io = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
          if (entry.isIntersecting) {
            cb(entry.target);
            io.unobserve(entry.target);
          }
        });
      }, { threshold: 0.2, rootMargin: '0px 0px -40px 0px' });
      io.observe(el);
    },

    /** Arrow-key navigation for a chip/tab group. */
    bindTablistKeys: function (container, selector) {
      container.addEventListener('keydown', function (ev) {
        if (ev.key !== 'ArrowRight' && ev.key !== 'ArrowLeft') return;
        var items = $$(selector, container);
        var idx = items.indexOf(document.activeElement);
        if (idx === -1) return;
        ev.preventDefault();
        var next = ev.key === 'ArrowRight' ? idx + 1 : idx - 1;
        if (next < 0) next = items.length - 1;
        if (next >= items.length) next = 0;
        items[next].focus();
        items[next].click();
      });
    }
  };

  window.EcoLoop = EcoLoop;

  /* ------------------------------------------------------------------------
     Sidebar: collapsible on tablet, persistent on desktop.
     The choice is remembered so it survives navigation (requirement R6.5).
     ------------------------------------------------------------------------ */
  (function sidebar() {
    var shell = $('#shell');
    var toggle = $('#sidebar-toggle');
    var scrim = $('#sidebar-scrim');
    var aside = $('#app-sidebar');
    if (!shell || !toggle) return;

    var KEY = 'ecoloop:sidebar';
    var desktop = window.matchMedia('(min-width: 1024px)');

    var setState = function (state, persist) {
      shell.setAttribute('data-sidebar', state);
      toggle.setAttribute('aria-expanded', state === 'expanded' ? 'true' : 'false');
      if (scrim) scrim.hidden = !(state === 'expanded' && !desktop.matches);
      if (persist) {
        try { window.localStorage.setItem(KEY, state); } catch (e) { /* private mode */ }
      }
    };

    var stored = null;
    try { stored = window.localStorage.getItem(KEY); } catch (e) { stored = null; }
    setState(stored === 'expanded' && !desktop.matches ? 'expanded' : 'collapsed', false);

    toggle.addEventListener('click', function () {
      var next = shell.getAttribute('data-sidebar') === 'expanded' ? 'collapsed' : 'expanded';
      setState(next, true);
      if (next === 'expanded' && aside) {
        var firstLink = $('.app-nav__item', aside);
        if (firstLink) firstLink.focus();
      }
    });

    if (scrim) {
      scrim.addEventListener('click', function () { setState('collapsed', true); });
    }

    document.addEventListener('keydown', function (ev) {
      if (ev.key === 'Escape' && shell.getAttribute('data-sidebar') === 'expanded') {
        setState('collapsed', true);
        toggle.focus();
      }
    });

    // Collapsing the overlay when the viewport grows past the desktop threshold.
    var onChange = function () {
      if (desktop.matches) setState('collapsed', false);
      else if (scrim) scrim.hidden = shell.getAttribute('data-sidebar') !== 'expanded';
    };
    if (desktop.addEventListener) desktop.addEventListener('change', onChange);
    else if (desktop.addListener) desktop.addListener(onChange);
  }());

  /* ------------------------------------------------------------------------
     Progress bars: animate to their target width on scroll-into-view (R5.10).
     ------------------------------------------------------------------------ */
  (function progressBars() {
    var bars = $$('[data-progress]');
    bars.forEach(function (bar) {
      EcoLoop.onReveal(bar, function (el) { el.classList.add('is-revealed'); });
    });
    // Safety net: if something prevented the observer from firing, reveal all.
    window.setTimeout(function () {
      bars.forEach(function (bar) { bar.classList.add('is-revealed'); });
    }, 1600);
  }());

  /* ------------------------------------------------------------------------
     Ring gauges: run the stroke-dashoffset transition once visible.
     ------------------------------------------------------------------------ */
  (function gauges() {
    $$('[data-gauge]').forEach(function (gauge) {
      var fill = gauge.querySelector('.gauge__fill');
      if (!fill) return;
      var target = fill.getAttribute('data-dash-offset');
      var apply = function () { fill.style.strokeDashoffset = target; };
      if (reduceMotion) { apply(); return; }
      EcoLoop.onReveal(gauge, function () { window.setTimeout(apply, 80); });
      window.setTimeout(apply, 1600);
    });
  }());

  /* ------------------------------------------------------------------------
     Animated metric counters (R2.6).
     ------------------------------------------------------------------------ */
  (function counters() {
    $$('[data-count-to]').forEach(function (el) {
      var target = parseFloat(el.getAttribute('data-count-to'));
      if (isNaN(target)) return;
      var raw = el.textContent.trim();
      var decimals = raw.indexOf('.') > -1 ? 1 : 0;
      EcoLoop.onReveal(el, function () { EcoLoop.countTo(el, target, decimals); });
    });
  }());

  /* ------------------------------------------------------------------------
     Dismissible toasts.
     ------------------------------------------------------------------------ */
  document.addEventListener('click', function (ev) {
    var btn = ev.target.closest ? ev.target.closest('[data-toast-close]') : null;
    if (!btn) return;
    var host = btn.closest('[data-scanner-toast], [data-redeem-toast]') || btn.closest('.toast');
    if (host) host.hidden = true;
  });

  /* ------------------------------------------------------------------------
     Profile preference switches — echo state for assistive tech.
     ------------------------------------------------------------------------ */
  $$('.switch input[type="checkbox"]').forEach(function (input) {
    input.addEventListener('change', function () {
      input.setAttribute('aria-checked', input.checked ? 'true' : 'false');
    });
  });
}());
