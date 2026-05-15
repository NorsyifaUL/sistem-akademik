@extends('layouts.guru')

@section('content')
<style>
    input[type=number] { padding-right: 2px; }
    .font-academic { font-family: 'Inter', sans-serif; }
    
    input::-webkit-outer-spin-button,
    input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
    /* Style tambahan untuk textarea agar lebih rapi */
    .capaian-textarea:focus {
        background-color: white;
        font-style: normal;
        color: #1a202c;
    }
</style>

<div class="max-w-7xl mx-auto px-2 py-4 font-academic text-gray-800">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        
        {{-- HEADER FILTER --}}
        <div class="border-t-4 border-green-600 px-6 py-4 bg-gray-50/50 border-b">
            <div class="flex flex-col lg:flex-row justify-between items-center gap-4">
                <div>
                    <h3 class="text-sm font-black text-gray-700 uppercase tracking-widest">Input Nilai Kolektif</h3>
                    <div class="flex items-center gap-2 mt-1">
                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-tight">TA: {{ $setting->tahun_ajaran ?? '-' }}</p>
                        <span class="text-gray-200">|</span>
                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-tight">Semester: 
                            <span class="text-green-600 font-black">{{ $setting->semester ?? ($semester ?? '-') }}</span>
                        </p>
                    </div>
                </div>

                <form action="{{ route('guru.lihat_nilai') }}" method="GET" class="flex flex-wrap items-center gap-2">
                    <select name="jadwal_id" required class="text-[11px] font-bold border-gray-200 rounded-lg py-1.5 focus:ring-green-500 uppercase tracking-tighter">
                        <option value="">-- Pilih Jadwal --</option>
                        @foreach($jadwals as $j)
                            <option value="{{ $j->id }}" {{ request('jadwal_id') == $j->id ? 'selected' : '' }}>
                                {{ $j->mapel->nama_mapel }} - {{ $j->kelas }}
                            </option>
                        @endforeach
                    </select>

                    <select name="jenis_nilai" required class="text-[11px] font-bold border-gray-200 rounded-lg py-1.5 focus:ring-green-500 uppercase tracking-tighter">
                        <option value="">-- Jenis Nilai --</option>
                        <option value="harian" {{ strtolower(request('jenis_nilai')) == 'harian' ? 'selected' : '' }}>Harian (UH)</option>
                        <option value="sikap" {{ strtolower(request('jenis_nilai')) == 'sikap' ? 'selected' : '' }}>Sikap</option>
                        <option value="ekskul" {{ strtolower(request('jenis_nilai')) == 'ekskul' ? 'selected' : '' }}>Ekstrakurikuler</option>
                        <option value="uts" {{ strtolower(request('jenis_nilai')) == 'uts' ? 'selected' : '' }}>UTS</option>
                        <option value="uas" {{ strtolower(request('jenis_nilai')) == 'uas' ? 'selected' : '' }}>UAS</option>
                        <option value="akhir" {{ strtolower(request('jenis_nilai')) == 'akhir' ? 'selected' : '' }}>Rekap Akhir</option>
                    </select>

                    <button type="submit" class="bg-gray-900 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-[10px] font-black uppercase tracking-widest transition-all shadow-sm active:scale-95">
                        Tampilkan
                    </button>
                </form>
            </div>
        </div>

        @if($jadwalTerpilih && request('jenis_nilai'))
            @php $jenis = strtolower(request('jenis_nilai')); @endphp
            <form action="{{ route('guru.nilai.simpan_massal') }}" method="POST">
                @csrf
                <input type="hidden" name="jadwal_id" value="{{ $jadwalTerpilih->id }}">
                <input type="hidden" name="jenis" value="{{ $jenis }}">

                <div class="px-6 py-2.5 bg-amber-50/50 border-b border-amber-100 flex justify-between items-center">
                    <span class="text-[10px] text-amber-700 font-black uppercase tracking-widest">
                        <i class="fas fa-edit mr-1.5"></i> Entry Mode: {{ strtoupper($jenis) }}
                    </span>
                    <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white px-5 py-1.5 rounded-lg shadow-md font-black text-[10px] uppercase tracking-widest transition-all active:scale-95">
                        <i class="fas fa-save mr-2"></i> Simpan Permanen
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse table-auto">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-100 text-gray-400 text-[9px] font-black uppercase tracking-widest">
                                <th class="px-4 py-3 text-center w-12 border-r border-gray-100">#</th>
                                <th class="px-6 py-3 min-w-[220px] border-r border-gray-100">Nama Siswa</th>
                                
                                @if($jenis == 'harian')
                                    <th class="px-2 py-3 text-center w-20 border-r border-gray-100">UH 1</th>
                                    <th class="px-2 py-3 text-center w-20 border-r border-gray-100">UH 2</th>
                                    <th class="px-2 py-3 text-center w-20 border-r border-gray-100">UH 3</th>
                                    <th class="px-2 py-3 text-center w-20 border-r border-gray-100">UH 4</th>
                                    <th class="px-3 py-3 text-center bg-green-50 text-green-700 w-24">Rata²</th>
                                @endif

                                @if(in_array($jenis, ['uts', 'uas']))
                                    <th class="px-4 py-3 text-center bg-green-50 text-green-700 w-32 font-black">NILAI {{ strtoupper($jenis) }}</th>
                                @endif

                                @if(in_array($jenis, ['sikap', 'ekskul']))
                                    <th class="px-4 py-3 text-center bg-blue-50 text-blue-700 w-32 font-black">Predikat</th>
                                    <th class="px-6 py-3 font-bold uppercase text-[8px] tracking-widest">Catatan / Deskripsi</th>
                                @endif

                                @if($jenis == 'akhir')
                                    <th class="px-1 py-3 text-center w-16 bg-blue-50/50 font-bold border-r border-white">Harian</th>
                                    <th class="px-1 py-3 text-center w-16 bg-blue-50/50 font-bold border-r border-white">UTS</th>
                                    <th class="px-1 py-3 text-center w-16 bg-blue-50/50 font-bold border-r border-white">UAS</th>
                                    <th class="px-4 py-3 text-center bg-green-100/50 text-green-800 w-24 font-black">Nilai Akhir</th>
                                    <th class="px-6 py-3 font-bold uppercase text-[8px] tracking-widest">Deskripsi Capaian</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($siswaData as $index => $s)
                            <tr class="hover:bg-green-50/10 transition-colors student-row">
                                <td class="px-4 py-3 text-center text-gray-300 font-bold text-[10px] border-r border-gray-50">{{ $index + 1 }}</td>
                                <td class="px-6 py-3 font-black text-gray-700 uppercase text-[11px] tracking-tight border-r border-gray-50">{{ $s->nama }}</td>
                                
                                @if($jenis == 'harian')
                                    <td class="px-2 py-2 text-center border-r border-gray-50">
                                        <input type="number" name="nilai[{{ $s->id }}][uh1]" value="{{ $s->uh1 ?? '' }}" min="0" max="100" class="uh-input w-16 text-center border-gray-200 rounded-md text-[10px] font-bold py-1 focus:ring-green-500 transition-all">
                                    </td>
                                    <td class="px-2 py-2 text-center border-r border-gray-50">
                                        <input type="number" name="nilai[{{ $s->id }}][uh2]" value="{{ $s->uh2 ?? '' }}" min="0" max="100" class="uh-input w-16 text-center border-gray-200 rounded-md text-[10px] font-bold py-1 focus:ring-green-500 transition-all">
                                    </td>
                                    <td class="px-2 py-2 text-center border-r border-gray-50">
                                        <input type="number" name="nilai[{{ $s->id }}][uh3]" value="{{ $s->uh3 ?? '' }}" min="0" max="100" class="uh-input w-16 text-center border-gray-200 rounded-md text-[10px] font-bold py-1 focus:ring-green-500 transition-all">
                                    </td>
                                    <td class="px-2 py-2 text-center border-r border-gray-50">
                                        <input type="number" name="nilai[{{ $s->id }}][uh4]" value="{{ $s->uh4 ?? '' }}" min="0" max="100" class="uh-input w-16 text-center border-gray-200 rounded-md text-[10px] font-bold py-1 focus:ring-green-500 transition-all">
                                    </td>
                                    <td class="px-3 py-2 bg-green-50/30 text-center">
                                        <input type="number" name="nilai[{{ $s->id }}][rata_rata]" value="{{ $s->harian ?? '' }}" class="final-input w-full text-center border-none bg-transparent font-black text-green-700 text-xs pointer-events-none" readonly>
                                    </td>
                                @endif

                                @if(in_array($jenis, ['uts', 'uas']))
                                    <td class="px-4 py-2 bg-green-50/30 text-center">
                                        <input type="number" name="nilai[{{ $s->id }}][angka]" value="{{ $s->nilai_existing ?? '' }}" class="w-24 text-center border-gray-200 rounded-lg font-black text-xs py-1.5 focus:ring-green-600 shadow-sm" required min="0" max="100">
                                    </td>
                                @endif

                                @if(in_array($jenis, ['sikap', 'ekskul']))
                                    <td class="px-4 py-2 bg-blue-50/20 text-center">
                                        <select name="nilai[{{ $s->id }}][angka]" class="w-28 text-center border-gray-200 rounded-lg font-black text-[10px] py-1.5 focus:ring-blue-500 uppercase">
                                            <option value="">-- Pilih --</option>
                                            <option value="Sangat Baik" {{ ($s->nilai_existing ?? '') == 'Sangat Baik' ? 'selected' : '' }}>Sangat Baik</option>
                                            <option value="Baik" {{ ($s->nilai_existing ?? '') == 'Baik' ? 'selected' : '' }}>Baik</option>
                                            <option value="Cukup" {{ ($s->nilai_existing ?? '') == 'Cukup' ? 'selected' : '' }}>Cukup</option>
                                            <option value="Kurang" {{ ($s->nilai_existing ?? '') == 'Kurang' ? 'selected' : '' }}>Kurang</option>
                                        </select>
                                    </td>
                                    <td class="px-6 py-2">
                                        <textarea name="nilai[{{ $s->id }}][deskripsi]" rows="1" class="w-full text-[10px] border-gray-100 rounded-lg focus:ring-blue-500 italic text-gray-500 px-3 py-1 bg-gray-50/50 resize-none">{{ $s->deskripsi_existing ?? '' }}</textarea>
                                    </td>
                                @endif

                                @if($jenis == 'akhir')
                                    <td class="px-1 py-3 text-center bg-blue-50/20 text-[10px] font-bold text-gray-500 border-r border-white">{{ $s->harian ?? 0 }}</td>
                                    <td class="px-1 py-3 text-center bg-blue-50/20 text-[10px] font-bold text-gray-500 border-r border-white">{{ $s->uts ?? 0 }}</td>
                                    <td class="px-1 py-3 text-center bg-blue-50/20 text-[10px] font-bold text-gray-500 border-r border-white">{{ $s->uas ?? 0 }}</td>
                                    <td class="px-4 py-3 bg-green-100/30 text-center font-black text-green-700 text-xs">
                                        @php $na = (($s->harian ?? 0) + ($s->uts ?? 0) + ($s->uas ?? 0)) / 3; @endphp
                                        <span class="nilai-akhir-text">{{ round($na) }}</span>
                                        <input type="hidden" name="nilai[{{ $s->id }}][angka]" value="{{ round($na) }}" class="input-na-hidden">
                                    </td>
                                    <td class="px-6 py-2">
                                        <textarea name="nilai[{{ $s->id }}][deskripsi]" rows="2" 
                                            placeholder="Tulis deskripsi..." 
                                            data-siswa="{{ $s->nama }}"
                                            class="capaian-textarea w-full text-[10px] border-gray-100 rounded-lg focus:ring-green-500 italic text-gray-500 px-3 py-1 bg-gray-50/50 resize-none transition-all">{{ $s->deskripsi_existing ?? $s->deskripsi_tampil }}</textarea>
                                    </td>
                                @endif
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </form>
        @else
            <div class="py-24 text-center">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-green-50 rounded-2xl mb-4 text-green-200">
                    <i class="fas fa-layer-group text-3xl"></i>
                </div>
                <h4 class="text-sm font-black text-gray-700 uppercase tracking-widest">Panel Input Kolektif</h4>
                <p class="text-[11px] text-gray-400 mt-1 max-w-xs mx-auto italic tracking-tight">Silakan tentukan jadwal dan jenis nilai untuk mengisi data siswa secara massal.</p>
            </div>
        @endif
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const rows = document.querySelectorAll('.student-row');
        
        rows.forEach(row => {
            // Logika Perhitungan Harian
            const inputs = row.querySelectorAll('.uh-input');
            const finalInput = row.querySelector('.final-input');
            
            if(inputs.length > 0 && finalInput) {
                inputs.forEach(input => {
                    input.addEventListener('input', () => {
                        let total = 0, count = 0;
                        inputs.forEach(i => {
                            if(i.value !== '') { 
                                total += parseFloat(i.value); 
                                count++; 
                            }
                        });
                        finalInput.value = count > 0 ? Math.round(total / count) : 0;
                    });
                });
            }

            // Logika Otomatis Capaian Kompetensi (Khusus mode Rekap Akhir)
            const textarea = row.querySelector('.capaian-textarea');
            const nilaiAkhirText = row.querySelector('.nilai-akhir-text');
            
            if(textarea && nilaiAkhirText) {
                // Fungsi untuk generate narasi
                const generateNarasi = (nilai) => {
                    if (nilai >= 85) return "Menunjukkan penguasaan yang sangat baik dalam memahami kompetensi mata pelajaran ini.";
                    if (nilai >= 75) return "Menunjukkan penguasaan yang baik dalam memahami kompetensi mata pelajaran ini.";
                    if (nilai > 0) return "Perlu bimbingan dalam memahami materi dan pengerjaan tugas.";
                    return "";
                };

                // Isi otomatis jika saat load textarea masih kosong
                if(textarea.value.trim() === "") {
                    textarea.value = generateNarasi(parseInt(nilaiAkhirText.innerText));
                }

                // Opsional: Biarkan textarea auto-resize saat diketik
                textarea.addEventListener('input', function() {
                    this.style.height = 'auto';
                    this.style.height = (this.scrollHeight) + 'px';
                });
            }
        });
    });
</script>
@endsection