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

        // Ambil log aktivitas absensi terbaru untuk ditampilkan di dashboard admin
        $notifikasis = Notifikasi::where('isi_pesan', 'LIKE', 'LOG:%')
                        ->latest()
                        ->take(10)
                        ->get();

        // --- LOGIKA GRAFIK REAL-TIME SESUAI DATABASE ---
        $tingkat = [
            'Kelas X'   => 'X%',      // Mencari X1, X2, dst
            'Kelas XI'  => 'XI%',     // Mencari XI 1, XI 2, dst
            'Kelas XII' => 'XII%'     // Mencari XII IPA, XII IPS, dst
        ];

        $dataChart = ['hadir' => [], 'izin' => [], 'alpa' => []];

        foreach ($tingkat as $label => $pola) {
            // Hitung Hadir
            $dataChart['hadir'][] = Absensi::whereDate('tanggal', Carbon::today())
                ->where('status', 'H')
                ->whereHas('siswa', function($q) use ($pola) {
                    $q->where('kelas', 'LIKE', $pola);
                })->count();

            // Hitung Izin & Sakit (digabung)
            $dataChart['izin'][] = Absensi::whereDate('tanggal', Carbon::today())
                ->whereIn('status', ['I', 'S'])
                ->whereHas('siswa', function($q) use ($pola) {
                    $q->where('kelas', 'LIKE', $pola);
                })->count();

            // Hitung Alpa
            $dataChart['alpa'][] = Absensi::whereDate('tanggal', Carbon::today())
                ->where('status', 'A')
                ->whereHas('siswa', function($q) use ($pola) {
                    $q->where('kelas', 'LIKE', $pola);
                })->count();
        }

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

    public function destroyJadwal($id)
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

    public function cetakRaport($id)
    {
        $siswa = Siswa::with(['dataKelas.guru'])->findOrFail($id);

        $setting = DB::table('settings')->first() ?: (object)[
            'semester' => '1',
            'tahun_ajaran' => '2025/2026',
            'nama_kepsek' => 'Nama Kepala Sekolah, M.Pd',
            'nip_kepsek' => '19283746500001'
        ];
        
        if ($siswa->dataKelas && $siswa->dataKelas->guru) {
            $nama_wali = $siswa->dataKelas->guru->nama;
            $nip = $siswa->dataKelas->guru->nip;
        } else {
            $kelasManual = Kelas::where('nama_kelas', $siswa->kelas)->first();
            if ($kelasManual && $kelasManual->guru) {
                $nama_wali = $kelasManual->guru->nama;
                $nip = $kelasManual->guru->nip;
            } else {
                $nama_wali = "Wali Kelas Belum Diset";
                $nip = "-";
            }
        }

        $nama_kepsek = $setting->nama_kepsek ?? 'Nama Kepala Sekolah';
        $nip_kepsek  = $setting->nip_kepsek ?? '-';

        $mapels = Mapel::orderBy('nama_mapel', 'asc')->get();
        $siswasInKelas = $this->prosesDataNilai($siswa->kelas, $mapels);
        $currentSiswa = $siswasInKelas->where('id', $id)->first();

        $dataRaport = [];
        foreach ($mapels as $mapel) {
            $field = "nilai_mapel_" . $mapel->id;
            $dataRaport[] = [
                'mapel' => $mapel->nama_mapel,
                'akhir' => $currentSiswa->$field ?? 0
            ];
        }

        $absensi = [
            'sakit' => Absensi::where('siswa_id', $id)->where('status', 'S')->count(),
            'izin'  => Absensi::where('siswa_id', $id)->where('status', 'I')->count(),
            'alfa'  => Absensi::where('siswa_id', $id)->where('status', 'A')->count(),
        ];

        return view('guru.raport_pdf', compact(
            'siswa', 'dataRaport', 'absensi', 'setting', 
            'nama_wali', 'nip', 'nama_kepsek', 'nip_kepsek'
        ));
    }

    private function prosesDataNilai($kelas, $mapels)
    {
        $siswas = Siswa::where('kelas', $kelas)->orderBy('nama', 'asc')->get();
        foreach ($siswas as $siswa) {
            $totalNilaiSiswa = 0;
            $jumlahMapelTerhitung = 0;

            foreach ($mapels as $mapel) {
                $jadwal = Jadwal::where('mapel_id', $mapel->id)->where('kelas', $kelas)->first();
                $akhir = 0;
                if ($jadwal) {
                    $nilaiData = Nilai::where('siswa_id', $siswa->id)->where('jadwal_id', $jadwal->id)->get();
                    $harian = $nilaiData->where('jenis', 'harian')->first()->nilai ?? 0;
                    $uts    = $nilaiData->where('jenis', 'uts')->first()->nilai ?? 0;
                    $uas    = $nilaiData->where('jenis', 'uas')->first()->nilai ?? 0;
                    $akhir = ($harian * 0.4) + ($uts * 0.3) + ($uas * 0.3);
                }
                $siswa->{"nilai_mapel_" . $mapel->id} = round($akhir, 2);
                if($akhir > 0) { $totalNilaiSiswa += $akhir; $jumlahMapelTerhitung++; }
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

    public function updateAbsensi(Request $request, $id)
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

    public function destroyAbsensi($id)
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

    public function destroyNotifikasi($id)
    {
        Notifikasi::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Notifikasi berhasil dihapus!');
    }
}