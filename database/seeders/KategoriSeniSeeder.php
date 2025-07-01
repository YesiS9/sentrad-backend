<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KategoriSeniSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $kategori = [
            [
                'id' => 'b939df28-9700-11ef-8df1-3822e23dbac4',
                'user_id' => 'f1e67262-96ff-11ef-8df1-3822e23dbac4',
                'nama_kategori' => 'Seni Rupa',
                'deskripsi_kategori' => 'Lorem ipsum odor amet, consectetuer adipiscing elit. Quam curabitur quis...',
            ],
            [
                'id' => 'b939ef2c-9700-11ef-8df1-3822e23dbac4',
                'user_id' => 'f1e67262-96ff-11ef-8df1-3822e23dbac4',
                'nama_kategori' => 'Seni Tari',
                'deskripsi_kategori' => 'Lorem ipsum odor amet, consectetuer adipiscing elit. Egestas curae elementum...',
            ],
            [
                'id' => 'b939fe11-9700-11ef-8df1-3822e23dbac4',
                'user_id' => 'f1e67262-96ff-11ef-8df1-3822e23dbac4',
                'nama_kategori' => 'Seni Teater',
                'deskripsi_kategori' => 'Lorem ipsum odor amet, consectetuer adipiscing elit. Viverra feugiat sed...',
            ],
            [
                'id' => 'b93a0c22-9700-11ef-8df1-3822e23dbac4',
                'user_id' => 'f1e67262-96ff-11ef-8df1-3822e23dbac4',
                'nama_kategori' => 'Seni Musik',
                'deskripsi_kategori' => 'Lorem ipsum odor amet, consectetuer adipiscing elit. Viverra feugiat sed...',
            ],
            [
                'id' => 'b93a19dc-9700-11ef-8df1-3822e23dbac4',
                'user_id' => 'f1e67262-96ff-11ef-8df1-3822e23dbac4',
                'nama_kategori' => 'Seni Kerajinan Tangan',
                'deskripsi_kategori' => 'Lorem ipsum odor amet, consectetuer adipiscing elit. Viverra feugiat sed...',
            ],
        ];

        foreach ($kategori as $item) {
            DB::table('kategori_senis')->updateOrInsert(
                ['id' => $item['id']],
                array_merge($item, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }
}
