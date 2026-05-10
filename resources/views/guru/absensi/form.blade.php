@extends('layouts.guru')

@section('content')
<div class="max-w-5xl mx-auto px-2 py-3 font-academic">
    <div class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden">
        
        {{-- Garis Atas Hijau Solid --}}
        <div class="h-1.5 bg-emerald-600 w-full"></div>

        {{-- 1. HEADER --}}
        <div class="px-5 py-4 border-b border-gray-100 bg-white">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 bg-emerald-600 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-emerald-50">
                        <i class="fa-solid fa-user-check text-xl"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-black text-gray-900 uppercase tracking-tighter italic leading-none">Presensi <span class="text-emerald-600 not-italic">Digital</span></h2>
                        <p class="text-[9px] text-gray-400 font-bold uppercase tracking-[0.2em] mt-1">
                            {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
                        </p>
                    </div>
                </div>

                {{-- INFO AKADEMIK --}}
                <div class="flex flex-wrap items-center gap-2">
                    <div class="bg-slate-900 text-white px-3 py-1.5 rounded-xl flex flex-col items-start shadow-sm min-w-[110px]">
                        <span class="text-[7px] text-slate-400 font-black uppercase tracking-widest leading-none mb-1">Mata Pelajaran</span>
                        <span class="text-[10px] font-black uppercase italic tracking-tight">{{ $jadwal->mapel->nama_mapel ?? $jadwal->mapel->nama }}</span>
                    </div>
                    <div class="bg-emerald-50 border border-emerald-100 px-3 py-1.5 rounded-xl flex flex-col items-start shadow-sm">
                        <span class="text-[7px] text-emerald-600 font-black uppercase tracking-widest leading-none mb-1">Rombel</span>
                        <span class="text-[10px] text-emerald-700 font-black uppercase italic">{{ $jadwal->kelas }}</span>
                    </div>
                    <div class="bg-blue-50 border border-blue-100 px-3 py-1.5 rounded-xl flex flex-col items-start shadow-sm">
                        <span class="text-[7px] text-blue-600 font-black uppercase tracking-widest leading-none mb-1">Jadwal & Waktu</span>
                        <span class="text-[10px] text-blue-700 font-black uppercase tracking-tighter">
                            {{ $jadwal->hari }}, {{ substr($jadwal->jam_mulai, 0, 5) }}-{{ substr($jadwal->jam_selesai, 0, 5) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <form action="{{ Auth::user()->role == 'guru' ? route('guru.absensi.simpan') : route('admin.absensi.simpan') }}" method="POST">
            @csrf
            <input type="hidden" name="jadwal_id" value="{{ $jadwal->id }}">
            <input type="hidden" name="tanggal" value="{{ date('Y-m-d') }}">

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="text-[9px] font-black uppercase tracking-widest text-slate-400 border-b border-slate-50 bg-slate-50/50">
                            <th class="px-6 py-3 w-16 text-center italic">No.</th>
                            <th class="px-6 py-3">Identitas Siswa</th>
                            <th class="px-6 py-3 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse($siswa as $index => $s)
                        <tr class="hover:bg-emerald-50/20 transition-all group">
                            <td class="px-6 py-3 text-[10px] font-black text-slate-300 text-center font-mono italic">
                                {{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}
                            </td>
                            <td class="px-6 py-3">
                                <p class="text-[11px] font-black text-slate-700 uppercase tracking-tight group-hover:text-emerald-700 transition-colors italic leading-none">
                                    {{ $s->nama }}
                                </p>
                                <span class="text-[8px] font-bold text-slate-400 uppercase tracking-tighter opacity-60">NISN: {{ $s->nisn ?? '-' }}</span>
                            </td>
                            <td class="px-6 py-3">
                                <div class="flex justify-center gap-2">
                                    @foreach([
                                        'H' => ['desc' => 'Hadir', 'color' => 'peer-checked:bg-emerald-600 peer-checked:ring-emerald-100'],
                                        'S' => ['desc' => 'Sakit', 'color' => 'peer-checked:bg-amber-500 peer-checked:ring-amber-100'],
                                        'I' => ['desc' => 'Izin', 'color' => 'peer-checked:bg-blue-600 peer-checked:ring-blue-100'],
                                        'A' => ['desc' => 'Alpa', 'color' => 'peer-checked:bg-rose-600 peer-checked:ring-rose-100']
                                    ] as $key => $status)
                                    
                                    @php 
                                        $record = $sudah_absen[$s->id] ?? null;
                                        $oldStatus = $record ? substr(strtoupper($record->status), 0, 1) : 'H'; 
                                    @endphp

                                    <label class="cursor-pointer">
                                        <input type="radio" name="status[{{ $s->id }}]" value="{{ $key }}" 
                                            class="hidden peer" {{ $oldStatus == $key ? 'checked' : '' }} 
                                            {{ $sudah_absen->count() > 0 ? 'disabled' : '' }} required>
                                        
                                        <div class="w-10 h-10 flex flex-col items-center justify-center rounded-xl border-2 border-slate-100 bg-white text-slate-400 shadow-sm transition-all duration-300 peer-checked:border-transparent {{ $status['color'] }} peer-checked:text-white peer-checked:ring-4 peer-checked:scale-110 peer-disabled:opacity-30">
                                            <span class="text-[11px] font-black leading-none">{{ $key }}</span>
                                            <span class="text-[6px] font-black uppercase tracking-tighter mt-1">{{ $status['desc'] }}</span>
                                        </div>
                                    </label>
                                    @endforeach
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="3" class="py-12 text-center italic text-slate-300">Data Kosong</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- FOOTER --}}
            <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <span class="relative flex h-2.5 w-2.5">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full {{ $sudah_absen->count() > 0 ? 'bg-amber-400' : 'bg-emerald-400' }} opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2.5 w-2.5 {{ $sudah_absen->count() > 0 ? 'bg-amber-500' : 'bg-emerald-500' }}"></span>
                    </span>
                    <span class="text-[9px] font-black text-slate-500 uppercase tracking-widest hidden sm:inline">
                        {{ $sudah_absen->count() > 0 ? 'TERKUNCI' : 'SIAP INPUT' }}
                    </span>
                </div>
                
                <div class="flex items-center gap-2">
                    <a href="{{ route('guru.absensi.index') }}" class="bg-slate-800 hover:bg-slate-900 text-white px-5 py-2.5 rounded-xl text-[9px] font-black uppercase tracking-widest transition-all shadow-md active:scale-95 flex items-center gap-2">
                        <i class="fa-solid fa-arrow-left"></i> Kembali
                    </a>

                    @if($sudah_absen->count() == 0)
                        <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white px-7 py-3 rounded-xl text-[9px] font-black uppercase tracking-widest shadow-lg shadow-emerald-100 transition-all active:scale-95 flex items-center gap-2">
                            Simpan Presensi <i class="fa-solid fa-cloud-arrow-up"></i>
                        </button>
                    @endif
                </div>
            </div>
        </form>
    </div>
</div>
@endsection