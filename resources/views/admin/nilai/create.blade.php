@extends('layouts.admin')

@section('content')
<div class="p-6 space-y-6 animate-fade-in">
    {{-- Header --}}
    <div class="mb-6">
        <h2 class="text-2xl font-black text-gray-800 tracking-tight uppercase">Input Nilai Akademik</h2>
        <p class="text-xs font-bold text-gray-400 mt-1 uppercase tracking-widest">Pilih parameter kelas untuk mulai mengisi nilai</p>
    </div>

    {{-- Form Pilih Parameter --}}
    <div class="max-w-4xl bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden border-t-4 border-t-blue-600">
        <div class="p-6 bg-blue-50/30 border-b border-blue-50">
            <h3 class="text-[10px] font-black text-blue-600 uppercase tracking-widest flex items-center gap-2">
                <i class="fa-solid fa-filter"></i> Parameter Penilaian
            </h3>
        </div>

        <form action="{{ route('admin.nilai.store') }}" method="POST" class="p-8 space-y-8">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                {{-- Pilih Kelas --}}
                <div class="space-y-3">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Pilih Kelas</label>
                    <div class="relative group">
                        <select name="kelas" class="w-full px-4 py-3 rounded-xl border-gray-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-sm font-bold shadow-sm transition-all outline-none appearance-none cursor-pointer text-gray-700" required>
                            <option value="" disabled selected>-- Pilih Kelas --</option>
                            @foreach(['X 1', 'X 2', 'XI 1', 'XI 2', 'XII IPA', 'XII IPS'] as $kls)
                                <option value="{{ $kls }}">{{ $kls }}</option>
                            @endforeach
                        </select>
                        <i class="fa-solid fa-chevron-down absolute right-4 top-4 text-gray-300 text-[10px] pointer-events-none group-focus-within:rotate-180 transition-transform"></i>
                    </div>
                </div>

                {{-- Pilih Mata Pelajaran --}}
                <div class="space-y-3">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Mata Pelajaran</label>
                    <div class="relative group">
                        <select name="mapel_id" class="w-full px-4 py-3 rounded-xl border-gray-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-sm font-bold shadow-sm transition-all outline-none appearance-none cursor-pointer text-gray-700" required>
                            <option value="" disabled selected>-- Pilih Mapel --</option>
                            @foreach($mapels as $mapel)
                                <option value="{{ $mapel->id }}">{{ $mapel->nama_mapel }}</option>
                            @endforeach
                        </select>
                        <i class="fa-solid fa-chevron-down absolute right-4 top-4 text-gray-300 text-[10px] pointer-events-none group-focus-within:rotate-180 transition-transform"></i>
                    </div>
                </div>
            </div>

            <div class="bg-gray-50 rounded-2xl p-6 border border-gray-100 border-dashed text-center">
                <i class="fa-solid fa-circle-info text-blue-500 mb-2"></i>
                <p class="text-[10px] text-gray-500 font-bold uppercase tracking-wide">
                    Setelah menekan tombol lanjut, daftar siswa akan muncul berdasarkan kelas yang dipilih.
                </p>
            </div>

            <div class="pt-6 flex justify-between items-center border-t border-gray-50">
                <a href="{{ route('admin.nilai.index') }}" class="text-xs font-black text-gray-400 uppercase tracking-widest hover:text-rose-500 transition-colors">
                    <i class="fa-solid fa-arrow-left-long mr-2"></i> Kembali
                </a>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-black py-4 px-12 rounded-xl shadow-lg shadow-blue-200 transition-all active:scale-95 uppercase text-xs tracking-[0.2em]">
                    Lanjutkan <i class="fa-solid fa-chevron-right ml-2"></i>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection