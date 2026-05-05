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
            <li class="text-emerald-800 font-semibold">Penilaian Sikap</li>
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
                    <p class="text-xs text-emerald-700 font-medium">Input Lembar Observasi Karakter Siswa</p>
                </div>
            </div>
            <div class="text-right">
                <p class="text-[10px] font-bold text-emerald-800/40 uppercase tracking-widest mb-1 text-center md:text-right">Status Penilaian</p>
                <div class="flex gap-2">
                    <span class="px-2 py-1 bg-white border border-emerald-200 text-emerald-800 text-[10px] font-bold rounded shadow-sm">TA 2025/2026</span>
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
                        <i class="fas fa-edit mr-2 text-emerald-400"></i> Panel Input Nilai
                    </h3>
                </div>
                <div class="p-6">
                    <form action="{{ route('guru.nilai.sikap.store') }}" method="POST" class="space-y-5">
                        @csrf
                        <input type="hidden" name="siswa_id" value="{{ $siswa->id }}">
                        <input type="hidden" name="jenis" value="sikap">

                        {{-- Aspek --}}
                        <div>
                            <label class="block text-xs font-bold text-emerald-900 mb-2">Aspek Penilaian</label>
                            <select name="aspek" id="aspekSelect" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded text-xs font-medium focus:ring-1 focus:ring-emerald-600 focus:bg-white outline-none transition-all" onchange="generateDescription()" required>
                                <option value="">-- Pilih Aspek --</option>
                                <option value="Sikap Spiritual">Sikap Spiritual</option>
                                <option value="Sikap Sosial">Sikap Sosial</option>
                            </select>
                        </div>

                        {{-- Predikat --}}
                        <div>
                            <label class="block text-xs font-bold text-emerald-900 mb-2">Predikat / Nilai</label>
                            <div class="flex gap-2">
                                @foreach(['A', 'B', 'C', 'D'] as $v)
                                <label class="flex-1 cursor-pointer group">
                                    <input type="radio" name="nilai" value="{{ $v }}" class="hidden peer" required onchange="generateDescription()">
                                    <div class="py-2 text-center border border-slate-200 rounded text-xs font-bold text-slate-500 group-hover:bg-emerald-50 peer-checked:bg-emerald-700 peer-checked:text-white peer-checked:border-emerald-700 transition-all">
                                        {{ $v }}
                                    </div>
                                </label>
                                @endforeach
                            </div>
                        </div>

                        {{-- Deskripsi --}}
                        <div>
                            <label class="block text-xs font-bold text-emerald-900 mb-2">Deskripsi Capaian</label>
                            <textarea id="keterangan" name="keterangan" rows="8" 
                                class="w-full px-4 py-3 bg-white border border-slate-200 rounded text-xs font-medium text-slate-600 leading-relaxed focus:ring-1 focus:ring-emerald-600 outline-none resize-none transition-all" 
                                placeholder="Pilih aspek & nilai untuk memunculkan draft deskripsi..." required></textarea>
                            <p class="mt-2 text-[10px] text-emerald-600 italic font-medium text-center">* Deskripsi otomatis dapat diedit kembali.</p>
                        </div>

                        <button type="submit" class="w-full bg-emerald-800 text-white py-3 rounded font-bold text-xs uppercase tracking-widest hover:bg-emerald-900 transition-all shadow-md active:scale-95">
                            <i class="fas fa-save mr-2"></i> Simpan Data
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Tabel Rekap --}}
        <div class="lg:col-span-8">
            <div class="bg-white rounded-lg border border-emerald-200 shadow-sm overflow-hidden">
                <div class="bg-emerald-50 px-6 py-3 border-b border-emerald-100 flex justify-between items-center">
                    <h3 class="text-xs font-bold text-emerald-900 uppercase tracking-wider">Riwayat Penilaian Karakter</h3>
                    <span class="hidden md:block text-[10px] font-bold text-emerald-700/50 uppercase tracking-tight">NISN: {{ $siswa->nisn }}</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse text-left">
                        <thead>
                            <tr class="bg-white text-[10px] font-bold text-emerald-800/40 uppercase tracking-widest border-b border-emerald-50">
                                <th class="py-4 px-6 text-center" width="5%">No</th>
                                <th class="py-4 px-4" width="25%">Aspek Penilaian</th>
                                <th class="py-4 px-4 text-center" width="10%">Nilai</th>
                                <th class="py-4 px-4">Deskripsi Observasi</th>
                                <th class="py-4 px-6 text-center" width="10%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-emerald-50">
                            @forelse($nilaiSikap as $index => $n)
                            <tr class="hover:bg-emerald-50/30 transition-colors">
                                <td class="py-4 px-6 text-center text-xs font-semibold text-emerald-800/30">{{ $index + 1 }}</td>
                                <td class="py-4 px-4">
                                    <span class="text-[10px] font-bold px-3 py-1 rounded-full {{ $n->aspek == 'Sikap Spiritual' ? 'bg-emerald-100 text-emerald-700 border border-emerald-200' : 'bg-teal-100 text-teal-700 border border-teal-200' }}">
                                        {{ strtoupper($n->aspek) }}
                                    </span>
                                </td>
                                <td class="py-4 px-4 text-center">
                                    <span class="inline-block px-3 py-1 bg-emerald-800 text-white rounded font-black text-[10px] shadow-sm">
                                        {{ $n->predikat ?? ($n->nilai ?? '-') }}
                                    </span>
                                </td>
                                <td class="py-4 px-4 text-[11px] text-slate-600 leading-normal font-medium italic">
                                    "{{ $n->keterangan }}"
                                </td>
                                <td class="py-4 px-6 text-center">
                                    <form action="{{ route('guru.nilai.destroy', $n->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus penilaian ini?')">
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
                                    <i class="fas fa-folder-open mb-2 block text-xl opacity-20"></i> Belum ada data terekam
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
    function generateDescription() {
        const aspekSelect = document.getElementById('aspekSelect');
        const predikatRadio = document.querySelector('input[name="nilai"]:checked');
        const textarea = document.getElementById('keterangan');

        if (aspekSelect.value && predikatRadio) {
            const aspek = aspekSelect.value;
            const predikat = predikatRadio.value;

            const dataKalimat = {
                'Sikap Spiritual': {
                    'A': "Sangat taat dalam beribadah, selalu menunjukkan rasa syukur, aktif dalam kegiatan keagamaan di sekolah, dan menjadi teladan bagi siswa lain dalam menghargai perbedaan agama.",
                    'B': "Menunjukkan sikap taat beribadah dan rasa syukur dengan baik. Sudah konsisten dalam berdoa sebelum dan sesudah melakukan aktivitas rutin.",
                    'C': "Mulai menunjukkan ketaatan dalam beribadah, namun masih memerlukan pengingat rutin untuk konsisten dalam menjalankan ibadah harian.",
                    'D': "Memerlukan bimbingan intensif dalam meningkatkan ketaatan beribadah serta kesadaran untuk menunjukkan rasa syukur dalam lingkungan sekolah."
                },
                'Sikap Sosial': {
                    'A': "Memiliki kejujuran, disiplin, dan tanggung jawab yang sangat tinggi dalam tugas. Sangat santun dalam bertutur kata serta memiliki inisiatif tinggi dalam membantu teman.",
                    'B': "Menunjukkan sikap jujur, disiplin, dan santun dengan baik. Sudah mampu bekerja sama secara aktif dalam kelompok dan menyelesaikan tugas tepat waktu.",
                    'C': "Menunjukkan sikap cukup disiplin dan jujur, namun perlu ditingkatkan lagi dalam aspek tanggung jawab tugas kelompok serta kesantunan berkomunikasi.",
                    'D': "Memerlukan pengawasan rutin untuk bersikap disiplin dan jujur, serta perlu bimbingan dalam membangun komunikasi yang santun dengan warga sekolah."
                }
            };

            if (dataKalimat[aspek] && dataKalimat[aspek][predikat]) {
                textarea.value = dataKalimat[aspek][predikat];
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