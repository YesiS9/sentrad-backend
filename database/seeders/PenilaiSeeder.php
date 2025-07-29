<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PenilaiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $penilai = [
            [
                'id' => 'p1a11111-9700-11ef-8df1-3822e23dbac4',
                'user_id' => 'd5e67890-96ff-11ef-8df1-3822e23dbac4',
                'kategori_id' => 'b939df28-9700-11ef-8df1-3822e23dbac4',
                'nama_penilai' => 'Penilai Satu',
                'alamat_penilai' => 'Jl. Anggrek No. 11, Yogyakarta',
                'noTelp_penilai' => '081111111111',
                'bidang_ahli' => 'Seni Lukis',
                'lembaga' => 'Lembaga Seni Nasional',
                'tgl_lahir' => '1980-02-15',
                'status_penilai' => 'Aktif',
                'kuota' => 10,
                'deleted_at' => null,
            ],
            [
                'id' => 'p2b22222-9700-11ef-8df1-3822e23dbac4',
                'user_id' => 'e6f78901-96ff-11ef-8df1-3822e23dbac4',
                'kategori_id' => 'b939df28-9700-11ef-8df1-3822e23dbac4',
                'nama_penilai' => 'Penilai Dua',
                'alamat_penilai' => 'Jl. Sakura No. 22, Bandung',
                'noTelp_penilai' => '082222222222',
                'bidang_ahli' => 'Seni Patung',
                'lembaga' => 'Asosiasi Seniman Indonesia',
                'tgl_lahir' => '1975-07-25',
                'status_penilai' => 'Aktif',
                'kuota' => 15,
                'deleted_at' => null,
            ],
            [
                'id' => 'p3c33333-9700-11ef-8df1-3822e23dbac4',
                'user_id' => 'f7g89012-96ff-11ef-8df1-3822e23dbac4',
                'kategori_id' => 'b939df28-9700-11ef-8df1-3822e23dbac4',
                'nama_penilai' => 'Penilai Tiga',
                'alamat_penilai' => 'Jl. Flamboyan No. 33, Jakarta',
                'noTelp_penilai' => '083333333333',
                'bidang_ahli' => 'Kaligrafi',
                'lembaga' => 'Institut Seni Modern',
                'tgl_lahir' => '1983-11-10',
                'status_penilai' => 'Aktif',
                'kuota' => 12,
                'deleted_at' => null,
            ],
        ];

        foreach ($penilai as $item) {
            DB::table('penilais')->updateOrInsert(
                ['id' => $item['id']],
                array_merge($item, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }
}
