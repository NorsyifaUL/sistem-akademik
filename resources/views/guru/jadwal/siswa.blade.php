@extends('layouts.guru')

@section('content')
<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
<style>
    .font-academic { font-family: 'Inter', system-ui, -apple-system, sans-serif; }
    input::-webkit-outer-spin-button,
    input::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }
</style>

<div class="font-academic max-w-7xl mx-auto px-2 pb-10">
    {{-- BREADCRUMB & HEADER RAMPING --}}
    <div class="mb-6">
        <nav class="flex items-center gap-2 text-[9px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2">
            <a href="{{ route('guru.jadwal') }}" class="hover:text-green-600 transition-colors">Jadwal</a>
            <svg xmlns="http://www.w3.org/2000/svg" class="h-2.5 w-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7" />
            </svg>
            <span class="text-gray-600 underline decoration-green-500/30 underline-offset-4">Manajemen Nilai</span>
        </nav>
        
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="border-l-4 border-green-600 pl-4">
                <h2 class="text-2xl font-black text-gray-900 tracking-tighter uppercase">Manajemen Nilai</h2>
                <p class="text-[11px] text-gray-500 mt-0.5">
                    <span class="font-bold text-gray-700 uppercase">{{ $jadwal->mapel->nama_mapel ?? $jadwal->mapel->nama }}</span> 
                    <span class="mx-1 text-gray-300">•</span> 
                    <span class="font-black text-green-700 uppercase tracking-tighter text-[10px] bg-green-50 px-2 py-0.5 rounded">Kelas {{ $jadwal->kelas }}</span>
                </p>
            </div>
            <div class="flex items-center gap-2 bg-white p-1.5 rounded-xl border border-gray-100 shadow-sm">
                <div class="px-3 border-r border-gray-100 text-right">
                    <p class="text-[8px] text-gray-400 font-black uppercase leading-none mb-1 text-right">Semester</p>
                    <p class="text-[10px] font-bold text-gray-700">
                        {{ $setting->tahun_ajaran ?? '2025/2026' }} ({{ ($setting->semester ?? 1) == 1 ? 'Ganjil' : 'Genap' }})
                    </p>
                </div>
                <span class="text-green-700 text-[9px] font-black px-3 py-1.5 uppercase tracking-tighter">
                    Input Aktif
                </span>
            </div>
        </div>
    </div>

    {{-- ALERT SUCCESS RAMPING --}}
    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" class="mb-5 p-3 bg-emerald-600 text-white rounded-xl flex justify-between items-center shadow-lg shadow-emerald-100 italic">
            <div class="flex items-center gap-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
                <p class="text-[11px] font-bold tracking-wide uppercase">{{ session('success') }}</p>
            </div>
            <button @click="show = false" class="opacity-70 hover:opacity-100"><i class="fas fa-times text-xs"></i></button>
        </div>
    @endif

    {{-- TABEL SISWA RAMPING --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-gray-50/80 border-b border-gray-200 text-gray-400 text-[9px] uppercase font-black tracking-[0.15em]">
                        <th class="px-6 py-4">Informasi Siswa</th>
                        <th class="px-6 py-4">Identitas</th>
                        <th class="px-6 py-4 text-center">Status Capaian Nilai</th>
                        <th class="px-6 py-4 text-right">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($siswa as $s)
                        @php
                            $nHarian = $s->nilais->where('jadwal_id', $jadwal->id)->where('jenis', 'harian')->first();
                            $nUts    = $s->nilais->where('jadwal_id', $jadwal->id)->where('jenis', 'uts')->first();
                            $nUas    = $s->nilais->where('jadwal_id', $jadwal->id)->where('jenis', 'uas')->first();
                        @endphp
                        <tr class="hover:bg-green-50/10 transition-all group">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-[12px] font-black text-gray-800 uppercase tracking-tight group-hover:text-green-700 transition-colors">{{ $s->nama }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-[10px] font-mono font-bold text-gray-400 bg-gray-50 px-2 py-0.5 rounded border border-gray-100">
                                    {{ $s->nisn ?? 'N/A' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex justify-center gap-1.5">
                                    {{-- Status Boxes (Dikecilkan ukurannya) --}}
                                    @foreach([
                                        ['label' => 'Harian', 'data' => $nHarian, 'color' => 'blue'],
                                        ['label' => 'UTS', 'data' => $nUts, 'color' => 'indigo'],
                                        ['label' => 'UAS', 'data' => $nUas, 'color' => 'purple']
                                    ] as $item)
                                    <div class="flex flex-col items-center justify-center min-w-[50px] py-1.5 rounded-xl border transition-all {{ $item['data'] ? "bg-{$item['color']}-50 border-{$item['color']}-100 shadow-sm" : 'bg-gray-50/50 border-gray-100 opacity-60' }}">
                                        <span class="text-[7px] font-black uppercase tracking-tighter {{ $item['data'] ? "text-{$item['color']}-400" : 'text-gray-300' }}">{{ $item['label'] }}</span>
                                        <span class="text-[11px] font-black mt-0.5 {{ $item['data'] ? "text-{$item['color']}-700" : 'text-gray-300' }}">
                                            {{ $item['data']->nilai ?? '-' }}
                                        </span>
                                    </div>
                                    @endforeach
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('guru.nilai.input', [$jadwal->id, $s->id]) }}" 
                                   class="inline-flex items-center text-[9px] font-black text-green-700 bg-green-50 hover:bg-green-700 hover:text-white px-4 py-2 rounded-lg border border-green-100 transition-all active:scale-95 uppercase tracking-widest shadow-sm">
                                    <i class="fas fa-edit mr-1.5"></i> Input Nilai
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-16 text-center">
                                <p class="text-[10px] font-black text-gray-300 uppercase tracking-widest italic">Data siswa kosong</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- FOOTER FINALISASI RAMPING --}}
    <div class="mt-8 bg-gray-900 rounded-3xl overflow-hidden shadow-xl">
        <div class="p-6 flex flex-col md:flex-row items-center justify-between gap-6">
            <div class="flex-1">
                <div class="flex items-center gap-2 mb-2">
                    <span class="w-2 h-2 rounded-full bg-green-500"></span>
                    <h4 class="text-lg font-black text-white tracking-tight uppercase">Finalisasi Rekap Nilai</h4>
                </div>
                <p class="text-gray-400 text-[11px] leading-relaxed max-w-xl italic">
                    Pastikan seluruh nilai telah terverifikasi sebelum melakukan pelaporan ke Kurikulum SMANJA.
                </p>
            </div>

            <div class="flex flex-col items-end gap-3">
                <div class="flex gap-4 mr-2">
                    <div class="text-right">
                        <p class="text-[8px] text-gray-500 font-black uppercase">Peserta</p>
                        <p class="text-xs text-white font-bold">{{ $siswa->count() }} Siswa</p>
                    </div>
                    <div class="text-right">
                        <p class="text-[8px] text-gray-500 font-black uppercase">Status</p>
                        <p class="text-xs text-amber-400 font-bold italic">Ready</p>
                    </div>
                </div>
                <a href="{{ route('guru.jadwal.legger', $jadwal->id) }}" 
                   class="inline-flex items-center justify-center bg-green-600 hover:bg-green-500 text-white px-8 py-3 rounded-xl font-black text-[10px] transition-all group active:scale-95 uppercase tracking-[0.15em] shadow-lg shadow-green-900/20">
                    <i class="fas fa-print mr-2 opacity-70"></i> Lihat Rekap Nilai
                </a>
            </div>
        </div>
    </div>
</div>
@endsection