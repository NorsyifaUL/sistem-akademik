<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Keterangan extends Model
{
    use HasFactory;

    // Pastikan nama tabel sesuai dengan yang ada di DB Anda
    protected $table = 'keterangan'; 

    // Masukkan kolom yang ada di tabel keterangan Anda
    protected $fillable = [
        'nilai_id', 
        'deskripsi', // atau nama kolom teks Anda
    ];

    // Relasi balik ke Nilai
    public function nilai()
    {
        return $this->belongsTo(Nilai::class, 'nilai_id');
    }
}