<?php
/**
 * EcoLoop — current user fixture (primary persona: mahasiswa).
 */

declare(strict_types=1);

namespace EcoLoop\Data;

final class User
{
    /** @return array<string, mixed> */
    public static function current(): array
    {
        return [
            'name'        => 'Nadia Prameswari',
            'first_name'  => 'Nadia',
            'initials'    => 'NP',
            'student_id'  => '21/478912/TK/52418',
            'faculty'     => 'Fakultas Teknik',
            'program'     => 'Teknik Industri',
            'joined'      => 'Bergabung Feb 2026',
            'level'       => 'Eco Champion',
            'level_rank'  => 3,
            'points'      => 2450,
            'points_month'=> 610,
            'streak_days' => 7,
            'total_kg'    => 12.4,
            'co2_kg'      => 18.6,
            'deposits'    => 24,
            'rank_campus' => 18,
            'rank_faculty'=> 3,
        ];
    }

    /**
     * Seven-day streak state. `done` = completed, `today` = current day.
     *
     * @return array<int, array{label:string,day:string,done:bool,today:bool,points:int}>
     */
    public static function streak(): array
    {
        return [
            ['label' => '1', 'day' => 'Sen', 'done' => true,  'today' => false, 'points' => 20],
            ['label' => '2', 'day' => 'Sel', 'done' => true,  'today' => false, 'points' => 50],
            ['label' => '3', 'day' => 'Rab', 'done' => true,  'today' => false, 'points' => 30],
            ['label' => '4', 'day' => 'Kam', 'done' => true,  'today' => false, 'points' => 20],
            ['label' => '5', 'day' => 'Jum', 'done' => true,  'today' => false, 'points' => 100],
            ['label' => '6', 'day' => 'Sab', 'done' => true,  'today' => false, 'points' => 30],
            ['label' => '7', 'day' => 'Min', 'done' => true,  'today' => true,  'points' => 40],
        ];
    }

    /**
     * Preference switches rendered on the profile page.
     *
     * @return array<int, array{key:string,label:string,desc:string,on:bool}>
     */
    public static function preferences(): array
    {
        return [
            [
                'key'   => 'reminder',
                'label' => 'Pengingat setor harian',
                'desc'  => 'Notifikasi pukul 16.00 setiap hari kuliah.',
                'on'    => true,
            ],
            [
                'key'   => 'leaderboard',
                'label' => 'Tampil di leaderboard',
                'desc'  => 'Nama kamu terlihat pada peringkat fakultas.',
                'on'    => true,
            ],
            [
                'key'   => 'pickup_sms',
                'label' => 'Konfirmasi pickup via SMS',
                'desc'  => 'Selain email, kirim juga notifikasi singkat.',
                'on'    => false,
            ],
            [
                'key'   => 'digest',
                'label' => 'Ringkasan dampak bulanan',
                'desc'  => 'Laporan reduksi emisi setiap awal bulan.',
                'on'    => true,
            ],
        ];
    }
}
