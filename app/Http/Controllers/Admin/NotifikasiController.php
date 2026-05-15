<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notifikasi;
use App\Models\Kelas;
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
        // 1. Ambil daftar kelas untuk dropdown filter
        $kelasList = Kelas::select('nama_kelas')
                        ->orderBy('nama_kelas', 'asc')
                        ->get();

        // 2. Query Notifikasi (Eager Load absensi, siswa, dan dataKelas)
        $query = Notifikasi::with(['absensi.siswa.dataKelas'])
                    ->where('isi_pesan', 'LIKE', '%ALPA%')
                    ->where('isi_pesan', 'NOT LIKE', '%Selamat Datang%');

        // 3. Logika Filter Kelas
        if ($request->filled('kelas')) {
            $query->whereHas('absensi.siswa', function($q) use ($request) {
                $q->where('kelas', $request->kelas);
            });
        }

        // 4. Urutkan dari yang terbaru dan gunakan pagination
        $notifikasis = $query->latest()->paginate(15);

        return view('admin.notifikasi.index', compact('notifikasis', 'kelasList'));
    }

    /**
     * Menghapus log notifikasi tertentu.
     */
    public function destroy(int $id): RedirectResponse
    {
        try {
            $notifikasi = Notifikasi::findOrFail($id);
            $notifikasi->delete();

            return redirect()->back()->with('success', 'Log notifikasi berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus log: ' . $e->getMessage());
        }
    }
}