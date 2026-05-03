<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotifikasisTable extends Migration
{
    public function up(): void
    {
        Schema::create('notifikasis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('absensi_id')
                  ->constrained('absensis')
                  ->onDelete('cascade');
            $table->dateTime('tanggal');
            $table->text('isi_pesan');
            $table->enum('status_kirim', ['Terkirim', 'Gagal'])->default('Gagal');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifikasis');
    }
}