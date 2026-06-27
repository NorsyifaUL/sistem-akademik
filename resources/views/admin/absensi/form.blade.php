@extends('layouts.admin')

@section('content')
<div class="p-4 space-y-6 animate-fade-in">
    {{-- Header --}}
    <div class="px-1">
        <h1 class="text-lg font-black text-slate-800 tracking-tight uppercase leading-none">
            Form <span class="text-green-600">Absensi</span>
        </h1>
        <p class="text-[9px] text-slate-400 font-bold uppercase tracking-[0.2em] mt-1.5">
            SIAKAD SMAN 1 JEJANGKIT
        </p>
    </div>

    {{-- Info Card --}}
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden flex items-center">
        <div class="w-1.5 h-full bg-green-600 self-stretch"></div>
        <div class="p-4">
            <h2 class="text-[11px] font-black text-slate-700 uppercase tracking-widest">
                {{ $jadwal->mapel->nama_mapel ?? $jadwal->mapel->nama }}
            </h2>
            <div class="flex items-center gap-3 mt-1">
                <span class="text-[9px] font-bold text-green-700 uppercase bg-green-50 px-2 py-0.5 rounded">{{ $jadwal->guru->nama }}</span>
                <span class="text-slate-300">|</span>
                <span class="text-[9px] font-bold text-slate-500 uppercase">{{ $jadwal->kelas }}</span>
            </div>
        </div>
    </div>

    @php
        $routeAction = Auth::user()->role == 'guru' ? route('guru.absensi.simpan') : route('admin.absensi.simpan');
    @endphp

    <form action="{{ $routeAction }}" method="POST">
        @csrf
        <input type="hidden" name="jadwal_id" value="{{ $jadwal->id }}">

        <div class="bg-white shadow-sm rounded-xl border border-slate-200 overflow-hidden">
            {{-- Toolbar --}}
            <div class="p-3 border-b border-slate-100 bg-slate-50 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <label class="text-[9px] font-black text-slate-500 uppercase tracking-widest">Tanggal</label>
                    <input type="date" name="tanggal" value="{{ date('Y-m-d') }}"
                        class="border border-slate-200 rounded-lg px-3 py-1.5 text-[10px] font-bold text-slate-700 focus:ring-2 focus:ring-green-500 focus:outline-none w-32"
                        required>
                </div>
            </div>

            {{-- Table --}}
            <div class="overflow-x-auto">
                <table class="w-full text-[11px]">
                    <thead class="bg-slate-800 text-white uppercase tracking-widest">
                        <tr>
                            <th class="px-6 py-3 text-left w-12">No</th>
                            <th class="px-6 py-3 text-left">Nama Siswa</th>
                            <th class="px-6 py-3 text-center w-40">Kehadiran</th>
                            <th class="px-6 py-3 text-left w-64">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($siswas as $index => $siswa)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-6 py-3 text-slate-400 font-bold">{{ $index + 1 }}</td>
                            <td class="px-6 py-3 font-bold text-slate-700">{{ $siswa->nama }}</td>
                            <td class="px-6 py-3">
                                <select name="status[{{ $siswa->id }}]"
                                    class="border border-slate-200 rounded-lg px-3 py-1.5 w-full focus:ring-2 focus:ring-green-500 outline-none font-black text-[10px] uppercase"
                                    required>
                                    <option value="H" class="text-green-600">Hadir</option>
                                    <option value="S" class="text-amber-600">Sakit</option>
                                    <option value="I" class="text-blue-600">Izin</option>
                                    <option value="A" class="text-rose-600">Alpa</option>
                                </select>
                            </td>
                            <td class="px-6 py-3">
                                <input type="text" name="keterangan[{{ $siswa->id }}]"
                                    class="border border-slate-200 rounded-lg px-3 py-1.5 w-full text-[10px] focus:border-green-500 outline-none transition-all"
                                    placeholder="Opsional...">
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Footer Actions --}}
        <div class="mt-6 flex items-center gap-2">
            <button type="submit" 
                class="bg-green-600 hover:bg-green-700 text-white px-6 py-2.5 rounded-lg font-black text-[10px] uppercase tracking-widest shadow-sm transition-all active:scale-95">
                Simpan Presensi
            </button>
            <a href="{{ Auth::user()->role == 'guru' ? route('guru.jadwal') : route('admin.jadwal.index') }}"
               class="bg-slate-100 hover:bg-slate-200 text-slate-600 px-6 py-2.5 rounded-lg font-black text-[10px] uppercase tracking-widest transition-all">
               Batal
            </a>
        </div>
    </form>
</div>

<style>
    .animate-fade-in { animation: fadeIn 0.4s ease-out forwards; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(5px); } to { opacity: 1; transform: translateY(0); } }
</style>
@endsection