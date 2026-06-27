@extends('layouts.admin')

@section('content')
<div class="p-4 space-y-6 animate-fade-in">
    {{-- Header --}}
    <div class="px-1">
        <h1 class="text-xl font-black text-slate-800 tracking-tight uppercase leading-none">
            Koreksi <span class="text-blue-600">Absensi</span>
        </h1>
        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-[0.2em] mt-1.5">
            SIAKAD SMAN 1 JEJANGKIT
        </p>
    </div>

    {{-- Main Card --}}
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="h-[4px] bg-blue-600 w-full"></div>

        <div class="p-6">
            <form action="{{ route('admin.absensi.update', $absensi->id) }}" method="POST">
                @csrf
                @method('PUT')

                {{-- Info Siswa --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
                    <div class="flex items-center gap-4 bg-slate-50 p-3 rounded-lg border border-slate-100">
                        <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest w-20">Siswa</span>
                        <span class="text-[11px] font-black text-slate-700 uppercase">{{ $absensi->siswa->nama }}</span>
                    </div>
                    <div class="flex items-center gap-4 bg-slate-50 p-3 rounded-lg border border-slate-100">
                        <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest w-20">Kelas</span>
                        <span class="text-[11px] font-black text-blue-600 uppercase">{{ $absensi->siswa->dataKelas->nama_kelas ?? 'N/A' }}</span>
                    </div>
                    <div class="flex items-center gap-4 bg-slate-50 p-3 rounded-lg border border-slate-100">
                        <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest w-20">Waktu</span>
                        <span class="text-[11px] font-bold text-slate-700">{{ $absensi->created_at->format('d/m/y - H:i') }}</span>
                    </div>
                    <div class="flex items-center gap-4 bg-slate-50 p-3 rounded-lg border border-slate-100">
                        <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest w-20">ID Log</span>
                        <span class="text-[11px] font-bold text-slate-500">#{{ $absensi->id }}</span>
                    </div>
                </div>

                {{-- Status Selection --}}
                <div class="space-y-4">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] block">Pilih Status Baru</label>
                    
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                        @php
                            $options = [
                                'Hadir' => 'emerald-500', 
                                'Sakit' => 'amber-500', 
                                'Izin'  => 'blue-500', 
                                'Alpa'  => 'rose-500'
                            ];
                        @endphp

                        @foreach($options as $st => $color)
                        <label class="relative cursor-pointer group">
                            <input type="radio" name="status" value="{{ $st }}" class="sr-only peer" {{ $absensi->status == $st ? 'checked' : '' }}>
                            <div class="py-4 border-2 border-slate-200 rounded-xl bg-white transition-all duration-300 
                                peer-checked:border-{{ $color }} peer-checked:bg-{{ $color }} 
                                hover:border-slate-400 flex items-center justify-center shadow-sm">
                                <span class="text-[11px] font-black uppercase tracking-widest text-slate-500 peer-checked:text-white transition-colors duration-300">
                                    {{ $st }}
                                </span>
                            </div>
                        </label>
                        @endforeach
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="flex items-center justify-end gap-3 mt-10 pt-6 border-t border-slate-100">
                    <a href="{{ route('admin.absensi.index') }}" 
                       class="bg-rose-600 hover:bg-rose-700 text-white px-6 py-2.5 rounded-lg font-black text-[10px] uppercase tracking-widest transition-all active:scale-95 shadow-sm">
                        Batal
                    </a>
                    <button type="submit" 
                        class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-lg font-black text-[10px] uppercase tracking-widest shadow-sm transition-all active:scale-95">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Tailwind Safelist agar warna aktif selalu muncul --}}
<div class="hidden peer-checked:border-emerald-500 peer-checked:bg-emerald-500 peer-checked:text-white
             peer-checked:border-amber-500 peer-checked:bg-amber-500 peer-checked:text-white
             peer-checked:border-blue-500 peer-checked:bg-blue-500 peer-checked:text-white
             peer-checked:border-rose-500 peer-checked:bg-rose-500 peer-checked:text-white"></div>

<style>
    .animate-fade-in { animation: fadeIn 0.4s ease-out forwards; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(5px); } to { opacity: 1; transform: translateY(0); } }
</style>
@endsection