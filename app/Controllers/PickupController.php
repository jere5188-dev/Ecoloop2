<?php
/**
 * EcoLoop — Waste Pickup multi-step wizard.
 *
 * A server-driven state machine. Every step is a real PHP POST, state lives in
 * the session, and validation runs on the server, so the whole flow works with
 * JavaScript disabled (requirements R4.1–R4.10).
 */

declare(strict_types=1);

namespace EcoLoop\Controllers;

use EcoLoop\Data\Pickup;
use EcoLoop\Data\User;
use EcoLoop\View;

final class PickupController
{
    private const SESSION_KEY = 'pickup';
    private const DONE_KEY = 'pickup_done';
    private const LAST_STEP = 4;

    /* --------------------------------------------------------------------- */
    /* Session state                                                         */
    /* --------------------------------------------------------------------- */

    /** @return array{step:int,data:array<string,mixed>,errors:array<string,string>} */
    private function state(): array
    {
        $state = $_SESSION[self::SESSION_KEY] ?? null;
        if (!is_array($state)) {
            $state = ['step' => 1, 'data' => $this->defaults(), 'errors' => []];
            $_SESSION[self::SESSION_KEY] = $state;
        }
        $state['step'] = max(1, min(self::LAST_STEP, (int) ($state['step'] ?? 1)));
        $state['data'] = is_array($state['data'] ?? null) ? $state['data'] : $this->defaults();
        $state['errors'] = is_array($state['errors'] ?? null) ? $state['errors'] : [];

        return $state;
    }

    /** @param array{step:int,data:array<string,mixed>,errors:array<string,string>} $state */
    private function save(array $state): void
    {
        $_SESSION[self::SESSION_KEY] = $state;
    }

    /** @return array<string, mixed> */
    private function defaults(): array
    {
        return [
            'location'        => '',
            'location_detail' => '',
            'contact'         => '',
            'types'           => [],
            'weight'          => '',
            'container'       => 'karung',
            'date'            => '',
            'window'          => '',
            'notes'           => '',
            'confirm'         => '',
        ];
    }

    /* --------------------------------------------------------------------- */
    /* GET                                                                   */
    /* --------------------------------------------------------------------- */

    public function show(): void
    {
        // Confirmation screen (POST-redirect-GET target).
        if (($_GET['step'] ?? '') === 'done' && !empty($_SESSION[self::DONE_KEY])) {
            $this->renderDone($_SESSION[self::DONE_KEY]);

            return;
        }

        $state = $this->state();

        // Allow stepping back to an already-completed step via a plain link.
        if (isset($_GET['step']) && ctype_digit((string) $_GET['step'])) {
            $requested = (int) $_GET['step'];
            if ($requested >= 1 && $requested <= $state['step']) {
                $state['step'] = $requested;
                $state['errors'] = [];
                $this->save($state);
            }
        }

        $this->renderStep($state);
    }

    public function reset(): void
    {
        unset($_SESSION[self::SESSION_KEY], $_SESSION[self::DONE_KEY]);
        redirect('/pickup');
    }

    /* --------------------------------------------------------------------- */
    /* POST                                                                  */
    /* --------------------------------------------------------------------- */

    public function submit(): void
    {
        $state = $this->state();

        if (!csrf_valid($_POST['_csrf'] ?? null)) {
            $state['errors'] = ['form' => 'Sesi telah kedaluwarsa. Silakan kirim ulang formulir.'];
            $this->save($state);
            $this->renderStep($state);

            return;
        }

        $step = (int) ($_POST['step'] ?? $state['step']);
        $step = max(1, min(self::LAST_STEP, $step));
        $action = (string) ($_POST['action'] ?? 'next');

        // Merge this step's fields into the persisted data before anything else,
        // so going back never loses input (requirement R4.9).
        $state['data'] = array_merge($state['data'], $this->collect($step));

        if ($action === 'back') {
            $state['step'] = max(1, $step - 1);
            $state['errors'] = [];
            $this->save($state);
            $this->renderStep($state);

            return;
        }

        if ($action === 'edit') {
            $target = (int) ($_POST['target'] ?? 1);
            $state['step'] = max(1, min(self::LAST_STEP, $target));
            $state['errors'] = [];
            $this->save($state);
            $this->renderStep($state);

            return;
        }

        $errors = $this->validate($step, $state['data']);
        if ($errors) {
            $state['step'] = $step;
            $state['errors'] = $errors;
            $this->save($state);
            $this->renderStep($state);

            return;
        }

        if ($step === self::LAST_STEP) {
            $this->complete($state['data']);

            return;
        }

        $state['step'] = $step + 1;
        $state['errors'] = [];
        $this->save($state);
        $this->renderStep($state);
    }

    /* --------------------------------------------------------------------- */
    /* Collection & validation                                               */
    /* --------------------------------------------------------------------- */

    /** @return array<string, mixed> */
    private function collect(int $step): array
    {
        $str = static fn (string $k): string => trim((string) ($_POST[$k] ?? ''));

        return match ($step) {
            1 => [
                'location'        => $str('location'),
                'location_detail' => $str('location_detail'),
                'contact'         => $str('contact'),
            ],
            2 => [
                'types'     => array_values(array_filter(
                    (array) ($_POST['types'] ?? []),
                    static fn ($t) => in_array($t, array_column(Pickup::wasteTypes(), 'key'), true)
                )),
                'weight'    => $str('weight'),
                'container' => $str('container') !== '' ? $str('container') : 'karung',
            ],
            3 => [
                'date'   => $str('date'),
                'window' => $str('window'),
                'notes'  => $str('notes'),
            ],
            4 => [
                'confirm' => $str('confirm'),
            ],
            default => [],
        };
    }

    /**
     * @param array<string, mixed> $data
     *
     * @return array<string, string> field => message
     */
    private function validate(int $step, array $data): array
    {
        $errors = [];

        if ($step === 1) {
            $valid = array_column(Pickup::locations(), 'key');
            if ($data['location'] === '' || !in_array($data['location'], $valid, true)) {
                $errors['location'] = 'Pilih salah satu lokasi penjemputan.';
            }
            $detail = (string) $data['location_detail'];
            if ($data['location'] === 'other' && mb_strlen($detail) < 5) {
                $errors['location_detail'] = 'Tuliskan titik temu minimal 5 karakter.';
            } elseif ($detail !== '' && mb_strlen($detail) > 160) {
                $errors['location_detail'] = 'Detail lokasi maksimal 160 karakter.';
            }
            $contact = (string) $data['contact'];
            if ($contact === '') {
                $errors['contact'] = 'Nomor WhatsApp diperlukan agar petugas bisa menghubungi kamu.';
            } elseif (!preg_match('/^[0-9+\-\s]{9,20}$/', $contact)) {
                $errors['contact'] = 'Format nomor tidak valid (9–20 digit).';
            }
        }

        if ($step === 2) {
            if (!$data['types']) {
                $errors['types'] = 'Pilih minimal satu jenis sampah.';
            }
            $weight = (string) $data['weight'];
            if ($weight === '') {
                $errors['weight'] = 'Masukkan estimasi berat total.';
            } elseif (!is_numeric($weight)) {
                $errors['weight'] = 'Estimasi berat harus berupa angka.';
            } elseif ((float) $weight < 2) {
                $errors['weight'] = 'Layanan pickup mulai dari 2 kg. Gunakan drop-off box untuk jumlah kecil.';
            } elseif ((float) $weight > 200) {
                $errors['weight'] = 'Untuk di atas 200 kg, hubungi tim EcoLoop langsung.';
            }
        }

        if ($step === 3) {
            $date = (string) $data['date'];
            if ($date === '') {
                $errors['date'] = 'Pilih tanggal penjemputan.';
            } else {
                $parsed = \DateTimeImmutable::createFromFormat('!Y-m-d', $date);
                $today = new \DateTimeImmutable('today');
                if (!$parsed) {
                    $errors['date'] = 'Format tanggal tidak dikenali.';
                } elseif ($parsed < $today) {
                    $errors['date'] = 'Tanggal tidak boleh di masa lalu.';
                } elseif ($parsed > $today->modify('+30 days')) {
                    $errors['date'] = 'Reservasi maksimal 30 hari ke depan.';
                }
            }
            $windows = array_column(Pickup::timeWindows(), 'key');
            if ($data['window'] === '' || !in_array($data['window'], $windows, true)) {
                $errors['window'] = 'Pilih slot waktu penjemputan.';
            }
            if (mb_strlen((string) $data['notes']) > 300) {
                $errors['notes'] = 'Catatan maksimal 300 karakter.';
            }
        }

        if ($step === 4 && $data['confirm'] !== 'yes') {
            $errors['confirm'] = 'Centang konfirmasi untuk mengirim permintaan.';
        }

        return $errors;
    }

    /* --------------------------------------------------------------------- */
    /* Completion                                                            */
    /* --------------------------------------------------------------------- */

    /** @param array<string, mixed> $data */
    private function complete(array $data): void
    {
        $weight = (float) $data['weight'];
        $types = (array) $data['types'];

        $_SESSION[self::DONE_KEY] = [
            'request_id' => sprintf('ECO-%s-%04d', date('ymd'), random_int(1, 9999)),
            'data'       => $data,
            'points'     => Pickup::estimatePoints($weight, $types),
            'weight'     => $weight,
            'created_at' => date('d M Y, H:i'),
        ];
        unset($_SESSION[self::SESSION_KEY]);

        redirect('/pickup?step=done');
    }

    /* --------------------------------------------------------------------- */
    /* Rendering                                                             */
    /* --------------------------------------------------------------------- */

    /** @param array{step:int,data:array<string,mixed>,errors:array<string,string>} $state */
    private function renderStep(array $state): void
    {
        $data = $state['data'];
        $weight = is_numeric($data['weight']) ? (float) $data['weight'] : 0.0;

        View::render('pickup', [
            'step'        => $state['step'],
            'data'        => $data,
            'errors'      => $state['errors'],
            'steps'       => Pickup::STEPS,
            'locations'   => Pickup::locations(),
            'wasteTypes'  => Pickup::wasteTypes(),
            'timeWindows' => Pickup::timeWindows(),
            'timeline'    => Pickup::timeline(),
            'estimate'    => Pickup::estimatePoints($weight, (array) $data['types']),
            'user'        => User::current(),
            'done'        => null,
            'minDate'     => date('Y-m-d'),
            'maxDate'     => date('Y-m-d', strtotime('+30 days')),
        ], [
            'title'     => 'Waste Pickup',
            'eyebrow'   => 'Collect',
            'heading'   => 'Request Waste Pickup',
            'subtitle'  => 'Jadwalkan penjemputan sampah daur ulang volume besar.',
            'bodyClass' => 'page-pickup',
        ]);
    }

    /** @param array<string, mixed> $done */
    private function renderDone(array $done): void
    {
        View::render('pickup', [
            'step'        => 5,
            'data'        => $done['data'],
            'errors'      => [],
            'steps'       => Pickup::STEPS,
            'locations'   => Pickup::locations(),
            'wasteTypes'  => Pickup::wasteTypes(),
            'timeWindows' => Pickup::timeWindows(),
            'timeline'    => Pickup::timeline(),
            'estimate'    => (int) $done['points'],
            'user'        => User::current(),
            'done'        => $done,
            'minDate'     => date('Y-m-d'),
            'maxDate'     => date('Y-m-d', strtotime('+30 days')),
        ], [
            'title'     => 'Pickup Terjadwal',
            'eyebrow'   => 'Collect',
            'heading'   => 'Permintaan Pickup Terkirim',
            'subtitle'  => 'Tim armada EcoLoop akan datang sesuai jadwal reservasi.',
            'bodyClass' => 'page-pickup page-pickup--done',
        ]);
    }
}
