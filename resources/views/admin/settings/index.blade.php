@extends('layouts.admin')

@section('content')
<div class="mb-6 animate-fade-in">
    <h2 class="text-2xl font-black text-gray-800 uppercase tracking-tight">Konfigurasi Raport Semester</h2>
    <p class="text-sm text-gray-500 font-medium">Atur informasi akademik global untuk SMAN 1 Jejangkit</p>
</div>

{{-- Notifikasi Sukses --}}
@if(session('success'))
<div class="mb-6 p-4 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-700 text-sm font-bold rounded-r-xl shadow-sm flex items-center gap-3 animate-bounce-short">
    <i class="fa-solid fa-circle-check text-lg"></i>
    {{ session('success') }}
</div>
@endif

{{-- Notifikasi Error Validasi --}}
@if($errors->any())
<div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 text-sm font-bold rounded-r-xl shadow-sm">
    <ul class="list-disc ml-5">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden ring-1 ring-black/5">
    <div class="p-6 border-b border-gray-50 bg-gray-50/50">
        <h3 class="font-black text-gray-700 flex items-center gap-3 uppercase text-xs tracking-widest">
            <span class="h-8 w-8 bg-blue-600 rounded-lg flex items-center justify-center text-white shadow-lg shadow-blue-200">
                <i class="fa-solid fa-sliders"></i>
            </span>
            Form Pengaturan Akademik
        </h3>
    </div>

    {{-- 
        PENTING: 
        1. Action diarahkan ke admin.settings.update
        2. Tambahkan @method('PUT') karena di Route kita pakai PUT
    --}}
    <form action="{{ route('admin.settings.update') }}" method="POST" class="p-8 space-y-8">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            {{-- Tahun Pelajaran --}}
            <div class="space-y-2">
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] ml-1">Tahun Pelajaran</label>
                <input type="text" name="tahun_ajaran" value="{{ old('tahun_ajaran', $setting->tahun_ajaran) }}" 
                       class="w-full px-5 py-4 rounded-2xl border-gray-100 bg-gray-50 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-sm font-bold transition-all" 
                       placeholder="Contoh: 2024/2025">
            </div>

            {{-- Semester --}}
            <div class="space-y-2">
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] ml-1">Semester Aktif</label>
                <select name="semester" class="w-full px-5 py-4 rounded-2xl border-gray-100 bg-gray-50 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-sm font-bold transition-all cursor-pointer">
                    <option value="1" {{ old('semester', $setting->semester) == '1' ? 'selected' : '' }}>1 (Ganjil)</option>
                    <option value="2" {{ old('semester', $setting->semester) == '2' ? 'selected' : '' }}>2 (Genap)</option>
                </select>
            </div>

            {{-- Tanggal Raport --}}
            <div class="space-y-2">
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] ml-1">Tanggal Cetak Raport</label>
                {{-- Format tanggal disesuaikan agar bisa dibaca input date --}}
                <input type="date" name="tgl_raport" 
                       value="{{ old('tgl_raport', $setting->tgl_raport ? \Carbon\Carbon::parse($setting->tgl_raport)->format('Y-m-d') : '') }}" 
                       class="w-full px-5 py-4 rounded-2xl border-gray-100 bg-gray-50 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-sm font-bold transition-all">
            </div>

            {{-- Nama Kepsek --}}
            <div class="space-y-2">
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] ml-1">Nama Kepala Sekolah</label>
                <input type="text" name="nama_kepsek" value="{{ old('nama_kepsek', $setting->nama_kepsek) }}" 
                       class="w-full px-5 py-4 rounded-2xl border-gray-100 bg-gray-50 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-sm font-bold transition-all"
                       placeholder="Nama Lengkap & Gelar">
            </div>

            {{-- NIP Kepsek --}}
            <div class="space-y-2 md:col-span-2">
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] ml-1">NIP Kepala Sekolah</label>
                <input type="text" name="nip_kepsek" value="{{ old('nip_kepsek', $setting->nip_kepsek) }}" 
                       class="w-full px-5 py-4 rounded-2xl border-gray-100 bg-gray-50 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-sm font-bold transition-all"
                       placeholder="Masukkan NIP Resmi">
            </div>
        </div>

        <div class="pt-8 border-t border-gray-50 flex justify-end">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-black py-4 px-10 rounded-2xl shadow-xl shadow-blue-200 transition-all active:scale-95 flex items-center gap-3 uppercase text-[10px] tracking-[0.2em]">
                <i class="fa-solid fa-cloud-arrow-up text-lg"></i>
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>

<style>
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    .animate-fade-in { animation: fadeIn 0.4s ease-out forwards; }
    .animate-bounce-short { animation: bounce 1s ease-in-out 1; }
    @keyframes bounce { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-5px); } }
</style>
@endsection