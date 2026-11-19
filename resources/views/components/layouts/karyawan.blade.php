<!DOCTYPE html>
<html lang="id" class="h-full bg-surface-bg">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Pengantaran' }} — PASKA JAYA</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300..800;1,300..800&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js"></script>
</head>
<body
    x-data="{
        sidebarOpen: false,
        toasts: []
    }"
    class="min-h-full font-sans antialiased bg-blue-50/40 text-text-slate flex flex-col"
>
    {{-- Toast Notification --}}
    <div
        x-on:toast.window="toasts.push({ type: $event.detail.type || 'info', message: typeof $event.detail === 'string' ? $event.detail : ($event.detail.message || ''), id: Date.now() }); setTimeout(() => toasts.shift(), 4000)"
        class="fixed top-4 right-4 z-50 space-y-2 max-w-sm w-full pointer-events-none"
    >
        <template x-for="toast in toasts" :key="toast.id">
            <div
                x-transition
                :class="{
                    'bg-emerald-600': toast.type === 'success',
                    'bg-red-600': toast.type === 'error',
                    'bg-amber-600': toast.type === 'warning',
                    'bg-blue-600': toast.type === 'info'
                }"
                class="pointer-events-auto text-white px-4 py-3 rounded-xl shadow-lg text-sm font-medium flex items-center justify-between"
            >
                <span x-text="toast.message"></span>
            </div>
        </template>
    </div>

    {{-- Mobile Sidebar Backdrop --}}
    <div
        x-show="sidebarOpen"
        x-transition:enter="transition-opacity ease-linear duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity ease-linear duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        x-on:click="sidebarOpen = false"
        class="fixed inset-0 z-40 bg-slate-900/60 backdrop-blur-xs lg:hidden"
        style="display: none;"
    ></div>

    <div class="flex flex-1 min-h-screen">
        {{-- Sidebar --}}
        <aside
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
            class="fixed inset-y-0 left-0 z-40 w-[230px] bg-gradient-to-b from-[#1E3A8A] via-[#1E40AF] to-[#2563EB] text-white flex flex-col transition-transform duration-200 ease-in-out lg:static shrink-0 shadow-lg"
        >
            {{-- Logo --}}
            <div class="px-5 py-6">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-white/15 backdrop-blur-xs border border-white/20 flex items-center justify-center font-black text-lg tracking-wider text-white shadow-inner">
                        PJ
                    </div>
                    <div>
                        <div class="font-extrabold text-sm tracking-tight leading-tight text-white">PASKA JAYA</div>
                        <div class="text-[9px] font-bold tracking-widest text-blue-200/80 uppercase">TOKO BAHAN BANGUNAN</div>
                    </div>
                </div>
            </div>

            {{-- Label Menu --}}
            <div class="px-5 pt-1 pb-2">
                <span class="text-[10px] font-bold tracking-widest uppercase text-blue-200/70">MENU KARYAWAN</span>
            </div>

            {{-- Navigasi --}}
            <nav class="flex-1 px-3 space-y-1">
                @php
                    $badgeBaru = auth()->check() ? app(\App\Services\PengirimanService::class)->jumlahBaru(auth()->id()) : 0;
                @endphp
                <a
                    href="{{ route('karyawan.pengantaran.index') }}"
                    class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-bold transition {{ request()->routeIs('karyawan.pengantaran*') ? 'bg-white text-[#1E40AF] shadow-sm' : 'text-white/85 hover:bg-white/10 hover:text-white' }}"
                >
                    <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 0 0-3.213-9.193 2.056 2.056 0 0 0-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 0 0-10.026 0 1.106 1.106 0 0 0-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12" />
                    </svg>
                    <span class="flex-1">Pengantaran</span>
                    @if($badgeBaru > 0)
                        <span class="inline-flex items-center justify-center min-w-[20px] h-5 px-1.5 rounded-full bg-red-500 text-white text-[11px] font-black leading-none shadow-xs">
                            {{ $badgeBaru }}
                        </span>
                    @endif
                </a>

                <a
                    href="{{ Route::has('karyawan.profil') ? route('karyawan.profil') : '#' }}"
                    class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-bold transition {{ request()->routeIs('karyawan.profil*') ? 'bg-white text-[#1E40AF] shadow-sm' : 'text-white/85 hover:bg-white/10 hover:text-white' }}"
                >
                    <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7 0 3.75 3.75 0 017 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                    </svg>
                    <span>Profil</span>
                </a>
            </nav>

            {{-- Tombol Keluar --}}
            <div class="p-3 border-t border-white/10">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button
                        type="submit"
                        class="w-full flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold text-white/75 hover:bg-white/10 hover:text-white transition"
                    >
                        <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" />
                        </svg>
                        <span>Keluar</span>
                    </button>
                </form>
            </div>
        </aside>

        {{-- Main Area --}}
        <div class="flex-1 flex flex-col min-w-0">
            {{-- Topbar 3 Bagian --}}
            <header class="bg-gradient-to-r from-[#1E3A8A] via-[#1E40AF] to-[#2563EB] text-white px-4 sm:px-6 py-3 shadow-xs sticky top-0 z-30 rounded-b-2xl sm:rounded-b-3xl">
                <div class="flex items-center justify-between gap-2 sm:gap-4">
                    {{-- Sisi Kiri: Drawer Mobile + Penunjuk Halaman Aktif --}}
                    <div class="flex items-center gap-2.5 sm:gap-3 min-w-0 sm:flex-1">
                        <button
                            type="button"
                            x-on:click="sidebarOpen = true"
                            class="p-2 -ml-2 rounded-lg text-white/90 hover:bg-white/10 lg:hidden focus:outline-none shrink-0"
                            aria-label="Buka Menu"
                        >
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                            </svg>
                        </button>

                        <div class="inline-flex items-center gap-2 bg-white/15 backdrop-blur-xs text-white rounded-xl px-3.5 py-1.5 border border-white/10 shadow-xs min-w-0">
                            <svg class="w-4 h-4 text-[#DBEAFE] shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                            </svg>
                            <span class="text-xs sm:text-sm font-bold text-white truncate">{{ $title ?? 'Pengantaran' }}</span>
                        </div>
                    </div>

                    {{-- Sisi Tengah: Card Tanggal (Bold) & Jam Digital Real-Time (Normal, Poppins) --}}
                    <div
                        x-data="{
                            timeStr: '',
                            init() {
                                const updateTime = () => {
                                    const now = new Date();
                                    this.timeStr = now.toLocaleTimeString('id-ID', {
                                        timeZone: 'Asia/Jakarta',
                                        hour: '2-digit',
                                        minute: '2-digit',
                                        second: '2-digit',
                                        hour12: false
                                    }).replace(/\./g, ':');
                                };
                                updateTime();
                                setInterval(updateTime, 1000);
                            }
                        }"
                        class="shrink-0 bg-white/10 backdrop-blur-xs border border-white/15 rounded-2xl px-3.5 sm:px-5 py-1.5 text-center shadow-2xs flex flex-col items-center justify-center font-['Poppins',sans-serif]"
                    >
                        <span class="text-[11px] sm:text-xs font-bold text-blue-100 leading-tight whitespace-nowrap">{{ now()->timezone('Asia/Jakarta')->locale('id')->isoFormat('dddd, D MMMM Y') }}</span>
                        <span class="text-xs sm:text-sm font-normal tracking-wider text-white tabular-nums leading-tight mt-0.5" x-text="timeStr">--:--:--</span>
                    </div>

                    {{-- Sisi Kanan: Profil Pengguna --}}
                    <div class="flex items-center justify-end gap-3 shrink-0 sm:flex-1 min-w-0">
                        @auth
                            <a href="{{ Route::has('karyawan.profil') ? route('karyawan.profil') : '#' }}" class="flex items-center gap-3 hover:opacity-90 transition">
                                <div class="hidden sm:block text-right">
                                    <p class="text-xs font-bold text-white leading-tight">{{ auth()->user()->nama }}</p>
                                    <p class="text-[10px] text-blue-200/80 leading-tight uppercase font-medium">Karyawan</p>
                                </div>
                                @if(auth()->user()->fotoUrl())
                                    <img src="{{ auth()->user()->fotoUrl() }}" alt="{{ auth()->user()->nama }}" class="w-8 h-8 rounded-full object-cover border border-white/30 shadow-inner" />
                                @else
                                    <div class="w-8 h-8 rounded-full bg-white/20 border border-white/30 text-white flex items-center justify-center font-bold text-xs shadow-inner">
                                        {{ inisial(auth()->user()->nama) }}
                                    </div>
                                @endif
                            </a>
                        @endauth
                    </div>
                </div>
            </header>

            {{-- Content --}}
            <main class="flex-1 p-4 sm:p-6 max-w-4xl w-full mx-auto">
                {{ $slot }}
            </main>
        </div>
    </div>
</body>
</html>
