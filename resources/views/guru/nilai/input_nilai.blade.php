@extends('layouts.guru')

@section('content')
<style>
    .academic-card {
        background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23f1f5f9' fill-opacity='0.4'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
    }
</style>

<div class="font-academic pb-12 max-w-4xl mx-auto">
    {{-- Breadcrumb Formal --}}
    <nav class="flex mb-8 items-center gap-3 text-[10px] uppercase tracking-[0.2em] font-bold text-slate-400">
        <a href="{{ route('guru.nilai.index') }}" class="hover:text-emerald-700 transition-colors">Portofolio Nilai</a>
        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 opacity-30" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
        <a href="{{ route('guru.jadwal.siswa', $jadwal->id) }}" class="hover:text-emerald-700 transition-colors">Basis Data Siswa</a>
        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 opacity-30" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
        <span class="text-slate-800 border-b-2 border-emerald-500/30 pb-0.5">Otentikasi Nilai</span>
    </nav>

    <div class="bg-white rounded-[1.5rem] shadow-2xl shadow-slate-200/50 border border-slate-200 overflow-hidden academic-card">
        
        {{-- Header --}}
        <div class="relative px-10 py-10 border-b border-slate-100 bg-white/80 backdrop-blur-sm">
            <div class="absolute top-0 right-0 p-6 opacity-10">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-24 w-24 text-slate-900" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 14l9-5-9-5-9 5 9 5z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                </svg>
            </div>

            <div class="relative flex justify-between items-start">
                <div>
                    <span class="inline-block bg-emerald-600 text-white text-[9px] font-black px-3 py-1 rounded-md uppercase tracking-[0.2em] mb-4">Dokumen Resmi Akademik</span>
                    <h2 class="text-3xl font-serif font-bold text-slate-900 tracking-tight">Lembar Evaluasi Capaian</h2>
                    <p class="text-xs text-slate-500 mt-2 font-medium">Periode: <span class="text-emerald-700">Tahun Ajaran 2025/2026 (Ganjil)</span></p>
                </div>
                <div class="text-right border-l-2 border-slate-100 pl-6">
                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Mata Pelajaran</p>
                    <p class="text-sm font-bold text-slate-800 uppercase">{{ $jadwal->mapel->nama_mapel }}</p>
                    <p class="text-[10px] font-bold text-emerald-600 mt-1 uppercase tracking-tighter">Kelas {{ $jadwal->kelas }}</p>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('guru.nilai.store', [$jadwal->id, $siswa->id]) }}" class="relative bg-white/60">
            @csrf
            <div class="p-10">
                
                {{-- Student Identity --}}
                <div class="mb-12 flex items-center gap-8 p-8 bg-slate-50/50 rounded-2xl border border-slate-100">
                    <div class="h-20 w-20 rounded-2xl bg-white border border-slate-200 flex items-center justify-center shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-slate-300" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-1">Nama Siswa</p>
                        <h3 class="text-2xl font-bold text-slate-800 tracking-tight uppercase">{{ $siswa->nama }}</h3>
                        <p class="text-sm font-mono font-bold text-emerald-600 mt-1">NISN: {{ $siswa->nisn ?? '11012345' }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-12 mb-16">
                    {{-- Kategori Nilai (Simplified) --}}
                    <div class="space-y-4">
                        <div class="flex items-center gap-2 mb-2">
                            <div class="w-1.5 h-1.5 rounded-full bg-emerald-500"></div>
                            <label class="text-[11px] font-black text-slate-600 uppercase tracking-widest">Kategori Nilai</label>
                        </div>
                        <select name="jenis" required class="w-full bg-white border-2 border-slate-100 rounded-xl px-6 py-5 text-sm font-bold text-slate-700 focus:border-emerald-500 focus:ring-0 transition-all cursor-pointer appearance-none uppercase tracking-wider">
                            <option value="">-- Pilih Kategori --</option>
                            <option value="harian">RATA HARIAN</option>
                            <option value="uts">UTS</option>
                            <option value="uas">UAS</option>
                        </select>
                    </div>

                    {{-- Skor Input --}}
                    <div class="space-y-4">
                        <div class="flex items-center gap-2 mb-2">
                            <div class="w-1.5 h-1.5 rounded-full bg-emerald-500"></div>
                            <label class="text-[11px] font-black text-slate-600 uppercase tracking-widest">Skor Capaian</label>
                        </div>
                        <div class="relative">
                            <input type="number" name="nilai" min="0" max="100" required placeholder="00"
                                class="w-full bg-white border-2 border-slate-100 rounded-xl px-6 py-5 text-4xl font-serif font-black text-emerald-700 focus:border-emerald-500 focus:ring-0 transition-all placeholder-slate-200">
                            <div class="absolute inset-y-0 right-6 flex items-center">
                                <span class="text-[10px] font-black text-slate-300 uppercase tracking-widest">Points</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="flex flex-col md:flex-row gap-4 pt-10 border-t border-slate-100">
                    <a href="{{ route('guru.jadwal.siswa', $jadwal->id) }}" 
                       class="flex-1 inline-flex items-center justify-center text-[11px] font-bold text-slate-400 hover:text-slate-600 bg-slate-50 px-8 py-5 rounded-xl border border-slate-200 transition-all uppercase tracking-widest">
                        Batal
                    </a>

                    <button type="submit" 
                            class="flex-[2] inline-flex items-center justify-center bg-slate-900 hover:bg-emerald-800 text-white px-8 py-5 rounded-xl font-bold text-[11px] transition-all shadow-xl shadow-slate-200 active:scale-[0.98] uppercase tracking-[0.3em]">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-3 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                        </svg>
                        Simpan Nilai
                    </button>
                </div>
            </div>
        </form>

        <div class="bg-slate-900 px-10 py-6 text-center">
            <p class="text-[9px] text-slate-500 font-bold uppercase tracking-[0.4em]">
                Sistem Informasi Akademik SMAN 1 Jejangkit
            </p>
        </div>
    </div>
</div>
@endsection