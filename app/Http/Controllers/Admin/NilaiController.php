<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Nilai;
use App\Models\Siswa;
use App\Models\Mapel;
use App\Models\Jadwal;
use App\Models\Setting; 
use Barryvdh\DomPDF\Facade\Pdf; // Facade yang benar

class NilaiController extends Controller
{
    /**
     * HALAMAN UTAMA REKAP NILAI (DASHBOARD)
     */
    public function index(Request $request)
    {
        $kelasTerpilih = $request->get('kelas');
        $siswas = [];

        if ($kelasTerpilih) {
            $siswas = Siswa::where('kelas', $kelasTerpilih)
                            ->orderBy('nama', 'asc')
                            ->get();
            
            foreach ($siswas as $siswa) {
                $totalNilai = 0;
                $jumlahMapel = 0;
                
                $nilais = Nilai::where('siswa_id', $siswa->id)->get();
                $grouped = $nilais->groupBy('jadwal_id');
                
                foreach($grouped as $g) {
                    $harian = $g->whereIn('jenis', ['harian', 'ulangan_bab'])->avg('nilai') ?? 0;
                    $uts = $g->where('jenis', 'uts')->first()->nilai ?? 0;
                    $uas = $g->where('jenis', 'uas')->first()->nilai ?? 0;
                    $akhir = ($harian + $uts + $uas) / 3;
                    
                    if($akhir > 0) {
                        $totalNilai += $akhir;
                        $jumlahMapel++;
                    }
                }
                $siswa->rata_rata_akhir = $jumlahMapel > 0 ? $totalNilai / $jumlahMapel : 0;
            }
        }

        return view('admin.nilai.index', compact('siswas', 'kelasTerpilih'));
    }

    /**
     * PRINT REKAP NILAI SATU KELAS (TABEL BESAR/LEGGER)
     */
    public function print(Request $request)
    {
        $kelasTerpilih = $request->get('kelas');
        $mapels = Mapel::orderBy('nama_mapel', 'asc')->get();
        
        if (!$kelasTerpilih) {
            return redirect()->back()->with('error', 'Pilih kelas terlebih dahulu');
        }

        $siswas = $this->getDataRekap($kelasTerpilih, $mapels);

        return view('admin.nilai_print', compact('mapels', 'siswas', 'kelasTerpilih'));
    }

    /**
     * FUNGSI CETAK RAPORT INDIVIDU (OUTPUT PDF PORTRAIT)
     */
public function cetakRaport($id)
{
    $siswa = Siswa::findOrFail($id);
    $setting = Setting::first();

    $semester_aktif = $setting->semester ?? '1'; 
    $tahun_ajar_aktif = $setting->tahun_ajaran ?? '2025/2026';

    $mapels = Mapel::orderBy('nama_mapel', 'asc')->get();
    $dataRaport = []; 

    foreach ($mapels as $mapel) {
        $jadwal = Jadwal::where('mapel_id', $mapel->id)
                        ->where('kelas', $siswa->kelas)
                        ->first();

        $akhir = 0;
        $narasi = '-';

        if ($jadwal) {
            // Kita gunakan where standar. 
            // PENTING: Jika masih kosong, hapus filter semester & tahun_ajaran untuk tes.
            $nilaiData = Nilai::where('siswa_id', $siswa->id)
                                ->where('jadwal_id', $jadwal->id)
                                ->where('semester', $semester_aktif)
                                ->where('tahun_ajaran', $tahun_ajar_aktif)
                                ->get();

            $harian = $nilaiData->whereIn('jenis', ['harian', 'ulangan_bab'])->avg('nilai') ?? 0;
            $uts    = $nilaiData->where('jenis', 'uts')->first()->nilai ?? 0;
            $uas    = $nilaiData->where('jenis', 'uas')->first()->nilai ?? 0;

            if ($harian > 0 || $uts > 0 || $uas > 0) {
                $akhir = ($harian + $uts + $uas) / 3;
                
                if ($akhir >= 85) { $narasi = "Sangat Baik dalam " . $mapel->nama_mapel; }
                elseif ($akhir >= 75) { $narasi = "Baik dalam " . $mapel->nama_mapel; }
                else { $narasi = "Perlu bimbingan dalam " . $mapel->nama_mapel; }
            }
        }

        $dataRaport[] = [
            'mapel' => $mapel->nama_mapel,
            'akhir' => round($akhir),
            'capaian_kompetensi' => $narasi 
        ];
    }

    // Ambil Ekskul - Pastikan jenisnya 'eskul' (huruf kecil semua)
    $ekskul = Nilai::where('siswa_id', $siswa->id)
                    ->where('jenis', 'eskul')
                    ->where('semester', $semester_aktif)
                    ->get();

    // Mapping Wali Kelas
    $wali = Jadwal::with('guru')->where('kelas', $siswa->kelas)->first();
    $nama_wali = $wali->guru->nama ?? '................';
    $nip_wali = $wali->guru->nip ?? '................';

    $pdf = Pdf::loadView('guru.raport_pdf', [
        'siswa'         => $siswa,
        'dataRaport'    => $dataRaport,
        'ekskul'        => $ekskul, 
        'semester'      => $semester_aktif,    
        'tahun_ajaran'  => $tahun_ajar_aktif,  
        'nama_kepsek'   => $setting->nama_kepsek ?? '................',
        'nip_kepsek'    => $setting->nip_kepsek ?? '................',
        'nama_wali'     => $nama_wali,
        'nip'           => $nip_wali,
        'absensi'       => ['sakit' => 0, 'izin' => 0, 'alfa' => 0],
        'catatan_wali'  => "Terus pertahankan prestasimu.",
        'tgl_cetak'     => date('d m Y'),
    ]);

    return $pdf->setPaper('a4', 'portrait')->stream('Raport.pdf');
}

    /**
     * INPUT NILAI AKADEMIK (MAPEL)
     */
    public function create($jadwal_id)
    {
        $jadwal = Jadwal::with('mapel')->findOrFail($jadwal_id);
        $siswas = Siswa::where('kelas', $jadwal->kelas)->orderBy('nama', 'asc')->get();

        return view('admin.nilai_input', compact('jadwal', 'siswas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'jadwal_id' => 'required',
            'jenis' => 'required',
            'nilai' => 'required|array',
        ]);

        $setting = Setting::first();
        $tahun = $setting->tahun_ajaran ?? '2025/2026';
        $semester = $setting->semester ?? '1';

        foreach ($request->nilai as $siswa_id => $skor) {
            if ($skor !== null) {
                Nilai::updateOrCreate(
                    [
                        'siswa_id' => $siswa_id,
                        'jadwal_id' => $request->jadwal_id,
                        'jenis' => $request->jenis,
                    ],
                    [
                        'aspek' => $request->aspek,
                        'nilai' => $skor,
                        'semester' => $semester,
                        'tahun_ajaran' => $tahun,
                    ]
                );
            }
        }

        return redirect()->route('admin.nilai')->with('success', 'Nilai akademik berhasil disimpan!');
    }

    public function createSikap($siswa_id)
    {
        $siswa = Siswa::findOrFail($siswa_id);
        $nilaiSikap = Nilai::where('siswa_id', $siswa_id)->where('jenis', 'sikap')->get();
        return view('admin.nilai_sikap_input', compact('siswa', 'nilaiSikap'));
    }

    public function storeSikap(Request $request)
    {
        $request->validate([
            'siswa_id' => 'required',
            'aspek' => 'required',
            'predikat' => 'required',
        ]);

        $setting = Setting::first();

        Nilai::updateOrCreate(
            [
                'siswa_id' => $request->siswa_id,
                'jenis' => 'sikap',
                'aspek' => $request->aspek,
            ],
            [
                'predikat' => $request->predikat,
                'keterangan' => $request->keterangan,
                'semester' => $setting->semester ?? '1',
                'tahun_ajaran' => $setting->tahun_ajaran ?? '2025/2026'
            ]
        );

        return redirect()->back()->with('success', 'Nilai Sikap berhasil disimpan!');
    }

    public function createEskul($siswa_id)
    {
        $siswa = Siswa::findOrFail($siswa_id);
        $nilaiEskul = Nilai::where('siswa_id', $siswa_id)->where('jenis', 'eskul')->get();
        return view('admin.nilai_eskul_input', compact('siswa', 'nilaiEskul'));
    }

    public function storeEskul(Request $request)
    {
        $request->validate([
            'siswa_id' => 'required',
            'nama_ekskul' => 'required',
            'predikat' => 'required',
        ]);

        $setting = Setting::first();

        Nilai::updateOrCreate(
            [
                'siswa_id' => $request->siswa_id,
                'jenis' => 'eskul',
                'aspek' => $request->nama_ekskul,
            ],
            [
                'predikat' => $request->predikat,
                'keterangan' => $request->keterangan,
                'semester' => $setting->semester ?? '1',
                'tahun_ajaran' => $setting->tahun_ajaran ?? '2025/2026',
            ]
        );

        return redirect()->back()->with('success', 'Nilai Eskul berhasil disimpan!');
    }

    private function getDataRekap($kelasTerpilih, $mapels)
    {
        $siswas = Siswa::where('kelas', $kelasTerpilih)->orderBy('nama', 'asc')->get();

        foreach ($siswas as $siswa) {
            $totalNilaiSeluruhMapel = 0;
            $hitungMapel = 0;

            foreach ($mapels as $mapel) {
                $jadwal = Jadwal::where('mapel_id', $mapel->id)
                                ->where('kelas', $kelasTerpilih)
                                ->first();

                $akhir = 0;
                if ($jadwal) {
                    $nilaiData = Nilai::where('siswa_id', $siswa->id)
                                      ->where('jadwal_id', $jadwal->id)
                                      ->get();

                    $harian = $nilaiData->whereIn('jenis', ['harian', 'ulangan_bab'])->avg('nilai') ?? 0;
                    $uts    = $nilaiData->where('jenis', 'uts')->first()->nilai ?? 0;
                    $uas    = $nilaiData->where('jenis', 'uas')->first()->nilai ?? 0;

                    if ($harian > 0 || $uts > 0 || $uas > 0) {
                        $akhir = ($harian + $uts + $uas) / 3;
                    }
                }

                $siswa->{"nilai_mapel_" . $mapel->id} = round($akhir, 2);
                
                if($akhir > 0) {
                    $totalNilaiSeluruhMapel += $akhir;
                    $hitungMapel++;
                }
            }
            $siswa->rata_rata_akhir = $hitungMapel > 0 ? round($totalNilaiSeluruhMapel / $hitungMapel, 2) : 0;
        }

        return $siswas->sortByDesc('rata_rata_akhir')->values();
    }

    public function show($id)
    {
        $siswa = Siswa::findOrFail($id);
        $mapels = Mapel::orderBy('nama_mapel', 'asc')->get();
        
        $details = [];
        $totalAkhir = 0;
        $jumlahMapel = 0;

        foreach ($mapels as $mapel) {
            $jadwal = Jadwal::where('mapel_id', $mapel->id)
                            ->where('kelas', $siswa->kelas)
                            ->first();

            if ($jadwal) {
                $nilaiData = Nilai::where('siswa_id', $id)
                                ->where('jadwal_id', $jadwal->id)
                                ->get();

                $tugas = $nilaiData->whereIn('jenis', ['harian', 'ulangan_bab'])->avg('nilai') ?? 0;
                $uts = $nilaiData->where('jenis', 'uts')->first()->nilai ?? 0;
                $uas = $nilaiData->where('jenis', 'uas')->first()->nilai ?? 0;
                $akhir = ($tugas > 0 || $uts > 0 || $uas > 0) ? ($tugas + $uts + $uas) / 3 : 0;

                if ($akhir > 0) {
                    $totalAkhir += $akhir;
                    $jumlahMapel++;
                }

                $details[] = (object)[
                    'nama_mapel' => $mapel->nama_mapel,
                    'tugas' => round($tugas, 1),
                    'uts' => $uts,
                    'uas' => $uas,
                    'nilai_akhir' => round($akhir, 2)
                ];
            }
        }

        $rataRata = $jumlahMapel > 0 ? $totalAkhir / $jumlahMapel : 0;

        return view('admin.nilai.show', compact('siswa', 'details', 'rataRata'));
    }
}