@extends('layouts.guru')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8 font-academic">
    <div class="bg-white rounded-md shadow-sm border border-gray-200 overflow-hidden">
        <div class="h-[3px] bg-[#28a745]"></div>

        {{-- Header & Filter Section --}}
        <div class="px-6 py-5 border-b border-gray-200 bg-white">
            <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6">
                <div class="text-left">
                    <h2 class="text-lg font-black text-gray-800 leading-none uppercase tracking-tight">Input Nilai Kolektif</h2>
                    <div class="flex items-center gap-4 mt-2">
                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">TA: {{ $setting->tahun_ajaran ?? '2025/2026' }}</p>
                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest text-green-600">Semester: {{ $semesterAktif ?? 'Ganjil' }}</p>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-2 w-full lg:w-auto">
                    <select id="select-jadwal" class="border border-gray-200 rounded px-3 py-2 text-[12px] font-bold text-gray-600 focus:outline-none focus:ring-1 focus:ring-green-500 min-w-[250px] bg-white">
                        <option value="">-- Pilih Jadwal (Mapel - Kelas) --</option>
                        @foreach($jadwals as $j)
                            <option value="{{ $j->id }}" {{ (isset($jadwalTerpilih) && $jadwalTerpilih->id == $j->id) ? 'selected' : '' }}>
                                {{ $j->mapel->nama_mapel ?? 'Mapel Unknown' }} - {{ $j->nama_display_kelas }}
                            </option>
                        @endforeach
                    </select>

                    <select id="select-jenis" class="border border-gray-200 rounded px-3 py-2 text-[12px] font-bold text-gray-600 focus:outline-none focus:ring-1 focus:ring-green-500 min-w-[180px] bg-white">
                        <option value="">-- Pilih Jenis Nilai --</option>
                        <option value="harian" {{ $jenisNilai == 'harian' ? 'selected' : '' }}>Harian</option>
                        <option value="uts" {{ $jenisNilai == 'uts' ? 'selected' : '' }}>UTS</option>
                        <option value="uas" {{ $jenisNilai == 'uas' ? 'selected' : '' }}>UAS</option>
                        <option value="rekap" {{ $jenisNilai == 'rekap' ? 'selected' : '' }}>Rekap Capaian Kompetensi</option>
                    </select>

                    <button type="button" onclick="navigateFilter()" class="bg-[#1a1d23] hover:bg-black text-white font-black py-2 px-6 rounded text-[11px] uppercase transition-all tracking-widest">
                        Tampilkan
                    </button>
                </div>
            </div>
        </div>

        {{-- Content Area --}}
        <div class="min-h-[400px]">
            @if(isset($jadwalTerpilih) && isset($siswaData) && count($siswaData) > 0)
                <form action="{{ route('guru.nilai.simpan_massal') }}" method="POST">
                    @csrf
                    <input type="hidden" name="jadwal_id" value="{{ $jadwalTerpilih->id }}">
                    <input type="hidden" name="jenis" value="{{ $jenisNilai }}">

                    {{-- Sticky Action Bar --}}
                    <div class="px-6 py-3 border-b border-gray-100 flex justify-between items-center bg-white sticky top-0 z-10 shadow-sm">
                        <div class="flex items-center gap-2">
                            <span class="text-[10px] font-black text-orange-600 uppercase tracking-widest">
                                <i class="fa-solid fa-edit mr-1"></i> Mode: {{ $jenisNilai == 'rekap' ? 'Capaian Kompetensi' : strtoupper($jenisNilai) }}
                            </span>
                        </div>
                        <button type="submit" class="bg-[#28a745] hover:bg-green-700 text-white text-[10px] font-black uppercase px-4 py-2 rounded flex items-center gap-2 transition-all active:scale-95">
                            <i class="fa-solid fa-save"></i> Simpan Semua Nilai
                        </button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full border-collapse">
                            <thead>
                                <tr class="bg-white border-b border-gray-100 text-gray-400 text-[10px] font-black uppercase tracking-widest">
                                    <th class="px-6 py-4 w-16 text-center">#</th>
                                    <th class="px-4 py-4 text-left">Nama Siswa</th>
                                    
                                    @if($jenisNilai == 'harian')
                                        <th class="px-2 py-4 text-center w-20 bg-green-50/30 text-green-700 border-l border-gray-100">UH 1</th>
                                        <th class="px-2 py-4 text-center w-20 bg-green-50/30 text-green-700">UH 2</th>
                                        <th class="px-2 py-4 text-center w-20 bg-green-50/30 text-green-700">UH 3</th>
                                        <th class="px-2 py-4 text-center w-20 bg-green-50/30 text-green-700">UH 4</th>
                                        <th class="px-4 py-4 text-center w-24 bg-orange-50 text-orange-700 italic border-l border-orange-100">Rerata</th>
                                    @elseif($jenisNilai == 'rekap')
                                        <th class="px-6 py-4 text-left bg-blue-50/50 text-blue-700 border-l border-gray-100 italic">Deskripsi Capaian Kompetensi (Otomatis & Bisa Diedit)</th>
                                    @else
                                        <th class="px-6 py-4 text-center w-48 bg-green-50/50 text-green-700 border-l border-gray-100">{{ strtoupper($jenisNilai) }}</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @foreach($siswaData as $index => $s)
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="px-6 py-4 text-center text-[11px] font-bold text-gray-300 align-top">{{ $index + 1 }}</td>
                                    <td class="px-4 py-4 text-[12px] font-black text-gray-700 uppercase italic tracking-tight align-top">
                                        {{ $s->nama }}
                                    </td>
                                    
                                    @if($jenisNilai == 'harian')
                                        @foreach(['uh1', 'uh2', 'uh3', 'uh4'] as $uh)
                                        <td class="px-2 py-4 text-center bg-green-50/10">
                                            <input type="number" name="nilai[{{ $s->id }}][{{ $uh }}]" value="{{ $s->$uh }}" class="input-nilai-{{ $s->id }} w-16 border border-gray-200 rounded text-center text-[12px] font-black focus:border-green-500 outline-none" oninput="hitungRataRata('{{ $s->id }}')">
                                        </td>
                                        @endforeach
                                        <td class="px-4 py-4 text-center bg-orange-50/30 italic font-black text-orange-700 text-[12px]">
                                            <span id="rata-{{ $s->id }}">{{ $s->harian ?? '0' }}</span>
                                        </td>

                                    @elseif($jenisNilai == 'rekap')
                                        <td class="px-6 py-4 text-left border-l border-gray-50">
                                            {{-- 1. SIMPAN NILAI ANGKA (HIDDEN) AGAR MASUK KE DB --}}
                                            <input type="hidden" name="nilai[{{ $s->id }}][angka]" value="{{ $s->nilai_akhir_calculated }}">

                                            <div class="flex flex-col gap-2">
                                                {{-- 2. SIMPAN DESKRIPSI (TEXTAREA) --}}
                                                <textarea name="nilai[{{ $s->id }}][deskripsi]" 
                                                          rows="5" 
                                                          data-nilai="{{ $s->nilai_akhir_calculated }}"
                                                          class="capaian-auto w-full min-w-[600px] border border-gray-200 rounded-lg px-4 py-3 text-[11px] font-medium leading-relaxed text-gray-700 focus:border-green-500 focus:ring-1 focus:ring-green-500 outline-none transition-all italic bg-white" 
                                                          placeholder="Memuat deskripsi otomatis...">{{ $s->deskripsi_existing ?? '' }}</textarea>
                                                
                                                <div class="flex justify-between items-center px-1">
                                                    <span class="text-[9px] text-gray-400 font-bold uppercase">
                                                        <i class="fa-solid fa-calculator mr-1"></i> Nilai Akhir: 
                                                        <span class="text-red-600 font-black label-nilai-akhir">{{ $s->nilai_akhir_calculated }}</span>
                                                    </span>
                                                    <span class="text-[9px] text-blue-600 font-black uppercase italic bg-blue-50 px-2 py-0.5 rounded border border-blue-100">
                                                        <i class="fa-solid fa-magic-wand-sparkles mr-1"></i> Auto-Generate Mode
                                                    </span>
                                                </div>
                                            </div>
                                        </td>
                                    @else
                                        <td class="px-6 py-4 text-center bg-green-50/20 border-l border-green-50">
                                            {{-- MODE UTS / UAS: SESUAIKAN INDEX KE [angka] AGAR DIBACA CONTROLLER --}}
                                            <input type="number" step="1" min="0" max="100" 
                                                   name="nilai[{{ $s->id }}][angka]" 
                                                   value="{{ $s->nilai_existing }}" 
                                                   class="w-24 border border-gray-200 rounded px-2 py-1.5 text-center text-[12px] font-black text-green-700 focus:ring-1 focus:ring-green-500 outline-none">
                                        </td>
                                    @endif
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </form>
            @else
                {{-- Empty State tetap sama --}}
                <div class="flex flex-col items-center justify-center py-24">
                    <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mb-4 border border-gray-100">
                        <i class="fa-solid fa-folder-open text-gray-300 text-3xl"></i>
                    </div>
                    <h3 class="text-gray-800 font-black uppercase italic tracking-tighter">Tidak Ada Data</h3>
                    <p class="text-gray-400 text-[10px] font-bold uppercase mt-1 tracking-widest text-center">Pilih Jadwal dan Jenis Nilai <br> untuk menampilkan daftar siswa.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
    function navigateFilter() {
        const j = document.getElementById('select-jadwal').value;
        const n = document.getElementById('select-jenis').value;
        if (!j || !n) return alert('Pilih Jadwal & Jenis Nilai!');
        window.location.href = `{{ route('guru.lihat_nilai') }}?jadwal_id=${j}&jenis_nilai=${n}`;
    }

    function hitungRataRata(id) {
        const ins = document.querySelectorAll(`.input-nilai-${id}`);
        let t = 0, c = 0;
        ins.forEach(i => {
            let v = parseInt(i.value);
            if (!isNaN(v)) { t += v; c++; }
        });
        const disp = document.getElementById(`rata-${id}`);
        if (disp) disp.innerText = c > 0 ? Math.round(t / c) : 0;
    }

    // Generator Narasi Kurikulum Merdeka
    function generateSaran(n) {
        if (n >= 90) return "Menunjukkan penguasaan kompetensi yang sangat baik dalam seluruh materi pembelajaran. Peserta didik mampu mendemonstrasikan pemahaman konsep secara mendalam, kritis, serta memiliki inisiatif tinggi dalam menyelesaikan tugas-tugas kompleks dengan tingkat akurasi yang tinggi.";
        if (n >= 80) return "Menunjukkan penguasaan kompetensi yang baik dalam memahami materi utama. Peserta didik sudah mampu menerapkan teori ke dalam praktik secara sistematis dan menunjukkan keaktifan yang konsisten, namun perlu sedikit peningkatan pada ketelitian detail teknis.";
        if (n >= 75) return "Telah mencapai kriteria ketercapaian tujuan pembelajaran minimal yang ditetapkan. Peserta didik mampu mengikuti alur pembelajaran dengan cukup baik, namun memerlukan penguatan pada aspek konsistensi dan pendalaman materi agar pemahaman konsep lebih kokoh.";
        if (n > 0) return "Mulai menunjukkan upaya dalam penguasaan kompetensi, meskipun masih memerlukan bimbingan intensif dan pendampingan khusus. Diharapkan untuk lebih aktif dalam sesi pengayaan dan meningkatkan frekuensi latihan mandiri guna memahami konsep dasar materi.";
        return "-";
    }

    document.addEventListener('DOMContentLoaded', function() {
        const areas = document.querySelectorAll('.capaian-auto');
        areas.forEach(a => {
            const val = parseInt(a.getAttribute('data-nilai')) || 0;
            const existing = a.value.trim();
            
            // Auto-fill jika data lama kosong
            if (existing === "" || existing === "-" || existing === "0") {
                a.value = generateSaran(val);
            } else {
                a.classList.remove('italic');
            }

            a.addEventListener('input', () => a.classList.remove('italic'));
        });
    });
</script>
@endsection