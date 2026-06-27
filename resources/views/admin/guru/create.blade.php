{{-- Menggunakan layout master 'admin' --}}
@extends('layouts.admin')

@section('content')
<div class="p-4 space-y-4 animate-fade-in">
    {{-- Header Section --}}
    <div class="px-1 flex justify-between items-end">
        <div>
            <h1 class="text-lg font-black text-slate-800 tracking-tight uppercase leading-none">
                Input <span class="text-blue-600">Data Guru</span>
            </h1>
            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-[0.2em] mt-1">SMAN 1 Jejangkit</p>
        </div>
    </div>

    {{-- Form Container --}}
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="h-[3px] bg-blue-600 w-full"></div>
        
        <div class="p-3 border-b border-slate-50 bg-slate-50/30">
            <h3 class="text-[11px] font-black text-slate-500 uppercase tracking-widest flex items-center gap-2">
                <i class="fa-solid fa-file-signature text-blue-600"></i> Formulir Identitas
            </h3>
        </div>

        {{-- Form Action: Mengarah ke route 'store' untuk menyimpan data baru ke database --}}
        <form action="{{ route('admin.guru.store') }}" method="POST" class="p-5 md:p-6">
            @csrf {{-- Token keamanan wajib untuk mencegah serangan Cross-Site Request Forgery --}}
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
                {{-- Input Nama --}}
                <div class="md:col-span-2 group">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.1em] mb-1.5 group-focus-within:text-blue-600">Nama Lengkap & Gelar</label>
                    <div class="relative">
                        {{-- 'old('nama')' menjaga data tetap ada di input jika form gagal saat validasi pertama kali --}}
                        <input type="text" name="nama" placeholder="MASUKKAN NAMA LENGKAP" value="{{ old('nama') }}"
                               class="w-full px-4 py-2.5 rounded-lg border border-slate-200 bg-slate-50/30 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/5 text-[13px] font-bold transition-all outline-none uppercase placeholder:text-slate-300" required>
                        <i class="fa-solid fa-user-tie absolute right-4 top-3 text-slate-300 text-[11px] pointer-events-none group-focus-within:text-blue-500"></i>
                    </div>
                </div>

                {{-- Input NIP --}}
                <div class="group">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.1em] mb-1.5 group-focus-within:text-blue-600">NIP / Identitas</label>
                    <div class="relative">
                        <input type="text" name="nip" placeholder="NOMOR INDUK PEGAWAI" value="{{ old('nip') }}"
                               class="w-full px-4 py-2.5 rounded-lg border border-slate-200 bg-slate-50/30 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/5 text-[13px] font-bold transition-all outline-none uppercase placeholder:text-slate-300" required>
                        <i class="fa-solid fa-id-card absolute right-4 top-3 text-slate-300 text-[11px] pointer-events-none group-focus-within:text-blue-500"></i>
                    </div>
                </div>

                {{-- Input Email --}}
                <div class="group">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.1em] mb-1.5 group-focus-within:text-blue-600">Email Akun</label>
                    <div class="relative">
                        <input type="email" name="email" placeholder="contoh@guru.com" value="{{ old('email') }}"
                               class="w-full px-4 py-2.5 rounded-lg border border-slate-200 bg-slate-50/30 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/5 text-[13px] font-bold transition-all outline-none lowercase placeholder:text-slate-300" required>
                        <i class="fa-solid fa-envelope absolute right-4 top-3 text-slate-300 text-[11px] pointer-events-none group-focus-within:text-blue-500"></i>
                    </div>
                </div>
            </div>

            {{-- Info Tip: Memberi tahu admin password default --}}
            <div class="mt-6 bg-blue-50/50 p-3.5 rounded-lg border border-blue-100 border-dashed flex items-center gap-3">
                <i class="fa-solid fa-shield-halved text-blue-500 text-[11px]"></i>
                <p class="text-[10px] text-blue-700 font-bold uppercase tracking-wider">
                    Password default: <span class="text-blue-900 font-black italic">password123</span>
                </p>
            </div>

            {{-- Action Buttons --}}
            <div class="mt-6 pt-4 flex justify-end items-center gap-3 border-t border-slate-100">
                {{-- Link Batal untuk membatalkan proses dan kembali ke daftar --}}
                <a href="{{ route('admin.guru.index') }}" 
                   class="px-6 py-2.5 rounded-lg text-[11px] font-black text-white bg-rose-600 hover:bg-rose-700 transition-all uppercase tracking-widest active:scale-95 shadow-sm shadow-rose-100 text-center">
                    Batal
                </a>
                
                {{-- Tombol Submit untuk menyimpan data ke database --}}
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-black py-2.5 px-8 rounded-lg shadow-sm transition-all active:scale-95 uppercase tracking-widest text-[11px]">
                    Simpan Data
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Animasi Fade-In --}}
<style>
    @keyframes fadeIn { from { opacity: 0; transform: translateY(5px); } to { opacity: 1; transform: translateY(0); } }
    .animate-fade-in { animation: fadeIn 0.4s ease-out forwards; }
</style>
@endsection