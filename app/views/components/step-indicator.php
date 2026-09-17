<?php
/**
 * Component: step-indicator
 *
 * Numbered 4-step progress indicator for the pickup wizard (requirement R4.1).
 *
 * Props:
 *   steps   array<int, array{label:string,en:string}>  1-indexed step map
 *   current int                                        active step number
 */

$steps = (array) ($props['steps'] ?? []);
$current = (int) ($props['current'] ?? 1);
$total = count($steps);
?>
<ol class="steps" aria-label="Progres formulir pickup">
    <?php foreach ($steps as $no => $step):
        $no = (int) $no;
        $done = $no < $current;
        $isCurrent = $no === $current;
        ?>
        <li class="<?= e(cx(['steps__item', $done ? 'is-done' : '', $isCurrent ? 'is-current' : ''])) ?>"
            <?= $isCurrent ? 'aria-current="step"' : '' ?>>
            <span class="steps__dot">
                <?php if ($done): ?>
                    <?php component('icon', ['name' => 'check', 'size' => 'sm']); ?>
                <?php else: ?>
                    <?= e($no) ?>
                <?php endif; ?>
            </span>
            <span class="steps__label"><?= e((string) ($step['label'] ?? $no)) ?></span>
            <span class="sr-only">
                Langkah <?= e($no) ?> dari <?= e($total) ?><?= $done ? ' — selesai' : ($isCurrent ? ' — sedang diisi' : '') ?>
            </span>
        </li>
    <?php endforeach; ?>
</ol>
