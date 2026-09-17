<?php
/**
 * Page: EcoPoints & Gamify — streaks, badges, rewards and the 500 KG
 * Campus Competition Challenge (requirements R5.1–R5.10).
 *
 * Vars: $user, $streak, $competition, $leaderboard, $badges, $actions,
 *       $rewards, $incentives, $missions
 */
?>

<!-- ── Balance + streak (R5.1, R5.2) ───────────────────────────────────── -->
<section class="section">
    <div class="grid grid--split">
        <article class="card card--dark balance">
            <header class="card__head">
                <span class="tile tile--on-dark">
                    <?php component('icon', ['name' => 'leaf', 'size' => 'lg']); ?>
                </span>
                <div class="u-grow">
                    <h2 class="card__title">EcoPoints Balance</h2>
                    <p class="card__meta"><?= e($user['level']) ?> · Peringkat #<?= e($user['rank_campus']) ?> kampus</p>
                </div>
                <button class="btn btn--leaf btn--sm card__action" type="button" data-redeem>
                    <?php component('icon', ['name' => 'gift', 'size' => 'sm']); ?>Redeem
                </button>
            </header>

            <p class="balance__value" data-count-to="<?= e($user['points']) ?>">
                <?= e(number_format($user['points'])) ?><span class="balance__unit">pts</span>
            </p>
            <p class="t-small" style="color: var(--c-on-dark-3)">
                +<?= e(number_format($user['points_month'])) ?> pts bulan ini ·
                <?= e($user['deposits']) ?> setoran terverifikasi
            </p>

            <hr style="border-color: var(--c-border-dark); margin: var(--sp-2) 0">

            <?php component('streak', [
                'days'  => $streak,
                'count' => $user['streak_days'],
                'dark'  => true,
                'note'  => 'Streak 7 hari penuh — bonus mingguan +50 pts sudah aktif.',
            ]); ?>
        </article>

        <!-- Campus Competition (R5.5) -->
        <article class="card competition">
            <header class="card__head">
                <span class="tile tile--warning">
                    <?php component('icon', ['name' => 'trophy', 'size' => 'lg']); ?>
                </span>
                <div class="u-grow">
                    <h2 class="card__title"><?= e($competition['title']) ?></h2>
                    <p class="card__meta"><?= e($competition['period']) ?> · <?= e($competition['teams']) ?> fakultas</p>
                </div>
                <span class="pill pill--warning card__action">
                    <?php component('icon', ['name' => 'clock']); ?><?= e($competition['days_left']) ?> hari lagi
                </span>
            </header>

            <p class="t-body t-secondary">“<?= e($competition['subtitle']) ?>”</p>

            <div class="competition__figure">
                <p class="t-small t-muted">Monthly Target</p>
                <p class="t-data-xl" data-count-to="<?= e($competition['current']) ?>">
                    <?= e(number_format($competition['current'], 0)) ?><span class="t-h4 t-muted"> / <?= e(number_format($competition['target'], 0)) ?> kg</span>
                </p>
            </div>

            <?php component('progress', [
                'value'     => $competition['progress'],
                'size'      => 'lg',
                'display'   => fmt_pct($competition['progress'], 0),
                'label'     => 'Progres kolektif kampus',
                'footLeft'  => 'Sisa ' . number_format($competition['target'] - $competition['current'], 0) . ' kg',
                'footRight' => 'Rata-rata ' . number_format(($competition['target'] - $competition['current']) / max($competition['days_left'], 1), 1) . ' kg/hari',
                'aria'      => 'Progres Campus Competition Challenge 500 KG',
            ]); ?>

            <div class="callout">
                <?php component('icon', ['name' => 'gift', 'size' => 'md']); ?>
                <span><strong>Hadiah:</strong> <?= e($competition['reward']) ?></span>
            </div>
        </article>
    </div>
</section>

<!-- ── Leaderboard (R5.6) ──────────────────────────────────────────────── -->
<section class="section">
    <div class="grid grid--split">
        <article class="card">
            <header class="card__head">
                <div class="u-grow">
                    <h2 class="card__title">Green Campus Challenge</h2>
                    <p class="card__meta">Peringkat fakultas — Top <?= e(count($leaderboard)) ?></p>
                </div>
                <span class="pill pill--forest card__action">Live</span>
            </header>

            <ol class="leaderboard">
                <?php foreach ($leaderboard as $row): ?>
                    <li class="<?= e(cx(['leaderboard__row', $row['you'] ? 'is-you' : ''])) ?>">
                        <span class="leaderboard__rank"><?= e($row['rank']) ?></span>
                        <span class="leaderboard__team">
                            <span class="leaderboard__name">
                                <?= e($row['team']) ?>
                                <?php if ($row['you']): ?>
                                    <span class="pill pill--leaf">Fakultasmu</span>
                                <?php endif; ?>
                            </span>
                            <?php component('progress', [
                                'value' => $row['progress'],
                                'size'  => 'sm',
                                'aria'  => 'Progres ' . $row['team'],
                            ]); ?>
                        </span>
                        <span class="leaderboard__kg"><?= e(number_format($row['kg'], 1)) ?> kg</span>
                    </li>
                <?php endforeach; ?>
            </ol>

            <footer class="card__foot">
                <span class="t-small t-muted">Kontribusimu</span>
                <strong class="t-data-sm" style="margin-left:auto"><?= e(fmt_kg($user['total_kg'])) ?> · #<?= e($user['rank_faculty']) ?> di fakultas</strong>
            </footer>
        </article>

        <!-- Weekly missions -->
        <article class="card">
            <header class="card__head">
                <div class="u-grow">
                    <h2 class="card__title">Weekly Mission</h2>
                    <p class="card__meta">Reset setiap Senin pukul 00.00</p>
                </div>
                <span class="pill pill--info card__action">
                    <?php component('icon', ['name' => 'target']); ?>+150 pts total
                </span>
            </header>

            <ul class="u-stack">
                <?php foreach ($missions as $mission): ?>
                    <li class="mission <?= $mission['done'] ? 'is-done' : '' ?>">
                        <span class="mission__mark">
                            <?php component('icon', ['name' => $mission['done'] ? 'check' : 'target', 'size' => 'sm']); ?>
                        </span>
                        <span class="u-grow">
                            <?php component('progress', [
                                'label'     => $mission['label'],
                                'display'   => $mission['current'] . ' / ' . $mission['target'],
                                'value'     => $mission['progress'],
                                'tone'      => $mission['done'] ? 'success' : '',
                                'footLeft'  => $mission['done'] ? 'Selesai' : 'Sedang berjalan',
                                'footRight' => '+' . $mission['points'] . ' pts',
                                'aria'      => 'Progres misi ' . $mission['label'],
                            ]); ?>
                        </span>
                    </li>
                <?php endforeach; ?>
            </ul>

            <footer class="card__foot">
                <a class="btn btn--gamify btn--sm btn--block" href="<?= e(url('/sorting')) ?>">
                    <?php component('icon', ['name' => 'scan', 'size' => 'sm']); ?>Lanjut misi dengan Smart Scan
                </a>
            </footer>
        </article>
    </div>
</section>

<!-- ── Badges (R5.3, R5.4, R5.9) ───────────────────────────────────────── -->
<section class="section">
    <?php component('section-head', [
        'title' => 'Badges',
        'sub'   => 'Penghargaan atas konsistensi memilah dan menyetor sampah.',
    ]); ?>
    <div class="grid grid--badges" data-badge-grid>
        <?php foreach ($badges as $badge): ?>
            <?php component('badge', $badge); ?>
        <?php endforeach; ?>
    </div>
</section>

<!-- ── Earning actions (R5.7) ──────────────────────────────────────────── -->
<section class="section">
    <?php component('section-head', [
        'title' => 'Cara Mengumpulkan Poin',
        'sub'   => 'Empat aksi berpoin yang bisa dilakukan setiap hari di kampus.',
    ]); ?>
    <div class="grid grid--4">
        <?php foreach ($actions as $action): ?>
            <article class="action-tile">
                <div class="u-row">
                    <span class="tile tile--sm tile--<?= e($action['tone']) ?>">
                        <?php component('icon', ['name' => $action['icon'], 'size' => 'md']); ?>
                    </span>
                    <span class="action-tile__points" style="margin-left:auto">+<?= e($action['points']) ?></span>
                </div>
                <h3 class="t-h5"><?= e($action['label']) ?></h3>
                <p class="t-small t-muted"><?= e($action['desc']) ?></p>
            </article>
        <?php endforeach; ?>
    </div>
</section>

<!-- ── Rewards catalog (R5.8) ──────────────────────────────────────────── -->
<section class="section">
    <?php component('section-head', [
        'title' => 'Pilihan Penukaran EcoPoints',
        'sub'   => 'Saldo kamu saat ini ' . fmt_pts($user['points']) . '.',
    ]); ?>
    <div class="grid grid--4" data-reward-grid>
        <?php foreach ($rewards as $reward):
            $affordable = $user['points'] >= $reward['cost'];
            $short = $reward['cost'] - $user['points'];
            ?>
            <article class="<?= e(cx(['reward', $affordable ? '' : 'is-locked'])) ?>"
                     data-reward-cost="<?= e($reward['cost']) ?>">
                <div class="u-row">
                    <span class="tile tile--<?= e($reward['tone']) ?>">
                        <?php component('icon', ['name' => $reward['icon'], 'size' => 'lg']); ?>
                    </span>
                    <span class="pill pill--muted" style="margin-left:auto"><?= e($reward['stock']) ?></span>
                </div>

                <div>
                    <h3 class="t-h5 reward__title"><?= e($reward['label']) ?></h3>
                    <p class="t-small t-muted"><?= e($reward['desc']) ?></p>
                </div>

                <span class="reward__cost">
                    <?php component('icon', ['name' => 'coin', 'size' => 'sm']); ?>
                    <?= e(number_format($reward['cost'])) ?> pts
                </span>

                <?php if ($affordable): ?>
                    <button class="btn btn--gamify btn--sm btn--block u-mt-auto" type="button" data-redeem>
                        <?php component('icon', ['name' => 'gift', 'size' => 'sm']); ?>Claim Reward
                    </button>
                <?php else: ?>
                    <button class="btn btn--secondary btn--sm btn--block u-mt-auto is-disabled" type="button" disabled>
                        Butuh <?= e(number_format($short)) ?> pts lagi
                    </button>
                <?php endif; ?>
            </article>
        <?php endforeach; ?>
    </div>
</section>

<!-- ── Academic & social incentives ────────────────────────────────────── -->
<section class="section">
    <div class="grid grid--2">
        <article class="card card--tinted">
            <header class="card__head">
                <div class="u-grow">
                    <h2 class="card__title">Insentif Akademik &amp; Sosial</h2>
                    <p class="card__meta">Poin yang berdampak di luar aplikasi</p>
                </div>
            </header>

            <ul class="u-stack-3">
                <?php foreach ($incentives as $inc): ?>
                    <li class="u-row">
                        <span class="tile tile--sm tile--forest">
                            <?php component('icon', ['name' => $inc['icon'], 'size' => 'md']); ?>
                        </span>
                        <span class="u-grow">
                            <span class="t-body" style="font-weight: var(--fw-semibold); display:block"><?= e($inc['label']) ?></span>
                            <span class="t-small t-muted"><?= e($inc['desc']) ?></span>
                        </span>
                    </li>
                <?php endforeach; ?>
            </ul>
        </article>

        <article class="card card--dark">
            <header class="card__head">
                <span class="tile tile--on-dark">
                    <?php component('icon', ['name' => 'sparkle', 'size' => 'lg']); ?>
                </span>
                <div class="u-grow">
                    <h2 class="card__title">More rewards</h2>
                    <p class="card__meta">at the end of the month!</p>
                </div>
            </header>

            <p class="t-body" style="color: var(--c-on-dark-2)">
                Tiga fakultas dengan tonase tertinggi mendapatkan tambahan kuota merchandise dan
                sesi apresiasi bersama rektorat. Jaga streak dan ajak satu angkatan ikut memilah.
            </p>

            <?php component('progress', [
                'value'     => $competition['progress'],
                'size'      => 'lg',
                'dark'      => true,
                'label'     => 'Campus Competition',
                'display'   => number_format($competition['current'], 0) . ' / ' . number_format($competition['target'], 0) . ' kg',
                'footLeft'  => 'Top 3 fakultas',
                'footRight' => fmt_pct($competition['progress'], 0),
                'aria'      => 'Progres tantangan kampus',
            ]); ?>

            <a class="btn btn--leaf btn--block" href="<?= e(url('/pickup')) ?>">
                <?php component('icon', ['name' => 'truck', 'size' => 'md']); ?>Setor massal lewat pickup
            </a>
        </article>
    </div>
</section>

<!-- Redeem feedback toast -->
<div class="floating-toast" data-redeem-toast hidden>
    <div class="toast">
        <span class="tile tile--sm tile--leaf">
            <?php component('icon', ['name' => 'gift', 'size' => 'md']); ?>
        </span>
        <div class="u-grow">
            <p class="toast__title">Penukaran dicatat</p>
            <p class="toast__desc">Kode voucher dikirim ke email kampus kamu.</p>
        </div>
        <button class="toast__close" type="button" data-toast-close>
            <?php component('icon', ['name' => 'x', 'size' => 'sm', 'label' => 'Tutup notifikasi']); ?>
        </button>
    </div>
</div>
