<div class="max-w-5xl mx-auto space-y-6">
    {{-- Header Halaman --}}
    <div>
        <h1 class="text-2xl sm:text-3xl font-extrabold text-text-on-surface tracking-tight">Profil Saya</h1>
        <p class="mt-1 text-xs sm:text-sm text-text-muted">Kelola data akun, profil, dan keamanan.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
        {{-- Kolom Kiri: Data Akun (Read-only) & Hapus Akun --}}
        <div class="space-y-6">
            {{-- Data Akun --}}
            <x-ui.card title="Data Akun" subtitle="Informasi akun yang dikelola sistem.">
                <div class="flex items-center gap-4 pb-5 border-b border-surface-border">
                    @if ($user->fotoUrl())
                        <img
                            src="{{ $user->fotoUrl() }}"
                            alt="{{ $user->nama }}"
                            class="h-14 w-14 rounded-2xl object-cover ring-2 ring-primary/20 shrink-0"
                        />
                    @else
                        <div class="h-14 w-14 rounded-2xl bg-primary text-white font-extrabold text-lg flex items-center justify-center shadow-xs shrink-0 select-none">
                            {{ inisial($user->nama) }}
                        </div>
                    @endif

                    <div class="min-w-0">
                        <p class="text-sm font-bold text-text-slate truncate">{{ $user->nama }}</p>
                        <div class="mt-1.5">
                            <x-ui.badge variant="primary" size="sm">{{ $user->role?->label ?? ucfirst((string) $user->role?->nama) }}</x-ui.badge>
                        </div>
                    </div>
                </div>

                <dl class="mt-4 space-y-3 text-xs">
                    <div class="flex items-center justify-between gap-2">
                        <dt class="text-text-muted font-medium">Email</dt>
                        <dd class="text-text-slate font-semibold truncate">{{ $user->email }}</dd>
                    </div>
                    <div class="flex items-center justify-between gap-2">
                        <dt class="text-text-muted font-medium">Role</dt>
                        <dd class="text-text-slate font-semibold">{{ $user->role?->label ?? '-' }}</dd>
                    </div>
                    <div class="flex items-center justify-between gap-2">
                        <dt class="text-text-muted font-medium">Tanggal dibuat</dt>
                        <dd class="text-text-slate font-semibold">{{ $user->created_at ? tanggal_id($user->created_at) : '-' }}</dd>
                    </div>
                    <div class="flex items-center justify-between gap-2">
                        <dt class="text-text-muted font-medium">Status akun</dt>
                        <dd>
                            @if ($user->isAktif())
                                <x-ui.badge variant="lunas" size="sm">Aktif</x-ui.badge>
                            @else
                                <x-ui.badge variant="kritis" size="sm">Nonaktif</x-ui.badge>
                            @endif
                        </dd>
                    </div>
                </dl>
            </x-ui.card>

            {{-- Hapus Akun (Kartu Merah Muda) --}}
            <div class="rounded-2xl border border-red-200 bg-status-danger-bg/60 p-5 sm:p-6 shadow-xs space-y-3">
                <div class="flex items-center gap-2 text-status-danger">
                    <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <h3 class="text-sm font-bold tracking-tight">Hapus Akun</h3>
                </div>

                <p class="text-xs text-text-body leading-relaxed">
                    Tindakan ini permanen dan tidak dapat dibatalkan.
                </p>

                <div class="pt-1">
                    <x-ui.button
                        type="button"
                        variant="danger"
                        size="sm"
                        class="w-full justify-center border border-red-200 cursor-pointer"
                        x-on:click="$dispatch('open-modal', 'hapus-akun-modal')"
                    >
                        Hapus Akun
                    </x-ui.button>
                </div>
            </div>
        </div>

        {{-- Kolom Kanan: Data Profil & Ubah Password --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Data Profil --}}
            <x-ui.card title="Data Profil" subtitle="Perbarui foto, nama, nomor HP, dan alamat.">
                <form wire:submit="simpanProfil" class="space-y-4" novalidate>
                    {{-- Foto Profil --}}
                    <div class="flex flex-col sm:flex-row sm:items-center gap-4 pb-4 border-b border-surface-border">
                        <div class="shrink-0">
                            @if ($foto)
                                <img
                                    src="{{ $foto->temporaryUrl() }}"
                                    alt="Pratinjau foto profil"
                                    class="h-16 w-16 rounded-2xl object-cover border border-surface-border shadow-xs"
                                />
                            @elseif ($user->fotoUrl())
                                <img
                                    src="{{ $user->fotoUrl() }}"
                                    alt="{{ $user->nama }}"
                                    class="h-16 w-16 rounded-2xl object-cover border border-surface-border shadow-xs"
                                />
                            @else
                                <div class="h-16 w-16 rounded-2xl bg-surface-container text-primary font-extrabold text-xl flex items-center justify-center border border-blue-200 select-none">
                                    {{ inisial($user->nama) }}
                                </div>
                            @endif
                        </div>

                        <div class="flex-1 min-w-0">
                            <label for="foto-profil" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl border border-surface-border bg-surface-card text-xs font-semibold text-text-slate hover:bg-surface-container-low transition cursor-pointer shadow-xs">
                                <svg class="w-4 h-4 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                </svg>
                                <span>Ganti Foto</span>
                                <input
                                    id="foto-profil"
                                    type="file"
                                    wire:model="foto"
                                    accept="image/jpeg,image/png,image/webp"
                                    class="sr-only"
                                />
                            </label>
                            <p class="mt-1.5 text-[11px] text-text-muted">JPG, PNG, atau WEBP maksimal 4 MB.</p>
                            <div wire:loading wire:target="foto" class="mt-1 text-xs text-primary font-medium">
                                Mengunggah pratinjau foto...
                            </div>
                            @error('foto')
                                <p class="mt-1 text-xs text-status-danger font-medium">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <x-ui.input
                            name="nama"
                            wire:model="nama"
                            label="Nama Lengkap"
                            placeholder="Nama lengkap Anda"
                            :required="true"
                        />

                        <x-ui.input
                            name="no_hp"
                            type="tel"
                            wire:model="no_hp"
                            label="Nomor HP"
                            placeholder="0812 3456 7890"
                        />
                    </div>

                    <div>
                        <label for="alamat" class="block text-xs font-semibold uppercase tracking-wider text-text-slate mb-1.5">
                            Alamat
                        </label>
                        <textarea
                            id="alamat"
                            name="alamat"
                            rows="3"
                            wire:model="alamat"
                            placeholder="Alamat tempat tinggal..."
                            class="block w-full rounded-xl text-sm text-text-slate placeholder:text-text-muted/60 bg-surface-card border border-surface-border hover:border-surface-border-subtle focus:border-primary focus:ring-2 focus:ring-primary/20 focus:outline-none px-3.5 py-2.5 shadow-xs transition"
                        ></textarea>
                        @error('alamat')
                            <p class="mt-1.5 text-xs text-status-danger font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-end pt-2">
                        <x-ui.button
                            type="submit"
                            variant="primary"
                            class="cursor-pointer"
                            wire:loading.attr="disabled"
                            wire:target="simpanProfil,foto"
                        >
                            <span wire:loading.remove wire:target="simpanProfil">Simpan Perubahan</span>
                            <span wire:loading wire:target="simpanProfil">Menyimpan...</span>
                        </x-ui.button>
                    </div>
                </form>
            </x-ui.card>

            {{-- Ubah Password --}}
            <x-ui.card title="Ubah Password" subtitle="Gunakan password minimal 8 karakter yang sulit ditebak.">
                <form wire:submit="ubahPassword" class="space-y-4" x-data="{ tampilLama: false, tampilBaru: false, tampilKonf: false }" novalidate>
                    <x-ui.input
                        name="password_lama"
                        type="password"
                        x-bind:type="tampilLama ? 'text' : 'password'"
                        wire:model="password_lama"
                        label="Password Lama"
                        placeholder="Masukkan password saat ini"
                        autocomplete="current-password"
                        :required="true"
                    >
                        <x-slot:rightSlot>
                            <button type="button" x-on:click="tampilLama = ! tampilLama" class="pointer-events-auto cursor-pointer text-text-muted hover:text-primary" aria-label="Tampilkan password lama">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </button>
                        </x-slot:rightSlot>
                    </x-ui.input>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <x-ui.input
                            name="password_baru"
                            type="password"
                            x-bind:type="tampilBaru ? 'text' : 'password'"
                            wire:model="password_baru"
                            label="Password Baru"
                            placeholder="Minimal 8 karakter"
                            hint="Minimal 8 karakter"
                            autocomplete="new-password"
                            :required="true"
                        >
                            <x-slot:rightSlot>
                                <button type="button" x-on:click="tampilBaru = ! tampilBaru" class="pointer-events-auto cursor-pointer text-text-muted hover:text-primary" aria-label="Tampilkan password baru">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </button>
                            </x-slot:rightSlot>
                        </x-ui.input>

                        <x-ui.input
                            name="password_baru_konfirmasi"
                            type="password"
                            x-bind:type="tampilKonf ? 'text' : 'password'"
                            wire:model="password_baru_konfirmasi"
                            label="Konfirmasi Password Baru"
                            placeholder="Minimal 8 karakter"
                            autocomplete="new-password"
                            :required="true"
                        >
                            <x-slot:rightSlot>
                                <button type="button" x-on:click="tampilKonf = ! tampilKonf" class="pointer-events-auto cursor-pointer text-text-muted hover:text-primary" aria-label="Tampilkan konfirmasi password baru">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </button>
                            </x-slot:rightSlot>
                        </x-ui.input>
                    </div>

                    <div class="flex justify-end pt-2">
                        <x-ui.button
                            type="submit"
                            variant="primary"
                            class="cursor-pointer"
                            wire:loading.attr="disabled"
                            wire:target="ubahPassword"
                        >
                            <span wire:loading.remove wire:target="ubahPassword">Ubah Password</span>
                            <span wire:loading wire:target="ubahPassword">Memproses...</span>
                        </x-ui.button>
                    </div>
                </form>
            </x-ui.card>
        </div>
    </div>

    {{-- Modal Konfirmasi Hapus Akun --}}
    <x-ui.modal name="hapus-akun-modal" title="Konfirmasi Hapus Akun" maxWidth="sm">
        <form wire:submit="hapusAkun" class="space-y-4" novalidate>
            <p class="text-xs text-text-body leading-relaxed">
                Masukkan password Anda untuk mengonfirmasi penghapusan akun. Tindakan ini permanen dan tidak dapat dibatalkan.
            </p>

            <x-ui.input
                name="password_hapus"
                type="password"
                wire:model="password_hapus"
                label="Password Konfirmasi"
                placeholder="Masukkan password Anda"
                autocomplete="current-password"
                :required="true"
            />

            <div class="flex items-center justify-end gap-3 pt-2">
                <x-ui.button type="button" variant="outline" size="sm" x-on:click="open = false" class="cursor-pointer">
                    Batal
                </x-ui.button>
                <x-ui.button
                    type="submit"
                    variant="danger"
                    size="sm"
                    class="cursor-pointer border border-red-200"
                    wire:loading.attr="disabled"
                    wire:target="hapusAkun"
                >
                    <span wire:loading.remove wire:target="hapusAkun">Ya, hapus</span>
                    <span wire:loading wire:target="hapusAkun">Menghapus...</span>
                </x-ui.button>
            </div>
        </form>
    </x-ui.modal>
</div>
