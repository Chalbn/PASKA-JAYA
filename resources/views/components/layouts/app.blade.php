@props([
    'title' => 'Ringkasan Sistem',
    'role' => null, // admin, owner, kasir, karyawan (jika null, deteksi dari auth)
])

@php
    $user = auth()->user();
    $user?->loadMissing('role');
    // Tentukan role aktif: prop -> user role -> default admin
    $activeRole = $role ?? ($user?->role?->nama ?? 'admin');
    $activeRole = strtolower((string) $activeRole);
    if (!in_array($activeRole, ['admin', 'owner', 'kasir', 'karyawan'])) {
        $activeRole = 'admin';
    }

    $menuConfig = config('menu.' . $activeRole, config('menu.admin'));
    $menuLabel = $menuConfig['label'] ?? 'MENU ' . strtoupper($activeRole);
    $menuItems = $menuConfig['items'] ?? [];

    $defaultNames = [
        'admin' => 'Rudi Hartono',
        'owner' => 'Pak Hendra',
        'kasir' => 'Rina Wijaya',
        'karyawan' => 'Joko Susilo',
    ];
    $userName = $user?->nama ?? ($defaultNames[$activeRole] ?? 'Pengguna ' . ucfirst($activeRole));
    $userEmail = $user?->email ?? ($activeRole . '@paskajaya.test');
    $userInitials = inisial($userName);
    $userPhoto = $user?->fotoUrl();
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

    {{-- Google Fonts: Plus Jakarta Sans & Poppins --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300..800;1,300..800&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    {{-- Chart.js via CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    {{-- Kunci posisi sidebar sebelum render pertama agar tidak lompat saat pindah halaman --}}
    <script>
        document.documentElement.classList.add('pj-preload');
        if (localStorage.getItem('paska_sidebar_collapsed') === 'true') {
            document.documentElement.classList.add('pj-sidebar-collapsed');
        }
        window.addEventListener('DOMContentLoaded', () => {
            requestAnimationFrame(() => document.documentElement.classList.remove('pj-preload'));
        });
    </script>
    <style>
        @media (min-width: 1024px) {
            html.pj-sidebar-collapsed aside[data-pj-sidebar] { width: 76px !important; }
        }
        html.pj-preload aside[data-pj-sidebar] { transition: none !important; }
    </style>

    {{-- Vite Scripts & Styles --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js"></script>
</head>
<body 
    x-data="{ 
        sidebarOpen: false,
        sidebarCollapsed: localStorage.getItem('paska_sidebar_collapsed') === 'true',
        toggleCollapse() {
            this.sidebarCollapsed = !this.sidebarCollapsed;
            localStorage.setItem('paska_sidebar_collapsed', this.sidebarCollapsed);
            document.documentElement.classList.toggle('pj-sidebar-collapsed', this.sidebarCollapsed);
        },
        activeRole: '{{ $activeRole }}'
    }" 
    class="h-full font-sans antialiased bg-surface-bg text-text-slate flex overflow-hidden"
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
        class="fixed inset-0 z-40 bg-[#0F172A]/70 backdrop-blur-xs lg:hidden"
        style="display: none;"
    ></div>

    {{-- SIDEBAR KIRI GRADIEN BIRU ROYAL RESMI --}}
    <aside 
        data-pj-sidebar
        :class="{
            'translate-x-0': sidebarOpen,
            '-translate-x-full lg:translate-x-0': !sidebarOpen,
            'lg:w-[76px]': sidebarCollapsed,
            'lg:w-[260px]': !sidebarCollapsed
        }"
        class="fixed inset-y-0 left-0 z-50 w-[260px] bg-gradient-to-b from-[#1E3A8A] via-[#1E40AF] to-[#2563EB] text-white flex flex-col transition-all duration-300 ease-in-out lg:relative shrink-0 shadow-lg"
    >
        {{-- Tombol Toggle Collapsible Icon-Rail di Desktop --}}
        <button 
            type="button"
            x-on:click="toggleCollapse()"
            class="hidden lg:flex absolute -right-3 top-9 z-50 h-6 w-6 rounded-full bg-white text-primary shadow-md border border-slate-200 items-center justify-center hover:bg-primary-fixed hover:text-primary-deep transition select-none cursor-pointer"
            :title="sidebarCollapsed ? 'Perluas Sidebar' : 'Ciutkan Sidebar'"
            aria-label="Toggle Sidebar"
        >
            <svg x-show="!sidebarCollapsed" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
            </svg>
            <svg x-show="sidebarCollapsed" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="display: none;">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
            </svg>
        </button>

        {{-- Header Logo --}}
        <div class="h-24 px-5 flex items-center justify-between border-b border-white/10 shrink-0">
            <a href="/" x-show="!sidebarCollapsed" class="flex items-center gap-3 focus:outline-none focus:ring-2 focus:ring-white/40 rounded-xl">
                <x-brand.logo variant="white" size="lg" />
            </a>

            <a href="/" x-show="sidebarCollapsed" class="w-full flex items-center justify-center focus:outline-none focus:ring-2 focus:ring-white/40 rounded-xl" style="display: none;">
                <x-brand.mark variant="white" class="w-11 h-11" />
            </a>

            <button 
                type="button" 
                x-on:click="sidebarOpen = false"
                class="lg:hidden p-1.5 rounded-lg text-white/70 hover:text-white hover:bg-white/10 transition"
                aria-label="Tutup menu"
            >
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        {{-- Label Menu Per-Role --}}
        <div x-show="!sidebarCollapsed" class="px-5 pt-6 pb-2 shrink-0 transition-opacity duration-200">
            <span class="text-[10px] font-extrabold uppercase tracking-widest text-[#DBEAFE]/80">
                {{ $menuLabel }}
            </span>
        </div>

        {{-- Daftar Navigasi Menu --}}
        <nav class="flex-1 px-3 space-y-1.5 overflow-y-auto custom-scrollbar py-2">
            @foreach($menuItems as $item)
                @php
                    $isCurrent = request()->routeIs($item['route'], $item['route'] . '.*')
                        || request()->is(trim($item['url'], '/'))
                        || (request()->is('dev/ui*') && $loop->first && !request()->has('tab'));
                @endphp
                <a 
                    href="{{ Route::has($item['route']) ? route($item['route']) : $item['url'] }}"
                    @if($isCurrent) aria-current="page" @endif
                    :title="sidebarCollapsed ? '{{ $item['title'] }}' : ''"
                    class="group flex items-center rounded-xl text-xs font-semibold transition duration-150 ease-in-out {{ $isCurrent ? 'bg-white text-primary font-bold shadow-xs' : 'text-white/80 hover:text-white hover:bg-white/10' }}"
                    :class="sidebarCollapsed ? 'justify-center p-3' : 'justify-between px-3.5 py-2.5'"
                >
                    <div class="flex items-center gap-3 truncate" :class="sidebarCollapsed ? 'justify-center' : ''">
                        <span class="shrink-0 {{ $isCurrent ? 'text-primary' : 'text-white/70 group-hover:text-white' }}">
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
                        <span x-show="!sidebarCollapsed" class="truncate">{{ $item['title'] }}</span>
                    </div>

                    @if(!empty($item['has_badge']))
                        <span x-show="!sidebarCollapsed" class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-[#B91C1C] text-white animate-pulse">
                            Baru
                        </span>
                    @endif
                </a>
            @endforeach
        </nav>

        {{-- Tombol Keluar (Bawah Sidebar) --}}
        <div class="p-3 border-t border-white/10 shrink-0">
            @auth
                <form method="POST" action="{{ Route::has('logout') ? route('logout') : url('/logout') }}">
                    @csrf
                    <button 
                        type="submit" 
                        class="w-full flex items-center rounded-xl text-xs font-semibold text-white/80 hover:text-white hover:bg-white/10 transition duration-150 cursor-pointer"
                        :class="sidebarCollapsed ? 'justify-center p-3' : 'gap-3 px-3.5 py-2.5'"
                        :title="sidebarCollapsed ? 'Keluar' : ''"
                    >
                        <svg class="w-4 h-4 text-white/70" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        <span x-show="!sidebarCollapsed">Keluar</span>
                    </button>
                </form>
            @else
                <a 
                    href="/logout" 
                    class="w-full flex items-center rounded-xl text-xs font-semibold text-white/80 hover:text-white hover:bg-white/10 transition duration-150"
                    :class="sidebarCollapsed ? 'justify-center p-3' : 'gap-3 px-3.5 py-2.5'"
                    :title="sidebarCollapsed ? 'Keluar' : ''"
                >
                    <svg class="w-4 h-4 text-white/70" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    <span x-show="!sidebarCollapsed">Keluar</span>
                </a>
            @endauth
        </div>
    </aside>

    {{-- KONTEN UTAMA & TOPBAR --}}
    <div class="flex-1 flex flex-col min-w-0 overflow-y-auto bg-surface-bg">
        {{-- TOPBAR GRADIEN BIRU ROYAL RESMI 3 BAGIAN --}}
        <header class="h-16 sm:h-20 bg-gradient-to-r from-[#1E3A8A] via-[#1E40AF] to-[#2563EB] text-white px-4 sm:px-6 lg:px-8 flex items-center justify-between gap-2 sm:gap-4 shadow-sm shrink-0 sticky top-0 z-30 rounded-b-2xl sm:rounded-b-3xl">
            <div class="flex items-center gap-2.5 sm:gap-3.5 min-w-0">
                <button 
                    type="button" 
                    x-on:click="sidebarOpen = true"
                    class="lg:hidden p-2 rounded-xl text-white/80 hover:text-white hover:bg-white/10 transition focus:outline-none shrink-0"
                    aria-label="Buka menu navigasi"
                >
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>

                <div class="inline-flex items-center gap-2 bg-white/15 backdrop-blur-xs text-white rounded-xl px-3.5 py-1.5 border border-white/10 shadow-xs min-w-0">
                    <svg class="w-4 h-4 text-[#DBEAFE] shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                    </svg>
                    <span class="text-xs sm:text-sm font-bold text-white truncate">{{ $title }}</span>
                </div>
            </div>

            {{-- Jam & Tanggal (sembunyi otomatis saat zoom tinggi / layar < xl agar tidak menimpa pencarian) --}}
            <div class="hidden xl:flex items-center justify-center shrink-0">
                <x-ui.topbar-clock :tanggal="$tanggalHariIni" />
            </div>

            @php
                $topbarProfileHref = Route::has($activeRole . '.profil')
                    ? route($activeRole . '.profil')
                    : ($activeRole === 'admin' ? '/admin/tim' : '#');
            @endphp
            <div class="flex items-center justify-end gap-2 sm:gap-3 shrink-0">
                <x-ui.global-search />
                <x-ui.notification-bell :role="$activeRole" />

                <div
                    x-data="{
                        profileOpen: false,
                        nama: @js($userName),
                        email: @js($userEmail),
                        inisial: @js($userInitials),
                        foto: @js($userPhoto),
                    }"
                    x-on:profil-diperbarui.window="
                        nama = $event.detail.nama;
                        inisial = $event.detail.inisial;
                        foto = $event.detail.foto;
                    "
                    x-on:click.outside="profileOpen = false"
                    x-on:keydown.escape.window="profileOpen = false"
                    class="relative shrink-0"
                >
                    <button
                        type="button"
                        x-on:click="profileOpen = !profileOpen"
                        :aria-expanded="profileOpen"
                        aria-haspopup="menu"
                        class="flex items-center gap-2.5 sm:gap-3 rounded-xl px-2 py-1 hover:bg-white/10 transition cursor-pointer focus:outline-none"
                    >
                        <template x-if="foto">
                            <img
                                x-bind:src="foto"
                                x-bind:alt="nama"
                                class="w-8 h-8 sm:w-9 sm:h-9 rounded-full object-cover ring-2 ring-white/30 shadow-xs"
                            />
                        </template>
                        <template x-if="!foto">
                            <div
                                class="w-8 h-8 sm:w-9 sm:h-9 rounded-full bg-white/20 text-white font-extrabold text-xs flex items-center justify-center ring-2 ring-white/30 shadow-xs select-none"
                                x-text="inisial"
                            >{{ $userInitials }}</div>
                        </template>

                        <div class="text-left hidden sm:block">
                            <p class="text-xs font-bold text-white tracking-tight leading-tight whitespace-nowrap" x-text="nama">{{ $userName }}</p>
                            <p class="text-[10px] font-semibold text-[#DBEAFE]/90 capitalize leading-tight">{{ $activeRole }}</p>
                        </div>

                        <svg
                            class="w-3.5 h-3.5 text-blue-200 hidden sm:block transition-transform duration-200"
                            :class="profileOpen ? 'rotate-180' : ''"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div
                        x-show="profileOpen"
                        x-transition:enter="transition ease-out duration-150"
                        x-transition:enter-start="opacity-0 -translate-y-1.5 scale-95"
                        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                        x-transition:leave="transition ease-in duration-100"
                        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                        x-transition:leave-end="opacity-0 -translate-y-1.5 scale-95"
                        style="display: none;"
                        class="absolute right-0 mt-2.5 w-60 rounded-2xl bg-white border border-slate-100 shadow-[0_12px_32px_-4px_rgba(15,23,42,0.14)] p-2 z-50 text-left"
                        role="menu"
                    >
                        <div class="px-3 py-2.5 rounded-xl bg-slate-50/70 mb-1.5">
                            <p class="text-xs font-bold text-slate-800 truncate" x-text="nama">{{ $userName }}</p>
                            <p class="text-[11px] text-slate-500 truncate mt-0.5" x-text="email">{{ $userEmail }}</p>
                            <div class="mt-2">
                                <span class="inline-flex items-center bg-[#EEF3FF] text-primary text-[11px] font-semibold px-2.5 py-0.5 rounded-full capitalize">
                                    {{ $activeRole }}
                                </span>
                            </div>
                        </div>

                        <a
                            href="{{ $topbarProfileHref }}"
                            class="flex items-center gap-2.5 px-3 py-2 text-sm font-medium text-slate-700 hover:bg-[#EEF3FF] hover:text-primary rounded-xl transition"
                            role="menuitem"
                        >
                            <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            <span>Profil Saya</span>
                        </a>

                        <div class="border-t border-slate-100 my-1.5"></div>

                        @auth
                            <form method="POST" action="{{ Route::has('logout') ? route('logout') : url('/logout') }}">
                                @csrf
                                <button
                                    type="submit"
                                    class="flex items-center gap-2.5 w-full text-left text-rose-600 hover:bg-rose-50 rounded-xl px-3 py-2 text-sm font-medium transition cursor-pointer"
                                    role="menuitem"
                                >
                                    <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                    </svg>
                                    <span>Keluar</span>
                                </button>
                            </form>
                        @else
                            <a
                                href="{{ Route::has('login') ? route('login') : url('/login') }}"
                                class="flex items-center gap-2.5 w-full text-left text-rose-600 hover:bg-rose-50 rounded-xl px-3 py-2 text-sm font-medium transition"
                                role="menuitem"
                            >
                                <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                                <span>Keluar</span>
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        </header>

        {{-- KANVAS KONTEN UTAMA (#F4F6FB) --}}
        <main class="flex-1 p-4 sm:p-6 lg:p-8 bg-surface-bg min-h-0">
            {{ $slot }}
        </main>
    </div>

    {{-- Global Toast Notification Listener --}}
    <x-ui.toast />

</body>
</html>