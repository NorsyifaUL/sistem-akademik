<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $table = 'settings';

    protected $fillable = [
        'tahun_ajaran',
        'semester',
        'tgl_raport',
        'nama_kepsek',
        'nip_kepsek',
    ];

    /**
     * Trik Tambahan:
     * Mengatur casting untuk tgl_raport agar otomatis menjadi objek Carbon (Tanggal)
     * Ini memudahkan kamu saat ingin menampilkan format tanggal di Blade
     */
    protected $casts = [
        'tgl_raport' => 'date',
    ];
}