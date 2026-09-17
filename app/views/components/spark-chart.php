<?php
/**
 * Component: spark-chart
 *
 * Inline SVG line + area chart with dot markers and an x-axis label row.
 * Hand-drawn so the platform ships no charting library (requirements R2.3, N3.3).
 *
 * Props:
 *   values  array<float>   series values                (required)
 *   labels  array<string>  x-axis labels
 *   unit    string         unit appended in the tooltip title
 *   id      string         unique id for the gradient defs
 *   height  int            svg viewBox height           (default 160)
 *   aria    string         accessible description
 */

$values = array_values(array_map('floatval', (array) ($props['values'] ?? [])));
$labels = array_values((array) ($props['labels'] ?? []));
$unit = (string) ($props['unit'] ?? 'kg');
$id = preg_replace('/[^a-z0-9\-]/i', '', (string) ($props['id'] ?? 'spark')) ?: 'spark';
$h = (int) ($props['height'] ?? 160);
$aria = (string) ($props['aria'] ?? 'Grafik tren');

$count = count($values);
if ($count === 0) {
    return;
}

$w = 600;
$padX = 14;
$padTop = 18;
$padBottom = 16;
$max = max($values);
$min = min($values);
$span = max($max - $min, 0.0001);
// Add headroom so the peak never touches the top edge.
$top = $max + $span * 0.18;
$bottom = max(0.0, $min - $span * 0.12);
$range = max($top - $bottom, 0.0001);

$plotW = $w - $padX * 2;
$plotH = $h - $padTop - $padBottom;

$points = [];
foreach ($values as $i => $v) {
    $x = $count === 1 ? $w / 2 : $padX + ($plotW * $i / ($count - 1));
    $y = $padTop + $plotH * (1 - (($v - $bottom) / $range));
    $points[] = [round($x, 2), round($y, 2), $v];
}

$line = implode(' ', array_map(static fn ($p) => $p[0] . ',' . $p[1], $points));
$area = $line . ' ' . $points[$count - 1][0] . ',' . ($h - $padBottom) . ' ' . $points[0][0] . ',' . ($h - $padBottom);
?>
<figure class="spark" role="group" aria-label="<?= e($aria) ?>">
    <svg class="spark__svg" viewBox="0 0 <?= e($w) ?> <?= e($h) ?>" preserveAspectRatio="none"
         role="img" aria-label="<?= e($aria) ?>">
        <defs>
            <linearGradient id="<?= e($id) ?>-area" x1="0" y1="0" x2="0" y2="1">
                <stop offset="0%" stop-color="var(--c-forest)" stop-opacity="0.26"></stop>
                <stop offset="100%" stop-color="var(--c-forest)" stop-opacity="0"></stop>
            </linearGradient>
        </defs>

        <?php for ($g = 0; $g <= 3; $g++):
            $gy = round($padTop + $plotH * $g / 3, 2); ?>
            <line class="spark__grid" x1="<?= e($padX) ?>" y1="<?= e($gy) ?>" x2="<?= e($w - $padX) ?>" y2="<?= e($gy) ?>"></line>
        <?php endfor; ?>

        <polygon class="spark__area" points="<?= e($area) ?>" fill="url(#<?= e($id) ?>-area)"></polygon>
        <polyline class="spark__line" points="<?= e($line) ?>"></polyline>

        <?php foreach ($points as $i => $p): ?>
            <circle class="<?= e($i === $count - 1 ? 'spark__dot spark__dot--last' : 'spark__dot') ?>"
                    cx="<?= e($p[0]) ?>" cy="<?= e($p[1]) ?>" r="<?= e($i === $count - 1 ? 6 : 4.5) ?>">
                <title><?= e(($labels[$i] ?? ('#' . ($i + 1))) . ': ' . number_format($p[2], 1) . ' ' . $unit) ?></title>
            </circle>
        <?php endforeach; ?>
    </svg>

    <?php if ($labels): ?>
        <figcaption class="spark__labels">
            <?php foreach ($labels as $i => $l): ?>
                <span class="<?= e($i === $count - 1 ? 'is-last' : '') ?>"><?= e($l) ?></span>
            <?php endforeach; ?>
        </figcaption>
    <?php endif; ?>
</figure>
