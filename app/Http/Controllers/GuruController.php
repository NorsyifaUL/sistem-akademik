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
     * Helper untuk data dashboard & konversi semester
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
        
        $setting = Setting::first();
        $semRaw = $setting->semester ?? '1';
        $semesterAktif = ($semRaw == '1' || strtolower($semRaw) == 'ganjil') ? 'Ganjil' : 'Genap';

        return [
            'sapaan' => $sapaan,
            'hari_ini' => $hariIndo[date('l')] ?? 'Senin',
            'semester_aktif' => $semesterAktif,
            'tahun_ajaran' => $setting->tahun_ajaran ?? date('Y')
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
            
        $setting = Setting::first();

        return view('guru.dashboard', array_merge($meta, compact(
            'jadwalHariIni', 'absensiHariIni', 'totalSiswa', 'alpaHariIni', 'nilaiSiswa', 'setting' )));
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
        $meta = $this->getDashboardData();
        $semester = $meta['semester_aktif'];
        
        $setting = Setting::first();
        $jadwals = Jadwal::where('guru_id', $guru->id)->with(['mapel'])->get();
        
        $jadwalId = $request->jadwal_id;
        $jenisNilai = $request->jenis_nilai; 
        $siswaData = collect();
        $jadwalTerpilih = null;

        if ($jadwalId) {
            $jadwalTerpilih = Jadwal::with('mapel')->findOrFail($jadwalId);
            $siswas = Siswa::where('kelas', $jadwalTerpilih->kelas)->orderBy('nama', 'asc')->get();

            $siswaData = $siswas->map(function($s) use ($jadwalId, $jenisNilai, $semester) {
                $nilais = Nilai::where('siswa_id', $s->id)
                               ->where('jadwal_id', $jadwalId)
                               ->where('semester', $semester) // Filter semester aktif
                               ->get();

                $uh1 = $nilais->where('jenis', 'harian')->filter(fn($n) => strtolower($n->aspek) == 'uh1')->first()->nilai ?? null;
                $uh2 = $nilais->where('jenis', 'harian')->filter(fn($n) => strtolower($n->aspek) == 'uh2')->first()->nilai ?? null;
                $uh3 = $nilais->where('jenis', 'harian')->filter(fn($n) => strtolower($n->aspek) == 'uh3')->first()->nilai ?? null;
                $uh4 = $nilais->where('jenis', 'harian')->filter(fn($n) => strtolower($n->aspek) == 'uh4')->first()->nilai ?? null;

                $uts = $nilais->filter(fn($n) => strtolower($n->jenis) == 'uts')->first()->nilai ?? 0;
                $uas = $nilais->filter(fn($n) => strtolower($n->jenis) == 'uas')->first()->nilai ?? 0;

                $semuaHarian = $nilais->filter(fn($n) => in_array(strtolower($n->jenis), ['harian', 'tugas', 'ulangan_bab']));
                $rataHarian = $semuaHarian->count() > 0 ? $semuaHarian->avg('nilai') : 0;

                $nilaiKomponen = collect([$rataHarian, $uts, $uas])->filter(fn($v) => $v > 0);
                $pembagi = $nilaiKomponen->count();
                $akhir = $pembagi > 0 ? round($nilaiKomponen->sum() / $pembagi) : 0;

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
        }

        return view('guru.nilai.manajemen', compact('jadwals', 'siswaData', 'jadwalTerpilih', 'setting', 'user', 'semester'));
    }

    public function simpanNilaiMassal(Request $request)
    {
        $request->validate([
            'jadwal_id' => 'required', 
            'jenis' => 'required', 
            'nilai' => 'required|array'
        ]);
        
        $meta = $this->getDashboardData();
        $semester = $meta['semester_aktif'];
        $tahun_ajaran = $meta['tahun_ajaran'];

        foreach ($request->nilai as $siswaId => $dataAspek) {
            if (is_array($dataAspek)) {
                foreach ($dataAspek as $aspek => $skor) {
                    if ($skor !== null && $skor !== '') {
                        Nilai::updateOrCreate(
                            [
                                'siswa_id'  => $siswaId, 
                                'jadwal_id' => $request->jadwal_id, 
                                'jenis'     => $request->jenis, 
                                'aspek'     => $aspek,
                                'semester'  => $semester
                            ],
                            [
                                'nilai' => $skor, 
                                'tahun_ajaran' => $tahun_ajaran,
                                'updated_at' => now()
                            ]
                        );
                    }
                }
            } else {
                if ($dataAspek !== null && $dataAspek !== '') {
                    Nilai::updateOrCreate(
                        [
                            'siswa_id'  => $siswaId, 
                            'jadwal_id' => $request->jadwal_id, 
                            'jenis'     => $request->jenis,
                            'semester'  => $semester
                        ],
                        [
                            'nilai' => $dataAspek, 
                            'tahun_ajaran' => $tahun_ajaran,
                            'updated_at' => now()
                        ]
                    );
                }
            }
        }
        
        return redirect()->back()->with('success', 'Nilai semester ' . $semester . ' berhasil diperbarui!');
    }

    /*
    |--------------------------------------------------------------------------
    | ABSENSI & NOTIFIKASI WA (FONNTE)
    |--------------------------------------------------------------------------
    */
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

    public function formAbsensi(Request $request, int $jadwalId)
    {
        $guru = Auth::user()->guru;
        $jadwal = Jadwal::where('id', $jadwalId)->where('guru_id', $guru->id)->firstOrFail();
        $tanggal = $request->tanggal ?? date('Y-m-d');
        $siswa = Siswa::where('kelas', $jadwal->kelas)->orderBy('nama', 'asc')->get();
        $sudah_absen = Absensi::where('jadwal_id', $jadwalId)->whereDate('tanggal', $tanggal)->get()->keyBy('siswa_id');

        return view('guru.absensi.form', compact('jadwal', 'siswa', 'sudah_absen', 'tanggal'));
    }

    public function simpanAbsensi(Request $request)
    {
        $request->validate(['jadwal_id' => 'required', 'tanggal' => 'required|date', 'status' => 'required|array']);
        
        if ($request->tanggal !== Carbon::today()->format('Y-m-d')) {
            return redirect()->back()->with('error', 'Absensi hanya diperbolehkan untuk hari ini.');
        }

        $jadwal = Jadwal::with('mapel')->findOrFail($request->jadwal_id);

        foreach ($request->status as $siswaId => $status) {
            $statusMap = ['A' => 'Alpa', 'H' => 'Hadir', 'S' => 'Sakit', 'I' => 'Izin'];
            $statusFix = $statusMap[strtoupper($status)] ?? $status;
            $target_siswa = Siswa::find($siswaId);
            $status_wa = null;
            
            // LOGIKA CEK ALPA PERTAMA (CEGAH SPAM)
            if ($statusFix == 'Alpa' && $target_siswa && $target_siswa->no_wa_ortu) {
                
                // Cari apakah hari ini sudah ada catatan Alpa/Alfa di jam pelajaran lain
                $sudahAdaAlpaLain = Absensi::where('siswa_id', $siswaId)
                    ->whereDate('tanggal', $request->tanggal)
                    ->whereIn('status', ['Alpa', 'alpa', 'A', 'Alfa', 'alfa'])
                    ->where('jadwal_id', '!=', $request->jadwal_id) // Kecuali jadwal yang sedang diinput
                    ->exists();

                // Hanya kirim WA jika belum ada catatan Alpa lain hari ini
                if (!$sudahAdaAlpaLain) {
                    $status_wa = $this->kirimNotifWA($target_siswa, $jadwal, $request->tanggal);
                } else {
                    $status_wa = 'already_sent_today'; // Tandai agar tidak kirim ulang
                }
            }

            $absen = Absensi::updateOrCreate(
                ['jadwal_id' => $request->jadwal_id, 'siswa_id' => $siswaId, 'tanggal' => $request->tanggal],
                ['status' => $statusFix, 'status_wa' => $status_wa, 'updated_at' => now()]
            );

            // Log Notifikasi untuk Admin (Hanya jika WA terkirim)
            if ($statusFix == 'Alpa' && $status_wa == 'sent') {
                $admin = \App\Models\User::where('role', 'admin')->first();
                if ($admin && $target_siswa) {
                    \App\Models\Notifikasi::create([
                        'user_id' => $admin->id,
                        'absensi_id' => $absen->id,
                        'tanggal' => now(),
                        'kelas' => $target_siswa->kelas, 
                        'isi_pesan' => "LOG: WA Terkirim ke Ortu {$target_siswa->nama} (Kelas {$target_siswa->kelas}) karena ALPA",
                        'status_kirim' => 'Terkirim', 
                    ]);
                }
            }
        }
        return redirect()->route('guru.absensi.index')->with('success', 'Presensi berhasil disimpan!');
    }

    public function indexRekapAbsensi(Request $request)
    {
        $user = Auth::user();
        $guru = $user->guru;
        $jadwals = Jadwal::where('guru_id', $guru->id)->with('mapel')->get();
        $kelasList = Jadwal::where('guru_id', $guru->id)->select('kelas')->distinct()->get();

        $jadwalId = $request->jadwal_id;
        $kelasFilter = $request->kelas;
        $tanggalMulai = $request->tanggal_mulai ?? Carbon::now()->startOfMonth()->format('Y-m-d');
        $tanggalSelesai = $request->tanggal_selesai ?? Carbon::now()->format('Y-m-d');

        $query = Absensi::whereHas('jadwal', function($q) use ($guru) { $q->where('guru_id', $guru->id); })
                ->with(['siswa', 'jadwal.mapel'])
                ->whereBetween('tanggal', [$tanggalMulai, $tanggalSelesai]);

        if ($jadwalId) $query->where('jadwal_id', $jadwalId);
        if ($kelasFilter) $query->whereHas('siswa', function($q) use ($kelasFilter) { $q->where('kelas', $kelasFilter); });

        $rekapAbsensi = $query->latest('tanggal')->get();
        return view('guru.absensi.rekap', compact('jadwals', 'kelasList', 'tanggalMulai', 'tanggalSelesai', 'jadwalId'))->with('rekaps', $rekapAbsensi);
    }

    private function kirimNotifWA(Siswa $siswa, Jadwal $jadwal, string $tanggal)
    {
        $token = env('FONNTE_TOKEN', 'CKW3RDixtZqdnn4k5hkP'); 
        $target = $siswa->no_wa_ortu;
        $target = str_replace([' ', '-', '.', '+'], '', $target);
        if (substr($target, 0, 1) === '0') $target = '62' . substr($target, 1);
        elseif (substr($target, 0, 2) !== '62') $target = '62' . $target;

        $tglFormat = Carbon::parse($tanggal)->translatedFormat('d F Y');
        $pesan = "🔔 *PEMBERITAHUAN ABSENSI SMANJA*\n\nKepada Yth. Orang Tua/Wali dari:\nSiswa: *{$siswa->nama}*\nStatus: *ALPA (Tidak Hadir)*\nMapel: " . ($jadwal->mapel->nama_mapel ?? 'Mata Pelajaran') . "\nTanggal: {$tglFormat}\n\n_Mohon bapak/ibu dapat memberikan keterangan terkait ketidakhadiran putra/putrinya. Terima kasih._";

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
        return (isset($resArray['status']) && $resArray['status'] == true) ? 'sent' : 'failed';
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
        
        $meta = $this->getDashboardData();
        $semester = $meta['semester_aktif']; 
        $tahun_ajaran = $meta['tahun_ajaran'];
        $setting = Setting::first() ?? new Setting();

        $jadwals = Jadwal::with('mapel')->where('kelas', $siswa->kelas)->get();
        
        $allRecords = Nilai::where('siswa_id', $siswa->id)
                            ->where('semester', $semester)
                            ->where('tahun_ajaran', $tahun_ajaran)
                            ->get();

        $dataRaport = $jadwals->map(function ($jadwal) use ($allRecords) {
            $mapel = $jadwal->mapel->nama_mapel ?? 'Mata Pelajaran';
            $nilaiMapel = $allRecords->where('jadwal_id', $jadwal->id);
            
            $harian = $nilaiMapel->where('jenis', 'harian')->avg('nilai') ?? 0;
            $uts = $nilaiMapel->where('jenis', 'uts')->avg('nilai') ?? 0;
            $uas = $nilaiMapel->where('jenis', 'uas')->avg('nilai') ?? 0;
            
            $totalNilai = $harian + $uts + $uas;
            $nilaiAkhir = $totalNilai > 0 ? round($totalNilai / 3) : 0;

            $narasi = $nilaiMapel->whereNotNull('keterangan')
                                 ->where('keterangan', '!=', '')
                                 ->last()->keterangan ?? "Data capaian kompetensi untuk mata pelajaran $mapel belum tersedia.";

            return [
                'mapel' => $mapel, 
                'akhir' => $nilaiAkhir > 0 ? $nilaiAkhir : '-', 
                'capaian_kompetensi' => $narasi
            ];
        });

        $dataSikap = $allRecords->where('jenis', 'sikap')->values();
        
        $eskul = $allRecords->whereIn('jenis', ['eskul', 'ekstra', 'ekstrakurikuler'])->map(function($e) {
            $nilaiFinal = $e->predikat ?? $e->nilai ?? '-';
            $predikatTeks = ($nilaiFinal == 'A') ? 'Sangat Baik' : (($nilaiFinal == 'B') ? 'Baik' : 'Cukup');
            return [
                'kegiatan' => $e->aspek, 
                'nilai' => $nilaiFinal, 
                'keterangan' => $e->keterangan ?? "Melaksanakan kegiatan $e->aspek dengan kualifikasi $predikatTeks."
            ];
        })->values();
        
        /**
         * FIX ABSENSI (UNIQUE DAYS):
         * Menghitung tanggal yang berbeda (distinct) agar absen per mapel tidak menumpuk di raport.
         */
        $absensi = [
            'sakit' => \App\Models\Absensi::where('siswa_id', $siswa->id)
                        ->whereIn('status', ['Sakit', 'sakit', 'S'])
                        ->distinct('tanggal')
                        ->count('tanggal'),
            'izin'  => \App\Models\Absensi::where('siswa_id', $siswa->id)
                        ->whereIn('status', ['Izin', 'izin', 'I'])
                        ->distinct('tanggal')
                        ->count('tanggal'),
            'alpa'  => \App\Models\Absensi::where('siswa_id', $siswa->id)
                        ->whereIn('status', ['Alpa', 'alpa', 'A', 'Alfa', 'alfa', 'Tanpa Keterangan', 'TK'])
                        ->distinct('tanggal')
                        ->count('tanggal'),
        ];

        return \Barryvdh\DomPDF\Facade\Pdf::loadView('guru.raport_pdf', [
            'siswa'         => $siswa, 
            'setting'       => $setting, 
            'user'          => $user, 
            'dataRaport'    => $dataRaport, 
            'dataSikap'     => $dataSikap, 
            'eskul'         => $eskul, 
            'absensi'       => $absensi, 
            'semester'      => $semester,
            'tahun_ajaran'  => $tahun_ajaran, 
            'tgl_cetak'     => now()->translatedFormat('d F Y'),
            'nama_kepsek'   => $setting->nama_kepsek, 
            'nip_kepsek'    => $setting->nip_kepsek, 
            'nama_wali'     => $user->name,
            'nip'           => $user->nip ?? '-', 
            'catatan_wali'  => $dataSikap->first()->keterangan ?? 'Tingkatkan terus prestasimu.'
        ])->setPaper('a4', 'portrait')->stream('Raport_'.$siswa->nama.'.pdf');
    }
       /*
    |--------------------------------------------------------------------------
    | INPUT SIKAP & ESKUL
    |--------------------------------------------------------------------------
    */
    public function inputSikap(int $id)
    {
        $siswa = Siswa::findOrFail($id);
        $meta = $this->getDashboardData();
        $semester = $meta['semester_aktif'];
        
        $nilaiSikap = Nilai::where('siswa_id', $id)
                           ->where('jenis', 'sikap')
                           ->where('semester', $semester)
                           ->get();
                           
        return view('guru.nilai_sikap_input', compact('siswa', 'semester', 'nilaiSikap'));
    }

    public function inputEskul(int $id)
    {
        $siswa = Siswa::findOrFail($id);
        $meta = $this->getDashboardData();
        $semester = $meta['semester_aktif'];
        
        $nilaiEskul = Nilai::where('siswa_id', $id)
                           ->where('jenis', 'ekstra')
                           ->where('semester', $semester)
                           ->get();

        return view('guru.nilai_eskul_input', compact('siswa', 'semester', 'nilaiEskul'));
    }

    public function storeSikap(Request $request)
    {
        $request->validate(['siswa_id' => 'required', 'aspek' => 'required', 'nilai' => 'required', 'keterangan' => 'required']);
        $meta = $this->getDashboardData();
        
        Nilai::updateOrCreate(
            ['siswa_id' => $request->siswa_id, 'aspek' => $request->aspek, 'jenis' => 'sikap', 'semester' => $meta['semester_aktif']],
            ['nilai' => null, 'predikat' => $request->nilai, 'keterangan' => $request->keterangan, 'tahun_ajaran' => $meta['tahun_ajaran']]
        );
        return redirect()->back()->with('success', 'Nilai sikap diperbarui!');
    }

    public function storeEskul(Request $request)
    {
        $request->validate(['siswa_id' => 'required', 'nama_ekskul' => 'required', 'predikat' => 'required', 'keterangan' => 'required']);
        $meta = $this->getDashboardData();

        Nilai::updateOrCreate(
            ['siswa_id' => $request->siswa_id, 'aspek' => $request->nama_ekskul, 'jenis' => 'ekstra', 'semester' => $meta['semester_aktif']],
            ['nilai' => null, 'predikat' => $request->predikat, 'keterangan' => $request->keterangan, 'tahun_ajaran' => $meta['tahun_ajaran']]
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
        $guru = auth()->user()->guru;
        $jadwals = Jadwal::with(['mapel'])->where('guru_id', $guru->id)->get();
        $setting = Setting::first();
        $jadwalTerpilih = null;
        $siswas = [];
        $dataNilai = [];

        if ($request->filled('jadwal_id')) {
            $jadwalTerpilih = Jadwal::with(['mapel', 'kelas'])->find($request->jadwal_id);
            if ($jadwalTerpilih) {
                $siswas = Siswa::where('kelas', $jadwalTerpilih->kelas)->get();
                $dataNilai = Nilai::where('jadwal_id', $jadwalTerpilih->id)->get();
            }
        }
        $kelasList = Siswa::select('kelas')->distinct()->get();
        return view('guru.nilai.manajemen', compact('jadwals', 'jadwalTerpilih', 'setting', 'siswas', 'dataNilai', 'kelasList'));
    }

    public function leggerJadwal(int $id)
    {
        $guruId = auth()->user()->guru->id;
        $jadwal = Jadwal::with(['mapel'])->findOrFail($id);
        $semuaJadwal = Jadwal::with(['mapel'])->where('guru_id', $guruId)->get();
        $meta = $this->getDashboardData();
        $semester = $meta['semester_aktif'];
        $setting = Setting::first();
        $siswas = Siswa::where('kelas', $jadwal->kelas)->get();

        $rekapData = $siswas->map(function($siswa) use ($id, $semester) {
            $semuaNilaiSiswa = Nilai::where('siswa_id', $siswa->id)
                                    ->where('jadwal_id', $id)
                                    ->where('semester', $semester)
                                    ->get();
                                    
            $siswa->harian = $semuaNilaiSiswa->where('jenis', 'harian')->avg('nilai') ?? 0;
            $siswa->uts = $semuaNilaiSiswa->where('jenis', 'uts')->first()->nilai ?? 0;
            $siswa->uas = $semuaNilaiSiswa->where('jenis', 'uas')->first()->nilai ?? 0;
            
            $pembagi = collect([$siswa->harian, $siswa->uts, $siswa->uas])->filter(fn($v) => $v > 0)->count();
            $siswa->akhir = $pembagi > 0 ? round(($siswa->harian + $siswa->uts + $siswa->uas) / $pembagi, 2) : 0;
            
            return $siswa;
        })->sortByDesc('akhir')->values();

        foreach ($rekapData as $index => $item) { $item->ranking = $index + 1; }
        return view('guru.jadwal.legger', compact('jadwal', 'semuaJadwal', 'rekapData', 'setting', 'semester'));
    }

    public function indexRaport()
    {
        $user = auth()->user();
        $setting = Setting::first();
        if ($user->wali_kelas) {
            $siswas = Siswa::where('kelas', $user->wali_kelas)->orderBy('nama', 'asc')->get();
            $namaKelas = $user->wali_kelas;
        } else {
            $guru = Auth::user()->guru;
            $guru_id = $guru ? $guru->id : 0;
            $kelasDiajar = Jadwal::where('guru_id', $guru_id)->pluck('kelas');
            $siswas = Siswa::whereIn('kelas', $kelasDiajar)->orderBy('nama', 'asc')->get();
            $namaKelas = "Bukan Wali Kelas";
        }
        $infoKelas = (object)['nama_kelas' => $namaKelas];
        return view('guru.raport.index', compact('siswas', 'setting', 'infoKelas'));
    }

    public function indexJadwal()
    {
        $user = auth()->user();
        $guru = $user->guru;
        if (!$guru) return redirect()->back()->with('error', 'Profil Guru tidak ditemukan.');
        $setting = Setting::first();
        $jadwals = Jadwal::with('mapel')->where('guru_id', $guru->id)->get();
        return view('guru.jadwal.index', compact('jadwals', 'guru', 'setting'));
    }

    public function storeNilai(Request $request, $id)
    {
        return redirect()->back()->with('success', 'Nilai berhasil disimpan.');
    }

    public function lihatSiswa(int $jadwal_id)
    {
        $jadwal = Jadwal::with('mapel')->findOrFail($jadwal_id);
        $siswas = Siswa::with(['nilais' => function($query) use ($jadwal_id) {
                $query->where('jadwal_id', $jadwal_id);
            }])
            ->where('kelas', $jadwal->kelas)->orderBy('nama', 'asc')->get();
        $setting = Setting::first(); 
        return view('guru.nilai.siswa_list', compact('jadwal', 'siswas', 'setting'));
    }

    public function profil()
    {
        $user = auth()->user();
        $guru = $user->guru; 
        $setting = Setting::first();
        return view('guru.profil', compact('user', 'guru', 'setting'));
    }
}