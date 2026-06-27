@extends('layouts.admin')

@section('content')
<div class="p-4 space-y-6 animate-fade-in">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 px-1">
        <div>
            <h1 class="text-xl font-black text-slate-800 tracking-tight uppercase leading-none">
                Rekapitulasi <span class="text-blue-600">Absensi</span>
            </h1>
            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-[0.2em] mt-1.5">
                Total Akumulasi Ketidakhadiran Siswa
            </p>
        </div>
        
        <button onclick="window.print()" class="bg-white border border-slate-200 hover:bg-slate-800 hover:text-white text-slate-600 px-5 py-2.5 rounded-lg text-[10px] font-black uppercase tracking-widest transition-all">
            <i class="fa-solid fa-print mr-2"></i> Cetak Rekap
        </button>
    </div>

    {{-- Filter Form --}}
    <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
        <form action="{{ route('admin.absensi.rekap') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-3 items-end">
            <div class="space-y-1.5">
                <label class="block text-[9px] font-black text-slate-400 uppercase tracking-widest px-1">Pilih Bulan</label>
                <select name="bulan" class="w-full h-10 bg-slate-50 border border-slate-200 rounded-lg px-4 text-[11px] font-bold uppercase outline-none focus:border-blue-500 cursor-pointer">
                    @for ($i = 1; $i <= 12; $i++)
                        <option value="{{ sprintf('%02d', $i) }}" {{ request('bulan', date('m')) == sprintf('%02d', $i) ? 'selected' : '' }}>
                            {{ strtoupper(Carbon\Carbon::create()->month($i)->translatedFormat('F')) }}
                        </option>
                    @endfor
                </select>
            </div>

            <div class="space-y-1.5">
                <label class="block text-[9px] font-black text-slate-400 uppercase tracking-widest px-1">Pilih Kelas</label>
                <select name="kelas" class="w-full h-10 bg-slate-50 border border-slate-200 rounded-lg px-4 text-[11px] font-bold uppercase outline-none focus:border-blue-500 cursor-pointer">
                    <option value="">SEMUA KELAS</option>
                    @foreach($kelasList as $k)
                        <option value="{{ $k->kelas }}" {{ request('kelas') == $k->kelas ? 'selected' : '' }}>{{ $k->kelas }}</option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="h-10 bg-slate-800 hover:bg-blue-600 text-white rounded-lg text-[10px] font-black uppercase tracking-widest transition-all shadow-sm">
                Tampilkan Data
            </button>
        </form>
    </div>

    {{-- Card Rekap --}}
    <div class="bg-white shadow-sm rounded-xl border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr class="text-[10px] font-black text-slate-500 uppercase tracking-widest">
                        <th class="px-6 py-4 text-center w-16">No</th>
                        <th class="px-6 py-4">Nama Siswa</th>
                        <th class="px-4 py-4 text-center">Hadir</th>
                        <th class="px-4 py-4 text-center">Sakit</th>
                        <th class="px-4 py-4 text-center">Izin</th>
                        <th class="px-4 py-4 text-center">Alfa</th>
                        <th class="px-6 py-4 text-center">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($rekaps as $index => $r)
                    <tr class="hover:bg-blue-50/20 transition-all text-[11px]">
                        <td class="px-6 py-4 text-center font-bold text-slate-400">{{ $index + 1 }}</td>
                        <td class="px-6 py-4">
                            <div class="font-black text-slate-700 uppercase">{{ $r->nama }}</div>
                            <div class="text-[9px] text-slate-400 font-bold uppercase tracking-widest">SMAN 1 JEJANGKIT</div>
                        </td>
                        <td class="px-4 py-4 text-center font-black text-emerald-600">{{ $r->hadir }}</td>
                        <td class="px-4 py-4 text-center font-black text-amber-600">{{ $r->sakit }}</td>
                        <td class="px-4 py-4 text-center font-black text-blue-600">{{ $r->izin }}</td>
                        <td class="px-4 py-4 text-center font-black text-rose-600">{{ $r->alpa }}</td>
                        <td class="px-6 py-4 text-center font-black text-slate-800 bg-slate-50">
                            {{ $r->sakit + $r->izin + $r->alpa }}
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