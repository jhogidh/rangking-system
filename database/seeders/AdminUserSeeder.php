<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat Guru BK
        User::updateOrCreate(
            ['email' => 'bk@guru.com'],
            [
                'name' => 'Guru BK',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]
        );

        // 2. Buat Admin (Sisi input)
        User::updateOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name' => 'Administrasi',
                'password' => Hash::make('password'),
                'role' => 'guru',
            ]
        );
    }
}
