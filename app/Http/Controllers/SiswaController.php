<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Absensi;
use App\Models\Nilai;
use App\Models\Jadwal;
use App\Models\Siswa;
use App\Models\Setting;
use App\Models\Notifikasi;
use Carbon\Carbon;

class SiswaController extends Controller
{
    /**
     * Dashboard Siswa
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
        $setting = Setting::first();

        // Query Jadwal Hari Ini
        $jadwalHariIni = Jadwal::where('kelas', $siswa->kelas) 
            ->where('hari', $hariIni)
            ->with(['mapel', 'guru']) 
            ->orderBy('jam_mulai', 'asc')
            ->get();

        // --- AMAN DARI ERROR: Statistik Berdasarkan Kolom Tanggal Berjalan ---
        $absensiHariIni = Absensi::where('siswa_id', $siswaId)
            ->whereDate('tanggal', Carbon::today())
            ->count();
        
        // Membaca rentang tahun dari text setting (Misal "2025/2026" diambil "2025")
        $tahunRentang = explode('/', $setting->tahun_ajaran ?? date('Y'))[0];

        $semuaAbsensi = Absensi::where('siswa_id', $siswaId)
            ->whereYear('tanggal', '>=', (int)$tahunRentang - 1)
            ->get();

        $totalHadir = $semuaAbsensi->where('status', 'Hadir')->count();
        $totalIzinSakit = $semuaAbsensi->whereIn('status', ['Izin', 'Sakit'])->count();
        $totalAlpa = $semuaAbsensi->whereIn('status', ['Alpa', 'Alfa'])->count();
        $totalAbsensi = $semuaAbsensi->count(); 
        // ---------------------------------------------------------------------------------

        $totalNilai = Nilai::where('siswa_id', $siswaId)->count();

        // Riwayat Singkat 5 data terakhir
        $absensi = Absensi::where('siswa_id', $siswaId)
                    ->with(['jadwal.mapel'])
                    ->latest('tanggal')
                    ->take(5)
                    ->get();

        $nilai = Nilai::where('siswa_id', $siswaId)->with(['jadwal.mapel'])->latest()->take(5)->get();

        // Notifikasi untuk dashboard
        $notifikasis = Notifikasi::where('user_id', $user->id)
                        ->orWhere('kelas', trim($siswa->kelas))
                        ->latest()
                        ->take(5)
                        ->get();

        return view('siswa.dashboard', compact(
            'siswa', 'absensiHariIni', 'totalHadir', 'totalIzinSakit', 
            'totalAlpa', 'totalAbsensi', 'totalNilai', 'absensi', 
            'nilai', 'jadwalHariIni', 'hariIni', 'setting', 'notifikasis'
        ));
    }

    /**
     * Fitur Nilai (Laporan Hasil Belajar) - Versi Tanpa Koma
     */
    public function nilai(Request $request)
    {
        $user = Auth::user();
        $siswa = $user->siswa; 
        
        if (!$siswa) return redirect()->back()->with('error', 'Data profil siswa belum lengkap.');

        $setup = Setting::first();

        // Menggunakan data dari tabel settings & nilais yang memang memiliki kolom tahun_ajaran
        $tahun_filter = $request->get('tahun_ajaran', $setup->tahun_ajaran);
        $semester_filter = $request->get('semester', $setup->semester);
        $semester_kata = ($semester_filter == 1) ? 'Ganjil' : 'Genap';

        $listTahun = Nilai::select('tahun_ajaran')->distinct()->pluck('tahun_ajaran')->toArray();
        if (empty($listTahun)) {
            $listTahun = ['2024/2025', '2025/2026'];
        }

        $jadwals = Jadwal::with(['mapel', 'guru'])
                    ->where('kelas', trim($siswa->kelas->nama_kelas ?? $siswa->kelas))
                    ->get();

        $rekapNilai = [];

        foreach ($jadwals as $jadwal) {
    $nilaiData = Nilai::where('siswa_id', $siswa->id)
                        ->where('jadwal_id', $jadwal->id)
                        ->where('tahun_ajaran', $tahun_filter)
                        ->get();

    // 1. Hitung Rata-rata Harian dengan logic yang sama (round dulu)
    // Di GuruController: $harianList->avg() di-round.
    $semuaHarian = $nilaiData->filter(function($n) use ($semester_filter, $semester_kata) {
        $jenis = strtolower($n->jenis);
        $cocokSem = ($n->semester == $semester_filter || $n->semester == $semester_kata);
        return in_array($jenis, ['harian', 'tugas', 'ulangan']) && $cocokSem;
    });
    $rataHarian = $semuaHarian->count() > 0 ? round($semuaHarian->avg('nilai')) : 0;

    // 2. Ambil nilai UTS & UAS
    $uts = $nilaiData->where('jenis', 'uts')->whereIn('semester', [$semester_filter, $semester_kata])->first()->nilai ?? 0;
    $uas = $nilaiData->where('jenis', 'uas')->whereIn('semester', [$semester_filter, $semester_kata])->first()->nilai ?? 0;

    // 3. Hitung Akhir dengan rumus yang SAMA PERSIS dengan GuruController
    // Guru: round(($rataHarian + $uts + $uas) / 3)
    $akhir = round(($rataHarian + $uts + $uas) / 3);

    $rekapNilai[] = [
        'mapel' => $jadwal->mapel->nama_mapel ?? 'Mata Pelajaran',
        'harian' => $rataHarian,
        'uts' => (int)$uts,
        'uas' => (int)$uas,
        'akhir' => (int)$akhir,
        'predikat' => $this->hitungPredikat((int)$akhir)
    ];
}

        return view('siswa.nilai', compact('rekapNilai', 'siswa', 'setup', 'listTahun', 'tahun_filter', 'semester_filter'));
    }

    private function hitungPredikat(int $nilai)
    {
        if ($nilai >= 85) return 'A';
        if ($nilai >= 75) return 'B';
        if ($nilai >= 60) return 'C';
        if ($nilai > 0) return 'D';
        return '-';
    }

    /**
     * Fitur Absensi (FIXED: Bebas Hambatan Tanpa Mencari Column tahun_ajaran di Jadwal)
     */
    public function absensi(Request $request)
    {
        $siswa = Auth::user()->siswa;
        if (!$siswa) return redirect()->back();

        $tahunSekarang = date('Y');

        // Statistik Card (Aman dari pembatasan model join)
        $semuaAbsensi = Absensi::where('siswa_id', $siswa->id)
            ->whereYear('tanggal', $tahunSekarang)
            ->get();

        // Query tabel utama berbasis data tahun masehi berjalan
        $query = Absensi::where('siswa_id', $siswa->id)
            ->with(['jadwal.mapel'])
            ->whereYear('tanggal', $tahunSekarang);

        // Filter Dropdown Bulan
        if ($request->filled('bulan')) {
            $query->whereMonth('tanggal', $request->bulan);
        }

        // Filter Dropdown Status
        if ($request->filled('status')) {
            if (in_array(strtolower($request->status), ['alpa', 'alfa'])) {
                $query->whereIn('status', ['Alpa', 'Alfa']);
            } else {
                $query->where('status', $request->status);
            }
        }

        $absensi = $query->latest('tanggal')
                         ->paginate(10)
                         ->withQueryString(); 

        return view('siswa.absensi', compact('absensi', 'semuaAbsensi'));
    }

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

    public function jadwal()
    {
        $user = Auth::user();
        $siswa = $user->siswa;
        $setup = Setting::first();

        if (!$siswa) {
            return view('siswa.jadwal', [
                'jadwals' => collect(),
                'setup' => $setup
            ]);
        }

        // Query Jadwal berdasarkan kelas siswa
        $jadwals = Jadwal::where('kelas', trim($siswa->kelas->nama_kelas ?? $siswa->kelas))
                    ->with(['mapel', 'guru']) 
                    ->orderBy('jam_mulai', 'asc')
                    ->get()
                    ->groupBy('hari'); 

        return view('siswa.jadwal', compact('jadwals', 'setup', 'siswa'));
    }

    public function profil()
    {
        $user = auth()->user();
        $siswa = $user->siswa; 

        return view('siswa.profil', compact('user', 'siswa'));
    }
}