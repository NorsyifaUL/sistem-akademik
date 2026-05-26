@extends('layouts.siswa')

@section('content')
<div class="max-w-4xl text-left animate-fade-in p-6">
    {{-- Header --}}
    <div class="mb-6">
        <h2 class="text-2xl font-black text-gray-800 tracking-tight uppercase">Profil Siswa</h2>
        <p class="text-[11px] text-gray-400 mt-1 uppercase tracking-widest font-bold italic">
            Biodata Diri & Pengaturan Keamanan Akun
        </p>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 bg-emerald-50 border-l-4 border-[#064e3b] text-[#064e3b] rounded-r-xl shadow-sm text-xs font-bold flex items-center gap-2">
            <i class="fa-solid fa-circle-check"></i>
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        {{-- Sidebar Profil Siswa --}}
        <div class="md:col-span-1 space-y-6">
            <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-6 text-center">
                {{-- Foto / Icon --}}
                <div class="w-24 h-24 bg-emerald-50 rounded-full mx-auto mb-4 flex items-center justify-center border-4 border-emerald-100 text-[#064e3b]">
                    <i class="fa-solid fa-user-graduate text-4xl"></i>
                </div>
                
                <h3 class="text-sm font-black text-gray-800 uppercase tracking-tight">{{ Auth::user()->name }}</h3>
                <p class="text-[10px] font-bold text-[#064e3b] uppercase tracking-tighter mb-4">Siswa Aktif SMANJA</p>
                
                <div class="pt-4 border-t border-gray-50 flex flex-col gap-3">
                    <div class="flex justify-between text-[10px] font-bold">
                        <span class="text-gray-400 uppercase">NISN</span>
                        <span class="text-gray-700 tracking-widest">{{ $siswa->nisn ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between text-[10px] font-bold">
                        <span class="text-gray-400 uppercase">Kelas</span>
                        {{-- FIX: Mengakses relasi dataKelas lalu kolom nama_kelas --}}
                        <span class="text-[#064e3b] font-black uppercase">
                            {{ $siswa->dataKelas->nama_kelas ?? 'Belum Diplot' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Form Update Siswa --}}
        <div class="md:col-span-2 space-y-6">
            <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden border-t-4 border-t-[#064e3b]">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-100 flex items-center justify-between">
                    <h4 class="text-xs font-black text-gray-500 uppercase tracking-widest">Informasi Akun</h4>
                    <span class="text-[9px] font-bold bg-[#ffb800] text-white px-2 py-1 rounded-md uppercase tracking-tighter italic">Data Terverifikasi</span>
                </div>
                
                <form action="{{ route('siswa.profil.update') }}" method="POST" class="p-6 space-y-4">
                    @csrf
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider">Nama Lengkap</label>
                            <input type="text" value="{{ Auth::user()->name }}" disabled
                                   class="w-full bg-gray-100 border-gray-100 rounded-xl px-4 py-2 text-xs font-bold text-gray-400 cursor-not-allowed">
                        </div>

                        <div class="space-y-1.5">
                            <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider">WA Orang Tua</label>
                            <input type="text" value="{{ $siswa->no_wa_ortu ?? '-' }}" disabled
                                   class="w-full bg-gray-100 border-gray-100 rounded-xl px-4 py-2 text-xs font-bold text-gray-400 cursor-not-allowed">
                        </div>
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider">Email Login</label>
                        <input type="email" value="{{ Auth::user()->email }}" disabled
                               class="w-full bg-gray-100 border-gray-100 rounded-xl px-4 py-2 text-xs font-bold text-gray-400 cursor-not-allowed italic">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider">Password Baru</label>
                            <div class="relative">
                                <input type="password" name="password" id="pass_siswa"
                                       class="w-full bg-gray-50 border-gray-200 rounded-xl px-4 py-2 text-xs font-bold text-gray-700 focus:ring-2 focus:ring-[#064e3b] focus:border-[#064e3b] transition-all shadow-inner"
                                       placeholder="Kosongkan jika tidak ganti">
                                <button type="button" onclick="togglePass('pass_siswa', 'eye-siswa-1')" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-[#064e3b] transition-colors">
                                    <svg id="eye-siswa-1" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                        
                        <div class="space-y-1.5">
                            <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider">Ulangi Password</label>
                            <div class="relative">
                                <input type="password" name="password_confirmation" id="conf_siswa"
                                       class="w-full bg-gray-50 border-gray-200 rounded-xl px-4 py-2 text-xs font-bold text-gray-700 focus:ring-2 focus:ring-[#064e3b] focus:border-[#064e3b] transition-all shadow-inner"
                                       placeholder="Ulangi password">
                                <button type="button" onclick="togglePass('conf_siswa', 'eye-siswa-2')" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-[#064e3b] transition-colors">
                                    <svg id="eye-siswa-2" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="pt-4 flex items-center justify-between gap-4">
                        <p class="text-[9px] text-gray-400 italic max-w-[200px]">Ubah password secara berkala untuk menjaga keamanan akun Anda.</p>
                        <button type="submit" class="bg-[#064e3b] hover:bg-[#053f30] text-white px-8 py-2.5 rounded-xl font-black text-[10px] uppercase tracking-widest shadow-lg shadow-[#064e3b]/10 transition-all flex items-center gap-2">
                            <i class="fa-solid fa-shield-halved"></i>
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function togglePass(inputId, iconId) {
        const input = document.getElementById(inputId);
        const icon = document.getElementById(iconId);
        
        if (input.type === 'password') {
            input.type = 'text';
            icon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />`;
        } else {
            input.type = 'password';
            icon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />`;
        }
    }
</script>

<style>
    @keyframes fadeIn { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: translateY(0); } }
    .animate-fade-in { animation: fadeIn 0.4s ease-out forwards; }
</style>
@endsection