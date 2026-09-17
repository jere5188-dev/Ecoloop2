<?php
/**
 * Sidebar navigation.
 *
 * Hidden on mobile (the bottom bar takes over), a collapsible icon rail on
 * tablet, and a persistent labelled sidebar on desktop
 * (requirements R6.3–R6.5).
 *
 * Shared: $navItems, $authUser
 */
$items = (array) shared('navItems', []);
$user = (array) shared('authUser', []);
$main = array_filter($items, static fn ($i) => $i['group'] === 'main');
$account = array_filter($items, static fn ($i) => $i['group'] === 'account');
?>
<aside class="app-sidebar" id="app-sidebar" aria-label="Navigasi utama">
    <nav class="u-stack-3">
        <p class="app-sidebar__label sidebar-hide-rail">Menu</p>
        <ul class="app-nav">
            <?php foreach ($main as $item):
                $active = is_active($item['path']); ?>
                <li>
                    <a class="<?= e(cx(['app-nav__item', $active ? 'is-active' : ''])) ?>"
                       href="<?= e(url($item['path'])) ?>"
                       <?= $active ? 'aria-current="page"' : '' ?>
                       title="<?= e($item['label']) ?>">
                        <?php component('icon', ['name' => $item['icon'], 'size' => 'lg']); ?>
                        <span class="app-nav__text sidebar-hide-rail"><?= e($item['label']) ?></span>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </nav>

    <nav class="u-stack-3">
        <p class="app-sidebar__label sidebar-hide-rail">Akun</p>
        <ul class="app-nav">
            <?php foreach ($account as $item):
                $active = is_active($item['path']); ?>
                <li>
                    <a class="<?= e(cx(['app-nav__item', $active ? 'is-active' : ''])) ?>"
                       href="<?= e(url($item['path'])) ?>"
                       <?= $active ? 'aria-current="page"' : '' ?>
                       title="<?= e($item['label']) ?>">
                        <?php component('icon', ['name' => $item['icon'], 'size' => 'lg']); ?>
                        <span class="app-nav__text sidebar-hide-rail"><?= e($item['label']) ?></span>
                    </a>
                </li>
            <?php endforeach; ?>
            <li>
                <a class="app-nav__item" href="<?= e(url('/pickup/reset')) ?>" title="Reset form pickup">
                    <?php component('icon', ['name' => 'settings', 'size' => 'lg']); ?>
                    <span class="app-nav__text sidebar-hide-rail">Reset Draft Pickup</span>
                </a>
            </li>
        </ul>
    </nav>

    <div class="sidebar-card sidebar-hide-rail">
        <p class="sidebar-card__title">Dampak bulan ini</p>
        <p class="sidebar-card__value"><?= e(number_format((float) ($user['co2_kg'] ?? 0), 1)) ?> kg</p>
        <p class="t-caption" style="color: var(--c-on-dark-3)">CO₂ berhasil direduksi</p>
        <a class="btn btn--leaf btn--sm btn--block" style="margin-top: var(--sp-3)" href="<?= e(url('/impact')) ?>">
            Lihat detail<?php component('icon', ['name' => 'arrow-right', 'size' => 'sm']); ?>
        </a>
    </div>

    <div class="sidebar-rail-only">
        <a class="icon-btn icon-btn--filled" href="<?= e(url('/sorting')) ?>" title="Smart Scan">
            <?php component('icon', ['name' => 'scan', 'size' => 'lg', 'label' => 'Smart Scan']); ?>
        </a>
    </div>
</aside>
