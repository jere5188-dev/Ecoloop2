<?php
/**
 * EcoLoop — Smart Sorting (AI camera) data.
 *
 * Holds the detection fixture and the mandatory 4-step preparation instructions
 * from the pitch deck: Kosongkan, Bilas, Lepas, Remas (requirement R3.4).
 */

declare(strict_types=1);

namespace EcoLoop\Data;

final class Sorting
{
    /** The simulated AI detection result. @return array<string, mixed> */
    public static function detection(): array
    {
        return [
            'material'    => 'Botol Plastik PET',
            'material_en' => 'PET Bottle',
            'technical'   => 'Polyethylene Terephthalate (PET / #1)',
            'icon'        => 'bottle',
            'tone'        => 'info',
            'recyclable'  => '100% Recyclable Material',
            'confidence'  => 96,
            'weight_min'  => 12,
            'weight_max'  => 15,
            'weight_note' => 'Estimasi berat: 12–15 g',
            'points'      => 100,
            'bin'         => 'Bin Plastik — kode biru',
            'bin_tone'    => 'info',
        ];
    }

    /**
     * The 4 preparation steps shown in the instruction tooltip.
     *
     * @return array<int, array{no:int,title:string,en:string,desc:string,icon:string}>
     */
    public static function steps(): array
    {
        return [
            [
                'no'    => 1,
                'title' => 'Kosongkan',
                'en'    => 'Empty',
                'desc'  => 'Buang sisa cairan atau partikel makanan dari dalam kemasan.',
                'icon'  => 'empty',
            ],
            [
                'no'    => 2,
                'title' => 'Bilas',
                'en'    => 'Rinse',
                'desc'  => 'Bilas dengan air secukupnya agar bersih dari bau dan residu gula.',
                'icon'  => 'drop',
            ],
            [
                'no'    => 3,
                'title' => 'Lepas',
                'en'    => 'Remove',
                'desc'  => 'Lepas tutup botol dan label plastik, pisahkan ke kategori berbeda.',
                'icon'  => 'unlink',
            ],
            [
                'no'    => 4,
                'title' => 'Remas',
                'en'    => 'Crush',
                'desc'  => 'Remas botol agar volumenya kecil, lalu masukkan ke bin daur ulang.',
                'icon'  => 'compress',
            ],
        ];
    }

    /**
     * Reference guide of materials the scanner recognises.
     *
     * @return array<int, array<string, mixed>>
     */
    public static function guide(): array
    {
        return [
            [
                'label'      => 'PET / Botol Plastik',
                'icon'       => 'bottle',
                'tone'       => 'info',
                'accepted'   => true,
                'points'     => '40 pts / kg',
                'examples'   => 'Botol air mineral, botol soda, gelas plastik bening',
            ],
            [
                'label'      => 'Kertas & Kardus',
                'icon'       => 'paper',
                'tone'       => 'warning',
                'accepted'   => true,
                'points'     => '30 pts / kg',
                'examples'   => 'Kertas HVS, buku tulis, kardus makanan bersih',
            ],
            [
                'label'      => 'Kaleng & Logam',
                'icon'       => 'can',
                'tone'       => 'sage',
                'accepted'   => true,
                'points'     => '38 pts / kg',
                'examples'   => 'Kaleng minuman aluminium, kaleng makanan',
            ],
            [
                'label'      => 'Residu / Sampah Campur',
                'icon'       => 'alert',
                'tone'       => 'error',
                'accepted'   => false,
                'points'     => 'Tidak berpoin',
                'examples'   => 'Tisu bekas, styrofoam berminyak, sisa makanan',
            ],
        ];
    }

    /**
     * Scanner viewport hint chips.
     *
     * @return array<int, string>
     */
    public static function tips(): array
    {
        return [
            'Posisikan kemasan di dalam bingkai',
            'Pastikan pencahayaan cukup',
            'Satu objek per pemindaian',
        ];
    }
}
