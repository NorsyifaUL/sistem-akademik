@extends('layouts.siswa')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8 animate-fade-in">
    
    {{-- KOLOM KIRI: WELCOME & JADWAL --}}
    <div class="lg:col-span-2 space-y-8">
        
        {{-- Welcome Card --}}
        <div class="bg-[#064e3b] rounded-3xl p-8 text-white relative overflow-hidden shadow-lg border-b-8 border-[#ffb800]">
            <div class="relative z-10">
                <h2 class="text-3xl font-extrabold tracking-tight">Halo, {{ auth()->user()->name }}!</h2>
                <p class="text-green-100 mt-2 font-medium opacity-90">Selamat Datang di Sistem Informasi Akademik SMAN 1 Jejangkit</p>
                <div class="mt-6 inline-flex items-center gap-2 bg-white/10 px-4 py-2 rounded-xl backdrop-blur-sm border border-white/10">
                    <i class="fa-solid fa-graduation-cap text-[#ffb800]"></i>
                    <span class="text-xs font-bold uppercase tracking-widest">Kelas: {{ $siswa->kelas ?? '-' }}</span>
                </div>
            </div>
            {{-- Dekorasi --}}
            <div class="absolute -right-10 -top-10 w-40 h-40 bg-[#ffb800] rounded-full opacity-20"></div>
            <div class="absolute right-20 -bottom-10 w-24 h-24 bg-white rounded-full opacity-10"></div>
        </div>

        {{-- Jadwal Hari Ini --}}
        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8">
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h3 class="text-lg font-black text-gray-800 uppercase tracking-tight">Jadwal Pelajaran</h3>
                    <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">{{ $hariIni }}, {{ date('d M Y') }}</p>
                </div>
                <a href="{{ route('siswa.jadwal') }}" class="text-[10px] font-black text-[#ffb800] hover:text-[#064e3b] transition-colors uppercase tracking-widest border-2 border-[#ffb800]/20 px-4 py-2 rounded-xl">Lihat Semua</a>
            </div>
            
            <div class="space-y-4">
                @forelse($jadwalHariIni as $j)
                <div class="flex items-center p-5 bg-gray-50 rounded-2xl border-l-4 border-[#ffb800] group hover:bg-white hover:shadow-md transition-all duration-300">
                    <div class="w-24 text-center border-r border-gray-200 pr-4">
                        <p class="text-sm font-black text-[#064e3b]">{{ $j->jam_mulai }}</p>
                        <p class="text-[9px] text-gray-400 font-bold uppercase italic">s/d {{ $j->jam_selesai }}</p>
                    </div>
                    <div class="ml-6 flex-1">
                        <h4 class="font-black text-gray-800 tracking-tight text-sm uppercase">{{ $j->mapel?->nama_mapel ?? 'Mapel Tidak Diketahui' }}</h4>
                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider flex items-center gap-2 mt-1">
                            <i class="fa-solid fa-user-tie text-[#ffb800]"></i> {{ $j->guru?->nama ?? 'Guru Pengampu' }}
                        </p>
                    </div>
                    <div class="hidden md:block">
                        <span class="text-[9px] font-black bg-white text-[#064e3b] px-3 py-1.5 rounded-lg border border-gray-100 uppercase">Ruang {{ $siswa->kelas ?? '-' }}</span>
                    </div>
                </div>
                @empty
                <div class="text-center py-10 bg-gray-50 rounded-3xl border-2 border-dashed border-gray-200">
                    <i class="fa-solid fa-calendar-day text-gray-200 text-4xl mb-3"></i>
                    <p class="text-gray-400 font-bold text-xs uppercase italic tracking-widest">Tidak ada jadwal untuk hari ini.</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- KOLOM KANAN: STATISTIK & STATUS --}}
    <div class="space-y-8">
        
        {{-- Statistik Presensi --}}
        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8 relative overflow-hidden">
            <h3 class="text-sm font-black text-gray-800 mb-6 uppercase tracking-widest flex items-center gap-2">
                <i class="fa-solid fa-chart-line text-[#064e3b]"></i> Statistik Presensi
            </h3>
            
            <div class="grid grid-cols-1 gap-4">
                {{-- Hadir --}}
                <div class="flex items-center justify-between p-4 bg-emerald-50 rounded-2xl border border-emerald-100">
                    <div class="flex items-center gap-3">
                        <div class="h-10 w-10 bg-white rounded-xl flex items-center justify-center shadow-sm">
                            <i class="fa-solid fa-check text-emerald-500"></i>
                        </div>
                        <span class="text-xs font-black text-emerald-700 uppercase tracking-widest">Hadir</span>
                    </div>
                    <p class="text-2xl font-black text-emerald-700">{{ $absensi->where('status', 'Hadir')->count() }}</p>
                </div>

                {{-- Izin/Sakit --}}
                <div class="flex items-center justify-between p-4 bg-amber-50 rounded-2xl border border-amber-100">
                    <div class="flex items-center gap-3">
                        <div class="h-10 w-10 bg-white rounded-xl flex items-center justify-center shadow-sm">
                            <i class="fa-solid fa-envelope text-amber-500"></i>
                        </div>
                        <span class="text-xs font-black text-amber-700 uppercase tracking-widest">Izin/Sakit</span>
                    </div>
                    <p class="text-2xl font-black text-amber-700">{{ $absensi->whereIn('status', ['Izin', 'Sakit'])->count() }}</p>
                </div>

                {{-- Alpa --}}
                <div class="flex items-center justify-between p-4 bg-rose-50 rounded-2xl border border-rose-100">
                    <div class="flex items-center gap-3">
                        <div class="h-10 w-10 bg-white rounded-xl flex items-center justify-center shadow-sm">
                            <i class="fa-solid fa-xmark text-rose-500"></i>
                        </div>
                        <span class="text-xs font-black text-rose-700 uppercase tracking-widest">Alpa</span>
                    </div>
                    <p class="text-2xl font-black text-rose-700">{{ $absensi->where('status', 'Alpa')->count() }}</p>
                </div>
            </div>
            
            <div class="mt-6 pt-6 border-t border-gray-50 flex justify-between items-center px-2">
                <p class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-400">Total Sesi</p>
                <p class="text-xs font-black text-[#064e3b]">{{ $totalAbsensi }} Pertemuan</p>
            </div>
        </div>

        {{-- Tombol Cepat Rapor --}}
        <a href="{{ route('siswa.nilai') }}" class="block group">
            <div class="bg-[#ffb800] p-6 rounded-3xl shadow-lg transition-all duration-300 group-hover:bg-[#064e3b] group-hover:-translate-y-2 relative overflow-hidden">
                <div class="relative z-10 flex items-center justify-between">
                    <div>
                        <p class="text-[#064e3b] group-hover:text-white font-black uppercase text-xs tracking-widest">Laporan Nilai</p>
                        <p class="text-[#064e3b]/70 group-hover:text-white/70 text-[9px] mt-1 font-bold uppercase italic">Cek Nilai Semester Ini</p>
                    </div>
                    <div class="h-10 w-10 bg-white/20 rounded-xl flex items-center justify-center group-hover:rotate-12 transition-transform">
                        <i class="fa-solid fa-file-invoice text-[#064e3b] group-hover:text-white"></i>
                    </div>
                </div>
                <div class="absolute -right-5 -bottom-5 h-20 w-20 bg-white/10 rounded-full group-hover:scale-150 transition-transform duration-700"></div>
            </div>
        </a>
    </div>
</div>

<style>
    @keyframes fadeIn { 
        from { opacity: 0; transform: translateY(15px); } 
        to { opacity: 1; transform: translateY(0); } 
    }
    .animate-fade-in { animation: fadeIn 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
</style>
@endsection