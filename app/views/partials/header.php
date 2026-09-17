<?php
/**
 * Application header.
 *
 * Brand lockup + tagline (styleguide masthead), a tablet-only sidebar toggle,
 * desktop search, an EcoPoints pill and the account avatar.
 *
 * Shared: $authUser
 */
$user = shared('authUser', []);
?>
<header class="app-header">
    <button class="header-icon-btn sidebar-toggle" type="button"
            id="sidebar-toggle" aria-controls="app-sidebar" aria-expanded="false">
        <?php component('icon', ['name' => 'menu', 'size' => 'lg', 'label' => 'Buka menu navigasi']); ?>
    </button>

    <a class="app-header__brand" href="<?= e(url('/')) ?>">
        <span class="brand-mark">
            <?php component('icon', ['name' => 'leaf', 'size' => 'lg']); ?>
        </span>
        <span class="brand-text">
            <span class="brand-name"><?= e(APP_NAME) ?></span>
            <span class="brand-tagline"><?= e(APP_TAGLINE) ?></span>
        </span>
    </a>

    <form class="app-header__search" role="search" action="<?= e(url('/passbook')) ?>" method="get">
        <?php component('icon', ['name' => 'search', 'size' => 'md']); ?>
        <label class="sr-only" for="global-search">Cari material, transaksi, atau titik drop-off</label>
        <input id="global-search" name="q" type="search" placeholder="Cari material atau transaksi…" autocomplete="off">
    </form>

    <div class="app-header__actions">
        <span class="header-pill" title="Saldo EcoPoints">
            <?php component('icon', ['name' => 'coin', 'size' => 'sm']); ?>
            <?= e(fmt_pts((int) ($user['points'] ?? 0), false)) ?> pts
        </span>

        <a class="header-icon-btn" href="<?= e(url('/sorting')) ?>" title="Smart Sorting">
            <?php component('icon', ['name' => 'scan', 'size' => 'lg', 'label' => 'Buka Smart Sorting']); ?>
        </a>

        <a class="header-avatar" href="<?= e(url('/profile')) ?>" title="<?= e((string) ($user['name'] ?? 'Profil')) ?>">
            <span aria-hidden="true"><?= e((string) ($user['initials'] ?? 'EL')) ?></span>
            <span class="sr-only">Buka profil <?= e((string) ($user['name'] ?? '')) ?></span>
        </a>
    </div>
</header>
