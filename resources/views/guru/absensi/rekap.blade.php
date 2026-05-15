@extends('layouts.guru')

@section('content')
<div class="max-w-7xl mx-auto px-2 py-3 font-academic">
    
    {{-- ALERT STATUS --}}
    @if(session('success'))
        <div class="mb-4 p-4 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-700 text-[10px] font-black uppercase tracking-widest animate-pulse flex items-center gap-3 rounded-r-xl shadow-sm">
            <i class="fas fa-check-circle text-lg"></i>
            {{ session('success') }}
        </div>
    @endif

    {{-- CARD UTAMA --}}
    <div class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden">
        {{-- Garis Atas Hijau Solid --}}
        <div class="h-1.5 bg-emerald-600 w-full"></div>

        {{-- 1. HEADER (Disederhanakan) --}}
        <div class="px-6 py-5 border-b border-gray-100 bg-white">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 bg-emerald-600 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-emerald-50">
                    <i class="fa-solid fa-file-lines text-xl"></i>
                </div>
                <div>
                    <h2 class="text-xl font-black text-gray-900 uppercase tracking-tighter italic leading-none">Rekapitulasi <span class="text-emerald-600 not-italic">Presensi</span></h2>
                    <p class="text-[9px] text-gray-400 font-bold uppercase tracking-[0.2em] mt-1">
                        Laporan Kehadiran — <span class="italic text-slate-500">Mode Pantauan Guru</span>
                    </p>
                </div>
            </div>
        </div>

        {{-- 2. FILTER & SEARCH --}}
        <div class="px-6 py-5 bg-slate-50/50 border-b border-gray-100">
            <form action="{{ route('guru.absensi.rekap') }}" method="GET" class="flex flex-wrap items-end gap-3">
                <div class="w-32">
                    <label class="block text-[8px] text-slate-400 uppercase font-black tracking-widest mb-1.5 ml-1 italic">Mode Cari</label>
                    <select id="modeSelect" name="mode" class="w-full bg-white border-2 border-slate-100 rounded-xl px-3 py-2 text-[10px] font-black text-emerald-700 outline-none focus:border-emerald-500 shadow-sm transition-all cursor-pointer uppercase">
                        <option value="daily" {{ request('mode') == 'daily' ? 'selected' : '' }}>Harian</option>
                        <option value="monthly" {{ request('mode') == 'monthly' ? 'selected' : '' }}>Bulanan</option>
                    </select>
                </div>

                <div class="w-40" id="dailyInputWrapper">
                    <label class="block text-[8px] text-slate-400 uppercase font-black tracking-widest mb-1.5 ml-1 italic">Pilih Tanggal</label>
                    <input type="date" name="tanggal" value="{{ request('tanggal') ?? date('Y-m-d') }}" class="w-full bg-white border-2 border-slate-100 rounded-xl px-3 py-2 text-[10px] font-black text-slate-700 outline-none focus:border-emerald-500 shadow-sm">
                </div>

                <div class="w-40 hidden" id="monthlyInputWrapper">
                    <label class="block text-[8px] text-slate-400 uppercase font-black tracking-widest mb-1.5 ml-1 italic">Pilih Bulan</label>
                    <select name="bulan" class="w-full bg-white border-2 border-slate-100 rounded-xl px-3 py-2 text-[10px] font-black text-slate-700 outline-none focus:border-emerald-500 shadow-sm uppercase">
                        @for ($i = 1; $i <= 12; $i++)
                            @php $m = str_pad($i, 2, '0', STR_PAD_LEFT); @endphp
                            <option value="{{ $m }}" {{ (request('bulan') ?? date('m')) == $m ? 'selected' : '' }}>
                                {{ Carbon\Carbon::create()->month($i)->translatedFormat('F') }}
                            </option>
                        @endfor
                    </select>
                </div>

                <div class="w-32">
                    <label class="block text-[8px] text-slate-400 uppercase font-black tracking-widest mb-1.5 ml-1 italic">Kelas</label>
                    <select name="kelas" class="w-full bg-white border-2 border-slate-100 rounded-xl px-3 py-2 text-[10px] font-black text-slate-700 outline-none focus:border-emerald-500 shadow-sm cursor-pointer uppercase">
                        <option value="">Semua</option>
                        @foreach($kelasList as $k)
                            <option value="{{ $k->nama_kelas ?? $k->kelas }}" {{ request('kelas') == ($k->nama_kelas ?? $k->kelas) ? 'selected' : '' }}>
                                {{ $k->nama_kelas ?? $k->kelas }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-2.5 rounded-xl font-black text-[9px] uppercase tracking-widest transition-all shadow-md active:scale-95 flex items-center gap-2">
                    <i class="fas fa-filter text-[8px]"></i> Filter
                </button>

                <div class="flex-grow"></div>

                <div class="w-full lg:w-48 text-right">
                    <label class="block text-[8px] text-slate-400 uppercase font-black tracking-widest mb-1.5 mr-1 italic">Cari Nama</label>
                    <div class="relative">
                        <input type="text" id="searchInput" placeholder="CARI SISWA..." class="w-full bg-white border-2 border-slate-100 rounded-xl px-4 py-2 text-[9px] font-black text-emerald-700 placeholder-slate-300 outline-none shadow-sm focus:border-emerald-500 transition-all uppercase">
                        <i class="fas fa-search absolute right-3 top-2.5 text-slate-300 text-[10px]"></i>
                    </div>
                </div>
            </form>
        </div>

        {{-- 3. TABLE --}}
        <div class="overflow-x-auto">
            <table class="w-full border-collapse text-left" id="absensiTable">
                <thead>
                    <tr class="text-[9px] font-black text-slate-400 uppercase tracking-[0.15em] border-b border-slate-50 bg-slate-50/30">
                        <th class="px-6 py-4 text-center w-16 italic">No.</th>
                        <th class="px-6 py-4">Identitas Siswa</th>
                        <th class="px-6 py-4 text-center">Status Kehadiran</th>
                        <th class="px-6 py-4 text-center">Notifikasi WA</th>
                        <th class="px-6 py-4 text-center">Tgl Presensi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50" id="tableBody">
                    @php $no = 1; @endphp
                    @forelse($rekaps as $r)
                        @if(!$r->siswa) @continue @endif

                        <tr class="hover:bg-emerald-50/10 transition-all group row-siswa">
                            <td class="px-6 py-5 text-center text-slate-300 font-black text-[10px] font-mono italic group-hover:text-emerald-600 transition-colors">
                                {{ str_pad($no++, 2, '0', STR_PAD_LEFT) }}
                            </td>
                            <td class="px-6 py-5">
                                <div class="flex flex-col">
                                    <span class="text-[11px] font-black text-slate-700 uppercase tracking-tight italic group-hover:text-emerald-700 transition-colors nama-siswa">{{ $r->siswa->nama }}</span>
                                    <span class="text-[8px] text-slate-400 font-bold uppercase tracking-tighter mt-0.5">
                                        {{-- PERBAIKAN DI SINI: Memanggil properti nama_kelas secara spesifik agar tidak muncul JSON --}}
                                        NISN: {{ $r->siswa->nisn }} • Kelas {{ $r->siswa->dataKelas->nama_kelas ?? $r->siswa->kelas }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-5 text-center">
                                @php 
                                    $st = strtoupper($r->status);
                                    $statusStyle = match($st) {
                                        'HADIR', 'H' => ['text' => 'text-emerald-700', 'dot' => 'bg-emerald-500', 'label' => 'Hadir'],
                                        'ALPA', 'A'  => ['text' => 'text-rose-700', 'dot' => 'bg-rose-500', 'label' => 'Alpa'],
                                        'SAKIT', 'S' => ['text' => 'text-amber-700', 'dot' => 'bg-amber-500', 'label' => 'Sakit'],
                                        'IZIN', 'I'  => ['text' => 'text-blue-700', 'dot' => 'bg-blue-500', 'label' => 'Izin'],
                                        default      => ['text' => 'text-slate-500', 'dot' => 'bg-slate-400', 'label' => $st],
                                    };
                                @endphp
                                
                                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-white border border-slate-100 shadow-sm">
                                    <span class="relative flex h-2 w-2">
                                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full {{ $statusStyle['dot'] }} opacity-20"></span>
                                        <span class="relative inline-flex rounded-full h-2 w-2 {{ $statusStyle['dot'] }}"></span>
                                    </span>
                                    <span class="text-[9px] font-black uppercase tracking-widest {{ $statusStyle['text'] }}">
                                        {{ $statusStyle['label'] }}
                                    </span>
                                </div>
                            </td>

                            <td class="px-6 py-5 text-center">
                                @if(in_array(strtoupper($r->status), ['ALPA', 'A']))
                                    <div class="flex flex-col items-center gap-1">
                                        @if($r->status_wa == 'sent')
                                            <i class="fas fa-check-double text-emerald-500 text-[10px]"></i>
                                            <span class="text-[7px] font-black text-emerald-600 uppercase tracking-tighter">Terkirim</span>
                                        @elseif($r->status_wa == 'failed')
                                            <i class="fas fa-exclamation-circle text-rose-500 text-[10px]"></i>
                                            <form action="{{ route('guru.absensi.resend', $r->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="bg-rose-100 text-rose-700 px-2 py-0.5 rounded-lg text-[7px] font-black uppercase tracking-tighter hover:bg-rose-700 hover:text-white transition-all">
                                                    Resend <i class="fas fa-sync-alt ml-0.5"></i>
                                                </button>
                                            </form>
                                        @else
                                            <i class="fas fa-circle-notch fa-spin text-slate-200 text-[10px]"></i>
                                            <span class="text-[7px] font-black text-slate-300 uppercase tracking-tighter">Proses</span>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-slate-200 text-[10px]">——</span>
                                @endif
                            </td>

                            <td class="px-6 py-5 text-center text-[10px] font-black text-slate-500 font-mono tracking-tighter">
                                {{ \Carbon\Carbon::parse($r->tanggal)->translatedFormat('d/m/Y') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-24 text-center text-[10px] font-black text-slate-300 uppercase italic tracking-[0.2em]">Data Rekapitulasi Tidak Tersedia</td>
                        </tr>
                    @endforelse
                    <tr id="noResultsRow" style="display: none;">
                        <td colspan="5" class="px-6 py-24 text-center text-[10px] font-black text-slate-400 uppercase italic tracking-widest">Pencarian Siswa Tidak Ditemukan</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

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