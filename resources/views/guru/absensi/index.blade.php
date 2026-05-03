@extends('layouts.guru')

@section('content')
<div class="max-w-5xl text-left">
    {{-- Header Section --}}
    <div class="mb-8 flex flex-col md:flex-row md:justify-between md:items-end gap-4">
        <div class="text-left">
            <h2 class="text-3xl font-black text-gray-800 tracking-tight uppercase italic">Presensi Mengajar</h2>
            <p class="text-[11px] text-gray-400 mt-2 uppercase tracking-[0.2em] font-black flex items-center gap-2">
                <span class="bg-green-100 text-green-700 px-3 py-1 rounded-lg border border-green-200 shadow-sm not-italic">
                    HARI INI: {{ $hari_ini }}
                </span>
                <span class="text-gray-300">/</span>
                <span class="italic font-bold text-gray-500">{{ date('d F Y') }}</span>
            </p>
        </div>
    </div>

    {{-- Grid Jadwal --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 text-left">
        @forelse($jadwals as $j)
            @php
                // Logika Smart: Cek apakah hari di jadwal sama dengan hari ini
                // Menggunakan trim & strtolower agar perbandingan teks lebih akurat
                $is_today = (trim(strtolower($j->hari)) == trim(strtolower($hari_ini)));
            @endphp

            {{-- Card Jadwal --}}
            <div class="bg-white p-7 rounded-[2.5rem] border-2 transition-all duration-500 group relative overflow-hidden {{ $is_today ? 'border-green-500 shadow-2xl shadow-green-100 ring-8 ring-green-50/50' : 'border-gray-100 opacity-60 hover:opacity-100' }}">
                
                {{-- Label Pulse "Jadwal Hari Ini" --}}
                @if($is_today)
                <div class="absolute top-0 right-0">
                    <span class="bg-green-500 text-white text-[8px] font-black px-4 py-2 uppercase tracking-widest rounded-bl-[1.5rem] flex items-center gap-2 shadow-lg shadow-green-200 z-20">
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-white opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-white border border-green-600"></span>
                        </span>
                        Aktif Sekarang
                    </span>
                </div>
                @endif

                <div class="relative z-10 text-left">
                    {{-- Badge Hari --}}
                    <span class="text-[10px] font-black {{ $is_today ? 'text-green-700 bg-green-50 border-green-100' : 'text-gray-400 bg-gray-50 border-gray-100' }} px-3 py-1.5 rounded-xl uppercase tracking-widest border transition-colors shadow-sm">
                        {{ $j->hari }}
                    </span>
                    
                    <h3 class="text-xl font-black text-gray-800 mt-5 leading-tight uppercase tracking-tight group-hover:text-green-700 transition-colors italic">
                        {{ $j->mapel->nama_mapel ?? $j->mapel->nama }}
                    </h3>
                    
                    <div class="mt-3 flex items-center gap-2">
                        <span class="bg-gray-100 text-gray-500 text-[10px] font-black px-2 py-1 rounded-lg uppercase tracking-tighter">
                            Kelas {{ $j->kelas }}
                        </span>
                        <span class="text-gray-300">•</span>
                        {{-- Menampilkan jam tanpa detik (08:40 WIB) --}}
                        <p class="text-[11px] text-gray-400 font-black uppercase tracking-widest">
                            {{ substr($j->jam_mulai, 0, 5) }} WIB
                        </p>
                    </div>
                    
                    {{-- Tombol Presensi --}}
                    <a href="{{ route('guru.absensi.form', $j->id) }}" 
                    class="mt-8 w-full {{ $is_today ? 'bg-green-700 hover:bg-green-800 shadow-xl shadow-green-200' : 'bg-gray-900 hover:bg-black shadow-lg shadow-gray-200' }} text-white py-4 rounded-2xl text-[10px] font-black uppercase tracking-[0.2em] transition-all flex items-center justify-center gap-3 active:scale-95 group/btn">
                        {{ $is_today ? 'Mulai Absensi' : 'Lihat Data' }}
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 group-hover/btn:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </a>
                </div>

                {{-- Icon Background --}}
                <svg xmlns="http://www.w3.org/2000/svg" class="absolute -right-6 -bottom-6 h-32 w-32 {{ $is_today ? 'text-green-50/50' : 'text-gray-50' }} transition-all opacity-40 -rotate-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
            </div>
        @empty
            <div class="col-span-full py-20 text-center bg-gray-50 rounded-[3rem] border-4 border-dashed border-gray-100 flex flex-col items-center">
                <div class="bg-white p-5 rounded-full shadow-sm mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-gray-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <p class="text-xs font-black text-gray-400 uppercase tracking-widest italic">Belum ada jadwal terdaftar.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection