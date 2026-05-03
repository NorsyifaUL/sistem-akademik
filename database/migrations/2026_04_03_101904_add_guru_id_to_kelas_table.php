<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kelas', function (Blueprint $table) {
            // Tambahkan kolom guru_id setelah nama_kelas
            // Kita gunakan nullable() agar data yang sudah ada tidak error
            $table->unsignedBigInteger('guru_id')->nullable()->after('nama_kelas');

            // Membuat relasi (Foreign Key) ke tabel gurus
            $table->foreign('guru_id')->references('id')->on('gurus')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('kelas', function (Blueprint $table) {
            $table->dropForeign(['guru_id']);
            $table->dropColumn('guru_id');
        });
    }
};