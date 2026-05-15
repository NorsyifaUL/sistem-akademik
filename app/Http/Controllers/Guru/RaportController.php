<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use App\Models\Setting;
use App\Models\Nilai; 
use App\Models\Absensi; 
use App\Models\Kelas;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class RaportController extends Controller
{
    /**
     * Middleware Constructor
     */
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            // Memastikan user adalah Wali Kelas
            if (Auth::user()->is_wali_kelas != 1) {
                return redirect()->route('guru.dashboard')
                    ->with('error', 'Akses Dibatalkan: Anda tidak memiliki otoritas sebagai Wali Kelas.');
            }
            return $next($request);
        });
    }

    /**
     * Menampilkan daftar siswa berdasarkan kelas yang diampu
     */
    public function index()
    {
        $user = Auth::user();

        if (empty($user->wali_kelas)) {
            return redirect()->route('guru.dashboard')
                ->with('error', 'Data Wali Kelas Anda belum disetting oleh Admin.');
        }

        $setting = Setting::first() ?? new Setting();
        $namaKelasUser = trim($user->wali_kelas);
        
        // Cari info kelas untuk mendapatkan data tambahan jika diperlukan
        $infoKelas = Kelas::where('nama_kelas', $namaKelasUser)->first();

        // Mengambil siswa yang terdaftar di kelas wali kelas tersebut
        $siswas = Siswa::where('kelas', 'LIKE', '%' . $namaKelasUser . '%')
            ->orderBy('nama', 'asc')
            ->get();

        return view('guru.raport.index', compact('user', 'setting', 'siswas', 'infoKelas'));
    }

    /**
     * Proses Cetak PDF Raport
     */
    public function cetak(int $siswaId)
    {
        $user = Auth::user(); 
        $siswa = Siswa::findOrFail($siswaId);
        
        // Proteksi Keamanan: Wali kelas hanya bisa cetak raport kelasnya sendiri
        if (trim($siswa->kelas) !== trim($user->wali_kelas)) {
            abort(403, 'Tindakan Ilegal: Anda hanya diizinkan mencetak raport siswa di kelas ' . $user->wali_kelas);
        }

        $setting = Setting::first() ?? new Setting();
        $semesterAktif = trim($setting->semester ?? 'Ganjil');

        // Ambil semua record nilai siswa pada semester aktif
        $allRecords = Nilai::with(['jadwal.mapel'])
            ->where('siswa_id', $siswa->id)
            ->where('semester', $semesterAktif)
            ->get();

        /**
         * 1. LOGIKA UTAMA: Ambil Nilai Akhir & Deskripsi Capaian
         */
        $dataRaport = $allRecords->where('jenis', 'rekap')
            ->map(function ($item) {
                return [
                    'mapel' => $item->jadwal->mapel->nama_mapel ?? 'Mata Pelajaran', 
                    'akhir' => $item->nilai,
                    'capaian_kompetensi' => $item->keterangan ?? 'Peserta didik telah menunjukkan progres belajar sesuai kriteria yang ditetapkan.' 
                ];
            })->values();

        /**
         * 2. Filter Nilai Sikap
         */
        $dataSikap = $allRecords->where('jenis', 'sikap')->map(function($s) {
            return [
                'aspek' => $s->aspek,
                'predikat' => $s->predikat ?? 'BAIK',
                'keterangan' => $s->keterangan ?? 'Menunjukkan sikap yang positif dalam kegiatan pembelajaran.'
            ];
        })->values();

        /**
         * 3. Filter Nilai Ekstrakurikuler
         */
        // Mapping eskul agar variabel di blade ($eskul) cocok dengan controller ($dataEskul)
        $eskul = $allRecords->where('jenis', 'eskul')->map(function($e) {
            return [
                'kegiatan' => $e->aspek, // disesuaikan dengan key 'kegiatan' di blade
                'nilai' => $e->nilai ?? 'A',
                'keterangan' => $e->keterangan ?? 'Aktif dan berpartisipasi dengan baik.'
            ];
        })->values();

        /**
         * 4. LOGIKA ABSENSI (UPDATE): Menghitung jumlah berdasarkan status di DB
         */
        $allAbsensi = Absensi::where('siswa_id', $siswa->id)
            ->where('semester', $semesterAktif)
            ->get();

        $absensi = [
            'sakit' => $allAbsensi->where('status', 'Sakit')->count(),
            'izin'  => $allAbsensi->where('status', 'Izin')->count(),
            'alpa'  => $allAbsensi->where('status', 'Alpa')->count(),
        ];

        // Variabel pendukung tampilan PDF
        $semester_nama = $semesterAktif;
        $tgl_cetak = now()->translatedFormat('d F Y');
        $nama_kepsek = $setting->nama_kepsek ?? 'Nama Kepala Sekolah';
        $nip_kepsek  = $setting->nip_kepsek  ?? 'NIP Kepala Sekolah';
        $nama_wali   = $user->name;
        $nip         = $user->nip ?? '...........................'; // key 'nip' disesuaikan dengan blade
        
        // Catatan wali kelas
        $catatan_wali = $dataSikap->first()['keterangan'] ?? 'Terus pertahankan semangat belajar dan prestasi yang telah diraih.';

        // Inisialisasi PDF
        $pdf = Pdf::loadView('guru.raport_pdf', compact(
            'siswa', 'setting', 'user', 'dataRaport', 'dataSikap', 'eskul',
            'absensi', 'nama_kepsek', 'nip_kepsek', 'nama_wali', 'nip',
            'semester_nama', 'tgl_cetak', 'catatan_wali'
        ));

        return $pdf->setPaper('a4', 'portrait')
                   ->stream('Raport_'.$siswa->nama.'_Smt'.$semester_nama.'.pdf');
    }
}