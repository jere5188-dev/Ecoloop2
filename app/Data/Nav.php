<?php
/**
 * EcoLoop — navigation registry.
 *
 * Single source of truth for the sidebar, the mobile bottom bar and the
 * dashboard quick actions (requirement R6.2).
 */

declare(strict_types=1);

namespace EcoLoop\Data;

final class Nav
{
    /**
     * @return array<int, array{label:string,path:string,icon:string,short:string,primary:bool,group:string}>
     */
    public static function items(): array
    {
        return [
            [
                'label'   => 'Home',
                'short'   => 'Home',
                'path'    => '/',
                'icon'    => 'home',
                'primary' => true,
                'group'   => 'main',
            ],
            [
                'label'   => 'Waste Passbook',
                'short'   => 'Passbook',
                'path'    => '/passbook',
                'icon'    => 'passbook',
                'primary' => true,
                'group'   => 'main',
            ],
            [
                'label'   => 'Impact Metric',
                'short'   => 'Impact',
                'path'    => '/impact',
                'icon'    => 'leaf',
                'primary' => true,
                'group'   => 'main',
            ],
            [
                'label'   => 'Smart Sorting',
                'short'   => 'Scan',
                'path'    => '/sorting',
                'icon'    => 'camera',
                'primary' => true,
                'group'   => 'main',
            ],
            [
                'label'   => 'Waste Pickup',
                'short'   => 'Pickup',
                'path'    => '/pickup',
                'icon'    => 'truck',
                'primary' => false,
                'group'   => 'main',
            ],
            [
                'label'   => 'Competition',
                'short'   => 'Compete',
                'path'    => '/competition',
                'icon'    => 'trophy',
                'primary' => true,
                'group'   => 'main',
            ],
            [
                'label'   => 'Profile',
                'short'   => 'Profile',
                'path'    => '/profile',
                'icon'    => 'profile',
                'primary' => false,
                'group'   => 'account',
            ],
        ];
    }

    /**
     * The 5 destinations shown in the mobile bottom navigation bar.
     *
     * @return array<int, array<string, mixed>>
     */
    public static function bottom(): array
    {
        return array_values(array_filter(self::items(), static fn (array $i) => $i['primary']));
    }

    /** @return array<int, array<string, mixed>> */
    public static function group(string $group): array
    {
        return array_values(array_filter(self::items(), static fn (array $i) => $i['group'] === $group));
    }

    /**
     * Shortcut tiles surfaced on the dashboard.
     *
     * @return array<int, array{label:string,desc:string,path:string,icon:string,tone:string}>
     */
    public static function quickActions(): array
    {
        return [
            [
                'label' => 'Scan Sampah',
                'desc'  => 'Kenali material dengan kamera AI',
                'path'  => '/sorting',
                'icon'  => 'scan',
                'tone'  => 'forest',
            ],
            [
                'label' => 'Request Pickup',
                'desc'  => 'Jemput sampah volume besar',
                'path'  => '/pickup',
                'icon'  => 'truck',
                'tone'  => 'info',
            ],
            [
                'label' => 'Drop-off Point',
                'desc'  => '12 titik aktif di kampus',
                'path'  => '/pickup',
                'icon'  => 'map',
                'tone'  => 'sage',
            ],
            [
                'label' => 'Tukar Poin',
                'desc'  => '2,450 pts siap ditukar',
                'path'  => '/competition',
                'icon'  => 'gift',
                'tone'  => 'leaf',
            ],
        ];
    }
}
