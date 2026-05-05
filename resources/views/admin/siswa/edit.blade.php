@extends('layouts.admin')

@section('content')
<div class="p-6 space-y-6 animate-fade-in">
    <div class="mb-2">
        <h1 class="text-2xl font-black text-gray-800 tracking-tight uppercase">Edit Profil Siswa</h1>
        <p class="text-xs font-bold text-gray-400 mt-1 uppercase tracking-widest">Pembaruan data: <span class="text-blue-600">{{ $siswa->nama }}</span></p>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden border-t-4 border-t-blue-600">
        <div class="p-5 border-b border-gray-50 bg-gray-50/30">
            <h3 class="text-xs font-black text-gray-500 uppercase tracking-widest flex items-center gap-2">
                <i class="fa-solid fa-user-pen text-blue-600"></i> Formulir Edit Data
            </h3>
        </div>

        <form action="{{ route('admin.siswa.update', $siswa->id) }}" method="POST" class="p-8 space-y-6">
            @csrf
            @method('PUT')

            @if ($errors->any())
            <div class="bg-rose-50 border-l-4 border-rose-500 p-4 rounded-xl mb-6">
                <ul class="list-none">
                    @foreach ($errors->all() as $error)
                        <li class="text-[10px] text-rose-600 font-black uppercase tracking-tight flex items-center gap-2">
                            <i class="fa-solid fa-triangle-exclamation"></i> {{ $error }}
                        </li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Nama Lengkap --}}
                <div class="md:col-span-2">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2">Nama Lengkap Siswa</label>
                    <input type="text" name="nama" value="{{ old('nama', $siswa->nama) }}"
                           class="w-full px-4 py-3 rounded-lg border border-gray-200 bg-gray-50/50 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-sm font-bold transition-all outline-none" required>
                </div>

                {{-- NISN --}}
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2">NISN</label>
                    <input type="text" name="nisn" value="{{ old('nisn', $siswa->nisn) }}"
                           class="w-full px-4 py-3 rounded-lg border border-gray-200 bg-gray-50/50 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-sm font-bold transition-all outline-none" required>
                </div>

                {{-- Pilih Kelas (Dinamis dari Tabel Kelas) --}}
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2">Kelas</label>
                    <div class="relative">
                        <select name="kelas_id" class="w-full px-4 py-3 rounded-lg border border-gray-200 bg-gray-50/50 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-sm font-bold transition-all outline-none appearance-none cursor-pointer" required>
                            <option value="" disabled>Pilih Kelas</option>
                            @foreach($kelasList as $kls)
                                <option value="{{ $kls->id }}" {{ old('kelas_id', $siswa->kelas_id) == $kls->id ? 'selected' : '' }}>
                                    Kelas {{ $kls->nama_kelas }}
                                </option>
                            @endforeach
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-4 flex items-center text-gray-400">
                            <i class="fa-solid fa-chevron-down text-xs"></i>
                        </div>
                    </div>
                </div>

                {{-- Email Akun --}}
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2">Email Akun</label>
                    <input type="email" name="email" value="{{ old('email', $siswa->user->email ?? '') }}"
                           class="w-full px-4 py-3 rounded-lg border border-gray-200 bg-gray-50/50 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-sm font-bold transition-all outline-none" required>
                </div>

                {{-- No WA Ortu --}}
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2">No WA Orang Tua</label>
                    <input type="text" name="no_wa_ortu" value="{{ old('no_wa_ortu', $siswa->no_wa_ortu) }}"
                           class="w-full px-4 py-3 rounded-lg border border-gray-200 bg-gray-50/50 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-sm font-bold transition-all outline-none" required>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="pt-6 flex flex-col md:flex-row justify-between items-center gap-4 border-t border-gray-50">
                {{-- Tombol Batal Merah --}}
                <a href="{{ route('admin.siswa.index') }}" 
                   class="w-full md:w-auto px-10 py-3 rounded-xl text-sm font-black text-white bg-rose-600 hover:bg-rose-700 shadow-lg shadow-rose-100 transition-all active:scale-95 uppercase tracking-widest text-center">
                    Batal
                </a>
                
                {{-- Tombol Update --}}
                <button type="submit" class="w-full md:w-auto bg-blue-600 hover:bg-blue-700 text-white font-black py-3 px-10 rounded-xl shadow-lg shadow-blue-100 transition-all active:scale-95 uppercase tracking-widest text-sm">
                    Update Data
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