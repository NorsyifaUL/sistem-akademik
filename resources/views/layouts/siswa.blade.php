<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Siswa - SIAKAD SMANJA</title>
    @vite('resources/css/app.css')
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    {{-- Font Awesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Inter', sans-serif; }
        
        /* Custom Scrollbar untuk area konten */
        .custom-scrollbar::-webkit-scrollbar { width: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #f1f1f1; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #064e3b; border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #0a6d53; }
        
        /* Animasi Transisi Halaman */
        .page-enter { animation: slideUp 0.4s ease-out forwards; }
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body class="bg-[#F8FAFC] antialiased flex h-screen overflow-hidden text-slate-900">

    {{-- SIDEBAR --}}
    <aside class="w-64 bg-[#064e3b] flex flex-col shadow-2xl z-30 border-r border-white/5 flex-shrink-0">
        <div class="p-6 text-center border-b border-white/10">
            <div class="inline-flex p-3 rounded-2xl bg-white shadow-xl mb-3 transform hover:rotate-3 transition-transform">
                <img src="{{ asset('logo Smanja.png') }}" 
                     alt="Logo SMAN 1 Jejangkit"
                     class="w-12 h-12 mx-auto object-contain">
            </div>
            <h1 class="text-base font-black tracking-tighter text-white uppercase"> <span class="text-[#ffb800]">SIAKAD</span></h1>
            <p class="text-[9px] text-green-300 font-bold uppercase tracking-[0.2em] mt-1 opacity-70">SMAN 1 JEJANGKIT</p>
        </div>

        {{-- Navigasi Sidebar --}}
        <nav class="flex-1 p-4 space-y-2 overflow-y-auto custom-scrollbar">
            {{-- Dashboard --}}
            <a href="{{ route('siswa.dashboard') }}" 
               class="flex items-center px-4 py-3 rounded-xl transition-all duration-200 group
               {{ request()->routeIs('siswa.dashboard') ? 'bg-[#ffb800] font-black text-[#064e3b] shadow-lg shadow-yellow-900/20' : 'text-green-100 hover:bg-white/10 hover:text-white' }}">
                <i class="fa-solid fa-house-chimney w-5 mr-3 {{ request()->routeIs('siswa.dashboard') ? '' : 'opacity-50 group-hover:opacity-100' }}"></i>
                <span class="text-xs uppercase tracking-widest">Dashboard</span>
            </a>

            {{-- Jadwal Pelajaran --}}
            <a href="{{ route('siswa.jadwal') }}" 
               class="flex items-center px-4 py-3 rounded-xl transition-all duration-200 group
               {{ request()->routeIs('siswa.jadwal') ? 'bg-[#ffb800] font-black text-[#064e3b] shadow-lg' : 'text-green-100 hover:bg-white/10 hover:text-white' }}">
                <i class="fa-solid fa-calendar-week w-5 mr-3 {{ request()->routeIs('siswa.jadwal') ? '' : 'opacity-50 group-hover:opacity-100' }}"></i>
                <span class="text-xs uppercase tracking-widest">Jadwal Pelajaran</span>
            </a>

            {{-- Presensi --}}
            <a href="{{ route('siswa.absensi') }}" 
               class="flex items-center px-4 py-3 rounded-xl transition-all duration-200 group
               {{ request()->routeIs('siswa.absensi') ? 'bg-[#ffb800] font-black text-[#064e3b] shadow-lg' : 'text-green-100 hover:bg-white/10 hover:text-white' }}">
                <i class="fa-solid fa-clipboard-user w-5 mr-3 {{ request()->routeIs('siswa.absensi') ? '' : 'opacity-50 group-hover:opacity-100' }}"></i>
                <span class="text-xs uppercase tracking-widest">Presensi</span>
            </a>

            {{-- Rapor & Nilai --}}
            <a href="{{ route('siswa.nilai') }}" 
               class="flex items-center px-4 py-3 rounded-xl transition-all duration-200 group
               {{ request()->routeIs('siswa.nilai') ? 'bg-[#ffb800] font-black text-[#064e3b] shadow-lg' : 'text-green-100 hover:bg-white/10 hover:text-white' }}">
                <i class="fa-solid fa-graduation-cap w-5 mr-3 {{ request()->routeIs('siswa.nilai') ? '' : 'opacity-50 group-hover:opacity-100' }}"></i>
                <span class="text-xs uppercase tracking-widest">Nilai</span>
            </a>
        </nav>

        <div class="p-4 border-t border-white/10 text-center">
            <p class="text-[9px] text-green-400/40 uppercase font-black tracking-[0.2em] leading-relaxed">&copy; 2026 SMAN 1 JEJANGKIT</p>
        </div>
    </aside>

    {{-- PEMBUNGKUS AREA KONTEN (HEADER + ISI) --}}
    <div class="flex-1 flex flex-col min-w-0 h-full relative">
        
        {{-- HEADER --}}
        <header class="bg-white border-b border-gray-200 h-16 flex justify-between items-center px-8 z-20 shadow-sm flex-shrink-0">
            <div class="flex items-center gap-4">
                <button class="text-gray-400 hover:text-[#064e3b] transition-colors focus:outline-none md:hidden">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7" />
                    </svg>
                </button>
                <h1 class="text-lg font-bold text-gray-800 tracking-tight">
                    Sistem Informasi <span class="text-[#064e3b]">Akademik</span> <span class="text-[#ffb800]">SMANJA</span>
                </h1>
            </div>

            <div class="flex items-center gap-6">
                <div class="flex items-center gap-3 border-r pr-6 border-gray-100">
                    <div class="text-right hidden sm:block">
                        <p class="text-xs font-bold text-gray-900 leading-none capitalize">{{ auth()->user()->name }}</p>
                        <p class="text-[10px] text-[#064e3b] font-semibold mt-1 uppercase tracking-wider">
                            Siswa • {{ auth()->user()->siswa->nisn ?? '-' }}
                        </p>
                    </div>
                    <div class="h-9 w-9 rounded-full bg-green-50 flex items-center justify-center text-[#064e3b] font-bold border-2 border-green-100 shadow-sm">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                </div>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="flex items-center gap-2 text-rose-500 hover:text-rose-700 text-xs font-bold uppercase tracking-wider transition-colors focus:outline-none">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        Logout
                    </button>
                </form>
            </div>
        </header>

        {{-- MAIN CONTENT --}}
        <main class="flex-1 overflow-y-auto p-8 bg-[#F8FAFC] custom-scrollbar">
            <div class="max-w-6xl mx-auto page-enter pb-12">
                @yield('content')
            </div>
        </main>
    </div>

</body>
</html>