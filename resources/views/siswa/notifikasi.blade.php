@extends('layouts.siswa')

@section('content')
<div class="space-y-6">
    {{-- Header Halaman --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 border-b border-gray-100 pb-6">
        <div>
            <h2 class="text-2xl font-black text-slate-800 uppercase tracking-tighter flex items-center gap-2">
                <i class="fa-solid fa-triangle-exclamation text-rose-600"></i>
                Log Pelanggaran Absensi
            </h2>
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em] mt-1">
                Daftar peringatan ketidakhadiran (ALPA) yang tercatat di sistem
            </p>
        </div>
        <div class="bg-rose-50 px-4 py-2 rounded-xl border border-rose-100 shadow-sm">
            <span class="text-[10px] font-black text-rose-600 uppercase">Total: {{ $notifikasis->total() }} Peringatan</span>
        </div>
    </div>

    {{-- List Notifikasi --}}
    <div class="grid gap-4">
        @forelse($notifikasis as $notif)
            <div class="bg-white border border-gray-100 p-5 rounded-2xl shadow-sm hover:shadow-md hover:border-rose-200 transition-all group relative overflow-hidden">
                {{-- Garis Indikator Kiri (Selalu Merah karena isinya hanya Alpa) --}}
                <div class="absolute left-0 top-0 bottom-0 w-1.5 bg-rose-500"></div>

                <div class="flex items-start gap-4">
                    {{-- Icon Peringatan --}}
                    <div class="h-12 w-12 shrink-0 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center text-xl">
                        <i class="fa-solid fa-circle-exclamation"></i>
                    </div>

                    <div class="flex-1">
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="font-black text-slate-800 uppercase tracking-tight group-hover:text-rose-600 transition-colors">
                                    Catatan Alpa Otomatis
                                </h3>
                                <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-2">
                                    <i class="fa-regular fa-clock mr-1"></i> {{ $notif->created_at->diffForHumans() }}
                                </p>
                            </div>
                        </div>
                        
                        {{-- Tampilan Pesan --}}
                        <div class="text-sm text-rose-700 leading-relaxed bg-rose-50/50 p-3 rounded-lg border border-rose-100">
                            {{ $notif->isi_pesan }}
                        </div>
                    </div>
                </div>
            </div>
        @empty
            {{-- Tampilan jika bersih dari Alpa --}}
            <div class="bg-white border-2 border-dashed border-gray-100 rounded-3xl py-20 flex flex-col items-center justify-center text-center">
                <div class="h-20 w-20 bg-green-50 rounded-full flex items-center justify-center mb-4 text-green-400">
                    <i class="fa-solid fa-check-double text-3xl"></i>
                </div>
                <h3 class="text-sm font-black text-slate-800 uppercase tracking-widest">Kotak Masuk Bersih</h3>
                <p class="text-[10px] text-slate-400 font-bold mt-1 uppercase">Bagus! Tidak ada catatan ketidakhadiran (ALPA) untuk saat ini.</p>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    <div class="mt-6">
        {{ $notifikasis->links() }}
    </div>
</div>
@endsection