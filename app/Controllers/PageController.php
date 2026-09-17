<?php
/**
 * EcoLoop — read-only page controllers.
 *
 * Each method assembles the data a page needs from the data layer and hands it
 * to the view, keeping template files free of literals (requirement R7.5).
 */

declare(strict_types=1);

namespace EcoLoop\Controllers;

use EcoLoop\Data\Gamify;
use EcoLoop\Data\Impact;
use EcoLoop\Data\Nav;
use EcoLoop\Data\Sorting;
use EcoLoop\Data\User;
use EcoLoop\Data\Waste;
use EcoLoop\View;

final class PageController
{
    public function dashboard(): void
    {
        $ranges = Impact::ranges();

        View::render('dashboard', [
            'user'         => User::current(),
            'totals'       => Waste::totals(),
            'summaryRows'  => Waste::summaryRows(),
            'impact'       => $ranges[Impact::defaultRange()],
            'competition'  => Gamify::competition(),
            'streak'       => User::streak(),
            'quickActions' => Nav::quickActions(),
            'missions'     => Gamify::missions(),
            'transactions' => array_slice(Waste::transactions(), 0, 3),
        ], [
            'title'    => 'Dashboard',
            'eyebrow'  => 'Beranda',
            'heading'  => 'Dashboard',
            'subtitle' => 'Ringkasan kontribusi daur ulang kamu hari ini.',
            'bodyClass' => 'page-dashboard',
        ]);
    }

    public function passbook(): void
    {
        View::render('passbook', [
            'user'         => User::current(),
            'filters'      => Waste::filters(),
            'ledger'       => Waste::ledger(),
            'totals'       => Waste::totals(),
            'transactions' => Waste::transactions(),
        ], [
            'title'    => 'Waste Passbook',
            'eyebrow'  => 'Track',
            'heading'  => 'Waste Passbook',
            'subtitle' => 'Buku tabungan digital setiap kilogram sampah yang kamu setor.',
            'bodyClass' => 'page-passbook',
        ]);
    }

    public function impact(): void
    {
        $ranges = Impact::ranges();
        $default = Impact::defaultRange();

        View::render('impact', [
            'user'          => User::current(),
            'ranges'        => $ranges,
            'activeRange'   => $default,
            'impact'        => $ranges[$default],
            'series'        => Impact::series(),
            'breakdown'     => Impact::breakdown(),
            'equivalences'  => Impact::equivalences(),
            'milestones'    => Impact::milestones(),
        ], [
            'title'    => 'Impact Metric',
            'eyebrow'  => 'Impact',
            'heading'  => 'Impact Metric',
            'subtitle' => 'Visualisasi reduksi emisi karbon dari aksi daur ulangmu.',
            'bodyClass' => 'page-impact',
        ]);
    }

    public function sorting(): void
    {
        View::render('sorting', [
            'detection' => Sorting::detection(),
            'steps'     => Sorting::steps(),
            'guide'     => Sorting::guide(),
            'tips'      => Sorting::tips(),
        ], [
            'title'    => 'Smart Sorting',
            'eyebrow'  => 'Sort',
            'heading'  => 'AI Smart Sorting',
            'subtitle' => 'Bingung sampah ini masuk kategori mana? Arahkan kamera.',
            'bodyClass' => 'page-sorting',
        ]);
    }

    public function gamify(): void
    {
        View::render('gamify', [
            'user'        => User::current(),
            'streak'      => User::streak(),
            'competition' => Gamify::competition(),
            'leaderboard' => Gamify::leaderboard(),
            'badges'      => Gamify::badges(),
            'actions'     => Gamify::actions(),
            'rewards'     => Gamify::rewards(),
            'incentives'  => Gamify::incentives(),
            'missions'    => Gamify::missions(),
        ], [
            'title'    => 'EcoPoints & Gamify',
            'eyebrow'  => 'Reward',
            'heading'  => 'EcoPoints & Campus Competition',
            'subtitle' => 'Streak harian, badge pencapaian, dan tantangan 500 KG bulan ini.',
            'bodyClass' => 'page-gamify',
        ]);
    }

    public function profile(): void
    {
        View::render('profile', [
            'user'        => User::current(),
            'badges'      => Gamify::badges(),
            'preferences' => User::preferences(),
            'totals'      => Waste::totals(),
            'impact'      => Impact::ranges()[Impact::defaultRange()],
        ], [
            'title'    => 'Profile',
            'eyebrow'  => 'Akun',
            'heading'  => 'Profil Saya',
            'subtitle' => 'Identitas kampus, pencapaian, dan preferensi notifikasi.',
            'bodyClass' => 'page-profile',
        ]);
    }
}
