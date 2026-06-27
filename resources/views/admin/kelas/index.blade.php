@extends('layouts.admin')

@section('content')
<div class="p-4 space-y-4 animate-fade-in">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 px-1">
        <div>
            <h1 class="text-xl font-black text-slate-800 tracking-tight uppercase leading-none">
                Data <span class="text-blue-600">Kelas</span>
            </h1>
            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-[0.2em] mt-1">SMAN 1 Jejangkit</p>
        </div>
        
        <a href="{{ route('admin.kelas.create') }}" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg font-black text-[11px] transition-all shadow-sm uppercase tracking-widest active:scale-95 w-max">
            <i class="fa-solid fa-plus text-[10px]"></i> Tambah Kelas
        </a>
    </div>

    {{-- Main Card --}}
    <div class="w-full bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="h-[3px] bg-blue-600 w-full"></div>
        
        <div class="w-full overflow-x-auto">
            <table class="w-full text-left min-w-[600px]"> 
                <thead>
                    <tr class="bg-slate-50 text-slate-500 font-black uppercase text-[10px] tracking-widest border-b border-slate-100">
                        <th class="px-6 py-4 text-center w-16">No</th>
                        <th class="px-6 py-4">Identitas Kelas</th>
                        <th class="px-6 py-4">Wali Kelas</th>
                        <th class="px-6 py-4 text-center w-32">Opsi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($kelas as $item)
                    <tr class="hover:bg-blue-50/20 transition-all group">
                        <td class="px-6 py-4 text-center">
                            <span class="text-[11px] font-bold text-slate-300 group-hover:text-blue-500 transition-colors italic">
                                #{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-lg bg-slate-100 flex items-center justify-center group-hover:bg-blue-600 group-hover:text-white transition-all duration-300 text-slate-400 border border-slate-200">
                                    <i class="fa-solid fa-chalkboard text-[11px]"></i>
                                </div>
                                <span class="font-black text-slate-700 uppercase text-[13px] tracking-tight group-hover:text-blue-600 transition-colors">
                                    Kelas {{ $item->nama_kelas }}
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            @if($item->guru && $item->guru->user && !empty($item->guru->user->wali_kelas))
                                <div class="flex flex-col">
                                    <span class="text-[11px] font-black text-slate-700 uppercase tracking-tight">{{ $item->guru->nama }}</span>
                                    <span class="text-[9px] text-slate-400 font-bold uppercase tracking-widest mt-0.5 italic">NIP. {{ $item->guru->nip ?? '-' }}</span>
                                </div>
                            @else
                                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[9px] font-black bg-rose-50 text-rose-500 border border-rose-100 uppercase tracking-widest">
                                    Belum Ditentukan
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex justify-center gap-2">
                                <a href="{{ route('admin.kelas.edit', $item->id) }}" class="w-8 h-8 flex items-center justify-center bg-white text-amber-500 hover:bg-amber-500 hover:text-white rounded-lg transition-all border border-amber-100" title="Edit Kelas">
                                    <i class="fa-solid fa-pen-to-square text-[11px]"></i>
                                </a>
                                <form action="{{ route('admin.kelas.destroy', $item->id) }}" method="POST" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="w-8 h-8 flex items-center justify-center bg-white text-rose-500 hover:bg-rose-500 hover:text-white rounded-lg transition-all border border-rose-100" title="Hapus Kelas" onclick="return confirm('Hapus data kelas ini?')">
                                        <i class="fa-solid fa-trash-can text-[11px]"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="py-16 text-center">
                            <div class="flex flex-col items-center opacity-20">
                                <i class="fa-solid fa-layer-group text-5xl mb-3 text-slate-300"></i>
                                <span class="text-[11px] font-black text-slate-500 uppercase tracking-widest">Data Kosong</span>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
    @keyframes fadeIn { from { opacity: 0; transform: translateY(5px); } to { opacity: 1; transform: translateY(0); } }
    .animate-fade-in { animation: fadeIn 0.4s ease-out forwards; }
</style>
@endsection