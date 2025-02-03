<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('roles')->insert([
            [
                'id' => 'bb32fc47-96ff-11ef-8df1-3822e23dbac4',
                'nama_role' => 'Penilai',
                'created_at' => now(),
            ],
            [
                'id' => 'bb330861-96ff-11ef-8df1-3822e23dbac4',
                'nama_role' => 'Seniman',
                'created_at' => now(),
            ],
            [
                'id' => 'bb32ea99-96ff-11ef-8df1-3822e23dbac4',
                'nama_role' => 'Admin',
                'created_at' => now(),
            ],
        ]);
    }
}
