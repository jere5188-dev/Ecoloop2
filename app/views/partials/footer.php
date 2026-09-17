<?php
/**
 * Application footer — sits inside the content container.
 */
?>
<footer class="app-footer">
    <div class="app-footer__row">
        <div class="u-row">
            <span class="tile tile--sm tile--dark">
                <?php component('icon', ['name' => 'leaf', 'size' => 'md']); ?>
            </span>
            <div>
                <p class="t-small" style="color: var(--c-text-2); font-weight: var(--fw-semibold)">
                    <?= e(APP_NAME) ?> — <?= e(APP_TAGLINE) ?>
                </p>
                <p class="t-caption">Small actions. Big impact.</p>
            </div>
        </div>

        <ul class="app-footer__links">
            <li><a href="<?= e(url('/passbook')) ?>">Waste Passbook</a></li>
            <li><a href="<?= e(url('/impact')) ?>">Impact Metric</a></li>
            <li><a href="<?= e(url('/sorting')) ?>">Smart Sorting</a></li>
            <li><a href="<?= e(url('/pickup')) ?>">Waste Pickup</a></li>
            <li><a href="<?= e(url('/competition')) ?>">EcoPoints</a></li>
        </ul>
    </div>

    <p class="app-footer__note t-caption">
        Mendukung SDG 12 — Responsible Consumption &amp; Production. Mengubah kebiasaan linear
        (take–make–dispose) menjadi siklus ekonomi sirkular yang terukur di lingkungan kampus.
        <span class="t-nowrap">v<?= e(APP_VERSION) ?></span>
    </p>
</footer>
