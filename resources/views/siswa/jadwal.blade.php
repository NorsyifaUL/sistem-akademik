@extends('layouts.siswa')

@section('content')
<div class="space-y-8 animate-fade-in pb-10">
    {{-- Header Section --}}
    <div class="border-b border-gray-100 pb-5 flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <h2 class="text-2xl font-black text-gray-800 tracking-tighter uppercase italic">Jadwal Pelajaran</h2>
            <div class="flex items-center gap-2 mt-1">
                {{-- Logic untuk mengambil nama kelas yang bersih --}}
                @php 
                    $nama_kelas = $siswa->kelas->nama_kelas ?? '-'; 
                @endphp
                <span class="bg-[#064e3b] text-white text-[9px] font-black px-3 py-1 rounded-lg uppercase tracking-widest">
                    Kelas: {{ $nama_kelas }}
                </span>
                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-tighter">
                    Tahun Ajaran {{ $setup->tahun_ajaran ?? '2025/2026' }} 
                    <span class="text-[#064e3b]">({{ ($setup->semester ?? 1) == 1 ? 'Ganjil' : 'Genap' }})</span>
                </p>
            </div>
        </div>
        <div class="hidden md:block">
            <span class="bg-amber-50 text-amber-600 border border-amber-100 text-[10px] font-black px-4 py-2 rounded-xl uppercase italic">
                <i class="fa-solid fa-calendar-day mr-1 text-[#ffb800]"></i> {{ \Carbon\Carbon::now()->isoFormat('dddd') }}
            </span>
        </div>
    </div>

    {{-- Looping Hari --}}
    @forelse($jadwals as $hari => $listJadwal)
        <div class="space-y-4">
            <div class="flex items-center gap-3">
                <div class="h-6 w-1.5 bg-[#ffb800] rounded-full shadow-sm"></div>
                <h3 class="text-lg font-black text-gray-800 uppercase italic tracking-tighter">{{ $hari }}</h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                @foreach($listJadwal as $jadwal)
                <div class="bg-white rounded-[2rem] p-6 shadow-sm border border-gray-100 hover:shadow-lg hover:border-[#ffb800]/30 transition-all group overflow-hidden relative">
                    {{-- Badge Waktu --}}
                    <div class="flex justify-between items-center mb-4 relative z-10">
                        <span class="bg-gray-900 text-white text-[10px] font-black px-3 py-1 rounded-lg shadow-md">
                            {{ date('H:i', strtotime($jadwal->jam_mulai)) }} — {{ date('H:i', strtotime($jadwal->jam_selesai)) }}
                        </span>
                        <i class="fa-solid fa-book-open text-gray-100 group-hover:text-[#ffb800]/20 text-2xl transition-colors"></i>
                    </div>
                    
                    {{-- Info Mapel --}}
                    <h3 class="text-lg font-black text-gray-800 leading-tight uppercase group-hover:text-[#064e3b] transition-colors">
                        {{ $jadwal->mapel->nama_mapel ?? 'Mata Pelajaran' }}
                    </h3>

                    <div class="mt-4 pt-4 border-t border-gray-50 space-y-3">
                        {{-- Row Guru --}}
                        <div class="flex items-center gap-3">
                            <div class="h-8 w-8 rounded-lg bg-emerald-50 flex items-center justify-center border border-black/5">
                                <i class="fa-solid fa-chalkboard-user text-emerald-600 text-[10px]"></i>
                            </div>
                            <div class="min-w-0">
                                <p class="text-[8px] font-black text-gray-400 uppercase tracking-widest leading-none mb-0.5">Guru Pengajar</p>
                                <p class="text-[11px] text-gray-700 font-bold leading-tight truncate uppercase">
                                    {{ $jadwal->guru->nama ?? '-' }}
                                </p>
                            </div>
                        </div>

                        {{-- Row Ruangan --}}
                        <div class="flex items-center gap-3">
                            <div class="h-8 w-8 rounded-lg bg-amber-50 flex items-center justify-center border border-black/5">
                                <i class="fa-solid fa-location-dot text-amber-600 text-[10px]"></i>
                            </div>
                            <div class="min-w-0">
                                <p class="text-[8px] font-black text-gray-400 uppercase tracking-widest leading-none mb-0.5">Ruang Belajar</p>
                                <p class="text-[11px] text-gray-700 font-bold leading-tight truncate uppercase">
                                    {{ $jadwal->ruangan ?? 'Ruang ' . $nama_kelas }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    @empty
        <div class="py-20 text-center bg-gray-50 rounded-[2.5rem] border-2 border-dashed border-gray-200">
            <i class="fa-solid fa-calendar-xmark text-4xl text-gray-200 mb-4"></i>
            <p class="text-gray-400 font-black uppercase text-xs tracking-widest">Jadwal Belum Tersedia Untuk Kelas Ini</p>
        </div>
    @endforelse
</div>

<style>
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    .animate-fade-in { animation: fadeIn 0.4s ease-out forwards; }
</style>
@endsection