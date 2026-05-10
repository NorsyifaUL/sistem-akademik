@extends('layouts.admin')

@section('content')
<div class="p-4 space-y-4 animate-fade-in">
    {{-- Header Section - Lebih Ramping --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <div class="p-2 bg-blue-600 rounded-lg text-white shadow-md shadow-blue-100">
                <i class="fa-solid fa-file-invoice text-sm"></i>
            </div>
            <div>
                <h2 class="text-lg font-black text-gray-800 tracking-tight uppercase">Rekapitulasi Nilai</h2>
                <p class="text-[8px] font-bold text-gray-400 uppercase tracking-widest flex items-center gap-1.5 mt-0.5">
                    <span class="w-1.5 h-1.5 bg-green-500 rounded-full animate-pulse"></span>
                    Aktif: {{ $setup->tahun_ajaran }} • SMT {{ $setup->semester == 1 ? 'Ganjil' : 'Genap' }}
                </p>
            </div>
        </div>
    </div>

    {{-- MAIN CARD WRAPPER --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden relative">
        <div class="absolute top-0 left-0 right-0 h-1 bg-blue-600"></div>

        {{-- Filter Area - Lebih Padat --}}
        <div class="p-5 border-b border-gray-50 bg-gray-50/30 pt-6">
            <form action="{{ route('admin.nilai.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-3 items-end">
                
                {{-- Filter Kelas --}}
                <div class="space-y-1.5">
                    <label class="text-[8px] font-black text-gray-400 uppercase tracking-widest ml-1">Pilih Kelas</label>
                    <select name="kelas" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 bg-white focus:border-blue-600 focus:ring-0 text-[11px] font-bold transition-all outline-none cursor-pointer">
                        <option value="" disabled {{ !$kelasTerpilih ? 'selected' : '' }}>-- Kelas --</option>
                        @foreach($data_kelas as $k)
                            <option value="{{ $k }}" {{ $kelasTerpilih == $k ? 'selected' : '' }}>Kelas {{ $k }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Filter Tahun Ajaran --}}
                <div class="space-y-1.5">
                    <label class="text-[8px] font-black text-gray-400 uppercase tracking-widest ml-1">Tahun Ajaran</label>
                    <select name="tahun_ajaran" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 bg-white focus:border-blue-600 focus:ring-0 text-[11px] font-bold transition-all outline-none cursor-pointer">
                        @foreach($listTahun as $th)
                            <option value="{{ $th }}" {{ $tahun_filter == $th ? 'selected' : '' }}>{{ $th }}</option>
                        @endforeach
                        @if($listTahun->isEmpty() || !in_array($setup->tahun_ajaran, $listTahun->toArray()))
                            <option value="{{ $setup->tahun_ajaran }}" {{ $tahun_filter == $setup->tahun_ajaran ? 'selected' : '' }}>{{ $setup->tahun_ajaran }}</option>
                        @endif
                    </select>
                </div>

                {{-- Filter Semester --}}
                <div class="space-y-1.5">
                    <label class="text-[8px] font-black text-gray-400 uppercase tracking-widest ml-1">Semester</label>
                    <select name="semester" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 bg-white focus:border-blue-600 focus:ring-0 text-[11px] font-bold transition-all outline-none cursor-pointer">
                        <option value="1" {{ $semester_filter == '1' ? 'selected' : '' }}>1 (Ganjil)</option>
                        <option value="2" {{ $semester_filter == '2' ? 'selected' : '' }}>2 (Genap)</option>
                    </select>
                </div>
                
                <div class="flex gap-2">
                    <button type="submit" class="flex-1 bg-gray-900 hover:bg-blue-600 text-white font-black py-2.5 rounded-xl transition-all active:scale-95 flex items-center justify-center gap-2">
                        <i class="fa-solid fa-magnifying-glass text-[10px]"></i>
                        <span class="text-[10px] tracking-widest uppercase">Filter</span>
                    </button>

                    @if($kelasTerpilih)
                    <a href="{{ route('admin.nilai.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-500 font-black py-2.5 px-4 rounded-xl transition-all flex items-center justify-center">
                        <i class="fa-solid fa-rotate-left text-[10px]"></i>
                    </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- Table Area --}}
        @if($kelasTerpilih)
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50">
                        <th class="px-6 py-3 text-[9px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-50">No</th>
                        <th class="px-4 py-3 text-[9px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-50">Informasi Siswa</th>
                        <th class="px-4 py-3 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center border-b border-gray-50">Periode</th>
                        <th class="px-4 py-3 text-[9px] font-black text-blue-600 uppercase tracking-widest text-center border-b border-gray-50">Rata-Rata</th>
                        <th class="px-6 py-3 text-[9px] font-black text-gray-400 uppercase tracking-widest text-right border-b border-gray-50">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($siswas as $s)
                    <tr class="group hover:bg-blue-50/10 transition-all duration-200">
                        <td class="px-6 py-3 text-[10px] font-bold text-gray-300">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</td>
                        <td class="px-4 py-3">
                            <div class="text-xs font-black text-gray-800 uppercase tracking-tight group-hover:text-blue-700 transition-colors">{{ $s->nama }}</div>
                            <div class="text-[9px] text-gray-400 font-bold uppercase tracking-widest">{{ $s->nisn }}</div>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="inline-block px-2.5 py-1 bg-blue-50 text-blue-600 text-[8px] font-black rounded-md uppercase tracking-tighter">
                                {{ $tahun_filter }} • SMT {{ $semester_filter }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <div class="text-xs font-black {{ $s->rata_rata_akhir < 75 ? 'text-rose-500' : 'text-blue-600' }}">
                                {{ number_format($s->rata_rata_akhir, 2) }}
                            </div>
                        </td>
                        <td class="px-6 py-3">
                            <div class="flex justify-end">
                                <a href="{{ route('admin.nilai.show', $s->id) }}?tahun={{ $tahun_filter }}&semester={{ $semester_filter }}" 
                                   class="w-8 h-8 flex items-center justify-center rounded-lg border border-gray-100 text-orange-400 hover:bg-orange-400 hover:text-white transition-all shadow-sm">
                                    <i class="fa-solid fa-pen-to-square text-[10px]"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-10 text-center text-gray-400 font-black uppercase tracking-widest text-[9px]">
                            Tidak ada data siswa ditemukan
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @else
        <div class="p-16 text-center">
            <div class="inline-flex p-4 bg-gray-50 rounded-full mb-3">
                <i class="fa-solid fa-filter text-gray-200 text-xl"></i>
            </div>
            <p class="text-gray-400 font-black text-[9px] uppercase tracking-[0.2em]">Pilih Kelas untuk menampilkan data</p>
        </div>
        @endif
    </div>
</div>

<style>
    @keyframes fadeIn { from { opacity: 0; transform: translateY(5px); } to { opacity: 1; transform: translateY(0); } }
    .animate-fade-in { animation: fadeIn 0.4s ease-out forwards; }
</style>
@endsection