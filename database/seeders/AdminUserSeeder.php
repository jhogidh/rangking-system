<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat Admin
        User::updateOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]
        );

        // 2. Buat Guru (Wali Kelas)
        User::updateOrCreate(
            ['email' => 'guru@guru.com'],
            [
                'name' => 'Guru',
                'password' => Hash::make('password'),
                'role' => 'guru',
            ]
        );
    }
}
