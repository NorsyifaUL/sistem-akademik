{{-- Menggunakan layout dasar dari admin --}}
@extends('layouts.admin')

@section('content')
{{-- Container utama dengan padding dan efek animasi fade-in --}}
<div class="p-6 space-y-6 animate-fade-in max-w-7xl mx-auto">
    
    {{-- Header: Menampilkan judul halaman, informasi tahun ajaran, dan tombol tambah data --}}
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 px-1">
        <div>
            <h1 class="text-xl font-black text-slate-800 tracking-tight uppercase leading-none">
                Data <span class="text-blue-600">Guru</span>
            </h1>
            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-[0.2em] mt-1">SMAN 1 Jejangkit</p>
        </div>
        <div class="flex items-center gap-3">
            {{-- Menampilkan status tahun ajaran dan semester aktif dari database --}}
            <div class="hidden md:flex items-center gap-2 px-4 py-2 bg-blue-50 rounded-lg border border-blue-100">
                <span class="w-2 h-2 bg-blue-500 rounded-full animate-pulse"></span>
                <div class="text-[10px] font-black text-blue-600 uppercase tracking-wider">
                    TA: {{ $setting->tahun_ajaran ?? '-' }} (Sms {{ $setting->semester ?? '-' }})
                </div>
            </div>
            {{-- Tombol untuk mengarah ke form tambah guru --}}
            <a href="{{ route('admin.guru.create') }}" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg font-black text-[11px] transition-all shadow-sm uppercase tracking-widest active:scale-95">
                <i class="fa-solid fa-plus text-[10px]"></i> Tambah Guru
            </a>
        </div>
    </div>

    {{-- Main Card: Wadah utama untuk tabel data --}}
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="h-[4px] bg-blue-600 w-full"></div>
        
        {{-- Search Area: Formulir pencarian berdasarkan nama atau NIP --}}
        <div class="p-5 border-b border-slate-100 bg-slate-50/30">
            <form action="{{ route('admin.guru.index') }}" method="GET" class="flex gap-3">
                <div class="relative flex-1 md:flex-none">
                    <i class="fa-solid fa-magnifying-glass absolute left-3 top-3 text-slate-400 text-[11px]"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="CARI NAMA / NIP..." 
                           class="border border-slate-200 pl-9 pr-4 py-2.5 rounded-lg text-[12px] font-bold outline-none focus:ring-4 focus:ring-blue-500/5 focus:border-blue-500 w-full md:w-72 transition-all uppercase placeholder:text-slate-300">
                </div>
                <button type="submit" class="bg-slate-800 text-white px-6 py-2.5 rounded-lg text-[10px] font-black hover:bg-slate-900 transition-all uppercase tracking-widest">
                    Cari
                </button>
            </form>
        </div>

        {{-- Tabel Data Guru --}}
        <div class="overflow-x-auto w-full">
            <table class="w-full text-left min-w-[700px]"> 
                <thead>
                    <tr class="bg-slate-50 text-slate-500 font-black uppercase text-[10px] tracking-widest border-b border-slate-100">
                        <th class="px-6 py-4 text-center w-20">No</th>
                        <th class="px-6 py-4">Tenaga Pendidik / Mapel</th>
                        <th class="px-6 py-4">NIP / Identitas</th>
                        <th class="px-6 py-4">Akses Email</th>
                        <th class="px-6 py-4 text-center">Opsi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    {{-- Loop data guru dari controller --}}
                    @forelse($gurus as $g)
                    <tr class="hover:bg-blue-50/20 transition-all group">
                        <td class="px-6 py-4 text-center">
                            <span class="text-[12px] font-bold text-slate-400 group-hover:text-blue-500 transition-colors">
                                {{ $loop->iteration }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-col">
                                {{-- Nama Guru --}}
                                <span class="font-black text-slate-700 uppercase text-[13px] tracking-tight group-hover:text-blue-600 transition-colors">
                                    {{ $g->nama }}
                                </span>
                                {{-- Daftar mata pelajaran yang diampu (relasi many-to-many) --}}
                                <div class="flex items-center gap-1.5 mt-1">
                                    <i class="fa-solid fa-book-open text-[9px] text-blue-400"></i>
                                    <span class="text-[10px] text-blue-500 font-bold uppercase tracking-wide italic">
                                        {{ $g->mapels->pluck('nama_mapel')->unique()->implode(', ') ?: 'Belum Ada Mapel' }}
                                    </span>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            {{-- NIP guru --}}
                            <span class="inline-flex items-center px-3 py-1 rounded text-[10px] font-black bg-slate-100 text-slate-600 border border-slate-200 uppercase tracking-widest">
                                {{ $g->nip ?? 'NON-NIP' }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            {{-- Email login guru --}}
                            <span class="text-[12px] font-medium text-slate-500 italic">
                                {{ $g->user->email ?? '-' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            {{-- Bagian Opsi: Edit, Reset Password, dan Hapus Data --}}
                            <div class="flex justify-center gap-2">
                                {{-- Link ke halaman edit --}}
                                <a href="{{ route('admin.guru.edit',$g->id) }}" class="w-8 h-8 flex items-center justify-center bg-white text-amber-500 hover:bg-amber-500 hover:text-white rounded-lg transition-all border border-amber-200 shadow-sm" title="Edit Data">
                                    <i class="fa-solid fa-pen-to-square text-[11px]"></i>
                                </a>
                                {{-- Form untuk mereset password user --}}
                                <form action="{{ route('admin.guru.reset-password',$g->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="w-8 h-8 flex items-center justify-center bg-white text-blue-500 hover:bg-blue-500 hover:text-white rounded-lg transition-all border border-blue-200 shadow-sm" onclick="return confirm('Reset password guru ini?')" title="Reset Password">
                                        <i class="fa-solid fa-key text-[11px]"></i>
                                    </button>
                                </form>
                                {{-- Form untuk menghapus data guru --}}
                                <form action="{{ route('admin.guru.destroy',$g->id) }}" method="POST" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="w-8 h-8 flex items-center justify-center bg-white text-rose-500 hover:bg-rose-500 hover:text-white rounded-lg transition-all border border-rose-200 shadow-sm" onclick="return confirm('Hapus data guru?')" title="Hapus Data">
                                        <i class="fa-solid fa-trash-can text-[11px]"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    {{-- Pesan jika tidak ada data ditemukan --}}
                    <tr>
                        <td colspan="5" class="py-20 text-center">
                            <div class="flex flex-col items-center opacity-30">
                                <i class="fa-solid fa-user-slash text-5xl mb-4 text-slate-300"></i>
                                <span class="text-[11px] font-black text-slate-500 uppercase tracking-widest">Data Kosong</span>
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
    /* CSS Kustom untuk animasi muncul halus dan perataan tabel */
    @keyframes fadeIn { from { opacity: 0; transform: translateY(5px); } to { opacity: 1; transform: translateY(0); } }
    .animate-fade-in { animation: fadeIn 0.4s ease-out forwards; }
    table { border-collapse: separate; border-spacing: 0; }
</style>
@endsection