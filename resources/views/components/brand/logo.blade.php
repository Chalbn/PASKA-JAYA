@props([
    'variant' => 'default', // 'default' atau 'white'
    'size' => 'lg', // 'sm', 'md', 'lg', 'xl'
    'class' => '',
    'alt' => 'PASKA JAYA - Toko Bahan Bangunan',
])

@php
    $isWhite = $variant === 'white';

    $iconSizes = [
        'sm' => 'w-9 h-9',
        'md' => 'w-10 h-10',
        'lg' => 'w-11 h-11',
        'xl' => 'w-14 h-14',
    ];

    $iconClass = $iconSizes[$size] ?? $iconSizes['lg'];
@endphp

<div {{ $attributes->merge(['class' => 'inline-flex items-center gap-3 select-none ' . $class]) }}>
    <x-brand.mark :variant="$variant" :class="$iconClass" :alt="$alt" />

    <div class="flex flex-col justify-center leading-none">
        <div
            class="flex items-baseline gap-1.5 uppercase"
            style="font-family: 'Poppins', sans-serif; font-size: 16px; letter-spacing: 0.08em; line-height: 1;"
        >
            <span style="font-weight: 800; color: {{ $isWhite ? '#FFFFFF' : '#0F172A' }};">PASKA</span>
            <span style="font-weight: 700; color: {{ $isWhite ? '#93C5FD' : '#1D4ED8' }};">JAYA</span>
        </div>

        <div
            style="height: 1px; width: 100%; margin: 5px 0; background-color: {{ $isWhite ? 'rgba(147, 197, 253, 0.45)' : '#CBD5E1' }};"
        ></div>

        <span
            class="uppercase"
            style="font-family: 'Poppins', sans-serif; font-size: 8.5px; font-weight: 600; letter-spacing: 0.18em; color: {{ $isWhite ? 'rgba(219, 234, 254, 0.92)' : '#64748B' }}; line-height: 1;"
        >
            TOKO BAHAN BANGUNAN
        </span>
    </div>
</div>