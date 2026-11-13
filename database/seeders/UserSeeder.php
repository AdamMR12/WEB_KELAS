<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            ['name' => 'Admin Utama', 'email' => 'admin@memorywall.test', 'level' => User::LEVEL_ADMIN],
            ['name' => 'User Biasa',  'email' => 'user@memorywall.test',  'level' => User::LEVEL_USER],
        ];

        foreach ($users as $u) {
            User::updateOrCreate(
                ['email' => $u['email']],
                array_merge($u, [
                    'password'          => Hash::make('password'),
                    'email_verified_at' => now(),
                    'is_active'         => true,
                ])
            );
        }
    }
}
