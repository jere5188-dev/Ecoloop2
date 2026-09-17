<?php
/**
 * Component: stat-card
 *
 * Icon tile + label + data value + optional delta (styleguide 04).
 *
 * Props:
 *   icon   string  icon key                             (default 'leaf')
 *   tone   string  tile tone: forest|info|warning|sage|leaf|success|error|dark
 *   label  string  metric label                         (required)
 *   value  string  formatted value                      (required)
 *   unit   string  small unit suffix rendered after the value
 *   delta  string  signed change label, e.g. '+2.3 kg'
 *   trend  string  'up' | 'down' | 'flat'               (default 'up')
 *   note   string  caption shown next to the delta
 *   dark   bool    dark surface variant
 *   count  bool    animate the value from zero (default true)
 *   hook   string  value for data-stat-hook, letting JS update this figure
 */

$tone = (string) ($props['tone'] ?? 'forest');
$dark = (bool) ($props['dark'] ?? false);
$trend = (string) ($props['trend'] ?? 'up');
$delta = (string) ($props['delta'] ?? '');
$note = (string) ($props['note'] ?? '');
$unit = (string) ($props['unit'] ?? '');
$value = (string) ($props['value'] ?? '0');
$count = (bool) ($props['count'] ?? true);
$hook = (string) ($props['hook'] ?? '');

$trendIcon = match ($trend) {
    'down' => 'arrow-right',
    'flat' => 'minus',
    default => 'arrow-up',
};
?>
<article class="<?= e(cx(['stat', $dark ? 'stat--dark' : ''])) ?>">
    <div class="stat__top">
        <span class="<?= e(cx(['tile', 'tile--sm', $dark ? 'tile--on-dark' : 'tile--' . $tone])) ?>">
            <?php component('icon', ['name' => (string) ($props['icon'] ?? 'leaf'), 'size' => 'md']); ?>
        </span>
        <span class="stat__label"><?= e((string) ($props['label'] ?? '')) ?></span>
    </div>
    <p class="stat__value"<?= $count ? ' data-count-to="' . e(preg_replace('/[^0-9.]/', '', $value) ?: '0') . '"' : '' ?><?= $hook !== '' ? ' data-stat-hook="' . e($hook) . '"' : '' ?>><?= e($value) ?><?php if ($unit !== ''): ?><span class="stat__unit"><?= e($unit) ?></span><?php endif; ?></p>
    <?php if ($delta !== '' || $note !== ''): ?>
        <div class="stat__foot">
            <?php if ($delta !== ''): ?>
                <span class="<?= e(cx(['stat__delta', $trend !== 'up' ? 'stat__delta--' . $trend : ''])) ?>">
                    <?php component('icon', ['name' => $trendIcon, 'size' => 'sm']); ?>
                    <span<?= $hook !== '' ? ' data-delta-hook="' . e($hook) . '"' : '' ?>><?= e($delta) ?></span>
                </span>
            <?php endif; ?>
            <?php if ($note !== ''): ?>
                <span<?= $hook !== '' ? ' data-note-hook="' . e($hook) . '"' : '' ?>><?= e($note) ?></span>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</article>
