@props([
    'name' => 'photo',
    'id' => null,
    'label' => 'Unggah Foto Bukti',
    'hint' => 'Format JPG, PNG hingga 5MB. Pada HP Anda dapat langsung membuka kamera.',
    'camera' => true, // Dukungan capture="environment" untuk kamera HP
    'preview' => null, // URL foto existing jika ada
    'error' => null,
    'required' => false,
])

@php
    $uploadId = $id ?? ($name ?? 'upload-' . md5(uniqid()));
    $hasError = $error || ($name && isset($errors) && $errors->has($name));
    $errorMessage = $error ?? (($name && isset($errors)) ? $errors->first($name) : null);
@endphp

<div 
    x-data="{
        previewUrl: '{{ $preview }}',
        hasFile: {{ $preview ? 'true' : 'false' }},
        fileName: '',
        handleFileChange(e) {
            const file = e.target.files[0];
            if (file) {
                this.fileName = file.name;
                this.previewUrl = URL.createObjectURL(file);
                this.hasFile = true;
            }
        },
        removeFile() {
            this.previewUrl = '';
            this.hasFile = false;
            this.fileName = '';
            const input = document.getElementById('{{ $uploadId }}');
            if (input) input.value = '';
            $dispatch('file-removed');
        }
    }" 
    class="w-full"
>
    @if($label)
        <label class="block text-xs font-semibold uppercase tracking-wider text-text-slate mb-1.5">
            {{ $label }}
            @if($required)
                <span class="text-status-danger">*</span>
            @endif
        </label>
    @endif

    {{-- Dropzone & Preview Box --}}
    <div class="relative rounded-2xl border-2 border-dashed {{ $hasError ? 'border-status-danger bg-red-50/20' : 'border-surface-border-subtle bg-surface-canvas/40 hover:bg-surface-canvas/80' }} p-5 text-center transition">
        {{-- Preview Box if image exists --}}
        <div x-show="hasFile" class="space-y-3" style="display: none;">
            <div class="relative inline-block max-w-xs mx-auto overflow-hidden rounded-xl border border-surface-border shadow-xs">
                <img :src="previewUrl" alt="Pratinjau" class="max-h-48 w-auto object-contain mx-auto" />
                <button 
                    type="button" 
                    x-on:click="removeFile()"
                    class="absolute top-2 right-2 rounded-full bg-status-danger text-white p-1 shadow-md hover:bg-red-700 transition"
                    title="Hapus foto"
                >
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <p x-show="fileName" x-text="fileName" class="text-xs text-text-muted truncate max-w-xs mx-auto"></p>
        </div>

        {{-- Upload Controls when no file selected --}}
        <div x-show="!hasFile" class="space-y-2">
            <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-xl bg-surface-container text-primary">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
            </div>

            <div class="flex flex-col sm:flex-row items-center justify-center gap-2 pt-1">
                {{-- Standard File Picker --}}
                <label for="{{ $uploadId }}" class="cursor-pointer inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl bg-primary text-primary-on text-xs font-semibold hover:bg-primary-deep transition shadow-xs">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                    </svg>
                    <span>Pilih Berkas</span>
                </label>

                @if($camera)
                    {{-- Direct Camera Trigger for HP/Mobile --}}
                    <label for="{{ $uploadId }}-cam" class="cursor-pointer inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl bg-secondary text-secondary-on text-xs font-bold hover:bg-secondary-container transition shadow-xs">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                        </svg>
                        <span>Buka Kamera</span>
                    </label>
                @endif
            </div>

            <p class="text-xs text-text-muted mt-2">{{ $hint }}</p>
        </div>

        {{-- Hidden Input Elements --}}
        <input 
            type="file" 
            id="{{ $uploadId }}" 
            name="{{ $name }}" 
            accept="image/*" 
            x-on:change="handleFileChange($event)"
            class="hidden"
            {{ $attributes }}
        />

        @if($camera)
            <input 
                type="file" 
                id="{{ $uploadId }}-cam" 
                accept="image/*" 
                capture="environment" 
                x-on:change="handleFileChange($event)"
                class="hidden"
            />
        @endif
    </div>

    @if($hasError)
        <p class="mt-1.5 text-xs text-status-danger flex items-center gap-1 font-medium">
            <svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
            </svg>
            <span>{{ $errorMessage }}</span>
        </p>
    @endif
</div>
