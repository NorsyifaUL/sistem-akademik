{{-- Mengambil kerangka utama (layout) dari file 'admin.blade.php' --}}
@extends('layouts.admin')

{{-- Memulai konten yang akan dimasukkan ke dalam @yield('content') di layout utama --}}
@section('content')

{{-- Container utama dengan efek animasi fade-in --}}
<div class="p-4 space-y-4 animate-fade-in">

    {{-- Header Section: Judul Halaman --}}
    <div class="px-1 flex justify-between items-end">
        <div>
            <h1 class="text-xl font-black text-slate-800 tracking-tight uppercase leading-none">
                Update <span class="text-blue-600">Data Guru</span>
            </h1>
            {{-- Menampilkan nama guru yang sedang di-edit --}}
            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-[0.1em] mt-1">
                Perbarui Identitas Pendidik: <span class="text-slate-600">{{ $guru->nama }}</span>
            </p>
        </div>
    </div>

    {{-- Form Container: Wadah untuk membungkus form --}}
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        {{-- Garis aksen warna biru di atas form --}}
        <div class="h-[3px] bg-blue-600 w-full"></div>
        
        {{-- Judul bagian dalam form --}}
        <div class="p-3 border-b border-slate-50 bg-slate-50/30">
            <h3 class="text-[10px] font-black text-slate-500 uppercase tracking-widest flex items-center gap-2">
                <i class="fa-solid fa-user-pen text-blue-600 text-[11px]"></i> Formulir Pembaruan Data
            </h3>
        </div>

        {{-- Form: Mengirim data ke route update guru berdasarkan ID --}}
        <form action="{{ route('admin.guru.update', $guru->id) }}" method="POST" class="p-5 md:p-6">
            @csrf {{-- Wajib ada di Laravel untuk keamanan dari serangan CSRF --}}
            @method('PUT') {{-- Memberitahu server bahwa ini adalah permintaan 'update' (bukan create/POST biasa) --}}
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-3">
                
                {{-- Field: Nama Lengkap --}}
                <div class="md:col-span-2 group">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.1em] mb-1 group-focus-within:text-blue-600 transition-colors">Nama Lengkap & Gelar</label>
                    <div class="relative">
                        {{-- 'old' berfungsi agar input tidak hilang jika terjadi error validasi --}}
                        <input type="text" name="nama" value="{{ old('nama', $guru->nama) }}"
                               class="w-full px-4 py-2.5 rounded-lg border border-slate-200 bg-slate-50/30 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/5 text-[13px] font-bold transition-all outline-none uppercase placeholder:text-slate-300" required>
                        <i class="fa-solid fa-user-tie absolute right-4 top-3 text-slate-300 text-[11px] pointer-events-none group-focus-within:text-blue-500 transition-colors"></i>
                    </div>
                </div>

                {{-- Field: NIP --}}
                <div class="group">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.1em] mb-1 group-focus-within:text-blue-600 transition-colors">NIP</label>
                    <div class="relative">
                        <input type="text" name="nip" value="{{ old('nip', $guru->nip) }}"
                               class="w-full px-4 py-2.5 rounded-lg border border-slate-200 bg-slate-50/30 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/5 text-[13px] font-bold transition-all outline-none uppercase placeholder:text-slate-300" required>
                        <i class="fa-solid fa-id-card absolute right-4 top-3 text-slate-300 text-[11px] pointer-events-none group-focus-within:text-blue-500 transition-colors"></i>
                    </div>
                </div>

                {{-- Field: Email --}}
                <div class="group">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.1em] mb-1 group-focus-within:text-blue-600 transition-colors">Email Akun (Login)</label>
                    <div class="relative">
                        <input type="email" name="email" value="{{ old('email', $guru->user->email) }}"
                               class="w-full px-4 py-2.5 rounded-lg border border-slate-200 bg-slate-50/30 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/5 text-[13px] font-bold transition-all outline-none lowercase placeholder:text-slate-300" required>
                        <i class="fa-solid fa-envelope absolute right-4 top-3 text-slate-300 text-[11px] pointer-events-none group-focus-within:text-blue-500 transition-colors"></i>
                    </div>
                </div>
            </div>

            {{-- Info Tip: Peringatan visual --}}
            <div class="mt-5 bg-amber-50/50 p-3 rounded-lg border border-amber-100 border-dashed flex items-center gap-3">
                <i class="fa-solid fa-circle-exclamation text-amber-500 text-[11px]"></i>
                <p class="text-[9px] text-amber-700 font-black uppercase tracking-wider leading-tight">
                    Catatan: Perubahan email akan berdampak pada kredensial login guru yang bersangkutan.
                </p>
            </div>

            {{-- Action Buttons --}}
            <div class="mt-6 pt-4 flex justify-end items-center gap-3 border-t border-slate-100">
                {{-- Tombol Batal: Mengarahkan kembali ke daftar guru --}}
                <a href="{{ route('admin.guru.index') }}" 
                   class="px-6 py-2.5 rounded-lg text-[10px] font-black text-white bg-rose-600 hover:bg-rose-700 transition-all uppercase tracking-widest active:scale-95 shadow-sm shadow-rose-100 text-center">
                    Batal
                </a>
                
                {{-- Tombol Simpan: Trigger untuk mengirim form (submit) --}}
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-black py-2.5 px-8 rounded-lg shadow-sm transition-all active:scale-95 uppercase tracking-widest text-[10px]">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- CSS Tambahan: Animasi muncul halus --}}
<style>
    @keyframes fadeIn { from { opacity: 0; transform: translateY(5px); } to { opacity: 1; transform: translateY(0); } }
    .animate-fade-in { animation: fadeIn 0.4s ease-out forwards; }
</style>

@endsection