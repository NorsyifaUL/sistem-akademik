@extends('layouts.admin')

@section('content')
<div class="p-6 space-y-6 animate-fade-in">
    {{-- Header & Back Button --}}
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.nilai.index') }}" class="group flex items-center gap-3 bg-white hover:bg-gray-900 text-gray-500 hover:text-white px-5 py-3 rounded-2xl border border-gray-100 shadow-sm transition-all active:scale-95">
            <i class="fa-solid fa-arrow-left-long group-hover:-translate-x-1 transition-transform"></i>
            <span class="text-[10px] font-black uppercase tracking-widest">Kembali ke Rekap</span>
        </a>
        
        <div class="flex items-center gap-3">
            <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest bg-gray-100 px-4 py-2 rounded-full">Detail Akademik Siswa</span>
        </div>
    </div>

    {{-- 1. PROFIL CARD (With Blue Line) --}}
    <div class="bg-white rounded-[35px] border border-gray-100 shadow-xl shadow-gray-50/50 overflow-hidden relative">
        {{-- Garis Biru Tipis Akses --}}
        <div class="absolute top-0 left-0 right-0 h-1.5 bg-blue-600"></div>

        <div class="p-10 flex flex-col md:flex-row items-center gap-8 relative">
            {{-- Watermark Icon --}}
            <div class="absolute top-0 right-0 p-10 opacity-[0.03] pointer-events-none">
                <i class="fa-solid fa-id-card text-[180px]"></i>
            </div>

            {{-- Avatar --}}
            <div class="w-28 h-28 bg-gradient-to-br from-blue-600 to-indigo-700 rounded-[35px] flex items-center justify-center text-white shadow-2xl shadow-blue-200 relative z-10">
                <i class="fa-solid fa-user-graduate text-5xl"></i>
            </div>

            {{-- Identity --}}
            <div class="flex-1 text-center md:text-left relative z-10">
                <p class="text-[10px] font-black text-blue-600 uppercase tracking-[0.3em] mb-1">Nama Lengkap Siswa</p>
                <h2 class="text-4xl font-black text-gray-800 uppercase tracking-tight">{{ $siswa->nama }}</h2>
                <div class="flex flex-wrap justify-center md:justify-start gap-3 mt-4">
                    <div class="px-4 py-2 bg-gray-50 border border-gray-100 rounded-xl flex items-center gap-2">
                        <i class="fa-solid fa-fingerprint text-gray-300"></i>
                        <span class="text-[10px] font-black text-gray-500 uppercase tracking-widest">{{ $siswa->nisn }}</span>
                    </div>
                    <div class="px-4 py-2 bg-blue-50 border border-blue-100 rounded-xl flex items-center gap-2">
                        <i class="fa-solid fa-layer-group text-blue-400"></i>
                        <span class="text-[10px] font-black text-blue-600 uppercase tracking-widest">Kelas {{ $siswa->kelas }}</span>
                    </div>
                </div>
            </div>

            {{-- Score Summary --}}
            <div class="bg-gray-900 p-8 rounded-[35px] text-center min-w-[200px] shadow-2xl shadow-gray-200 relative z-10">
                <p class="text-[9px] font-bold text-gray-500 uppercase tracking-[0.2em] mb-2">Rata-Rata Akhir</p>
                <h4 class="text-5xl font-black text-white leading-none">{{ number_format($rataRata, 2) }}</h4>
                <div class="mt-4 h-1 w-12 bg-blue-600 mx-auto rounded-full"></div>
            </div>
        </div>
    </div>

    {{-- 2. TABLE DETAIL CARD (With Blue Line) --}}
    <div class="bg-white rounded-[35px] border border-gray-100 shadow-xl shadow-gray-50/50 overflow-hidden relative">
        {{-- Garis Biru Tipis Akses --}}
        <div class="absolute top-0 left-0 right-0 h-1.5 bg-blue-600"></div>

        <div class="p-8 border-b border-gray-50 bg-gray-50/30 flex items-center justify-between pt-10">
            <h3 class="text-[11px] font-black text-gray-800 uppercase tracking-[0.2em] flex items-center gap-3">
                <i class="fa-solid fa-list-check text-blue-600"></i> Rincian Nilai Per Mata Pelajaran
            </h3>
            
            <a href="{{ route('admin.nilai.raport', $siswa->id) }}" target="_blank" class="bg-rose-500 hover:bg-rose-600 text-white text-[10px] font-black py-3 px-6 rounded-2xl transition-all shadow-lg shadow-rose-100 flex items-center gap-2 uppercase tracking-widest active:scale-95">
                <i class="fa-solid fa-print"></i> Cetak Raport
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">
                        <th class="px-10 py-6">Mata Pelajaran</th>
                        <th class="px-6 py-6 text-center">Rata Harian</th>
                        <th class="px-6 py-6 text-center">UTS</th>
                        <th class="px-6 py-6 text-center">UAS</th>
                        <th class="px-10 py-6 text-center bg-blue-50/50 text-blue-600">Nilai Akhir</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($details as $d)
                    <tr class="group hover:bg-blue-50/30 transition-all">
                        <td class="px-10 py-6">
                            <span class="text-sm font-black text-gray-700 uppercase tracking-tight group-hover:text-blue-700 transition-colors">{{ $d->nama_mapel }}</span>
                        </td>
                        <td class="px-6 py-6 text-center">
                            <span class="text-xs font-bold text-gray-500">{{ $d->tugas }}</span>
                        </td>
                        <td class="px-6 py-6 text-center">
                            <span class="text-xs font-bold text-gray-500">{{ $d->uts }}</span>
                        </td>
                        <td class="px-6 py-6 text-center">
                            <span class="text-xs font-bold text-gray-500">{{ $d->uas }}</span>
                        </td>
                        <td class="px-10 py-6 text-center bg-blue-50/20">
                            <div class="inline-block px-4 py-1.5 rounded-xl font-black text-sm {{ $d->nilai_akhir < 75 ? 'bg-rose-100 text-rose-600' : 'bg-blue-600 text-white shadow-lg shadow-blue-100' }}">
                                {{ $d->nilai_akhir }}
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
    @keyframes fadeIn { from { opacity: 0; transform: translateY(15px); } to { opacity: 1; transform: translateY(0); } }
    .animate-fade-in { animation: fadeIn 0.6s cubic-bezier(0.34, 1.56, 0.64, 1) forwards; }
    
    /* Custom Scrollbar */
    .overflow-x-auto::-webkit-scrollbar { height: 8px; }
    .overflow-x-auto::-webkit-scrollbar-track { background: #f8fafc; }
    .overflow-x-auto::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
</style>
@endsection