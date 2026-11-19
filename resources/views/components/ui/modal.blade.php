@props([
    'name' => 'modal',
    'title' => null,
    'maxWidth' => 'md', // sm, md, lg, xl, 2xl
    'show' => false,
])

@php
    $maxWidthClasses = [
        'sm' => 'sm:max-w-sm',
        'md' => 'sm:max-w-md',
        'lg' => 'sm:max-w-lg',
        'xl' => 'sm:max-w-xl',
        '2xl' => 'sm:max-w-2xl',
    ][$maxWidth] ?? 'sm:max-w-md';

    $wireModel = $attributes->whereStartsWith('wire:model')->first();
@endphp

<div
    x-data="{ open: {{ $show ? 'true' : 'false' }} }"
    x-show="open"
    x-on:open-modal.window="if ($event.detail === '{{ $name }}') open = true"
    x-on:close-modal.window="if ($event.detail === '{{ $name }}') open = false"
    x-on:keydown.escape.window="open = false"
    class="relative z-50"
    style="display: none;"
    aria-labelledby="modal-title"
    role="dialog"
    aria-modal="true"
>
    {{-- Backdrop --}}
    <div 
        x-show="open"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-[#0F172A]/60 backdrop-blur-xs transition-opacity"
        x-on:click="open = false"
    ></div>

    <div class="fixed inset-0 z-10 overflow-y-auto p-4 sm:p-6 flex min-h-full items-end sm:items-center justify-center">
        <div 
            x-show="open"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            class="relative transform overflow-hidden rounded-2xl bg-surface-card border border-surface-border text-left shadow-xl transition-all w-full {{ $maxWidthClasses }}"
        >
            {{-- Header: Judul Kiri + X Kanan --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-surface-border">
                <h3 class="text-base font-bold text-text-slate tracking-tight" id="modal-title">
                    {{ $title }}
                </h3>
                <button 
                    type="button" 
                    x-on:click="open = false"
                    class="rounded-lg p-1.5 text-text-muted hover:text-text-slate hover:bg-surface-container-low transition"
                    aria-label="Tutup modal"
                >
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            {{-- Body --}}
            <div class="px-6 py-5">
                {{ $slot }}
            </div>

            {{-- Footer: Batal (outline) + Simpan (primary) bila tidak dioverride --}}
            @if(isset($footer))
                <div class="px-6 py-4 bg-surface-canvas/50 border-t border-surface-border flex items-center justify-end gap-3">
                    {{ $footer }}
                </div>
            @endif
        </div>
    </div>
</div>
