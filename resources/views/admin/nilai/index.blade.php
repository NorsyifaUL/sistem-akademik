@extends('layouts.admin')

@section('content')
<div class="p-4 space-y-6 animate-fade-in">
    {{-- Header Section --}}
    <div class="px-1 flex justify-between items-end">
        <div>
            <h1 class="text-xl font-black text-slate-800 tracking-tight uppercase leading-none">
                Rekapitulasi <span class="text-blue-600">Nilai</span>
            </h1>
            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-[0.2em] mt-1.5">
                Pusat Data Akademik SMAN 1 Jejangkit
            </p>
        </div>
        <div class="flex items-center gap-2 bg-slate-50 px-3 py-1.5 rounded-lg border border-slate-100">
            <span class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></span>
            <span class="text-[9px] font-black text-slate-600 uppercase tracking-widest">
                {{ $setup->tahun_ajaran }} • SMT {{ $setup->semester == 1 ? 'GANJIL' : 'GENAP' }}
            </span>
        </div>
    </div>

    {{-- Main Card --}}
    <div class="w-full bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="h-[4px] bg-blue-600 w-full"></div>
        
        {{-- Filter Area --}}
        <div class="p-4 border-b border-slate-50 bg-slate-50/30">
            <form action="{{ route('admin.nilai.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-3 items-end">
                
                <div class="space-y-1.5">
                    <label class="block text-[9px] font-black text-slate-400 uppercase tracking-widest px-1">Pilih Kelas</label>
                    <select name="kelas_id" class="w-full px-4 py-3 bg-white border border-slate-200 rounded-lg text-[11px] font-bold uppercase outline-none focus:border-blue-500 transition-all cursor-pointer">
                        <option value="" disabled {{ !$kelasTerpilih ? 'selected' : '' }}>-- PILIH KELAS --</option>
                        @foreach($data_kelas as $k)
                            <option value="{{ $k->id }}" {{ $kelasTerpilih == $k->id ? 'selected' : '' }}>{{ strtoupper($k->nama_kelas) }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="space-y-1.5">
                    <label class="block text-[9px] font-black text-slate-400 uppercase tracking-widest px-1">Tahun Ajaran</label>
                    <select name="tahun_ajaran" class="w-full px-4 py-3 bg-white border border-slate-200 rounded-lg text-[11px] font-bold uppercase outline-none focus:border-blue-500 transition-all cursor-pointer">
                        @foreach($listTahun as $th)
                            <option value="{{ $th }}" {{ $tahun_filter == $th ? 'selected' : '' }}>{{ $th }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="space-y-1.5">
                    <label class="block text-[9px] font-black text-slate-400 uppercase tracking-widest px-1">Semester</label>
                    <select name="semester" class="w-full px-4 py-3 bg-white border border-slate-200 rounded-lg text-[11px] font-bold uppercase outline-none focus:border-blue-500 transition-all cursor-pointer">
                        <option value="1" {{ $semester_filter == '1' ? 'selected' : '' }}>1 (GANJIL)</option>
                        <option value="2" {{ $semester_filter == '2' ? 'selected' : '' }}>2 (GENAP)</option>
                    </select>
                </div>
                
                <div class="flex gap-2">
                    <button type="submit" class="flex-1 bg-slate-800 hover:bg-blue-600 text-white font-black py-3 rounded-lg transition-all active:scale-95 text-[10px] uppercase tracking-widest">
                        Filter Data
                    </button>
                    @if($kelasTerpilih)
                    <a href="{{ route('admin.nilai.index') }}" class="px-4 py-3 bg-rose-600 hover:bg-rose-700 text-white rounded-lg transition-all flex items-center justify-center">
                        <i class="fa-solid fa-rotate text-[10px]"></i>
                    </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- Table Area --}}
        @if($kelasTerpilih)
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 font-black uppercase text-[10px] tracking-widest border-b border-slate-100">
                        <th class="px-6 py-4 text-center w-16">No</th>
                        <th class="px-6 py-4">Informasi Siswa</th>
                        <th class="px-6 py-4 text-center">Periode</th>
                        <th class="px-6 py-4 text-center">Rata-Rata</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($siswas as $s)
                    <tr class="hover:bg-blue-50/20 transition-all">
                        <td class="px-6 py-4 text-center text-[11px] font-bold text-slate-400">#{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</td>
                        <td class="px-6 py-4">
                            <div class="text-[12px] font-black text-slate-700 uppercase tracking-wide">{{ $s->nama }}</div>
                            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">{{ $s->nisn }}</div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="px-3 py-1 bg-blue-50 text-blue-600 text-[9px] font-black rounded-lg uppercase tracking-wider">
                                {{ $tahun_filter }} • SMT {{ $semester_filter }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="text-[12px] font-black {{ $s->rata_rata_akhir < 75 ? 'text-rose-500' : 'text-blue-600' }}">
                                {{ number_format($s->rata_rata_akhir, 2) }}
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex justify-center">
                                <a href="{{ route('admin.nilai.show', $s->id) }}?tahun={{ $tahun_filter }}&semester={{ $semester_filter }}" 
                                   class="w-8 h-8 flex items-center justify-center bg-white text-amber-500 hover:bg-amber-500 hover:text-white rounded-lg border border-amber-100 transition-all">
                                    <i class="fa-solid fa-pen-to-square text-[10px]"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-16 text-center text-[11px] text-slate-400 uppercase font-black">Data siswa tidak ditemukan</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @else
        <div class="py-20 text-center">
            <div class="text-slate-300 mb-2"><i class="fa-solid fa-filter text-2xl"></i></div>
            <p class="text-[10px] text-slate-400 font-black uppercase tracking-widest">Silakan pilih kelas untuk memuat rekapitulasi nilai</p>
        </div>
        @endif
    </div>
</div>

<style>
    .animate-fade-in { animation: fadeIn 0.4s ease-out forwards; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(5px); } to { opacity: 1; transform: translateY(0); } }
</style>
@endsection