@extends('layouts.admin')

@section('content')
<div class="p-6 space-y-6 animate-fade-in">
    {{-- Header --}}
    <div class="mb-2">
        <h1 class="text-2xl font-black text-gray-800 tracking-tight uppercase">Edit Data Guru</h1>
        <p class="text-xs font-bold text-gray-400 mt-1 uppercase tracking-widest">Sistem Informasi Akademik <span class="text-blue-600">Sman 1 Jejangkit</span></p>
    </div>

    {{-- Main Card dengan Garis Biru Tipis di Atas --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden border-t-4 border-t-blue-600">
        <div class="p-5 border-b border-gray-50 bg-gray-50/30">
            <h3 class="text-xs font-black text-gray-500 uppercase tracking-widest flex items-center gap-2">
                <i class="fa-solid fa-user-pen text-blue-600"></i> Formulir Pembaruan Data
            </h3>
        </div>

        <form action="{{ route('admin.guru.update', $guru->id) }}" method="POST" class="p-8 space-y-8">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Nama Lengkap --}}
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2">Nama Lengkap</label>
                    <input type="text" name="nama" value="{{ old('nama', $guru->nama) }}"
                           class="w-full px-4 py-3 rounded-lg border border-gray-200 bg-gray-50/50 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-sm font-bold transition-all outline-none" required>
                </div>

                {{-- NIP --}}
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2">NIP / NUPTK</label>
                    <input type="text" name="nip" value="{{ old('nip', $guru->nip) }}"
                           class="w-full px-4 py-3 rounded-lg border border-gray-200 bg-gray-50/50 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-sm font-bold transition-all outline-none" required>
                </div>

                {{-- Email --}}
                <div class="md:col-span-2">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2">Email Akun (Login)</label>
                    <input type="email" name="email" value="{{ old('email', $guru->user->email) }}"
                           class="w-full px-4 py-3 rounded-lg border border-gray-200 bg-gray-50/50 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-sm font-bold transition-all outline-none" required>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="pt-6 flex justify-between items-center border-t border-gray-50">
                {{-- Tombol Batal Merah Solid --}}
                <a href="{{ route('admin.guru.index') }}" 
                   class="px-8 py-2.5 rounded-lg text-sm font-bold text-white bg-rose-600 hover:bg-rose-700 transition-all shadow-md shadow-rose-100 uppercase tracking-widest text-center">
                    Batal
                </a>

                {{-- Tombol Simpan Biru --}}
                <button type="submit" 
                        class="bg-blue-600 hover:bg-blue-700 text-white font-black py-3 px-10 rounded-xl shadow-lg shadow-blue-100 transition-all active:scale-95 uppercase tracking-widest text-sm">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    @keyframes fadeIn { from { opacity: 0; transform: translateY(5px); } to { opacity: 1; transform: translateY(0); } }
    .animate-fade-in { animation: fadeIn 0.3s ease-out forwards; }
</style>
@endsection