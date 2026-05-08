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
     * Fitur Nilai (Laporan Hasil Belajar)
     */
    public function nilai(Request $request)
    {
        $user = Auth::user();
        $siswa = $user->siswa; 
        
        if (!$siswa) return redirect()->back()->with('error', 'Data profil siswa belum lengkap.');

        $setup = Setting::first();

        // 1. Ambil Filter
        $tahun_filter = $request->get('tahun_ajaran', $setup->tahun_ajaran ?? '2024/2025');
        $semester_filter = $request->get('semester', $setup->semester ?? '1');

        // 2. Terjemahkan angka ke kata
        $semester_kata = ($semester_filter == 1) ? 'Ganjil' : 'Genap';

        $listTahun = Nilai::select('tahun_ajaran')->distinct()->pluck('tahun_ajaran')->toArray();
        if (empty($listTahun)) {
            $listTahun = ['2023/2024', '2024/2025', '2025/2026', '2026/2027'];
        }

        $jadwals = Jadwal::with(['mapel', 'guru'])
                    ->where('kelas', trim($siswa->kelas))
                    ->get();

        $rekapNilai = [];

        foreach ($jadwals as $jadwal) {
            $nilaiData = Nilai::where('siswa_id', $siswa->id)
                            ->where('jadwal_id', $jadwal->id)
                            ->where('tahun_ajaran', $tahun_filter)
                            ->get();

            // 3. Filter Harian
            $semuaHarian = $nilaiData->filter(function($n) use ($semester_filter) {
                $jenis = strtolower($n->jenis);
                return in_array($jenis, ['harian', 'ulangan_bab', 'tugas', 'ulangan']) 
                       && $n->semester == $semester_filter;
            });
            $harian = $semuaHarian->count() > 0 ? $semuaHarian->avg('nilai') : 0;

            // 4. Filter UTS
            $dataUts = $nilaiData->filter(function($n) use ($semester_kata) {
                return strtolower($n->jenis) == 'uts' && $n->semester == $semester_kata;
            })->first();
            $uts = $dataUts ? $dataUts->nilai : 0;

            // 5. Filter UAS
            $dataUas = $nilaiData->filter(function($n) use ($semester_kata) {
                return strtolower($n->jenis) == 'uas' && $n->semester == $semester_kata;
            })->first();
            $uas = $dataUas ? $dataUas->nilai : 0;

            $komponen = collect([$harian, $uts, $uas])->filter(fn($v) => $v > 0);
            
            if ($komponen->count() > 0) {
                $rataRata = $komponen->sum() / $komponen->count();
                $akhir = number_format($rataRata, 1, '.', ''); 
            } else {
                $akhir = 0;
            }

            $rekapNilai[] = [
                'mapel' => $jadwal->mapel->nama_mapel ?? 'Mata Pelajaran',
                'harian' => number_format($harian, 1, '.', ''),
                'uts' => $uts,
                'uas' => $uas,
                'akhir' => $akhir,
                'predikat' => $this->hitungPredikat((float)$akhir)
            ];
        }

        return view('siswa.nilai', compact('rekapNilai', 'siswa', 'setup', 'listTahun', 'tahun_filter', 'semester_filter'));
    }

    private function hitungPredikat($nilai)
    {
        if ($nilai >= 85) return 'A';
        if ($nilai >= 75) return 'B';
        if ($nilai >= 60) return 'C';
        if ($nilai > 0) return 'D';
        return '-';
    }

    /**
     * Fitur Absensi dengan Filter Bulan & Status
     */
    public function absensi(Request $request)
    {
        $siswa = Auth::user()->siswa;
        if (!$siswa) return redirect()->back();

        // Mengambil semua data tanpa filter untuk keperluan perhitungan statistik di kartu (cards)
        $semuaAbsensi = Absensi::where('siswa_id', $siswa->id)->get();

        // Mulai Query untuk tabel (dengan pagination dan filter)
        $query = Absensi::where('siswa_id', $siswa->id)->with(['jadwal.mapel']);

        // Filter Berdasarkan Dropdown Bulan (Angka 1-12)
        if ($request->filled('bulan')) {
            $query->whereMonth('tanggal', $request->bulan);
            // Secara default membatasi pada tahun saat ini agar data tahun lalu tidak tercampur
            $query->whereYear('tanggal', date('Y'));
        }

        // Filter Berdasarkan Status (Hadir, Sakit, Izin, Alpa)
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Ambil data dengan urutan terbaru, 10 data per halaman
        $absensi = $query->latest('tanggal')
                         ->paginate(10)
                         ->withQueryString(); // Penting: agar filter tidak hilang saat klik halaman berikutnya

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
        
        // Ambil pengaturan aktif untuk Tahun Ajaran dan Semester
        $setup = Setting::first();

        if (!$siswa) {
            return view('siswa.jadwal', [
                'jadwals' => collect(),
                'setup' => $setup
            ]);
        }

        // Query Jadwal berdasarkan kelas siswa
        $jadwals = Jadwal::where('kelas', trim($siswa->kelas))
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