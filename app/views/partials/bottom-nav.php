<?php
/**
 * Mobile bottom navigation bar (requirement R6.6).
 *
 * Five primary destinations, each a ≥44px touch target inside a 64px bar that
 * respects the iOS/Android safe-area inset.
 *
 * Shared: $navBottom
 */
$items = (array) shared('navBottom', []);
?>
<nav class="bottom-nav" aria-label="Navigasi utama (mobile)">
    <?php foreach ($items as $item):
        $active = is_active($item['path']); ?>
        <a class="<?= e(cx(['bottom-nav__item', $active ? 'is-active' : ''])) ?>"
           href="<?= e(url($item['path'])) ?>"
           <?= $active ? 'aria-current="page"' : '' ?>>
            <span class="bottom-nav__blob">
                <?php component('icon', ['name' => $item['icon'], 'size' => 'lg']); ?>
            </span>
            <span><?= e($item['short']) ?></span>
        </a>
    <?php endforeach; ?>
</nav>
