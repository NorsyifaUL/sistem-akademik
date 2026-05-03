<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan penghapusan tabel yang double dan tidak terpakai.
     */
    public function up(): void
    {
        // Hapus tabel yang double (pakai yang 'siswas')
        Schema::dropIfExists('siswa');

        // Hapus tabel nilai yang akan digabung ke tabel 'nilais'
        Schema::dropIfExists('nilai_sikaps');
        Schema::dropIfExists('nilai_ekskuls');
    }

    /**
     * Balikkan perubahan (kosongkan saja karena ini operasi pembersihan).
     */
    public function down(): void
    {
        // Biarkan kosong
    }
};