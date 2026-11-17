@props([
    'title' => 'PASKA JAYA',
    'role' => null, // admin, owner, kasir, karyawan (jika null, deteksi dari auth)
])

@php
    $user = auth()->user();
    // Tentukan role aktif: prop -> user role -> default kasir
    $activeRole = $role ?? ($user?->role?->nama ?? ($user?->role ?? 'admin'));
    $activeRole = strtolower($activeRole);
    if (!in_array($activeRole, ['admin', 'owner', 'kasir', 'karyawan'])) {
        $activeRole = 'admin';
    }

    $menuConfig = config('menu.' . $activeRole, config('menu.admin'));
    $menuLabel = $menuConfig['label'] ?? 'MENU ' . strtoupper($activeRole);
    $menuItems = $menuConfig['items'] ?? [];

    $userName = $user?->name ?? 'Pengguna ' . ucfirst($activeRole);
    $userInitials = inisial($userName);
    $tanggalHariIni = tanggal_id(now(), true);
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-surface-bg">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title }} — PASKA JAYA</title>

    {{-- Favicon & Touch Icon --}}
    <link rel="icon" type="image/x-icon" href="{{ asset('images/favicon.ico') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/apple-touch-icon.png') }}">

    {{-- Google Fonts: Plus Jakarta Sans --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">

    {{-- Chart.js via CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    {{-- Vite Scripts & Styles --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @if(class_exists(\Livewire\Livewire::class))
        @livewireStyles
    @endif
</head>
<body 
    x-data="{ 
        sidebarOpen: false,
        sidebarCollapsed: localStorage.getItem('paska_sidebar_collapsed') === 'true',
        toggleCollapse() {
            this.sidebarCollapsed = !this.sidebarCollapsed;
            localStorage.setItem('paska_sidebar_collapsed', this.sidebarCollapsed);
        },
        activeRole: '{{ $activeRole }}'
    }" 
    class="min-h-full font-sans antialiased bg-surface-bg text-text-slate flex flex-col lg:flex-row"
>
    {{-- BACKDROP MOBILE DRAWER (< lg) --}}
    <div 
        x-show="sidebarOpen" 
        x-transition:enter="transition-opacity ease-linear duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity ease-linear duration-300"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        x-on:click="sidebarOpen = false"
        class="fixed inset-0 z-40 bg-[#162D4D]/75 backdrop-blur-xs lg:hidden"
        style="display: none;"
    ></div>

    {{-- LIQUID GLASS & FLOATING SIDEBAR (Collapsible Icon-Rail) --}}
    <aside 
        :class="{
            'translate-x-0': sidebarOpen,
            '-translate-x-full lg:translate-x-0': !sidebarOpen,
            'lg:w-[260px]': !sidebarCollapsed,
            'lg:w-[76px]': sidebarCollapsed
        }"
        class="fixed inset-y-0 left-0 z-50 w-[260px] lg:static lg:m-3 lg:h-[calc(100vh-1.5rem)] lg:rounded-2xl lg:sticky lg:top-3 shrink-0 flex flex-col transition-all duration-300 ease-in-out bg-gradient-to-b from-[#162D4D]/90 to-[#2E4365]/80 backdrop-blur-xl border border-white/20 shadow-[0_8px_32px_rgba(22,45,77,0.28)] text-white"
    >
        {{-- TOMBOL FLOATING COLLAPSE / HIDE SIDEBAR (Desktop lg:flex) --}}
        <button 
            type="button" 
            x-on:click="toggleCollapse()"
            class="absolute -right-3 top-7 h-6 w-6 rounded-full bg-white text-[#162D4D] shadow-md border border-slate-200 hidden lg:flex items-center justify-center hover:bg-[#E59D2C] hover:text-slate-900 transition duration-150 z-50 focus:outline-none"
            :title="sidebarCollapsed ? 'Perluas sidebar' : 'Ciutkan sidebar'"
            aria-label="Toggle sidebar collapse"
        >
            <svg 
                class="w-3.5 h-3.5 transition-transform duration-300" 
                :class="sidebarCollapsed ? 'rotate-180' : ''" 
                fill="none" 
                viewBox="0 0 24 24" 
                stroke="currentColor"
            >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
            </svg>
        </button>

        {{-- HEADER LOGO (Liquid Glass) --}}
        <div class="h-20 px-4 flex items-center justify-between border-b border-white/15 relative shrink-0">
            <a href="/" class="focus:outline-none focus:ring-2 focus:ring-white/40 rounded-xl flex items-center w-full" :class="sidebarCollapsed ? 'justify-center' : 'justify-start px-2'">
                {{-- Logo penuh saat terbuka --}}
                <div x-show="!sidebarCollapsed" x-transition.opacity.duration.200ms class="truncate">
                    <x-brand.logo variant="white" class="h-9 w-auto" />
                </div>
                {{-- Mark logo saat ciut (Icon-Rail) --}}
                <div x-show="sidebarCollapsed" x-transition.opacity.duration.200ms style="display: none;" class="flex justify-center">
                    <x-brand.mark class="h-8 w-8" />
                </div>
            </a>

            {{-- Close button di drawer mobile --}}
            <button 
                type="button" 
                x-on:click="sidebarOpen = false"
                class="lg:hidden p-1.5 rounded-lg text-white/70 hover:text-white hover:bg-white/10 transition shrink-0"
                aria-label="Tutup sidebar"
            >
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        {{-- LABEL KATEGORI ROLE ("MENU {ROLE}") --}}
        <div 
            x-show="!sidebarCollapsed" 
            x-transition.opacity.duration.200ms
            class="px-6 pt-5 pb-2 shrink-0"
        >
            <span class="text-[10px] font-extrabold uppercase tracking-widest text-[#D6E3FF]/70">
                {{ $menuLabel }}
            </span>
        </div>

        {{-- DAFTAR MENU (COLLAPSIBLE ICON-RAIL) --}}
        <nav class="flex-1 px-3 space-y-1.5 overflow-y-auto custom-scrollbar py-3">
            @foreach($menuItems as $item)
                @php
                    $isCurrent = request()->routeIs($item['route'] . '*') || request()->is(trim($item['url'], '/') . '*');
                @endphp
                <a 
                    href="{{ Route::has($item['route']) ? route($item['route']) : $item['url'] }}"
                    @if($isCurrent) aria-current="page" @endif
                    title="{{ $item['title'] }}"
                    class="group relative flex items-center rounded-xl text-xs font-semibold transition-all duration-150 ease-in-out {{ $isCurrent ? 'bg-white text-[#162D4D] shadow-sm font-bold' : 'text-white/80 hover:text-white hover:bg-white/10' }}"
                    :class="sidebarCollapsed ? 'justify-center w-11 h-11 mx-auto' : 'justify-between px-3.5 py-2.5'"
                >
                    <div class="flex items-center gap-3 truncate" :class="sidebarCollapsed ? 'justify-center' : ''">
                        {{-- Icon Menu --}}
                        <span class="shrink-0 {{ $isCurrent ? 'text-[#162D4D]' : 'text-white/70 group-hover:text-white' }}">
                            @switch($item['icon'] ?? 'default')
                                @case('dashboard')
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h4a1 1 0 011 1v5a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM14 5a1 1 0 011-1h4a1 1 0 011 1v2a1 1 0 01-1 1h-4a1 1 0 01-1-1V5zM4 15a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H5a1 1 0 01-1-1v-4zM14 12a1 1 0 011-1h4a1 1 0 011 1v7a1 1 0 01-1 1h-4a1 1 0 01-1-1v-7z" /></svg>
                                    @break
                                @case('users')
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                                    @break
                                @case('team')
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                                    @break
                                @case('report')
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                    @break
                                @case('bon')
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                    @break
                                @case('items')
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>
                                    @break
                                @case('attendance')
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                    @break
                                @case('profile')
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                                    @break
                                @case('home')
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>
                                    @break
                                @case('pos')
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                                    @break
                                @case('wallet')
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" /></svg>
                                    @break
                                @case('chart')
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z" /></svg>
                                    @break
                                @case('history')
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                    @break
                                @case('note-bon')
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                    @break
                                @case('materials')
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
                                    @break
                                @case('delivery')
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" /></svg>
                                    @break
                                @default
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                            @endswitch
                        </span>
                        {{-- Label Teks Menu --}}
                        <span x-show="!sidebarCollapsed" x-transition.opacity.duration.200ms class="truncate">{{ $item['title'] }}</span>
                    </div>

                    {{-- Badge notifikasi --}}
                    @if(!empty($item['has_badge']))
                        {{-- Saat Terbuka --}}
                        <span x-show="!sidebarCollapsed" x-transition.opacity.duration.200ms class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-[#B91C1C] text-white animate-pulse">
                            Baru
                        </span>
                        {{-- Saat Ciut (Titik Merah Mengambang) --}}
                        <span x-show="sidebarCollapsed" class="absolute top-1.5 right-1.5 w-2.5 h-2.5 rounded-full bg-[#B91C1C] ring-2 ring-[#162D4D]"></span>
                    @endif
                </a>
            @endforeach
        </nav>

        {{-- BOTTOM AREA: TOMBOL KELUAR (Liquid Glass Divider) --}}
        <div class="p-3 border-t border-white/15 bg-black/10 shrink-0">
            @auth
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button 
                        type="submit" 
                        class="w-full flex items-center rounded-xl text-xs font-semibold text-white/80 hover:text-white hover:bg-white/10 transition duration-150"
                        :class="sidebarCollapsed ? 'justify-center w-11 h-11 p-0 mx-auto' : 'gap-3 px-3.5 py-2.5'"
                        :title="sidebarCollapsed ? 'Keluar' : ''"
                    >
                        <svg class="w-4 h-4 text-white/70 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        <span x-show="!sidebarCollapsed" x-transition.opacity.duration.200ms>Keluar</span>
                    </button>
                </form>
            @else
                <a 
                    href="/logout" 
                    class="w-full flex items-center rounded-xl text-xs font-semibold text-white/80 hover:text-white hover:bg-white/10 transition duration-150"
                    :class="sidebarCollapsed ? 'justify-center w-11 h-11 p-0 mx-auto' : 'gap-3 px-3.5 py-2.5'"
                    :title="sidebarCollapsed ? 'Keluar' : ''"
                >
                    <svg class="w-4 h-4 text-white/70 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    <span x-show="!sidebarCollapsed" x-transition.opacity.duration.200ms>Keluar</span>
                </a>
            @endauth
        </div>
    </aside>

    {{-- KONTEN UTAMA & TOPBAR --}}
    <div class="flex-1 flex flex-col min-w-0 overflow-y-auto transition-all duration-300 ease-in-out">
        {{-- TOPBAR LIQUID GLASS & GRADIENT --}}
        <header class="h-20 bg-gradient-to-r from-[#162D4D] via-[#243754] to-[#2E4365] text-white px-4 sm:px-8 flex items-center justify-between shadow-md shrink-0 sticky top-0 z-30 lg:rounded-b-2xl lg:mx-3 lg:mt-3 border-b lg:border border-white/10 backdrop-blur-md">
            <div class="flex items-center gap-3.5 min-w-0">
                {{-- Hamburger Menu untuk Layar Mobile (< lg) --}}
                <button 
                    type="button" 
                    x-on:click="sidebarOpen = true"
                    class="lg:hidden p-2 rounded-xl text-white/80 hover:text-white hover:bg-white/10 transition focus:outline-none"
                    aria-label="Buka menu navigasi"
                >
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>

                {{-- Tanggal & Judul Halaman --}}
                <div class="truncate">
                    <p class="text-[11px] font-medium text-[#D6E3FF] flex items-center gap-1.5 tabular-nums">
                        <svg class="w-3.5 h-3.5 opacity-80" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <span>{{ $tanggalHariIni }}</span>
                    </p>
                    <h1 class="text-base sm:text-lg font-extrabold tracking-tight text-white truncate mt-0.5">
                        {{ $title }}
                    </h1>
                </div>
            </div>

            {{-- Profil & Avatar Inisial Kanan --}}
            <div class="flex items-center gap-3 shrink-0">
                <div class="hidden sm:block text-right">
                    <p class="text-xs font-bold text-white tracking-tight leading-none">{{ $userName }}</p>
                    <span class="inline-block mt-1 text-[10px] font-bold uppercase tracking-wider text-secondary bg-white/10 px-2 py-0.5 rounded-full border border-white/10">
                        {{ $activeRole }}
                    </span>
                </div>

                {{-- Initial Avatar --}}
                <div class="w-10 h-10 rounded-full bg-secondary text-secondary-on font-black text-sm flex items-center justify-center ring-2 ring-white/30 shadow-xs select-none">
                    {{ $userInitials }}
                </div>
            </div>
        </header>

        {{-- MAIN CONTENT CANVAS (#F4F6F9) --}}
        <main class="flex-1 p-4 sm:p-6 lg:p-6 bg-surface-bg min-h-0">
            {{ $slot }}
        </main>
    </div>

    {{-- Global Toast Notification Listener --}}
    <x-ui.toast />

    @if(class_exists(\Livewire\Livewire::class))
        @livewireScripts
    @endif
</body>
</html>
