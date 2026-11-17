@props([
    'active' => 'cards', // 'cards' atau 'table'
    'leftLabel' => 'Kartu',
    'rightLabel' => 'Tabel',
])

@php
    $wireModel = $attributes->whereStartsWith('wire:model')->first();
@endphp

<div 
    x-data="{ mode: @if($wireModel) @entangle($wireModel) @else '{{ $active }}' @endif }"
    {{ $attributes->merge(['class' => 'inline-flex items-center p-1 bg-surface-canvas border border-surface-border rounded-xl shadow-xs']) }}
>
    {{-- Tombol Kiri (Kartu) --}}
    <button 
        type="button"
        x-on:click="mode = 'cards'; $dispatch('view-mode-changed', 'cards')"
        :class="mode === 'cards' 
            ? 'bg-surface-card text-primary font-semibold shadow-xs border border-surface-border' 
            : 'text-text-muted hover:text-text-slate font-medium'"
        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs transition duration-150 ease-in-out focus:outline-none select-none"
        aria-label="Tampilan Kartu"
    >
        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
        </svg>
        <span>{{ $leftLabel }}</span>
    </button>

    {{-- Tombol Kanan (Tabel) --}}
    <button 
        type="button"
        x-on:click="mode = 'table'; $dispatch('view-mode-changed', 'table')"
        :class="mode === 'table' 
            ? 'bg-surface-card text-primary font-semibold shadow-xs border border-surface-border' 
            : 'text-text-muted hover:text-text-slate font-medium'"
        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs transition duration-150 ease-in-out focus:outline-none select-none"
        aria-label="Tampilan Tabel"
    >
        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
        </svg>
        <span>{{ $rightLabel }}</span>
    </button>
</div>
