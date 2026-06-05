<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Log;

class Kelas extends Model
{
    protected $table = 'kelas'; 

    protected $fillable = ['nama_kelas', 'guru_id'];

    /**
     * Booted method untuk sinkronisasi otomatis.
     */
    protected static function booted()
    {
        static::saved(function ($kelas) {
            // 1. Log untuk memastikan event berjalan
            Log::info('Event Saved dipanggil untuk: ' . $kelas->nama_kelas);

            // Bersihkan data wali kelas lama dari user lain
            \App\Models\User::where('wali_kelas', $kelas->nama_kelas)->update(['wali_kelas' => null]);
    
            if ($kelas->guru_id) {
                $guru = \App\Models\Guru::find($kelas->guru_id);
                
                if ($guru) {
                    Log::info('Guru ditemukan: ' . $guru->nama);
                    
                    if ($guru->user) {
                        $guru->user->update(['wali_kelas' => $kelas->nama_kelas]);
                        Log::info('User diupdate: ' . $guru->user->name . ' dengan kelas ' . $kelas->nama_kelas);
                    } else {
                        Log::warning('Guru ID ' . $kelas->guru_id . ' tidak memiliki relasi ke User!');
                    }
                } else {
                    Log::warning('Guru ID ' . $kelas->guru_id . ' tidak ditemukan di database!');
                }
            }
        });

        static::deleted(function ($kelas) {
            \App\Models\User::where('wali_kelas', $kelas->nama_kelas)->update(['wali_kelas' => null]);
            Log::info('Data Kelas dihapus, sinkronisasi wali kelas dibersihkan: ' . $kelas->nama_kelas);
        });
    }

    /**
     * Relasi ke Siswa
     */
    public function siswas(): HasMany
    {
        return $this->hasMany(Siswa::class, 'kelas_id');
    }

    /**
     * Relasi ke Guru
     */
    public function guru(): BelongsTo
    {
        return $this->belongsTo(Guru::class, 'guru_id');
    }

    /**
     * Helper untuk mengakses User dari Wali Kelas
     */
    public function getWaliKelasAttribute()
    {
        return $this->guru ? $this->guru->user : null;
    }
}