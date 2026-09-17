<?php
/**
 * Page heading block: eyebrow, H1, subtitle and optional actions.
 *
 * Shared: $eyebrow, $heading, $subtitle
 */
$eyebrow = (string) shared('eyebrow', '');
$heading = (string) shared('heading', '');
$subtitle = (string) shared('subtitle', '');

if ($heading === '') {
    return;
}
?>
<div class="page-head">
    <div class="page-head__text">
        <?php if ($eyebrow !== ''): ?><p class="t-eyebrow"><?= e($eyebrow) ?></p><?php endif; ?>
        <h1><?= e($heading) ?></h1>
        <?php if ($subtitle !== ''): ?>
            <p class="page-head__subtitle t-body-lg"><?= e($subtitle) ?></p>
        <?php endif; ?>
    </div>
    <div class="page-head__actions">
        <a class="btn btn--secondary btn--sm" href="<?= e(url('/sorting')) ?>">
            <?php component('icon', ['name' => 'scan', 'size' => 'sm']); ?>Smart Scan
        </a>
        <a class="btn btn--primary btn--sm" href="<?= e(url('/pickup')) ?>">
            Request Pickup<?php component('icon', ['name' => 'arrow-right', 'size' => 'sm']); ?>
        </a>
    </div>
</div>
