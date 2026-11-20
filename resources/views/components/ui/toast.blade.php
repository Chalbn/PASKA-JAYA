<div
    x-data="{
        toasts: [],
        add(detail) {
            const payload = Array.isArray(detail) ? (detail[0] ?? {}) : (detail ?? {});
            const message = typeof payload === 'string' ? payload : (payload.message || '');
            if (!message) return;

            const id = Date.now() + Math.random();
            const toast = {
                id: id,
                message: message,
                type: payload.type || 'success', // success, info, warning, error
                timeout: payload.timeout || 3500,
            };
            this.toasts.push(toast);
            setTimeout(() => this.remove(id), toast.timeout);
        },
        remove(id) {
            this.toasts = this.toasts.filter(t => t.id !== id);
        },
        init() {
            @if(session()->has('success'))
                this.add({ message: @js(session('success')), type: 'success' });
            @endif
            @if(session()->has('status'))
                this.add({ message: @js(session('status')), type: 'info' });
            @endif
            @if(session()->has('info'))
                this.add({ message: @js(session('info')), type: 'info' });
            @endif
            @if(session()->has('warning'))
                this.add({ message: @js(session('warning')), type: 'warning' });
            @endif
            @if(session()->has('error'))
                this.add({ message: @js(session('error')), type: 'error' });
            @endif

            if (window.Livewire && typeof window.Livewire.on === 'function') {
                window.Livewire.on('notify', (data) => this.add(data));
            }
        }
    }"
    x-on:toast.window="add($event.detail)"
    x-on:notify.window="add($event.detail)"
    class="fixed top-20 right-5 z-50 flex flex-col gap-2.5 pointer-events-none"
    aria-live="assertive"
>
    <template x-for="t in toasts" :key="t.id">
        <div
            x-show="true"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-x-4 scale-95"
            x-transition:enter-end="opacity-100 translate-x-0 scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 translate-x-4 scale-95"
            class="pointer-events-auto relative overflow-hidden bg-white/95 backdrop-blur-xs border border-slate-200/80 rounded-2xl shadow-lg px-4 py-3.5 w-80"
            role="alert"
        >
            <div class="flex items-start justify-between gap-3">
                <div class="flex items-start gap-3 min-w-0">
                    {{-- Ikon Status --}}
                    <div
                        class="w-8 h-8 rounded-xl flex items-center justify-center shrink-0 mt-0.5"
                        :class="{
                            'bg-emerald-50 text-emerald-600': t.type === 'success',
                            'bg-[#EBF2FF] text-[#1D3FA3]': t.type === 'info',
                            'bg-amber-50 text-amber-600': t.type === 'warning',
                            'bg-rose-50 text-rose-600': t.type === 'error',
                        }"
                    >
                        <template x-if="t.type === 'success'">
                            <svg class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                        </template>
                        <template x-if="t.type === 'info'">
                            <svg class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </template>
                        <template x-if="t.type === 'warning'">
                            <svg class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </template>
                        <template x-if="t.type === 'error'">
                            <svg class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </template>
                    </div>

                    {{-- Pesan --}}
                    <div class="min-w-0 pt-0.5">
                        <p class="text-xs font-semibold leading-relaxed text-slate-700 break-words" x-text="t.message"></p>
                    </div>
                </div>

                {{-- Tombol Tutup --}}
                <button
                    type="button"
                    x-on:click="remove(t.id)"
                    class="shrink-0 text-slate-400 hover:text-slate-700 hover:bg-slate-100 p-1 rounded-lg transition cursor-pointer"
                    aria-label="Tutup notifikasi"
                >
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            {{-- Progress Bar Tipis Auto-Close (3.5 detik) --}}
            <div class="absolute bottom-0 left-0 right-0 h-[3px] bg-slate-100">
                <div
                    class="h-full animate-toast-progress"
                    :class="{
                        'bg-emerald-500': t.type === 'success',
                        'bg-[#1D3FA3]': t.type === 'info',
                        'bg-amber-500': t.type === 'warning',
                        'bg-rose-500': t.type === 'error',
                    }"
                ></div>
            </div>
        </div>
    </template>
</div>
