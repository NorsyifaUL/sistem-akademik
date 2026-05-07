<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Absensi;
use App\Models\Nilai;
use App\Models\Jadwal;
use App\Models\Mapel;
use App\Models\Siswa;
use App\Models\Setting;
use App\Models\Notifikasi;
use Carbon\Carbon;

class SiswaController extends Controller
{
    /**
     * Dashboard Siswa
     * Menampilkan ringkasan jadwal hari ini, statistik absensi, dan notifikasi terbaru.
     */
    public function dashboard()
    {
        $user = Auth::user();
        $siswa = Siswa::where('user_id', $user->id)->first();
        
        if (!$siswa) {
            abort(404, "Profil Siswa tidak ditemukan.");
        }

        $siswaId = $siswa->id;
        $hariIni = Carbon::now()->isoFormat('dddd'); 

        // Query Jadwal Hari Ini
        $jadwalHariIni = Jadwal::where('kelas', $siswa->kelas) 
            ->where('hari', $hariIni)
            ->with(['mapel', 'guru']) 
            ->orderBy('jam_mulai', 'asc')
            ->get();

        // Statistik
        $absensiHariIni = Absensi::where('siswa_id', $siswaId)->whereDate('tanggal', Carbon::today())->count();
        $totalAbsensi = Absensi::where('siswa_id', $siswaId)->count();
        $totalNilai = Nilai::where('siswa_id', $siswaId)->count();

        // Riwayat Singkat
        $absensi = Absensi::where('siswa_id', $siswaId)
                    ->with(['jadwal.mapel'])
                    ->latest('tanggal')
                    ->take(5)
                    ->get();

        $nilai = Nilai::where('siswa_id', $siswaId)->with(['jadwal.mapel'])->latest()->take(5)->get();
        
        // Ambil pengaturan sistem aktif
        $setting = Setting::first();

        // Notifikasi untuk dashboard
        $notifikasis = Notifikasi::where('user_id', $user->id)
                        ->orWhere('kelas', trim($siswa->kelas))
                        ->latest()
                        ->take(5)
                        ->get();

        return view('siswa.dashboard', compact(
            'siswa', 'absensiHariIni', 'totalAbsensi', 'totalNilai', 
            'absensi', 'nilai', 'jadwalHariIni', 'hariIni', 'setting', 'notifikasis'
        ));
    }

    /**
     * Fitur Nilai (Rekap per Mapel)
     * Sinkron dengan Pengaturan Admin dan Tombol Cari.
     */
    public function nilai(Request $request)
    {
        $user = Auth::user();
        $siswa = $user->siswa;
        if (!$siswa) return redirect()->back()->with('error', 'Data siswa tidak ditemukan');

        // 1. Ambil Pengaturan Pusat dari Admin
        $setup = Setting::first();

        // 2. Logika Filter: Gunakan input 'Cari', jika kosong gunakan default Admin
        $tahun_filter = $request->get('tahun_ajaran', $setup->tahun_ajaran ?? '2024/2025');
        $semester_filter = $request->get('semester', $setup->semester ?? '1');

        // 3. Ambil daftar mata pelajaran
        $mapels = Mapel::orderBy('nama_mapel', 'asc')->get();
        $rekapNilai = [];

        foreach ($mapels as $mapel) {
            // Cari Jadwal yang sesuai kelas siswa dan mapel
            $jadwalIds = Jadwal::where('mapel_id', $mapel->id)
                                ->where('kelas', trim($siswa->kelas))
                                ->pluck('id');

            // Ambil data nilai berdasarkan filter tahun dan semester
            $nilaiData = Nilai::where('siswa_id', $siswa->id)
                            ->whereIn('jadwal_id', $jadwalIds)
                            ->where('tahun_ajaran', $tahun_filter)
                            ->where('semester', $semester_filter)
                            ->get();

            if ($nilaiData->isEmpty()) continue; // Skip jika mapel ini tidak ada nilai di periode ini

            // Hitung rata-rata harian
            $semuaHarian = $nilaiData->filter(function($n) {
                return in_array(strtolower($n->jenis), ['harian', 'ulangan_bab', 'tugas']);
            });
            $harian = $semuaHarian->count() > 0 ? $semuaHarian->avg('nilai') : 0;

            // Ambil UTS & UAS
            $uts = $nilaiData->filter(fn($n) => strtolower($n->jenis) == 'uts')->first()->nilai ?? 0;
            $uas = $nilaiData->filter(fn($n) => strtolower($n->jenis) == 'uas')->first()->nilai ?? 0;

            // Rumus Akhir Dinamis
            $komponenAktif = collect([$harian, $uts, $uas])->filter(fn($v) => $v > 0);
            $pembagi = $komponenAktif->count();
            $akhir = $pembagi > 0 ? round($komponenAktif->sum() / $pembagi) : 0;

            $rekapNilai[] = [
                'mapel' => $mapel->nama_mapel,
                'harian' => round($harian),
                'uts' => $uts,
                'uas' => $uas,
                'akhir' => $akhir,
                'predikat' => $this->hitungPredikat($akhir)
            ];
        }

        // List untuk dropdown filter
        $listTahun = ['2023/2024', '2024/2025', '2025/2026', '2026/2027'];

        return view('siswa.nilai', compact(
            'rekapNilai', 'siswa', 'setup', 'listTahun',
            'tahun_filter', 'semester_filter'
        ));
    }

    /**
     * Helper Hitung Predikat
     */
    private function hitungPredikat($nilai)
    {
        if ($nilai >= 85) return 'A';
        if ($nilai >= 75) return 'B';
        if ($nilai >= 60) return 'C';
        if ($nilai > 0) return 'D';
        return '-';
    }

    /**
     * Fitur Notifikasi
     */
    public function notifikasi()
    {
        $user = Auth::user();
        $siswa = $user->siswa;
        if (!$siswa) return redirect()->back();

        $notifikasis = Notifikasi::where('user_id', $user->id)
            ->orWhere('kelas', trim($siswa->kelas))
            ->latest()
            ->paginate(10);

        return view('siswa.notifikasi', compact('notifikasis', 'siswa'));
    }

    /**
     * Fitur Jadwal Pelajaran
     */
    public function jadwal()
    {
        $siswa = Auth::user()->siswa;
        if (!$siswa) return view('siswa.jadwal', ['jadwals' => collect()]);

        $jadwals = Jadwal::where('kelas', $siswa->kelas)
                    ->with(['mapel', 'guru']) 
                    ->orderBy('jam_mulai', 'asc')
                    ->get()
                    ->groupBy('hari'); 

        return view('siswa.jadwal', compact('jadwals'));
    }

    /**
     * Fitur Absensi
     */
    public function absensi()
    {
        $siswa = Auth::user()->siswa;
        if (!$siswa) return redirect()->back();

        $semuaAbsensi = Absensi::where('siswa_id', $siswa->id)->get();
        $absensi = Absensi::where('siswa_id', $siswa->id)
                    ->latest('tanggal')
                    ->paginate(10); 

        return view('siswa.absensi', compact('absensi', 'semuaAbsensi'));
    }

    /**
     * Tampilan Profil
     */
    public function profil()
    {
        $user = auth()->user();
        $siswa = $user->siswa; 

        return view('siswa.profil', compact('user', 'siswa'));
    }
}