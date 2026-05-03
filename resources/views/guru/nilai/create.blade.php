@extends('layouts.guru')

@section('content')
<div class="font-academic pb-12 max-w-3xl mx-auto">
    <div class="mb-8">
        <h2 class="text-3xl font-bold text-gray-900 tracking-tight">Input Nilai Siswa</h2>
        <p class="text-gray-500 mt-1 italic text-sm">Kelola daftar penilaian akademik kurikulum SMAN 1 Jejangkit</p>
    </div>

    <div class="bg-white rounded-[2rem] shadow-sm border border-gray-200 overflow-hidden">
        <form action="{{ route('guru.nilai.store', [$jadwal->id, $siswa->id]) }}" method="POST">
            @csrf
            <div class="p-10">
                <div class="space-y-8">
                    <div>
                        <label class="block text-[11px] font-black text-gray-400 uppercase tracking-widest mb-3 ml-1">Nama Lengkap Siswa</label>
                        <input type="text" value="{{ $siswa->nama }}" disabled 
                               class="w-full bg-gray-50 border border-gray-100 rounded-2xl px-6 py-4 text-sm font-bold text-gray-500 italic">
                    </div>

                    <div>
                        <label class="block text-[11px] font-black text-gray-400 uppercase tracking-widest mb-3 ml-1">Kategori Penilaian</label>
                        <select name="jenis" required class="w-full bg-gray-50 border border-gray-200 rounded-2xl px-6 py-4 text-sm font-bold text-gray-700 focus:ring-2 focus:ring-green-500/20 outline-none">
                            <option value="harian">Nilai Harian</option>
                            <option value="uts">UTS (Tengah Semester)</option>
                            <option value="uas">UAS (Akhir Semester)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-[11px] font-black text-gray-400 uppercase tracking-widest mb-3 ml-1">Masukan Skor (0-100)</label>
                        <input type="number" name="nilai" min="0" max="100" required placeholder="Contoh: 85, 90..." 
                               class="w-full bg-white border border-gray-200 rounded-2xl px-6 py-4 text-sm font-bold text-gray-800 focus:border-green-500 outline-none transition-all">
                    </div>

                    <div class="p-5 bg-blue-50 rounded-2xl border border-blue-100 flex items-center gap-4 text-blue-700">
                        <div class="bg-blue-500 text-white rounded-full p-1.5 shadow-lg shadow-blue-200">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"/></svg>
                        </div>
                        <p class="text-[11px] font-bold uppercase tracking-wider">Pastikan kategori penilaian sudah sesuai.</p>
                    </div>
                </div>
            </div>

            <div class="px-10 py-8 bg-gray-50/50 border-t border-gray-100 flex items-center gap-4">
                <a href="{{ url()->previous() }}" class="flex-1 text-center py-4 text-[11px] font-bold text-red-400 uppercase tracking-widest hover:bg-red-50 rounded-2xl transition">
                    Kembali
                </a>
                <button type="submit" class="flex-[2] bg-blue-600 hover:bg-blue-700 text-white py-4 rounded-2xl font-bold text-[11px] uppercase tracking-widest shadow-xl shadow-blue-100 transition-all active:scale-95">
                    Simpan Nilai
                </button>
            </div>
        </form>
    </div>
</div>
@endsection