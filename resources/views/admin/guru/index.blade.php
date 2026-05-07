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

    {{-- Main Card --}}
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
            
            {{-- SINKRONISASI TAHUN AKADEMIK --}}
            <div class="flex items-center gap-2 px-4 py-2 bg-blue-50 rounded-full border border-blue-100">
                <span class="w-2 h-2 bg-blue-500 rounded-full animate-pulse"></span>
                <div class="text-[10px] font-black text-blue-600 uppercase tracking-widest">
                    Periode Aktif: {{ $setting->tahun_ajaran ?? 'Belum Diatur' }} (Semester {{ $setting->semester ?? '-' }})
                </div>
            </div>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 text-gray-400 font-black uppercase text-[10px] tracking-widest">
                        <th class="px-6 py-4 text-left border-b w-16">No</th>
                        <th class="px-6 py-4 text-left border-b">Nama Lengkap</th>
                        <th class="px-6 py-4 text-left border-b">NIP</th>
                        <th class="px-6 py-4 text-left border-b">Email</th>
                        <th class="px-6 py-4 text-center border-b">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($gurus as $g)
                    <tr class="hover:bg-blue-50/30 transition-colors group">
                        <td class="px-6 py-4 font-bold text-gray-400">{{ $loop->iteration }}</td>
                        <td class="px-6 py-4 font-black text-gray-700 group-hover:text-blue-600 transition-colors">{{ $g->nama }}</td>
                        <td class="px-6 py-4">
                            <span class="bg-gray-100 text-gray-600 px-3 py-1 rounded-full font-bold text-[10px]">{{ $g->nip }}</span>
                        </td>
                        <td class="px-6 py-4 text-gray-500 italic">{{ $g->user->email ?? '-' }}</td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex justify-center gap-2">
                                <a href="{{ route('admin.guru.edit',$g->id) }}" class="text-orange-400 hover:text-orange-600 p-2 hover:bg-orange-50 rounded-lg transition-all" title="Edit Data">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                                <form action="{{ route('admin.guru.reset-password',$g->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button class="text-blue-500 hover:text-blue-700 p-2 hover:bg-blue-50 rounded-lg transition-all" onclick="return confirm('Reset password guru ini menjadi default?')" title="Reset Password">
                                        <i class="fa-solid fa-key"></i>
                                    </button>
                                </form>
                                <form action="{{ route('admin.guru.destroy',$g->id) }}" method="POST" class="inline">
                                    @csrf @method('DELETE')
                                    <button class="text-rose-400 hover:text-rose-600 p-2 hover:bg-rose-50 rounded-lg transition-all" onclick="return confirm('Hapus data guru? Semua jadwal terkait juga akan terpengaruh.')" title="Hapus Data">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-20 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <i class="fa-solid fa-user-slash text-4xl text-gray-200"></i>
                                <span class="text-xs font-bold text-gray-400 uppercase tracking-[0.2em]">Data Guru Tidak Ditemukan</span>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection