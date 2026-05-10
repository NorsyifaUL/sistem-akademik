@extends('layouts.admin')

@section('content')
<div class="p-4 space-y-4 animate-fade-in">
    {{-- Header Ramping & Luas --}}
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 px-1">
        <div>
            <h1 class="text-base font-black text-slate-800 tracking-tight uppercase leading-none">Data <span class="text-blue-600">Mata Pelajaran</span></h1>
            <p class="text-[8px] text-slate-400 font-bold uppercase tracking-[0.1em] mt-1">Kurikulum SMAN 1 Jejangkit</p>
        </div>
        <a href="{{ route('admin.mapel.create') }}" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg font-black text-[9px] transition-all shadow-sm shadow-blue-100 uppercase tracking-widest active:scale-95">
            <i class="fa-solid fa-plus text-[8px]"></i> Tambah Mapel
        </a>
    </div>

    {{-- Notifikasi --}}
    @if(session('success'))
    <div class="bg-emerald-50 border border-emerald-100 p-3 rounded-xl flex items-center gap-3">
        <div class="w-6 h-6 bg-emerald-500 rounded-lg flex items-center justify-center shadow-sm shadow-emerald-200">
            <i class="fa-solid fa-check text-white text-[10px]"></i>
        </div>
        <p class="text-[10px] text-emerald-700 font-black uppercase tracking-tight">{{ session('success') }}</p>
    </div>
    @endif

    {{-- Main Card - Lebar Maksimal --}}
    <div class="w-full bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="h-[3px] bg-blue-600 w-full"></div>
        
        {{-- Toolbar / Search Bar --}}
        <div class="p-4 border-b border-slate-50 bg-slate-50/30 flex flex-col md:flex-row justify-between items-center gap-4">
            <form action="{{ route('admin.mapel.index') }}" method="GET" class="flex gap-2 w-full md:w-auto">
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                        <i class="fa-solid fa-magnifying-glass text-[10px]"></i>
                    </span>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="CARI MATA PELAJARAN..." 
                           class="w-full md:w-72 pl-9 pr-4 py-1.5 bg-white border border-slate-200 rounded-lg text-[10px] font-bold outline-none focus:ring-2 focus:ring-blue-500/10 focus:border-blue-500 transition-all uppercase tracking-widest placeholder:text-slate-300">
                </div>
                <button type="submit" class="bg-slate-800 hover:bg-slate-900 text-white px-4 py-1.5 rounded-lg text-[9px] font-black transition-all uppercase tracking-widest">
                    Filter
                </button>
            </form>
            
            <div class="text-[9px] font-black text-slate-400 uppercase tracking-widest bg-white border border-slate-200 px-3 py-1.5 rounded-full shadow-sm">
                <i class="fa-solid fa-book-bookmark text-blue-500 mr-1"></i> Total: <span class="text-blue-600">{{ $mapels->total() ?? 0 }}</span> Data
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50/50 text-slate-400 font-black uppercase text-[9px] tracking-widest border-b border-slate-100">
                        <th class="px-6 py-4 text-left w-20">No</th>
                        <th class="px-6 py-4 text-left">Nama Mata Pelajaran</th>
                        <th class="px-6 py-4 text-center w-40">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($mapels as $item)
                    <tr class="hover:bg-blue-50/30 transition-all group">
                        <td class="px-6 py-4">
                            <span class="text-[11px] font-bold text-slate-300 group-hover:text-blue-500 transition-colors italic">
                                #{{ str_pad(($mapels->currentPage() - 1) * $mapels->perPage() + $loop->iteration, 2, '0', STR_PAD_LEFT) }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center group-hover:bg-blue-600 group-hover:text-white transition-all duration-300">
                                    <i class="fa-solid fa-book-open text-[10px]"></i>
                                </div>
                                <span class="uppercase font-black text-slate-700 tracking-tight text-xs group-hover:text-blue-600 transition-colors">
                                    {{ $item->nama_mapel }}
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex justify-center gap-2">
                                {{-- Tombol Edit - Oranye --}}
                                <a href="{{ route('admin.mapel.edit', $item->id) }}" 
                                   class="w-9 h-9 flex items-center justify-center bg-amber-50 text-amber-500 hover:bg-amber-500 hover:text-white rounded-xl transition-all shadow-sm shadow-amber-100 border border-amber-100" title="Edit">
                                    <i class="fa-solid fa-pen-to-square text-xs"></i>
                                </a>
                                {{-- Tombol Hapus - Merah --}}
                                <form action="{{ route('admin.mapel.destroy', $item->id) }}" method="POST" class="inline">
                                    @csrf @method('DELETE')
                                    <button class="w-9 h-9 flex items-center justify-center bg-rose-50 text-rose-500 hover:bg-rose-500 hover:text-white rounded-xl transition-all shadow-sm shadow-rose-100 border border-rose-100" 
                                            onclick="return confirm('Hapus Mata Pelajaran ini?')" title="Hapus">
                                        <i class="fa-solid fa-trash-can text-xs"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="py-20 text-center">
                            <div class="flex flex-col items-center">
                                <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mb-4">
                                    <i class="fa-solid fa-folder-open text-2xl text-slate-200"></i>
                                </div>
                                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest text-xs">Mata Pelajaran Tidak Ditemukan</span>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="p-5 border-t border-slate-50 bg-slate-50/20">
            <div class="custom-pagination">
                {{ $mapels->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>

<style>
    @keyframes fadeIn { from { opacity: 0; transform: translateY(5px); } to { opacity: 1; transform: translateY(0); } }
    .animate-fade-in { animation: fadeIn 0.3s ease-out forwards; }
    
    /* Menyesuaikan pagination agar serasi */
    .custom-pagination nav svg { height: 14px; width: 14px; }
    .custom-pagination nav span, .custom-pagination nav a { font-size: 10px !important; font-weight: 800; border-radius: 8px !important; }
</style>
@endsection