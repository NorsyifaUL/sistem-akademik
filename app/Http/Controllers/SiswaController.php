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

        // --- UPDATE BAGIAN STATISTIK DISINI ---
        $absensiHariIni = Absensi::where('siswa_id', $siswaId)->whereDate('tanggal', Carbon::today())->count();
        
        // Ambil semua data absensi siswa untuk menghitung distribusi status
        $semuaAbsensi = Absensi::where('siswa_id', $siswaId)->get();

        $totalHadir = $semuaAbsensi->where('status', 'Hadir')->count();
        $totalIzinSakit = $semuaAbsensi->whereIn('status', ['Izin', 'Sakit'])->count();
        $totalAlpa = $semuaAbsensi->whereIn('status', ['Alpa', 'Alfa'])->count();
        $totalAbsensi = $semuaAbsensi->count(); // Menghasilkan angka 7 (Total Sesi)
        // --------------------------------------

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

        // Pastikan variabel baru dimasukkan ke dalam compact()
        return view('siswa.dashboard', compact(
            'siswa', 'absensiHariIni', 'totalHadir', 'totalIzinSakit', 
            'totalAlpa', 'totalAbsensi', 'totalNilai', 'absensi', 
            'nilai', 'jadwalHariIni', 'hariIni', 'setting', 'notifikasis'
        ));
    }

    /**
     * Fitur Nilai (Laporan Hasil Belajar) - Versi Full Terupdate
     */
/**
     * Fitur Nilai (Laporan Hasil Belajar) - Versi Tanpa Koma
     */
    public function nilai(Request $request)
    {
        $user = Auth::user();
        $siswa = $user->siswa; 
        
        if (!$siswa) return redirect()->back()->with('error', 'Data profil siswa belum lengkap.');

        // Ambil data dari tabel settings
        $setup = Setting::first();

        // 1. Ambil Filter (Gunakan data dari settings sebagai default)
        $tahun_filter = $request->get('tahun_ajaran', $setup->tahun_ajaran);
        $semester_filter = $request->get('semester', $setup->semester);

        // Konversi semester angka ke teks
        $semester_kata = ($semester_filter == 1) ? 'Ganjil' : 'Genap';

        $listTahun = Nilai::select('tahun_ajaran')->distinct()->pluck('tahun_ajaran')->toArray();
        if (empty($listTahun)) {
            $listTahun = ['2024/2025', '2025/2026'];
        }

        // 2. Ambil Jadwal berdasarkan Kelas
        // Menggunakan nama_kelas dari objek relasi kelas
        $jadwals = Jadwal::with(['mapel', 'guru'])
                    ->where('kelas', trim($siswa->kelas->nama_kelas ?? $siswa->kelas))
                    ->get();

        $rekapNilai = [];

        foreach ($jadwals as $jadwal) {
            // Ambil data nilai berdasarkan filter tahun yang dipilih
            $nilaiData = Nilai::where('siswa_id', $siswa->id)
                                ->where('jadwal_id', $jadwal->id)
                                ->where('tahun_ajaran', $tahun_filter)
                                ->get();

            // 3. Filter Nilai Harian (Dibulatkan tanpa koma)
            $semuaHarian = $nilaiData->filter(function($n) use ($semester_filter, $semester_kata) {
                $jenis = strtolower($n->jenis);
                $cocokSem = ($n->semester == $semester_filter || $n->semester == $semester_kata);
                return in_array($jenis, ['harian', 'tugas', 'ulangan']) && $cocokSem;
            });
            // round() digunakan untuk menghilangkan koma
            $harian = $semuaHarian->count() > 0 ? round($semuaHarian->avg('nilai')) : 0;

            // 4. Filter UTS
            $dataUts = $nilaiData->filter(function($n) use ($semester_filter, $semester_kata) {
                $cocokSem = ($n->semester == $semester_filter || $n->semester == $semester_kata);
                return strtolower($n->jenis) == 'uts' && $cocokSem;
            })->first();
            $uts = $dataUts ? round($dataUts->nilai) : 0;

            // 5. Filter UAS
            $dataUas = $nilaiData->filter(function($n) use ($semester_filter, $semester_kata) {
                $cocokSem = ($n->semester == $semester_filter || $n->semester == $semester_kata);
                return strtolower($n->jenis) == 'uas' && $cocokSem;
            })->first();
            $uas = $dataUas ? round($dataUas->nilai) : 0;

            // 6. Hitung Nilai Akhir (Tanpa Koma)
            $komponen = collect([$harian, $uts, $uas])->filter(fn($v) => $v > 0);
            $akhir = $komponen->count() > 0 ? round($komponen->sum() / $komponen->count()) : 0;

            $rekapNilai[] = [
                'mapel' => $jadwal->mapel->nama_mapel ?? 'Mata Pelajaran',
                'harian' => $harian,
                'uts' => $uts,
                'uas' => $uas,
                'akhir' => $akhir,
                'predikat' => $this->hitungPredikat((float)$akhir)
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
        $jadwals = Jadwal::where('kelas', trim($siswa->kelas->nama_kelas ))
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