@extends('layouts.admin')

@section('content')
<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-800">Konfigurasi Raport Semester</h2>
    <p class="text-sm text-gray-500">Atur informasi akademik global untuk SMAN 1 Jejangkit</p>
</div>

@if(session('success'))
<div class="mb-4 p-4 bg-green-100 border-l-4 border-green-500 text-green-700 text-sm font-medium rounded shadow-sm">
    {{ session('success') }}
</div>
@endif

<div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
    <div class="p-6 border-b border-gray-100 bg-gray-50">
        <h3 class="font-bold text-gray-700 flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
            </svg>
            Form Pengaturan Akademik
        </h3>
    </div>

    <form action="{{ route('admin.settings.update') }}" method="POST" class="p-8 space-y-6">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Tahun Pelajaran</label>
                <input type="text" name="tahun_ajaran" value="{{ $setting->tahun_ajaran }}" 
                       class="w-full px-4 py-3 rounded-xl border-gray-200 focus:border-blue-500 focus:ring-blue-500 text-sm shadow-sm transition-all" 
                       placeholder="Contoh: 2024/2025">
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Semester Aktif</label>
                <select name="semester" class="w-full px-4 py-3 rounded-xl border-gray-200 focus:border-blue-500 focus:ring-blue-500 text-sm shadow-sm transition-all">
                    <option value="1" {{ $setting->semester == '1' ? 'selected' : '' }}>1 (Ganjil)</option>
                    <option value="2" {{ $setting->semester == '2' ? 'selected' : '' }}>2 (Genap)</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Tanggal Cetak Raport</label>
                <input type="date" name="tgl_raport" value="{{ $setting->tgl_raport }}" 
                       class="w-full px-4 py-3 rounded-xl border-gray-200 focus:border-blue-500 focus:ring-blue-500 text-sm shadow-sm transition-all">
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Nama Kepala Sekolah</label>
                <input type="text" name="nama_kepsek" value="{{ $setting->nama_kepsek }}" 
                       class="w-full px-4 py-3 rounded-xl border-gray-200 focus:border-blue-500 focus:ring-blue-500 text-sm shadow-sm transition-all">
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">NIP Kepala Sekolah</label>
                <input type="text" name="nip_kepsek" value="{{ $setting->nip_kepsek }}" 
                       class="w-full px-4 py-3 rounded-xl border-gray-200 focus:border-blue-500 focus:ring-blue-500 text-sm shadow-sm transition-all">
            </div>
        </div>

        <div class="pt-6 border-t border-gray-100 flex justify-end">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-xl shadow-lg shadow-blue-200 transition-all active:scale-95 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                </svg>
                Simpan Konfigurasi
            </button>
        </div>
    </form>
</div>
@endsection