@extends('layouts.admin')

@section('content')
<div class="p-4 space-y-6 animate-fade-in">
    {{-- Header --}}
    <div class="px-1">
        <h1 class="text-xl font-black text-slate-800 tracking-tight uppercase leading-none">
            Tambah <span class="text-blue-600">Jadwal</span>
        </h1>
        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-[0.2em] mt-1.5">
            Susun kegiatan belajar mengajar SMAN 1 Jejangkit
        </p>
    </div>

    {{-- Form Card --}}
    <div class="w-full bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="h-[4px] bg-blue-600 w-full"></div>
        
        <form action="{{ route('admin.jadwal.store') }}" method="POST" class="p-6 space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-3 gap-x-6 gap-y-5">
                
                {{-- Pilih Guru --}}
                <div class="space-y-1.5">
                    <label class="block text-[9px] font-black text-slate-400 uppercase tracking-widest px-1">Guru Pengajar</label>
                    <select name="guru_id" class="w-full px-4 py-3 bg-white border border-slate-200 rounded-lg text-[11px] font-bold uppercase outline-none focus:border-blue-500 transition-all cursor-pointer" required>
                        <option value="" disabled selected>-- PILIH GURU --</option>
                        @foreach($gurus as $guru)
                            <option value="{{ $guru->id }}" {{ old('guru_id') == $guru->id ? 'selected' : '' }}>{{ strtoupper($guru->nama) }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Pilih Mapel --}}
                <div class="space-y-1.5">
                    <label class="block text-[9px] font-black text-slate-400 uppercase tracking-widest px-1">Mata Pelajaran</label>
                    <select name="mapel_id" class="w-full px-4 py-3 bg-white border border-slate-200 rounded-lg text-[11px] font-bold uppercase outline-none focus:border-blue-500 transition-all cursor-pointer" required>
                        <option value="" disabled selected>-- PILIH MAPEL --</option>
                        @foreach($mapels as $mapel)
                            <option value="{{ $mapel->id }}" {{ old('mapel_id') == $mapel->id ? 'selected' : '' }}>{{ strtoupper($mapel->nama_mapel) }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Pilih Kelas --}}
                <div class="space-y-1.5">
                    <label class="block text-[9px] font-black text-blue-600 uppercase tracking-widest px-1">Pilih Kelas</label>
                    <select name="kelas" class="w-full px-4 py-3 bg-blue-50/30 border border-blue-100 rounded-lg text-[11px] font-black uppercase text-blue-900 outline-none focus:border-blue-500 transition-all cursor-pointer" required>
                        <option value="" disabled selected>-- PILIH KELAS --</option>
                        @foreach($data_kelas as $kls)
                            <option value="{{ $kls->nama_kelas }}" {{ old('kelas') == $kls->nama_kelas ? 'selected' : '' }}>
                                {{ strtoupper($kls->nama_kelas) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Hari --}}
                <div class="space-y-1.5">
                    <label class="block text-[9px] font-black text-slate-400 uppercase tracking-widest px-1">Hari Pelajaran</label>
                    <select name="hari" class="w-full px-4 py-3 bg-white border border-slate-200 rounded-lg text-[11px] font-bold uppercase outline-none focus:border-blue-500 transition-all cursor-pointer" required>
                        @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'] as $hari)
                            <option value="{{ $hari }}" {{ old('hari') == $hari ? 'selected' : '' }}>{{ strtoupper($hari) }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Waktu --}}
                <div class="md:col-span-2 space-y-1.5">
                    <label class="block text-[9px] font-black text-slate-400 uppercase tracking-widest px-1">Durasi KBM</label>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="flex items-center gap-3 bg-slate-50 border border-slate-200 rounded-lg px-4 py-2">
                            <span class="text-[9px] font-black text-blue-500 uppercase">Mulai</span>
                            <input type="text" name="jam_mulai" value="{{ old('jam_mulai') }}" placeholder="07:30" maxlength="5" class="time-mask w-full bg-transparent text-[12px] font-black text-slate-800 outline-none text-center">
                        </div>
                        <div class="flex items-center gap-3 bg-slate-50 border border-slate-200 rounded-lg px-4 py-2">
                            <span class="text-[9px] font-black text-blue-500 uppercase">Selesai</span>
                            <input type="text" name="jam_selesai" value="{{ old('jam_selesai') }}" placeholder="14:00" maxlength="5" class="time-mask w-full bg-transparent text-[12px] font-black text-slate-800 outline-none text-center">
                        </div>
                    </div>
                </div>
            </div>

            <div class="pt-6 border-t border-slate-50 flex justify-end items-center gap-3">
                <a href="{{ route('admin.jadwal.index') }}" class="px-8 py-2.5 rounded-lg text-[10px] font-black text-white bg-rose-600 hover:bg-rose-700 transition-all uppercase tracking-widest text-center active:scale-95">Batal</a>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-black py-2.5 px-10 rounded-lg transition-all uppercase text-[10px] tracking-widest flex items-center gap-2 active:scale-95">
                    <i class="fa-solid fa-floppy-disk"></i> Simpan Jadwal
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
    });
</script>
@endsection