<x-layouts.app title="Manajemen Pengguna" role="admin">
    <div
        x-data="{
            showFormModal: false,
            showHapusModal: false,
            editId: null,
            nama: '',
            email: '',
            no_hp: '',
            role: 'karyawan',
            hapusId: null,
            hapusNama: '',
            hapusEmail: '',
            actorId: {{ (int) ($actorId ?? auth()->id()) }},
            bukaTambah() {
                this.editId = null;
                this.nama = '';
                this.email = '';
                this.no_hp = '';
                this.role = 'karyawan';
                this.showFormModal = true;
            },
            bukaEdit(user) {
                this.editId = user.id;
                this.nama = user.nama;
                this.email = user.email;
                this.no_hp = user.no_hp || '';
                this.role = user.role || 'karyawan';
                this.showFormModal = true;
            },
            bukaHapus(user) {
                this.hapusId = user.id;
                this.hapusNama = user.nama;
                this.hapusEmail = user.email;
                this.showHapusModal = true;
            }
        }"
        class="-m-4 sm:-m-6 lg:-m-8 p-4 sm:p-6 lg:p-8 min-h-[calc(100vh-5rem)] bg-gradient-to-br from-[#F4F7FE] via-[#EEF4FF] to-[#DBE8FF]"
    >
        <div class="space-y-6">
            {{-- Header Halaman --}}
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-text-on-surface tracking-tight">Manajemen Pengguna</h1>
                    <p class="mt-1 text-xs sm:text-sm text-text-muted">Tambah, ubah role, nonaktifkan, atau hapus akun.</p>
                </div>

                <div class="shrink-0">
                    <button
                        type="button"
                        x-on:click="bukaTambah()"
                        class="inline-flex items-center gap-2 rounded-xl bg-[#1D4ED8] hover:bg-[#1E40AF] text-white text-xs sm:text-sm font-semibold px-4 py-2.5 shadow-sm transition cursor-pointer"
                    >
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                        </svg>
                        + Tambah Pengguna
                    </button>
                </div>
            </div>

            {{-- Kartu Putih Utama --}}
            <div class="rounded-2xl bg-white/95 border border-white/90 shadow-xs p-5 sm:p-6 space-y-5">
                {{-- Pencarian --}}
                <form method="GET" action="{{ route('admin.pengguna') }}" class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div class="relative w-full sm:max-w-xs">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-text-muted">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input
                            type="search"
                            name="cari"
                            value="{{ $search ?? request('cari', '') }}"
                            placeholder="Cari nama atau email…"
                            aria-label="Cari nama atau email pengguna"
                            class="block w-full rounded-xl bg-white border border-surface-border pl-10 pr-3.5 py-2 text-sm text-text-slate placeholder:text-text-muted/70 focus:border-primary focus:ring-2 focus:ring-primary/20 focus:outline-none transition"
                        />
                    </div>
                </form>

                {{-- Tabel Pengguna --}}
                <div class="rounded-xl border border-[#EDF2FA] overflow-hidden">
                    <div class="overflow-x-auto custom-scrollbar">
                        <table class="w-full text-left text-sm text-text-slate border-collapse">
                            <thead class="bg-[#EEF4FE] border-b border-[#E3ECFA] text-[11px] uppercase tracking-wider font-bold text-text-muted select-none">
                                <tr>
                                    <th scope="col" class="px-4 py-3">PENGGUNA</th>
                                    <th scope="col" class="px-4 py-3">ROLE</th>
                                    <th scope="col" class="px-4 py-3">DIBUAT</th>
                                    <th scope="col" class="px-4 py-3">STATUS</th>
                                    <th scope="col" class="px-4 py-3">AKSI</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[#EFF3FA]">
                                @forelse ($daftarPengguna as $user)
                                    @php
                                        $adalahSaya = $user->id === ($actorId ?? auth()->id());
                                        $rolePengguna = (string) ($user->role?->nama ?? 'karyawan');
                                        $aktif = $user->isAktif();
                                    @endphp
                                    <tr class="odd:bg-white even:bg-[#F9FBFF] hover:bg-[#F3F7FF] transition-colors">
                                        <td class="px-4 py-3">
                                            <div class="flex items-center gap-3 min-w-0">
                                                @if ($user->fotoUrl())
                                                    <img
                                                        src="{{ $user->fotoUrl() }}"
                                                        alt="{{ $user->nama }}"
                                                        class="h-9 w-9 rounded-full object-cover border border-[#DBEAFE] shrink-0"
                                                    />
                                                @else
                                                    <div class="h-9 w-9 rounded-full bg-[#EBF2FF] text-[#1D4ED8] font-bold text-xs flex items-center justify-center shrink-0 select-none">
                                                        {{ inisial($user->nama) }}
                                                    </div>
                                                @endif

                                                <div class="min-w-0">
                                                    <div class="flex items-center gap-1.5 flex-wrap">
                                                        <span class="font-bold text-text-on-surface truncate">{{ $user->nama }}</span>
                                                        @if ($adalahSaya)
                                                            <span class="text-xs font-medium text-text-muted">(Anda)</span>
                                                        @endif
                                                    </div>
                                                    <p class="text-xs text-text-muted truncate">{{ $user->email }}</p>
                                                </div>
                                            </div>
                                        </td>

                                        <td class="px-4 py-3">
                                            <form method="POST" action="{{ route('admin.pengguna.role', $user->id) }}">
                                                @csrf
                                                @method('PATCH')
                                                @php
                                                    $roleOptions = collect($daftarRole)->mapWithKeys(fn ($opsiRole) => [
                                                        $opsiRole->value => $opsiRole->label(),
                                                    ])->all();
                                                @endphp
                                                <div class="min-w-[140px]">
                                                    <x-ui.select
                                                        name="role"
                                                        :compact="true"
                                                        :searchable="false"
                                                        :disabled="$adalahSaya"
                                                        :auto-submit="true"
                                                        :value="$rolePengguna"
                                                        :options="$roleOptions"
                                                        aria-label="Role {{ $user->nama }}"
                                                    />
                                                </div>
                                            </form>
                                        </td>

                                        <td class="px-4 py-3 text-xs text-text-body whitespace-nowrap">
                                            {{ $user->created_at ? tanggal_id($user->created_at) : '-' }}
                                        </td>

                                        <td class="px-4 py-3 whitespace-nowrap">
                                            @if ($aktif)
                                                <span class="inline-flex items-center rounded-full bg-[#EBF2FF] text-[#1D4ED8] px-3 py-1 text-xs font-semibold">
                                                    Aktif
                                                </span>
                                            @else
                                                <span class="inline-flex items-center rounded-full bg-[#F1F5F9] text-text-muted px-3 py-1 text-xs font-semibold">
                                                    Nonaktif
                                                </span>
                                            @endif
                                        </td>

                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <div class="inline-flex items-center gap-1.5">
                                                <button
                                                    type="button"
                                                    x-on:click="bukaEdit(@js(['id' => $user->id, 'nama' => $user->nama, 'email' => $user->email, 'no_hp' => (string) $user->no_hp, 'role' => $rolePengguna]))"
                                                    class="inline-flex items-center gap-1.5 rounded-xl border border-surface-border bg-white px-3 py-1.5 text-xs font-medium text-text-slate hover:bg-[#F8FAFC] transition cursor-pointer"
                                                >
                                                    <svg class="w-3.5 h-3.5 text-text-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125" />
                                                    </svg>
                                                    Edit
                                                </button>

                                                <form method="POST" action="{{ route('admin.pengguna.status', $user->id) }}" class="inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button
                                                        type="submit"
                                                        @disabled($adalahSaya)
                                                        class="inline-flex items-center rounded-xl border border-surface-border bg-white px-3 py-1.5 text-xs font-medium text-text-slate hover:bg-[#F8FAFC] transition cursor-pointer disabled:opacity-45 disabled:cursor-not-allowed"
                                                    >
                                                        {{ $aktif ? 'Nonaktifkan' : 'Aktifkan' }}
                                                    </button>
                                                </form>

                                                <button
                                                    type="button"
                                                    @disabled($adalahSaya)
                                                    x-on:click="bukaHapus(@js(['id' => $user->id, 'nama' => $user->nama, 'email' => $user->email]))"
                                                    title="Hapus {{ $user->nama }}"
                                                    aria-label="Hapus {{ $user->nama }}"
                                                    class="inline-flex h-8 w-8 items-center justify-center rounded-xl border border-surface-border bg-white text-text-muted hover:text-status-danger hover:border-red-200 hover:bg-status-danger-bg/50 transition cursor-pointer disabled:opacity-45 disabled:cursor-not-allowed"
                                                >
                                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="p-6">
                                            <x-ui.empty-state
                                                title="Pengguna tidak ditemukan"
                                                :description="trim($search ?? '') !== '' ? 'Tidak ada pengguna yang cocok dengan kata kunci pencarian Anda.' : 'Belum ada data pengguna di sistem.'"
                                            />
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if ($daftarPengguna->hasPages())
                        <div class="px-5 py-3 border-t border-[#EFF3FA] bg-[#F9FBFF]">
                            {{ $daftarPengguna->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Modal Tambah / Edit Pengguna --}}
        <div x-show="showFormModal" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-[#0F172A]/60 backdrop-blur-xs">
            <div x-on:click.outside="showFormModal = false" class="bg-surface-card border border-surface-border rounded-2xl shadow-xl w-full max-w-md overflow-hidden">
                <div class="flex items-center justify-between px-6 py-4 border-b border-surface-border">
                    <h3 class="text-base font-bold text-text-slate tracking-tight" x-text="editId ? 'Edit Pengguna' : 'Tambah Pengguna'"></h3>
                    <button type="button" x-on:click="showFormModal = false" class="rounded-lg p-1.5 text-text-muted hover:text-text-slate hover:bg-surface-container-low transition">✕</button>
                </div>

                <form method="POST" x-bind:action="editId ? '{{ url('/admin/pengguna') }}/' + editId : '{{ route('admin.pengguna.store') }}'" class="p-6 space-y-4">
                    @csrf
                    <template x-if="editId">
                        <input type="hidden" name="_method" value="PUT" />
                    </template>

                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-text-slate mb-1.5">Nama Lengkap <span class="text-status-danger">*</span></label>
                        <input type="text" name="nama" x-model="nama" required class="block w-full rounded-xl text-sm text-text-slate bg-surface-card border border-surface-border px-3.5 py-2.5 shadow-xs focus:border-primary focus:ring-2 focus:ring-primary/20 focus:outline-none" placeholder="Nama lengkap pengguna" />
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-text-slate mb-1.5">Email <span class="text-status-danger">*</span></label>
                        <input type="email" name="email" x-model="email" required class="block w-full rounded-xl text-sm text-text-slate bg-surface-card border border-surface-border px-3.5 py-2.5 shadow-xs focus:border-primary focus:ring-2 focus:ring-primary/20 focus:outline-none" placeholder="nama@gmail.com" />
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-text-slate mb-1.5">Nomor HP</label>
                        <input type="tel" name="no_hp" x-model="no_hp" class="block w-full rounded-xl text-sm text-text-slate bg-surface-card border border-surface-border px-3.5 py-2.5 shadow-xs focus:border-primary focus:ring-2 focus:ring-primary/20 focus:outline-none" placeholder="0812 3456 7890" />
                    </div>

                    <div>
                        @php
                            $modalRoleOptions = collect($daftarRole)->mapWithKeys(fn ($opsiRole) => [
                                $opsiRole->value => $opsiRole->label(),
                            ])->all();
                        @endphp
                        <x-ui.select
                            name="role"
                            label="Role"
                            :required="true"
                            :searchable="false"
                            disabled-expr="editId === actorId"
                            x-model="role"
                            :options="$modalRoleOptions"
                        />
                    </div>

                    <template x-if="editId !== actorId">
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-text-slate mb-1.5" x-text="editId ? 'Password Baru (Opsional)' : 'Password *'"></label>
                            <input type="password" name="password" x-bind:required="!editId" class="block w-full rounded-xl text-sm text-text-slate bg-surface-card border border-surface-border px-3.5 py-2.5 shadow-xs focus:border-primary focus:ring-2 focus:ring-primary/20 focus:outline-none" x-bind:placeholder="editId ? 'Kosongkan jika tidak ingin mengganti' : 'Minimal 8 karakter'" />
                        </div>
                    </template>

                    <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-surface-border">
                        <x-ui.button variant="outline" type="button" x-on:click="showFormModal = false">Batal</x-ui.button>
                        <x-ui.button variant="primary" type="submit">
                            <span x-text="editId ? 'Simpan Perubahan' : 'Simpan Pengguna'"></span>
                        </x-ui.button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Modal Konfirmasi Hapus --}}
        <div x-show="showHapusModal" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-[#0F172A]/60 backdrop-blur-xs">
            <div x-on:click.outside="showHapusModal = false" class="bg-surface-card border border-surface-border rounded-2xl shadow-xl w-full max-w-sm p-6 space-y-4">
                <h3 class="text-base font-bold text-text-slate">Hapus Pengguna</h3>
                <p class="text-sm text-text-body leading-relaxed">
                    Apakah Anda yakin ingin menghapus akun <span class="font-bold text-text-slate" x-text="hapusNama"></span> (<span x-text="hapusEmail"></span>)? Riwayat transaksi sebelumnya tetap terjaga di sistem.
                </p>

                <form method="POST" x-bind:action="'{{ url('/admin/pengguna') }}/' + hapusId" class="flex items-center justify-end gap-2.5 pt-3 border-t border-surface-border">
                    @csrf
                    @method('DELETE')
                    <x-ui.button variant="outline" type="button" x-on:click="showHapusModal = false">Batal</x-ui.button>
                    <x-ui.button variant="danger" type="submit">Hapus Pengguna</x-ui.button>
                </form>
            </div>
        </div>
    </div>
</x-layouts.app>
