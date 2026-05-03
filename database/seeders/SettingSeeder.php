<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Setting::updateOrCreate(
        ['id' => 1], // Pastikan hanya ada 1 baris data dengan ID 1
        [
            'tahun_ajaran' => '2024/2025',
            'semester' => '1',
            'tgl_raport' => '2026-06-20',
            'nama_kepsek' => 'KASMUDIN, M.Pd.',
            'nip_kepsek' => '197810232006041007',
            'is_wali_kelas' => false,
        ]
    );
    }
}
