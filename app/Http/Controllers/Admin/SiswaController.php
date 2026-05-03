<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class SiswaController extends Controller
{
    public function index(Request $request)
    {
        $query = Siswa::with('user');

        // Fitur Pencarian Nama/NISN
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('nama', 'like', '%' . $request->search . '%')
                  ->orWhere('nisn', 'like', '%' . $request->search . '%');
            });
        }

        // Filter per Kelas
        if ($request->kelas) {
            $query->where('kelas', $request->kelas);
        }

        $siswa = $query->latest()->paginate(10)->appends($request->all());
        
        // List kelas unik dari database untuk filter di halaman index
        $kelasList = Siswa::select('kelas')->distinct()->orderBy('kelas', 'asc')->get();

        return view('admin.siswa.index', compact('siswa', 'kelasList'));
    }

    public function create()
    {
        // List kelas manual agar pilihan selalu lengkap meskipun database kosong
        $kelasList = ['X 1', 'X 2', 'XI 1', 'XI 2', 'XII IPA', 'XII IPS'];
        return view('admin.siswa.create', compact('kelasList'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required',
            'nisn' => 'required|unique:siswas,nisn',
            'email' => 'required|email|unique:users,email',
            'kelas' => 'required',
            'no_wa_ortu' => 'required'
        ]);

        DB::transaction(function () use ($request) {
            $user = User::create([
                'name' => $request->nama,
                'email' => $request->email,
                'password' => Hash::make('password123'),
                'role' => 'siswa'
            ]);

            Siswa::create([
                'user_id' => $user->id,
                'nama' => $request->nama,
                'nisn' => $request->nisn,
                'kelas' => $request->kelas,
                'no_wa_ortu' => $request->no_wa_ortu
            ]);
        });

        return redirect()->route('admin.siswa.index')->with('success', 'Siswa berhasil ditambahkan');
    }

    public function edit(Siswa $siswa)
    {
        // Gunakan list yang sama dengan create agar konsisten
        $kelasList = ['X 1', 'X 2', 'XI 1', 'XI 2', 'XII IPA', 'XII IPS'];
        return view('admin.siswa.edit', compact('siswa', 'kelasList'));
    }

    public function update(Request $request, Siswa $siswa)
    {
        $request->validate([
            'nama' => 'required',
            'nisn' => 'required|unique:siswas,nisn,' . $siswa->id,
            'email' => 'required|email|unique:users,email,' . $siswa->user_id,
            'kelas' => 'required',
            'no_wa_ortu' => 'required'
        ]);

        DB::transaction(function () use ($request, $siswa) {
            $siswa->user->update([
                'name' => $request->nama,
                'email' => $request->email
            ]);

            $siswa->update([
                'nama' => $request->nama,
                'nisn' => $request->nisn,
                'kelas' => $request->kelas,
                'no_wa_ortu' => $request->no_wa_ortu
            ]);
        });

        return redirect()->route('admin.siswa.index')->with('success', 'Data siswa diperbarui');
    }

    public function destroy(Siswa $siswa)
    {
        DB::transaction(function () use ($siswa) {
            if ($siswa->user) {
                $siswa->user()->delete();
            }
            $siswa->delete();
        });

        return redirect()->route('admin.siswa.index')->with('success', 'Data siswa dihapus');
    }

    public function resetPassword(Siswa $siswa)
    {
        $siswa->user->update([
            'password' => Hash::make('password123')
        ]);

        return redirect()->back()->with('success', 'Password ' . $siswa->nama . ' direset ke: password123');
    }
}