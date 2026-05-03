@extends('layouts.admin')

@section('content')
<div class="p-6 space-y-6 animate-fade-in">
    <div class="mb-6">
        <h2 class="text-2xl font-black text-gray-800 tracking-tight uppercase">Tambah Jadwal Pelajaran</h2>
        <p class="text-xs font-bold text-gray-400 mt-1 uppercase tracking-widest">Susun kegiatan belajar mengajar SMAN 1 Jejangkit</p>
    </div>

    <div class="max-w-4xl bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden border-t-4 border-t-blue-600">
        <form action="{{ route('admin.jadwal.store') }}" method="POST" class="p-8 space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Pilih Guru --}}
                <div class="space-y-2">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Guru Pengajar</label>
                    <div class="relative group">
                        <select name="guru_id" class="w-full px-4 py-3 rounded-xl border-gray-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-sm font-bold shadow-sm transition-all outline-none appearance-none cursor-pointer text-gray-700 bg-gray-50/30" required>
                            <option value="" disabled selected>-- Pilih Guru --</option>
                            @foreach($gurus as $guru)
                                <option value="{{ $guru->id }}" {{ old('guru_id') == $guru->id ? 'selected' : '' }}>{{ $guru->nama }}</option>
                            @endforeach
                        </select>
                        <i class="fa-solid fa-chevron-down absolute right-4 top-4 text-gray-300 text-[10px] pointer-events-none group-focus-within:rotate-180 transition-transform"></i>
                    </div>
                </div>

                {{-- Pilih Mapel --}}
                <div class="space-y-2">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Mata Pelajaran</label>
                    <div class="relative group">
                        <select name="mapel_id" class="w-full px-4 py-3 rounded-xl border-gray-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-sm font-bold shadow-sm transition-all outline-none appearance-none cursor-pointer text-gray-700 bg-gray-50/30" required>
                            <option value="" disabled selected>-- Pilih Mapel --</option>
                            @foreach($mapels as $mapel)
                                <option value="{{ $mapel->id }}" {{ old('mapel_id') == $mapel->id ? 'selected' : '' }}>{{ $mapel->nama_mapel }}</option>
                            @endforeach
                        </select>
                        <i class="fa-solid fa-chevron-down absolute right-4 top-4 text-gray-300 text-[10px] pointer-events-none group-focus-within:rotate-180 transition-transform"></i>
                    </div>
                </div>

                {{-- Pilih Kelas (Dropdown Baru) --}}
                <div class="space-y-2">
                    <label class="block text-[10px] font-black text-blue-600 uppercase tracking-[0.2em]">Pilih Kelas</label>
                    <div class="relative group">
                        <select name="kelas" class="w-full px-4 py-3 rounded-xl border-blue-100 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-sm font-black shadow-sm transition-all outline-none appearance-none cursor-pointer text-blue-900 bg-blue-50/30" required>
                            <option value="" disabled selected>-- Semua Kelas --</option>
                            @foreach(['X 1', 'X 2', 'XI 1', 'XI 2', 'XII IPA', 'XII IPS'] as $kls)
                                <option value="{{ $kls }}" {{ old('kelas') == $kls ? 'selected' : '' }}>Kelas {{ $kls }}</option>
                            @endforeach
                        </select>
                        <i class="fa-solid fa-chalkboard absolute right-4 top-4 text-blue-300 text-xs pointer-events-none"></i>
                    </div>
                </div>

                {{-- Pilih Hari --}}
                <div class="space-y-2">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Hari</label>
                    <div class="relative group">
                        <select name="hari" class="w-full px-4 py-3 rounded-xl border-gray-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-sm font-bold shadow-sm transition-all outline-none appearance-none cursor-pointer text-gray-700 bg-gray-50/30" required>
                            @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'] as $hari)
                                <option value="{{ $hari }}" {{ old('hari') == $hari ? 'selected' : '' }}>{{ $hari }}</option>
                            @endforeach
                        </select>
                        <i class="fa-solid fa-calendar-day absolute right-4 top-4 text-gray-300 text-xs pointer-events-none"></i>
                    </div>
                </div>

                {{-- Jam Input --}}
                <div class="md:col-span-2 grid grid-cols-2 gap-4 p-6 bg-blue-50/50 rounded-2xl border border-blue-100 border-dashed items-center">
                    <div class="col-span-2 flex items-center gap-2 mb-2">
                        <i class="fa-solid fa-clock text-blue-600"></i>
                        <h4 class="text-[10px] font-black text-blue-800 uppercase tracking-widest">Interval Waktu KBM (Format 24 Jam)</h4>
                    </div>
                    <div>
                        <label class="block text-[9px] font-black text-blue-400 uppercase mb-2 text-center">Mulai</label>
                        <input type="text" name="jam_mulai" value="{{ old('jam_mulai') }}" placeholder="07:30" maxlength="5" autocomplete="off"
                               class="time-mask w-full px-4 py-3 rounded-xl border-white focus:border-blue-500 focus:ring-0 text-lg font-black shadow-sm transition-all text-center text-blue-900 uppercase">
                    </div>
                    <div>
                        <label class="block text-[9px] font-black text-blue-400 uppercase mb-2 text-center">Selesai</label>
                        <input type="text" name="jam_selesai" value="{{ old('jam_selesai') }}" placeholder="14:00" maxlength="5" autocomplete="off"
                               class="time-mask w-full px-4 py-3 rounded-xl border-white focus:border-blue-500 focus:ring-0 text-lg font-black shadow-sm transition-all text-center text-blue-900 uppercase">
                    </div>
                </div>
            </div>

            <div class="pt-6 flex justify-between items-center border-t border-gray-50 mt-4">
                <a href="{{ route('admin.jadwal.index') }}" 
                class="px-8 py-2.5 rounded-lg text-sm font-bold text-white bg-rose-600 hover:bg-rose-700 transition-all shadow-md shadow-rose-100 uppercase tracking-widest text-center">
                    Batal
                </a>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-black py-4 px-12 rounded-xl shadow-lg shadow-blue-200 transition-all active:scale-95 uppercase text-xs tracking-[0.2em]">
                    Simpan Jadwal
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Script Masking Jam --}}
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
@endsection