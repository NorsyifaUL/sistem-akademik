@extends('layouts.admin')

@section('content')
<div class="p-4 space-y-6 animate-fade-in">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 px-1">
        <div>
            <h1 class="text-xl font-black text-slate-800 tracking-tight uppercase leading-none">
                Data <span class="text-blue-600">Mata Pelajaran</span>
            </h1>
            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-[0.2em] mt-1.5">
                Pusat Pengelolaan Kurikulum SMAN 1 Jejangkit
            </p>
        </div>
        <a href="{{ route('admin.mapel.create') }}" class="inline-flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-lg font-black text-[11px] uppercase tracking-widest transition-all active:scale-95 shadow-sm w-max">
            <i class="fa-solid fa-plus text-[10px]"></i> Tambah Mapel
        </a>
    </div>

    {{-- Notifikasi --}}
    @if(session('success'))
    <div class="p-4 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-700 rounded-r-lg font-black text-[10px] uppercase tracking-widest shadow-sm">
        {{ session('success') }}
    </div>
    @endif

    {{-- Main Card --}}
    <div class="w-full bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="h-[4px] bg-blue-600 w-full"></div>
        
        {{-- Toolbar Filter --}}
        <div class="p-4 border-b border-slate-50 bg-slate-50/30">
            <form method="GET" action="{{ route('admin.mapel.index') }}" class="flex gap-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="CARI NAMA MAPEL..." 
                       class="flex-1 bg-white border border-slate-200 text-[11px] font-bold uppercase rounded-lg px-4 py-2.5 outline-none focus:border-blue-500 placeholder:text-slate-300 transition-all">
                <button type="submit" class="bg-slate-800 hover:bg-slate-900 text-white px-6 py-2.5 rounded-lg text-[10px] font-black uppercase tracking-widest transition-all">
                    Cari
                </button>
                @if(request('search'))
                    <a href="{{ route('admin.mapel.index') }}" class="bg-rose-500 hover:bg-rose-600 text-white px-6 py-2.5 rounded-lg text-[10px] font-black uppercase tracking-widest transition-all flex items-center">
                        Reset
                    </a>
                @endif
            </form>
        </div>

        {{-- Table Content --}}
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 font-black uppercase text-[10px] tracking-widest border-b border-slate-100">
                        <th class="px-6 py-4 text-center w-16">No</th>
                        <th class="px-6 py-4">Nama Mata Pelajaran</th>
                        <th class="px-6 py-4 text-center">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($mapels as $item)
                    <tr class="hover:bg-blue-50/20 transition-all">
                        <td class="px-6 py-4 text-center text-[11px] font-bold text-slate-400">#{{ str_pad(($mapels->currentPage() - 1) * $mapels->perPage() + $loop->iteration, 2, '0', STR_PAD_LEFT) }}</td>
                        <td class="px-6 py-4 font-bold text-slate-700 text-[12px] uppercase tracking-wide">{{ $item->nama_mapel }}</td>
                        <td class="px-6 py-4">
                            <div class="flex justify-center gap-2">
                                <a href="{{ route('admin.mapel.edit', $item->id) }}" class="w-8 h-8 flex items-center justify-center bg-white text-amber-500 hover:bg-amber-500 hover:text-white rounded-lg border border-amber-100 transition-all">
                                    <i class="fa-solid fa-pen-to-square text-[11px]"></i>
                                </a>
                                <form action="{{ route('admin.mapel.destroy', $item->id) }}" method="POST">
                                    @csrf @method('DELETE')
                                    <button type="submit" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')" class="w-8 h-8 flex items-center justify-center bg-white text-rose-500 hover:bg-rose-500 hover:text-white rounded-lg border border-rose-100 transition-all">
                                        <i class="fa-solid fa-trash-can text-[11px]"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="py-16 text-center text-[11px] text-slate-400 uppercase font-black">Data Kosong</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="p-4 border-t border-slate-50 bg-slate-50/20">
            {{ $mapels->appends(request()->query())->links('pagination::tailwind') }}
        </div>
    </div>
</div>
@endsection