<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Jadwal;
use App\Models\Absensi;
use App\Models\Nilai;
use App\Models\Siswa;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class GuruController extends Controller
{
    /**
     * Helper untuk data dashboard (Sapaan & Hari Indo).
     */
    private function getDashboardData()
    {
        $jam = date('H');
        if ($jam >= 5 && $jam < 11) $sapaan = "Selamat Pagi";
        elseif ($jam >= 11 && $jam < 15) $sapaan = "Selamat Siang";
        elseif ($jam >= 15 && $jam < 18) $sapaan = "Selamat Sore";
        else $sapaan = "Selamat Malam";

        $hariIndo = [
            'Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'
        ];
        
        return [
            'sapaan' => $sapaan,
            'hari_ini' => $hariIndo[date('l')] ?? 'Senin'
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
            ->with(['mapel'])
            ->get();

        $kelasIds = Jadwal::where('guru_id', $guru->id)->pluck('kelas')->unique();
        $totalSiswa = Siswa::whereIn('kelas', $kelasIds)->count();
        
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

        return view('guru.dashboard', array_merge($meta, compact(
            'jadwalHariIni', 'absensiHariIni', 'totalSiswa', 'alpaHariIni', 'nilaiSiswa'
        )));
    }

    /*
    |--------------------------------------------------------------------------
    | MANAJEMEN NILAI & REKAP (LEGGER)
    |--------------------------------------------------------------------------
    */
public function lihatNilaiSiswa(Request $request)
{
    $user = Auth::user();
    $guru = $user->guru;
    $setting = Setting::first();
    $jadwals = Jadwal::where('guru_id', $guru->id)->with(['mapel'])->get();
    
    $jadwalId = $request->jadwal_id;
    $jenisNilai = $request->jenis_nilai; 
    $siswaData = collect();
    $jadwalTerpilih = null;

    if ($jadwalId) {
        $jadwalTerpilih = Jadwal::with('mapel')->findOrFail($jadwalId);
        $siswas = Siswa::where('kelas', $jadwalTerpilih->kelas)->orderBy('nama', 'asc')->get();

        $siswaData = $siswas->map(function($s) use ($jadwalId, $jenisNilai) {
            $nilais = Nilai::where('siswa_id', $s->id)
                           ->where('jadwal_id', $jadwalId)
                           ->get();

            // 1. Ambil Nilai UH Spesifik
            $uh1 = $nilais->where('jenis', 'harian')->filter(fn($n) => strtolower($n->aspek) == 'uh1')->first()->nilai ?? null;
            $uh2 = $nilais->where('jenis', 'harian')->filter(fn($n) => strtolower($n->aspek) == 'uh2')->first()->nilai ?? null;
            $uh3 = $nilais->where('jenis', 'harian')->filter(fn($n) => strtolower($n->aspek) == 'uh3')->first()->nilai ?? null;
            $uh4 = $nilais->where('jenis', 'harian')->filter(fn($n) => strtolower($n->aspek) == 'uh4')->first()->nilai ?? null;

            // 2. Ambil UTS & UAS
            $uts = $nilais->filter(fn($n) => strtolower($n->jenis) == 'uts')->first()->nilai ?? 0;
            $uas = $nilais->filter(fn($n) => strtolower($n->jenis) == 'uas')->first()->nilai ?? 0;

            // 3. Rata-rata Harian
            $semuaHarian = $nilais->filter(fn($n) => in_array(strtolower($n->jenis), ['harian', 'tugas', 'ulangan_bab']));
            $rataHarian = $semuaHarian->count() > 0 ? $semuaHarian->avg('nilai') : 0;

            // 4. Nilai Akhir
            $nilaiKomponen = collect([$rataHarian, $uts, $uas])->filter(fn($v) => $v > 0);
            $pembagi = $nilaiKomponen->count();
            $akhir = $pembagi > 0 ? round($nilaiKomponen->sum() / $pembagi) : 0;

            // 5. Nilai Existing
            $nilaiExisting = null;
            if ($jenisNilai) {
                $findExisting = $nilais->filter(fn($n) => strtolower($n->jenis) == strtolower($jenisNilai))->first();
                $nilaiExisting = $findExisting ? $findExisting->nilai : null;
            }

            return (object)[
                'id' => $s->id, 
                'nama' => $s->nama, 
                'uh1' => $uh1, 
                'uh2' => $uh2, 
                'uh3' => $uh3, 
                'uh4' => $uh4,
                'harian' => round($rataHarian), 
                'uts' => $uts, 
                'uas' => $uas,
                'akhir' => $akhir,
                'nilai_existing' => $nilaiExisting,
                'predikat' => ($akhir >= 85 ? 'A' : ($akhir >= 75 ? 'B' : ($akhir > 0 ? 'C' : '-')))
            ];
        });
 

    return view('guru.nilai.manajemen', compact('jadwals', 'siswaData', 'jadwalTerpilih', 'setting', 'user'));
}
        
        return view('guru.nilai.manajemen', compact('jadwals', 'siswaData', 'jadwalTerpilih', 'setting', 'user'));
    }
    public function simpanNilaiMassal(Request $request)
    {
        // Tambahkan 'aspek' dalam validasi jika diperlukan
        $request->validate([
            'jadwal_id' => 'required', 
            'jenis' => 'required', 
            'nilai' => 'required|array'
        ]);
        
        $semester = Setting::first()->semester ?? '1'; 

        foreach ($request->nilai as $siswaId => $dataAspek) {
            // Cek jika $dataAspek adalah array (artinya ada uh1, uh2, dll)
            if (is_array($dataAspek)) {
                foreach ($dataAspek as $aspek => $skor) {
                    if ($skor !== null && $skor !== '') {
                        Nilai::updateOrCreate(
                            [
                                'siswa_id'  => $siswaId, 
                                'jadwal_id' => $request->jadwal_id, 
                                'jenis'     => $request->jenis, 
                                'aspek'     => $aspek, // PENTING: Pembeda UH1, UH2, dll
                                'semester'  => $semester
                            ],
                            ['nilai' => $skor, 'updated_at' => now()]
                        );
                    }
                }
            } else {
                // Ini untuk handle jika input nilai bukan array (misal UTS/UAS tunggal)
                if ($dataAspek !== null && $dataAspek !== '') {
                    Nilai::updateOrCreate(
                        [
                            'siswa_id'  => $siswaId, 
                            'jadwal_id' => $request->jadwal_id, 
                            'jenis'     => $request->jenis,
                            'semester'  => $semester
                        ],
                        ['nilai' => $dataAspek, 'updated_at' => now()]
                    );
                }
            }
        }
        
        return redirect()->back()->with('success', 'Nilai berhasil diperbarui sesuai kolom!');
    }
    /*
    |--------------------------------------------------------------------------
    | ABSENSI & NOTIFIKASI WA (FONNTE)
    |--------------------------------------------------------------------------
    */
    public function indexAbsensi()
    {
        $guru = Auth::user()->guru;
        $meta = $this->getDashboardData();
        $hari_ini = $meta['hari_ini'];
        $jadwals = Jadwal::where('guru_id', $guru->id)->with(['mapel'])->get();
        return view('guru.absensi.index', compact('jadwals', 'hari_ini'));
    }

    /*
    |--------------------------------------------------------------------------
    | TAMPILKAN FORM ABSENSI
    |--------------------------------------------------------------------------
    */
    public function formAbsensi(Request $request, int $jadwalId)
    {
        $guru = Auth::user()->guru;
        
        // Memastikan jadwal tersebut memang milik guru yang sedang login
        $jadwal = Jadwal::where('id', $jadwalId)->where('guru_id', $guru->id)->firstOrFail();
        
        $tanggal = $request->tanggal ?? date('Y-m-d');
        
        // Ambil daftar siswa di kelas tersebut
        $siswa = Siswa::where('kelas', $jadwal->kelas)->orderBy('nama', 'asc')->get();
        
        // Ambil data absen yang mungkin sudah diisi sebelumnya pada tanggal tersebut
        $sudah_absen = Absensi::where('jadwal_id', $jadwalId)
                            ->whereDate('tanggal', $tanggal)
                            ->get()
                            ->keyBy('siswa_id');

        return view('guru.absensi.form', compact('jadwal', 'siswa', 'sudah_absen', 'tanggal'));
    }

 public function simpanAbsensi(Request $request)
    {
        $request->validate([
            'jadwal_id' => 'required', 
            'tanggal'   => 'required|date', 
            'status'    => 'required|array'
        ]);
        
        if ($request->tanggal !== \Carbon\Carbon::today()->format('Y-m-d')) {
            return redirect()->back()->with('error', 'Absensi hanya diperbolehkan untuk hari ini.');
        }

        $jadwal = Jadwal::with('mapel')->findOrFail($request->jadwal_id);

        foreach ($request->status as $siswaId => $status) {
            $statusMap = ['A' => 'Alpa', 'H' => 'Hadir', 'S' => 'Sakit', 'I' => 'Izin'];
            $statusFix = $statusMap[strtoupper($status)] ?? $status;

            $target_siswa = Siswa::find($siswaId);
            $status_wa = null;
            
            // 1. KIRIM WA (Hanya jika status Alpa)
            if ($statusFix == 'Alpa' && $target_siswa && $target_siswa->no_wa_ortu) {
                $status_wa = $this->kirimNotifWA($target_siswa, $jadwal, $request->tanggal);
            }

            // 2. SIMPAN DATA ABSENSI KE TABEL ABSENSIS
            $absen = Absensi::updateOrCreate(
                ['jadwal_id' => $request->jadwal_id, 'siswa_id' => $siswaId, 'tanggal' => $request->tanggal],
                ['status' => $statusFix, 'status_wa' => $status_wa, 'updated_at' => now()]
            );

            // 3. LOG UNTUK ADMIN (HANYA JIKA STATUS ALPA)
            if ($statusFix == 'Alpa') {
                $admin = \App\Models\User::where('role', 'admin')->first();
                if ($admin && $target_siswa) {
                    
                    // SESUAIKAN DENGAN ENUM DATABASE: 'Terkirim' atau 'Gagal'
                    $statusLogAdmin = ($status_wa == 'sent') ? 'Terkirim' : 'Gagal';

                    \App\Models\Notifikasi::create([
                        'user_id'      => $admin->id,
                        'absensi_id'   => $absen->id,
                        'tanggal'      => now(),
                        'kelas'        => $target_siswa->kelas, 
                        'isi_pesan'    => "LOG: Siswa {$target_siswa->nama} (Kelas {$target_siswa->kelas}) ALPA pada Mapel {$jadwal->mapel->nama_mapel}",
                        'status_kirim' => $statusLogAdmin, 
                    ]);
                }
            }
        }
        
        return redirect()->route('guru.absensi.index')->with('success', 'Presensi berhasil disimpan!');
    }
    /*
    |--------------------------------------------------------------------------
    | REKAP ABSENSI (RIWAYAT)
    |--------------------------------------------------------------------------
    */
    public function indexRekapAbsensi(Request $request)
    {
        $user = Auth::user();
        $guru = $user->guru;
        
        // --- 1. Ambil daftar jadwal (untuk filter Mapel) ---
        $jadwals = Jadwal::where('guru_id', $guru->id)->with('mapel')->get();

        // --- 2. Ambil daftar KELAS yang diajar oleh guru ini (untuk variabel $kelasList) ---
        $kelasList = Jadwal::where('guru_id', $guru->id)
                    ->select('kelas')
                    ->distinct()
                    ->get();

        // Ambil data filter dari request
        $jadwalId = $request->jadwal_id;
        $kelasFilter = $request->kelas; // Filter kelas
        $tanggalMulai = $request->tanggal_mulai ?? Carbon::now()->startOfMonth()->format('Y-m-d');
        $tanggalSelesai = $request->tanggal_selesai ?? Carbon::now()->format('Y-m-d');

        // --- 3. Query riwayat absensi dengan filter ---
        $query = Absensi::whereHas('jadwal', function($q) use ($guru) {
                    $q->where('guru_id', $guru->id);
                })
                ->with(['siswa', 'jadwal.mapel'])
                ->whereBetween('tanggal', [$tanggalMulai, $tanggalSelesai]);

        // Filter berdasarkan Jadwal/Mapel jika dipilih
        if ($jadwalId) {
            $query->where('jadwal_id', $jadwalId);
        }

        // Filter berdasarkan Kelas jika dipilih
        if ($kelasFilter) {
            $query->whereHas('siswa', function($q) use ($kelasFilter) {
                $q->where('kelas', $kelasFilter);
            });
        }

        $rekapAbsensi = $query->latest('tanggal')->get();

        // Nama variabel di dalam compact harus 'rekaps' agar sesuai dengan @forelse($rekaps ...) di Blade
        return view('guru.absensi.rekap', compact(
            'jadwals', 
            'kelasList', 
            'tanggalMulai', 
            'tanggalSelesai', 
            'jadwalId'
        ))->with('rekaps', $rekapAbsensi); // Kita gunakan ->with('rekaps') supaya pasti terbaca
    }

private function kirimNotifWA(Siswa $siswa, Jadwal $jadwal, string $tanggal)
    {
        $token = env('FONNTE_TOKEN', 'CKW3RDixtZqdnn4k5hkP'); 
        
        // --- TAMBAHAN: NORMALISASI NOMOR (Penting agar tidak failed) ---
        $target = $siswa->no_wa_ortu;
        $target = str_replace([' ', '-', '.', '+'], '', $target); // Hapus karakter aneh
        if (substr($target, 0, 1) === '0') {
            $target = '62' . substr($target, 1);
        } elseif (substr($target, 0, 2) !== '62') {
            $target = '62' . $target;
        }

        $tglFormat = Carbon::parse($tanggal)->translatedFormat('d F Y');
        
        $pesan = "🔔 *PEMBERITAHUAN ABSENSI SMANJA*\n\n" .
                 "Kepada Yth. Orang Tua/Wali dari:\n" .
                 "Siswa: *{$siswa->nama}*\n" .
                 "Status: *ALPA (Tidak Hadir)*\n" .
                 "Mapel: " . ($jadwal->mapel->nama_mapel ?? 'Mata Pelajaran') . "\n" .
                 "Tanggal: {$tglFormat}\n\n" .
                 "_Mohon bapak/ibu dapat memberikan keterangan terkait ketidakhadiran putra/putrinya. Terima kasih._";

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.fonnte.com/send',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array('target' => $target, 'message' => $pesan, 'countryCode' => '62'),
            CURLOPT_HTTPHEADER => array("Authorization: $token"),
            CURLOPT_SSL_VERIFYPEER => false,
        ));
        
        $response = curl_exec($curl);
        
        if(curl_errno($curl)){
            Log::error("Fonnte Error: " . curl_error($curl));
            curl_close($curl);
            return 'failed'; 
        }
        
        curl_close($curl);

        $resArray = json_decode($response, true);
        
        // Pastikan mengecek status dari Fonnte dengan benar
        if (isset($resArray['status']) && $resArray['status'] == true) {
            return 'sent';
        }

        Log::warning("Fonnte Gagal Dikirim: " . $response); // Agar kamu bisa cek di storage/logs/laravel.log
        return 'failed';
    }

    /*
    |--------------------------------------------------------------------------
    | RAPORT & CETAK PDF
    |--------------------------------------------------------------------------
    */
public function cetakRaport(int $id)
{
    $user = Auth::user();
    $siswa = Siswa::findOrFail($id);
    $setting = Setting::first() ?? new Setting();

    // 1. Ambil SEMUA Jadwal Pelajaran (Agar Mapel tanpa nilai tetap muncul di tabel)
    $jadwals = \App\Models\Jadwal::with('mapel')
        ->where('kelas', $siswa->kelas)
        ->get();

    // 2. Ambil semua data nilai siswa
    $allRecords = Nilai::where('siswa_id', $siswa->id)->get();

    // 3. Olah Data Nilai Akademik
    $dataRaport = $jadwals->map(function ($jadwal) use ($allRecords) {
        $mapel = $jadwal->mapel->nama_mapel ?? 'Mata Pelajaran';
        $nilaiMapel = $allRecords->where('jadwal_id', $jadwal->id);
        
        $harian = $nilaiMapel->where('jenis', 'harian')->avg('nilai') ?? 0;
        $uts = $nilaiMapel->where('jenis', 'uts')->avg('nilai') ?? 0;
        $uas = $nilaiMapel->where('jenis', 'uas')->avg('nilai') ?? 0;
        
        $nilaiAkhir = round(($harian + $uts + $uas) / 3);

        if ($nilaiAkhir >= 90) {
            $narasi = "Menunjukkan penguasaan kompetensi yang sangat luar biasa dalam memahami materi $mapel. Siswa mampu menganalisis konsep secara mendalam dan kritis.";
        } elseif ($nilaiAkhir >= 75) {
            $narasi = "Menunjukkan penguasaan kompetensi yang baik pada mata pelajaran $mapel. Siswa mampu memahami konsep-konsep inti dengan benar.";
        } elseif ($nilaiAkhir > 0) {
            $narasi = "Telah mencapai kriteria minimum namun memerlukan pendampingan pada beberapa kompetensi $mapel.";
        } else {
            $narasi = "Data nilai untuk mata pelajaran $mapel belum tersedia.";
        }

        return [
            'mapel' => $mapel, 
            'akhir' => $nilaiAkhir > 0 ? $nilaiAkhir : '-', 
            'capaian_kompetensi' => $narasi 
        ];
    });

    // 4. Olah Data Sikap
    $dataSikap = $allRecords->where('jenis', 'sikap')->values();

    // 5. Olah Data Eskul (PENTING: Jenis disamakan dengan fungsi storeEskul)
    $eskul = $allRecords->whereIn('jenis', ['eskul', 'ekstra', 'ekstrakurikuler'])->map(function($e) {
        // Ambil predikat dari kolom 'predikat', jika kosong ambil dari kolom 'nilai'
        $nilaiFinal = $e->predikat ?? $e->nilai ?? '-';
        
        // Menentukan teks keterangan otomatis jika kolom keterangan kosong
        $predikatTeks = ($nilaiFinal == 'A') ? 'Sangat Baik' : (($nilaiFinal == 'B') ? 'Baik' : 'Cukup');
        
        return [
            'kegiatan'   => $e->aspek, 
            'nilai'      => $nilaiFinal, 
            'keterangan' => $e->keterangan ?? "Melaksanakan kegiatan $e->aspek dengan kualifikasi $predikatTeks."
        ];
    })->values();
    
    // 6. Olah Data Absensi (Gunakan sum agar akurat)
    $absensi = [
        'sakit' => Absensi::where('siswa_id', $siswa->id)->where('status', 'Sakit')->count(),
        'izin'  => Absensi::where('siswa_id', $siswa->id)->where('status', 'Izin')->count(),
        'alfa'  => Absensi::where('siswa_id', $siswa->id)->whereIn('status', ['Alpa', 'A'])->count(),
    ];

    // 7. Variabel Pendukung
    $semester = ($setting->semester == '1') ? '1 (Ganjil)' : '2 (Genap)';
    $tahun_ajaran = $setting->tahun_ajaran ?? '2025/2026';
    $tgl_cetak = now()->translatedFormat('d F Y');
    $nama_kepsek = $setting->nama_kepsek ?? 'Nama Kepala Sekolah';
    $nip_kepsek  = $setting->nip_kepsek  ?? 'NIP Kepala Sekolah';
    $nama_wali   = $user->name;
    $nip         = $user->nip ?? '-';
    $catatan_wali = ($dataSikap->isNotEmpty()) ? $dataSikap->first()->keterangan : 'Pertahankan prestasi dan tetap semangat belajar.';

    return Pdf::loadView('guru.raport_pdf', compact(
        'siswa', 'setting', 'user', 'dataRaport', 'dataSikap', 'eskul',
        'absensi', 'semester', 'tahun_ajaran', 'tgl_cetak', 'nama_kepsek', 
        'nip_kepsek', 'nama_wali', 'nip', 'catatan_wali'
    ))->setPaper('a4', 'portrait')->stream('Raport_'.$siswa->nama.'.pdf');
}   /*
    |--------------------------------------------------------------------------
    | INPUT SIKAP & ESKUL
    |--------------------------------------------------------------------------
    */
    public function inputSikap(int $id)
    {
        $siswa = Siswa::findOrFail($id);
        $setting = Setting::first();
        $tahunAjaran = $setting->tahun_ajaran ?? '2025/2026';
        $nilaiSikap = Nilai::where('siswa_id', $id)
                        ->where('jenis', 'sikap')
                        ->where('tahun_ajaran', $tahunAjaran)
                        ->get();

        return view('guru.nilai_sikap_input', compact('siswa', 'setting', 'nilaiSikap'));
    }

    public function inputEskul(int $id)
    {
        $siswa = Siswa::findOrFail($id);
        $setting = Setting::first();
        $nilaiEskul = Nilai::where('siswa_id', $id)
            ->where('jenis', 'ekstra')
            ->where('tahun_ajaran', $setting->tahun_ajaran ?? '2025/2026')
            ->where('semester', $setting->semester ?? 'Ganjil')
            ->get();

        return view('guru.nilai_eskul_input', compact('siswa', 'setting', 'nilaiEskul'));
    }

    public function storeSikap(Request $request)
    {
        $request->validate(['siswa_id' => 'required', 'aspek' => 'required', 'nilai' => 'required', 'keterangan' => 'required']);
        $setting = Setting::first();
        Nilai::updateOrCreate(
            [
                'siswa_id' => $request->siswa_id, 'aspek' => $request->aspek, 'jenis' => 'sikap',
                'tahun_ajaran' => $setting->tahun_ajaran ?? '2025/2026', 'semester' => $setting->semester ?? 'Ganjil',
            ],
            ['nilai' => null, 'predikat' => $request->nilai, 'keterangan' => $request->keterangan]
        );
        return redirect()->back()->with('success', 'Nilai sikap diperbarui!');
    }

    public function storeEskul(Request $request)
    {
        $request->validate(['siswa_id' => 'required', 'nama_ekskul' => 'required', 'predikat' => 'required', 'keterangan' => 'required']);
        $setting = Setting::first();
        Nilai::updateOrCreate(
            [
                'siswa_id' => $request->siswa_id, 'aspek' => $request->nama_ekskul, 'jenis' => 'ekstra',
                'tahun_ajaran' => $setting->tahun_ajaran ?? '2025/2026', 'semester' => $setting->semester ?? 'Ganjil',
            ],
            ['nilai' => null, 'predikat' => $request->predikat, 'keterangan' => $request->keterangan]
        );
        return redirect()->back()->with('success', 'Data Ekstrakurikuler diperbarui!');
    }

    public function destroyNilai(int $id)
    {
        Nilai::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Data berhasil dihapus!');
    }

public function rekapNilai(Request $request)
{
    // 1. Ambil data Guru yang sedang login
    $guru = auth()->user()->guru;

    // 2. Ambil semua jadwal milik guru tersebut untuk isi dropdown
    $jadwals = \App\Models\Jadwal::with(['mapel'])
                ->where('guru_id', $guru->id)
                ->get();

    // 3. Ambil setting untuk menampilkan Tahun Akademik di header
    $setting = \App\Models\Setting::first();

    // 4. Inisialisasi variabel pendukung agar tidak "Undefined" saat halaman pertama dimuat
    $jadwalTerpilih = null;
    $siswas = [];
    $dataNilai = [];

    // 5. Cek jika guru sudah memilih jadwal di dropdown
    if ($request->filled('jadwal_id')) {
        // Ambil data jadwal spesifik yang dipilih guru
        $jadwalTerpilih = \App\Models\Jadwal::with(['mapel', 'kelas'])->find($request->jadwal_id);
        
        if ($jadwalTerpilih) {
            // Ambil daftar siswa berdasarkan kelas dari jadwal yang dipilih
            $siswas = \App\Models\Siswa::where('kelas', $jadwalTerpilih->kelas)->get();
            
            // Ambil data nilai yang sudah ada (untuk ditampilkan di input form)
            $dataNilai = \App\Models\Nilai::where('jadwal_id', $jadwalTerpilih->id)->get();
        }
    }

    // 6. Ambil daftar kelas unik (opsional, jika masih dibutuhkan di view)
    $kelasList = \App\Models\Siswa::select('kelas')->distinct()->get();

    // Pastikan 'jadwalTerpilih' masuk ke dalam compact
    return view('guru.nilai.manajemen', compact(
        'jadwals', 
        'jadwalTerpilih', 
        'setting', 
        'siswas', 
        'dataNilai', 
        'kelasList'
    ));
}

    public function leggerJadwal(int $id)
    {
        $guruId = auth()->user()->guru->id;
        $jadwal = \App\Models\Jadwal::with(['mapel'])->findOrFail($id);
        $semuaJadwal = \App\Models\Jadwal::with(['mapel'])->where('guru_id', $guruId)->get();
        $setting = \App\Models\Setting::first();

        $siswas = \App\Models\Siswa::where('kelas', $jadwal->kelas)->get();

        $rekapData = $siswas->map(function($siswa) use ($id) {
            // Ambil semua baris nilai untuk siswa ini pada jadwal ini
            $semuaNilaiSiswa = \App\Models\Nilai::where('siswa_id', $siswa->id)
                                                ->where('jadwal_id', $id)
                                                ->get();
            
            // Filter nilai berdasarkan kolom 'jenis' di database kamu
            $siswa->harian = $semuaNilaiSiswa->where('jenis', 'harian')->first()->nilai ?? 0;
            $siswa->uts = $semuaNilaiSiswa->where('jenis', 'uts')->first()->nilai ?? 0;
            $siswa->uas = $semuaNilaiSiswa->where('jenis', 'uas')->first()->nilai ?? 0;
            
            // Hitung nilai akhir secara manual karena di DB kamu nilainya terpisah baris
            $siswa->akhir = round(($siswa->harian + $siswa->uts + $siswa->uas) / 3, 2);
            
            return $siswa;
        });

        // Urutkan berdasarkan nilai akhir untuk ranking
        $rekapData = $rekapData->sortByDesc('akhir')->values();

        foreach ($rekapData as $index => $item) {
            $item->ranking = $index + 1;
        }

        return view('guru.jadwal.legger', compact('jadwal', 'semuaJadwal', 'rekapData', 'setting'));
    }

    public function indexRaport()
    {
        // 1. Ambil user yang sedang login
        $user = auth()->user();
        
        // 2. Ambil setting tahun ajaran
        $setting = \App\Models\Setting::first();

        // 3. Cek apakah kolom wali_kelas di tabel USERS ada isinya
        if ($user->wali_kelas) {
            // Ambil siswa yang kelasnya sama dengan wali_kelas di tabel users
            $siswas = \App\Models\Siswa::where('kelas', $user->wali_kelas)
                        ->orderBy('nama', 'asc')
                        ->get();
            
            $namaKelas = $user->wali_kelas;
        } else {
            // Jika di tabel users kolom wali_kelas kosong
            // Cari alternatif berdasarkan jadwal yang diajar oleh guru tersebut
            $guru = \App\Models\Guru::where('user_id', $user->id)->first();
            $guru_id = $guru ? $guru->id : 0;
            
            $kelasDiajar = \App\Models\Jadwal::where('guru_id', $guru_id)->pluck('kelas');
            $siswas = \App\Models\Siswa::whereIn('kelas', $kelasDiajar)
                        ->orderBy('nama', 'asc')
                        ->get();
                        
            $namaKelas = "Bukan Wali Kelas";
        }

        $infoKelas = (object)[
            'nama_kelas' => $namaKelas
        ];

        return view('guru.raport.index', compact('siswas', 'setting', 'infoKelas'));
    }

    public function indexJadwal()
    {
        // 1. Ambil data guru yang sedang login berdasarkan user_id
        $user = auth()->user();
        $guru = \App\Models\Guru::where('user_id', $user->id)->first();

        // 2. Ambil semua jadwal mengajar guru tersebut
        // Pastikan relasi 'mapel' sudah ada di Model Jadwal
        $jadwals = \App\Models\Jadwal::with('mapel')
                    ->where('guru_id', $guru->id)
                    ->get();

        // 3. Arahkan ke view index di folder guru/jadwal
        return view('guru.jadwal.index', compact('jadwals', 'guru'));
    }

   public function storeNilai(Request $request, $id)
    {
        // ... (Proses simpan nilai ke database kamu di sini) ...

        // Bagian Notifikasi::create saya hapus 
        // Supaya tabel Notifikasi tidak penuh dengan log update nilai.
        
        return redirect()->back()->with('success', 'Nilai berhasil disimpan.');
    }

    public function lihatSiswa(int $jadwal_id)
    {
        // Mengambil data jadwal beserta mapelnya
        $jadwal = Jadwal::with('mapel')->findOrFail($jadwal_id);

        // Mengambil daftar siswa berdasarkan kelas di jadwal tersebut
        // Menggunakan eager loading 'nilais' agar pengecekan status di Blade efisien
        $siswas = Siswa::with(['nilais' => function($query) use ($jadwal_id) {
                            $query->where('jadwal_id', $jadwal_id);
                    }])
                    ->where('kelas', $jadwal->kelas)
                    ->orderBy('nama', 'asc')
                    ->get();

        $setting = Setting::first(); 

        // Pastikan path view ini sesuai dengan folder yang kamu maksud
        return view('guru.nilai.siswa_list', compact('jadwal', 'siswas', 'setting'));
    }
}