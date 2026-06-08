<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Absensi extends Model
{
    use HasFactory;

    protected $table = 'absensis';

    protected $fillable = [
        'siswa_id',
        'jadwal_id',
        'tanggal',
        'status',
        'keterangan',
        'status_wa',
    ];

    /**
     * Casting tanggal agar otomatis menjadi objek Carbon.
     * Ini memudahkan kamu melakukan format tanggal di Blade atau Controller.
     */
    protected $casts = [
        'tanggal' => 'date',
    ];

    // Relasi ke siswa
    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }

    // Relasi ke jadwal
    public function jadwal()
    {
        return $this->belongsTo(Jadwal::class, 'jadwal_id');
    }

    // Relasi ke notifikasi
    public function notifikasis()
    {
        return $this->hasMany(Notifikasi::class, 'absensi_id');
    }
}