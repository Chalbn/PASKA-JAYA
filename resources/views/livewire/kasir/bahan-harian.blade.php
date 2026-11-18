<div class="space-y-6">
    {{-- Header Halaman --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h2 class="text-xl sm:text-2xl font-black text-text-slate tracking-tight">
                Bahan Harian
            </h2>
            <p class="text-xs sm:text-sm text-text-muted mt-0.5">
                Catat pembelian bahan operasional harian toko dan armada pengantaran.
            </p>
        </div>

        {{-- Badge Ringkasan Total Pengeluaran --}}
        <div class="inline-flex items-center gap-2.5 px-4 py-2.5 rounded-2xl bg-surface-card border border-surface-border shadow-xs">
            <span class="text-xs font-semibold text-text-muted">Total Pengeluaran:</span>
            <span class="text-base sm:text-lg font-black text-primary tabular-nums">
                {{ rupiah($this->totalPengeluaran) }}
            </span>
        </div>
    </div>

    {{-- Layout Dua Kolom: Kiri (Form Tambah/Edit), Kanan (Daftar & Total) --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

        {{-- Kartu Kiri: Tambah/Edit Bahan --}}
        <div class="lg:col-span-5">
            <x-ui.card :title="$editId ? 'Edit Bahan' : 'Tambah bahan'" padding="p-5 sm:p-6">
                <form wire:submit="simpan" class="space-y-4">

                    {{-- Banner Error Merah Sesuai Spesifikasi Prompt --}}
                    @if($errors->has('error_wajib') || $errors->has('nama_bahan') || $errors->has('jumlah') || $errors->has('harga'))
                        <div class="p-3.5 rounded-xl bg-status-danger-bg text-status-danger border border-red-200 text-xs font-bold flex items-center gap-2 shadow-xs">
                            <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                            <span>Nama bahan, jumlah, dan harga wajib diisi.</span>
                        </div>
                    @endif

                    {{-- Nama Bahan --}}
                    <div>
                        <x-ui.input
                            name="nama_bahan"
                            label="Nama Bahan"
                            placeholder="mis. Bensin pick-up, tali rafia"
                            wire:model="nama_bahan"
                            :required="true"
                        />
                    </div>

                    {{-- Jumlah & Satuan --}}
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <x-ui.input
                                name="jumlah"
                                label="Jumlah"
                                type="number"
                                step="0.01"
                                min="0.01"
                                placeholder="mis. 5"
                                wire:model="jumlah"
                                :required="true"
                            />
                        </div>

                        <div>
                            <x-ui.input
                                name="satuan"
                                label="Satuan"
                                placeholder="mis. liter, roll, dus"
                                wire:model="satuan"
                                :required="true"
                            />
                        </div>
                    </div>

                    {{-- Harga (Rp) --}}
                    <div>
                        <x-ui.input
                            name="harga"
                            label="Harga (Rp)"
                            type="number"
                            min="0"
                            placeholder="mis. 50000"
                            wire:model="harga"
                            suffix="Rp"
                            :required="true"
                        />
                    </div>

                    {{-- Tanggal --}}
                    <div>
                        <x-ui.input
                            name="tanggal"
                            label="Tanggal"
                            type="date"
                            wire:model="tanggal"
                            :required="true"
                        />
                    </div>

                    {{-- Keterangan --}}
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-text-slate mb-1.5">
                            Keterangan (Opsional)
                        </label>
                        <textarea
                            wire:model="keterangan"
                            rows="2"
                            placeholder="Catatan tambahan keperluan bahan operasional"
                            class="block w-full rounded-xl text-sm text-text-slate placeholder:text-text-muted/60 transition duration-150 ease-in-out border border-surface-border hover:border-surface-border-subtle focus:border-primary focus:ring-primary/20 bg-surface-card p-3 shadow-xs focus:outline-none focus:ring-2"
                        ></textarea>
                    </div>

                    {{-- Tombol Aksi --}}
                    <div class="pt-2 flex items-center gap-2.5">
                        <x-ui.button
                            type="submit"
                            variant="primary"
                            size="lg"
                            class="flex-1 font-bold"
                            wire:loading.attr="disabled"
                        >
                            <span wire:loading.remove wire:target="simpan">
                                {{ $editId ? 'Simpan Perubahan' : 'Tambah' }}
                            </span>
                            <span wire:loading wire:target="simpan" class="inline-flex items-center gap-2">
                                <x-ui.spinner size="sm" />
                                <span>Menyimpan...</span>
                            </span>
                        </x-ui.button>

                        @if($editId)
                            <x-ui.button
                                type="button"
                                variant="outline"
                                size="lg"
                                wire:click="batal"
                            >
                                Batal
                            </x-ui.button>
                        @endif
                    </div>
                </form>
            </x-ui.card>
        </div>

        {{-- Kartu Kanan: Daftar Bahan Harian + Total Pengeluaran --}}
        <div class="lg:col-span-7 space-y-4">
            <x-ui.card padding="p-0">
                <x-slot:header>
                    <div>
                        <h3 class="text-base font-bold text-text-slate tracking-tight">Daftar Bahan Harian</h3>
                        <p class="text-xs text-text-muted mt-0.5">
                            Total pengeluaran: <strong class="text-primary font-bold tabular-nums">{{ rupiah($this->totalPengeluaran) }}</strong>
                        </p>
                    </div>
                    <div class="shrink-0">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-surface-container text-primary border border-blue-200">
                            {{ count($this->daftarBahan) }} Baris
                        </span>
                    </div>
                </x-slot:header>

                <div class="overflow-x-auto custom-scrollbar">
                    <table class="w-full text-left text-sm text-text-slate border-collapse">
                        <thead class="bg-surface-container-low/80 border-b border-surface-border text-[11px] uppercase tracking-wider font-bold text-text-muted select-none">
                            <tr>
                                <th class="px-4 py-3">Tanggal</th>
                                <th class="px-4 py-3">Bahan</th>
                                <th class="px-4 py-3 text-right">Jumlah</th>
                                <th class="px-4 py-3 text-right">Harga</th>
                                <th class="px-4 py-3">Keterangan</th>
                                <th class="px-4 py-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-surface-border">
                            @forelse($this->daftarBahan as $item)
                                <tr class="hover:bg-surface-container-low/50 transition-colors" wire:key="bahan-{{ $item->id }}">
                                    {{-- Tanggal --}}
                                    <td class="px-4 py-3 text-xs text-text-muted whitespace-nowrap tabular-nums font-medium">
                                        {{ $item->tanggal ? $item->tanggal->timezone('Asia/Jakarta')->format('d/m/Y') : '-' }}
                                    </td>

                                    {{-- Bahan --}}
                                    <td class="px-4 py-3 font-bold text-text-slate">
                                        {{ $item->nama_bahan }}
                                    </td>

                                    {{-- Jumlah --}}
                                    <td class="px-4 py-3 text-right text-xs whitespace-nowrap tabular-nums">
                                        <span class="font-bold text-text-on-surface">{{ (float) $item->jumlah }}</span>
                                        <span class="text-text-muted text-[11px] ml-0.5">{{ $item->satuan }}</span>
                                    </td>

                                    {{-- Harga --}}
                                    <td class="px-4 py-3 text-right text-xs font-black text-text-slate whitespace-nowrap tabular-nums">
                                        {{ rupiah($item->harga) }}
                                    </td>

                                    {{-- Keterangan --}}
                                    <td class="px-4 py-3 text-xs text-text-muted max-w-[160px] truncate" title="{{ $item->keterangan }}">
                                        {{ $item->keterangan ?: '-' }}
                                    </td>

                                    {{-- Aksi --}}
                                    <td class="px-4 py-3 text-center whitespace-nowrap">
                                        <div class="inline-flex items-center gap-1.5">
                                            {{-- Tombol Edit --}}
                                            <button
                                                type="button"
                                                wire:click="edit({{ $item->id }})"
                                                class="p-1.5 rounded-lg text-primary hover:bg-surface-container transition"
                                                title="Edit bahan"
                                            >
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                                </svg>
                                            </button>

                                            {{-- Tombol Hapus --}}
                                            <button
                                                type="button"
                                                wire:click="konfirmasiHapus({{ $item->id }})"
                                                class="p-1.5 rounded-lg text-status-danger hover:bg-red-50 transition"
                                                title="Hapus bahan"
                                            >
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="p-8">
                                        <x-ui.empty-state
                                            title="Belum ada bahan harian"
                                            description="Bahan operasional harian yang dicatat kasir akan muncul di tabel ini."
                                        >
                                            <x-slot:icon>
                                                <svg class="h-7 w-7 text-primary/80" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                                                </svg>
                                            </x-slot:icon>
                                        </x-ui.empty-state>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-ui.card>
        </div>
    </div>

    {{-- Modal Konfirmasi Hapus --}}
    @if($showDeleteModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
            <div class="bg-surface-card border border-surface-border rounded-2xl shadow-xl max-w-sm w-full p-5 space-y-4">
                <div class="flex items-start gap-3.5">
                    <div class="p-3 rounded-2xl bg-status-danger-bg text-status-danger shrink-0">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-text-slate">Hapus Bahan Harian?</h4>
                        <p class="text-xs text-text-muted mt-1 leading-relaxed">
                            Apakah Anda yakin ingin menghapus catatan <strong>{{ $this->bahanYangDihapus?->nama_bahan }}</strong>? Tindakan ini tidak dapat dibatalkan.
                        </p>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2.5 pt-2 border-t border-surface-border">
                    <x-ui.button
                        type="button"
                        variant="outline"
                        size="sm"
                        wire:click="batalHapus"
                    >
                        Batal
                    </x-ui.button>

                    <x-ui.button
                        type="button"
                        variant="danger"
                        size="sm"
                        wire:click="hapus"
                    >
                        Ya, hapus
                    </x-ui.button>
                </div>
            </div>
        </div>
    @endif
</div>
