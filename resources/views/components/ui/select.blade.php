@props([
    'name' => null,
    'id' => null,
    'label' => null,
    'options' => [], // array key => value atau array objek [['value' => ..., 'label' => ..., 'disabled' => bool]]
    'value' => '',
    'placeholder' => 'Pilih opsi...',
    'allowNew' => false, // jika true, aktifkan mode combobox (bisa ketik opsi baru)
    'searchable' => null, // jika null, auto-true jika opsi >= 6
    'disabled' => false,
    'compact' => false, // ukuran lebih ringkas untuk sel tabel / toolbar filter
    'error' => null,
    'hint' => null,
    'required' => false,
])

@php
    $selectId = $id ?? ($name ?? 'select-' . md5(uniqid()));
    $hasError = $error || ($name && isset($errors) && $errors->has($name));
    $errorMessage = $error ?? (($name && isset($errors)) ? $errors->first($name) : null);
    $wireModelDirective = $attributes->wire('model');
    $hasWireModel = $wireModelDirective && $wireModelDirective->value();

    // Normalisasi options menjadi list [{ value, label, disabled }]
    $normalizedOptions = [];
    foreach ($options as $key => $opt) {
        if (is_array($opt)) {
            $normalizedOptions[] = [
                'value' => (string) ($opt['value'] ?? $key),
                'label' => (string) ($opt['label'] ?? $opt['value'] ?? ''),
                'disabled' => (bool) ($opt['disabled'] ?? false),
            ];
        } else {
            $normalizedOptions[] = [
                'value' => (string) $key,
                'label' => (string) $opt,
                'disabled' => false,
            ];
        }
    }

    $searchEnabled = $searchable ?? (count($normalizedOptions) >= 6);
    $triggerPadding = $compact ? 'px-3 py-2 text-xs' : 'px-4 py-3 text-sm';
@endphp

<div
    class="w-full relative"
    x-data="{
        open: false,
        search: '',
        disabled: {{ $disabled ? 'true' : 'false' }},
        selected: @if($hasWireModel) @entangle($wireModelDirective) @else @js((string) $value) @endif,
        options: @js($normalizedOptions),
        placeholder: @js($placeholder),
        get filteredOptions() {
            if (!this.search) return this.options;
            const q = this.search.toLowerCase();
            return this.options.filter(o => o.label.toLowerCase().includes(q));
        },
        get selectedLabel() {
            const val = this.selected === null || this.selected === undefined ? '' : String(this.selected);
            if (val === '') return this.placeholder;
            const found = this.options.find(o => o.value === val);
            return found ? found.label : val;
        },
        get hasSelection() {
            const val = this.selected === null || this.selected === undefined ? '' : String(this.selected);
            return val !== '';
        },
        select(opt) {
            if (this.disabled || opt.disabled) return;
            this.selected = opt.value;
            this.open = false;
            this.search = '';
            this.$nextTick(() => {
                if (this.$refs.hiddenInput) {
                    this.$refs.hiddenInput.value = opt.value;
                    this.$refs.hiddenInput.dispatchEvent(new Event('input', { bubbles: true }));
                    this.$refs.hiddenInput.dispatchEvent(new Event('change', { bubbles: true }));
                }
            });
        }
    }"
    x-on:click.outside="open = false"
    x-on:keydown.escape.window="open = false"
>
    @if($label)
        <label for="{{ $selectId }}" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
            {{ $label }}
            @if($required)
                <span class="text-rose-600">*</span>
            @endif
        </label>
    @endif

    {{-- Hidden input untuk sinkronisasi form HTML, wire:change, dan aksesibilitas --}}
    <input
        type="hidden"
        x-ref="hiddenInput"
        @if($name) name="{{ $name }}" @endif
        id="{{ $selectId }}"
        :value="selected ?? ''"
        @if($required) required @endif
        {{ $attributes->only(['wire:change']) }}
    />

    @if(!$allowNew)
        <div class="relative">
            {{-- Kotak Trigger Utama --}}
            <button
                type="button"
                @if($disabled) disabled @endif
                x-on:click="if (!disabled) { open = !open; if (open) { $nextTick(() => $refs.searchInput?.focus()); } }"
                aria-haspopup="listbox"
                :aria-expanded="open"
                class="w-full flex items-center justify-between gap-2 rounded-xl {{ $triggerPadding }} bg-white transition duration-150 ease-in-out focus:outline-none select-none"
                :class="{
                    'bg-slate-50 text-slate-400 cursor-not-allowed border border-slate-200': disabled,
                    'cursor-pointer border border-rose-500 ring-2 ring-rose-500/15': !disabled && {{ $hasError ? 'true' : 'false' }},
                    'cursor-pointer border border-[#1D3FA3] ring-2 ring-[#1D3FA3]/10': !disabled && open && !{{ $hasError ? 'true' : 'false' }},
                    'cursor-pointer border border-slate-200 hover:border-slate-300': !disabled && !open && !{{ $hasError ? 'true' : 'false' }}
                }"
            >
                <span
                    class="truncate text-left"
                    :class="hasSelection ? 'text-slate-800 font-medium' : 'text-slate-400 font-normal'"
                    x-text="selectedLabel"
                >{{ $placeholder }}</span>

                <svg
                    class="w-4 h-4 shrink-0 transition-transform duration-200"
                    :class="open ? 'rotate-180 text-[#1D3FA3]' : 'text-slate-500'"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    stroke-width="2"
                >
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                </svg>
            </button>

            {{-- Panel Menu Mengambang (Floating Dropdown Card) --}}
            <div
                x-show="open"
                x-transition:enter="transition ease-out duration-150"
                x-transition:enter-start="opacity-0 -translate-y-1 scale-[0.99]"
                x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                x-transition:leave="transition ease-in duration-100"
                x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                x-transition:leave-end="opacity-0 -translate-y-1 scale-[0.99]"
                class="absolute left-0 right-0 z-50 mt-2 w-full rounded-2xl bg-white border border-slate-100 shadow-[0_10px_30px_-5px_rgba(15,23,42,0.12)] p-2 focus:outline-none"
                style="display: none;"
            >
                @if($searchEnabled)
                    <div class="pb-2 mb-1.5 border-b border-slate-100">
                        <div class="relative">
                            <svg class="w-3.5 h-3.5 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            <input
                                type="search"
                                x-ref="searchInput"
                                x-model="search"
                                x-on:click.stop
                                placeholder="Cari opsi..."
                                class="w-full pl-8 pr-3 py-2 text-xs rounded-xl border border-slate-200 bg-slate-50/70 text-slate-700 placeholder:text-slate-400 focus:bg-white focus:outline-none focus:border-[#1D3FA3] focus:ring-2 focus:ring-[#1D3FA3]/10 transition"
                            />
                        </div>
                    </div>
                @endif

                <div class="max-h-60 overflow-y-auto dropdown-scrollbar space-y-0.5 pr-1" role="listbox">
                    <template x-for="opt in filteredOptions" :key="opt.value">
                        <button
                            type="button"
                            role="option"
                            :disabled="opt.disabled"
                            x-on:click="select(opt)"
                            class="w-full flex items-center justify-between gap-2 px-3.5 py-2.5 rounded-xl text-sm text-left transition-colors duration-150 select-none"
                            :class="{
                                'opacity-50 cursor-not-allowed text-slate-400': opt.disabled,
                                'bg-[#EBF2FF] text-[#1D3FA3] font-semibold cursor-pointer': !opt.disabled && String(selected ?? '') === opt.value,
                                'text-slate-700 hover:bg-[#EEF3FF] hover:text-[#1D3FA3] cursor-pointer': !opt.disabled && String(selected ?? '') !== opt.value
                            }"
                        >
                            <span x-text="opt.label" class="truncate"></span>
                            <svg
                                x-show="String(selected ?? '') === opt.value"
                                class="w-4 h-4 text-[#1D3FA3] shrink-0"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                stroke-width="2.2"
                            >
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                        </button>
                    </template>

                    <div x-show="filteredOptions.length === 0" class="px-3.5 py-3 text-xs text-slate-400 text-center">
                        Opsi tidak ditemukan.
                    </div>
                </div>
            </div>
        </div>
    @else
        {{-- Combobox Mode: Bisa pilih atau ketik baru --}}
        <div class="relative">
            <div class="relative">
                <input
                    type="text"
                    id="{{ $selectId }}-input"
                    x-model="selected"
                    x-on:focus="open = true"
                    placeholder="{{ $placeholder }}"
                    autocomplete="off"
                    class="block w-full rounded-xl {{ $triggerPadding }} pr-10 bg-white text-slate-800 border border-slate-200 focus:border-[#1D3FA3] focus:ring-2 focus:ring-[#1D3FA3]/10 focus:outline-none transition"
                    {{ $attributes->except(['wire:model', 'wire:model.live', 'wire:change']) }}
                />
                <button
                    type="button"
                    x-on:click="open = !open"
                    class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-slate-500 hover:text-[#1D3FA3] cursor-pointer"
                >
                    <svg class="w-4 h-4 transition-transform duration-200" :class="open ? 'rotate-180 text-[#1D3FA3]' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
            </div>

            <div
                x-show="open"
                x-transition
                class="absolute left-0 right-0 z-50 mt-2 w-full rounded-2xl bg-white border border-slate-100 shadow-[0_10px_30px_-5px_rgba(15,23,42,0.12)] p-2 focus:outline-none"
                style="display: none;"
            >
                <div class="max-h-60 overflow-y-auto dropdown-scrollbar space-y-0.5 pr-1">
                    <template x-for="opt in options" :key="opt.value">
                        <button
                            type="button"
                            x-show="!selected || opt.label.toLowerCase().includes(String(selected).toLowerCase())"
                            x-on:click="selected = opt.label; open = false"
                            class="w-full flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm text-left cursor-pointer transition-colors duration-150"
                            :class="String(selected ?? '') === opt.label ? 'bg-[#EBF2FF] text-[#1D3FA3] font-semibold' : 'text-slate-700 hover:bg-[#EEF3FF] hover:text-[#1D3FA3]'"
                        >
                            <span x-text="opt.label" class="truncate"></span>
                            <svg x-show="String(selected ?? '') === opt.label" class="w-4 h-4 text-[#1D3FA3] shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                        </button>
                    </template>

                    <button
                        type="button"
                        x-show="selected && !options.some(o => o.label.toLowerCase() === String(selected).toLowerCase())"
                        x-on:click="open = false"
                        class="w-full text-left cursor-pointer px-3.5 py-2.5 rounded-xl bg-[#EEF3FF] text-[#1D3FA3] font-medium flex items-center gap-1.5 text-sm"
                    >
                        <span class="text-xs">Gunakan opsi baru:</span>
                        <strong x-text="`&ldquo;${selected}&rdquo;`"></strong>
                    </button>
                </div>
            </div>
        </div>
    @endif

    @if($hasError)
        <p class="mt-1.5 text-xs text-rose-600 flex items-center gap-1 font-medium">
            <svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
            </svg>
            <span>{{ $errorMessage }}</span>
        </p>
    @elseif($hint)
        <p class="mt-1 text-xs text-slate-500">{{ $hint }}</p>
    @endif
</div>
