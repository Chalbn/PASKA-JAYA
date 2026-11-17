@props([
    'name' => 'confirm-modal',
    'title' => 'Konfirmasi Tindakan',
    'message' => 'Apakah Anda yakin ingin melanjutkan tindakan ini? Tindakan ini tidak dapat dibatalkan.',
    'confirmText' => 'Ya, hapus',
    'cancelText' => 'Batal',
    'confirmAction' => null,
    'variant' => 'danger', // danger, warning, primary
])

<x-ui.modal :name="$name" :title="$title" maxWidth="sm">
    <div class="flex items-start gap-4">
        <div class="shrink-0 p-3 rounded-2xl {{ $variant === 'danger' ? 'bg-status-danger-bg text-status-danger' : 'bg-status-warning-bg text-status-warning' }}">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
        </div>
        <div class="flex-1">
            <p class="text-sm text-text-body leading-relaxed">
                {{ $message }}
            </p>
            @if(isset($slot) && $slot->isNotEmpty())
                <div class="mt-3">
                    {{ $slot }}
                </div>
            @endif
        </div>
    </div>

    <x-slot:footer>
        <x-ui.button variant="outline" size="sm" x-on:click="open = false">
            {{ $cancelText }}
        </x-ui.button>
        @if($confirmAction)
            <x-ui.button 
                :variant="$variant === 'danger' ? 'danger' : 'primary'" 
                size="sm"
                wire:click="{{ $confirmAction }}"
                x-on:click="open = false"
            >
                {{ $confirmText }}
            </x-ui.button>
        @else
            <x-ui.button 
                :variant="$variant === 'danger' ? 'danger' : 'primary'" 
                size="sm"
                x-on:click="open = false"
            >
                {{ $confirmText }}
            </x-ui.button>
        @endif
    </x-slot:footer>
</x-ui.modal>
