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
        // Mengambil data kelas beserta relasi guru dan usernya
        $kelas = Kelas::with(['guru.user'])->get();
        return view('admin.kelas.index', compact('kelas'));
    }

    public function create()
    {
        // Mengambil semua data dari tabel gurus agar semua guru muncul di dropdown
        $gurus = Guru::all(); 

        return view('admin.kelas.create', compact('gurus'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_kelas' => 'required|unique:kelas,nama_kelas',
            'guru_id'    => 'nullable|exists:gurus,id' 
        ]);

        Kelas::create($request->all());

        return redirect()->route('admin.kelas.index')
                         ->with('success', 'Kelas berhasil ditambahkan');
    }

    public function edit(int $id)
    {
        $kela = Kelas::findOrFail($id);
        
        /**
         * UPDATE: Mengambil semua data guru tanpa filter is_wali_kelas 
         * agar konsisten dengan halaman create dan memastikan semua data guru muncul.
         */
        $gurus = Guru::all();

        return view('admin.kelas.edit', compact('kela', 'gurus'));
    }
    
    public function update(Request $request, int $id)
    {
        $kela = Kelas::findOrFail($id);

        $request->validate([
            'nama_kelas' => 'required|unique:kelas,nama_kelas,' . $kela->id,
            'guru_id'    => 'nullable|exists:gurus,id'
        ]);

        $kela->update($request->all());

        return redirect()->route('admin.kelas.index')
                         ->with('success', 'Kelas berhasil diupdate');
    }

    public function destroy(int $id)
    {
        $kela = Kelas::findOrFail($id);

        // Proteksi agar kelas yang masih memiliki siswa tidak terhapus
        if ($kela->siswas()->count() > 0) {
            return redirect()->back()->with('error', 'Kelas tidak bisa dihapus karena masih memiliki siswa!');
        }

        $kela->delete();

        return redirect()->route('admin.kelas.index')
                         ->with('success', 'Kelas berhasil dihapus');
    }
}