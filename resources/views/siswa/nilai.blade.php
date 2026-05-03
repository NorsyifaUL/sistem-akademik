@extends('layouts.siswa')

@section('content')
<div class="max-w-6xl mx-auto space-y-8 animate-fade-in">
    {{-- Header Dokumen Akademik --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-end border-b-4 border-[#064e3b] pb-6">
        <div>
            <div class="flex items-center gap-3 mb-3">
                <div class="h-10 w-10 bg-[#064e3b] rounded-lg flex items-center justify-center shadow-lg">
                    <i class="fa-solid fa-file-invoice text-[#ffb800] text-xl"></i>
                </div>
                <h1 class="text-3xl font-black text-gray-900 uppercase tracking-tighter">Laporan Hasil Belajar</h1>
            </div>
            <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-1">
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Nama : <span class="text-gray-900 ml-2">{{ auth()->user()->name }}</span></p>
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">NISN : <span class="text-gray-900 ml-2">{{ $siswa->nisn ?? '-' }}</span></p>
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Kelas Aktif : <span class="text-gray-900 ml-2">{{ $siswa->kelas ?? '-' }}</span></p>
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Status : <span class="text-[#064e3b] ml-2 italic">Siswa Aktif</span></p>
            </div>
        </div>
        <div class="mt-6 md:mt-0 text-right bg-gray-50 p-4 rounded-2xl border border-gray-100">
            <p class="text-[10px] font-black text-[#ffb800] uppercase tracking-[0.2em]">Semester {{ $semester_filter ?? $setting->semester }} ({{ ($semester_filter ?? $setting->semester) == 1 ? 'Ganjil' : 'Genap' }})</p>
            <p class="text-sm font-black text-[#064e3b] uppercase">Tahun Ajaran {{ $tahun_filter ?? $setting->tahun_ajaran }}</p>
        </div>
    </div>

    {{-- Form Filter Pencarian --}}
    <div class="bg-white p-5 rounded-3xl border border-gray-100 shadow-sm ring-1 ring-black/5">
        <form action="{{ route('siswa.nilai') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-5 items-end">
            <div>
                <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block ml-1">Tahun Ajaran</label>
                <select name="tahun_ajaran" class="w-full bg-gray-50 border-gray-100 rounded-xl text-xs font-bold focus:ring-2 focus:ring-[#064e3b] transition-all">
                    @foreach($listTahun as $th)
                        <option value="{{ $th }}" {{ ($tahun_filter ?? $setting->tahun_ajaran) == $th ? 'selected' : '' }}>{{ $th }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block ml-1">Semester</label>
                <select name="semester" class="w-full bg-gray-50 border-gray-100 rounded-xl text-xs font-bold focus:ring-2 focus:ring-[#064e3b] transition-all">
                    <option value="1" {{ ($semester_filter ?? $setting->semester) == 1 ? 'selected' : '' }}>1 (Ganjil)</option>
                    <option value="2" {{ ($semester_filter ?? $setting->semester) == 2 ? 'selected' : '' }}>2 (Genap)</option>
                </select>
            </div>
            <div>
                <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block ml-1">Pilih Kelas</label>
                <select name="kelas" class="w-full bg-gray-50 border-gray-100 rounded-xl text-xs font-bold focus:ring-2 focus:ring-[#064e3b] transition-all">
                    @foreach($listKelas as $kls)
                        <option value="{{ $kls }}" {{ ($kelas_filter ?? $siswa->kelas) == $kls ? 'selected' : '' }}>{{ $kls }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="bg-[#064e3b] hover:bg-[#053f30] text-white font-black py-3 rounded-xl transition-all shadow-lg shadow-[#064e3b]/20 text-[10px] uppercase tracking-widest group">
                <i class="fa-solid fa-magnifying-glass mr-2 group-hover:scale-110 transition-transform"></i> Filter Raport
            </button>
        </form>
    </div>

    {{-- Ringkasan Performa --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white border border-gray-100 p-5 rounded-2xl flex items-center justify-between shadow-sm group hover:border-[#064e3b]/30 transition-all">
            <div>
                <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Total Mata Pelajaran</p>
                <span class="text-2xl font-black text-gray-800">{{ count($rekapNilai) }}</span>
            </div>
            <div class="h-12 w-12 bg-gray-50 rounded-xl flex items-center justify-center text-[#064e3b]">
                <i class="fa-solid fa-book-open text-xl"></i>
            </div>
        </div>
        
        <div class="bg-white border border-gray-100 p-5 rounded-2xl flex items-center justify-between shadow-sm group hover:border-[#064e3b]/30 transition-all">
            <div>
                <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Nilai Rata-rata</p>
                <span class="text-2xl font-black text-gray-800">{{ number_format(collect($rekapNilai)->avg('akhir'), 1) }}</span>
            </div>
            <div class="h-12 w-12 bg-gray-50 rounded-xl flex items-center justify-center text-blue-600">
                <i class="fa-solid fa-chart-line text-xl"></i>
            </div>
        </div>

        <div class="bg-[#064e3b] p-5 rounded-2xl flex items-center justify-between shadow-xl shadow-[#064e3b]/20 relative overflow-hidden">
            <div class="relative z-10">
                <p class="text-[9px] font-black text-white/60 uppercase tracking-widest mb-1">Status Kelulusan</p>
                <span class="text-lg font-black text-white uppercase">{{ collect($rekapNilai)->every('akhir', '>=', 75) && count($rekapNilai) > 0 ? 'Lulus Mapel' : 'Ada Remedial' }}</span>
            </div>
            <div class="h-12 w-12 bg-white/10 rounded-xl flex items-center justify-center text-[#ffb800] relative z-10">
                <i class="fa-solid fa-graduation-cap text-xl"></i>
            </div>
            <div class="absolute -right-4 -bottom-4 h-16 w-16 bg-white/5 rounded-full"></div>
        </div>
    </div>

    {{-- Tabel Utama --}}
    <div class="bg-white border border-gray-100 rounded-2xl overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="px-6 py-5 font-black text-gray-400 uppercase tracking-widest text-center w-12">No</th>
                        <th class="px-6 py-5 font-black text-gray-400 uppercase tracking-widest">Mata Pelajaran</th>
                        <th class="px-4 py-5 font-black text-gray-400 uppercase tracking-widest text-center">Harian</th>
                        <th class="px-4 py-5 font-black text-gray-400 uppercase tracking-widest text-center">UTS</th>
                        <th class="px-4 py-5 font-black text-gray-400 uppercase tracking-widest text-center">UAS</th>
                        <th class="px-6 py-5 font-black text-[#064e3b] uppercase tracking-widest text-center bg-[#064e3b]/5">Akhir</th>
                        <th class="px-4 py-5 font-black text-gray-400 uppercase tracking-widest text-center">Predikat</th>
                        <th class="px-6 py-5 font-black text-gray-400 uppercase tracking-widest text-right">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($rekapNilai as $index => $item)
                    <tr class="hover:bg-gray-50/50 transition-all group">
                        <td class="px-6 py-5 text-center font-bold text-gray-300 group-hover:text-[#ffb800] transition-colors">{{ $index + 1 }}</td>
                        <td class="px-6 py-5">
                            <div class="font-black text-slate-700 uppercase tracking-tight">{{ $item['mapel'] }}</div>
                        </td>
                        <td class="px-4 py-5 text-center font-bold text-slate-600">{{ $item['harian'] }}</td>
                        <td class="px-4 py-5 text-center font-bold text-slate-600">{{ $item['uts'] }}</td>
                        <td class="px-4 py-5 text-center font-bold text-slate-600">{{ $item['uas'] }}</td>
                        <td class="px-6 py-5 text-center bg-[#064e3b]/[0.02]">
                            <span class="inline-flex items-center justify-center h-8 w-12 bg-[#064e3b] rounded-lg font-black text-white text-[11px] shadow-sm">
                                {{ $item['akhir'] }}
                            </span>
                        </td>
                        <td class="px-4 py-5 text-center">
                            <span class="font-black text-lg {{ $item['akhir'] >= 75 ? 'text-slate-800' : 'text-rose-600' }}">
                                {{ $item['predikat'] }}
                            </span>
                        </td>
                        <td class="px-6 py-5 text-right">
                            @if($item['akhir'] >= 75)
                                <span class="text-[9px] font-black text-emerald-600 uppercase bg-emerald-50 border border-emerald-100 px-3 py-1.5 rounded-md shadow-sm">Tuntas</span>
                            @elseif($item['akhir'] > 0)
                                <span class="text-[9px] font-black text-rose-600 uppercase bg-rose-50 border border-rose-100 px-3 py-1.5 rounded-md shadow-sm">Remedial</span>
                            @else
                                <span class="text-[9px] font-black text-gray-400 uppercase bg-gray-50 border border-gray-100 px-3 py-1.5 rounded-md">-</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-20 text-center">
                            <div class="flex flex-col items-center">
                                <div class="h-16 w-16 bg-gray-50 rounded-full flex items-center justify-center mb-4">
                                    <i class="fa-solid fa-folder-open text-gray-200 text-2xl"></i>
                                </div>
                                <p class="text-gray-400 font-black uppercase text-[10px] tracking-widest">Tidak ada data nilai untuk filter yang dipilih.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Footer Dokumen --}}
    <div class="flex flex-col md:flex-row justify-between items-center gap-6 pt-6 border-t border-gray-100">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-[#064e3b] rounded-xl flex items-center justify-center shadow-md">
                <span class="text-[#ffb800] text-xs font-black italic">SIA</span>
            </div>
            <div class="text-[10px] text-gray-400 font-black uppercase leading-tight tracking-[0.1em]">
                Sistem Informasi Akademik<br>
                <span class="text-[#064e3b]">SMAN 1 Jejangkit</span>
            </div>
        </div>
        <div class="flex flex-col items-end">
            <p class="text-[8px] text-gray-300 font-bold uppercase mt-2 tracking-tighter">Laporan digital ini sah dan dihasilkan secara sistem otomatis</p>
        </div>
    </div>
</div>

<style>
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    .animate-fade-in { animation: fadeIn 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
    
    /* Custom Scrollbar untuk Table */
    .overflow-x-auto::-webkit-scrollbar { height: 6px; }
    .overflow-x-auto::-webkit-scrollbar-track { background: #f1f1f1; }
    .overflow-x-auto::-webkit-scrollbar-thumb { background: #064e3b; border-radius: 10px; }
</style>
@endsection