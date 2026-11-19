@php
    $roleKey = strtolower((string) ($user->role?->nama ?? 'karyawan'));
    if (!in_array($roleKey, ['owner', 'kasir', 'karyawan'], true)) {
        $roleKey = 'karyawan';
    }
@endphp
<x-layouts.app title="Profil Saya">
    <div class="max-w-5xl mx-auto space-y-6" x-data="{ hapusAkunModal: false, pratinjauFoto: null }">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-text-on-surface tracking-tight">Profil Saya</h1>
            <p class="mt-1 text-xs sm:text-sm text-text-muted">Kelola data akun, profil, dan keamanan.</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
            <div class="space-y-6">
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

                <div class="rounded-2xl border border-red-200 bg-status-danger-bg/60 p-5 sm:p-6 shadow-xs space-y-3">
                    <div class="flex items-center gap-2 text-status-danger">
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
                            x-on:click="hapusAkunModal = true"
                        >
                            Hapus Akun
                        </x-ui.button>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-2 space-y-6">
                <x-ui.card title="Data Profil" subtitle="Perbarui foto, nama, nomor HP, dan alamat.">
                    <form method="POST" action="{{ route($roleKey . '.profil.update') }}" enctype="multipart/form-data" class="space-y-4" novalidate>
                        @csrf
                        @method('PUT')

                        <div class="flex flex-col sm:flex-row sm:items-center gap-4 pb-4 border-b border-surface-border">
                            <div class="shrink-0">
                                <template x-if="pratinjauFoto">
                                    <img x-bind:src="pratinjauFoto" alt="Pratinjau foto profil" class="h-16 w-16 rounded-2xl object-cover border border-surface-border shadow-xs" />
                                </template>
                                <template x-if="!pratinjauFoto">
                                    @if ($user->fotoUrl())
                                        <img src="{{ $user->fotoUrl() }}" alt="{{ $user->nama }}" class="h-16 w-16 rounded-2xl object-cover border border-surface-border shadow-xs" />
                                    @else
                                        <div class="h-16 w-16 rounded-2xl bg-surface-container text-primary font-extrabold text-xl flex items-center justify-center border border-blue-200 select-none">
                                            {{ inisial($user->nama) }}
                                        </div>
                                    @endif
                                </template>
                            </div>

                            <div class="flex-1 min-w-0">
                                <label for="foto-profil" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl border border-surface-border bg-surface-card text-xs font-semibold text-text-slate hover:bg-surface-container-low transition cursor-pointer shadow-xs">
                                    <span>Ganti Foto</span>
                                    <input
                                        id="foto-profil"
                                        type="file"
                                        name="foto"
                                        accept="image/jpeg,image/png,image/webp"
                                        class="sr-only"
                                        x-on:change="const f = $event.target.files[0]; pratinjauFoto = f ? URL.createObjectURL(f) : null;"
                                    />
                                </label>
                                <p class="mt-1.5 text-[11px] text-text-muted">JPG, PNG, atau WEBP maksimal 4 MB.</p>
                                @error('foto')
                                    <p class="mt-1 text-xs text-status-danger font-medium">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <x-ui.input
                                name="nama"
                                :value="old('nama', $user->nama)"
                                label="Nama Lengkap"
                                placeholder="Nama lengkap Anda"
                                :required="true"
                            />

                            <x-ui.input
                                name="no_hp"
                                type="tel"
                                :value="old('no_hp', (string) $user->no_hp)"
                                label="Nomor HP"
                                placeholder="0812 3456 7890"
                            />
                        </div>

                        <div>
                            <label for="alamat" class="block text-xs font-semibold uppercase tracking-wider text-text-slate mb-1.5">Alamat</label>
                            <textarea
                                id="alamat"
                                name="alamat"
                                rows="3"
                                placeholder="Alamat tempat tinggal..."
                                class="block w-full rounded-xl text-sm text-text-slate placeholder:text-text-muted/60 bg-surface-card border border-surface-border focus:border-primary focus:ring-2 focus:ring-primary/20 focus:outline-none px-3.5 py-2.5 shadow-xs transition"
                            >{{ old('alamat', (string) $user->alamat) }}</textarea>
                            @error('alamat')
                                <p class="mt-1.5 text-xs text-status-danger font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex justify-end pt-2">
                            <x-ui.button type="submit" variant="primary" class="cursor-pointer">
                                Simpan Perubahan
                            </x-ui.button>
                        </div>
                    </form>
                </x-ui.card>

                <x-ui.card title="Ubah Password" subtitle="Gunakan password minimal 8 karakter yang sulit ditebak.">
                    <form method="POST" action="{{ route($roleKey . '.profil.password') }}" class="space-y-4" x-data="{ tampilLama: false, tampilBaru: false, tampilKonf: false }" novalidate>
                        @csrf
                        @method('PUT')

                        <x-ui.input
                            name="password_lama"
                            type="password"
                            x-bind:type="tampilLama ? 'text' : 'password'"
                            label="Password Lama"
                            placeholder="Masukkan password saat ini"
                            autocomplete="current-password"
                            :required="true"
                        />

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <x-ui.input
                                name="password_baru"
                                type="password"
                                x-bind:type="tampilBaru ? 'text' : 'password'"
                                label="Password Baru"
                                placeholder="Minimal 8 karakter"
                                hint="Minimal 8 karakter"
                                autocomplete="new-password"
                                :required="true"
                            />

                            <x-ui.input
                                name="password_baru_konfirmasi"
                                type="password"
                                x-bind:type="tampilKonf ? 'text' : 'password'"
                                label="Konfirmasi Password Baru"
                                placeholder="Minimal 8 karakter"
                                autocomplete="new-password"
                                :required="true"
                            />
                        </div>

                        <div class="flex justify-end pt-2">
                            <x-ui.button type="submit" variant="primary" class="cursor-pointer">
                                Ubah Password
                            </x-ui.button>
                        </div>
                    </form>
                </x-ui.card>
            </div>
        </div>

        {{-- Modal Konfirmasi Hapus Akun --}}
        <div x-show="hapusAkunModal" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-xs">
            <div x-on:click.outside="hapusAkunModal = false" class="bg-surface-card border border-surface-border rounded-2xl shadow-xl max-w-sm w-full p-6 space-y-4">
                <h3 class="text-base font-bold text-text-slate">Konfirmasi Hapus Akun</h3>
                <form method="POST" action="{{ route($roleKey . '.profil.destroy') }}" class="space-y-4" novalidate>
                    @csrf
                    @method('DELETE')

                    <p class="text-xs text-text-body leading-relaxed">
                        Masukkan password Anda untuk mengonfirmasi penghapusan akun. Tindakan ini permanen dan tidak dapat dibatalkan.
                    </p>

                    <x-ui.input
                        name="password_hapus"
                        type="password"
                        label="Password Konfirmasi"
                        placeholder="Masukkan password Anda"
                        autocomplete="current-password"
                        :required="true"
                    />

                    <div class="flex items-center justify-end gap-3 pt-2">
                        <x-ui.button type="button" variant="outline" size="sm" x-on:click="hapusAkunModal = false">Batal</x-ui.button>
                        <x-ui.button type="submit" variant="danger" size="sm" class="border border-red-200">Ya, hapus</x-ui.button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.app>
