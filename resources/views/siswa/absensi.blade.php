@extends('layouts.siswa')

@section('content')
<div class="space-y-6 animate-fade-in">
    {{-- Header --}}
    <div class="border-b border-gray-100 pb-6">
        <h2 class="text-2xl font-black text-gray-800 tracking-tight uppercase">Riwayat Presensi</h2>
        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-1">Laporan Kehadiran Mandiri Siswa</p>
    </div>

    {{-- Statistik Cards --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm border-l-4 border-l-emerald-500">
            <p class="text-[9px] font-black text-gray-400 uppercase">Total Hadir</p>
            <p class="text-xl font-black text-emerald-600">{{ $semuaAbsensi->where('status', 'Hadir')->count() }}</p>
        </div>
        <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm border-l-4 border-l-blue-500">
            <p class="text-[9px] font-black text-gray-400 uppercase">Total Sakit</p>
            <p class="text-xl font-black text-blue-600">{{ $semuaAbsensi->where('status', 'Sakit')->count() }}</p>
        </div>
        <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm border-l-4 border-l-amber-500">
            <p class="text-[9px] font-black text-gray-400 uppercase">Total Izin</p>
            <p class="text-xl font-black text-amber-600">{{ $semuaAbsensi->where('status', 'Izin')->count() }}</p>
        </div>
        <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm border-l-4 border-l-rose-500">
            <p class="text-[9px] font-black text-gray-400 uppercase">Total Alpa</p>
            <p class="text-xl font-black text-rose-600">{{ $semuaAbsensi->whereIn('status', ['Alpa', 'Alfa'])->count() }}</p>
        </div>
    </div>

    {{-- Tabel Utama --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50 border-b border-gray-100">
                        <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">No</th>
                        <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Waktu & Tanggal</th>
                        <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Status</th>
                        <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Keterangan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($absensi as $index => $item)
                    <tr class="hover:bg-gray-50/50 transition-colors group">
                        <td class="px-6 py-4 text-center">
                            <span class="text-xs font-bold text-gray-300">{{ $absensi->firstItem() + $index }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-col">
                                {{-- Tanggal tetap dari kolom tanggal --}}
                                <span class="text-sm font-bold text-gray-700">
                                    {{ \Carbon\Carbon::parse($item->tanggal)->translatedFormat('d F Y') }}
                                </span>
                                {{-- Jam mengambil dari created_at agar tidak 00:00 --}}
                                <span class="text-[10px] text-[#ffb800] font-black uppercase tracking-tighter">
                                    <i class="fa-regular fa-clock mr-1"></i>
                                    {{ $item->created_at->format('H:i') }} WIB
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            @php
                                $color = [
                                    'Hadir' => 'bg-emerald-50 text-emerald-600 border-emerald-100',
                                    'Sakit' => 'bg-blue-50 text-blue-600 border-blue-100',
                                    'Izin' => 'bg-amber-50 text-amber-600 border-amber-100',
                                    'Alpa' => 'bg-rose-50 text-rose-600 border-rose-100',
                                    'Alfa' => 'bg-rose-50 text-rose-600 border-rose-100',
                                ][$item->status] ?? 'bg-gray-50 text-gray-600 border-gray-100';
                            @endphp
                            <span class="px-3 py-1 rounded-md text-[10px] font-black uppercase border {{ $color }}">
                                {{ $item->status }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-xs text-gray-500 italic leading-relaxed">
                                {{ $item->keterangan ?? '—' }}
                            </p>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="py-20 text-center">
                            <p class="text-gray-300 font-bold uppercase text-xs tracking-widest">Data Presensi Belum Tersedia</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pagination --}}
    <div class="flex justify-end pt-2">
        {{ $absensi->links() }}
    </div>
</div>

<style>
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    .animate-fade-in { animation: fadeIn 0.5s ease-out forwards; }
</style>
@endsection