@extends('layouts.guru')

@section('content')
<div class="container mx-auto p-4">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Jadwal Mengajar Saya</h2>
        <span class="text-sm text-gray-500 italic">SMAN 1 Jejangkit - TP 2025/2026</span>
    </div>

    @if($jadwals->isEmpty())
        <div class="bg-yellow-100 text-yellow-800 p-4 rounded-xl border border-yellow-200 shadow-sm">
            <div class="flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                </svg>
                <span>Tidak ada jadwal mengajar yang ditemukan untuk akun Anda.</span>
            </div>
        </div>
    @else
        <div class="bg-white shadow-md rounded-xl overflow-hidden border border-gray-200">
            <table class="min-w-full">
                <thead class="bg-green-700 text-white">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider">Hari</th>
                        <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider">Jam</th>
                        <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider">Mata Pelajaran</th>
                        <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-center">Kelas</th>
                        <th class="px-6 py-4 text-center text-xs font-bold uppercase tracking-wider">Manajemen Kelas</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @foreach($jadwals as $jadwal)
                    <tr class="hover:bg-green-50/50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="font-bold text-green-700">{{ $jadwal->hari }}</span>
                        </td>
                        <td class="px-6 py-4 text-gray-600 whitespace-nowrap">
                            <span class="font-medium">
                                {{ substr($jadwal->jam_mulai, 0, 5) ?? '00:00' }} - {{ substr($jadwal->jam_selesai, 0, 5) ?? '00:00' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-gray-800 font-medium">
                            {{ $jadwal->mapel->nama_mapel ?? $jadwal->mapel->nama ?? 'Mapel Tidak Diketahui' }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-extrabold shadow-sm border border-blue-200 uppercase">
                                {{ $jadwal->kelas ?? '-' }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-wrap justify-center items-center gap-4">
                                
                                <a href="{{ route('guru.absensi.index', $jadwal->id) }}"
                                   class="bg-green-600 text-white px-5 py-2 rounded-lg text-xs font-bold hover:bg-green-700 transition shadow-sm flex items-center gap-2 group" title="Input Absensi Hari Ini">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 group-hover:scale-110 transition" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                                    </svg>
                                    Absen
                                </a>

                                <a href="{{ route('guru.jadwal.siswa', $jadwal->id) }}"
                                   class="bg-blue-600 text-white px-5 py-2 rounded-lg text-xs font-bold hover:bg-blue-700 transition shadow-sm flex items-center gap-2 group" title="Lihat Siswa & Input Nilai">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 group-hover:scale-110 transition" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                    Siswa
                                </a>

                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

<div class="mt-8 text-center text-gray-400 text-xs">
    &copy; 2026 Sistem Informasi Akademik - SMAN 1 Jejangkit
</div>
@endsection