<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Jadwal;
use App\Models\Guru;
use App\Models\Mapel;

class JadwalController extends Controller
{
    public function index()
    {
        $jadwals = Jadwal::with('guru','mapel')->get();
        return view('jadwal.index', compact('jadwals'));

        $query =
        Jadwal::with(['guru', 'mapel']);

        //filter kelas
        if ($request->kelas) {
            $query->where('kelas', 'like', '%' . $request->kelas . '%');
        }

        //filter hari
        if ($request->hari) {
            $query->where('hari', $request->hari );
        }

        $jadwal = $query->lastest()->get();
            return view('admin.jadwal.index', compact('jadwal'));
    }

    public function create()
    {
        $gurus = Guru::all();
        $mapels = Mapel::all();
        return view('jadwal.create', compact('gurus','mapels'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_guru' => 'required|exists:gurus,id',
            'id_mapel' => 'required|exists:mapels,id',
            'kelas' => 'required',
            'hari' => 'required',
            'jam' => 'required',
        ]);

        Jadwal::create($request->all());
        return redirect()->route('jadwal.index')->with('success', 'Jadwal berhasil ditambahkan!');
    }

    public function edit(Jadwal $jadwal)
    {
        $gurus = Guru::all();
        $mapels = Mapel::all();
        return view('jadwal.edit', compact('jadwal','gurus','mapels'));
    }

    public function update(Request $request, Jadwal $jadwal)
    {
        $request->validate([
            'id_guru' => 'required|exists:gurus,id',
            'id_mapel' => 'required|exists:mapels,id',
            'kelas' => 'required',
            'hari' => 'required',
            'jam' => 'required',
        ]);

        $jadwal->update($request->all());
        return redirect()->route('jadwal.index')->with('success', 'Jadwal berhasil diperbarui!');
    }

    public function destroy(Jadwal $jadwal)
    {
        $jadwal->delete();
        return redirect()->route('jadwal.index')->with('success', 'Jadwal berhasil dihapus!');
    }
}