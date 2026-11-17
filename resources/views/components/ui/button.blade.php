@props([
    'variant' => 'primary', // primary, secondary, outline, danger, ghost
    'size' => 'md', // sm, md, lg
    'type' => 'button',
    'href' => null,
    'disabled' => false,
    'loading' => false,
    'icon' => null,
    'iconPosition' => 'left',
])

@php
    $baseClasses = 'inline-flex items-center justify-center font-medium rounded-xl transition duration-150 ease-in-out focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed select-none gap-2';

    $variants = [
        // Primary: Marigold (#E59D2C) dengan teks gelap tebal (#1E293B) untuk aksi utama
        'primary' => 'bg-secondary text-secondary-on font-semibold hover:bg-secondary-container active:opacity-95 shadow-sm focus:ring-secondary',
        // Secondary: Police Blue (#2E4365) dengan teks putih
        'secondary' => 'bg-primary text-primary-on font-medium hover:bg-primary-deep active:opacity-95 shadow-sm focus:ring-primary',
        // Outline: Putih dengan border #CBD5E1
        'outline' => 'bg-surface-card text-text-slate border border-surface-border-subtle hover:bg-surface-container-low focus:ring-primary shadow-xs',
        // Danger: Merah lembut #FEE2E2 dengan teks #B91C1C / #991B1B
        'danger' => 'bg-status-danger-bg text-status-danger hover:bg-red-200 focus:ring-red-500 font-medium',
        // Ghost: Transparan
        'ghost' => 'bg-transparent text-text-body hover:bg-surface-container-low hover:text-text-slate focus:ring-primary',
    ];

    $sizes = [
        'sm' => 'px-3 py-1.5 text-xs',
        'md' => 'px-4 py-2 text-sm',
        'lg' => 'px-5 py-2.5 text-base',
    ];

    $classes = $baseClasses . ' ' . ($variants[$variant] ?? $variants['primary']) . ' ' . ($sizes[$size] ?? $sizes['md']);
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        @if($icon && $iconPosition === 'left')
            <span class="shrink-0">{!! $icon !!}</span>
        @endif
        <span>{{ $slot }}</span>
        @if($icon && $iconPosition === 'right')
            <span class="shrink-0">{!! $icon !!}</span>
        @endif
    </a>
@else
    <button 
        type="{{ $type }}" 
        {{ $attributes->merge(['class' => $classes]) }}
        @if($disabled) disabled @endif
    >
        @if($loading)
            <x-ui.spinner size="sm" class="shrink-0" />
        @elseif($icon && $iconPosition === 'left')
            <span class="shrink-0">{!! $icon !!}</span>
        @endif

        <span>{{ $slot }}</span>

        @if(!$loading && $icon && $iconPosition === 'right')
            <span class="shrink-0">{!! $icon !!}</span>
        @endif
    </button>
@endif
