<?php
/**
 * Component: progress
 *
 * Accessible progress bar with an optional label row and footer
 * (requirements R5.5, R5.10, N2.5).
 *
 * Props:
 *   value    float   percentage 0–100                   (required)
 *   label    string  left-hand label
 *   display  string  right-hand value text, e.g. '320 / 500 kg'
 *   size     string  '' | 'sm' | 'lg'
 *   tone     string  '' | 'success' | 'warning' | 'muted'
 *   dark     bool    on-dark variant
 *   footLeft  string caption bottom-left
 *   footRight string caption bottom-right
 *   aria     string  accessible name for the bar
 *   valueHook string value for data-value-hook on the display span, so JS can
 *                    update the figure without re-rendering the component
 */

$value = clamp_pct((float) ($props['value'] ?? 0));
$label = (string) ($props['label'] ?? '');
$display = (string) ($props['display'] ?? '');
$size = (string) ($props['size'] ?? '');
$tone = (string) ($props['tone'] ?? '');
$dark = (bool) ($props['dark'] ?? false);
$footLeft = (string) ($props['footLeft'] ?? '');
$footRight = (string) ($props['footRight'] ?? '');
$aria = (string) ($props['aria'] ?? ($label !== '' ? $label : 'Progres'));

$classes = cx([
    'progress',
    $size !== '' ? 'progress--' . $size : '',
    $tone !== '' ? 'progress--' . $tone : '',
    $dark ? 'progress--on-dark' : '',
]);
?>
<div class="<?= e($classes) ?>" data-progress style="--progress-value: <?= e(number_format($value, 2, '.', '')) ?>%">
    <?php if ($label !== '' || $display !== ''): ?>
        <div class="progress__head">
            <?php if ($label !== ''): ?><span class="progress__label"><?= e($label) ?></span><?php endif; ?>
            <?php if ($display !== ''): ?>
                <span class="progress__value"<?= ($props['valueHook'] ?? '') !== '' ? ' data-value-hook="' . e((string) $props['valueHook']) . '"' : '' ?>><?= e($display) ?></span>
            <?php endif; ?>
        </div>
    <?php endif; ?>
    <div class="progress__track" role="progressbar"
         aria-label="<?= e($aria) ?>"
         aria-valuenow="<?= e(round($value, 1)) ?>" aria-valuemin="0" aria-valuemax="100">
        <span class="progress__fill"></span>
    </div>
    <?php if ($footLeft !== '' || $footRight !== ''): ?>
        <div class="progress__foot">
            <span><?= e($footLeft) ?></span>
            <span><?= e($footRight) ?></span>
        </div>
    <?php endif; ?>
</div>
