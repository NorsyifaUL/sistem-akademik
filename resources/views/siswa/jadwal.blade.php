@extends('layouts.siswa')

@section('content')
<div class="space-y-10">
    {{-- Header: Bersih & Modern --}}
    <div class="border-b border-gray-100 pb-6">
        <h2 class="text-3xl font-black text-gray-800 tracking-tight">Jadwal Pelajaran</h2>
        <div class="flex items-center gap-2 mt-2">
            {{-- Perbaikan: Menggunakan @endphp dan nama variabel yang konsisten --}}
            @php
                $nama_kelas = auth()->user()->siswa->dataKelas->nama_kelas ?? (auth()->user()->siswa->kelas ?? 'Belum Ditentukan');
            @endphp
            
            <span class="bg-green-100 text-[#064e3b] text-[10px] font-extrabold px-3 py-1 rounded-lg uppercase tracking-wider">
                Kelas: {{ $nama_kelas }}
            </span>
            <span class="text-gray-300 text-xs">•</span>
            <p class="text-xs text-gray-500 font-bold uppercase tracking-widest">Tahun Ajaran 2025/2026</p>
        </div>
    </div>

    {{-- Looping Berdasarkan Hari --}}
    @forelse($jadwals as $hari => $listJadwal)
        <div class="space-y-5">
            {{-- Judul Hari --}}
            <div class="flex items-center gap-4">
                <div class="h-10 w-1 bg-[#ffb800] rounded-full"></div>
                <h3 class="text-xl font-black text-[#064e3b] uppercase tracking-tighter italic">
                    {{ $hari }}
                </h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($listJadwal as $jadwal)
                <div class="bg-white rounded-[2rem] p-7 shadow-sm border border-gray-100 hover:shadow-xl hover:border-[#ffb800]/30 transition-all duration-300 group">
                    <div class="flex justify-between items-start mb-5">
                        <div class="flex flex-col">
                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Waktu Belajar</span>
                            <span class="bg-[#064e3b] text-white text-xs font-bold px-4 py-1.5 rounded-xl shadow-inner">
                                {{ date('H:i', strtotime($jadwal->jam_mulai)) }} — {{ date('H:i', strtotime($jadwal->jam_selesai)) }}
                            </span>
                        </div>
                        <div class="h-10 w-10 rounded-2xl bg-gray-50 flex items-center justify-center text-gray-300 group-hover:text-[#ffb800] group-hover:bg-yellow-50 transition-all duration-300">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                        </div>
                    </div>
                    
                    {{-- Judul Mata Pelajaran --}}
                    <h3 class="text-xl font-extrabold text-gray-800 leading-none tracking-tight">
                        {{ $jadwal->mapel->nama_mapel ?? 'Mata Pelajaran' }}
                    </h3>

                    <div class="mt-5 space-y-3">
                        {{-- Nama Guru --}}
                        <div class="flex items-center gap-3">
                            <div class="h-8 w-8 rounded-lg bg-green-50 flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-[#064e3b]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Guru Mapel</p>
                                <p class="text-[13px] text-gray-600 font-bold leading-tight">
                                    {{ $jadwal->guru->nama_guru ?? ($jadwal->guru->nama ?? 'Guru Belum Ditentukan') }}
                                </p>
                            </div>
                        </div>

                        {{-- Lokasi Ruangan: Otomatis ke Nama Kelas --}}
                        <div class="flex items-center gap-3">
                            <div class="h-8 w-8 rounded-lg bg-yellow-50 flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-[#ffb800]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Lokasi Belajar</p>
                                <p class="text-[11px] text-[#ffb800] font-bold uppercase tracking-tight">
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
            <div class="h-20 w-20 bg-gray-50 rounded-full flex items-center justify-center mb-4 text-gray-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2-2v12a2 2 0 002 2z" />
                </svg>
            </div>
            <p class="text-gray-400 font-black uppercase tracking-[0.2em] text-sm">Jadwal Masih Kosong</p>
            <p class="text-gray-300 text-xs mt-1">Belum ada jadwal yang diinput untuk kelas Anda.</p>
        </div>
    @endforelse
</div>
@endsection