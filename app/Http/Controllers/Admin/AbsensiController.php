<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Absensi;
use App\Models\Kelas;
use App\Models\Setting; // Pastikan model Setting sudah ada
use Barryvdh\DomPDF\Facade\Pdf;

class AbsensiController extends Controller
{
    public function index(Request $request)
    {
        // 1. Tangkap parameter filter dari request
        $mode = $request->get('mode', 'harian');
        $filterKelas = $request->get('kelas');
        $tanggal = $request->get('filter_date', date('Y-m-d'));
        $bulan = $request->get('filter_month', date('m'));

        // 2. Query dasar dengan relasi
        $query = Absensi::with(['siswa.dataKelas', 'jadwal.mapel']);

        // 3. Logika Filter Waktu (Harian vs Bulanan)
        if ($mode == 'bulanan') {
            $query->whereMonth('created_at', $bulan)
                  ->whereYear('created_at', date('Y'));
        } else {
            $query->whereDate('created_at', $tanggal);
        }

        // 4. Logika Filter Kelas
        if ($filterKelas) {
            $query->whereHas('siswa.dataKelas', function($q) use ($filterKelas) {
                $q->where('nama_kelas', $filterKelas);
            });
        }

        $absensis = $query->latest()->get();
        
        // 5. Ambil list kelas untuk dropdown
        $listKelas = Kelas::orderBy('nama_kelas', 'asc')->get(); 

        // 6. Kirim semua variabel ke view
        return view('admin.absensi.index', [
            'absensis' => $absensis,
            'listKelas' => $listKelas,
            'mode' => $mode,
            'filter_date' => $tanggal,
            'filter_month' => $bulan,
            'filterKelas' => $filterKelas
        ]);
    }

    /**
     * Fungsi untuk Export ke PDF dengan data dari Tabel Settings
     */
    public function cetak(Request $request)
    {
        $mode = $request->get('mode', 'harian');
        $filterKelas = $request->get('kelas');
        $tanggal = $request->get('filter_date', date('Y-m-d'));
        $bulan = $request->get('filter_month', date('m'));

        // Ambil Data Absensi
        $query = Absensi::with(['siswa.dataKelas', 'jadwal.mapel']);

        if ($mode == 'bulanan') {
            $query->whereMonth('created_at', $bulan)
                ->whereYear('created_at', date('Y'));
        } else {
            $query->whereDate('created_at', $tanggal);
        }

        if ($filterKelas) {
            $query->whereHas('siswa.dataKelas', function($q) use ($filterKelas) {
                $q->where('nama_kelas', $filterKelas);
            });
        }

        $absensis = $query->latest()->get();

        // Ambil Data Pengaturan Aplikasi/Sekolah
        $settings = Setting::first(); 

        // Data meta untuk header di PDF
        $info = [
            'mode'           => $mode,
            'tanggal'        => $tanggal,
            'bulan'          => $this->getNamaBulan($bulan),
            'kelas'          => $filterKelas ?? 'Semua Kelas',
            'total'          => $absensis->count(),
            
            // Data dinamis disesuaikan dengan kolom di database (image_f363df.jpg)
            'nama_sekolah'   => $settings->nama_sekolah ?? 'NAMA SEKOLAH BELUM DIATUR',
            'alamat'         => $settings->alamat ?? 'Alamat belum diatur di menu settings.',
            'logo'           => $settings->logo ?? null,
            
            // Perubahan di sini agar sinkron dengan tabel 'settings' Anda
            'kepala_sekolah' => $settings->nama_kepsek ?? '...........................',
            'nip'            => $settings->nip_kepsek ?? '...........................'
        ];

        $pdf = Pdf::loadView('admin.absensi.pdf', compact('absensis', 'info'))
                ->setPaper('a4', 'portrait');

        return $pdf->stream('Laporan_Absensi_' . date('YmdHis') . '.pdf');
    }

    /**
     * Fungsi pembantu untuk konversi angka bulan ke nama Indonesia
     */
    private function getNamaBulan($bulan)
    {
        $bulanStr = str_pad($bulan, 2, '0', STR_PAD_LEFT);
        $daftarBulan = [
            '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', 
            '04' => 'April', '05' => 'Mei', '06' => 'Juni', 
            '07' => 'Juli', '08' => 'Agustus', '09' => 'September', 
            '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
        ];
        return $daftarBulan[$bulanStr] ?? $bulan;
    }
}