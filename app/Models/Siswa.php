<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Siswa extends Model
{
    use HasFactory;

    protected $table = 'siswas';

    protected $fillable = [
        'user_id',
        'nama',
        'nisn',
        'no_wa_ortu',
        'kelas_id',
    ];

    /**
     * Relasi ke User
     */
    public function user(): BelongsTo
    { 
        return $this->belongsTo(User::class, 'user_id', 'id'); 
    }

    /**
     * Relasi ke semua Nilai
     */
    public function nilais(): HasMany
    { 
        return $this->hasMany(Nilai::class, 'siswa_id'); 
    }

    /**
     * Relasi ke Absensi
     */
    public function absensis(): HasMany
    { 
        return $this->hasMany(Absensi::class, 'siswa_id'); 
    }

    /**
     * Relasi ke Kelas (Alias 1)
     */
    public function dataKelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    /**
     * Relasi ke Kelas (Alias 2)
     * Ditambahkan agar matching dengan pemanggilan with(['kelas']) di GuruController
     */
    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    /**
     * Mengambil Nilai khusus jenis Sikap
     */
    public function nilaiSikap(): HasOne
    {
        return $this->hasOne(Nilai::class, 'siswa_id')->where('jenis', 'sikap');
    }

    /**
     * Mengambil Nilai khusus jenis Ekstrakurikuler
     * Menggunakan 'ekstra' agar selaras dengan data di database dan rute sistem
     */
    public function nilaiEkskul(): HasMany
    {
        // Disesuaikan dari 'eskul' menjadi 'ekstra' jika data di tabel nilais menggunakan label tersebut
        return $this->hasMany(Nilai::class, 'siswa_id')->where('jenis', 'ekstra');
    }
}