@props([
    'name' => null,
    'id' => null,
    'label' => null,
    'options' => [], // array key => value atau array objek
    'placeholder' => 'Pilih opsi...',
    'allowNew' => false, // jika true, aktifkan mode combobox (bisa ketik opsi baru)
    'error' => null,
    'hint' => null,
    'required' => false,
])

@php
    $selectId = $id ?? ($name ?? 'select-' . md5(uniqid()));
    $hasError = $error || ($name && isset($errors) && $errors->has($name));
    $errorMessage = $error ?? (($name && isset($errors)) ? $errors->first($name) : null);
    $wireModel = $attributes->whereStartsWith('wire:model')->first();
@endphp

<div class="w-full" @if($allowNew) x-data="{
    open: false,
    query: '',
    options: {{ json_encode($options) }},
    get filteredOptions() {
        if (!this.query) return this.options;
        return Object.entries(this.options).filter(([k, v]) => 
            v.toLowerCase().includes(this.query.toLowerCase()) || 
            k.toLowerCase().includes(this.query.toLowerCase())
        );
    },
    selectOption(val) {
        this.query = val;
        this.open = false;
        $dispatch('input', val);
    }
}" @endif>
    @if($label)
        <label for="{{ $selectId }}" class="block text-xs font-semibold uppercase tracking-wider text-text-slate mb-1.5">
            {{ $label }}
            @if($required)
                <span class="text-status-danger">*</span>
            @endif
        </label>
    @endif

    @if(!$allowNew)
        {{-- Standard Native Select --}}
        <div class="relative rounded-xl shadow-xs">
            <select 
                name="{{ $name }}" 
                id="{{ $selectId }}"
                @if($required) required @endif
                {{ $attributes->merge([
                    'class' => 'block w-full appearance-none rounded-xl text-sm text-text-slate py-2.5 pl-3.5 pr-10 transition duration-150 ease-in-out '
                        . ($hasError 
                            ? 'border-status-danger text-status-danger focus:border-status-danger focus:ring-status-danger/30 ' 
                            : 'border-surface-border hover:border-surface-border-subtle focus:border-primary focus:ring-primary/20 ')
                        . 'bg-surface-card border shadow-xs focus:outline-none focus:ring-2'
                ]) }}
            >
                @if($placeholder)
                    <option value="" disabled selected class="text-text-muted">{{ $placeholder }}</option>
                @endif

                @if(!empty($options))
                    @foreach($options as $val => $text)
                        <option value="{{ $val }}">{{ $text }}</option>
                    @endforeach
                @else
                    {{ $slot }}
                @endif
            </select>

            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3.5 text-text-muted">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </div>
        </div>
    @else
        {{-- Combobox Mode: Bisa pilih atau ketik baru --}}
        <div class="relative">
            <div class="relative rounded-xl shadow-xs">
                <input 
                    type="text"
                    id="{{ $selectId }}"
                    name="{{ $name }}"
                    x-model="query"
                    x-on:focus="open = true"
                    x-on:click.outside="open = false"
                    placeholder="{{ $placeholder }}"
                    autocomplete="off"
                    class="block w-full rounded-xl text-sm text-text-slate py-2.5 pl-3.5 pr-10 bg-surface-card border border-surface-border focus:border-primary focus:ring-2 focus:ring-primary/20 focus:outline-none shadow-xs"
                    {{ $attributes->except(['wire:model', 'wire:model.live']) }}
                />
                <button 
                    type="button" 
                    x-on:click="open = !open" 
                    class="absolute inset-y-0 right-0 flex items-center pr-3 text-text-muted hover:text-text-slate"
                >
                    <svg class="w-4 h-4 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
            </div>

            {{-- Dropdown options --}}
            <div 
                x-show="open" 
                x-transition 
                class="absolute z-30 mt-1 max-h-60 w-full overflow-auto rounded-xl bg-surface-card py-1 text-sm shadow-lg border border-surface-border focus:outline-none"
                style="display: none;"
            >
                <template x-for="[key, value] in Object.entries(options)" :key="key">
                    <div 
                        x-show="!query || value.toLowerCase().includes(query.toLowerCase())"
                        x-on:click="selectOption(value)"
                        class="cursor-pointer px-4 py-2 hover:bg-surface-container-low text-text-slate flex items-center justify-between"
                    >
                        <span x-text="value"></span>
                        <span x-show="query === value" class="text-primary font-bold">✓</span>
                    </div>
                </template>

                {{-- Option to use custom value if query is not in list --}}
                <div 
                    x-show="query && !Object.values(options).some(v => v.toLowerCase() === query.toLowerCase())"
                    x-on:click="open = false"
                    class="cursor-pointer px-4 py-2 bg-amber-50/60 hover:bg-amber-100/60 text-secondary-dark border-t border-surface-border font-medium flex items-center gap-1.5"
                >
                    <span class="text-xs">Gunakan opsi baru:</span>
                    <strong x-text="`&ldquo;${query}&rdquo;`"></strong>
                </div>
            </div>
        </div>
    @endif

    @if($hasError)
        <p class="mt-1.5 text-xs text-status-danger flex items-center gap-1 font-medium">
            <svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
            </svg>
            <span>{{ $errorMessage }}</span>
        </p>
    @elseif($hint)
        <p class="mt-1 text-xs text-text-muted">{{ $hint }}</p>
    @endif
</div>
