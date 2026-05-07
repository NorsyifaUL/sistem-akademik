@extends('layouts.admin')

@section('content')
<div class="p-8 space-y-6 animate-fade-in bg-gray-50/50 min-h-screen">
    
    {{-- HEADER SECTION --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-black text-slate-800 tracking-tight flex items-center gap-3">
                <div class="h-10 w-10 bg-indigo-600 rounded-xl flex items-center justify-center shadow-lg shadow-indigo-200">
                    <i class="fa-solid fa-box-archive text-white text-xs"></i>
                </div>
                Log Riwayat Notifikasi
            </h2>
            <p class="text-sm text-slate-500 mt-2">Daftar rekaman notifikasi sistem kategori ALPA.</p>
        </div>

        <div class="flex items-center gap-2 text-[11px] font-bold text-slate-400 uppercase tracking-widest bg-white px-4 py-2 rounded-full border border-gray-200 shadow-sm">
            <span class="h-2 w-2 bg-indigo-500 rounded-full animate-pulse"></span>
            System Status: Online
        </div>
    </div>

    {{-- MAIN CARD --}}
    <div class="bg-white rounded-[2rem] border border-gray-200 shadow-sm overflow-hidden border-t-4 border-t-indigo-500">
        
        {{-- Toolbar Filter --}}
        <div class="p-6 border-b border-gray-100 bg-slate-50/30 flex flex-col md:flex-row justify-between items-center gap-4">
            <form action="{{ route('admin.notifikasi.index') }}" method="GET" class="flex items-center gap-3 w-full md:w-auto">
                <div class="relative w-full md:w-64 group">
                    <select name="kelas" onchange="this.form.submit()" 
                        class="appearance-none w-full bg-white border border-gray-200 text-slate-700 text-sm rounded-xl focus:ring-2 focus:ring-indigo-500 block pl-4 pr-10 py-2.5 transition-all outline-none shadow-sm cursor-pointer">
                        
                        <option value="">Pilih Filter Kelas</option>
                        
                        @foreach($kelasList as $k)
                            {{-- Menggunakan $k->nama_kelas sesuai gambar database --}}
                            <option value="{{ $k->nama_kelas }}" {{ request('kelas') == $k->nama_kelas ? 'selected' : '' }}>
                                Kelas {{ $k->nama_kelas }}
                            </option>
                        @endforeach
                        
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-indigo-500">
                        <i class="fa-solid fa-chevron-down text-[10px]"></i>
                    </div>
                </div>

                @if(request('kelas'))
                    <a href="{{ route('admin.notifikasi.index') }}" 
                       class="flex items-center gap-2 text-[10px] font-bold text-red-500 hover:bg-red-50 px-4 py-2.5 rounded-xl transition-all uppercase tracking-widest border border-transparent hover:border-red-100">
                        <i class="fa-solid fa-circle-xmark"></i>
                        Reset Filter
                    </a>
                @endif
            </form>

            <div class="hidden md:flex items-center gap-2">
                <i class="fa-solid fa-database text-slate-300 text-[10px]"></i>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">Data Terarsip</span>
            </div>
        </div>

        {{-- Tabel Log --}}
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="text-[11px] font-bold text-slate-400 uppercase tracking-widest border-b border-gray-100">
                        <th class="px-8 py-5 text-indigo-600/70">Waktu & Tanggal</th>
                        <th class="px-8 py-5 text-indigo-600/70">Siswa & Kelas</th>
                        <th class="px-8 py-5 text-indigo-600/70 border-r border-gray-50">Isi Notifikasi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($notifikasis as $n)
                        <tr class="group hover:bg-indigo-50/10 transition-all">
                            <td class="px-8 py-6">
                                <div class="flex flex-col">
                                    <span class="text-sm font-black text-slate-700 tracking-tight">{{ $n->created_at->format('H:i') }}</span>
                                    <span class="text-[10px] font-bold text-slate-400 uppercase mt-0.5">{{ $n->created_at->translatedFormat('d M Y') }}</span>
                                </div>
                            </td>
                            <td class="px-8 py-6">
                                <div class="flex items-center gap-4">
                                    <div class="h-9 w-9 bg-indigo-600 rounded-xl flex items-center justify-center text-white font-black text-[10px] shadow-md shadow-indigo-100">
                                        {{ $n->absensi->siswa->kelas ?? '?' }}
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-sm font-bold text-slate-800 uppercase tracking-tight">{{ $n->absensi->siswa->nama ?? 'Siswa' }}</span>
                                        <span class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">NISN: {{ $n->absensi->siswa->nisn ?? '-' }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-6">
                                <div class="max-w-3xl bg-slate-50 border border-slate-100 p-3 rounded-xl group-hover:bg-white group-hover:border-indigo-100 transition-all italic text-slate-600 text-sm">
                                    "{{ $n->isi_pesan }}"
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-24 text-center text-slate-300 uppercase text-xs font-bold tracking-widest">
                                <div class="flex flex-col items-center gap-4">
                                    <i class="fa-solid fa-folder-open text-4xl opacity-20"></i>
                                    <span>Belum ada notifikasi alpa untuk kelas ini</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- FOOTER CARD --}}
        <div class="px-8 py-6 bg-slate-50/50 border-t border-gray-100 flex flex-col md:flex-row justify-between items-center gap-4">
            <div class="flex items-center gap-3">
                <div class="h-8 w-1 bg-indigo-500 rounded-full"></div>
                <div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest leading-none">Total Notifikasi</p>
                    <p class="text-sm font-black text-slate-800 tracking-tight">{{ $notifikasis->total() }} <span class="font-medium text-slate-500 text-[11px]">Entri Log</span></p>
                </div>
            </div>
            <div class="flex-shrink-0">
                {{ $notifikasis->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>
@endsection