<?php
/**
 * Page: Impact Metric — personal carbon-reduction dashboard
 * (requirements R2.1–R2.6).
 *
 * Vars: $user, $ranges, $activeRange, $impact, $series, $breakdown,
 *       $equivalences, $milestones
 */
$pct = $impact['goal'] > 0 ? $impact['co2'] / $impact['goal'] * 100 : 0;
?>

<!-- ── Hero: ring gauge + equivalences (R2.1, R2.2) ────────────────────── -->
<section class="section">
    <div class="impact-hero card--dark" data-impact-root
         data-ranges='<?= e(json_encode($ranges, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR)) ?>'
         data-series='<?= e(json_encode($series, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR)) ?>'>

        <div class="impact-hero__head">
            <div class="u-grow">
                <p class="t-eyebrow" style="color: var(--c-leaf)">Impact Metric</p>
                <h2 class="t-h2" style="color: var(--c-on-dark)">Dampak karbon kamu</h2>
                <p class="t-body" style="color: var(--c-on-dark-2); max-width: 46ch">
                    Setiap kilogram yang dialihkan dari TPA dikonversi menjadi angka reduksi emisi
                    berdasarkan faktor emisi material daur ulang.
                </p>
            </div>

            <!-- Range tabs (R2.5) -->
            <div class="segmented segmented--on-dark" role="tablist" aria-label="Rentang waktu" data-impact-tabs>
                <?php foreach ($ranges as $key => $range): ?>
                    <button class="segmented__btn" type="button" role="tab"
                            data-range="<?= e($key) ?>"
                            aria-selected="<?= $key === $activeRange ? 'true' : 'false' ?>">
                        <?= e($range['label']) ?>
                    </button>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="impact-hero__body">
            <div class="impact-hero__gauge">
                <?php component('ring-gauge', [
                    'value'   => $pct,
                    'display' => number_format($impact['co2'], 1),
                    'unit'    => 'kg',
                    'label'   => 'CO₂ Reduced',
                    'size'    => 176,
                    'stroke'  => 12,
                    'dark'    => true,
                    'leaf'    => true,
                    'aria'    => 'Reduksi CO₂ terhadap target',
                ]); ?>
                <p class="t-caption t-center" style="color: var(--c-on-dark-3)">
                    <span data-impact-goal-pct><?= e(fmt_pct($pct, 0)) ?></span> dari target
                    <span data-impact-goal><?= e(number_format($impact['goal'], 0)) ?></span> kg
                </p>
            </div>

            <ul class="impact-hero__equiv">
                <?php foreach ($equivalences as $i => $eq): ?>
                    <li class="equiv">
                        <span class="tile tile--on-dark">
                            <?php component('icon', ['name' => $eq['icon'], 'size' => 'lg']); ?>
                        </span>
                        <span class="u-grow">
                            <span class="equiv__value" data-impact-equiv="<?= e($i) ?>"><?= e($eq['value']) ?></span>
                            <span class="equiv__label"><?= e($eq['label']) ?></span>
                        </span>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</section>

<!-- ── Supporting metrics (R2.2) ───────────────────────────────────────── -->
<section class="section" aria-labelledby="im-stats">
    <h2 class="sr-only" id="im-stats">Metrik pendukung</h2>
    <div class="grid grid--stats">
        <?php
        component('stat-card', [
            'icon'  => 'leaf',
            'tone'  => 'forest',
            'label' => 'CO₂ Reduced',
            'value' => number_format($impact['co2'], 1),
            'unit'  => 'kg CO₂e',
            'delta' => $impact['delta_co2'],
            'note'  => $impact['delta_note'],
            'hook'  => 'co2',
        ]);
        component('stat-card', [
            'icon'  => 'recycle',
            'tone'  => 'info',
            'label' => 'Waste Diverted',
            'value' => number_format($impact['diverted'], 1),
            'unit'  => 'kg',
            'delta' => $impact['delta_diverted'],
            'note'  => 'dialihkan dari TPA',
            'hook'  => 'diverted',
        ]);
        component('stat-card', [
            'icon'  => 'drop',
            'tone'  => 'sage',
            'label' => 'Air bersih dihemat',
            'value' => number_format($impact['water'], 0),
            'unit'  => 'liter',
            'note'  => 'proses produksi primer',
            'hook'  => 'water',
        ]);
        component('stat-card', [
            'icon'  => 'bolt',
            'tone'  => 'warning',
            'label' => 'Energi tidak terpakai',
            'value' => number_format($impact['energy'], 1),
            'unit'  => 'kWh',
            'note'  => 'setara 3 hari listrik kos',
            'hook'  => 'energy',
        ]);
        ?>
    </div>
</section>

<!-- ── Trend chart (R2.3) + breakdown (R2.4) ───────────────────────────── -->
<section class="section">
    <div class="grid grid--split">
        <article class="card">
            <header class="card__head">
                <div class="u-grow">
                    <h2 class="card__title">Tren Reduksi CO₂</h2>
                    <p class="card__meta" data-impact-range-label><?= e($ranges[$activeRange]['label']) ?></p>
                </div>
                <span class="pill pill--forest card__action pill--data">
                    <?php component('icon', ['name' => 'chart']); ?>
                    <span data-impact-co2><?= e(number_format($impact['co2'], 1)) ?></span> kg
                </span>
            </header>

            <div data-impact-chart>
                <?php component('spark-chart', [
                    'values' => $series[$activeRange]['values'],
                    'labels' => $series[$activeRange]['labels'],
                    'unit'   => 'kg CO₂',
                    'id'     => 'impact-trend',
                    'height' => 170,
                    'aria'   => 'Grafik tren reduksi CO₂ per periode',
                ]); ?>
            </div>

            <footer class="card__foot">
                <div class="callout u-grow">
                    <?php component('icon', ['name' => 'sparkle', 'size' => 'md']); ?>
                    <span>
                        Puncak reduksi terjadi saat kamu menyetor kardus logistik dalam satu pickup terjadwal.
                        Setoran terkonsolidasi menghasilkan efisiensi transportasi yang lebih tinggi.
                    </span>
                </div>
            </footer>
        </article>

        <article class="card">
            <header class="card__head">
                <div class="u-grow">
                    <h2 class="card__title">Kontribusi per Material</h2>
                    <p class="card__meta">Faktor emisi berdasarkan jenis polimer</p>
                </div>
            </header>

            <ul class="u-stack">
                <?php foreach ($breakdown as $item): ?>
                    <li class="breakdown" data-share="<?= e($item['share']) ?>">
                        <span class="tile tile--sm tile--<?= e($item['tone']) ?>">
                            <?php component('icon', ['name' => $item['icon'], 'size' => 'md']); ?>
                        </span>
                        <span class="u-grow">
                            <?php component('progress', [
                                'label'     => $item['label'],
                                'display'   => number_format($item['co2'], 1) . ' kg',
                                'value'     => $item['share'],
                                'footLeft'  => $item['note'],
                                'footRight' => fmt_pct($item['share']) . ' dari total',
                                'aria'      => 'Kontribusi ' . $item['label'],
                                'valueHook' => 'breakdown',
                            ]); ?>
                        </span>
                    </li>
                <?php endforeach; ?>
            </ul>

            <footer class="card__foot">
                <span class="t-small t-muted">Total reduksi</span>
                <strong class="t-data-md" style="margin-left:auto">
                    <span data-impact-total><?= e(number_format($impact['co2'], 1)) ?></span> kg CO₂e
                </strong>
            </footer>
        </article>
    </div>
</section>

<!-- ── Milestones ──────────────────────────────────────────────────────── -->
<section class="section">
    <div class="grid grid--2">
        <article class="card">
            <header class="card__head">
                <div class="u-grow">
                    <h2 class="card__title">Milestone Dampak</h2>
                    <p class="card__meta">Tangga pencapaian reduksi emisi pribadi</p>
                </div>
            </header>

            <ul class="u-stack-3">
                <?php foreach ($milestones as $ms): ?>
                    <li class="milestone <?= $ms['done'] ? 'is-done' : '' ?>">
                        <span class="milestone__mark">
                            <?php component('icon', ['name' => $ms['done'] ? 'check' : 'target', 'size' => 'sm']); ?>
                        </span>
                        <span class="u-grow">
                            <?php component('progress', [
                                'label'   => $ms['label'],
                                'display' => fmt_pct($ms['progress'], 0),
                                'value'   => $ms['progress'],
                                'size'    => 'sm',
                                'tone'    => $ms['done'] ? 'success' : '',
                                'aria'    => 'Progres milestone ' . $ms['label'],
                            ]); ?>
                        </span>
                    </li>
                <?php endforeach; ?>
            </ul>
        </article>

        <article class="card card--tinted">
            <header class="card__head">
                <span class="tile tile--forest">
                    <?php component('icon', ['name' => 'tree', 'size' => 'lg']); ?>
                </span>
                <div class="u-grow">
                    <h2 class="card__title">Setara <span data-impact-trees><?= e(number_format($impact['trees'], 1)) ?></span> pohon</h2>
                    <p class="card__meta">Kapasitas serapan karbon selama satu tahun</p>
                </div>
            </header>

            <p class="t-body t-secondary">
                Reduksi <strong><span data-impact-total><?= e(number_format($impact['co2'], 1)) ?></span> kg CO₂e</strong> yang kamu hasilkan setara dengan
                kerja penyerapan karbon oleh pohon muda selama setahun penuh. Angka ini dihitung dari
                gabungan penghematan energi produksi primer dan pencegahan gas metana dari TPA.
            </p>

            <ul class="u-stack-2">
                <li class="u-row">
                    <?php component('icon', ['name' => 'check-circle', 'size' => 'sm', 'class' => 't-forest']); ?>
                    <span class="t-small t-secondary">Data siap dipakai untuk laporan UI GreenMetric kampus.</span>
                </li>
                <li class="u-row">
                    <?php component('icon', ['name' => 'check-circle', 'size' => 'sm', 'class' => 't-forest']); ?>
                    <span class="t-small t-secondary">Audit emisi transparan per transaksi setoran.</span>
                </li>
            </ul>

            <a class="btn btn--primary btn--block" href="<?= e(url('/passbook')) ?>">
                <?php component('icon', ['name' => 'passbook', 'size' => 'md']); ?>Lihat rincian setoran
            </a>
        </article>
    </div>
</section>
