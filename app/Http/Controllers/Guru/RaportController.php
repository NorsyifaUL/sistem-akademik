<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use App\Models\Setting;
use App\Models\Nilai; 
use App\Models\Absensi; 
use App\Models\Kelas;
use App\Models\Jadwal;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class RaportController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (empty(Auth::user()->wali_kelas)) {
                return redirect()->route('guru.dashboard')
                    ->with('error', 'Akses Dibatalkan: Anda tidak memiliki otoritas sebagai Wali Kelas.');
            }
            return $next($request);
        });
    }

    public function index()
    {
        $user = Auth::user();
        if (empty($user->wali_kelas)) {
            return redirect()->route('guru.dashboard')->with('error', 'Data Wali Kelas belum disetting.');
        }

        $setting = Setting::first() ?? new Setting();
        $namaKelasUser = trim($user->wali_kelas);
        $infoKelas = Kelas::where('nama_kelas', $namaKelasUser)->first();

        $siswas = $infoKelas ? Siswa::where('kelas_id', $infoKelas->id)->orderBy('nama', 'asc')->get() : collect();

        return view('guru.raport.index', compact('user', 'setting', 'siswas', 'infoKelas'));
    }

    public function cetak(int $siswaId)
    {
        $user = Auth::user(); 
        $siswa = Siswa::findOrFail($siswaId);
        $infoKelas = Kelas::where('nama_kelas', trim($user->wali_kelas))->first();

        if (!$infoKelas || (int)$siswa->kelas_id !== (int)$infoKelas->id) {
            abort(403, 'Tindakan Ilegal: Anda hanya diizinkan mencetak raport kelas Anda.');
        }

        $setting = Setting::first() ?? new Setting();
        
        // SINKRONISASI 1: Menggunakan string 'Genap' sesuai data database kamu
        $semesterDatabase = 'Genap';

        $allRecords = Nilai::with(['jadwal.mapel'])
            ->where('siswa_id', $siswa->id)
            ->where('semester', $semesterDatabase)
            ->get();

        /**
         * SINKRONISASI 2: Menggunakan kolom 'kelas' (tanpa 'kelas_id')
         * sesuai struktur tabel jadwals kamu.
         */
        $allJadwal = Jadwal::with('mapel')
            ->where('kelas', $infoKelas->nama_kelas)
            ->get();

        $dataRaport = $allJadwal->map(function ($jadwal) use ($allRecords) {
            $namaMapelJadwal = $jadwal->mapel->nama_mapel ?? ($jadwal->mapel->nama ?? 'Mata Pelajaran');

            $nilaiSiswa = $allRecords->where('jadwal_id', $jadwal->id)
                                     ->where('jenis', 'rekap')
                                     ->first();

            if ($nilaiSiswa) {
                $nilaiAkhir = $nilaiSiswa->nilai;
                $capaian = $nilaiSiswa->keterangan;
            } else {
                $nilaiMapel = $allRecords->where('jadwal_id', $jadwal->id);
                $nilaiHarian = $nilaiMapel->filter(fn($n) => in_array(strtolower($n->jenis), ['harian', 'uh']));
                $rataUH = $nilaiHarian->count() > 0 ? $nilaiHarian->avg('nilai') : 0;
                
                $uts = $nilaiMapel->filter(fn($n) => strtolower($n->jenis) == 'uts')->first()->nilai ?? 0;
                $uas = $nilaiMapel->filter(fn($n) => strtolower($n->jenis) == 'uas')->first()->nilai ?? 0;

                $totalMurni = ($rataUH * 0.4) + ($uts * 0.3) + ($uas * 0.3);
                $nilaiAkhir = $totalMurni > 0 ? round($totalMurni) : 0;
                $capaian = null;
            }

            if (empty($capaian)) {
                $capaian = $nilaiAkhir >= 70 ? "Siswa telah mencapai standar kompetensi dengan hasil yang baik." : "Siswa masih dalam tahap pendampingan.";
            }

            return [
                'mapel' => $namaMapelJadwal, 
                'akhir' => $nilaiAkhir > 0 ? $nilaiAkhir : '-',
                'capaian_kompetensi' => $capaian
            ];
        })->values();

        $dataSikap = $allRecords->where('jenis', 'sikap')->map(fn($s) => [
            'aspek' => $s->aspek ?? 'Sikap',
            'predikat' => $s->predikat ?? 'B',
            'keterangan' => $s->keterangan ?? 'Menunjukkan sikap positif.'
        ])->values();

        $eskul = $allRecords->whereIn('jenis', ['eskul', 'ekstra'])->map(fn($e) => [
            'kegiatan' => $e->aspek ?? 'Kegiatan', 
            'nilai' => $e->predikat ?? $e->nilai ?? '-',
            'keterangan' => $e->keterangan ?? 'Aktif mengikuti kegiatan.'
        ])->values();

        $absensi = [
            'sakit' => Absensi::where('siswa_id', $siswa->id)->whereIn('status', ['S', 's', 'Sakit'])->count(),
            'izin'  => Absensi::where('siswa_id', $siswa->id)->whereIn('status', ['I', 'i', 'Izin'])->count(),
            'alpa'  => Absensi::where('siswa_id', $siswa->id)->whereIn('status', ['A', 'a', 'Alpa', 'TK'])->count(),
        ];

        $semester_nama = $setting->semester ?? '2';
        $nama_wali = $user->guru->nama ?? $user->name;
        $nip = $user->guru->nip ?? '...........................'; 
        $catatan_wali = $allRecords->where('jenis', 'sikap')->first()->keterangan ?? 'Pertahankan prestasi.';

        $pdf = Pdf::loadView('guru.raport_pdf', compact(
            'siswa', 'setting', 'user', 'dataRaport', 'dataSikap', 'eskul',
            'absensi', 'nama_wali', 'nip', 'semester_nama', 'catatan_wali'
        ));

        return $pdf->setPaper('a4', 'portrait')
                   ->stream('Raport_'.$siswa->nama.'.pdf');
    }
}