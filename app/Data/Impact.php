<?php
/**
 * EcoLoop — Impact Metric data (carbon reduction dashboard).
 */

declare(strict_types=1);

namespace EcoLoop\Data;

final class Impact
{
    /**
     * Range tabs. Each range carries its own headline figures so the tabs can
     * swap values client-side without a request.
     *
     * @return array<string, array<string, mixed>>
     */
    public static function ranges(): array
    {
        return [
            'week' => [
                'label'    => 'This Week',
                'co2'      => 4.2,
                'goal'     => 6.0,
                'diverted' => 2.9,
                'trees'    => 0.3,
                'water'    => 118,
                'energy'   => 9.4,
                'points'   => 152,
                'delta_co2'      => '+0.8 kg',
                'delta_diverted' => '+1.4 kg',
                'delta_note'     => 'vs minggu lalu',
            ],
            'month' => [
                'label'    => 'This Month',
                'co2'      => 18.6,
                'goal'     => 25.0,
                'diverted' => 124.3,
                'trees'    => 1.2,
                'water'    => 486,
                'energy'   => 38.7,
                'points'   => 610,
                'delta_co2'      => '+4.2 kg',
                'delta_diverted' => '+18.6 kg',
                'delta_note'     => 'vs bulan lalu',
            ],
            'all' => [
                'label'    => 'All Time',
                'co2'      => 96.4,
                'goal'     => 120.0,
                'diverted' => 512.8,
                'trees'    => 6.4,
                'water'    => 2540,
                'energy'   => 201.5,
                'points'   => 2450,
                'delta_co2'      => '+21.7 kg',
                'delta_diverted' => '+96.4 kg',
                'delta_note'     => 'kuartal terakhir',
            ],
        ];
    }

    public static function defaultRange(): string
    {
        return 'month';
    }

    /**
     * CO₂ trend series per range — 7 points each, drawn as an inline SVG chart.
     *
     * @return array<string, array{labels:array<int,string>,values:array<int,float>}>
     */
    public static function series(): array
    {
        return [
            'week' => [
                'labels' => ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'],
                'values' => [0.4, 0.9, 0.6, 0.5, 1.1, 0.3, 0.4],
            ],
            'month' => [
                'labels' => ['W1', 'W2', 'W3', 'W4', 'W5', 'W6', 'W7'],
                'values' => [1.8, 2.4, 2.1, 3.2, 2.8, 3.6, 2.7],
            ],
            'all' => [
                'labels' => ['Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep'],
                'values' => [6.2, 9.4, 11.8, 13.1, 15.6, 21.7, 18.6],
            ],
        ];
    }

    /**
     * How much each material contributes to the CO₂ saving.
     *
     * @return array<int, array<string, mixed>>
     */
    public static function breakdown(): array
    {
        return [
            [
                'label' => 'Botol Plastik (PET)',
                'icon'  => 'bottle',
                'tone'  => 'info',
                'co2'   => 9.8,
                'share' => 52.7,
                'note'  => '1.5 kg CO₂e per kg PET',
            ],
            [
                'label' => 'Kertas & Kardus',
                'icon'  => 'paper',
                'tone'  => 'warning',
                'co2'   => 5.4,
                'share' => 29.0,
                'note'  => '1.3 kg CO₂e per kg kertas',
            ],
            [
                'label' => 'Kaleng & Logam',
                'icon'  => 'can',
                'tone'  => 'sage',
                'co2'   => 3.4,
                'share' => 18.3,
                'note'  => '2.0 kg CO₂e per kg aluminium',
            ],
        ];
    }

    /**
     * Real-world equivalences for the hero panel.
     *
     * @return array<int, array{icon:string,value:string,label:string}>
     */
    public static function equivalences(): array
    {
        return [
            ['icon' => 'tree',   'value' => '1.2 pohon', 'label' => 'setara diserap setahun'],
            ['icon' => 'drop',   'value' => '486 liter', 'label' => 'air bersih dihemat'],
            ['icon' => 'bolt',   'value' => '38.7 kWh',  'label' => 'energi tidak terpakai'],
        ];
    }

    /**
     * Milestones toward the personal impact ladder.
     *
     * @return array<int, array{label:string,target:string,progress:float,done:bool}>
     */
    public static function milestones(): array
    {
        return [
            ['label' => 'Reduksi 5 kg CO₂',   'target' => '5 kg',   'progress' => 100.0, 'done' => true],
            ['label' => 'Reduksi 10 kg CO₂',  'target' => '10 kg',  'progress' => 100.0, 'done' => true],
            ['label' => 'Reduksi 25 kg CO₂',  'target' => '25 kg',  'progress' => 74.4,  'done' => false],
            ['label' => 'Reduksi 50 kg CO₂',  'target' => '50 kg',  'progress' => 37.2,  'done' => false],
        ];
    }
}
