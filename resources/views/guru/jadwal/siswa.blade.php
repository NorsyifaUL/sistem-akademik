@extends('layouts.guru')

@section('content')
<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
<style>
    .font-academic { font-family: 'Inter', system-ui, -apple-system, sans-serif; }
    /* Menghilangkan spin button pada input number */
    input::-webkit-outer-spin-button,
    input::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }
</style>

<div class="font-academic pb-12">
    <div class="mb-8">
        <nav class="flex items-center gap-2 text-[11px] font-semibold text-gray-400 uppercase tracking-widest mb-3">
            <a href="{{ route('guru.jadwal') }}" class="hover:text-green-600 transition-colors">Jadwal Mengajar</a>
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
            <span class="text-gray-600">Daftar Siswa & Penilaian</span>
        </nav>
        
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
            <div>
                <h2 class="text-3xl font-bold text-gray-900 tracking-tight">Manajemen Nilai Siswa</h2>
                <p class="text-gray-500 mt-1 italic text-sm">
                    {{ $jadwal->mapel->nama_mapel ?? $jadwal->mapel->nama }} 
                    <span class="mx-2 text-gray-300">|</span> 
                    <span class="font-semibold text-green-700 not-italic uppercase tracking-wider">Kelas {{ $jadwal->kelas }}</span>
                </p>
            </div>
            <div class="flex items-center gap-3">
                <div class="text-right hidden sm:block">
                    <p class="text-[10px] text-gray-400 font-bold uppercase tracking-tighter">Tahun Ajaran</p>
                    <p class="text-xs font-bold text-gray-700">
                        {{ $setting->tahun_ajaran ?? '2025/2026' }} 
                        {{ ($setting->semester ?? 1) == 1 ? 'Ganjil' : 'Genap' }}
                    </p>
                </div>
                <div class="h-8 w-[1px] bg-gray-200 mx-2 hidden sm:block"></div>
                <span class="bg-green-50 text-green-700 text-[10px] font-bold px-4 py-2 rounded-full border border-green-100 uppercase tracking-widest">
                    Periode Input Aktif
                </span>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl flex justify-between items-center animate-fade-in-down">
            <div class="flex items-center gap-3">
                <div class="bg-emerald-500 p-1 rounded-full text-white">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                </div>
                <p class="text-sm font-semibold tracking-tight">{{ session('success') }}</p>
            </div>
            <button @click="show = false" class="text-emerald-400 hover:text-emerald-600 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    @endif

    <div class="bg-white rounded-[1.5rem] shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-gray-50/50 border-b border-gray-200 text-gray-500 text-[11px] uppercase font-bold tracking-[0.1em]">
                        <th class="px-8 py-5">Nama Lengkap Siswa</th>
                        <th class="px-8 py-5">Identitas (NISN)</th>
                        <th class="px-8 py-5 text-center">Status Capaian Nilai</th>
                        <th class="px-8 py-5 text-right">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($siswa as $s)
                        @php
                            $nHarian = $s->nilais->where('jadwal_id', $jadwal->id)->where('jenis', 'harian')->first();
                            $nUts    = $s->nilais->where('jadwal_id', $jadwal->id)->where('jenis', 'uts')->first();
                            $nUas    = $s->nilais->where('jadwal_id', $jadwal->id)->where('jenis', 'uas')->first();
                        @endphp
                        <tr class="hover:bg-gray-50/50 transition-all duration-200">
                            <td class="px-8 py-5">
                                <span class="text-sm font-bold text-gray-800 uppercase tracking-tight">{{ $s->nama }}</span>
                            </td>
                            <td class="px-8 py-5">
                                <span class="text-[11px] font-mono font-bold text-gray-400 bg-gray-50 px-3 py-1 rounded-md border border-gray-100">
                                    {{ $s->nisn ?? 'N/A' }}
                                </span>
                            </td>
                            <td class="px-8 py-5">
                                <div class="flex justify-center gap-2">
                                    <div class="flex flex-col items-center justify-center min-w-[60px] py-2 rounded-2xl border transition-all {{ $nHarian ? 'bg-blue-50 border-blue-100 shadow-sm' : 'bg-gray-50 border-gray-100' }}">
                                        <span class="text-[8px] font-black uppercase tracking-widest {{ $nHarian ? 'text-blue-400' : 'text-gray-300' }}">Harian</span>
                                        <span class="text-sm font-black mt-0.5 {{ $nHarian ? 'text-blue-700' : 'text-gray-300' }}">
                                            {{ $nHarian->nilai ?? '-' }}
                                        </span>
                                    </div>

                                    <div class="flex flex-col items-center justify-center min-w-[60px] py-2 rounded-2xl border transition-all {{ $nUts ? 'bg-indigo-50 border-indigo-100 shadow-sm' : 'bg-gray-50 border-gray-100' }}">
                                        <span class="text-[8px] font-black uppercase tracking-widest {{ $nUts ? 'text-indigo-400' : 'text-gray-300' }}">UTS</span>
                                        <span class="text-sm font-black mt-0.5 {{ $nUts ? 'text-indigo-700' : 'text-gray-300' }}">
                                            {{ $nUts->nilai ?? '-' }}
                                        </span>
                                    </div>

                                    <div class="flex flex-col items-center justify-center min-w-[60px] py-2 rounded-2xl border transition-all {{ $nUas ? 'bg-purple-50 border-purple-100 shadow-sm' : 'bg-gray-50 border-gray-100' }}">
                                        <span class="text-[8px] font-black uppercase tracking-widest {{ $nUas ? 'text-purple-400' : 'text-gray-300' }}">UAS</span>
                                        <span class="text-sm font-black mt-0.5 {{ $nUas ? 'text-purple-700' : 'text-gray-300' }}">
                                            {{ $nUas->nilai ?? '-' }}
                                        </span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-5 text-right">
                                <a href="{{ route('guru.nilai.input', [$jadwal->id, $s->id]) }}" 
                                   class="inline-flex items-center text-[11px] font-bold text-green-700 hover:text-white bg-green-50 hover:bg-green-700 px-5 py-2.5 rounded-xl border border-green-100 transition-all active:scale-95 uppercase tracking-widest">
                                    Input Nilai
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-8 py-20 text-center">
                                <div class="inline-flex items-center justify-center w-16 h-16 bg-gray-50 rounded-2xl mb-4 border border-dashed border-gray-200">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                                </div>
                                <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest italic">Data siswa belum tersedia untuk kelas ini.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-12 bg-white border border-gray-200 rounded-[2rem] overflow-hidden shadow-sm hover:shadow-md transition-shadow">
        <div class="h-1.5 bg-green-700"></div>
        <div class="p-8 flex flex-col md:flex-row items-center justify-between gap-8">
            <div class="flex-1">
                <div class="flex items-center gap-3 mb-3">
                    <span class="bg-green-100 text-green-700 text-[9px] font-black px-2.5 py-1 rounded uppercase tracking-[0.2em]">Langkah Terakhir</span>
                    <h4 class="text-xl font-bold text-gray-900 tracking-tight">Finalisasi Rekapitulasi Nilai</h4>
                </div>
                <p class="text-gray-500 text-sm leading-relaxed max-w-2xl">
                    Setelah seluruh nilai siswa terinput, sistem akan merangkum perolehan skor ke dalam lembar rekapitulasi kelas. Pastikan data sudah valid sebelum dilakukan pelaporan ke bagian Kurikulum.
                </p>
                
                <div class="flex gap-6 mt-6">
                    <div class="flex items-center gap-2">
                        <div class="w-2 h-2 rounded-full bg-green-500"></div>
                        <span class="text-[10px] text-gray-400 font-bold uppercase tracking-wider">Total Peserta: <span class="text-gray-900">{{ $siswa->count() }} Siswa</span></span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-2 h-2 rounded-full bg-amber-400 animate-pulse"></div>
                        <span class="text-[10px] text-gray-400 font-bold uppercase tracking-wider">Status: <span class="text-gray-900 italic">Siap Verifikasi</span></span>
                    </div>
                </div>
            </div>

            <div class="flex shrink-0">
                <a href="{{ route('guru.jadwal.legger', $jadwal->id) }}" 
                   class="inline-flex items-center justify-center bg-green-700 hover:bg-green-800 text-white px-10 py-4 rounded-2xl font-bold text-xs transition-all shadow-xl shadow-green-100 group active:scale-95 uppercase tracking-widest">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2.5 opacity-70 group-hover:rotate-12 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2a4 4 0 00-4-4H5m11 4h2a2 2 0 002-2v-5a2 2 0 00-2-2H5a2 2 0 00-2 2v5a2 2 0 002 2h2" />
                    </svg>
                    Lihat Rekap Nilai
                </a>
            </div>
        </div>
        
        <div class="bg-gray-50/80 px-8 py-3.5 border-t border-gray-100">
            <p class="text-[10px] text-gray-400 italic">
                * Data yang tersaji secara otomatis terintegrasi dengan database akademik server SMANJA.
            </p>
        </div>
    </div>
</div>
@endsection