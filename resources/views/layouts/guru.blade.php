<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Guru - SIAKAD SMANJA</title>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>
        body { font-family: 'Inter', sans-serif; }
        [x-cloak] { display: none !important; }
        .sidebar-transition { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
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

@php
    $guruId = auth()->user()->guru->id ?? null;
    $mapelUtama = $guruId ? \App\Models\Jadwal::where('guru_id', $guruId)->with('mapel')->first() : null;
    $namaMapel = $mapelUtama->mapel->nama_mapel ?? 'Guru Aktif';
    $jadwalPertama = $guruId ? \App\Models\Jadwal::where('guru_id', $guruId)->first() : null;
@endphp

<div class="flex h-screen w-full relative overflow-hidden">
    <div x-show="sidebarOpen && window.innerWidth < 1024" @click="sidebarOpen = false" class="fixed inset-0 z-40 bg-black/50 lg:hidden" x-cloak></div>

    <aside class="bg-green-900 text-white flex flex-col shadow-2xl z-50 fixed inset-y-0 left-0 sidebar-transition lg:relative lg:translate-x-0" 
           :class="sidebarOpen ? 'translate-x-0 w-64' : '-translate-x-full lg:translate-x-0 lg:w-20'">
        
        <div class="p-6 text-center border-b border-green-800/50 flex-shrink-0 overflow-hidden">
            <div class="inline-flex p-2 rounded-xl bg-white/10 mb-3 shadow-inner ring-1 ring-white/20">
                <img src="{{ asset('logo Smanja.png') }}" alt="Logo" class="w-14 h-14 mx-auto object-contain">
            </div>
            <div x-show="sidebarOpen || window.innerWidth >= 1024" x-transition.opacity>
                <h1 class="text-base font-extrabold tracking-wider uppercase whitespace-nowrap">SIAKAD</h1>
                <p class="text-[10px] text-green-300 font-medium uppercase tracking-widest mt-1 whitespace-nowrap">SMAN 1 JEJANGKIT</p>
            </div>
        </div>

        <nav class="flex-1 p-4 space-y-1.5 overflow-y-auto custom-scrollbar overflow-x-hidden">
            @php $menus = [
                ['route' => 'guru.dashboard', 'icon' => 'fa-house-chimney', 'label' => 'Dashboard'],
                ['route' => 'guru.jadwal', 'icon' => 'fa-calendar-days', 'label' => 'Jadwal Mengajar'],
                ['route' => 'guru.absensi.index', 'icon' => 'fa-user-check', 'label' => 'Absensi Siswa'],
                ['route' => 'guru.absensi.rekap', 'icon' => 'fa-file-lines', 'label' => 'Rekap Absensi'],
            ]; @endphp

            @foreach($menus as $menu)
            <a href="{{ route($menu['route']) }}" class="flex items-center px-4 py-2.5 rounded-xl transition-all duration-200 group {{ request()->routeIs($menu['route']) ? 'bg-green-700 font-bold shadow-lg text-white' : 'text-green-100 hover:bg-green-800 hover:text-white' }}">
                <i class="fa-solid {{ $menu['icon'] }} w-6 text-center"></i>
                <span class="ml-3 text-sm" x-show="sidebarOpen || window.innerWidth >= 1024">{{ $menu['label'] }}</span>
            </a>
            @endforeach

            <hr class="border-green-800/50 my-2">

            <div x-data="{ open: {{ (request()->is('guru/nilai*') || request()->routeIs('guru.lihat_nilai') || request()->routeIs('guru.jadwal.legger')) ? 'true' : 'false' }} }">
                <button @click="sidebarOpen ? open = !open : (sidebarOpen = true, open = true)" 
                    class="w-full flex items-center px-4 py-2.5 rounded-xl transition-all duration-200 group {{ (request()->is('guru/nilai*') || request()->routeIs('guru.lihat_nilai') || request()->routeIs('guru.jadwal.legger')) ? 'bg-green-700 text-white shadow-lg' : 'text-green-100 hover:bg-green-800 hover:text-white' }}">
                    <i class="fa-solid fa-file-signature w-6 text-center"></i>
                    <span class="ml-3 text-sm" x-show="sidebarOpen || window.innerWidth >= 1024">Laporan Nilai</span>
                    <i class="fa-solid fa-chevron-down ml-auto text-[10px]" x-show="sidebarOpen || window.innerWidth >= 1024" :class="open ? 'rotate-180' : ''"></i>
                </button>
                <div x-show="(open && (sidebarOpen || window.innerWidth >= 1024))" x-cloak x-collapse class="mt-1 ml-4 pl-4 border-l border-green-700 space-y-1">
                    <a href="{{ route('guru.lihat_nilai') }}" class="block px-4 py-2 text-xs rounded-lg {{ request()->routeIs('guru.lihat_nilai') ? 'bg-green-600 text-white' : 'text-green-200 hover:text-white' }}">Input Nilai</a>
                    <a href="{{ $jadwalPertama ? route('guru.jadwal.legger', $jadwalPertama->id) : route('guru.jadwal') }}" class="block px-4 py-2 text-xs rounded-lg {{ request()->routeIs('guru.jadwal.legger') ? 'bg-green-600 text-white' : 'text-green-200 hover:text-white' }}">Rekap Nilai</a>
                    @if(!empty(Auth::user()->wali_kelas))
                        <a href="{{ route('guru.raport.index') }}" class="block px-4 py-2 text-xs rounded-lg {{ request()->routeIs('guru.raport.*') ? 'bg-green-600 text-white' : 'text-green-200 hover:text-white' }}">Raport</a>
                    @endif
                </div>
            </div>
        </nav>

        <div class="p-4 border-t border-green-800/50">
            <p class="text-[9px] text-center text-green-400 font-bold uppercase" x-show="sidebarOpen || window.innerWidth >= 1024">&copy; 2026 SIAKAD SMANJA</p>
        </div>
    </aside>

    <div class="flex-1 flex flex-col min-w-0 h-screen overflow-hidden bg-gray-50">
        <header class="bg-white border-b border-gray-200 h-16 flex justify-between items-center px-4 sm:px-8 shadow-sm flex-shrink-0">
            <div class="flex items-center gap-4">
                <button @click="sidebarOpen = !sidebarOpen" class="text-gray-500 hover:text-green-700 transition-colors p-1"><i class="fa-solid fa-bars-staggered text-xl"></i></button>
                <h1 class="text-sm sm:text-lg font-bold text-gray-800 truncate">Sistem Informasi <span class="text-green-700 hidden md:inline">Akademik</span> SMANJA</h1>
            </div>

            <div class="flex items-center gap-4">
                {{-- LINK PROFIL GURU --}}
                <a href="{{ route('guru.profil') }}" class="flex items-center gap-3 border-r pr-4 sm:pr-6 border-gray-200 hover:opacity-80 transition-all">
                    <div class="text-right hidden sm:block">
                        <p class="text-xs font-bold text-gray-900 leading-tight">{{ auth()->user()->name }}</p>
                        <p class="text-[10px] text-green-600 italic uppercase">Guru {{ $namaMapel }}</p>
                    </div>
                    <div class="h-9 w-9 rounded-full bg-green-100 flex items-center justify-center text-green-700 font-bold border border-green-200 text-sm">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                </a>
                
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-red-500 hover:text-red-700 font-bold text-xs uppercase flex items-center gap-2">
                        <i class="fa-solid fa-right-from-bracket"></i> <span class="hidden md:inline">Logout</span>
                    </button>
                </form>
            </div>
        </header>

        <main class="flex-1 overflow-y-auto p-4 sm:p-8 content-scrollbar">
            <div class="max-w-7xl mx-auto w-full">@yield('content')</div>
        </main>
    </div>
</div>

<script>
    @if(session('success')) Swal.fire({ icon: 'success', title: 'BERHASIL!', text: "{{ session('success') }}", timer: 3000, iconColor: '#15803d' }); @endif
    @if(session('error')) Swal.fire({ icon: 'error', title: 'GAGAL!', text: "{{ session('error') }}", confirmButtonColor: '#15803d' }); @endif
</script>
</body>
</html>