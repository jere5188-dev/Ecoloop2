<?php
/**
 * Page: Profile — identity, achievements and notification preferences.
 *
 * Vars: $user, $badges, $preferences, $totals, $impact
 */
?>

<section class="section">
    <div class="grid grid--split">
        <article class="card">
            <div class="profile-head">
                <span class="profile-head__avatar"><?= e($user['initials']) ?></span>
                <div class="u-grow">
                    <h2 class="t-h2"><?= e($user['name']) ?></h2>
                    <p class="t-body t-secondary"><?= e($user['program']) ?> · <?= e($user['faculty']) ?></p>
                    <p class="t-small t-muted"><?= e($user['student_id']) ?> · <?= e($user['joined']) ?></p>
                    <div class="u-row u-row--wrap" style="margin-top: var(--sp-3)">
                        <span class="pill pill--forest">
                            <?php component('icon', ['name' => 'medal']); ?><?= e($user['level']) ?>
                        </span>
                        <span class="pill pill--leaf pill--data">
                            <?php component('icon', ['name' => 'coin']); ?><?= e(fmt_pts($user['points'])) ?>
                        </span>
                        <span class="pill pill--warning">
                            <?php component('icon', ['name' => 'flame']); ?><?= e($user['streak_days']) ?> hari streak
                        </span>
                    </div>
                </div>
            </div>

            <div class="grid grid--stats" style="--gap: var(--sp-3)">
                <?php
                component('stat-card', [
                    'icon'  => 'weight',
                    'tone'  => 'forest',
                    'label' => 'Total terdaur ulang',
                    'value' => fmt_kg($totals['kg'], false),
                    'unit'  => 'KG',
                ]);
                component('stat-card', [
                    'icon'  => 'leaf',
                    'tone'  => 'info',
                    'label' => 'CO₂ direduksi',
                    'value' => number_format($impact['co2'], 1),
                    'unit'  => 'kg',
                ]);
                component('stat-card', [
                    'icon'  => 'trophy',
                    'tone'  => 'warning',
                    'label' => 'Peringkat kampus',
                    'value' => '#' . $user['rank_campus'],
                    'count' => false,
                ]);
                ?>
            </div>

            <footer class="card__foot">
                <a class="btn btn--secondary btn--sm" href="<?= e(url('/impact')) ?>">
                    <?php component('icon', ['name' => 'chart', 'size' => 'sm']); ?>Laporan dampak
                </a>
                <a class="btn btn--primary btn--sm" style="margin-left:auto" href="<?= e(url('/competition')) ?>">
                    EcoPoints<?php component('icon', ['name' => 'arrow-right', 'size' => 'sm']); ?>
                </a>
            </footer>
        </article>

        <article class="card">
            <header class="card__head">
                <div class="u-grow">
                    <h2 class="card__title">Preferensi</h2>
                    <p class="card__meta">Atur pengingat dan visibilitas datamu</p>
                </div>
            </header>

            <ul class="u-stack">
                <?php foreach ($preferences as $pref): ?>
                    <li class="pref-row">
                        <span class="u-grow">
                            <span class="t-body" style="font-weight: var(--fw-semibold); display:block"><?= e($pref['label']) ?></span>
                            <span class="t-small t-muted"><?= e($pref['desc']) ?></span>
                        </span>
                        <label class="switch">
                            <input type="checkbox" <?= $pref['on'] ? 'checked' : '' ?>>
                            <span class="switch__track"></span>
                            <span class="sr-only"><?= e($pref['label']) ?></span>
                        </label>
                    </li>
                <?php endforeach; ?>
            </ul>

            <footer class="card__foot">
                <a class="btn btn--ghost btn--sm" href="<?= e(url('/pickup/reset')) ?>">
                    <?php component('icon', ['name' => 'settings', 'size' => 'sm']); ?>Reset draft pickup
                </a>
            </footer>
        </article>
    </div>
</section>

<section class="section">
    <?php component('section-head', [
        'title'      => 'Pencapaian',
        'sub'        => 'Badge yang sudah terbuka dan yang sedang dikejar.',
        'actionText' => 'Semua badge',
        'actionHref' => '/competition',
    ]); ?>
    <div class="grid grid--badges">
        <?php foreach ($badges as $badge): ?>
            <?php component('badge', $badge); ?>
        <?php endforeach; ?>
    </div>
</section>
