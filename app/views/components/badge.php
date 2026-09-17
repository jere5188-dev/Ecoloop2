<?php
/**
 * Component: badge
 *
 * Gamification achievement badge (styleguide 07). Locked badges are
 * de-emphasised and expose progress toward unlock (requirements R5.3, R5.4).
 *
 * Props:
 *   title    string  badge name                        (required)
 *   req      string  unlock requirement caption        (required)
 *   icon     string  icon key
 *   unlocked bool    unlocked state                    (default false)
 *   progress float   percentage toward unlock (0–100)
 *   earned   string  extra caption (earn date or progress text)
 */

$unlocked = (bool) ($props['unlocked'] ?? false);
$progress = clamp_pct((float) ($props['progress'] ?? 0));
$title = (string) ($props['title'] ?? '');
$earned = (string) ($props['earned'] ?? '');
?>
<article class="<?= e(cx(['badge-card', $unlocked ? 'is-unlocked' : 'is-locked'])) ?>">
    <?php if (!$unlocked): ?>
        <span class="badge-card__lock" title="Belum terbuka">
            <?php component('icon', ['name' => 'lock', 'label' => 'Belum terbuka']); ?>
        </span>
    <?php endif; ?>

    <span class="badge-card__medal">
        <?php component('icon', ['name' => (string) ($props['icon'] ?? 'medal')]); ?>
    </span>

    <h3 class="badge-card__title"><?= e($title) ?></h3>
    <p class="badge-card__req"><?= e((string) ($props['req'] ?? '')) ?></p>

    <?php if ($unlocked): ?>
        <span class="pill pill--success">
            <?php component('icon', ['name' => 'check']); ?>Terbuka
        </span>
        <?php if ($earned !== ''): ?><p class="t-caption t-muted"><?= e($earned) ?></p><?php endif; ?>
    <?php else: ?>
        <div class="badge-card__progress">
            <?php component('progress', [
                'value'   => $progress,
                'size'    => 'sm',
                'aria'    => 'Progres badge ' . $title,
            ]); ?>
        </div>
        <?php if ($earned !== ''): ?><p class="t-caption t-muted"><?= e($earned) ?></p><?php endif; ?>
    <?php endif; ?>
</article>
