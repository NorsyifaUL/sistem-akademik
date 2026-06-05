<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Guru;
use Illuminate\Http\Request;

class KelasController extends Controller
{
    public function index()
    {
        // Tetap menggunakan with() agar performa cepat (menghindari N+1)
        $kelas = Kelas::with(['guru.user'])->get();
        return view('admin.kelas.index', compact('kelas'));
    }

    public function create()
    {
        $gurus = Guru::all();
        return view('admin.kelas.create', compact('gurus'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_kelas' => 'required|unique:kelas,nama_kelas',
            'guru_id'    => 'nullable|exists:gurus,id|unique:kelas,guru_id'
        ], [
            'guru_id.unique' => 'Guru ini sudah menjadi wali kelas di kelas lain!'
        ]);

        // Cukup create, event 'saved' di Model akan menangani sinkronisasi ke tabel User
        Kelas::create($request->all());

        return redirect()->route('admin.kelas.index')->with('success', 'Kelas berhasil ditambahkan');
    }

    public function edit($id)
    {
        $kela = Kelas::findOrFail($id);
        $gurus = Guru::all();
        return view('admin.kelas.edit', compact('kela', 'gurus'));
    }

    public function update(Request $request, $id)
    {
        $kela = Kelas::findOrFail($id);

        $request->validate([
            'nama_kelas' => 'required|unique:kelas,nama_kelas,' . $kela->id,
            'guru_id'    => 'nullable|exists:gurus,id|unique:kelas,guru_id,' . $kela->id,
        ], [
            'guru_id.unique' => 'Guru ini sudah menjadi wali kelas di kelas lain!'
        ]);

        // Cukup update, event 'saved' di Model akan menangani pembersihan data lama & sinkronisasi baru
        $kela->update($request->all());

        return redirect()->route('admin.kelas.index')->with('success', 'Kelas berhasil diupdate');
    }

    public function destroy($id)
    {
        $kela = Kelas::findOrFail($id);

        if ($kela->siswas()->count() > 0) {
            return redirect()->back()->with('error', 'Kelas tidak bisa dihapus karena masih memiliki siswa!');
        }

        // Cukup delete, event 'deleted' di Model akan menangani pembersihan data wali_kelas di tabel User
        $kela->delete();

        return redirect()->route('admin.kelas.index')->with('success', 'Kelas berhasil dihapus');
    }
}