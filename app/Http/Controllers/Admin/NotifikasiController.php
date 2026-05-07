<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notifikasi;
use App\Models\Kelas; // Pastikan mengimport model Kelas
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotifikasiController extends Controller
{
    /**
     * Menampilkan daftar log notifikasi dengan filter kelas.
     */
    public function index(Request $request): View
    {
        // 1. Ambil daftar kelas dari tabel 'kelas' sesuai database (Screenshot 2026-05-06 094659.jpg)
        // Kita mengambil kolom 'nama_kelas' agar dropdown berisi "X 1", "X 2", dsb.
        $kelasList = Kelas::select('nama_kelas')
                        ->orderBy('nama_kelas', 'asc')
                        ->get();

        // 2. Query Notifikasi (Eager Load absensi dan siswa)
        // Fokus pada notifikasi Alpa dan mengecualikan pesan selamat datang
        $query = Notifikasi::with(['absensi.siswa'])
                    ->where('isi_pesan', 'LIKE', '%ALPA%')
                    ->where('isi_pesan', 'NOT LIKE', '%Selamat Datang%');

        // 3. Logika Filter Kelas
        // Mencocokkan nilai dropdown dengan kolom 'kelas' pada tabel siswa
        if ($request->filled('kelas')) {
            $query->whereHas('absensi.siswa', function($q) use ($request) {
                $q->where('kelas', $request->kelas);
            });
        }

        // 4. Urutkan dari yang terbaru dan gunakan pagination
        $notifikasis = $query->latest()->paginate(15);

        // 5. Kirim variabel ke View (fungsi destroy dihapus sesuai permintaan)
        return view('admin.notifikasi.index', compact('notifikasis', 'kelasList'));
    }
}