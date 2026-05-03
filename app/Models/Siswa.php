<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Siswa extends Model
{
    use HasFactory;

    protected $table = 'siswas';

    protected $fillable = [
        'user_id',
        'nama',
        'nisn',
        'kelas',
        'no_wa_ortu',
        'kelas_id',
    ];

    public function user() 
    { 
        return $this->belongsTo(User::class, 'user_id', 'id'); 
    }

    public function nilais() 
    { 
        return $this->hasMany(Nilai::class, 'siswa_id'); 
    }

    public function absensis() 
    { 
        return $this->hasMany(Absensi::class, 'siswa_id'); 
    }

    public function dataKelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    public function nilaiSikap()
    {
        return $this->hasOne(Nilai::class, 'siswa_id')->where('jenis', 'sikap');
    }

    public function nilaiEkskul()
    {
        return $this->hasMany(Nilai::class, 'siswa_id')->where('jenis', 'eskul');
    }
    
}