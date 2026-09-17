<?php
/**
 * EcoLoop — Waste Pickup wizard option sets and validation rules.
 */

declare(strict_types=1);

namespace EcoLoop\Data;

final class Pickup
{
    public const STEPS = [
        1 => ['key' => 'location', 'label' => 'Lokasi',     'en' => 'Location'],
        2 => ['key' => 'waste',    'label' => 'Jenis',      'en' => 'Waste Type'],
        3 => ['key' => 'schedule', 'label' => 'Jadwal',     'en' => 'Schedule'],
        4 => ['key' => 'review',   'label' => 'Konfirmasi', 'en' => 'Review'],
    ];

    /**
     * Step 1 — pickup locations.
     *
     * @return array<int, array{key:string,label:string,desc:string,icon:string}>
     */
    public static function locations(): array
    {
        return [
            [
                'key'   => 'dormitory',
                'label' => 'Asrama / Kos',
                'desc'  => 'Contoh: Asrama Dahlia D2, Kos Kenanga',
                'icon'  => 'home',
            ],
            [
                'key'   => 'faculty',
                'label' => 'Gedung Fakultas',
                'desc'  => 'Lobby atau area parkir gedung kuliah',
                'icon'  => 'building',
            ],
            [
                'key'   => 'association',
                'label' => 'Ruang Himpunan Mahasiswa',
                'desc'  => 'Sekretariat organisasi atau UKM',
                'icon'  => 'users',
            ],
            [
                'key'   => 'other',
                'label' => 'Lokasi Lain',
                'desc'  => 'Tulis titik temu secara spesifik',
                'icon'  => 'location',
            ],
        ];
    }

    /**
     * Step 2 — waste types (multi-select chips) with their point rates.
     *
     * @return array<int, array{key:string,label:string,icon:string,tone:string,rate:int}>
     */
    public static function wasteTypes(): array
    {
        return [
            ['key' => 'pet',       'label' => 'PET',       'icon' => 'bottle',    'tone' => 'info',    'rate' => 40],
            ['key' => 'paper',     'label' => 'Paper',     'icon' => 'paper',     'tone' => 'warning', 'rate' => 30],
            ['key' => 'cardboard', 'label' => 'Cardboard', 'icon' => 'cardboard', 'tone' => 'sage',    'rate' => 30],
            ['key' => 'cans',      'label' => 'Cans',      'icon' => 'can',       'tone' => 'sage',    'rate' => 38],
            ['key' => 'mixed',     'label' => 'Mixed',     'icon' => 'recycle',   'tone' => 'forest',  'rate' => 20],
        ];
    }

    /**
     * Step 3 — collection time windows.
     *
     * @return array<int, array{key:string,label:string,desc:string}>
     */
    public static function timeWindows(): array
    {
        return [
            ['key' => '08-10', 'label' => '08.00 – 10.00', 'desc' => 'Pagi'],
            ['key' => '10-12', 'label' => '10.00 – 12.00', 'desc' => 'Menjelang siang'],
            ['key' => '13-15', 'label' => '13.00 – 15.00', 'desc' => 'Siang'],
            ['key' => '15-17', 'label' => '15.00 – 17.00', 'desc' => 'Sore'],
        ];
    }

    /**
     * The 6-stage service flow shown on the confirmation screen.
     *
     * @return array<int, array{no:int,title:string,desc:string,icon:string}>
     */
    public static function timeline(): array
    {
        return [
            ['no' => 1, 'title' => 'Request Pickup',  'desc' => 'Permintaan tercatat di sistem EcoLoop.',            'icon' => 'clipboard'],
            ['no' => 2, 'title' => 'Pilih Jenis',     'desc' => 'Kategori material terverifikasi petugas.',          'icon' => 'recycle'],
            ['no' => 3, 'title' => 'Jadwal & Lokasi', 'desc' => 'Titik temu dan slot waktu dikonfirmasi.',           'icon' => 'calendar'],
            ['no' => 4, 'title' => 'Tim Menjemput',   'desc' => 'Armada datang sesuai jadwal reservasi.',            'icon' => 'truck'],
            ['no' => 5, 'title' => 'Timbang Digital', 'desc' => 'Penimbangan dan pemeriksaan kualitas material.',    'icon' => 'weight'],
            ['no' => 6, 'title' => 'Poin Masuk',      'desc' => 'EcoPoints terakumulasi ke dompet aplikasi.',        'icon' => 'coin'],
        ];
    }

    /** Estimated points for a given weight and set of selected types. */
    public static function estimatePoints(float $kg, array $types): int
    {
        $rates = array_column(self::wasteTypes(), 'rate', 'key');
        $selected = array_intersect_key($rates, array_flip($types));
        if (!$selected) {
            return 0;
        }
        $avg = array_sum($selected) / count($selected);

        return (int) round($kg * $avg);
    }

    public static function locationLabel(string $key): string
    {
        foreach (self::locations() as $loc) {
            if ($loc['key'] === $key) {
                return $loc['label'];
            }
        }

        return $key;
    }

    public static function wasteTypeLabel(string $key): string
    {
        foreach (self::wasteTypes() as $type) {
            if ($type['key'] === $key) {
                return $type['label'];
            }
        }

        return $key;
    }

    public static function timeWindowLabel(string $key): string
    {
        foreach (self::timeWindows() as $win) {
            if ($win['key'] === $key) {
                return $win['label'] . ' (' . $win['desc'] . ')';
            }
        }

        return $key;
    }
}
