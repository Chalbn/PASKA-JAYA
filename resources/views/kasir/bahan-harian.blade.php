<x-layouts.app title="Bahan Harian" role="kasir">
    <div
        class="space-y-6"
        x-data="{
            editId: {{ isset($editItem) && $editItem ? (int) $editItem->id : 'null' }},
            nama_bahan: @js(old('nama_bahan', $editItem?->nama_bahan ?? '')),
            jumlah: @js(old('jumlah', isset($editItem) && $editItem ? (float) $editItem->jumlah : '')),
            satuan: @js(old('satuan', $editItem?->satuan ?? '')),
            harga: @js(old('harga', $editItem?->harga ?? '')),
            tanggal: @js(old('tanggal', isset($editItem) && $editItem?->tanggal ? $editItem->tanggal->format('Y-m-d') : '')),
            keterangan: @js(old('keterangan', $editItem?->keterangan ?? '')),
            showDeleteModal: false,
            hapusId: null,
            hapusNama: '',
            mulaiEdit(item) {
                this.editId = item.id;
                this.nama_bahan = item.nama_bahan;
                this.jumlah = item.jumlah;
                this.satuan = item.satuan;
                this.harga = item.harga;
                this.tanggal = item.tanggal;
                this.keterangan = item.keterangan || '';
            },
            batalEdit() {
                this.editId = null;
                this.nama_bahan = '';
                this.jumlah = '';
                this.satuan = '';
                this.harga = '';
                this.tanggal = '';
                this.keterangan = '';
            },
            konfirmasiHapus(id, nama) {
                this.hapusId = id;
                this.hapusNama = nama;
                this.showDeleteModal = true;
            }
        }"
    >
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="text-xl sm:text-2xl font-black text-text-slate tracking-tight">Bahan Harian</h2>
                <p class="text-xs sm:text-sm text-text-muted mt-0.5">
                    Catat pembelian bahan operasional harian toko dan armada pengantaran.
                </p>
            </div>

            <div class="inline-flex items-center gap-2.5 px-4 py-2.5 rounded-2xl bg-surface-card border border-surface-border shadow-xs">
                <span class="text-xs font-semibold text-text-muted">Total Pengeluaran:</span>
                <span class="text-base sm:text-lg font-black text-primary tabular-nums">
                    {{ rupiah($totalPengeluaran ?? 0) }}
                </span>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            <div class="lg:col-span-5">
                <x-ui.card padding="p-5 sm:p-6">
                    <x-slot:header>
                        <h3 class="text-base font-bold text-text-slate tracking-tight" x-text="editId ? 'Edit Bahan' : 'Tambah bahan'"></h3>
                    </x-slot:header>

                    <form method="POST" x-bind:action="editId ? '{{ url('/kasir/bahan-harian') }}/' + editId : '{{ route('kasir.bahan-harian.store') }}'" class="space-y-4">
                        @csrf
                        <template x-if="editId">
                            <input type="hidden" name="_method" value="PUT" />
                        </template>

                        @if($errors->has('error_wajib') || $errors->has('nama_bahan') || $errors->has('jumlah') || $errors->has('harga'))
                            <div class="p-3.5 rounded-xl bg-status-danger-bg text-status-danger border border-red-200 text-xs font-bold flex items-center gap-2 shadow-xs">
                                <span>Nama bahan, jumlah, dan harga wajib diisi.</span>
                            </div>
                        @endif

                        <div>
                            <x-ui.input
                                name="nama_bahan"
                                label="Nama Bahan"
                                placeholder="mis. Bensin pick-up, tali rafia"
                                x-model="nama_bahan"
                                :required="true"
                            />
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <x-ui.input
                                    name="jumlah"
                                    label="Jumlah"
                                    type="number"
                                    step="0.01"
                                    min="0.01"
                                    placeholder="mis. 5"
                                    x-model="jumlah"
                                    :required="true"
                                />
                            </div>

                            <div>
                                <x-ui.input
                                    name="satuan"
                                    label="Satuan"
                                    placeholder="mis. liter, roll, dus"
                                    x-model="satuan"
                                    :required="true"
                                />
                            </div>
                        </div>

                        <div>
                            <x-ui.input
                                name="harga"
                                label="Harga (Rp)"
                                type="number"
                                min="0"
                                placeholder="mis. 50000"
                                x-model="harga"
                                suffix="Rp"
                                :required="true"
                            />
                        </div>

                        <div>
                            <x-ui.input
                                name="tanggal"
                                label="Tanggal"
                                type="date"
                                x-model="tanggal"
                                :required="true"
                            />
                        </div>

                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-text-slate mb-1.5">
                                Keterangan (Opsional)
                            </label>
                            <textarea
                                name="keterangan"
                                x-model="keterangan"
                                rows="2"
                                placeholder="Catatan tambahan keperluan bahan operasional"
                                class="block w-full rounded-xl text-sm text-text-slate placeholder:text-text-muted/60 transition duration-150 ease-in-out border border-surface-border bg-surface-card p-3 shadow-xs focus:outline-none focus:ring-2 focus:ring-primary/20"
                            ></textarea>
                        </div>

                        <div class="pt-2 flex items-center gap-2.5">
                            <x-ui.button type="submit" variant="primary" size="lg" class="flex-1 font-bold">
                                <span x-text="editId ? 'Simpan Perubahan' : 'Tambah'"></span>
                            </x-ui.button>

                            <template x-if="editId">
                                <x-ui.button type="button" variant="outline" size="lg" x-on:click="batalEdit()">
                                    Batal
                                </x-ui.button>
                            </template>
                        </div>
                    </form>
                </x-ui.card>
            </div>

            <div class="lg:col-span-7 space-y-4">
                <x-ui.card padding="p-0">
                    <x-slot:header>
                        <div>
                            <h3 class="text-base font-bold text-text-slate tracking-tight">Daftar Bahan Harian</h3>
                            <p class="text-xs text-text-muted mt-0.5">
                                Total pengeluaran: <strong class="text-primary font-bold tabular-nums">{{ rupiah($totalPengeluaran ?? 0) }}</strong>
                            </p>
                        </div>
                        <div class="shrink-0">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-surface-container text-primary border border-blue-200">
                                {{ count($daftarBahan ?? []) }} Baris
                            </span>
                        </div>
                    </x-slot:header>

                    <div class="overflow-x-auto max-h-[520px] overflow-y-auto custom-scrollbar">
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
                                @forelse($daftarBahan as $item)
                                    <tr class="hover:bg-surface-container-low/50 transition-colors">
                                        <td class="px-4 py-3 text-xs text-text-muted whitespace-nowrap tabular-nums font-medium">
                                            {{ $item->tanggal ? $item->tanggal->timezone('Asia/Jakarta')->format('d/m/Y') : '-' }}
                                        </td>
                                        <td class="px-4 py-3 font-bold text-text-slate">{{ $item->nama_bahan }}</td>
                                        <td class="px-4 py-3 text-right text-xs whitespace-nowrap tabular-nums">
                                            <span class="font-bold text-text-on-surface">{{ (float) $item->jumlah }}</span>
                                            <span class="text-text-muted text-[11px] ml-0.5">{{ $item->satuan }}</span>
                                        </td>
                                        <td class="px-4 py-3 text-right text-xs font-black text-text-slate whitespace-nowrap tabular-nums">
                                            {{ rupiah($item->harga) }}
                                        </td>
                                        <td class="px-4 py-3 text-xs text-text-muted max-w-[160px] truncate" title="{{ $item->keterangan }}">
                                            {{ $item->keterangan ?: '-' }}
                                        </td>
                                        <td class="px-4 py-3 text-center whitespace-nowrap">
                                            <div class="inline-flex items-center gap-1.5">
                                                <button
                                                    type="button"
                                                    x-on:click="mulaiEdit(@js([
                                                        'id' => $item->id,
                                                        'nama_bahan' => $item->nama_bahan,
                                                        'jumlah' => (float) $item->jumlah,
                                                        'satuan' => $item->satuan,
                                                        'harga' => (int) $item->harga,
                                                        'tanggal' => $item->tanggal ? $item->tanggal->format('Y-m-d') : now()->toDateString(),
                                                        'keterangan' => $item->keterangan,
                                                    ]))"
                                                    class="p-1.5 rounded-lg text-primary hover:bg-surface-container transition cursor-pointer"
                                                    title="Edit bahan"
                                                >
                                                    ✎
                                                </button>

                                                <button
                                                    type="button"
                                                    x-on:click="konfirmasiHapus({{ $item->id }}, @js($item->nama_bahan))"
                                                    class="p-1.5 rounded-lg text-status-danger hover:bg-red-50 transition cursor-pointer"
                                                    title="Hapus bahan"
                                                >
                                                    🗑
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
                                            />
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
        <div x-show="showDeleteModal" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
            <div x-on:click.outside="showDeleteModal = false" class="bg-surface-card border border-surface-border rounded-2xl shadow-xl max-w-sm w-full p-5 space-y-4">
                <h4 class="text-sm font-bold text-text-slate">Hapus Bahan Harian?</h4>
                <p class="text-xs text-text-muted leading-relaxed">
                    Apakah Anda yakin ingin menghapus catatan <strong x-text="hapusNama"></strong>? Tindakan ini tidak dapat dibatalkan.
                </p>

                <form method="POST" x-bind:action="'{{ url('/kasir/bahan-harian') }}/' + hapusId" class="flex items-center justify-end gap-2.5 pt-2 border-t border-surface-border">
                    @csrf
                    @method('DELETE')
                    <x-ui.button type="button" variant="outline" size="sm" x-on:click="showDeleteModal = false">Batal</x-ui.button>
                    <x-ui.button type="submit" variant="danger" size="sm">Ya, hapus</x-ui.button>
                </form>
            </div>
        </div>
    </div>
</x-layouts.app>
