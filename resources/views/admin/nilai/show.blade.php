@extends('layouts.admin')

@section('content')
<div class="p-4 space-y-6 animate-fade-in">
    {{-- Header & Navigasi --}}
    <div class="flex items-center justify-between px-1">
        <a href="{{ route('admin.nilai.index') }}" class="group flex items-center gap-2 bg-white hover:bg-slate-800 text-slate-500 hover:text-white px-5 py-2.5 rounded-lg border border-slate-200 transition-all active:scale-95">
            <i class="fa-solid fa-arrow-left text-[10px] group-hover:-translate-x-1 transition-transform"></i>
            <span class="font-black uppercase tracking-widest text-[10px]">Kembali</span>
        </a>
        
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.nilai.raport', $siswa->id) }}" target="_blank" class="flex items-center gap-2 bg-rose-600 hover:bg-rose-700 text-white px-5 py-2.5 rounded-lg shadow-sm transition-all active:scale-95">
                <i class="fa-solid fa-file-pdf text-[10px]"></i>
                <span class="font-black uppercase tracking-widest text-[10px]">Cetak Raport</span>
            </a>
            <div class="hidden md:flex flex-col items-end px-3">
                <span class="text-[8px] font-black text-slate-400 uppercase tracking-widest">Periode</span>
                <span class="text-[10px] font-black text-blue-600 uppercase tracking-wide">{{ $tahun_tampil }} | SMT {{ $semester_tampil }}</span>
            </div>
        </div>
    </div>

    {{-- Main Card --}}
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="h-[4px] bg-blue-600 w-full"></div>

        {{-- Info Siswa --}}
        <div class="p-6 border-b border-slate-50 bg-slate-50/30">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-blue-600 rounded-lg flex items-center justify-center text-white shadow-sm shrink-0">
                    <i class="fa-solid fa-user-graduate text-lg"></i>
                </div>
                <div>
                    <h2 class="text-lg font-black text-slate-800 uppercase tracking-tight">{{ $siswa->nama }}</h2>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-0.5">
                        NISN: <span class="text-blue-600">{{ $siswa->nisn }}</span> • 
                        KELAS: <span class="text-blue-600">{{ $siswa->dataKelas->nama_kelas ?? 'TIDAK ADA' }}</span>
                    </p>
                </div>
            </div>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 font-black uppercase text-[10px] tracking-widest border-b border-slate-100">
                        <th class="px-6 py-4 w-16 text-center">No</th>
                        <th class="px-4 py-4">Mata Pelajaran</th>
                        <th class="px-4 py-4 text-center">Harian</th>
                        <th class="px-4 py-4 text-center">UTS</th>
                        <th class="px-4 py-4 text-center">UAS</th>
                        <th class="px-6 py-4 text-center bg-blue-50 text-blue-700">Nilai Akhir</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($details as $d)
                    <tr class="hover:bg-blue-50/20 transition-all">
                        <td class="px-6 py-4 text-center text-[11px] font-bold text-slate-400">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</td>
                        <td class="px-4 py-4 text-[12px] font-black text-slate-700 uppercase tracking-wide">{{ $d->nama_mapel }}</td>
                        <td class="px-4 py-4 text-center text-[11px] font-bold text-slate-600">{{ $d->tugas }}</td>
                        <td class="px-4 py-4 text-center text-[11px] font-bold text-slate-600">{{ $d->uts }}</td>
                        <td class="px-4 py-4 text-center text-[11px] font-bold text-slate-600">{{ $d->uas }}</td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-block px-3 py-1.5 rounded-lg font-black text-[11px] {{ $d->nilai_akhir < 75 ? 'bg-rose-50 text-rose-600' : 'bg-blue-600 text-white' }}">
                                {{ round($d->nilai_akhir) }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-16 text-center text-[11px] text-slate-400 uppercase font-black">Data nilai tidak tersedia</td>
                    </tr>
                    @endforelse
                </tbody>
                @if(count($details) > 0)
                <tfoot>
                    <tr class="bg-slate-50/50">
                        <td colspan="5" class="px-6 py-4 text-right text-[10px] font-black text-slate-500 uppercase tracking-widest">Rata-Rata Nilai Akhir</td>
                        <td class="px-6 py-4 text-center text-[14px] font-black text-slate-800">{{ round($rataRata) }}</td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>

        <div class="p-4 bg-slate-50/50 border-t border-slate-100 flex justify-between items-center text-[9px] font-black text-slate-400 uppercase tracking-widest">
            <p>Sistem Informasi Akademik SMAN 1 Jejangkit</p>
            <p>{{ date('d/m/Y') }}</p>
        </div>
    </div>
</div>
@endsection