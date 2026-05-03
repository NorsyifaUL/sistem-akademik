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
        Schema::table('users', function (Blueprint $table) {
        $table->boolean('is_wali_kelas')->default(false); // Penanda dia Wali Kelas atau bukan
        $table->string('wali_kelas')->nullable();        // Nama kelas yang diampu (Contoh: 'X-A')
        $table->string('nip')->nullable();               // Untuk tanda tangan
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
