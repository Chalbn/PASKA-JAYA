@props([
    'variant' => 'default', // 'default' atau 'white'
    'class' => 'h-10 w-auto',
    'alt' => 'PASKA JAYA - Toko Bahan Bangunan',
])

@php
    $src = $variant === 'white' 
        ? asset('images/logo-white.svg') 
        : asset('images/logo.svg');
@endphp

<div {{ $attributes->merge(['class' => 'inline-flex items-center gap-2']) }}>
    <img 
        src="{{ $src }}" 
        alt="{{ $alt }}" 
        class="{{ $class }} object-contain"
        loading="eager"
        onerror="this.onerror=null; this.classList.add('hidden'); this.nextElementSibling.classList.remove('hidden');"
    />
    <div class="hidden flex items-center gap-2 font-bold {{ $variant === 'white' ? 'text-white' : 'text-primary' }}">
        <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg {{ $variant === 'white' ? 'bg-white text-primary' : 'bg-primary text-white' }} text-sm font-black">
            PJ
        </span>
        <div class="leading-none text-left">
            <div class="text-base font-extrabold tracking-tight">PASKA JAYA</div>
            <div class="text-[9px] uppercase tracking-widest opacity-80">Toko Bahan Bangunan</div>
        </div>
    </div>
</div>
