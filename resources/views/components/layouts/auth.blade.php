@props([
    'title' => 'Masuk',
])

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

    {{-- Vite Scripts & Styles --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js"></script>

    <style>
        @media (min-width: 1024px) {
            .pj-auth-curve {
                border-top-left-radius: 36% 50%;
                border-bottom-left-radius: 36% 50%;
        }
    }
    </style>
</head>
<body class="min-h-full font-sans antialiased text-text-slate bg-surface-bg flex flex-col items-center justify-center p-4 sm:p-6 lg:p-10">

    {{-- KARTU UTAMA TENGAH (Desktop: Form Kiri + Panel Lengkung Kanan | HP: Banner Foto Atas + Form Menumpuk) --}}
    <div class="w-full max-w-5xl bg-surface-card border border-surface-border/90 rounded-3xl shadow-[0_24px_60px_-15px_rgba(30,58,138,0.18)] overflow-hidden grid grid-cols-1 lg:grid-cols-12">

        {{-- BANNER MOBILE (< lg): Foto Toko + Overlay Biru Royal + Logo --}}
        <div class="relative lg:hidden h-48 sm:h-56 pb-6 flex flex-col items-center justify-center text-center px-6 overflow-hidden text-white bg-[#1E3A8A]">
            <img
                src="{{ asset('images/foto-toko.jpg') }}"
                alt="Toko Bahan Bangunan PASKA JAYA"
                class="absolute inset-0 h-full w-full object-cover object-center"
            />
            <div class="absolute inset-0 bg-gradient-to-br from-[#1E3A8A]/85 via-[#1E40AF]/80 to-[#2563EB]/80 backdrop-blur-[1px]"></div>

            <a href="/" class="relative z-10 inline-block focus:outline-none focus:ring-2 focus:ring-white/40 rounded-xl">
                <x-brand.logo variant="white" class="h-11 w-auto mx-auto" />
            </a>
            <p class="relative z-10 mt-2 text-xs font-semibold text-[#DBEAFE]">
                Sistem Manajemen Toko Bahan Bangunan
            </p>
        </div>

        {{-- PANEL FORM (Kiri di Desktop, Bawah Menumpuk Melengkung di HP) --}}
        <div class="relative z-10 -mt-6 rounded-t-3xl lg:mt-0 lg:rounded-none lg:order-1 lg:col-span-6 p-6 sm:p-8 lg:p-10 xl:p-12 flex flex-col justify-center bg-surface-card">
            <div class="w-full max-w-md mx-auto">
                {{ $slot }}

                <div class="mt-6 pt-5 border-t border-surface-border/70 text-center text-xs text-text-muted">
                    <span>Butuh bantuan akses sistem? Hubungi administrator toko.</span>
                </div>
            </div>
        </div>

        {{-- PANEL VISUAL KANAN DESKTOP (>= lg): Area Lengkung + Foto Toko + Overlay Biru Royal --}}
        <div class="pj-auth-curve relative hidden lg:flex lg:order-2 lg:col-span-6 flex-col justify-between lg:py-12 lg:pr-10 lg:pl-28 xl:pl-32 overflow-hidden text-white bg-[#1E3A8A]">
            <img
                src="{{ asset('images/foto-toko.jpg') }}"
                alt="Toko Bahan Bangunan PASKA JAYA"
                class="absolute inset-0 h-full w-full object-cover object-center"
            />
            <div class="absolute inset-0 bg-gradient-to-br from-[#1E3A8A]/88 via-[#1E40AF]/85 to-[#2563EB]/80 backdrop-blur-[1.5px]"></div>

            {{-- Logo Atas --}}
            <div class="relative z-10">
                <a href="/" class="inline-block focus:outline-none focus:ring-2 focus:ring-white/40 rounded-xl">
                    <x-brand.logo variant="white" class="h-11 w-auto" />
                </a>
            </div>

            {{-- Konten Tengah --}}
            <div class="relative z-10 my-auto py-8">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-secondary/20 border border-secondary/40 text-secondary-buff text-[11px] font-bold uppercase tracking-wider mb-5">
                    <span>Sistem Manajemen Toko Bahan Bangunan</span>
                </div>

                <h1 class="text-2xl xl:text-3xl font-black tracking-tight text-white leading-tight">
                    Every sack, bar, and delivery in one place.
                </h1>

                <p class="mt-3 text-sm text-[#DBEAFE] font-normal leading-relaxed">
                    Stock, cashier, and delivery tracking for your building-material store, all in a single system.
                </p>

                <div class="mt-6 pt-6 border-t border-white/15 grid grid-cols-1 gap-3 text-xs">
                    <div class="flex items-center gap-2.5 text-white/90">
                        <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-secondary text-secondary-on font-black text-[10px]">✓</span>
                        <span class="font-medium">Kasir POS Cepat &amp; Pengelolaan Piutang Bon Terpadu</span>
                    </div>
                    <div class="flex items-center gap-2.5 text-white/90">
                        <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-secondary text-secondary-on font-black text-[10px]">✓</span>
                        <span class="font-medium">Pelacakan Pengantaran Karyawan dengan Bukti Foto</span>
                    </div>
                    <div class="flex items-center gap-2.5 text-white/90">
                        <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-secondary text-secondary-on font-black text-[10px]">✓</span>
                        <span class="font-medium">Multi-Peran Khusus: Admin, Owner, Kasir, dan Karyawan</span>
                    </div>
                </div>
            </div>

            {{-- Copyright Bawah --}}
            <div class="relative z-10 text-[11px] text-white/65">
                &copy; {{ date('Y') }} Toko Bahan Bangunan PASKA JAYA. Hak cipta dilindungi.
            </div>
        </div>
    </div>

    {{-- Global Toast --}}
    <x-ui.toast />

</body>
</html>