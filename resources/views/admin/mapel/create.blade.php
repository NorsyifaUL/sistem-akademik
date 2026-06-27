@extends('layouts.admin')

@section('content')
<div class="p-4 space-y-6 animate-fade-in">
    {{-- Header --}}
    <div class="px-1">
        <h1 class="text-xl font-black text-slate-800 tracking-tight uppercase leading-none">
            Tambah <span class="text-blue-600">Mata Pelajaran</span>
        </h1>
        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-[0.2em] mt-1.5">
            Pusat Pengelolaan Kurikulum SMAN 1 Jejangkit
        </p>
    </div>

    {{-- Form Card --}}
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="h-[4px] bg-blue-600 w-full"></div>
        
        <div class="p-4 border-b border-slate-50 bg-slate-50/30">
            <h3 class="text-[10px] font-black text-slate-500 uppercase tracking-widest flex items-center gap-2">
                <i class="fa-solid fa-file-signature text-blue-600"></i> Form Input Mata Pelajaran Baru
            </h3>
        </div>

        <form action="{{ route('admin.mapel.store') }}" method="POST" class="p-6 space-y-6">
            @csrf
            
            <div class="grid grid-cols-1 gap-6">
                {{-- Nama Mapel --}}
                <div class="space-y-1.5">
                    <label class="block text-[9px] font-black text-slate-400 uppercase tracking-widest px-1">
                        Nama Mata Pelajaran <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative group">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-slate-300 group-focus-within:text-blue-500 transition-colors">
                            <i class="fa-solid fa-book text-[11px]"></i>
                        </span>
                        <input type="text" name="nama_mapel" placeholder="CONTOH: MATEMATIKA PEMINATAN" 
                               value="{{ old('nama_mapel') }}"
                               class="w-full pl-10 pr-4 py-3 bg-white border border-slate-200 rounded-lg text-[11px] font-bold uppercase outline-none focus:border-blue-500 transition-all @error('nama_mapel') border-rose-500 @enderror" 
                               required>
                    </div>
                    @error('nama_mapel')
                        <p class="text-[9px] text-rose-600 font-black uppercase tracking-widest flex items-center gap-1.5">
                            <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Box Info --}}
                <div class="bg-blue-50/30 rounded-lg border border-blue-100 border-dashed p-4 flex items-start gap-4">
                    <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center text-white shrink-0">
                        <i class="fa-solid fa-circle-info text-[12px]"></i>
                    </div>
                    <div>
                        <h4 class="text-[10px] font-black text-blue-800 uppercase tracking-widest">Panduan Penginputan</h4>
                        <p class="text-[10px] text-blue-600 font-bold leading-relaxed uppercase mt-1">
                            Nama mata pelajaran yang diinput akan digunakan sebagai referensi utama pada modul Jadwal, Absensi, dan Laporan Hasil Belajar (Raport). Pastikan penulisan sesuai dengan standar kurikulum.
                        </p>
                    </div>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="pt-6 border-t border-slate-50 flex justify-end items-center gap-3">
                <a href="{{ route('admin.mapel.index') }}" 
                   class="px-8 py-2.5 rounded-lg text-[10px] font-black text-white bg-rose-600 hover:bg-rose-700 transition-all uppercase tracking-widest text-center active:scale-95">
                    Batal
                </a>
                <button type="submit" 
                        class="bg-blue-600 hover:bg-blue-700 text-white font-black py-2.5 px-10 rounded-lg shadow-sm transition-all active:scale-95 uppercase text-[10px] tracking-widest flex items-center gap-2">
                    <i class="fa-solid fa-floppy-disk text-[10px]"></i> Simpan Data
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    .animate-fade-in { animation: fadeIn 0.4s ease-out forwards; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(5px); } to { opacity: 1; transform: translateY(0); } }
</style>
@endsection