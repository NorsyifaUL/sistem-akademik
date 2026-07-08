@extends('layouts.guru')

@section('content')
@php
    // 1. Hitung Nilai Akhir untuk semua siswa dan simpan dalam koleksi sementara (Untuk Perhitungan Ranking)
    $koleksiRanking = $rekapData->map(function($s) use ($jadwal) {
        $nilaiSiswa = $s->nilais->where('jadwal_id', $jadwal->id);
        
        // Cek apakah sudah ada data jenis rekap (untuk akurasi ranking)
        $dataRekap = $nilaiSiswa->where('jenis', 'rekap')->first();

        if ($dataRekap) {
            $totalMurni = $dataRekap->nilai;
        } else {
            $skorUH = $nilaiSiswa->filter(function($item) {
                $jenisClean = strtolower(str_replace(' ', '', $item->jenis));
                return preg_match('/uh[1-4]/i', $jenisClean) || $jenisClean == 'harian';
            })->pluck('nilai')->toArray();

            $rataUH = count($skorUH) > 0 ? array_sum($skorUH) / count($skorUH) : 0;
            $uts = $nilaiSiswa->filter(fn($n) => strtolower($n->jenis) == 'uts')->first()->nilai ?? 0;
            $uas = $nilaiSiswa->filter(fn($n) => strtolower($n->jenis) == 'uas')->first()->nilai ?? 0;
            
            // SINKRONISASI RUMUS PERSENTASE: (Harian 40% + UTS 30% + UAS 30%)
            $calc = ($rataUH * 0.4) + ($uts * 0.3) + ($uas * 0.3);
            $totalMurni = $calc > 0 ? round($calc) : 0;
        }

        return [
            'siswa_id' => $s->id,
            'skor_akhir' => $totalMurni
        ];
    });

    // 2. Urutkan berdasarkan skor tertinggi
    $sorted = $koleksiRanking->sortByDesc('skor_akhir')->values();

    // 3. Buat mapping ranking [siswa_id => rank]
    $rankMapping = [];
    $currentRank = 0;
    $lastScore = -1;
    
    foreach ($sorted as $index => $item) {
        if ($item['skor_akhir'] > 0) {
            if ($item['skor_akhir'] !== $lastScore) {
                $currentRank = $index + 1;
            }
            $rankMapping[$item['siswa_id']] = $currentRank;
            $lastScore = $item['skor_akhir'];
        } else {
            $rankMapping[$item['siswa_id']] = '-';
        }
    }
@endphp

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
        <a href="{{ route('guru.nilai.cetak_pdf', $jadwal->id) }}" 
        target="_blank" 
        class="bg-red-600 text-white px-4 py-2 rounded-lg text-xs font-bold shadow-sm hover:bg-red-700 transition-all flex items-center gap-2">
            <i class="fas fa-file-pdf"></i> CETAK REKAP PDF
        </a>
    </div>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
    <div class="border-t-4 border-green-600 bg-gray-50 px-6 py-4 flex flex-col lg:flex-row lg:items-center justify-between gap-4 border-b">
        <h3 class="text-gray-700 font-bold text-sm uppercase tracking-wider flex items-center min-w-max">
            <i class="fas fa-file-invoice text-green-600 mr-3"></i>
            Rekap: {{ $jadwal->mapel->nama_mapel ?? $jadwal->mapel->nama }} (Kelas {{ $jadwal->kelas }})
        </h3>
        
        <div class="flex flex-col sm:flex-row items-center gap-4 print:hidden w-full lg:w-auto">
            <div class="relative w-full sm:w-64 group">
                <input type="text" id="inputCariNama" onkeyup="cariNamaSiswa()" 
                       placeholder="Cari nama siswa..." 
                       class="w-full bg-white border border-gray-300 text-gray-700 text-[11px] font-bold rounded-lg px-4 py-2 pl-9 focus:ring-2 focus:ring-green-500 outline-none">
                <div class="absolute left-3 top-2.5 text-gray-400 group-focus-within:text-green-600">
                    <i class="fas fa-search text-[10px]"></i>
                </div>
            </div>

            <div class="flex items-center gap-2 w-full sm:w-auto">
                <span class="text-[10px] text-gray-500 font-bold uppercase tracking-widest whitespace-nowrap">Pilih Kelas:</span>
                <select onchange="window.location.href='{{ route('guru.jadwal.legger', '') }}/' + this.value" 
                        class="w-full sm:w-auto bg-white border border-gray-300 text-gray-700 text-[11px] font-bold rounded-lg px-3 py-1.5 focus:ring-2 focus:ring-green-500 outline-none">
                    @foreach($semuaJadwal->sortBy('kelas') as $j)
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
                    <th class="px-4 py-5 text-center border-r w-12">No</th>
                    <th class="px-6 py-5 text-left border-r w-64">Nama Siswa</th>
                    <th class="px-4 py-5 text-center border-r bg-blue-50/30">HARIAN</th>
                    <th class="px-4 py-5 text-center border-r">UTS</th>
                    <th class="px-4 py-5 text-center border-r">UAS</th>
                    <th class="px-4 py-5 text-center border-r bg-green-50 text-green-700">Akhir</th>
                    <th class="px-4 py-5 text-center border-r">Rank</th>
                    <th class="px-6 py-5 text-left">Capaian Kompetensi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100">
                @forelse($rekapData as $index => $s)
                @php
                    $nilaiSiswa = $s->nilais->where('jadwal_id', $jadwal->id);
                    
                    // Ambil detail nilai komponen
                    $skorUH = $nilaiSiswa->filter(function($item) {
                        $jenisClean = strtolower(str_replace(' ', '', $item->jenis));
                        return preg_match('/uh[1-4]/i', $jenisClean) || $jenisClean == 'harian';
                    })->pluck('nilai')->toArray();

                    $rataUH = count($skorUH) > 0 ? round(array_sum($skorUH) / count($skorUH)) : 0;
                    $uts = $nilaiSiswa->filter(fn($n) => strtolower($n->jenis) == 'uts')->first()->nilai ?? 0;
                    $uas = $nilaiSiswa->filter(fn($n) => strtolower($n->jenis) == 'uas')->first()->nilai ?? 0;

                    // Logika Pengambilan Data Rekap
                    $dataRekap = $nilaiSiswa->where('jenis', 'rekap')->first();
                    
                    if ($dataRekap) {
                        $nilaiAkhir = $dataRekap->nilai;
                        $deskripsi = $dataRekap->keterangan; 
                    } else {
                        // FALLBACK SINKRONISASI: Menghitung persentase murni jika row rekap belum tercipta
                        $calcFallback = ($rataUH * 0.4) + ($uts * 0.3) + ($uas * 0.3);
                        $nilaiAkhir = $calcFallback > 0 ? round($calcFallback) : 0;
                        $deskripsi = null; 
                    }

                    $rank = $rankMapping[$s->id] ?? '-';
                @endphp
                <tr class="item-siswa hover:bg-gray-50 transition-colors group">
                    <td class="px-4 py-5 text-center text-xs font-bold text-gray-400 border-r">{{ $index + 1 }}</td>
                    <td class="px-6 py-5 border-r">
                        <div class="flex flex-col">
                            <span class="nama-siswa text-sm font-black text-gray-900 uppercase tracking-tight">{{ $s->nama }}</span>
                            <span class="text-[9px] font-bold text-gray-400 uppercase tracking-tighter">NISN: {{ $s->nisn ?? '000000' }}</span>
                        </div>
                    </td>
                    
                    <td class="px-4 py-5 text-center text-sm font-medium border-r bg-blue-50/10">
                        <span class="{{ $rataUH == 0 ? 'text-gray-300' : 'text-blue-600 font-bold' }}">{{ $rataUH ?: '-' }}</span>
                    </td>
                    <td class="px-4 py-5 text-center text-sm font-medium border-r">{{ $uts ?: '-' }}</td>
                    <td class="px-4 py-5 text-center text-sm font-medium border-r">{{ $uas ?: '-' }}</td>
                    <td class="px-4 py-5 text-center text-sm font-bold border-r bg-green-50/30">
                        <span class="{{ $nilaiAkhir < 75 && $nilaiAkhir > 0 ? 'text-red-600' : ($nilaiAkhir >= 75 ? 'text-green-700' : 'text-gray-300') }}">
                            {{ $nilaiAkhir ?: '-' }}
                        </span>
                    </td>

                    <td class="px-4 py-5 text-center border-r">
                         <span class="text-xs font-black {{ $rank !== '-' && $rank <= 3 ? 'text-green-700 bg-green-50 px-2 py-1 rounded' : 'text-gray-500' }}">
                            {{ $rank }}
                         </span>
                    </td>

                    <td class="px-6 py-5">
                        <p class="text-[11px] leading-relaxed text-gray-600 italic font-medium">
                            @if($deskripsi)
                                <i class="fas fa-quote-left text-[8px] text-green-400 mr-1"></i>
                                {{ $deskripsi }}
                            @else
                                <span class="text-gray-300">Belum diproses (Simpan di menu Input Nilai).</span>
                            @endif
                        </p>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-8 py-20 text-center text-gray-400 font-bold uppercase tracking-widest">Data tidak ditemukan</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="bg-gray-50 px-8 py-4 border-t border-gray-200 flex items-center justify-between">
        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">
            Data Terupdate: {{ date('d/m/Y H:i') }}
        </p>
        <p class="text-[9px] text-gray-300 font-medium italic">Sistem Informasi Akademik • SMAN 1 Jejangkit</p>
    </div>
</div>
@endsection