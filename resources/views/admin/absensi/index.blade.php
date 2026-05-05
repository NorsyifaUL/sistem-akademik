@extends('layouts.admin')

@section('content')
<div class="p-6 space-y-6 animate-fade-in">
    {{-- Header Section --}}
    <div class="mb-2 flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <h2 class="text-2xl font-black text-gray-800 tracking-tight uppercase">Monitoring Absensi</h2>
            <p class="text-xs font-bold text-gray-400 mt-1 uppercase tracking-widest">
                Sistem Informasi Akademik <span class="text-blue-600">SMAN 1 Jejangkit</span>
            </p>
        </div>
        
        {{-- Tombol Cetak PDF --}}
        @if(request('kelas'))
        <a href="{{ route('admin.absensi.cetak', request()->all()) }}" target="_blank" 
           class="bg-emerald-500 hover:bg-emerald-600 text-white font-black py-2.5 px-6 rounded-xl shadow-lg shadow-emerald-100 transition-all active:scale-95 uppercase text-[10px] tracking-widest flex items-center gap-2 w-fit">
            <i class="fa-solid fa-print"></i> Cetak PDF
        </a>
        @endif
    </div>

    {{-- Main Container Card --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden border-t-4 border-t-blue-600">
        
        {{-- Filter Section - Terkunci Satu Baris --}}
        <div class="p-6 border-b border-gray-50 bg-gray-50/30">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                {{-- Label Parameter --}}
                <div class="flex-shrink-0">
                    <h3 class="text-xs font-black text-gray-800 uppercase tracking-widest flex items-center gap-2 mb-1">
                        <i class="fa-solid fa-filter text-blue-600"></i> Parameter Filter
                    </h3>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-tight italic">Mode: {{ request('mode') == 'bulanan' ? 'Rekap Bulanan' : 'Harian' }}</p>
                </div>

                {{-- Form Filter --}}
                <div class="overflow-x-auto pb-2 lg:pb-0">
                    <form method="GET" action="{{ route('admin.absensi.index') }}" class="flex flex-nowrap items-end gap-3 min-w-max">
                        {{-- Mode Filter --}}
                        <div class="flex flex-col gap-1.5">
                            <label class="text-[9px] font-black text-blue-600 uppercase tracking-[0.1em] ml-1">Rentang Waktu</label>
                            <select name="mode" onchange="this.form.submit()" class="h-[42px] bg-white border border-gray-200 text-gray-700 text-xs rounded-xl px-4 py-2.5 font-bold shadow-sm outline-none focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 appearance-none cursor-pointer">
                                <option value="harian" {{ request('mode') == 'harian' ? 'selected' : '' }}>Harian</option>
                                <option value="bulanan" {{ request('mode') == 'bulanan' ? 'selected' : '' }}>Bulanan</option>
                            </select>
                        </div>

                        {{-- Filter Dinamis: Tanggal atau Dropdown Bulan --}}
                        <div class="flex flex-col gap-1.5">
                            <label class="text-[9px] font-black text-blue-600 uppercase tracking-[0.1em] ml-1">
                                {{ request('mode') == 'bulanan' ? 'Pilih Bulan' : 'Pilih Tanggal' }}
                            </label>
                            
                            @if(request('mode') == 'bulanan')
                                <select name="filter_month" class="h-[42px] w-48 bg-white border border-gray-200 text-gray-700 text-xs rounded-xl px-4 py-2.5 font-bold shadow-sm outline-none focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 appearance-none cursor-pointer">
                                    @php
                                        $months = [
                                            '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', 
                                            '04' => 'April', '05' => 'Mei', '06' => 'Juni', 
                                            '07' => 'Juli', '08' => 'Agustus', '09' => 'September', 
                                            '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
                                        ];
                                    @endphp
                                    @foreach($months as $value => $name)
                                        <option value="{{ $value }}" {{ request('filter_month', date('m')) == $value ? 'selected' : '' }}>
                                            {{ $name }}
                                        </option>
                                    @endforeach
                                </select>
                            @else
                                <input type="date" name="filter_date" value="{{ request('filter_date', date('Y-m-d')) }}" 
                                       class="h-[42px] bg-white border border-gray-200 text-gray-700 text-xs rounded-xl px-4 py-2.5 font-bold shadow-sm outline-none focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all cursor-pointer">
                            @endif
                        </div>

                        {{-- Filter Kelas --}}
                        <div class="flex flex-col gap-1.5">
                            <label class="text-[9px] font-black text-blue-600 uppercase tracking-[0.1em] ml-1">Pilih Kelas</label>
                            <div class="relative group">
                                <select name="kelas" class="h-[42px] w-44 bg-white border border-gray-200 text-gray-700 text-xs rounded-xl px-4 py-2.5 font-bold shadow-sm outline-none focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 appearance-none cursor-pointer">
                                    <option value="">-- Semua Kelas --</option>
                                    @foreach($listKelas as $k)
                                        <option value="{{ $k->nama_kelas }}" {{ request('kelas') == $k->nama_kelas ? 'selected' : '' }}>
                                            Kelas {{ $k->nama_kelas }}
                                        </option>
                                    @endforeach
                                </select>
                                <i class="fa-solid fa-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-gray-300 text-[9px] pointer-events-none group-focus-within:rotate-180 transition-transform"></i>
                            </div>
                        </div>

                        {{-- Tombol Submit --}}
                        <button type="submit" class="h-[42px] bg-blue-600 hover:bg-blue-700 text-white font-black px-6 rounded-xl shadow-lg shadow-blue-100 transition-all active:scale-95 uppercase text-[10px] tracking-widest flex items-center gap-2 flex-shrink-0">
                            <i class="fa-solid fa-magnifying-glass"></i> Filter
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Info Bar --}}
        <div class="px-8 py-3 bg-white flex items-center justify-between border-b border-gray-50">
            <div class="flex items-center gap-2">
                <div class="w-2 h-2 bg-blue-600 rounded-full animate-pulse"></div>
                <span class="text-[10px] font-black text-slate-700 uppercase tracking-widest">
                    @if(request('mode') == 'bulanan')
                        Rekap Bulanan: {{ $months[request('filter_month', date('m'))] }}
                    @else
                        Presensi Harian: {{ \Carbon\Carbon::parse(request('filter_date', date('Y-m-d')))->format('d F Y') }}
                    @endif
                </span>
            </div>
            <span class="text-[10px] font-bold text-gray-400 uppercase italic">
                Data: {{ $absensis->count() }} Records
            </span>
        </div>

        {{-- Table Section --}}
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="text-gray-400 border-b border-gray-50 bg-gray-50/10">
                        <th class="px-8 py-4 text-center text-[10px] font-black uppercase tracking-widest w-16">No</th>
                        <th class="px-8 py-4 text-left text-[10px] font-black uppercase tracking-widest">Mahasiswa</th>
                        <th class="px-8 py-4 text-center text-[10px] font-black uppercase tracking-widest">Kelas</th>
                        <th class="px-8 py-4 text-center text-[10px] font-black uppercase tracking-widest">Status</th>
                        <th class="px-8 py-4 text-center text-[10px] font-black uppercase tracking-widest">Timestamp</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($absensis as $key => $a)
                    <tr class="hover:bg-blue-50/20 transition-all group">
                        <td class="px-8 py-5 text-gray-300 font-bold text-center italic text-xs">{{ $key + 1 }}</td>
                        <td class="px-8 py-5">
                            <div class="flex flex-col">
                                <span class="font-black text-slate-700 uppercase text-sm group-hover:text-blue-600 transition-colors">
                                    {{ $a->siswa->nama ?? 'Unknown User' }}
                                </span>
                                <span class="text-[9px] text-gray-400 font-bold italic uppercase tracking-tighter">
                                    ID: {{ $a->siswa->nisn ?? '-' }}
                                </span>
                            </div>
                        </td>
                        <td class="px-8 py-5 text-center">
                            <span class="bg-blue-50 text-blue-700 text-[10px] font-black px-3 py-1.5 rounded-lg border border-blue-100 uppercase italic">
                                {{ $a->siswa->dataKelas->nama_kelas ?? 'N/A' }}
                            </span>
                        </td>
                        <td class="px-8 py-5 text-center">
                            @php
                                $statusMap = [
                                    'H' => ['label' => 'HADIR', 'css' => 'bg-emerald-50 text-emerald-600 border-emerald-100'],
                                    'S' => ['label' => 'SAKIT', 'css' => 'bg-amber-50 text-amber-600 border-amber-100'],
                                    'I' => ['label' => 'IZIN', 'css' => 'bg-blue-50 text-blue-600 border-blue-100'],
                                    'A' => ['label' => 'ALFA', 'css' => 'bg-rose-50 text-rose-600 border-rose-100']
                                ];
                                $current = $statusMap[$a->status] ?? ['label' => $a->status, 'css' => 'bg-gray-100'];
                            @endphp
                            <span class="{{ $current['css'] }} border text-[9px] font-black px-4 py-2 rounded-xl inline-block min-w-[90px] text-center shadow-sm">
                                {{ $current['label'] }}
                            </span>
                        </td>
                        <td class="px-8 py-5 text-center">
                            <div class="flex flex-col items-center">
                                <span class="text-xs font-bold text-gray-700">{{ $a->created_at->format('H:i') }}</span>
                                <span class="text-[9px] text-gray-400 font-bold italic uppercase tracking-widest">{{ $a->created_at->format('d M Y') }}</span>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-8 py-32 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-2">
                                    <i class="fa-solid fa-database text-gray-200 text-3xl"></i>
                                </div>
                                <p class="text-[11px] font-black text-gray-400 uppercase tracking-[0.2em]">Data Tidak Ditemukan</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Footer --}}
        <div class="bg-gray-50/50 px-8 py-4 border-t border-gray-50">
            <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest text-center">
                Siakad &bull; SMAN 1 Jejangkit &bull; {{ date('Y') }}
            </p>
        </div>
    </div>
</div>
@endsection