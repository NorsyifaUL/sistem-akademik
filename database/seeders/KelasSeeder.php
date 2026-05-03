<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kelas; // Pastikan Model Kelas di-import

class KelasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Menggunakan updateOrCreate agar jika data belum ada, Laravel akan membuatnya (insert)
        // Jika data sudah ada (berdasarkan nama_kelas), Laravel hanya akan memperbarui guru_id-nya (update)
        
        Kelas::updateOrCreate(
            ['nama_kelas' => 'X 1'], // Kunci pencarian
            ['guru_id' => 1]         // Data yang diisi/diperbarui
        );

        Kelas::updateOrCreate(
            ['nama_kelas' => 'X 2'],
            ['guru_id' => 2]
        );
        
        $this->command->info('Data Wali Kelas berhasil disinkronkan!');
    }
}