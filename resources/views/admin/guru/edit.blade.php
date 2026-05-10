@extends('layouts.admin')

@section('content')
<div class="p-4 space-y-4 animate-fade-in">
    {{-- Header Section Ramping --}}
    <div class="px-1">
        <h1 class="text-base font-black text-slate-800 tracking-tight uppercase leading-none">
            Update <span class="text-blue-600">Data Guru</span>
        </h1>
        <p class="text-[8px] text-slate-400 font-bold uppercase tracking-[0.1em] mt-1">Perbarui Identitas Pendidik: <span class="text-slate-600">{{ $guru->nama }}</span></p>
    </div>

    {{-- Form Container --}}
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="h-[3px] bg-blue-600 w-full"></div>
        
        <div class="p-4 border-b border-slate-50 bg-slate-50/30">
            <h3 class="text-[9px] font-black text-slate-500 uppercase tracking-widest flex items-center gap-2">
                <i class="fa-solid fa-user-pen text-blue-600 text-[10px]"></i> Formulir Pembaruan Data
            </h3>
        </div>

        <form action="{{ route('admin.guru.update', $guru->id) }}" method="POST" class="p-6 md:p-8">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-3">
                {{-- Nama Lengkap --}}
                <div class="md:col-span-2 group">
                    <label class="block text-[9px] font-black text-slate-400 uppercase tracking-[0.2em] mb-0.5 group-focus-within:text-blue-600 transition-colors">Nama Lengkap & Gelar</label>
                    <div class="relative">
                        <input type="text" name="nama" value="{{ old('nama', $guru->nama) }}"
                               class="w-full px-4 py-2 rounded-lg border border-slate-200 bg-slate-50/30 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/5 text-xs font-bold transition-all outline-none uppercase placeholder:text-slate-300" required>
                        <i class="fa-solid fa-user-tie absolute right-4 top-2.5 text-slate-300 text-[10px] pointer-events-none group-focus-within:text-blue-500 transition-colors"></i>
                    </div>
                </div>

                {{-- NIP --}}
                <div class="group">
                    <label class="block text-[9px] font-black text-slate-400 uppercase tracking-[0.2em] mb-0.5 group-focus-within:text-blue-600 transition-colors">NIP</label>
                    <div class="relative">
                        <input type="text" name="nip" value="{{ old('nip', $guru->nip) }}"
                               class="w-full px-4 py-2 rounded-lg border border-slate-200 bg-slate-50/30 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/5 text-xs font-bold transition-all outline-none uppercase placeholder:text-slate-300" required>
                        <i class="fa-solid fa-id-card absolute right-4 top-2.5 text-slate-300 text-[10px] pointer-events-none group-focus-within:text-blue-500 transition-colors"></i>
                    </div>
                </div>

                {{-- Email --}}
                <div class="group">
                    <label class="block text-[9px] font-black text-slate-400 uppercase tracking-[0.2em] mb-0.5 group-focus-within:text-blue-600 transition-colors">Email Akun (Login)</label>
                    <div class="relative">
                        <input type="email" name="email" value="{{ old('email', $guru->user->email) }}"
                               class="w-full px-4 py-2 rounded-lg border border-slate-200 bg-slate-50/30 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/5 text-xs font-bold transition-all outline-none lowercase placeholder:text-slate-300" required>
                        <i class="fa-solid fa-envelope absolute right-4 top-2.5 text-slate-300 text-[10px] pointer-events-none group-focus-within:text-blue-500 transition-colors"></i>
                    </div>
                </div>
            </div>

            {{-- Info Tip --}}
            <div class="mt-4 bg-amber-50/50 p-3 rounded-lg border border-amber-100 border-dashed flex items-center gap-3">
                <i class="fa-solid fa-circle-exclamation text-amber-500 text-[10px]"></i>
                <p class="text-[8px] text-amber-700 font-black uppercase tracking-wider leading-tight">
                    Catatan: Perubahan email akan berdampak pada kredensial login guru yang bersangkutan.
                </p>
            </div>

            {{-- Action Buttons --}}
            <div class="mt-8 pt-4 flex flex-col md:flex-row justify-end items-center gap-3 border-t border-slate-50">
                {{-- Tombol Batal Merah Solid --}}
                <a href="{{ route('admin.guru.index') }}" 
                   class="w-full md:w-auto px-8 py-2 rounded-lg text-[9px] font-black text-white bg-rose-600 hover:bg-rose-700 transition-all uppercase tracking-widest text-center shadow-sm shadow-rose-100 active:scale-95">
                    Batal
                </a>
                
                {{-- Tombol Update --}}
                <button type="submit" class="w-full md:w-auto bg-blue-600 hover:bg-blue-700 text-white font-black py-2 px-10 rounded-lg shadow-sm shadow-blue-100 transition-all active:scale-95 uppercase tracking-[0.2em] text-[9px]">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    @keyframes fadeIn { from { opacity: 0; transform: translateY(5px); } to { opacity: 1; transform: translateY(0); } }
    .animate-fade-in { animation: fadeIn 0.4s ease-out forwards; }
</style>
@endsection