<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notifikasi;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class NotifikasiController extends Controller
{
    /**
     * Menampilkan daftar log notifikasi dengan filter kelas.
     */
    public function index(Request $request): View
    {
        // 1. Ambil daftar kelas unik dari tabel Siswa untuk dropdown filter
        $kelasList = Siswa::select('kelas')
                        ->distinct()
                        ->orderBy('kelas', 'asc')
                        ->get();

        // 2. Query Notifikasi (Eager Load absensi dan siswa agar tidak berat)
        $query = Notifikasi::with(['absensi.siswa'])
                    ->where('isi_pesan', 'LIKE', '%ALPA%')
                    ->where('isi_pesan', 'NOT LIKE', '%Selamat Datang%');

        // 3. Logika Filter Kelas
        if ($request->filled('kelas')) {
            $query->whereHas('absensi.siswa', function($q) use ($request) {
                $q->where('kelas', $request->kelas);
            });
        }

        // 4. Urutkan yang terbaru dan gunakan pagination
        $notifikasis = $query->latest()->paginate(15);

        // 5. Kirim variabel ke View
        return view('admin.notifikasi.index', compact('notifikasis', 'kelasList'));
    }

    /**
     * Menghapus log notifikasi tertentu.
     */
    public function destroy(int $id): RedirectResponse
    {
        $notif = Notifikasi::findOrFail($id);
        $notif->delete();

        return redirect()->back()->with('success', 'Log notifikasi berhasil dihapus.');
    }
}