@extends('layouts.guru')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8 font-academic">
    {{-- HEADER DASHBOARD --}}
    <div class="mb-8 border-l-4 border-green-600 pl-5">
        <h2 class="text-2xl font-black text-gray-800 tracking-tight uppercase">Dashboard Guru</h2>
        <p class="text-[11px] text-gray-400 mt-1 uppercase tracking-widest font-bold italic">
            Ringkasan Aktivitas Akademik & Monitoring Siswa — SMAN 1 Jejangkit
        </p>
    </div>

    {{-- STAT CARDS --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
        {{-- Jadwal Card --}}
        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-5 group hover:border-green-200 transition-all">
            <div class="w-12 h-12 bg-green-50 rounded-xl flex items-center justify-center text-green-700 group-hover:scale-110 transition-transform">
                <i class="fas fa-calendar-alt text-xl"></i>
            </div>
            <div>
                <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest leading-none mb-1">Jadwal Hari Ini</p>
                <p class="text-xl font-black text-gray-800">{{ $jadwalHariIni->count() }} <span class="text-[10px] text-gray-400 font-medium lowercase">Sesi</span></p>
            </div>
        </div>

        {{-- Absensi Card --}}
        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-5 group hover:border-red-200 transition-all">
            <div class="w-12 h-12 bg-red-50 rounded-xl flex items-center justify-center text-red-600 group-hover:scale-110 transition-transform">
                <i class="fas fa-user-check text-xl"></i>
            </div>
            <div>
                <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest leading-none mb-1">Input Absensi</p>
                <p class="text-xl font-black text-gray-800">{{ $absensiHariIni }} <span class="text-[10px] text-gray-400 font-medium lowercase">Siswa</span></p>
            </div>
        </div>

        {{-- Nilai Card --}}
        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-5 group hover:border-blue-200 transition-all">
            <div class="w-12 h-12 bg-blue-50 rounded-xl flex items-center justify-center text-blue-600 group-hover:scale-110 transition-transform">
                <i class="fas fa-edit text-xl"></i>
            </div>
            <div>
                <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest leading-none mb-1 text-left">Total Nilai Masuk</p>
                <p class="text-xl font-black text-gray-800">{{ $nilaiSiswa->count() }} <span class="text-[10px] text-gray-400 font-medium lowercase">Record</span></p>
            </div>
        </div>
    </div>

    {{-- GRID TABEL DENGAN AKSEN GARIS HIJAU --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-start">
        
        {{-- CARD JADWAL MENGAJAR --}}
        <div class="bg-white rounded-2xl shadow-xl shadow-gray-100/50 border border-gray-200 overflow-hidden">
            {{-- Garis Hijau di Atas --}}
            <div class="h-1.5 bg-green-600 w-full"></div>
            
            <div class="px-6 py-4 bg-gray-50/50 border-b border-gray-100 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                    <h3 class="text-[11px] font-black text-gray-500 uppercase tracking-widest">Jadwal Mengajar Hari Ini</h3>
                </div>
            </div>
            <div class="p-0 overflow-x-auto">
                <table class="w-full text-[11px] border-collapse text-left">
                    <tbody class="divide-y divide-gray-50">
                        @forelse($jadwalHariIni as $jadwal)
                        <tr class="hover:bg-green-50/30 transition-colors group text-left">
                            <td class="px-6 py-4 font-bold text-gray-800 text-left">
                                <span class="block text-green-700 uppercase tracking-tighter mb-0.5 italic">Kelas {{ $jadwal->kelas->nama_kelas ?? $jadwal->kelas }}</span>
                                <span class="text-gray-600 font-black uppercase text-[12px] tracking-tight">{{ $jadwal->mapel->nama_mapel }}</span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <span class="bg-white border border-gray-200 px-3 py-1 rounded-lg text-gray-400 font-black italic shadow-sm group-hover:border-green-200">
                                    {{ $jadwal->jam_mulai }} - {{ $jadwal->jam_selesai }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="2" class="px-6 py-12 text-center text-gray-300 italic font-bold uppercase tracking-widest">Belum ada jadwal hari ini.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- CARD AKTIVITAS NILAI --}}
        <div class="bg-white rounded-2xl shadow-xl shadow-gray-100/50 border border-gray-200 overflow-hidden">
            {{-- Garis Hijau di Atas (Disamakan agar serasi) --}}
            <div class="h-1.5 bg-green-600 w-full"></div>
            
            <div class="px-6 py-4 bg-gray-50/50 border-b border-gray-100 flex items-center justify-between">
                <div class="flex items-center gap-2 text-left">
                    <i class="fas fa-history text-green-600 text-xs text-left"></i>
                    <h3 class="text-[11px] font-black text-gray-500 uppercase tracking-widest text-left">Aktivitas Nilai Terakhir</h3>
                </div>
            </div>
            <div class="p-0 overflow-x-auto">
                <table class="w-full text-[11px] border-collapse text-left">
                    <tbody class="divide-y divide-gray-50">
                        @forelse($nilaiSiswa->unique('siswa_id')->take(5) as $nilai)
                        <tr class="hover:bg-green-50/30 transition-colors group text-left">
                            <td class="px-6 py-4 text-left">
                                <span class="font-black text-gray-800 block uppercase mb-1 leading-none italic group-hover:text-green-700">{{ $nilai->siswa->nama }}</span>
                                <span class="text-[9px] text-gray-400 uppercase font-bold tracking-widest">{{ $nilai->jadwal->mapel->nama_mapel }}</span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex flex-col items-end gap-1">
                                    <span class="px-2.5 py-1 bg-green-50 text-green-700 rounded-md font-black border border-green-100 text-[9px] uppercase tracking-tighter">
                                        {{ $nilai->jenis }} : {{ $nilai->nilai }}
                                    </span>
                                    <span class="text-[9px] text-gray-300 font-bold uppercase tracking-tighter italic">
                                        {{ \Carbon\Carbon::parse($nilai->tanggal)->translatedFormat('d M') }}
                                    </span>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="2" class="px-6 py-12 text-center text-gray-300 italic font-bold uppercase tracking-widest">Belum ada aktivitas nilai.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        @if(isset($alpaHariIni) && $alpaHariIni > 0)
            Swal.fire({
                title: '<span class="text-xs font-black uppercase tracking-widest">Laporan Presensi</span>',
                html: '<p class="text-[11px] font-bold text-gray-500 uppercase">Tercatat {{ $alpaHariIni }} siswa Alpa. Pesan telah diteruskan ke orang tua.</p>',
                icon: 'info',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 5000,
                timerProgressBar: true,
                background: '#ffffff',
                color: '#15803d',
                customClass: {
                    popup: 'rounded-xl border border-green-100 shadow-xl'
                }
            });
        @endif
    });
</script>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;700;900&display=swap');
    .font-academic { font-family: 'Inter', sans-serif; }
</style>
@endsection