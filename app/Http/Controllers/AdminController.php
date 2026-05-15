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
     * Tampilan Dashboard Admin (Fix Grafik & Relasi)
     */
    public function dashboard()
    {
        $totalGuru = Guru::count();
        $totalSiswa = Siswa::count();
        $totalMapel = Mapel::count();
        $absensiHariIni = Absensi::whereDate('tanggal', Carbon::today())->count();

        $notifikasis = Notifikasi::where('isi_pesan', 'LIKE', 'LOG:%')
                        ->latest()
                        ->take(10)
                        ->get();

        // --- LOGIKA GRAFIK DINAMIS: Menggunakan Relasi kelas_id ---
        $daftarKelas = Kelas::orderBy('nama_kelas', 'asc')->get();

        $labels = [];
        $dataHadir = [];
        $dataIzin = [];
        $dataAlpa = [];

        foreach ($daftarKelas as $k) {
            $labels[] = $k->nama_kelas;

            // Hitung Hadir (H) menggunakan kelas_id
            $dataHadir[] = Absensi::whereDate('tanggal', Carbon::today())
                ->where('status', 'H')
                ->whereHas('siswa', function($q) use ($k) {
                    $q->where('kelas_id', $k->id);
                })->count();

            // Hitung Izin & Sakit (I & S) menggunakan kelas_id
            $dataIzin[] = Absensi::whereDate('tanggal', Carbon::today())
                ->whereIn('status', ['I', 'S'])
                ->whereHas('siswa', function($q) use ($k) {
                    $q->where('kelas_id', $k->id);
                })->count();

            // Hitung Alpa (A) menggunakan kelas_id
            $dataAlpa[] = Absensi::whereDate('tanggal', Carbon::today())
                ->where('status', 'A')
                ->whereHas('siswa', function($q) use ($k) {
                    $q->where('kelas_id', $k->id);
                })->count();
        }

        $dataChart = [
            'labels' => $labels,
            'hadir'  => $dataHadir,
            'izin'   => $dataIzin,
            'alpa'   => $dataAlpa,
        ];

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
        $jadwals = Jadwal::with(['mapel', 'guru', 'dataKelas'])->latest()->get();
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

        Jadwal::create([
            'kelas_id' => $request->kelas_id,
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

        if ($request->filled('kelas_id')) {
            $query->where('kelas_id', $request->kelas_id);
        }

        $siswas = $query->orderBy('nama', 'asc')->get();
        $tahunAjaran = ['2025/2026 - I (Satu)'];

        return view('admin.nilai', compact('siswas', 'datakelas', 'tahunAjaran'));
    }

public function cetakRaport(int $id)
{
    // Ambil data siswa beserta relasi kelas dan wali kelasnya
    $siswa = Siswa::with('dataKelas.guru')->findOrFail($id);
    
    // Ambil setting dari DB
    $setting = DB::table('settings')->first();
    $semRaw = $setting->semester ?? '1';
    $semester = ($semRaw == '1' || strtolower($semRaw) == 'ganjil') ? 'Ganjil' : 'Genap';
    $tahun_ajaran = $setting->tahun_ajaran ?? date('Y');

    // Ambil Nama & NIP Wali Kelas dari relasi
    $nama_wali = $siswa->dataKelas->guru->nama ?? "Wali Kelas";
    $nip_wali = $siswa->dataKelas->guru->nip ?? "-";

    // Ambil Jadwal berdasarkan kelas siswa
    $jadwals = Jadwal::with('mapel')->where('kelas_id', $siswa->kelas_id)->get();
    
    // Ambil semua records nilai siswa di semester aktif
    $allRecords = Nilai::where('siswa_id', $siswa->id)
                       ->where('semester', $semester)
                       ->get();

    $dataRaport = $jadwals->map(function ($jadwal) use ($allRecords) {
        $mapel = $jadwal->mapel->nama_mapel ?? 'Mata Pelajaran';
        $nilaiMapel = $allRecords->where('jadwal_id', $jadwal->id);
        
        // PRIORITAS 1: Cek apakah ada data jenis 'rekap' (hasil input kolektif)
        $dataRekap = $nilaiMapel->where('jenis', 'rekap')->first();

        if ($dataRekap) {
            $nilaiAkhir = $dataRekap->nilai;
            $narasi = $dataRekap->keterangan ?? "Peserta didik telah menunjukkan penguasaan kompetensi dengan baik.";
        } else {
            // PRIORITAS 2: Hitung manual jika data rekap belum ada
            $harian = $nilaiMapel->where('jenis', 'harian')->avg('nilai') ?? 0;
            $uts = $nilaiMapel->where('jenis', 'uts')->avg('nilai') ?? 0;
            $uas = $nilaiMapel->where('jenis', 'uas')->avg('nilai') ?? 0;
            
            $count = collect([$harian, $uts, $uas])->filter(fn($v) => $v > 0)->count();
            $nilaiAkhir = $count > 0 ? round(($harian + $uts + $uas) / $count) : 0;

            // Ambil narasi terakhir yang tersedia atau default
            $narasi = $nilaiMapel->whereNotNull('keterangan')
                                 ->where('keterangan', '!=', '')
                                 ->last()->keterangan ?? "Data capaian kompetensi belum tersedia.";
        }

        return [
            'mapel' => $mapel,
            'akhir' => $nilaiAkhir > 0 ? $nilaiAkhir : '-',
            'capaian_kompetensi' => $narasi
        ];
    });

    // Data Ekstrakurikuler
    $eskul = $allRecords->whereIn('jenis', ['eskul', 'ekstra'])->map(function($e) {
        return [
            'kegiatan' => $e->aspek, 
            'nilai' => $e->predikat ?? ($e->nilai ?? '-'), 
            'keterangan' => $e->keterangan ?? "Aktif mengikuti kegiatan ekstrakurikuler dengan baik."
        ];
    })->values();

    // Data Absensi (Menggunakan whereIn sesuai data di database Anda agar tidak 0)
    $absensi = [
        'sakit' => Absensi::where('siswa_id', $id)
                    ->whereIn('status', ['S', 's', 'Sakit', 'sakit'])
                    ->count(),
        'izin'  => Absensi::where('siswa_id', $id)
                    ->whereIn('status', ['I', 'i', 'Izin', 'izin'])
                    ->count(),
        'alpa'  => Absensi::where('siswa_id', $id)
                    ->whereIn('status', ['A', 'a', 'Alpa', 'alpa', 'Tanpa Keterangan', 'TK', 'tk'])
                    ->count(),
    ];

    // Ambil Catatan Wali Kelas dari data Sikap (jika ada)
    $catatan_sikap = $allRecords->where('jenis', 'sikap')->first();
    $catatan_wali = $catatan_sikap->keterangan ?? "Tingkatkan terus prestasi dan semangat belajarmu.";

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
        'catatan_wali' => $catatan_wali
    ])->setPaper('a4', 'portrait')->stream('Raport_'.$siswa->nama.'.pdf');
}

    /**
     * Manajemen Absensi
     */
    public function indexAbsensi(Request $request)
    {
        $datakelas = Kelas::all(); 
        $query = Absensi::with(['siswa.dataKelas']);
        $tanggal = $request->input('tanggal', Carbon::today()->format('Y-m-d'));
        $query->whereDate('tanggal', $tanggal);

        if ($request->filled('kelas_id')) {
            $query->whereHas('siswa', function($q) use ($request) {
                $q->where('kelas_id', $request->kelas_id);
            });
        }

        $absensis = $query->orderBy('created_at', 'desc')->get();
        return view('admin.absensi.index', compact('absensis', 'datakelas', 'tanggal'));
    }

    public function updateAbsensi(Request $request, int $id)
    {
        $request->validate(['status' => 'required|in:H,S,I,A', 'tanggal' => 'required|date']);
        Absensi::findOrFail($id)->update(['status' => $request->status, 'tanggal' => $request->tanggal]);
        return redirect()->back()->with('success', 'Data absensi diperbarui!');
    }

    public function destroyAbsensi(int $id)
    {
        Absensi::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Data absensi dihapus!');
    }

    public function rekapAbsensi()
    {
        $rekapData = Siswa::with('dataKelas')->withCount([
            'absensis as total_sakit' => function($q) { $q->where('status', 'S'); },
            'absensis as total_izin' => function($q) { $q->where('status', 'I'); },
            'absensis as total_alpa' => function($q) { $q->where('status', 'A'); },
        ])->get();

        return view('admin.absensi.rekap', compact('rekapData'));
    }

    /**
     * Manajemen Notifikasi
     */
    public function indexNotifikasi(Request $request)
    {
        $kelasList = Kelas::orderBy('nama_kelas', 'asc')->get();

        $query = Notifikasi::with(['absensi.siswa.dataKelas'])
                        ->where('isi_pesan', 'LIKE', '%ALPA%')
                        ->where('isi_pesan', 'NOT LIKE', '%Selamat Datang%');

        if ($request->filled('kelas_id')) {
            $query->whereHas('absensi.siswa', function($q) use ($request) {
                $q->where('kelas_id', $request->kelas_id);
            });
        }

        $notifikasis = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('admin.notifikasi.index', compact('notifikasis', 'kelasList'));
    }

    public function destroyNotifikasi(int $id)
    {
        Notifikasi::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Notifikasi dihapus!');
    }

    public function cetakRekapBulanan(Request $request)
    {
        $kelasId = $request->kelas_id;
        $bulan = $request->bulan ?? date('m'); 
        $tahun = $request->tahun ?? date('Y'); 

        if (!$kelasId) {
            return redirect()->back()->with('error', 'Silakan pilih kelas.');
        }

        $kelas = Kelas::findOrFail($kelasId);
        $setting = \App\Models\Setting::first();
        $nama_bulan = \Carbon\Carbon::createFromDate($tahun, $bulan, 1)->translatedFormat('F');
        $jumlah_hari = \Carbon\Carbon::createFromDate($tahun, $bulan, 1)->daysInMonth;

        $siswas = Siswa::where('kelas_id', $kelasId)->orderBy('nama', 'asc')->get();

        $data_rekap = $siswas->map(function ($siswa) use ($bulan, $tahun, $jumlah_hari) {
            $hari = [];
            for ($tgl = 1; $tgl <= $jumlah_hari; $tgl++) {
                $absen = Absensi::where('siswa_id', $siswa->id)
                    ->whereYear('tanggal', $tahun)
                    ->whereMonth('tanggal', $bulan)
                    ->whereDay('tanggal', $tgl)
                    ->get();

                if ($absen->isEmpty()) {
                    $status = '.'; 
                } else {
                    if ($absen->where('status', 'A')->count() > 0) $status = 'A';
                    elseif ($absen->where('status', 'I')->count() > 0) $status = 'I';
                    elseif ($absen->where('status', 'S')->count() > 0) $status = 'S';
                    else $status = 'H';
                }
                $hari[$tgl] = $status;
            }

            return [
                'nama' => $siswa->nama,
                'hari' => $hari,
                'total' => [
                    'S' => collect($hari)->filter(fn($v) => $v == 'S')->count(),
                    'I' => collect($hari)->filter(fn($v) => $v == 'I')->count(),
                    'A' => collect($hari)->filter(fn($v) => $v == 'A')->count(),
                ]
            ];
        });

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.absensi.cetak_bulanan_pdf', [
            'data' => $data_rekap,
            'jumlah_hari' => $jumlah_hari,
            'bulan_teks' => $nama_bulan,
            'tahun' => $tahun,
            'kelas' => $kelas->nama_kelas,
            'setting' => $setting
        ]);

        return $pdf->setPaper('a4', 'landscape')->stream("Rekap_Bulanan_{$kelas->nama_kelas}.pdf");
    }
}