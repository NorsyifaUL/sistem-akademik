@extends('layouts.guru')

@section('content')
<div class="container-fluid mx-auto px-4 py-6">
    
    <div class="bg-white rounded shadow-md border border-gray-200 overflow-hidden">
        
        <div class="border-t-4 border-green-600 px-5 py-4 bg-gray-50 border-b">
            <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                <div>
                    <h3 class="text-lg font-bold text-gray-700 uppercase tracking-wider">Input Nilai Kolektif</h3>
                    <div class="flex items-center gap-2 mt-1">
                        @php
                            $now = \Carbon\Carbon::now();
                            $tahunSekarang = $now->year;
                            $bulanSekarang = $now->month;

                            if ($bulanSekarang >= 7) {
                                $tapel = $tahunSekarang . '/' . ($tahunSekarang + 1);
                                $semester = 'Ganjil';
                            } else {
                                $tapel = ($tahunSekarang - 1) . '/' . $tahunSekarang;
                                $semester = 'Genap';
                            }
                        @endphp
                        <p class="text-xs text-gray-500">Tahun Akademik: {{ $tapel }}</p>
                        <span class="text-gray-300">|</span>
                        <p class="text-xs text-gray-500 uppercase">Semester: {{ $semester }}</p>
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
                        <option value="harian" {{ strtolower(request('jenis_nilai')) == 'harian' ? 'selected' : '' }}>Nilai Harian (Rata-rata)</option>
                        <option value="UTS" {{ request('jenis_nilai') == 'UTS' ? 'selected' : '' }}>Nilai UTS</option>
                        <option value="UAS" {{ request('jenis_nilai') == 'UAS' ? 'selected' : '' }}>Nilai UAS</option>
                    </select>

                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded text-sm font-bold shadow-sm transition">
                        Tampilkan Siswa
                    </button>
                </form>
            </div>
        </div>

        @if($jadwalTerpilih && request('jenis_nilai'))
            {{-- Bagian Tabel (Tetap sama dengan kode asli Anda) --}}
            <form action="{{ route('guru.nilai.simpan_massal') }}" method="POST">
                @csrf
                <input type="hidden" name="jadwal_id" value="{{ $jadwalTerpilih->id }}">
                <input type="hidden" name="jenis" value="{{ strtolower(request('jenis_nilai')) }}">

                <div class="px-6 py-3 bg-yellow-50 border-b border-yellow-100 flex justify-between items-center">
                    <span class="text-sm text-yellow-800 font-medium">
                        <i class="fas fa-info-circle mr-1"></i> 
                        Sekarang menginput <strong>Nilai {{ strtoupper(request('jenis_nilai')) }}</strong> 
                    </span>
                    <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-2 rounded shadow font-bold text-sm transition shadow-lg">
                        <i class="fas fa-save mr-2"></i> SIMPAN SEMUA NILAI
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-100 text-gray-700 uppercase text-xs">
                                <th class="px-6 py-4 border-r text-center w-12">No</th>
                                <th class="px-6 py-4 border-r">Nama Siswa</th>
                                @if(strtolower(request('jenis_nilai')) == 'harian')
                                    <th class="px-2 py-4 border-r text-center w-20 text-green-600">UH 1</th>
                                    <th class="px-2 py-4 border-r text-center w-20 text-green-600">UH 2</th>
                                    <th class="px-2 py-4 border-r text-center w-20 text-green-600">UH 3</th>
                                    <th class="px-2 py-4 border-r text-center w-20 text-green-600">UH 4</th>
                                @endif
                                <th class="px-6 py-4 text-center bg-green-50 border-l-4 border-green-500 w-48 font-bold text-green-700">
                                    {{ strtolower(request('jenis_nilai')) == 'harian' ? 'RATA-RATA AKHIR' : 'NILAI '.strtoupper(request('jenis_nilai')) }}
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($siswaData as $index => $s)
                            <tr class="hover:bg-gray-50 transition student-row">
                                <td class="px-6 py-4 border-r text-center text-gray-500">{{ $index + 1 }}</td>
                                <td class="px-6 py-4 border-r font-medium text-gray-800 uppercase">{{ $s->nama }}</td>
                                
                                @if(strtolower(request('jenis_nilai')) == 'harian')
                                    <td class="px-2 py-2 border-r text-center">
                                        <input type="number" name="nilai[{{ $s->id }}][uh1]" value="{{ $s->uh1 }}" class="uh-input w-16 text-center border-gray-300 rounded text-sm">
                                    </td>
                                    <td class="px-2 py-2 border-r text-center">
                                        <input type="number" name="nilai[{{ $s->id }}][uh2]" value="{{ $s->uh2 }}" class="uh-input w-16 text-center border-gray-300 rounded text-sm">
                                    </td>
                                    <td class="px-2 py-2 border-r text-center">
                                        <input type="number" name="nilai[{{ $s->id }}][uh3]" value="{{ $s->uh3 }}" class="uh-input w-16 text-center border-gray-300 rounded text-sm">
                                    </td>
                                    <td class="px-2 py-2 border-r text-center">
                                        <input type="number" name="nilai[{{ $s->id }}][uh4]" value="{{ $s->uh4 }}" class="uh-input w-16 text-center border-gray-300 rounded text-sm">
                                    </td>
                                @endif

                                <td class="px-6 py-2 bg-green-50/50 text-center">
                                    <input type="number" 
                                        {{ strtolower(request('jenis_nilai')) != 'harian' ? 'name=nilai['.$s->id.']' : '' }} 
                                        value="{{ $s->nilai_existing ?? '' }}"
                                        class="final-input w-24 text-center border-gray-300 rounded-md font-bold py-2 shadow-sm bg-white"
                                        {{ strtolower(request('jenis_nilai')) == 'harian' ? 'readonly' : 'required' }}>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </form>
        @else
            {{-- Tampilan Kosong (Sesuai Gambar Anda) --}}
            <div class="p-20 text-center bg-white">
                <div class="w-24 h-24 bg-green-50 rounded-full flex items-center justify-center mx-auto mb-4 text-green-300">
                    <i class="fas fa-edit text-4xl"></i>
                </div>
                <h4 class="text-gray-700 font-bold text-lg">Pilih Parameter Input</h4>
                <p class="text-gray-400 max-w-md mx-auto mt-2 text-sm text-balance">
                    Silakan pilih <strong>Jadwal Pelajaran</strong> dan <strong>Jenis Nilai</strong> pada menu di atas untuk mulai melakukan input nilai kolektif.
                </p>
            </div>
        @endif
    </div>
</div>

<script>
{{-- Script perhitungan rata-rata tetap sama --}}
</script>
@endsection