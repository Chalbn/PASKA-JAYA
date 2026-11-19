<x-layouts.auth title="Verifikasi Kode">
    <div class="space-y-5">
        <div>
            <a href="{{ route('login') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-text-muted hover:text-primary transition group">
                <svg class="w-4 h-4 transition-transform group-hover:-translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                <span>Back to Login</span>
            </a>
        </div>

        @if (session('error'))
            <div class="rounded-xl border border-red-200 bg-status-danger-bg px-4 py-3 text-xs font-medium text-status-danger" role="alert">
                {{ session('error') }}
            </div>
        @endif

        @if (session('success'))
            <div class="rounded-xl border border-emerald-200 bg-status-success-bg px-4 py-3 text-xs font-medium text-status-success" role="status">
                {{ session('success') }}
            </div>
        @endif

        <div>
            <h2 class="text-2xl font-black text-text-slate tracking-tight">Verify your account</h2>
            <p class="mt-1 text-xs text-text-muted">
                Enter the 6-digit code we sent to
                <span class="font-semibold text-text-slate">{{ $emailTersamar ?? '' }}</span>.
                The code is valid for {{ config('auth.otp_expire_minutes', 10) }} minutes.
            </p>
        </div>

        <form method="POST" action="{{ route('otp.verify.submit') }}" class="space-y-4" novalidate>
            @csrf

            <x-ui.input
                name="kode"
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
            >
                Verify
            </x-ui.button>
        </form>

        <div
            x-data="{ sisa: {{ (int) ($sisaCooldown ?? 0) }}, timer: null }"
            x-init="timer = setInterval(() => { if (sisa > 0) sisa--; else clearInterval(timer); }, 1000)"
            class="text-center pt-2 border-t border-surface-border text-xs text-text-muted"
        >
            <span>Didn't receive the code?</span>
            <form method="POST" action="{{ route('otp.resend') }}" class="inline">
                @csrf
                <button
                    type="submit"
                    x-bind:disabled="sisa > 0"
                    class="font-bold text-primary hover:underline ml-1 cursor-pointer disabled:text-text-muted disabled:no-underline disabled:cursor-not-allowed"
                >
                    <span x-show="sisa <= 0">Resend code</span>
                    <span x-show="sisa > 0" style="display: none;">Resend in <span x-text="sisa"></span>s</span>
                </button>
            </form>
        </div>
    </div>
</x-layouts.auth>
