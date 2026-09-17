<?php
/**
 * Component: streak
 *
 * Seven-slot daily streak row (requirement R5.2, styleguide 07).
 *
 * Props:
 *   days  array<array{label:string,day:string,done:bool,today:bool,points:int}>
 *   count int    streak length in days
 *   dark  bool   on-dark variant
 *   note  string caption under the row
 */

$days = (array) ($props['days'] ?? []);
$count = (int) ($props['count'] ?? count(array_filter($days, static fn ($d) => !empty($d['done']))));
$dark = (bool) ($props['dark'] ?? false);
$note = (string) ($props['note'] ?? '');
?>
<div class="<?= e(cx(['streak', $dark ? 'streak--on-dark' : ''])) ?>">
    <div class="streak__head">
        <span class="streak__flame">
            <?php component('icon', ['name' => 'flame', 'size' => 'sm']); ?>
        </span>
        <span class="streak__count"><?= e($count) ?> hari</span>
        <span class="t-small <?= $dark ? '' : 't-muted' ?>">Daily Streak</span>
    </div>

    <ol class="streak__days">
        <?php foreach ($days as $day):
            $done = !empty($day['done']);
            $today = !empty($day['today']);
            $state = $done ? 'sudah setor' : 'belum setor';
            ?>
            <li class="<?= e(cx(['streak__day', $done ? 'is-done' : '', $today ? 'is-today' : ''])) ?>">
                <span class="streak__dot" title="<?= e(($day['day'] ?? '') . ' — ' . $state . ($done ? ' (+' . (int) ($day['points'] ?? 0) . ' pts)' : '')) ?>">
                    <?php if ($done): ?>
                        <?php component('icon', ['name' => 'check']); ?>
                    <?php else: ?>
                        <?= e((string) ($day['label'] ?? '')) ?>
                    <?php endif; ?>
                </span>
                <span class="streak__label"><?= e((string) ($day['day'] ?? '')) ?></span>
                <span class="sr-only"><?= e($state) ?></span>
            </li>
        <?php endforeach; ?>
    </ol>

    <?php if ($note !== ''): ?>
        <p class="t-caption <?= $dark ? '' : 't-muted' ?>"><?= e($note) ?></p>
    <?php endif; ?>
</div>
