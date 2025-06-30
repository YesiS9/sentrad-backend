<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SenimanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('seniman')->insert([
            [
                'id' => 's1a11111-96ff-11ef-8df1-3822e23dbac4',
                'user_id' => 'a2b34567-96ff-11ef-8df1-3822e23dbac4', // seniman1 user_id
                'tingkatan_id' => null,
                'nama_seniman' => 'Seniman Satu',
                'tgl_lahir' => '1995-05-10',
                'deskripsi_seniman' => 'Seniman spesialis lukisan realis.',
                'alamat_seniman' => 'Jl. Melati No. 10, Yogyakarta',
                'noTelp_seniman' => '081234567890',
                'lama_pengalaman' => 5,
                'status_seniman' => 1,
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => null,
            ],
            [
                'id' => 's2b22222-96ff-11ef-8df1-3822e23dbac4',
                'user_id' => 'b3c45678-96ff-11ef-8df1-3822e23dbac4', // seniman2 user_id
                'tingkatan_id' => null,
                'nama_seniman' => 'Seniman Dua',
                'tgl_lahir' => '1990-08-15',
                'deskripsi_seniman' => 'Ahli seni patung modern.',
                'alamat_seniman' => 'Jl. Kenanga No. 5, Jakarta',
                'noTelp_seniman' => '082234567890',
                'lama_pengalaman' => 7,
                'status_seniman' => 1,
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => null,
            ],
            [
                'id' => 's3c33333-96ff-11ef-8df1-3822e23dbac4',
                'user_id' => 'c4d56789-96ff-11ef-8df1-3822e23dbac4', // seniman3 user_id
                'tingkatan_id' => null,
                'nama_seniman' => 'Seniman Tiga',
                'tgl_lahir' => '1988-12-20',
                'deskripsi_seniman' => 'Seniman kaligrafi kontemporer.',
                'alamat_seniman' => 'Jl. Mawar No. 15, Bandung',
                'noTelp_seniman' => '083334567890',
                'lama_pengalaman' => 10,
                'status_seniman' => 1,
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => null,
            ],
        ]);
    }

}
