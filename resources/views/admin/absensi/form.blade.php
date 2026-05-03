@extends('layouts.admin')

@section('content')
<div class="p-6">
    <h2 class="text-2xl font-bold mb-6 text-gray-800">Form Absensi Siswa</h2>

    <div class="bg-white shadow-sm rounded-xl p-6 mb-6 border-l-4 border-green-600">
        <h5 class="text-lg font-bold text-gray-800">
            Mata Pelajaran : {{ $jadwal->mapel->nama_mapel ?? $jadwal->mapel->nama }}
        </h5>
        <p class="text-sm text-gray-600 mt-1">
            <span class="font-semibold text-green-700">Guru:</span> {{ $jadwal->guru->nama }} 
            <span class="mx-2 text-gray-300">|</span> 
            <span class="font-semibold text-green-700">Kelas:</span> {{ $jadwal->kelas }}
        </p>
    </div>

    @php
        $routeAction = Auth::user()->role == 'guru' ? route('guru.absensi.simpan') : route('admin.absensi.simpan');
    @endphp

    <form action="{{ $routeAction }}" method="POST">
        @csrf
        <input type="hidden" name="jadwal_id" value="{{ $jadwal->id }}">

        <div class="bg-white shadow rounded-xl overflow-hidden border border-gray-100">
            <div class="p-4 border-b bg-gray-50 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <label class="text-sm font-bold text-gray-700">Tanggal Presensi:</label>
                    <input
                        type="date"
                        name="tanggal"
                        value="{{ date('Y-m-d') }}"
                        class="border rounded-lg px-3 py-2 w-48 focus:ring-2 focus:ring-green-500 focus:outline-none shadow-sm"
                        required
                    >
                </div>
                <span class="text-xs text-gray-500 italic">* Default hari ini</span>
            </div>

            <table class="min-w-full text-sm">
                <thead class="bg-gray-800 text-white uppercase text-[11px] tracking-wider">
                    <tr>
                        <th class="px-6 py-4 text-left w-12">No</th>
                        <th class="px-6 py-4 text-left">Nama Siswa</th>
                        <th class="px-6 py-4 text-center w-52">Status Kehadiran</th>
                        <th class="px-6 py-4 text-left">Keterangan</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100">
                @foreach($siswas as $index => $siswa)
                <tr class="hover:bg-green-50/50 transition-colors">
                    <td class="px-6 py-4 text-gray-500 font-medium">{{ $index + 1 }}</td>
                    <td class="px-6 py-4">
                        <span class="font-bold text-gray-800">{{ $siswa->nama }}</span>
                    </td>
                    <td class="px-6 py-4">
                        <select
                            name="status[{{ $siswa->id }}]"
                            class="border rounded-lg px-3 py-1.5 w-full focus:ring-2 focus:ring-green-500 outline-none text-sm font-semibold"
                            required
                        >
                            <option value="H" class="text-green-600 font-bold">Hadir</option>
                            <option value="S" class="text-yellow-600 font-bold">Sakit</option>
                            <option value="I" class="text-blue-600 font-bold">Izin</option>
                            <option value="A" class="text-red-600 font-bold">Alpa</option>
                        </select>
                    </td>
                    <td class="px-6 py-4">
                        <input
                            type="text"
                            name="keterangan[{{ $siswa->id }}]"
                            class="border border-gray-200 bg-gray-50 rounded-lg px-3 py-1.5 w-full text-xs focus:bg-white transition-all"
                            placeholder="Opsional (cth: Demam)"
                        >
                    </td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-6 flex items-center gap-3">
            <button type="submit" class="bg-green-700 hover:bg-green-800 text-white px-8 py-2.5 rounded-xl shadow-lg shadow-green-900/20 font-bold transition-all flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                </svg>
                Simpan Presensi
            </button>

            <a href="{{ Auth::user()->role == 'guru' ? route('guru.jadwal') : route('admin.jadwal.index') }}"
               class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-2.5 rounded-xl font-bold transition-all">
               Batal
            </a>
        </div>
    </form>
</div>
@endsection