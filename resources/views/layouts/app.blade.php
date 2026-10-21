<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Sentinel Guard Systems') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Inter', sans-serif; margin: 0; padding: 0; }
        h1, h2, h3, h4, h5, h6, .font-headline-lg, .font-display, .font-headline-md, .font-headline-lg-mobile { font-family: 'Hanken Grotesk', sans-serif; }

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

        /* Custom scrollbar */
        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: rgba(150,150,150,0.25); border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: rgba(150,150,150,0.45); }

        /* Sidebar gradient brand header */
        .sidebar-brand {
            background: linear-gradient(135deg, #1e3a5f 0%, #1a2e4a 60%, #0f1e30 100%);
        }

        /* Nav item active glow */
        .nav-active {
            background: linear-gradient(90deg, rgba(var(--color-primary-rgb, 59,130,246), 0.18) 0%, rgba(var(--color-primary-rgb, 59,130,246), 0.06) 100%);
            border-left: 3px solid currentColor;
        }

        /* Sidebar pill active */
        .sidebar-item-active {
            position: relative;
        }
        .sidebar-item-active::before {
            content: '';
            position: absolute;
            left: 0; top: 0; bottom: 0;
            width: 3px;
            border-radius: 0 3px 3px 0;
        }

        /* Smooth accordion */
        .sub-menu {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .sub-menu.open {
            max-height: 300px;
        }
    </style>
</head>
<body class="bg-background text-on-background flex h-screen overflow-hidden">

    <!-- ===== SIDEBAR ===== -->
    <aside class="hidden md:flex flex-col h-screen w-64 fixed left-0 top-0 z-40 bg-surface/95 backdrop-blur-md border-r border-outline-variant/70 shadow-soft overflow-hidden">

        <!-- Brand Header -->
        <div class="sidebar-brand px-md py-lg flex items-center gap-sm flex-shrink-0">
            <div class="w-9 h-9 rounded-xl bg-white/15 flex items-center justify-center flex-shrink-0 border border-white/20">
                <span class="material-symbols-outlined text-white text-[22px]" style="font-variation-settings: 'FILL' 1;">shield</span>
            </div>
            <div class="min-w-0">
                <h1 class="text-[15px] font-bold text-white leading-tight tracking-tight truncate">Command Center</h1>
                <p class="text-[11px] text-white/60 font-medium tracking-widest uppercase mt-0.5">Sentinel Guard</p>
            </div>
        </div>

        <!-- User Info Strip -->
        <div class="px-md py-sm border-b border-outline-variant flex items-center gap-sm bg-surface-container flex-shrink-0">
            <div class="w-8 h-8 rounded-full bg-primary-container border border-primary/30 flex items-center justify-center flex-shrink-0">
                <span class="text-xs font-bold text-on-primary-container">
                    @if(Auth::check()) {{ strtoupper(substr(Auth::user()->name, 0, 2)) }} @else SG @endif
                </span>
            </div>
            <div class="min-w-0 flex-1">
                <p class="text-body-sm font-semibold text-on-surface truncate leading-tight">
                    @if(Auth::check()) {{ Auth::user()->name }} @else Admin @endif
                </p>
                <p class="text-[11px] text-secondary leading-tight">
                    {{ Auth::check() ? (Auth::user()->role?->role_name ?? 'User') : 'Supervisor' }}
                </p>
            </div>
            <div class="w-2 h-2 rounded-full bg-emerald-500 flex-shrink-0" title="Online"></div>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 overflow-y-auto py-sm px-xs space-y-xs" id="sidebar-nav">

            <!-- Section Label -->
            <p class="text-[10px] font-bold text-secondary/60 uppercase tracking-[0.12em] px-sm pt-xs pb-xs">Menu Utama</p>

            <!-- Dashboard -->
            <a href="{{ route('dashboard') }}"
               class="group flex items-center gap-sm px-sm py-[9px] rounded-xl transition-all duration-200
               {{ request()->routeIs('dashboard')
                   ? 'bg-primary text-on-primary font-bold shadow-sm shadow-primary/20'
                   : 'text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface' }}">
                <span class="material-symbols-outlined text-[20px] flex-shrink-0"
                    @if(request()->routeIs('dashboard')) style="font-variation-settings: 'FILL' 1;" @endif>
                    dashboard
                </span>
                <span class="text-[13px] font-medium leading-none">Dashboard</span>
                @if(request()->routeIs('dashboard'))
                    <span class="ml-auto w-1.5 h-1.5 rounded-full bg-on-primary/60"></span>
                @endif
            </a>

            <!-- Laporan Insiden -->
            <a href="{{ route('incidents.index') }}"
               class="group flex items-center gap-sm px-sm py-[9px] rounded-xl transition-all duration-200
               {{ request()->routeIs('incidents.*')
                   ? 'bg-primary text-on-primary font-bold shadow-sm shadow-primary/20'
                   : 'text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface' }}">
                <span class="material-symbols-outlined text-[20px] flex-shrink-0"
                    @if(request()->routeIs('incidents.*')) style="font-variation-settings: 'FILL' 1;" @endif>
                    report_problem
                </span>
                <span class="text-[13px] font-medium leading-none">Laporan Insiden</span>
                <!-- Badge notif contoh -->
                @if(!request()->routeIs('incidents.*'))
                <span class="ml-auto px-1.5 py-0.5 text-[10px] font-bold bg-red-100 text-red-600 rounded-full leading-none">1</span>
                @endif
            </a>

            <!-- Section Label -->
            <p class="text-[10px] font-bold text-secondary/60 uppercase tracking-[0.12em] px-sm pt-sm pb-xs">Guard Management</p>

            <!-- Guard Management Accordion -->
            <div x-data="{ open: {{ request()->routeIs('patrol-schedules.*') || request()->routeIs('areas.*') || request()->routeIs('guards.*') || request()->routeIs('shifts.*') ? 'true' : 'false' }} }">
                <button @click="open = !open"
                    class="w-full flex items-center gap-sm px-sm py-[9px] rounded-xl text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface transition-all duration-200"
                    :class="open ? 'bg-surface-container text-on-surface font-medium' : ''">
                    <span class="material-symbols-outlined text-[20px] flex-shrink-0" :style="open ? 'font-variation-settings: \'FILL\' 1;' : ''">group</span>
                    <span class="text-[13px] font-medium leading-none flex-1 text-left">Guard Management</span>
                    <span class="material-symbols-outlined text-[16px] transition-transform duration-200" :class="open ? 'rotate-180' : ''">expand_more</span>
                </button>

                <!-- Sub-items -->
                <div x-show="open"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 -translate-y-1"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 -translate-y-1"
                     class="mt-xs pl-[30px] space-y-[2px]">

                    <a href="{{ route('patrol-schedules.index') }}"
                       class="flex items-center gap-sm px-sm py-[7px] rounded-lg text-[12px] transition-all duration-150
                       {{ request()->routeIs('patrol-schedules.*') ? 'bg-primary/10 text-primary font-bold' : 'text-secondary hover:text-on-surface hover:bg-surface-container-high' }}">
                        <span class="material-symbols-outlined text-[16px]" @if(request()->routeIs('patrol-schedules.*')) style="font-variation-settings: 'FILL' 1;" @endif>route</span>
                        <span>Rute Patroli</span>
                    </a>

                    <a href="{{ route('areas.index') }}"
                       class="flex items-center gap-sm px-sm py-[7px] rounded-lg text-[12px] transition-all duration-150
                       {{ request()->routeIs('areas.*') ? 'bg-primary/10 text-primary font-bold' : 'text-secondary hover:text-on-surface hover:bg-surface-container-high' }}">
                        <span class="material-symbols-outlined text-[16px]" @if(request()->routeIs('areas.*')) style="font-variation-settings: 'FILL' 1;" @endif>location_on</span>
                        <span>Area &amp; Checkpoint</span>
                    </a>

                    <a href="{{ route('petugas.index') }}"
                       class="flex items-center gap-sm px-sm py-[7px] rounded-lg text-[12px] transition-all duration-150
                       {{ request()->routeIs('petugas.*') ? 'bg-primary/10 text-primary font-bold' : 'text-secondary hover:text-on-surface hover:bg-surface-container-high' }}">
                        <span class="material-symbols-outlined text-[16px]" @if(request()->routeIs('petugas.*')) style="font-variation-settings: 'FILL' 1;" @endif>badge</span>
                        <span>Data Petugas</span>
                    </a>

                    <a href="{{ route('shifts.index') }}"
                       class="flex items-center gap-sm px-sm py-[7px] rounded-lg text-[12px] transition-all duration-150
                       {{ request()->routeIs('shifts.*') ? 'bg-primary/10 text-primary font-bold' : 'text-secondary hover:text-on-surface hover:bg-surface-container-high' }}">
                        <span class="material-symbols-outlined text-[16px]" @if(request()->routeIs('shifts.*')) style="font-variation-settings: 'FILL' 1;" @endif>schedule</span>
                        <span>Data Shift</span>
                    </a>
                </div>
            </div>

            <!-- Section Label -->
            <p class="text-[10px] font-bold text-secondary/60 uppercase tracking-[0.12em] px-sm pt-sm pb-xs">Sistem</p>

            <!-- Settings -->
            <a href="{{ route('settings.index') }}"
               class="group flex items-center gap-sm px-sm py-[9px] rounded-xl transition-all duration-200
               {{ request()->routeIs('settings.*')
                   ? 'bg-primary text-on-primary font-bold shadow-sm shadow-primary/20'
                   : 'text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface' }}">
                <span class="material-symbols-outlined text-[20px] flex-shrink-0"
                    @if(request()->routeIs('settings.*')) style="font-variation-settings: 'FILL' 1;" @endif>
                    settings
                </span>
                <span class="text-[13px] font-medium leading-none">System Settings</span>
            </a>
        </nav>

        <!-- Footer Actions -->
        <div class="flex-shrink-0 p-md border-t border-outline-variant space-y-sm bg-surface">

            <!-- Generate Report Button -->
            <button class="w-full bg-primary text-on-primary text-[13px] font-bold py-[10px] px-md rounded-xl hover:opacity-90 active:scale-95 transition-all flex items-center justify-center gap-xs shadow-sm shadow-primary/20">
                <span class="material-symbols-outlined text-[18px]">add_chart</span>
                Generate Report
            </button>

            <!-- Logout -->
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="w-full flex items-center gap-sm px-sm py-[9px] rounded-xl text-secondary hover:bg-red-50 hover:text-red-600 transition-all duration-200 group">
                    <span class="material-symbols-outlined text-[20px] flex-shrink-0 group-hover:text-red-500">logout</span>
                    <span class="text-[13px] font-medium">Logout</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- ===== MAIN CONTENT ===== -->
    <div class="flex-1 flex flex-col md:ml-64 h-full relative">

        <!-- TopNavBar -->
        <header class="flex justify-between items-center px-gutter w-full sticky top-0 z-30 bg-surface/80 backdrop-blur-md border-b border-outline-variant/70 h-16 flex-shrink-0">
            <div class="md:hidden flex items-center gap-sm">
                <h1 class="text-headline-md font-headline-md font-bold text-primary">Sentinel Guard Systems</h1>
            </div>
            <div class="hidden md:flex items-center gap-sm flex-1 max-w-md">
                <div class="relative w-full">
                    <span class="material-symbols-outlined absolute left-sm top-1/2 -translate-y-1/2 text-secondary text-[18px]">search</span>
                    <input class="w-full bg-surface-container-lowest border border-outline-variant rounded-full py-xs pl-[38px] pr-sm text-body-sm focus:border-primary focus:ring-2 focus:ring-primary/10 transition-all outline-none text-on-surface placeholder:text-secondary"
                           placeholder="Cari data sistem..." type="text">
                </div>
            </div>
            <div class="flex items-center gap-sm ml-auto">
                <!-- Notification Bell -->
                <button class="relative p-xs text-on-surface-variant hover:bg-surface-container rounded-full transition-colors">
                    <span class="material-symbols-outlined">notifications</span>
                    <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full border-2 border-surface"></span>
                </button>
                <!-- Help -->
                <button class="p-xs text-on-surface-variant hover:bg-surface-container rounded-full transition-colors">
                    <span class="material-symbols-outlined">help</span>
                </button>
                <!-- Avatar -->
                <div class="w-8 h-8 rounded-full bg-primary-container border border-primary/30 flex items-center justify-center cursor-pointer ml-xs">
                    @if(Auth::check())
                        <span class="text-xs font-bold text-on-primary-container">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</span>
                    @else
                        <span class="text-xs font-bold text-on-primary-container">U</span>
                    @endif
                </div>
            </div>
        </header>

        <!-- Scrollable Content Canvas -->
        <main class="flex-1 overflow-y-auto p-gutter lg:p-[32px] bg-transparent">
            {{ $slot }}
        </main>
    </div>

</body>
</html>
