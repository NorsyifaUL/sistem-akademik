<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Nilai;
use App\Models\Siswa;
use App\Models\Jadwal;
use App\Models\Kelas;
use App\Models\Setting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class NilaiController extends Controller
{
    /**
     * HALAMAN UTAMA: DAFTAR MAPEL
     */
    public function index()
    {
        $user = Auth::user();
        if (!$user->guru) return redirect()->back()->with('error', 'Data Guru tidak ditemukan.');

        $jadwals = Jadwal::with(['mapel'])
            ->where('guru_id', $user->guru->id)
            ->get();

        return view('guru.nilai.index', compact('jadwals'));
    }

    /**
     * FILTER & TAMPILAN KOLEKTIF
     */
    public function rekap(Request $request, $jadwal_id = null)
    {
        $guru = Auth::user()->guru;
        $setting = Setting::first();
        $jadwalId = $jadwal_id ?? $request->jadwal_id;
        
        $jenisNilai = strtolower($request->jenis_nilai ?? 'harian');
        $semesterAktif = trim($setting->semester ?? 'Ganjil');
        $tahunAjaranAktif = $setting->tahun_ajaran; 

        $jadwals = Jadwal::with(['mapel', 'kelasRelation']) // Pastikan relasi ke kelas dimuat
        ->where('guru_id', $guru->id)
        ->join('kelas', 'jadwals.kelas_id', '=', 'kelas.id') // Join ke tabel kelas
        ->orderBy('kelas', 'asc') // Urutkan berdasarkan nama kelas
        ->orderBy('mapel_id', 'asc')
        ->get();
        $siswaData = collect(); 
        $jadwalTerpilih = null;

        if ($jadwalId) {
            $jadwalTerpilih = Jadwal::with(['mapel'])->where('guru_id', $guru->id)->findOrFail($jadwalId);
            
            $siswas = Siswa::whereHas('kelas', function($q) use ($jadwalTerpilih) {
                $namaKelas = $jadwalTerpilih->class ?? $jadwalTerpilih->kelas;
                $q->where('nama_kelas', $namaKelas);
            })->orderBy('nama', 'asc')->get();

            $siswaData = $siswas->map(function($s) use ($jadwalId, $semesterAktif, $jenisNilai, $tahunAjaranAktif) {
                $nilais = Nilai::where('siswa_id', $s->id)
                               ->where('jadwal_id', $jadwalId)
                               ->where('semester', $semesterAktif)
                               ->get();

                // 1. Ambil Nilai Harian
                $s->uh1 = $nilais->whereIn('aspek', ['UH1', 'uh1'])->first()->nilai ?? null;
                $s->uh2 = $nilais->whereIn('aspek', ['UH2', 'uh2'])->first()->nilai ?? null;
                $s->uh3 = $nilais->whereIn('aspek', ['UH3', 'uh3'])->first()->nilai ?? null;
                $s->uh4 = $nilais->whereIn('aspek', ['UH4', 'uh4'])->first()->nilai ?? null;

                $vals = collect([$s->uh1, $s->uh2, $s->uh3, $s->uh4])->filter(fn($v) => !is_null($v) && $v !== '');
                $s->harian = $vals->count() > 0 ? round($vals->avg()) : 0;

                // 2. Ambil UTS & UAS
                $s->uts = $nilais->filter(fn($n) => strtolower($n->jenis) == 'uts')->first()->nilai ?? 0;
                $s->uas = $nilais->filter(fn($n) => strtolower($n->jenis) == 'uas')->first()->nilai ?? 0;

                // 3. Hitung Nilai Akhir Otomatis (Calculated)
                $s->nilai_akhir_calculated = round(($s->harian + $s->uts + $s->uas) / 3);

                // 4. Logika Pengambilan Data Berdasarkan Mode
                if ($jenisNilai == 'rekap') {
                    // Cari data dengan jenis rekap
                    $n = $nilais->filter(fn($item) => strtolower($item->jenis) == 'rekap')->first();
                    $s->nilai_existing = $n->nilai ?? $s->nilai_akhir_calculated;
                    $s->deskripsi_existing = $n->keterangan ?? null;
                } else {
                    $n = $nilais->filter(fn($item) => strtolower($item->jenis) == $jenisNilai)->first();
                    $s->nilai_existing = $n->nilai ?? null;
                    $s->deskripsi_existing = $n->keterangan ?? null;
                }

                return $s;
            });
        }

        return view('guru.nilai.siswa_list', [
            'jadwals' => $jadwals,
            'jadwalTerpilih' => $jadwalTerpilih,
            'siswaData' => $siswaData,
            'setting' => $setting,
            'semesterAktif' => $semesterAktif,
            'jadwalId' => $jadwalId,
            'jenisNilai' => $jenisNilai,
            'jenis' => $jenisNilai 
        ]);
    }

    /**
     * SIMPAN NILAI KOLEKTIF (MASS UPDATE)
     */
    public function storeKolektif(Request $request)
    {
        $request->validate([
            'jadwal_id' => 'required',
            'jenis' => 'required',
            'nilai' => 'required|array'
        ]);

        $guru_id = Auth::user()->guru->id;
        $setting = Setting::first();
        $semester = trim($setting->semester ?? 'Ganjil');
        $tahun = trim($setting->tahun_ajaran ?? '2025/2026');
        $jadwal = Jadwal::findOrFail($request->jadwal_id);

        foreach ($request->nilai as $siswa_id => $data) {
            $jenisHeader = strtolower($request->jenis);

            if ($jenisHeader == 'harian') {
                foreach (['uh1', 'uh2', 'uh3', 'uh4'] as $aspek) {
                    if (isset($data[$aspek]) && $data[$aspek] !== '') {
                        DB::table('nilais')->updateOrInsert(
                            [
                                'jadwal_id'    => $request->jadwal_id,
                                'siswa_id'     => $siswa_id,
                                'aspek'        => strtoupper($aspek), 
                                'semester'     => $semester,
                            ],
                            [
                                'jenis'         => 'harian',
                                'mapel_id'      => $jadwal->mapel_id,
                                'guru_id'       => $guru_id,
                                'tahun_ajaran' => $tahun,
                                'nilai'         => $data[$aspek],
                                'updated_at'    => now(),
                                'created_at'    => now(),
                            ]
                        );
                    }
                }
            } else {
                // UNTUK UTS, UAS, DAN REKAP
                $skor = $data['angka'] ?? 0;
                
                // Logika khusus untuk REKAP agar Aspek menjadi 'Nilai Akhir'
                $aspekLabel = ($jenisHeader == 'rekap') ? 'Nilai Akhir' : strtoupper($jenisHeader);

                DB::table('nilais')->updateOrInsert(
                    [
                        'jadwal_id'    => $request->jadwal_id,
                        'siswa_id'     => $siswa_id,
                        'jenis'        => $jenisHeader, 
                        'semester'     => $semester,
                    ],
                    [
                        'aspek'        => $aspekLabel, // Menggunakan label 'Nilai Akhir' jika rekap
                        'mapel_id'      => $jadwal->mapel_id,
                        'guru_id'       => $guru_id,
                        'tahun_ajaran' => $tahun,
                        'nilai'         => $skor,
                        'keterangan'    => $data['deskripsi'] ?? null, // Deskripsi disimpan di kolom keterangan
                        'updated_at'    => now(),
                        'created_at'    => now(),
                    ]
                );
            }
        }

        return redirect()->back()->with('success', 'Data nilai dan capaian berhasil diperbarui!');
    }

    // Fungsi tambahan tetap dipertahankan
    public function siswa(int $jadwal_id)
{
    $user = Auth::user();
    $setting = Setting::first();
    $semesterAktif = $setting->semester ?? 'Ganjil';

    // 1. Ambil data jadwal yang dipilih
    $jadwal = Jadwal::with(['mapel'])->where('guru_id', $user->guru->id)->findOrFail($jadwal_id);
    
    // 2. Ambil daftar jadwal untuk dropdown (yang sudah diurutkan)
    $jadwals = Jadwal::where('guru_id', $user->guru->id)
        ->join('kelas', 'jadwals.kelas_id', '=', 'kelas.id')
        ->orderBy('kelas', 'asc')
        ->orderBy('mapel_id', 'asc')
        ->select('jadwals.*')
        ->get();

    // 3. Ambil data siswa
    $kelasInfo = Kelas::where('nama_kelas', $jadwal->kelas)->first();
    if ($kelasInfo) {
        $siswas = Siswa::where('kelas_id', $kelasInfo->id)
            ->with(['nilais' => function($q) use ($jadwal_id, $semesterAktif) {
                $q->where('jadwal_id', $jadwal_id)
                  ->where('semester', $semesterAktif);
            }])
            ->orderBy('nama', 'asc')
            ->get();
    } else {
        $siswas = collect();
    }

    return view('guru.nilai.siswa_list', compact('jadwals', 'jadwal', 'siswas', 'setting'));
}

    public function input(int $jadwal_id, int $siswa_id)
    {
        $guru_id = Auth::user()->guru->id;
        $jadwal = Jadwal::with(['mapel'])->where('guru_id', $guru_id)->findOrFail($jadwal_id);
        $siswa = Siswa::findOrFail($siswa_id);
        $nilai_exist = Nilai::where('jadwal_id', $jadwal_id)->where('siswa_id', $siswa_id)->get();

        return view('guru.nilai.input_nilai', compact('jadwal', 'siswa', 'nilai_exist'));
    }

    public function store(Request $request, int $jadwal_id, int $siswa_id)
    {
        $request->validate([
            'jenis' => 'required|in:harian,uts,uas',
            'nilai' => 'required|numeric|min:0|max:100',
        ]);

        $guru_id = Auth::user()->guru->id;
        $setting = Setting::first();
        $semester = $setting->semester ?? 'Ganjil';
        $tahun = $setting->tahun_ajaran ?? '2025/2026';

        Nilai::updateOrCreate(
            [
                'jadwal_id'    => $jadwal_id, 
                'siswa_id'     => $siswa_id, 
                'jenis'        => $request->jenis,
                'aspek'        => strtoupper($request->jenis),
                'semester'     => $semester,
                'tahun_ajaran' => $tahun
            ],
            [
                'nilai'   => $request->nilai,
                'guru_id' => $guru_id
            ]
        );

        return redirect()->route('guru.nilai.siswa', $jadwal_id)->with('success', 'Nilai berhasil disimpan!');
    }
}