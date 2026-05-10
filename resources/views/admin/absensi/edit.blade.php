@extends('layouts.admin')

@section('content')
<div class="p-4 space-y-4 animate-fade-in">
    {{-- Header Mini --}}
    <div class="flex items-end justify-between px-1">
        <div>
            <h2 class="text-base font-black text-slate-800 tracking-tight uppercase leading-none">Koreksi <span class="text-blue-600">Absensi</span></h2>
            <p class="text-[8px] text-slate-400 font-bold uppercase tracking-[0.1em] mt-1">SIAKAD SMAN 1 JEJANGKIT</p>
        </div>
    </div>

    {{-- Main Card --}}
    <div class="w-full bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        {{-- Accent Line --}}
        <div class="h-[3px] bg-blue-600 w-full"></div>

        <div class="p-5">
            <form action="{{ route('admin.absensi.update', $absensi->id) }}" method="POST">
                @csrf
                @method('PUT')

                {{-- Info Siswa: Jarak Dirapatkan --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-12 gap-y-2 mb-6">
                    <div class="flex items-center gap-4 border-b border-slate-50 py-1">
                        <span class="text-[8px] font-black text-blue-500 uppercase tracking-widest w-20">Siswa</span>
                        <span class="text-[10px] font-bold text-slate-700 uppercase">{{ $absensi->siswa->nama }}</span>
                    </div>
                    <div class="flex items-center gap-4 border-b border-slate-50 py-1">
                        <span class="text-[8px] font-black text-blue-500 uppercase tracking-widest w-20">Kelas</span>
                        <span class="text-[10px] font-bold text-slate-700 uppercase">{{ $absensi->siswa->dataKelas->nama_kelas }}</span>
                    </div>
                    <div class="flex items-center gap-4 border-b border-slate-50 py-1">
                        <span class="text-[8px] font-black text-blue-500 uppercase tracking-widest w-20">Waktu</span>
                        <span class="text-[10px] font-bold text-slate-700 uppercase">{{ $absensi->created_at->format('d/m/y - H:i') }}</span>
                    </div>
                    <div class="flex items-center gap-4 border-b border-slate-50 py-1">
                        <span class="text-[8px] font-black text-blue-500 uppercase tracking-widest w-20">ID Log</span>
                        <span class="text-[10px] font-bold text-slate-400 uppercase">#{{ $absensi->id }}</span>
                    </div>
                </div>

                {{-- Status Selection --}}
                <div class="space-y-3">
                    <label class="text-[9px] font-black text-slate-400 uppercase tracking-[0.2em] block">Pilih Status Baru</label>
                    
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                        @php
                            $options = [
                                'Hadir' => 'emerald', 'Sakit' => 'amber', 
                                'Izin' => 'blue', 'Alpa' => 'rose'
                            ];
                        @endphp

                        @foreach($options as $st => $color)
                        <label class="relative cursor-pointer">
                            <input type="radio" name="status" value="{{ $st }}" class="sr-only peer" {{ $absensi->status == $st ? 'checked' : '' }}>
                            <div class="py-2 border border-slate-200 rounded-lg bg-white transition-all duration-200 
                                peer-checked:border-{{ $color }}-500 peer-checked:bg-{{ $color }}-50/50 
                                hover:border-slate-300 flex items-center justify-center shadow-sm">
                                <span class="text-[9px] font-black uppercase tracking-widest text-slate-500 peer-checked:text-{{ $color }}-700">
                                    {{ $st }}
                                </span>
                            </div>
                            <div class="absolute -top-1 -right-1 w-3 h-3 bg-{{ $color }}-500 rounded-full items-center justify-center hidden peer-checked:flex border border-white">
                                <i class="fa-solid fa-check text-[6px] text-white"></i>
                            </div>
                        </label>
                        @endforeach
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="flex items-center justify-end gap-2 mt-8 pt-4 border-t border-slate-50">
                    <a href="{{ route('admin.absensi.index') }}" 
                       class="bg-rose-500 hover:bg-rose-600 text-white px-5 py-2 rounded-md font-black text-[9px] uppercase tracking-widest shadow-sm transition-all active:scale-95 flex items-center gap-2">
                        <i class="fa-solid fa-xmark text-[8px]"></i>
                        Batal
                    </a>
                    <button type="submit" 
                        class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-md font-black text-[9px] uppercase tracking-widest shadow-sm transition-all active:scale-95 flex items-center gap-2">
                        <i class="fa-solid fa-save text-[8px]"></i>
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Tailwind Safelist --}}
<div class="hidden border-emerald-500 bg-emerald-50/50 text-emerald-700 bg-emerald-500
             border-amber-500 bg-amber-50/50 text-amber-700 bg-amber-500
             border-blue-500 bg-blue-50/50 text-blue-700 bg-blue-500
             border-rose-500 bg-rose-50/50 text-rose-700 bg-rose-500"></div>
@endsection