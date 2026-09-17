/* ==========================================================================
   EcoLoop — Impact Metric: range tabs, counter tweening, chart redraw
   (requirements R2.3, R2.5, R2.6)
   ========================================================================== */
(function () {
  'use strict';

  var root = document.querySelector('[data-impact-root]');
  if (!root || !window.EcoLoop) return;

  var $ = window.EcoLoop.$;
  var $$ = window.EcoLoop.$$;
  var fmt = window.EcoLoop.formatNumber;

  var ranges;
  var series;
  try {
    ranges = JSON.parse(root.getAttribute('data-ranges') || '{}');
    series = JSON.parse(root.getAttribute('data-series') || '{}');
  } catch (e) {
    return;
  }

  var tabs = $$('[data-range]', root);
  var gauge = root.querySelector('[data-gauge]');
  var gaugeValue = gauge ? gauge.querySelector('.gauge__value') : null;
  var gaugeFill = gauge ? gauge.querySelector('.gauge__fill') : null;
  var goalPct = root.querySelector('[data-impact-goal-pct]');
  var goal = root.querySelector('[data-impact-goal]');
  var equivNodes = $$('[data-impact-equiv]', root);

  /* Nodes outside the hero that also react to the selected range. */
  var co2Node = document.querySelector('[data-impact-co2]');
  var rangeLabel = document.querySelector('[data-impact-range-label]');
  var treesNode = document.querySelector('[data-impact-trees]');
  var chartHost = document.querySelector('[data-impact-chart]');
  var totalNodes = $$('[data-impact-total]');
  var breakdownRows = $$('.breakdown[data-share]');

  /* Stat strip: hook name → [range key, decimals]. */
  var STAT_FIELDS = {
    co2: ['co2', 1],
    diverted: ['diverted', 1],
    water: ['water', 0],
    energy: ['energy', 1]
  };

  var equivFor = function (data) {
    return [
      fmt(data.trees, 1) + ' pohon',
      fmt(data.water, 0) + ' liter',
      fmt(data.energy, 1) + ' kWh'
    ];
  };

  /* Redraw the inline SVG polyline/area for a new dataset. */
  var redrawChart = function (values, labels) {
    if (!chartHost) return;
    var svg = chartHost.querySelector('svg');
    var line = chartHost.querySelector('.spark__line');
    var area = chartHost.querySelector('.spark__area');
    var dots = $$('.spark__dot', chartHost);
    if (!svg || !line || !area) return;

    var box = (svg.getAttribute('viewBox') || '0 0 600 170').split(/\s+/);
    var w = parseFloat(box[2]) || 600;
    var h = parseFloat(box[3]) || 170;
    var padX = 14;
    var padTop = 18;
    var padBottom = 16;

    var max = Math.max.apply(null, values);
    var min = Math.min.apply(null, values);
    var span = Math.max(max - min, 0.0001);
    var top = max + span * 0.18;
    var bottom = Math.max(0, min - span * 0.12);
    var rangeY = Math.max(top - bottom, 0.0001);
    var plotW = w - padX * 2;
    var plotH = h - padTop - padBottom;

    var pts = values.map(function (v, i) {
      var x = values.length === 1 ? w / 2 : padX + (plotW * i / (values.length - 1));
      var y = padTop + plotH * (1 - ((v - bottom) / rangeY));
      return [Math.round(x * 100) / 100, Math.round(y * 100) / 100];
    });

    var linePts = pts.map(function (p) { return p[0] + ',' + p[1]; }).join(' ');
    line.setAttribute('points', linePts);
    area.setAttribute(
      'points',
      linePts + ' ' + pts[pts.length - 1][0] + ',' + (h - padBottom) + ' ' + pts[0][0] + ',' + (h - padBottom)
    );

    dots.forEach(function (dot, i) {
      if (!pts[i]) { dot.setAttribute('r', '0'); return; }
      dot.setAttribute('cx', pts[i][0]);
      dot.setAttribute('cy', pts[i][1]);
      dot.setAttribute('r', i === pts.length - 1 ? '6' : '4.5');
      var title = dot.querySelector('title');
      if (title) title.textContent = (labels[i] || ('#' + (i + 1))) + ': ' + fmt(values[i], 1) + ' kg CO₂';
    });

    var labelHost = chartHost.querySelector('.spark__labels');
    if (labelHost) {
      $$('span', labelHost).forEach(function (span, i) {
        span.textContent = labels[i] || '';
      });
    }
  };

  var select = function (key) {
    var data = ranges[key];
    if (!data) return;

    tabs.forEach(function (tab) {
      tab.setAttribute('aria-selected', tab.getAttribute('data-range') === key ? 'true' : 'false');
    });

    var pct = data.goal > 0 ? Math.min((data.co2 / data.goal) * 100, 100) : 0;

    if (gaugeValue) {
      gaugeValue.innerHTML = fmt(data.co2, 1) + '<span class="t-small"> kg</span>';
    }
    if (gaugeFill) {
      var full = parseFloat(gaugeFill.getAttribute('data-dash-full')) || 0;
      var offset = full * (1 - pct / 100);
      gaugeFill.setAttribute('data-dash-offset', offset);
      gaugeFill.style.strokeDashoffset = offset;
    }
    if (gauge) {
      gauge.setAttribute('aria-label', 'Reduksi CO₂ terhadap target: ' + fmt(pct, 1) + '%');
    }
    if (goalPct) goalPct.textContent = fmt(pct, 0) + '%';
    if (goal) goal.textContent = fmt(data.goal, 0);
    if (co2Node) co2Node.textContent = fmt(data.co2, 1);
    if (treesNode) treesNode.textContent = fmt(data.trees, 1);
    if (rangeLabel) rangeLabel.textContent = data.label;

    var values = equivFor(data);
    equivNodes.forEach(function (node, i) {
      if (values[i]) node.textContent = values[i];
    });

    /* Supporting stat strip follows the selected range. */
    Object.keys(STAT_FIELDS).forEach(function (hook) {
      var node = document.querySelector('[data-stat-hook="' + hook + '"]');
      if (node) {
        var field = STAT_FIELDS[hook];
        var unit = node.querySelector('.stat__unit');
        node.innerHTML = fmt(data[field[0]], field[1]) + (unit ? unit.outerHTML : '');
      }
      var deltaNode = document.querySelector('[data-delta-hook="' + hook + '"]');
      if (deltaNode && data['delta_' + hook]) deltaNode.textContent = data['delta_' + hook];
      var noteNode = document.querySelector('[data-note-hook="co2"]');
      if (noteNode && data.delta_note) noteNode.textContent = data.delta_note;
    });

    /* Material shares are constant; their absolute values scale with the range. */
    totalNodes.forEach(function (node) { node.textContent = fmt(data.co2, 1); });
    breakdownRows.forEach(function (row) {
      var share = parseFloat(row.getAttribute('data-share')) || 0;
      var node = row.querySelector('[data-value-hook="breakdown"]');
      if (node) node.textContent = fmt(data.co2 * share / 100, 1) + ' kg';
    });

    if (series[key]) {
      redrawChart(series[key].values, series[key].labels);
    }
  };

  tabs.forEach(function (tab) {
    tab.addEventListener('click', function () { select(tab.getAttribute('data-range')); });
  });

  var group = $('[data-impact-tabs]', root);
  if (group) window.EcoLoop.bindTablistKeys(group, '[data-range]');
}());
