@extends('layouts.admin')

@section('content')
<div class="p-4 space-y-4 animate-fade-in">
    {{-- Header & Navigasi - Lebih Ramping --}}
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.nilai.index') }}" class="group flex items-center gap-2 bg-white hover:bg-blue-600 text-gray-500 hover:text-white px-4 py-2 rounded-lg border border-gray-100 shadow-sm transition-all active:scale-95 text-xs">
            <i class="fa-solid fa-arrow-left group-hover:-translate-x-1 transition-transform"></i>
            <span class="font-bold uppercase tracking-wider">Kembali</span>
        </a>
        
        <div class="flex items-center gap-3">
            <div class="text-right hidden md:block">
                <p class="text-[8px] font-black text-gray-400 uppercase tracking-widest">Periode</p>
                <p class="text-[10px] font-black text-blue-600 uppercase">{{ $tahun_tampil }} | Smstr {{ $semester_tampil }}</p>
            </div>
            <div class="h-6 w-[1px] bg-gray-200 hidden md:block"></div>
            <span class="text-[9px] font-black text-blue-600 uppercase tracking-widest bg-blue-50 px-3 py-1.5 rounded-full border border-blue-100">Rekap Nilai</span>
        </div>
    </div>

    {{-- MAIN TABLE CARD --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden relative">
        <div class="absolute top-0 left-0 right-0 h-1 bg-blue-600"></div>

        {{-- Info Siswa - Padding dikurangi --}}
        <div class="p-5 border-b border-gray-50 bg-gray-50/30 pt-6">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 bg-blue-600 rounded-xl flex items-center justify-center text-white shadow-md shadow-blue-100">
                    <i class="fa-solid fa-user-graduate text-lg"></i>
                </div>
                <div>
                    <h2 class="text-lg font-black text-gray-800 uppercase tracking-tight">{{ $siswa->nama }}</h2>
                    <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">
                        NISN: <span class="text-blue-600">{{ $siswa->nisn }}</span> • Kelas: <span class="text-blue-600">{{ $siswa->kelas }}</span>
                    </p>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="text-[9px] font-black text-gray-400 uppercase tracking-widest bg-gray-50/50">
                        <th class="px-6 py-3 border-b w-16 text-center">No</th>
                        <th class="px-4 py-3 border-b">Mata Pelajaran</th>
                        <th class="px-4 py-3 text-center border-b">Harian</th>
                        <th class="px-4 py-3 text-center border-b">UTS</th>
                        <th class="px-4 py-3 text-center border-b">UAS</th>
                        <th class="px-6 py-3 text-center bg-blue-50/30 text-blue-600 border-b">Nilai Akhir</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($details as $d)
                    <tr class="group hover:bg-blue-50/10 transition-all">
                        <td class="px-6 py-3 text-[10px] font-bold text-gray-300 text-center">
                            {{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}
                        </td>
                        <td class="px-4 py-3">
                            <span class="text-xs font-black text-gray-700 uppercase tracking-tight group-hover:text-blue-700 transition-colors">{{ $d->nama_mapel }}</span>
                        </td>
                        <td class="px-4 py-3 text-center text-[10px] font-bold text-blue-500">{{ $d->tugas }}</td>
                        <td class="px-4 py-3 text-center text-[10px] font-bold text-gray-500">{{ $d->uts }}</td>
                        <td class="px-4 py-3 text-center text-[10px] font-bold text-gray-500">{{ $d->uas }}</td>
                        <td class="px-6 py-3 text-center bg-blue-50/5">
                            <div class="inline-block px-3 py-1 rounded-lg font-black text-xs {{ $d->nilai_akhir < 75 ? 'bg-rose-100 text-rose-600' : 'bg-blue-600 text-white shadow-sm' }}">
                                {{ $d->nilai_akhir }}
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-gray-400 uppercase text-[9px] font-black tracking-widest">Data tidak ditemukan</td>
                    </tr>
                    @endforelse
                </tbody>

                {{-- BARIS RATA-RATA - POLOS & LEBIH PENDEK --}}
                @if(count($details) > 0)
                <tfoot>
                    <tr class="bg-white border-t border-gray-100">
                        <td colspan="2" class="px-6 py-4">
                            <span class="text-[9px] font-black text-gray-500 uppercase tracking-widest">Rata-Rata Nilai Akhir :</span>
                        </td>
                        <td colspan="3" class="px-4 py-4"></td>
                        <td class="px-6 py-4 text-center">
                            <span class="text-sm font-black text-gray-800 tracking-tight">{{ number_format($rataRata, 2) }}</span>
                        </td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>

        <div class="p-4 bg-gray-50/30 border-t border-gray-50 flex justify-between items-center text-[8px] font-black text-gray-400 uppercase tracking-widest">
            <p>Sistem Akademik SMANJA</p>
            <p>{{ date('d/m/Y') }}</p>
        </div>
    </div>
</div>

<style>
    @keyframes fadeIn { from { opacity: 0; transform: translateY(5px); } to { opacity: 1; transform: translateY(0); } }
    .animate-fade-in { animation: fadeIn 0.4s ease-out forwards; }
</style>
@endsection