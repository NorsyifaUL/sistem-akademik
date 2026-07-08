<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Jadwal;
use App\Models\Guru;
use App\Models\Mapel;
use App\Models\Kelas;
use Illuminate\Http\Request;

class JadwalController extends Controller
{
    // =========================
    // INDEX + SEARCH
    // =========================
 public function index(Request $request)
    {
        // 1. Inisialisasi query dengan relasi
        $query = Jadwal::with(['guru', 'mapel']);

        // 2. Filter Kelas berdasarkan nama kelas
        if ($request->filled('kelas_id')) {
            $kelas = \App\Models\Kelas::find($request->kelas_id);
            
            if ($kelas) {
                // Menggunakan trim() untuk menghapus spasi, 
                // dan 'like' untuk pencarian yang lebih fleksibel
                $namaKelasBersih = trim($kelas->nama_kelas);
                $query->where('kelas', 'like', '%' . $namaKelasBersih . '%');
            }
        }

        // 3. Filter Hari
        if ($request->filled('hari')) {
            $query->where('hari', $request->hari);
        }

        // 4. Ambil data dengan pagination
        $jadwal = $query->latest()->paginate(10);

        // 5. Ambil semua data kelas dan urutkan berdasarkan nama_kelas secara ascending
        $data_kelas = \App\Models\Kelas::orderBy('nama_kelas', 'asc')->get(); 

        return view('admin.jadwal.index', compact('jadwal', 'data_kelas'));
    }

    // =========================
    // CREATE
    // =========================
    public function create()
{
    $gurus = Guru::all();
    $mapels = Mapel::all();
    
    // Menambahkan orderBy agar urutan kelas di dropdown menjadi rapi
    $data_kelas = Kelas::orderBy('nama_kelas', 'asc')->get();

    return view('admin.jadwal.create', compact('gurus', 'mapels', 'data_kelas'));
}

    // =========================
    // STORE
    // =========================
    public function store(Request $request)
    {
        $request->validate([
            'guru_id'     => 'required',
            'mapel_id'    => 'required',
            'kelas'       => 'required', // Kolom ini yang ada di database
            'hari'        => 'required',
            'jam_mulai'   => 'required',
            'jam_selesai' => 'required',
        ]);

        Jadwal::create([
            'guru_id'     => $request->guru_id,
            'mapel_id'    => $request->mapel_id,
            'kelas'       => $request->kelas,
            'hari'        => $request->hari,
            'jam_mulai'   => $request->jam_mulai,
            'jam_selesai' => $request->jam_selesai,
        ]);

        return redirect()->route('admin.jadwal.index')
            ->with('success', 'Jadwal berhasil ditambahkan');
    }

    // =========================
    // EDIT
    // =========================
    public function edit(Jadwal $jadwal)
    {
        $gurus = Guru::all();
        $mapels = Mapel::all();
        
        // Menambahkan orderBy agar urutan kelas di dropdown tetap rapi saat edit
        $data_kelas = Kelas::orderBy('nama_kelas', 'asc')->get();
    
        return view('admin.jadwal.edit', compact('jadwal', 'gurus', 'mapels', 'data_kelas'));
    }

    // =========================
    // UPDATE
    // =========================
    public function update(Request $request, Jadwal $jadwal)
    {
        $request->validate([
            'guru_id'     => 'required',
            'mapel_id'    => 'required',
            'kelas'       => 'required',
            'hari'        => 'required',
            'jam_mulai'   => 'required',
            'jam_selesai' => 'required',
        ]);

        $jadwal->update([
            'guru_id'     => $request->guru_id,
            'mapel_id'    => $request->mapel_id,
            'kelas'       => $request->kelas,
            'hari'        => $request->hari,
            'jam_mulai'   => $request->jam_mulai,
            'jam_selesai' => $request->jam_selesai,
        ]);

        return redirect()->route('admin.jadwal.index')
            ->with('success', 'Jadwal berhasil diperbarui');
    }

    // =========================
    // DELETE
    // =========================
    public function destroy(Jadwal $jadwal)
    {
        $jadwal->delete();

        return redirect()->route('admin.jadwal.index')
            ->with('success', 'Jadwal berhasil dihapus');
    }
}