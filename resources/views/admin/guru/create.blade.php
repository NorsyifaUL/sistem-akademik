@extends('layouts.admin')

@section('content')
<div class="p-6 space-y-6 animate-fade-in">
    {{-- Header --}}
    <div class="mb-2">
        <h1 class="text-2xl font-black text-gray-800 tracking-tight uppercase">Input Data Guru</h1>
        <p class="text-xs font-bold text-gray-400 mt-1 uppercase tracking-widest">Pendaftaran Akun Pendidik Baru</p>
    </div>

    {{-- Form Card dengan Garis Biru --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden border-t-4 border-t-blue-600">
        <div class="p-5 border-b border-gray-50 bg-gray-50/30">
            <h3 class="text-xs font-black text-gray-500 uppercase tracking-widest flex items-center gap-2">
                <i class="fa-solid fa-file-signature text-blue-600"></i> Formulir Identitas
            </h3>
        </div>

        <form action="{{ route('admin.guru.store') }}" method="POST" class="p-8 space-y-6">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2">Nama Lengkap & Gelar</label>
                    <input type="text" name="nama" placeholder="Masukkan Nama Lengkap" value="{{ old('nama') }}"
                           class="w-full px-4 py-3 rounded-lg border border-gray-200 bg-gray-50/50 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-sm font-bold transition-all outline-none" required>
                </div>

                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2">NIP</label>
                    <input type="text" name="nip" placeholder="Nomor Induk Pegawai" value="{{ old('nip') }}"
                           class="w-full px-4 py-3 rounded-lg border border-gray-200 bg-gray-50/50 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-sm font-bold transition-all outline-none" required>
                </div>

                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2">Email Akun</label>
                    <input type="email" name="email" placeholder="contoh@guru.com" value="{{ old('email') }}"
                           class="w-full px-4 py-3 rounded-lg border border-gray-200 bg-gray-50/50 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-sm font-bold transition-all outline-none" required>
                </div>
            </div>

            <div class="bg-blue-50 p-4 rounded-lg border border-blue-100 border-dashed flex items-center gap-3">
                <i class="fa-solid fa-circle-info text-blue-500"></i>
                <p class="text-[11px] text-blue-700 font-bold uppercase tracking-tight">Password default: <span class="underline">password123</span></p>
            </div>

            {{-- Buttons --}}
            <div class="flex justify-end gap-3 pt-6 border-t border-gray-50">
                <a href="{{ route('admin.guru.index') }}" 
                class="px-8 py-2.5 rounded-lg text-sm font-bold text-white bg-rose-600 hover:bg-rose-700 transition-all shadow-md shadow-rose-100 uppercase tracking-widest text-center">
                    Batal
                </a>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-2.5 rounded-lg font-black text-sm transition-all shadow-md shadow-blue-100 uppercase tracking-widest">
                    Simpan Data
                </button>
            </div>
        </form>
    </div>
</div>
@endsection