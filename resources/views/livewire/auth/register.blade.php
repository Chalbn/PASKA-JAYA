<div class="space-y-5" x-data="{ tampilPassword: false, tampilKonfirmasi: false }">
    {{-- Tombol kembali (←) ke Login --}}
    <div>
        <a href="{{ route('login') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-text-muted hover:text-primary transition group">
            <svg class="w-4 h-4 transition-transform group-hover:-translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            <span>Back to Login</span>
        </a>
    </div>

    <div>
        <h2 class="text-2xl font-black text-text-slate tracking-tight">Create Account</h2>
        <p class="mt-1 text-xs text-text-muted">
            Fill in your details to get started.
            @if (config('auth.otp_enabled'))
                We'll verify your account with a one-time code.
            @endif
        </p>
    </div>

    <form wire:submit="daftar" class="space-y-3.5" novalidate>
        <x-ui.input
            name="nama"
            wire:model.blur="nama"
            label="Full Name"
            placeholder="Budi Santoso"
            autocomplete="name"
            :required="true"
        >
            <x-slot:leftIcon>
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
            </x-slot:leftIcon>
        </x-ui.input>

        <x-ui.input
            name="email"
            type="email"
            wire:model.blur="email"
            label="Email"
            placeholder="name@toko.com"
            autocomplete="email"
            :required="true"
        >
            <x-slot:leftIcon>
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
            </x-slot:leftIcon>
        </x-ui.input>

        <x-ui.input
            name="no_hp"
            type="tel"
            inputmode="tel"
            wire:model.blur="no_hp"
            label="Phone Number"
            placeholder="0812 3456 7890"
            autocomplete="tel"
            :required="true"
        >
            <x-slot:leftIcon>
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                </svg>
            </x-slot:leftIcon>
        </x-ui.input>

        <x-ui.input
            name="password"
            type="password"
            x-bind:type="tampilPassword ? 'text' : 'password'"
            wire:model.blur="password"
            label="Password"
            placeholder="Minimal 8 karakter"
            autocomplete="new-password"
            :required="true"
        >
            <x-slot:leftIcon>
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
            </x-slot:leftIcon>
            <x-slot:rightSlot>
                <button type="button" x-on:click="tampilPassword = ! tampilPassword"
                    class="pointer-events-auto cursor-pointer text-text-muted hover:text-primary transition focus:outline-none focus:text-primary"
                    x-bind:aria-label="tampilPassword ? 'Hide password' : 'Show password'">
                    <svg x-show="! tampilPassword" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    <svg x-show="tampilPassword" style="display: none;" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                    </svg>
                </button>
            </x-slot:rightSlot>
        </x-ui.input>

        <x-ui.input
            name="password_confirmation"
            type="password"
            x-bind:type="tampilKonfirmasi ? 'text' : 'password'"
            wire:model.blur="password_confirmation"
            label="Confirm Password"
            placeholder="Ulangi password Anda"
            autocomplete="new-password"
            :required="true"
        >
            <x-slot:leftIcon>
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
            </x-slot:leftIcon>
            <x-slot:rightSlot>
                <button type="button" x-on:click="tampilKonfirmasi = ! tampilKonfirmasi"
                    class="pointer-events-auto cursor-pointer text-text-muted hover:text-primary transition focus:outline-none focus:text-primary"
                    x-bind:aria-label="tampilKonfirmasi ? 'Hide password' : 'Show password'">
                    <svg x-show="! tampilKonfirmasi" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    <svg x-show="tampilKonfirmasi" style="display: none;" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                    </svg>
                </button>
            </x-slot:rightSlot>
        </x-ui.input>

        {{-- Terms & Privacy (wajib) --}}
        <div class="pt-1">
            <label class="flex items-start gap-2.5 cursor-pointer select-none text-xs">
                <input
                    type="checkbox"
                    name="terms"
                    wire:model="terms"
                    class="mt-0.5 rounded border-surface-border text-primary focus:ring-primary h-4 w-4"
                />
                <span class="text-text-body leading-relaxed">
                    I agree to the <span class="font-semibold text-primary">Terms &amp; Conditions</span> and <span class="font-semibold text-primary">Privacy Policy</span>.
                </span>
            </label>
            @error('terms')
                <p class="mt-1.5 text-xs text-status-danger font-medium">{{ $message }}</p>
            @enderror
        </div>

        {{-- Register nonaktif sampai semua field valid; validasi sebenarnya tetap di server --}}
        <div class="pt-2">
            <x-ui.button
                type="submit"
                variant="primary"
                class="w-full justify-center py-2.5"
                x-bind:disabled="! ($wire.nama && $wire.email && $wire.no_hp && ($wire.password || '').length >= 8 && $wire.password === $wire.password_confirmation && $wire.terms)"
                wire:loading.attr="disabled"
                wire:target="daftar"
            >
                <span wire:loading.remove wire:target="daftar">Register</span>
                <span wire:loading wire:target="daftar">Memproses...</span>
            </x-ui.button>
        </div>
    </form>

    <div class="text-center pt-3 border-t border-surface-border text-xs text-text-muted">
        <span>Already have an account?</span>
        <a href="{{ route('login') }}" class="font-bold text-primary hover:underline ml-1">Login</a>
    </div>
</div>
