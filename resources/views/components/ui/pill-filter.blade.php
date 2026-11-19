@props([
    'options' => [], // ['semua' => 'Semua', 'belum-lunas' => 'Belum Lunas', 'lunas' => 'Lunas']
    'active' => null,
])

@php
    $wireModel = $attributes->whereStartsWith('wire:model')->first();
@endphp

<div 
    x-data="{ selected: '{{ $active ?? array_key_first($options) }}' }"
    {{ $attributes->merge(['class' => 'inline-flex items-center gap-1.5 p-1 bg-surface-canvas border border-surface-border rounded-xl shadow-xs']) }}
    role="tablist"
>
    @if(!empty($options))
        @foreach($options as $val => $label)
            <button 
                type="button"
                x-on:click="selected = '{{ $val }}'; $dispatch('filter-changed', '{{ $val }}')"
                :class="selected == '{{ $val }}' 
                    ? 'bg-primary text-primary-on font-semibold shadow-xs' 
                    : 'text-text-body hover:text-text-slate hover:bg-surface-card font-medium'"
                class="px-3.5 py-1.5 rounded-lg text-xs transition duration-150 ease-in-out select-none whitespace-nowrap focus:outline-none"
                role="tab"
            >
                {{ $label }}
            </button>
        @endforeach
    @else
        {{ $slot }}
    @endif
</div>
