@extends('layouts.guru') 

@section('content')
<div class="p-6">
    {{-- Header --}}
    <div class="mb-8">
        <h2 class="text-2xl font-black text-gray-800 tracking-tight uppercase">Edit Presensi Siswa</h2>
        <p class="text-xs text-gray-500 font-bold uppercase tracking-widest">Perbarui informasi kehadiran untuk mata pelajaran ini</p>
    </div>

    <div class="max-w-3xl">
        {{-- Alert Error --}}
        @if($errors->any())
            <div class="mb-6 p-4 bg-rose-50 border-l-4 border-rose-500 rounded-xl">
                <div class="flex items-center mb-2">
                    <svg class="w-5 h-5 text-rose-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="text-rose-800 font-bold text-sm uppercase">Terjadi Kesalahan:</span>
                </div>
                <ul class="list-disc list-inside text-xs text-rose-600 font-medium ml-7">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Card Form --}}
        <div class="bg-white shadow-2xl shadow-gray-200/50 rounded-3xl border border-gray-100 overflow-hidden">
            {{-- PERBAIKAN ROUTE: Mengarah ke guru.absensi.manage.update --}}
            <form action="{{ route('guru.absensi.manage.update', $absensi->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="p-8 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Info Siswa (Read Only untuk Guru agar data tidak berantakan) --}}
                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-gray-400 ml-1">Nama Siswa</label>
                            <div class="w-full bg-gray-100 border border-gray-200 text-gray-500 text-sm rounded-2xl p-3 font-bold">
                                {{ strtoupper($absensi->siswa->nama) }}
                            </div>
                        </div>

                        {{-- Info Tanggal (Read Only) --}}
                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-gray-400 ml-1">Tanggal Absensi</label>
                            <div class="w-full bg-gray-100 border border-gray-200 text-gray-500 text-sm rounded-2xl p-3 font-bold">
                                {{ \Carbon\Carbon::parse($absensi->tanggal)->translatedFormat('d F Y') }}
                            </div>
                        </div>
                    </div>

                    {{-- Info Mapel --}}
                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-gray-400 ml-1">Jadwal Pelajaran</label>
                        <div class="w-full bg-gray-100 border border-gray-200 text-gray-500 text-sm rounded-2xl p-3 font-bold">
                            {{ $absensi->jadwal->mapel->nama_mapel }} ({{ $absensi->jadwal->kelas }})
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Status Kehadiran --}}
                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-gray-400 ml-1">Status Kehadiran</label>
                            <div class="grid grid-cols-2 gap-2">
                                @foreach(['Hadir' => 'emerald', 'Izin' => 'blue', 'Sakit' => 'amber', 'Alfa' => 'rose'] as $st => $color)
                                <label class="relative flex items-center justify-center p-3 border border-gray-200 rounded-2xl cursor-pointer hover:bg-gray-50 transition-all group">
                                    <input type="radio" name="status" value="{{ $st }}" class="sr-only peer" {{ $absensi->status == $st ? 'checked' : '' }}>
                                    <div class="peer-checked:text-{{ $color }}-600 peer-checked:font-black text-gray-400 text-xs font-bold uppercase tracking-tighter transition-all">
                                        {{ $st }}
                                    </div>
                                    {{-- Border dinamis saat dipilih --}}
                                    <div class="absolute inset-0 border-2 border-transparent peer-checked:border-{{ $color == 'emerald' ? 'emerald-500' : ($color == 'blue' ? 'blue-500' : ($color == 'amber' ? 'amber-500' : 'rose-500')) }} rounded-2xl pointer-events-none"></div>
                                </label>
                                @endforeach
                            </div>
                        </div>

                        {{-- Keterangan --}}
                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-gray-400 ml-1">Keterangan Tambahan</label>
                            <textarea name="keterangan" rows="2" placeholder="Catatan tambahan..."
                                class="w-full bg-gray-50 border border-gray-200 text-gray-700 text-sm rounded-2xl p-3 shadow-sm font-bold outline-none focus:ring-2 focus:ring-emerald-500 transition-all">{{ $absensi->keterangan }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- Footer Buttons --}}
                <div class="p-6 bg-gray-50 border-t border-gray-100 flex items-center justify-end gap-3">
                    <a href="{{ route('guru.absensi.rekap') }}" 
                        class="group flex items-center justify-center bg-white border-2 border-gray-200 hover:border-gray-300 hover:bg-gray-100 text-gray-500 px-8 py-3.5 rounded-2xl font-black text-xs uppercase tracking-widest transition-all active:scale-95 shadow-sm">
                        Batal
                    </a>
                    <button type="submit" class="bg-[#064e3b] hover:bg-[#053f30] text-white px-10 py-3.5 rounded-2xl font-black text-xs uppercase tracking-widest transition-all shadow-lg shadow-emerald-200 active:scale-95">
                        Update Data
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Tailwind Color Safelist agar warna dinamis muncul --}}
<div class="hidden peer-checked:border-emerald-500 peer-checked:text-emerald-600 peer-checked:border-blue-500 peer-checked:text-blue-600 peer-checked:border-amber-500 peer-checked:text-amber-600 peer-checked:border-rose-500 peer-checked:text-rose-600"></div>
@endsection