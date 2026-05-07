@extends('layouts.guru')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-8">
    
    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden transition-all duration-300">
        
        <div class="h-1.5 bg-gradient-to-r from-green-600 to-emerald-400 w-full"></div>

        {{-- 1. HEADER DENGAN TAHUN AJARAN & SEMESTER OTOMATIS --}}
        <div class="p-6 md:p-8 border-b border-gray-50 bg-white">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                <div class="space-y-1">
                    <h2 class="text-2xl font-black text-gray-800 uppercase tracking-tighter">
                        Lembar Presensi <span class="text-green-600">Digital</span>
                    </h2>
                    <div class="flex flex-wrap items-center gap-2">
                        @php
                            $now = \Carbon\Carbon::now();
                            $tahunSekarang = $now->year;
                            $bulanSekarang = $now->month;

                            // Logika Tahun Pelajaran (Juli ke atas masuk tahun baru)
                            if ($bulanSekarang >= 7) {
                                $tapel = $tahunSekarang . '/' . ($tahunSekarang + 1);
                                $semester = 'Ganjil';
                            } else {
                                $tapel = ($tahunSekarang - 1) . '/' . $tahunSekarang;
                                $semester = 'Genap';
                            }
                        @endphp
                        <span class="px-2 py-0.5 bg-green-50 text-[10px] font-black text-green-700 rounded border border-green-100 uppercase tracking-wider">
                            TP: {{ $tapel }}
                        </span>
                        <span class="px-2 py-0.5 bg-blue-50 text-[10px] font-black text-blue-700 rounded border border-blue-100 uppercase tracking-wider">
                            Semester {{ $semester }}
                        </span>
                        <span class="text-gray-300 hidden md:block">•</span>
                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">SMAN 1 Jejangkit</span>
                    </div>
                </div>

                <div class="flex items-center gap-4 bg-gray-50 p-3 rounded-2xl border border-gray-100">
                    <div class="text-right">
                        <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest leading-none mb-1">Hari & Tanggal</p>
                        <p class="text-sm font-bold text-gray-700 uppercase">{{ $now->translatedFormat('l, d F Y') }}</p>
                    </div>
                    <div class="w-10 h-10 bg-white rounded-xl shadow-sm flex items-center justify-center text-green-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        {{-- 2. INFO PELAJARAN --}}
        <div class="px-8 py-6 bg-gray-50/40 border-b border-gray-100 grid grid-cols-2 md:grid-cols-4 gap-6">
            <div class="space-y-1 border-r border-gray-100">
                <span class="text-[9px] font-black text-gray-400 uppercase tracking-[0.2em] block">Mata Pelajaran</span>
                <span class="text-sm font-black text-gray-800 uppercase italic flex items-center gap-2">
                    <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                    {{ $jadwal->mapel->nama_mapel ?? $jadwal->mapel->nama }}
                </span>
            </div>
            <div class="space-y-1 border-r border-gray-100">
                <span class="text-[9px] font-black text-gray-400 uppercase tracking-[0.2em] block">Rombel / Kelas</span>
                <span class="text-sm font-black text-gray-800 uppercase">{{ $jadwal->kelas }}</span>
            </div>
            <div class="space-y-1 border-r border-gray-100">
                <span class="text-[9px] font-black text-gray-400 uppercase tracking-[0.2em] block">Hari</span>
                <span class="text-sm font-black text-gray-800 uppercase">{{ $jadwal->hari }}</span>
            </div>
            <div class="space-y-1">
                <span class="text-[9px] font-black text-gray-400 uppercase tracking-[0.2em] block">Alokasi Waktu</span>
                <span class="text-sm font-black text-green-700 bg-green-100 px-2 py-0.5 rounded inline-block">
                    {{ substr($jadwal->jam_mulai, 0, 5) }} - {{ substr($jadwal->jam_selesai, 0, 5) }} WIB
                </span>
            </div>
        </div>

        {{-- 3. FORM & TABEL --}}
        <form action="{{ Auth::user()->role == 'guru' ? route('guru.absensi.simpan') : route('admin.absensi.simpan') }}" method="POST">
            @csrf
            <input type="hidden" name="jadwal_id" value="{{ $jadwal->id }}">
            <input type="hidden" name="tanggal" value="{{ date('Y-m-d') }}">

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="text-[10px] font-black uppercase tracking-widest text-gray-400 border-b border-gray-100 bg-white">
                            <th class="px-8 py-5 w-20 text-center">No.</th>
                            <th class="px-8 py-5">Identitas Siswa</th>
                            <th class="px-8 py-5 text-center">Status Kehadiran</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($siswa as $index => $s)
                        <tr class="hover:bg-green-50/30 transition-all duration-200 group">
                            <td class="px-8 py-6 text-xs font-black text-gray-300 text-center italic">
                                {{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}.
                            </td>
                            <td class="px-8 py-6">
                                <p class="text-sm font-black text-gray-800 uppercase tracking-tight group-hover:text-green-700 transition-colors">
                                    {{ $s->nama }}
                                </p>
                                <span class="text-[9px] font-bold text-gray-400 uppercase tracking-tighter">NISN: {{ $s->nisn ?? '-' }}</span>
                            </td>
                            <td class="px-8 py-6">
                                <div class="flex justify-center gap-3">
                                    @foreach([
                                        'H' => ['label' => 'Hadir', 'color' => 'peer-checked:bg-green-600'],
                                        'S' => ['label' => 'Sakit', 'color' => 'peer-checked:bg-yellow-500'],
                                        'I' => ['label' => 'Izin', 'color' => 'peer-checked:bg-blue-500'],
                                        'A' => ['label' => 'Alpa', 'color' => 'peer-checked:bg-red-600']
                                    ] as $key => $data)
                                    
                                    @php 
                                        $record = $sudah_absen[$s->id] ?? null;
                                        $oldStatus = $record ? substr(strtoupper($record->status), 0, 1) : 'H'; 
                                    @endphp
                                    
                                    <label class="cursor-pointer group relative">
                                        <input type="radio" name="status[{{ $s->id }}]" value="{{ $key }}" 
                                            class="hidden peer" {{ $oldStatus == $key ? 'checked' : '' }} 
                                            {{ $sudah_absen->count() > 0 ? 'disabled' : '' }} required>
                                        
                                        <div class="w-12 h-12 flex flex-col items-center justify-center rounded-xl border-2 border-gray-100 bg-white text-gray-400 shadow-sm transition-all duration-200 peer-checked:border-transparent {{ $data['color'] }} peer-checked:text-white peer-checked:shadow-lg peer-checked:scale-110 peer-disabled:opacity-40">
                                            <span class="text-[10px] font-black">{{ $key }}</span>
                                            <span class="text-[6px] font-black uppercase tracking-tighter">{{ $data['label'] }}</span>
                                        </div>
                                    </label>
                                    @endforeach
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="py-20 text-center uppercase text-[10px] font-black text-gray-300 tracking-[0.2em]">Data Siswa Belum Tersedia</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- 4. FOOTER --}}
            <div class="p-8 bg-gray-50/80 border-t border-gray-100 flex flex-col md:flex-row items-center justify-between gap-6">
                <div class="flex items-center gap-4 bg-white px-5 py-3 rounded-2xl border border-gray-100">
                    <span class="relative flex h-3 w-3">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full {{ $sudah_absen->count() > 0 ? 'bg-orange-400' : 'bg-green-400' }} opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3 {{ $sudah_absen->count() > 0 ? 'bg-orange-500' : 'bg-green-500' }}"></span>
                    </span>
                    <p class="text-[10px] text-gray-500 font-black uppercase tracking-widest leading-none">
                        {{ $sudah_absen->count() > 0 ? 'Data Presensi Hari Ini Sudah Terkunci' : 'Sistem Siap Digunakan' }}
                    </p>
                </div>
                
                <div class="flex items-center gap-4">
                    @if($sudah_absen->count() > 0)
                        <a href="{{ route('guru.absensi.index') }}" class="bg-gray-900 hover:bg-black text-white px-8 py-4 rounded-xl text-[10px] font-black uppercase tracking-[0.2em] shadow-lg transition-all active:scale-95">
                            &larr; Kembali ke Jadwal
                        </a>
                    @else
                        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-10 py-4 rounded-xl text-[10px] font-black uppercase tracking-[0.2em] shadow-xl shadow-green-100 transition-all active:scale-95 flex items-center gap-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                            Simpan Kehadiran &rarr;
                        </button>
                    @endif
                </div>
            </div>
        </form>
    </div>
</div>
@endsection