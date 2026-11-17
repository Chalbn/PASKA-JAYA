<div 
    x-data="{
        toasts: [],
        add(detail) {
            const id = Date.now() + Math.random();
            const toast = {
                id: id,
                message: typeof detail === 'string' ? detail : (detail.message || ''),
                type: detail.type || 'success', // success, error, warning, info
                timeout: detail.timeout || 4000,
            };
            this.toasts.push(toast);
            setTimeout(() => this.remove(id), toast.timeout);
        },
        remove(id) {
            this.toasts = this.toasts.filter(t => t.id !== id);
        }
    }"
    x-init="
        @if(session()->has('success'))
            add({ message: '{{ addslashes(session('success')) }}', type: 'success' });
        @endif
        @if(session()->has('error'))
            add({ message: '{{ addslashes(session('error')) }}', type: 'error' });
        @endif
        @if(session()->has('warning'))
            add({ message: '{{ addslashes(session('warning')) }}', type: 'warning' });
        @endif
    "
    x-on:toast.window="add($event.detail)"
    class="fixed bottom-5 right-5 z-50 flex flex-col gap-2.5 max-w-sm w-full pointer-events-none"
    aria-live="assertive"
>
    <template x-for="t in toasts" :key="t.id">
        <div 
            x-show="true"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-3 scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 translate-y-2 scale-95"
            :class="{
                'bg-surface-card border-status-success text-text-slate border-l-4': t.type === 'success',
                'bg-surface-card border-status-danger text-text-slate border-l-4': t.type === 'error',
                'bg-surface-card border-status-warning text-text-slate border-l-4': t.type === 'warning',
                'bg-surface-card border-primary text-text-slate border-l-4': t.type === 'info',
            }"
            class="pointer-events-auto p-4 rounded-xl shadow-lg border border-surface-border flex items-start justify-between gap-3"
            role="alert"
        >
            <div class="flex items-start gap-2.5">
                {{-- Icon --}}
                <div class="shrink-0 mt-0.5">
                    <template x-if="t.type === 'success'">
                        <span class="text-status-success">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                            </svg>
                        </span>
                    </template>
                    <template x-if="t.type === 'error'">
                        <span class="text-status-danger">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </span>
                    </template>
                    <template x-if="t.type === 'warning'">
                        <span class="text-status-warning">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </span>
                    </template>
                    <template x-if="t.type === 'info'">
                        <span class="text-primary">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </span>
                    </template>
                </div>

                {{-- Text Message --}}
                <div class="text-xs font-semibold leading-relaxed text-text-slate" x-text="t.message"></div>
            </div>

            {{-- Close Button --}}
            <button 
                type="button" 
                x-on:click="remove(t.id)"
                class="shrink-0 text-text-muted hover:text-text-slate p-0.5 rounded-lg transition"
                aria-label="Tutup notifikasi"
            >
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </template>
</div>
