<?php
/**
 * EcoLoop application layout.
 *
 * Composes the shell: header, sidebar (tablet/desktop), page content, bottom
 * navigation (mobile) and footer. The `.shell` CSS Grid decides which of those
 * regions is visible at each breakpoint (requirement R6.1).
 *
 * Available: $content, plus everything View::share()'d.
 */

$bodyClass = shared('bodyClass', '');
?>
<!DOCTYPE html>
<html lang="id" class="no-js">
<head>
<?php partial('head'); ?>
</head>
<body class="<?= e(trim('app ' . $bodyClass)) ?>" data-route="<?= e(current_path()) ?>">
<script>document.documentElement.classList.remove('no-js');</script>

<a class="skip-link" href="#main">Lompat ke konten utama</a>

<div class="shell" id="shell" data-sidebar="collapsed">

<?php partial('header'); ?>
<?php partial('sidebar'); ?>

    <main class="shell__main" id="main" tabindex="-1">
        <div class="container">
<?php partial('page-head'); ?>
            <?= $content ?>
<?php partial('footer'); ?>
        </div>
    </main>

<?php partial('bottom-nav'); ?>

    <div class="shell__scrim" id="sidebar-scrim" hidden></div>
</div>

<script src="<?= e(asset('assets/js/app.js')) ?>" defer></script>
<script src="<?= e(asset('assets/js/passbook.js')) ?>" defer></script>
<script src="<?= e(asset('assets/js/impact.js')) ?>" defer></script>
<script src="<?= e(asset('assets/js/scanner.js')) ?>" defer></script>
<script src="<?= e(asset('assets/js/pickup.js')) ?>" defer></script>
<script src="<?= e(asset('assets/js/gamify.js')) ?>" defer></script>
</body>
</html>
