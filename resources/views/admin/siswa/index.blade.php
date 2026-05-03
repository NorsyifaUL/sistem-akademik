@extends('layouts.admin')

@section('content')
<div class="p-6 space-y-6 animate-fade-in">
    {{-- Header & Action --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-gray-800 tracking-tight uppercase">Data Siswa</h1>
            <p class="text-xs font-bold text-gray-400 mt-1 uppercase tracking-widest">Sistem Manajemen Siswa SMAN 1 Jejangkit</p>
        </div>
        <a href="{{ route('admin.siswa.create') }}" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-lg font-bold text-sm transition-all shadow-md shadow-blue-100 uppercase tracking-widest">
            <i class="fa-solid fa-user-plus text-xs"></i> Tambah Siswa
        </a>
    </div>

    {{-- Notifikasi Sukses --}}
    @if(session('success'))
    <div class="bg-emerald-50 border-l-4 border-emerald-500 p-4 rounded-xl flex items-center gap-3 animate-fade-in">
        <i class="fa-solid fa-circle-check text-emerald-500"></i>
        <p class="text-sm text-emerald-700 font-bold uppercase tracking-tight">{{ session('success') }}</p>
    </div>
    @endif

    {{-- Main Card dengan Garis Biru --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden border-t-4 border-t-blue-600">
        
        {{-- Area Search & Filter --}}
        <div class="p-5 border-b border-gray-50 bg-gray-50/30 flex flex-col md:flex-row justify-between items-center gap-4">
            <form action="{{ route('admin.siswa.index') }}" method="GET" class="flex flex-col md:flex-row gap-3 w-full md:w-auto">
                
                {{-- Input Pencarian --}}
                <div class="relative flex-1 md:w-64">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                        <i class="fa-solid fa-magnifying-glass text-xs"></i>
                    </span>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Nama atau NISN..." 
                           class="w-full pl-10 pr-4 py-2 bg-white border border-gray-200 rounded-lg text-sm outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                </div>

                {{-- Dropdown Filter Kelas --}}
                <div class="relative md:w-44">
                    <select name="kelas" class="w-full pl-4 pr-10 py-2 bg-white border border-gray-200 rounded-lg text-sm outline-none focus:ring-2 focus:ring-blue-500 appearance-none font-bold text-gray-600 cursor-pointer">
                        <option value="">-- Semua Kelas --</option>
                        @foreach(['X 1', 'X 2', 'XI 1', 'XI 2', 'XII IPA', 'XII IPS'] as $kls)
                            <option value="{{ $kls }}" {{ request('kelas') == $kls ? 'selected' : '' }}>
                                Kelas {{ $kls }}
                            </option>
                        @endforeach
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-3 flex items-center text-gray-400">
                        <i class="fa-solid fa-chevron-down text-[10px]"></i>
                    </div>
                </div>

                {{-- Tombol Submit --}}
                <div class="flex gap-2">
                    <button type="submit" class="bg-gray-800 text-white px-5 py-2 rounded-lg text-sm font-bold hover:bg-gray-900 transition-all shadow-sm uppercase tracking-widest flex-1 md:flex-none">
                        Filter
                    </button>

                    @if(request('search') || request('kelas'))
                    <a href="{{ route('admin.siswa.index') }}" class="bg-rose-50 text-rose-600 border border-rose-100 px-4 py-2 rounded-lg text-sm font-bold hover:bg-rose-600 hover:text-white transition-all text-center uppercase tracking-widest">
                        Reset
                    </a>
                    @endif
                </div>
            </form>

            <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest hidden md:block">
                <i class="fa-solid fa-users-viewfinder mr-1"></i> Total: {{ $siswa->total() }} Siswa
            </div>
        </div>

        {{-- Table Content --}}
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 text-gray-400 font-black uppercase text-[10px] tracking-widest">
                        <th class="px-6 py-4 text-left border-b w-16">No</th>
                        <th class="px-6 py-4 text-left border-b">Informasi Siswa</th>
                        <th class="px-6 py-4 text-left border-b">NISN</th>
                        <th class="px-6 py-4 text-left border-b">Kelas</th>
                        <th class="px-6 py-4 text-left border-b">No WA Ortu</th>
                        <th class="px-6 py-4 text-center border-b">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($siswa as $item)
                    <tr class="hover:bg-blue-50/30 transition-colors group">
                        <td class="px-6 py-4 font-bold text-gray-300 group-hover:text-blue-500 transition-colors tracking-tighter text-xs">
                            {{ ($siswa->currentPage() - 1) * $siswa->perPage() + $loop->iteration }}
                        </td>
                        <td class="px-6 py-4">
                            <p class="font-black text-gray-700 leading-tight group-hover:text-blue-600 transition-colors uppercase">{{ $item->nama }}</p>
                            <p class="text-[10px] text-gray-400 font-bold mt-0.5 italic lowercase tracking-wider">{{ $item->user->email }}</p>
                        </td>
                        <td class="px-6 py-4">
                            <span class="bg-gray-100 text-gray-600 px-2.5 py-1 rounded-md font-bold text-[11px] tracking-wider border border-gray-200">
                                {{ $item->nisn }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-black bg-blue-50 text-blue-600 border border-blue-100 uppercase">
                                {{ $item->kelas }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <div class="w-2 h-2 rounded-full bg-emerald-400"></div>
                                <span class="font-mono text-[11px] text-gray-500 font-bold">{{ $item->no_wa_ortu }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex justify-center gap-1">
                                {{-- Edit --}}
                                <a href="{{ route('admin.siswa.edit', $item->id) }}" 
                                   class="w-8 h-8 flex items-center justify-center text-orange-400 hover:bg-orange-50 rounded-lg transition-all" title="Edit Data">
                                    <i class="fa-solid fa-pen-to-square text-sm"></i>
                                </a>
                                {{-- Reset Password --}}
                                <form action="{{ route('admin.siswa.reset-password', $item->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button class="w-8 h-8 flex items-center justify-center text-blue-500 hover:bg-blue-50 rounded-lg transition-all" 
                                            title="Reset Password" onclick="return confirm('Reset password siswa ini?')">
                                        <i class="fa-solid fa-key text-sm"></i>
                                    </button>
                                </form>
                                {{-- Hapus --}}
                                <form action="{{ route('admin.siswa.destroy', $item->id) }}" method="POST" class="inline">
                                    @csrf @method('DELETE')
                                    <button class="w-8 h-8 flex items-center justify-center text-rose-400 hover:bg-rose-50 rounded-lg transition-all" 
                                            title="Hapus Data" onclick="return confirm('Yakin hapus data siswa ini secara permanen?')">
                                        <i class="fa-solid fa-trash text-sm"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-20 text-center text-gray-400 font-bold uppercase tracking-widest text-xs">
                            <i class="fa-solid fa-box-open text-4xl mb-4 block opacity-20"></i>
                            Data Siswa Tidak Ditemukan
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination Footer --}}
        <div class="p-5 bg-gray-50/20 border-t border-gray-50">
            {{ $siswa->appends(request()->query())->links() }}
        </div>
    </div>
</div>

<style>
    @keyframes fadeIn { from { opacity: 0; transform: translateY(5px); } to { opacity: 1; transform: translateY(0); } }
    .animate-fade-in { animation: fadeIn 0.4s ease-out forwards; }
</style>
@endsection