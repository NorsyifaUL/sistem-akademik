<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('nilais', function (Blueprint $table) {
            // 1. Ubah 'jenis' agar fleksibel (tidak terkunci di harian/uts/uas saja)
            $table->string('jenis')->change();

            // 2. Tambahkan kolom 'aspek' untuk nama Bab (Ulangan Bab) atau nama Karakter (Sikap)
            $table->string('aspek')->nullable()->after('jenis');

            // 3. Tambahkan kolom 'predikat' untuk menyimpan (Sangat Baik, Baik, A, B, dll)
            $table->string('predikat')->nullable()->after('nilai');
            
            // 4. Pastikan kolom nilai bisa kosong (nullable) karena Sikap pakai Predikat
            $table->integer('nilai')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('nilais', function (Blueprint $table) {
            $table->dropColumn(['aspek', 'predikat']);
        });
    }
};