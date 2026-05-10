@extends('layouts.admin')

@section('content')
<div class="p-4 space-y-4 animate-fade-in">
    {{-- Header Ramping --}}
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 px-1">
        <div>
            <h1 class="text-lg font-black text-slate-800 tracking-tight uppercase leading-none">
                Data <span class="text-blue-600">Guru</span>
            </h1>
            <p class="text-[9px] text-slate-400 font-bold uppercase tracking-[0.1em] mt-1">SMAN 1 Jejangkit</p>
        </div>
        <div class="flex items-center gap-3">
            {{-- Status TA Ramping --}}
            <div class="hidden md:flex items-center gap-2 px-3 py-1.5 bg-blue-50 rounded-lg border border-blue-100">
                <span class="w-1.5 h-1.5 bg-blue-500 rounded-full animate-pulse"></span>
                <div class="text-[9px] font-black text-blue-600 uppercase tracking-wider">
                    TA: {{ $setting->tahun_ajaran ?? '-' }} (Sms {{ $setting->semester ?? '-' }})
                </div>
            </div>
            <a href="{{ route('admin.guru.create') }}" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-black text-[10px] transition-all shadow-sm uppercase tracking-widest active:scale-95">
                <i class="fa-solid fa-plus text-[9px]"></i> Tambah Guru
            </a>
        </div>
    </div>

    {{-- Main Card --}}
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="h-[3px] bg-blue-600 w-full"></div>
        
        {{-- Search Area Ramping --}}
        <div class="p-4 border-b border-slate-100 bg-slate-50/30">
            <form action="{{ route('admin.guru.index') }}" method="GET" class="flex gap-2">
                <div class="relative">
                    <i class="fa-solid fa-magnifying-glass absolute left-3 top-2.5 text-slate-400 text-[10px]"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="CARI NAMA / NIP..." 
                           class="border border-slate-200 pl-8 pr-4 py-2 rounded-lg text-[11px] font-bold outline-none focus:ring-4 focus:ring-blue-500/5 focus:border-blue-500 w-64 transition-all uppercase placeholder:text-slate-300">
                </div>
                <button type="submit" class="bg-slate-800 text-white px-4 py-2 rounded-lg text-[9px] font-black hover:bg-slate-900 transition-all uppercase tracking-widest">
                    Cari
                </button>
            </form>
        </div>

        {{-- Table - Baris Lebih Ramping --}}
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 font-black uppercase text-[9px] tracking-widest border-b border-slate-100">
                        <th class="px-5 py-3 text-center w-16">No</th>
                        <th class="px-5 py-3">Tenaga Pendidik / Mapel</th>
                        <th class="px-5 py-3">NIP / Identitas</th>
                        <th class="px-5 py-3">Akses Email</th>
                        <th class="px-5 py-3 text-center">Opsi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($gurus as $g)
                    <tr class="hover:bg-blue-50/20 transition-all group">
                        <td class="px-5 py-3 text-center">
                            <span class="text-[10px] font-bold text-slate-300 group-hover:text-blue-500 transition-colors italic">
                                #{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}
                            </span>
                        </td>
                        <td class="px-5 py-3">
                            <div class="flex flex-col">
                                <span class="font-black text-slate-700 uppercase text-[12px] tracking-tight group-hover:text-blue-600 transition-colors">
                                    {{ $g->nama }}
                                </span>
                                <div class="flex items-center gap-1 mt-0.5">
                                    <i class="fa-solid fa-book-open text-[8px] text-blue-400"></i>
                                    <span class="text-[9px] text-blue-500 font-bold uppercase tracking-wide italic">
                                        @if($g->mapels && $g->mapels->count() > 0)
                                            {{ $g->mapels->pluck('nama_mapel')->unique()->implode(', ') }}
                                        @else
                                            <span class="text-slate-300 font-medium">Belum Ada Mapel</span>
                                        @endif
                                    </span>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-3">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[9px] font-black bg-slate-100 text-slate-600 border border-slate-200 uppercase tracking-widest group-hover:bg-white group-hover:border-blue-200 transition-all">
                                {{ $g->nip ?? 'NON-NIP' }}
                            </span>
                        </td>
                        <td class="px-5 py-3">
                            <span class="text-[11px] font-semibold text-slate-400 lowercase italic opacity-80 group-hover:opacity-100 transition-opacity">
                                {{ $g->user->email ?? '-' }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-center">
                            <div class="flex justify-center gap-1.5">
                                {{-- Edit --}}
                                <a href="{{ route('admin.guru.edit',$g->id) }}" 
                                   class="w-7 h-7 flex items-center justify-center bg-white text-amber-500 hover:bg-amber-500 hover:text-white rounded-lg transition-all border border-amber-100" title="Edit Data">
                                    <i class="fa-solid fa-pen-to-square text-[10px]"></i>
                                </a>

                                {{-- Reset Password --}}
                                <form action="{{ route('admin.guru.reset-password',$g->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="w-7 h-7 flex items-center justify-center bg-white text-blue-500 hover:bg-blue-500 hover:text-white rounded-lg transition-all border border-blue-100" 
                                            onclick="return confirm('Reset password guru ini?')" title="Reset Password">
                                        <i class="fa-solid fa-key text-[10px]"></i>
                                    </button>
                                </form>

                                {{-- Hapus --}}
                                <form action="{{ route('admin.guru.destroy',$g->id) }}" method="POST" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="w-7 h-7 flex items-center justify-center bg-white text-rose-500 hover:bg-rose-500 hover:text-white rounded-lg transition-all border border-rose-100" 
                                            onclick="return confirm('Hapus data guru?')" title="Hapus Data">
                                        <i class="fa-solid fa-trash-can text-[10px]"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-16 text-center">
                            <div class="flex flex-col items-center opacity-20">
                                <i class="fa-solid fa-user-slash text-4xl mb-3 text-slate-300"></i>
                                <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Data Kosong</span>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
    @keyframes fadeIn { from { opacity: 0; transform: translateY(5px); } to { opacity: 1; transform: translateY(0); } }
    .animate-fade-in { animation: fadeIn 0.4s ease-out forwards; }
    table { border-collapse: separate; border-spacing: 0; }
</style>
@endsection