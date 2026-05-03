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
        // 1. Ambil input filter
        $tanggal = $request->get('tanggal', date('Y-m-d'));
        $filterKelas = $request->get('kelas');

        // 2. Query data absensi
        // Tambahkan select('*') untuk memastikan semua kolom terambil
        $query = Absensi::with(['siswa.dataKelas'])
                ->whereDate('created_at', $tanggal);

        // 3. Logika Filter Kelas
        if ($filterKelas) {
            $query->whereHas('siswa.dataKelas', function($q) use ($filterKelas) {
                // Sesuai Screenshot 2026-05-01 150508.jpg, kolomnya adalah nama_kelas
                $q->where('nama_kelas', $filterKelas);
            });
        }

        $absensis = $query->latest()->get();
        
        // 4. Ambil semua data kelas (Urutkan agar rapi di dropdown)
        $listKelas = Kelas::orderBy('nama_kelas', 'asc')->get(); 

        // 5. Kirim semua variabel ke view
        return view('admin.absensi.index', compact('absensis', 'listKelas', 'tanggal'));
    }
}