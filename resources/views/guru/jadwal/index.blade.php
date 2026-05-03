@extends('layouts.guru')

@section('content')
<div class="mb-8 flex flex-col md:flex-row md:items-end justify-between gap-4">
    <div>
        <h2 class="text-3xl font-extrabold text-gray-900 tracking-tight">Jadwal Mengajar</h2>
        <p class="text-gray-500 mt-1 text-sm italic">Pusat kendali kelas dan manajemen pengajaran harian Anda.</p>
    </div>
    <div class="flex items-center gap-3">
        <div class="text-right hidden sm:block">
            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-tighter">Tahun Ajaran</p>
            <p class="text-xs font-bold text-gray-700">2025/2026 ({{ ($setting->semester ?? 1) == 1 ? 'Ganjil' : 'Genap' }})</p>
        </div>
        <div class="h-8 w-[1px] bg-gray-200 mx-2 hidden sm:block"></div>
        <span class="bg-green-50 text-green-700 text-[10px] font-bold px-4 py-2 rounded-full border border-green-100 uppercase tracking-widest shadow-sm">
            Status: Guru Aktif
        </span>
    </div>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden transition-all duration-300">
    <div class="bg-gradient-to-r from-green-700 to-green-600 px-6 py-4 flex justify-between items-center">
        <h3 class="text-white font-bold text-sm uppercase tracking-wider flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            Agenda Tatap Muka
        </h3>
        <span class="text-[9px] text-green-100 font-medium uppercase tracking-widest bg-white/10 px-2 py-1 rounded">
            Total: {{ $jadwals->count() }} Mata Pelajaran
        </span>
    </div>
    
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50/50">
                <tr class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                    <th class="px-8 py-5 text-left">Hari & Waktu</th>
                    <th class="px-8 py-5 text-left">Mata Pelajaran</th>
                    <th class="px-8 py-5 text-center">Kelas & Ruang</th>
                    <th class="px-8 py-5 text-right">Opsi Manajemen</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100">
                @forelse($jadwals as $index => $item)
                <tr class="hover:bg-green-50/20 transition-colors group">
                    <td class="px-8 py-5 whitespace-nowrap">
                        <div class="flex items-center gap-3">
                            <div class="bg-green-100 p-2.5 rounded-xl text-green-700 group-hover:bg-green-600 group-hover:text-white transition-all shadow-sm border border-green-200">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="flex flex-col">
                                <span class="text-sm font-black text-gray-800 uppercase tracking-tight">{{ $item->hari }}</span>
                                <span class="text-[10px] font-bold text-gray-400">
                                    {{ date('H:i', strtotime($item->jam_mulai)) }} - {{ date('H:i', strtotime($item->jam_selesai)) }} WITA
                                </span>
                            </div>
                        </div>
                    </td>

                    <td class="px-8 py-5">
                        <div class="flex flex-col">
                            <span class="text-sm font-black text-gray-900 uppercase tracking-tight group-hover:text-green-700 transition-colors">
                                {{ $item->mapel->nama_mapel ?? $item->mapel->nama }}
                            </span>
                            <span class="text-[9px] font-bold text-gray-400 uppercase tracking-tighter">KODE: {{ $item->mapel->kode_mapel ?? '-' }}</span>
                        </div>
                    </td>

                    <td class="px-8 py-5 text-center">
                        <div class="flex flex-col items-center">
                            <span class="bg-indigo-50 text-indigo-700 text-[10px] font-black px-4 py-1.5 rounded-lg border border-indigo-100 uppercase shadow-sm">
                                KELAS {{ $item->kelas }}
                            </span>
                            <span class="text-[9px] text-gray-400 font-bold uppercase tracking-widest mt-1.5">{{ $item->ruangan ?? 'R. KELAS' }}</span>
                        </div>
                    </td>

                    <td class="px-8 py-5 text-right">
                        <div class="flex justify-end gap-3 print:hidden">
                            <a href="{{ route('guru.absensi.index') }}" 
                               class="inline-flex items-center px-4 py-2.5 bg-emerald-50 hover:bg-emerald-600 text-emerald-700 hover:text-white rounded-xl font-bold text-[10px] uppercase tracking-widest border border-emerald-100 transition-all active:scale-95 shadow-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                                Absen
                            </a>

                            <a href="{{ route('guru.jadwal.siswa', $item->id) }}" 
                               class="inline-flex items-center px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold text-[10px] uppercase tracking-widest shadow-lg shadow-blue-100 transition-all active:scale-95">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                                Siswa
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-8 py-24 text-center">
                        <div class="flex flex-col items-center">
                            <div class="bg-gray-50 p-5 rounded-full mb-4 text-gray-200 border border-gray-100">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-14 w-14" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <p class="text-[11px] font-black text-gray-400 uppercase tracking-widest">Belum Ada Jadwal Aktif</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="bg-gray-50 px-8 py-4 border-t border-gray-100 flex items-center justify-between">
        <div class="flex items-center gap-2">
            <div class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></div>
            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">
                Sinkronisasi Database Kurikulum SMANJA Aktif
            </p>
        </div>
        <p class="text-[9px] text-gray-300 font-medium italic">v.2.0 - Academic Management System</p>
    </div>
</div>
@endsection