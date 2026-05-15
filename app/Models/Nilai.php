<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nilai extends Model
{
    use HasFactory;

    protected $table = 'nilais'; 

    protected $primaryKey = 'id';

    public $incrementing = true;

    protected $fillable = [
        'siswa_id',
        'jadwal_id',
        // 'mapel_id',
        'guru_id',
        'jenis',
        'aspek',
        'nilai',
        'predikat',  
        'keterangan',
        'capaian_kompetensi',
        'semester',
        'tahun_ajaran'
    ];

    /**
     * Casting tipe data kolom agar sinkron dengan perhitungan PHP
     */
    protected $casts = [
        'nilai' => 'integer',
        'siswa_id' => 'integer',
        'jadwal_id' => 'integer',
        'mapel_id' => 'integer',
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
    
    // Relasi ke Mapel
    public function mapel() 
    {
        return $this->belongsTo(Mapel::class, 'mapel_id');
    }

    public function keterangan_relasi()
    {
        return $this->hasOne(Keterangan::class, 'nilai_id');
    }
}