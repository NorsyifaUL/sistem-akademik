@extends('layouts.admin')

@section('content')
<div class="p-4 space-y-4 animate-fade-in">
    {{-- Header Ramping --}}
    <div class="flex items-end justify-between px-1">
        <div>
            <h2 class="text-base font-black text-slate-800 tracking-tight uppercase leading-none">Edit <span class="text-blue-600">Jadwal</span></h2>
            <p class="text-[8px] text-slate-400 font-bold uppercase tracking-[0.1em] mt-1">
                Perbarui informasi KBM Kelas: <span class="text-blue-600">{{ $jadwal->kelas }}</span>
            </p>
        </div>
    </div>

    {{-- Form Card - Lebar Maksimal --}}
    <div class="w-full bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="h-[3px] bg-blue-600 w-full"></div>
        
        <form action="{{ route('admin.jadwal.update', $jadwal->id) }}" method="POST" class="p-6 space-y-6">
            @csrf
            @method('PUT')

            {{-- Grid 3 Kolom --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-x-6 gap-y-4">
                
                {{-- Pilih Guru --}}
                <div class="space-y-1">
                    <label class="block text-[9px] font-black text-slate-400 uppercase tracking-widest px-1">Guru Pengajar</label>
                    <div class="relative group">
                        <select name="guru_id" class="w-full pl-3 pr-8 py-2 bg-slate-50 border border-slate-200 rounded-lg text-[10px] font-bold uppercase outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all appearance-none cursor-pointer" required>
                            @foreach($gurus as $guru)
                                <option value="{{ $guru->id }}" {{ $jadwal->guru_id == $guru->id ? 'selected' : '' }}>{{ strtoupper($guru->nama) }}</option>
                            @endforeach
                        </select>
                        <i class="fa-solid fa-chevron-down absolute right-3 top-2.5 text-[8px] text-slate-300 pointer-events-none group-focus-within:rotate-180 transition-transform"></i>
                    </div>
                </div>

                {{-- Pilih Mapel --}}
                <div class="space-y-1">
                    <label class="block text-[9px] font-black text-slate-400 uppercase tracking-widest px-1">Mata Pelajaran</label>
                    <div class="relative group">
                        <select name="mapel_id" class="w-full pl-3 pr-8 py-2 bg-slate-50 border border-slate-200 rounded-lg text-[10px] font-bold uppercase outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all appearance-none cursor-pointer" required>
                            @foreach($mapels as $mapel)
                                <option value="{{ $mapel->id }}" {{ $jadwal->mapel_id == $mapel->id ? 'selected' : '' }}>{{ strtoupper($mapel->nama_mapel) }}</option>
                            @endforeach
                        </select>
                        <i class="fa-solid fa-chevron-down absolute right-3 top-2.5 text-[8px] text-slate-300 pointer-events-none group-focus-within:rotate-180 transition-transform"></i>
                    </div>
                </div>

                {{-- Pilih Kelas --}}
                <div class="space-y-1">
                    <label class="block text-[9px] font-black text-blue-600 uppercase tracking-widest px-1">Kelas</label>
                    <div class="relative group">
                        <select name="kelas" class="w-full pl-3 pr-8 py-2 bg-blue-50/50 border border-blue-100 rounded-lg text-[10px] font-black uppercase text-blue-900 outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all appearance-none cursor-pointer" required>
                            @foreach(['X 1', 'X 2', 'XI 1', 'XI 2', 'XII IPA', 'XII IPS'] as $kls)
                                <option value="{{ $kls }}" {{ $jadwal->kelas == $kls ? 'selected' : '' }}>KELAS {{ $kls }}</option>
                            @endforeach
                        </select>
                        <i class="fa-solid fa-chalkboard absolute right-3 top-2.5 text-[9px] text-blue-300 pointer-events-none"></i>
                    </div>
                </div>

                {{-- Pilih Hari --}}
                <div class="space-y-1">
                    <label class="block text-[9px] font-black text-slate-400 uppercase tracking-widest px-1">Hari Pelajaran</label>
                    <div class="relative group">
                        <select name="hari" class="w-full pl-3 pr-8 py-2 bg-slate-50 border border-slate-200 rounded-lg text-[10px] font-bold uppercase outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all appearance-none cursor-pointer" required>
                            @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'] as $hari)
                                <option value="{{ $hari }}" {{ $jadwal->hari == $hari ? 'selected' : '' }}>{{ strtoupper($hari) }}</option>
                            @endforeach
                        </select>
                        <i class="fa-solid fa-calendar-day absolute right-3 top-2.5 text-[9px] text-slate-300 pointer-events-none"></i>
                    </div>
                </div>

                {{-- Penyesuaian Waktu (2 Kolom) --}}
                <div class="md:col-span-2">
                    <label class="block text-[9px] font-black text-slate-400 uppercase tracking-widest px-1 mb-1">Penyesuaian Waktu KBM</label>
                    <div class="grid grid-cols-2 gap-4 p-2 bg-slate-50 border border-slate-100 border-dashed rounded-lg">
                        <div class="flex items-center gap-3">
                            <span class="text-[8px] font-black text-blue-500 uppercase w-10 text-right">Mulai</span>
                            <input type="text" name="jam_mulai" value="{{ \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') }}" placeholder="07:30" maxlength="5"
                                   class="time-mask w-full px-3 py-1 bg-white border border-slate-200 rounded-md text-[11px] font-black text-center text-blue-900 focus:border-blue-500 focus:ring-0 transition-all">
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="text-[8px] font-black text-blue-500 uppercase w-10 text-right">Selesai</span>
                            <input type="text" name="jam_selesai" value="{{ \Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i') }}" placeholder="14:00" maxlength="5"
                                   class="time-mask w-full px-3 py-1 bg-white border border-slate-200 rounded-md text-[11px] font-black text-center text-blue-900 focus:border-blue-500 focus:ring-0 transition-all">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="mt-10 pt-6 flex flex-col md:flex-row justify-end items-center gap-3 border-t border-slate-50">
                <a href="{{ route('admin.jadwal.index') }}" 
                   class="w-full md:w-auto px-8 py-2 rounded-lg text-[9px] font-black text-white bg-rose-600 hover:bg-rose-700 transition-all uppercase tracking-widest text-center shadow-sm shadow-rose-100 active:scale-95">
                    Batal
                </a>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-black py-2 px-10 rounded-lg shadow-sm transition-all active:scale-95 uppercase text-[9px] tracking-widest flex items-center gap-2">
                    <i class="fa-solid fa-rotate text-[8px]"></i> Update Jadwal
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.querySelectorAll('.time-mask').forEach(input => {
        input.addEventListener('input', function(e) {
            let val = e.target.value.replace(/\D/g, ''); 
            if (val.length > 2) val = val.slice(0, 2) + ':' + val.slice(2, 4);
            e.target.value = val;
        });
        input.addEventListener('blur', function(e) {
            let val = e.target.value;
            if (val.length > 0) {
                const parts = val.split(':');
                let hr = parts[0] ? parseInt(parts[0]) : 0;
                let min = parts[1] ? parseInt(parts[1]) : 0;
                if (hr > 23) hr = 23; if (min > 59) min = 59;
                e.target.value = hr.toString().padStart(2, '0') + ':' + min.toString().padStart(2, '0');
            }
        });
    });
</script>

<style>
    @keyframes fadeIn { from { opacity: 0; transform: translateY(5px); } to { opacity: 1; transform: translateY(0); } }
    .animate-fade-in { animation: fadeIn 0.3s ease-out forwards; }
</style>
@endsection