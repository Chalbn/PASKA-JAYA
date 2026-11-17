@props([
    'title' => 'Belum ada data',
    'description' => 'Data belum tersedia atau database masih kosong.',
    'icon' => null,
    'actionLabel' => null,
    'actionRoute' => null,
    'actionEvent' => null,
])

<div {{ $attributes->merge(['class' => 'text-center py-12 px-4 rounded-2xl bg-surface-canvas/50 border border-dashed border-surface-border']) }}>
    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-surface-container text-primary shadow-xs mb-4">
        @if($icon)
            {!! $icon !!}
        @else
            <svg class="h-7 w-7 text-primary/80" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
            </svg>
        @endif
    </div>

    <h3 class="text-sm font-bold text-text-slate tracking-tight">{{ $title }}</h3>
    <p class="mt-1 text-xs text-text-muted max-w-sm mx-auto leading-relaxed">{{ $description }}</p>

    @if($actionLabel || isset($action))
        <div class="mt-5">
            @if(isset($action))
                {{ $action }}
            @elseif($actionRoute)
                <x-ui.button variant="primary" size="sm" :href="$actionRoute">
                    {{ $actionLabel }}
                </x-ui.button>
            @elseif($actionEvent)
                <x-ui.button variant="primary" size="sm" wire:click="{{ $actionEvent }}">
                    {{ $actionLabel }}
                </x-ui.button>
            @else
                <x-ui.button variant="primary" size="sm">
                    {{ $actionLabel }}
                </x-ui.button>
            @endif
        </div>
    @endif
</div>
