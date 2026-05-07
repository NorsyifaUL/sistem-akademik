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
     */
    public function dashboard()
    {
        $user = Auth::user();
        $siswa = Siswa::where('user_id', $user->id)->first();
        
        if (!$siswa) {
            abort(404, "Profil Siswa tidak ditemukan.");
        }

        $siswaId = $siswa->id;
        $hariIni = \Carbon\Carbon::now()->isoFormat('dddd'); 

        // Query Jadwal Hari Ini
        $jadwalHariIni = Jadwal::where('kelas', $siswa->kelas) 
            ->where('hari', $hariIni)
            ->with(['mapel', 'guru']) 
            ->orderBy('jam_mulai', 'asc')
            ->get();

        // Statistik
        $absensiHariIni = Absensi::where('siswa_id', $siswaId)->whereDate('tanggal', \Carbon\Carbon::today())->count();
        $totalAbsensi = Absensi::where('siswa_id', $siswaId)->count();
        $totalNilai = Nilai::where('siswa_id', $siswaId)->count();

        // Riwayat
        $absensi = Absensi::where('siswa_id', $siswaId)
                    ->with(['jadwal.mapel'])
                    ->latest('tanggal')
                    ->take(5)
                    ->get();

        $nilai = Nilai::where('siswa_id', $siswaId)->with(['jadwal.mapel'])->latest()->take(5)->get();
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
     * Fitur Nilai (Rekap per Mapel) - Versi Fix Error Column
     */
public function nilai(Request $request)
{
    $siswa = Auth::user()->siswa;
    if (!$siswa) return redirect()->back();

    $setting = Setting::first() ?? new Setting([
        'tahun_ajaran' => '2025/2026',
        'semester' => '1'
    ]);

    $tahun_filter = $request->get('tahun_ajaran', $setting->tahun_ajaran);
    $semester_filter = $request->get('semester', $setting->semester);
    $kelas_filter = $request->get('kelas', $siswa->kelas);

    // Ambil SEMUA mata pelajaran agar tetap muncul di tabel
    $mapels = Mapel::orderBy('nama_mapel', 'asc')->get();
    $rekapNilai = [];

    foreach ($mapels as $mapel) {
        // Cari ID Jadwal yang sesuai dengan Mapel dan Kelas yang difilter
        $jadwalIds = Jadwal::where('mapel_id', $mapel->id)
                            ->where('kelas', trim($kelas_filter))
                            ->pluck('id');

        // Ambil data nilai berdasarkan jadwal tersebut
        $nilaiData = Nilai::where('siswa_id', $siswa->id)
                        ->whereIn('jadwal_id', $jadwalIds)
                        ->get();

        // HITUNG NILAI (Jika tidak ada data, otomatis nilainya 0)
        $harian = $nilaiData->filter(function($n) {
            return in_array(strtolower($n->jenis), ['harian', 'ulangan_bab', 'tugas']);
        })->avg('nilai') ?? 0;

        $uts = $nilaiData->filter(fn($n) => strtolower($n->jenis) == 'uts')->first()->nilai ?? 0;
        $uas = $nilaiData->filter(fn($n) => strtolower($n->jenis) == 'uas')->first()->nilai ?? 0;

        // Rumus Akhir
        $akhir = round(($harian + $uts + $uas) / 3);

        // MASUKKAN KE ARRAY (Sekarang tanpa pengecekan count > 0 agar semua Mapel tampil)
        $rekapNilai[] = [
            'mapel' => $mapel->nama_mapel,
            'harian' => round($harian),
            'uts' => $uts,
            'uas' => $uas,
            'akhir' => $akhir,
            'predikat' => $this->hitungPredikat($akhir)
        ];
    }

    $listTahun = ['2023/2024', '2024/2025', '2025/2026'];
    $listKelas = ['X 1', 'X 2', 'XI 1', 'XI 2', 'XII IPA', 'XII IPS'];

    return view('siswa.nilai', compact(
        'rekapNilai', 'siswa', 'setting', 'listTahun', 'listKelas', 
        'tahun_filter', 'semester_filter', 'kelas_filter'
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
            ->where('isi_pesan', 'LIKE', '%Alpa%') 
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

    public function profil()
    {
        $user = auth()->user();
        $siswa = $user->siswa; // Mengambil relasi data siswa

        return view('siswa.profil', compact('user', 'siswa'));
    }
}