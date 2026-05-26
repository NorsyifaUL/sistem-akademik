<x-guest-layout>
    {{-- Lapisan 1: Background Utama via CSS (Lebih Stabil) --}}
    <div class="min-h-screen w-full flex items-center justify-center relative font-sans antialiased py-6 px-4 bg-slate-900">
        
        {{-- Background Image dengan Style Inline agar Cover Sempurna --}}
        <div class="absolute inset-0 z-0" 
             style="background-image: url('{{ asset('bg_sekolah.jpeg') }}'); 
                    background-size: cover; 
                    background-position: center; 
                    background-repeat: no-repeat;
                    background-attachment: fixed;">
        </div>
        
        {{-- Lapisan 2: Overlay Gradasi Tipis (Dibuat lebih transparan agar gambar sekolah jelas) --}}
        <div class="absolute inset-0 bg-gradient-to-br from-blue-950/20 via-indigo-950/30 to-black/50 z-1010"></div>

        {{-- Lapisan 3: Kartu Login (Ukuran Compact & Pas untuk HP) --}}
        <div class="w-full max-w-[18.5rem] bg-white/95 backdrop-blur-sm p-5 rounded-[1.25rem] shadow-[0_20px_45px_rgba(0,0,0,0.45)] relative z-20 border border-white/20 animate-fade-in">
            
            {{-- Header: Logo & Judul --}}
            <div class="text-center mb-4">
                <div class="inline-flex p-1 rounded-full bg-blue-50/50 mb-1.5 shadow-inner ring-1 ring-white">
                    <img src="{{ asset('logo Smanja.png') }}" 
                         alt="Logo SMANJA" 
                         class="w-12 h-12 object-contain drop-shadow-md">
                </div>
                <h2 class="text-lg font-extrabold text-gray-900 tracking-tight uppercase leading-none mb-1">
                    SIAKAD
                </h2>
                <p class="text-[8px] text-blue-600 font-bold uppercase tracking-[0.2em] leading-none mb-1">
                    SMAN 1 JEJANGKIT
                </p>
                <p class="text-gray-400 text-[8px] italic leading-tight">Sistem Informasi Akademik</p>
            </div>

            <x-auth-session-status class="text-[9px] text-center mb-2.5 text-blue-600 font-semibold" :status="session('status')" />

            <form method="POST" action="{{ route('login') }}" class="space-y-3">
                @csrf

                <div class="space-y-2.5">
                    {{-- Input Email --}}
                    <div>
                        <label for="email" class="block text-[8px] font-bold text-gray-500 uppercase ml-1 mb-0.5 tracking-wider">Email</label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none text-gray-400 group-focus-within:text-blue-500 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                                    <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                                </svg>
                            </div>
                            <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus 
                                class="appearance-none block w-full pl-8 pr-3 py-1.5 border border-gray-200 rounded-lg text-[10px] placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-transparent transition duration-200 shadow-sm leading-none bg-white text-gray-800 font-medium" 
                                placeholder="Email user">
                        </div>
                    </div>

                    {{-- Input Password --}}
                    <div>
                        <label for="password" class="block text-[8px] font-bold text-gray-500 uppercase ml-1 mb-0.5 tracking-wider">Password</label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none text-gray-400 group-focus-within:text-blue-500 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <input id="password" name="password" type="password" required autocomplete="current-password"
                                class="appearance-none block w-full pl-8 pr-8 py-1.5 border border-gray-200 rounded-lg text-[10px] placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-transparent transition duration-200 shadow-sm leading-none bg-white text-gray-800 font-medium" 
                                placeholder="••••••••">
                            
                            {{-- Tombol Lihat/Sembunyikan Sandi --}}
                            <button type="button" onclick="togglePasswordVisibility()" class="absolute inset-y-0 right-0 pr-2.5 flex items-center text-gray-400 hover:text-blue-600 focus:outline-none">
                                <svg id="eye-open" xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <svg id="eye-closed" xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Ingat Saya --}}
                <div class="flex items-center pt-0.5">
                    <input id="remember_me" name="remember" type="checkbox" class="h-2.5 w-2.5 text-blue-600 focus:ring-0 border-gray-300 rounded bg-white cursor-pointer">
                    <label for="remember_me" class="ml-1.5 block text-[9px] text-gray-500 font-semibold select-none cursor-pointer">Ingat saya</label>
                </div>

                {{-- Tombol Login --}}
                <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent text-[10px] font-extrabold rounded-md text-white bg-blue-600 hover:bg-blue-700 shadow-md shadow-blue-500/15 active:scale-[0.97] transition-all duration-200 uppercase tracking-[0.12em]">
                    Login
                </button>
            </form>

            {{-- Footer Kartu --}}
            <div class="mt-5 text-center pt-2 border-t border-gray-100/70">
                <p class="text-[7.5px] text-gray-400 uppercase font-bold tracking-widest">SIAKAD SMANJA &copy; 2026</p>
            </div>
        </div>
    </div>

    {{-- SweetAlert2 Notifikasi Error Bawaan Laravel --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function togglePasswordVisibility() {
            const passwordField = document.getElementById('password');
            const eyeOpen = document.getElementById('eye-open');
            const eyeClosed = document.getElementById('eye-closed');
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                eyeOpen.classList.add('hidden');
                eyeClosed.classList.remove('hidden');
            } else {
                passwordField.type = 'password';
                eyeOpen.classList.remove('hidden');
                eyeClosed.classList.add('hidden');
            }
        }

        @if($errors->any())
            Swal.fire({
                icon: 'error',
                title: '<span class="text-xs font-black uppercase tracking-tighter text-rose-600">Gagal Masuk</span>',
                html: '<span class="text-[10px] font-bold uppercase tracking-tight text-gray-600">{{ $errors->first() }}</span>',
                confirmButtonColor: '#2563eb',
                customClass: { popup: 'rounded-xl border border-slate-100 shadow-xl' }
            });
        @endif
    </script>
</x-guest-layout>