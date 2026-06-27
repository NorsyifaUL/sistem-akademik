@extends('layouts.admin')

@section('content')
<div class="p-4 space-y-4 animate-fade-in">
    
    {{-- Header Mini --}}
    <div class="px-1">
        <h2 class="text-lg font-black text-slate-800 tracking-tight uppercase leading-none">
            Konfigurasi <span class="text-blue-600">Raport</span>
        </h2>
        <p class="text-[9px] text-slate-400 font-bold uppercase tracking-[0.2em] mt-1.5">Pengaturan Akademik Global SMAN 1 JEJANGKIT</p>
    </div>

    {{-- Notifikasi Status --}}
    @if(session('success'))
    <div class="p-3 bg-emerald-50 border border-emerald-100 text-emerald-700 rounded-lg shadow-sm flex items-center gap-3">
        <i class="fa-solid fa-circle-check text-[10px]"></i>
        <span class="text-[10px] font-black uppercase tracking-wider">{{ session('success') }}</span>
    </div>
    @endif

    @if($errors->any())
    <div class="p-3 bg-rose-50 border border-rose-100 text-rose-700 rounded-lg shadow-sm">
        <ul class="list-none space-y-1">
            @foreach($errors->all() as $error)
                <li class="text-[10px] font-black uppercase tracking-wider flex items-center gap-2">
                    <i class="fa-solid fa-triangle-exclamation"></i> {{ $error }}
                </li>
            @endforeach
        </ul>
    </div>
    @endif

    {{-- Main Card --}}
    <div class="w-full bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="h-[4px] bg-blue-600 w-full"></div>

        <div class="p-6">
            <form action="{{ route('admin.settings.update') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Tahun Pelajaran --}}
                    <div class="space-y-1.5">
                        <label class="text-[9px] font-black text-slate-400 uppercase tracking-[0.2em] ml-1 flex items-center gap-2">
                            <i class="fa-solid fa-calendar-days text-blue-500 text-[10px]"></i> Tahun Pelajaran
                        </label>
                        <input type="text" name="tahun_ajaran" value="{{ old('tahun_ajaran', $setting->tahun_ajaran) }}" 
                               class="w-full px-4 py-2.5 rounded-lg border border-slate-200 bg-slate-50 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10 text-[11px] font-bold text-slate-700 transition-all outline-none" 
                               placeholder="Contoh: 2024/2025">
                    </div>

                    {{-- Semester --}}
                    <div class="space-y-1.5">
                        <label class="text-[9px] font-black text-slate-400 uppercase tracking-[0.2em] ml-1 flex items-center gap-2">
                            <i class="fa-solid fa-layer-group text-blue-500 text-[10px]"></i> Semester Aktif
                        </label>
                        <select name="semester" class="w-full px-4 py-2.5 rounded-lg border border-slate-200 bg-slate-50 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10 text-[11px] font-bold text-slate-700 transition-all outline-none cursor-pointer">
                            <option value="1" {{ old('semester', $setting->semester) == '1' ? 'selected' : '' }}>1 (GANJIL)</option>
                            <option value="2" {{ old('semester', $setting->semester) == '2' ? 'selected' : '' }}>2 (GENAP)</option>
                        </select>
                    </div>

                    {{-- Tanggal Raport --}}
                    <div class="space-y-1.5">
                        <label class="text-[9px] font-black text-slate-400 uppercase tracking-[0.2em] ml-1 flex items-center gap-2">
                            <i class="fa-solid fa-calendar-check text-blue-500 text-[10px]"></i> Tanggal Cetak
                        </label>
                        <input type="date" name="tgl_raport" 
                               value="{{ old('tgl_raport', $setting->tgl_raport ? \Carbon\Carbon::parse($setting->tgl_raport)->format('Y-m-d') : '') }}" 
                               class="w-full px-4 py-2.5 rounded-lg border border-slate-200 bg-slate-50 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10 text-[11px] font-bold text-slate-700 transition-all outline-none">
                    </div>

                    {{-- Nama Kepsek --}}
                    <div class="space-y-1.5">
                        <label class="text-[9px] font-black text-slate-400 uppercase tracking-[0.2em] ml-1 flex items-center gap-2">
                            <i class="fa-solid fa-user-tie text-blue-500 text-[10px]"></i> Kepala Sekolah
                        </label>
                        <input type="text" name="nama_kepsek" value="{{ old('nama_kepsek', $setting->nama_kepsek) }}" 
                               class="w-full px-4 py-2.5 rounded-lg border border-slate-200 bg-slate-50 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10 text-[11px] font-bold text-slate-700 transition-all outline-none"
                               placeholder="Nama Lengkap & Gelar">
                    </div>

                    {{-- NIP Kepsek --}}
                    <div class="space-y-1.5 md:col-span-2">
                        <label class="text-[9px] font-black text-slate-400 uppercase tracking-[0.2em] ml-1 flex items-center gap-2">
                            <i class="fa-solid fa-id-card text-blue-500 text-[10px]"></i> NIP Kepala Sekolah
                        </label>
                        <input type="text" name="nip_kepsek" value="{{ old('nip_kepsek', $setting->nip_kepsek) }}" 
                               class="w-full px-4 py-2.5 rounded-lg border border-slate-200 bg-slate-50 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10 text-[11px] font-bold text-slate-700 transition-all outline-none"
                               placeholder="Masukkan NIP Resmi">
                    </div>
                </div>

                {{-- Footer Actions --}}
                <div class="flex items-center justify-between mt-10 pt-6 border-t border-slate-100">
                    <span class="text-[9px] font-bold text-slate-300 uppercase tracking-[0.2em]">Siakad System v2.0</span>
                    
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-lg font-black text-[10px] uppercase tracking-[0.2em] shadow-sm transition-all active:scale-95 flex items-center gap-2">
                        <i class="fa-solid fa-cloud-arrow-up text-[11px]"></i>
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    @keyframes fadeIn { from { opacity: 0; transform: translateY(5px); } to { opacity: 1; transform: translateY(0); } }
    .animate-fade-in { animation: fadeIn 0.3s ease-out forwards; }
</style>
@endsection