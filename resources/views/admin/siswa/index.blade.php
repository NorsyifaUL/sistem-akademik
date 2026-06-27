@extends('layouts.admin')

@section('content')
<div class="p-4 space-y-6 animate-fade-in">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 px-1">
        <div>
            <h1 class="text-xl font-black text-slate-800 tracking-tight uppercase leading-none">
                Data <span class="text-blue-600">Siswa</span>
            </h1>
            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-[0.2em] mt-1.5">SMAN 1 Jejangkit</p>
        </div>
        <a href="{{ route('admin.siswa.create') }}" class="inline-flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-lg font-black text-[11px] transition-all shadow-sm uppercase tracking-widest active:scale-95 w-max">
            <i class="fa-solid fa-user-plus text-[10px]"></i> Tambah Siswa
        </a>
    </div>

    {{-- Main Card --}}
    <div class="w-full bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="h-[4px] bg-blue-600 w-full"></div>
        
        {{-- Filter Form --}}
        <div class="p-4 border-b border-slate-50 bg-slate-50/30">
            <form action="{{ route('admin.siswa.index') }}" method="GET" class="flex flex-col md:flex-row gap-3">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="CARI NAMA SISWA..." class="w-full md:w-72 px-4 py-2.5 bg-white border border-slate-200 rounded-lg text-[11px] font-bold outline-none focus:border-blue-500 uppercase">
                
                <select name="kelas_id" class="w-full md:w-56 px-4 py-2.5 bg-white border border-slate-200 rounded-lg text-[11px] font-bold outline-none focus:border-blue-500 uppercase">
                    <option value="">SEMUA KELAS</option>
                    @foreach($semuaKelas as $kelas)
                        <option value="{{ $kelas->id }}" {{ request('kelas_id') == $kelas->id ? 'selected' : '' }}>
                            {{ $kelas->nama_kelas }}
                        </option>
                    @endforeach
                </select>

                <button type="submit" class="bg-slate-800 text-white px-6 py-2.5 rounded-lg text-[10px] font-black uppercase tracking-widest hover:bg-slate-900 transition-all">Filter</button>
                <a href="{{ route('admin.siswa.index') }}" class="bg-rose-500 text-white px-6 py-2.5 rounded-lg text-[10px] font-black uppercase tracking-widest hover:bg-rose-600 transition-all flex items-center justify-center">Reset</a>
            </form>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="w-full text-left min-w-[1000px]">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 font-black uppercase text-[10px] tracking-widest border-b border-slate-100">
                        <th class="px-6 py-4 text-center w-16">No</th>
                        <th class="px-6 py-4">Nama Siswa</th>
                        <th class="px-6 py-4">NISN</th>
                        <th class="px-6 py-4">Kelas</th>
                        <th class="px-6 py-4">Email</th>
                        <th class="px-6 py-4">No. WA Ortu</th>
                        <th class="px-6 py-4 text-center">Opsi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($siswa as $item)
                    <tr class="hover:bg-blue-50/20 transition-all">
                        <td class="px-6 py-4 text-center text-[11px] font-bold text-slate-400">#{{ $loop->iteration }}</td>
                        <td class="px-6 py-4 font-black text-slate-700 text-[13px] uppercase">{{ $item->nama }}</td>
                        <td class="px-6 py-4 text-[12px] font-black text-slate-500">{{ $item->nisn }}</td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 rounded text-[9px] font-black bg-blue-50 text-blue-600 border border-blue-100 uppercase">{{ $item->dataKelas->nama_kelas ?? 'N/A' }}</span>
                        </td>
                        <td class="px-6 py-4 text-[12px] font-bold text-slate-500">{{ $item->user->email ?? '-' }}</td>
                        <td class="px-6 py-4 text-[12px] font-bold text-slate-500">{{ $item->no_wa_ortu ?? '-' }}</td>
                        <td class="px-6 py-4">
                            <div class="flex justify-center gap-2">
                                <a href="{{ route('admin.siswa.edit', $item->id) }}" class="w-8 h-8 flex items-center justify-center bg-white text-amber-500 hover:bg-amber-500 hover:text-white rounded-lg border border-amber-100 transition-all"><i class="fa-solid fa-pen-to-square text-[11px]"></i></a>
                                <form action="{{ route('admin.siswa.reset-password', $item->id) }}" method="POST">
                                    @csrf
                                    <button class="w-8 h-8 flex items-center justify-center bg-white text-blue-500 hover:bg-blue-500 hover:text-white rounded-lg border border-blue-100 transition-all" onclick="return confirm('Reset password?')"><i class="fa-solid fa-key text-[11px]"></i></button>
                                </form>
                                <form action="{{ route('admin.siswa.destroy', $item->id) }}" method="POST">
                                    @csrf @method('DELETE')
                                    <button class="w-8 h-8 flex items-center justify-center bg-white text-rose-500 hover:bg-rose-500 hover:text-white rounded-lg border border-rose-100 transition-all" onclick="return confirm('Hapus data?')"><i class="fa-solid fa-trash-can text-[11px]"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="py-16 text-center text-[11px] text-slate-400 uppercase font-black">Data Kosong</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 bg-slate-50/20 border-t border-slate-50 custom-pagination">
            {{ $siswa->links('pagination::tailwind') }}
        </div>
    </div>
</div>

<style>
    .animate-fade-in { animation: fadeIn 0.4s ease-out forwards; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(5px); } to { opacity: 1; transform: translateY(0); } }
    .custom-pagination nav { display: flex; justify-content: center; align-items: center; }
    .custom-pagination svg { width: 16px; height: 16px; }
    .custom-pagination span[aria-current="page"] span { background-color: #2563eb !important; color: white !important; }
    .custom-pagination [aria-label="Previous"], .custom-pagination [aria-label="Next"] { font-size: 10px !important; }
</style>
@endsection