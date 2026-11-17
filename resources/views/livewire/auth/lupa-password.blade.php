<div class="space-y-5">
    <div>
        <a href="{{ route('login') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-text-muted hover:text-primary transition group">
            <svg class="w-4 h-4 transition-transform group-hover:-translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            <span>Back to Login</span>
        </a>
    </div>

    @if ($token === null)
        {{-- Minta tautan reset --}}
        <div>
            <h2 class="text-2xl font-black text-text-slate tracking-tight">Forgot password?</h2>
            <p class="mt-1 text-xs text-text-muted">Enter your email and we'll send you a link to reset your password.</p>
        </div>

        @if ($terkirim)
            <div class="rounded-xl border border-emerald-200 bg-status-success-bg p-4 text-xs text-status-success font-medium leading-relaxed" role="status">
                Jika email tersebut terdaftar, tautan reset password sudah kami kirim. Periksa kotak masuk (dan folder spam) Anda.
            </div>
        @else
            <form wire:submit="kirimTautan" class="space-y-4" novalidate>
                <x-ui.input
                    name="email"
                    type="email"
                    wire:model="email"
                    label="Email"
                    placeholder="name@toko.com"
                    autocomplete="email"
                    autofocus
                    :required="true"
                >
                    <x-slot:leftIcon>
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    </x-slot:leftIcon>
                </x-ui.input>

                <x-ui.button type="submit" variant="primary" class="w-full justify-center py-2.5" wire:loading.attr="disabled" wire:target="kirimTautan">
                    <span wire:loading.remove wire:target="kirimTautan">Send reset link</span>
                    <span wire:loading wire:target="kirimTautan">Memproses...</span>
                </x-ui.button>
            </form>
        @endif
    @else
        {{-- Set password baru --}}
        <div x-data="{ tampil: false }">
            <h2 class="text-2xl font-black text-text-slate tracking-tight">Reset password</h2>
            <p class="mt-1 text-xs text-text-muted">Choose a new password for your account.</p>

            <form wire:submit="resetPassword" class="mt-5 space-y-4" novalidate>
                <x-ui.input
                    name="email"
                    type="email"
                    wire:model="email"
                    label="Email"
                    autocomplete="email"
                    :required="true"
                />

                <x-ui.input
                    name="password"
                    type="password"
                    x-bind:type="tampil ? 'text' : 'password'"
                    wire:model="password"
                    label="New password"
                    placeholder="Minimal 8 karakter"
                    autocomplete="new-password"
                    :required="true"
                >
                    <x-slot:rightSlot>
                        <button type="button" x-on:click="tampil = ! tampil"
                            class="pointer-events-auto cursor-pointer text-text-muted hover:text-primary transition focus:outline-none focus:text-primary"
                            x-bind:aria-label="tampil ? 'Hide password' : 'Show password'">
                            <svg x-show="! tampil" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <svg x-show="tampil" style="display: none;" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                            </svg>
                        </button>
                    </x-slot:rightSlot>
                </x-ui.input>

                <x-ui.input
                    name="password_confirmation"
                    type="password"
                    x-bind:type="tampil ? 'text' : 'password'"
                    wire:model="password_confirmation"
                    label="Confirm new password"
                    placeholder="Ulangi password baru"
                    autocomplete="new-password"
                    :required="true"
                />

                <x-ui.button type="submit" variant="primary" class="w-full justify-center py-2.5" wire:loading.attr="disabled" wire:target="resetPassword">
                    <span wire:loading.remove wire:target="resetPassword">Reset password</span>
                    <span wire:loading wire:target="resetPassword">Memproses...</span>
                </x-ui.button>
            </form>
        </div>
    @endif
</div>
