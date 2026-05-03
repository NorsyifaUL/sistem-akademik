@extends('layouts.guru')

@section('content')
<div class="max-w-4xl text-left">
    <div class="mb-6">
        <h2 class="text-2xl font-black text-gray-800 tracking-tight">Profil Pendidik</h2>
        <p class="text-[11px] text-gray-400 mt-1 uppercase tracking-widest font-bold italic">
            Informasi Pribadi & Pengaturan Akun Keamanan
        </p>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 rounded-r-xl shadow-sm text-xs font-bold flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="md:col-span-1 space-y-6">
            <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-6 text-center">
                <div class="w-24 h-24 bg-green-100 rounded-full mx-auto mb-4 flex items-center justify-center border-4 border-green-50">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-green-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
                <h3 class="text-sm font-black text-gray-800 capitalize">{{ Auth::user()->name }}</h3>
                <p class="text-[10px] font-bold text-green-600 uppercase tracking-tighter mb-4">Tenaga Pengajar Aktif</p>
                
                <div class="pt-4 border-t border-gray-50 flex flex-col gap-2">
                    <div class="flex justify-between text-[10px] font-bold">
                        <span class="text-gray-400 uppercase">NIP saat ini</span>
                        <span class="text-gray-700">{{ Auth::user()->guru->nip ?? 'Belum diatur' }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="md:col-span-2 space-y-6 text-left">
            <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-100">
                    <h4 class="text-xs font-black text-gray-500 uppercase tracking-widest">Update Data Diri & Keamanan</h4>
                </div>
                
                <form action="{{ route('guru.profil.update') }}" method="POST" class="p-6 space-y-4">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider">Nama Lengkap</label>
                            <input type="text" name="nama" value="{{ old('nama', Auth::user()->name) }}" required
                                   class="w-full bg-gray-50 border-gray-200 rounded-xl px-4 py-2 text-xs font-bold text-gray-700 focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all shadow-inner">
                        </div>

                        <div class="space-y-1.5">
                            <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider">NIP (Nomor Induk Pegawai)</label>
                            <input type="text" name="nip" value="{{ old('nip', Auth::user()->guru->nip ?? '') }}" required
                                   class="w-full bg-gray-50 border-gray-200 rounded-xl px-4 py-2 text-xs font-bold text-gray-700 focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all shadow-inner"
                                   placeholder="Contoh: 19820301...">
                        </div>
                    </div>

                    <div class="space-y-1.5 text-left">
                        <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider">Email Akun (Tetap)</label>
                        <input type="email" value="{{ Auth::user()->email }}" disabled
                               class="w-full bg-gray-100 border-gray-100 rounded-xl px-4 py-2 text-xs font-bold text-gray-400 cursor-not-allowed italic">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-left">
                        <div class="space-y-1.5 text-left">
                            <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider">Password Baru</label>
                            <input type="password" name="password" 
                                   class="w-full bg-gray-50 border-gray-200 rounded-xl px-4 py-2 text-xs font-bold text-gray-700 focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all shadow-inner"
                                   placeholder="Kosongkan jika tidak ganti">
                        </div>
                        <div class="space-y-1.5 text-left">
                            <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider">Konfirmasi Password</label>
                            <input type="password" name="password_confirmation" 
                                   class="w-full bg-gray-50 border-gray-200 rounded-xl px-4 py-2 text-xs font-bold text-gray-700 focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all shadow-inner"
                                   placeholder="Ulangi password">
                        </div>
                    </div>

                    <div class="pt-4 flex items-center justify-between gap-4">
                        <p class="text-[9px] text-gray-400 italic max-w-[200px]">Data NIP akan digunakan secara otomatis pada tanda tangan raport digital.</p>
                        <button type="submit" class="bg-green-700 hover:bg-green-800 text-white px-6 py-2.5 rounded-xl font-bold text-xs shadow-lg shadow-green-900/10 transition-all flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                            </svg>
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection