<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Nilai;
use App\Models\Siswa;
use App\Models\Mapel;
use App\Models\Jadwal;
use App\Models\Kelas;
use App\Models\Setting; 
use Barryvdh\DomPDF\Facade\Pdf;

class NilaiController extends Controller
{
    /**
     * HALAMAN UTAMA REKAP NILAI (LEGGER KELAS)
     */
    public function index(Request $request)
    {
        $setup = Setting::first();
        
        $kelasTerpilih = $request->get('kelas');
        $tahun_filter = $request->get('tahun_ajaran', $setup->tahun_ajaran);
        $semester_filter = $request->get('semester', $setup->semester);
        
        $data_kelas = Kelas::orderBy('nama_kelas', 'asc')->pluck('nama_kelas');
        $listTahun = Nilai::select('tahun_ajaran')->distinct()->pluck('tahun_ajaran');

        $siswas = [];
        if ($kelasTerpilih) {
            $mapels = Mapel::orderBy('nama_mapel', 'asc')->get();
            $siswas = $this->getDataRekap($kelasTerpilih, $mapels, $tahun_filter, $semester_filter);
        }

        return view('admin.nilai.index', compact(
            'siswas', 'kelasTerpilih', 'data_kelas', 
            'setup', 'tahun_filter', 'semester_filter', 'listTahun'
        ));
    }

    /**
     * PRINT REKAP NILAI SATU KELAS
     */
    public function print(Request $request)
    {
        $kelasTerpilih = $request->get('kelas');
        $tahun_filter = $request->get('tahun_ajaran');
        $semester_filter = $request->get('semester');
        $mapels = Mapel::orderBy('nama_mapel', 'asc')->get();
        
        if (!$kelasTerpilih) {
            return redirect()->back()->with('error', 'Pilih kelas terlebih dahulu');
        }

        $siswas = $this->getDataRekap($kelasTerpilih, $mapels, $tahun_filter, $semester_filter);

        return view('admin.nilai_print', compact('mapels', 'siswas', 'kelasTerpilih', 'tahun_filter', 'semester_filter'));
    }

    /**
     * CETAK RAPORT PDF (LOGIKA HARUS SAMA DENGAN SHOW)
     */
    public function cetakRaport(Request $request, int $id)
    {
        $siswa = Siswa::findOrFail($id);
        $setting = Setting::first();

        $semester_tampil = $request->get('semester', $setting->semester); 
        $tahun_tampil = $request->get('tahun_ajaran', $setting->tahun_ajaran);
        $semester_alias = ($semester_tampil == '1' || $semester_tampil == 'Ganjil') ? 'Ganjil' : 'Genap';

        $mapels = Mapel::orderBy('nama_mapel', 'asc')->get();
        $dataRaport = []; 

        foreach ($mapels as $mapel) {
            $jadwal = Jadwal::where('mapel_id', $mapel->id)
                            ->where('kelas', $siswa->kelas)
                            ->first();

            $akhir = 0;
            $narasi = '-';

            if ($jadwal) {
                $nilaiData = Nilai::where('siswa_id', $siswa->id)
                                    ->where('jadwal_id', $jadwal->id)
                                    ->where('tahun_ajaran', $tahun_tampil)
                                    ->whereIn('semester', [$semester_tampil, $semester_alias])
                                    ->get();

                $harianAsli = $nilaiData->filter(fn($n) => str_contains(strtolower($n->jenis), 'harian'))->avg('nilai') ?? 0;
                $uts    = $nilaiData->where('jenis', 'uts')->first()->nilai ?? 0;
                $uas    = $nilaiData->where('jenis', 'uas')->first()->nilai ?? 0;

                if ($harianAsli > 0 || $uts > 0 || $uas > 0) {
                    $akhir = ($harianAsli + $uts + $uas) / 3;
                    
                    if ($akhir >= 85) { $narasi = "Sangat Baik dalam memahami materi " . $mapel->nama_mapel; }
                    elseif ($akhir >= 75) { $narasi = "Baik dalam memahami materi " . $mapel->nama_mapel; }
                    else { $narasi = "Perlu bimbingan lebih lanjut dalam materi " . $mapel->nama_mapel; }
                }
            }

            $dataRaport[] = [
                'mapel' => $mapel->nama_mapel,
                'akhir' => round($akhir),
                'capaian_kompetensi' => $narasi 
            ];
        }

        $ekskul_data = Nilai::where('siswa_id', $siswa->id)
                        ->where('jenis', 'eskul')
                        ->where('tahun_ajaran', $tahun_tampil)
                        ->get();

        $wali = Jadwal::with('guru')->where('kelas', $siswa->kelas)->first();
        
        $pdf = Pdf::loadView('guru.raport_pdf', [
            'siswa'         => $siswa,
            'dataRaport'    => $dataRaport,
            'eskul'         => $ekskul_data,
            'setting'       => $setting, 
            'semester'      => $semester_tampil,    
            'tahun_ajaran'  => $tahun_tampil, 
            'nama_kepsek'   => $setting->nama_kepsek ?? '................',
            'nip_kepsek'    => $setting->nip_kepsek ?? '................',
            'nama_wali'     => $wali->guru->nama ?? '................',
            'nip'           => $wali->guru->nip ?? '................',
            'absensi'       => ['sakit' => 0, 'izin' => 0, 'alfa' => 0],
            'catatan_wali'  => "Pertahankan prestasimu dan teruslah belajar dengan giat.",
            'tgl_cetak'     => date('d F Y'),
        ]);

        return $pdf->setPaper('a4', 'portrait')->stream('Raport_'.$siswa->nama.'.pdf');
    }

    /**
     * PRIVATE FUNCTION: HITUNG REKAP (LEGGER)
     */
    private function getDataRekap(string $kelasTerpilih, $mapels, $tahun, $semester)
    {
        $siswas = Siswa::where('kelas', $kelasTerpilih)->orderBy('nama', 'asc')->get();
        $semester_alias = ($semester == '1' || $semester == 'Ganjil') ? 'Ganjil' : 'Genap';

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
                                      ->where('tahun_ajaran', $tahun)
                                      ->whereIn('semester', [$semester, $semester_alias])
                                      ->get();

                    $harianAsli = $nilaiData->filter(fn($n) => str_contains(strtolower($n->jenis), 'harian'))->avg('nilai') ?? 0;
                    $uts    = $nilaiData->where('jenis', 'uts')->first()->nilai ?? 0;
                    $uas    = $nilaiData->where('jenis', 'uas')->first()->nilai ?? 0;

                    if ($harianAsli > 0 || $uts > 0 || $uas > 0) {
                        $akhir = ($harianAsli + $uts + $uas) / 3;
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

    /**
     * HALAMAN DETAIL (SHOW) - TEMPAT PEMBUKTIAN SINKRONISASI
     */
    public function show(Request $request, int $id)
    {
        $siswa = Siswa::findOrFail($id);
        $setting = Setting::first();
        
        $tahun_tampil = $request->get('tahun', $setting->tahun_ajaran);
        $semester_tampil = $request->get('semester', $setting->semester);
        $semester_alias = ($semester_tampil == '1' || $semester_tampil == 'Ganjil') ? 'Ganjil' : 'Genap';

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
                                ->where('tahun_ajaran', $tahun_tampil)
                                ->whereIn('semester', [$semester_tampil, $semester_alias])
                                ->get();

                // LOGIKA UTAMA:
                // 1. Ambil nilai rata-rata asli (desimal) untuk hitungan
                $harianAsli = $nilaiData->filter(fn($n) => str_contains(strtolower($n->jenis), 'harian'))->avg('nilai') ?? 0;
                
                $uts = $nilaiData->where('jenis', 'uts')->first()->nilai ?? 0;
                $uas = $nilaiData->where('jenis', 'uas')->first()->nilai ?? 0;

                // 2. Nilai Akhir dihitung pakai angka asli (misal 71.5 + 75 + 80) / 3 = 75.50
                $akhir = ($harianAsli > 0 || $uts > 0 || $uas > 0) ? ($harianAsli + $uts + $uas) / 3 : 0;

                if ($akhir > 0) {
                    $totalAkhir += $akhir;
                    $jumlahMapel++;
                }

                $details[] = (object)[
                    'nama_mapel' => $mapel->nama_mapel,
                    'tugas' => round($harianAsli), // TAMPILAN JADI 72 (Bulat)
                    'uts' => $uts,
                    'uas' => $uas,
                    'nilai_akhir' => round($akhir, 2) // HASIL TETAP 75.50 (Sesuai Guru)
                ];
            }
        }

        $rataRata = $jumlahMapel > 0 ? $totalAkhir / $jumlahMapel : 0;

        return view('admin.nilai.show', compact(
            'siswa', 'details', 'rataRata', 'tahun_tampil', 'semester_tampil'
        ));
    }
}