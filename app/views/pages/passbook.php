<?php
/**
 * Page: Waste Passbook — the digital ledger (requirements R1.1–R1.8).
 *
 * Vars: $user, $filters, $ledger, $totals, $transactions
 */
?>

<!-- ── Summary strip (R1.1) ─────────────────────────────────────────────── -->
<section class="section" aria-labelledby="pb-summary">
    <h2 class="sr-only" id="pb-summary">Ringkasan buku tabungan</h2>
    <div class="grid grid--stats">
        <?php
        component('stat-card', [
            'icon'  => 'weight',
            'tone'  => 'forest',
            'label' => 'Total sampah terdaur ulang',
            'value' => fmt_kg($totals['kg'], false),
            'unit'  => 'KG',
            'delta' => fmt_delta($totals['delta_kg']),
            'note'  => 'minggu ini',
        ]);
        component('stat-card', [
            'icon'  => 'coin',
            'tone'  => 'leaf',
            'label' => 'Total EcoPoints terkumpul',
            'value' => '+' . fmt_pts($totals['points'], false),
            'unit'  => 'pts',
            'delta' => '+' . number_format($user['points_month']) . ' pts',
            'note'  => 'bulan ini',
        ]);
        component('stat-card', [
            'icon'  => 'recycle',
            'tone'  => 'info',
            'label' => 'Kategori material aktif',
            'value' => (string) $totals['categories'],
            'unit'  => 'kategori',
            'note'  => 'PET · Kertas · Logam',
        ]);
        component('stat-card', [
            'icon'  => 'clipboard',
            'tone'  => 'sage',
            'label' => 'Transaksi tercatat',
            'value' => (string) $totals['deposits'],
            'unit'  => 'setoran',
            'delta' => '+5',
            'note'  => 'bulan ini',
        ]);
        ?>
    </div>
</section>

<!-- ── Ledger table (R1.2–R1.7) ─────────────────────────────────────────── -->
<section class="section">
    <article class="card" data-passbook>
        <header class="card__head card__head--stacked">
            <div class="u-grow">
                <h2 class="card__title">Digital Waste Passbook</h2>
                <p class="card__meta">Setiap kilogram tercatat, terverifikasi, dan transparan.</p>
            </div>
            <span class="pill pill--success card__action">
                <?php component('icon', ['name' => 'check-circle']); ?>Terverifikasi bank sampah
            </span>
        </header>

        <!-- Filter chips (R1.6) -->
        <div class="passbook__filters" role="tablist" aria-label="Filter kategori sampah" data-passbook-filters>
            <?php foreach ($filters as $i => $filter): ?>
                <button class="<?= e(cx(['chip', $i === 0 ? 'is-active' : ''])) ?>"
                        type="button" role="tab"
                        aria-selected="<?= $i === 0 ? 'true' : 'false' ?>"
                        data-filter="<?= e($filter['key']) ?>">
                    <?php if ($i === 0): ?><?php component('icon', ['name' => 'filter']); ?><?php endif; ?>
                    <?= e($filter['label']) ?>
                </button>
            <?php endforeach; ?>
        </div>

        <?php component('table-passbook', [
            'rows'   => $ledger,
            'totals' => $totals,
        ]); ?>

        <p class="passbook__empty" data-passbook-empty hidden>
            <?php component('icon', ['name' => 'info', 'size' => 'sm']); ?>
            Belum ada setoran untuk kategori ini. Mulai dengan Smart Scan atau drop-off box terdekat.
        </p>

        <footer class="card__foot">
            <div class="callout u-grow">
                <?php component('icon', ['name' => 'info', 'size' => 'md']); ?>
                <span>
                    Proporsi dihitung dari total <?= e(fmt_kg($totals['kg'])) ?> yang sudah terverifikasi.
                    Material berstatus <strong>Menunggu</strong> belum menghasilkan EcoPoints sampai penimbangan digital selesai.
                </span>
            </div>
        </footer>
    </article>
</section>

<!-- ── Category detail cards ───────────────────────────────────────────── -->
<section class="section">
    <?php component('section-head', [
        'title' => 'Rincian per Kategori',
        'sub'   => 'Nilai tukar poin dan kontribusi emisi masing-masing material.',
    ]); ?>
    <div class="grid grid--3">
        <?php foreach ($ledger as $row):
            if ($row['kg'] <= 0) {
                continue;
            } ?>
            <article class="card card--tight">
                <div class="u-row">
                    <span class="tile tile--<?= e($row['tone']) ?>">
                        <?php component('icon', ['name' => $row['icon'], 'size' => 'lg']); ?>
                    </span>
                    <div class="u-grow">
                        <h3 class="t-h5"><?= e($row['label']) ?></h3>
                        <p class="t-caption t-muted"><?= e($row['rate']) ?> pts / kg</p>
                    </div>
                </div>

                <p class="t-data-lg"><?= e(fmt_kg((float) $row['kg'])) ?></p>

                <?php component('progress', [
                    'value'     => (float) $row['proportion'],
                    'display'   => fmt_pct((float) $row['proportion']),
                    'label'     => 'Proporsi',
                    'footLeft'  => '+' . number_format((int) $row['points']) . ' pts diperoleh',
                    'footRight' => number_format((float) $row['co2'], 1) . ' kg CO₂',
                    'aria'      => 'Proporsi ' . $row['label'],
                ]); ?>
            </article>
        <?php endforeach; ?>
    </div>
</section>

<!-- ── Transaction history (R1.8) ──────────────────────────────────────── -->
<section class="section">
    <article class="card">
        <header class="card__head">
            <div class="u-grow">
                <h2 class="card__title">Riwayat Transaksi</h2>
                <p class="card__meta"><?= e(count($transactions)) ?> setoran terakhir</p>
            </div>
            <span class="pill pill--muted card__action">September 2026</span>
        </header>

        <ul class="activity">
            <?php foreach ($transactions as $tx): ?>
                <li class="activity__row">
                    <span class="tile tile--sm tile--<?= e($tx['tone']) ?>">
                        <?php component('icon', ['name' => $tx['icon'], 'size' => 'md']); ?>
                    </span>
                    <span class="u-grow">
                        <span class="activity__title"><?= e($tx['title']) ?></span>
                        <span class="activity__meta">
                            <?= e($tx['date']) ?> · <?= e($tx['time']) ?> · <?= e($tx['category']) ?>
                        </span>
                        <span class="activity__tags">
                            <span class="pill pill--muted"><?= e($tx['method']) ?></span>
                            <span class="pill pill--<?= e(status_tone($tx['status_key'])) ?>"><?= e($tx['status']) ?></span>
                        </span>
                    </span>
                    <span class="activity__figures">
                        <span class="t-data-sm"><?= e(fmt_kg((float) $tx['kg'])) ?></span>
                        <span class="t-caption <?= $tx['points'] > 0 ? 't-forest' : 't-muted' ?>">
                            <?= $tx['points'] > 0 ? '+' . e($tx['points']) . ' pts' : 'menunggu' ?>
                        </span>
                    </span>
                </li>
            <?php endforeach; ?>
        </ul>

        <footer class="card__foot">
            <a class="btn btn--secondary btn--sm" href="<?= e(url('/pickup')) ?>">
                <?php component('icon', ['name' => 'truck', 'size' => 'sm']); ?>Jadwalkan pickup berikutnya
            </a>
            <a class="link-action" style="margin-left:auto" href="<?= e(url('/impact')) ?>">
                Lihat dampak karbon<?php component('icon', ['name' => 'chevron-right', 'size' => 'sm']); ?>
            </a>
        </footer>
    </article>
</section>
