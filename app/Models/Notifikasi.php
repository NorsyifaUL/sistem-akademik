<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notifikasi extends Model
{
    use HasFactory;

    protected $table = 'notifikasis';

    protected $fillable = [
        'user_id',      // Tambahkan ini agar tahu notif milik siapa
        'kelas',        // Tambahkan ini jika ingin kirim per kelas
        'absensi_id',   // Sesuai model kamu sebelumnya
        'tanggal',
        'isi_pesan',
        'status_kirim',
    ];

    // Relasi ke User (Penting untuk menampilkan notif per user)
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relasi ke Absensi
    public function absensi()
    {
        return $this->belongsTo(Absensi::class, 'absensi_id');
    }
}