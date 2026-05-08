@extends('layouts.siswa')

@section('content')
<div class="space-y-6 animate-fade-in">
    {{-- Header & Filter Section --}}
    <div class="flex flex-col md:flex-row md:items-end justify-between border-b border-gray-100 pb-6 gap-4">
        <div>
            <h2 class="text-2xl font-black text-gray-800 tracking-tight uppercase">Riwayat Presensi</h2>
            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-1">Laporan Kehadiran Mandiri Siswa</p>
        </div>

        {{-- Form Filter --}}
        <form action="{{ route('siswa.absensi') }}" method="GET" class="flex flex-wrap items-center gap-2">
            {{-- Dropdown Bulan --}}
            <div class="flex flex-col">
                <label class="text-[9px] font-black text-gray-400 uppercase mb-1 ml-1">Pilih Bulan</label>
                <select name="bulan" class="text-xs font-bold border-gray-100 rounded-xl focus:ring-emerald-500 focus:border-emerald-500 bg-white shadow-sm py-2 min-w-[140px]">
                    <option value="">Semua Bulan</option>
                    @php
                        $namaBulan = [
                            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                        ];
                    @endphp
                    @foreach($namaBulan as $key => $bulan)
                        <option value="{{ $key }}" {{ request('bulan') == $key ? 'selected' : '' }}>
                            {{ $bulan }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Dropdown Status --}}
            <div class="flex flex-col">
                <label class="text-[9px] font-black text-gray-400 uppercase mb-1 ml-1">Status</label>
                <select name="status" class="text-xs font-bold border-gray-100 rounded-xl focus:ring-emerald-500 focus:border-emerald-500 bg-white shadow-sm py-2 min-w-[120px]">
                    <option value="">Semua Status</option>
                    <option value="Hadir" {{ request('status') == 'Hadir' ? 'selected' : '' }}>Hadir</option>
                    <option value="Sakit" {{ request('status') == 'Sakit' ? 'selected' : '' }}>Sakit</option>
                    <option value="Izin" {{ request('status') == 'Izin' ? 'selected' : '' }}>Izin</option>
                    <option value="Alpa" {{ request('status') == 'Alpa' ? 'selected' : '' }}>Alpa</option>
                </select>
            </div>

            <div class="flex items-end h-full pt-4">
                <button type="submit" class="bg-gray-900 text-white px-5 py-2.5 rounded-xl text-[10px] font-black uppercase hover:bg-emerald-600 transition-all shadow-sm">
                    <i class="fa-solid fa-magnifying-glass mr-1"></i> Cari
                </button>
            </div>

            @if(request()->filled('bulan') || request()->filled('status'))
            <div class="flex items-end h-full pt-4">
                <a href="{{ route('siswa.absensi') }}" class="bg-rose-50 text-rose-500 px-5 py-2.5 rounded-xl text-[10px] font-black uppercase border border-rose-100 hover:bg-rose-100 transition-all flex items-center">
                    Reset
                </a>
            </div>
            @endif
        </form>
    </div>

    {{-- Statistik Cards --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm border-l-4 border-l-emerald-500 transition-transform hover:scale-[1.02]">
            <p class="text-[9px] font-black text-gray-400 uppercase">Total Hadir</p>
            <p class="text-xl font-black text-emerald-600">{{ $semuaAbsensi->where('status', 'Hadir')->count() }}</p>
        </div>
        <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm border-l-4 border-l-blue-500 transition-transform hover:scale-[1.02]">
            <p class="text-[9px] font-black text-gray-400 uppercase">Total Sakit</p>
            <p class="text-xl font-black text-blue-600">{{ $semuaAbsensi->where('status', 'Sakit')->count() }}</p>
        </div>
        <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm border-l-4 border-l-amber-500 transition-transform hover:scale-[1.02]">
            <p class="text-[9px] font-black text-gray-400 uppercase">Total Izin</p>
            <p class="text-xl font-black text-amber-600">{{ $semuaAbsensi->where('status', 'Izin')->count() }}</p>
        </div>
        <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm border-l-4 border-l-rose-500 transition-transform hover:scale-[1.02]">
            <p class="text-[9px] font-black text-gray-400 uppercase">Total Alpa</p>
            <p class="text-xl font-black text-rose-600">{{ $semuaAbsensi->whereIn('status', ['Alpa', 'Alfa'])->count() }}</p>
        </div>
    </div>

    {{-- Tabel Utama (Fokus Tanpa Keterangan) --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50 border-b border-gray-100">
                        <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">No</th>
                        <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Mata Pelajaran</th>
                        <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Waktu & Tanggal</th>
                        <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Status Kehadiran</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($absensi as $index => $item)
                    <tr class="hover:bg-gray-50/50 transition-colors group">
                        <td class="px-6 py-4 text-center">
                            <span class="text-xs font-bold text-gray-300">{{ $absensi->firstItem() + $index }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-xs font-black text-gray-800 uppercase tracking-tight group-hover:text-emerald-600 transition-colors">
                                {{ $item->jadwal->mapel->nama_mapel ?? 'Mata Pelajaran' }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-col">
                                <span class="text-[11px] font-bold text-gray-600">
                                    {{ \Carbon\Carbon::parse($item->tanggal)->translatedFormat('d F Y') }}
                                </span>
                                <span class="text-[9px] text-[#ffb800] font-black uppercase flex items-center mt-0.5">
                                    <i class="fa-regular fa-clock mr-1"></i>
                                    {{ $item->created_at->format('H:i') }} WIB
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            @php
                                $color = [
                                    'Hadir' => 'bg-emerald-50 text-emerald-600 border-emerald-100',
                                    'Sakit' => 'bg-blue-50 text-blue-600 border-blue-100',
                                    'Izin' => 'bg-amber-50 text-amber-600 border-amber-100',
                                    'Alpa' => 'bg-rose-50 text-rose-600 border-rose-100',
                                    'Alfa' => 'bg-rose-50 text-rose-600 border-rose-100',
                                ][$item->status] ?? 'bg-gray-50 text-gray-600 border-gray-100';
                            @endphp
                            <span class="px-4 py-1.5 rounded-full text-[10px] font-black uppercase border {{ $color }}">
                                {{ $item->status }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="py-20 text-center">
                            <div class="flex flex-col items-center">
                                <i class="fa-solid fa-clipboard-question text-3xl text-gray-100 mb-3"></i>
                                <p class="text-gray-300 font-bold uppercase text-xs tracking-widest">Data Presensi Tidak Ditemukan</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pagination --}}
    <div class="flex justify-end pt-2">
        {{ $absensi->links() }}
    </div>
</div>

<style>
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    .animate-fade-in { animation: fadeIn 0.5s ease-out forwards; }
</style>
@endsection