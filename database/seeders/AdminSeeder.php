<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // ADMIN
        User::create([
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'role' => 'admin',
            'password' => Hash::make('admin123'),
        ]);

        // GURU
        User::create([
            'name' => 'Guru',
            'email' => 'guru@gmail.com',
            'role' => 'guru',
            'password' => Hash::make('guru123'),
        ]);

        // SISWA
        User::create([
            'name' => 'Siswa',
            'email' => 'siswa@gmail.com',
            'role' => 'siswa',
            'password' => Hash::make('siswa123'),
        ]);
    }
}