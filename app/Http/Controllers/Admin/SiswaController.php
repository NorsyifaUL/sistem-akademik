<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use App\Models\User;
use App\Models\Kelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SiswaController extends Controller
{
    public function index(Request $request)
    {
        $semuaKelas = Kelas::orderBy('nama_kelas', 'asc')->get();
        $query = Siswa::with(['user', 'dataKelas']);

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('nama', 'like', '%' . $request->search . '%')
                  ->orWhere('nisn', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('kelas_id')) {
            $query->where('kelas_id', $request->kelas_id);
        }

        $siswa = $query->latest()->paginate(10);
        // Tetap gunakan appends untuk membawa parameter filter di URL
        $siswa->appends($request->all());

        return view('admin.siswa.index', compact('siswa', 'semuaKelas'));
    }

    public function create()
    {
        $semuaKelas = Kelas::orderBy('nama_kelas', 'asc')->get();
        return view('admin.siswa.create', compact('semuaKelas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama'       => 'required|string|max:255',
            'nisn'       => 'required|unique:siswas,nisn',
            'email'      => 'required|email|unique:users,email',
            'kelas_id'   => 'required|exists:kelas,id',
            'no_wa_ortu' => 'required'
        ]);

        try {
            DB::beginTransaction();

            $user = User::create([
                'name'     => $request->nama,
                'email'    => $request->email,
                'password' => Hash::make('password123'),
                'role'     => 'siswa'
            ]);

            Siswa::create([
                'user_id'    => $user->id,
                'nama'       => $request->nama,
                'nisn'       => $request->nisn,
                'kelas_id'   => $request->kelas_id,
                'no_wa_ortu' => $request->no_wa_ortu
            ]);

            DB::commit();
            return redirect()->route('admin.siswa.index')->with('success', 'Siswa berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Gagal Simpan Siswa: " . $e->getMessage());
            return back()->withInput()->with('error', 'Terjadi kesalahan sistem.');
        }
    }

    public function edit(Siswa $siswa)
    {
        $semuaKelas = Kelas::orderBy('nama_kelas', 'asc')->get();
        return view('admin.siswa.edit', compact('siswa', 'semuaKelas'));
    }

    public function update(Request $request, Siswa $siswa)
    {
        $request->validate([
            'nama'       => 'required|string|max:255',
            'nisn'       => 'required|unique:siswas,nisn,' . $siswa->id,
            'email'      => 'required|email|unique:users,email,' . $siswa->user_id,
            'kelas_id'   => 'required|exists:kelas,id',
            'no_wa_ortu' => 'required'
        ]);

        try {
            DB::beginTransaction();

            $siswa->user->update([
                'name'  => $request->nama,
                'email' => $request->email
            ]);

            $siswa->update($request->only(['nama', 'nisn', 'kelas_id', 'no_wa_ortu']));

            DB::commit();
            return redirect()->route('admin.siswa.index')->with('success', 'Data siswa berhasil diperbarui');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Gagal Update Siswa: " . $e->getMessage());
            return back()->withInput()->with('error', 'Gagal memperbarui data.');
        }
    }

    public function destroy(Siswa $siswa)
    {
        try {
            DB::beginTransaction();
            // Ambil user sebelum siswa dihapus
            $user = $siswa->user;
            $siswa->delete();
            // Hapus user jika ada
            if ($user) { $user->delete(); }
            
            DB::commit();
            return redirect()->route('admin.siswa.index')->with('success', 'Siswa berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Gagal Hapus Siswa: " . $e->getMessage());
            return back()->with('error', 'Gagal menghapus data.');
        }
    }

    public function resetPassword($id)
    {
        try {
            $siswa = Siswa::findOrFail($id);
            $siswa->user->update(['password' => Hash::make('password123')]);
            return back()->with('success', 'Password berhasil direset ke: password123');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mereset password.');
        }
    }
}