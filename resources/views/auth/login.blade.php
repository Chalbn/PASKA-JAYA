<x-layouts.auth title="Login">
    <div class="space-y-6">
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

        @auth
            @php
                $penggunaAktif = auth()->user();$penggunaAktif?->loadMissing('role');
            @endphp
            <div class="rounded-xl border border-blue-200 bg-surface-container px-4 py-3 text-xs text-text-slate flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2.5">
                <div>
                    <p class="font-semibold">
                        Anda sedang masuk sebagai {{ $penggunaAktif?->nama }}
                        @if ($penggunaAktif?->role?->label)
                            ({{ $penggunaAktif->role->label }})
                        @endif
                    </p>
                    <p class="text-text-muted mt-0.5">Kembali ke beranda atau keluar untuk mengganti akun.</p>
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    <a href="{{ $penggunaAktif?->urlBeranda() }}" class="inline-flex items-center px-2.5 py-1.5 rounded-lg bg-primary text-white font-semibold hover:bg-primary-container transition">
                        Ke Beranda
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="inline-flex items-center px-2.5 py-1.5 rounded-lg border border-surface-border bg-white text-text-slate font-semibold hover:bg-surface-container-low transition cursor-pointer">
                            Keluar
                        </button>
                    </form>
                </div>
            </div>
        @endauth

        <div>
            <h2 class="text-2xl font-black text-text-slate tracking-tight">Welcome back</h2>
            <p class="mt-1 text-xs text-text-muted">Log in to continue to your dashboard</p>
        </div>

        <form method="POST" action="{{ route('login.attempt') }}" class="space-y-4" novalidate>
            @csrf

            <x-ui.input
                name="login"
                :value="old('login')"
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
                class="w-full justify-center py-2.5 cursor-pointer"
            >
                Login
            </x-ui.button>
        </form>

        <div class="relative flex items-center py-1">
            <div class="grow border-t border-surface-border"></div>
            <span class="shrink mx-3 text-[11px] font-semibold uppercase tracking-wider text-text-muted">atau</span>
            <div class="grow border-t border-surface-border"></div>
        </div>

        <a
            href="{{ route('google.redirect') }}"
            class="w-full inline-flex items-center justify-center gap-2.5 rounded-xl border border-surface-border bg-white px-4 py-2.5 text-xs font-bold text-text-slate shadow-xs hover:bg-surface-canvas transition"
        >
            <svg class="w-4 h-4" viewBox="0 0 24 24">
                <path fill="#EA4335" d="M12 10.2v3.9h5.5c-.2 1.2-1.4 3.6-5.5 3.6-3.3 0-6-2.7-6-6s2.7-6 6-6c1.9 0 3.1.8 3.8 1.5l2.6-2.5C16.8 3.2 14.6 2.2 12 2.2 6.6 2.2 2.2 6.6 2.2 12s4.4 9.8 9.8 9.8c5.7 0 9.4-4 9.4-9.6 0-.6-.1-1.1-.2-1.6H12z"/>
                <path fill="#34A853" d="M3.3 7.3l3.2 2.3C7.4 7.4 9.5 5.8 12 5.8c1.9 0 3.1.8 3.8 1.5l2.6-2.5C16.8 3.2 14.6 2.2 12 2.2 8.2 2.2 4.9 4.3 3.3 7.3z"/>
                <path fill="#FBBC05" d="M12 21.8c2.6 0 4.8-.9 6.4-2.4l-3-2.5c-.8.6-1.9 1-3.4 1-2.6 0-4.8-1.7-5.6-4.1l-3.2 2.5c1.6 3.2 4.9 5.5 8.8 5.5z"/>
                <path fill="#4285F4" d="M21.4 12.2c0-.6-.1-1.1-.2-1.6H12v3.9h5.5c-.3 1.4-1.1 2.5-2.1 3.2l3 2.5c1.8-1.6 3-4.1 3-8z"/>
            </svg>
            <span>Lanjutkan dengan Google</span>
        </a>

        <div class="text-center pt-2 border-t border-surface-border text-xs text-text-muted">
            <span>Don't have an account?</span>
            <a href="{{ route('register') }}" class="font-bold text-primary hover:underline ml-1">Register</a>
        </div>
    </div>
</x-layouts.auth>