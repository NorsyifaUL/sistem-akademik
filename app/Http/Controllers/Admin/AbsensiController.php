<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Absensi;
use App\Models\Kelas;
use App\Models\Setting;
use App\Models\Nilai; 
use Barryvdh\DomPDF\Facade\Pdf;

class AbsensiController extends Controller
{
    /**
     * Menampilkan daftar absensi dengan filter sinkron
     */
    public function index(Request $request)
    {
        $setup = Setting::first();

        // 1. Tangkap parameter filter
        $mode = $request->get('mode', 'harian');
        $filterKelas = $request->get('kelas');
        $tanggal = $request->get('filter_date', date('Y-m-d'));
        $bulan = $request->get('filter_month', date('m'));
        $semester = $request->get('semester', $setup->semester ?? '1');
        $tahun_ajaran = $request->get('tahun_ajaran', $setup->tahun_ajaran ?? '');

        // 2. Query dasar dengan relasi
        $query = Absensi::with(['siswa.dataKelas', 'jadwal.mapel']);

        // 3. Logika Filter Waktu
        if ($mode == 'bulanan') {
            $query->whereMonth('created_at', $bulan);
            
            // Mengambil tahun dari filter_date jika ada, atau tahun saat ini
            $tahunInput = date('Y', strtotime($tanggal));
            $query->whereYear('created_at', $tahunInput);
        } else {
            $query->whereDate('created_at', $tanggal);
        }

        // 4. Logika Filter Semester (Berdasarkan Rentang Bulan)
        if ($semester == '1') {
            // Semester Ganjil: Juli s/d Desember
            $query->whereMonth('created_at', '>=', '07')
                  ->whereMonth('created_at', '<=', '12');
        } else {
            // Semester Genap: Januari s/d Juni
            $query->whereMonth('created_at', '>=', '01')
                  ->whereMonth('created_at', '<=', '06');
        }

        // 5. Logika Filter Kelas
        if ($filterKelas) {
            $query->whereHas('siswa.dataKelas', function($q) use ($filterKelas) {
                $q->where('nama_kelas', $filterKelas);
            });
        }

        $absensis = $query->latest()->get();
        
        // 6. Data Pendukung Dropdown
        $listKelas = Kelas::orderBy('nama_kelas', 'asc')->get(); 
        $listTahun = Nilai::select('tahun_ajaran')->distinct()->pluck('tahun_ajaran');
        
        if($listTahun->isEmpty() && isset($setup->tahun_ajaran)) {
            $listTahun = collect([$setup->tahun_ajaran]);
        }

        $months = [
            '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', 
            '04' => 'April', '05' => 'Mei', '06' => 'Juni', 
            '07' => 'Juli', '08' => 'Agustus', '09' => 'September', 
            '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
        ];

        return view('admin.absensi.index', [
            'absensis' => $absensis,
            'listKelas' => $listKelas,
            'listTahun' => $listTahun,
            'setup' => $setup,
            'mode' => $mode,
            'filter_date' => $tanggal,
            'filter_month' => $bulan,
            'filter_semester' => $semester,
            'filter_tahun' => $tahun_ajaran,
            'months' => $months,
            'filterKelas' => $filterKelas
        ]);
    }

    /**
     * Menampilkan form edit presensi
     * Ditambahkan untuk memperbaiki BadMethodCallException
     */
    public function edit(int $id)
    {
        $absensi = Absensi::with('siswa')->findOrFail($id);
        
        // Anda bisa mengarahkan ke halaman edit khusus 
        // atau jika menggunakan Modal, pastikan ID ini dilempar dengan benar
        return view('admin.absensi.edit', compact('absensi'));
    }

    /**
     * Memproses update data presensi
     */
    public function update(Request $request, int $id)
    {
        $request->validate([
            'status' => 'required|in:Hadir,Sakit,Izin,Alpa,H,S,I,A',
            'keterangan' => 'nullable|string|max:255'
        ]);

        $absensi = Absensi::findOrFail($id);
        $absensi->update([
            'status' => $request->status,
            'keterangan' => $request->keterangan
        ]);

        return redirect()->route('admin.absensi.index')->with('success', 'Data presensi berhasil dikoreksi.');
    }

    /**
     * Export ke PDF dengan header periode yang sesuai
     */
/**
     * Export ke PDF dengan header periode yang sesuai
     */
    public function cetak(Request $request)
    {
        $setup = Setting::first();
        $mode = $request->get('mode', 'harian');
        $filterKelas = $request->get('kelas');
        $tanggal = $request->get('filter_date', date('Y-m-d'));
        $bulan = $request->get('filter_month', date('m'));
        $tahun = date('Y', strtotime($tanggal)); // Mengambil tahun dari filter_date
        $semester = $request->get('semester', $setup->semester ?? '1');

        $query = Absensi::with(['siswa.dataKelas', 'jadwal.mapel']);

        if ($mode == 'bulanan') {
            $query->whereMonth('created_at', $bulan);
            $query->whereYear('created_at', $tahun);
        } else {
            $query->whereDate('created_at', $tanggal);
        }

        // Filter Kelas
        if ($filterKelas) {
            $query->whereHas('siswa.dataKelas', function($q) use ($filterKelas) {
                $q->where('nama_kelas', $filterKelas);
            });
        }

        $absensis = $query->latest()->get();

        // --- BAGIAN TAMBAHAN UNTUK REKAP KALENDER ---
        $bulan_teks = $this->getNamaBulan((int)$bulan);
        $jumlah_hari = \Carbon\Carbon::createFromDate($tahun, $bulan, 1)->daysInMonth;
        
        // Ambil data siswa berdasarkan kelas untuk rekap 1-31 hari
        $siswas = \App\Models\Siswa::whereHas('dataKelas', function($q) use ($filterKelas) {
                        if($filterKelas) $q->where('nama_kelas', $filterKelas);
                    })->orderBy('nama', 'asc')->get();

        $data_rekap = $siswas->map(function ($siswa) use ($bulan, $tahun, $jumlah_hari) {
            $hari = [];
            for ($tgl = 1; $tgl <= $jumlah_hari; $tgl++) {
                $absen = Absensi::where('siswa_id', $siswa->id)
                    ->whereYear('created_at', $tahun)
                    ->whereMonth('created_at', $bulan)
                    ->whereDay('created_at', $tgl)
                    ->first();

                if (!$absen) {
                    $status = '.'; 
                } else {
                    $s = strtoupper(substr($absen->status, 0, 1));
                    $status = in_array($s, ['H','S','I','A']) ? $s : '.';
                }
                $hari[$tgl] = $status;
            }

            return [
                'nama' => $siswa->nama,
                'hari' => $hari,
                'total' => [
                    'H' => collect($hari)->filter(fn($v) => $v == 'H')->count(),
                    'S' => collect($hari)->filter(fn($v) => $v == 'S')->count(),
                    'I' => collect($hari)->filter(fn($v) => $v == 'I')->count(),
                    'A' => collect($hari)->filter(fn($v) => $v == 'A')->count(),
                ]
            ];
        });
        // --- END BAGIAN TAMBAHAN ---

        // Kirim semua variabel secara terpisah agar terbaca oleh Blade
        $pdf = Pdf::loadView('admin.absensi.pdf', [
            'data' => $data_rekap,
            'bulan_teks' => $bulan_teks,
            'tahun' => $tahun,
            'jumlah_hari' => $jumlah_hari,
            'kelas' => $filterKelas ?? 'Semua Kelas',
            'setting' => $setup
        ])->setPaper('a4', 'landscape'); // Gunakan landscape agar muat 31 hari

        return $pdf->stream('Laporan_Rekap_Absensi.pdf');
    }

    private function getNamaBulan(int $bulan)
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

    public function destroy(int $id)
    {
        try {
            $absensi = \App\Models\Absensi::findOrFail($id);
            $absensi->delete();

            return redirect()->back()->with('success', 'Data absensi berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus data.');
        }
    }
}