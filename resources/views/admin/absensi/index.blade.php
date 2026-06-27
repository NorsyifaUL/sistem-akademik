@extends('layouts.admin')

@section('content')
<div class="p-4 space-y-6 animate-fade-in">
    {{-- Header Section --}}
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 px-1">
        <div>
            <h1 class="text-xl font-black text-slate-800 tracking-tight uppercase leading-none">
                Monitoring <span class="text-blue-600">Absensi</span>
            </h1>
            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-[0.2em] mt-1.5">
                Sistem Informasi Akademik SMAN 1 Jejangkit
            </p>
        </div>
        
        @if(request('mode') == 'bulanan' && request('kelas'))
        <a href="{{ route('admin.absensi.cetak', request()->all()) }}" target="_blank" 
           class="bg-emerald-600 hover:bg-emerald-700 text-white font-black py-2.5 px-6 rounded-lg shadow-sm transition-all active:scale-95 uppercase text-[10px] tracking-widest flex items-center gap-2 w-fit">
            <i class="fa-solid fa-print"></i> Cetak Rekap Bulanan
        </a>
        @endif
    </div>

    {{-- Main Container Card --}}
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="h-[4px] bg-blue-600 w-full"></div>
        
        {{-- Filter Section --}}
        <div class="p-4 border-b border-slate-50 bg-slate-50/30">
            <form method="GET" action="{{ route('admin.absensi.index') }}" class="grid grid-cols-2 md:grid-cols-6 gap-3 items-end">
                <div class="space-y-1.5">
                    <label class="block text-[9px] font-black text-slate-400 uppercase tracking-widest px-1">Tahun Ajaran</label>
                    <select name="tahun_ajaran" class="w-full h-10 bg-white border border-slate-200 rounded-lg px-3 text-[11px] font-bold uppercase outline-none focus:border-blue-500 cursor-pointer">
                        @foreach($listTahun as $th)
                            <option value="{{ $th }}" {{ request('tahun_ajaran', $setup->tahun_ajaran) == $th ? 'selected' : '' }}>{{ $th }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="space-y-1.5">
                    <label class="block text-[9px] font-black text-slate-400 uppercase tracking-widest px-1">Semester</label>
                    <select name="semester" class="w-full h-10 bg-white border border-slate-200 rounded-lg px-3 text-[11px] font-bold uppercase outline-none focus:border-blue-500 cursor-pointer">
                        <option value="1" {{ request('semester', $setup->semester) == '1' ? 'selected' : '' }}>1 (GANJIL)</option>
                        <option value="2" {{ request('semester', $setup->semester) == '2' ? 'selected' : '' }}>2 (GENAP)</option>
                    </select>
                </div>

                <div class="space-y-1.5">
                    <label class="block text-[9px] font-black text-slate-400 uppercase tracking-widest px-1">Rentang</label>
                    <select name="mode" onchange="this.form.submit()" class="w-full h-10 bg-white border border-slate-200 rounded-lg px-3 text-[11px] font-bold uppercase outline-none focus:border-blue-500 cursor-pointer">
                        <option value="harian" {{ request('mode') == 'harian' ? 'selected' : '' }}>HARIAN</option>
                        <option value="bulanan" {{ request('mode') == 'bulanan' ? 'selected' : '' }}>BULANAN</option>
                    </select>
                </div>

                <div class="space-y-1.5">
                    <label class="block text-[9px] font-black text-slate-400 uppercase tracking-widest px-1">
                        {{ request('mode') == 'bulanan' ? 'Bulan' : 'Tanggal' }}
                    </label>
                    @if(request('mode') == 'bulanan')
                        <select name="filter_month" class="w-full h-10 bg-white border border-slate-200 rounded-lg px-3 text-[11px] font-bold uppercase outline-none focus:border-blue-500 cursor-pointer">
                            @foreach($months as $value => $name)
                                <option value="{{ $value }}" {{ request('filter_month', date('m')) == $value ? 'selected' : '' }}>{{ strtoupper($name) }}</option>
                            @endforeach
                        </select>
                    @else
                        <input type="date" name="filter_date" value="{{ request('filter_date', date('Y-m-d')) }}" 
                               class="w-full h-10 bg-white border border-slate-200 rounded-lg px-3 text-[11px] font-bold uppercase outline-none focus:border-blue-500 cursor-pointer">
                    @endif
                </div>

                <div class="space-y-1.5">
                    <label class="block text-[9px] font-black text-slate-400 uppercase tracking-widest px-1">Kelas</label>
                    <select name="kelas" class="w-full h-10 bg-white border border-slate-200 rounded-lg px-3 text-[11px] font-bold uppercase outline-none focus:border-blue-500 cursor-pointer">
                        <option value="">SEMUA KELAS</option>
                        @foreach($listKelas as $k)
                            <option value="{{ $k->nama_kelas }}" {{ request('kelas') == $k->nama_kelas ? 'selected' : '' }}>{{ $k->nama_kelas }}</option>
                        @endforeach
                    </select>
                </div>

                <button type="submit" class="h-10 bg-slate-800 hover:bg-blue-600 text-white font-black rounded-lg shadow-sm transition-all active:scale-95 uppercase text-[10px] tracking-widest flex items-center justify-center gap-2">
                    <i class="fa-solid fa-magnifying-glass"></i> FILTER
                </button>
            </form>
        </div>

        {{-- Table Section --}}
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 font-black uppercase text-[10px] tracking-widest border-b border-slate-100">
                        <th class="px-6 py-4 text-center">No</th>
                        <th class="px-4 py-4">Siswa</th>
                        <th class="px-4 py-4 text-center">Kelas</th>
                        <th class="px-4 py-4 text-center">Mapel</th>
                        <th class="px-4 py-4 text-center">Status</th>
                        <th class="px-4 py-4 text-center">Waktu</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($absensis as $a)
                    <tr class="hover:bg-blue-50/20 transition-all">
                        <td class="px-6 py-4 text-center text-[11px] font-bold text-slate-400">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</td>
                        <td class="px-4 py-4">
                            <div class="text-[12px] font-black text-slate-700 uppercase">{{ $a->siswa->nama ?? '-' }}</div>
                            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">NISN: {{ $a->siswa->nisn ?? '-' }}</div>
                        </td>
                        <td class="px-4 py-4 text-center">
                            <span class="text-[10px] font-black text-blue-600 bg-blue-50 px-2.5 py-1 rounded-lg uppercase">{{ $a->siswa->dataKelas->nama_kelas ?? 'N/A' }}</span>
                        </td>
                        <td class="px-4 py-4 text-center text-[11px] font-bold text-slate-600">{{ $a->jadwal->mapel->nama_mapel ?? '-' }}</td>
                        <td class="px-4 py-4 text-center">
                            @php
                                $status = [
                                    'Hadir' => 'bg-emerald-50 text-emerald-600',
                                    'Sakit' => 'bg-amber-50 text-amber-600',
                                    'Izin'  => 'bg-blue-50 text-blue-600',
                                    'Alpa'  => 'bg-rose-50 text-rose-600'
                                ];
                            @endphp
                            <span class="px-3 py-1 text-[9px] font-black rounded-lg uppercase {{ $status[$a->status] ?? 'bg-slate-100' }}">
                                {{ $a->status }}
                            </span>
                        </td>
                        <td class="px-4 py-4 text-center">
                            <div class="text-[11px] font-bold text-slate-700">{{ $a->created_at->format('H:i') }}</div>
                            <div class="text-[9px] text-slate-400 font-bold uppercase">{{ $a->created_at->format('d/m/y') }}</div>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('admin.absensi.edit', $a->id) }}" class="w-8 h-8 flex items-center justify-center bg-slate-50 text-slate-400 hover:bg-blue-600 hover:text-white rounded-lg transition-all">
                                    <i class="fa-solid fa-pen-to-square text-[10px]"></i>
                                </a>
                                <form action="{{ route('admin.absensi.destroy', $a->id) }}" method="POST" onsubmit="return confirm('Hapus data ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="w-8 h-8 flex items-center justify-center bg-slate-50 text-rose-400 hover:bg-rose-600 hover:text-white rounded-lg transition-all">
                                        <i class="fa-solid fa-trash-can text-[10px]"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-16 text-center text-[11px] text-slate-400 uppercase font-black">Data tidak ditemukan</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
    .animate-fade-in { animation: fadeIn 0.4s ease-out forwards; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(5px); } to { opacity: 1; transform: translateY(0); } }
</style>
@endsection