@extends('layouts.guru')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8 font-academic">
    {{-- Breadcrumb --}}
    <nav class="flex mb-5 text-gray-500 text-[9px] uppercase tracking-[0.2em] font-black" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-2">
            <li><a href="{{ route('guru.dashboard') }}" class="hover:text-green-700 transition-colors">Dashboard</a></li>
            <li><span class="text-gray-300">/</span></li>
            <li class="text-gray-800 tracking-tighter">Rekapitulasi</li>
        </ol>
    </nav>

    {{-- ALERT STATUS --}}
    @if(session('success'))
        <div class="mb-4 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 text-[10px] font-black uppercase tracking-widest animate-pulse">
            {{ session('success') }}
        </div>
    @endif

    {{-- CARD UTAMA --}}
    <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
        <div class="h-1.5 bg-green-600 w-full"></div>

        {{-- 1. HEADER (Tombol Cetak Sudah Dihapus) --}}
        <div class="p-8 border-b border-gray-100">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                <div>
                    <h2 class="text-2xl font-black text-gray-800 uppercase tracking-tighter italic">Rekapitulasi Presensi</h2>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em] mt-1">Laporan Kehadiran Siswa — <span class="text-green-600 italic">Mode Guru</span></p>
                </div>
                {{-- Tombol cetak dihilangkan agar hanya Admin yang bisa mengeksekusi laporan final --}}
            </div>
        </div>

        {{-- 2. ULTRA-CLEAN FILTER --}}
        <div class="px-8 py-6 bg-gray-50/50 border-b border-gray-100">
            <form action="{{ route('guru.absensi.rekap') }}" method="GET" class="flex flex-wrap items-end gap-4">
                <div class="w-32">
                    <label class="block text-[9px] text-gray-400 uppercase font-black tracking-widest mb-2 ml-1 italic">Mode Cari</label>
                    <select id="modeSelect" name="mode" class="w-full bg-white border border-gray-200 rounded-xl px-3 py-2.5 text-[11px] font-bold text-green-700 outline-none focus:ring-2 focus:ring-green-500 shadow-sm transition-all">
                        <option value="daily" {{ request('mode') == 'daily' ? 'selected' : '' }}>Harian</option>
                        <option value="monthly" {{ request('mode') == 'monthly' ? 'selected' : '' }}>Bulanan</option>
                    </select>
                </div>

                <div class="w-44" id="dailyInputWrapper">
                    <label class="block text-[9px] text-gray-400 uppercase font-black tracking-widest mb-2 ml-1 italic">Pilih Tanggal</label>
                    <input type="date" name="tanggal" value="{{ request('tanggal') ?? date('Y-m-d') }}" class="w-full bg-white border border-gray-200 rounded-xl px-3 py-2.5 text-[11px] font-bold text-gray-700 outline-none focus:ring-2 focus:ring-green-500 shadow-sm">
                </div>

                <div class="w-44 hidden" id="monthlyInputWrapper">
                    <label class="block text-[9px] text-gray-400 uppercase font-black tracking-widest mb-2 ml-1 italic">Pilih Bulan</label>
                    <select name="bulan" class="w-full bg-white border border-gray-200 rounded-xl px-3 py-2.5 text-[11px] font-bold text-gray-700 outline-none focus:ring-2 focus:ring-green-500 shadow-sm">
                        @for ($i = 1; $i <= 12; $i++)
                            @php $m = str_pad($i, 2, '0', STR_PAD_LEFT); @endphp
                            <option value="{{ $m }}" {{ (request('bulan') ?? date('m')) == $m ? 'selected' : '' }}>
                                {{ Carbon\Carbon::create()->month($i)->translatedFormat('F') }}
                            </option>
                        @endfor
                    </select>
                </div>

                <div class="w-32">
                    <label class="block text-[9px] text-gray-400 uppercase font-black tracking-widest mb-2 ml-1 italic">Kelas</label>
                    <select name="kelas" class="w-full bg-white border border-gray-200 rounded-xl px-3 py-2.5 text-[11px] font-bold text-gray-700 outline-none focus:ring-2 focus:ring-green-500 shadow-sm">
                        <option value="">Semua</option>
                        @foreach($kelasList as $k)
                            <option value="{{ $k->kelas }}" {{ request('kelas') == $k->kelas ? 'selected' : '' }}>{{ $k->kelas }}</option>
                        @endforeach
                    </select>
                </div>

                <button type="submit" class="bg-green-700 hover:bg-green-800 text-white px-8 py-2.5 rounded-xl font-black text-[10px] uppercase tracking-widest transition-all shadow-md active:scale-95 h-[42px]">
                    Filter
                </button>

                <div class="flex-grow"></div>

                <div class="w-full lg:w-48 text-right">
                    <label class="block text-[9px] text-gray-400 uppercase font-black tracking-widest mb-2 mr-1 italic">Cari Nama</label>
                    <input type="text" id="searchInput" placeholder="KETIK NAMA..." class="w-full bg-white border border-gray-200 rounded-xl px-4 py-2.5 text-[10px] font-black text-green-700 placeholder-gray-300 outline-none shadow-sm focus:ring-2 focus:ring-green-500 transition-all">
                </div>
            </form>
        </div>

        {{-- 3. TABLE --}}
        <div class="overflow-x-auto">
            <table class="w-full border-collapse text-left" id="absensiTable">
                <thead>
                    <tr class="bg-white text-[10px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-100">
                        <th class="px-8 py-5 text-center w-16">No</th>
                        <th class="px-8 py-5">Identitas Siswa</th>
                        <th class="px-8 py-5 text-center">Status Absen</th>
                        <th class="px-8 py-5 text-center">Notifikasi WA</th>
                        <th class="px-8 py-5 text-center">Tgl Presensi</th>
                        <th class="px-8 py-5 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50" id="tableBody">
                    @php $no = 1; @endphp
                    @forelse($rekaps as $r)
                        @if(!$r->siswa) @continue @endif

                        <tr class="hover:bg-green-50/5 transition-all group row-siswa">
                            <td class="px-8 py-6 text-center text-gray-300 font-black text-xs italic group-hover:text-green-600">
                                {{ str_pad($no++, 2, '0', STR_PAD_LEFT) }}
                            </td>
                            <td class="px-8 py-6">
                                <div class="flex flex-col">
                                    <span class="text-sm font-black text-gray-800 uppercase nama-siswa">{{ $r->siswa->nama }}</span>
                                    <span class="text-[9px] text-gray-400 font-black tracking-widest uppercase italic">NISN: {{ $r->siswa->nisn }} • Kelas {{ $r->siswa->kelas }}</span>
                                </div>
                            </td>
                            <td class="px-8 py-6 text-center">
                                @php 
                                    $st = strtoupper($r->status);
                                    $color = match($st) {
                                        'HADIR', 'H' => 'bg-green-50 text-green-600 border-green-100',
                                        'ALPA', 'A' => 'bg-red-50 text-red-600 border-red-100',
                                        'SAKIT', 'S' => 'bg-yellow-50 text-yellow-600 border-yellow-100',
                                        default => 'bg-blue-50 text-blue-600 border-blue-100',
                                    };
                                @endphp
                                <span class="px-3 py-1.5 rounded-full text-[9px] font-black uppercase border {{ $color }}">{{ $st }}</span>
                            </td>

                            <td class="px-8 py-6 text-center">
                                @if(in_array(strtoupper($r->status), ['ALPA', 'A']))
                                    <div class="flex flex-col items-center gap-1">
                                        @if($r->status_wa == 'sent')
                                            <i class="fas fa-check-double text-green-500 text-[10px]"></i>
                                            <span class="text-[8px] font-black text-green-600 uppercase tracking-tighter">Terkirim</span>
                                        @elseif($r->status_wa == 'failed')
                                            <i class="fas fa-exclamation-circle text-red-500 text-[10px]"></i>
                                            <span class="text-[8px] font-black text-red-600 uppercase tracking-tighter mb-1">Gagal</span>
                                            
                                            <form action="{{ route('guru.absensi.resend', $r->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="bg-red-100 text-red-700 px-2 py-0.5 rounded text-[7px] font-black uppercase tracking-tighter hover:bg-red-700 hover:text-white transition-all shadow-sm">
                                                    Resend <i class="fas fa-sync-alt ml-0.5"></i>
                                                </button>
                                            </form>
                                        @else
                                            <i class="fas fa-clock text-gray-300 text-[10px]"></i>
                                            <span class="text-[8px] font-black text-gray-300 uppercase tracking-tighter">Pending</span>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-gray-200">—</span>
                                @endif
                            </td>

                            <td class="px-8 py-6 text-center text-[11px] font-black text-gray-500">
                                {{ \Carbon\Carbon::parse($r->tanggal)->translatedFormat('d/m/Y') }}
                            </td>
                            <td class="px-8 py-6 text-center">
                                <div class="flex justify-center gap-2">
                                    <a href="{{ route('guru.absensi.manage.edit', $r->id) }}" class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center hover:bg-emerald-600 hover:text-white transition-all shadow-sm">
                                        <i class="fas fa-edit text-[10px]"></i>
                                    </a>
                                    <form action="{{ route('guru.absensi.manage.destroy', $r->id) }}" method="POST" onsubmit="return confirm('Hapus data presensi ini?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="w-8 h-8 rounded-lg bg-red-50 text-red-600 flex items-center justify-center hover:bg-red-600 hover:text-white transition-all shadow-sm">
                                            <i class="fas fa-trash text-[10px]"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-8 py-20 text-center text-[10px] font-black text-gray-300 uppercase italic tracking-widest">Tidak ada data untuk periode ini</td>
                        </tr>
                    @endforelse
                    <tr id="noResultsRow" style="display: none;">
                        <td colspan="6" class="px-8 py-20 text-center text-[10px] font-black text-gray-400 uppercase italic">Siswa tidak ditemukan</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- SCRIPT TETAP SAMA --}}
<script>
    const modeSelect = document.getElementById('modeSelect');
    const dailyWrapper = document.getElementById('dailyInputWrapper');
    const monthlyWrapper = document.getElementById('monthlyInputWrapper');

    function toggleFilterMode() {
        if (modeSelect.value === 'monthly') {
            dailyWrapper.classList.add('hidden');
            monthlyWrapper.classList.remove('hidden');
        } else {
            dailyWrapper.classList.remove('hidden');
            monthlyWrapper.classList.add('hidden');
        }
    }

    modeSelect.addEventListener('change', toggleFilterMode);
    window.addEventListener('load', toggleFilterMode);

    document.getElementById('searchInput').addEventListener('keyup', function() {
        let val = this.value.toUpperCase().trim();
        let rows = document.querySelectorAll('.row-siswa');
        let noResultsRow = document.getElementById('noResultsRow');
        let foundAny = false;
        
        rows.forEach(row => {
            let nama = row.querySelector('.nama-siswa').innerText.toUpperCase();
            if (nama.includes(val)) {
                row.style.display = "";
                foundAny = true;
            } else {
                row.style.display = "none";
            }
        });
        noResultsRow.style.display = (val !== "" && !foundAny) ? "" : "none";
    });
</script>
@endsection