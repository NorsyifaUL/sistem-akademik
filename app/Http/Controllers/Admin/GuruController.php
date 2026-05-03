<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class GuruController extends Controller
{
    public function index(Request $request) // Tambahkan Request $request
{
    $gurus = Guru::with('user')
        ->when($request->search, function ($query) use ($request) {
            $query->where('nama', 'like', '%' . $request->search . '%')
                  ->orWhere('nip', 'like', '%' . $request->search . '%');
        })
        ->latest()
        ->get(); // Tetap pakai get() jika kamu belum mau pakai paginasi

    return view('admin.guru.index', compact('gurus'));
}

    public function create()
    {
        return view('admin.guru.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required',
            'nip' => 'required|unique:gurus',
            'email' => 'required|email|unique:users',
            'is_wali_kelas' => 'nullable',
            'wali_kelas' => 'required_if:is_wali_kelas,1'
        ]);

        // 1. Buat User Login (Data NIP disimpan di sini agar bisa dipanggil Auth::user()->nip)
        $user = User::create([
            'name' => $request->nama,
            'email' => $request->email,
            'password' => Hash::make('password123'),
            'role' => 'guru',
            'nip' => $request->nip, // <-- SINKRONISASI NIP KE TABEL USERS
            'is_wali_kelas' => $request->has('is_wali_kelas') ? 1 : 0,
            'wali_kelas' => $request->is_wali_kelas ? $request->wali_kelas : null,
        ]);

        // 2. Buat Profil Guru di Tabel Gurus
        Guru::create([
            'nama' => $request->nama,
            'nip' => $request->nip,
            'user_id' => $user->id
        ]);

        return redirect()->route('admin.guru.index')
            ->with('success','Guru berhasil ditambahkan. Password default: password123');
    }

    public function edit(Guru $guru)
    {
        return view('admin.guru.edit', compact('guru'));
    }

    public function update(Request $request, Guru $guru)
    {
        $request->validate([
            'nama' => 'required',
            'nip' => 'required|unique:gurus,nip,' . $guru->id,
            'email' => 'required|email|unique:users,email,' . $guru->user_id,
            'is_wali_kelas' => 'nullable',
            'wali_kelas' => 'required_if:is_wali_kelas,1'
        ]);

        // 1. Update Akun User (Memastikan Auth::user() mendapatkan NIP terbaru)
        $guru->user->update([
            'name' => $request->nama,
            'email' => $request->email,
            'nip' => $request->nip, // <-- SINKRONISASI NIP SAAT UPDATE
            'is_wali_kelas' => $request->has('is_wali_kelas') ? 1 : 0,
            'wali_kelas' => $request->is_wali_kelas ? $request->wali_kelas : null,
        ]);

        // 2. Update Profil Guru
        $guru->update([
            'nama' => $request->nama,
            'nip' => $request->nip,
        ]);

        return redirect()->route('admin.guru.index')
            ->with('success','Guru berhasil diupdate');
    }

    public function destroy(Guru $guru)
    {
        // Hapus user terkait dulu baru hapus data guru
        $guru->user()->delete();
        $guru->delete();

        return redirect()->route('admin.guru.index')
            ->with('success','Guru berhasil dihapus');
    }

    public function resetPassword(Guru $guru)
    {
        $newPassword = Str::random(8);

        $guru->user->update([
            'password' => Hash::make($newPassword)
        ]);

        return redirect()->route('admin.guru.index')
            ->with('success', 'Password baru untuk '.$guru->nama.' : '.$newPassword);
    }
}