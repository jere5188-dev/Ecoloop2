<?php
/**
 * Page: Waste Pickup — 4-step scheduling wizard
 * (requirements R4.1–R4.10).
 *
 * Vars: $step, $data, $errors, $steps, $locations, $wasteTypes, $timeWindows,
 *       $timeline, $estimate, $user, $done, $minDate, $maxDate
 *
 * $step is 1–4 for the wizard and 5 for the confirmation screen.
 */
$err = static fn (string $k): string => (string) ($errors[$k] ?? '');
?>

<?php if ($step === 5 && $done): ?>

    <!-- ── Confirmation (R4.8) ─────────────────────────────────────────── -->
    <section class="section">
        <div class="grid grid--split">
            <article class="card">
                <div class="confirm">
                    <span class="confirm__check">
                        <?php component('icon', ['name' => 'check', 'size' => '2xl']); ?>
                    </span>
                    <h2 class="t-h2">Permintaan pickup terkirim</h2>
                    <p class="t-body-lg t-secondary">
                        Tim armada EcoLoop akan datang sesuai jadwal reservasi. Simpan nomor permintaan
                        di bawah untuk keperluan konfirmasi di lokasi.
                    </p>
                    <p class="confirm__id"><?= e($done['request_id']) ?></p>
                    <p class="t-small t-muted">Dibuat <?= e($done['created_at']) ?></p>
                </div>

                <dl class="summary-list">
                    <div class="summary-list__row">
                        <dt class="summary-list__key">Lokasi penjemputan</dt>
                        <dd class="summary-list__val">
                            <?= e(\EcoLoop\Data\Pickup::locationLabel((string) $data['location'])) ?>
                            <?php if ($data['location_detail'] !== ''): ?>
                                <br><span class="t-small t-muted"><?= e($data['location_detail']) ?></span>
                            <?php endif; ?>
                        </dd>
                    </div>
                    <div class="summary-list__row">
                        <dt class="summary-list__key">Kontak</dt>
                        <dd class="summary-list__val"><?= e($data['contact']) ?></dd>
                    </div>
                    <div class="summary-list__row">
                        <dt class="summary-list__key">Jenis sampah</dt>
                        <dd class="summary-list__val">
                            <?php foreach ((array) $data['types'] as $t): ?>
                                <span class="pill pill--forest"><?= e(\EcoLoop\Data\Pickup::wasteTypeLabel((string) $t)) ?></span>
                            <?php endforeach; ?>
                        </dd>
                    </div>
                    <div class="summary-list__row">
                        <dt class="summary-list__key">Estimasi berat</dt>
                        <dd class="summary-list__val"><?= e(fmt_kg((float) $done['weight'])) ?></dd>
                    </div>
                    <div class="summary-list__row">
                        <dt class="summary-list__key">Jadwal</dt>
                        <dd class="summary-list__val">
                            <?= e(date('D, d M Y', strtotime((string) $data['date']))) ?><br>
                            <span class="t-small t-muted"><?= e(\EcoLoop\Data\Pickup::timeWindowLabel((string) $data['window'])) ?></span>
                        </dd>
                    </div>
                    <?php if ($data['notes'] !== ''): ?>
                        <div class="summary-list__row">
                            <dt class="summary-list__key">Catatan</dt>
                            <dd class="summary-list__val"><?= e($data['notes']) ?></dd>
                        </div>
                    <?php endif; ?>
                </dl>

                <div class="callout">
                    <?php component('icon', ['name' => 'coin', 'size' => 'md']); ?>
                    <span>
                        Estimasi <strong>+<?= e(number_format((int) $done['points'])) ?> EcoPoints</strong> akan masuk
                        setelah penimbangan digital dan verifikasi kualitas material selesai.
                    </span>
                </div>

                <footer class="card__foot">
                    <a class="btn btn--secondary btn--sm" href="<?= e(url('/pickup/reset')) ?>">
                        <?php component('icon', ['name' => 'plus', 'size' => 'sm']); ?>Buat permintaan baru
                    </a>
                    <a class="btn btn--primary btn--sm" style="margin-left:auto" href="<?= e(url('/passbook')) ?>">
                        Lihat passbook<?php component('icon', ['name' => 'arrow-right', 'size' => 'sm']); ?>
                    </a>
                </footer>
            </article>

            <article class="card">
                <header class="card__head">
                    <div class="u-grow">
                        <h2 class="card__title">Alur Layanan Waste Pickup</h2>
                        <p class="card__meta">Enam tahap dari permintaan sampai poin masuk</p>
                    </div>
                </header>

                <ol class="timeline">
                    <?php foreach ($timeline as $i => $stage): ?>
                        <li class="<?= e(cx(['timeline__item', $i < 3 ? 'is-done' : ''])) ?>">
                            <span class="timeline__dot">
                                <?php if ($i < 3): ?>
                                    <?php component('icon', ['name' => 'check']); ?>
                                <?php else: ?>
                                    <?php component('icon', ['name' => $stage['icon']]); ?>
                                <?php endif; ?>
                            </span>
                            <span class="timeline__body">
                                <span class="timeline__title"><?= e($stage['no']) ?>. <?= e($stage['title']) ?></span>
                                <span class="timeline__desc"><?= e($stage['desc']) ?></span>
                            </span>
                        </li>
                    <?php endforeach; ?>
                </ol>
            </article>
        </div>
    </section>

<?php else: ?>

    <!-- ── Wizard (R4.1–R4.7) ──────────────────────────────────────────── -->
    <section class="section">
        <div class="grid grid--split">
            <article class="card wizard" data-pickup>
                <header class="wizard__head">
                    <div class="u-row u-row--between">
                        <div>
                            <h2 class="card__title">Request Waste Pickup</h2>
                            <p class="card__meta">
                                Langkah <?= e($step) ?> dari <?= e(count($steps)) ?> ·
                                <?= e($steps[$step]['label'] ?? '') ?>
                            </p>
                        </div>
                        <span class="pill pill--forest"><?= e($steps[$step]['en'] ?? '') ?></span>
                    </div>

                    <?php component('step-indicator', ['steps' => $steps, 'current' => $step]); ?>
                </header>

                <?php if ($err('form') !== ''): ?>
                    <div class="callout callout--error" role="alert">
                        <?php component('icon', ['name' => 'alert', 'size' => 'md']); ?>
                        <span><?= e($err('form')) ?></span>
                    </div>
                <?php elseif ($errors): ?>
                    <div class="callout callout--error" role="alert">
                        <?php component('icon', ['name' => 'alert', 'size' => 'md']); ?>
                        <span>
                            <?= e(count($errors)) ?> kolom perlu diperbaiki sebelum melanjutkan.
                            Periksa keterangan merah di bawah.
                        </span>
                    </div>
                <?php endif; ?>

                <form class="wizard__form" method="post" action="<?= e(url('/pickup')) ?>" novalidate>
                    <?= csrf_field() ?>
                    <input type="hidden" name="step" value="<?= e($step) ?>">

                    <?php /* ── STEP 1 · Location ─────────────────────────── */ ?>
                    <?php if ($step === 1): ?>
                        <fieldset class="field">
                            <legend class="field__label">Select Location</legend>
                            <p class="field__hint">Pilih titik temu paling dekat dengan tumpukan sampahmu.</p>

                            <div class="option-list" data-pickup-locations>
                                <?php foreach ($locations as $loc): ?>
                                    <label class="option">
                                        <input type="radio" name="location" value="<?= e($loc['key']) ?>"
                                               <?= old($data, 'location') === $loc['key'] ? 'checked' : '' ?>
                                               <?= $err('location') !== '' ? 'aria-invalid="true"' : '' ?>>
                                        <span class="option__mark" aria-hidden="true"></span>
                                        <span class="option__body">
                                            <span class="option__label"><?= e($loc['label']) ?></span>
                                            <span class="option__desc"><?= e($loc['desc']) ?></span>
                                        </span>
                                        <span class="option__icon">
                                            <?php component('icon', ['name' => $loc['icon'], 'size' => 'lg']); ?>
                                        </span>
                                    </label>
                                <?php endforeach; ?>
                            </div>

                            <?php if ($err('location') !== ''): ?>
                                <p class="field__error">
                                    <?php component('icon', ['name' => 'alert']); ?><?= e($err('location')) ?>
                                </p>
                            <?php endif; ?>
                        </fieldset>

                        <div class="field" data-pickup-detail>
                            <label class="field__label" for="location_detail">Detail titik temu</label>
                            <input class="input" type="text" id="location_detail" name="location_detail"
                                   value="<?= e(old($data, 'location_detail')) ?>"
                                   placeholder="Contoh: Asrama Dahlia blok D2, depan pos jaga"
                                   maxlength="160"
                                   <?= $err('location_detail') !== '' ? 'aria-invalid="true" aria-describedby="err-location_detail"' : '' ?>>
                            <?php if ($err('location_detail') !== ''): ?>
                                <p class="field__error" id="err-location_detail">
                                    <?php component('icon', ['name' => 'alert']); ?><?= e($err('location_detail')) ?>
                                </p>
                            <?php else: ?>
                                <p class="field__hint">Wajib diisi bila memilih “Lokasi Lain”.</p>
                            <?php endif; ?>
                        </div>

                        <div class="field">
                            <label class="field__label" for="contact">Nomor WhatsApp</label>
                            <input class="input" type="tel" id="contact" name="contact" inputmode="tel"
                                   value="<?= e(old($data, 'contact')) ?>"
                                   placeholder="0812 3456 7890"
                                   <?= $err('contact') !== '' ? 'aria-invalid="true" aria-describedby="err-contact"' : '' ?>>
                            <?php if ($err('contact') !== ''): ?>
                                <p class="field__error" id="err-contact">
                                    <?php component('icon', ['name' => 'alert']); ?><?= e($err('contact')) ?>
                                </p>
                            <?php else: ?>
                                <p class="field__hint">Dipakai petugas untuk konfirmasi saat tiba di lokasi.</p>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <?php /* ── STEP 2 · Waste type ───────────────────────── */ ?>
                    <?php if ($step === 2): ?>
                        <fieldset class="field">
                            <legend class="field__label">Waste Type</legend>
                            <p class="field__hint">Pilih semua kategori yang akan dijemput (bisa lebih dari satu).</p>

                            <div class="chips" data-pickup-types>
                                <?php foreach ($wasteTypes as $type): ?>
                                    <span class="chip-check">
                                        <input type="checkbox" name="types[]" value="<?= e($type['key']) ?>"
                                               id="type-<?= e($type['key']) ?>"
                                               <?= in_array($type['key'], (array) $data['types'], true) ? 'checked' : '' ?>>
                                        <label class="chip" for="type-<?= e($type['key']) ?>">
                                            <?php component('icon', ['name' => $type['icon']]); ?>
                                            <?= e($type['label']) ?>
                                            <span class="t-caption" style="opacity:.75"><?= e($type['rate']) ?>/kg</span>
                                        </label>
                                    </span>
                                <?php endforeach; ?>
                            </div>

                            <?php if ($err('types') !== ''): ?>
                                <p class="field__error">
                                    <?php component('icon', ['name' => 'alert']); ?><?= e($err('types')) ?>
                                </p>
                            <?php endif; ?>
                        </fieldset>

                        <div class="field">
                            <label class="field__label" for="weight">Estimasi berat total</label>
                            <span class="input-group">
                                <input class="input" type="number" id="weight" name="weight"
                                       inputmode="decimal" step="0.5" min="2" max="200"
                                       value="<?= e(old($data, 'weight')) ?>" placeholder="12.5"
                                       <?= $err('weight') !== '' ? 'aria-invalid="true" aria-describedby="err-weight"' : '' ?>>
                                <span class="input-group__suffix">KG</span>
                            </span>
                            <?php if ($err('weight') !== ''): ?>
                                <p class="field__error" id="err-weight">
                                    <?php component('icon', ['name' => 'alert']); ?><?= e($err('weight')) ?>
                                </p>
                            <?php else: ?>
                                <p class="field__hint">Minimal 2 kg. Perkiraan kasar sudah cukup — berat final ditentukan timbangan digital petugas.</p>
                            <?php endif; ?>
                        </div>

                        <fieldset class="field">
                            <legend class="field__label">Wadah penyimpanan</legend>
                            <div class="option-list option-list--inline">
                                <?php foreach ([
                                    'karung' => ['Karung / trash bag', 'Sudah terpilah per kategori'],
                                    'kardus' => ['Kardus terbuka', 'Ditumpuk di satu titik'],
                                    'campur' => ['Belum dipilah', 'Petugas bantu memilah di lokasi'],
                                ] as $key => [$label, $desc]): ?>
                                    <label class="option option--check">
                                        <input type="radio" name="container" value="<?= e($key) ?>"
                                               <?= old($data, 'container', 'karung') === $key ? 'checked' : '' ?>>
                                        <span class="option__mark" aria-hidden="true"></span>
                                        <span class="option__body">
                                            <span class="option__label"><?= e($label) ?></span>
                                            <span class="option__desc"><?= e($desc) ?></span>
                                        </span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </fieldset>
                    <?php endif; ?>

                    <?php /* ── STEP 3 · Schedule ─────────────────────────── */ ?>
                    <?php if ($step === 3): ?>
                        <div class="field">
                            <label class="field__label" for="date">Preferred Date</label>
                            <input class="input" type="date" id="date" name="date"
                                   value="<?= e(old($data, 'date')) ?>"
                                   min="<?= e($minDate) ?>" max="<?= e($maxDate) ?>"
                                   <?= $err('date') !== '' ? 'aria-invalid="true" aria-describedby="err-date"' : '' ?>>
                            <?php if ($err('date') !== ''): ?>
                                <p class="field__error" id="err-date">
                                    <?php component('icon', ['name' => 'alert']); ?><?= e($err('date')) ?>
                                </p>
                            <?php else: ?>
                                <p class="field__hint">Reservasi tersedia hari ini sampai 30 hari ke depan.</p>
                            <?php endif; ?>
                        </div>

                        <fieldset class="field">
                            <legend class="field__label">Slot waktu</legend>
                            <div class="grid grid--4" style="--gap: var(--sp-3)">
                                <?php foreach ($timeWindows as $win): ?>
                                    <label class="option option--compact">
                                        <input type="radio" name="window" value="<?= e($win['key']) ?>"
                                               <?= old($data, 'window') === $win['key'] ? 'checked' : '' ?>>
                                        <span class="option__mark" aria-hidden="true"></span>
                                        <span class="option__body">
                                            <span class="option__label"><?= e($win['label']) ?></span>
                                            <span class="option__desc"><?= e($win['desc']) ?></span>
                                        </span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                            <?php if ($err('window') !== ''): ?>
                                <p class="field__error">
                                    <?php component('icon', ['name' => 'alert']); ?><?= e($err('window')) ?>
                                </p>
                            <?php endif; ?>
                        </fieldset>

                        <div class="field">
                            <label class="field__label" for="notes">Catatan untuk petugas <span class="t-muted">(opsional)</span></label>
                            <textarea class="textarea" id="notes" name="notes" maxlength="300"
                                      placeholder="Contoh: hubungi dulu lewat WhatsApp, gerbang asrama terkunci setelah jam 21.00"
                                      <?= $err('notes') !== '' ? 'aria-invalid="true" aria-describedby="err-notes"' : '' ?>><?= e(old($data, 'notes')) ?></textarea>
                            <?php if ($err('notes') !== ''): ?>
                                <p class="field__error" id="err-notes">
                                    <?php component('icon', ['name' => 'alert']); ?><?= e($err('notes')) ?>
                                </p>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <?php /* ── STEP 4 · Review ───────────────────────────── */ ?>
                    <?php if ($step === 4): ?>
                        <h3 class="t-h4">Periksa kembali permintaanmu</h3>

                        <dl class="summary-list">
                            <div class="summary-list__row">
                                <dt class="summary-list__key">
                                    Lokasi penjemputan
                                    <a class="link-action" href="<?= e(url('/pickup') . '?step=1') ?>">Ubah</a>
                                </dt>
                                <dd class="summary-list__val">
                                    <?= e(\EcoLoop\Data\Pickup::locationLabel((string) $data['location'])) ?>
                                    <?php if ($data['location_detail'] !== ''): ?>
                                        <br><span class="t-small t-muted"><?= e($data['location_detail']) ?></span>
                                    <?php endif; ?>
                                </dd>
                            </div>
                            <div class="summary-list__row">
                                <dt class="summary-list__key">Kontak</dt>
                                <dd class="summary-list__val"><?= e($data['contact']) ?></dd>
                            </div>
                            <div class="summary-list__row">
                                <dt class="summary-list__key">
                                    Jenis sampah
                                    <a class="link-action" href="<?= e(url('/pickup') . '?step=2') ?>">Ubah</a>
                                </dt>
                                <dd class="summary-list__val">
                                    <?php foreach ((array) $data['types'] as $t): ?>
                                        <span class="pill pill--forest"><?= e(\EcoLoop\Data\Pickup::wasteTypeLabel((string) $t)) ?></span>
                                    <?php endforeach; ?>
                                </dd>
                            </div>
                            <div class="summary-list__row">
                                <dt class="summary-list__key">Estimasi berat</dt>
                                <dd class="summary-list__val"><?= e(fmt_kg((float) $data['weight'])) ?></dd>
                            </div>
                            <div class="summary-list__row">
                                <dt class="summary-list__key">
                                    Jadwal
                                    <a class="link-action" href="<?= e(url('/pickup') . '?step=3') ?>">Ubah</a>
                                </dt>
                                <dd class="summary-list__val">
                                    <?= e($data['date'] !== '' ? date('D, d M Y', strtotime((string) $data['date'])) : '—') ?><br>
                                    <span class="t-small t-muted"><?= e(\EcoLoop\Data\Pickup::timeWindowLabel((string) $data['window'])) ?></span>
                                </dd>
                            </div>
                            <?php if ($data['notes'] !== ''): ?>
                                <div class="summary-list__row">
                                    <dt class="summary-list__key">Catatan</dt>
                                    <dd class="summary-list__val"><?= e($data['notes']) ?></dd>
                                </div>
                            <?php endif; ?>
                        </dl>

                        <div class="estimate">
                            <span class="tile tile--leaf">
                                <?php component('icon', ['name' => 'coin', 'size' => 'lg']); ?>
                            </span>
                            <div class="u-grow">
                                <p class="t-small t-muted">Estimasi EcoPoints</p>
                                <p class="t-data-lg t-forest">+<?= e(number_format($estimate)) ?> pts</p>
                            </div>
                            <span class="pill pill--muted">setelah timbang digital</span>
                        </div>

                        <label class="option option--check">
                            <input type="checkbox" name="confirm" value="yes"
                                   <?= old($data, 'confirm') === 'yes' ? 'checked' : '' ?>
                                   <?= $err('confirm') !== '' ? 'aria-invalid="true"' : '' ?>>
                            <span class="option__mark" aria-hidden="true"></span>
                            <span class="option__body">
                                <span class="option__label">Saya siap di lokasi pada jadwal yang dipilih</span>
                                <span class="option__desc">
                                    Material sudah dikosongkan dan dibilas sesuai panduan Smart Sorting.
                                </span>
                            </span>
                        </label>
                        <?php if ($err('confirm') !== ''): ?>
                            <p class="field__error">
                                <?php component('icon', ['name' => 'alert']); ?><?= e($err('confirm')) ?>
                            </p>
                        <?php endif; ?>
                    <?php endif; ?>

                    <!-- Sticky action row -->
                    <footer class="wizard__actions">
                        <?php if ($step > 1): ?>
                            <button class="btn btn--secondary" type="submit" name="action" value="back" formnovalidate>
                                <?php component('icon', ['name' => 'chevron-left', 'size' => 'sm']); ?>Kembali
                            </button>
                        <?php else: ?>
                            <a class="btn btn--ghost" href="<?= e(url('/')) ?>">Batal</a>
                        <?php endif; ?>

                        <button class="btn btn--primary u-grow" type="submit" name="action" value="next">
                            <?php if ($step === 4): ?>
                                <?php component('icon', ['name' => 'check', 'size' => 'md']); ?>Schedule Pickup
                            <?php else: ?>
                                Lanjutkan<?php component('icon', ['name' => 'arrow-right', 'size' => 'sm']); ?>
                            <?php endif; ?>
                        </button>
                    </footer>
                </form>
            </article>

            <!-- Contextual rail -->
            <div class="u-stack">
                <article class="card card--dark">
                    <header class="card__head">
                        <span class="tile tile--on-dark">
                            <?php component('icon', ['name' => 'truck', 'size' => 'lg']); ?>
                        </span>
                        <div class="u-grow">
                            <h2 class="card__title">Layanan gratis</h2>
                            <p class="card__meta">Untuk mahasiswa &amp; himpunan</p>
                        </div>
                    </header>
                    <p class="t-body" style="color: var(--c-on-dark-2)">
                        Punya sampah daur ulang dalam jumlah besar? Pesan penjemputan langsung ke kos,
                        asrama, atau ruang himpunan tanpa repot mengangkut sendiri.
                    </p>
                    <ul class="u-stack-2">
                        <?php foreach ([
                            'Minimal 2 kg per penjemputan',
                            'Armada beroperasi Senin–Sabtu',
                            'Timbangan digital tersertifikasi',
                            'EcoPoints masuk otomatis',
                        ] as $point): ?>
                            <li class="u-row">
                                <?php component('icon', ['name' => 'check-circle', 'size' => 'sm', 'class' => 'u-shrink-0']); ?>
                                <span class="t-small" style="color: var(--c-on-dark-2)"><?= e($point) ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </article>

                <article class="card">
                    <header class="card__head">
                        <div class="u-grow">
                            <h2 class="card__title">Alur Layanan</h2>
                            <p class="card__meta">Enam tahap sampai poin masuk</p>
                        </div>
                    </header>

                    <ol class="timeline">
                        <?php foreach ($timeline as $i => $stage): ?>
                            <li class="<?= e(cx(['timeline__item', $i + 1 <= $step ? 'is-done' : ''])) ?>">
                                <span class="timeline__dot">
                                    <?php component('icon', ['name' => $stage['icon']]); ?>
                                </span>
                                <span class="timeline__body">
                                    <span class="timeline__title"><?= e($stage['no']) ?>. <?= e($stage['title']) ?></span>
                                    <span class="timeline__desc"><?= e($stage['desc']) ?></span>
                                </span>
                            </li>
                        <?php endforeach; ?>
                    </ol>
                </article>
            </div>
        </div>
    </section>

<?php endif; ?>
