@props([
    'variant' => 'white', // 'white' atau 'default'
    'class' => 'w-11 h-11',
    'alt' => 'PASKA JAYA Mark',
])

@php
    $isWhite = $variant === 'white';
@endphp

<div {{ $attributes->merge(['class' => 'relative inline-flex items-center justify-center select-none ' . $class]) }}>
    <div class="relative w-full h-full rounded-2xl {{ $isWhite ? 'bg-white shadow-md' : 'bg-primary shadow-sm' }} flex items-center justify-center shrink-0 overflow-hidden transition-transform duration-200 hover:scale-105">
        <div class="absolute -bottom-2 -right-2 w-5 h-5 rounded-full {{ $isWhite ? 'bg-slate-200/60' : 'bg-primary-deep/40' }} pointer-events-none"></div>
        <span class="relative z-10 font-black italic text-lg sm:text-xl tracking-tighter {{ $isWhite ? 'text-primary' : 'text-white' }}">
            PJ
        </span>
    </div>
</div>
