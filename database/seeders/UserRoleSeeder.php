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
            [

                'id' => 'fed7482c-96ff-11ef-8df1-3822e23dbac4',
                'user_id' => 'b3c45678-96ff-11ef-8df1-3822e23dbac4',
                'role_id' => 'bb330861-96ff-11ef-8df1-3822e23dbac4',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 'fed7483c-96ff-11ef-8df1-3822e23dbac4',
                'user_id' => 'c4d56789-96ff-11ef-8df1-3822e23dbac4',
                'role_id' => 'bb330861-96ff-11ef-8df1-3822e23dbac4',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 'fed7484c-96ff-11ef-8df1-3822e23dbac4',
                'user_id' => 'd5e67890-96ff-11ef-8df1-3822e23dbac4',
                'role_id' => 'bb32fc47-96ff-11ef-8df1-3822e23dbac4',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 'fed7485c-96ff-11ef-8df1-3822e23dbac4',
                'user_id' => 'e6f78901-96ff-11ef-8df1-3822e23dbac4',
                'role_id' => 'bb32fc47-96ff-11ef-8df1-3822e23dbac4',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 'fed7486c-96ff-11ef-8df1-3822e23dbac4',
                'user_id' => 'f7g89012-96ff-11ef-8df1-3822e23dbac4',
                'role_id' => 'bb32fc47-96ff-11ef-8df1-3822e23dbac4',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
