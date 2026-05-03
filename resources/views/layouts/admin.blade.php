<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - SIAKAD SMANJA</title>
    
    {{-- Alpine.js untuk fitur Toggle --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    {{-- TAMBAHAN: FontAwesome agar ikon di halaman dashboard muncul --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    @vite('resources/css/app.css')
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body { font-family: 'Inter', sans-serif; }
        [x-cloak] { display: none !important; }

        .sidebar-transition {
            transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 10px; }

        .content-scrollbar::-webkit-scrollbar { width: 6px; }
        .content-scrollbar::-webkit-scrollbar-track { background: #f1f5f9; }
        .content-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    </style>
</head>

<body class="bg-gray-100 antialiased h-screen overflow-hidden" x-data="{ sidebarOpen: true }">

<div class="flex h-full">

    {{-- SIDEBAR --}}
    <aside 
        class="bg-blue-900 text-white flex flex-col shadow-xl z-30 flex-shrink-0 sidebar-transition"
        :class="sidebarOpen ? 'w-64' : 'w-20'">

        <div class="p-6 text-center border-b border-blue-800/50 flex-shrink-0 overflow-hidden">
            <div class="inline-flex p-2 rounded-xl bg-white/10 mb-3 shadow-inner ring-1 ring-white/20">
                <img src="{{ asset('logo Smanja.png') }}" 
                     alt="Logo Sekolah"
                     class="w-14 h-14 mx-auto object-contain">
            </div>

            <div x-show="sidebarOpen" x-transition.opacity>
                <h1 class="text-base font-extrabold tracking-wider leading-tight uppercase whitespace-nowrap">SIAKAD</h1>
                <p class="text-[10px] text-blue-300 font-medium uppercase tracking-widest mt-1 whitespace-nowrap">SMAN 1 JEJANGKIT</p>
            </div>
        </div>

        {{-- DAFTAR FITUR LENGKAP --}}
        <nav class="flex-1 p-4 space-y-1.5 overflow-y-auto custom-scrollbar overflow-x-hidden">

            {{-- Dashboard --}}
            <a href="{{ route('admin.dashboard') }}" 
               class="flex items-center px-4 py-2.5 rounded-xl transition-all duration-200 group whitespace-nowrap
               {{ request()->routeIs('admin.dashboard') ? 'bg-blue-700 font-bold shadow-lg shadow-black/20 text-white' : 'text-blue-100 hover:bg-blue-800 hover:text-white' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0 {{ request()->routeIs('admin.dashboard') ? 'text-white' : 'text-blue-400 group-hover:text-white' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                <span class="ml-3 text-sm" x-show="sidebarOpen" x-transition.opacity>Dashboard</span>
            </a>

            {{-- Data Guru --}}
            <a href="{{ route('admin.guru.index') }}" 
               class="flex items-center px-4 py-2.5 rounded-xl transition-all duration-200 group whitespace-nowrap
               {{ request()->routeIs('admin.guru.index') ? 'bg-blue-700 font-bold shadow-lg shadow-black/20 text-white' : 'text-blue-100 hover:bg-blue-800 hover:text-white' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0 {{ request()->routeIs('admin.guru.index') ? 'text-white' : 'text-blue-400 group-hover:text-white' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
                <span class="ml-3 text-sm" x-show="sidebarOpen" x-transition.opacity>Data Guru</span>
            </a>

            {{-- Data Siswa --}}
            <a href="{{ route('admin.siswa.index') ?? '#' }}" 
               class="flex items-center px-4 py-2.5 rounded-xl transition-all duration-200 group whitespace-nowrap
               {{ request()->routeIs('admin.siswa.*') ? 'bg-blue-700 font-bold shadow-lg shadow-black/20 text-white' : 'text-blue-100 hover:bg-blue-800 hover:text-white' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0 {{ request()->routeIs('admin.siswa.*') ? 'text-white' : 'text-blue-400 group-hover:text-white' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                <span class="ml-3 text-sm" x-show="sidebarOpen" x-transition.opacity>Data Siswa</span>
            </a>

            {{-- Mata Pelajaran --}}
            <a href="{{ route('admin.mapel.index') ?? '#' }}" 
               class="flex items-center px-4 py-2.5 rounded-xl transition-all duration-200 group whitespace-nowrap
               {{ request()->routeIs('admin.mapel.*') ? 'bg-blue-700 font-bold shadow-lg shadow-black/20 text-white' : 'text-blue-100 hover:bg-blue-800 hover:text-white' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0 {{ request()->routeIs('admin.mapel.*') ? 'text-white' : 'text-blue-400 group-hover:text-white' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
                <span class="ml-3 text-sm" x-show="sidebarOpen" x-transition.opacity>Mata Pelajaran</span>
            </a>

            {{-- Jadwal Pelajaran --}}
            <a href="{{ route('admin.jadwal.index') }}" 
               class="flex items-center px-4 py-2.5 rounded-xl transition-all duration-200 group whitespace-nowrap
               {{ request()->routeIs('admin.jadwal.*') ? 'bg-blue-700 font-bold shadow-lg shadow-black/20 text-white' : 'text-blue-100 hover:bg-blue-800 hover:text-white' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0 {{ request()->routeIs('admin.jadwal.*') ? 'text-white' : 'text-blue-400 group-hover:text-white' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <span class="ml-3 text-sm" x-show="sidebarOpen" x-transition.opacity>Jadwal Pelajaran</span>
            </a>

            {{-- Nilai Siswa --}}
            <a href="{{ route('admin.nilai.index') }}" 
               class="flex items-center px-4 py-2.5 rounded-xl transition-all duration-200 group whitespace-nowrap
               {{ request()->routeIs('admin.nilai.index') ? 'bg-blue-700 font-bold shadow-lg shadow-black/20 text-white' : 'text-blue-100 hover:bg-blue-800 hover:text-white' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0 {{ request()->routeIs('admin.nilai.index') ? 'text-white' : 'text-blue-400 group-hover:text-white' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
                <span class="ml-3 text-sm" x-show="sidebarOpen" x-transition.opacity>Nilai Siswa</span>
            </a>

            {{-- Absensi --}}
            <a href="{{ route('admin.absensi.index') }}" 
               class="flex items-center px-4 py-2.5 rounded-xl transition-all duration-200 group whitespace-nowrap
               {{ request()->routeIs('admin.absensi.*') ? 'bg-blue-700 font-bold shadow-lg shadow-black/20 text-white' : 'text-blue-100 hover:bg-blue-800 hover:text-white' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0 {{ request()->routeIs('admin.absensi.*') ? 'text-white' : 'text-blue-400 group-hover:text-white' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                </svg>
                <span class="ml-3 text-sm" x-show="sidebarOpen" x-transition.opacity>Absensi</span>
            </a>

            {{-- Notifikasi --}}
            <a href="{{ route('admin.notifikasi.index') }}" 
               class="flex items-center px-4 py-2.5 rounded-xl transition-all duration-200 group whitespace-nowrap
               {{ request()->routeIs('admin.notifikasi.*') ? 'bg-blue-700 font-bold shadow-lg shadow-black/20 text-white' : 'text-blue-100 hover:bg-blue-800 hover:text-white' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0 {{ request()->routeIs('admin.notifikasi.*') ? 'text-white' : 'text-blue-400 group-hover:text-white' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
                <span class="ml-3 text-sm" x-show="sidebarOpen" x-transition.opacity>Notifikasi</span>
            </a>

            {{-- Pengaturan Raport --}}
            <a href="{{ route('admin.settings.index') }}" 
               class="flex items-center px-4 py-2.5 rounded-xl transition-all duration-200 group whitespace-nowrap
               {{ request()->routeIs('admin.settings.*') ? 'bg-blue-700 font-bold shadow-lg shadow-black/20 text-white' : 'text-blue-100 hover:bg-blue-800 hover:text-white' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0 {{ request()->routeIs('admin.settings.*') ? 'text-white' : 'text-blue-400 group-hover:text-white' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                <span class="ml-3 text-sm" x-show="sidebarOpen" x-transition.opacity>Pengaturan Raport</span>
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
                <button @click="sidebarOpen = !sidebarOpen" class="text-gray-400 hover:text-blue-600 transition-colors focus:outline-none">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7" />
                    </svg>
                </button>
                <h1 class="text-lg font-bold text-gray-800 tracking-tight">
                    Sistem Informasi <span class="text-blue-600">Akademik</span> SMANJA
                </h1>
            </div>

            <div class="flex items-center gap-6">
                <div class="flex items-center gap-3 border-r pr-6 border-gray-100">
                    <div class="text-right">
                        <p class="text-xs font-bold text-gray-900 leading-none capitalize">{{ auth()->user()->name }}</p>
                        <p class="text-[10px] text-blue-600 font-semibold mt-1 uppercase tracking-wider">Administrator</p>
                    </div>
                    <div class="h-9 w-9 rounded-full bg-blue-100 flex items-center justify-center text-blue-700 font-bold border-2 border-blue-200 shadow-sm">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                </div>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="flex items-center gap-2 text-red-500 hover:text-red-700 text-xs font-bold uppercase tracking-wider transition-colors focus:outline-none">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        Logout
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