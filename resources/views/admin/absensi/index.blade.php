@extends('layouts.admin')

@section('content')
<div class="p-4 space-y-4 animate-fade-in">
    {{-- Header Section --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-3">
        <div>
            <h2 class="text-lg font-black text-gray-800 tracking-tight uppercase">Monitoring Absensi</h2>
            <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">
                Sistem Informasi Akademik <span class="text-blue-600">SMAN 1 Jejangkit</span>
            </p>
        </div>
        
        {{-- Tombol hanya muncul jika Kelas sudah dipilih DAN Mode adalah Bulanan --}}
        @if(request('kelas') && request('mode') == 'bulanan')
        <a href="{{ route('admin.absensi.cetak', request()->all()) }}" target="_blank" 
           class="bg-emerald-500 hover:bg-emerald-600 text-white font-black py-2 px-4 rounded-lg shadow-sm transition-all active:scale-95 uppercase text-[9px] tracking-widest flex items-center gap-2 w-fit">
            <i class="fa-solid fa-print"></i> Cetak Rekap Bulanan
        </a>
        @endif
    </div>

    {{-- Main Container Card --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden border-t-2 border-t-blue-600">
        
        {{-- Filter Section --}}
        <div class="p-4 border-b border-gray-50 bg-gray-50/30">
            <form method="GET" action="{{ route('admin.absensi.index') }}">
                <div class="flex flex-wrap items-end gap-3">
                    
                    {{-- Tahun Ajaran --}}
                    <div class="flex flex-col gap-1.5">
                        <label class="text-[8px] font-black text-blue-600 uppercase tracking-widest ml-1">Tahun Ajaran</label>
                        <div class="relative flex items-center">
                            <select name="tahun_ajaran" class="h-9 w-32 bg-white border border-gray-200 text-gray-700 text-[10px] rounded-lg px-3 font-bold outline-none focus:border-blue-500 cursor-pointer appearance-none leading-none">
                                @foreach($listTahun as $th)
                                    <option value="{{ $th }}" {{ request('tahun_ajaran', $setup->tahun_ajaran) == $th ? 'selected' : '' }}>{{ $th }}</option>
                                @endforeach
                            </select>
                            <i class="fa-solid fa-chevron-down absolute right-3 text-[8px] text-gray-400 pointer-events-none"></i>
                        </div>
                    </div>

                    {{-- Semester --}}
                    <div class="flex flex-col gap-1.5">
                        <label class="text-[8px] font-black text-blue-600 uppercase tracking-widest ml-1">SMT</label>
                        <div class="relative flex items-center">
                            <select name="semester" class="h-9 w-24 bg-white border border-gray-200 text-gray-700 text-[10px] rounded-lg px-3 font-bold outline-none focus:border-blue-500 cursor-pointer appearance-none leading-none">
                                <option value="1" {{ request('semester', $setup->semester) == '1' ? 'selected' : '' }}>1 (Ganjil)</option>
                                <option value="2" {{ request('semester', $setup->semester) == '2' ? 'selected' : '' }}>2 (Genap)</option>
                            </select>
                            <i class="fa-solid fa-chevron-down absolute right-3 text-[8px] text-gray-400 pointer-events-none"></i>
                        </div>
                    </div>

                    {{-- Rentang --}}
                    <div class="flex flex-col gap-1.5">
                        <label class="text-[8px] font-black text-blue-600 uppercase tracking-widest ml-1">Rentang</label>
                        <div class="relative flex items-center">
                            <select name="mode" onchange="this.form.submit()" class="h-9 bg-white border border-gray-200 text-gray-700 text-[10px] rounded-lg px-3 pr-8 font-bold outline-none focus:border-blue-500 cursor-pointer appearance-none leading-none">
                                <option value="harian" {{ request('mode') == 'harian' ? 'selected' : '' }}>Harian</option>
                                <option value="bulanan" {{ request('mode') == 'bulanan' ? 'selected' : '' }}>Bulanan</option>
                            </select>
                            <i class="fa-solid fa-chevron-down absolute right-3 text-[8px] text-gray-400 pointer-events-none"></i>
                        </div>
                    </div>

                    {{-- Tanggal/Bulan --}}
                    <div class="flex flex-col gap-1.5">
                        <label class="text-[8px] font-black text-blue-600 uppercase tracking-widest ml-1">
                            {{ request('mode') == 'bulanan' ? 'Bulan' : 'Tanggal' }}
                        </label>
                        @if(request('mode') == 'bulanan')
                            <div class="relative flex items-center">
                                <select name="filter_month" class="h-9 w-32 bg-white border border-gray-200 text-gray-700 text-[10px] rounded-lg px-3 font-bold outline-none focus:border-blue-500 cursor-pointer appearance-none leading-none">
                                    @foreach($months as $value => $name)
                                        <option value="{{ $value }}" {{ request('filter_month', date('m')) == $value ? 'selected' : '' }}>{{ $name }}</option>
                                    @endforeach
                                </select>
                                <i class="fa-solid fa-chevron-down absolute right-3 text-[8px] text-gray-400 pointer-events-none"></i>
                            </div>
                        @else
                            <input type="date" name="filter_date" value="{{ request('filter_date', date('Y-m-d')) }}" 
                                   class="h-9 bg-white border border-gray-200 text-gray-700 text-[10px] rounded-lg px-3 font-bold outline-none focus:border-blue-500 transition-all cursor-pointer flex items-center leading-none">
                        @endif
                    </div>

                    {{-- Kelas --}}
                    <div class="flex flex-col gap-1.5">
                        <label class="text-[8px] font-black text-blue-600 uppercase tracking-widest ml-1">Kelas</label>
                        <div class="relative flex items-center">
                            <select name="kelas" class="h-9 w-32 bg-white border border-gray-200 text-gray-700 text-[10px] rounded-lg px-3 font-bold outline-none focus:border-blue-500 cursor-pointer appearance-none leading-none">
                                <option value="">-- Semua --</option>
                                @foreach($listKelas as $k)
                                    <option value="{{ $k->nama_kelas }}" {{ request('kelas') == $k->nama_kelas ? 'selected' : '' }}>{{ $k->nama_kelas }}</option>
                                @endforeach
                            </select>
                            <i class="fa-solid fa-chevron-down absolute right-3 text-[8px] text-gray-400 pointer-events-none"></i>
                        </div>
                    </div>

                    <button type="submit" class="h-9 bg-blue-600 hover:bg-blue-700 text-white font-black px-5 rounded-lg shadow-sm transition-all active:scale-95 uppercase text-[9px] tracking-widest flex items-center justify-center gap-2 leading-none">
                        <i class="fa-solid fa-magnifying-glass text-[9px]"></i> Cari
                    </button>
                </div>
            </form>
        </div>

        {{-- Info Bar Mini --}}
        <div class="px-6 py-2 bg-white flex items-center justify-between border-b border-gray-50">
            <div class="flex items-center gap-2">
                <div class="w-1.5 h-1.5 bg-blue-600 rounded-full animate-pulse"></div>
                <span class="text-[9px] font-black text-slate-700 uppercase tracking-widest">
                    {{ request('tahun_ajaran', $setup->tahun_ajaran) }} (Smtr {{ request('semester', $setup->semester) }})
                </span>
            </div>
            <span class="text-[9px] font-bold text-gray-400 uppercase italic">
                {{ $absensis->count() }} Records
            </span>
        </div>

        {{-- Table Section --}}
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50">
                        <th class="px-6 py-3 text-center text-[9px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-50 w-12">No</th>
                        <th class="px-4 py-3 text-[9px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-50">Siswa</th>
                        <th class="px-4 py-3 text-center text-[9px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-50">Kelas</th>
                        <th class="px-4 py-3 text-center text-[9px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-50">Status</th>
                        <th class="px-4 py-3 text-center text-[9px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-50">Waktu</th>
                        <th class="px-6 py-3 text-right text-[9px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-50">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($absensis as $key => $a)
                    <tr class="hover:bg-blue-50/10 transition-all group">
                        <td class="px-6 py-3 text-gray-300 font-bold text-center text-[10px]">{{ $key + 1 }}</td>
                        <td class="px-4 py-3">
                            <div class="flex flex-col">
                                <span class="font-black text-slate-700 uppercase text-xs group-hover:text-blue-600 transition-colors">
                                    {{ $a->siswa->nama ?? 'Unknown' }}
                                </span>
                                <span class="text-[8px] text-gray-400 font-bold uppercase tracking-tighter">
                                    NISN: {{ $a->siswa->nisn ?? '-' }}
                                </span>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="text-blue-600 text-[9px] font-black px-2 py-1 rounded-md bg-blue-50 border border-blue-100 uppercase italic">
                                {{ $a->siswa->dataKelas->nama_kelas ?? 'N/A' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            @php
                                $statusMap = [
                                    'Hadir' => ['label' => 'HADIR', 'css' => 'bg-emerald-50 text-emerald-600 border-emerald-100'],
                                    'Sakit' => ['label' => 'SAKIT', 'css' => 'bg-amber-50 text-amber-600 border-amber-100'],
                                    'Izin'  => ['label' => 'IZIN', 'css' => 'bg-blue-50 text-blue-600 border-blue-100'],
                                    'Alpa'  => ['label' => 'ALPA', 'css' => 'bg-rose-50 text-rose-600 border-rose-100'],
                                    'H'     => ['label' => 'HADIR', 'css' => 'bg-emerald-50 text-emerald-600 border-emerald-100'],
                                    'S'     => ['label' => 'SAKIT', 'css' => 'bg-amber-50 text-amber-600 border-amber-100'],
                                    'I'     => ['label' => 'IZIN', 'css' => 'bg-blue-50 text-blue-600 border-blue-100'],
                                    'A'     => ['label' => 'ALPA', 'css' => 'bg-rose-50 text-rose-600 border-rose-100']
                                ];
                                $current = $statusMap[$a->status] ?? ['label' => $a->status, 'css' => 'bg-gray-50 text-gray-600 border-gray-100'];
                            @endphp
                            <span class="{{ $current['css'] }} border text-[8px] font-black px-3 py-1 rounded-lg inline-block min-w-[70px] text-center uppercase tracking-wider">
                                {{ $current['label'] }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <div class="flex flex-col items-center">
                                <span class="text-[10px] font-bold text-gray-700">{{ $a->created_at->format('H:i') }}</span>
                                <span class="text-[8px] text-gray-400 font-bold uppercase tracking-tighter">{{ $a->created_at->format('d/m/y') }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-3 text-right">
                            <div class="flex items-center justify-end gap-2">
                                {{-- Tombol Edit --}}
                                <a href="{{ route('admin.absensi.edit', $a->id) }}" 
                                   class="inline-flex items-center justify-center w-7 h-7 bg-slate-50 text-slate-400 rounded-lg border border-gray-100 hover:bg-blue-600 hover:text-white transition-all shadow-sm active:scale-90"
                                   title="Edit">
                                    <i class="fa-solid fa-pen-to-square text-[9px]"></i>
                                </a>

                                {{-- Tombol Hapus --}}
                                <form action="{{ route('admin.absensi.destroy', $a->id) }}" method="POST" onsubmit="return confirm('Hapus data absensi ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="inline-flex items-center justify-center w-7 h-7 bg-slate-50 text-rose-400 rounded-lg border border-gray-100 hover:bg-rose-600 hover:text-white transition-all shadow-sm active:scale-90"
                                            title="Hapus">
                                        <i class="fa-solid fa-trash-can text-[9px]"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-16 text-center text-gray-400 uppercase text-[9px] font-black tracking-widest">
                            Data Tidak Ditemukan
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Footer --}}
        <div class="bg-gray-50/30 px-6 py-3 border-t border-gray-50">
            <p class="text-[8px] font-black text-gray-400 uppercase tracking-widest text-center">
                Siakad SMAN 1 Jejangkit &copy; {{ date('Y') }}
            </p>
        </div>
    </div>
</div>

<style>
    @keyframes fadeIn { from { opacity: 0; transform: translateY(5px); } to { opacity: 1; transform: translateY(0); } }
    .animate-fade-in { animation: fadeIn 0.4s ease-out forwards; }
    
    input[type="date"]::-webkit-calendar-picker-indicator {
        cursor: pointer;
        opacity: 0.6;
    }
</style>
@endsection