<x-layouts.auth title="Masuk ke Akun">
    <div class="space-y-6">
        <div>
            <h2 class="text-2xl font-black text-text-slate tracking-tight">
                Selamat Datang
            </h2>
            <p class="mt-1 text-xs text-text-muted">
                Masukkan email atau nomor HP dan kata sandi Anda untuk mengakses sistem.
            </p>
        </div>

        <form action="#" method="POST" class="space-y-4">
            <x-ui.input 
                name="login"
                label="Email atau No. Handphone"
                placeholder="nama@paskajaya.com atau 08123456789"
                :required="true"
            >
                <x-slot:leftIcon>
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                    </svg>
                </x-slot:leftIcon>
            </x-ui.input>

            <x-ui.input 
                type="password"
                name="password"
                label="Kata Sandi"
                placeholder="••••••••"
                :required="true"
            >
                <x-slot:leftIcon>
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </x-slot:leftIcon>
            </x-ui.input>

            <div class="flex items-center justify-between text-xs">
                <label class="flex items-center gap-2 cursor-pointer select-none">
                    <input type="checkbox" name="remember" class="rounded border-surface-border text-primary focus:ring-primary h-4 w-4" />
                    <span class="text-text-body font-medium">Ingat saya</span>
                </label>

                <a href="#" class="font-semibold text-primary hover:underline">
                    Lupa kata sandi?
                </a>
            </div>

            <div class="pt-2">
                <x-ui.button type="button" variant="primary" class="w-full justify-center text-sm py-2.5">
                    Masuk ke Sistem
                </x-ui.button>
            </div>
        </form>

        <div class="text-center pt-2 border-t border-surface-border text-xs text-text-muted">
            <a href="/dev/ui" class="font-semibold text-primary hover:underline">
                &larr; Kembali ke Galeri Komponen
            </a>
        </div>
    </div>
</x-layouts.auth>
