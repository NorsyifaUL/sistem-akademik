@extends('layouts.admin')

@section('content')
<div class="p-4 space-y-6 animate-fade-in">
    {{-- Header --}}
    <div class="px-1">
        <h1 class="text-xl font-black text-slate-800 tracking-tight uppercase leading-none">
            Edit <span class="text-blue-600">Jadwal</span>
        </h1>
        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-[0.2em] mt-1.5">
            Perbarui informasi KBM Kelas: <span class="text-blue-600 font-black">{{ $jadwal->kelas }}</span>
        </p>
    </div>

    {{-- Form Card --}}
    <div class="w-full bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="h-[4px] bg-blue-600 w-full"></div>
        
        <form action="{{ route('admin.jadwal.update', $jadwal->id) }}" method="POST" class="p-6 space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-3 gap-x-6 gap-y-5">
                
                {{-- Pilih Guru --}}
                <div class="space-y-1.5">
                    <label class="block text-[9px] font-black text-slate-400 uppercase tracking-widest px-1">Guru Pengajar</label>
                    <select name="guru_id" class="w-full px-4 py-3 bg-white border border-slate-200 rounded-lg text-[11px] font-bold uppercase outline-none focus:border-blue-500 transition-all cursor-pointer" required>
                        @foreach($gurus as $guru)
                            <option value="{{ $guru->id }}" {{ $jadwal->guru_id == $guru->id ? 'selected' : '' }}>{{ strtoupper($guru->nama) }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Pilih Mapel --}}
                <div class="space-y-1.5">
                    <label class="block text-[9px] font-black text-slate-400 uppercase tracking-widest px-1">Mata Pelajaran</label>
                    <select name="mapel_id" class="w-full px-4 py-3 bg-white border border-slate-200 rounded-lg text-[11px] font-bold uppercase outline-none focus:border-blue-500 transition-all cursor-pointer" required>
                        @foreach($mapels as $mapel)
                            <option value="{{ $mapel->id }}" {{ $jadwal->mapel_id == $mapel->id ? 'selected' : '' }}>{{ strtoupper($mapel->nama_mapel) }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Pilih Kelas --}}
                <div class="space-y-1.5">
                    <label class="block text-[9px] font-black text-blue-600 uppercase tracking-widest px-1">Kelas</label>
                    <select name="kelas" class="w-full px-4 py-3 bg-blue-50/30 border border-blue-100 rounded-lg text-[11px] font-black uppercase text-blue-900 outline-none focus:border-blue-500 transition-all cursor-pointer" required>
                        @foreach(['X 1', 'X 2', 'XI 1', 'XI 2', 'XII IPA', 'XII IPS'] as $kls)
                            <option value="{{ $kls }}" {{ $jadwal->kelas == $kls ? 'selected' : '' }}>KELAS {{ $kls }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Hari --}}
                <div class="space-y-1.5">
                    <label class="block text-[9px] font-black text-slate-400 uppercase tracking-widest px-1">Hari Pelajaran</label>
                    <select name="hari" class="w-full px-4 py-3 bg-white border border-slate-200 rounded-lg text-[11px] font-bold uppercase outline-none focus:border-blue-500 transition-all cursor-pointer" required>
                        @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'] as $hari)
                            <option value="{{ $hari }}" {{ $jadwal->hari == $hari ? 'selected' : '' }}>{{ strtoupper($hari) }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Waktu --}}
                <div class="md:col-span-2 space-y-1.5">
                    <label class="block text-[9px] font-black text-slate-400 uppercase tracking-widest px-1">Penyesuaian Waktu KBM</label>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="flex items-center gap-3 bg-slate-50 border border-slate-200 rounded-lg px-4 py-2">
                            <span class="text-[9px] font-black text-blue-500 uppercase">Mulai</span>
                            <input type="text" name="jam_mulai" value="{{ \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') }}" placeholder="07:30" maxlength="5" class="time-mask w-full bg-transparent text-[12px] font-black text-slate-800 outline-none text-center">
                        </div>
                        <div class="flex items-center gap-3 bg-slate-50 border border-slate-200 rounded-lg px-4 py-2">
                            <span class="text-[9px] font-black text-blue-500 uppercase">Selesai</span>
                            <input type="text" name="jam_selesai" value="{{ \Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i') }}" placeholder="14:00" maxlength="5" class="time-mask w-full bg-transparent text-[12px] font-black text-slate-800 outline-none text-center">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="pt-6 border-t border-slate-50 flex justify-end items-center gap-3">
                <a href="{{ route('admin.jadwal.index') }}" class="px-8 py-2.5 rounded-lg text-[10px] font-black text-white bg-rose-600 hover:bg-rose-700 transition-all uppercase tracking-widest text-center active:scale-95">Batal</a>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-black py-2.5 px-10 rounded-lg transition-all uppercase text-[10px] tracking-widest flex items-center gap-2 active:scale-95">
                    <i class="fa-solid fa-rotate text-[10px]"></i> Update Jadwal
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
    .animate-fade-in { animation: fadeIn 0.4s ease-out forwards; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(5px); } to { opacity: 1; transform: translateY(0); } }
</style>
@endsection