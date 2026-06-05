<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - SIAKAD SMANJA</title>
    
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body { font-family: 'Inter', sans-serif; }
        [x-cloak] { display: none !important; }
        .sidebar-transition { transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
        .content-scrollbar::-webkit-scrollbar { width: 6px; }
        .content-scrollbar::-webkit-scrollbar-track { background: #f1f5f9; }
        .content-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.2); border-radius: 10px; }
    </style>
</head>

<body class="bg-gray-100 antialiased h-screen overflow-hidden" 
      x-data="{ sidebarOpen: window.innerWidth >= 1024 }"
      @resize.window="sidebarOpen = window.innerWidth >= 1024">

<div class="flex h-screen w-full relative overflow-hidden">
    
    {{-- BACKDROP (Untuk mobile) --}}
    <div x-show="sidebarOpen && window.innerWidth < 1024" 
         @click="sidebarOpen = false"
         class="fixed inset-0 z-40 bg-black/50 lg:hidden"
         x-cloak>
    </div>

    {{-- SIDEBAR --}}
    <aside class="bg-blue-900 text-white flex flex-col shadow-2xl z-50 fixed inset-y-0 left-0 sidebar-transition lg:relative lg:translate-x-0" 
            :class="sidebarOpen ? 'translate-x-0 w-64' : '-translate-x-full lg:translate-x-0 lg:w-20'">
        
        <div class="p-6 text-center border-b border-blue-800/50 flex-shrink-0 overflow-hidden">
            <div class="inline-flex p-2 rounded-xl bg-white/10 mb-3 shadow-inner ring-1 ring-white/20">
                <img src="{{ asset('logo Smanja.png') }}" alt="Logo Sekolah" class="w-14 h-14 mx-auto object-contain">
            </div>
            <div x-show="sidebarOpen || window.innerWidth >= 1024" x-transition.opacity>
                <h1 class="text-base font-extrabold tracking-wider leading-tight uppercase whitespace-nowrap">SIAKAD</h1>
                <p class="text-[10px] text-blue-300 font-medium uppercase tracking-widest mt-1 whitespace-nowrap">SMAN 1 JEJANGKIT</p>
            </div>
        </div>

        <nav class="flex-1 p-4 space-y-1.5 overflow-y-auto custom-scrollbar overflow-x-hidden">
            @php $menus = [
                ['route' => 'admin.dashboard', 'icon' => 'fa-house', 'label' => 'Dashboard'],
                ['route' => 'admin.guru.index', 'icon' => 'fa-chalkboard-user', 'label' => 'Data Guru'],
                ['route' => 'admin.kelas.index', 'icon' => 'fa-school', 'label' => 'Data Kelas'],
                ['route' => 'admin.siswa.index', 'icon' => 'fa-user-graduate', 'label' => 'Data Siswa'],
                ['route' => 'admin.mapel.index', 'icon' => 'fa-book', 'label' => 'Mata Pelajaran'],
                ['route' => 'admin.jadwal.index', 'icon' => 'fa-calendar-days', 'label' => 'Jadwal Pelajaran'],
                ['route' => 'admin.nilai.index', 'icon' => 'fa-file-signature', 'label' => 'Nilai Siswa'],
                ['route' => 'admin.absensi.index', 'icon' => 'fa-clipboard-check', 'label' => 'Absensi'],
                ['route' => 'admin.notifikasi.index', 'icon' => 'fa-bell', 'label' => 'Notifikasi'],
                ['route' => 'admin.settings.index', 'icon' => 'fa-gears', 'label' => 'Pengaturan'],
            ]; @endphp

            @foreach($menus as $menu)
                {{-- Logika Aktif ditambahkan di sini --}}
                @php $isActive = request()->routeIs($menu['route'] . '*'); @endphp
                
                <a href="{{ route($menu['route']) }}" class="flex items-center px-4 py-2.5 rounded-xl transition-all duration-200 group whitespace-nowrap {{ $isActive ? 'bg-blue-600 font-bold shadow-lg text-white' : 'text-blue-100 hover:bg-blue-800 hover:text-white' }}">
                    <i class="fa-solid {{ $menu['icon'] }} w-6 text-center {{ $isActive ? 'text-white' : 'text-blue-300 group-hover:text-white' }}"></i>
                    <span class="ml-3 text-sm" x-show="sidebarOpen || window.innerWidth >= 1024">{{ $menu['label'] }}</span>
                </a>
            @endforeach
        </nav>

        {{-- FOOTER SIDEBAR --}}
        <div class="p-4 border-t border-blue-800/50 flex-shrink-0">
            <p class="text-[9px] text-center text-blue-400 uppercase font-bold tracking-tighter">
                <span x-show="sidebarOpen || window.innerWidth >= 1024">&copy; 2026 SIAKAD SMANJA</span>
                <span x-show="!sidebarOpen && window.innerWidth < 1024" x-cloak>2026</span>
            </p>
        </div>
    </aside>

    {{-- KONTEN UTAMA --}}
    <div class="flex-1 flex flex-col min-w-0 h-screen overflow-hidden bg-gray-50">
        <header class="bg-white border-b border-gray-200 h-16 flex justify-between items-center px-4 sm:px-8 shadow-sm flex-shrink-0">
            <div class="flex items-center gap-2 sm:gap-4">
                <button @click="sidebarOpen = !sidebarOpen" class="text-gray-400 hover:text-blue-600 transition-colors p-1">
                    <i class="fa-solid fa-bars-staggered text-xl"></i>
                </button>
                <h1 class="text-sm sm:text-lg font-bold text-gray-800 tracking-tight uppercase truncate">
                    Sistem Informasi <span class="text-blue-600 font-extrabold hidden md:inline">Akademik</span> SMANJA
                </h1>
            </div>

            <div class="flex items-center gap-3 sm:gap-6 flex-shrink-0">
                <a href="{{ route('admin.profil') }}" class="flex items-center gap-2 border-r pr-3 sm:pr-6 border-gray-100 text-right group hover:opacity-80 transition-all min-w-0">
                    <div class="block text-right">
                        <p class="text-[10px] sm:text-xs font-bold text-gray-900 leading-none truncate max-w-[80px] sm:max-w-[120px]">{{ auth()->user()->name }}</p>
                        <p class="text-[9px] text-blue-600 font-semibold mt-1 uppercase">Administrator</p>
                    </div>
                    <div class="h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-700 font-bold border border-blue-200 text-xs">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                </a>
                
                {{-- TOMBOL LOGOUT --}}
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-red-500 hover:text-red-700 text-xs font-bold uppercase tracking-wider flex items-center gap-2">
                        <i class="fa-solid fa-right-from-bracket"></i> Logout
                    </button>
                </form>
            </div>
        </header>

        <main class="flex-1 overflow-y-auto p-4 sm:p-8 content-scrollbar">
            <div class="max-w-7xl mx-auto w-full">
                @yield('content')
            </div>
        </main>
    </div>
</div>

<script>
    @if(session('success')) Swal.fire({ icon: 'success', title: 'BERHASIL!', text: "{{ session('success') }}", showConfirmButton: false, timer: 3000 }); @endif
    @if(session('error')) Swal.fire({ icon: 'error', title: 'GAGAL!', text: "{{ session('error') }}", confirmButtonColor: '#1d4ed8' }); @endif
</script>
</body>
</html>