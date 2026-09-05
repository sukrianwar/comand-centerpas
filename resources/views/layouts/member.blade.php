<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#1a1a2e">
    <title>{{ config('app.name', 'Sentinel Guard Systems') }} — Member Area</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Inter', sans-serif; margin: 0; padding: 0; }
        h1, h2, h3, h4, h5, h6, .font-headline-lg, .font-display, .font-headline-md { font-family: 'Hanken Grotesk', sans-serif; }
        .font-label-md { font-family: 'JetBrains Mono', monospace; }
        
        .material-symbols-outlined {
            font-family: 'Material Symbols Outlined';
            font-weight: normal;
            font-style: normal;
            font-size: 24px;
            line-height: 1;
            letter-spacing: normal;
            text-transform: none;
            display: inline-block;
            white-space: nowrap;
            word-wrap: normal;
            direction: ltr;
            -webkit-font-feature-settings: 'liga';
            -webkit-font-smoothing: antialiased;
        }
        
        /* Custom scrollbar for an elegant look */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: rgba(150, 150, 150, 0.3); border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: rgba(150, 150, 150, 0.5); }

        /* 1. Base Structure - Mobile First (< 1024px) */
        .app-container {
            display: flex;
            width: 100vw;
            height: 100vh;
            overflow: hidden;
            position: relative;
        }

        /* Sidebar: Mobile behaves as Off-Canvas (Fixed) */
        .app-sidebar {
            position: fixed;
            top: 0; left: 0; bottom: 0;
            width: 280px; /* Lebar tetap permanen */
            z-index: 50;
            flex-shrink: 0;
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            
            /* Default state: hidden off-canvas, murni CSS */
            transform: translateX(-100%);
        }
        
        /* State saat sidebar terbuka di mobile/tablet */
        .app-sidebar.sidebar-open {
            transform: translateX(0);
        }

        /* Main Content: Takes full width on mobile */
        .app-main {
            flex: 1 1 0%;
            min-width: 0; /* Cegah overflow konten / blowout */
            height: 100%;
            display: flex;
            flex-direction: column;
            position: relative;
            z-index: 10;
        }

        /* 2. Desktop Structure (>= 1024px) */
        @media (min-width: 1024px) {
            /* Sidebar dicabut dari status fixed, berubah jadi elemen Flex sejajar */
            .app-sidebar {
                position: relative !important;
                transform: translateX(0) !important; /* Selalu tampil/permanen */
                box-shadow: none !important; /* Hapus bayangan overlay di desktop */
            }
            
            /* Elemen overlay & hamburger dihilangkan di desktop */
            .mobile-overlay, .mobile-toggle {
                display: none !important;
            }
        }
    </style>
</head>
<body class="bg-background text-on-background antialiased overflow-hidden" x-data="{ sidebarOpen: false }">

    <!-- Container Utama Aplikasi (Flexbox Induk) -->
    <div class="app-container">

        <!-- Mobile Sidebar Overlay -->
        <div x-show="sidebarOpen" 
             x-transition:enter="transition-opacity ease-linear duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-black/60 z-40 mobile-overlay backdrop-blur-sm"
             @click="sidebarOpen = false" x-cloak></div>

        <!-- 1. Elemen Kiri: Sidebar -->
        <aside class="app-sidebar bg-surface border-r border-outline-variant flex flex-col shadow-2xl"
               :class="{ 'sidebar-open': sidebarOpen }" x-cloak>
            
            <!-- Sidebar Header -->
            <div class="flex items-center justify-between px-lg py-md border-b border-outline-variant">
                <div>
                    <h1 class="text-title-lg font-bold text-primary tracking-tight">Sentinel Guard</h1>
                    <p class="text-label-md text-secondary mt-1">Member Area</p>
                </div>
                <!-- Close button on mobile -->
                <button @click="sidebarOpen = false" class="mobile-toggle p-xs text-on-surface-variant hover:bg-surface-container rounded-full">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <!-- Sidebar Navigation -->
            <nav class="flex-1 overflow-y-auto py-md px-sm space-y-sm">
                <a href="{{ route('member.dashboard') }}" class="flex items-center gap-md px-md py-sm rounded-xl transition-all {{ request()->routeIs('member.dashboard') ? 'bg-primary text-on-primary font-bold shadow-md shadow-primary/20' : 'text-on-surface-variant hover:bg-surface-container hover:text-on-surface' }}">
                    <span class="material-symbols-outlined text-[26px]" @if(request()->routeIs('member.dashboard')) style="font-variation-settings: 'FILL' 1;" @endif>home</span>
                    <span class="text-body-lg">Beranda</span>
                </a>
                
                <a href="{{ route('member.patrol') }}" class="flex items-center gap-md px-md py-sm rounded-xl transition-all {{ request()->routeIs('member.patrol') ? 'bg-primary text-on-primary font-bold shadow-md shadow-primary/20' : 'text-on-surface-variant hover:bg-surface-container hover:text-on-surface' }}">
                    <span class="material-symbols-outlined text-[26px]" @if(request()->routeIs('member.patrol')) style="font-variation-settings: 'FILL' 1;" @endif>explore</span>
                    <span class="text-body-lg">Patroli</span>
                </a>
                
                <a href="{{ route('member.riwayat') }}" class="flex items-center gap-md px-md py-sm rounded-xl transition-all {{ request()->routeIs('member.riwayat') ? 'bg-primary text-on-primary font-bold shadow-md shadow-primary/20' : 'text-on-surface-variant hover:bg-surface-container hover:text-on-surface' }}">
                    <span class="material-symbols-outlined text-[26px]" @if(request()->routeIs('member.riwayat')) style="font-variation-settings: 'FILL' 1;" @endif>history</span>
                    <span class="text-body-lg">Riwayat</span>
                </a>
                
                <a href="{{ route('member.profile') }}" class="flex items-center gap-md px-md py-sm rounded-xl transition-all {{ request()->routeIs('member.profile') ? 'bg-primary text-on-primary font-bold shadow-md shadow-primary/20' : 'text-on-surface-variant hover:bg-surface-container hover:text-on-surface' }}">
                    <span class="material-symbols-outlined text-[26px]" @if(request()->routeIs('member.profile')) style="font-variation-settings: 'FILL' 1;" @endif>person</span>
                    <span class="text-body-lg">Profil</span>
                </a>
            </nav>

            <!-- Sidebar Footer (Logout) -->
            <div class="p-md border-t border-outline-variant">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-md px-md py-sm rounded-xl text-red-600 hover:bg-red-50 hover:text-red-700 transition-colors font-medium">
                        <span class="material-symbols-outlined text-[26px]">logout</span>
                        <span class="text-body-lg">Logout</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- 2. Elemen Kanan: Main Content Wrapper -->
        <div class="app-main bg-background">
            
            <!-- Top Header Bar -->
            <header class="sticky top-0 z-30 bg-surface/90 backdrop-blur-md border-b border-outline-variant shadow-sm h-16 flex items-center justify-between px-md lg:px-lg flex-shrink-0">
                <div class="flex items-center gap-sm">
                    <!-- Hamburger Menu Button (Mobile) -->
                    <button @click="sidebarOpen = true" class="mobile-toggle p-xs text-on-surface-variant hover:bg-surface-container rounded-full transition-colors mr-2">
                        <span class="material-symbols-outlined text-[28px]">menu</span>
                    </button>
                    <!-- Desktop Title -->
                    <h2 class="text-title-lg font-bold text-on-surface hidden lg:block">
                        @if(request()->routeIs('member.dashboard')) Beranda
                        @elseif(request()->routeIs('member.patrol')) Patroli
                        @elseif(request()->routeIs('member.riwayat')) Riwayat
                        @elseif(request()->routeIs('member.profile')) Profil
                        @else Member Area
                        @endif
                    </h2>
                    <!-- Mobile Title -->
                    <h2 class="text-title-md font-bold text-primary lg:hidden">
                        Sentinel Guard
                    </h2>
                </div>
                
                <div class="flex items-center gap-md">
                    <!-- Online/Offline Indicator -->
                    <div id="connStatus" class="flex items-center gap-xs px-3 py-1.5 bg-emerald-50 text-emerald-700 text-label-md font-bold rounded-full border border-emerald-200 shadow-sm">
                        <span class="w-2.5 h-2.5 bg-emerald-500 rounded-full animate-pulse shadow-[0_0_8px_rgba(16,185,129,0.8)]"></span>
                        <span class="hidden sm:inline">Online</span>
                    </div>
                    
                    <button class="p-sm text-on-surface-variant hover:bg-surface-container hover:text-primary rounded-full transition-all relative">
                        <span class="material-symbols-outlined text-[26px]">notifications</span>
                        <span class="absolute top-1.5 right-1.5 w-2.5 h-2.5 bg-red-500 rounded-full border-2 border-surface"></span>
                    </button>
                    
                    <a href="{{ route('member.profile') }}" class="w-10 h-10 rounded-full bg-primary-container border-2 border-primary flex items-center justify-center text-on-primary-container font-bold text-title-md cursor-pointer hover:shadow-lg transition-all">
                        AR
                    </a>
                </div>
            </header>

            <!-- Main Scrollable Content Area -->
            <main class="flex-1 overflow-y-auto bg-surface-container-lowest">
                <div class="w-full p-md lg:p-lg lg:py-xl">

                    {{-- ── Flash: Error (mis. redirect dari middleware restrict.guard) ──────── --}}
                    @if(session('error'))
                    <div class="mb-md flex items-center gap-sm px-md py-sm bg-red-50 border border-red-200 rounded-xl text-red-800 text-body-sm font-medium shadow-sm">
                        <span class="material-symbols-outlined text-red-500 text-[20px] flex-shrink-0">block</span>
                        {{ session('error') }}
                    </div>
                    @endif

                    {{-- ── Flash: Success ──────────────────────────────────────────────────── --}}
                    @if(session('success'))
                    <div class="mb-md flex items-center gap-sm px-md py-sm bg-emerald-50 border border-emerald-200 rounded-xl text-emerald-800 text-body-sm font-medium shadow-sm">
                        <span class="material-symbols-outlined text-emerald-500 text-[20px] flex-shrink-0">check_circle</span>
                        {{ session('success') }}
                    </div>
                    @endif

    {{ $slot }}
</div>
            </main>
            
        </div>
        
    </div>

</body>
</html>
