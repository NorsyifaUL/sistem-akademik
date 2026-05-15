@extends('layouts.siswa')

@section('content')
<div class="max-w-6xl mx-auto space-y-6 animate-fade-in pb-8">
    {{-- Header Dokumen Akademik --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-end border-b-4 border-[#064e3b] pb-5">
        <div>
            <div class="flex items-center gap-3 mb-2">
                <div class="h-9 w-9 bg-[#064e3b] rounded-lg flex items-center justify-center shadow-lg">
                    <i class="fa-solid fa-file-invoice text-[#ffb800] text-lg"></i>
                </div>
                <h1 class="text-2xl font-black text-gray-900 uppercase tracking-tighter">Laporan Hasil Belajar</h1>
            </div>
            <div class="mt-3 grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-0.5">
                <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Nama : <span class="text-gray-900 ml-1">{{ auth()->user()->name }}</span></p>
                <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest">NISN : <span class="text-gray-900 ml-1">{{ $siswa->nisn ?? '-' }}</span></p>
                <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Kelas : <span class="text-gray-900 ml-1 font-bold">{{ $siswa->kelas->nama_kelas ?? '-' }}</span></p>
                <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Status : <span class="text-[#064e3b] ml-1 italic">Siswa Aktif</span></p>
            </div>
        </div>
        <div class="mt-4 md:mt-0 text-right bg-gray-50/80 p-3 rounded-xl border border-gray-100">
            <p class="text-[9px] font-black text-[#ffb800] uppercase tracking-[0.2em]">
                Semester {{ $semester_filter }} ({{ $semester_filter == 1 ? 'Ganjil' : 'Genap' }})
            </p>
            <p class="text-xs font-black text-[#064e3b] uppercase">TA {{ $tahun_filter }}</p>
        </div>
    </div>

    {{-- Form Filter --}}
    <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm">
        <form action="{{ route('siswa.nilai') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
            <div>
                <label class="text-[8px] font-black text-gray-400 uppercase tracking-widest mb-1.5 block ml-1">Tahun Ajaran</label>
                <select name="tahun_ajaran" class="w-full bg-gray-50 border-gray-100 rounded-xl text-[11px] font-bold py-2 focus:ring-2 focus:ring-[#064e3b]">
                    @foreach($listTahun as $th)
                        <option value="{{ $th }}" {{ $tahun_filter == $th ? 'selected' : '' }}>{{ $th }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="text-[8px] font-black text-gray-400 uppercase tracking-widest mb-1.5 block ml-1">Semester</label>
                <select name="semester" class="w-full bg-gray-50 border-gray-100 rounded-xl text-[11px] font-bold py-2 focus:ring-2 focus:ring-[#064e3b]">
                    <option value="1" {{ $semester_filter == 1 ? 'selected' : '' }}>1 (Ganjil)</option>
                    <option value="2" {{ $semester_filter == 2 ? 'selected' : '' }}>2 (Genap)</option>
                </select>
            </div>

            <button type="submit" class="bg-[#064e3b] hover:bg-[#053f30] text-white font-black py-2.5 rounded-xl transition-all shadow-md text-[9px] uppercase tracking-widest group">
                <i class="fa-solid fa-magnifying-glass mr-2 group-hover:scale-110 transition-transform"></i> Tampilkan Nilai
            </button>
        </form>
    </div>

    {{-- Tabel Utama - Versi Bersih (Nilai Saja) --}}
    <div class="bg-white border border-gray-100 rounded-2xl overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="px-5 py-4 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center w-12">No</th>
                        <th class="px-5 py-4 text-[9px] font-black text-gray-400 uppercase tracking-widest">Mata Pelajaran</th>
                        <th class="px-4 py-4 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center">Harian</th>
                        <th class="px-4 py-4 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center">UTS</th>
                        <th class="px-4 py-4 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center">UAS</th>
                        <th class="px-5 py-4 text-[9px] font-black text-[#064e3b] uppercase tracking-widest text-center bg-[#064e3b]/5">Akhir</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($rekapNilai as $index => $item)
                    <tr class="hover:bg-gray-50/50 transition-all group">
                        <td class="px-5 py-4 text-center font-bold text-gray-300 group-hover:text-[#ffb800] text-[10px]">{{ $index + 1 }}</td>
                        <td class="px-5 py-4">
                            <div class="font-black text-slate-700 uppercase tracking-tight text-[11px]">{{ $item['mapel'] }}</div>
                        </td>
                        <td class="px-4 py-4 text-center font-bold text-slate-600 text-[11px]">{{ $item['harian'] }}</td>
                        <td class="px-4 py-4 text-center font-bold text-slate-600 text-[11px]">{{ $item['uts'] }}</td>
                        <td class="px-4 py-4 text-center font-bold text-slate-600 text-[11px]">{{ $item['uas'] }}</td>
                        <td class="px-5 py-4 text-center bg-[#064e3b]/[0.01]">
                            <span class="inline-flex items-center justify-center h-7 w-11 bg-[#064e3b] rounded-lg font-black text-white text-[10px] shadow-sm">
                                {{ $item['akhir'] }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-5 py-16 text-center">
                            <div class="flex flex-col items-center">
                                <i class="fa-solid fa-folder-open text-gray-100 text-4xl mb-3"></i>
                                <p class="text-gray-400 font-black uppercase text-[9px] tracking-widest">Data nilai tidak ditemukan.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                
                @if(count($rekapNilai) > 0)
                <tfoot class="bg-gray-50/50 border-t-2 border-gray-100">
                    <tr>
                        <td colspan="2" class="px-5 py-5 font-black text-gray-500 uppercase tracking-widest text-[10px]">
                            <div class="flex items-center gap-2">
                                <i class="fa-solid fa-chart-simple text-[#064e3b]"></i>
                                Rata-rata Nilai
                            </div>
                        </td>
                        <td colspan="3"></td>
                        <td class="px-5 py-5 text-center bg-[#064e3b]/10">
                            <span class="text-xs font-black text-[#064e3b]">
                                {{ round(collect($rekapNilai)->avg('akhir')) }}
                            </span>
                        </td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>

    {{-- Footer Dokumen --}}
    <div class="flex flex-col md:flex-row justify-between items-center gap-4 pt-4 border-t border-gray-100">
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 bg-[#064e3b] rounded-lg flex items-center justify-center shadow-md">
                <span class="text-[#ffb800] text-[10px] font-black italic">SIA</span>
            </div>
            <div class="text-[9px] text-gray-400 font-black uppercase leading-tight tracking-wider">
                Sistem Informasi Akademik <span class="text-[#064e3b]">SMAN 1 Jejangkit</span>
            </div>
        </div>
        <p class="text-[8px] text-gray-300 font-bold uppercase italic tracking-tighter">Terbitan digital otomatis oleh sistem informasi sekolah</p>
    </div>
</div>

<style>
    @keyframes fadeIn { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: translateY(0); } }
    .animate-fade-in { animation: fadeIn 0.5s ease-out forwards; }
    .overflow-x-auto::-webkit-scrollbar { height: 4px; }
    .overflow-x-auto::-webkit-scrollbar-track { background: transparent; }
    .overflow-x-auto::-webkit-scrollbar-thumb { background: #064e3b; border-radius: 10px; }
</style>
@endsection