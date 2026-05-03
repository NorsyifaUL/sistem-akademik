@extends('layouts.admin')

@section('content')
<div class="space-y-6 animate-fade-in">
    {{-- 1. Welcome Banner --}}
    <div class="relative overflow-hidden bg-gradient-to-r from-blue-800 to-indigo-900 rounded-2xl p-8 shadow-lg">
        <div class="relative z-10">
            <h1 class="text-2xl font-black text-white tracking-tight">Selamat Datang, {{ auth()->user()->name }}! 👋</h1>
            <p class="text-blue-100 text-sm mt-1 font-medium opacity-90">Panel Kendali Administrator SIAKAD SMAN 1 JEJANGKIT</p>
        </div>
        <div class="absolute top-0 right-0 -mt-4 -mr-4 w-32 h-32 bg-white/10 rounded-full blur-3xl"></div>
    </div>

    {{-- 2. Statistik Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        @php
            $stats = [
                ['label' => 'Total Guru', 'val' => $totalGuru, 'icon' => 'fa-chalkboard-user', 'bg' => 'bg-blue-600'],
                ['label' => 'Total Siswa', 'val' => $totalSiswa, 'icon' => 'fa-user-graduate', 'bg' => 'bg-emerald-600'],
                ['label' => 'Mata Pelajaran', 'val' => $totalMapel, 'icon' => 'fa-book', 'bg' => 'bg-orange-500'],
                ['label' => 'Absen Hari Ini', 'val' => $absensiHariIni, 'icon' => 'fa-calendar-check', 'bg' => 'bg-rose-500'],
            ];
        @endphp
        @foreach($stats as $stat)
        <div class="flex bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden h-24 hover:shadow-md transition-all group">
            <div class="w-20 {{ $stat['bg'] }} flex items-center justify-center group-hover:scale-105 transition-transform duration-300">
                <i class="fa-solid {{ $stat['icon'] }} text-3xl text-white"></i>
            </div>
            <div class="flex-1 flex flex-col justify-center px-6">
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest leading-none">{{ $stat['label'] }}</p>
                <p class="text-2xl font-black text-gray-800 mt-2">{{ $stat['val'] }}</p>
            </div>
        </div>
        @endforeach
    </div>

    {{-- 3. Bagian Bawah: Jadwal & Diagram --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- KOLOM JADWAL BERLANGSUNG --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 flex flex-col overflow-hidden">
            <div class="p-5 border-b border-gray-50 flex items-center justify-between bg-gray-50/50">
                <h3 class="font-bold text-gray-800 text-[11px] uppercase tracking-widest flex items-center gap-2">
                    <i class="fa-solid fa-clock text-blue-600"></i> Jadwal Berlangsung
                </h3>
                <span class="animate-pulse flex h-2 w-2 rounded-full bg-green-500"></span>
            </div>
            <div class="p-5 flex-1 overflow-y-auto max-h-[320px] custom-scrollbar">
                @forelse($jadwalHariIni as $jadwal)
                    <div class="flex items-start gap-4 p-3 rounded-xl border border-gray-50 mb-3 hover:bg-blue-50/30 transition-colors group">
                        <div class="mt-1 h-8 w-8 bg-white shadow-sm border border-blue-100 text-blue-600 rounded-lg flex items-center justify-center flex-shrink-0 group-hover:bg-blue-600 group-hover:text-white transition-all">
                            <i class="fa-solid fa-book-open text-[12px]"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-[11px] text-gray-800 font-bold leading-tight truncate">{{ $jadwal->mapel->nama_mapel ?? 'Mapel' }}</p>
                            <p class="text-[10px] text-gray-500 font-medium italic mt-0.5">Kelas {{ $jadwal->relasiKelas->nama_kelas ?? $jadwal->kelas }} • {{ $jadwal->jam_mulai }}</p>
                            <div class="flex items-center gap-1.5 mt-2">
                                <i class="fa-solid fa-user-tie text-[9px] text-blue-400"></i>
                                <span class="text-[9px] text-gray-400 font-bold uppercase truncate">{{ $jadwal->guru->nama ?? 'Guru' }}</span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-12">
                        <i class="fa-solid fa-calendar-xmark text-gray-200 text-3xl mb-2"></i>
                        <p class="text-gray-400 text-[10px] font-bold uppercase tracking-widest">Tidak ada jadwal hari ini</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- KOLOM DIAGRAM ABSENSI --}}
        <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-5 border-b border-gray-50 flex items-center justify-between bg-gray-50/50">
                <h3 class="font-bold text-gray-800 text-[11px] uppercase tracking-widest flex items-center gap-2">
                    <i class="fa-solid fa-chart-bar text-emerald-600"></i> Statistik Presensi Harian
                </h3>
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-tighter">Ringkasan Tingkat</span>
            </div>
            <div class="p-6">
                <div class="h-[250px] w-full">
                    <canvas id="barChartAbsensi"></canvas>
                </div>
                {{-- Legend --}}
                <div class="flex justify-center gap-6 mt-5">
                    <div class="flex items-center gap-2">
                        <div class="w-2.5 h-2.5 rounded-full bg-emerald-500"></div>
                        <span class="text-[10px] font-bold text-gray-500 uppercase tracking-wide">Hadir</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-2.5 h-2.5 rounded-full bg-blue-500"></div>
                        <span class="text-[10px] font-bold text-gray-500 uppercase tracking-wide">Izin/Sakit</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-2.5 h-2.5 rounded-full bg-rose-500"></div>
                        <span class="text-[10px] font-bold text-gray-500 uppercase tracking-wide">Alpa</span>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

{{-- Script Chart.js --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('barChartAbsensi').getContext('2d');
        
        // Data ini dikirim dari Controller yang menggunakan query LIKE 'X%', 'XI%', 'XII%'
        const dataHadir = @json($dataChart['hadir'] ?? [0,0,0]);
        const dataIzin = @json($dataChart['izin'] ?? [0,0,0]);
        const dataAlpa = @json($dataChart['alpa'] ?? [0,0,0]);

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Kelas X (X1, X2)', 'Kelas XI (XI 1, 2)', 'Kelas XII (IPA/IPS)'],
                datasets: [
                    { 
                        label: 'Hadir', 
                        data: dataHadir, 
                        backgroundColor: '#10b981', 
                        borderRadius: 6, 
                        barThickness: 25 
                    },
                    { 
                        label: 'Izin/Sakit', 
                        data: dataIzin, 
                        backgroundColor: '#3b82f6', 
                        borderRadius: 6, 
                        barThickness: 25 
                    },
                    { 
                        label: 'Alpa', 
                        data: dataAlpa, 
                        backgroundColor: '#f43f5e', 
                        borderRadius: 6, 
                        barThickness: 25 
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { 
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            title: function(context) {
                                return context[0].label;
                            }
                        }
                    }
                },
                scales: {
                    x: { 
                        grid: { display: false }, 
                        ticks: { font: { size: 10, weight: 'bold' } } 
                    },
                    y: { 
                        beginAtZero: true, 
                        grid: { color: '#f3f4f6' }, 
                        ticks: { 
                            font: { size: 10 },
                            stepSize: 1
                        } 
                    }
                }
            }
        });
    });
</script>

<style>
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    .animate-fade-in { animation: fadeIn 0.5s ease-out forwards; }
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #e5e7eb; border-radius: 10px; }
</style>
@endsection