@extends('layouts.guru')

@section('content')
<div class="max-w-6xl mx-auto px-2 py-4 font-academic">
    {{-- Breadcrumb Ramping --}}
    <nav class="flex mb-4 text-[9px] uppercase tracking-[0.2em] font-black" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-2">
            <li>
                <a href="{{ route('guru.jadwal') }}" class="text-gray-400 hover:text-green-700 transition-colors flex items-center gap-1.5">
                    <i class="fa-solid fa-calendar-day"></i> Jadwal
                </a>
            </li>
            <li class="text-gray-200">/</li>
            <li class="text-green-700 italic">Peserta Didik</li>
        </ol>
    </nav>

    <div class="bg-white border border-gray-200 shadow-sm rounded-2xl overflow-hidden">
        {{-- Header & Aksi Utama --}}
        <div class="px-8 py-6 bg-white border-b border-gray-100 flex flex-col md:flex-row justify-between items-center gap-6">
            <div>
                <h2 class="text-xl font-black text-gray-900 uppercase tracking-tighter italic">Data Peserta Didik</h2>
                <div class="flex items-center gap-3 mt-1">
                    <span class="px-2 py-0.5 bg-green-50 text-green-700 text-[9px] font-black rounded border border-green-100 uppercase italic">
                        {{ $jadwal->mapel->nama_mapel ?? 'Mata Pelajaran' }}
                    </span>
                    <span class="text-gray-200 font-thin">|</span>
                    <p class="text-[10px] text-gray-500 font-bold uppercase tracking-widest">
                        Kelas: <span class="text-gray-800 font-black">{{ $jadwal->kelas->nama_kelas ?? $jadwal->kelas }}</span>
                    </p>
                </div>
            </div>
            
            <div class="flex flex-wrap gap-2">
                {{-- Tombol Absensi --}}
                <a href="{{ route('guru.absensi.form', $jadwal->id) }}" 
                   class="px-4 py-2 bg-white border border-blue-100 text-blue-600 text-[10px] font-black uppercase rounded-lg hover:bg-blue-50 transition-all shadow-sm flex items-center gap-2 active:scale-95">
                    <i class="fa-solid fa-user-check text-[12px]"></i>
                    Presensi Siswa
                </a>

                {{-- TOMBOL INPUT NILAI --}}
                <a href="{{ route('guru.rekap.index', ['jadwal_id' => $jadwal->id]) }}" 
                   class="px-4 py-2 bg-gray-900 text-white text-[10px] font-black uppercase rounded-lg hover:bg-green-700 transition-all shadow-md flex items-center gap-2 active:scale-95">
                    <i class="fa-solid fa-pen-nib text-[12px]"></i>
                    Kelola Nilai
                </a>
            </div>
        </div>

        {{-- Tabel Informasi Siswa --}}
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50 border-b border-gray-100 text-gray-400 text-[9px] font-black uppercase tracking-[0.15em]">
                        <th class="px-8 py-4 w-20 text-center">#</th>
                        <th class="px-8 py-4">Profil Peserta Didik</th>
                        <th class="px-8 py-4 text-center w-48 border-l border-gray-50">Status Data Nilai</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($siswas as $index => $s)
                    <tr class="hover:bg-green-50/10 transition-colors group">
                        <td class="px-8 py-5 text-[10px] font-black text-gray-300 group-hover:text-green-600 transition-colors text-center font-mono">
                            {{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}
                        </td>
                        <td class="px-8 py-5">
                            <div class="flex flex-col sm:flex-row sm:items-center gap-1 sm:gap-4">
                                {{-- Nama Siswa --}}
                                <span class="text-[11px] font-black text-gray-700 uppercase tracking-tight group-hover:text-green-700 transition-colors italic whitespace-nowrap">
                                    {{ $s->nama }}
                                </span>
                                
                                {{-- Garis Pembatas Halus --}}
                                <span class="text-gray-100 font-thin hidden sm:block">|</span>

                                {{-- NISN --}}
                                <div class="flex items-center">
                                    <span class="text-[9px] font-bold text-gray-400 tracking-widest bg-gray-50 px-2 py-0.5 rounded border border-gray-100 uppercase group-hover:bg-white transition-colors">
                                        NISN: {{ $s->nisn ?? '----------' }}
                                    </span>
                                </div>
                            </div>
                        </td>
                        <td class="px-8 py-5 text-center border-l border-gray-50/50">
                            @php
                                $nilaiSiswa = $s->nilais->where('jadwal_id', $jadwal->id)->first();
                            @endphp

                            @if($nilaiSiswa)
                                <div class="inline-flex items-center px-3 py-1 bg-emerald-50 text-emerald-600 text-[8px] font-black uppercase rounded border border-emerald-100 tracking-widest">
                                    <span class="w-1 h-1 rounded-full bg-emerald-500 mr-2 animate-pulse"></span>
                                    Sudah Terinput
                                </div>
                            @else
                                <div class="inline-flex items-center px-3 py-1 bg-gray-50 text-gray-400 text-[8px] font-black uppercase rounded border border-gray-100 italic tracking-widest opacity-60">
                                    Belum Ada Data
                                </div>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="px-8 py-20 text-center">
                            <div class="flex flex-col items-center">
                                <div class="w-12 h-12 bg-gray-50 rounded-xl flex items-center justify-center mb-3">
                                    <i class="fa-solid fa-user-slash text-gray-200 text-xl"></i>
                                </div>
                                <p class="text-gray-400 font-black uppercase tracking-widest text-[9px]">Daftar siswa tidak ditemukan.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Footer Info --}}
        <div class="px-8 py-4 bg-gray-50/30 border-t border-gray-100 flex flex-col sm:flex-row justify-between items-center gap-2">
            <p class="text-[9px] text-gray-400 font-bold uppercase tracking-tight">
                <i class="fa-solid fa-info-circle text-amber-400 mr-1.5 text-[10px]"></i> 
                Klik <span class="text-gray-600 font-black">"Kelola Nilai"</span> untuk menginput angka & capaian kompetensi.
            </p>
            <p class="text-[8px] text-gray-300 font-black uppercase tracking-[0.2em]">
                SMAN 1 Jejangkit • Portal Akademik
            </p>
        </div>
    </div>
</div>

<style>
    .font-academic { font-family: 'Inter', sans-serif; }
</style>
@endsection