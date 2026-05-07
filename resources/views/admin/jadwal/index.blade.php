@extends('layouts.admin')

@section('content')
<div class="p-6 space-y-6 animate-fade-in">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-gray-800 tracking-tight uppercase">Jadwal Pelajaran</h1>
            <p class="text-xs font-bold text-gray-400 mt-1 uppercase tracking-widest">Sistem Manajemen KBM SMAN 1 Jejangkit</p>
        </div>
        <a href="{{ route('admin.jadwal.create') }}" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-lg font-bold text-sm transition-all shadow-md shadow-blue-100 uppercase tracking-widest">
            <i class="fa-solid fa-calendar-plus text-xs"></i> Tambah Jadwal
        </a>
    </div>

    {{-- Notifikasi --}}
    @if(session('success'))
    <div class="bg-emerald-50 border-l-4 border-emerald-500 p-4 rounded-xl flex items-center gap-3 animate-fade-in">
        <i class="fa-solid fa-circle-check text-emerald-500"></i>
        <p class="text-sm text-emerald-700 font-bold uppercase tracking-tight">{{ session('success') }}</p>
    </div>
    @endif

    {{-- Main Card --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden border-t-4 border-t-blue-600">
        
        {{-- Area 🔍 SEARCH & FILTER --}}
        <div class="p-5 border-b border-gray-50 bg-gray-50/30">
            <form method="GET" action="{{ route('admin.jadwal.index') }}" class="flex flex-wrap items-center gap-3">
                
                {{-- Dropdown Pilih Kelas Dinamis --}}
                <div class="relative flex-1 md:flex-none md:w-64">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                        <i class="fa-solid fa-chalkboard-user text-xs"></i>
                    </span>
                    <select name="kelas_id" class="w-full pl-10 pr-10 py-2.5 bg-white border border-gray-200 rounded-xl text-sm font-bold text-gray-600 outline-none focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 appearance-none cursor-pointer">
                        <option value="">-- Semua Kelas --</option>
                        @foreach($data_kelas as $k)
                            <option value="{{ $k->id }}" {{ request('kelas_id') == $k->id ? 'selected' : '' }}>
                                KELAS {{ $k->nama_kelas }}
                            </option>
                        @endforeach
                    </select>
                    <div class="absolute inset-y-0 right-3 flex items-center pointer-events-none text-gray-400 font-black">
                        <i class="fa-solid fa-chevron-down text-[10px]"></i>
                    </div>
                </div>

                {{-- Dropdown Pilih Hari --}}
                <div class="relative flex-1 md:flex-none md:w-48">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                        <i class="fa-solid fa-calendar-day text-[10px]"></i>
                    </span>
                    <select name="hari" class="w-full pl-10 pr-10 py-2.5 bg-white border border-gray-200 rounded-xl text-sm font-bold text-gray-600 outline-none focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 appearance-none cursor-pointer">
                        <option value="">-- Semua Hari --</option>
                        @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'] as $h)
                            <option value="{{ $h }}" {{ request('hari') == $h ? 'selected' : '' }}>{{ $h }}</option>
                        @endforeach
                    </select>
                    <div class="absolute inset-y-0 right-3 flex items-center pointer-events-none text-gray-400 font-black">
                        <i class="fa-solid fa-chevron-down text-[10px]"></i>
                    </div>
                </div>

                {{-- Tombol Cari --}}
                <button type="submit" class="bg-gray-800 text-white px-6 py-2.5 rounded-xl text-xs font-black uppercase tracking-widest hover:bg-black transition-all shadow-sm">
                    Filter Data
                </button>

                @if(request('kelas_id') || request('hari'))
                    <a href="{{ route('admin.jadwal.index') }}" class="bg-rose-50 text-rose-600 border border-rose-100 px-4 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-rose-600 hover:text-white transition-all">
                        Reset
                    </a>
                @endif
            </form>
        </div>

        {{-- Table Content --}}
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 text-gray-400 font-black uppercase text-[10px] tracking-widest">
                        <th class="px-6 py-4 text-left border-b w-16">No</th>
                        <th class="px-6 py-4 text-left border-b">Informasi KBM</th>
                        <th class="px-6 py-4 text-left border-b">Guru & Mapel</th>
                        <th class="px-6 py-4 text-center border-b">Kelas</th>
                        <th class="px-6 py-4 text-center border-b">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($jadwal as $item)
                    <tr class="hover:bg-blue-50/30 transition-colors group">
                        <td class="px-6 py-4 font-bold text-gray-300 group-hover:text-blue-500 transition-colors">
                            {{ ($jadwal->currentPage() - 1) * $jadwal->perPage() + $loop->iteration }}
                        </td>
                        <td class="px-6 py-4">
                            <span class="block font-black text-gray-700 uppercase tracking-tight">{{ $item->hari }}</span>
                            <span class="inline-flex items-center gap-1.5 text-[11px] font-black text-blue-600 mt-1">
                                <i class="fa-regular fa-clock text-[10px]"></i>
                                {{-- Menggunakan Accessor dari Model Jadwal --}}
                                {{ $item->jam_mulai }} - {{ $item->jam_selesai }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-xs font-bold">
                            <p class="text-gray-800 uppercase font-black tracking-tight group-hover:text-blue-600 transition-colors">
                                {{ $item->mapel->nama_mapel ?? '-' }}
                            </p>
                            <p class="text-gray-400 mt-0.5 uppercase tracking-widest text-[9px]">
                                <i class="fa-solid fa-user-tie mr-1"></i> {{ $item->guru->nama ?? '-' }}
                            </p>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="bg-gray-800 text-white px-3 py-1 rounded-full font-black text-[10px] uppercase tracking-widest shadow-sm">
                                {{-- LOGIKA PERBAIKAN: Cek relasi dataKelas dulu, jika null ambil kolom teks 'kelas' --}}
                                {{ $item->dataKelas->nama_kelas ?? $item->kelas ?? 'N/A' }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex justify-center gap-1">
                                <a href="{{ route('admin.jadwal.edit', $item->id) }}" 
                                   class="w-9 h-9 flex items-center justify-center text-orange-400 hover:bg-orange-50 rounded-xl transition-all" title="Edit Jadwal">
                                    <i class="fa-solid fa-pen-to-square text-sm"></i>
                                </a>
                                <form action="{{ route('admin.jadwal.destroy', $item->id) }}" method="POST" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="w-9 h-9 flex items-center justify-center text-rose-400 hover:bg-rose-50 rounded-xl transition-all" 
                                            onclick="return confirm('Yakin hapus jadwal ini?')" title="Hapus Jadwal">
                                        <i class="fa-solid fa-trash text-sm"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-20 text-center text-gray-400 font-bold uppercase tracking-widest text-xs">
                            <i class="fa-solid fa-calendar-xmark text-4xl mb-4 block opacity-20"></i>
                            Jadwal tidak ditemukan
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if(method_exists($jadwal, 'links'))
        <div class="p-5 border-t border-gray-50 bg-gray-50/20">
            {{ $jadwal->appends(request()->query())->links() }}
        </div>
        @endif
    </div>
</div>

<style>
    @keyframes fadeIn { from { opacity: 0; transform: translateY(5px); } to { opacity: 1; transform: translateY(0); } }
    .animate-fade-in { animation: fadeIn 0.4s ease-out forwards; }
</style>
@endsection