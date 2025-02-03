<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'id' => 'f1e67262-96ff-11ef-8df1-3822e23dbac4',
            'username' => 'Admin',
            'email' => 'Admin@gmail.com',
            'password' => '$2a$12$I.VxjJWbbhKrhKar7t/WneY8kKWfbxVWnuxX0RA/d366XIqUazU/u',
            'foto' => 'profile_user/user.jpg',
            'email_verified_at' => '2024-10-30 13:44:33',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
