@extends('layouts.guru')

@section('content')
{{-- Diubah ke max-w-7xl untuk memaksimalkan ruang layar --}}
<div class="max-w-7xl mx-auto text-left animate-fade-in p-6">
    <div class="mb-6">
        <h2 class="text-2xl font-black text-gray-800 tracking-tight uppercase">Profil Pendidik</h2>
        <p class="text-[11px] text-gray-400 mt-1 uppercase tracking-widest font-bold italic">
            Informasi Pribadi & Pengaturan Keamanan Akun
        </p>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 bg-emerald-50 border-l-4 border-emerald-700 text-emerald-800 rounded-r-xl shadow-sm text-xs font-bold flex items-center gap-2">
            <i class="fa-solid fa-circle-check"></i>
            {{ session('success') }}
        </div>
    @endif

    {{-- Grid 4 kolom agar sidebar tetap proporsional --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        {{-- Sidebar Profil --}}
        <div class="md:col-span-1 space-y-6">
            <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-6 text-center">
                <div class="w-24 h-24 bg-emerald-50 rounded-full mx-auto mb-4 flex items-center justify-center border-4 border-emerald-100 text-emerald-700">
                    <i class="fa-solid fa-user-tie text-4xl"></i>
                </div>
                <h3 class="text-sm font-black text-gray-800 uppercase tracking-tight">{{ Auth::user()->name }}</h3>
                <p class="text-[10px] font-bold text-emerald-600 uppercase tracking-tighter mb-4">Tenaga Pengajar Aktif</p>
                
                <div class="pt-4 border-t border-gray-50 flex flex-col gap-3">
                    <div class="flex justify-between text-[10px] font-bold">
                        <span class="text-gray-400 uppercase">NIP</span>
                        <span class="text-gray-700 tracking-widest">{{ Auth::user()->guru->nip ?? '-' }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Form Utama (mengambil 3 kolom) --}}
        <div class="md:col-span-3 space-y-6 text-left">
            <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden border-t-4 border-t-emerald-700">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-100 flex items-center justify-between">
                    <h4 class="text-xs font-black text-gray-500 uppercase tracking-widest">Update Data Diri & Keamanan</h4>
                    <span class="text-[9px] font-bold bg-emerald-100 text-emerald-700 px-2 py-1 rounded-md uppercase tracking-tighter italic">Mode Edit</span>
                </div>
                
                <form action="{{ route('guru.profil.update') }}" method="POST" class="p-6 space-y-4">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider">Nama Lengkap</label>
                            <input type="text" name="nama" value="{{ old('nama', Auth::user()->name) }}" required
                                   class="w-full bg-gray-50 border-gray-200 rounded-xl px-4 py-2 text-xs font-bold text-gray-700 focus:ring-2 focus:ring-emerald-700 focus:border-emerald-700 transition-all shadow-inner">
                        </div>

                        <div class="space-y-1.5">
                            <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider">NIP</label>
                            <input type="text" name="nip" value="{{ old('nip', Auth::user()->guru->nip ?? '') }}" required
                                   class="w-full bg-gray-50 border-gray-200 rounded-xl px-4 py-2 text-xs font-bold text-gray-700 focus:ring-2 focus:ring-emerald-700 focus:border-emerald-700 transition-all shadow-inner"
                                   placeholder="Masukkan NIP...">
                        </div>
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider">Email Akun (Login)</label>
                        <input type="email" value="{{ Auth::user()->email }}" disabled
                               class="w-full bg-gray-100 border-gray-100 rounded-xl px-4 py-2 text-xs font-bold text-gray-400 cursor-not-allowed italic">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider">Password Baru</label>
                            <div class="relative">
                                <input type="password" name="password" id="password"
                                       class="w-full bg-gray-50 border-gray-200 rounded-xl px-4 py-2 text-xs font-bold text-gray-700 focus:ring-2 focus:ring-emerald-700 focus:border-emerald-700 transition-all shadow-inner"
                                       placeholder="Kosongkan jika tidak ganti">
                                <button type="button" onclick="togglePassword('password', 'eye-1')" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-emerald-700 transition-colors">
                                    <svg id="eye-1" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                        
                        <div class="space-y-1.5">
                            <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider">Konfirmasi Password</label>
                            <div class="relative">
                                <input type="password" name="password_confirmation" id="conf"
                                       class="w-full bg-gray-50 border-gray-200 rounded-xl px-4 py-2 text-xs font-bold text-gray-700 focus:ring-2 focus:ring-emerald-700 focus:border-emerald-700 transition-all shadow-inner"
                                       placeholder="Ulangi password">
                                <button type="button" onclick="togglePassword('conf', 'eye-2')" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-emerald-700 transition-colors">
                                    <svg id="eye-2" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="pt-4 flex items-center justify-between gap-4">
                        <p class="text-[9px] text-gray-400 italic max-w-[250px]">Data NIP digunakan secara otomatis untuk tanda tangan pada laporan akademik dan dokumen resmi lainnya.</p>
                        <div class="flex items-center gap-2">
                            <a href="{{ route('guru.dashboard') }}" class="bg-red-600 hover:bg-red-700 text-white px-6 py-2.5 rounded-xl font-black text-[10px] uppercase tracking-widest transition-all">
                                Batal
                            </a>
                            <button type="submit" class="bg-emerald-700 hover:bg-emerald-800 text-white px-8 py-2.5 rounded-xl font-black text-[10px] uppercase tracking-widest shadow-lg shadow-emerald-900/10 transition-all flex items-center gap-2">
                                <i class="fa-solid fa-shield-halved"></i>
                                Simpan Data
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function togglePassword(inputId, iconId) {
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