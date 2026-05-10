<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Guru;
use App\Models\Siswa;
use App\Models\Mapel;
use App\Models\Absensi;
use App\Models\Notifikasi;
use App\Models\Jadwal;
use App\Models\Nilai;
use App\Models\Kelas; 
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    /**
     * Tampilan Dashboard Admin
     */
 public function dashboard()
{
    $totalGuru = Guru::count();
    $totalSiswa = Siswa::count();
    $totalMapel = Mapel::count();
    $absensiHariIni = Absensi::whereDate('tanggal', Carbon::today())->count();

    // Ambil log aktivitas absensi terbaru
    $notifikasis = Notifikasi::where('isi_pesan', 'LIKE', 'LOG:%')
                    ->latest()
                    ->take(10)
                    ->get();

    // --- LOGIKA GRAFIK DINAMIS: Mengikuti Tabel Kelas ---
    $daftarKelas = Kelas::orderBy('nama_kelas', 'asc')->get();

    $labels = [];
    $dataHadir = [];
    $dataIzin = [];
    $dataAlpa = [];

    foreach ($daftarKelas as $k) {
        // Ambil nama kelas sebagai label sumbu X
        $labels[] = $k->nama_kelas;

        // Hitung Hadir (H) per kelas hari ini
        $dataHadir[] = Absensi::whereDate('tanggal', Carbon::today())
            ->where('status', 'H')
            ->whereHas('siswa', function($q) use ($k) {
                $q->where('kelas', $k->nama_kelas);
            })->count();

        // Hitung Izin & Sakit (I & S) per kelas hari ini
        $dataIzin[] = Absensi::whereDate('tanggal', Carbon::today())
            ->whereIn('status', ['I', 'S'])
            ->whereHas('siswa', function($q) use ($k) {
                $q->where('kelas', $k->nama_kelas);
            })->count();

        // Hitung Alpa (A) per kelas hari ini
        $dataAlpa[] = Absensi::whereDate('tanggal', Carbon::today())
            ->where('status', 'A')
            ->whereHas('siswa', function($q) use ($k) {
                $q->where('kelas', $k->nama_kelas);
            })->count();
    }

    // Susun array dataChart agar bisa dibaca JavaScript
    $dataChart = [
        'labels' => $labels,
        'hadir'  => $dataHadir,
        'izin'   => $dataIzin,
        'alpa'   => $dataAlpa,
    ];

    // Ambil jadwal hari ini
    $jadwalHariIni = Jadwal::with(['mapel', 'guru'])
        ->where('hari', Carbon::now()->translatedFormat('l')) 
        ->orderBy('jam_mulai', 'asc')
        ->get();

    return view('admin.dashboard', compact(
        'totalGuru', 'totalSiswa', 'totalMapel', 'absensiHariIni', 
        'jadwalHariIni', 'dataChart', 'notifikasis'
    ));
}

    /**
     * Manajemen Jadwal
     */
    public function indexJadwal()
    {
        $jadwals = Jadwal::with(['mapel', 'guru'])->latest()->get();
        $datakelas = Kelas::all();
        $datamapel = Mapel::all();
        $dataguru = Guru::all();

        return view('admin.jadwal.index', compact('jadwals', 'datakelas', 'datamapel', 'dataguru'));
    }

    public function storeJadwal(Request $request)
    {
        $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
            'mapel_id' => 'required|exists:mapels,id',
            'guru_id' => 'required|exists:gurus,id',
            'hari' => 'required',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required',
        ]);

        $kelas = Kelas::find($request->kelas_id);

        Jadwal::create([
            'kelas_id' => $request->kelas_id,
            'kelas' => $kelas->nama_kelas,
            'mapel_id' => $request->mapel_id,
            'guru_id' => $request->guru_id,
            'hari' => $request->hari,
            'jam_mulai' => $request->jam_mulai,
            'jam_selesai' => $request->jam_selesai,
            'ruangan' => $request->ruangan ?? 'Ruang Kelas',
        ]);

        return redirect()->back()->with('success', 'Jadwal berhasil diterbitkan!');
    }

    public function destroyJadwal(int $id)
    {
        Jadwal::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Jadwal berhasil dihapus!');
    }

    /**
     * Manajemen Nilai & Raport
     */
    public function indexNilai(Request $request)
    {
        $datakelas = Kelas::all();
        $query = Siswa::with('nilais');

        if ($request->filled('kelas')) {
            $query->where('kelas', $request->kelas);
        }

        $siswas = $query->orderBy('nama', 'asc')->get();
        $tahunAjaran = ['2021/2022 - II (Dua)', '2025/2026 - I (Satu)'];

        return view('admin.nilai', compact('siswas', 'datakelas', 'tahunAjaran'));
    }

public function cetakRaport(int $id)
{
    $siswa = Siswa::findOrFail($id);
    
    // 1. Samakan Logika Semester dengan GuruController
    $setting = DB::table('settings')->first();
    $semRaw = $setting->semester ?? '1';
    $semester = ($semRaw == '1' || strtolower($semRaw) == 'ganjil') ? 'Ganjil' : 'Genap';
    $tahun_ajaran = $setting->tahun_ajaran ?? date('Y');

    // 2. Ambil data Wali Kelas
    $kelasData = Kelas::where('nama_kelas', $siswa->kelas)->with('guru')->first();
    $nama_wali = $kelasData->guru->nama ?? "Wali Kelas";
    $nip_wali = $kelasData->guru->nip ?? "-";

    // 3. Ambil Jadwal dan Nilai (Logika ini harus sama dengan GuruController)
    $jadwals = Jadwal::with('mapel')->where('kelas', $siswa->kelas)->get();
    
    // Ambil semua record nilai siswa ini di semester ini
    $allRecords = Nilai::where('siswa_id', $siswa->id)
                       ->where('semester', $semester)
                       ->get();

    $dataRaport = $jadwals->map(function ($jadwal) use ($allRecords) {
        $mapel = $jadwal->mapel->nama_mapel ?? 'Mata Pelajaran';
        $nilaiMapel = $allRecords->where('jadwal_id', $jadwal->id);
        
        // Perhitungan Nilai Akhir (Sesuai rumus GuruController)
        $harian = $nilaiMapel->where('jenis', 'harian')->avg('nilai') ?? 0;
        $uts = $nilaiMapel->where('jenis', 'uts')->avg('nilai') ?? 0;
        $uas = $nilaiMapel->where('jenis', 'uas')->avg('nilai') ?? 0;
        
        // Cek pembagi agar tidak bagi nol
        $count = collect([$harian, $uts, $uas])->filter(fn($v) => $v > 0)->count();
        $nilaiAkhir = $count > 0 ? round(($harian + $uts + $uas) / $count) : 0;

        // Ambil Narasi / Keterangan
        $narasi = $nilaiMapel->whereNotNull('keterangan')
                             ->where('keterangan', '!=', '')
                             ->last()->keterangan ?? "Data capaian kompetensi untuk mata pelajaran $mapel belum tersedia.";

        return [
            'mapel' => $mapel,
            'akhir' => $nilaiAkhir > 0 ? $nilaiAkhir : '-',
            'capaian_kompetensi' => $narasi
        ];
    });

    // 4. Ambil Data Ekskul (Agar PMR muncul di Admin)
    $eskul = $allRecords->whereIn('jenis', ['eskul', 'ekstra', 'ekstrakurikuler'])->map(function($e) {
        $nilaiFinal = $e->predikat ?? $e->nilai ?? '-';
        $predikatTeks = ($nilaiFinal == 'A') ? 'Sangat Baik' : (($nilaiFinal == 'B') ? 'Baik' : 'Cukup');
        return [
            'kegiatan' => $e->aspek, 
            'nilai' => $nilaiFinal, 
            'keterangan' => $e->keterangan ?? "Melaksanakan kegiatan $e->aspek dengan kualifikasi $predikatTeks."
        ];
    })->values();

    // 5. Ambil Absensi
    $absensi = [
        'sakit' => Absensi::where('siswa_id', $id)->where('status', 'Sakit')->count(),
        'izin'  => Absensi::where('siswa_id', $id)->where('status', 'Izin')->count(),
        'alpa'  => Absensi::where('siswa_id', $id)->whereIn('status', ['Alpa', 'A'])->count(),
    ];

    // 6. Return ke View yang sama dengan Guru
    return \Barryvdh\DomPDF\Facade\Pdf::loadView('guru.raport_pdf', [
        'siswa' => $siswa,
        'setting' => $setting,
        'dataRaport' => $dataRaport,
        'eskul' => $eskul,
        'absensi' => $absensi,
        'semester' => $semester,
        'tahun_ajaran' => $tahun_ajaran,
        'tgl_cetak' => now()->translatedFormat('d F Y'),
        'nama_kepsek' => $setting->nama_kepsek ?? '-',
        'nip_kepsek' => $setting->nip_kepsek ?? '-',
        'nama_wali' => $nama_wali,
        'nip' => $nip_wali,
        'catatan_wali' => "Tingkatkan prestasimu."
    ])->setPaper('a4', 'portrait')->stream('Raport_'.$siswa->nama.'.pdf');
}

private function prosesDataNilai($kelas, $mapels)
{
    $siswas = Siswa::where('kelas', $kelas)->orderBy('nama', 'asc')->get();
    
    // Ambil setting semester aktif dari DB
    $setting = DB::table('settings')->first();
    $semesterAktif = $setting->semester ?? '1'; // misal: '2' sesuai gambar

    foreach ($siswas as $siswa) {
        $totalNilaiSiswa = 0;
        $jumlahMapelTerhitung = 0;

        foreach ($mapels as $mapel) {
            // Cari jadwal yang tepat untuk mapel di kelas ini
            $jadwal = Jadwal::where('mapel_id', $mapel->id)
                            ->where('kelas', $kelas)
                            ->first();
            
            $akhir = 0;
            $narasi = null;

            if ($jadwal) {
                // Ambil nilai berdasarkan siswa, jadwal, dan semester
                $nilaiData = Nilai::where('siswa_id', $siswa->id)
                                 ->where('jadwal_id', $jadwal->id)
                                 ->where('semester', $semesterAktif) 
                                 ->get();
                
                if ($nilaiData->count() > 0) {
                    $harian = $nilaiData->where('jenis', 'harian')->first()->nilai ?? 0;
                    $uts    = $nilaiData->where('jenis', 'uts')->first()->nilai ?? 0;
                    $uas    = $nilaiData->where('jenis', 'uas')->first()->nilai ?? 0;
                    
                    $akhir = ($harian * 0.4) + ($uts * 0.3) + ($uas * 0.3);

                    // AMBIL NARASI: Ambil dari kolom 'keterangan' yang tidak kosong
                    $recordNarasi = $nilaiData->whereNotNull('keterangan')
                                              ->where('keterangan', '!=', '')
                                              ->first();
                    
                    $narasi = $recordNarasi ? $recordNarasi->keterangan : null;
                }
            }

            // Simpan ke objek siswa agar bisa dipanggil di cetakRaport
            $siswa->{"nilai_mapel_" . $mapel->id} = round($akhir, 2);
            $siswa->{"narasi_mapel_" . $mapel->id} = $narasi; 

            if($akhir > 0) { 
                $totalNilaiSiswa += $akhir; 
                $jumlahMapelTerhitung++; 
            }
        }
        $siswa->rata_rata_akhir = $jumlahMapelTerhitung > 0 ? round($totalNilaiSiswa / $jumlahMapelTerhitung, 2) : 0;
    }
    return $siswas->sortByDesc('rata_rata_akhir')->values();
}

    /**
     * Manajemen Absensi
     */
    public function indexAbsensi(Request $request)
    {
        $datakelas = Kelas::all(); 
        $query = Absensi::with('siswa');
        $tanggal = $request->input('tanggal', Carbon::today()->format('Y-m-d'));
        $query->whereDate('tanggal', $tanggal);

        if ($request->filled('kelas')) {
            $query->whereHas('siswa', function($q) use ($request) {
                $q->where('kelas', $request->kelas);
            });
        }

        $absensis = $query->orderBy('created_at', 'desc')->get();
        return view('admin.absensi.index', compact('absensis', 'datakelas', 'tanggal'));
    }

    public function updateAbsensi(Request $request, int $id)
    {
        $request->validate([
            'status' => 'required|in:H,S,I,A',
            'tanggal' => 'required|date'
        ]);

        $absensi = Absensi::findOrFail($id);
        $absensi->update([
            'status' => $request->status,
            'tanggal' => $request->tanggal
        ]);

        return redirect()->back()->with('success', 'Data absensi berhasil diperbarui!');
    }

    public function destroyAbsensi(int $id)
    {
        Absensi::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Data absensi berhasil dihapus!');
    }

    public function rekapAbsensi()
    {
        $rekapData = Siswa::withCount([
            'absensis as total_sakit' => function($q) { $q->where('status', 'S'); },
            'absensis as total_izin' => function($q) { $q->where('status', 'I'); },
            'absensis as total_alfa' => function($q) { $q->where('status', 'A'); },
        ])->get();

        return view('admin.absensi.rekap', compact('rekapData'));
    }

    /**
     * Manajemen Notifikasi / Log Aktivitas
     */
   /**
     * Manajemen Notifikasi / Log Aktivitas
     */
    public function indexNotifikasi(Request $request)
    {
        // 1. Ambil daftar kelas unik untuk dropdown filter
        $kelasList = Siswa::select('kelas')
                        ->distinct()
                        ->orderBy('kelas', 'asc')
                        ->get();

        // 2. Query dasar: Hanya ambil log ALPA dan bukan ucapan selamat datang
        $query = Notifikasi::with(['absensi.siswa'])
                        ->where('isi_pesan', 'LIKE', '%ALPA%')
                        ->where('isi_pesan', 'NOT LIKE', '%Selamat Datang%');

        // 3. Tambahkan Filter Kelas jika Admin memilih kelas tertentu
        if ($request->filled('kelas')) {
            $query->whereHas('absensi.siswa', function($q) use ($request) {
                $q->where('kelas', $request->kelas);
            });
        }

        // 4. Urutkan berdasarkan yang terbaru dan bagi per halaman (paginate)
        $notifikasis = $query->orderBy('created_at', 'desc')->paginate(15);

        // 5. Kirim kedua variabel ke view
        return view('admin.notifikasi.index', compact('notifikasis', 'kelasList'));
    }

    public function destroyNotifikasi(int $id)
    {
        Notifikasi::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Notifikasi berhasil dihapus!');
    }
}