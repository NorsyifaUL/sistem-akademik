<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            <!-- Navbar -->
            <nav class="bg-blue-600 text-white shadow">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16">
                        <div class="flex">
                            <div class="flex-shrink-0 flex items-center">
                                <a href="{{ url('/') }}" class="font-bold text-lg">SIA</a>
                            </div>
                            <div class="hidden sm:-my-px sm:ml-6 sm:flex sm:space-x-8">
                                @auth
                                    @if(auth()->user()->role == 'admin')
                                        <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center px-1 pt-1 text-sm font-medium">Dashboard</a>
                                        <a href="{{ route('absensi.index') }}" class="inline-flex items-center px-1 pt-1 text-sm font-medium">Absensi</a>
                                        <a href="{{ route('notifikasi.index') }}" class="inline-flex items-center px-1 pt-1 text-sm font-medium">Notifikasi</a>
                                    @elseif(auth()->user()->role == 'guru')
                                        <a href="{{ route('guru.dashboard') }}" class="inline-flex items-center px-1 pt-1 text-sm font-medium">Dashboard</a>
                                        <a href="{{ route('guru.absensi.index') }}" class="inline-flex items-center px-1 pt-1 text-sm font-medium">Absensi</a>
                                        <a href="{{ route('notifikasi.index') }}" class="inline-flex items-center px-1 pt-1 text-sm font-medium">Notifikasi</a>
                                    @elseif(auth()->user()->role == 'siswa')
                                        <a href="{{ route('siswa.dashboard') }}" class="inline-flex items-center px-1 pt-1 text-sm font-medium">Dashboard</a>
                                    @endif
                                @endauth
                            </div>
                        </div>

                        <div class="flex items-center">
                            @auth
                                <a href="{{ route('profile.edit') }}" class="mr-4 text-sm">Profile</a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="text-sm">Logout</button>
                                </form>
                            @else
                                <a href="{{ route('login') }}" class="text-sm">Login</a>
                            @endauth
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Page Heading -->
            @if (isset($header))
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <!-- Page Content -->
            <main class="p-6">
            @yield('content')
            </main>
        </div>
    </body>
</html>