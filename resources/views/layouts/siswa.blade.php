<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Siswa - SIAKAD SMANJA</title>
    
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        [x-cloak] { display: none !important; }
        
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #064e3b; border-radius: 10px; }
        
        .page-enter { animation: slideUp 0.3s ease-out forwards; }
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(8px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body class="bg-[#F8FAFC] antialiased h-screen overflow-hidden text-slate-900" 
      x-data="{ sidebarOpen: window.innerWidth >= 1024 }"
      @resize.window="sidebarOpen = window.innerWidth >= 1024">

    <div class="flex h-full">
        {{-- SIDEBAR - Menggunakan Hijau Gelap SMANJA --}}
        <aside 
            class="bg-[#064e3b] text-white flex flex-col z-30 flex-shrink-0 transition-all duration-300 ease-in-out border-r border-white/10"
            :class="sidebarOpen ? 'w-60' : 'w-20'">
            
            <div class="p-5 text-center border-b border-white/10 flex-shrink-0 overflow-hidden">
                <div class="inline-flex p-2.5 rounded-xl bg-white shadow-lg mb-2 transform hover:rotate-3 transition-transform">
                    <img src="{{ asset('logo Smanja.png') }}" 
                         alt="Logo SMAN 1 Jejangkit"
                         class="w-10 h-10 mx-auto object-contain transition-transform duration-300"
                         :class="!sidebarOpen && 'scale-75'">
                </div>
                <div x-show="sidebarOpen" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                    <h1 class="text-sm font-black tracking-tighter text-white uppercase italic text-center whitespace-nowrap">SIAKAD <span class="text-[#ffb800] not-italic">SMANJA</span></h1>
                    <p class="text-[8px] text-green-300 font-bold uppercase tracking-[0.2em] mt-0.5 opacity-60 italic whitespace-nowrap">Sman 1 Jejangkit</p>
                </div>
            </div>

            <nav class="flex-1 p-3 space-y-1.5 overflow-y-auto custom-scrollbar overflow-x-hidden">
                @php
                    $menus = [
                        ['route' => 'siswa.dashboard', 'icon' => 'fa-house-chimney', 'label' => 'Dashboard'],
                        ['route' => 'siswa.jadwal', 'icon' => 'fa-calendar-week', 'label' => 'Jadwal'],
                        ['route' => 'siswa.absensi', 'icon' => 'fa-clipboard-user', 'label' => 'Presensi'],
                        ['route' => 'siswa.nilai', 'icon' => 'fa-graduation-cap', 'label' => 'Nilai'],
                    ];
                @endphp

                @foreach($menus as $menu)
                <a href="{{ route($menu['route']) }}" 
                   class="flex items-center px-4 py-2.5 rounded-lg transition-all duration-200 group border border-transparent whitespace-nowrap
                   {{ request()->routeIs($menu['route']) ? 'bg-[#ffb800] font-black text-[#064e3b] shadow-md border-[#d99c00]' : 'text-green-100 hover:bg-white/5 hover:text-white' }}">
                    <i class="fa-solid {{ $menu['icon'] }} w-5 mr-3 text-center {{ request()->routeIs($menu['route']) ? '' : 'opacity-40 group-hover:opacity-100' }}"></i>
                    <span class="text-[10px] font-black uppercase tracking-widest" x-show="sidebarOpen" x-transition.opacity>{{ $menu['label'] }}</span>
                </a>
                @endforeach
            </nav>

            <div class="p-4 border-t border-white/10 text-center flex-shrink-0">
                <p x-show="sidebarOpen" class="text-[8px] text-green-400/30 uppercase font-black tracking-widest italic" x-transition.opacity>&copy; 2026 SIAKAD SMAN 1 Jejangkit</p>
                <p x-show="!sidebarOpen" class="text-center font-bold text-green-400/30" x-cloak></p>
            </div>
        </aside>

        {{-- AREA KONTEN --}}
        <div class="flex-1 flex flex-col min-w-0 h-full relative">
            
            {{-- HEADER --}}
            <header class="bg-white border-b border-gray-200 h-16 flex justify-between items-center px-4 sm:px-8 z-20 flex-shrink-0 shadow-sm gap-2">
                <div class="flex items-center gap-3 sm:gap-6 min-w-0">
                    <button @click="sidebarOpen = !sidebarOpen" class="text-slate-400 hover:text-[#064e3b] transition-colors focus:outline-none p-1">
                        <i class="fa-solid fa-bars text-base sm:text-xl"></i>
                    </button>
                    
                    <h1 class="text-sm sm:text-lg font-extrabold text-slate-800 tracking-tight uppercase truncate">
                        Sistem Informasi <span class="text-[#064e3b]">Akademik</span> SMANJA
                    </h1>
                </div>

                <div class="flex items-center gap-3 sm:gap-6 flex-shrink-0">
                    {{-- Info User --}}
                    <div class="flex items-center gap-2 sm:gap-4 border-r pr-3 sm:pr-6 border-gray-200 min-w-0">
                        <div class="text-right hidden sm:block">
                            <p class="text-sm font-black text-slate-900 leading-none capitalize truncate max-w-[120px] lg:max-w-[180px]">
                                {{ auth()->user()->name }}
                            </p>
                            <p class="text-[10px] text-emerald-600 font-bold mt-1 uppercase tracking-wider truncate max-w-[120px] lg:max-w-[180px]">
                                SISWA • {{ auth()->user()->siswa->nisn ?? '-' }}
                            </p>
                        </div>
                        {{-- Inisial Bulat Sesuai Gambar --}}
                        <a href="{{ route('siswa.profil') }}" class="h-8 w-8 sm:h-10 sm:w-10 rounded-full bg-emerald-100 border-2 border-emerald-200 flex items-center justify-center text-emerald-600 font-black text-xs sm:text-sm hover:border-emerald-400 hover:bg-white transition-all shadow-sm flex-shrink-0">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </a>
                    </div>

                    {{-- Logout Merah Sesuai Gambar --}}
                    <form method="POST" action="{{ route('logout') }}" class="flex-shrink-0">
                        @csrf
                        <button type="submit" class="flex items-center gap-1.5 text-rose-500 hover:text-rose-600 transition-all focus:outline-none group">
                            <i class="fa-solid fa-right-from-bracket text-base sm:text-lg group-hover:translate-x-1 transition-transform"></i>
                            <span class="text-[10px] sm:text-xs font-black uppercase tracking-widest hidden md:inline">Logout</span>
                        </button>
                    </form>
                </div>
            </header>

            {{-- MAIN CONTENT --}}
            <main class="flex-1 overflow-y-auto p-4 sm:p-6 bg-[#F8FAFC] custom-scrollbar">
                <div class="max-w-6xl mx-auto page-enter pb-10">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    <script>
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: '<span class="text-sm font-black uppercase italic tracking-tighter">Berhasil</span>',
                html: '<span class="text-[11px] font-bold uppercase tracking-tight">{{ session("success") }}</span>',
                showConfirmButton: false,
                timer: 2500,
                iconColor: '#064e3b',
                customClass: { popup: 'rounded-2xl border-2 border-slate-100 shadow-xl' }
            });
        @endif

        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: '<span class="text-sm font-black uppercase italic tracking-tighter text-rose-600">Gagal</span>',
                html: '<span class="text-[11px] font-bold uppercase tracking-tight">{{ session("error") }}</span>',
                confirmButtonColor: '#064e3b',
                customClass: { popup: 'rounded-2xl border-2 border-slate-100 shadow-xl' }
            });
        @endif
    </script>
</body>
</html>