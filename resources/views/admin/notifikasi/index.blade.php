@extends('layouts.admin')

@section('content')
<div class="p-4 space-y-4 animate-fade-in">
    
    {{-- HEADER SECTION RAMPING --}}
    <div class="flex items-end justify-between px-1">
        <div>
            <h2 class="text-base font-black text-slate-800 tracking-tight uppercase leading-none flex items-center gap-2">
                <i class="fa-solid fa-box-archive text-indigo-600 text-xs"></i>
                Riwayat <span class="text-indigo-600">Notifikasi</span>
            </h2>
            <p class="text-[8px] text-slate-400 font-bold uppercase tracking-[0.1em] mt-1">Rekaman sistem otomatis kategori ALPA</p>
        </div>

        <div class="hidden md:flex items-center gap-2 text-[8px] font-bold text-slate-400 uppercase tracking-widest bg-white px-3 py-1.5 rounded-lg border border-slate-200 shadow-sm">
            <span class="h-1.5 w-1.5 bg-indigo-500 rounded-full animate-pulse"></span>
            System Online
        </div>
    </div>

    {{-- MAIN CARD --}}
    <div class="w-full bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="h-[3px] bg-indigo-600 w-full"></div>
        
        {{-- Toolbar Filter --}}
        <div class="px-5 py-3 border-b border-slate-100 bg-slate-50/30 flex flex-col md:flex-row justify-between items-center gap-3">
            <form action="{{ route('admin.notifikasi.index') }}" method="GET" class="flex items-center gap-2 w-full md:w-auto">
                <div class="relative w-full md:w-48 group">
                    <select name="kelas" onchange="this.form.submit()" 
                        class="appearance-none w-full bg-white border border-slate-200 text-slate-700 text-[10px] font-bold uppercase rounded-lg focus:ring-2 focus:ring-indigo-500 block pl-3 pr-8 py-1.5 transition-all outline-none shadow-sm cursor-pointer tracking-wider">
                        <option value="">-- Semua Kelas --</option>
                        @foreach($kelasList as $k)
                            <option value="{{ $k->nama_kelas }}" {{ request('kelas') == $k->nama_kelas ? 'selected' : '' }}>
                                Kelas {{ $k->nama_kelas }}
                            </option>
                        @endforeach
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-indigo-500">
                        <i class="fa-solid fa-chevron-down text-[8px]"></i>
                    </div>
                </div>

                @if(request('kelas'))
                    <a href="{{ route('admin.notifikasi.index') }}" 
                       class="bg-rose-500 hover:bg-rose-600 text-white px-3 py-1.5 rounded-lg font-black text-[9px] uppercase tracking-widest transition-all active:scale-95 flex items-center gap-1">
                        <i class="fa-solid fa-circle-xmark text-[8px]"></i>
                        Reset
                    </a>
                @endif
            </form>

            <div class="flex items-center gap-2">
                <i class="fa-solid fa-database text-slate-300 text-[9px]"></i>
                <span class="text-[8px] font-bold text-slate-400 uppercase tracking-[0.2em]">Arsip Data Log</span>
            </div>
        </div>

        {{-- Tabel Log --}}
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="text-[9px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-50">
                        <th class="px-6 py-3 bg-slate-50/50">Waktu</th>
                        <th class="px-6 py-3 bg-slate-50/50">Siswa & Kelas</th>
                        <th class="px-6 py-3 bg-slate-50/50">Pesan Notifikasi</th>
                        <th class="px-6 py-3 bg-slate-50/50 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($notifikasis as $n)
                        <tr class="group hover:bg-slate-50/50 transition-all">
                            <td class="px-6 py-3">
                                <div class="flex flex-col">
                                    <span class="text-xs font-black text-slate-700 tracking-tight leading-none">{{ $n->created_at->format('H:i') }}</span>
                                    <span class="text-[8px] font-bold text-slate-400 uppercase mt-1">{{ $n->created_at->translatedFormat('d M Y') }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="h-7 w-7 bg-indigo-600 rounded-lg flex items-center justify-center text-white font-black text-[8px] shadow-sm">
                                        {{ $n->absensi->siswa->dataKelas->nama_kelas ?? '?' }}
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-[10px] font-black text-slate-700 uppercase tracking-tight leading-none">{{ $n->absensi->siswa->nama ?? 'Siswa' }}</span>
                                        <span class="text-[8px] text-slate-400 font-bold uppercase tracking-widest mt-1">NISN: {{ $n->absensi->siswa->nisn ?? '-' }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-3">
                                <div class="bg-slate-50 border border-slate-100 px-3 py-2 rounded-lg text-slate-500 text-[10px] italic leading-relaxed group-hover:bg-white group-hover:border-indigo-100 transition-all">
                                    "{{ $n->isi_pesan }}"
                                </div>
                            </td>
                            <td class="px-6 py-3 text-center">
                                <form action="{{ route('admin.notifikasi.destroy', $n->id) }}" method="POST" onsubmit="return confirm('Hapus log notifikasi ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="h-8 w-8 bg-rose-50 text-rose-500 hover:bg-rose-500 hover:text-white rounded-lg transition-all flex items-center justify-center group/btn shadow-sm">
                                        <i class="fa-solid fa-trash-can text-[10px]"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center gap-2 opacity-20">
                                    <i class="fa-solid fa-folder-open text-3xl"></i>
                                    <span class="text-[10px] font-black uppercase tracking-[0.2em]">Tidak Ada Log</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- FOOTER CARD --}}
        <div class="px-6 py-3 bg-slate-50/30 border-t border-slate-100 flex flex-col md:flex-row justify-between items-center gap-3">
            <div class="flex items-center gap-3">
                <div class="h-6 w-[2px] bg-indigo-500 rounded-full"></div>
                <div>
                    <p class="text-[8px] font-bold text-slate-400 uppercase tracking-widest leading-none">Total Log</p>
                    <p class="text-[11px] font-black text-slate-800 tracking-tight">{{ $notifikasis->total() }} <span class="font-medium text-slate-400 text-[9px]">Entri Terarsip</span></p>
                </div>
            </div>
            <div class="scale-90 origin-right">
                {{ $notifikasis->appends(request()->query())->links() }}
            </div>
        </div>
    </div>

    <p class="text-center text-[8px] font-bold text-slate-300 uppercase tracking-[0.3em] pt-2">SIAKAD SYSTEM &bull; v2.0</p>
</div>
@endsection