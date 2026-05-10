@extends('layouts.admin')

@section('content')
<div class="p-4 space-y-4 animate-fade-in">
    {{-- Header Ramping --}}
    <div class="flex items-end justify-between px-1">
        <div>
            <h2 class="text-base font-black text-slate-800 tracking-tight uppercase leading-none">Jadwal <span class="text-blue-600">Pelajaran</span></h2>
            <p class="text-[8px] text-slate-400 font-bold uppercase tracking-[0.1em] mt-1">Sistem Manajemen KBM SMAN 1 Jejangkit</p>
        </div>
        <a href="{{ route('admin.jadwal.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-black text-[9px] uppercase tracking-widest shadow-sm transition-all active:scale-95 flex items-center gap-2">
            <i class="fa-solid fa-plus text-[8px]"></i> Jadwal
        </a>
    </div>

    {{-- Notifikasi Mini --}}
    @if(session('success'))
    <div class="p-3 bg-emerald-50 border border-emerald-100 text-emerald-700 rounded-lg flex items-center gap-3 animate-fade-in">
        <i class="fa-solid fa-circle-check text-[10px]"></i>
        <p class="text-[9px] font-black uppercase tracking-wider">{{ session('success') }}</p>
    </div>
    @endif

    {{-- Main Card --}}
    <div class="w-full bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        {{-- Accent Line --}}
        <div class="h-[3px] bg-blue-600 w-full"></div>
        
        {{-- Toolbar Filter Ramping --}}
        <div class="px-5 py-3 border-b border-slate-100 bg-slate-50/30">
            <form method="GET" action="{{ route('admin.jadwal.index') }}" class="flex flex-wrap items-center gap-2">
                {{-- Filter Kelas --}}
                <div class="relative w-full md:w-44 group">
                    <select name="kelas_id" class="appearance-none w-full bg-white border border-slate-200 text-slate-700 text-[10px] font-bold uppercase rounded-lg focus:ring-2 focus:ring-blue-500 block pl-8 pr-8 py-1.5 transition-all outline-none shadow-sm cursor-pointer">
                        <option value="">-- KELAS --</option>
                        @foreach($data_kelas as $k)
                            <option value="{{ $k->id }}" {{ request('kelas_id') == $k->id ? 'selected' : '' }}>
                                KELAS {{ $k->nama_kelas }}
                            </option>
                        @endforeach
                    </select>
                    <div class="absolute inset-y-0 left-2.5 flex items-center text-slate-400 pointer-events-none">
                        <i class="fa-solid fa-chalkboard-user text-[9px]"></i>
                    </div>
                </div>

                {{-- Filter Hari --}}
                <div class="relative w-full md:w-36 group">
                    <select name="hari" class="appearance-none w-full bg-white border border-slate-200 text-slate-700 text-[10px] font-bold uppercase rounded-lg focus:ring-2 focus:ring-blue-500 block pl-8 pr-8 py-1.5 transition-all outline-none shadow-sm cursor-pointer">
                        <option value="">-- HARI --</option>
                        @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'] as $h)
                            <option value="{{ $h }}" {{ request('hari') == $h ? 'selected' : '' }}>{{ strtoupper($h) }}</option>
                        @endforeach
                    </select>
                    <div class="absolute inset-y-0 left-2.5 flex items-center text-slate-400 pointer-events-none">
                        <i class="fa-solid fa-calendar-day text-[9px]"></i>
                    </div>
                </div>

                <button type="submit" class="bg-slate-800 hover:bg-black text-white px-4 py-1.5 rounded-lg text-[9px] font-black uppercase tracking-widest transition-all">
                    FILTER
                </button>

                @if(request('kelas_id') || request('hari'))
                    <a href="{{ route('admin.jadwal.index') }}" class="bg-rose-500 hover:bg-rose-600 text-white px-4 py-1.5 rounded-lg text-[9px] font-black uppercase tracking-widest transition-all active:scale-95 flex items-center gap-1">
                        <i class="fa-solid fa-rotate-left text-[8px]"></i> RESET
                    </a>
                @endif
            </form>
        </div>

        {{-- Table Content Ramping --}}
        <div class="overflow-x-auto">
            <table class="w-full border-collapse">
                <thead>
                    <tr class="text-[9px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-50">
                        <th class="px-6 py-3 bg-slate-50/50 w-12 text-center text-blue-600/70">NO</th>
                        <th class="px-6 py-3 bg-slate-50/50">WAKTU & HARI</th>
                        <th class="px-6 py-3 bg-slate-50/50">MAPEL & GURU</th>
                        <th class="px-6 py-3 bg-slate-50/50 text-center">KELAS</th>
                        <th class="px-6 py-3 bg-slate-50/50 text-center">AKSI</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($jadwal as $item)
                    <tr class="group hover:bg-slate-50/50 transition-all">
                        <td class="px-6 py-3 text-center text-[10px] font-bold text-slate-300">
                            {{ ($jadwal->currentPage() - 1) * $jadwal->perPage() + $loop->iteration }}
                        </td>
                        <td class="px-6 py-3">
                            <span class="block text-[10px] font-black text-slate-700 uppercase leading-none">{{ $item->hari }}</span>
                            <span class="text-[8px] font-black text-blue-500 uppercase mt-1 inline-block">
                                <i class="fa-regular fa-clock mr-1"></i>{{ $item->jam_mulai }} - {{ $item->jam_selesai }}
                            </span>
                        </td>
                        <td class="px-6 py-3">
                            <span class="block text-[10px] font-black text-slate-700 uppercase leading-none group-hover:text-blue-600 transition-colors">
                                {{ $item->mapel->nama_mapel ?? '-' }}
                            </span>
                            <span class="text-[8px] text-slate-400 font-bold uppercase tracking-widest mt-1 inline-block">
                                <i class="fa-solid fa-user-tie mr-1"></i> {{ $item->guru->nama ?? '-' }}
                            </span>
                        </td>
                        <td class="px-6 py-3 text-center">
                            <span class="bg-slate-800 text-white px-2.5 py-0.5 rounded text-[8px] font-black uppercase tracking-wider">
                                {{ $item->dataKelas->nama_kelas ?? $item->kelas ?? 'N/A' }}
                            </span>
                        </td>
                        <td class="px-6 py-3">
                            <div class="flex justify-center gap-1">
                                <a href="{{ route('admin.jadwal.edit', $item->id) }}" 
                                   class="w-7 h-7 flex items-center justify-center text-amber-500 hover:bg-amber-50 rounded-lg transition-all" title="Edit">
                                    <i class="fa-solid fa-pen-to-square text-[10px]"></i>
                                </a>
                                <form action="{{ route('admin.jadwal.destroy', $item->id) }}" method="POST" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="w-7 h-7 flex items-center justify-center text-rose-500 hover:bg-rose-50 rounded-lg transition-all" 
                                            onclick="return confirm('Hapus jadwal ini?')" title="Hapus">
                                        <i class="fa-solid fa-trash text-[10px]"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center gap-2 opacity-20">
                                <i class="fa-solid fa-calendar-xmark text-3xl"></i>
                                <span class="text-[9px] font-black uppercase tracking-[0.2em]">Data Kosong</span>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Footer/Pagination Mini --}}
        @if(method_exists($jadwal, 'links'))
        <div class="px-6 py-3 bg-slate-50/30 border-t border-slate-100 flex flex-col md:flex-row justify-between items-center gap-3">
            <span class="text-[8px] font-bold text-slate-300 uppercase tracking-widest">SIAKAD Jadwal v2.0</span>
            <div class="scale-90 origin-right">
                {{ $jadwal->appends(request()->query())->links() }}
            </div>
        </div>
        @endif
    </div>
</div>

<style>
    @keyframes fadeIn { from { opacity: 0; transform: translateY(5px); } to { opacity: 1; transform: translateY(0); } }
    .animate-fade-in { animation: fadeIn 0.3s ease-out forwards; }
</style>
@endsection