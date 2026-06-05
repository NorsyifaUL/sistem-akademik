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

    /**
     * Booted method untuk sinkronisasi otomatis ke tabel users
     */
    protected static function booted()
    {
        // Sinkronisasi saat data Guru dibuat atau diupdate
        static::saved(function ($guru) {
            if ($guru->user) {
                $guru->user->update([
                    'nip'  => $guru->nip,
                    'name' => $guru->nama
                ]);
            }
        });

        // Sinkronisasi saat data Guru dihapus
        static::deleted(function ($guru) {
            if ($guru->user) {
                // Opsional: Jika Anda ingin menghapus user sekalian saat guru dihapus, 
                // atau cukup kosongkan NIP di tabel user saja.
                $guru->user->update(['nip' => null]);
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