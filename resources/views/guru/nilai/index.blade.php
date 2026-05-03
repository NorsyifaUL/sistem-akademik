@extends('layouts.guru')

@section('content')
<div class="mb-8 flex justify-between items-end">
    <div>
        <h2 class="text-2xl font-extrabold text-gray-800">Manajemen Nilai Akademik</h2>
        <p class="text-gray-500 text-sm mt-1">Silakan pilih mata pelajaran untuk melakukan penginputan nilai siswa.</p>
    </div>
    <div class="hidden md:block">
        {{-- BAGIAN YANG DIPERBARUI: Mengambil data dari variabel $setting --}}
        <span class="text-[10px] bg-green-50 text-green-700 px-3 py-1 rounded-full font-bold uppercase tracking-widest border border-green-100">
            Tahun Ajaran: {{ $setting->tahun_ajaran ?? 'Belum Diatur' }} 
            ({{ ($setting->semester ?? 1) == 1 ? 'Ganjil' : 'Genap' }})
        </span>
    </div>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="bg-gradient-to-r from-green-700 to-green-600 px-6 py-4 flex justify-between items-center">
        <h3 class="text-white font-bold text-sm uppercase tracking-wider flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
            </svg>
            Daftar Jadwal Mengajar Anda
        </h3>
        <span class="text-[9px] text-green-100 font-medium uppercase tracking-widest bg-white/10 px-2 py-1 rounded">
            Total: {{ $jadwals->count() }} Mata Pelajaran
        </span>
    </div>
    
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-4 text-left text-[10px] font-bold text-gray-400 uppercase tracking-widest">No</th>
                    <th class="px-6 py-4 text-left text-[10px] font-bold text-gray-400 uppercase tracking-widest">Informasi Mata Pelajaran</th>
                    <th class="px-6 py-4 text-center text-[10px] font-bold text-gray-400 uppercase tracking-widest">Kelas</th>
                    <th class="px-6 py-4 text-center text-[10px] font-bold text-gray-400 uppercase tracking-widest">Hari & Jam</th>
                    <th class="px-6 py-4 text-right text-[10px] font-bold text-gray-400 uppercase tracking-widest">Opsi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100">
                @forelse($jadwals as $index => $jadwal)
                <tr class="hover:bg-green-50/30 transition-colors group">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-400">
                        {{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="w-10 h-10 rounded-lg bg-green-100 text-green-700 flex items-center justify-center font-bold text-sm shadow-sm border border-green-200 uppercase">
                                {{ substr($jadwal->mapel->nama_mapel ?? 'M', 0, 1) }}
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-bold text-gray-900 group-hover:text-green-700 transition-colors">
                                    {{ $jadwal->mapel->nama_mapel ?? 'Mapel Tidak Ditemukan' }}
                                </div>
                                <div class="text-[10px] font-semibold text-gray-400 uppercase tracking-tighter">
                                    KODE: {{ $jadwal->mapel->kode_mapel ?? '-' }}
                                </div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center">
                        <span class="px-4 py-1.5 text-[11px] font-bold rounded-lg bg-gray-100 text-gray-700 border border-gray-200">
                            KELAS {{ $jadwal->kelas ?? '-' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center">
                        <div class="text-xs font-bold text-gray-700">{{ $jadwal->hari }}</div>
                        <div class="text-[10px] text-gray-400 font-medium lowercase">
                            {{ $jadwal->jam_mulai }} - {{ $jadwal->jam_selesai }} wita
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right">
                        <a href="{{ route('guru.jadwal.siswa', $jadwal->id) }}" 
                           class="inline-flex items-center px-5 py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-xl font-bold text-[11px] uppercase tracking-widest shadow-lg shadow-green-100 transition-all active:scale-95 group">
                           <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                           </svg>
                           Input Nilai
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-16 text-center">
                        <div class="flex flex-col items-center">
                            <div class="bg-gray-50 p-4 rounded-full mb-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <p class="text-gray-500 font-medium">Belum ada jadwal mengajar yang terdaftar di akun Anda.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="bg-gray-50 px-6 py-3 border-t border-gray-100">
        <p class="text-[10px] text-gray-400 italic font-medium uppercase tracking-tight">
            * Data di atas adalah jadwal resmi yang telah divalidasi oleh Bagian Kurikulum SMANJA.
        </p>
    </div>
</div>
@endsection