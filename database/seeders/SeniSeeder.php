<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SeniSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $data = [
            [
                'id' => 'se1a1111-9700-11ef-8df1-3822e23dbac4',
                'kategori_id' => 'b939df28-9700-11ef-8df1-3822e23dbac4',
                'nama_seni' => 'Seni Lukis',
                'deskripsi_seni' => 'Seni lukis adalah cabang seni rupa yang menggunakan media kanvas, cat, dan alat lukis untuk menciptakan karya visual.',
                'status_seni' => 'Non-budaya',
                'deleted_at' => null,
            ],
            [
                'id' => 'se2b2222-9700-11ef-8df1-3822e23dbac4',
                'kategori_id' => 'b939df28-9700-11ef-8df1-3822e23dbac4',
                'nama_seni' => 'Seni Patung',
                'deskripsi_seni' => 'Seni patung merupakan seni tiga dimensi yang diciptakan dengan teknik memahat, mencetak, atau merakit bahan.',
                'status_seni' => 'Non-budaya',
                'deleted_at' => null,
            ],
            [
                'id' => 'se3c3333-9700-11ef-8df1-3822e23dbac4',
                'kategori_id' => 'b939df28-9700-11ef-8df1-3822e23dbac4',
                'nama_seni' => 'Kaligrafi',
                'deskripsi_seni' => 'Kaligrafi adalah seni menulis indah yang menggabungkan unsur huruf, estetika, dan ekspresi visual.',
                'status_seni' => 'Budaya',
                'deleted_at' => null,
            ],
        ];

        foreach ($data as $item) {
            DB::table('senis')->updateOrInsert(
                ['id' => $item['id']],
                array_merge($item, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }
}
