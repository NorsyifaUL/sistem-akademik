@extends('layouts.admin')

@section('content')
<div class="p-8 space-y-8 animate-fade-in">
    {{-- Header --}}
    <div>
        <h2 class="text-2xl font-black text-slate-800 tracking-tight uppercase">Koreksi Absensi</h2>
        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-[0.2em]">Sistem Informasi Akademik <span class="text-blue-600">Sman 1 Jejangkit</span></p>
    </div>

    {{-- Main Container --}}
    <div class="w-full">
        {{-- BAGIAN INI DIPERTEBAL: pt-4 membuat garis biru di atas lebih lebar/panjang ke bawah --}}
        <div class="bg-blue-600 rounded-[2.5rem] pt-1.5 shadow-xl shadow-blue-100/50 overflow-hidden">
            
            {{-- Konten Utama (Putih) --}}
            <div class="bg-white p-10 rounded-t-[1.8rem]">
                
                {{-- Detail Siswa --}}
                <div class="bg-gray-50/50 rounded-[1.5rem] border border-gray-100 p-8 mb-10">
                    <div class="flex items-start gap-4 mb-6">
                        <i class="fa-solid fa-user-pen text-blue-600 mt-1"></i>
                        <div>
                            <h3 class="text-sm font-black text-slate-700 uppercase tracking-widest">Detail Siswa</h3>
                            <p class="text-[9px] text-slate-400 font-bold uppercase italic">Informasi data yang akan diubah</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                        <div>
                            <span class="block text-[10px] font-black text-blue-600 uppercase tracking-widest mb-2">Nama Siswa</span>
                            <p class="text-sm font-black text-slate-700 uppercase tracking-tight">{{ $absensi->siswa->nama }}</p>
                            <p class="text-[9px] text-slate-400 font-bold tracking-widest mt-1">ID: {{ $absensi->siswa->id }}</p>
                        </div>
                        <div>
                            <span class="block text-[10px] font-black text-blue-600 uppercase tracking-widest mb-2">Kelas</span>
                            <span class="inline-block bg-blue-50 text-blue-600 text-[10px] font-black px-4 py-1 rounded-lg italic">
                                {{ $absensi->siswa->dataKelas->nama_kelas }}
                            </span>
                        </div>
                        <div>
                            <span class="block text-[10px] font-black text-blue-600 uppercase tracking-widest mb-2">Waktu Asli</span>
                            <p class="text-sm font-bold text-slate-700">{{ $absensi->created_at->format('H:i') }}</p>
                            <p class="text-[9px] text-slate-400 font-bold uppercase">{{ $absensi->created_at->format('d M Y') }}</p>
                        </div>
                    </div>
                </div>

                {{-- Form Update --}}
                <form action="{{ route('admin.absensi.update', $absensi->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="space-y-8">
                        {{-- Status Kehadiran --}}
                        <div>
                            <div class="flex items-center gap-2 mb-6">
                                <div class="w-2 h-2 bg-blue-600 rounded-full animate-pulse"></div>
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Pilih Status Baru</label>
                            </div>
                            
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                @foreach([
                                    'Hadir' => 'emerald', 
                                    'Sakit' => 'amber', 
                                    'Izin' => 'blue', 
                                    'Alpa' => 'rose'
                                ] as $st => $color)
                                <label class="relative flex flex-col items-center justify-center p-6 border border-gray-100 rounded-2xl cursor-pointer hover:bg-gray-50 transition-all group">
                                    <input type="radio" name="status" value="{{ $st }}" class="sr-only peer" {{ $absensi->status == $st ? 'checked' : '' }}>
                                    
                                    <span class="peer-checked:text-{{ $color }}-600 peer-checked:font-black text-slate-400 text-xs font-black uppercase tracking-widest transition-all">
                                        {{ $st }}
                                    </span>

                                    <div class="absolute inset-0 border-2 border-transparent peer-checked:border-{{ $color }}-500 peer-checked:bg-{{ $color }}-50/30 rounded-2xl pointer-events-none transition-all"></div>
                                </label>
                                @endforeach
                            </div>
                        </div>

                        {{-- Tombol Aksi --}}
                        <div class="flex items-center justify-end gap-4 pt-8 border-t border-gray-50">
                            {{-- Tombol Batal warna merah sesuai permintaan --}}
                            <a href="{{ route('admin.absensi.index') }}" class="bg-rose-500 hover:bg-rose-600 text-white text-[10px] font-black uppercase tracking-widest px-10 py-4 rounded-xl transition-all shadow-lg shadow-rose-100 active:scale-95">
                                Batal
                            </a>
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-14 py-4 rounded-xl font-black text-[10px] uppercase tracking-[0.2em] transition-all shadow-lg shadow-blue-100 active:scale-95 flex items-center gap-3">
                                <i class="fa-solid fa-save text-xs"></i>
                                Update Data
                            </button>
                        </div>
                    </div>
                </form>

                <div class="mt-12 text-center">
                    <p class="text-[9px] font-black text-gray-300 uppercase tracking-[0.4em]">SIAKAD &bull; SMAN 1 JEJANGKIT &bull; 2026</p>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Tailwind Color Safelist --}}
<div class="hidden peer-checked:border-emerald-500 peer-checked:bg-emerald-50/30 peer-checked:text-emerald-600 peer-checked:border-blue-500 peer-checked:bg-blue-50/30 peer-checked:text-blue-600 peer-checked:border-amber-500 peer-checked:bg-amber-50/30 peer-checked:text-amber-600 peer-checked:border-rose-500 peer-checked:bg-rose-50/30 peer-checked:text-rose-600"></div>
@endsection