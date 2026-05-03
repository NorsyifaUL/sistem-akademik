<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Nilai;
use App\Models\Siswa;
use App\Models\Jadwal;
use App\Models\Setting;
use Illuminate\Support\Facades\Auth;

class NilaiController extends Controller
{
    /**
     * TAMPILKAN DAFTAR MATA PELAJARAN YANG DIAJAR GURU
     */
    public function index()
    {
        $user = Auth::user();
        
        // Pastikan relasi guru ada
        if (!$user->guru) {
            return redirect()->back()->with('error', 'Data Guru tidak ditemukan untuk akun ini.');
        }

        $jadwals = Jadwal::with(['mapel'])
            ->where('guru_id', $user->guru->id)
            ->get();

        return view('guru.nilai.index', compact('jadwals'));
    }

    /**
     * TAMPILKAN DAFTAR SISWA BERDASARKAN JADWAL/KELAS
     */
    public function siswa($jadwal_id)
    {
        $guru_id = Auth::user()->guru->id;
        
        // Pastikan guru hanya bisa melihat jadwal miliknya
        $jadwal = Jadwal::with(['mapel'])->where('guru_id', $guru_id)->findOrFail($jadwal_id);

        $siswa = Siswa::where('kelas', $jadwal->kelas)
            ->with(['nilais' => function($q) use ($jadwal_id) {
                $q->where('jadwal_id', $jadwal_id);
            }])
            ->orderBy('nama', 'asc')
            ->get();

        return view('guru.jadwal.siswa', compact('jadwal', 'siswa'));
    }

    /**
     * FORM INPUT NILAI INDIVIDU
     */
    public function input($jadwal_id, $siswa_id)
    {
        $guru_id = Auth::user()->guru->id;
        $jadwal = Jadwal::with('mapel')->where('guru_id', $guru_id)->findOrFail($jadwal_id);
        $siswa = Siswa::findOrFail($siswa_id);

        // Ambil nilai yang sudah ada (jika ingin edit)
        $nilai_exist = Nilai::where('jadwal_id', $jadwal_id)->where('siswa_id', $siswa_id)->get();

        return view('guru.nilai.input_nilai', compact('jadwal', 'siswa', 'nilai_exist'));
    }

    /**
     * SIMPAN ATAU UPDATE NILAI
     */
    public function store(Request $request, $jadwal_id, $siswa_id)
    {
        $request->validate([
            'jenis' => 'required|in:harian,uts,uas',
            'nilai' => 'required|numeric|min:0|max:100',
        ]);

        $guru_id = Auth::user()->guru->id;
        // Validasi kepemilikan jadwal
        $jadwal = Jadwal::where('guru_id', $guru_id)->findOrFail($jadwal_id);

        // Ambil setting aktif (Tahun ajaran & Semester)
        $setting = Setting::first();
        $semester = $setting->semester ?? '1';
        $tahun = $setting->tahun_ajaran ?? '2025/2026';

        Nilai::updateOrCreate(
            [
                'jadwal_id' => $jadwal->id, 
                'siswa_id'  => $siswa_id, 
                'jenis'     => $request->jenis,
                'semester'  => $semester,
                'tahun_ajaran' => $tahun
            ],
            [
                'nilai'     => $request->nilai,
                'guru_id'   => $guru_id
            ]
        );

        return redirect()->route('guru.nilai.siswa', $jadwal_id)
            ->with('success', 'Nilai ' . strtoupper($request->jenis) . ' untuk ' . $request->nama_siswa . ' berhasil disimpan!');
    }

    /**
     * FITUR REKAP NILAI & RANKING PER MATA PELAJARAN
     */
    public function rekap(Request $request)
    {
        $guru = Auth::user()->guru;
        $jadwalId = $request->jadwal_id;

        // Dropdown filter jadwal
        $jadwals = Jadwal::with('mapel')->where('guru_id', $guru->id)->get();

        $rekap = [];

        if ($jadwalId) {
            $jadwal = Jadwal::where('guru_id', $guru->id)->findOrFail($jadwalId);
            $siswas = Siswa::where('kelas', $jadwal->kelas)->orderBy('nama', 'asc')->get();

            foreach ($siswas as $siswa) {
                // Ambil nilai
                $nilaiData = Nilai::where('jadwal_id', $jadwalId)->where('siswa_id', $siswa->id)->get();
                
                $harian = $nilaiData->where('jenis', 'harian')->avg('nilai') ?? 0;
                $uts    = $nilaiData->where('jenis', 'uts')->first()->nilai ?? 0;
                $uas    = $nilaiData->where('jenis', 'uas')->first()->nilai ?? 0;

                // Logika Perhitungan: Samakan dengan NilaiController Admin (Rata-rata murni)
                // Jika ingin menggunakan bobot (0.4, 0.3, 0.3) pastikan di Admin juga diubah hal yang sama
                $nilaiAkhir = 0;
                if ($harian > 0 || $uts > 0 || $uas > 0) {
                    $nilaiAkhir = ($harian + $uts + $uas) / 3;
                }

                // Tentukan Predikat
                if ($nilaiAkhir >= 90) $predikat = 'A';
                elseif ($nilaiAkhir >= 80) $predikat = 'B';
                elseif ($nilaiAkhir >= 70) $predikat = 'C';
                else $predikat = 'D';

                $rekap[] = [
                    'nama_siswa' => $siswa->nama,
                    'rata_harian' => round($harian, 2),
                    'uts' => $uts,
                    'uas' => $uas,
                    'nilai_akhir' => round($nilaiAkhir, 2),
                    'predikat' => $predikat
                ];
            }

            // Urutkan untuk Ranking
            $rekap = collect($rekap)->sortByDesc('nilai_akhir')->values()->all();

            foreach ($rekap as $index => $item) {
                $rekap[$index]['ranking'] = $index + 1;
            }
        }

        return view('guru.nilai.rekap', compact('jadwals', 'rekap', 'jadwalId'));
    }
}