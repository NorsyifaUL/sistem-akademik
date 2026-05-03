<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Jadwal extends Model
{
    use HasFactory;

    protected $fillable = [
        'guru_id',
        'mapel_id',
        'kelas_id', // Pastikan menggunakan foreign key jika berelasi ke tabel Kelas
        'kelas',    // Jika kolom ini adalah string teks (misal: "X-IPA")
        'hari',
        'jam_mulai',
        'jam_selesai',
        'ruangan'
    ];

    // =========================================================================
    // ACCESSOR (Menghilangkan Detik secara Otomatis)
    // =========================================================================
    
    /**
     * Mengubah format jam_mulai dari 08:40:00 menjadi 08:40
     */
    protected function jamMulai(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value ? \Carbon\Carbon::parse($value)->format('H:i') : '-',
        );
    }

    /**
     * Mengubah format jam_selesai dari 10:00:00 menjadi 10:00
     */
    protected function jamSelesai(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value ? \Carbon\Carbon::parse($value)->format('H:i') : '-',
        );
    }

    // =========================================================================
    // RELASI DATABASE
    // =========================================================================

    // Relasi ke Guru
    public function guru()
    {
        return $this->belongsTo(Guru::class, 'guru_id');
    }

    // Relasi ke Mata Pelajaran
    public function mapel()
    {
        return $this->belongsTo(Mapel::class, 'mapel_id');
    }

    // Relasi ke Data Kelas (Tabel Kelas)
    // Gunakan ini jika kamu punya tabel 'kelas' terpisah
    public function dataKelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    // Relasi ke Absensi
    public function absensis()
    {
        return $this->hasMany(Absensi::class, 'jadwal_id');
    }

    // Relasi ke Nilai
    public function nilais()
    {
        return $this->hasMany(Nilai::class, 'jadwal_id');
    }
}