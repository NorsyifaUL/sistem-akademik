@extends('layouts.admin')

@section('content')
<div class="p-6 space-y-6 animate-fade-in">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-gray-800 tracking-tight uppercase">Data Mata Pelajaran</h1>
            <p class="text-xs font-bold text-gray-400 mt-1 uppercase tracking-widest">Kurikulum SMAN 1 Jejangkit</p>
        </div>
        <a href="{{ route('admin.mapel.create') }}" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-lg font-bold text-sm transition-all shadow-md shadow-blue-100 uppercase tracking-widest">
            <i class="fa-solid fa-plus text-xs"></i> Tambah Mapel
        </a>
    </div>

    {{-- Notifikasi --}}
    @if(session('success'))
    <div class="bg-emerald-50 border-l-4 border-emerald-500 p-4 rounded-xl flex items-center gap-3">
        <i class="fa-solid fa-circle-check text-emerald-500"></i>
        <p class="text-sm text-emerald-700 font-bold uppercase tracking-tight">{{ session('success') }}</p>
    </div>
    @endif

    {{-- Main Card --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden border-t-4 border-t-blue-600">
        
        {{-- Search Bar --}}
        <div class="p-5 border-b border-gray-50 bg-gray-50/30 flex flex-col md:flex-row justify-between items-center gap-4">
            <form action="{{ route('admin.mapel.index') }}" method="GET" class="flex gap-2 w-full md:w-auto">
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                        <i class="fa-solid fa-magnifying-glass text-xs"></i>
                    </span>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Mata Pelajaran..." 
                           class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-lg text-sm outline-none focus:ring-2 focus:ring-blue-500 md:w-80 transition-all">
                </div>
                <button type="submit" class="bg-gray-800 text-white px-5 py-2 rounded-lg text-sm font-bold hover:bg-gray-900 transition-all uppercase tracking-widest">
                    Filter
                </button>
            </form>
            <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest">
                <i class="fa-solid fa-book-bookmark mr-1"></i> Total: {{ $mapels->total() ?? 0 }} Mata Pelajaran
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 text-gray-400 font-black uppercase text-[10px] tracking-widest">
                        <th class="px-6 py-4 text-left border-b w-20">No</th>
                        <th class="px-6 py-4 text-left border-b">Nama Mata Pelajaran</th>
                        <th class="px-6 py-4 text-center border-b w-40">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($mapels as $item)
                    <tr class="hover:bg-blue-50/30 transition-colors group">
                        <td class="px-6 py-4 font-bold text-gray-300 group-hover:text-blue-500 transition-colors">
                            {{ ($mapels->currentPage() - 1) * $mapels->perPage() + $loop->iteration }}
                        </td>
                        <td class="px-6 py-4 uppercase font-black text-gray-700 tracking-tight group-hover:text-blue-600 transition-colors">
                            {{ $item->nama_mapel }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex justify-center gap-2">
                                <a href="{{ route('admin.mapel.edit', $item->id) }}" 
                                   class="w-9 h-9 flex items-center justify-center text-orange-400 hover:bg-orange-50 rounded-xl transition-all border border-transparent hover:border-orange-100" title="Edit">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                                <form action="{{ route('admin.mapel.destroy', $item->id) }}" method="POST" class="inline">
                                    @csrf @method('DELETE')
                                    <button class="w-9 h-9 flex items-center justify-center text-rose-400 hover:bg-rose-50 rounded-xl transition-all border border-transparent hover:border-rose-100" 
                                            onclick="return confirm('Hapus Mata Pelajaran ini?')" title="Hapus">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="py-20 text-center text-gray-400 font-bold uppercase tracking-widest text-xs">
                            <i class="fa-solid fa-folder-open text-4xl mb-4 block opacity-20"></i>
                            Mata Pelajaran Tidak Ditemukan
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="p-5 border-t border-gray-50 bg-gray-50/20">
            {{ $mapels->appends(request()->query())->links() }}
        </div>
    </div>
</div>
@endsection