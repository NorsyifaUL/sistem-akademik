@extends('layouts.guru')

@section('content')
<div class="max-w-6xl mx-auto">
    {{-- Breadcrumb --}}
    <nav class="flex mb-5 text-gray-400 text-[10px] uppercase tracking-widest font-bold" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li>
                <a href="{{ route('guru.jadwal') }}" class="hover:text-green-700 flex items-center gap-1 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    Jadwal Mengajar
                </a>
            </li>
            <li><span class="mx-2 text-gray-300">/</span></li>
            <li class="text-green-700">Daftar Peserta Didik</li>
        </ol>
    </nav>

    <div class="bg-white border border-gray-200 shadow-sm rounded-2xl overflow-hidden">
        {{-- Header & Aksi Utama --}}
        <div class="px-8 py-7 bg-white border-b border-gray-100 flex flex-col md:flex-row justify-between items-center gap-4">
            <div>
                <h2 class="text-2xl font-black text-gray-900 uppercase tracking-tight italic">Data Peserta Didik</h2>
                <div class="flex items-center gap-3 mt-1.5">
                    <span class="px-3 py-1 bg-green-50 text-green-700 text-[10px] font-black rounded-full border border-green-100 uppercase italic">
                        {{ $jadwal->mapel->nama_mapel ?? 'Mata Pelajaran' }}
                    </span>
                    <span class="text-gray-300 font-light">|</span>
                    <p class="text-xs text-gray-500 font-bold uppercase tracking-widest">
                        Kelas: <span class="text-gray-800">{{ $jadwal->kelas->nama_kelas ?? $jadwal->kelas }}</span>
                    </p>
                </div>
            </div>
            
            <div class="flex flex-wrap gap-3">
                {{-- Tombol Absensi --}}
                <a href="{{ route('guru.absensi.form', $jadwal->id) }}" 
                   class="px-5 py-2.5 bg-white border border-blue-200 text-blue-600 text-[10px] font-black uppercase rounded-xl hover:bg-blue-50 transition-all shadow-sm flex items-center gap-2">
                    <i class="fa-solid fa-user-check"></i>
                    Input Absensi
                </a>

                {{-- TOMBOL INPUT NILAI --}}
                <a href="{{ route('guru.rekap.index', ['jadwal_id' => $jadwal->id]) }}" 
                   class="px-5 py-2.5 bg-green-700 text-white text-[10px] font-black uppercase rounded-xl hover:bg-green-800 transition-all shadow-lg shadow-green-100 flex items-center gap-2">
                    <i class="fa-solid fa-pen-to-square"></i>
                    Kelola & Input Nilai
                </a>
            </div>
        </div>

        {{-- Tabel Informasi Siswa --}}
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-gray-50/50 border-b border-gray-100">
                        <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] w-20 text-center">No</th>
                        <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Informasi Peserta Didik</th>
                        <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] text-center w-48">Status Akademik</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($siswas as $index => $s)
                    <tr class="hover:bg-green-50/10 transition-all group">
                        <td class="px-8 py-6 text-sm font-bold text-gray-300 group-hover:text-green-600 transition-colors text-center font-mono">
                            {{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}
                        </td>
                        <td class="px-8 py-6">
                            {{-- Layout Horizontal: Menggunakan flex-row agar Nama & NISN sejajar --}}
                            <div class="flex flex-row items-center gap-4">
                                {{-- Nama Siswa --}}
                                <span class="text-sm font-black text-gray-800 uppercase tracking-tight group-hover:text-green-700 transition-colors italic whitespace-nowrap">
                                    {{ $s->nama }}
                                </span>
                                
                                {{-- Garis Pembatas Halus --}}
                                <span class="text-gray-200 font-thin hidden sm:block">|</span>

                                {{-- Container Detail (NISN & Sekolah) --}}
                                <div class="flex items-center gap-3">
                                    <span class="text-[9px] font-bold text-gray-400 tracking-tighter bg-gray-50 px-2 py-0.5 rounded border border-gray-100 uppercase group-hover:bg-white transition-colors">
                                        NISN: {{ $s->nisn ?? '----------' }}
                                    </span>
                                    <span class="text-[9px] font-bold text-gray-400 uppercase tracking-tighter hidden md:inline opacity-60">
                                        • Siswa SMAN 1 Jejangkit
                                    </span>
                                </div>
                            </div>
                        </td>
                        <td class="px-8 py-6 text-center">
                            @php
                                $nilaiSiswa = $s->nilais->where('jadwal_id', $jadwal->id)->first();
                            @endphp

                            @if($nilaiSiswa)
                                <span class="inline-flex items-center px-3 py-1.5 bg-blue-50 text-blue-600 text-[9px] font-black uppercase rounded-lg border border-blue-100 shadow-sm">
                                    <span class="w-1.5 h-1.5 rounded-full bg-blue-500 mr-2 animate-pulse"></span>
                                    Terinput
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1.5 bg-gray-50 text-gray-400 text-[9px] font-black uppercase rounded-lg border border-gray-100 italic shadow-sm">
                                    Kosong
                                </span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="px-8 py-20 text-center">
                            <div class="flex flex-col items-center">
                                <i class="fa-solid fa-users-slash text-gray-200 text-3xl mb-3"></i>
                                <p class="text-gray-400 font-black uppercase tracking-widest text-[10px]">Belum ada peserta didik di kelas ini.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-8 py-5 bg-gray-50/50 border-t border-gray-100 flex justify-between items-center">
            <p class="text-[10px] text-gray-500 font-bold tracking-tight uppercase">
                <i class="fa-solid fa-circle-info text-yellow-400 mr-1"></i> 
                Gunakan tombol "Kelola & Input Nilai" untuk pengisian nilai kolektif.
            </p>
            <p class="text-[9px] text-gray-300 font-bold uppercase tracking-widest">
                SMAN 1 Jejangkit • Akademik
            </p>
        </div>
    </div>
</div>
@endsection