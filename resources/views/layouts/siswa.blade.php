<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Siswa - SIAKAD SMANJA</title>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        [x-cloak] { display: none !important; }
        .sidebar-transition { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #064e3b; border-radius: 10px; }
        .page-enter { animation: slideUp 0.3s ease-out forwards; }
        @keyframes slideUp { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</head>
<body class="bg-[#F8FAFC] antialiased h-screen overflow-hidden text-slate-900" 
      x-data="{ sidebarOpen: window.innerWidth >= 1024 }"
      @resize.window="sidebarOpen = window.innerWidth >= 1024">

    <div class="flex h-screen w-full relative overflow-hidden">
        {{-- BACKDROP (Mobile) --}}
        <div x-show="sidebarOpen && window.innerWidth < 1024" @click="sidebarOpen = false" class="fixed inset-0 z-40 bg-black/50 lg:hidden" x-cloak></div>

        {{-- SIDEBAR --}}
        <aside class="bg-[#064e3b] text-white flex flex-col z-50 fixed inset-y-0 left-0 sidebar-transition lg:relative lg:translate-x-0 border-r border-white/10"
               :class="sidebarOpen ? 'translate-x-0 w-60' : '-translate-x-full lg:translate-x-0 lg:w-20'">
            
            <div class="p-5 text-center border-b border-white/10 flex-shrink-0 overflow-hidden">
                <div class="inline-flex p-2.5 rounded-xl bg-white shadow-lg mb-2 transform hover:rotate-3 transition-transform">
                    <img src="{{ asset('logo Smanja.png') }}" alt="Logo" class="w-10 h-10 mx-auto object-contain">
                </div>
                <div x-show="sidebarOpen || window.innerWidth >= 1024" x-transition.opacity>
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
                   class="flex items-center px-4 py-2.5 rounded-lg transition-all duration-200 group border border-transparent {{ request()->routeIs($menu['route']) ? 'bg-[#ffb800] font-black text-[#064e3b] shadow-md border-[#d99c00]' : 'text-green-100 hover:bg-white/5 hover:text-white' }}">
                    <i class="fa-solid {{ $menu['icon'] }} w-5 mr-3 text-center {{ request()->routeIs($menu['route']) ? '' : 'opacity-40 group-hover:opacity-100' }}"></i>
                    <span class="text-[10px] font-black uppercase tracking-widest" x-show="sidebarOpen || window.innerWidth >= 1024" x-transition.opacity>{{ $menu['label'] }}</span>
                </a>
                @endforeach
            </nav>

            <div class="p-4 border-t border-white/10 text-center flex-shrink-0">
                <p class="text-[8px] text-green-400/30 uppercase font-black tracking-widest italic" x-show="sidebarOpen || window.innerWidth >= 1024">&copy; 2026 SIAKAD SMAN 1 Jejangkit</p>
            </div>
        </aside>

        {{-- AREA KONTEN --}}
        <div class="flex-1 flex flex-col min-w-0 h-full relative bg-[#F8FAFC]">
            <header class="bg-white border-b border-gray-200 h-16 flex justify-between items-center px-4 sm:px-8 z-20 flex-shrink-0 shadow-sm gap-2">
                <div class="flex items-center gap-3 sm:gap-6 min-w-0">
                    <button @click="sidebarOpen = !sidebarOpen" class="text-slate-400 hover:text-[#064e3b] transition-colors p-1">
                        <i class="fa-solid fa-bars text-lg"></i>
                    </button>
                    <h1 class="text-sm sm:text-lg font-extrabold text-slate-800 uppercase truncate">Sistem Informasi <span class="text-[#064e3b] hidden md:inline">Akademik</span> SMANJA</h1>
                </div>

                <div class="flex items-center gap-3 sm:gap-6 flex-shrink-0">
                    <a href="{{ route('siswa.profil') }}" class="flex items-center gap-2 sm:gap-4 border-r pr-3 sm:pr-6 border-gray-200 group">
                        <div class="text-right hidden sm:block">
                            <p class="text-sm font-black text-slate-900 leading-none truncate max-w-[120px]">{{ auth()->user()->name }}</p>
                            <p class="text-[10px] text-emerald-600 font-bold mt-1 uppercase tracking-wider">SISWA • {{ auth()->user()->siswa->nisn ?? '-' }}</p>
                        </div>
                        <div class="h-9 w-9 rounded-full bg-emerald-100 border-2 border-emerald-200 flex items-center justify-center text-emerald-600 font-black text-sm group-hover:bg-emerald-600 group-hover:text-white transition-colors">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                    </a>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-rose-500 hover:text-rose-600 flex items-center gap-2 group">
                            <i class="fa-solid fa-right-from-bracket text-lg"></i>
                            <span class="text-xs font-black uppercase tracking-widest hidden md:inline">Logout</span>
                        </button>
                    </form>
                </div>
            </header>

            <main class="flex-1 overflow-y-auto p-4 sm:p-6 custom-scrollbar">
                <div class="max-w-6xl mx-auto page-enter pb-10">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    <script>
        @if(session('success')) Swal.fire({ icon: 'success', title: 'Berhasil', text: "{{ session('success') }}", timer: 2500, showConfirmButton: false, iconColor: '#064e3b' }); @endif
        @if(session('error')) Swal.fire({ icon: 'error', title: 'Gagal', text: "{{ session('error') }}", confirmButtonColor: '#064e3b' }); @endif
    </script>
</body>
</html>