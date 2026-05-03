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
     * Menambahkan Middleware Constructor 
     * Ini akan memastikan semua fungsi di dalam controller ini 
     * hanya bisa diakses jika is_wali_kelas == 1
     */
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (Auth::user()->is_wali_kelas != 1) {
                return redirect()->route('guru.dashboard')
                    ->with('error', 'Akses Dibatalkan: Anda tidak memiliki otoritas sebagai Wali Kelas.');
            }
            return $next($request);
        });
    }

    /**
     * Menampilkan daftar siswa (Hanya untuk Wali Kelas)
     */
    public function index()
    {
        $user = Auth::user();

        // Double Check: Memastikan kolom wali_kelas di tabel users tidak kosong
        if (empty($user->wali_kelas)) {
            return redirect()->route('guru.dashboard')
                ->with('error', 'Data Wali Kelas Anda belum disetting oleh Admin.');
        }

        $setting = Setting::first() ?? new Setting();
        $namaKelasUser = trim($user->wali_kelas);

        // Ambil info kelas untuk header halaman
        $infoKelas = Kelas::where('nama_kelas', $namaKelasUser)->first();

        // Kunci data: Hanya ambil siswa yang nama kelasnya sesuai dengan user login
        $siswas = Siswa::where('kelas', $namaKelasUser)
            ->orderBy('nama', 'asc')
            ->get();

        return view('guru.raport.index', compact('user', 'setting', 'siswas', 'infoKelas'));
    }

    /**
     * Proses Cetak PDF Raport
     */
    public function cetak($siswaId)
    {
        $user = Auth::user(); 
        $siswa = Siswa::findOrFail($siswaId);
        
        // Proteksi Tambahan: Mencegah manipulasi URL ID Siswa dari kelas lain
        if (trim($siswa->kelas) !== trim($user->wali_kelas)) {
            abort(403, 'Tindakan Ilegal: Anda hanya diizinkan mencetak raport kelas ' . $user->wali_kelas);
        }

        $setting = Setting::first() ?? new Setting();

        // Load semua record terkait siswa
        $allRecords = Nilai::with(['jadwal.mapel'])
            ->where('siswa_id', $siswa->id)
            ->get();

        // Filter Akademik
        $dataRaport = $allRecords->whereIn('jenis', ['harian', 'uts', 'uas'])
            ->groupBy('jadwal_id')
            ->map(function ($items) {
                $item = $items->first();
                return [
                    'mapel' => $item->jadwal->mapel->nama_mapel ?? 'Mata Pelajaran', 
                    'akhir' => round($items->avg('nilai')), 
                    'capaian_kompetensi' => 'Menunjukkan penguasaan yang sangat baik dalam memahami materi.'
                ];
            })->values();

        // Filter Sikap
        $dataSikap = $allRecords->where('jenis', 'sikap')->map(function($s) {
            return [
                'aspek' => $s->aspek,
                'predikat' => $s->predikat ?? 'BAIK',
                'keterangan' => $s->keterangan
            ];
        })->values();

        // Filter Eskul
        $dataEskul = $allRecords->where('jenis', 'eskul')->map(function($e) {
            return [
                'aspek' => $e->aspek,
                'nilai' => $e->nilai ?? 'A',
                'keterangan' => $e->keterangan
            ];
        })->values();

        // Data Absensi
        $absensiRecord = Absensi::where('siswa_id', $siswa->id)->first();
        $absensi = [
            'sakit' => $absensiRecord->sakit ?? 0,
            'izin'  => $absensiRecord->izin ?? 0,
            'alfa'  => $absensiRecord->alfa ?? 0,
        ];

        // Kelengkapan Berkas
        $semester = ($setting->semester == '1') ? 'Ganjil' : 'Genap';
        $tgl_cetak = now()->translatedFormat('d F Y');
        $nama_kepsek = $setting->nama_kepsek ?? 'Nama Kepala Sekolah';
        $nip_kepsek  = $setting->nip_kepsek  ?? 'NIP Kepala Sekolah';
        $nama_wali   = $user->name;
        $nip         = $user->nip ?? '...........................'; 
        
        $catatan_wali = $dataSikap->first()['keterangan'] ?? 'Tingkatkan terus prestasi dan semangat belajarnya.';

        $pdf = Pdf::loadView('guru.raport_pdf', compact(
            'siswa', 'setting', 'user', 'dataRaport', 'dataSikap', 'dataEskul',
            'absensi', 'nama_kepsek', 'nip_kepsek', 'nama_wali', 'nip',
            'semester', 'tgl_cetak', 'catatan_wali'
        ));

        return $pdf->setPaper('a4', 'portrait')
                   ->stream('Raport_'.$siswa->nama.'.pdf');
    }
}