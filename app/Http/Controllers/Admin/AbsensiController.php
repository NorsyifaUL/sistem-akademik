<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Absensi;
use App\Models\Kelas;

class AbsensiController extends Controller
{
    public function index(Request $request)
    {
        // 1. Tangkap data dari filter (Pastikan 'name' di Blade adalah 'tanggal' dan 'kelas')
        $tanggal = $request->get('tanggal', date('Y-m-d'));
        $filterKelas = $request->get('kelas'); 

        // 2. Query dasar dengan relasi
        $query = Absensi::with(['siswa.dataKelas', 'jadwal.mapel'])
                        ->whereDate('created_at', $tanggal); 

        // 3. Logika Filter Kelas
        if ($filterKelas) {
            $query->whereHas('siswa.dataKelas', function($q) use ($filterKelas) {
                // Kita cari berdasarkan string nama_kelas (misal: "X 1")
                $q->where('nama_kelas', $filterKelas);
            });
        }

        $absensis = $query->latest()->get();
        
        // 4. Ambil list kelas untuk dropdown (agar tidak kosong)
        $listKelas = Kelas::orderBy('nama_kelas', 'asc')->get(); 

        // 5. Kirim semua variabel ke view
        return view('admin.absensi.index', [
            'absensis' => $absensis,
            'listKelas' => $listKelas,
            'tanggal' => $tanggal,
            'filterKelas' => $filterKelas
        ]);
    }
}