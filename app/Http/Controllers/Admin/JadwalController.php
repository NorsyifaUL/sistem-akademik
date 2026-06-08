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
        // Sesuaikan with(), hapus 'kelasRelation' jika di model Jadwal belum ada relasi tersebut
        $query = Jadwal::with(['guru', 'mapel']);

        // Filter berdasarkan kolom 'kelas' (bukan 'kelas_id')
        if ($request->filled('kelas')) {
            $query->where('kelas', $request->kelas);
        }

        // Filter berdasarkan hari
        if ($request->filled('hari')) {
            $query->where('hari', $request->hari);
        }

        $jadwal = $query->paginate(10);
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
        $data_kelas = Kelas::all();

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
        $data_kelas = Kelas::all();

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
            'ruangan'     => 'nullable'
        ]);

        $jadwal->update([
            'guru_id'     => $request->guru_id,
            'mapel_id'    => $request->mapel_id,
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