<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Kelas extends Model
{
    protected $table = 'kelas'; 

    protected $fillable = ['nama_kelas', 'guru_id'];

    /**
     * Relasi ke Siswa
     * Satu kelas memiliki banyak siswa
     */
    public function siswas(): HasMany
    {
        return $this->hasMany(Siswa::class, 'kelas_id');
    }

    /**
     * Relasi ke User (Wali Kelas)
     * Mengambil data dari tabel users berdasarkan guru_id
     */
    // app/Models/Kelas.php
    public function guru(): BelongsTo
    {
        // HARUS merujuk ke model Guru, bukan User
        return $this->belongsTo(Guru::class, 'guru_id');
    }
}