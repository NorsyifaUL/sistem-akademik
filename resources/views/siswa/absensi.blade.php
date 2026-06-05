@extends('layouts.siswa')

@section('content')
<div class="space-y-5 animate-fade-in pb-8">
    {{-- Header & Filter Section --}}
    <div class="flex flex-col md:flex-row md:items-end justify-between border-b border-gray-100 pb-4 gap-3">
        <div>
            <h2 class="text-2xl font-black text-gray-800 tracking-tight uppercase">Riwayat Presensi</h2>
            <p class="text-[9px] text-gray-400 font-bold uppercase tracking-widest mt-0.5">Laporan Kehadiran Mandiri</p>
        </div>

        {{-- Form Filter yang telah disesuaikan agar rapi dan proporsional --}}
        <form action="{{ route('siswa.absensi') }}" method="GET" class="flex flex-wrap items-end gap-2">
            @php
                $namaBulan = [1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'];
            @endphp
            
            <div class="flex flex-col">
                <label class="text-[8px] font-black text-gray-400 uppercase mb-0.5 ml-1">Pilih Bulan</label>
                <select name="bulan" class="text-[11px] font-bold border-gray-100 rounded-xl bg-white shadow-sm py-2.5 px-3 min-w-[130px] focus:ring-emerald-500">
                    <option value="">Semua Bulan</option>
                    @foreach($namaBulan as $key => $bulan)
                        <option value="{{ $key }}" {{ request('bulan') == $key ? 'selected' : '' }}>{{ $bulan }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex flex-col">
                <label class="text-[8px] font-black text-gray-400 uppercase mb-0.5 ml-1">Status</label>
                <select name="status" class="text-[11px] font-bold border-gray-100 rounded-xl bg-white shadow-sm py-2.5 px-3 min-w-[110px] focus:ring-emerald-500">
                    <option value="">Semua</option>
                    @foreach(['Hadir', 'Sakit', 'Izin', 'Alpa'] as $st)
                        <option value="{{ $st }}" {{ request('status') == $st ? 'selected' : '' }}>{{ $st }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-center gap-2">
                <button type="submit" class="bg-gray-900 text-white px-5 py-[9px] rounded-xl text-[10px] font-black uppercase hover:bg-emerald-600 transition-all shadow-sm flex items-center">
                    <i class="fa-solid fa-magnifying-glass mr-1.5"></i> Cari
                </button>
                @if(request()->filled('bulan') || request()->filled('status'))
                    <a href="{{ route('siswa.absensi') }}" class="bg-rose-50 text-rose-500 px-4 py-[9px] rounded-xl text-[10px] font-black uppercase border border-rose-100 flex items-center">
                        <i class="fa-solid fa-xmark"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Statistik Cards --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
        @foreach([
            ['Hadir', $semuaAbsensi->where('status', 'Hadir')->count(), 'emerald-500', 'emerald-600'],
            ['Sakit', $semuaAbsensi->where('status', 'Sakit')->count(), 'blue-500', 'blue-600'],
            ['Izin', $semuaAbsensi->where('status', 'Izin')->count(), 'amber-500', 'amber-600'],
            ['Alpa', $semuaAbsensi->whereIn('status', ['Alpa', 'Alfa'])->count(), 'rose-500', 'rose-600']
        ] as [$label, $count, $border, $text])
        <div class="bg-white p-3 rounded-xl border border-gray-100 shadow-sm border-l-4 border-l-{{ $border }} transition-transform hover:scale-[1.01]">
            <p class="text-[8px] font-black text-gray-400 uppercase tracking-tighter">Total {{ $label }}</p>
            <p class="text-lg font-black text-{{ $text }}">{{ $count }}</p>
        </div>
        @endforeach
    </div>

    {{-- Tabel Utama --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50 border-b border-gray-100">
                        <th class="px-5 py-3 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center">No</th>
                        <th class="px-5 py-3 text-[9px] font-black text-gray-400 uppercase tracking-widest">Mata Pelajaran</th>
                        <th class="px-5 py-3 text-[9px] font-black text-gray-400 uppercase tracking-widest">Waktu & Tanggal</th>
                        <th class="px-5 py-3 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($absensi as $index => $item)
                    <tr class="hover:bg-gray-50/50 transition-colors group">
                        <td class="px-5 py-3 text-center">
                            <span class="text-[10px] font-bold text-gray-300">{{ $absensi->firstItem() + $index }}</span>
                        </td>
                        <td class="px-5 py-3">
                            <span class="text-[11px] font-black text-gray-800 uppercase tracking-tight group-hover:text-emerald-600 transition-colors">
                                {{ $item->jadwal->mapel->nama_mapel ?? 'Mata Pelajaran' }}
                            </span>
                        </td>
                        <td class="px-5 py-3">
                            <div class="flex flex-col leading-none">
                                <span class="text-[10px] font-bold text-gray-600">{{ \Carbon\Carbon::parse($item->tanggal)->translatedFormat('d F Y') }}</span>
                                <span class="text-[8px] text-[#ffb800] font-black uppercase mt-1">
                                    <i class="fa-regular fa-clock mr-0.5"></i> {{ $item->created_at->format('H:i') }} WIB
                                </span>
                            </div>
                        </td>
                        <td class="px-5 py-3 text-center">
                            @php
                                $statusMap = [
                                    'Hadir' => 'bg-emerald-50 text-emerald-600 border-emerald-100',
                                    'Sakit' => 'bg-blue-50 text-blue-600 border-blue-100',
                                    'Izin' => 'bg-amber-50 text-amber-600 border-amber-100',
                                    'Alpa' => 'bg-rose-50 text-rose-600 border-rose-100',
                                    'Alfa' => 'bg-rose-50 text-rose-600 border-rose-100',
                                ];
                                $colorClass = $statusMap[$item->status] ?? 'bg-gray-50 text-gray-600 border-gray-100';
                            @endphp
                            <span class="px-3 py-1 rounded-full text-[9px] font-black uppercase border {{ $colorClass }}">
                                {{ $item->status }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="py-12 text-center text-gray-300 font-bold text-[10px] uppercase">Data Tidak Ditemukan</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pagination --}}
    <div class="flex justify-end pt-1">
        {{ $absensi->links() }}
    </div>
</div>
@endsection