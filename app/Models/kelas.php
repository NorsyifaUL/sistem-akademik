<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    // Memberitahu Laravel bahwa nama tabel di phpMyAdmin adalah 'kelas'
    protected $table = 'kelas'; 

    protected $fillable = ['nama_kelas', 'guru_id'];

    // Relasi ke Siswa
    public function siswas()
    {
        return $this->hasMany(Siswa::class, 'kelas_id');
    }

    // Relasi ke Guru (Wali Kelas)
    public function guru() 
    {
        return $this->belongsTo(Guru::class, 'guru_id');
    }
}