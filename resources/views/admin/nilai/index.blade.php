@extends('layouts.admin')

@section('content')
<div class="p-6 space-y-6 animate-fade-in">
    {{-- Header Section --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="space-y-1">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-blue-600 rounded-lg text-white">
                    <i class="fa-solid fa-file-invoice text-lg"></i>
                </div>
                <h2 class="text-2xl font-black text-gray-800 tracking-tight uppercase">Rekapitulasi Nilai</h2>
            </div>
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em] flex items-center gap-2">
                <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                Monitoring Hasil Belajar • SMAN 1 Jejangkit
            </p>
        </div>
        
        @if($kelasTerpilih)
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.nilai.print', ['kelas' => $kelasTerpilih]) }}" target="_blank" 
               class="group bg-emerald-500 hover:bg-emerald-600 text-white font-black py-3 px-6 rounded-2xl shadow-lg shadow-emerald-100 transition-all flex items-center gap-3 active:scale-95">
                <i class="fa-solid fa-print text-sm group-hover:rotate-12 transition-transform"></i>
                <span class="text-[11px] tracking-widest uppercase">Cetak Legger</span>
            </a>
        </div>
        @endif
    </div>

    {{-- MAIN CARD WRAPPER --}}
    <div class="bg-white rounded-[35px] border border-gray-100 shadow-xl shadow-gray-50/50 overflow-hidden relative">
        
        {{-- INI GARIS BIRU TIPIS DI ATASNYA --}}
        <div class="absolute top-0 left-0 right-0 h-1.5 bg-blue-600"></div>

        {{-- Filter Area inside Card --}}
        <div class="p-8 border-b border-gray-50 bg-gray-50/30 pt-10"> {{-- pt-10 disesuaikan agar tidak mentok garis --}}
            <form action="{{ route('admin.nilai.index') }}" method="GET" class="flex flex-col md:flex-row items-end gap-4">
                <div class="w-full md:w-1/3 space-y-2">
                    <label class="text-[9px] font-black text-gray-400 uppercase tracking-[0.2em] ml-1">Filter Berdasarkan Kelas</label>
                    <div class="relative group">
                        <div class="absolute left-5 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-blue-600 transition-colors">
                            <i class="fa-solid fa-chalkboard-user text-sm"></i>
                        </div>
                        <select name="kelas" class="w-full pl-12 pr-5 py-4 rounded-2xl border-2 border-gray-100 bg-white focus:border-blue-600 focus:ring-0 text-xs font-bold transition-all outline-none appearance-none cursor-pointer">
                            <option value="" disabled {{ !$kelasTerpilih ? 'selected' : '' }}>-- Semua Kelas --</option>
                            @foreach(['X 1', 'X 2', 'XI 1', 'XI 2', 'XII IPA', 'XII IPS'] as $kls)
                                <option value="{{ $kls }}" {{ $kelasTerpilih == $kls ? 'selected' : '' }}>Kelas {{ $kls }}</option>
                            @endforeach
                        </select>
                        <i class="fa-solid fa-chevron-down absolute right-5 top-1/2 -translate-y-1/2 text-gray-300 text-[10px] pointer-events-none group-focus-within:rotate-180 transition-transform"></i>
                    </div>
                </div>
                
                <button type="submit" class="w-full md:w-auto bg-gray-900 hover:bg-blue-600 text-white font-black py-4 px-8 rounded-2xl transition-all active:scale-95 flex items-center justify-center gap-3">
                    <i class="fa-solid fa-magnifying-glass text-xs"></i>
                    <span class="text-[11px] tracking-widest uppercase">Filter Data</span>
                </button>

                @if($kelasTerpilih)
                <a href="{{ route('admin.nilai.index') }}" class="w-full md:w-auto bg-gray-100 hover:bg-gray-200 text-gray-500 font-black py-4 px-8 rounded-2xl transition-all text-center">
                    <i class="fa-solid fa-rotate-left text-xs"></i>
                </a>
                @endif
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
                        <th class="px-6 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center border-b border-gray-50">Kelas</th>
                        <th class="px-6 py-6 text-[10px] font-black text-blue-600 uppercase tracking-widest text-center border-b border-gray-50">Rata-Rata</th>
                        <th class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right border-b border-gray-50">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($siswas as $s)
                    <tr class="group hover:bg-blue-50/30 transition-all duration-300">
                        <td class="px-8 py-6">
                            <span class="text-xs font-black text-gray-300 group-hover:text-blue-600 transition-colors">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                        </td>
                        <td class="px-6 py-6">
                            <div class="text-sm font-black text-gray-800 uppercase tracking-tight">{{ $s->nama }}</div>
                            <div class="flex items-center gap-2 mt-1">
                                <i class="fa-solid fa-fingerprint text-[10px] text-gray-300"></i>
                                <span class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">{{ $s->nisn }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-6 text-center">
                            <span class="inline-block px-4 py-1.5 bg-gray-900 text-white text-[9px] font-black rounded-lg uppercase tracking-tighter shadow-sm">
                                {{ $s->kelas }}
                            </span>
                        </td>
                        <td class="px-6 py-6 text-center">
                            <div class="text-sm font-black {{ $s->rata_rata_akhir < 75 ? 'text-rose-500' : 'text-blue-600' }}">
                                {{ number_format($s->rata_rata_akhir, 2) }}
                            </div>
                        </td>
                        <td class="px-8 py-6">
                            <div class="flex justify-end items-center gap-3">
                                {{-- Detail Button --}}
                                <a href="{{ route('admin.nilai.show', $s->id) }}" 
                                   class="w-10 h-10 flex items-center justify-center rounded-xl border-2 border-gray-50 text-orange-400 hover:bg-orange-400 hover:text-white hover:border-orange-400 transition-all duration-300"
                                   title="Lihat Detail">
                                    <i class="fa-solid fa-pen-to-square text-xs"></i>
                                </a>

                                {{-- Print Button --}}
                                <a href="{{ route('admin.nilai.raport', $s->id) }}" target="_blank"
                                   class="w-10 h-10 flex items-center justify-center rounded-xl border-2 border-gray-50 text-rose-400 hover:bg-rose-400 hover:text-white hover:border-rose-400 transition-all duration-300"
                                   title="Cetak Raport">
                                    <i class="fa-solid fa-print text-xs"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-8 py-20 text-center">
                            <div class="inline-flex p-6 bg-gray-50 rounded-[30px] mb-4">
                                <i class="fa-solid fa-folder-open text-4xl text-gray-200"></i>
                            </div>
                            <h4 class="text-sm font-black text-gray-400 uppercase tracking-widest">Tidak ada data siswa</h4>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @else
        {{-- State Saat Belum Pilih Kelas --}}
        <div class="py-32 flex flex-col items-center justify-center text-center space-y-4 pt-40"> {{-- pt disesuaikan --}}
            <div class="w-20 h-20 bg-blue-50 text-blue-600 rounded-[25px] flex items-center justify-center animate-bounce">
                <i class="fa-solid fa-mouse-pointer text-3xl"></i>
            </div>
            <div>
                <h3 class="text-lg font-black text-gray-800 uppercase tracking-tight">Silahkan Pilih Kelas</h3>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em]">Gunakan filter di atas untuk menampilkan data</p>
            </div>
        </div>
        @endif
    </div>
</div>

<style>
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    .animate-fade-in { animation: fadeIn 0.5s ease-out forwards; }
    
    /* Scrollbar Styling */
    .overflow-x-auto::-webkit-scrollbar { height: 8px; }
    .overflow-x-auto::-webkit-scrollbar-track { background: #f8fafc; }
    .overflow-x-auto::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
</style>
@endsection