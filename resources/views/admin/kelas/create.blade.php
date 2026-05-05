@extends('layouts.admin')

@section('content')
<div class="p-6 space-y-6 animate-fade-in">
    {{-- Header Section --}}
    <div class="mb-6">
        <h2 class="text-2xl font-black text-gray-800 tracking-tight uppercase">Tambah Data Kelas</h2>
        <p class="text-xs font-bold text-gray-400 mt-1 uppercase tracking-widest">Manajemen Ruang Kelas SMAN 1 Jejangkit</p>
    </div>

    {{-- Form Container --}}
    <div class="max-w-4xl bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden border-t-4 border-t-blue-600">
        <form action="{{ route('admin.kelas.store') }}" method="POST" class="p-8 space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Input Nama Kelas --}}
                <div class="space-y-2">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Nama Kelas</label>
                    <div class="relative group">
                        <input type="text" name="nama_kelas" value="{{ old('nama_kelas') }}" 
                               placeholder="Misal: XI 1" 
                               class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-sm font-bold shadow-sm transition-all outline-none text-gray-700 bg-gray-50/30 uppercase" 
                               required>
                        <i class="fa-solid fa-chalkboard absolute right-4 top-4 text-gray-300 text-xs pointer-events-none transition-colors group-focus-within:text-blue-500"></i>
                    </div>
                </div>

                {{-- Pilih Wali Kelas (Mengambil dari tabel gurus) --}}
                <div class="space-y-2">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Wali Kelas</label>
                    <div class="relative group">
                        <select name="guru_id" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-sm font-bold shadow-sm transition-all outline-none appearance-none cursor-pointer text-gray-700 bg-gray-50/30" required>
                            <option value="" disabled selected>-- Pilih Wali Kelas --</option>
                            @foreach($gurus as $guru)
                                <option value="{{ $guru->id }}" {{ old('guru_id') == $guru->id ? 'selected' : '' }}>
                                    {{ $guru->nama }}
                                </option>
                            @endforeach
                        </select>
                        <i class="fa-solid fa-chevron-down absolute right-4 top-4 text-gray-300 text-[10px] pointer-events-none transition-transform group-focus-within:rotate-180"></i>
                    </div>
                </div>
            </div>

            {{-- Info Tip --}}
            <div class="p-4 bg-blue-50 rounded-xl border border-blue-100 flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-blue-600 flex items-center justify-center flex-shrink-0 shadow-sm">
                    <i class="fa-solid fa-lightbulb text-white text-xs"></i>
                </div>
                <p class="text-[11px] text-blue-700 font-bold leading-tight uppercase tracking-wider">
                    Pastikan nama kelas unik dan wali kelas sudah sesuai dengan data di tabel guru.
                </p>
            </div>

            {{-- Action Buttons --}}
            <div class="pt-6 flex justify-between items-center border-t border-gray-50">
                <a href="{{ route('admin.kelas.index') }}" 
                   class="px-8 py-2.5 rounded-lg text-xs font-black text-white bg-rose-600 hover:bg-rose-700 transition-all shadow-md shadow-rose-100 uppercase tracking-widest">
                    Batal
                </a>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-black py-4 px-12 rounded-xl shadow-lg shadow-blue-100 transition-all active:scale-95 uppercase text-xs tracking-[0.2em]">
                    Simpan Data Kelas
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in {
        animation: fadeIn 0.5s ease-out forwards;
    }
</style>
@endsection