@props([
    'title' => null,
    'subtitle' => null,
    'action' => null,
    'padding' => 'p-5 sm:p-6',
    'rounded' => 'rounded-2xl',
])

<div {{ $attributes->merge(['class' => "bg-surface-card border border-surface-border {$rounded} shadow-xs transition"]) }}>
    @if($title || isset($header) || $action || $subtitle)
        <div class="px-5 py-4 sm:px-6 border-b border-surface-border flex items-center justify-between gap-4">
            @if(isset($header))
                {{ $header }}
            @else
                <div>
                    @if($title)
                        <h3 class="text-base font-bold text-text-slate tracking-tight">{{ $title }}</h3>
                    @endif
                    @if($subtitle)
                        <p class="text-xs text-text-muted mt-0.5">{{ $subtitle }}</p>
                    @endif
                </div>
                @if($action)
                    <div class="shrink-0">
                        {{ $action }}
                    </div>
                @endif
            @endif
        </div>
    @endif

    <div class="{{ $padding }}">
        {{ $slot }}
    </div>

    @if(isset($footer))
        <div class="px-5 py-3 sm:px-6 bg-surface-canvas/50 border-t border-surface-border rounded-b-2xl">
            {{ $footer }}
        </div>
    @endif
</div>
