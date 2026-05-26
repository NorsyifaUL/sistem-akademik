<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - SIAKAD SMANJA</title>
    
    {{-- SweetAlert2 --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    {{-- Alpine.js untuk fitur Dropdown & Collapse --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    {{-- Menggunakan FontAwesome 6 Free --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body { font-family: 'Inter', sans-serif; }
        [x-cloak] { display: none !important; }
        .sidebar-transition { transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
        
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
        .custom-scrollbar::-webkit-scrollbar { 
            width: 4px; 
        }
        .custom-scrollbar::-webkit-scrollbar-thumb { 
            background: rgba(255,255,255,0.1); 
            border-radius: 10px; 
        }
    </style>
</head>

<body class="bg-gray-100 antialiased h-screen overflow-hidden" 
      x-data="{ sidebarOpen: window.innerWidth >= 1024 }"
      @resize.window="sidebarOpen = window.innerWidth >= 1024">

<div class="flex h-full">
    {{-- SIDEBAR --}}
    <aside class="bg-blue-900 text-white flex flex-col shadow-xl z-30 flex-shrink-0 sidebar-transition" :class="sidebarOpen ? 'w-64' : 'w-20'">
        <div class="p-6 text-center border-b border-blue-800/50 flex-shrink-0 overflow-hidden">
            <div class="inline-flex p-2 rounded-xl bg-white/10 mb-3 shadow-inner ring-1 ring-white/20">
                <img src="{{ asset('logo Smanja.png') }}" alt="Logo Sekolah" class="w-14 h-14 mx-auto object-contain">
            </div>
            <div x-show="sidebarOpen" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                <h1 class="text-base font-extrabold tracking-wider leading-tight uppercase whitespace-nowrap">SIAKAD</h1>
                <p class="text-[10px] text-blue-300 font-medium uppercase tracking-widest mt-1 whitespace-nowrap">SMAN 1 JEJANGKIT</p>
            </div>
        </div>

        <nav class="flex-1 p-4 space-y-1.5 overflow-y-auto custom-scrollbar overflow-x-hidden">
            {{-- Dashboard --}}
            <a href="{{ route('admin.dashboard') }}" class="flex items-center px-4 py-2.5 rounded-xl transition-all duration-200 group whitespace-nowrap {{ request()->routeIs('admin.dashboard') ? 'bg-blue-700 font-bold shadow-lg text-white' : 'text-blue-100 hover:bg-blue-800 hover:text-white' }}">
                <i class="fa-solid fa-house w-6 text-center {{ request()->routeIs('admin.dashboard') ? 'text-white' : 'text-blue-400 group-hover:text-white' }}"></i>
                <span class="ml-3 text-sm" x-show="sidebarOpen" x-transition.opacity>Dashboard</span>
            </a>
            
            {{-- Data Guru --}}
            <a href="{{ route('admin.guru.index') }}" class="flex items-center px-4 py-2.5 rounded-xl transition-all duration-200 group whitespace-nowrap {{ request()->routeIs('admin.guru.*') ? 'bg-blue-700 font-bold shadow-lg text-white' : 'text-blue-100 hover:bg-blue-800 hover:text-white' }}">
                <i class="fa-solid fa-chalkboard-user w-6 text-center {{ request()->routeIs('admin.guru.*') ? 'text-white' : 'text-blue-400 group-hover:text-white' }}"></i>
                <span class="ml-3 text-sm" x-show="sidebarOpen" x-transition.opacity>Data Guru</span>
            </a>

            {{-- Data Kelas --}}
            <a href="{{ route('admin.kelas.index') }}" class="flex items-center px-4 py-2.5 rounded-xl transition-all duration-200 group whitespace-nowrap {{ request()->routeIs('admin.kelas.*') ? 'bg-blue-700 font-bold shadow-lg text-white' : 'text-blue-100 hover:bg-blue-800 hover:text-white' }}">
                <i class="fa-solid fa-school w-6 text-center {{ request()->routeIs('admin.kelas.*') ? 'text-white' : 'text-blue-400 group-hover:text-white' }}"></i>
                <span class="ml-3 text-sm" x-show="sidebarOpen" x-transition.opacity>Data Kelas</span>
            </a>

            {{-- Data Siswa --}}
            <a href="{{ route('admin.siswa.index') }}" class="flex items-center px-4 py-2.5 rounded-xl transition-all duration-200 group whitespace-nowrap {{ request()->routeIs('admin.siswa.*') ? 'bg-blue-700 font-bold shadow-lg text-white' : 'text-blue-100 hover:bg-blue-800 hover:text-white' }}">
                <i class="fa-solid fa-user-graduate w-6 text-center {{ request()->routeIs('admin.siswa.*') ? 'text-white' : 'text-blue-400 group-hover:text-white' }}"></i>
                <span class="ml-3 text-sm" x-show="sidebarOpen" x-transition.opacity>Data Siswa</span>
            </a>

            {{-- Mata Pelajaran --}}
            <a href="{{ route('admin.mapel.index') }}" class="flex items-center px-4 py-2.5 rounded-xl transition-all duration-200 group whitespace-nowrap {{ request()->routeIs('admin.mapel.*') ? 'bg-blue-700 font-bold shadow-lg text-white' : 'text-blue-100 hover:bg-blue-800 hover:text-white' }}">
                <i class="fa-solid fa-book w-6 text-center {{ request()->routeIs('admin.mapel.*') ? 'text-white' : 'text-blue-400 group-hover:text-white' }}"></i>
                <span class="ml-3 text-sm" x-show="sidebarOpen" x-transition.opacity>Mata Pelajaran</span>
            </a>

            {{-- Jadwal Pelajaran --}}
            <a href="{{ route('admin.jadwal.index') }}" class="flex items-center px-4 py-2.5 rounded-xl transition-all duration-200 group whitespace-nowrap {{ request()->routeIs('admin.jadwal.*') ? 'bg-blue-700 font-bold shadow-lg text-white' : 'text-blue-100 hover:bg-blue-800 hover:text-white' }}">
                <i class="fa-solid fa-calendar-days w-6 text-center {{ request()->routeIs('admin.jadwal.*') ? 'text-white' : 'text-blue-400 group-hover:text-white' }}"></i>
                <span class="ml-3 text-sm" x-show="sidebarOpen" x-transition.opacity>Jadwal Pelajaran</span>
            </a>

            {{-- Nilai Siswa --}}
            <a href="{{ route('admin.nilai.index') }}" class="flex items-center px-4 py-2.5 rounded-xl transition-all duration-200 group whitespace-nowrap {{ request()->routeIs('admin.nilai.*') ? 'bg-blue-700 font-bold shadow-lg text-white' : 'text-blue-100 hover:bg-blue-800 hover:text-white' }}">
                <i class="fa-solid fa-file-signature w-6 text-center {{ request()->routeIs('admin.nilai.*') ? 'text-white' : 'text-blue-400 group-hover:text-white' }}"></i>
                <span class="ml-3 text-sm" x-show="sidebarOpen" x-transition.opacity>Nilai Siswa</span>
            </a>

            {{-- Absensi --}}
            <a href="{{ route('admin.absensi.index') }}" class="flex items-center px-4 py-2.5 rounded-xl transition-all duration-200 group whitespace-nowrap {{ request()->routeIs('admin.absensi.*') ? 'bg-blue-700 font-bold shadow-lg text-white' : 'text-blue-100 hover:bg-blue-800 hover:text-white' }}">
                <i class="fa-solid fa-clipboard-check w-6 text-center {{ request()->routeIs('admin.absensi.*') ? 'text-white' : 'text-blue-400 group-hover:text-white' }}"></i>
                <span class="ml-3 text-sm" x-show="sidebarOpen" x-transition.opacity>Absensi</span>
            </a>

            {{-- Notifikasi --}}
            <a href="{{ route('admin.notifikasi.index') }}" class="flex items-center px-4 py-2.5 rounded-xl transition-all duration-200 group whitespace-nowrap {{ request()->routeIs('admin.notifikasi.*') ? 'bg-blue-700 font-bold shadow-lg text-white' : 'text-blue-100 hover:bg-blue-800 hover:text-white' }}">
                <i class="fa-solid fa-bell w-6 text-center {{ request()->routeIs('admin.notifikasi.*') ? 'text-white' : 'text-blue-400 group-hover:text-white' }}"></i>
                <span class="ml-3 text-sm" x-show="sidebarOpen" x-transition.opacity>Notifikasi</span>
            </a>

            {{-- Pengaturan --}}
            <a href="{{ route('admin.settings.index') }}" class="flex items-center px-4 py-2.5 rounded-xl transition-all duration-200 group whitespace-nowrap {{ request()->routeIs('admin.settings.*') ? 'bg-blue-700 font-bold shadow-lg text-white' : 'text-blue-100 hover:bg-blue-800 hover:text-white' }}">
                <i class="fa-solid fa-gears w-6 text-center {{ request()->routeIs('admin.settings.*') ? 'text-white' : 'text-blue-400 group-hover:text-white' }}"></i>
                <span class="ml-3 text-sm" x-show="sidebarOpen" x-transition.opacity>Pengaturan</span>
            </a>
        </nav>

        <div class="p-4 border-t border-blue-800/50 flex-shrink-0">
            <p class="text-[9px] text-center text-blue-400 uppercase font-bold tracking-tighter">
                <span x-show="sidebarOpen">&copy; 2026 SIAKAD SMANJA</span>
                <span x-show="!sidebarOpen" x-cloak>2026</span>
            </p>
        </div>
    </aside>

    {{-- KONTEN --}}
    <div class="flex-1 flex flex-col min-w-0">
        <header class="bg-white border-b border-gray-200 h-16 flex justify-between items-center px-4 sm:px-8 z-20 shadow-sm flex-shrink-0 gap-2">
            <div class="flex items-center gap-2 sm:gap-4 min-w-0">
                <button @click="sidebarOpen = !sidebarOpen" class="text-gray-400 hover:text-blue-600 transition-colors p-1">
                    <i class="fa-solid fa-bars-staggered text-base sm:text-xl"></i>
                </button>
                <h1 class="text-sm sm:text-lg font-bold text-gray-800 tracking-tight uppercase truncate">
                    Sistem Informasi <span class="text-blue-600 font-extrabold hidden md:inline">Akademik</span> SMANJA
                </h1>
            </div>

            <div class="flex items-center gap-3 sm:gap-6 flex-shrink-0">
                <a href="{{ route('admin.profil') }}" class="flex items-center gap-2 sm:gap-3 border-r pr-3 sm:pr-6 border-gray-100 text-right group hover:opacity-80 transition-all min-w-0">
                    <div class="hidden sm:block">
                        <p class="text-xs font-bold text-gray-900 leading-none capitalize group-hover:text-blue-600 transition-colors truncate max-w-[120px] lg:max-w-[180px]">{{ auth()->user()->name }}</p>
                        <p class="text-[10px] text-blue-600 font-semibold mt-1 uppercase tracking-wider">Administrator</p>
                    </div>
                    <div class="h-8 w-8 sm:h-9 sm:w-9 rounded-full bg-blue-100 flex items-center justify-center text-blue-700 font-bold border-2 border-blue-200 shadow-sm group-hover:border-blue-500 group-hover:bg-blue-600 group-hover:text-white transition-all text-xs sm:text-sm flex-shrink-0">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                </a>
                
                <form method="POST" action="{{ route('logout') }}" class="flex-shrink-0">
                    @csrf
                    <button type="submit" class="text-red-500 hover:text-red-700 text-[10px] sm:text-xs font-bold uppercase tracking-wider transition-colors focus:outline-none flex items-center gap-1">
                        <i class="fa-solid fa-right-from-bracket"></i> <span class="hidden md:inline">Logout</span>
                    </button>
                </form>
            </div>
        </header>

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
            iconColor: '#1d4ed8'
        });
    @endif

    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'GAGAL!',
            text: "{{ session('error') }}",
            confirmButtonColor: '#1d4ed8'
        });
    @endif
</script>

</body>
</html>