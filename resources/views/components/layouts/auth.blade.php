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

</head>
<body class="h-full font-sans antialiased text-text-slate bg-surface-bg">
    <div class="min-h-full flex flex-col lg:flex-row">
        {{-- PANEL KIRI (SPLIT-SCREEN): Foto Toko + Overlay Deep Cobalt (#1E3A8A/85) + Logo White + Headline --}}
        <div class="relative hidden lg:flex lg:w-1/2 xl:w-5/12 flex-col justify-between p-12 overflow-hidden text-white bg-[#1E3A8A]">
            {{-- Background Image: foto-toko.jpg --}}
            <img 
                src="{{ asset('images/foto-toko.jpg') }}" 
                alt="Toko Bahan Bangunan PASKA JAYA" 
                class="absolute inset-0 h-full w-full object-cover object-center"
            />
            
            {{-- Overlay #1E3A8A/85 --}}
            <div class="absolute inset-0 bg-[#1E3A8A]/85 backdrop-blur-[2px]"></div>

            {{-- Konten Atas: Logo White --}}
            <div class="relative z-10">
                <a href="/" class="inline-block focus:outline-none focus:ring-2 focus:ring-white/40 rounded-xl">
                    <x-brand.logo variant="white" class="h-12 w-auto" />
                </a>
            </div>

            {{-- Konten Tengah: Headline & Subteks Resmi --}}
            <div class="relative z-10 my-auto py-12 max-w-lg">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-secondary/20 border border-secondary/40 text-secondary-buff text-xs font-bold uppercase tracking-wider mb-6">
                    <span>Sistem Manajemen Toko Bahan Bangunan</span>
                </div>

                <h1 class="text-3xl sm:text-4xl xl:text-5xl font-black tracking-tight text-white leading-tight">
                    Every sack, bar, and delivery in one place.
                </h1>

                <p class="mt-4 text-base text-[#DBEAFE] font-normal leading-relaxed">
                    Stock, cashier, and delivery tracking for your building-material store, all in a single system.
                </p>

                {{-- Pilar Utama Sistem --}}
                <div class="mt-8 pt-8 border-t border-white/15 grid grid-cols-1 gap-4 text-xs">
                    <div class="flex items-center gap-3 text-white/90">
                        <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-secondary text-secondary-on font-black">✓</span>
                        <span class="font-medium">Kasir POS Cepat & Pengelolaan Piutang Bon Terpadu</span>
                    </div>
                    <div class="flex items-center gap-3 text-white/90">
                        <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-secondary text-secondary-on font-black">✓</span>
                        <span class="font-medium">Pelacakan Pengantaran Karyawan dengan Bukti Foto</span>
                    </div>
                    <div class="flex items-center gap-3 text-white/90">
                        <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-secondary text-secondary-on font-black">✓</span>
                        <span class="font-medium">Multi-Peran Khusus: Admin, Owner, Kasir, dan Karyawan</span>
                    </div>
                </div>
            </div>

            {{-- Konten Bawah: Copyright --}}
            <div class="relative z-10 text-xs text-white/60">
                &copy; {{ date('Y') }} Toko Bahan Bangunan PASKA JAYA. Hak cipta dilindungi.
            </div>
        </div>

        {{-- PANEL KANAN: Form Card (TANPA DEMO ACCOUNT) --}}
        <div class="flex-1 flex flex-col justify-center py-12 px-4 sm:px-6 lg:px-12 xl:px-20 bg-surface-bg">
            <div class="mx-auto w-full max-w-md">
                {{-- Header Logo untuk Layar Mobile (< lg) --}}
                <div class="lg:hidden text-center mb-8">
                    <a href="/" class="inline-block">
                        <x-brand.logo variant="default" class="h-10 w-auto mx-auto" />
                    </a>
                </div>

                {{-- Slot Form Auth --}}
                <div class="bg-surface-card border border-surface-border rounded-2xl p-6 sm:p-8 shadow-sm">
                    {{ $slot }}
                </div>

                {{-- Catatan Keamanan / Bantuan --}}
                <div class="mt-6 text-center text-xs text-text-muted">
                    <span>Butuh bantuan akses sistem? Hubungi administrator toko.</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Global Toast --}}
    <x-ui.toast />

</body>
</html>
