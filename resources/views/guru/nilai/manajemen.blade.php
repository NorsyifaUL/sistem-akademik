@extends('layouts.guru')

@section('content')
<div class="container-fluid mx-auto px-4 py-6">
    
    <div class="bg-white rounded shadow-md border border-gray-200 overflow-hidden">
        
        {{-- Header Panel --}}
        <div class="border-t-4 border-green-600 px-5 py-4 bg-gray-50 border-b">
            <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                <div>
                    <h3 class="text-lg font-bold text-gray-700 uppercase tracking-wider">Input Nilai & Capaian</h3>
                    <div class="flex items-center gap-2 mt-1">
                        <p class="text-xs text-gray-500">Tahun Akademik: {{ $setting->tahun_ajaran ?? '-' }}</p>
                        <span class="text-gray-300">|</span>
                        <p class="text-xs text-gray-500 uppercase">Semester: <span class="font-bold text-green-600">{{ $semester }}</span></p>
                    </div>
                </div>

                <form action="{{ route('guru.lihat_nilai') }}" method="GET" class="flex flex-wrap items-center gap-2">
                    <select name="jadwal_id" required class="text-sm border-gray-300 rounded shadow-sm focus:ring-green-500 focus:border-green-500">
                        <option value="">-- Pilih Jadwal --</option>
                        @foreach($jadwals as $j)
                            <option value="{{ $j->id }}" {{ request('jadwal_id') == $j->id ? 'selected' : '' }}>
                                {{ $j->mapel->nama_mapel }} - {{ $j->kelas }}
                            </option>
                        @endforeach
                    </select>

                    <select name="jenis_nilai" required class="text-sm border-gray-300 rounded shadow-sm focus:ring-green-500 focus:border-green-500">
                        <option value="">-- Pilih Jenis Nilai --</option>
                        <option value="harian" {{ strtolower(request('jenis_nilai')) == 'harian' ? 'selected' : '' }}>Nilai Harian (UH)</option>
                        <option value="UTS" {{ request('jenis_nilai') == 'UTS' ? 'selected' : '' }}>Nilai UTS</option>
                        <option value="UAS" {{ request('jenis_nilai') == 'UAS' ? 'selected' : '' }}>Nilai UAS</option>
                        <option value="akhir" {{ request('jenis_nilai') == 'akhir' ? 'selected' : '' }}>REKAP NILAI AKHIR</option>
                    </select>

                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded text-sm font-bold shadow-sm transition">
                        Tampilkan Siswa
                    </button>
                </form>
            </div>
        </div>

        @if($jadwalTerpilih && request('jenis_nilai'))
            <form action="{{ route('guru.nilai.simpan_massal') }}" method="POST">
                @csrf
                <input type="hidden" name="jadwal_id" value="{{ $jadwalTerpilih->id }}">
                <input type="hidden" name="jenis" value="{{ strtolower(request('jenis_nilai')) }}">

                <div class="px-6 py-3 bg-yellow-50 border-b border-yellow-100 flex justify-between items-center">
                    <span class="text-sm text-yellow-800 font-medium">
                        <i class="fas fa-edit mr-1"></i> 
                        Entry Nilai <strong>{{ strtoupper(request('jenis_nilai')) }}</strong>
                    </span>
                    <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-2 rounded shadow font-bold text-sm transition hover:scale-105 transform">
                        <i class="fas fa-save mr-2"></i> SIMPAN PERUBAHAN
                    </button>
                </div>

                <div class="overflow-x-auto">
                    {{-- Menggunakan min-width lebih besar untuk mode "Akhir" agar deskripsi punya ruang --}}
                    <table class="w-full text-sm text-left border-collapse" style="min-width: {{ request('jenis_nilai') == 'akhir' ? '1100px' : '850px' }};">
                        <thead>
                            <tr class="bg-gray-100 text-gray-700 uppercase text-[10px] tracking-wider">
                                <th class="px-4 py-4 border-r text-center w-12">No</th>
                                <th class="px-6 py-4 border-r w-64">Nama Siswa</th>
                                
                                {{-- MODE HARIAN --}}
                                @if(strtolower(request('jenis_nilai')) == 'harian')
                                    <th class="px-1 py-4 border-r text-center" style="width: 70px;">UH 1</th>
                                    <th class="px-1 py-4 border-r text-center" style="width: 70px;">UH 2</th>
                                    <th class="px-1 py-4 border-r text-center" style="width: 70px;">UH 3</th>
                                    <th class="px-1 py-4 border-r text-center" style="width: 70px;">UH 4</th>
                                    <th class="px-4 py-4 text-center bg-green-50 border-r w-24 font-bold">RATA-RATA</th>
                                @endif

                                {{-- MODE UTS / UAS --}}
                                @if(in_array(strtolower(request('jenis_nilai')), ['uts', 'uas']))
                                    <th class="px-4 py-4 text-center bg-green-50 border-r w-24 font-bold">SKOR {{ strtoupper(request('jenis_nilai')) }}</th>
                                @endif

                                {{-- MODE NILAI AKHIR (REKAP) --}}
                                @if(strtolower(request('jenis_nilai')) == 'akhir')
                                    <th class="px-2 py-4 border-r text-center w-20 bg-blue-50">Harian</th>
                                    <th class="px-2 py-4 border-r text-center w-20 bg-blue-50">UTS</th>
                                    <th class="px-2 py-4 border-r text-center w-20 bg-blue-50">UAS</th>
                                    <th class="px-4 py-4 text-center bg-green-100 border-r w-24 font-bold text-green-800">NILAI AKHIR</th>
                                    <th class="px-6 py-4 text-left">Capaian Kompetensi (Deskripsi)</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($siswaData as $index => $s)
                            <tr class="hover:bg-gray-50 transition student-row">
                                <td class="px-4 py-4 border-r text-center text-gray-400 font-bold">{{ $index + 1 }}</td>
                                <td class="px-6 py-4 border-r font-bold text-gray-800 uppercase text-[11px]">{{ $s->nama }}</td>
                                
                                {{-- TAMPILAN HARIAN --}}
                                @if(strtolower(request('jenis_nilai')) == 'harian')
                                    <td class="px-1 py-2 border-r text-center">
                                        <input type="number" name="nilai[{{ $s->id }}][uh1]" value="{{ $s->uh1 ?? '' }}" min="0" max="100" class="uh-input w-full max-w-[55px] text-center border-gray-300 rounded text-xs px-1 py-1.5 focus:ring-green-500">
                                    </td>
                                    <td class="px-1 py-2 border-r text-center">
                                        <input type="number" name="nilai[{{ $s->id }}][uh2]" value="{{ $s->uh2 ?? '' }}" min="0" max="100" class="uh-input w-full max-w-[55px] text-center border-gray-300 rounded text-xs px-1 py-1.5 focus:ring-green-500">
                                    </td>
                                    <td class="px-1 py-2 border-r text-center">
                                        <input type="number" name="nilai[{{ $s->id }}][uh3]" value="{{ $s->uh3 ?? '' }}" min="0" max="100" class="uh-input w-full max-w-[55px] text-center border-gray-300 rounded text-xs px-1 py-1.5 focus:ring-green-500">
                                    </td>
                                    <td class="px-1 py-2 border-r text-center">
                                        <input type="number" name="nilai[{{ $s->id }}][uh4]" value="{{ $s->uh4 ?? '' }}" min="0" max="100" class="uh-input w-full max-w-[55px] text-center border-gray-300 rounded text-xs px-1 py-1.5 focus:ring-green-500">
                                    </td>
                                    <td class="px-4 py-2 bg-green-50/50 text-center border-r">
                                        <input type="number" name="nilai[{{ $s->id }}][rata_rata]" value="{{ $s->harian ?? '' }}" class="final-input w-16 text-center border-gray-300 rounded-md font-black py-1 bg-white focus:ring-green-600 text-sm" readonly>
                                    </td>
                                @endif

                                {{-- TAMPILAN UTS / UAS --}}
                                @if(in_array(strtolower(request('jenis_nilai')), ['uts', 'uas']))
                                    <td class="px-4 py-2 bg-green-50/50 text-center border-r">
                                        <input type="number" name="nilai[{{ $s->id }}][angka]" value="{{ $s->nilai_existing ?? '' }}" class="w-16 text-center border-gray-300 rounded-md font-black py-1 focus:ring-green-600 text-sm" required min="0" max="100">
                                    </td>
                                @endif

                                {{-- TAMPILAN REKAP AKHIR --}}
                                @if(strtolower(request('jenis_nilai')) == 'akhir')
                                    <td class="px-2 py-2 border-r text-center bg-blue-50/30 text-gray-600 text-xs">{{ $s->harian ?? 0 }}</td>
                                    <td class="px-2 py-2 border-r text-center bg-blue-50/30 text-gray-600 text-xs">{{ $s->uts ?? 0 }}</td>
                                    <td class="px-2 py-2 border-r text-center bg-blue-50/30 text-gray-600 text-xs">{{ $s->uas ?? 0 }}</td>
                                    
                                    <td class="px-4 py-2 bg-green-100/50 text-center border-r font-black text-green-700">
                                        @php
                                            // Menghitung nilai akhir sederhana (bisa disesuaikan bobotnya di controller)
                                            $na = (($s->harian ?? 0) + ($s->uts ?? 0) + ($s->uas ?? 0)) / 3;
                                        @endphp
                                        {{ round($na) }}
                                        <input type="hidden" name="nilai[{{ $s->id }}][angka]" value="{{ round($na) }}">
                                    </td>

                                    <td class="px-6 py-2">
                                        <textarea 
                                            name="nilai[{{ $s->id }}][deskripsi]" 
                                            rows="2" 
                                            placeholder="Tulis capaian kompetensi akhir..."
                                            class="w-full text-[11px] border-gray-200 rounded-md focus:ring-green-500 italic text-gray-600 px-3 py-1.5"
                                        >{{ $s->deskripsi_existing ?? '' }}</textarea>
                                    </td>
                                @endif
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </form>
        @else
            {{-- Landing Page State --}}
            <div class="p-20 text-center bg-white">
                <div class="w-24 h-24 bg-green-50 rounded-full flex items-center justify-center mx-auto mb-4 text-green-300">
                    <i class="fas fa-clipboard-list text-4xl"></i>
                </div>
                <h4 class="text-gray-700 font-bold text-lg">Manajemen Nilai Kolektif</h4>
                <p class="text-gray-400 max-w-md mx-auto mt-2 text-sm">
                    Pilih jadwal dan jenis nilai untuk mengelola angka nilai dan narasi capaian kompetensi siswa secara efisien.
                </p>
            </div>
        @endif
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const rows = document.querySelectorAll('.student-row');

        rows.forEach(row => {
            const inputs = row.querySelectorAll('.uh-input');
            const finalInput = row.querySelector('.final-input');

            if(inputs.length > 0 && finalInput) {
                inputs.forEach(input => {
                    input.addEventListener('input', () => {
                        let total = 0;
                        let count = 0;

                        inputs.forEach(i => {
                            if(i.value !== '') {
                                total += parseFloat(i.value);
                                count++;
                            }
                        });

                        const rataRata = count > 0 ? Math.round(total / count) : 0;
                        finalInput.value = rataRata;
                    });
                });
            }
        });
    });
</script>
@endsection