@extends('layouts.admin')

@section('content')
<div class="p-6">
    {{-- Header --}}
    <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-black text-gray-800 tracking-tight uppercase">Rekapitulasi Absensi</h2>
            <p class="text-xs text-gray-500 font-bold uppercase tracking-widest">Total Akumulasi Ketidakhadiran Siswa</p>
        </div>
        
        {{-- Tombol Cetak (Opsional jika ingin ditambahkan nanti) --}}
        <div class="flex gap-2">
            <button onclick="window.print()" class="bg-gray-100 hover:bg-gray-200 text-gray-800 px-4 py-2 rounded-xl text-xs font-black uppercase transition-all border border-gray-300">
                🖨️ Cetak Rekap
            </button>
        </div>
    </div>

    {{-- Filter Form --}}
    <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 mb-6">
        <form action="{{ route('admin.absensi.rekap') }}" method="GET" class="flex flex-wrap items-end gap-4">
            <div class="w-full md:w-48">
                <label class="block text-[10px] font-black uppercase text-gray-500 mb-2">Pilih Bulan</label>
                <select name="bulan" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm font-bold focus:ring-2 focus:ring-gray-800 outline-none">
                    @for ($i = 1; $i <= 12; $i++)
                        <option value="{{ sprintf('%02d', $i) }}" {{ request('bulan', date('m')) == sprintf('%02d', $i) ? 'selected' : '' }}>
                            {{ Carbon\Carbon::create()->month($i)->translatedFormat('F') }}
                        </option>
                    @endfor
                </select>
            </div>

            <div class="w-full md:w-48">
                <label class="block text-[10px] font-black uppercase text-gray-500 mb-2">Pilih Kelas</label>
                <select name="kelas" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm font-bold focus:ring-2 focus:ring-gray-800 outline-none">
                    <option value="">Semua Kelas</option>
                    @foreach($kelasList as $k)
                        <option value="{{ $k->kelas }}" {{ request('kelas') == $k->kelas ? 'selected' : '' }}>{{ $k->kelas }}</option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="bg-gray-800 hover:bg-black text-white px-6 py-2.5 rounded-xl text-xs font-black uppercase transition-all shadow-lg shadow-gray-200">
                Tampilkan Data
            </button>
        </form>
    </div>

    {{-- Card Rekap --}}
    <div class="bg-white shadow-2xl rounded-3xl border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-800 text-white">
                    <tr>
                        <th class="px-6 py-4 text-left text-[10px] font-black uppercase tracking-wider">No</th>
                        <th class="px-6 py-4 text-left text-[10px] font-black uppercase tracking-wider">Nama Siswa</th>
                        <th class="px-6 py-4 text-center text-[10px] font-black uppercase tracking-wider bg-green-600">Hadir</th>
                        <th class="px-6 py-4 text-center text-[10px] font-black uppercase tracking-wider bg-amber-500">Sakit</th>
                        <th class="px-6 py-4 text-center text-[10px] font-black uppercase tracking-wider bg-blue-500">Izin</th>
                        <th class="px-6 py-4 text-center text-[10px] font-black uppercase tracking-wider bg-rose-500">Alfa</th>
                        <th class="px-6 py-4 text-center text-[10px] font-black uppercase tracking-wider border-l border-gray-700">Total Absen</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($rekaps as $index => $r)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 text-gray-400 font-bold text-center">{{ $index + 1 }}</td>
                        <td class="px-6 py-4">
                            <span class="font-bold text-gray-800 uppercase block leading-tight">{{ $r->nama }}</span>
                            <span class="text-[9px] text-gray-400 uppercase tracking-tighter">Siswa SMAN 1 Jejangkit</span>
                        </td>
                        <td class="px-6 py-4 text-center font-black text-green-600 bg-green-50/30">{{ $r->hadir }}</td>
                        <td class="px-6 py-4 text-center font-black text-amber-600 bg-amber-50/30">{{ $r->sakit }}</td>
                        <td class="px-6 py-4 text-center font-black text-blue-600 bg-blue-50/30">{{ $r->izin }}</td>
                        <td class="px-6 py-4 text-center font-black text-rose-600 bg-rose-50/30">{{ $r->alpa }}</td>
                        <td class="px-6 py-4 text-center font-black text-gray-900 bg-gray-100/50 border-l border-gray-100">
                            {{ $r->sakit + $r->izin + $r->alpa }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-400 font-bold uppercase tracking-widest text-xs">
                            Data tidak ditemukan untuk periode/kelas ini
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection