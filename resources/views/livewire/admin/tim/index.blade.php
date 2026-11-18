<div class="-m-4 sm:-m-6 lg:-m-8 p-4 sm:p-6 lg:p-8 relative min-h-[calc(100vh-5rem)] overflow-hidden bg-gradient-to-br from-[#F4F7FE] via-[#EDF3FF] to-[#DBE8FF] flex flex-col justify-between">
    {{-- Lapisan Latar Belakang Transparan untuk Foto Bersama Tim --}}
    <div class="pointer-events-none select-none absolute inset-0 overflow-hidden" aria-hidden="true">
        @if ($fotoBersamaUrl)
            <img
                src="{{ $fotoBersamaUrl }}"
                alt="Foto bersama tim PASKA JAYA"
                class="h-full w-full object-cover object-center opacity-35"
            />
            <div class="absolute inset-0 bg-gradient-to-b from-[#F4F7FE]/70 via-[#EEF4FF]/35 to-[#D9E6FF]/65"></div>
        @else
            <div class="absolute -top-24 -right-24 h-96 w-96 rounded-full bg-primary/10 blur-3xl"></div>
            <div class="absolute bottom-0 left-1/4 h-80 w-80 rounded-full bg-sky-300/20 blur-3xl"></div>
            <div class="absolute -bottom-20 -right-12 h-96 w-96 rounded-full bg-indigo-300/20 blur-3xl"></div>
        @endif
    </div>

    {{-- Konten Utama --}}
    <div class="relative z-10 space-y-6">
        {{-- Header Halaman --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-text-on-surface tracking-tight">Tim</h1>
                <p class="mt-1 text-xs sm:text-sm text-text-muted">Anggota tim PASKA JAYA.</p>
            </div>

            <div class="shrink-0">
                <button
                    type="button"
                    wire:click="bukaModalFotoBersama"
                    class="inline-flex items-center gap-2 rounded-xl bg-white/75 backdrop-blur-md border border-white/85 px-3.5 py-2 text-xs sm:text-sm font-semibold text-text-slate hover:bg-white shadow-xs transition cursor-pointer"
                >
                    <svg class="w-4 h-4 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                    </svg>
                    {{ $fotoBersamaUrl ? 'Ganti Foto Bersama' : 'Foto Bersama' }}
                </button>
            </div>
        </div>

        {{-- 5 Kartu Anggota Tim (Tampilan Langsung Seperti Referensi) --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4 sm:gap-5">
            @foreach ($daftarKartu as $kartu)
                @php
                    $tints = [
                        1 => 'from-sky-100/75 via-blue-50/45 to-white/70',
                        2 => 'from-amber-50/75 via-orange-50/40 to-white/70',
                        3 => 'from-teal-50/75 via-cyan-50/45 to-white/70',
                        4 => 'from-rose-50/70 via-purple-50/40 to-white/70',
                        5 => 'from-indigo-100/70 via-sky-50/45 to-white/70',
                    ];
                    $cardTint = $tints[$kartu['slot']] ?? $tints[1];
                @endphp
                <div
                    wire:key="kartu-tim-{{ $kartu['slot'] }}"
                    class="group relative rounded-2xl bg-gradient-to-b {{ $cardTint }} backdrop-blur-md border border-white/85 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 px-5 pt-7 pb-5 flex flex-col items-center justify-between text-center"
                >
                    <div class="flex flex-col items-center w-full">
                        @if ($kartu['foto_url'])
                            <img
                                src="{{ $kartu['foto_url'] }}"
                                alt="{{ $kartu['nama'] }}"
                                class="h-24 w-24 rounded-3xl object-cover border-2 border-primary/35 shadow-xs bg-white/85"
                            />
                        @else
                            <div class="h-24 w-24 rounded-3xl bg-white/85 backdrop-blur-xs border-2 border-primary/35 shadow-xs flex items-center justify-center text-2xl font-extrabold tracking-tight text-text-on-surface select-none">
                                {{ $kartu['inisial'] }}
                            </div>
                        @endif

                        <h2 class="mt-4 text-sm sm:text-base font-bold text-text-slate leading-snug line-clamp-2">
                            {{ $kartu['nama'] }}
                        </h2>
                        <p class="mt-1 text-xs font-medium text-text-muted leading-snug line-clamp-2">
                            {{ $kartu['jabatan'] }}
                        </p>
                    </div>

                    <div class="mt-4 pt-1">
                        <button
                            type="button"
                            wire:click="bukaModalEdit({{ $kartu['slot'] }})"
                            title="Edit {{ $kartu['nama'] }}"
                            aria-label="Edit {{ $kartu['nama'] }}"
                            class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-surface-border/80 bg-white/85 text-text-body hover:bg-primary hover:text-white hover:border-primary shadow-2xs transition cursor-pointer"
                        >
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125" />
                            </svg>
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Indikator Halus Area Foto Bersama Tim --}}
    <div class="relative z-10 pt-8 flex items-center justify-center sm:justify-end">
        <button
            type="button"
            wire:click="bukaModalFotoBersama"
            class="inline-flex items-center gap-2 rounded-full bg-white/55 backdrop-blur-md border border-white/80 px-3.5 py-1.5 text-[11px] font-semibold text-text-muted hover:text-text-slate hover:bg-white/85 shadow-2xs transition cursor-pointer"
        >
            <svg class="w-3.5 h-3.5 text-primary/80" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0z" />
            </svg>
            {{ $fotoBersamaUrl ? 'Latar: Foto Bersama Tim (Klik untuk ganti)' : 'Tempat Foto Bersama Tim (Klik untuk unggah)' }}
        </button>
    </div>

    {{-- Modal Foto Bersama Tim (Background Transparan) --}}
    <x-ui.modal
        wire:model="showFotoBersamaModal"
        name="foto-bersama-tim"
        title="Foto Bersama Tim"
        maxWidth="md"
    >
        <form wire:submit="simpanFotoBersama" class="space-y-4" novalidate>
            <p class="text-xs text-text-muted leading-relaxed">
                Foto bersama akan tampil secara transparan sebagai latar belakang di halaman Tim.
            </p>

            <div class="relative aspect-video w-full overflow-hidden rounded-2xl border border-dashed border-surface-border bg-gradient-to-br from-[#F4F7FE] to-[#DCE8FF]">
                @if ($fotoBersama)
                    <img
                        src="{{ $fotoBersama->temporaryUrl() }}"
                        alt="Pratinjau foto bersama tim"
                        class="h-full w-full object-cover opacity-50"
                    />
                    <div class="absolute inset-0 bg-gradient-to-b from-white/35 to-sky-100/40 pointer-events-none"></div>
                    <span class="absolute bottom-2.5 right-2.5 rounded-full bg-white/80 backdrop-blur-xs px-2.5 py-1 text-[11px] font-semibold text-text-slate shadow-2xs">
                        Pratinjau latar transparan
                    </span>
                @elseif ($fotoBersamaUrl)
                    <img
                        src="{{ $fotoBersamaUrl }}"
                        alt="Foto bersama tim saat ini"
                        class="h-full w-full object-cover opacity-45"
                    />
                    <div class="absolute inset-0 bg-gradient-to-b from-white/35 to-sky-100/40 pointer-events-none"></div>
                    <span class="absolute bottom-2.5 right-2.5 rounded-full bg-white/80 backdrop-blur-xs px-2.5 py-1 text-[11px] font-semibold text-text-slate shadow-2xs">
                        Foto aktif saat ini
                    </span>
                @else
                    <div class="flex h-full flex-col items-center justify-center p-4 text-center">
                        <svg class="w-8 h-8 text-primary/60 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5z" />
                        </svg>
                        <p class="text-xs font-semibold text-text-slate">Belum ada foto bersama tim</p>
                        <p class="text-[11px] text-text-muted mt-0.5">Unggah gambar lanskap (JPG/PNG/WEBP, maks 4 MB).</p>
                    </div>
                @endif
            </div>

            <div>
                <label for="tim-foto-bersama" class="block text-xs font-semibold uppercase tracking-wider text-text-slate mb-1.5">
                    Pilih Foto Bersama
                </label>
                <input
                    id="tim-foto-bersama"
                    type="file"
                    wire:model="fotoBersama"
                    accept="image/png,image/jpeg,image/webp"
                    class="block w-full text-xs text-text-muted file:mr-3 file:py-2 file:px-3.5 file:rounded-xl file:border file:border-surface-border file:text-xs file:font-semibold file:bg-surface-container-low file:text-text-slate hover:file:bg-surface-container cursor-pointer"
                />
                @error('fotoBersama')
                    <p class="mt-1.5 text-xs text-status-danger font-medium">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-between gap-2.5 pt-3 border-t border-surface-border">
                <div>
                    @if ($fotoBersamaUrl)
                        <x-ui.button
                            variant="danger"
                            type="button"
                            size="sm"
                            wire:click="hapusFotoBersama"
                            wire:loading.attr="disabled"
                            wire:target="hapusFotoBersama"
                            class="cursor-pointer"
                        >
                            Hapus Foto
                        </x-ui.button>
                    @endif
                </div>

                <div class="flex items-center gap-2">
                    <x-ui.button variant="outline" type="button" wire:click="$set('showFotoBersamaModal', false)" class="cursor-pointer">
                        Batal
                    </x-ui.button>

                    <x-ui.button variant="primary" type="submit" wire:loading.attr="disabled" wire:target="simpanFotoBersama,fotoBersama" class="cursor-pointer">
                        <span wire:loading.remove wire:target="simpanFotoBersama">Simpan Foto</span>
                        <span wire:loading wire:target="simpanFotoBersama">Mengunggah…</span>
                    </x-ui.button>
                </div>
            </div>
        </form>
    </x-ui.modal>

    {{-- Modal Edit Kartu Anggota Tim --}}
    <x-ui.modal
        wire:model="showFormModal"
        name="form-anggota-tim"
        title="Edit Anggota Tim"
        maxWidth="md"
    >
        <form wire:submit="simpan" class="space-y-4" novalidate>
            <x-ui.input
                name="nama"
                wire:model="nama"
                label="Nama Lengkap"
                placeholder="Contoh: Budi Santoso"
                :required="true"
            />

            <x-ui.input
                name="jabatan"
                wire:model="jabatan"
                label="Jabatan"
                placeholder="Contoh: Kepala Gudang"
                :required="true"
            />

            <div>
                <label for="tim-foto" class="block text-xs font-semibold uppercase tracking-wider text-text-slate mb-1.5">
                    Foto Anggota (Opsional)
                </label>

                <div class="flex items-center gap-4">
                    @if ($foto)
                        <img
                            src="{{ $foto->temporaryUrl() }}"
                            alt="Pratinjau foto"
                            class="h-14 w-14 rounded-xl object-cover border border-surface-border shrink-0"
                        />
                    @elseif ($fotoLama && ! $hapusFotoSaatIni)
                        <img
                            src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($fotoLama) }}"
                            alt="{{ $nama }}"
                            class="h-14 w-14 rounded-xl object-cover border border-surface-border shrink-0"
                        />
                    @else
                        <div class="h-14 w-14 rounded-xl bg-surface-container text-primary font-bold text-base flex items-center justify-center shrink-0 select-none">
                            {{ inisial($nama) }}
                        </div>
                    @endif

                    <div class="flex-1 space-y-1.5">
                        <input
                            id="tim-foto"
                            type="file"
                            wire:model="foto"
                            accept="image/png,image/jpeg,image/webp"
                            class="block w-full text-xs text-text-muted file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border file:border-surface-border file:text-xs file:font-semibold file:bg-surface-container-low file:text-text-slate hover:file:bg-surface-container cursor-pointer"
                        />
                        @if ($fotoLama && ! $hapusFotoSaatIni && ! $foto)
                            <button
                                type="button"
                                wire:click="$set('hapusFotoSaatIni', true)"
                                class="text-xs font-semibold text-status-danger hover:underline cursor-pointer"
                            >
                                Hapus foto saat ini (pakai inisial)
                            </button>
                        @endif
                    </div>
                </div>

                @error('foto')
                    <p class="mt-1.5 text-xs text-status-danger font-medium">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-surface-border">
                <x-ui.button variant="outline" type="button" wire:click="$set('showFormModal', false)" class="cursor-pointer">
                    Batal
                </x-ui.button>

                <x-ui.button variant="primary" type="submit" wire:loading.attr="disabled" wire:target="simpan,foto" class="cursor-pointer">
                    <span wire:loading.remove wire:target="simpan">Simpan Perubahan</span>
                    <span wire:loading wire:target="simpan">Menyimpan…</span>
                </x-ui.button>
            </div>
        </form>
    </x-ui.modal>
</div>
