@extends('layouts.siswa')

@section('content')
<div class="space-y-10 animate-fade-in">
    {{-- Header: Dinamis berdasarkan Setting --}}
    <div class="border-b border-gray-100 pb-6 flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <h2 class="text-3xl font-black text-gray-800 tracking-tight uppercase">Jadwal Pelajaran</h2>
            <div class="flex items-center gap-2 mt-2">
                @php
                    $nama_kelas = auth()->user()->siswa->dataKelas->nama_kelas ?? (auth()->user()->siswa->kelas ?? 'Belum Ditentukan');
                @endphp
                
                <span class="bg-emerald-100 text-emerald-800 text-[10px] font-extrabold px-3 py-1 rounded-lg uppercase tracking-wider">
                    Kelas: {{ $nama_kelas }}
                </span>
                <span class="text-gray-300 text-xs">•</span>
                <p class="text-xs text-gray-500 font-bold uppercase tracking-widest">
                    Tahun Ajaran {{ $setup->tahun_ajaran ?? '2025/2026' }} 
                    <span class="text-emerald-500">({{ $setup->semester == 1 ? 'Ganjil' : 'Genap' }})</span>
                </p>
            </div>
        </div>

        {{-- Badge Hari Ini --}}
        <div class="hidden md:block">
            <span class="bg-amber-50 text-amber-600 border border-amber-100 text-[10px] font-black px-4 py-2 rounded-xl uppercase tracking-tighter">
                <i class="fa-solid fa-calendar-day mr-1"></i> Hari Ini: {{ \Carbon\Carbon::now()->isoFormat('dddd') }}
            </span>
        </div>
    </div>

    {{-- Looping Berdasarkan Hari --}}
    @forelse($jadwals as $hari => $listJadwal)
        <div class="space-y-5">
            {{-- Judul Hari --}}
            <div class="flex items-center gap-4">
                <div class="h-8 w-1.5 bg-amber-400 rounded-full shadow-sm"></div>
                <h3 class="text-xl font-black text-gray-800 uppercase tracking-tighter italic">
                    {{ $hari }}
                </h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($listJadwal as $jadwal)
                <div class="bg-white rounded-[2rem] p-7 shadow-sm border border-gray-100 hover:shadow-xl hover:border-amber-400/30 transition-all duration-300 group relative overflow-hidden">
                    {{-- Decorative Element --}}
                    <div class="absolute -right-4 -top-4 h-16 w-16 bg-gray-50 rounded-full group-hover:bg-amber-50 transition-colors duration-300"></div>

                    <div class="flex justify-between items-start mb-5 relative">
                        <div class="flex flex-col">
                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Waktu Belajar</span>
                            <span class="bg-gray-900 text-white text-xs font-bold px-4 py-1.5 rounded-xl shadow-sm">
                                {{ date('H:i', strtotime($jadwal->jam_mulai)) }} — {{ date('H:i', strtotime($jadwal->jam_selesai)) }}
                            </span>
                        </div>
                        <div class="h-10 w-10 rounded-2xl bg-gray-50 flex items-center justify-center text-gray-300 group-hover:text-amber-500 group-hover:bg-white transition-all duration-300 shadow-sm">
                            <i class="fa-solid fa-book-open text-lg"></i>
                        </div>
                    </div>
                    
                    {{-- Judul Mata Pelajaran --}}
                    <h3 class="text-xl font-extrabold text-gray-800 leading-none tracking-tight group-hover:text-emerald-700 transition-colors">
                        {{ $jadwal->mapel->nama_mapel ?? 'Mata Pelajaran' }}
                    </h3>

                    <div class="mt-6 space-y-4">
                        {{-- Nama Guru --}}
                        <div class="flex items-center gap-3">
                            <div class="h-9 w-9 rounded-xl bg-emerald-50 flex items-center justify-center border border-emerald-100">
                                <i class="fa-solid fa-chalkboard-user text-emerald-600 text-xs"></i>
                            </div>
                            <div>
                                <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest leading-none mb-1">Guru Pengajar</p>
                                <p class="text-[12px] text-gray-700 font-bold leading-tight">
                                    {{ $jadwal->guru->nama_guru ?? ($jadwal->guru->nama ?? 'Guru Belum Ditentukan') }}
                                </p>
                            </div>
                        </div>

                        {{-- Lokasi Ruangan --}}
                        <div class="flex items-center gap-3">
                            <div class="h-9 w-9 rounded-xl bg-amber-50 flex items-center justify-center border border-amber-100">
                                <i class="fa-solid fa-location-dot text-amber-600 text-xs"></i>
                            </div>
                            <div>
                                <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest leading-none mb-1">Ruang / Lokasi</p>
                                <p class="text-[11px] text-amber-600 font-extrabold uppercase tracking-tight">
                                    {{ $jadwal->ruangan ?? 'Ruang Kelas ' . $nama_kelas }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    @empty
        {{-- State Kosong --}}
        <div class="bg-white p-24 rounded-[3rem] text-center border-4 border-dashed border-gray-50 flex flex-col items-center">
            <div class="h-20 w-20 bg-gray-50 rounded-full flex items-center justify-center mb-6 text-gray-200">
                <i class="fa-solid fa-calendar-xmark text-4xl"></i>
            </div>
            <p class="text-gray-400 font-black uppercase tracking-[0.2em] text-sm">Jadwal Belum Tersedia</p>
            <p class="text-gray-300 text-xs mt-2 font-bold uppercase">Silahkan hubungi bagian kurikulum atau wali kelas.</p>
        </div>
    @endforelse
</div>

<style>
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    .animate-fade-in { animation: fadeIn 0.5s ease-out forwards; }
</style>
@endsection