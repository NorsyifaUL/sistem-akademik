<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    /**
     * Tampilkan halaman pengaturan akademik.
     */
    public function index()
    {
        // Gunakan firstOrNew agar jika tabel kosong, sistem tetap memberikan object kosong
        // Ini mencegah error "Attempt to read property on null" di file Blade
        $setting = Setting::first() ?? new Setting();
        
        return view('admin.settings.index', compact('setting'));
    }

    /**
     * Update pengaturan akademik secara global.
     */
    public function update(Request $request)
    {
        $request->validate([
            'tahun_ajaran' => 'required|string',
            'semester'     => 'required|in:1,2',
            'tgl_raport'   => 'required|date',
            'nama_kepsek'  => 'required|string',
            'nip_kepsek'   => 'required|string',
        ], [
            // Tambahkan pesan custom agar lebih user-friendly untuk Admin SMANJA
            'tahun_ajaran.required' => 'Tahun Pelajaran tidak boleh kosong!',
            'tgl_raport.required'   => 'Tanggal raport harus ditentukan!',
        ]);

        // Tetap menggunakan logika andalanmu: Cari ID 1, atau buat baru jika tidak ada
        $setting = Setting::first() ?? new Setting();
        
        $setting->tahun_ajaran = $request->tahun_ajaran;
        $setting->semester     = $request->semester;
        $setting->tgl_raport   = $request->tgl_raport;
        $setting->nama_kepsek  = $request->nama_kepsek;
        $setting->nip_kepsek   = $request->nip_kepsek;
        $setting->save();

        return redirect()->route('admin.settings.index')->with('success', 'Konfigurasi Akademik SMAN 1 Jejangkit Berhasil Diperbarui!');
    }
}