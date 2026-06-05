@extends('layouts.admin')

@section('content')
<div class="p-4 space-y-4 animate-fade-in">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 px-1">
        <div>
            <h1 class="text-base font-black text-slate-800 tracking-tight uppercase leading-none">
                Tambah <span class="text-blue-600">Siswa Baru</span>
            </h1>
            <p class="text-[8px] text-slate-400 font-bold uppercase tracking-[0.1em] mt-1">
                Pendaftaran Peserta Didik Baru SMAN 1 Jejangkit
            </p>
        </div>
        <a href="{{ route('admin.siswa.index') }}" class="text-[9px] font-black text-slate-400 hover:text-rose-500 transition-colors uppercase tracking-widest flex items-center gap-2">
            <i class="fa-solid fa-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="h-[3px] bg-blue-600 w-full"></div>
        
        <div class="p-4 border-b border-slate-50 bg-slate-50/30">
            <h3 class="text-[9px] font-black text-slate-500 uppercase tracking-widest flex items-center gap-2">
                <i class="fa-solid fa-user-plus text-blue-600 text-[10px]"></i> Formulir Biodata
            </h3>
        </div>

        <div class="p-6 md:p-8">
            {{-- BAGIAN PESAN ERROR --}}
            @if ($errors->any())
                <div class="mb-6 p-4 bg-rose-50 border-l-4 border-rose-500 rounded-r-xl shadow-sm">
                    <div class="flex items-center mb-2">
                        <i class="fa-solid fa-circle-exclamation text-rose-500 mr-2 text-xs"></i>
                        <h3 class="text-[10px] font-black text-rose-700 uppercase tracking-widest">Gagal Menyimpan Data</h3>
                    </div>
                    <ul class="space-y-1">
                        @foreach ($errors->all() as $error)
                            <li class="text-[9px] text-rose-600 font-bold uppercase tracking-wider">• {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 p-4 bg-amber-50 border-l-4 border-amber-500 rounded-r-xl text-amber-700 text-[9px] font-black uppercase tracking-widest">
                    <i class="fa-solid fa-database mr-2"></i> {{ session('error') }}
                </div>
            @endif

            {{-- FORM START --}}
            <form action="{{ route('admin.siswa.store') }}" method="POST">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-3">
                    {{-- Nama Lengkap --}}
                    <div class="md:col-span-2 group">
                        <label class="block text-[9px] font-black text-slate-400 uppercase tracking-[0.2em] mb-0.5 group-focus-within:text-blue-600 transition-colors">Nama Lengkap Siswa</label>
                        <input type="text" name="nama" placeholder="CONTOH: NORSYIFA ULHASANAH" value="{{ old('nama') }}"
                               class="w-full px-4 py-2 rounded-lg border border-slate-200 bg-slate-50/30 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/5 text-xs font-bold transition-all outline-none uppercase placeholder:text-slate-300" required>
                    </div>

                    {{-- NISN --}}
                    <div class="group">
                        <label class="block text-[9px] font-black text-slate-400 uppercase tracking-[0.2em] mb-0.5 group-focus-within:text-blue-600 transition-colors">NISN</label>
                        <input type="text" name="nisn" placeholder="10 DIGIT NOMOR INDUK" value="{{ old('nisn') }}"
                               class="w-full px-4 py-2 rounded-lg border border-slate-200 bg-slate-50/30 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/5 text-xs font-bold transition-all outline-none tracking-widest placeholder:text-slate-300" required>
                    </div>

                    {{-- Pilih Kelas --}}
                    <div class="group">
                        <label class="block text-[9px] font-black text-slate-400 uppercase tracking-[0.2em] mb-0.5 group-focus-within:text-blue-600 transition-colors text-left">Kelas</label>
                        <div class="relative">
                            {{-- Variabel diubah menjadi $semuaKelas --}}
                            <select name="kelas_id" class="w-full px-4 py-2 rounded-lg border border-slate-200 bg-slate-50/30 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/5 text-xs font-black transition-all outline-none appearance-none cursor-pointer uppercase tracking-wider" required>
                                <option value="" disabled selected>-- PILIH KELAS --</option>
                                @foreach($semuaKelas as $kls)
                                    <option value="{{ $kls->id }}" {{ old('kelas_id') == $kls->id ? 'selected' : '' }}>
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
                        <label class="block text-[9px] font-black text-slate-400 uppercase tracking-[0.2em] mb-0.5 group-focus-within:text-blue-600 transition-colors text-left">Email Akun</label>
                        <input type="email" name="email" placeholder="siswa@mail.com" value="{{ old('email') }}"
                               class="w-full px-4 py-2 rounded-lg border border-slate-200 bg-slate-50/30 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/5 text-xs font-bold transition-all outline-none placeholder:text-slate-300" required>
                    </div>

                    {{-- No WA Ortu --}}
                    <div class="group">
                        <label class="block text-[9px] font-black text-slate-400 uppercase tracking-[0.2em] mb-0.5 group-focus-within:text-blue-600 transition-colors text-left">WhatsApp Orang Tua</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400 text-[10px] font-bold">+62</span>
                            <input type="text" name="no_wa_ortu" placeholder="8XXXXXXXXXX" value="{{ old('no_wa_ortu') }}"
                                   class="w-full pl-11 pr-4 py-2 rounded-lg border border-slate-200 bg-slate-50/30 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/5 text-xs font-bold transition-all outline-none tracking-widest placeholder:text-slate-300" required>
                        </div>
                    </div>
                </div>

                {{-- Info Password --}}
                <div class="mt-4 bg-blue-50/50 p-2.5 rounded-lg border border-blue-100 border-dashed flex items-center gap-2">
                    <i class="fa-solid fa-shield-halved text-blue-500 text-[10px]"></i>
                    <p class="text-[8px] text-blue-700 font-black uppercase tracking-wider leading-none">
                        Password Otomatis: <span class="bg-blue-600 text-white px-1.5 py-0.5 rounded ml-1 lowercase">password123</span>
                    </p>
                </div>

                {{-- Action Buttons --}}
                <div class="mt-6 pt-4 flex flex-col md:flex-row justify-end items-center gap-3 border-t border-slate-50">
                    <a href="{{ route('admin.siswa.index') }}" 
                       class="w-full md:w-auto px-8 py-2 rounded-lg text-[9px] font-black text-white bg-rose-600 hover:bg-rose-700 transition-all uppercase tracking-widest text-center shadow-sm shadow-rose-100 active:scale-95">
                        Batal
                    </a>
                    <button type="submit" class="w-full md:w-auto bg-blue-600 hover:bg-blue-700 text-white font-black py-2 px-10 rounded-lg shadow-sm transition-all active:scale-95 uppercase tracking-widest text-[9px]">
                        Simpan Siswa
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection