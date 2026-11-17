<x-layouts.app :title="$title ?? 'Beranda'">
    <div class="max-w-4xl mx-auto space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-text-on-surface tracking-tight">
                    {{ $title ?? 'Beranda' }}
                </h1>
                <p class="mt-1 text-xs sm:text-sm text-text-muted">
                    {{ $subtitle ?? 'Selamat datang di sistem manajemen Toko Bahan Bangunan PASKA JAYA.' }}
                </p>
            </div>
        </div>

        <x-ui.empty-state
            :title="$title ?? 'Halaman Belum Tersedia'"
            description="Modul fitur ini sedang dalam tahap pengembangan berikutnya. Anda sudah dapat mengelola profil atau keluar dari menu samping."
            actionLabel="Buka Profil Saya"
            :actionRoute="Route::has($roleKey . '.profil') ? route($roleKey . '.profil') : null"
        />
    </div>
</x-layouts.app>
