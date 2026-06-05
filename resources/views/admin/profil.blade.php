@extends('layouts.admin')

@section('content')
<div class="animate-fade-in">
    {{-- Judul Halaman --}}
    <div class="mb-6">
        <h1 class="text-2xl font-black text-slate-800 tracking-tight uppercase">Profil Administrator</h1>
        <p class="text-xs font-bold text-slate-400 tracking-wider uppercase mt-0.5">Informasi Pribadi & Pengaturan Akun Keamanan</p>
    </div>

    {{-- Notifikasi Sukses --}}
    @if(session('success'))
        <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl text-xs font-bold uppercase tracking-wider flex items-center gap-3 shadow-sm">
            <i class="fa-solid fa-circle-check text-base text-emerald-500"></i>
            {{ session('success') }}
        </div>
    @endif

    {{-- Notifikasi Error --}}
    @if($errors->any())
        <div class="mb-6 p-4 bg-rose-50 border border-rose-200 text-rose-700 rounded-xl text-xs font-bold uppercase tracking-wider space-y-1 shadow-sm">
            <ul class="list-disc list-inside pl-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
        {{-- Sisi Kiri: Info Profil --}}
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-8 text-center">
            <div class="h-28 w-28 rounded-full bg-blue-50 flex items-center justify-center text-blue-600 mx-auto mb-5 border-4 border-blue-100">
                <i class="fa-solid fa-user-gear text-4xl"></i>
            </div>
            <h2 class="text-lg font-black text-slate-800 leading-tight capitalize">{{ $admin->name }}</h2>
            <p class="text-[10px] font-black text-blue-600 uppercase tracking-widest mt-1">Administrator Sistem</p>
        </div>

        {{-- Sisi Kanan: Form --}}
        <div class="lg:col-span-2 bg-white rounded-2xl border-t-4 border-t-blue-600 border-x border-b border-slate-100 shadow-sm overflow-hidden">
            <form action="{{ url('/admin/profil/update') }}" method="POST" class="p-8 space-y-6">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Nama Lengkap</label>
                        <input type="text" name="name" value="{{ old('name', $admin->name) }}" required class="w-full text-sm font-bold text-slate-700 bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 focus:border-blue-500 outline-none">
                    </div>
                    <div>
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Email Login</label>
                        <input type="email" name="email" value="{{ old('email', $admin->email) }}" required class="w-full text-sm font-bold text-slate-700 bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 focus:border-blue-500 outline-none">
                    </div>
                </div>

                {{-- Password Section --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5" x-data="{ showPass: false, showConfirm: false }">
                    <div class="relative">
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Password Baru</label>
                        <div class="relative">
                            <input :type="showPass ? 'text' : 'password'" name="password" placeholder="••••••••" class="w-full text-sm font-bold text-slate-700 bg-white border border-slate-200 rounded-xl px-4 py-3 focus:border-blue-500 outline-none pr-10">
                            <button type="button" @click="showPass = !showPass" class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                                <i class="fa-solid" :class="showPass ? 'fa-eye-slash' : 'fa-eye'"></i>
                            </button>
                        </div>
                    </div>
                    <div class="relative">
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Konfirmasi Password</label>
                        <div class="relative">
                            <input :type="showConfirm ? 'text' : 'password'" name="password_confirmation" placeholder="••••••••" class="w-full text-sm font-bold text-slate-700 bg-white border border-slate-200 rounded-xl px-4 py-3 focus:border-blue-500 outline-none pr-10">
                            <button type="button" @click="showConfirm = !showConfirm" class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                                <i class="fa-solid" :class="showConfirm ? 'fa-eye-slash' : 'fa-eye'"></i>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="flex flex-col md:flex-row justify-end items-center gap-3 pt-2">
                    <a href="{{ route('admin.dashboard') }}" 
                       class="w-full md:w-auto px-8 py-3 rounded-lg text-[10px] font-black text-white bg-rose-600 hover:bg-rose-700 transition-all uppercase tracking-widest text-center shadow-sm shadow-rose-100 active:scale-95">
                        Batal
                    </a>
                    <button type="submit" class="w-full md:w-auto bg-blue-600 text-white font-black text-[10px] uppercase px-8 py-3 rounded-lg shadow-md hover:bg-blue-700 transition-all active:scale-95">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection