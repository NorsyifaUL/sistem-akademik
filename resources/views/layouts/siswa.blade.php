<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    {{-- SweetAlert2 --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    {{-- Alpine.js untuk fitur Sidebar Toggle --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Siswa - SIAKAD SMANJA</title>
    @vite('resources/css/app.css')
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    {{-- Font Awesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body { font-family: 'Inter', sans-serif; }
        [x-cloak] { display: none !important; }
        
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
<body class="bg-[#F8FAFC] antialiased h-screen overflow-hidden text-slate-900" x-data="{ sidebarOpen: true }">

    <div class="flex h-full">
        {{-- SIDEBAR --}}
        <aside 
            class="bg-[#064e3b] text-white flex flex-col shadow-2xl z-30 flex-shrink-0 transition-all duration-300 ease-in-out"
            :class="sidebarOpen ? 'w-64' : 'w-20'">
            
            <div class="p-6 text-center border-b border-white/10">
                <div class="inline-flex p-3 rounded-2xl bg-white shadow-xl mb-3 transform hover:rotate-3 transition-transform">
                    <img src="{{ asset('logo Smanja.png') }}" 
                         alt="Logo SMAN 1 Jejangkit"
                         class="w-12 h-12 mx-auto object-contain transition-transform duration-300"
                         :class="!sidebarOpen && 'scale-75'">
                </div>
                <div x-show="sidebarOpen" x-transition.opacity>
                    <h1 class="text-base font-black tracking-tighter text-white uppercase"> <span class="text-[#ffb800]">SIAKAD</span></h1>
                    <p class="text-[9px] text-green-300 font-bold uppercase tracking-[0.2em] mt-1 opacity-70">SMAN 1 JEJANGKIT</p>
                </div>
            </div>

            {{-- Navigasi Sidebar --}}
            <nav class="flex-1 p-4 space-y-2 overflow-y-auto custom-scrollbar">
                {{-- Dashboard --}}
                <a href="{{ route('siswa.dashboard') }}" 
                   class="flex items-center px-4 py-3 rounded-xl transition-all duration-200 group
                   {{ request()->routeIs('siswa.dashboard') ? 'bg-[#ffb800] font-black text-[#064e3b] shadow-lg shadow-yellow-900/20' : 'text-green-100 hover:bg-white/10 hover:text-white' }}">
                    <i class="fa-solid fa-house-chimney w-5 mr-3 {{ request()->routeIs('siswa.dashboard') ? '' : 'opacity-50 group-hover:opacity-100' }}"></i>
                    <span class="text-xs uppercase tracking-widest" x-show="sidebarOpen" x-transition.opacity>Dashboard</span>
                </a>

                {{-- Jadwal Pelajaran --}}
                <a href="{{ route('siswa.jadwal') }}" 
                   class="flex items-center px-4 py-3 rounded-xl transition-all duration-200 group
                   {{ request()->routeIs('siswa.jadwal') ? 'bg-[#ffb800] font-black text-[#064e3b] shadow-lg' : 'text-green-100 hover:bg-white/10 hover:text-white' }}">
                    <i class="fa-solid fa-calendar-week w-5 mr-3 {{ request()->routeIs('siswa.jadwal') ? '' : 'opacity-50 group-hover:opacity-100' }}"></i>
                    <span class="text-xs uppercase tracking-widest" x-show="sidebarOpen" x-transition.opacity>Jadwal</span>
                </a>

                {{-- Presensi --}}
                <a href="{{ route('siswa.absensi') }}" 
                   class="flex items-center px-4 py-3 rounded-xl transition-all duration-200 group
                   {{ request()->routeIs('siswa.absensi') ? 'bg-[#ffb800] font-black text-[#064e3b] shadow-lg' : 'text-green-100 hover:bg-white/10 hover:text-white' }}">
                    <i class="fa-solid fa-clipboard-user w-5 mr-3 {{ request()->routeIs('siswa.absensi') ? '' : 'opacity-50 group-hover:opacity-100' }}"></i>
                    <span class="text-xs uppercase tracking-widest" x-show="sidebarOpen" x-transition.opacity>Presensi</span>
                </a>

                {{-- Rapor & Nilai --}}
                <a href="{{ route('siswa.nilai') }}" 
                   class="flex items-center px-4 py-3 rounded-xl transition-all duration-200 group
                   {{ request()->routeIs('siswa.nilai') ? 'bg-[#ffb800] font-black text-[#064e3b] shadow-lg' : 'text-green-100 hover:bg-white/10 hover:text-white' }}">
                    <i class="fa-solid fa-graduation-cap w-5 mr-3 {{ request()->routeIs('siswa.nilai') ? '' : 'opacity-50 group-hover:opacity-100' }}"></i>
                    <span class="text-xs uppercase tracking-widest" x-show="sidebarOpen" x-transition.opacity>Nilai</span>
                </a>
            </nav>

            <div class="p-4 border-t border-white/10 text-center">
                <p x-show="sidebarOpen" class="text-[9px] text-green-400/40 uppercase font-black tracking-[0.2em]" x-transition.opacity>&copy; 2026 SMAN 1 JEJANGKIT</p>
            </div>
        </aside>

        {{-- PEMBUNGKUS AREA KONTEN --}}
        <div class="flex-1 flex flex-col min-w-0 h-full relative">
            
            {{-- HEADER --}}
            <header class="bg-white border-b border-gray-200 h-16 flex justify-between items-center px-8 z-20 shadow-sm flex-shrink-0">
                <div class="flex items-center gap-4">
                    {{-- Tombol Toggle Sidebar --}}
                    <button @click="sidebarOpen = !sidebarOpen" class="text-gray-500 hover:text-[#064e3b] transition-colors focus:outline-none">
                        <i class="fa-solid fa-bars-staggered text-lg"></i>
                    </button>
                    <h1 class="text-sm font-bold text-gray-800 tracking-tight uppercase">
                        Sistem Informasi <span class="text-[#064e3b]">Akademik</span> <span class="text-[#ffb800]">SMANJA</span>
                    </h1>
                </div>

                <div class="flex items-center gap-6">
                    {{-- User Profile Link (Bisa diklik ke Profil) --}}
                    <a href="{{ route('siswa.profil') }}" class="flex items-center gap-3 border-r pr-6 border-gray-100 group transition-all duration-200">
                        <div class="text-right hidden sm:block">
                            <p class="text-xs font-bold text-gray-900 leading-none capitalize group-hover:text-[#064e3b]">{{ auth()->user()->name }}</p>
                            <p class="text-[10px] text-[#064e3b] font-semibold mt-1 uppercase tracking-wider">
                                Siswa • {{ auth()->user()->siswa->nisn ?? '-' }}
                            </p>
                        </div>
                        <div class="h-9 w-9 rounded-full bg-green-50 flex items-center justify-center text-[#064e3b] font-bold border-2 border-green-100 shadow-sm group-hover:border-[#ffb800] group-hover:bg-white transition-all">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                    </a>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="flex items-center gap-2 text-rose-500 hover:text-rose-700 text-xs font-bold uppercase tracking-wider transition-colors focus:outline-none">
                            <i class="fa-solid fa-power-off"></i>
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
    </div>

    {{-- Script Notifikasi SweetAlert2 --}}
    <script>
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'BERHASIL!',
                text: "{{ session('success') }}",
                showConfirmButton: false,
                timer: 3000,
                iconColor: '#064e3b'
            });
        @endif

        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'GAGAL!',
                text: "{{ session('error') }}",
                confirmButtonColor: '#064e3b'
            });
        @endif
    </script>
</body>
</html>