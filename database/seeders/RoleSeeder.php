<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $roles = [
            [
                'id' => 'bb32fc47-96ff-11ef-8df1-3822e23dbac4',
                'nama_role' => 'Penilai',
            ],
            [
                'id' => 'bb330861-96ff-11ef-8df1-3822e23dbac4',
                'nama_role' => 'Seniman',
            ],
            [
                'id' => 'bb32ea99-96ff-11ef-8df1-3822e23dbac4',
                'nama_role' => 'Admin',
            ],
        ];

        foreach ($roles as $role) {
            DB::table('roles')->updateOrInsert(
                ['id' => $role['id']],
                array_merge($role, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }
}
