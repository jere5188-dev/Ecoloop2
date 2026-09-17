<?php
/**
 * EcoLoop — Waste Passbook data (digital ledger).
 *
 * Figures mirror the pitch deck: 12.4 KG recycled, +450 EcoPoints across three
 * verified categories.
 */

declare(strict_types=1);

namespace EcoLoop\Data;

final class Waste
{
    /**
     * Filter chips for the ledger.
     *
     * @return array<int, array{key:string,label:string}>
     */
    public static function filters(): array
    {
        return [
            ['key' => 'all',       'label' => 'All'],
            ['key' => 'pet',       'label' => 'PET'],
            ['key' => 'paper',     'label' => 'Paper'],
            ['key' => 'cardboard', 'label' => 'Cardboard'],
            ['key' => 'cans',      'label' => 'Cans'],
        ];
    }

    /**
     * Ledger rows. `proportion` is pre-computed against the 12.4 KG total.
     *
     * @return array<int, array<string, mixed>>
     */
    public static function ledger(): array
    {
        return [
            [
                'key'        => 'pet',
                'label'      => 'Botol Plastik (PET)',
                'sub'        => 'Polyethylene Terephthalate',
                'icon'       => 'bottle',
                'tone'       => 'info',
                'kg'         => 6.5,
                'proportion' => 52.4,
                'points'     => 260,
                'rate'       => 40,
                'status'     => 'Terverifikasi',
                'status_key' => 'verified',
                'delta'      => 2.3,
                'co2'        => 9.8,
            ],
            [
                'key'        => 'paper',
                'label'      => 'Kertas & Kardus',
                'sub'        => 'Kertas HVS, buku, karton',
                'icon'       => 'paper',
                'tone'       => 'warning',
                'kg'         => 4.2,
                'proportion' => 33.9,
                'points'     => 125,
                'rate'       => 30,
                'status'     => 'Terverifikasi',
                'status_key' => 'verified',
                'delta'      => 1.1,
                'co2'        => 5.4,
            ],
            [
                'key'        => 'cans',
                'label'      => 'Kaleng & Logam',
                'sub'        => 'Aluminium, kaleng minuman',
                'icon'       => 'can',
                'tone'       => 'sage',
                'kg'         => 1.7,
                'proportion' => 13.7,
                'points'     => 65,
                'rate'       => 38,
                'status'     => 'Terverifikasi',
                'status_key' => 'verified',
                'delta'      => 0.6,
                'co2'        => 3.4,
            ],
            [
                'key'        => 'cardboard',
                'label'      => 'Kardus Logistik',
                'sub'        => 'Menunggu penimbangan digital',
                'icon'       => 'cardboard',
                'tone'       => 'sage',
                'kg'         => 0.0,
                'proportion' => 0.0,
                'points'     => 0,
                'rate'       => 30,
                'status'     => 'Menunggu',
                'status_key' => 'pending',
                'delta'      => 0.0,
                'co2'        => 0.0,
            ],
        ];
    }

    /** Aggregate totals for the summary strip. @return array<string, mixed> */
    public static function totals(): array
    {
        $rows = self::ledger();

        return [
            'kg'         => array_sum(array_column($rows, 'kg')),
            'points'     => array_sum(array_column($rows, 'points')),
            'categories' => count(array_filter($rows, static fn ($r) => $r['kg'] > 0)),
            'deposits'   => 24,
            'delta_kg'   => array_sum(array_column($rows, 'delta')),
        ];
    }

    /**
     * Condensed passbook rows for the dashboard card (styleguide 04).
     *
     * @return array<int, array<string, mixed>>
     */
    public static function summaryRows(): array
    {
        return array_values(array_filter(self::ledger(), static fn ($r) => $r['kg'] > 0));
    }

    /**
     * Recent deposit history.
     *
     * @return array<int, array<string, mixed>>
     */
    public static function transactions(): array
    {
        return [
            [
                'date'     => '17 Sep 2026',
                'time'     => '14:20',
                'title'    => 'Drop-off Box — Kantin Teknik',
                'category' => 'Botol Plastik (PET)',
                'icon'     => 'bottle',
                'tone'     => 'info',
                'kg'       => 1.4,
                'points'   => 56,
                'method'   => 'Drop-off',
                'status'   => 'Terverifikasi',
                'status_key' => 'verified',
            ],
            [
                'date'     => '16 Sep 2026',
                'time'     => '09:05',
                'title'    => 'Smart Scan — Ruang Himpunan',
                'category' => 'Kaleng & Logam',
                'icon'     => 'can',
                'tone'     => 'sage',
                'kg'       => 0.6,
                'points'   => 100,
                'method'   => 'Smart Scan',
                'status'   => 'Terverifikasi',
                'status_key' => 'verified',
            ],
            [
                'date'     => '15 Sep 2026',
                'time'     => '16:45',
                'title'    => 'Pickup Terjadwal — Asrama Dahlia',
                'category' => 'Kertas & Kardus',
                'icon'     => 'paper',
                'tone'     => 'warning',
                'kg'       => 3.2,
                'points'   => 96,
                'method'   => 'Pickup',
                'status'   => 'Terverifikasi',
                'status_key' => 'verified',
            ],
            [
                'date'     => '14 Sep 2026',
                'time'     => '11:30',
                'title'    => 'Drop-off Box — Perpustakaan Pusat',
                'category' => 'Botol Plastik (PET)',
                'icon'     => 'bottle',
                'tone'     => 'info',
                'kg'       => 0.9,
                'points'   => 36,
                'method'   => 'Drop-off',
                'status'   => 'Terverifikasi',
                'status_key' => 'verified',
            ],
            [
                'date'     => '13 Sep 2026',
                'time'     => '08:15',
                'title'    => 'Pickup Terjadwal — Kos Kenanga',
                'category' => 'Kardus Logistik',
                'icon'     => 'cardboard',
                'tone'     => 'sage',
                'kg'       => 2.1,
                'points'   => 0,
                'method'   => 'Pickup',
                'status'   => 'Menunggu',
                'status_key' => 'pending',
            ],
        ];
    }
}
