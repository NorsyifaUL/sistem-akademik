@extends('layouts.guru')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-8">
    
    <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
        
        <div class="h-1.5 bg-green-600 w-full"></div>

        {{-- 1. HEADER --}}
        <div class="p-6 border-b border-gray-100 bg-white">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                <div>
                    <h2 class="text-xl font-black text-gray-800 uppercase tracking-tight">Lembar Presensi Digital</h2>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em] mt-1">
                        Tahun Akademik: 2025/2026 — SMAN 1 Jejangkit
                    </p>
                </div>

                <div class="bg-gray-50 border border-gray-100 rounded-lg px-4 py-2">
                    <span class="text-[10px] font-black text-gray-400 uppercase block tracking-widest">Tanggal Hari Ini</span>
                    <span class="text-xs font-bold text-gray-700">{{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</span>
                </div>
            </div>
        </div>

        {{-- 2. INFO PELAJARAN --}}
        <div class="px-6 py-4 bg-gray-50/50 border-b border-gray-100 grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="flex flex-col">
                <span class="text-[8px] font-black text-gray-400 uppercase tracking-widest">Mata Pelajaran</span>
                <span class="text-xs font-black text-gray-700 uppercase italic">{{ $jadwal->mapel->nama_mapel ?? $jadwal->mapel->nama }}</span>
            </div>
            <div class="flex flex-col">
                <span class="text-[8px] font-black text-gray-400 uppercase tracking-widest">Kelas & Hari</span>
                <span class="text-xs font-black text-gray-700 uppercase">Kelas {{ $jadwal->kelas }} — {{ $jadwal->hari }}</span>
            </div>
            <div class="flex flex-col">
                <span class="text-[8px] font-black text-gray-400 uppercase tracking-widest">Waktu</span>
                <span class="text-xs font-black text-green-700 uppercase">{{ substr($jadwal->jam_mulai, 0, 5) }} - {{ substr($jadwal->jam_selesai, 0, 5) }} WIB</span>
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
                            <th class="px-8 py-5 w-16 text-center">No.</th>
                            <th class="px-8 py-5">Identitas Siswa</th>
                            <th class="px-8 py-5 text-center">Status Kehadiran</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($siswa as $index => $s)
                        <tr class="hover:bg-green-50/20 transition-colors">
                            <td class="px-8 py-6 text-xs font-black text-gray-300 text-center italic">{{ $index + 1 }}.</td>
                            <td class="px-8 py-6">
                                <p class="text-sm font-black text-gray-800 uppercase tracking-tight">{{ $s->nama }}</p>
                                <span class="text-[9px] font-bold text-gray-400 uppercase">NISN: {{ $s->nisn ?? '-' }}</span>
                            </td>
                            <td class="px-8 py-6">
                                <div class="flex justify-center gap-2">
                                    @foreach(['H' => 'Hadir', 'S' => 'Sakit', 'I' => 'Izin', 'A' => 'Alpa'] as $key => $label)
                                    @php 
                                        $record = $sudah_absen[$s->id] ?? null;
                                        $oldStatus = $record ? substr(strtoupper($record->status), 0, 1) : 'H'; 
                                    @endphp
                                    <label class="cursor-pointer group">
                                        <input type="radio" name="status[{{ $s->id }}]" value="{{ $key }}" 
                                            class="hidden peer" {{ $oldStatus == $key ? 'checked' : '' }} 
                                            {{ $sudah_absen->count() > 0 ? 'disabled' : '' }} required>
                                        <div class="w-12 h-12 flex flex-col items-center justify-center rounded-xl border border-gray-100 bg-white text-gray-400 shadow-sm peer-checked:bg-green-700 peer-checked:text-white peer-disabled:opacity-50 peer-disabled:cursor-not-allowed">
                                            <span class="text-[10px] font-black">{{ $key }}</span>
                                            <span class="text-[6px] font-black uppercase">{{ $label }}</span>
                                        </div>
                                    </label>
                                    @endforeach
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="py-20 text-center text-gray-300 uppercase text-[10px] font-black">Data Siswa Kosong</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- 4. FOOTER DINAMIS --}}
            <div class="p-8 bg-gray-50 border-t border-gray-100 flex flex-col md:flex-row items-center justify-between gap-6">
                <div class="flex items-center gap-3">
                    <span class="w-2 h-2 rounded-full {{ $sudah_absen->count() > 0 ? 'bg-orange-500' : 'bg-green-500' }} animate-pulse"></span>
                    <p class="text-[9px] text-gray-400 font-bold uppercase tracking-widest">
                        {{ $sudah_absen->count() > 0 ? 'Data Terkunci: Presensi hari ini sudah diinput.' : 'Sistem Siap: Silakan input data kehadiran.' }}
                    </p>
                </div>
                
                @if($sudah_absen->count() > 0)
                    {{-- JIKA SUDAH ABSEN --}}
                    <div class="flex items-center gap-4">
                        <span class="text-[9px] font-bold text-orange-600 italic uppercase mr-2"></span>
                        <a href="{{ route('guru.absensi.index') }}" class="bg-gray-900 hover:bg-black text-white px-10 py-4 rounded-xl text-[10px] font-black uppercase tracking-widest shadow-lg transition-all">
                            &larr; Kembali ke Jadwal
                        </a>
                    </div>
                @else
                    {{-- JIKA BELUM ABSEN --}}
                    <button type="submit" class="bg-gray-900 hover:bg-green-700 text-white px-10 py-4 rounded-xl text-[10px] font-black uppercase tracking-[0.2em] transition-all shadow-xl flex items-center gap-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                        Simpan Presensi &rarr;
                    </button>
                @endif
            </div>
        </form>
    </div>
</div>
@endsection