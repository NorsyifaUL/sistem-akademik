@extends('layouts.guru')

@section('content')
<div class="max-w-7xl mx-auto px-2 font-academic">
    {{-- HEADER RAMPING --}}
    <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-3">
        <div>
            <h2 class="text-2xl font-black text-gray-900 tracking-tight uppercase">Jadwal Mengajar</h2>
            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest italic leading-none mt-1">Pusat kendali kelas harian — SMAN 1 Jejangkit</p>
        </div>
        <div class="flex items-center gap-2 bg-white p-1.5 rounded-xl border border-gray-100 shadow-sm">
            <div class="px-3 border-r border-gray-100">
                <p class="text-[8px] text-gray-400 font-black uppercase tracking-tighter">Tahun Ajaran</p>
                <p class="text-[10px] font-bold text-gray-700">
                    {{ $setting->tahun_ajaran ?? '-' }} ({{ ($setting->semester ?? 1) == 1 || $setting->semester == 'Ganjil' ? 'Ganjil' : 'Genap' }})
                </p>
            </div>
            <span class="bg-green-50 text-green-700 text-[9px] font-black px-3 py-1.5 rounded-lg border border-green-100 uppercase tracking-tighter">
                Guru Aktif
            </span>
        </div>
    </div>

    {{-- TABEL RAMPING --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        {{-- Header Tabel Tetap Hijau --}}
        <div class="bg-gradient-to-r from-green-700 to-green-600 px-5 py-3 flex justify-between items-center">
            <h3 class="text-white font-black text-[11px] uppercase tracking-wider flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                Agenda Tatap Muka
            </h3>
            <span class="text-[9px] text-green-100 font-bold uppercase tracking-widest bg-white/10 px-2 py-0.5 rounded">
                Total: {{ $jadwals->count() }} Mapel
            </span>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50/50">
                    <tr class="text-[9px] font-black text-gray-400 uppercase tracking-widest">
                        <th class="px-6 py-3 text-left">Hari & Waktu</th>
                        <th class="px-6 py-3 text-left">Mata Pelajaran</th>
                        <th class="px-6 py-3 text-center">Kelas & Ruang</th>
                        <th class="px-6 py-3 text-right">Opsi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($jadwals as $item)
                    <tr class="hover:bg-green-50/20 transition-colors group">
                        <td class="px-6 py-3 whitespace-nowrap">
                            <div class="flex items-center gap-3">
                                <div class="bg-green-100 p-2 rounded-lg text-green-700 group-hover:bg-green-600 group-hover:text-white transition-all border border-green-200">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-[11px] font-black text-gray-800 uppercase leading-none">{{ $item->hari }}</span>
                                    <span class="text-[9px] font-bold text-gray-400 mt-1 italic">
                                        {{ date('H:i', strtotime($item->jam_mulai)) }} - {{ date('H:i', strtotime($item->jam_selesai)) }}
                                    </span>
                                </div>
                            </div>
                        </td>

                        <td class="px-6 py-3">
                            <div class="flex flex-col">
                                <span class="text-[11px] font-black text-gray-900 uppercase tracking-tight group-hover:text-green-700 transition-colors leading-none">
                                    {{ $item->mapel->nama_mapel ?? $item->mapel->nama }}
                                </span>
                            </div>
                        </td>

                        <td class="px-6 py-3 text-center">
                            <div class="inline-flex flex-col items-center">
                                <span class="bg-indigo-50 text-indigo-700 text-[9px] font-black px-3 py-1 rounded border border-indigo-100 uppercase leading-none">
                                    KELAS {{ $item->kelas }}
                                </span>
                                <span class="text-[8px] text-gray-300 font-bold uppercase mt-1 italic tracking-tighter">{{ $item->ruangan ?? 'R. KELAS ' . $item->kelas }}</span>
                            </div>
                        </td>

                        <td class="px-6 py-3 text-right">
                            <div class="flex justify-end gap-2">
                                {{-- Tombol Absen Emerald --}}
                                <a href="{{ route('guru.absensi.index', ['jadwal_id' => $item->id]) }}" 
                                   class="inline-flex items-center px-3 py-1.5 bg-emerald-50 hover:bg-emerald-600 text-emerald-700 hover:text-white rounded-lg font-black text-[9px] uppercase tracking-tighter border border-emerald-100 transition-all shadow-sm active:scale-95">
                                    Absen
                                </a>

                                {{-- Tombol Siswa Blue --}}
                                <a href="{{ route('guru.jadwal.siswa', $item->id) }}" 
                                   class="inline-flex items-center px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-black text-[9px] uppercase tracking-tighter shadow-sm transition-all active:scale-95">
                                    Siswa & Nilai
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-16 text-center text-gray-300 italic font-black uppercase text-[10px] tracking-widest">
                            Belum Ada Jadwal Aktif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Footer Ramping --}}
        <div class="bg-gray-50/50 px-6 py-2 border-t border-gray-100 flex items-center justify-between">
            <p class="text-[9px] text-gray-400 font-bold uppercase tracking-widest flex items-center gap-1">
                <span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></span>
                DB Sync Active
            </p>
            <p class="text-[8px] text-gray-300 font-medium italic tracking-tighter uppercase">Academic v.2.0</p>
        </div>
    </div>
</div>
@endsection