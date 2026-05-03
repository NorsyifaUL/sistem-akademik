<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guru extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nama',
        'nip',
    ];

    // --- TAMBAHKAN INI AGAR OTOMATIS SINKRON ---
    protected static function booted()
    {
        // Setiap kali data Guru di-update...
        static::updated(function ($guru) {
            if ($guru->user) {
                // ...update juga NIP di tabel users
                $guru->user->update([
                    'nip' => $guru->nip,
                    'name' => $guru->nama
                ]);
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function jadwals()
    {
        return $this->hasMany(Jadwal::class, 'guru_id');
    }

    public function mapels()
    {
        return $this->hasManyThrough(
            Mapel::class,
            Jadwal::class,
            'guru_id',
            'id',
            'id',
            'mapel_id'
        );
    }

    public function waliKelas()
    {
        return $this->hasOne(Kelas::class, 'guru_id');
    }
}