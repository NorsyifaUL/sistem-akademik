@extends('layouts.admin')

@section('content')
<div class="p-6 space-y-6 animate-fade-in">
    {{-- Header --}}
    <div class="mb-2">
        <h2 class="text-2xl font-black text-gray-800 tracking-tight uppercase">Monitoring Absensi</h2>
        <p class="text-xs font-bold text-gray-400 mt-1 uppercase tracking-widest">
            Rekapitulasi Kehadiran Siswa <span class="text-blue-600">SMANJA</span>
        </p>
    </div>

    {{-- Main Card --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden border-t-4 border-t-blue-600">
        
        {{-- Filter Section --}}
        <div class="p-6 border-b border-gray-50 bg-gray-50/30">
            <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-6">
                <div>
                    <h3 class="text-xs font-black text-gray-800 uppercase tracking-widest flex items-center gap-2 mb-1">
                        <i class="fa-solid fa-filter text-blue-600"></i> Parameter Filter
                    </h3>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-tight">Data ditarik secara real-time dari sistem akademik</p>
                </div>

                <form method="GET" action="{{ route('admin.absensi.index') }}" class="flex flex-wrap items-center gap-3">
                    {{-- Filter Tanggal --}}
                    <div class="flex flex-col gap-1.5">
                        <label class="text-[9px] font-black text-blue-600 uppercase tracking-[0.1em] ml-1">Pilih Tanggal</label>
                        <input type="date" name="tanggal" value="{{ request('tanggal', date('Y-m-d')) }}" 
                            class="bg-white border border-gray-200 text-gray-700 text-xs rounded-xl px-4 py-2.5 font-bold shadow-sm outline-none focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all cursor-pointer">
                    </div>

                    {{-- Filter Kelas (Dinamis dari Database) --}}
                    <div class="flex flex-col gap-1.5">
                        <label class="text-[9px] font-black text-blue-600 uppercase tracking-[0.1em] ml-1">Pilih Kelas</label>
                        <div class="relative group">
                            <select name="kelas" class="w-48 bg-white border border-gray-200 text-gray-700 text-xs rounded-xl px-4 py-2.5 font-bold shadow-sm outline-none focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 appearance-none cursor-pointer">
                                <option value="">-- Semua Kelas --</option>
                                
                                @if(isset($listKelas) && $listKelas->count() > 0)
                                    @foreach($listKelas as $k)
                                        {{-- Value menggunakan nama_kelas agar sesuai dengan logic filter di Controller --}}
                                        <option value="{{ $k->nama_kelas }}" {{ request('kelas') == $k->nama_kelas ? 'selected' : '' }}>
                                            Kelas {{ $k->nama_kelas }}
                                        </option>
                                    @endforeach
                                @else
                                    <option value="" disabled>Data Kelas Kosong</option>
                                @endif
                            </select>
                            {{-- Icon Panah --}}
                            <i class="fa-solid fa-chevron-down absolute right-3 top-3 text-gray-300 text-[9px] pointer-events-none group-focus-within:rotate-180 transition-transform"></i>
                        </div>
                    </div>

                    <div class="self-end">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-black py-2.5 px-6 rounded-xl shadow-lg shadow-blue-100 transition-all active:scale-95 uppercase text-[10px] tracking-widest flex items-center gap-2">
                            <i class="fa-solid fa-magnifying-glass"></i> Cari Data
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Info Bar & Table tetap sama seperti sebelumnya --}}
        <div class="px-8 py-3 bg-white flex items-center justify-between border-b border-gray-50">
            <div class="flex items-center gap-2">
                <div class="w-2 h-2 bg-blue-600 rounded-full animate-pulse"></div>
                <span class="text-[10px] font-black text-slate-700 uppercase tracking-widest">Live Monitoring</span>
            </div>
            <span class="text-[10px] font-bold text-gray-400 uppercase italic">
                Total Terdata: {{ $absensis->count() }} Siswa
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="text-gray-400 border-b border-gray-50 bg-gray-50/10">
                        <th class="px-8 py-4 text-center text-[10px] font-black uppercase tracking-widest w-16">#</th>
                        <th class="px-8 py-4 text-left text-[10px] font-black uppercase tracking-widest">Nama Siswa</th>
                        <th class="px-8 py-4 text-center text-[10px] font-black uppercase tracking-widest">Kelas</th>
                        <th class="px-8 py-4 text-center text-[10px] font-black uppercase tracking-widest">Status</th>
                        <th class="px-8 py-4 text-center text-[10px] font-black uppercase tracking-widest">Waktu Input</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($absensis as $key => $a)
                    <tr class="hover:bg-blue-50/20 transition-all group">
                        <td class="px-8 py-5 text-gray-300 font-bold text-center italic text-xs">{{ $key + 1 }}</td>
                        <td class="px-8 py-5">
                            <div class="flex flex-col">
                                <span class="font-black text-slate-700 uppercase text-sm">
                                    {{ $a->siswa->nama ?? 'Siswa Terhapus' }}
                                </span>
                                <span class="text-[9px] text-gray-400 font-bold italic uppercase">
                                    NISN: {{ $a->siswa->nisn ?? '-' }}
                                </span>
                            </div>
                        </td>
                        <td class="px-8 py-5 text-center">
                            <span class="bg-blue-50 text-blue-700 text-[10px] font-black px-3 py-1.5 rounded-lg border border-blue-100 uppercase">
                                {{ $a->siswa->dataKelas->nama_kelas ?? 'N/A' }}
                            </span>
                        </td>
                        <td class="px-8 py-5 text-center">
                            @php
                                $styles = [
                                    'H' => 'bg-emerald-50 text-emerald-600 border-emerald-100',
                                    'S' => 'bg-amber-50 text-amber-600 border-amber-100',
                                    'I' => 'bg-blue-50 text-blue-600 border-blue-100',
                                    'A' => 'bg-rose-50 text-rose-600 border-rose-100'
                                ];
                                $labels = ['H' => 'HADIR', 'S' => 'SAKIT', 'I' => 'IZIN', 'A' => 'ALFA'];
                            @endphp
                            <span class="{{ $styles[$a->status] ?? 'bg-gray-100' }} border text-[9px] font-black px-4 py-1.5 rounded-xl inline-block min-w-[80px] text-center">
                                {{ $labels[$a->status] ?? $a->status }}
                            </span>
                        </td>
                        <td class="px-8 py-5 text-center">
                            <div class="flex flex-col items-center">
                                <span class="text-xs font-bold text-gray-600">{{ $a->created_at->format('H:i') }}</span>
                                <span class="text-[9px] text-gray-400 font-bold">{{ $a->created_at->format('d/m/Y') }}</span>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-8 py-24 text-center">
                            <div class="flex flex-col items-center gap-2">
                                <i class="fa-solid fa-clipboard-question text-gray-200 text-4xl mb-2"></i>
                                <p class="text-[10px] font-black text-gray-300 uppercase tracking-widest">Belum ada data absensi</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection