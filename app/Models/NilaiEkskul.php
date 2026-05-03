<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NilaiEkskul extends Model
{
    use HasFactory;

    protected $table = 'nilai_ekskuls';

    protected $fillable = [
        'siswa_id',
        'nama_ekskul',
        'predikat',
        'keterangan',
        'semester',
        'tahun_ajaran'
    ];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }
}