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
        // Mengambil data pengaturan pertama (ID 1)
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
            'tahun_ajaran.required' => 'Tahun Pelajaran tidak boleh kosong!',
            'tgl_raport.required'   => 'Tanggal raport harus ditentukan!',
            'nama_kepsek.required'  => 'Nama Kepala Sekolah wajib diisi!',
        ]);

        // Menggunakan updateOrCreate agar lebih ringkas & aman (mengunci ID 1)
        Setting::updateOrCreate(
            ['id' => 1], 
            [
                'tahun_ajaran' => $request->tahun_ajaran,
                'semester'     => $request->semester,
                'tgl_raport'   => $request->tgl_raport,
                'nama_kepsek'  => $request->nama_kepsek,
                'nip_kepsek'   => $request->nip_kepsek,
            ]
        );

        return redirect()->route('admin.settings.index')->with('success', 'Konfigurasi Akademik SMAN 1 Jejangkit Berhasil Diperbarui!');
    }
}