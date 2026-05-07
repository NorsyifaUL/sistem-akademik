<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - SIAKAD SMANJA</title>
    
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    {{-- Menggunakan FontAwesome 6 Free --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    @vite('resources/css/app.css')
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body { font-family: 'Inter', sans-serif; }
        [x-cloak] { display: none !important; }
        .sidebar-transition { transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 10px; }
    </style>
</head>

<body class="bg-gray-100 antialiased h-screen overflow-hidden" x-data="{ sidebarOpen: true }">

<div class="flex h-full">
    {{-- SIDEBAR --}}
    <aside class="bg-blue-900 text-white flex flex-col shadow-xl z-30 flex-shrink-0 sidebar-transition" :class="sidebarOpen ? 'w-64' : 'w-20'">
        <div class="p-6 text-center border-b border-blue-800/50 flex-shrink-0 overflow-hidden">
            <div class="inline-flex p-2 rounded-xl bg-white/10 mb-3 shadow-inner ring-1 ring-white/20">
                <img src="{{ asset('logo Smanja.png') }}" alt="Logo Sekolah" class="w-14 h-14 mx-auto object-contain">
            </div>
            <div x-show="sidebarOpen" x-transition.opacity>
                <h1 class="text-base font-extrabold tracking-wider leading-tight uppercase whitespace-nowrap">SIAKAD</h1>
                <p class="text-[10px] text-blue-300 font-medium uppercase tracking-widest mt-1 whitespace-nowrap">SMAN 1 JEJANGKIT</p>
            </div>
        </div>

        <nav class="flex-1 p-4 space-y-1.5 overflow-y-auto custom-scrollbar overflow-x-hidden">
            {{-- Dashboard --}}
            <a href="{{ route('admin.dashboard') }}" class="flex items-center px-4 py-2.5 rounded-xl transition-all duration-200 group whitespace-nowrap {{ request()->routeIs('admin.dashboard') ? 'bg-blue-700 font-bold shadow-lg text-white' : 'text-blue-100 hover:bg-blue-800 hover:text-white' }}">
                <i class="fa-solid fa-house w-6 text-center {{ request()->routeIs('admin.dashboard') ? 'text-white' : 'text-blue-400 group-hover:text-white' }}"></i>
                <span class="ml-3 text-sm" x-show="sidebarOpen">Dashboard</span>
            </a>

            {{-- Data Guru --}}
            <a href="{{ route('admin.guru.index') }}" class="flex items-center px-4 py-2.5 rounded-xl transition-all duration-200 group whitespace-nowrap {{ request()->routeIs('admin.guru.*') ? 'bg-blue-700 font-bold shadow-lg text-white' : 'text-blue-100 hover:bg-blue-800 hover:text-white' }}">
                <i class="fa-solid fa-chalkboard-user w-6 text-center {{ request()->routeIs('admin.guru.*') ? 'text-white' : 'text-blue-400 group-hover:text-white' }}"></i>
                <span class="ml-3 text-sm" x-show="sidebarOpen">Data Guru</span>
            </a>

            {{-- Data Kelas (PERBAIKAN IKON DI SINI) --}}
            <a href="{{ route('admin.kelas.index') }}" class="flex items-center px-4 py-2.5 rounded-xl transition-all duration-200 group whitespace-nowrap {{ request()->routeIs('admin.kelas.*') ? 'bg-blue-700 font-bold shadow-lg text-white' : 'text-blue-100 hover:bg-blue-800 hover:text-white' }}">
                <i class="fa-solid fa-school w-6 text-center {{ request()->routeIs('admin.kelas.*') ? 'text-white' : 'text-blue-400 group-hover:text-white' }}"></i>
                <span class="ml-3 text-sm" x-show="sidebarOpen">Data Kelas</span>
            </a>

            {{-- Data Siswa --}}
            <a href="{{ route('admin.siswa.index') }}" class="flex items-center px-4 py-2.5 rounded-xl transition-all duration-200 group whitespace-nowrap {{ request()->routeIs('admin.siswa.*') ? 'bg-blue-700 font-bold shadow-lg text-white' : 'text-blue-100 hover:bg-blue-800 hover:text-white' }}">
                <i class="fa-solid fa-user-graduate w-6 text-center {{ request()->routeIs('admin.siswa.*') ? 'text-white' : 'text-blue-400 group-hover:text-white' }}"></i>
                <span class="ml-3 text-sm" x-show="sidebarOpen">Data Siswa</span>
            </a>

            {{-- Mata Pelajaran --}}
            <a href="{{ route('admin.mapel.index') }}" class="flex items-center px-4 py-2.5 rounded-xl transition-all duration-200 group whitespace-nowrap {{ request()->routeIs('admin.mapel.*') ? 'bg-blue-700 font-bold shadow-lg text-white' : 'text-blue-100 hover:bg-blue-800 hover:text-white' }}">
                <i class="fa-solid fa-book w-6 text-center {{ request()->routeIs('admin.mapel.*') ? 'text-white' : 'text-blue-400 group-hover:text-white' }}"></i>
                <span class="ml-3 text-sm" x-show="sidebarOpen">Mata Pelajaran</span>
            </a>

            {{-- Jadwal Pelajaran --}}
            <a href="{{ route('admin.jadwal.index') }}" class="flex items-center px-4 py-2.5 rounded-xl transition-all duration-200 group whitespace-nowrap {{ request()->routeIs('admin.jadwal.*') ? 'bg-blue-700 font-bold shadow-lg text-white' : 'text-blue-100 hover:bg-blue-800 hover:text-white' }}">
                <i class="fa-solid fa-calendar-days w-6 text-center {{ request()->routeIs('admin.jadwal.*') ? 'text-white' : 'text-blue-400 group-hover:text-white' }}"></i>
                <span class="ml-3 text-sm" x-show="sidebarOpen">Jadwal Pelajaran</span>
            </a>

            {{-- Nilai Siswa --}}
            <a href="{{ route('admin.nilai.index') }}" class="flex items-center px-4 py-2.5 rounded-xl transition-all duration-200 group whitespace-nowrap {{ request()->routeIs('admin.nilai.*') ? 'bg-blue-700 font-bold shadow-lg text-white' : 'text-blue-100 hover:bg-blue-800 hover:text-white' }}">
                <i class="fa-solid fa-file-signature w-6 text-center {{ request()->routeIs('admin.nilai.*') ? 'text-white' : 'text-blue-400 group-hover:text-white' }}"></i>
                <span class="ml-3 text-sm" x-show="sidebarOpen">Nilai Siswa</span>
            </a>

            {{-- Absensi --}}
            <a href="{{ route('admin.absensi.index') }}" class="flex items-center px-4 py-2.5 rounded-xl transition-all duration-200 group whitespace-nowrap {{ request()->routeIs('admin.absensi.*') ? 'bg-blue-700 font-bold shadow-lg text-white' : 'text-blue-100 hover:bg-blue-800 hover:text-white' }}">
                <i class="fa-solid fa-clipboard-check w-6 text-center {{ request()->routeIs('admin.absensi.*') ? 'text-white' : 'text-blue-400 group-hover:text-white' }}"></i>
                <span class="ml-3 text-sm" x-show="sidebarOpen">Absensi</span>
            </a>

            {{-- Notifikasi --}}
            <a href="{{ route('admin.notifikasi.index') }}" class="flex items-center px-4 py-2.5 rounded-xl transition-all duration-200 group whitespace-nowrap {{ request()->routeIs('admin.notifikasi.*') ? 'bg-blue-700 font-bold shadow-lg text-white' : 'text-blue-100 hover:bg-blue-800 hover:text-white' }}">
                <i class="fa-solid fa-bell w-6 text-center {{ request()->routeIs('admin.notifikasi.*') ? 'text-white' : 'text-blue-400 group-hover:text-white' }}"></i>
                <span class="ml-3 text-sm" x-show="sidebarOpen">Notifikasi</span>
            </a>

            {{-- Pengaturan --}}
            <a href="{{ route('admin.settings.index') }}" class="flex items-center px-4 py-2.5 rounded-xl transition-all duration-200 group whitespace-nowrap {{ request()->routeIs('admin.settings.*') ? 'bg-blue-700 font-bold shadow-lg text-white' : 'text-blue-100 hover:bg-blue-800 hover:text-white' }}">
                <i class="fa-solid fa-gears w-6 text-center {{ request()->routeIs('admin.settings.*') ? 'text-white' : 'text-blue-400 group-hover:text-white' }}"></i>
                <span class="ml-3 text-sm" x-show="sidebarOpen">Pengaturan</span>
            </a>
        </nav>

        <div class="p-4 border-t border-blue-800/50 flex-shrink-0">
            <p class="text-[9px] text-center text-blue-400 uppercase font-bold tracking-tighter">
                <span x-show="sidebarOpen">&copy; 2026 SIAKAD SMANJA</span>
                <span x-show="!sidebarOpen">2026</span>
            </p>
        </div>
    </aside>

    {{-- KONTEN --}}
    <div class="flex-1 flex flex-col min-w-0">
        <header class="bg-white border-b border-gray-200 h-16 flex justify-between items-center px-8 z-20 shadow-sm flex-shrink-0">
            <div class="flex items-center gap-4">
                <button @click="sidebarOpen = !sidebarOpen" class="text-gray-400 hover:text-blue-600 transition-colors">
                    <i class="fa-solid fa-bars-staggered text-xl"></i>
                </button>
                <h1 class="text-lg font-bold text-gray-800 tracking-tight">Sistem Informasi <span class="text-blue-600">Akademik</span> SMANJA</h1>
            </div>

            <div class="flex items-center gap-6">
                <div class="flex items-center gap-3 border-r pr-6 border-gray-100 text-right">
                    <div>
                        <p class="text-xs font-bold text-gray-900 leading-none capitalize">{{ auth()->user()->name }}</p>
                        <p class="text-[10px] text-blue-600 font-semibold mt-1 uppercase">Administrator</p>
                    </div>
                    <div class="h-9 w-9 rounded-full bg-blue-100 flex items-center justify-center text-blue-700 font-bold border-2 border-blue-200">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-red-500 hover:text-red-700 text-xs font-bold uppercase tracking-wider transition-colors">
                        <i class="fa-solid fa-right-from-bracket mr-1"></i> Logout
                    </button>
                </form>
            </div>
        </header>

        <main class="flex-1 overflow-y-auto bg-gray-50 p-8 content-scrollbar">
            <div class="max-w-7xl mx-auto">
                @yield('content')
            </div>
        </main>
    </div>
</div>

</body>
</html>