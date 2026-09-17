<?php
/**
 * Page: Smart Sorting — the AI camera scanner overlay
 * (requirements R3.1–R3.8).
 *
 * Vars: $detection, $steps, $guide, $tips
 */
?>

<section class="section">
    <div class="scanner" data-scanner data-state="idle">

        <!-- ── Camera viewport (R3.1, R3.7) ─────────────────────────────── -->
        <div class="scanner__stage">
            <div class="viewport" data-scanner-viewport>
                <div class="viewport__scene" aria-hidden="true">
                    <!-- Simulated camera feed: a PET bottle silhouette -->
                    <svg class="viewport__object" viewBox="0 0 120 260" fill="none">
                        <defs>
                            <linearGradient id="bottle-body" x1="0" y1="0" x2="1" y2="1">
                                <stop offset="0%" stop-color="#dff0e4" stop-opacity="0.92"></stop>
                                <stop offset="45%" stop-color="#a8c9b4" stop-opacity="0.72"></stop>
                                <stop offset="100%" stop-color="#6b8e71" stop-opacity="0.5"></stop>
                            </linearGradient>
                        </defs>
                        <path d="M46 8h28v18c0 9 3 13 8 19 6 7 9 14 9 24v168a11 11 0 0 1-11 11H50a11 11 0 0 1-11-11V69c0-10 3-17 9-24 5-6 8-10 8-19z"
                              fill="url(#bottle-body)" stroke="#ffffff" stroke-opacity="0.5" stroke-width="2"></path>
                        <rect x="40" y="120" width="40" height="52" rx="6" fill="#2e7d32" fill-opacity="0.5"></rect>
                        <path d="M44 132h32M44 142h24M44 152h32" stroke="#ffffff" stroke-opacity="0.6" stroke-width="2.5" stroke-linecap="round"></path>
                        <rect x="44" y="2" width="32" height="10" rx="3" fill="#a7c957" fill-opacity="0.9"></rect>
                        <path d="M52 40c-4 8-6 16-6 26v150" stroke="#ffffff" stroke-opacity="0.35" stroke-width="3" stroke-linecap="round"></path>
                    </svg>
                </div>

                <!-- Scan reticle with animated corner brackets -->
                <div class="reticle" data-reticle>
                    <span class="reticle__corner reticle__corner--tl"></span>
                    <span class="reticle__corner reticle__corner--tr"></span>
                    <span class="reticle__corner reticle__corner--bl"></span>
                    <span class="reticle__corner reticle__corner--br"></span>
                    <span class="reticle__sweep" data-sweep></span>
                    <span class="reticle__tag" data-reticle-tag><?= e($detection['material_en']) ?></span>
                </div>

                <!-- Top overlay chrome -->
                <div class="viewport__top">
                    <span class="pill pill--on-dark">
                        <?php component('icon', ['name' => 'camera']); ?>AI Smart Sorting
                    </span>
                    <span class="pill pill--on-dark pill--data" data-scanner-conf hidden>
                        <?= e($detection['confidence']) ?>% match
                    </span>
                </div>

                <!-- Status line + progress (R3.2) -->
                <div class="viewport__status" data-scanner-status aria-live="polite">
                    <p class="viewport__status-text" data-status-text>Arahkan kamera ke kemasan sampah</p>
                    <div class="scan-progress" data-scan-progress hidden>
                        <span class="scan-progress__label">Scanning…</span>
                        <span class="scan-progress__track"><span class="scan-progress__fill" data-scan-fill></span></span>
                    </div>
                </div>

                <!-- Controls (R3.8) -->
                <div class="viewport__controls">
                    <button class="viewport__ctl" type="button" title="Galeri">
                        <?php component('icon', ['name' => 'paper', 'size' => 'lg', 'label' => 'Pilih dari galeri']); ?>
                    </button>

                    <button class="shutter" type="button" data-scanner-shutter aria-describedby="shutter-hint">
                        <span class="shutter__ring"></span>
                        <span class="shutter__core"></span>
                        <span class="sr-only">Ambil gambar dan deteksi material</span>
                    </button>

                    <button class="viewport__ctl" type="button" data-scanner-reset title="Ulangi pemindaian">
                        <?php component('icon', ['name' => 'x', 'size' => 'lg', 'label' => 'Ulangi pemindaian']); ?>
                    </button>
                </div>
            </div>

            <p class="t-caption t-muted t-center" id="shutter-hint">
                Mode demo — deteksi disimulasikan tanpa mengakses kamera perangkat.
            </p>

            <ul class="scanner__tips">
                <?php foreach ($tips as $tip): ?>
                    <li class="chip chip--outline chip--static">
                        <?php component('icon', ['name' => 'info']); ?><?= e($tip) ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>

        <!-- ── Result panel (R3.3–R3.6) ────────────────────────────────── -->
        <div class="scanner__panel">

            <!-- Idle prompt -->
            <article class="card" data-scanner-idle>
                <header class="card__head">
                    <span class="tile tile--forest">
                        <?php component('icon', ['name' => 'scan', 'size' => 'lg']); ?>
                    </span>
                    <div class="u-grow">
                        <h2 class="card__title">Bingung sampah ini masuk kategori mana?</h2>
                        <p class="card__meta">Satu jepretan, EcoLoop kenali materialnya.</p>
                    </div>
                </header>

                <p class="t-body t-secondary">
                    Kamera AI mendeteksi jenis kemasan dan langsung memberi instruksi sortir yang benar,
                    sehingga material tetap bernilai tinggi saat sampai di mitra daur ulang.
                </p>

                <ol class="u-stack-2">
                    <li class="u-row">
                        <span class="num-dot">1</span>
                        <span class="t-small t-secondary">Letakkan satu kemasan di dalam bingkai pemindaian.</span>
                    </li>
                    <li class="u-row">
                        <span class="num-dot">2</span>
                        <span class="t-small t-secondary">Tekan tombol shutter, tunggu hasil deteksi.</span>
                    </li>
                    <li class="u-row">
                        <span class="num-dot">3</span>
                        <span class="t-small t-secondary">Ikuti 4 langkah persiapan sebelum menyetor.</span>
                    </li>
                </ol>

                <button class="btn btn--primary btn--block" type="button" data-scanner-shutter>
                    <?php component('icon', ['name' => 'camera', 'size' => 'md']); ?>Mulai deteksi material
                </button>
            </article>

            <!-- Detection result -->
            <article class="card result" data-scanner-result hidden>
                <header class="card__head">
                    <span class="tile tile--lg tile--<?= e($detection['tone']) ?>">
                        <?php component('icon', ['name' => $detection['icon'], 'size' => 'xl']); ?>
                    </span>
                    <div class="u-grow">
                        <p class="t-eyebrow">Detected</p>
                        <h2 class="t-h3"><?= e($detection['material']) ?></h2>
                        <p class="card__meta"><?= e($detection['technical']) ?></p>
                    </div>
                </header>

                <div class="result__facts">
                    <div class="result__fact">
                        <span class="t-caption t-muted">Recyclability</span>
                        <span class="t-small t-forest" style="font-weight: var(--fw-semibold)">
                            <?= e($detection['recyclable']) ?>
                        </span>
                    </div>
                    <div class="result__fact">
                        <span class="t-caption t-muted">Estimasi berat</span>
                        <span class="t-data-sm"><?= e($detection['weight_min']) ?>–<?= e($detection['weight_max']) ?> g</span>
                    </div>
                    <div class="result__fact">
                        <span class="t-caption t-muted">Confidence</span>
                        <span class="t-data-sm"><?= e($detection['confidence']) ?>%</span>
                    </div>
                    <div class="result__fact">
                        <span class="t-caption t-muted">Reward</span>
                        <span class="t-data-sm t-forest">+<?= e($detection['points']) ?> pts</span>
                    </div>
                </div>

                <!-- 4-step instruction tooltip (R3.4, R3.5) -->
                <section class="prep" aria-labelledby="prep-title">
                    <header class="prep__head">
                        <h3 class="t-h5" id="prep-title">Follow these steps</h3>
                        <span class="pill pill--forest">4 langkah sebelum menyetor</span>
                    </header>

                    <ol class="prep__list" data-prep-list>
                        <?php foreach ($steps as $i => $step): ?>
                            <li class="<?= e(cx(['prep__step', $i === 0 ? 'is-current' : ''])) ?>"
                                data-prep-step="<?= e($step['no']) ?>">
                                <button class="prep__toggle" type="button" aria-expanded="<?= $i === 0 ? 'true' : 'false' ?>">
                                    <span class="prep__no"><?= e($step['no']) ?></span>
                                    <span class="prep__heading">
                                        <span class="prep__title"><?= e($step['title']) ?></span>
                                        <span class="prep__en">(<?= e($step['en']) ?>)</span>
                                    </span>
                                    <span class="prep__icon">
                                        <?php component('icon', ['name' => $step['icon'], 'size' => 'md']); ?>
                                    </span>
                                </button>
                                <p class="prep__desc"><?= e($step['desc']) ?></p>
                            </li>
                        <?php endforeach; ?>
                    </ol>

                    <div class="prep__nav">
                        <button class="btn btn--secondary btn--sm" type="button" data-prep-prev>
                            <?php component('icon', ['name' => 'chevron-left', 'size' => 'sm']); ?>Sebelumnya
                        </button>
                        <span class="t-small t-muted" data-prep-counter>Langkah 1 dari 4</span>
                        <button class="btn btn--primary btn--sm" type="button" data-prep-next>
                            Berikutnya<?php component('icon', ['name' => 'chevron-right', 'size' => 'sm']); ?>
                        </button>
                    </div>
                </section>

                <div class="callout callout--info">
                    <?php component('icon', ['name' => 'map', 'size' => 'md']); ?>
                    <span>Masukkan ke <strong><?= e($detection['bin']) ?></strong>. Drop-off box terdekat: Kantin Teknik (120 m).</span>
                </div>

                <footer class="card__foot">
                    <button class="btn btn--secondary btn--sm" type="button" data-scanner-reset>
                        <?php component('icon', ['name' => 'scan', 'size' => 'sm']); ?>Scan lagi
                    </button>
                    <a class="btn btn--primary btn--sm" style="margin-left:auto" href="<?= e(url('/pickup')) ?>">
                        Kumpulkan &amp; pickup<?php component('icon', ['name' => 'arrow-right', 'size' => 'sm']); ?>
                    </a>
                </footer>
            </article>

            <!-- Material detection toast (R3.6) -->
            <div class="scanner__toast" data-scanner-toast hidden>
                <div class="toast">
                    <span class="tile tile--sm tile--<?= e($detection['tone']) ?>">
                        <?php component('icon', ['name' => $detection['icon'], 'size' => 'md']); ?>
                    </span>
                    <div class="u-grow">
                        <p class="toast__title"><?= e($detection['material_en']) ?> detected</p>
                        <p class="toast__desc"><?= e($detection['weight_note']) ?></p>
                    </div>
                    <button class="toast__close" type="button" data-toast-close>
                        <?php component('icon', ['name' => 'x', 'size' => 'sm', 'label' => 'Tutup notifikasi']); ?>
                    </button>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ── Material guide ──────────────────────────────────────────────────── -->
<section class="section">
    <?php component('section-head', [
        'title' => 'Panduan Material',
        'sub'   => 'Kategori yang dikenali sistem beserta nilai tukar EcoPoints.',
    ]); ?>
    <div class="grid grid--4">
        <?php foreach ($guide as $item): ?>
            <article class="card card--tight <?= $item['accepted'] ? '' : 'card--outline' ?>">
                <div class="u-row">
                    <span class="tile tile--sm tile--<?= e($item['tone']) ?>">
                        <?php component('icon', ['name' => $item['icon'], 'size' => 'md']); ?>
                    </span>
                    <span class="pill pill--<?= $item['accepted'] ? 'success' : 'error' ?>" style="margin-left:auto">
                        <?php component('icon', ['name' => $item['accepted'] ? 'check' : 'x']); ?>
                        <?= $item['accepted'] ? 'Diterima' : 'Residu' ?>
                    </span>
                </div>
                <h3 class="t-h5"><?= e($item['label']) ?></h3>
                <p class="t-small t-muted"><?= e($item['examples']) ?></p>
                <p class="t-data-sm <?= $item['accepted'] ? 't-forest' : 't-muted' ?>"><?= e($item['points']) ?></p>
            </article>
        <?php endforeach; ?>
    </div>
</section>

<!-- ── The 4 steps as a standalone reference ───────────────────────────── -->
<section class="section">
    <article class="card">
        <header class="card__head">
            <div class="u-grow">
                <h2 class="card__title">4 Langkah Sebelum Menyetor</h2>
                <p class="card__meta">Berlaku untuk semua kemasan plastik, kertas, dan logam.</p>
            </div>
            <span class="pill pill--leaf card__action">Wajib</span>
        </header>

        <ol class="grid grid--4">
            <?php foreach ($steps as $step): ?>
                <li class="step-card">
                    <span class="step-card__no"><?= e($step['no']) ?></span>
                    <span class="tile tile--forest">
                        <?php component('icon', ['name' => $step['icon'], 'size' => 'lg']); ?>
                    </span>
                    <h3 class="t-h5"><?= e($step['title']) ?></h3>
                    <p class="t-caption t-muted"><?= e($step['en']) ?></p>
                    <p class="t-small t-secondary"><?= e($step['desc']) ?></p>
                </li>
            <?php endforeach; ?>
        </ol>
    </article>
</section>
