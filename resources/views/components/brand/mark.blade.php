@props([
    'variant' => 'white', // 'white' atau 'default'
    'class' => 'w-11 h-11',
    'alt' => 'PASKA JAYA Mark',
])

@php
    $isWhite = $variant === 'white';
@endphp

<div {{ $attributes->merge(['class' => 'relative inline-flex items-center justify-center select-none shrink-0 ' . $class]) }}>
    <svg
        viewBox="0 0 40 40"
        fill="none"
        xmlns="http://www.w3.org/2000/svg"
        role="img"
        aria-label="{{ $alt }}"
        class="w-full h-full drop-shadow-sm transition-transform duration-200 hover:scale-105"
    >
        @if($isWhite)
            <rect width="40" height="40" rx="11" fill="#FFFFFF" />
            <rect x="0.75" y="0.75" width="38.5" height="38.5" rx="10.25" stroke="#DBEAFE" stroke-width="1" />
        @else
            <defs>
                <linearGradient id="pjPrismBg" x1="0" y1="0" x2="40" y2="40" gradientUnits="userSpaceOnUse">
                    <stop offset="0%" stop-color="#172554" />
                    <stop offset="100%" stop-color="#1E40AF" />
                </linearGradient>
            </defs>
            <rect width="40" height="40" rx="11" fill="url(#pjPrismBg)" />
        @endif

        {{-- Monogram P (Prisma Kiri-Atas, diperbesar) --}}
        <path
            d="M7 12.5L20 5L25.8 8.3V19.6L15.8 25.4V20.2L21.4 17V10.9L11.4 16.7V30.8L7 28.3V12.5Z"
            fill="{{ $isWhite ? '#1E3A8A' : '#FFFFFF' }}"
        />
        <path
            d="M7 12.5L20 5L25.8 8.3L21.4 10.9L11.4 16.7L7 12.5Z"
            fill="{{ $isWhite ? '#1E40AF' : '#DBEAFE' }}"
        />

        {{-- Monogram J (Prisma Kanan-Bawah, diperbesar) --}}
        <path
            d="M28.6 10L33 12.5V28.3L20 35.8L14.2 32.5V27.8L20 30.8L28.6 25.8V10Z"
            fill="{{ $isWhite ? '#2563EB' : '#93C5FD' }}"
        />
        <path
            d="M33 28.3L20 35.8V30.8L28.6 25.8L33 28.3Z"
            fill="{{ $isWhite ? '#3B82F6' : '#60A5FA' }}"
        />
    </svg>
</div>