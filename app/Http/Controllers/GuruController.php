<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Jadwal;
use App\Models\Absensi;
use App\Models\Nilai;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class GuruController extends Controller
{
    /**
     * Helper untuk mengambil metadata dashboard (Sapaan, Hari, Semester)
     */
private function getDashboardData()
{
    // 1. Logika Sapaan Berdasarkan Jam
    $jam = date('H');
    if ($jam >= 5 && $jam < 11) $sapaan = "Selamat Pagi";
    elseif ($jam >= 11 && $jam < 15) $sapaan = "Selamat Siang";
    elseif ($jam >= 15 && $jam < 18) $sapaan = "Selamat Sore";
    else $sapaan = "Selamat Malam";

    // 2. Terjemahan Hari ke Bahasa Indonesia
    $hariIndo = [
        'Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa',
        'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'
    ];
    
    // 3. Ambil Konfigurasi dari Database
    $setting = Setting::first();

    // 4. Logika Semester: Menggunakan angka 1/2 dari database
    // Pastikan nilai default adalah 'Ganjil' jika data setting belum ada
    $semesterAktif = ($setting && $setting->semester == '2') ? 'Genap' : 'Ganjil';

    // 5. Kembalikan data dalam bentuk Array
    return [
        'sapaan' => $sapaan,
        'hari_ini' => $hariIndo[date('l')] ?? 'Senin',
        'semester_aktif' => $semesterAktif,
        // Pastikan key ini 'tahun_ajaran' agar sinkron dengan fungsi storeSikap
        'tahun_ajaran' => $setting->tahun_ajaran ?? date('Y') . '/' . (date('Y') + 1)
    ];
}
    /*
    |--------------------------------------------------------------------------
    | DASHBOARD
    |--------------------------------------------------------------------------
    */
    public function dashboard()
    {
        $user = Auth::user();
        $guru = $user->guru;

        if (!$guru) {
            abort(403, "Akun ini tidak memiliki data guru.");
        }

        $meta = $this->getDashboardData();

        $jadwalHariIni = Jadwal::where('guru_id', $guru->id)
            ->where('hari', $meta['hari_ini'])
            ->with(['mapel', 'kelasRelation'])
            ->get();

        $namaKelasJadwal = Jadwal::where('guru_id', $guru->id)->pluck('kelas')->unique();
        $kelasIds = Kelas::whereIn('nama_kelas', $namaKelasJadwal)->pluck('id');
        $totalSiswa = Siswa::whereIn('kelas_id', $kelasIds)->count();
        
        $alpaHariIni = Absensi::whereHas('jadwal', function($q) use ($guru) {
                $q->where('guru_id', $guru->id);
            })
            ->whereDate('tanggal', Carbon::today())
            ->whereIn('status', ['A', 'Alpa'])
            ->count();

        $absensiHariIni = Absensi::whereHas('jadwal', function($q) use ($guru) {
                $q->where('guru_id', $guru->id);
            })
            ->whereDate('tanggal', Carbon::today())
            ->count();

        $nilaiSiswa = Nilai::whereHas('jadwal', function($q) use ($guru){
                $q->where('guru_id', $guru->id);
            })
            ->with(['siswa', 'jadwal.mapel'])
            ->latest('updated_at')
            ->take(5)
            ->get();
            
        $setting = Setting::first();

        return view('guru.dashboard', array_merge($meta, compact(
            'jadwalHariIni', 'absensiHariIni', 'totalSiswa', 'alpaHariIni', 'nilaiSiswa', 'setting' 
        )));
    }

    /*
    |--------------------------------------------------------------------------
    | MANAJEMEN NILAI
    |--------------------------------------------------------------------------
    */
    public function lihatNilaiSiswa(Request $request)
    {
        $user = Auth::user();
        $guru = $user->guru;
        
        // 1. Ambil data meta dashboard (semester & tahun ajaran aktif)
        $meta = $this->getDashboardData();
        $semesterAktif = $meta['semester_aktif']; 
        $tahunAjaran = $meta['tahun_ajaran']; 
        $setting = Setting::first();
        
        // 2. Ambil daftar jadwal yang diampu oleh guru tersebut
        $jadwals = Jadwal::where('guru_id', $guru->id)->with(['mapel'])->get();
        
        $jadwalId = $request->query('jadwal_id');
        $jenisNilai = strtolower(trim($request->query('jenis_nilai', 'harian'))); 
        
        $siswaData = collect();
        $jadwalTerpilih = null;

        if ($jadwalId) {
            $jadwalTerpilih = Jadwal::with(['mapel'])->where('guru_id', $guru->id)->find($jadwalId);
            
            if ($jadwalTerpilih) {
                // Ambil data siswa yang terdaftar di kelas pada jadwal tersebut
                $siswas = Siswa::whereHas('kelas', function($q) use ($jadwalTerpilih) {
                    $q->where('nama_kelas', $jadwalTerpilih->kelas);
                })->orderBy('nama', 'asc')->get();

                $siswaData = $siswas->map(function($s) use ($jadwalId, $jenisNilai, $semesterAktif, $tahunAjaran) {
                    // Query kolektif nilai siswa untuk efisiensi
                    $nilais = Nilai::where('siswa_id', $s->id)
                        ->where('jadwal_id', $jadwalId)
                        ->where('semester', $semesterAktif)
                        ->where('tahun_ajaran', $tahunAjaran)
                        ->get();

                    // 3. Inisialisasi Default Variabel
                    $s->uh1 = ''; $s->uh2 = ''; $s->uh3 = ''; $s->uh4 = ''; 
                    $s->harian = 0; $s->uts = 0; $s->uas = 0;
                    $s->nilai_existing = ''; $s->deskripsi_existing = '';
                    $s->nilai_akhir_calculated = 0; 

                    // 4. Logika Berdasarkan Mode Jenis Nilai
                    
                    // --- MODE HARIAN ---
                    if ($jenisNilai == 'harian') {
                        $s->uh1 = $nilais->where('aspek', 'uh1')->first()->nilai ?? '';
                        $s->uh2 = $nilais->where('aspek', 'uh2')->first()->nilai ?? '';
                        $s->uh3 = $nilais->where('aspek', 'uh3')->first()->nilai ?? '';
                        $s->uh4 = $nilais->where('aspek', 'uh4')->first()->nilai ?? '';
                        $s->harian = $nilais->where('aspek', 'rata_rata')->first()->nilai ?? 0;
                        
                        $s->nilai_akhir_calculated = $s->harian;
                    } 
                    
                    // --- MODE UTS / UAS ---
                    elseif (in_array($jenisNilai, ['uts', 'uas'])) {
                        // Cari baris nilai yang jenisnya sesuai (uts/uas)
                        $findRow = $nilais->where('jenis', $jenisNilai)->whereNull('aspek')->first();
                        if ($findRow) {
                            $s->nilai_existing = $findRow->nilai;
                            $s->deskripsi_existing = $findRow->keterangan;
                        }
                        $s->nilai_akhir_calculated = $s->nilai_existing ?? 0;
                    } 

                    // --- MODE REKAP / AKHIR (RAPORT) ---
                    elseif ($jenisNilai == 'akhir' || $jenisNilai == 'rekap') {
                        // Tarik komponen komponen nilai terbaru dari database
                        $s->uts = $nilais->where('jenis', 'uts')->first()->nilai ?? 0;
                        $s->uas = $nilais->where('jenis', 'uas')->first()->nilai ?? 0;
                        $s->harian = $nilais->where('aspek', 'rata_rata')->first()->nilai ?? 0;
                        
                        // OPSI A: Jika pakai Rumus Persentase (Harian 40% + UTS 30% + UAS 30%)
                        $calc = ($s->harian * 0.4) + ($s->uts * 0.3) + ($s->uas * 0.3);
                        $nilaiAkhir = ($calc > 0) ? round($calc) : 0;

                        // OPSI B: Jika mau pakai Rumus Rata-rata Murni biasa (Aktifkan jika diperlukan)
                        // $totalKomponen = $s->harian + $s->uts + $s->uas;
                        // $nilaiAkhir = ($totalKomponen > 0) ? round($totalKomponen / 3) : 0;

                        $s->nilai_akhir_calculated = $nilaiAkhir;

                        // --- PROSES UPDATE/INSERT OTOMATIS KE DATABASE phpMyAdmin ---
                        // Kode ini akan mengupdate ID 26 tadi dengan nilai kalkulasi baru secara otomatis
                        $rekapRow = \App\Models\Nilai::updateOrCreate(
                            [
                                'siswa_id'     => $s->id,
                                'jadwal_id'    => $jadwalId,
                                'semester'     => $semesterAktif,
                                'tahun_ajaran' => $tahunAjaran,
                                'jenis'        => 'rekap', 
                            ],
                            [
                                'aspek'        => 'akhir', // Menyesuaikan dengan kolom aspek di DB kamu
                                'nilai'        => $nilaiAkhir,
                                'keterangan'   => $this->generateDeskripsiOtomatis($nilaiAkhir)
                            ]
                        );

                        // Tampilkan data hasil sinkronisasi terbaru ke view blade
                        $s->nilai_existing = $rekapRow->nilai;
                        $s->deskripsi_existing = $rekapRow->keterangan;
                    }

                    // --- FALLBACK (ASPEK LAINNYA) ---
                    else {
                        $findRow = $nilais->where('aspek', $jenisNilai)->first();
                        if ($findRow) {
                            $s->nilai_existing = $findRow->nilai;
                            $s->deskripsi_existing = $findRow->keterangan;
                        }
                        $s->nilai_akhir_calculated = $s->nilai_existing ?? 0;
                    }

                    // 5. Generate Deskripsi Otomatis berdasarkan Nilai Akhir yang didapat
                    $s->deskripsi_otomatis = $this->generateDeskripsiOtomatis($s->nilai_akhir_calculated);

                    return $s;
                });
            }
        }

        // 6. Return ke View
        return view('guru.nilai.siswa_list', [
            'jadwals' => $jadwals,
            'siswaData' => $siswaData,
            'jadwalTerpilih' => $jadwalTerpilih,
            'setting' => $setting,
            'semesterAktif' => $semesterAktif,
            'jenisNilai' => $jenisNilai
        ]);
    }

/**
 * Helper untuk membuat teks deskripsi berdasarkan range nilai (Pemicu Raport)
 */
private function generateDeskripsiOtomatis($nilai = null)
{
    // Jika nilai tidak ditemukan atau 0
    if ($nilai === null || $nilai === '' || $nilai == 0) {
        return "Belum ada nilai yang cukup untuk menghasilkan deskripsi capaian.";
    }

    $nilai = (float) $nilai;

    if ($nilai >= 90) {
        return "Siswa menunjukkan performa yang sangat luar biasa dalam menguasai seluruh kompetensi, menunjukkan pemahaman mendalam, dan mampu menyelesaikan tugas dengan hasil sempurna.";
    } elseif ($nilai >= 80) {
        return "Siswa telah mencapai standar kompetensi dengan hasil yang baik, menunjukkan pemahaman yang stabil pada materi inti, dan mampu mengaplikasikan konsep secara mandiri.";
    } elseif ($nilai >= 70) {
        return "Siswa menunjukkan penguasaan yang cukup dalam mencapai kompetensi, sudah memahami dasar-dasar materi namun perlu meningkatkan ketelitian dalam pengerjaan tugas.";
    } else {
        return "Siswa masih berada dalam tahap pendampingan untuk mencapai tujuan pembelajaran. Diharapkan dapat lebih aktif dalam sesi pengayaan dan perbaikan.";
    }
}

public function simpanNilaiMassal(Request $request)
{
    $meta = $this->getDashboardData();
    $semester = $meta['semester_aktif'];
    $tahun_ajaran = $meta['tahun_ajaran'];
    $jenisHeader = strtolower($request->jenis);
    $guru_id = Auth::user()->guru->id;
    
    $jadwal = Jadwal::findOrFail($request->jadwal_id);

    $request->validate([
        'jadwal_id' => 'required|exists:jadwals,id',
        'nilai' => 'required|array',
    ]);

    try {
        DB::beginTransaction();

        foreach ($request->nilai as $siswaId => $dataNilai) {
            
            // --- 1. LOGIKA KHUSUS: NILAI HARIAN ---
            if ($jenisHeader == 'harian' && is_array($dataNilai)) {
                $subAspek = ['uh1', 'uh2', 'uh3', 'uh4'];
                $total = 0;
                $count = 0;

                foreach ($subAspek as $aspek) {
                    if (isset($dataNilai[$aspek]) && $dataNilai[$aspek] !== '') {
                        $val = round((float)$dataNilai[$aspek]);
                        
                        Nilai::updateOrCreate(
                            [
                                'siswa_id'     => $siswaId, 
                                'jadwal_id'    => $request->jadwal_id, 
                                'aspek'        => $aspek, 
                                'semester'     => $semester,
                                'tahun_ajaran' => $tahun_ajaran
                            ],
                            [
                                'jenis'        => 'harian', 
                                'guru_id'      => $guru_id,
                                'nilai'        => $val
                            ]
                        );
                        $total += $val; 
                        $count++; 
                    } else {
                        Nilai::where([
                            'siswa_id'     => $siswaId,
                            'jadwal_id'    => $request->jadwal_id,
                            'aspek'        => $aspek,
                            'semester'     => $semester,
                            'tahun_ajaran' => $tahun_ajaran
                        ])->delete();
                    }
                }

                if ($count > 0) {
                    $rerata = round($total / $count);
                    Nilai::updateOrCreate(
                        [
                            'siswa_id'     => $siswaId, 
                            'jadwal_id'    => $request->jadwal_id, 
                            'aspek'        => 'rata_rata', 
                            'semester'     => $semester, 
                            'tahun_ajaran' => $tahun_ajaran
                        ],
                        [
                            'jenis'        => 'harian', 
                            'guru_id'      => $guru_id, 
                            'nilai'        => $rerata
                        ]
                    );
                } else {
                    Nilai::where([
                        'siswa_id'     => $siswaId,
                        'jadwal_id'    => $request->jadwal_id,
                        'aspek'        => 'rata_rata',
                        'semester'     => $semester,
                        'tahun_ajaran' => $tahun_ajaran
                    ])->delete();
                }
            } 
            
            // --- 2. LOGIKA REKAP (NILAI AKHIR & DESKRIPSI) ---
            // Ini blok baru untuk menangani input deskripsi otomatis
            else if ($jenisHeader == 'rekap') {
                $nilaiAngka = isset($dataNilai['angka']) ? $dataNilai['angka'] : null;
                $deskripsi = isset($dataNilai['deskripsi']) ? $dataNilai['deskripsi'] : null;

                if ($nilaiAngka !== null && $nilaiAngka !== '') {
                    Nilai::updateOrCreate(
                        [
                            'siswa_id'     => $siswaId, 
                            'jadwal_id'    => $request->jadwal_id, 
                            'jenis'        => 'rekap', 
                            'semester'     => $semester,
                            'tahun_ajaran' => $tahun_ajaran
                        ],
                        [
                            'aspek'        => 'akhir', // Memberikan penanda aspek akhir
                            'guru_id'      => $guru_id,
                            'nilai'        => round((float)$nilaiAngka), 
                            'keterangan'   => $deskripsi
                        ]
                    );
                }
            }

            // --- 3. LOGIKA UTS / UAS ---
            else if (in_array($jenisHeader, ['uts', 'uas'])) {
                $nilaiRaw = is_array($dataNilai) ? ($dataNilai['angka'] ?? null) : $dataNilai;
                $deskripsi = is_array($dataNilai) ? ($dataNilai['deskripsi'] ?? null) : null;

                if ($nilaiRaw !== null && $nilaiRaw !== '') {
                    Nilai::updateOrCreate(
                        [
                            'siswa_id'     => $siswaId, 
                            'jadwal_id'    => $request->jadwal_id, 
                            'jenis'        => $jenisHeader, 
                            'aspek'        => null,
                            'semester'     => $semester,
                            'tahun_ajaran' => $tahun_ajaran
                        ],
                        [
                            'guru_id'      => $guru_id,
                            'nilai'        => round((float)$nilaiRaw), 
                            'keterangan'   => $deskripsi
                        ]
                    );
                } else {
                    Nilai::where([
                        'siswa_id'     => $siswaId,
                        'jadwal_id'    => $request->jadwal_id,
                        'jenis'        => $jenisHeader,
                        'aspek'        => null,
                        'semester'     => $semester,
                        'tahun_ajaran' => $tahun_ajaran
                    ])->delete();
                }
            }
        }

        DB::commit();
        return redirect()->back()->with('success', 'Berhasil memperbarui nilai!');
        
    } catch (\Exception $e) {
        DB::rollback();
        return redirect()->back()->with('error', 'Gagal: ' . $e->getMessage());
    }
}

    /*
    |--------------------------------------------------------------------------
    | FITUR LEGER, RAPORT, ABSENSI & PROFIL
    |--------------------------------------------------------------------------
    */
    public function leggerJadwal(int $jadwal_id)
    {
        $user = Auth::user();
        $guru = $user->guru;
        $meta = $this->getDashboardData();
        $semester = $meta['semester_aktif'];
        $setting = Setting::first();

        $jadwal = Jadwal::with(['mapel', 'guru', 'kelasRelation'])->where('guru_id', $guru->id)->findOrFail($jadwal_id);
        $semuaJadwal = Jadwal::with(['mapel', 'kelasRelation'])->where('guru_id', $guru->id)->get();
        
        $namaKelas = is_object($jadwal->kelasRelation) ? $jadwal->kelasRelation->nama_kelas : $jadwal->kelas;
        $kelasObj = Kelas::where('nama_kelas', $namaKelas)->first();
        
        $siswas = Siswa::where('kelas_id', $kelasObj->id ?? 0)
            ->with(['nilais' => function($q) use ($jadwal_id, $semester) {
                $q->where('jadwal_id', $jadwal_id)->where('semester', $semester);
            }])->get();

        $processedData = $siswas->map(function($s) {
            $harianList = $s->nilais->filter(fn($item) => preg_match('/UH[1-4]/i', $item->aspek))->pluck('nilai');
            $rataHarian = $harianList->count() > 0 ? round($harianList->avg()) : ($s->nilais->where('aspek', 'rata_rata')->first()->nilai ?? 0);
            
            $uts = $s->nilais->where('aspek', 'uts')->first()->nilai ?? 0;
            $uas = $s->nilais->where('aspek', 'uas')->first()->nilai ?? 0;
            
            $s->nilai_akhir_avg = round(($rataHarian + $uts + $uas) / 3);
            return $s;
        });

        $rekapData = $processedData->sortBy('nama');
        return view('guru.jadwal.legger', compact('jadwal', 'rekapData', 'setting', 'meta', 'semuaJadwal'));
    }

public function indexRaport(Request $request)
{
    $guru = Auth::user()->guru;
    $setting = Setting::first();
    $meta = $this->getDashboardData();

    // 1. Identifikasi kelas di mana guru ini adalah wali kelasnya
    $kelasWali = Kelas::where('guru_id', $guru->id)->first();

    if ($kelasWali) {
        // 2. Ambil siswa dari kelas perwalian tersebut (Sesuai Screenshot 2026-05-01 142316.jpg)
        $siswas = Siswa::where('kelas_id', $kelasWali->id)
            ->when($request->search, function($query) use ($request) {
                $query->where('nama', 'like', '%' . $request->search . '%');
            })
            ->orderBy('nama', 'asc')
            ->get();
        
        $namaKelas = $kelasWali->nama_kelas;
    } else {
        // Jika guru bukan wali kelas, siswas kosong
        $siswas = collect();
        $namaKelas = "-";
    }

    // Tetap sediakan listKelas jika Anda ingin ada opsi pindah kelas secara manual
    $namaKelasJadwal = Jadwal::where('guru_id', $guru->id)->pluck('kelas')->unique();
    $listKelas = Kelas::whereIn('nama_kelas', $namaKelasJadwal)->get();

    return view('guru.raport.index', compact('listKelas', 'siswas', 'setting', 'meta', 'namaKelas'));
}

/**
 * Menampilkan halaman input nilai sikap
 */
public function inputSikap(int $siswa_id)
{
    // Mengambil data siswa beserta relasi kelasnya
    // Pastikan model Siswa sudah memiliki public function kelas()
    $siswa = Siswa::with(['kelas'])->findOrFail($siswa_id);
    
    $setting = Setting::first();
    $meta = $this->getDashboardData();
    $semester = $meta['semester_aktif'];

    // Ambil semua data nilai berjenis 'sikap' untuk siswa ini di semester aktif
    $nilaiSikap = Nilai::where('siswa_id', $siswa_id)
        ->where('jenis', 'sikap')
        ->where('semester', $semester)
        ->get();

    // Mengambil baris pertama untuk pengisian default jika diperlukan
    $sikapUtama = $nilaiSikap->first();

    return view('guru.nilai_sikap_input', compact(
        'siswa', 
        'setting', 
        'meta', 
        'nilaiSikap', 
        'sikapUtama'
    ));
}

/**
 * Menyimpan atau memperbarui nilai sikap
 */
/**
 * Menyimpan atau memperbarui nilai sikap
 */
public function storeSikap(Request $request) // HAPUS 'int $siswa_id' dari sini
{
    // Ambil data semester dan tahun ajaran aktif dari method bantuan
    $meta = $this->getDashboardData(); 
    
    // 1. Validasi Input (Tambahkan siswa_id agar wajib diisi)
    $request->validate([
        'siswa_id' => 'required', // Tambahkan ini
        'aspek' => 'required',
        'nilai' => 'required',
        'keterangan' => 'required',
    ]);

    // Ambil ID siswa dari input form
    $siswa_id = $request->siswa_id;

    // 2. Tentukan aspek (Logika "Lainnya")
    $aspekTerpilih = ($request->aspek === 'Lainnya') ? $request->aspek_custom : $request->aspek;

    // 3. Simpan atau Update
    try {
        Nilai::updateOrCreate(
            [
                'siswa_id' => $siswa_id,
                'jenis'    => 'sikap',
                'aspek'    => $aspekTerpilih,
                'semester' => $meta['semester_aktif'],
            ],
            [
                'predikat'     => $request->nilai,
                'keterangan'   => $request->keterangan,
                'tahun_ajaran' => $meta['tahun_ajaran'], // Pastikan key sesuai getDashboardData
            ]
        );

        return redirect()->back()->with('success', 'Data observasi sikap berhasil disimpan.');
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Gagal menyimpan data: ' . $e->getMessage());
    }
}

public function destroyNilai(int $id)
{
    try {
        $nilai = Nilai::findOrFail($id);
        $nilai->delete();
        
        return redirect()->back()->with('success', 'Data penilaian berhasil dihapus.');
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Gagal menghapus data: ' . $e->getMessage());
    }
}
/**
 * Menampilkan halaman input nilai ekstrakurikuler
 */
public function inputEskul(int $siswa_id)
{
    $siswa = Siswa::findOrFail($siswa_id);
    $setting = Setting::first();
    $meta = $this->getDashboardData();
    $semester = $meta['semester_aktif'];

    // Ambil semua nilai dengan jenis 'ekstra' untuk siswa ini
    $nilaiEskul = Nilai::where('siswa_id', $siswa_id)
        ->where('jenis', 'ekstra')
        ->where('semester', $semester)
        ->get();

    return view('guru.nilai_eskul_input', compact('siswa', 'setting', 'meta', 'nilaiEskul'));
}

/**
 * Menyimpan atau memperbarui nilai ekstrakurikuler
 */
public function storeEskul(Request $request)
{
    // Validasi input minimal
    $request->validate([
        'siswa_id' => 'required',
        'nama_ekskul' => 'required',
        'predikat' => 'required'
    ]);

    $meta = $this->getDashboardData();
    
    try {
        Nilai::updateOrCreate(
            [
                'siswa_id' => $request->siswa_id,
                'jenis'    => 'ekstra',
                'aspek'    => strtoupper($request->nama_ekskul),
                'semester' => $meta['semester_aktif'],
            ],
            [
                'predikat'     => $request->predikat,
                'keterangan'   => $request->keterangan,
                // PERBAIKAN: Menggunakan 'tahun_ajaran' sesuai dengan key di getDashboardData()
                'tahun_ajaran' => $meta['tahun_ajaran'],
            ]
        );

        return redirect()->back()->with('success', 'Nilai ekstrakurikuler berhasil disimpan');
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Gagal menyimpan data eskul: ' . $e->getMessage());
    }
}

public function cetakRaport(int $siswa_id)
{
    // Ambil data siswa beserta relasi kelasnya
    $siswa = \App\Models\Siswa::with(['kelas'])->findOrFail($siswa_id);
    $setting = \App\Models\Setting::first();
    $meta = $this->getDashboardData();
    $semester = $meta['semester_aktif'];

    // 1. Ambil SEMUA Jadwal/Mapel untuk kelas siswa tersebut
    $jadwalKelas = \App\Models\Jadwal::where('kelas', $siswa->kelas->nama_kelas)
        ->orWhere('kelas', $siswa->kelas_id)
        ->with(['mapel'])
        ->get();

    // 2. Ambil semua data nilai siswa untuk semester ini
    $semuaNilai = \App\Models\Nilai::where('siswa_id', $siswa_id)
        ->where('semester', $semester)
        ->get();

    // 3. Gabungkan Jadwal dengan Nilai menggunakan Rumus Rekap (SINKRONISASI)
    $dataRaport = $jadwalKelas->map(function($j) use ($semuaNilai) {
        $nilaiMapel = $semuaNilai->where('jadwal_id', $j->id);

        // A. Hitung Rata-rata UH (Harian)
        $nilaiHarian = $nilaiMapel->filter(fn($n) => in_array(strtolower($n->jenis), ['harian', 'uh']));
        $rataUH = $nilaiHarian->count() > 0 ? $nilaiHarian->avg('nilai') : 0;

        // B. Ambil Nilai UTS
        $uts = $nilaiMapel->filter(fn($n) => strtolower($n->jenis) == 'uts')->first()->nilai ?? 0;

        // C. Ambil Nilai UAS
        $uas = $nilaiMapel->filter(fn($n) => strtolower($n->jenis) == 'uas')->first()->nilai ?? 0;

        // D. Hitung Total Akhir (Sama dengan Fitur Rekap)
        $totalMurni = ($rataUH + $uts + $uas) > 0 ? ($rataUH + $uts + $uas) / 3 : 0;
        $nilaiAkhir = round($totalMurni);

        // E. Logika Capaian Kompetensi (Manual vs Otomatis)
        $inputanManual = $nilaiMapel->whereNotNull('keterangan')->where('keterangan', '!=', '')->first();
        
        if ($inputanManual) {
            $capaian = $inputanManual->keterangan;
        } else {
            // Narasi Otomatis jika kosong
            if ($nilaiAkhir >= 85) {
                $capaian = "Menunjukkan penguasaan yang sangat baik dalam memahami kompetensi mata pelajaran ini.";
            } elseif ($nilaiAkhir >= 75) {
                $capaian = "Menunjukkan penguasaan yang baik dalam memahami kompetensi mata pelajaran ini.";
            } elseif ($nilaiAkhir > 0) {
                $capaian = "Perlu bimbingan dalam meningkatkan pemahaman materi dan pengerjaan tugas.";
            } else {
                $capaian = "Data capaian kompetensi belum tersedia.";
            }
        }

        return [
            'mapel' => $j->mapel->nama_mapel ?? ($j->mapel->nama ?? '-'),
            'akhir' => $nilaiAkhir,
            'capaian_kompetensi' => $capaian
        ];
    });

    // 4. Kelompokkan Ekstrakurikuler
    $eskul = $semuaNilai->whereIn('jenis', ['ekstra', 'eskul'])->map(function($e) {
        return [
            'kegiatan' => $e->aspek, 
            'nilai' => $e->predikat ?? $e->nilai ?? '-',  
            'keterangan' => $e->keterangan ?? 'Aktif mengikuti kegiatan ekstrakurikuler dengan baik.'
        ];
    });

    // 5. Ambil Catatan Sikap/Wali Kelas
    $sikap = $semuaNilai->where('jenis', 'sikap')->first();
    $catatan_wali = $sikap ? $sikap->keterangan : "Tingkatkan terus prestasi dan semangat belajarmu.";

    // 6. Data Absensi (Disesuaikan dengan tulisan di database Anda: "Izin", "Sakit", dll)
   // 6. Data Absensi (Hapus filter semester karena kolom tidak ada di tabel absensis)
    $absensi = [
        'sakit' => \App\Models\Absensi::where('siswa_id', $siswa_id)
                    ->whereIn('status', ['S', 's', 'Sakit', 'sakit'])
                    ->count(),
        'izin'  => \App\Models\Absensi::where('siswa_id', $siswa_id)
                    ->whereIn('status', ['I', 'i', 'Izin', 'izin'])
                    ->count(),
        'alpa'  => \App\Models\Absensi::where('siswa_id', $siswa_id)
                    ->whereIn('status', ['A', 'a', 'Alpa', 'alpa', 'Tanpa Keterangan', 'TK', 'tk'])
                    ->count(),
    ];

    // 7. Data Wali Kelas (Guru yang login)
    $user = Auth::user();
    $nama_wali = $user->guru->nama ?? $user->name; 
    $nip = $user->guru->nip ?? ($user->nip ?? '-');

    // Format Tanggal Raport Indonesia
    $tgl_raport = \Carbon\Carbon::parse($setting->tgl_raport ?? now())->translatedFormat('d F Y');

    return \Barryvdh\DomPDF\Facade\Pdf::loadView('guru.raport_pdf', compact(
        'siswa', 
        'setting', 
        'meta', 
        'dataRaport', 
        'eskul', 
        'absensi', 
        'catatan_wali', 
        'nama_wali', 
        'nip',
        'tgl_raport'
    ))->setPaper('a4', 'portrait')->stream("Raport_{$siswa->nama}.pdf");
}

    public function indexJadwal()
    {
        $guru = Auth::user()->guru;
        $setting = Setting::first();
        $jadwals = Jadwal::with(['mapel', 'kelasRelation'])->where('guru_id', $guru->id)->get();
        return view('guru.jadwal.index', compact('jadwals', 'guru', 'setting'));
    }

    public function profil() 
    {
        $user = Auth::user();
        $guru = $user->guru;
        $setting = Setting::first();
        return view('guru.profil', compact('user', 'guru', 'setting'));
    }

    public function updateProfil(Request $request)
    {
        $guru = Auth::user()->guru;
        $guru->update($request->only(['nama', 'telp', 'alamat']));
        return redirect()->back()->with('success', 'Profil berhasil diperbarui!');
    }

    // Rekap/Lihat Siswa Aliases
    public function rekapNilai(int $id) { return $this->lihatNilaiSiswa(new Request(['jadwal_id' => $id, 'jenis_nilai' => 'rekap'])); }
    public function lihatSiswa(int $id) { return $this->lihatNilaiSiswa(new Request(['jadwal_id' => $id])); }

public function cetakPdf(int $jadwal_id)
{
    // 1. Ambil data jadwal dengan relasi yang benar (kelasRelation)
    $jadwal = Jadwal::with(['mapel', 'kelasRelation'])->findOrFail($jadwal_id);
    
    // 2. Ambil data setting (untuk semester/tahun ajaran)
    $setting = Setting::first();
    
    // 3. Ambil data siswa beserta nilainya yang sesuai dengan jadwal_id
    $rekapData = Siswa::whereHas('nilais', function($q) use ($jadwal_id) {
        $q->where('jadwal_id', $jadwal_id);
    })->with(['nilais' => function($q) use ($jadwal_id) {
        $q->where('jadwal_id', $jadwal_id);
    }])->get();

    // 4. Render view ke dalam format PDF
    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('guru.nilai.cetak_pdf', compact('jadwal', 'rekapData', 'setting'))
            ->setPaper('a4', 'portrait');

    // 5. Gunakan accessor nama_display_kelas untuk penamaan file agar lebih dinamis
    $fileName = 'Rekap_Nilai_' . str_replace(' ', '_', $jadwal->nama_display_kelas) . '.pdf';
    
    return $pdf->stream($fileName);
}

}

