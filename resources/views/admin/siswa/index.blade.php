@extends('layouts.admin')

@section('content')
<div class="p-4 space-y-4 animate-fade-in">
    {{-- Header Ramping & Luas --}}
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 px-1">
        <div>
            <h1 class="text-base font-black text-slate-800 tracking-tight uppercase leading-none">Data <span class="text-blue-600">Siswa</span></h1>
            <p class="text-[8px] text-slate-400 font-bold uppercase tracking-[0.1em] mt-1">Sistem Manajemen Siswa SMAN 1 Jejangkit</p>
        </div>
        <a href="{{ route('admin.siswa.create') }}" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg font-black text-[9px] transition-all shadow-sm shadow-blue-100 uppercase tracking-widest active:scale-95">
            <i class="fa-solid fa-user-plus text-[8px]"></i> Tambah Siswa
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

    {{-- Main Card --}}
    <div class="w-full bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="h-[3px] bg-blue-600 w-full"></div>
        
        {{-- Area Search & Filter --}}
        <div class="p-4 border-b border-slate-50 bg-slate-50/30 flex flex-col md:flex-row justify-between items-center gap-4">
            <form action="{{ route('admin.siswa.index') }}" method="GET" class="flex flex-col md:flex-row gap-2 w-full md:w-auto">
                {{-- Input Pencarian --}}
                <div class="relative group">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                        <i class="fa-solid fa-magnifying-glass text-[10px]"></i>
                    </span>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="CARI NAMA / NISN..." 
                           class="w-full md:w-64 pl-9 pr-4 py-1.5 bg-white border border-slate-200 rounded-lg text-[10px] font-bold outline-none focus:ring-2 focus:ring-blue-500/10 focus:border-blue-500 transition-all uppercase tracking-widest placeholder:text-slate-300">
                </div>

                {{-- Dropdown Filter Kelas --}}
                <div class="relative">
                    <select name="kelas_id" class="w-full md:w-44 pl-3 pr-8 py-1.5 bg-white border border-slate-200 rounded-lg text-[10px] font-black outline-none focus:ring-2 focus:ring-blue-500/10 appearance-none text-slate-600 cursor-pointer uppercase tracking-widest">
                        <option value="">-- SEMUA KELAS --</option>
                        @foreach($kelasList as $kls)
                            <option value="{{ $kls->id }}" {{ request('kelas_id') == $kls->id ? 'selected' : '' }}>
                                KELAS {{ $kls->nama_kelas }}
                            </option>
                        @endforeach
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-2.5 flex items-center text-slate-400">
                        <i class="fa-solid fa-chevron-down text-[8px]"></i>
                    </div>
                </div>

                <div class="flex gap-2">
                    <button type="submit" class="bg-slate-800 hover:bg-slate-900 text-white px-4 py-1.5 rounded-lg text-[9px] font-black transition-all uppercase tracking-widest">
                        Filter
                    </button>
                    @if(request('search') || request('kelas_id'))
                    <a href="{{ route('admin.siswa.index') }}" class="bg-rose-50 text-rose-500 border border-rose-100 px-3 py-1.5 rounded-lg text-[9px] font-black hover:bg-rose-500 hover:text-white transition-all uppercase tracking-widest">
                        Reset
                    </a>
                    @endif
                </div>
            </form>

            <div class="text-[9px] font-black text-slate-400 uppercase tracking-widest bg-white border border-slate-200 px-3 py-1.5 rounded-full shadow-sm">
                <i class="fa-solid fa-users text-blue-500 mr-1"></i> Total: <span class="text-blue-600">{{ $siswa->total() }}</span> Siswa
            </div>
        </div>

        {{-- Table Content --}}
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50/50 text-slate-400 font-black uppercase text-[9px] tracking-widest border-b border-slate-100">
                        <th class="px-6 py-4 text-left w-16">No</th>
                        <th class="px-6 py-4 text-left">Informasi Siswa</th>
                        <th class="px-6 py-4 text-left">NISN</th>
                        <th class="px-6 py-4 text-left">Kelas</th>
                        <th class="px-6 py-4 text-left">Kontak Ortu</th>
                        <th class="px-6 py-4 text-center">Opsi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($siswa as $item)
                    <tr class="hover:bg-blue-50/20 transition-all group">
                        <td class="px-6 py-4">
                            <span class="text-[10px] font-bold text-slate-300 group-hover:text-blue-500 transition-colors italic">
                                #{{ str_pad(($siswa->currentPage() - 1) * $siswa->perPage() + $loop->iteration, 2, '0', STR_PAD_LEFT) }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center group-hover:bg-blue-600 group-hover:text-white transition-all duration-300 text-slate-400 border border-slate-200">
                                    <i class="fa-solid fa-user text-[10px]"></i>
                                </div>
                                <div>
                                    <p class="font-black text-slate-700 leading-tight group-hover:text-blue-600 transition-colors uppercase text-xs tracking-tight">{{ $item->nama }}</p>
                                    <p class="text-[9px] text-slate-400 font-bold mt-0.5 tracking-wider">{{ $item->user->email ?? '-' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-xs font-black text-slate-500 uppercase tracking-widest">
                            {{ $item->nisn }}
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[8px] font-black bg-blue-50 text-blue-600 border border-blue-100 uppercase tracking-widest">
                                {{ $item->dataKelas->nama_kelas ?? 'N/A' }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <i class="fa-brands fa-whatsapp text-emerald-500 text-xs"></i>
                                <span class="font-mono text-[10px] text-slate-500 font-bold tracking-tighter">{{ $item->no_wa_ortu }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex justify-center gap-1">
                                {{-- Edit --}}
                                <a href="{{ route('admin.siswa.edit', $item->id) }}" 
                                   class="w-8 h-8 flex items-center justify-center bg-amber-50 text-amber-500 hover:bg-amber-500 hover:text-white rounded-lg transition-all border border-amber-100" title="Edit Data">
                                    <i class="fa-solid fa-pen-to-square text-[10px]"></i>
                                </a>
                                {{-- Reset Password --}}
                                <form action="{{ route('admin.siswa.reset-password', $item->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button class="w-8 h-8 flex items-center justify-center bg-blue-50 text-blue-500 hover:bg-blue-500 hover:text-white rounded-lg transition-all border border-blue-100" 
                                            title="Reset Password" onclick="return confirm('Reset password siswa ini?')">
                                        <i class="fa-solid fa-key text-[10px]"></i>
                                    </button>
                                </form>
                                {{-- Hapus --}}
                                <form action="{{ route('admin.siswa.destroy', $item->id) }}" method="POST" class="inline">
                                    @csrf @method('DELETE')
                                    <button class="w-8 h-8 flex items-center justify-center bg-rose-50 text-rose-500 hover:bg-rose-500 hover:text-white rounded-lg transition-all border border-rose-100" 
                                            title="Hapus Data" onclick="return confirm('Yakin hapus data siswa ini secara permanen?')">
                                        <i class="fa-solid fa-trash-can text-[10px]"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-20 text-center">
                            <div class="flex flex-col items-center opacity-20">
                                <i class="fa-solid fa-users-slash text-4xl mb-4 text-slate-400"></i>
                                <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Data Siswa Tidak Ditemukan</span>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination Footer --}}
        <div class="p-4 bg-slate-50/20 border-t border-slate-50">
            <div class="custom-pagination text-[10px]">
                {{ $siswa->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>

<style>
    @keyframes fadeIn { from { opacity: 0; transform: translateY(5px); } to { opacity: 1; transform: translateY(0); } }
    .animate-fade-in { animation: fadeIn 0.4s ease-out forwards; }
    
    .custom-pagination nav svg { height: 14px; width: 14px; }
    .custom-pagination nav p { font-size: 9px !important; text-transform: uppercase; font-weight: 800; }
</style>
@endsection