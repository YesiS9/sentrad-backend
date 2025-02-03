<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('user_roles')->insert([
            [
                'id' => 'fed7480c-96ff-11ef-8df1-3822e23dbac4',
                'user_id' => 'f1e67262-96ff-11ef-8df1-3822e23dbac4',
                'role_id' => 'bb32ea99-96ff-11ef-8df1-3822e23dbac4',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
