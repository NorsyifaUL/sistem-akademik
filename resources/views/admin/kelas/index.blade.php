@extends('layouts.admin')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h2 class="text-3xl font-extrabold text-gray-800 tracking-tight">DATA KELAS</h2>
        <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mt-1">SISTEM INFORMASI AKADEMIK SMANJA</p>
    </div>
    <a href="{{ route('admin.kelas.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-lg text-sm font-bold transition-all shadow-md flex items-center gap-2">
        <i class="fa-solid fa-plus text-xs"></i> Tambah Kelas
    </a>
</div>

<div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100">
    <div class="h-1.5 bg-blue-600 w-full"></div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead>
                <tr class="text-gray-400 uppercase text-[10px] font-extrabold tracking-widest border-b border-gray-100">
                    <th class="px-8 py-5 text-center w-20">NO</th>
                    <th class="px-8 py-5">NAMA KELAS</th>
                    <th class="px-8 py-5">WALI KELAS</th>
                    <th class="px-8 py-5 text-center">TINDAKAN</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($kelas as $item)
                <tr class="hover:bg-gray-50/50 transition-colors">
                    <td class="px-8 py-5 text-center font-bold text-gray-400">{{ $loop->iteration }}</td>
                    <td class="px-8 py-5 font-extrabold text-gray-800 uppercase">{{ $item->nama_kelas }}</td>
                    
                    <td class="px-8 py-5 text-gray-500 font-medium italic">
                        {{-- 
                            Rantai Relasi: 
                            1. $item->guru (Model Guru)
                            2. $item->guru->user (Model User)
                            3. is_wali_kelas == 1 (Verifikasi Role)
                        --}}
                        @if($item->guru && $item->guru->user && $item->guru->user->is_wali_kelas == 1)
                            {{ $item->guru->nama }}
                        @else
                            <span class="text-red-400 italic text-xs font-bold">Wali Kelas Tidak Ditemukan</span>
                        @endif
                    </td>

                    <td class="px-8 py-5 text-center">
                        <div class="flex justify-center items-center gap-4">
                            <a href="{{ route('admin.kelas.edit', $item->id) }}" class="text-orange-400 hover:text-orange-600">
                                <i class="fa-solid fa-pen-to-square text-lg"></i>
                            </a>
                            <form action="{{ route('admin.kelas.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Hapus data kelas ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-400 hover:text-red-600">
                                    <i class="fa-solid fa-trash-can text-lg"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-8 py-12 text-center text-gray-400 italic font-bold">Data Kelas Belum Tersedia.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection