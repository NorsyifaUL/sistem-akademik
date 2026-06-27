@extends('layouts.admin')

@section('content')
<div class="p-4 space-y-4 animate-fade-in">
    
    {{-- HEADER SECTION --}}
    <div class="flex items-end justify-between px-1">
        <div>
            <h2 class="text-lg font-black text-slate-800 tracking-tight uppercase leading-none flex items-center gap-2">
                <i class="fa-solid fa-box-archive text-indigo-600 text-sm"></i>
                Riwayat <span class="text-indigo-600">Notifikasi</span>
            </h2>
            <p class="text-[9px] text-slate-400 font-bold uppercase tracking-[0.2em] mt-1.5">Rekaman sistem otomatis kategori ALPA</p>
        </div>

        <div class="hidden md:flex items-center gap-2 text-[9px] font-bold text-slate-400 uppercase tracking-widest bg-white px-3 py-1.5 rounded-lg border border-slate-200 shadow-sm">
            <span class="h-1.5 w-1.5 bg-indigo-500 rounded-full animate-pulse"></span>
            System Online
        </div>
    </div>

    {{-- MAIN CARD --}}
    <div class="w-full bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="h-[3px] bg-indigo-600 w-full"></div>
        
        {{-- Toolbar Filter --}}
        <div class="px-5 py-4 border-b border-slate-100 bg-slate-50/50">
            <form action="{{ route('admin.notifikasi.index') }}" method="GET" class="flex flex-wrap items-end gap-4 w-full">
                
                <div class="flex flex-col gap-1.5 w-full md:w-36">
                    <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Dari Tanggal</label>
                    <input type="date" name="tanggal_awal" value="{{ request('tanggal_awal') }}"
                        class="w-full bg-white border border-slate-200 text-slate-700 text-[11px] font-bold rounded-lg px-3 py-2 outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div class="flex flex-col gap-1.5 w-full md:w-36">
                    <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Sampai Tanggal</label>
                    <input type="date" name="tanggal_akhir" value="{{ request('tanggal_akhir') }}"
                        class="w-full bg-white border border-slate-200 text-slate-700 text-[11px] font-bold rounded-lg px-3 py-2 outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <div class="flex flex-col gap-1.5 w-full md:w-28">
                    <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Bulan</label>
                    <select name="bulan" class="w-full bg-white border border-slate-200 text-slate-700 text-[11px] font-bold rounded-lg px-3 py-2 outline-none cursor-pointer">
                        <option value="">Semua</option>
                        @foreach([1=>'Jan',2=>'Feb',3=>'Mar',4=>'Apr',5=>'Mei',6=>'Jun',7=>'Jul',8=>'Agu',9=>'Sep',10=>'Okt',11=>'Nov',12=>'Des'] as $index => $nama)
                            <option value="{{ $index }}" {{ request('bulan') == $index ? 'selected' : '' }}>{{ $nama }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex flex-col gap-1.5 w-full md:w-36">
                    <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Kelas</label>
                    <select name="kelas" class="w-full bg-white border border-slate-200 text-slate-700 text-[11px] font-bold rounded-lg px-3 py-2 outline-none cursor-pointer">
                        <option value="">Semua Kelas</option>
                        @foreach($kelasList as $k)
                            <option value="{{ $k->nama_kelas }}" {{ request('kelas') == $k->nama_kelas ? 'selected' : '' }}>{{ $k->nama_kelas }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-center gap-2 mt-auto">
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2 rounded-lg font-black text-[10px] uppercase tracking-widest transition-all">
                        <i class="fa-solid fa-filter mr-1"></i> Filter
                    </button>
                    @if(request()->anyFilled(['tanggal_awal', 'tanggal_akhir', 'bulan', 'kelas']))
                        <a href="{{ route('admin.notifikasi.index') }}" class="bg-rose-500 hover:bg-rose-600 text-white px-4 py-2 rounded-lg font-black text-[10px] uppercase tracking-widest transition-all">
                            <i class="fa-solid fa-xmark"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- Tabel Log --}}
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-50">
                        <th class="px-6 py-4 bg-slate-50/50">Waktu</th>
                        <th class="px-6 py-4 bg-slate-50/50">Siswa & Kelas</th>
                        <th class="px-6 py-4 bg-slate-50/50">Pesan Notifikasi</th>
                        <th class="px-6 py-4 bg-slate-50/50 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($notifikasis as $n)
                        <tr class="group hover:bg-slate-50/50 transition-all">
                            <td class="px-6 py-4">
                                <div class="flex flex-col">
                                    <span class="text-[11px] font-black text-slate-700 tracking-tight leading-none">{{ $n->created_at->format('H:i') }}</span>
                                    <span class="text-[9px] font-bold text-slate-400 uppercase mt-1.5">{{ $n->created_at->translatedFormat('d M Y') }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="h-8 w-8 bg-indigo-600 rounded-lg flex items-center justify-center text-white font-black text-[10px] shadow-sm">
                                        {{ $n->absensi->siswa->dataKelas->nama_kelas ?? '?' }}
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-[11px] font-black text-slate-700 uppercase tracking-tight leading-none">{{ $n->absensi->siswa->nama ?? 'Siswa' }}</span>
                                        <span class="text-[9px] text-slate-400 font-bold uppercase tracking-widest mt-1.5">NISN: {{ $n->absensi->siswa->nisn ?? '-' }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="bg-slate-50 border border-slate-100 px-4 py-3 rounded-lg text-slate-600 text-[11px] leading-relaxed group-hover:bg-white group-hover:border-indigo-100 transition-all">
                                    "{{ $n->isi_pesan }}"
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <form action="{{ route('admin.notifikasi.destroy', $n->id) }}" method="POST" onsubmit="return confirm('Hapus log notifikasi ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="h-9 w-9 bg-rose-50 text-rose-500 hover:bg-rose-500 hover:text-white rounded-lg transition-all flex items-center justify-center shadow-sm">
                                        <i class="fa-solid fa-trash-can text-[11px]"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-16 text-center text-[10px] font-black text-slate-300 uppercase tracking-[0.2em]">Tidak Ada Log Ditemukan</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Footer Card --}}
        <div class="px-6 py-4 bg-slate-50/30 border-t border-slate-100 flex flex-col md:flex-row justify-between items-center gap-3">
            <div class="flex items-center gap-3">
                <div class="h-6 w-[3px] bg-indigo-500 rounded-full"></div>
                <div>
                    <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest leading-none">Total Entri</p>
                    <p class="text-[12px] font-black text-slate-800 tracking-tight mt-0.5">{{ $notifikasis->total() }} <span class="font-medium text-slate-400">Record</span></p>
                </div>
            </div>
            <div class="scale-95 origin-right">
                {{ $notifikasis->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>
@endsection