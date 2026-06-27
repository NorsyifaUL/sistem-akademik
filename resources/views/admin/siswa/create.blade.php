@extends('layouts.admin')

@section('content')
<div class="p-4 space-y-4 animate-fade-in">
    {{-- Header --}}
    <div class="px-1">
        <h1 class="text-lg font-black text-slate-800 tracking-tight uppercase leading-none">
            Tambah <span class="text-blue-600">Siswa Baru</span>
        </h1>
        <p class="text-[9px] text-slate-400 font-bold uppercase tracking-[0.2em] mt-1">
            Pendaftaran Peserta Didik Baru SMAN 1 Jejangkit
        </p>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="h-[3px] bg-blue-600 w-full"></div>
        
        <div class="p-3 border-b border-slate-50 bg-slate-50/30">
            <h3 class="text-[10px] font-black text-slate-500 uppercase tracking-widest flex items-center gap-2">
                <i class="fa-solid fa-user-plus text-blue-600 text-[10px]"></i> Formulir Biodata
            </h3>
        </div>

        <div class="p-5 md:p-6">
            {{-- Pesan Error --}}
            @if ($errors->any() || session('error'))
                <div class="mb-5 p-3 bg-rose-50 border-l-4 border-rose-500 rounded-r-lg text-[9px] font-black text-rose-700 uppercase tracking-wider">
                    {{ session('error') ?? 'Terdapat kesalahan pada input Anda.' }}
                </div>
            @endif

            <form action="{{ route('admin.siswa.store') }}" method="POST">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-3">
                    {{-- Nama --}}
                    <div class="md:col-span-2 group">
                        <label class="block text-[9px] font-black text-slate-400 uppercase tracking-[0.15em] mb-1">Nama Lengkap Siswa</label>
                        <input type="text" name="nama" value="{{ old('nama') }}" placeholder="CONTOH: NORSYIFA ULHASANAH" class="w-full px-3 py-2 rounded-lg border border-slate-200 bg-slate-50/50 focus:bg-white focus:border-blue-500 text-[12px] font-bold uppercase outline-none placeholder:text-slate-300" required>
                    </div>

                    {{-- NISN --}}
                    <div>
                        <label class="block text-[9px] font-black text-slate-400 uppercase tracking-[0.15em] mb-1">NISN</label>
                        <input type="text" name="nisn" value="{{ old('nisn') }}" placeholder="10 DIGIT NISN" class="w-full px-3 py-2 rounded-lg border border-slate-200 bg-slate-50/50 focus:bg-white focus:border-blue-500 text-[12px] font-bold uppercase outline-none placeholder:text-slate-300" required>
                    </div>

                    {{-- Kelas --}}
                    <div>
                        <label class="block text-[9px] font-black text-slate-400 uppercase tracking-[0.15em] mb-1">Kelas</label>
                        <select name="kelas_id" class="w-full px-3 py-2 rounded-lg border border-slate-200 bg-slate-50/50 focus:bg-white focus:border-blue-500 text-[12px] font-bold uppercase outline-none" required>
                            <option value="" disabled selected>PILIH KELAS</option>
                            @foreach($kelasList as $kls)
                                <option value="{{ $kls->id }}" {{ old('kelas_id') == $kls->id ? 'selected' : '' }}>{{ $kls->nama_kelas }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Email --}}
                    <div>
                        <label class="block text-[9px] font-black text-slate-400 uppercase tracking-[0.15em] mb-1">Email Akun</label>
                        <input type="email" name="email" value="{{ old('email') }}" placeholder="siswa@mail.com" class="w-full px-3 py-2 rounded-lg border border-slate-200 bg-slate-50/50 focus:bg-white focus:border-blue-500 text-[12px] font-bold uppercase outline-none placeholder:text-slate-300" required>
                    </div>

                    {{-- WA --}}
                    <div>
                        <label class="block text-[9px] font-black text-slate-400 uppercase tracking-[0.15em] mb-1">WhatsApp Ortu</label>
                        <div class="flex items-center gap-1">
                            <span class="text-[11px] font-black text-slate-400 bg-slate-100 px-3 py-2 rounded-lg border border-slate-200">+62</span>
                            <input type="text" name="no_wa_ortu" value="{{ old('no_wa_ortu') }}" placeholder="8XXXXXXXXXX" class="w-full px-3 py-2 rounded-lg border border-slate-200 bg-slate-50/50 focus:bg-white focus:border-blue-500 text-[12px] font-bold uppercase outline-none placeholder:text-slate-300" required>
                        </div>
                    </div>
                </div>

                {{-- Info Password --}}
                <div class="mt-4 bg-blue-50/50 p-3 rounded-lg border border-blue-100 border-dashed flex items-center gap-2">
                    <i class="fa-solid fa-shield-halved text-blue-500 text-[10px]"></i>
                    <p class="text-[9px] text-blue-700 font-black uppercase tracking-wider">
                        Password Otomatis: <span class="bg-blue-600 text-white px-1.5 py-0.5 rounded ml-1 lowercase">password123</span>
                    </p>
                </div>

                {{-- Action Buttons --}}
                <div class="mt-6 pt-4 flex items-center justify-end gap-3 border-t border-slate-50">
                    <a href="{{ route('admin.siswa.index') }}" class="px-6 py-2 rounded-lg text-[10px] font-black text-white bg-rose-600 hover:bg-rose-700 uppercase tracking-widest transition-all active:scale-95">Batal</a>
                    <button type="submit" class="px-6 py-2 rounded-lg text-[10px] font-black text-white bg-blue-600 hover:bg-blue-700 uppercase tracking-widest transition-all active:scale-95">Simpan Siswa</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .animate-fade-in { animation: fadeIn 0.4s ease-out forwards; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(5px); } to { opacity: 1; transform: translateY(0); } }
</style>
@endsection