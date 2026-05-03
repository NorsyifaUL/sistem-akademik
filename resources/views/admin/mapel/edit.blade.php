@extends('layouts.admin')

@section('content')
<div class="p-6 space-y-6 animate-fade-in">
    {{-- Header --}}
    <div class="mb-6">
        <h1 class="text-2xl font-black text-gray-800 tracking-tight uppercase">Edit Mata Pelajaran</h1>
        <p class="text-xs font-bold text-gray-400 mt-1 uppercase tracking-widest">
            Memperbarui Data: <span class="text-blue-600">{{ $mapel->nama_mapel }}</span>
        </p>
    </div>

    {{-- Form Card --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden border-t-4 border-t-blue-600">
        <div class="p-5 border-b border-gray-50 bg-gray-50/30">
            <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest flex items-center gap-2">
                <i class="fa-solid fa-pen-to-square text-blue-600"></i> Update Informasi Subjek
            </h3>
        </div>

        <form action="{{ route('admin.mapel.update', $mapel->id) }}" method="POST" class="p-8">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 gap-8">
                {{-- Nama Mapel --}}
                <div class="space-y-2">
                    <label class="block text-xs font-black text-gray-700 uppercase tracking-widest">
                        Nama Mata Pelajaran <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-gray-400">
                            <i class="fa-solid fa-book-open text-sm"></i>
                        </span>
                        <input type="text" 
                               name="nama_mapel" 
                               placeholder="Masukkan Nama Mata Pelajaran" 
                               value="{{ old('nama_mapel', $mapel->nama_mapel) }}"
                               class="w-full pl-11 pr-4 py-4 bg-gray-50 border border-gray-200 rounded-xl text-sm font-bold outline-none focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all @error('nama_mapel') border-rose-500 @enderror" 
                               required>
                    </div>
                    @error('nama_mapel')
                        <p class="text-[10px] text-rose-600 font-black uppercase mt-2 tracking-tight flex items-center gap-1">
                            <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Box Info --}}
                <div class="bg-blue-50 rounded-2xl border border-blue-100 border-dashed p-6 flex items-center gap-5">
                    <div class="w-12 h-12 bg-blue-600 rounded-xl flex items-center justify-center text-white shadow-lg shadow-blue-200 shrink-0">
                        <i class="fa-solid fa-info-circle text-xl"></i>
                    </div>
                    <div>
                        <h4 class="text-sm font-black text-blue-800 uppercase tracking-tight">Informasi</h4>
                        <p class="text-[11px] text-blue-600 font-bold leading-relaxed uppercase opacity-80">
                            Perubahan pada nama mata pelajaran akan langsung diperbarui pada jadwal pelajaran, data nilai, dan raport siswa yang terhubung.
                        </p>
                    </div>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="mt-10 pt-8 border-t border-gray-50 flex flex-col md:flex-row justify-end items-center gap-4">
                <a href="{{ route('admin.mapel.index') }}"  
                    class="px-8 py-2.5 rounded-lg text-sm font-bold text-white bg-rose-600 hover:bg-rose-700 transition-all shadow-md shadow-rose-100 uppercase tracking-widest text-center">
                        Batal
                    </a>
                <button type="submit" 
                        class="w-full md:w-auto bg-blue-600 hover:bg-blue-700 text-white font-black py-4 px-12 rounded-xl shadow-xl shadow-blue-100 transition-all active:scale-95 uppercase text-xs tracking-widest flex items-center justify-center gap-2">
                    <i class="fa-solid fa-sync"></i> Update Data
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    .animate-fade-in { animation: fadeIn 0.5s ease-out forwards; }
</style>
@endsection