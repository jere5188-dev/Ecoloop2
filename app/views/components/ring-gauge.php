<?php
/**
 * Component: ring-gauge
 *
 * Circular progress gauge built from two SVG circles and stroke-dasharray
 * (requirement R2.1, styleguide 04 "Impact Metric").
 *
 * Props:
 *   value   float   percentage 0–100                (required)
 *   display string  big centered value, e.g. '18.6' (required)
 *   unit    string  small suffix inside the value, e.g. 'kg'
 *   label   string  caption under the value
 *   size    int     pixel diameter                  (default 148)
 *   stroke  int     ring thickness                  (default 10)
 *   dark    bool    on-dark variant
 *   leaf    bool    leaf-green fill instead of forest
 *   aria    string  accessible name
 */

$value = clamp_pct((float) ($props['value'] ?? 0));
$size = (int) ($props['size'] ?? 148);
$stroke = (int) ($props['stroke'] ?? 10);
$dark = (bool) ($props['dark'] ?? false);
$leaf = (bool) ($props['leaf'] ?? false);
$display = (string) ($props['display'] ?? '0');
$unit = (string) ($props['unit'] ?? '');
$label = (string) ($props['label'] ?? '');
$aria = (string) ($props['aria'] ?? ($label !== '' ? $label : 'Progres'));

$r = ($size - $stroke) / 2;
$c = 2 * M_PI * $r;
$offset = $c * (1 - $value / 100);
$center = $size / 2;

$classes = cx(['gauge', $dark ? 'gauge--on-dark' : '', $leaf ? 'gauge--leaf' : '']);
?>
<div class="<?= e($classes) ?>" data-gauge
     style="--gauge-size: <?= e($size) ?>px; --gauge-stroke: <?= e($stroke) ?>"
     role="img" aria-label="<?= e($aria . ': ' . round($value, 1) . '%') ?>">
    <svg viewBox="0 0 <?= e($size) ?> <?= e($size) ?>" aria-hidden="true" focusable="false">
        <circle class="gauge__track" cx="<?= e($center) ?>" cy="<?= e($center) ?>" r="<?= e(round($r, 2)) ?>"></circle>
        <circle class="gauge__fill" cx="<?= e($center) ?>" cy="<?= e($center) ?>" r="<?= e(round($r, 2)) ?>"
                stroke-dasharray="<?= e(round($c, 2)) ?>"
                stroke-dashoffset="<?= e(round($c, 2)) ?>"
                data-dash-full="<?= e(round($c, 2)) ?>"
                data-dash-offset="<?= e(round($offset, 2)) ?>"></circle>
    </svg>
    <div class="gauge__center">
        <span class="gauge__value"><?= e($display) ?><?php if ($unit !== ''): ?><span class="t-small"> <?= e($unit) ?></span><?php endif; ?></span>
        <?php if ($label !== ''): ?><span class="gauge__label"><?= e($label) ?></span><?php endif; ?>
    </div>
</div>
