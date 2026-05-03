<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nilai extends Model
{
    use HasFactory;

    protected $table = 'nilais'; 

    protected $fillable = [
        'siswa_id',
        'jadwal_id',
        'mapel_id',
        'jenis',
        'aspek',     
        'nilai',
        'predikat',  
        'keterangan',
        'semester',
        'tahun_ajaran'
    ];

    // Relasi ke Siswa
    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }

    // Relasi ke Jadwal
    public function jadwal()
    {
        return $this->belongsTo(Jadwal::class, 'jadwal_id');
    }
    
    // Relasi Langsung ke Mapel (Jika kamu menyimpan mapel_id di tabel nilais)
    public function mapel() 
    {
        return $this->belongsTo(Mapel::class, 'mapel_id');
    }
}