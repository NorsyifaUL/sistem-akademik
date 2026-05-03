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
        Schema::table('nilais', function (Blueprint $table) {
        // Menambahkan kolom agar data nilai lebih detail
        $table->string('keterangan')->nullable()->after('nilai'); // Contoh: "Ulangan Bab 1"
        $table->string('semester')->default('Ganjil')->after('keterangan');
        $table->string('tahun_ajaran')->default('2025/2026')->after('semester');
        $table->foreignId('guru_id')->nullable()->constrained()->after('siswa_id'); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('nilais', function (Blueprint $table) {
            //
        });
    }
};
