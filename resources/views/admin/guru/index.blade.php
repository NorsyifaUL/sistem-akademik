@extends('layouts.admin')

@section('content')
<div class="p-6 space-y-6 animate-fade-in">
    {{-- Header --}}
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-black text-gray-800 tracking-tight uppercase">Data Guru</h1>
            <p class="text-xs font-bold text-gray-400 mt-1 uppercase tracking-widest">Sistem Informasi Akademik SMANJA</p>
        </div>
        <a href="{{ route('admin.guru.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-lg font-bold text-sm transition-all shadow-md flex items-center gap-2">
            <i class="fa-solid fa-plus"></i> Tambah Guru
        </a>
    </div>

    {{-- Main Card dengan Garis Biru --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden border-t-4 border-t-blue-600">
        {{-- Area Filter/Search --}}
        <div class="p-5 border-b border-gray-50 bg-gray-50/30 flex justify-between items-center">
            <form action="{{ route('admin.guru.index') }}" method="GET" class="flex gap-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Nama/NIP..." 
                       class="border border-gray-200 px-4 py-2 rounded-lg text-sm outline-none focus:ring-2 focus:ring-blue-500 w-64 transition-all">
                <button type="submit" class="bg-gray-800 text-white px-5 py-2 rounded-lg text-sm font-bold hover:bg-gray-900 transition-all">
                    Tampilkan
                </button>
            </form>
            <div class="text-[11px] font-black text-gray-400 uppercase tracking-tighter">
                Tahun Akademik: 2025/2026
            </div>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 text-gray-400 font-black uppercase text-[10px] tracking-widest">
                        <th class="px-6 py-4 text-left border-b">No</th>
                        <th class="px-6 py-4 text-left border-b">Nama Lengkap</th>
                        <th class="px-6 py-4 text-left border-b">NIP</th>
                        <th class="px-6 py-4 text-left border-b">Email</th>
                        <th class="px-6 py-4 text-center border-b">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($gurus as $g)
                    <tr class="hover:bg-blue-50/30 transition-colors">
                        <td class="px-6 py-4 font-bold text-gray-400">{{ $loop->iteration }}</td>
                        <td class="px-6 py-4 font-black text-gray-700">{{ $g->nama }}</td>
                        <td class="px-6 py-4">
                            <span class="bg-gray-100 text-gray-600 px-2 py-1 rounded font-bold text-[11px]">{{ $g->nip }}</span>
                        </td>
                        <td class="px-6 py-4 text-gray-500 italic">{{ $g->user->email }}</td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex justify-center gap-2">
                                <a href="{{ route('admin.guru.edit',$g->id) }}" class="text-orange-400 hover:text-orange-600 p-1 transition-colors">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                                <form action="{{ route('admin.guru.reset-password',$g->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button class="text-blue-500 hover:text-blue-700 p-1" onclick="return confirm('Reset password?')">
                                        <i class="fa-solid fa-key"></i>
                                    </button>
                                </form>
                                <form action="{{ route('admin.guru.destroy',$g->id) }}" method="POST" class="inline">
                                    @csrf @method('DELETE')
                                    <button class="text-rose-400 hover:text-rose-600 p-1" onclick="return confirm('Hapus data?')">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-10 text-center text-gray-400 font-bold uppercase tracking-widest text-xs">Data Tidak Ditemukan</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection