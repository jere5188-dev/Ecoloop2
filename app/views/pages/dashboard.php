<?php
/**
 * Page: Dashboard (home) — condensed widgets from all five core features
 * (requirement R6.7).
 *
 * Vars: $user, $totals, $summaryRows, $impact, $competition, $streak,
 *       $quickActions, $missions, $transactions
 */
$impactPct = $impact['goal'] > 0 ? $impact['co2'] / $impact['goal'] * 100 : 0;
?>

<!-- ── Greeting hero ────────────────────────────────────────────────────── -->
<section class="section">
    <div class="hero card--dark">
        <div class="hero__content">
            <p class="t-eyebrow" style="color: var(--c-leaf)">Selamat datang kembali</p>
            <h2 class="hero__title">Halo, <?= e($user['first_name']) ?></h2>
            <p class="hero__text">
                Kamu sudah menyelamatkan <strong><?= e(fmt_kg($totals['kg'])) ?></strong> sampah dari TPA
                dan mereduksi <strong><?= e(number_format($user['co2_kg'], 1)) ?> kg CO₂</strong> bulan ini.
                Aksi kecilmu jadi gerakan besar di kampus.
            </p>
            <div class="hero__actions">
                <a class="btn btn--leaf" href="<?= e(url('/sorting')) ?>">
                    <?php component('icon', ['name' => 'scan', 'size' => 'md']); ?>Mulai Smart Scan
                </a>
                <a class="btn btn--on-dark" href="<?= e(url('/pickup')) ?>">
                    Request Pickup<?php component('icon', ['name' => 'arrow-right', 'size' => 'sm']); ?>
                </a>
            </div>
            <div class="hero__streak">
                <?php component('streak', [
                    'days'  => $streak,
                    'count' => $user['streak_days'],
                    'dark'  => true,
                    'note'  => 'Setor hari ini untuk menjaga streak dan bonus +40 pts.',
                ]); ?>
            </div>
        </div>

        <div class="hero__aside">
            <?php component('ring-gauge', [
                'value'   => $impactPct,
                'display' => number_format($impact['co2'], 1),
                'unit'    => 'kg',
                'label'   => 'CO₂ Reduced',
                'size'    => 156,
                'dark'    => true,
                'leaf'    => true,
                'aria'    => 'Reduksi CO₂ terhadap target bulanan',
            ]); ?>
            <p class="t-caption t-center" style="color: var(--c-on-dark-3)">
                Target bulan ini <?= e(number_format($impact['goal'], 0)) ?> kg CO₂
            </p>
        </div>

        <span class="hero__leaf" aria-hidden="true">
            <?php component('icon', ['name' => 'leaf']); ?>
        </span>
    </div>
</section>

<!-- ── Stat strip ───────────────────────────────────────────────────────── -->
<section class="section" aria-labelledby="dash-stats">
    <h2 class="sr-only" id="dash-stats">Ringkasan angka</h2>
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
            'label' => 'EcoPoints terkumpul',
            'value' => fmt_pts($user['points'], false),
            'unit'  => 'pts',
            'delta' => '+' . number_format($user['points_month']) . ' pts',
            'note'  => 'bulan ini',
        ]);
        component('stat-card', [
            'icon'  => 'leaf',
            'tone'  => 'info',
            'label' => 'Reduksi emisi karbon',
            'value' => number_format($impact['co2'], 1),
            'unit'  => 'kg CO₂',
            'delta' => '+' . number_format($impact['co2'] - 14.4, 1) . ' kg',
            'note'  => 'vs bulan lalu',
        ]);
        component('stat-card', [
            'icon'  => 'trophy',
            'tone'  => 'warning',
            'label' => 'Peringkat fakultas',
            'value' => '#' . $user['rank_faculty'],
            'unit'  => '',
            'delta' => '+2 posisi',
            'note'  => 'Fakultas Teknik',
            'count' => false,
        ]);
        ?>
    </div>
</section>

<!-- ── Passbook + Impact + Competition ─────────────────────────────────── -->
<section class="section">
    <div class="grid grid--split">
        <div class="u-stack">
            <!-- Waste Passbook (condensed) -->
            <article class="card">
                <header class="card__head">
                    <div class="u-grow">
                        <h2 class="card__title">Waste Passbook</h2>
                        <p class="card__meta">Buku tabungan digital per kategori</p>
                    </div>
                    <a class="link-action card__action" href="<?= e(url('/passbook')) ?>">
                        View All<?php component('icon', ['name' => 'chevron-right', 'size' => 'sm']); ?>
                    </a>
                </header>

                <ul class="mini-ledger">
                    <?php foreach ($summaryRows as $row): ?>
                        <li class="mini-ledger__row">
                            <span class="tile tile--sm tile--<?= e($row['tone']) ?>">
                                <?php component('icon', ['name' => $row['icon'], 'size' => 'md']); ?>
                            </span>
                            <span class="u-grow">
                                <span class="mini-ledger__label"><?= e($row['label']) ?></span>
                                <span class="mini-ledger__delta t-caption t-success">
                                    <?= e(fmt_delta((float) $row['delta'])) ?> minggu ini
                                </span>
                            </span>
                            <span class="mini-ledger__value t-data-sm"><?= e(fmt_kg((float) $row['kg'])) ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>

                <footer class="card__foot">
                    <span class="t-small t-muted">Total terverifikasi</span>
                    <strong class="t-data-md u-mt-auto" style="margin-left:auto"><?= e(fmt_kg($totals['kg'])) ?></strong>
                </footer>
            </article>

            <!-- Weekly missions -->
            <article class="card">
                <header class="card__head">
                    <div class="u-grow">
                        <h2 class="card__title">Weekly Mission</h2>
                        <p class="card__meta">Selesaikan misi untuk bonus EcoPoints</p>
                    </div>
                    <span class="pill pill--forest">
                        <?php component('icon', ['name' => 'target']); ?>3 misi
                    </span>
                </header>

                <ul class="u-stack-3">
                    <?php foreach ($missions as $mission): ?>
                        <li>
                            <?php component('progress', [
                                'label'   => $mission['label'],
                                'display' => $mission['current'] . ' / ' . $mission['target'],
                                'value'   => $mission['progress'],
                                'tone'    => $mission['done'] ? 'success' : '',
                                'footLeft'  => $mission['done'] ? 'Selesai — poin sudah masuk' : 'Sedang berjalan',
                                'footRight' => '+' . $mission['points'] . ' pts',
                                'aria'    => 'Progres misi: ' . $mission['label'],
                            ]); ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </article>
        </div>

        <div class="u-stack">
            <!-- Impact Metric (condensed) -->
            <article class="card">
                <header class="card__head">
                    <div class="u-grow">
                        <h2 class="card__title">Impact Metric</h2>
                        <p class="card__meta">This Month</p>
                    </div>
                    <a class="link-action card__action" href="<?= e(url('/impact')) ?>">
                        Detail<?php component('icon', ['name' => 'chevron-right', 'size' => 'sm']); ?>
                    </a>
                </header>

                <div class="impact-mini">
                    <?php component('ring-gauge', [
                        'value'   => $impactPct,
                        'display' => number_format($impact['co2'], 1),
                        'unit'    => 'kg',
                        'label'   => 'CO₂ Reduced',
                        'size'    => 124,
                        'stroke'  => 9,
                        'aria'    => 'Reduksi CO₂ terhadap target bulanan',
                    ]); ?>
                    <div class="u-stack-2 u-grow">
                        <div class="metric">
                            <p class="metric__label">Waste Diverted</p>
                            <p class="metric__value metric__value--md" data-count-to="<?= e($impact['diverted']) ?>">
                                <?= e(number_format($impact['diverted'], 1)) ?> kg
                            </p>
                        </div>
                        <div class="callout" style="padding: var(--sp-3)">
                            <?php component('icon', ['name' => 'tree', 'size' => 'md']); ?>
                            <span><strong>+<?= e(number_format($impact['trees'], 1)) ?> pohon</strong> setara diserap setahun</span>
                        </div>
                    </div>
                </div>
            </article>

            <!-- Campus Competition -->
            <article class="card">
                <header class="card__head">
                    <div class="u-grow">
                        <h2 class="card__title">Campus Competition</h2>
                        <p class="card__meta"><?= e($competition['period']) ?></p>
                    </div>
                    <span class="pill pill--warning">
                        <?php component('icon', ['name' => 'clock']); ?><?= e($competition['days_left']) ?> hari lagi
                    </span>
                </header>

                <div class="u-stack-3">
                    <div>
                        <p class="t-small t-muted">Monthly Target</p>
                        <p class="t-data-lg">
                            <?= e(number_format($competition['current'], 0)) ?><span class="t-body t-muted"> / <?= e(number_format($competition['target'], 0)) ?> kg</span>
                        </p>
                    </div>
                    <?php component('progress', [
                        'value'     => $competition['progress'],
                        'size'      => 'lg',
                        'display'   => fmt_pct($competition['progress'], 0),
                        'footLeft'  => $competition['subtitle'],
                        'footRight' => $competition['teams'] . ' fakultas bersaing',
                        'aria'      => 'Progres Campus Competition Challenge',
                    ]); ?>
                    <a class="btn btn--gamify btn--block" href="<?= e(url('/competition')) ?>">
                        <?php component('icon', ['name' => 'trophy', 'size' => 'md']); ?>Lihat Leaderboard
                    </a>
                </div>
            </article>
        </div>
    </div>
</section>

<!-- ── Quick actions ────────────────────────────────────────────────────── -->
<section class="section" aria-labelledby="dash-quick">
    <?php component('section-head', [
        'title' => 'Aksi Cepat',
        'sub'   => 'Empat langkah paling sering dipakai mahasiswa.',
        'level' => 2,
    ]); ?>
    <div class="grid grid--4">
        <?php foreach ($quickActions as $action): ?>
            <a class="quick-action" href="<?= e(url($action['path'])) ?>">
                <span class="tile tile--<?= e($action['tone']) ?>">
                    <?php component('icon', ['name' => $action['icon'], 'size' => 'lg']); ?>
                </span>
                <span class="u-grow">
                    <span class="quick-action__label"><?= e($action['label']) ?></span>
                    <span class="quick-action__desc"><?= e($action['desc']) ?></span>
                </span>
                <?php component('icon', ['name' => 'chevron-right', 'size' => 'sm', 'class' => 'quick-action__chev']); ?>
            </a>
        <?php endforeach; ?>
    </div>
</section>

<!-- ── Recent activity ──────────────────────────────────────────────────── -->
<section class="section">
    <article class="card">
        <header class="card__head">
            <div class="u-grow">
                <h2 class="card__title">Aktivitas Terbaru</h2>
                <p class="card__meta">Tiga setoran terakhir</p>
            </div>
            <a class="link-action card__action" href="<?= e(url('/passbook')) ?>">
                Semua riwayat<?php component('icon', ['name' => 'chevron-right', 'size' => 'sm']); ?>
            </a>
        </header>

        <ul class="activity">
            <?php foreach ($transactions as $tx): ?>
                <li class="activity__row">
                    <span class="tile tile--sm tile--<?= e($tx['tone']) ?>">
                        <?php component('icon', ['name' => $tx['icon'], 'size' => 'md']); ?>
                    </span>
                    <span class="u-grow">
                        <span class="activity__title"><?= e($tx['title']) ?></span>
                        <span class="activity__meta"><?= e($tx['date']) ?> · <?= e($tx['time']) ?> · <?= e($tx['category']) ?></span>
                    </span>
                    <span class="activity__figures">
                        <span class="t-data-sm"><?= e(fmt_kg((float) $tx['kg'])) ?></span>
                        <span class="t-caption t-forest"><?= $tx['points'] > 0 ? '+' . e($tx['points']) . ' pts' : 'menunggu' ?></span>
                    </span>
                </li>
            <?php endforeach; ?>
        </ul>
    </article>
</section>
