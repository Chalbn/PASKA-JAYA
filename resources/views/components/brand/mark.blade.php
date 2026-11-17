@props([
    'class' => 'h-9 w-9',
    'alt' => 'PASKA JAYA Mark',
])

<div {{ $attributes->merge(['class' => 'inline-flex items-center justify-center']) }}>
    <img 
        src="{{ asset('images/logo-mark.svg') }}" 
        alt="{{ $alt }}" 
        class="{{ $class }} object-contain"
        loading="eager"
        onerror="this.onerror=null; this.classList.add('hidden'); this.nextElementSibling.classList.remove('hidden');"
    />
    <div class="hidden inline-flex items-center justify-center {{ $class }} rounded-lg bg-primary text-white font-black text-sm">
        PJ
    </div>
</div>
