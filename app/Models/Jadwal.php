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
        'kelas_id', 
        'kelas',    
        'hari',
        'jam_mulai',
        'jam_selesai',
        'ruangan'
    ];

    // =========================================================================
    // ACCESSOR (Penanganan Otomatis)
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

    /**
     * Accessor Virtual untuk Nama Kelas
     * Gunakan ini di Blade: $j->nama_display_kelas
     * Ini akan mengecek relasi dulu, jika kosong baru ambil dari kolom string 'kelas'
     */
    protected function namaDisplayKelas(): Attribute
    {
        return Attribute::make(
            get: function () {
                if ($this->kelasRelation) {
                    return $this->kelasRelation->nama_kelas;
                }
                return $this->attributes['kelas'] ?? '-';
            },
        );
    }

    // =========================================================================
    // RELASI DATABASE
    // =========================================================================

    public function guru()
    {
        return $this->belongsTo(Guru::class, 'guru_id');
    }

    public function mapel()
    {
        return $this->belongsTo(Mapel::class, 'mapel_id');
    }

    /**
     * Saya ubah nama fungsinya menjadi kelasRelation agar tidak bentrok 
     * dengan atribut 'kelas' di tabel Anda.
     */
    public function kelasRelation()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    public function absensis()
    {
        return $this->hasMany(Absensi::class, 'jadwal_id');
    }

    public function nilais()
    {
        return $this->hasMany(Nilai::class, 'jadwal_id');
    }
}