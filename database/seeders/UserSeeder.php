<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $users = [
            [
                'id' => 'f1e67262-96ff-11ef-8df1-3822e23dbac4',
                'username' => 'Admin',
                'email' => 'Admin@gmail.com',
                'password' => '$2a$12$I.VxjJWbbhKrhKar7t/WneY8kKWfbxVWnuxX0RA/d366XIqUazU/u',
                'foto' => 'profile_user/user.jpg',
                'email_verified_at' => '2024-10-30 13:44:33',
            ],
            [
                'id' => 'a2b34567-96ff-11ef-8df1-3822e23dbac4',
                'username' => 'seniman1',
                'email' => 'seniman1@gmail.com',
                'password' => '$2a$12$DuCOETWei/nRfu0TA8D0xukNhG2pY.99E/PIOcOJ3nS5cQkofrzWK',
                'foto' => 'profile_user/user.jpg',
                'email_verified_at' => now(),
            ],
            [
                'id' => 'b3c45678-96ff-11ef-8df1-3822e23dbac4',
                'username' => 'seniman2',
                'email' => 'seniman2@gmail.com',
                'password' => '$2a$12$DuCOETWei/nRfu0TA8D0xukNhG2pY.99E/PIOcOJ3nS5cQkofrzWK',
                'foto' => 'profile_user/user.jpg',
                'email_verified_at' => now(),
            ],
            [
                'id' => 'c4d56789-96ff-11ef-8df1-3822e23dbac4',
                'username' => 'seniman3',
                'email' => 'seniman3@gmail.com',
                'password' => '$2a$12$DuCOETWei/nRfu0TA8D0xukNhG2pY.99E/PIOcOJ3nS5cQkofrzWK',
                'foto' => 'profile_user/user.jpg',
                'email_verified_at' => now(),
            ],
            [
                'id' => 'd5e67890-96ff-11ef-8df1-3822e23dbac4',
                'username' => 'penilai1',
                'email' => 'penilai1@gmail.com',
                'password' => '$2a$12$DuCOETWei/nRfu0TA8D0xukNhG2pY.99E/PIOcOJ3nS5cQkofrzWK',
                'foto' => 'profile_user/user.jpg',
                'email_verified_at' => now(),
            ],
            [
                'id' => 'e6f78901-96ff-11ef-8df1-3822e23dbac4',
                'username' => 'penilai2',
                'email' => 'penilai2@gmail.com',
                'password' => '$2a$12$DuCOETWei/nRfu0TA8D0xukNhG2pY.99E/PIOcOJ3nS5cQkofrzWK',
                'foto' => 'profile_user/user.jpg',
                'email_verified_at' => now(),
            ],
            [
                'id' => 'f7g89012-96ff-11ef-8df1-3822e23dbac4',
                'username' => 'penilai3',
                'email' => 'penilai3@gmail.com',
                'password' => '$2a$12$DuCOETWei/nRfu0TA8D0xukNhG2pY.99E/PIOcOJ3nS5cQkofrzWK',
                'foto' => 'profile_user/user.jpg',
                'email_verified_at' => now(),
            ],
        ];

        foreach ($users as $user) {
            DB::table('users')->updateOrInsert(
                ['id' => $user['id']], // Cek berdasarkan id
                array_merge($user, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }
}
