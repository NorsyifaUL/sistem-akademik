<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('tahun_ajaran'); // Contoh: 2024/2025
            $table->enum('semester', ['1', '2']); // 1: Ganjil, 2: Genap
            $table->date('tgl_raport');     // Tanggal yang muncul di TTD
            $table->string('nama_kepsek');  // Nama Kepala Sekolah
            $table->string('nip_kepsek');   // NIP Kepala Sekolah
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
