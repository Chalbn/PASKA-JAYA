<div class="space-y-5">
    <div>
        <a href="{{ route('login') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-text-muted hover:text-primary transition group">
            <svg class="w-4 h-4 transition-transform group-hover:-translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            <span>Back to Login</span>
        </a>
    </div>

    <div>
        <h2 class="text-2xl font-black text-text-slate tracking-tight">Verify your account</h2>
        <p class="mt-1 text-xs text-text-muted">
            Enter the 6-digit code we sent to
            <span class="font-semibold text-text-slate">{{ $emailTersamar }}</span>.
            The code is valid for {{ config('auth.otp_expire_minutes', 10) }} minutes.
        </p>
    </div>

    <form wire:submit="verifikasi" class="space-y-4" novalidate>
        <x-ui.input
            name="kode"
            wire:model="kode"
            label="Verification code"
            placeholder="000000"
            inputmode="numeric"
            maxlength="6"
            autocomplete="one-time-code"
            autofocus
            class="text-center text-xl font-bold tracking-[0.5em]"
            :required="true"
        />

        <x-ui.button
            type="submit"
            variant="primary"
            class="w-full justify-center py-2.5"
            wire:loading.attr="disabled"
            wire:target="verifikasi"
        >
            <span wire:loading.remove wire:target="verifikasi">Verify</span>
            <span wire:loading wire:target="verifikasi">Memproses...</span>
        </x-ui.button>
    </form>

    {{-- Kirim ulang dengan cooldown; wire:key berubah tiap kirim ulang agar hitung mundur dibangun ulang --}}
    <div
        wire:key="kirim-ulang-{{ $versi }}"
        x-data="{ sisa: {{ $sisaCooldown }}, timer: null }"
        x-init="timer = setInterval(() => { if (sisa > 0) sisa--; else clearInterval(timer); }, 1000)"
        x-on:livewire:navigating.window="clearInterval(timer)"
        class="text-center pt-2 border-t border-surface-border text-xs text-text-muted"
    >
        <span>Didn't receive the code?</span>
        <button
            type="button"
            wire:click="kirimUlang"
            x-bind:disabled="sisa > 0"
            class="font-bold text-primary hover:underline ml-1 cursor-pointer disabled:text-text-muted disabled:no-underline disabled:cursor-not-allowed"
        >
            <span x-show="sisa <= 0">Resend code</span>
            <span x-show="sisa > 0" style="display: none;">Resend in <span x-text="sisa"></span>s</span>
        </button>
    </div>
</div>
