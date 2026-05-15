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
        // Menggunakan 'kelasRelation' sesuai dengan nama fungsi di model Jadwal
        $query = Jadwal::with(['guru', 'mapel', 'kelasRelation']);

        // Filter berdasarkan kelas_id (untuk relasi tabel kelas)
        if ($request->filled('kelas_id')) {
            $query->where('kelas_id', $request->kelas_id);
        }

        // Filter berdasarkan hari
        if ($request->filled('hari')) {
            $query->where('hari', $request->hari);
        }

        $jadwal = $query->paginate(10);
        
        // Ambil semua data kelas untuk isi dropdown filter
        $data_kelas = Kelas::all(); 

        return view('admin.jadwal.index', compact('jadwal', 'data_kelas'));
    }

    // =========================
    // CREATE
    // =========================
    public function create()
    {
        $gurus = Guru::all();
        $mapels = Mapel::all();
        $data_kelas = Kelas::all(); // Menambahkan data kelas untuk pilihan di form

        return view('admin.jadwal.create', compact('gurus', 'mapels', 'data_kelas'));
    }

    // =========================
    // STORE
    // =========================
    public function store(Request $request)
    {
        $request->validate([
            'guru_id'    => 'required',
            'mapel_id'   => 'required',
            'kelas_id'   => 'nullable', // Menangani id relasi
            'kelas'      => 'required', // Menangani string nama kelas
            'hari'       => 'required',
            'jam_mulai'  => 'required',
            'jam_selesai' => 'required',
            'ruangan'    => 'nullable'
        ]);

        Jadwal::create([
            'guru_id'     => $request->guru_id,
            'mapel_id'    => $request->mapel_id,
            'kelas_id'    => $request->kelas_id, 
            'kelas'       => $request->kelas,
            'hari'        => $request->hari,
            'jam_mulai'   => $request->jam_mulai,
            'jam_selesai' => $request->jam_selesai,
            'ruangan'     => $request->ruangan
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
        $data_kelas = Kelas::all(); // Menambahkan data kelas untuk pilihan di form

        return view('admin.jadwal.edit', compact('jadwal', 'gurus', 'mapels', 'data_kelas'));
    }

    // =========================
    // UPDATE
    // =========================
    public function update(Request $request, Jadwal $jadwal)
    {
        $request->validate([
            'guru_id'    => 'required',
            'mapel_id'   => 'required',
            'kelas_id'   => 'nullable',
            'kelas'      => 'required',
            'hari'       => 'required',
            'jam_mulai'  => 'required',
            'jam_selesai' => 'required',
            'ruangan'    => 'nullable'
        ]);

        $jadwal->update([
            'guru_id'     => $request->guru_id,
            'mapel_id'    => $request->mapel_id,
            'kelas_id'    => $request->kelas_id,
            'kelas'       => $request->kelas,
            'hari'        => $request->hari,
            'jam_mulai'   => $request->jam_mulai,
            'jam_selesai' => $request->jam_selesai,
            'ruangan'     => $request->ruangan
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