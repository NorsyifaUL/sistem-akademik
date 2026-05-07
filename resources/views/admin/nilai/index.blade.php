@extends('layouts.admin')

@section('content')
<div class="p-6 space-y-6 animate-fade-in">
    {{-- Header Section --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="space-y-1">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-blue-600 rounded-lg text-white shadow-lg shadow-blue-200">
                    <i class="fa-solid fa-file-invoice text-lg"></i>
                </div>
                <h2 class="text-2xl font-black text-gray-800 tracking-tight uppercase">Rekapitulasi Nilai</h2>
            </div>
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em] flex items-center gap-2">
                <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                Periode Aktif: {{ $setup->tahun_ajaran }} - Semester {{ $setup->semester == 1 ? 'Ganjil' : 'Genap' }}
            </p>
        </div>
    </div>

    {{-- MAIN CARD WRAPPER --}}
    <div class="bg-white rounded-[35px] border border-gray-100 shadow-xl shadow-gray-50/50 overflow-hidden relative">
        <div class="absolute top-0 left-0 right-0 h-1.5 bg-blue-600"></div>

        {{-- Filter Area --}}
        <div class="p-8 border-b border-gray-50 bg-gray-50/30 pt-10">
            <form action="{{ route('admin.nilai.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                
                {{-- Filter Kelas --}}
                <div class="space-y-2">
                    <label class="text-[9px] font-black text-gray-400 uppercase tracking-[0.2em] ml-1">Pilih Kelas</label>
                    <select name="kelas" class="w-full px-5 py-4 rounded-2xl border-2 border-gray-100 bg-white focus:border-blue-600 focus:ring-0 text-xs font-bold transition-all outline-none appearance-none cursor-pointer">
                        <option value="" disabled {{ !$kelasTerpilih ? 'selected' : '' }}>-- Kelas --</option>
                        @foreach($data_kelas as $k)
                            <option value="{{ $k }}" {{ $kelasTerpilih == $k ? 'selected' : '' }}>Kelas {{ $k }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Filter Tahun Ajaran --}}
                <div class="space-y-2">
                    <label class="text-[9px] font-black text-gray-400 uppercase tracking-[0.2em] ml-1">Tahun Ajaran</label>
                    <select name="tahun_ajaran" class="w-full px-5 py-4 rounded-2xl border-2 border-gray-100 bg-white focus:border-blue-600 focus:ring-0 text-xs font-bold transition-all outline-none appearance-none cursor-pointer">
                        @foreach($listTahun as $th)
                            <option value="{{ $th }}" {{ $tahun_filter == $th ? 'selected' : '' }}>{{ $th }}</option>
                        @endforeach
                        {{-- Opsi Default Jika belum ada data di DB --}}
                        @if($listTahun->isEmpty() || !in_array($setup->tahun_ajaran, $listTahun->toArray()))
                            <option value="{{ $setup->tahun_ajaran }}" {{ $tahun_filter == $setup->tahun_ajaran ? 'selected' : '' }}>{{ $setup->tahun_ajaran }} (Aktif)</option>
                        @endif
                    </select>
                </div>

                {{-- Filter Semester --}}
                <div class="space-y-2">
                    <label class="text-[9px] font-black text-gray-400 uppercase tracking-[0.2em] ml-1">Semester</label>
                    <select name="semester" class="w-full px-5 py-4 rounded-2xl border-2 border-gray-100 bg-white focus:border-blue-600 focus:ring-0 text-xs font-bold transition-all outline-none appearance-none cursor-pointer">
                        <option value="1" {{ $semester_filter == '1' ? 'selected' : '' }}>1 (Ganjil)</option>
                        <option value="2" {{ $semester_filter == '2' ? 'selected' : '' }}>2 (Genap)</option>
                    </select>
                </div>
                
                <div class="flex gap-2">
                    <button type="submit" class="flex-1 bg-gray-900 hover:bg-blue-600 text-white font-black py-4 rounded-2xl transition-all active:scale-95 flex items-center justify-center gap-3">
                        <i class="fa-solid fa-magnifying-glass text-xs"></i>
                        <span class="text-[11px] tracking-widest uppercase">Filter</span>
                    </button>

                    @if($kelasTerpilih)
                    <a href="{{ route('admin.nilai.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-500 font-black py-4 px-6 rounded-2xl transition-all flex items-center justify-center">
                        <i class="fa-solid fa-rotate-left text-xs"></i>
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
                    <tr>
                        <th class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-50">No</th>
                        <th class="px-6 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-50">Informasi Siswa</th>
                        <th class="px-6 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center border-b border-gray-50">Periode Data</th>
                        <th class="px-6 py-6 text-[10px] font-black text-blue-600 uppercase tracking-widest text-center border-b border-gray-50">Rata-Rata</th>
                        <th class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right border-b border-gray-50">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($siswas as $s)
                    <tr class="group hover:bg-blue-50/30 transition-all duration-300">
                        <td class="px-8 py-6 text-xs font-black text-gray-300">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</td>
                        <td class="px-6 py-6">
                            <div class="text-sm font-black text-gray-800 uppercase tracking-tight">{{ $s->nama }}</div>
                            <div class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">{{ $s->nisn }}</div>
                        </td>
                        <td class="px-6 py-6 text-center">
                            <span class="inline-block px-4 py-1.5 bg-blue-50 text-blue-600 text-[9px] font-black rounded-lg uppercase tracking-tighter">
                                {{ $tahun_filter }} • SMT {{ $semester_filter }}
                            </span>
                        </td>
                        <td class="px-6 py-6 text-center">
                            <div class="text-sm font-black {{ $s->rata_rata_akhir < 75 ? 'text-rose-500' : 'text-blue-600' }}">
                                {{ number_format($s->rata_rata_akhir, 2) }}
                            </div>
                        </td>
                        <td class="px-8 py-6">
                            <div class="flex justify-end gap-3">
                                <a href="{{ route('admin.nilai.show', $s->id) }}?tahun={{ $tahun_filter }}&semester={{ $semester_filter }}" 
                                   class="w-10 h-10 flex items-center justify-center rounded-xl border-2 border-gray-50 text-orange-400 hover:bg-orange-400 hover:text-white transition-all">
                                    <i class="fa-solid fa-pen-to-square text-xs"></i>
                                </a>
                                <a href="{{ route('admin.nilai.raport', $s->id) }}?tahun={{ $tahun_filter }}&semester={{ $semester_filter }}" target="_blank"
                                   class="w-10 h-10 flex items-center justify-center rounded-xl border-2 border-gray-50 text-rose-400 hover:bg-rose-400 hover:text-white transition-all">
                                    <i class="fa-solid fa-print text-xs"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    {{-- State Empty --}}
                    @endforelse
                </tbody>
            </table>
        </div>
        @else
        {{-- State Pilih Kelas --}}
        @endif
    </div>
</div>
@endsection