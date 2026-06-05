@extends('layouts.admin')

@section('content')
<div class="p-4 space-y-4 animate-fade-in">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 px-1">
        <div>
            <h1 class="text-base font-black text-slate-800 tracking-tight uppercase leading-none">
                Edit <span class="text-blue-600">Profil Siswa</span>
            </h1>
            <p class="text-[8px] text-slate-400 font-bold uppercase tracking-[0.1em] mt-1">
                Pembaruan data: <span class="text-slate-600">{{ $siswa->nama }}</span>
            </p>
        </div>
        <a href="{{ route('admin.siswa.index') }}" class="text-[9px] font-black text-slate-400 hover:text-rose-500 transition-colors uppercase tracking-widest flex items-center gap-2">
            <i class="fa-solid fa-arrow-left"></i> Kembali ke Daftar
        </a>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="h-[3px] bg-blue-600 w-full"></div>
        
        <div class="p-4 border-b border-slate-50 bg-slate-50/30">
            <h3 class="text-[9px] font-black text-slate-500 uppercase tracking-widest flex items-center gap-2">
                <i class="fa-solid fa-user-pen text-blue-600 text-[10px]"></i> Formulir Perubahan Data
            </h3>
        </div>

        <form action="{{ route('admin.siswa.update', $siswa->id) }}" method="POST" class="p-6 md:p-8">
            @csrf
            @method('PUT')

            {{-- Error Handling --}}
            @if ($errors->any())
            <div class="bg-rose-50 border border-rose-100 p-4 rounded-xl mb-6">
                <ul class="space-y-1">
                    @foreach ($errors->all() as $error)
                        <li class="text-[9px] text-rose-600 font-bold uppercase tracking-tight flex items-center gap-2">
                            <i class="fa-solid fa-circle-exclamation"></i> {{ $error }}
                        </li>
                    @endforeach
                </ul>
            </div>
            @endif

            {{-- Grid dengan Gap yang Lebih Rapat --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
                {{-- Nama Lengkap (Spans 2 columns) --}}
                <div class="md:col-span-2 group">
                    <label class="block text-[9px] font-black text-slate-400 uppercase tracking-[0.2em] mb-1.5 group-focus-within:text-blue-600 transition-colors">Nama Lengkap Siswa</label>
                    <input type="text" name="nama" value="{{ old('nama', $siswa->nama) }}"
                           class="w-full px-4 py-2 rounded-lg border border-slate-200 bg-slate-50/30 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/5 text-xs font-bold transition-all outline-none uppercase" required>
                </div>

                {{-- NISN --}}
                <div class="group">
                    <label class="block text-[9px] font-black text-slate-400 uppercase tracking-[0.2em] mb-1.5 group-focus-within:text-blue-600 transition-colors">NISN</label>
                    <input type="text" name="nisn" value="{{ old('nisn', $siswa->nisn) }}"
                           class="w-full px-4 py-2 rounded-lg border border-slate-200 bg-slate-50/30 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/5 text-xs font-bold transition-all outline-none tracking-widest" required>
                </div>

                {{-- Pilih Kelas --}}
                <div class="group">
                    <label class="block text-[9px] font-black text-slate-400 uppercase tracking-[0.2em] mb-1.5 group-focus-within:text-blue-600 transition-colors">Kelas</label>
                    <div class="relative">
                        <select name="kelas_id" class="w-full px-4 py-2 rounded-lg border border-slate-200 bg-slate-50/30 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/5 text-xs font-black transition-all outline-none appearance-none cursor-pointer uppercase tracking-wider" required>
                            {{-- Di sini variabel diubah menjadi $semuaKelas --}}
                            @foreach($semuaKelas as $kls)
                                <option value="{{ $kls->id }}" {{ old('kelas_id', $siswa->kelas_id) == $kls->id ? 'selected' : '' }}>
                                    KELAS {{ $kls->nama_kelas }}
                                </option>
                            @endforeach
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-4 flex items-center text-slate-400">
                            <i class="fa-solid fa-chevron-down text-[8px]"></i>
                        </div>
                    </div>
                </div>

                {{-- Email --}}
                <div class="group">
                    <label class="block text-[9px] font-black text-slate-400 uppercase tracking-[0.2em] mb-1.5 group-focus-within:text-blue-600 transition-colors">Email Akses</label>
                    <input type="email" name="email" value="{{ old('email', $siswa->user->email ?? '') }}"
                           class="w-full px-4 py-2 rounded-lg border border-slate-200 bg-slate-50/30 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/5 text-xs font-bold transition-all outline-none" required>
                </div>

                {{-- No WA Ortu --}}
                <div class="group">
                    <label class="block text-[9px] font-black text-slate-400 uppercase tracking-[0.2em] mb-1.5 group-focus-within:text-blue-600 transition-colors">WhatsApp Ortu</label>
                    <input type="text" name="no_wa_ortu" value="{{ old('no_wa_ortu', $siswa->no_wa_ortu) }}"
                           class="w-full px-4 py-2 rounded-lg border border-slate-200 bg-slate-50/30 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/5 text-xs font-bold transition-all outline-none tracking-widest" required>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="mt-10 pt-6 flex flex-col md:flex-row justify-end items-center gap-3 border-t border-slate-50">
                <a href="{{ route('admin.siswa.index') }}" 
                   class="w-full md:w-auto px-8 py-2 rounded-lg text-[9px] font-black text-white bg-rose-600 hover:bg-rose-700 transition-all uppercase tracking-widest text-center shadow-sm shadow-rose-100 active:scale-95">
                    Batal
                </a>
                <button type="submit" class="w-full md:w-auto bg-blue-600 hover:bg-blue-700 text-white font-black py-2 px-10 rounded-lg shadow-sm transition-all active:scale-95 uppercase tracking-widest text-[9px]">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection