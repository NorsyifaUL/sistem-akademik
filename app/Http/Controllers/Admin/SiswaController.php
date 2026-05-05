<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use App\Models\User;
use App\Models\Kelas; // Tambahkan Model Kelas
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class SiswaController extends Controller
{
    public function index(Request $request)
    {
        // Eager loading user dan dataKelas
        $query = Siswa::with(['user', 'dataKelas']);

        // Fitur Pencarian Nama/NISN
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('nama', 'like', '%' . $request->search . '%')
                  ->orWhere('nisn', 'like', '%' . $request->search . '%');
            });
        }

        // Filter per Kelas menggunakan kelas_id
        if ($request->kelas_id) {
            $query->where('kelas_id', $request->kelas_id);
        }

        $siswa = $query->latest()->paginate(10)->appends($request->all());
        
        // Ambil daftar kelas dari tabel kelas untuk filter dropdown di index
        $kelasList = Kelas::orderBy('nama_kelas', 'asc')->get();

        return view('admin.siswa.index', compact('siswa', 'kelasList'));
    }

    public function create()
    {
        // Ambil data dari database, bukan nulis manual lagi
        $kelasList = Kelas::orderBy('nama_kelas', 'asc')->get();
        return view('admin.siswa.create', compact('kelasList'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required',
            'nisn' => 'required|unique:siswas,nisn',
            'email' => 'required|email|unique:users,email',
            'kelas_id' => 'required|exists:kelas,id', // Validasi ke tabel kelas
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
                'kelas_id' => $request->kelas_id, // Gunakan kelas_id
                'no_wa_ortu' => $request->no_wa_ortu
            ]);
        });

        return redirect()->route('admin.siswa.index')->with('success', 'Siswa berhasil ditambahkan');
    }

    public function edit(Siswa $siswa)
    {
        $kelasList = Kelas::orderBy('nama_kelas', 'asc')->get();
        return view('admin.siswa.edit', compact('siswa', 'kelasList'));
    }

    public function update(Request $request, Siswa $siswa)
    {
        $request->validate([
            'nama' => 'required',
            'nisn' => 'required|unique:siswas,nisn,' . $siswa->id,
            'email' => 'required|email|unique:users,email,' . $siswa->user_id,
            'kelas_id' => 'required|exists:kelas,id',
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
                'kelas_id' => $request->kelas_id,
                'no_wa_ortu' => $request->no_wa_ortu
            ]);
        });

        return redirect()->route('admin.siswa.index')->with('success', 'Data siswa diperbarui');
    }

    // ... method destroy dan resetPassword tetap sama ...
}