@extends('layouts.guru')

@section('content')
<div class="font-sans pb-12 px-6 max-w-7xl mx-auto text-slate-800">
    {{-- Breadcrumb --}}
    <nav class="flex mb-6 text-slate-400 text-xs font-medium" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-2">
            <li><a href="{{ route('guru.dashboard') }}" class="hover:text-emerald-700">Dashboard</a></li>
            <li><i class="fas fa-chevron-right text-[8px] mx-1"></i></li>
            <li><a href="{{ route('guru.raport.index') }}" class="hover:text-emerald-700">Akademik</a></li>
            <li><i class="fas fa-chevron-right text-[8px] mx-1"></i></li>
            <li class="text-emerald-800 font-semibold">Pengembangan Diri</li>
        </ol>
    </nav>

    {{-- Header Section --}}
    <div class="mb-6 bg-white border border-emerald-100 rounded-lg shadow-sm">
        <div class="px-8 py-5 flex flex-col md:flex-row justify-between items-center bg-emerald-50/50 border-b border-emerald-100 rounded-t-lg">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-emerald-800 text-white rounded flex items-center justify-center font-bold text-xl shadow-md border-2 border-emerald-700">
                    {{ strtoupper(substr($siswa->nama, 0, 1)) }}
                </div>
                <div>
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight">{{ $siswa->nama }}</h1>
                    <p class="text-xs text-emerald-700 font-medium tracking-wide">Lembar Penilaian Ekstrakurikuler</p>
                </div>
            </div>
            <div class="text-right">
                <p class="text-[10px] font-bold text-emerald-800/40 uppercase tracking-widest mb-1 text-center md:text-right">Status Akademik</p>
                <div class="flex gap-2">
                    <span class="px-2 py-1 bg-white border border-emerald-200 text-emerald-800 text-[10px] font-bold rounded shadow-sm">TA: 2025/2026</span>
                    <a href="{{ route('guru.raport.index') }}" class="px-2 py-1 bg-emerald-800 hover:bg-emerald-900 text-white text-[10px] font-bold rounded transition-all shadow-sm">
                        <i class="fas fa-reply mr-1"></i> KEMBALI
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        {{-- Form Input --}}
        <div class="lg:col-span-4">
            <div class="bg-white rounded-lg border border-emerald-200 shadow-sm overflow-hidden">
                <div class="bg-emerald-800 px-5 py-3 border-b border-emerald-900">
                    <h3 class="text-xs font-bold text-white uppercase tracking-wider flex items-center">
                        <i class="fas fa-plus-circle mr-2 text-emerald-400"></i> Form Input Nilai
                    </h3>
                </div>
                <div class="p-6">
                    <form action="{{ route('guru.nilai.eskul.store') }}" method="POST" class="space-y-5">
                        @csrf
                        <input type="hidden" name="siswa_id" value="{{ $siswa->id }}">

                        {{-- Nama Ekskul --}}
                        <div>
                            <label class="block text-xs font-bold text-emerald-900 mb-2">Nama Ekstrakurikuler</label>
                            <input type="text" id="nama_ekskul" name="nama_ekskul" onkeyup="generateEskulDesc()" 
                                placeholder="CONTOH: PRAMUKA" 
                                class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded text-xs font-medium focus:ring-1 focus:ring-emerald-600 focus:bg-white outline-none uppercase transition-all" required>
                        </div>

                        {{-- Predikat --}}
                        <div>
                            <label class="block text-xs font-bold text-emerald-900 mb-2">Predikat Nilai</label>
                            <div class="flex gap-2">
                                @foreach(['A', 'B', 'C', 'D'] as $v)
                                <label class="flex-1 cursor-pointer group">
                                    <input type="radio" name="predikat" value="{{ $v }}" class="hidden peer" required onchange="generateEskulDesc()">
                                    <div class="py-2 text-center border border-slate-200 rounded text-xs font-bold text-slate-500 group-hover:bg-emerald-50 peer-checked:bg-emerald-700 peer-checked:text-white peer-checked:border-emerald-700 transition-all">
                                        {{ $v }}
                                    </div>
                                </label>
                                @endforeach
                            </div>
                        </div>

                        {{-- Deskripsi --}}
                        <div>
                            <label class="block text-xs font-bold text-emerald-900 mb-2 text-center md:text-left">Keterangan Capaian</label>
                            <textarea id="keterangan_eskul" name="keterangan" rows="7" 
                                class="w-full px-4 py-3 bg-white border border-slate-200 rounded text-xs font-medium text-slate-600 leading-relaxed focus:ring-1 focus:ring-emerald-600 outline-none resize-none transition-all" 
                                placeholder="Draft akan otomatis terisi..." required></textarea>
                            <p class="mt-2 text-[10px] text-emerald-600 italic font-medium text-center">* Deskripsi dapat diedit secara manual.</p>
                        </div>

                        <button type="submit" class="w-full bg-emerald-800 text-white py-3 rounded font-bold text-xs uppercase tracking-widest hover:bg-emerald-900 transition-all shadow-md active:scale-95">
                            <i class="fas fa-save mr-2"></i> Simpan Nilai
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Tabel Rekap --}}
        <div class="lg:col-span-8">
            <div class="bg-white rounded-lg border border-emerald-200 shadow-sm overflow-hidden">
                <div class="bg-emerald-50 px-6 py-3 border-b border-emerald-100 flex justify-between items-center">
                    <h3 class="text-xs font-bold text-emerald-900 uppercase tracking-wider text-center md:text-left">Rekapitulasi Pengembangan Diri</h3>
                    <span class="hidden md:block text-[10px] font-bold text-emerald-700/50 uppercase tracking-tight">NISN: {{ $siswa->nisn }}</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse text-left">
                        <thead>
                            <tr class="bg-white text-[10px] font-bold text-emerald-800/40 uppercase tracking-widest border-b border-emerald-50">
                                <th class="py-4 px-6 text-center" width="5%">No</th>
                                <th class="py-4 px-4" width="25%">Nama Kegiatan</th>
                                <th class="py-4 px-4 text-center" width="10%">Predikat</th>
                                <th class="py-4 px-4">Keterangan / Capaian</th>
                                <th class="py-4 px-6 text-center" width="10%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-emerald-50">
                            @forelse($nilaiEskul as $index => $e)
                            <tr class="hover:bg-emerald-50/30 transition-colors">
                                <td class="py-4 px-6 text-center text-xs font-semibold text-emerald-800/30">{{ $index + 1 }}</td>
                                <td class="py-4 px-4">
                                    <span class="text-xs font-bold text-slate-800 uppercase tracking-tight">
                                        {{ $e->aspek }}
                                    </span>
                                </td>
                                <td class="py-4 px-4 text-center">
                                    <span class="inline-block px-3 py-1 bg-emerald-100 text-emerald-800 rounded font-black text-[10px] uppercase border border-emerald-200 shadow-sm">
                                        {{ $e->predikat }}
                                    </span>
                                </td>
                                <td class="py-4 px-4 text-[11px] text-slate-600 leading-normal font-medium italic">
                                    "{{ $e->keterangan }}"
                                </td>
                                <td class="py-4 px-6 text-center">
                                    <form action="{{ route('guru.nilai.destroy', $e->id) }}" method="POST" onsubmit="return confirm('Hapus data penilaian ini?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="w-8 h-8 rounded-full bg-red-50 text-red-400 hover:bg-red-600 hover:text-white transition-all flex items-center justify-center mx-auto shadow-sm">
                                            <i class="fas fa-trash-alt text-[10px]"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="py-20 text-center text-emerald-200 text-[10px] font-bold uppercase tracking-[0.2em] italic">
                                    <i class="fas fa-folder-open mb-2 block text-xl opacity-20"></i> Data Kegiatan Belum Tersedia
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function generateEskulDesc() {
        const namaEskul = document.getElementById('nama_ekskul').value.trim().toUpperCase();
        const predikat = document.querySelector('input[name="predikat"]:checked')?.value;
        const textarea = document.getElementById('keterangan_eskul');

        if (namaEskul && predikat) {
            const dataKalimat = {
                'A': `Sangat aktif dalam mengikuti kegiatan ${namaEskul}, menunjukkan kedisiplinan yang tinggi, serta mampu memimpin teman-temannya dalam setiap latihan.`,
                'B': `Menunjukkan partisipasi yang aktif dan konsisten dalam kegiatan ${namaEskul} serta mampu menguasai teknik dasar dengan baik.`,
                'C': `Cukup aktif dalam mengikuti kegiatan ${namaEskul}, namun perlu meningkatkan lagi kedisiplinan dan kehadiran dalam jadwal latihan rutin.`,
                'D': `Kurang aktif dalam mengikuti kegiatan ${namaEskul}, memerlukan bimbingan ekstra dalam kedisiplinan dan penguasaan teknik dasar.`
            };

            if (dataKalimat[predikat]) {
                textarea.value = dataKalimat[predikat];
            }
        }
    }
</script>

<style>
    .font-sans { font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; }
    
    @keyframes fade-in {
        from { opacity: 0; transform: translateY(-5px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in { animation: fade-in 0.3s ease-out forwards; }
</style>
@endsection