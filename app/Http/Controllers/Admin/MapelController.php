<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Mapel;
use Illuminate\Http\Request;

class MapelController extends Controller
{

    public function index(Request $request)
    {
        $search = $request->get('search');
        
        // Pastikan di akhir menggunakan ->paginate(10) BUKAN ->get()
        $mapels = Mapel::where('nama_mapel', 'like', '%' . $search . '%')
                    ->paginate(10); 

        return view('admin.mapel.index', compact('mapels'));
    }

    public function create()
    {
        return view('admin.mapel.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_mapel' => 'required|unique:mapels,nama_mapel'
        ]);

        Mapel::create([
            'nama_mapel' => $request->nama_mapel
        ]);

        return redirect()->route('admin.mapel.index')
            ->with('success', 'Mata pelajaran berhasil ditambahkan');
    }

    public function edit(Mapel $mapel)
    {
        return view('admin.mapel.edit', compact('mapel'));
    }

    public function update(Request $request, Mapel $mapel)
    {
        $request->validate([
            'nama_mapel' => 'required|unique:mapels,nama_mapel,' . $mapel->id
        ]);

        $mapel->update([
            'nama_mapel' => $request->nama_mapel
        ]);

        return redirect()->route('admin.mapel.index')
            ->with('success', 'Mata pelajaran berhasil diperbarui');
    }

    public function destroy(Mapel $mapel)
    {
        $mapel->delete();

        return redirect()->route('admin.mapel.index')
            ->with('success', 'Mata pelajaran berhasil dihapus');
    }
}