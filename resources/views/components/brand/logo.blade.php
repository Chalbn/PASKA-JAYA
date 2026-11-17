@props([
    'variant' => 'default', // 'default' atau 'white'
    'size' => 'lg', // 'sm', 'md', 'lg', 'xl'
    'class' => '',
    'alt' => 'PASKA JAYA - Toko Bahan Bangunan',
])

@php
    $isWhite = $variant === 'white';
    
    $iconSizes = [
        'sm' => 'w-9 h-9 text-base rounded-xl',
        'md' => 'w-10 h-10 text-lg rounded-xl',
        'lg' => 'w-12 h-12 text-xl rounded-2xl',
        'xl' => 'w-14 h-14 text-2xl rounded-2xl',
    ];

    $iconClass = $iconSizes[$size] ?? $iconSizes['lg'];
@endphp

<div {{ $attributes->merge(['class' => 'inline-flex items-center gap-3.5 select-none ' . $class]) }}>
    {{-- Squircle Icon PJ (Lebih Gede & Tajam Sesuai Gambar Referensi) --}}
    <div class="relative {{ $iconClass }} {{ $isWhite ? 'bg-white shadow-md' : 'bg-primary shadow-sm' }} flex items-center justify-center shrink-0 overflow-hidden transition-transform duration-200 hover:scale-105">
        {{-- Aksen lengkungan bayangan halus di pojok kanan bawah icon --}}
        <div class="absolute -bottom-2 -right-2 w-5 h-5 rounded-full {{ $isWhite ? 'bg-slate-200/60' : 'bg-primary-deep/40' }} pointer-events-none"></div>
        <span class="relative z-10 font-black italic tracking-tighter {{ $isWhite ? 'text-primary' : 'text-white' }}">
            PJ
        </span>
    </div>

    {{-- Teks PASKA JAYA & TOKO BAHAN BANGUNAN --}}
    <div class="flex flex-col justify-center leading-tight">
        <span class="text-base sm:text-lg font-black tracking-wider uppercase font-sans {{ $isWhite ? 'text-white' : 'text-text-on-surface' }}">
            PASKA JAYA
        </span>
        <span class="text-[10px] sm:text-[11px] font-extrabold uppercase tracking-widest font-sans {{ $isWhite ? 'text-[#DBEAFE]/90' : 'text-text-muted' }} mt-0.5">
            TOKO BAHAN BANGUNAN
        </span>
    </div>
</div>
