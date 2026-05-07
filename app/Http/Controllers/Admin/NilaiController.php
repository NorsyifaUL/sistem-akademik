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
     * HALAMAN UTAMA REKAP NILAI (DASHBOARD)
     * Menampilkan daftar siswa per kelas beserta rata-rata nilainya.
     */
    public function index(Request $request)
    {
        $kelasTerpilih = $request->get('kelas');
        
        // Ambil data kelas dari tabel Kelas (untuk dropdown filter)
        $data_kelas = Kelas::orderBy('nama_kelas', 'asc')->pluck('nama_kelas');

        $siswas = [];

        if ($kelasTerpilih) {
            // Mengambil data siswa yang sudah dihitung rata-ratanya secara dinamis
            $mapels = Mapel::all();
            $siswas = $this->getDataRekap($kelasTerpilih, $mapels);
        }

        return view('admin.nilai.index', compact('siswas', 'kelasTerpilih', 'data_kelas'));
    }

    /**
     * PRINT REKAP NILAI SATU KELAS (LEGGER)
     */
    public function print(Request $request)
    {
        $kelasTerpilih = $request->get('kelas');
        $mapels = Mapel::orderBy('nama_mapel', 'asc')->get();
        
        if (!$kelasTerpilih) {
            return redirect()->back()->with('error', 'Pilih kelas terlebih dahulu');
        }

        // Mengambil data siswa melalui fungsi rekap agar ranking/rata-rata akurat
        $siswas = $this->getDataRekap($kelasTerpilih, $mapels);

        return view('admin.nilai_print', compact('mapels', 'siswas', 'kelasTerpilih'));
    }

    /**
     * FUNGSI CETAK RAPORT INDIVIDU
     */
    public function cetakRaport(int $id)
    {
        $siswa = Siswa::findOrFail($id);
        $setting = Setting::first();

        // Nama variabel yang didefinisikan:
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
                        ->where('semester', $semester_aktif)
                        ->get();

        $wali = Jadwal::with('guru')->where('kelas', $siswa->kelas)->first();
        $nama_wali = $wali->guru->nama ?? '................';
        $nip_wali = $wali->guru->nip ?? '................';

        // Load View PDF
        $pdf = Pdf::loadView('guru.raport_pdf', [
            'siswa'         => $siswa,
            'dataRaport'    => $dataRaport,
            'eskul'         => $ekskul_data, // DISESUAIKAN: Menjadi 'eskul' agar cocok dengan Blade
            'setting'       => $setting, 
            'semester'      => $semester_aktif,    
            'tahun_ajaran'  => $tahun_ajar_aktif, 
            'nama_kepsek'   => $setting->nama_kepsek ?? '................',
            'nip_kepsek'    => $setting->nip_kepsek ?? '................',
            'nama_wali'     => $nama_wali,
            'nip'           => $nip_wali,
            'absensi'       => ['sakit' => 0, 'izin' => 0, 'alfa' => 0],
            'catatan_wali'  => "Pertahankan prestasimu dan teruslah belajar dengan giat.",
            'tgl_cetak'     => date('d F Y'),
        ]);

        return $pdf->setPaper('a4', 'portrait')->stream('Raport_'.$siswa->nama.'.pdf');
    }

    /**
     * FUNGSI PRIVATE: Menghitung rekap nilai untuk efisiensi kode (DRY)
     */
    private function getDataRekap(string $kelasTerpilih, $mapels)
    {
        $siswas = Siswa::where('kelas', $kelasTerpilih)->orderBy('nama', 'asc')->get();
        $setting = Setting::first();
        $sem = $setting->semester ?? '1';
        $thn = $setting->tahun_ajaran ?? '2025/2026';

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
                                      ->where('semester', $sem)
                                      ->where('tahun_ajaran', $thn)
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

    /**
     * DETAIL NILAI SISWA (SHOW)
     */
    public function show(int $id)
    {
        $siswa = Siswa::findOrFail($id);
        $mapels = Mapel::orderBy('nama_mapel', 'asc')->get();
        $setting = Setting::first();
        
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
                                ->where('semester', $setting->semester ?? '1')
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