@extends('layouts.siswa')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 animate-fade-in pb-6">
    
    {{-- KOLOM KIRI --}}
    <div class="lg:col-span-2 space-y-5">
        {{-- Welcome Card --}}
        <div class="bg-[#064e3b] rounded-[2rem] p-6 text-white relative overflow-hidden shadow-lg border-b-[8px] border-[#ffb800]">
            <div class="relative z-10">
                <h2 class="text-2xl font-black italic tracking-tight">Halo, {{ auth()->user()->name }}!</h2>
                <p class="text-green-100/80 mt-1 font-medium text-xs">Selamat Datang di SIAKAD SMAN 1 Jejangkit</p>
                
                {{-- PERBAIKAN: Mengakses properti nama_kelas agar tidak tampil JSON --}}
                <div class="mt-4 inline-flex items-center gap-2 bg-white/20 px-3 py-1.5 rounded-lg backdrop-blur-md border border-white/10 text-[10px] font-bold uppercase tracking-widest">
                    <i class="fa-solid fa-graduation-cap text-[#ffb800]"></i> 
                    KELAS: {{ $siswa->kelas->nama_kelas ?? '-' }}
                </div>
            </div>
            <div class="absolute -right-10 -top-10 w-32 h-32 bg-[#ffb800] rounded-full opacity-20"></div>
        </div>

        {{-- Jadwal Hari Ini --}}
        <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-6">
            <div class="flex justify-between items-center mb-5">
                <div>
                    <h3 class="text-base font-black text-gray-800 uppercase tracking-tighter">Jadwal Pelajaran</h3>
                    <p class="text-[9px] text-gray-400 font-bold uppercase">{{ $hariIni }}, {{ date('d M Y') }}</p>
                </div>
                <a href="{{ route('siswa.jadwal') }}" class="text-[9px] font-black text-[#ffb800] border border-[#ffb800]/30 px-3 py-1.5 rounded-lg uppercase tracking-widest">Lihat Semua</a>
            </div>
            
            <div class="space-y-3">
                @forelse($jadwalHariIni as $j)
                <div class="flex items-center p-3 bg-gray-50 rounded-xl border-l-4 border-[#ffb800] hover:bg-white hover:shadow-md transition-all">
                    <div class="w-16 text-center border-r border-gray-200 pr-3 shrink-0">
                        <p class="text-xs font-black text-[#064e3b]">{{ $j->jam_mulai }}</p>
                        <p class="text-[7px] text-gray-400 font-bold italic uppercase">{{ $j->jam_selesai }}</p>
                    </div>
                    <div class="ml-4 flex-1 min-w-0 font-black">
                        <h4 class="text-gray-800 text-[11px] uppercase truncate">{{ $j->mapel?->nama_mapel }}</h4>
                        <p class="text-[8px] text-gray-400 uppercase truncate mt-0.5">
                            <i class="fa-solid fa-user-tie text-[#ffb800] mr-1"></i>{{ $j->guru?->nama }}
                        </p>
                    </div>
                    {{-- PERBAIKAN: Menggunakan nama_kelas untuk label ruangan --}}
                    <span class="hidden md:block text-[7px] font-black bg-white text-[#064e3b] px-2 py-1 rounded-md border border-gray-100 uppercase italic">
                        R. {{ $siswa->kelas->nama_kelas ?? '-' }}
                    </span>
                </div>
                @empty
                <div class="text-center py-8 bg-gray-50 rounded-2xl border-2 border-dashed border-gray-100 text-gray-400 font-bold text-[9px] uppercase tracking-widest">
                    Tidak ada jadwal
                </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- KOLOM KANAN --}}
    <div class="space-y-5">
        {{-- Statistik Presensi --}}
        <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-6 text-center">
            <h3 class="text-[9px] font-black text-gray-400 mb-4 uppercase tracking-[0.2em] flex justify-center items-center gap-2">
                <i class="fa-solid fa-chart-line text-[#064e3b]"></i> Statistik
            </h3>
            <div class="grid grid-cols-1 gap-2.5">
                @foreach([
                    ['Hadir', $totalHadir, 'emerald', 'fa-check'],
                    ['Izin/Sakit', $totalIzinSakit, 'amber', 'fa-envelope'],
                    ['Alpa', $totalAlpa, 'rose', 'fa-xmark']
                ] as [$label, $count, $color, $icon])
                <div class="flex items-center justify-between p-3 bg-{{$color}}-50 rounded-xl border border-{{$color}}-100/50">
                    <div class="flex items-center gap-2.5">
                        <div class="h-7 w-7 bg-white rounded-lg flex items-center justify-center shadow-sm text-{{$color}}-500 text-[10px]">
                            <i class="fa-solid {{$icon}}"></i>
                        </div>
                        <span class="text-[9px] font-black text-{{$color}}-700 uppercase tracking-widest">{{$label}}</span>
                    </div>
                    <p class="text-lg font-black text-{{$color}}-700">{{$count}}</p>
                </div>
                @endforeach
            </div>
            <div class="mt-4 pt-4 border-t flex justify-between items-center text-[9px] font-bold uppercase text-gray-400 px-1">
                <span>Total Sesi</span>
                <span class="text-[#064e3b] font-black">{{ $totalAbsensi }}</span>
            </div>
        </div>

        {{-- Laporan Nilai --}}
        <a href="{{ route('siswa.nilai') }}" class="block group">
            <div class="bg-[#ffb800] p-4 rounded-[1.5rem] shadow-lg transition-all duration-300 group-hover:bg-[#064e3b] group-hover:-translate-y-1 relative overflow-hidden border-b-4 border-black/10 text-center">
                <div class="relative z-10 flex items-center justify-between px-2">
                    <div class="text-left leading-tight">
                        <p class="text-[#064e3b] group-hover:text-white font-black uppercase text-[10px] tracking-widest">Laporan Nilai</p>
                        <p class="text-[#064e3b]/70 group-hover:text-white/60 text-[8px] font-bold uppercase italic mt-0.5">Lihat Nilai Semester Ini</p>
                    </div>
                    <div class="h-8 w-8 bg-white/20 rounded-lg flex items-center justify-center group-hover:rotate-12 transition-transform shadow-sm">
                        <i class="fa-solid fa-file-invoice text-[#064e3b] group-hover:text-white text-base"></i>
                    </div>
                </div>
                <div class="absolute -right-5 -bottom-5 h-16 w-16 bg-white/10 rounded-full"></div>
            </div>
        </a>
    </div>
</div>
@endsection