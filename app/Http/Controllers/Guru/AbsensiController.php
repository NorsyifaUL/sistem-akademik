<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\Siswa;
use App\Models\Jadwal;
use App\Models\Guru;
use App\Models\Kelas; // Tambahan penting agar tidak error
use App\Models\Notifikasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class AbsensiController extends Controller
{
    /**
     * 1. Dashboard Guru (Daftar Jadwal Mengajar)
     */
    public function indexGuru(): View
    {
        $hari_ini = Carbon::now()->isoFormat('dddd');
        $user = Auth::user();
        $guru = Guru::where('user_id', $user->id)->first();

        $jadwals = Jadwal::with(['mapel'])
            ->where('guru_id', $guru ? $guru->id : 0)
            ->orderByRaw("FIELD(hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu')")
            ->get();

        return view('guru.absensi.index', compact('jadwals', 'hari_ini'));
    }

    /**
     * 2. Riwayat Absensi
     */
    public function index(): View
    {
        $user = Auth::user();
        
        // 1. Logika Jika Login Sebagai GURU (Melihat Riwayat Absensi Miliknya)
        if ($user->role == 'guru') {
            $guru = Guru::where('user_id', $user->id)->first();
            
            $absensis = Absensi::with(['siswa', 'jadwal.mapel'])
                ->whereHas('jadwal', function($q) use ($guru) {
                    $q->where('guru_id', $guru->id ?? 0);
                })
                ->latest('tanggal')
                ->paginate(15);

            return view('guru.absensi.riwayat', compact('absensis'));
        } 

        // 2. Logika Jika Login Sebagai ADMIN (Melihat Semua Data)
        $absensis = Absensi::with(['siswa', 'jadwal.guru', 'jadwal.mapel'])
            ->latest('tanggal')
            ->paginate(15);

        return view('admin.absensi.index', compact('absensis'));
    }

    /**
     * 3. Form Input Presensi Massal (PERBAIKAN QUERY KELAS)
     */
    public function formAbsensi(int $jadwal_id): View
    {
        $jadwal = Jadwal::with(['guru', 'mapel'])->findOrFail($jadwal_id);
        $user = Auth::user();
        $guru = Guru::where('user_id', $user->id)->first();

        if ($user->role == 'guru') {
            abort_if(!$guru || $jadwal->guru_id !== $guru->id, 403, 'Akses ditolak.');
        }

        // --- PERBAIKAN: Cari ID Kelas berdasarkan string nama_kelas di Jadwal ---
        $kelasObj = Kelas::where('nama_kelas', $jadwal->kelas)->first();
        
        // Ambil siswa menggunakan kelas_id (menghindari error Unknown Column 'kelas')
        $siswa = Siswa::where('kelas_id', $kelasObj->id ?? 0)
            ->orderBy('nama', 'asc')
            ->get();
        
        // Menggunakan nama $sudah_absen agar cocok dengan logika radio button di Blade
        $sudah_absen = Absensi::where('jadwal_id', $jadwal_id)
            ->whereDate('tanggal', Carbon::today())
            ->get()->keyBy('siswa_id');

        $view = ($user->role == 'guru') ? 'guru.absensi.form' : 'admin.absensi.form';
        
        return view($view, compact('jadwal', 'siswa', 'sudah_absen'));
    }

    /**
     * 4. Simpan Absensi & Auto-WA
     */
    public function simpanAbsensi(Request $request): RedirectResponse
    {
        $request->validate([
            'jadwal_id' => 'required|exists:jadwals,id',
            'tanggal'   => 'required|date',
            'status'    => 'required|array', 
        ]);

        // Cek duplikasi input hari ini
        $sudahAbsen = Absensi::where('jadwal_id', $request->jadwal_id)
                            ->whereDate('tanggal', $request->tanggal)
                            ->exists();

        if ($sudahAbsen) {
            return redirect()->back()->with('error', 'Presensi hari ini sudah diisi. Silakan gunakan tombol Edit untuk mengubah data.');
        }

        $jadwal = Jadwal::with('mapel')->findOrFail($request->jadwal_id);

        foreach ($request->status as $siswa_id => $statusRaw) {
            $statusMap = [
                'H' => 'Hadir', 
                'I' => 'Izin', 
                'S' => 'Sakit', 
                'A' => 'Alfa'
            ];
            
            $inisial = strtoupper(substr($statusRaw, 0, 1));
            $status  = $statusMap[$inisial] ?? $statusRaw;

            $keteranganDefault = ($status == 'Alfa') 
                ? "Siswa tidak hadir pada mata pelajaran " . ($jadwal->mapel->nama_mapel ?? 'Mata Pelajaran') 
                : null;

            $absensi = Absensi::create([
                'siswa_id'   => $siswa_id, 
                'jadwal_id'  => $request->jadwal_id, 
                'tanggal'    => $request->tanggal,
                'status'     => $status, 
                'keterangan' => $request->keterangan[$siswa_id] ?? $keteranganDefault
            ]);

            if ($status == 'Alfa') {
                $this->kirimNotifikasiKeOrangTua($absensi);
            }
        }

        return redirect()->back()->with('success', 'Presensi berhasil disimpan.');
    }

    /**
     * 5. Edit Absensi
     */
    public function edit(int $id)
    {
        if (Auth::user()->role == 'admin') {
            return redirect()->back()->with('error', 'Admin hanya bisa memantau.');
        }

        $absensi = Absensi::with(['siswa', 'jadwal.mapel'])->findOrFail($id);
        return view('guru.absensi.edit', compact('absensi'));
    }

    /**
     * 6. Update Absensi
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $absensi = Absensi::findOrFail($id);
        $absensi->update([
            'status' => $request->status,
            'keterangan' => $request->keterangan
        ]);

        return redirect()->route('guru.absensi.rekap')->with('success', 'Data diperbarui.');
    }

    /**
     * 7. Hapus Absensi
     */
    public function destroy(int $id): RedirectResponse
    {
        Absensi::findOrFail($id)->delete();
        return back()->with('success', 'Data berhasil dihapus.');
    }

    /**
     * 8. Rekapitulasi (PERBAIKAN QUERY FILTER SISWA)
     */
    public function rekap(Request $request): View
    {
        $user = Auth::user();
        $mode = $request->mode ?? 'daily';
        $kelas_filter = $request->kelas; // Menggunakan variabel filter agar tidak tabrakan

        if ($user->role == 'guru') {
            $guru = Guru::where('user_id', $user->id)->first();
            $kelasList = Jadwal::where('guru_id', $guru->id ?? 0)
                ->select('kelas')->distinct()->get();
            $view = 'guru.absensi.rekap';
        } else {
            // Mengambil list unik dari kolom nama_kelas di tabel Kelas
            $kelasList = Kelas::select('nama_kelas as kelas')->distinct()->get();
            $view = 'admin.absensi.rekap';
        }

        $query = Absensi::with(['siswa', 'jadwal.mapel']);

        if ($mode == 'monthly') {
            $bulan = $request->bulan ?? date('m');
            $query->whereMonth('tanggal', $bulan)->whereYear('tanggal', date('Y'));
        } else {
            $tanggal = $request->tanggal ?? date('Y-m-d');
            $query->whereDate('tanggal', $tanggal);
        }

        if ($kelas_filter) {
            $query->whereHas('siswa.kelas', function($q) use ($kelas_filter) {
                $q->where('nama_kelas', $kelas_filter);
            });
        }

        if ($user->role == 'guru') {
            $guru = Guru::where('user_id', $user->id)->first();
            $query->whereHas('jadwal', function($q) use ($guru) {
                $q->where('guru_id', $guru->id ?? 0);
            });
        }

        $rekaps = $query->latest()->get();

        return view($view, compact('rekaps', 'kelasList', 'mode'))->with('kelas', $kelas_filter);
    }

    /**
     * 9. Resend WA
     */
    public function resendWa(int $id): RedirectResponse
    {
        $absensi = Absensi::findOrFail($id);

        if (!in_array(strtoupper($absensi->status), ['ALFA', 'A'])) {
            return back()->with('error', 'Hanya absensi Alfa yang bisa dikirim notifikasi.');
        }

        $hasil = $this->kirimNotifikasiKeOrangTua($absensi);

        return $hasil 
            ? back()->with('success', 'Notifikasi berhasil dikirim ulang.') 
            : back()->with('error', 'Gagal mengirim ulang notifikasi.');
    }

    /**
     * 10. Kirim WA via Fonnte
     */
    private function kirimNotifikasiKeOrangTua(Absensi $absensi): bool
    {
        $siswa = $absensi->siswa;
        if ($siswa && $siswa->no_hp_ortu) {
            $tgl = Carbon::parse($absensi->tanggal)->translatedFormat('d F Y');
            $mapel = $absensi->jadwal->mapel->nama_mapel ?? 'Mata Pelajaran';
            $pesan = "🔔 *INFO SIAKAD*\nYth. Orang Tua dari *{$siswa->nama}*,\nPutra/putri Anda tercatat *ALFA* pada:\n📅 Tanggal: {$tgl}\n📖 Mapel: {$mapel}\n\nMohon konfirmasinya.";

            try {
                $response = Http::withHeaders(['Authorization' => 'CKW3RDixtZqdnn4k5hkP'])->asForm()->post('https://api.fonnte.com/send', [
                    'target' => $siswa->no_hp_ortu, 
                    'message' => $pesan, 
                    'countryCode' => '62'
                ]);
                
                $statusWA = $response->successful() ? 'sent' : 'failed';
                
                if (Schema::hasColumn('absensis', 'status_wa')) {
                    $absensi->update(['status_wa' => $statusWA]);
                }

                Notifikasi::create([
                    'absensi_id' => $absensi->id, 
                    'siswa_id' => $siswa->id, 
                    'nomor_tujuan' => $siswa->no_hp_ortu, 
                    'isi_pesan' => $pesan, 
                    'status_kirim' => ($statusWA == 'sent' ? 'Terkirim' : 'Gagal')
                ]);

                return $response->successful();
            } catch (\Exception $e) { 
                Log::error("Gagal kirim WA: " . $e->getMessage());
                return false;
            }
        }
        return false;
    }
}