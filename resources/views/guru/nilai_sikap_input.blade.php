@extends('layouts.guru')

@section('content')
<div class="font-academic pb-12 px-4 max-w-7xl mx-auto">
    {{-- Breadcrumb --}}
    <nav class="flex mb-4 text-gray-400 text-[10px] uppercase tracking-[0.2em] font-bold" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-2">
            <li><a href="{{ route('guru.dashboard') }}" class="hover:text-green-700 transition-colors">Dashboard</a></li>
            <li><span class="mx-2 text-gray-300">/</span></li>
            <li><a href="{{ route('guru.raport.index') }}" class="hover:text-green-700 transition-colors">Manajemen Raport</a></li>
            <li><span class="mx-2 text-gray-300">/</span></li>
            <li class="text-gray-800">Input Nilai Sikap</li>
        </ol>
    </nav>

    {{-- Notifikasi --}}
    @if(session('success'))
    <div class="mb-6 p-4 bg-emerald-600 text-white rounded-xl text-[10px] font-black uppercase tracking-[0.2em] shadow-lg flex justify-between items-center animate-fade-in">
        <span><i class="fas fa-check-circle mr-2"></i> {{ session('success') }}</span>
        <button onclick="this.parentElement.remove()" class="opacity-50 hover:opacity-100"><i class="fas fa-times"></i></button>
    </div>
    @endif

    {{-- Header Section --}}
    <div class="mb-8 bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-sm">
        <div class="border-t-4 border-green-600 px-8 py-6 flex flex-col md:flex-row justify-between items-center gap-4">
            <div class="flex items-center gap-5">
                <div class="w-16 h-16 bg-green-600 text-white rounded-2xl flex items-center justify-center shadow-lg font-black text-2xl border-4 border-green-50">
                    {{ strtoupper(substr($siswa->nama, 0, 1)) }}
                </div>
                <div>
                    <h1 class="text-2xl font-black text-gray-900 tracking-tight uppercase leading-none">{{ $siswa->nama }}</h1>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em] mt-2 italic">
                        Input Penilaian Karakter & Sikap Siswa
                    </p>
                </div>
            </div>
            <div class="flex flex-col items-end">
                <div class="flex gap-2 mb-2">
                    <span class="px-3 py-1 bg-blue-50 text-blue-700 text-[9px] font-black rounded-full border border-blue-100 uppercase tracking-widest">Spiritual</span>
                    <span class="px-3 py-1 bg-purple-50 text-purple-700 text-[9px] font-black rounded-full border border-purple-100 uppercase tracking-widest">Sosial</span>
                </div>
                <a href="{{ route('guru.raport.index') }}" class="text-[10px] font-black text-gray-400 hover:text-red-500 transition-colors uppercase tracking-widest">
                    <i class="fas fa-arrow-left mr-1"></i> Kembali ke Daftar
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        {{-- Form Input --}}
        <div class="lg:col-span-4">
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden sticky top-6">
                <div class="bg-gray-50 px-6 py-4 border-b border-gray-100 text-center">
                    <h3 class="text-[11px] font-black text-gray-700 uppercase tracking-widest">Form Penilaian</h3>
                </div>
                <div class="p-6">
                    <form action="{{ route('guru.nilai.sikap.store') }}" method="POST" class="space-y-6">
                        @csrf
                        <input type="hidden" name="siswa_id" value="{{ $siswa->id }}">
                        <input type="hidden" name="jenis" value="sikap">

                        {{-- Pilihan Aspek --}}
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3 text-center">Pilih Aspek Sikap</label>
                            <div class="grid grid-cols-1 gap-2">
                                <label class="cursor-pointer">
                                    <input type="radio" name="aspek" value="Sikap Spiritual" class="hidden peer" required onchange="generateDescription()">
                                    <div class="py-3 px-4 border border-gray-200 rounded-xl text-[10px] font-black text-gray-400 peer-checked:bg-blue-600 peer-checked:text-white peer-checked:border-blue-600 hover:bg-gray-50 transition-all uppercase flex items-center justify-between">
                                        <span>Sikap Spiritual</span>
                                        <i class="fas fa-pray text-[12px]"></i>
                                    </div>
                                </label>
                                <label class="cursor-pointer">
                                    <input type="radio" name="aspek" value="Sikap Sosial" class="hidden peer" onchange="generateDescription()">
                                    <div class="py-3 px-4 border border-gray-200 rounded-xl text-[10px] font-black text-gray-400 peer-checked:bg-purple-600 peer-checked:text-white peer-checked:border-purple-600 hover:bg-gray-50 transition-all uppercase flex items-center justify-between">
                                        <span>Sikap Sosial</span>
                                        <i class="fas fa-users text-[12px]"></i>
                                    </div>
                                </label>
                            </div>
                        </div>

                        {{-- Pilihan Predikat --}}
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3 text-center">Predikat Nilai</label>
                            <div class="grid grid-cols-4 gap-2">
                                @foreach(['A', 'B', 'C', 'D'] as $v)
                                <label class="cursor-pointer text-center">
                                    <input type="radio" name="nilai" value="{{ $v }}" class="hidden peer" required onchange="generateDescription()">
                                    <div class="py-3 border border-gray-200 rounded-xl text-xs font-black text-gray-400 peer-checked:bg-green-600 peer-checked:text-white peer-checked:border-green-600 hover:bg-gray-50 transition-all">
                                        {{ $v }}
                                    </div>
                                </label>
                                @endforeach
                            </div>
                        </div>

                        {{-- Deskripsi Capaian --}}
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Deskripsi Capaian</label>
                            <textarea id="keterangan" name="keterangan" rows="6" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-xs font-medium text-gray-600 leading-relaxed focus:ring-2 focus:ring-green-500 outline-none resize-none" placeholder="Deskripsi akan terisi otomatis..." required></textarea>
                        </div>

                        <button type="submit" class="w-full bg-gray-900 text-white py-4 rounded-xl font-black text-[10px] uppercase tracking-[0.3em] hover:bg-green-700 transition-all shadow-lg active:scale-95">
                            <i class="fas fa-save mr-2"></i> Simpan Penilaian
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Tabel Rekap --}}
        <div class="lg:col-span-8">
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="bg-gray-100 px-6 py-4 border-b border-gray-200 text-center">
                    <h3 class="text-[11px] font-black text-gray-600 uppercase tracking-widest">Rekap Penilaian Sikap</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse text-left">
                        <thead>
                            <tr class="bg-gray-50 text-[10px] font-black text-gray-400 uppercase tracking-widest border-b">
                                <th class="py-5 px-6 border-r text-center" width="5%">NO</th>
                                <th class="py-5 px-4 border-r" width="20%">ASPEK</th>
                                <th class="py-5 px-4 border-r text-center" width="10%">NILAI</th>
                                <th class="py-5 px-4 border-r">DESKRIPSI</th>
                                <th class="py-5 px-6 text-center" width="10%">AKSI</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($nilaiSikap as $index => $n)
                            <tr class="hover:bg-green-50/20 transition-all">
                                <td class="py-5 px-6 text-center text-xs font-bold text-gray-300 border-r font-mono">{{ $index + 1 }}</td>
                                <td class="py-5 px-4 border-r">
                                    <span class="text-[10px] font-black uppercase tracking-tight {{ $n->aspek == 'Sikap Spiritual' ? 'text-blue-600' : 'text-purple-600' }}">
                                        {{ $n->aspek }}
                                    </span>
                                </td>
                                <td class="py-5 px-4 text-center border-r">
                                    <span class="inline-block px-3 py-1 rounded font-black text-[10px] bg-green-600 text-white uppercase">
                                        {{ $n->predikat ?? '-' }}
                                    </span>
                                </td>
                                <td class="py-5 px-4 text-[11px] text-gray-500 italic border-r leading-relaxed">
                                    "{{ $n->keterangan }}"
                                </td>
                                <td class="py-5 px-6 text-center">
                                    <form action="{{ route('guru.nilai.destroy', $n->id) }}" method="POST" onsubmit="return confirm('Hapus data ini?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-red-400 hover:text-red-600 transition-colors">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="py-20 text-center text-gray-300 text-[10px] font-black uppercase tracking-widest italic">Belum Ada Data</td>
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
    /**
     * Fungsi untuk mengisi deskripsi secara otomatis
     */
    function generateDescription() {
        const aspekRadio = document.querySelector('input[name="aspek"]:checked');
        const predikatRadio = document.querySelector('input[name="nilai"]:checked');
        const textarea = document.getElementById('keterangan');

        // Jika keduanya sudah dipilih, baru eksekusi
        if (aspekRadio && predikatRadio) {
            const aspek = aspekRadio.value.trim();
            const predikat = predikatRadio.value.trim();

            const dataKalimat = {
                'Sikap Spiritual': {
                    'A': "Sangat taat dalam beribadah, selalu menunjukkan rasa syukur, dan menjadi teladan bagi teman-teman dalam menghargai perbedaan agama.",
                    'B': "Menunjukkan sikap taat beribadah dan rasa syukur dengan baik. Sudah konsisten dalam berdoa sebelum dan sesudah melakukan aktivitas.",
                    'C': "Mulai menunjukkan ketaatan dalam beribadah, namun terkadang masih perlu diingatkan untuk konsisten dalam berdoa.",
                    'D': "Perlu bimbingan ekstra dalam meningkatkan ketaatan beribadah dan pembiasaan rasa syukur sehari-hari."
                },
                'Sikap Sosial': {
                    'A': "Sangat jujur, disiplin, dan memiliki tanggung jawab yang tinggi dalam setiap tugas. Sangat santun dalam bertutur kata kepada guru dan teman.",
                    'B': "Menunjukkan sikap jujur, disiplin, dan santun dengan baik. Sudah mampu bekerja sama dalam kelompok secara konsisten.",
                    'C': "Cukup disiplin dan jujur, namun perlu ditingkatkan lagi dalam hal tanggung jawab menyelesaikan tugas kelompok.",
                    'D': "Sering membutuhkan pengingat untuk bersikap disiplin dan memerlukan bantuan dalam membangun komunikasi yang santun."
                }
            };

            // Masukkan kalimat ke textarea jika datanya ada
            if (dataKalimat[aspek] && dataKalimat[aspek][predikat]) {
                textarea.value = dataKalimat[aspek][predikat];
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