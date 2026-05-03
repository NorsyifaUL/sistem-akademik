@extends('layouts.guru')

@section('content')
<div class="font-academic pb-12 px-6 max-w-7xl mx-auto">
    {{-- Notifikasi Sukses --}}
    @if(session('success'))
    <div class="mb-6 p-4 bg-emerald-600 text-white rounded-xl text-[10px] font-black uppercase tracking-[0.2em] shadow-lg flex justify-between items-center animate-fade-in">
        <span><i class="fas fa-check-circle mr-2"></i> {{ session('success') }}</span>
        <button onclick="this.parentElement.remove()" class="opacity-50 hover:opacity-100"><i class="fas fa-times"></i></button>
    </div>
    @endif

    {{-- Header Content --}}
    <div class="mb-6 flex flex-col md:flex-row justify-between items-center bg-white p-6 rounded-2xl border border-slate-100 shadow-sm gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight uppercase">PENGEMBANGAN DIRI</h1>
            <div class="flex items-center gap-4 mt-1">
                <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Kategori: <span class="text-emerald-700">Ekstrakurikuler</span></p>
                <div class="w-1 h-1 rounded-full bg-slate-300"></div>
                <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Semester: <span class="text-slate-900">{{ $setting->semester ?? 'Ganjil' }} — 2025/2026</span></p>
            </div>
        </div>
        <a href="{{ route('guru.raport.index') }}" class="px-5 py-2.5 bg-slate-100 border border-slate-200 rounded-lg text-[10px] font-black text-slate-600 uppercase tracking-widest hover:bg-slate-200 transition-all flex items-center gap-2">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        {{-- Form Input (KIRI) --}}
        <div class="lg:col-span-5 order-2 lg:order-1">
            <div class="bg-white p-8 rounded-2xl border border-slate-200 shadow-sm sticky top-6">
                <div class="flex items-center gap-3 mb-6 border-b border-slate-50 pb-4">
                    <div class="w-1.5 h-6 bg-emerald-600 rounded-full"></div>
                    <h3 class="text-[11px] font-black text-slate-800 uppercase tracking-[0.3em]">Input Nilai Baru</h3>
                </div>

                <form action="{{ route('guru.nilai.eskul.store') }}" method="POST" class="space-y-6">
                    @csrf
                    <input type="hidden" name="siswa_id" value="{{ $siswa->id }}">
                    
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Nama Ekstrakurikuler</label>
                        <input type="text" id="nama_ekskul" name="nama_ekskul" onkeyup="generateEskulDesc()" placeholder="CONTOH: PRAMUKA / FUTSAL" class="w-full px-4 py-3 rounded-xl border-2 border-slate-100 bg-slate-50/50 focus:bg-white focus:border-emerald-600 text-xs font-bold text-slate-700 transition-all outline-none uppercase" required>
                    </div>
                    
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1 text-center block">Predikat Nilai</label>
                        <div class="flex gap-2">
                            @foreach(['A', 'B', 'C', 'D'] as $v)
                            <label class="flex-1 cursor-pointer">
                                <input type="radio" name="predikat" value="{{ $v }}" class="hidden peer" required onchange="generateEskulDesc()">
                                <div class="py-3 text-center border-2 border-slate-100 rounded-xl text-xs font-black text-slate-400 peer-checked:bg-emerald-700 peer-checked:text-white peer-checked:border-emerald-700 hover:bg-slate-50 transition-all uppercase">
                                    {{ $v }}
                                </div>
                            </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Deskripsi Otomatis</label>
                        <textarea id="keterangan_eskul" name="keterangan" rows="5" class="w-full px-5 py-4 border-2 border-slate-100 rounded-xl focus:border-emerald-600 text-[11px] font-medium text-slate-600 leading-relaxed transition-all outline-none resize-none" placeholder="Isi nama ekskul dan pilih predikat..." required></textarea>
                    </div>

                    <button type="submit" class="w-full bg-[#064e3b] text-white py-4 rounded-xl font-black text-[10px] uppercase tracking-[0.4em] hover:bg-slate-900 transition-all shadow-lg active:scale-95">
                        <i class="fas fa-save mr-2"></i> Simpan Data
                    </button>
                </form>
            </div>
        </div>

        {{-- Tabel Rekap (KANAN) --}}
        <div class="lg:col-span-7 order-1 lg:order-2">
            <div class="bg-white border border-slate-100 rounded-2xl shadow-sm overflow-hidden">
                <div class="p-6 border-b border-slate-50 bg-slate-50/30 flex items-center gap-4">
                    <div class="w-12 h-12 bg-[#064e3b] text-white rounded-xl flex items-center justify-center shadow-md font-black text-lg border-2 border-emerald-50">
                        {{ strtoupper(substr($siswa->nama, 0, 1)) }}
                    </div>
                    <div>
                        <h2 class="text-lg font-black text-slate-900 tracking-tight uppercase leading-none">{{ $siswa->nama }}</h2>
                        <p class="text-[9px] font-bold text-slate-400 uppercase tracking-[0.2em] mt-1 italic">NISN: {{ $siswa->nisn }}</p>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-slate-50/50 border-b border-slate-100">
                            <tr class="text-[9px] font-black text-slate-400 uppercase tracking-widest">
                                <th class="py-4 px-6 text-center" width="5%">NO</th>
                                <th class="py-4 px-4 text-left">KEGIATAN</th>
                                <th class="py-4 px-4 text-center">PREDIKAT</th>
                                <th class="py-4 px-4 text-left">KETERANGAN</th>
                                <th class="py-4 px-6 text-right">OPSI</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @forelse($nilaiEskul as $index => $e)
                            <tr class="hover:bg-emerald-50/20 transition-all group">
                                <td class="py-5 px-6 text-center text-xs font-bold text-slate-300 font-mono">{{ $index + 1 }}</td>
                                <td class="py-5 px-4 font-black text-[11px] text-slate-800 uppercase tracking-tight">{{ $e->aspek }}</td>
                                <td class="py-5 px-4 text-center">
                                    <span class="inline-block px-3 py-1 bg-emerald-700 text-white rounded-md font-black text-[10px] uppercase shadow-sm">
                                        {{ $e->predikat }}
                                    </span>
                                </td>
                                <td class="py-5 px-4 text-[10px] text-slate-500 leading-relaxed italic">"{{ $e->keterangan }}"</td>
                                <td class="py-5 px-6 text-right">
                                    <form action="{{ route('guru.nilai.destroy', $e->id) }}" method="POST" onsubmit="return confirm('Hapus data ekskul?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="w-8 h-8 flex items-center justify-center rounded-lg bg-red-50 text-red-400 hover:bg-red-500 hover:text-white transition-all ml-auto">
                                            <i class="fas fa-trash text-[10px]"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="py-32 text-center">
                                    <div class="flex flex-col items-center">
                                        <i class="fas fa-folder-open text-slate-100 text-6xl mb-4"></i>
                                        <p class="text-[10px] font-black text-slate-200 uppercase tracking-[0.4em] italic">Data Belum Tersedia</p>
                                    </div>
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
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;700;900&display=swap');
    .font-academic { font-family: 'Inter', sans-serif; }
    
    @keyframes fade-in {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in { animation: fade-in 0.4s ease-out forwards; }
</style>
@endsection