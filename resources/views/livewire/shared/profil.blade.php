<div x-data="{ modalHapus: false }" class="max-w-6xl mx-auto space-y-6">
    <div>
        <h1 class="text-xl sm:text-2xl font-bold text-text-slate tracking-tight">Profil Saya</h1>
        <p class="text-xs sm:text-sm text-text-muted mt-0.5">
            Kelola data akun, profil, dan keamanan.
        </p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        <div class="lg:col-span-5 space-y-6">
            <div class="bg-surface-card border border-surface-border rounded-2xl p-5 sm:p-6 shadow-xs">
                <h2 class="text-sm font-bold text-text-slate">Data Akun</h2>

                <div class="flex items-center gap-4 mt-4 pb-5 border-b border-surface-border">
                    @if($foto)
                        <img
                            src="{{ $foto->temporaryUrl() }}"
                            alt="Pratinjau Foto"
                            class="h-14 w-14 rounded-full object-cover border border-surface-border shrink-0"
                        />
                    @elseif($user->foto_profil)
                        <img
                            src="{{ $user->fotoUrl() }}"
                            alt="{{ $user->nama }}"
                            class="h-14 w-14 rounded-full object-cover border border-surface-border shrink-0"
                        />
                    @else
                        <div class="h-14 w-14 rounded-full bg-surface-container text-primary flex items-center justify-center text-base font-bold shrink-0">
                            {{ inisial($user->nama) }}
                        </div>
                    @endif

                    <div class="min-w-0">
                        <div class="text-base font-bold text-text-slate truncate">{{ $user->nama }}</div>
                        <span class="inline-flex items-center mt-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-primary text-primary-on capitalize">
                            {{ $user->role?->nama ?? '-' }}
                        </span>
                    </div>
                </div>

                <dl class="mt-4 divide-y divide-surface-border/60 text-sm">
                    <div class="py-2.5 flex items-center justify-between gap-4">
                        <dt class="text-text-muted">Email</dt>
                        <dd class="font-semibold text-text-slate text-right break-all">{{ $user->email }}</dd>
                    </div>
                    <div class="py-2.5 flex items-center justify-between gap-4">
                        <dt class="text-text-muted">Role</dt>
                        <dd class="font-semibold text-text-slate text-right capitalize">{{ $user->role?->nama ?? '-' }}</dd>
                    </div>
                    <div class="py-2.5 flex items-center justify-between gap-4">
                        <dt class="text-text-muted">Tanggal dibuat</dt>
                        <dd class="font-semibold text-text-slate text-right">
                            {{ $user->created_at?->timezone('Asia/Jakarta')->translatedFormat('j F Y') ?? '-' }}
                        </dd>
                    </div>
                    <div class="py-2.5 flex items-center justify-between gap-4">
                        <dt class="text-text-muted">Status akun</dt>
                        <dd class="font-bold text-right {{ $user->status === 'aktif' ? 'text-primary' : 'text-status-danger' }} capitalize">
                            {{ $user->status }}
                        </dd>
                    </div>
                </dl>
            </div>

            <div class="bg-red-50/70 border border-red-200/80 rounded-2xl p-5 sm:p-6 shadow-xs">
                <h2 class="text-sm font-bold text-status-danger">Hapus Akun</h2>
                <p class="text-xs text-text-muted mt-1">
                    Tindakan ini permanen dan tidak dapat dibatalkan.
                </p>

                <button
                    type="button"
                    x-on:click="modalHapus = true"
                    class="mt-4 w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-status-danger hover:bg-red-800 text-white text-sm font-semibold transition shadow-xs cursor-pointer"
                >
                    <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    <span>Hapus Akun</span>
                </button>
            </div>
        </div>

        <div class="lg:col-span-7 space-y-6">
            <div class="bg-surface-card border border-surface-border rounded-2xl p-5 sm:p-6 shadow-xs">
                <h2 class="text-sm font-bold text-text-slate">Data Profil</h2>
                <p class="text-xs text-text-muted mt-0.5">
                    Perbarui foto, nama, nomor HP, dan alamat.
                </p>

                <form wire:submit="simpanProfil" class="mt-5 space-y-4">
                    <div class="flex items-center gap-4">
                        @if($foto)
                            <img
                                src="{{ $foto->temporaryUrl() }}"
                                alt="Pratinjau Foto"
                                class="h-14 w-14 rounded-full object-cover border border-surface-border shrink-0"
                            />
                        @elseif($user->foto_profil)
                            <img
                                src="{{ $user->fotoUrl() }}"
                                alt="{{ $user->nama }}"
                                class="h-14 w-14 rounded-full object-cover border border-surface-border shrink-0"
                            />
                        @else
                            <div class="h-14 w-14 rounded-full bg-surface-container text-primary flex items-center justify-center text-base font-bold shrink-0">
                                {{ inisial($user->nama) }}
                            </div>
                        @endif

                        <div>
                            <label
                                for="foto-profil-input"
                                class="cursor-pointer inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-surface-card text-text-slate border border-surface-border hover:bg-surface-container-low text-xs font-semibold transition shadow-xs"
                            >
                                <svg class="w-4 h-4 text-text-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <span>Ganti foto</span>
                            </label>
                            <input
                                id="foto-profil-input"
                                type="file"
                                accept="image/jpeg,image/png,image/webp"
                                wire:model="foto"
                                class="hidden"
                            />
                            <div wire:loading wire:target="foto" class="text-xs text-primary mt-1">
                                Mengunggah foto...
                            </div>
                            @error('foto')
                                <p class="text-xs text-status-danger mt-1 font-medium">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="nama" class="block text-xs font-semibold text-text-slate mb-1.5">
                                Nama lengkap
                            </label>
                            <input
                                id="nama"
                                type="text"
                                wire:model="nama"
                                required
                                class="block w-full rounded-xl text-sm text-text-slate bg-surface-card border px-3.5 py-2.5 shadow-xs focus:outline-none focus:ring-2 {{ $errors->has('nama') ? 'border-status-danger focus:ring-status-danger/30' : 'border-surface-border focus:border-primary focus:ring-primary/20' }}"
                            />
                            @error('nama')
                                <p class="mt-1 text-xs text-status-danger font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="no_hp" class="block text-xs font-semibold text-text-slate mb-1.5">
                                Nomor HP
                            </label>
                            <input
                                id="no_hp"
                                type="text"
                                wire:model="no_hp"
                                placeholder="0812-3456-7890"
                                class="block w-full rounded-xl text-sm text-text-slate bg-surface-card border px-3.5 py-2.5 shadow-xs focus:outline-none focus:ring-2 {{ $errors->has('no_hp') ? 'border-status-danger focus:ring-status-danger/30' : 'border-surface-border focus:border-primary focus:ring-primary/20' }}"
                            />
                            @error('no_hp')
                                <p class="mt-1 text-xs text-status-danger font-medium">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label for="alamat" class="block text-xs font-semibold text-text-slate mb-1.5">
                            Alamat
                        </label>
                        <textarea
                            id="alamat"
                            rows="3"
                            wire:model="alamat"
                            class="block w-full rounded-xl text-sm text-text-slate bg-surface-card border px-3.5 py-2.5 shadow-xs focus:outline-none focus:ring-2 {{ $errors->has('alamat') ? 'border-status-danger focus:ring-status-danger/30' : 'border-surface-border focus:border-primary focus:ring-primary/20' }}"
                        ></textarea>
                        @error('alamat')
                            <p class="mt-1 text-xs text-status-danger font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="pt-1">
                        <x-ui.button
                            type="submit"
                            variant="primary"
                            wire:loading.attr="disabled"
                            wire:target="simpanProfil,foto"
                        >
                            Simpan Perubahan
                        </x-ui.button>
                    </div>
                </form>
            </div>

            <div class="bg-surface-card border border-surface-border rounded-2xl p-5 sm:p-6 shadow-xs">
                <h2 class="text-sm font-bold text-text-slate">Ubah Password</h2>

                <form wire:submit="ubahPassword" class="mt-4 space-y-4">
                    <div>
                        <label for="password_lama" class="block text-xs font-semibold text-text-slate mb-1.5">
                            Password lama
                        </label>
                        <input
                            id="password_lama"
                            type="password"
                            wire:model="password_lama"
                            required
                            class="block w-full rounded-xl text-sm text-text-slate bg-surface-card border px-3.5 py-2.5 shadow-xs focus:outline-none focus:ring-2 {{ $errors->has('password_lama') ? 'border-status-danger focus:ring-status-danger/30' : 'border-surface-border focus:border-primary focus:ring-primary/20' }}"
                        />
                        @error('password_lama')
                            <p class="mt-1 text-xs text-status-danger font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="password_baru" class="block text-xs font-semibold text-text-slate mb-1.5">
                                Password baru
                            </label>
                            <input
                                id="password_baru"
                                type="password"
                                wire:model="password_baru"
                                required
                                class="block w-full rounded-xl text-sm text-text-slate bg-surface-card border px-3.5 py-2.5 shadow-xs focus:outline-none focus:ring-2 {{ $errors->has('password_baru') ? 'border-status-danger focus:ring-status-danger/30' : 'border-surface-border focus:border-primary focus:ring-primary/20' }}"
                            />
                            @error('password_baru')
                                <p class="mt-1 text-xs text-status-danger font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="password_baru_konfirmasi" class="block text-xs font-semibold text-text-slate mb-1.5">
                                Konfirmasi password baru
                            </label>
                            <input
                                id="password_baru_konfirmasi"
                                type="password"
                                wire:model="password_baru_konfirmasi"
                                required
                                class="block w-full rounded-xl text-sm text-text-slate bg-surface-card border px-3.5 py-2.5 shadow-xs focus:outline-none focus:ring-2 {{ $errors->has('password_baru_konfirmasi') ? 'border-status-danger focus:ring-status-danger/30' : 'border-surface-border focus:border-primary focus:ring-primary/20' }}"
                            />
                            @error('password_baru_konfirmasi')
                                <p class="mt-1 text-xs text-status-danger font-medium">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <p class="text-xs text-text-muted">Minimal 8 karakter</p>

                    <div>
                        <x-ui.button
                            type="submit"
                            variant="primary"
                            wire:loading.attr="disabled"
                            wire:target="ubahPassword"
                        >
                            Ubah Password
                        </x-ui.button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div
        x-show="modalHapus"
        x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs"
    >
        <div
            x-on:click.outside="modalHapus = false"
            class="w-full max-w-md bg-surface-card border border-surface-border rounded-2xl p-6 shadow-xl space-y-4"
        >
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h3 class="text-base font-bold text-text-slate">Konfirmasi Hapus Akun</h3>
                    <p class="text-xs text-text-muted mt-1">
                        Masukkan password Anda untuk mengonfirmasi penghapusan akun secara permanen