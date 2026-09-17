<?php
/**
 * EcoLoop — EcoPoints, badges, streaks and the Campus Competition Challenge.
 */

declare(strict_types=1);

namespace EcoLoop\Data;

final class Gamify
{
    /**
     * The 500 KG monthly Campus Competition Challenge (requirement R5.5).
     *
     * @return array<string, mixed>
     */
    public static function competition(): array
    {
        $current = 320.0;
        $target = 500.0;

        return [
            'title'     => 'Green Campus Challenge',
            'subtitle'  => 'Kumpulkan 500 KG sampah daur ulang bulan ini',
            'period'    => 'September 2026',
            'current'   => $current,
            'target'    => $target,
            'progress'  => round($current / $target * 100, 1),
            'days_left' => 13,
            'teams'     => 8,
            'reward'    => 'Sertifikat Green Campus Student + voucher kantin Rp 250.000',
        ];
    }

    /**
     * Faculty leaderboard for the competition.
     *
     * @return array<int, array{rank:int,team:string,short:string,kg:float,progress:float,you:bool}>
     */
    public static function leaderboard(): array
    {
        $target = self::competition()['target'];
        $rows = [
            ['rank' => 1, 'team' => 'Fakultas Teknik',            'short' => 'FT',  'kg' => 128.4, 'you' => true],
            ['rank' => 2, 'team' => 'Fakultas MIPA',              'short' => 'MIPA', 'kg' => 96.2, 'you' => false],
            ['rank' => 3, 'team' => 'Fakultas Ekonomi & Bisnis',  'short' => 'FEB', 'kg' => 61.7, 'you' => false],
            ['rank' => 4, 'team' => 'Fakultas Ilmu Budaya',       'short' => 'FIB', 'kg' => 21.9, 'you' => false],
            ['rank' => 5, 'team' => 'Fakultas Hukum',             'short' => 'FH',  'kg' => 11.8, 'you' => false],
        ];

        foreach ($rows as &$row) {
            $row['progress'] = round($row['kg'] / $target * 100, 1);
        }

        return $rows;
    }

    /**
     * Achievement badges (requirement R5.3 / R5.4).
     *
     * @return array<int, array<string, mixed>>
     */
    public static function badges(): array
    {
        return [
            [
                'key'      => 'first-sort',
                'title'    => 'First Sort',
                'req'      => 'Sort 10 items',
                'icon'     => 'sprout',
                'unlocked' => true,
                'progress' => 100.0,
                'earned'   => 'Diraih 2 Mar 2026',
            ],
            [
                'key'      => 'eco-champion',
                'title'    => 'Eco Champion',
                'req'      => '1,000 pts',
                'icon'     => 'medal',
                'unlocked' => true,
                'progress' => 100.0,
                'earned'   => 'Diraih 18 Jun 2026',
            ],
            [
                'key'      => 'recycling-hero',
                'title'    => 'Recycling Hero',
                'req'      => '50 kg terdaur ulang',
                'icon'     => 'recycle',
                'unlocked' => false,
                'progress' => 24.8,
                'earned'   => '12.4 / 50 kg',
            ],
            [
                'key'      => 'campus-leader',
                'title'    => 'Campus Leader',
                'req'      => 'Masuk Top 3 kampus',
                'icon'     => 'trophy',
                'unlocked' => false,
                'progress' => 60.0,
                'earned'   => 'Peringkat #18 dari 1,240',
            ],
        ];
    }

    /**
     * Point-earning actions (requirement R5.7).
     *
     * @return array<int, array{label:string,desc:string,points:int,icon:string,tone:string}>
     */
    public static function actions(): array
    {
        return [
            [
                'label'  => 'Smart Scan',
                'desc'   => 'Sortir sampah dengan verifikasi AI kamera.',
                'points' => 100,
                'icon'   => 'scan',
                'tone'   => 'forest',
            ],
            [
                'label'  => 'Weekly Mission',
                'desc'   => 'Selesaikan misi berkelanjutan mingguan.',
                'points' => 50,
                'icon'   => 'target',
                'tone'   => 'info',
            ],
            [
                'label'  => 'Drop-off Box',
                'desc'   => 'Gunakan drop box resmi di lingkungan kampus.',
                'points' => 30,
                'icon'   => 'map',
                'tone'   => 'sage',
            ],
            [
                'label'  => 'Recycle 1 KG',
                'desc'   => 'Timbang dan setor sampah bernilai daur ulang.',
                'points' => 20,
                'icon'   => 'weight',
                'tone'   => 'leaf',
            ],
        ];
    }

    /**
     * Redeemable rewards catalog (requirement R5.8).
     *
     * @return array<int, array<string, mixed>>
     */
    public static function rewards(): array
    {
        return [
            [
                'label'  => 'Voucher Kantin',
                'desc'   => 'Diskon Rp 25.000 di seluruh tenant kantin kampus.',
                'cost'   => 800,
                'icon'   => 'voucher',
                'tone'   => 'forest',
                'stock'  => 'Tersisa 42',
            ],
            [
                'label'  => 'Eco Merchandise',
                'desc'   => 'Pilih T-shirt katun organik atau tote bag EcoLoop.',
                'cost'   => 2000,
                'icon'   => 'shirt',
                'tone'   => 'leaf',
                'stock'  => 'Tersisa 18',
            ],
            [
                'label'  => 'Tumbler Stainless',
                'desc'   => 'Tumbler 500 ml, gratis isi ulang di semua water station.',
                'cost'   => 3200,
                'icon'   => 'bottle',
                'tone'   => 'info',
                'stock'  => 'Tersisa 9',
            ],
            [
                'label'  => 'Voucher Cetak Tugas',
                'desc'   => 'Diskon 40% fotokopi dan cetak di koperasi mahasiswa.',
                'cost'   => 600,
                'icon'   => 'paper',
                'tone'   => 'warning',
                'stock'  => 'Tersisa 75',
            ],
        ];
    }

    /**
     * Academic / social incentives from the deck.
     *
     * @return array<int, array{label:string,desc:string,icon:string}>
     */
    public static function incentives(): array
    {
        return [
            [
                'label' => 'Sertifikat Green Campus Student',
                'desc'  => 'Diterbitkan tiap semester bagi 100 kontributor teratas.',
                'icon'  => 'certificate',
            ],
            [
                'label' => 'Konversi jam pengabdian',
                'desc'  => '500 pts setara 1 jam pengabdian masyarakat / SKPI.',
                'icon'  => 'clock',
            ],
        ];
    }

    /**
     * Weekly missions.
     *
     * @return array<int, array{label:string,progress:float,current:int,target:int,points:int,done:bool}>
     */
    public static function missions(): array
    {
        return [
            [
                'label'    => 'Scan 5 kemasan berbeda',
                'current'  => 5,
                'target'   => 5,
                'progress' => 100.0,
                'points'   => 50,
                'done'     => true,
            ],
            [
                'label'    => 'Setor 3 kg sampah daur ulang',
                'current'  => 2,
                'target'   => 3,
                'progress' => 66.7,
                'points'   => 60,
                'done'     => false,
            ],
            [
                'label'    => 'Gunakan 2 drop-off point berbeda',
                'current'  => 1,
                'target'   => 2,
                'progress' => 50.0,
                'points'   => 40,
                'done'     => false,
            ],
        ];
    }
}
