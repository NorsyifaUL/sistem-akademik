<!DOCTYPE html>
<html lang="id">
<head>
    {{-- SweetAlert2 --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    {{-- Alpine.js untuk fitur Dropdown & Collapse --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Guru - SIAKAD SMANJA</title>

    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        body { font-family: 'Inter', sans-serif; }
        [x-cloak] { display: none !important; }

        /* Scrollbar untuk Bagian Konten Utama */
        .content-scrollbar::-webkit-scrollbar {
            width: 6px;
        }
        .content-scrollbar::-webkit-scrollbar-track {
            background: #f1f5f9;
        }
        .content-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }
        .content-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        /* Scrollbar untuk Sidebar */
        .sidebar-scrollbar::-webkit-scrollbar {
            width: 4px;
        }
        .sidebar-scrollbar::-webkit-scrollbar-thumb {
            background: rgba(255,255,255,0.1);
            border-radius: 10px;
        }

        .font-academic { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-100 antialiased h-screen overflow-hidden" 
      x-data="{ sidebarOpen: window.innerWidth >= 1024 }"
      @resize.window="sidebarOpen = window.innerWidth >= 1024">

@php
    // LOGIKA PENGAMBILAN DATA GURU & JADWAL
    $guruId = auth()->user()->guru->id ?? null;
    
    // Ambil nama mapel untuk profil (Fitur Baru)
    $mapelUtama = $guruId ? \App\Models\Jadwal::where('guru_id', $guruId)->with('mapel')->first() : null;
    $namaMapel = $mapelUtama->mapel->nama_mapel ?? 'Guru Aktif';

    // Cari jadwal pertama untuk link langsung Rekap Nilai
    $jadwalPertama = $guruId ? \App\Models\Jadwal::where('guru_id', $guruId)->first() : null;
    
    // Cek apakah guru adalah wali kelas
    $isWali = $guruId ? \App\Models\Kelas::where('guru_id', $guruId)->exists() : false;
@endphp

<div class="flex h-full">

    {{-- SIDEBAR --}}
    <aside 
        class="bg-green-900 text-white flex flex-col shadow-xl z-30 flex-shrink-0 transition-all duration-300 ease-in-out"
        :class="sidebarOpen ? 'w-64' : 'w-20'">
        
        <div class="p-6 text-center border-b border-green-800/50">
            <div class="inline-flex p-2 rounded-xl bg-white/10 mb-3 shadow-inner ring-1 ring-white/20">
                <img src="{{ asset('logo Smanja.png') }}" 
                     alt="Logo SMAN 1 Jejangkit"
                     class="w-14 h-14 mx-auto object-contain transition-transform duration-300"
                     :class="!sidebarOpen && 'scale-75'">
            </div>
            {{-- Nama Sekolah (Disembunyikan saat Toggle) --}}
            <div x-show="sidebarOpen" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                <h1 class="text-base font-extrabold tracking-wider leading-tight uppercase">SIAKAD</h1>
                <p class="text-[10px] text-green-300 font-medium uppercase tracking-widest mt-1">SMAN 1 JEJANGKIT</p>
            </div>
        </div>

        {{-- Sidebar Menu --}}
        <nav class="flex-1 p-4 space-y-2 overflow-y-auto sidebar-scrollbar">
            
            {{-- Dashboard --}}
            <a href="{{ route('guru.dashboard') }}" 
               class="flex items-center px-4 py-2.5 rounded-xl transition-all duration-200 group
               {{ request()->routeIs('guru.dashboard') ? 'bg-green-700 font-bold shadow-lg shadow-black/20 text-white' : 'text-green-100 hover:bg-green-800 hover:text-white' }}">
                <i class="fa-solid fa-house-chimney w-5 mr-3 {{ request()->routeIs('guru.dashboard') ? 'text-white' : 'text-green-400 group-hover:text-white' }}"></i>
                <span class="text-sm" x-show="sidebarOpen" x-transition.opacity>Dashboard</span>
            </a>

            {{-- Jadwal Mengajar --}}
            <a href="{{ route('guru.jadwal') }}" 
               class="flex items-center px-4 py-2.5 rounded-xl transition-all duration-200 group
               {{ (request()->routeIs('guru.jadwal') && !request()->routeIs('guru.jadwal.siswa') && !request()->routeIs('guru.jadwal.legger')) ? 'bg-green-700 font-bold shadow-lg shadow-black/20 text-white' : 'text-green-100 hover:bg-green-800 hover:text-white' }}">
                <i class="fa-solid fa-calendar-days w-5 mr-3 {{ (request()->routeIs('guru.jadwal') && !request()->routeIs('guru.jadwal.legger')) ? 'text-white' : 'text-green-400 group-hover:text-white' }}"></i>
                <span class="text-sm" x-show="sidebarOpen" x-transition.opacity>Jadwal Mengajar</span>
            </a>

            {{-- Absensi Area --}}
            <a href="{{ route('guru.absensi.index') }}" 
               class="flex items-center px-4 py-2.5 rounded-xl transition-all duration-200 group
               {{ request()->routeIs('guru.absensi.index') ? 'bg-green-700 font-bold shadow-lg shadow-black/20 text-white' : 'text-green-100 hover:bg-green-800 hover:text-white' }}">
                <i class="fa-solid fa-user-check w-5 mr-3 {{ request()->routeIs('guru.absensi.index') ? 'text-white' : 'text-green-400 group-hover:text-white' }}"></i>
                <span class="text-sm" x-show="sidebarOpen" x-transition.opacity>Absensi Siswa</span>
            </a>

            <a href="{{ route('guru.absensi.rekap') }}" 
               class="flex items-center px-4 py-2.5 rounded-xl transition-all duration-200 group
               {{ request()->routeIs('guru.absensi.rekap') ? 'bg-green-700 font-bold shadow-lg shadow-black/20 text-white' : 'text-green-100 hover:bg-green-800 hover:text-white' }}">
                <i class="fa-solid fa-file-lines w-5 mr-3 {{ request()->routeIs('guru.absensi.rekap') ? 'text-white' : 'text-green-400 group-hover:text-white' }}"></i>
                <span class="text-sm" x-show="sidebarOpen" x-transition.opacity>Rekap Absensi</span>
            </a>

            <hr class="border-green-800/50 my-2">

            {{-- MENU LAPORAN NILAI (DROPDOWN) --}}
            <div x-data="{ open: {{ (request()->is('guru/nilai*') || request()->routeIs('guru.lihat_nilai') || request()->routeIs('guru.jadwal.legger')) ? 'true' : 'false' }} }">
                <button @click="sidebarOpen ? open = !open : (sidebarOpen = true, open = true)" 
                    class="w-full flex items-center justify-between px-4 py-2.5 rounded-xl transition-all duration-200 group
                    {{ (request()->is('guru/nilai*') || request()->routeIs('guru.lihat_nilai') || request()->routeIs('guru.jadwal.legger')) ? 'bg-green-700 text-white shadow-lg shadow-black/20' : 'text-green-100 hover:bg-green-800 hover:text-white' }}">
                    <div class="flex items-center">
                        <i class="fa-solid fa-file-signature w-5 mr-3 {{ (request()->is('guru/nilai*') || request()->routeIs('guru.lihat_nilai') || request()->routeIs('guru.jadwal.legger')) ? 'text-white' : 'text-green-400 group-hover:text-white' }}"></i>
                        <span class="text-sm" x-show="sidebarOpen" x-transition.opacity>Laporan Nilai Siswa</span>
                    </div>
                    <i x-show="sidebarOpen" class="fa-solid fa-chevron-down text-[10px] transition-transform duration-200" :class="open ? 'rotate-180' : ''"></i>
                </button>

                <div x-show="open && sidebarOpen" x-cloak x-collapse
                     class="mt-2 ml-4 pl-4 border-l border-green-700 space-y-1">
                    
                    {{-- Submenu 1: Input Nilai --}}
                    <a href="{{ route('guru.lihat_nilai') }}" 
                       class="flex items-center px-4 py-2 text-xs rounded-lg transition-colors
                       {{ request()->routeIs('guru.lihat_nilai') ? 'bg-green-600 text-white font-bold' : 'text-green-200 hover:bg-green-800 hover:text-white' }}">
                        <i class="fa-solid fa-table-list mr-2 opacity-70"></i> Input Nilai
                    </a>

                    {{-- Submenu 2: Rekap Nilai (Legger) --}}
                    <a href="{{ $jadwalPertama ? route('guru.jadwal.legger', $jadwalPertama->id) : route('guru.jadwal') }}" 
                       class="flex items-center px-4 py-2 text-xs rounded-lg transition-colors
                       {{ request()->routeIs('guru.jadwal.legger') ? 'bg-green-600 text-white font-bold' : 'text-green-200 hover:bg-green-800 hover:text-white' }}">
                        <i class="fa-solid fa-file-invoice mr-2 opacity-70"></i> Rekap Nilai
                    </a>

                   @if(!empty(Auth::user()->wali_kelas))
                    {{-- Submenu 3: Raport (Hanya muncul jika Wali Kelas) --}}
                    <a href="{{ route('guru.raport.index') }}" 
                    class="flex items-center px-4 py-2 text-xs rounded-lg transition-colors
                    {{ request()->routeIs('guru.raport.*') ? 'bg-green-600 text-white font-bold' : 'text-green-200 hover:bg-green-800 hover:text-white' }}">
                        <i class="fa-solid fa-user-graduate mr-2 opacity-70"></i> Sikap & Eskul (Raport)
                    </a>
                @endif
                </div>
            </div>

        </nav>

        <div class="p-4 border-t border-green-800/50">
            <p x-show="sidebarOpen" class="text-[9px] text-center text-green-400 uppercase font-bold tracking-tighter" x-transition.opacity>&copy; 2026 SIAKAD SMANJA</p>
            <p x-show="!sidebarOpen" class="text-center font-bold text-green-400"></p>
        </div>
    </aside>

    {{-- KONTEN AREA --}}
    <div class="flex-1 flex flex-col min-w-0">
        
        {{-- HEADER --}}
        <header class="bg-white border-b border-gray-200 h-16 flex justify-between items-center px-4 sm:px-8 z-20 shadow-sm flex-shrink-0 gap-2">
            <div class="flex items-center gap-2 sm:gap-4 min-w-0">
                {{-- Tombol Toggle Sidebar --}}
                <button @click="sidebarOpen = !sidebarOpen" class="text-gray-500 hover:text-green-700 transition-colors focus:outline-none p-1">
                    <i class="fa-solid fa-bars-staggered text-base sm:text-lg"></i>
                </button>
                <h1 class="text-sm sm:text-lg font-bold text-gray-800 tracking-tight uppercase truncate">
                    Sistem Informasi <span class="text-green-700 font-extrabold hidden md:inline">Akademik</span> SMANJA
                </h1>
            </div>

            <div class="flex items-center gap-3 sm:gap-6 flex-shrink-0">
                <a href="{{ route('guru.profil') }}" class="flex items-center gap-2 sm:gap-3 border-r pr-3 sm:pr-6 border-gray-100 text-right hover:bg-gray-50 p-1 sm:p-2 rounded-lg transition-colors group min-w-0">
                    <div class="text-right hidden sm:block">
                        <p class="text-xs font-bold text-gray-900 leading-none capitalize truncate max-w-[120px] lg:max-w-[180px]">{{ auth()->user()->name }}</p>
                        {{-- Nama Mapel Spesifik --}}
                        <p class="text-[9px] sm:text-[10px] text-green-600 font-semibold mt-0.5 sm:mt-1 uppercase italic truncate max-w-[120px] lg:max-w-[180px]">
                            Guru {{ $namaMapel }}
                        </p>
                    </div>
                    <div class="h-8 w-8 sm:h-9 sm:w-9 rounded-full bg-green-100 flex items-center justify-center text-green-700 font-bold border-2 border-green-200 shadow-sm text-xs sm:text-sm flex-shrink-0">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                </a>

                <form method="POST" action="{{ route('logout') }}" class="flex-shrink-0">
                    @csrf
                    <button type="submit" class="flex items-center gap-1.5 text-red-500 hover:text-red-700 text-[10px] sm:text-xs font-bold uppercase tracking-wider transition-colors focus:outline-none">
                        <i class="fa-solid fa-power-off text-xs"></i>
                        <span class="hidden md:inline">Logout</span>
                    </button>
                </form>
            </div>
        </header>

        {{-- MAIN CONTENT --}}
        <main class="flex-1 overflow-y-auto bg-gray-50 p-4 sm:p-8 content-scrollbar">
            <div class="max-w-7xl mx-auto">
                @yield('content')
            </div>
        </main>
    </div>
</div>

<script>
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'BERHASIL!',
            text: "{{ session('success') }}",
            showConfirmButton: false,
            timer: 3000,
            iconColor: '#15803d'
        });
    @endif

    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'GAGAL!',
            text: "{{ session('error') }}",
            confirmButtonColor: '#15803d'
        });
    @endif
</script>

</body>
</html>