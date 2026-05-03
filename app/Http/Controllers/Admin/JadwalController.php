<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Jadwal;
use App\Models\Guru;
use App\Models\Mapel;
use Illuminate\Http\Request;

class JadwalController extends Controller
{
    // =========================
    // INDEX + SEARCH
    // =========================
   public function index(Request $request)
{
    $query = Jadwal::with(['guru', 'mapel']);

    if ($request->filled('kelas')) {
        $query->where('kelas', 'like', '%' . $request->kelas . '%');
    }

    if ($request->filled('hari')) {
        $query->where('hari', $request->hari);
    }

    // UBAH INI: dari ->get() menjadi ->paginate(10)
    $jadwal = $query->paginate(10); 

    return view('admin.jadwal.index', compact('jadwal'));
}

    // =========================
    // CREATE
    // =========================
    public function create()
    {
        $gurus = Guru::all();
        $mapels = Mapel::all();

        return view('admin.jadwal.create', compact('gurus','mapels'));
    }

    // =========================
    // STORE
    // =========================
    public function store(Request $request)
    {
        $request->validate([
            'guru_id' => 'required',
            'mapel_id' => 'required',
            'kelas' => 'required',
            'hari' => 'required',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required'
        ]);

        Jadwal::create([
            'guru_id' => $request->guru_id,
            'mapel_id' => $request->mapel_id,
            'kelas' => $request->kelas,
            'hari' => $request->hari,
            'jam_mulai' => $request->jam_mulai,
            'jam_selesai' => $request->jam_selesai
        ]);

        return redirect()->route('admin.jadwal.index')
            ->with('success','Jadwal berhasil ditambahkan');
    }

    // =========================
    // EDIT
    // =========================
    public function edit(Jadwal $jadwal)
    {
        $gurus = Guru::all();
        $mapels = Mapel::all();

        return view('admin.jadwal.edit', compact('jadwal','gurus','mapels'));
    }

    // =========================
    // UPDATE
    // =========================
    public function update(Request $request, Jadwal $jadwal)
    {
        $request->validate([
            'guru_id' => 'required',
            'mapel_id' => 'required',
            'kelas' => 'required',
            'hari' => 'required',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required'
        ]);

        $jadwal->update([
            'guru_id' => $request->guru_id,
            'mapel_id' => $request->mapel_id,
            'kelas' => $request->kelas,
            'hari' => $request->hari,
            'jam_mulai' => $request->jam_mulai,
            'jam_selesai' => $request->jam_selesai
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