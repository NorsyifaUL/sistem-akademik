@extends('layouts.guru')

@section('content')
<div class="max-w-6xl mx-auto">
    {{-- Breadcrumb --}}
    <nav class="flex mb-5 text-gray-500 text-[10px] uppercase tracking-widest font-bold" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li><a href="{{ route('guru.jadwal') }}" class="hover:text-green-700">Jadwal Mengajar</a></li>
            <li><span class="mx-2 text-gray-300">/</span></li>
            <li class="text-gray-800">Daftar Siswa & Manajemen</li>
        </ol>
    </nav>

    <div class="bg-white border border-gray-200 shadow-sm rounded-lg overflow-hidden">
        {{-- Header Informasi Kelas --}}
        <div class="px-8 py-6 bg-gray-50 border-b border-gray-200 flex justify-between items-center">
            <div>
                <h2 class="text-xl font-bold text-gray-800 uppercase tracking-tight">Data Peserta Didik</h2>
                <div class="flex items-center gap-4 mt-1">
                    <p class="text-sm text-gray-600">
                        <span class="font-bold text-green-700">{{ $jadwal->mapel->nama_mapel }}</span>
                    </p>
                    <span class="text-gray-300">|</span>
                    <p class="text-sm text-gray-600 uppercase font-medium">Kelas: {{ $jadwal->kelas }}</p>
                </div>
            </div>
            <div class="flex gap-2">
                {{-- Update: Sesuai dengan rute rekap yang sudah kita perbaiki di web.php --}}
                <a href="{{ route('guru.rekap.index', ['jadwal_id' => $jadwal->id]) }}" 
                   class="px-4 py-2 bg-white border border-gray-300 text-gray-700 text-[10px] font-bold uppercase rounded hover:bg-gray-50 transition-all shadow-sm">
                   Lihat Rekap Nilai
                </a>
            </div>
        </div>

        {{-- Tabel Daftar Siswa --}}
        <div class="overflow-x-auto">
            <table class="w-full border-collapse text-left">
                <thead>
                    <tr class="bg-gray-100 border-b border-gray-200">
                        <th class="px-6 py-4 text-[10px] font-bold text-gray-600 uppercase tracking-widest w-16">No</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-gray-600 uppercase tracking-widest">Nama Lengkap Peserta Didik</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-gray-600 uppercase tracking-widest text-center">Status</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-gray-600 uppercase tracking-widest text-center">Manajemen Siswa</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($siswas as $index => $s)
                    <tr class="hover:bg-gray-50/80 transition-all">
                        <td class="px-6 py-4 text-sm font-medium text-gray-500">{{ $index + 1 }}</td>
                        <td class="px-6 py-4">
                            <div class="flex flex-col">
                                <span class="text-sm font-bold text-gray-800 uppercase leading-tight">{{ $s->nama }}</span>
                                <span class="text-[9px] font-mono text-gray-400 mt-1">NISN: {{ $s->nisn ?? '----------' }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($s->nilais->count() > 0)
                                <span class="inline-flex items-center px-2 py-1 bg-blue-50 text-blue-700 text-[9px] font-bold uppercase rounded border border-blue-100">
                                    Sudah Ada Nilai
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-1 bg-gray-50 text-gray-400 text-[9px] font-bold uppercase rounded border border-gray-100">
                                    Belum Diinput
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex justify-center gap-2">
                                
                                {{-- 1. TOMBOL ABSENSI --}}
                                {{-- Update: Mengarah ke form absensi spesifik jadwal ini --}}
                                <a href="{{ route('guru.absensi.form', $jadwal->id) }}" 
                                   class="inline-flex items-center justify-center px-4 py-2 bg-white border border-blue-600 text-blue-600 hover:bg-blue-600 hover:text-white text-[10px] font-bold uppercase tracking-wider rounded transition-all shadow-sm">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Absensi
                                </a>

                                {{-- 2. TOMBOL INPUT NILAI --}}
                                {{-- Update: Menggunakan 'nilai.input' sesuai definisi di web.php kamu --}}
                                <a href="{{ route('guru.nilai.input', [$jadwal->id, $s->id]) }}" 
                                   class="inline-flex items-center justify-center px-4 py-2 bg-green-700 hover:bg-green-800 text-white text-[10px] font-bold uppercase tracking-wider rounded transition-all shadow-sm shadow-green-900/10">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                    Input Nilai
                                </a>

                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Footer Tabel --}}
        <div class="px-8 py-4 bg-gray-50 border-t border-gray-200">
            <p class="text-[10px] text-gray-500 font-medium italic">
                * Klik tombol Absensi untuk mencatat kehadiran atau Input Nilai untuk mengisi skor akademik.
            </p>
        </div>
    </div>
</div>
@endsection