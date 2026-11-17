@props([
    'name' => null,
    'id' => null,
    'label' => null,
    'type' => 'text',
    'placeholder' => null,
    'icon' => null,
    'suffix' => null,
    'error' => null,
    'hint' => null,
    'required' => false,
])

@php
    $inputId = $id ?? ($name ?? 'input-' . md5(uniqid()));
    $hasError = $error || ($name && isset($errors) && $errors->has($name));
    $errorMessage = $error ?? (($name && isset($errors)) ? $errors->first($name) : null);
@endphp

<div class="w-full">
    @if($label)
        <label for="{{ $inputId }}" class="block text-xs font-semibold uppercase tracking-wider text-text-slate mb-1.5">
            {{ $label }}
            @if($required)
                <span class="text-status-danger">*</span>
            @endif
        </label>
    @endif

    <div class="relative rounded-xl shadow-xs">
        @if($icon || isset($leftIcon))
            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-text-muted">
                @if(isset($leftIcon))
                    {{ $leftIcon }}
                @else
                    {!! $icon !!}
                @endif
            </div>
        @endif

        <input 
            type="{{ $type }}" 
            name="{{ $name }}" 
            id="{{ $inputId }}"
            placeholder="{{ $placeholder }}"
            @if($required) required @endif
            {{ $attributes->merge([
                'class' => 'block w-full rounded-xl text-sm text-text-slate placeholder:text-text-muted/60 transition duration-150 ease-in-out ' 
                    . ($icon || isset($leftIcon) ? 'pl-10 ' : 'pl-3.5 ')
                    . ($suffix || isset($rightSlot) ? 'pr-12 ' : 'pr-3.5 ')
                    . ($hasError 
                        ? 'border-status-danger text-status-danger focus:border-status-danger focus:ring-status-danger/30 ' 
                        : 'border-surface-border hover:border-surface-border-subtle focus:border-primary focus:ring-primary/20 ')
                    . 'bg-surface-card border py-2.5 shadow-xs focus:outline-none focus:ring-2'
            ]) }}
        />

        @if($suffix || isset($rightSlot))
            <div class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-xs font-semibold text-text-muted pointer-events-none">
                @if(isset($rightSlot))
                    {{ $rightSlot }}
                @else
                    {{ $suffix }}
                @endif
            </div>
        @endif
    </div>

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
