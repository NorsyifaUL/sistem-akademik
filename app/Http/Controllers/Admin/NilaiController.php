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
use App\Models\Absensi;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class NilaiController extends Controller
{
    /**
     * HALAMAN UTAMA REKAP NILAI (LEGGER KELAS)
     */
    public function index(Request $request)
    {
        $setup = Setting::first() ?? new Setting();
        
        $kelasTerpilih = $request->get('kelas_id'); 
        $tahun_filter = $request->get('tahun_ajaran', $setup->tahun_ajaran);
        $semester_filter = $request->get('semester', $setup->semester);
        
        $data_kelas = Kelas::orderBy('nama_kelas', 'asc')->get();
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
     * PRINT REKAP NILAI SATU KELAS (LEGGER)
     */
    public function print(Request $request)
    {
        $kelasId = $request->get('kelas_id');
        $tahun_filter = $request->get('tahun_ajaran');
        $semester_filter = $request->get('semester');
        $mapels = Mapel::orderBy('nama_mapel', 'asc')->get();
        
        if (!$kelasId) {
            return redirect()->back()->with('error', 'Pilih kelas terlebih dahulu');
        }

        $kelas = Kelas::findOrFail($kelasId);
        $siswas = $this->getDataRekap($kelasId, $mapels, $tahun_filter, $semester_filter);

        return view('admin.nilai_print', [
            'mapels' => $mapels,
            'siswas' => $siswas,
            'kelasTerpilih' => $kelas->nama_kelas,
            'tahun_filter' => $tahun_filter,
            'semester_filter' => $semester_filter
        ]);
    }

    /**
     * CETAK RAPORT PDF
     */
    public function cetakRaport(Request $request, int $id)
    {
        $siswa = Siswa::with('dataKelas.guru')->findOrFail($id);
        $setting = Setting::first() ?? new Setting();

        $semester_tampil = $request->get('semester', $setting->semester); 
        $tahun_tampil = $request->get('tahun_ajaran', $setting->tahun_ajaran);
        $semester_db = (in_array(strtolower($semester_tampil), ['1', 'ganjil'])) ? 'Ganjil' : 'Genap';

        // Menggunakan kolom 'kelas' karena struktur tabel jadwals menggunakan string nama kelas
        $namaKelasSiswa = $siswa->dataKelas->nama_kelas ?? $siswa->kelas;

        $jadwals = Jadwal::with('mapel')
                        ->where('kelas', $namaKelasSiswa)
                        ->get();

        $dataRaport = []; 

        foreach ($jadwals as $jadwal) {
            $mapel = $jadwal->mapel;
            if (!$mapel) continue;

            $akhir = 0;
            $narasi = 'Belum ada data nilai.';

            $nilaiData = Nilai::where('siswa_id', $siswa->id)
                                ->where('jadwal_id', $jadwal->id)
                                ->where('tahun_ajaran', $tahun_tampil)
                                ->where('semester', $semester_db)
                                ->get();

            $dataRekap = $nilaiData->where('jenis', 'rekap')->first();

            if ($dataRekap) {
                $akhir = $dataRekap->nilai;
                $narasi = $dataRekap->keterangan ?? "Menunjukkan penguasaan kompetensi yang baik pada mata pelajaran " . $mapel->nama_mapel;
            } else {
                $harianAsli = $nilaiData->where('jenis', 'harian')->avg('nilai') ?? 0;
                $uts = $nilaiData->where('jenis', 'uts')->first()->nilai ?? 0;
                $uas = $nilaiData->where('jenis', 'uas')->first()->nilai ?? 0;

                if ($harianAsli > 0 || $uts > 0 || $uas > 0) {
                    $akhir = ($harianAsli + $uts + $uas) / 3;
                    
                    if ($akhir >= 85) { 
                        $narasi = "Menunjukkan penguasaan kompetensi yang sangat baik dalam memahami materi " . $mapel->nama_mapel; 
                    } elseif ($akhir >= 75) { 
                        $narasi = "Menunjukkan penguasaan kompetensi yang baik dalam memahami materi " . $mapel->nama_mapel; 
                    } else { 
                        $narasi = "Perlu bimbingan dan peningkatan dalam memahami materi " . $mapel->nama_mapel; 
                    }
                }
            }

            $dataRaport[] = [
                'mapel' => $mapel->nama_mapel,
                'akhir' => round($akhir),
                'capaian_kompetensi' => $narasi 
            ];
        }

        // Data Ekstrakurikuler
        $ekskul_data = Nilai::where('siswa_id', $siswa->id)
                            ->whereIn('jenis', ['eskul', 'ekstra'])
                            ->where('semester', $semester_db)
                            ->get()
                            ->map(function($item) {
                                return [
                                    'kegiatan'   => $item->aspek ?? 'Ekstrakurikuler',
                                    'nilai'      => $item->nilai ?? $item->predikat ?? '-',
                                    'keterangan' => $item->keterangan ?? '-'
                                ];
                            })->values();

        // Data Sikap untuk Catatan Wali Kelas
        $sikap = Nilai::where('siswa_id', $siswa->id)
                      ->where('jenis', 'sikap')
                      ->where('semester', $semester_db)
                      ->first();

        $nama_wali = $siswa->dataKelas->guru->nama ?? '................';
        $nip_wali = $siswa->dataKelas->guru->nip ?? '................';
        
        $pdf = Pdf::loadView('guru.raport_pdf', [
            'siswa'         => $siswa,
            'dataRaport'    => $dataRaport,
            'eskul'         => $ekskul_data,
            'setting'       => $setting, 
            'nama_kepsek'   => $setting->nama_kepsek,
            'nip_kepsek'    => $setting->nip_kepsek,
            'nama_wali'     => $nama_wali,
            'nip'           => $nip_wali,
            'absensi'       => [
                'sakit' => Absensi::where('siswa_id', $id)->whereIn('status', ['S', 's', 'Sakit', 'sakit'])->count(),
                'izin'  => Absensi::where('siswa_id', $id)->whereIn('status', ['I', 'i', 'Izin', 'izin'])->count(),
                'alpa'  => Absensi::where('siswa_id', $id)->whereIn('status', ['A', 'a', 'Alpa', 'alpa', 'Tanpa Keterangan', 'TK', 'tk'])->count()
            ],
            'catatan_wali'  => $sikap->keterangan ?? "Pertahankan prestasimu dan teruslah belajar dengan giat.",
            'tgl_cetak'     => now()->translatedFormat('d F Y'),
            'semester'      => $semester_db,
            'tahun_ajaran'  => $tahun_tampil
        ]);

        return $pdf->setPaper('a4', 'portrait')->stream('Raport_'.$siswa->nama.'.pdf');
    }

    /**
     * PRIVATE FUNCTION: HITUNG REKAP (LEGGER)
     */
    private function getDataRekap(int $kelasId, $mapels, $tahun, $semester)
    {
        $kelas = Kelas::findOrFail($kelasId);
        $siswas = Siswa::where('kelas_id', $kelasId)->orderBy('nama', 'asc')->get();
        $semester_db = (in_array(strtolower($semester), ['1', 'ganjil'])) ? 'Ganjil' : 'Genap';

        foreach ($siswas as $siswa) {
            $totalNilaiSeluruhMapel = 0;
            $hitungMapel = 0;

            foreach ($mapels as $mapel) {
                $jadwal = Jadwal::where('mapel_id', $mapel->id)
                                ->where('kelas', $kelas->nama_kelas)
                                ->first();

                $akhir = 0;
                if ($jadwal) {
                    $nilaiData = Nilai::where('siswa_id', $siswa->id)
                                      ->where('jadwal_id', $jadwal->id)
                                      ->where('tahun_ajaran', $tahun)
                                      ->where('semester', $semester_db)
                                      ->get();

                    if ($nilaiData->isNotEmpty()) {
                        $rekap = $nilaiData->where('jenis', 'rekap')->first();
                        if ($rekap) {
                            $akhir = $rekap->nilai;
                        } else {
                            $harianAsli = $nilaiData->where('jenis', 'harian')->avg('nilai') ?? 0;
                            $uts = $nilaiData->where('jenis', 'uts')->first()->nilai ?? 0;
                            $uas = $nilaiData->where('jenis', 'uas')->first()->nilai ?? 0;
                            $akhir = ($harianAsli + $uts + $uas) / 3;
                        }
                        
                        $totalNilaiSeluruhMapel += $akhir;
                        $hitungMapel++;
                    }
                }
                $siswa->{"nilai_mapel_" . $mapel->id} = round($akhir);
            }
            $siswa->rata_rata_akhir = $hitungMapel > 0 ? round($totalNilaiSeluruhMapel / $hitungMapel) : 0;
        }

        return $siswas->sortByDesc('rata_rata_akhir')->values();
    }

    /**
     * HALAMAN DETAIL (SHOW)
     */
    public function show(Request $request, int $id)
    {
        $siswa = Siswa::findOrFail($id);
        $setting = Setting::first() ?? new Setting();
        
        $tahun_tampil = $request->get('tahun', $setting->tahun_ajaran);
        $semester_tampil = $request->get('semester', $setting->semester);
        $semester_db = (in_array(strtolower($semester_tampil), ['1', 'ganjil'])) ? 'Ganjil' : 'Genap';

        $namaKelasSiswa = $siswa->dataKelas->nama_kelas ?? $siswa->kelas;

        $jadwals = Jadwal::with('mapel')
                        ->where('kelas', $namaKelasSiswa)
                        ->get();

        $details = [];
        $totalAkhir = 0;
        $jumlahMapel = 0;

        foreach ($jadwals as $jadwal) {
            $mapel = $jadwal->mapel;
            if (!$mapel) continue;

            $nilaiData = Nilai::where('siswa_id', $id)
                            ->where('jadwal_id', $jadwal->id)
                            ->where('tahun_ajaran', $tahun_tampil)
                            ->where('semester', $semester_db)
                            ->get();

            $rekap = $nilaiData->where('jenis', 'rekap')->first();
            $harianAsli = $nilaiData->where('jenis', 'harian')->avg('nilai') ?? 0;
            $uts = $nilaiData->where('jenis', 'uts')->first()->nilai ?? 0;
            $uas = $nilaiData->where('jenis', 'uas')->first()->nilai ?? 0;

            if ($rekap) {
                $akhir = $rekap->nilai;
            } else {
                $akhir = ($harianAsli > 0 || $uts > 0 || $uas > 0) ? ($harianAsli + $uts + $uas) / 3 : 0;
            }

            if ($akhir > 0) {
                $totalAkhir += $akhir;
                $jumlahMapel++;
            }

            $details[] = (object)[
                'nama_mapel' => $mapel->nama_mapel,
                'tugas' => round($harianAsli),
                'uts' => $uts,
                'uas' => $uas,
                'nilai_akhir' => round($akhir)
            ];
        }

        $rataRata = $jumlahMapel > 0 ? round($totalAkhir / $jumlahMapel) : 0;

        return view('admin.nilai.show', compact(
            'siswa', 'details', 'rataRata', 'tahun_tampil', 'semester_tampil'
        ));
    }
}