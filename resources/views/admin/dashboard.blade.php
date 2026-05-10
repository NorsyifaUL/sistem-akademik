@extends('layouts.admin')

@section('content')
<div class="p-4 space-y-5 animate-fade-in">
    {{-- 1. Statistik Cards - Versi Standar Balanced --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        @php
            $stats = [
                ['label' => 'Total Guru', 'val' => $totalGuru, 'icon' => 'fa-chalkboard-user', 'bg' => 'bg-blue-600'],
                ['label' => 'Total Siswa', 'val' => $totalSiswa, 'icon' => 'fa-user-graduate', 'bg' => 'bg-emerald-600'],
                ['label' => 'Mata Pelajaran', 'val' => $totalMapel, 'icon' => 'fa-book', 'bg' => 'bg-orange-500'],
                ['label' => 'Absen Hari Ini', 'val' => $absensiHariIni, 'icon' => 'fa-calendar-check', 'bg' => 'bg-rose-500'],
            ];
        @endphp
        @foreach($stats as $stat)
        <div class="flex bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden h-20 hover:shadow-md transition-all group">
            <div class="w-14 {{ $stat['bg'] }} flex items-center justify-center group-hover:scale-105 transition-transform duration-300">
                <i class="fa-solid {{ $stat['icon'] }} text-xl text-white"></i>
            </div>
            <div class="flex-1 flex flex-col justify-center px-5">
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-0.5">{{ $stat['label'] }}</p>
                <p class="text-2xl font-black text-slate-800 leading-none">{{ $stat['val'] }}</p>
            </div>
        </div>
        @endforeach
    </div>

    {{-- 2. Bagian Bawah --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        
        {{-- JADWAL AKTIF - Tinggi Sedikit Dinaikkan --}}
        <div class="bg-white rounded-xl border border-slate-100 shadow-sm flex flex-col overflow-hidden h-[360px]">
            <div class="px-5 py-4 border-b border-slate-50 flex items-center justify-between bg-slate-50/30">
                <h3 class="font-black text-slate-700 text-[10px] uppercase tracking-widest flex items-center gap-2">
                    <i class="fa-solid fa-clock text-blue-600 text-xs"></i> Jadwal Aktif
                </h3>
            </div>
            <div class="p-4 flex-1 overflow-y-auto custom-scrollbar">
                @forelse($jadwalHariIni as $jadwal)
                    <div class="flex items-center gap-4 p-3 rounded-xl border border-slate-50 mb-2 hover:bg-blue-50/50 transition-colors group">
                        <div class="h-8 w-8 bg-white shadow-sm border border-blue-50 text-blue-600 rounded-lg flex items-center justify-center flex-shrink-0 group-hover:bg-blue-600 group-hover:text-white transition-all">
                            <i class="fa-solid fa-book-open text-[11px]"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-[11px] text-slate-800 font-black uppercase truncate leading-tight">{{ $jadwal->mapel->nama_mapel ?? 'Mapel' }}</p>
                            <p class="text-[10px] text-slate-500 font-bold tracking-tight mt-0.5">
                                {{ $jadwal->relasiKelas->nama_kelas ?? $jadwal->kelas }} <span class="mx-1 text-slate-300">•</span> <span class="text-blue-600">{{ $jadwal->jam_mulai }}</span>
                            </p>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-20">
                        <i class="fa-solid fa-calendar-xmark text-slate-100 text-4xl mb-3"></i>
                        <p class="text-slate-400 text-[10px] font-black uppercase tracking-widest">Belum Ada Jadwal</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- STATISTIK PRESENSI - Lebih Lega --}}
        <div class="lg:col-span-2 bg-white rounded-xl border border-slate-100 shadow-sm overflow-hidden flex flex-col h-[360px]">
            <div class="px-5 py-4 border-b border-slate-50 flex items-center justify-between bg-slate-50/30">
                <h3 class="font-black text-slate-700 text-[10px] uppercase tracking-widest flex items-center gap-2">
                    <i class="fa-solid fa-chart-bar text-emerald-600 text-xs"></i> Statistik Presensi
                </h3>
                <div class="flex gap-4">
                    @foreach(['Hadir' => 'bg-emerald-500', 'Izin' => 'bg-blue-500', 'Alpa' => 'bg-rose-500'] as $label => $color)
                    <div class="flex items-center gap-1.5">
                        <div class="w-2 h-2 rounded-full {{ $color }}"></div>
                        <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wide">{{ $label }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
            <div class="p-6 flex-1">
                <div class="h-full w-full">
                    <canvas id="barChartAbsensi"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('barChartAbsensi').getContext('2d');
        const labelsKelas = @json($dataChart['labels'] ?? []);

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labelsKelas,
                datasets: [
                    { 
                        label: 'Hadir', 
                        data: @json($dataChart['hadir'] ?? []), 
                        backgroundColor: '#10b981', 
                        borderRadius: 4, 
                        barThickness: labelsKelas.length > 5 ? 15 : 25 
                    },
                    { 
                        label: 'Izin', 
                        data: @json($dataChart['izin'] ?? []), 
                        backgroundColor: '#3b82f6', 
                        borderRadius: 4, 
                        barThickness: labelsKelas.length > 5 ? 15 : 25 
                    },
                    { 
                        label: 'Alpa', 
                        data: @json($dataChart['alpa'] ?? []), 
                        backgroundColor: '#f43f5e', 
                        borderRadius: 4, 
                        barThickness: labelsKelas.length > 5 ? 15 : 25 
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { 
                        grid: { display: false }, 
                        ticks: { font: { size: 10, weight: 'bold' }, color: '#64748b' } 
                    },
                    y: { 
                        beginAtZero: true, 
                        grid: { color: '#f1f5f9' }, 
                        ticks: { font: { size: 10 }, color: '#94a3b8', stepSize: 5 } 
                    }
                }
            }
        });
    });
</script>

<style>
    .custom-scrollbar::-webkit-scrollbar { width: 5px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    .animate-fade-in { animation: fadeIn 0.5s ease-out forwards; }
</style>
@endsection