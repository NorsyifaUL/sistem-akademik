<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Absensi;
use App\Models\Kelas;
use App\Models\Setting;
use App\Models\Nilai; 
use App\Models\Siswa;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class AbsensiController extends Controller
{
    public function index(Request $request)
    {
        $setup = Setting::first();

        // 1. Tangkap parameter filter
        $mode = $request->get('mode', 'harian');
        $filterKelas = $request->get('kelas');
        $tanggal = $request->get('filter_date', date('Y-m-d'));
        $bulan = $request->get('filter_month', date('m'));
        $semester = $request->get('semester', $setup->semester ?? '1');
        $tahun_ajaran = $request->get('tahun_ajaran', $setup->tahun_ajaran ?? '');

        $query = Absensi::with(['siswa.dataKelas', 'jadwal.mapel'])
                        ->whereHas('jadwal');

        // 2. Logika Filter Waktu
        if ($mode == 'bulanan') {
            $query->whereMonth('tanggal', $bulan)
                  ->whereYear('tanggal', date('Y', strtotime($tanggal)));
        } else {
            $query->whereDate('tanggal', $tanggal);
        }

        // 3. Filter Semester
        if ($semester == '1') {
            $query->whereMonth('tanggal', '>=', '07')->whereMonth('tanggal', '<=', '12');
        } else {
            $query->whereMonth('tanggal', '>=', '01')->whereMonth('tanggal', '<=', '06');
        }

        // 4. Filter Kelas
        if ($filterKelas) {
            $query->whereHas('siswa.dataKelas', function($q) use ($filterKelas) {
                $q->where('nama_kelas', $filterKelas);
            });
        }

        $absensis = $query->latest('tanggal')->get();
        
        $listKelas = Kelas::orderBy('nama_kelas', 'asc')->get(); 
        $listTahun = Nilai::distinct()->pluck('tahun_ajaran');
        
        if($listTahun->isEmpty() && isset($setup->tahun_ajaran)) {
            $listTahun = collect([$setup->tahun_ajaran]);
        }

        $months = [
            '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', 
            '04' => 'April', '05' => 'Mei', '06' => 'Juni', 
            '07' => 'Juli', '08' => 'Agustus', '09' => 'September', 
            '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
        ];

        return view('admin.absensi.index', compact(
            'absensis', 'listKelas', 'listTahun', 'setup', 'mode', 
            'months', 'filterKelas'
        ));
    }

    public function edit(int $id)
    {
        $absensi = Absensi::with('siswa')->findOrFail($id);
        return view('admin.absensi.edit', compact('absensi'));
    }

    public function update(Request $request, int $id)
    {
        $request->validate([
            'status' => 'required|in:Hadir,Sakit,Izin,Alpa,H,S,I,A',
            'keterangan' => 'nullable|string|max:255'
        ]);

        $absensi = Absensi::findOrFail($id);
        
        $statusMap = ['H' => 'Hadir', 'I' => 'Izin', 'S' => 'Sakit', 'A' => 'Alpa'];
        $input = strtoupper($request->status);
        $statusInput = $statusMap[$input] ?? $request->status;

        $absensi->update([
            'status' => $statusInput,
            'keterangan' => $request->keterangan
        ]);

        return redirect()->route('admin.absensi.index')->with('success', 'Data presensi berhasil dikoreksi.');
    }

    public function cetak(Request $request)
    {
        $setup = Setting::first();
        $bulan = $request->get('filter_month', date('m'));
        $tahun = date('Y', strtotime($request->get('filter_date', date('Y-m-d'))));
        $filterKelas = $request->get('kelas');

        $jumlah_hari = Carbon::createFromDate($tahun, (int)$bulan, 1)->daysInMonth;
        
        // Optimasi: Load semua absensi bulan tersebut sekaligus untuk mengurangi query dalam loop
        $semuaAbsensi = Absensi::whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->whereHas('siswa', function($q) use ($filterKelas) {
                if($filterKelas) $q->whereHas('dataKelas', fn($qc) => $qc->where('nama_kelas', $filterKelas));
            })->get();

        $siswas = Siswa::whereHas('dataKelas', function($q) use ($filterKelas) {
            if($filterKelas) $q->where('nama_kelas', $filterKelas);
        })->orderBy('nama', 'asc')->get();

        $data_rekap = $siswas->map(function ($siswa) use ($jumlah_hari, $semuaAbsensi, $bulan, $tahun) {
            $hari = [];
            for ($tgl = 1; $tgl <= $jumlah_hari; $tgl++) {
                $absen = $semuaAbsensi->first(function($a) use ($siswa, $tgl, $bulan, $tahun) {
                    return $a->siswa_id == $siswa->id && 
                           (int)$a->tanggal->format('d') == $tgl;
                });

                $hari[$tgl] = $absen ? strtoupper(substr($absen->status, 0, 1)) : '.';
            }

            return [
                'nama' => $siswa->nama,
                'hari' => $hari,
                'total' => [
                    'H' => collect($hari)->filter(fn($v) => $v == 'H')->count(),
                    'S' => collect($hari)->filter(fn($v) => $v == 'S')->count(),
                    'I' => collect($hari)->filter(fn($v) => $v == 'I')->count(),
                    'A' => collect($hari)->filter(fn($v) => $v == 'A')->count(),
                ]
            ];
        });

        $pdf = Pdf::loadView('admin.absensi.pdf', [
            'data' => $data_rekap,
            'bulan_teks' => $this->getNamaBulan((int)$bulan),
            'tahun' => $tahun,
            'jumlah_hari' => $jumlah_hari,
            'kelas' => $filterKelas ?? 'SEMUA KELAS',
            'setting' => $setup
        ])->setPaper('a4', 'landscape');

        return $pdf->stream('Rekap_Absensi_' . ($filterKelas ?? 'Semua') . '.pdf');
    }

    private function getNamaBulan(int $bulan)
    {
        $bulanStr = str_pad($bulan, 2, '0', STR_PAD_LEFT);
        $daftarBulan = ['01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April', '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus', '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'];
        return $daftarBulan[$bulanStr] ?? '';
    }

    public function destroy(int $id)
    {
        Absensi::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Data berhasil dihapus!');
    }
}