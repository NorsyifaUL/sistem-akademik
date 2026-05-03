<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Mapel;

class MapelController extends Controller
{
    public function index()
    {
        $mapels = Mapel::all();
        return view('mapel.index', compact('mapels'));
    }

    public function create()
    {
        return view('mapel.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_mapel' => 'required|unique:mapels,nama_mapel',
        ]);

        Mapel::create($request->all());

        return redirect()->route('mapel.index')->with('success', 'Mapel berhasil ditambahkan!');
    }

    public function edit(Mapel $mapel)
    {
        return view('mapel.edit', compact('mapel'));
    }

    public function update(Request $request, Mapel $mapel)
    {
        $request->validate([
            'nama_mapel' => 'required|unique:mapels,nama_mapel,' . $mapel->id,
        ]);

        $mapel->update($request->all());

        return redirect()->route('mapel.index')->with('success', 'Mapel berhasil diperbarui!');
    }

    public function destroy(Mapel $mapel)
    {
        $mapel->delete();
        return redirect()->route('mapel.index')->with('success', 'Mapel berhasil dihapus!');
    }
}