/* ==========================================================================
   EcoLoop — Waste Passbook: category chip filtering (requirement R1.6)
   Filters ledger rows in place and recomputes the visible totals row.
   ========================================================================== */
(function () {
  'use strict';

  var root = document.querySelector('[data-passbook]');
  if (!root || !window.EcoLoop) return;

  var $$ = window.EcoLoop.$$;
  var group = root.querySelector('[data-passbook-filters]');
  var table = root.querySelector('[data-passbook-table]');
  var emptyNote = root.querySelector('[data-passbook-empty]');
  if (!group || !table) return;

  var chips = $$('[data-filter]', group);
  var rows = $$('tbody tr', table);
  var footer = table.querySelector('[data-passbook-total]');
  var cellKg = table.querySelector('[data-total-kg]');
  var cellPct = table.querySelector('[data-total-pct]');
  var cellPts = table.querySelector('[data-total-points]');

  /* Read each row's numbers once from the rendered markup. */
  var data = rows.map(function (row) {
    var kgCell = row.querySelector('[data-label="Volume"]');
    var ptsCell = row.querySelector('[data-label="EcoPoints"]');
    var pctCell = row.querySelector('.bar-inline__value');
    return {
      row: row,
      category: row.getAttribute('data-category') || '',
      kg: parseFloat((kgCell ? kgCell.textContent : '0').replace(/[^0-9.]/g, '')) || 0,
      pts: parseFloat((ptsCell ? ptsCell.textContent : '0').replace(/[^0-9.]/g, '')) || 0,
      pct: parseFloat((pctCell ? pctCell.textContent : '0').replace(/[^0-9.]/g, '')) || 0
    };
  });

  var fmt = function (value, decimals) {
    return window.EcoLoop.formatNumber(value, decimals);
  };

  var apply = function (key) {
    var kg = 0;
    var pts = 0;
    var pct = 0;
    var shown = 0;

    data.forEach(function (item) {
      var match = key === 'all' || item.category === key;
      item.row.hidden = !match;
      if (match) {
        shown += 1;
        kg += item.kg;
        pts += item.pts;
        pct += item.pct;
      }
    });

    if (cellKg) cellKg.textContent = fmt(kg, 1) + ' KG';
    if (cellPts) cellPts.textContent = '+' + fmt(pts, 0) + ' Pts';
    if (cellPct) cellPct.textContent = fmt(Math.min(pct, 100), 1) + '%';
    if (footer) footer.hidden = shown === 0;
    if (emptyNote) emptyNote.hidden = shown !== 0;
  };

  chips.forEach(function (chip) {
    chip.addEventListener('click', function () {
      chips.forEach(function (other) {
        var active = other === chip;
        other.classList.toggle('is-active', active);
        other.setAttribute('aria-selected', active ? 'true' : 'false');
      });
      apply(chip.getAttribute('data-filter'));
    });
  });

  window.EcoLoop.bindTablistKeys(group, '[data-filter]');
}());
