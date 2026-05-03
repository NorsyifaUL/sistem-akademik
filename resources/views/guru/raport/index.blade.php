@extends('layouts.guru')

@section('content')
<div class="font-academic pb-12 px-4 max-w-7xl mx-auto">
    {{-- Header Section --}}
    <div class="mb-8 flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <nav class="flex mb-3 text-gray-400 text-[10px] uppercase tracking-[0.2em] font-bold" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-2">
                    <li><a href="{{ route('guru.dashboard') }}" class="hover:text-green-700 transition-colors">Dashboard</a></li>
                    <li><span class="mx-2 text-gray-300">/</span></li>
                    <li class="text-gray-800">Manajemen Raport</li>
                </ol>
            </nav>
            <h2 class="text-3xl font-extrabold text-gray-900 tracking-tight uppercase">Raport & Karakter</h2>
            <p class="text-gray-500 mt-1 text-sm italic font-medium">
                Wali Kelas: <span class="text-green-700 font-bold uppercase">{{ $infoKelas->nama_kelas ?? auth()->user()->wali_kelas }}</span>
            </p>
        </div>
        <div class="flex items-center gap-3">
            <div class="text-right hidden sm:block">
                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-tighter">Tahun Ajaran</p>
                <p class="text-xs font-bold text-gray-700">{{ $setting->tahun_ajaran ?? '2025/2026' }} - {{ ($setting->semester ?? '1') == '1' ? 'Ganjil' : 'Genap' }}</p>
            </div>
            <div class="h-8 w-[1px] bg-gray-200 mx-2 hidden sm:block"></div>
            <span class="px-4 py-2 bg-green-600 text-white text-[10px] font-black rounded-full uppercase tracking-widest shadow-md">
                <i class="fas fa-user-check mr-2"></i> {{ $siswas->count() }} Siswa
            </span>
        </div>
    </div>

    @if(session('error'))
        <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 text-xs font-bold uppercase tracking-widest rounded-r-xl shadow-sm animate-pulse">
            <i class="fas fa-exclamation-triangle mr-2"></i> {{ session('error') }}
        </div>
    @endif

    {{-- Main Card --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden transition-all duration-300">
        {{-- Card Header & Filter --}}
        <div class="border-t-4 border-green-600 bg-gray-50 px-8 py-5 flex flex-col lg:flex-row lg:items-center justify-between gap-4 border-b">
            <h3 class="text-gray-700 font-bold text-sm uppercase tracking-wider flex items-center min-w-max">
                <i class="fas fa-users text-green-600 mr-3"></i>
                Daftar Peserta Didik
            </h3>
            
            <div class="w-full md:w-80 relative group">
                <input type="text" id="customSearch" onkeyup="cariNamaSiswa()" 
                       placeholder="Cari nama siswa..." 
                       class="w-full bg-white border border-gray-300 text-gray-700 text-[11px] font-bold rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all pl-10 shadow-sm">
                <div class="absolute left-3.5 top-3 text-gray-400 group-focus-within:text-green-600 transition-colors">
                    <i class="fas fa-search text-[11px]"></i>
                </div>
            </div>
        </div>

        {{-- Table Section --}}
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200" id="table-raport">
                <thead class="bg-gray-100">
                    <tr class="text-[10px] font-bold text-gray-600 uppercase tracking-widest">
                        <th class="px-6 py-5 text-center border-r" width="5%">No</th>
                        <th class="px-8 py-5 text-left border-r">Identitas Peserta Didik</th>
                        <th class="px-6 py-5 text-center border-r">NIS / NISN</th>
                        <th class="px-6 py-5 text-center">Tindakan Wali Kelas</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($siswas as $index => $s)
                    <tr class="item-siswa hover:bg-green-50/20 transition-colors group">
                        <td class="px-6 py-5 text-center text-xs font-bold text-gray-400 border-r font-mono">{{ $index + 1 }}</td>
                        <td class="px-8 py-5 border-r">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-green-50 border border-green-100 text-green-700 rounded-xl flex items-center justify-center font-black text-xs shadow-sm mr-4 group-hover:scale-110 transition-transform">
                                    {{ strtoupper(substr($s->nama, 0, 1)) }}
                                </div>
                                <div class="flex flex-col">
                                    <span class="nama-siswa text-sm font-black text-gray-900 uppercase tracking-tight group-hover:text-green-700 transition-colors leading-none">
                                        {{ $s->nama }}
                                    </span>
                                    <span class="text-[9px] font-bold text-gray-400 mt-1.5 uppercase tracking-tighter italic">
                                        {{ $infoKelas->nama_kelas ?? 'Wali Kelas' }}
                                    </span>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-5 text-center border-r">
                            <div class="inline-flex flex-col gap-1 px-3 py-1 bg-gray-50 rounded-lg border border-gray-100 font-mono">
                                <span class="text-[10px] font-black text-gray-700 leading-none">{{ $s->nis }}</span>
                                <span class="text-[9px] font-bold text-blue-500 leading-none">{{ $s->nisn ?? '-' }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-5 text-center">
                            <div class="flex justify-center items-center gap-2">
                                <a href="{{ route('guru.nilai_sikap_input', $s->id) }}" class="bg-white text-blue-600 hover:bg-blue-600 hover:text-white px-3 py-2 rounded-lg font-black text-[9px] uppercase tracking-wider border border-blue-200 shadow-sm transition-all hover:-translate-y-0.5">
                                    <i class="fas fa-star mr-1"></i> Sikap
                                </a>
                                <a href="{{ route('guru.nilai_eskul_input', $s->id) }}" class="bg-white text-purple-600 hover:bg-purple-600 hover:text-white px-3 py-2 rounded-lg font-black text-[9px] uppercase tracking-wider border border-purple-200 shadow-sm transition-all hover:-translate-y-0.5">
                                    <i class="fas fa-running mr-1"></i> Eskul
                                </a>
                                <div class="w-px h-6 bg-gray-200 mx-1"></div>
                                <a href="{{ route('guru.raport.cetak', $s->id) }}" target="_blank" class="bg-gray-900 text-white hover:bg-green-600 px-4 py-2 rounded-lg font-black text-[9px] uppercase tracking-widest shadow-md flex items-center gap-2 transition-all active:scale-95">
                                    <i class="fas fa-print text-[10px]"></i> Cetak Raport
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="py-24 text-center">
                            <div class="flex flex-col items-center">
                                <i class="fas fa-user-slash text-gray-200 text-6xl mb-4"></i>
                                <p class="text-xs font-black text-gray-400 uppercase tracking-[0.4em] italic">Data Siswa Tidak Ditemukan</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Card Footer --}}
        <div class="bg-gray-50 px-8 py-4 border-t border-gray-200 flex items-center justify-between">
            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">
                Menampilkan {{ $siswas->count() }} Peserta Didik
            </p>
            <p class="text-[9px] text-gray-300 font-medium italic">Manajemen Raport & Karakter v.2.1</p>
        </div>
    </div>
</div>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;900&display=swap');
    .font-academic { font-family: 'Inter', sans-serif; }
    
    .item-siswa { transition: all 0.3s ease; }
    
    .overflow-x-auto::-webkit-scrollbar { height: 6px; }
    .overflow-x-auto::-webkit-scrollbar-track { background: #f1f1f1; }
    .overflow-x-auto::-webkit-scrollbar-thumb { background: #e5e7eb; border-radius: 10px; }
    .overflow-x-auto::-webkit-scrollbar-thumb:hover { background: #d1d5db; }
</style>

<script>
    function cariNamaSiswa() {
        let input = document.getElementById('customSearch').value.toUpperCase();
        let rows = document.getElementsByClassName('item-siswa');
        
        for (let i = 0; i < rows.length; i++) {
            let nama = rows[i].getElementsByClassName('nama-siswa')[0].innerText;
            if (nama.toUpperCase().indexOf(input) > -1) {
                rows[i].style.display = "";
            } else {
                rows[i].style.display = "none";
            }
        }
    }
</script>
@endsection