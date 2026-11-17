<div class="space-y-6">
    <div>
        <h2 class="text-2xl font-black text-text-slate tracking-tight">Welcome back</h2>
        <p class="mt-1 text-xs text-text-muted">Log in to continue to your dashboard</p>
    </div>

    <form wire:submit="masuk" class="space-y-4" novalidate>
        <x-ui.input
            name="login"
            wire:model="login"
            label="Email or phone number"
            placeholder="name@toko.com or 0812..."
            autocomplete="username"
            autofocus
            :required="true"
        >
            <x-slot:leftIcon>
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                </svg>
            </x-slot:leftIcon>
        </x-ui.input>

        <div x-data="{ tampil: false }">
            <x-ui.input
                name="password"
                type="password"
                x-bind:type="tampil ? 'text' : 'password'"
                wire:model="password"
                label="Password"
                placeholder="••••••••"
                autocomplete="current-password"
                :required="true"
            >
                <x-slot:leftIcon>
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </x-slot:leftIcon>
                <x-slot:rightSlot>
                    <button
                        type="button"
                        x-on:click="tampil = ! tampil"
                        class="pointer-events-auto cursor-pointer text-text-muted hover:text-primary transition focus:outline-none focus:text-primary"
                        x-bind:aria-label="tampil ? 'Hide password' : 'Show password'"
                    >
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
        </div>

        <div class="flex justify-end text-xs">
            <a href="{{ route('password.request') }}" class="font-semibold text-primary hover:underline">
                Forgot password?
            </a>
        </div>

        <x-ui.button
            type="submit"
            variant="primary"
            class="w-full justify-center py-2.5"
            wire:loading.attr="disabled"
            wire:target="masuk"
        >
            <span wire:loading.remove wire:target="masuk">Login</span>
            <span wire:loading wire:target="masuk">Memproses...</span>
        </x-ui.button>
    </form>

    <div class="text-center pt-2 border-t border-surface-border text-xs text-text-muted">
        <span>Don't have an account?</span>
        <a href="{{ route('register') }}" class="font-bold text-primary hover:underline ml-1">Register</a>
    </div>
</div>
