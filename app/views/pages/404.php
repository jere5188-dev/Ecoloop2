<?php
/**
 * Page: 404 / 405 (requirement R7.7).
 *
 * Vars: $path, $status
 */
$is405 = ($status ?? 404) === 405;
?>
<section class="section">
    <article class="card">
        <?php component('empty-state', [
            'icon'       => $is405 ? 'alert' : 'map',
            'title'      => $is405 ? 'Metode permintaan tidak didukung' : 'Halaman tidak ditemukan',
            'desc'       => $is405
                ? 'Rute ini ada, tapi tidak menerima metode HTTP yang kamu gunakan.'
                : 'Rute "' . $path . '" tidak terdaftar di EcoLoop. Mungkin tautannya sudah berubah.',
            'actionText' => 'Kembali ke dashboard',
            'actionHref' => '/',
        ]); ?>

        <div class="divider">Atau lanjut ke</div>

        <div class="grid grid--4">
            <?php foreach ([
                ['Waste Passbook', '/passbook', 'passbook'],
                ['Impact Metric', '/impact', 'leaf'],
                ['Smart Sorting', '/sorting', 'camera'],
                ['Waste Pickup', '/pickup', 'truck'],
            ] as [$label, $href, $icon]): ?>
                <a class="quick-action" href="<?= e(url($href)) ?>">
                    <span class="tile tile--forest">
                        <?php component('icon', ['name' => $icon, 'size' => 'lg']); ?>
                    </span>
                    <span class="u-grow">
                        <span class="quick-action__label"><?= e($label) ?></span>
                    </span>
                    <?php component('icon', ['name' => 'chevron-right', 'size' => 'sm', 'class' => 'quick-action__chev']); ?>
                </a>
            <?php endforeach; ?>
        </div>
    </article>
</section>
