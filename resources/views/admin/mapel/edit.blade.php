@extends('layouts.admin')

@section('content')
<div class="p-4 space-y-4 animate-fade-in">
    {{-- Header Ramping & Luas --}}
    <div class="flex items-end justify-between px-1">
        <div>
            <h2 class="text-base font-black text-slate-800 tracking-tight uppercase leading-none">Edit <span class="text-blue-600">Mata Pelajaran</span></h2>
            <p class="text-[8px] text-slate-400 font-bold uppercase tracking-[0.1em] mt-1">
                MEMPERBARUI DATA: <span class="text-blue-600">{{ $mapel->nama_mapel }}</span>
            </p>
        </div>
    </div>

    {{-- Form Card - Lebar Maksimal --}}
    <div class="w-full bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="h-[3px] bg-blue-600 w-full"></div>
        
        <div class="p-4 border-b border-slate-50 bg-slate-50/30">
            <h3 class="text-[9px] font-black text-slate-400 uppercase tracking-widest flex items-center gap-2">
                <i class="fa-solid fa-pen-to-square text-blue-600"></i> Update Informasi Subjek
            </h3>
        </div>

        <form action="{{ route('admin.mapel.update', $mapel->id) }}" method="POST" class="p-6 space-y-6">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 gap-6">
                {{-- Nama Mapel --}}
                <div class="space-y-1.5">
                    <label class="block text-[9px] font-black text-slate-500 uppercase tracking-widest px-1">
                        Nama Mata Pelajaran <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative group">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-300 group-focus-within:text-blue-500 transition-colors">
                            <i class="fa-solid fa-book-open text-[10px]"></i>
                        </span>
                        <input type="text" 
                               name="nama_mapel" 
                               placeholder="MASUKKAN NAMA MATA PELAJARAN" 
                               value="{{ old('nama_mapel', $mapel->nama_mapel) }}"
                               class="w-full pl-9 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-lg text-[10px] font-black uppercase outline-none focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all @error('nama_mapel') border-rose-500 @enderror" 
                               required>
                    </div>
                    @error('nama_mapel')
                        <p class="text-[9px] text-rose-600 font-black uppercase mt-1 tracking-tight flex items-center gap-1">
                            <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Box Info Ramping --}}
                <div class="bg-blue-50/50 rounded-xl border border-blue-100 border-dashed p-4 flex items-center gap-4">
                    <div class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center text-white shadow-sm shrink-0">
                        <i class="fa-solid fa-sync text-[10px]"></i>
                    </div>
                    <div>
                        <h4 class="text-[10px] font-black text-blue-800 uppercase tracking-tight">Sinkronisasi Otomatis</h4>
                        <p class="text-[9px] text-blue-500 font-bold leading-tight uppercase opacity-80 mt-0.5">
                            Perubahan nama akan otomatis diperbarui pada seluruh jadwal, data nilai, dan raport yang sudah terhubung dengan mata pelajaran ini.
                        </p>
                    </div>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="pt-6 border-t border-slate-50 flex justify-between items-center gap-4">
                <a href="{{ route('admin.mapel.index') }}" 
                   class="px-6 py-2 rounded-lg text-[9px] font-black text-white bg-rose-500 hover:bg-rose-600 transition-all shadow-sm uppercase tracking-widest text-center">
                    <i class="fa-solid fa-xmark mr-1"></i> Batal
                </a>
                <button type="submit" 
                        class="bg-blue-600 hover:bg-blue-700 text-white font-black py-2 px-10 rounded-lg shadow-sm transition-all active:scale-95 uppercase text-[9px] tracking-widest flex items-center gap-2">
                    <i class="fa-solid fa-rotate mr-1"></i> Update Mata Pelajaran
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