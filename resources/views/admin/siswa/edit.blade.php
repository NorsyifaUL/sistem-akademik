@extends('layouts.admin')

@section('content')
<div class="p-4 space-y-4 animate-fade-in">
    {{-- Header --}}
    <div class="px-1">
        <h1 class="text-lg font-black text-slate-800 tracking-tight uppercase leading-none">
            Edit <span class="text-blue-600">Profil Siswa</span>
        </h1>
        <p class="text-[9px] text-slate-400 font-bold uppercase tracking-[0.2em] mt-1">
            Pembaruan data: <span class="text-slate-600">{{ $siswa->nama }}</span>
        </p>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="h-[3px] bg-blue-600 w-full"></div>
        
        <div class="p-3 border-b border-slate-50 bg-slate-50/30">
            <h3 class="text-[10px] font-black text-slate-500 uppercase tracking-widest flex items-center gap-2">
                <i class="fa-solid fa-user-pen text-blue-600 text-[10px]"></i> Formulir Perubahan Data
            </h3>
        </div>

        <form action="{{ route('admin.siswa.update', $siswa->id) }}" method="POST" class="p-5 md:p-6">
            @csrf
            @method('PUT')

            {{-- Error Handling --}}
            @if ($errors->any())
            <div class="bg-rose-50 border-l-4 border-rose-500 p-3 rounded-r-lg mb-5">
                <ul class="space-y-1">
                    @foreach ($errors->all() as $error)
                        <li class="text-[9px] text-rose-700 font-black uppercase tracking-wider">• {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-3">
                {{-- Nama Lengkap --}}
                <div class="md:col-span-2 group">
                    <label class="block text-[9px] font-black text-slate-400 uppercase tracking-[0.15em] mb-1">Nama Lengkap Siswa</label>
                    <input type="text" name="nama" value="{{ old('nama', $siswa->nama) }}"
                           class="w-full px-3 py-2 rounded-lg border border-slate-200 bg-slate-50/50 focus:bg-white focus:border-blue-500 text-[12px] font-bold uppercase outline-none" required>
                </div>

                {{-- NISN --}}
                <div>
                    <label class="block text-[9px] font-black text-slate-400 uppercase tracking-[0.15em] mb-1">NISN</label>
                    <input type="text" name="nisn" value="{{ old('nisn', $siswa->nisn) }}"
                           class="w-full px-3 py-2 rounded-lg border border-slate-200 bg-slate-50/50 focus:bg-white focus:border-blue-500 text-[12px] font-bold uppercase outline-none" required>
                </div>

                {{-- Kelas --}}
                <div>
                    <label class="block text-[9px] font-black text-slate-400 uppercase tracking-[0.15em] mb-1">Kelas</label>
                    <select name="kelas_id" class="w-full px-3 py-2 rounded-lg border border-slate-200 bg-slate-50/50 focus:bg-white focus:border-blue-500 text-[12px] font-bold uppercase outline-none" required>
                        @foreach($kelasList as $kls)
                            <option value="{{ $kls->id }}" {{ old('kelas_id', $siswa->kelas_id) == $kls->id ? 'selected' : '' }}>
                                {{ $kls->nama_kelas }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Email --}}
                <div>
                    <label class="block text-[9px] font-black text-slate-400 uppercase tracking-[0.15em] mb-1">Email Akses</label>
                    <input type="email" name="email" value="{{ old('email', $siswa->user->email ?? '') }}"
                           class="w-full px-3 py-2 rounded-lg border border-slate-200 bg-slate-50/50 focus:bg-white focus:border-blue-500 text-[12px] font-bold uppercase outline-none" required>
                </div>

                {{-- No WA Ortu --}}
                <div>
                    <label class="block text-[9px] font-black text-slate-400 uppercase tracking-[0.15em] mb-1">WhatsApp Ortu</label>
                    <input type="text" name="no_wa_ortu" value="{{ old('no_wa_ortu', $siswa->no_wa_ortu) }}"
                           class="w-full px-3 py-2 rounded-lg border border-slate-200 bg-slate-50/50 focus:bg-white focus:border-blue-500 text-[12px] font-bold uppercase outline-none" required>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="mt-6 pt-4 flex items-center justify-end gap-3 border-t border-slate-50">
                <a href="{{ route('admin.siswa.index') }}" 
                   class="px-6 py-2 rounded-lg text-[10px] font-black text-white bg-rose-600 hover:bg-rose-700 transition-all uppercase tracking-widest active:scale-95">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2 rounded-lg text-[10px] font-black text-white bg-blue-600 hover:bg-blue-700 transition-all uppercase tracking-widest active:scale-95">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    .animate-fade-in { animation: fadeIn 0.4s ease-out forwards; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(5px); } to { opacity: 1; transform: translateY(0); } }
</style>
@endsection