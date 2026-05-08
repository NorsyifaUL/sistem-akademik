@extends('layouts.guru')

@section('content')
<div class="mb-8 flex flex-col md:flex-row md:items-end justify-between gap-4">
    <div>
        <h2 class="text-3xl font-extrabold text-gray-900 tracking-tight">Rekapitulasi Nilai</h2>
        <p class="text-gray-500 mt-1 text-sm italic">Laporan performa akademik siswa secara kolektif.</p>
    </div>
    <div class="flex items-center gap-3 print:hidden">
        <div class="text-right hidden sm:block">
            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-tighter">Semester Aktif</p>
            <p class="text-xs font-bold text-gray-700">{{ ($setting->semester ?? 1) == 1 ? 'Ganjil' : 'Genap' }} 2025/2026</p>
        </div>
        <div class="h-8 w-[1px] bg-gray-200 mx-2 hidden sm:block"></div>
    </div>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden transition-all duration-300">
    <div class="border-t-4 border-green-600 bg-gray-50 px-6 py-4 flex flex-col lg:flex-row lg:items-center justify-between gap-4 border-b">
        <h3 class="text-gray-700 font-bold text-sm uppercase tracking-wider flex items-center min-w-max">
            <i class="fas fa-file-invoice text-green-600 mr-3"></i>
            Rekap: {{ $jadwal->mapel->nama_mapel ?? $jadwal->mapel->nama }} (Kelas {{ $jadwal->kelas }})
        </h3>
        
        <div class="flex flex-col sm:flex-row items-center gap-4 print:hidden w-full lg:w-auto">
            <div class="relative w-full sm:w-64 group">
                <input type="text" id="inputCariNama" onkeyup="cariNamaSiswa()" 
                       placeholder="Cari nama siswa..." 
                       class="w-full bg-white border border-gray-300 text-gray-700 text-[11px] font-bold rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all pl-9">
                <div class="absolute left-3 top-2.5 text-gray-400 group-focus-within:text-green-600 transition-colors">
                    <i class="fas fa-search text-[10px]"></i>
                </div>
            </div>

            <div class="hidden sm:block h-6 w-[1px] bg-gray-300"></div>

            <div class="flex items-center gap-2 w-full sm:w-auto">
                <span class="text-[10px] text-gray-500 font-bold uppercase tracking-widest whitespace-nowrap">Pilih Kelas:</span>
                <select onchange="window.location.href='{{ route('guru.jadwal.legger', '') }}/' + this.value" 
                        class="w-full sm:w-auto bg-white border border-gray-300 text-gray-700 text-[11px] font-bold rounded-lg px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all cursor-pointer">
                    @foreach($semuaJadwal as $j)
                        <option value="{{ $j->id }}" {{ $j->id == $jadwal->id ? 'selected' : '' }}>
                            Kelas {{ $j->kelas }} - {{ $j->mapel->nama_mapel }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-100">
                <tr class="text-[10px] font-bold text-gray-600 uppercase tracking-widest">
                    <th class="px-6 py-5 text-center border-r w-12">No</th>
                    <th class="px-8 py-5 text-left border-r">Nama Lengkap Siswa</th>
                    <th class="px-6 py-5 text-center border-r">Harian (Avg)</th>
                    <th class="px-6 py-5 text-center border-r">UTS</th>
                    <th class="px-6 py-5 text-center border-r">UAS</th>
                    <th class="px-6 py-5 text-center border-r">Nilai Akhir</th>
                    <th class="px-6 py-5 text-center border-r">Rangking</th>
                    <th class="px-6 py-5 text-center">Status</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100">
                @forelse($rekapData as $index => $s)
                @php
                    $nilaiSiswa = $s->nilais->where('jadwal_id', $jadwal->id);

                    // Perhitungan Nilai
                    $skorHarian = $nilaiSiswa->where('jenis', 'harian')->pluck('nilai')->toArray();
                    $rataHarian = count($skorHarian) > 0 ? array_sum($skorHarian) / count($skorHarian) : 0;
                    $uts = $nilaiSiswa->where('jenis', 'uts')->first()->nilai ?? 0;
                    $uas = $nilaiSiswa->where('jenis', 'uas')->first()->nilai ?? 0;
                    $nilaiAkhir = ($rataHarian + $uts + $uas) > 0 ? ($rataHarian + $uts + $uas) / 3 : 0;
                    
                    $rank = $s->ranking ?? '-';
                @endphp
                <tr class="item-siswa hover:bg-gray-50 transition-colors group">
                    <td class="px-6 py-5 text-center text-xs font-bold text-gray-400 border-r">{{ $index + 1 }}</td>
                    <td class="px-8 py-5 border-r">
                        <div class="flex flex-col">
                            <span class="nama-siswa text-sm font-black text-gray-900 uppercase tracking-tight group-hover:text-green-700 transition-colors">
                                {{ $s->nama }}
                            </span>
                            <span class="text-[9px] font-bold text-gray-400 uppercase tracking-tighter">NISN: {{ $s->nisn ?? '000000' }}</span>
                        </div>
                    </td>
                    
                    {{-- Kolom Nilai Dibuat Seragam --}}
                    <td class="px-6 py-5 text-center text-sm font-medium text-gray-600 border-r">
                        {{ $rataHarian > 0 ? round($rataHarian) : '-' }}
                    </td>
                    <td class="px-6 py-5 text-center text-sm font-medium text-gray-600 border-r">
                        {{ $uts ?: '-' }}
                    </td>
                    <td class="px-6 py-5 text-center text-sm font-medium text-gray-600 border-r">
                        {{ $uas ?: '-' }}
                    </td>
                    <td class="px-6 py-5 text-center text-sm font-bold text-gray-900 border-r">
                        {{ number_format($nilaiAkhir, 2) }}
                    </td>

                    <td class="px-6 py-5 text-center border-r">
                        @if($rank == 1)
                            <span class="text-sm font-black text-green-700 bg-green-100 px-2.5 py-1 rounded border border-green-200">1</span>
                        @elseif($rank == 2)
                            <span class="text-sm font-black text-blue-700 bg-blue-100 px-2.5 py-1 rounded border border-blue-200">2</span>
                        @elseif($rank == 3)
                            <span class="text-sm font-black text-amber-700 bg-amber-100 px-2.5 py-1 rounded border border-amber-200">3</span>
                        @else
                            <span class="text-xs font-bold text-gray-400">{{ $rank }}</span>
                        @endif
                    </td>
                    <td class="px-6 py-5 text-center">
                        @if($nilaiAkhir >= 75)
                            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[9px] font-black uppercase tracking-tighter bg-emerald-100 text-emerald-700 border border-emerald-200">
                                <i class="fas fa-check-circle mr-1"></i> LULUS
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[9px] font-black uppercase tracking-tighter bg-red-100 text-red-700 border border-red-200">
                                <i class="fas fa-exclamation-triangle mr-1"></i> REMEDIAL
                            </span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-8 py-20 text-center">
                        <div class="flex flex-col items-center">
                            <i class="fas fa-folder-open text-gray-200 text-5xl mb-4"></i>
                            <p class="text-[11px] font-black text-gray-400 uppercase tracking-widest">Data nilai tidak ditemukan</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="bg-gray-50 px-8 py-4 border-t border-gray-200 flex items-center justify-between">
        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">
            Showing 1 to {{ $rekapData->count() }} of {{ $rekapData->count() }} entries
        </p>
        <p class="text-[9px] text-gray-300 font-medium italic">Sistem Akademik SMANJA v.2.0</p>
    </div>
</div>

<style>
    @media print {
        @page { 
            size: landscape; 
            margin: 1.5cm; 
        }

        .print\:hidden, 
        .main-sidebar, 
        .main-header, 
        .content-header,
        footer,
        button, 
        select, 
        input,
        .border-t-4 { 
            display: none !important; 
        }

        .bg-white { background-color: white !important; }
        .shadow-sm, .rounded-2xl { box-shadow: none !important; border: 1px solid #d1d5db !important; }
        
        table { 
            width: 100% !important; 
            border-collapse: collapse !important;
            font-size: 9pt !important;
        }

        th { 
            background-color: #f3f4f6 !important; 
            color: #374151 !important;
            -webkit-print-color-adjust: exact; 
        }

        th, td { 
            border: 0.5pt solid #d1d5db !important; 
            padding: 6px !important;
            background-color: transparent !important;
        }
    }
</style>

<script>
    function cariNamaSiswa() {
        let input = document.getElementById('inputCariNama').value.toUpperCase();
        let rows = document.getElementsByClassName('item-siswa');
        for (let i = 0; i < rows.length; i++) {
            let nama = rows[i].getElementsByClassName('nama-siswa')[0].innerText;
            rows[i].style.display = (nama.toUpperCase().indexOf(input) > -1) ? "" : "none";
        }
    }
</script>
@endsection