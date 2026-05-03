<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NilaiSikap extends Model
{
    use HasFactory;

    protected $table = 'nilai_sikaps'; // Nama tabel di database

    protected $fillable = [
        'siswa_id',
        'beribadah',
        'santun',
        'disiplin',
        'tanggung_jawab',
        'deskripsi_catatan',
        'semester',
        'tahun_ajaran'
    ];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }
}