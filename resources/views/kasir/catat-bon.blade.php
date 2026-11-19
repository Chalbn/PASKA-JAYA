<x-layouts.app title="Catat Bon" role="kasir">
    <div
        class="py-6"
        x-data="{
            masterItems: @js($masterItems->map(fn($i) => [
                'id' => $i->id,
                'nama_barang' => $i->nama_barang ?? $i->nama,
                'harga_jual' => (int) ($i->harga_jual ?? 0),
                'stok' => (int) ($i->stok ?? 0),
            ])->values()),
            selectedItemId: '',
            jumlah: 1,
            items: [],
            tambahItem() {
                const id = parseInt(this.selectedItemId);
                const qty = parseInt(this.jumlah) || 1;
                if (!id || qty < 1) return;
                const master = this.masterItems.find(m => m.id === id);
                if (!master) return;

                const idx = this.items.findIndex(c => c.barang_id === id);
                if (idx >= 0) {
                    this.items[idx].jumlah += qty;
                    this.items[idx].subtotal = this.items[idx].jumlah * this.items[idx].harga_satuan;
                } else {
                    this.items.push({
                        barang_id: master.id,
                        nama_barang: master.nama_barang,
                        jumlah: qty,
                        harga_satuan: master.harga_jual,
                        subtotal: qty * master.harga_jual,
                    });
                }
                this.selectedItemId = '';
                this.jumlah = 1;
            },
            hapusItem(idx) {
                this.items.splice(idx, 1);
            },
            formatRp(val) {
                return 'Rp ' + Math.round(Number(val) || 0).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            }
        }"
    >
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl p-6 border border-surface-border">
                <h2 class="text-2xl font-bold mb-4 text-gray-800">Catat Bon & Piutang Baru</h2>

                @if (session()->has('error'))
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-xl relative mb-4 text-xs font-semibold">
                        {{ session('error') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('kasir.bon.store') }}">
                    @csrf

                    <template x-for="(cartItem, idx) in items" :key="cartItem.barang_id">
                        <div>
                            <input type="hidden" x-bind:name="`items[${idx}][barang_id]`" x-bind:value="cartItem.barang_id" />
                            <input type="hidden" x-bind:name="`items[${idx}][jumlah]`" x-bind:value="cartItem.jumlah" />
                        </div>
                    </template>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nama Pelanggan</label>
                            <input type="text" name="nama_pelanggan" value="{{ old('nama_pelanggan') }}" class="mt-1 block w-full rounded-xl border-gray-300 shadow-xs border p-2.5 text-sm" required />
                            @error('nama_pelanggan') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Kontak / No. HP</label>
                            <input type="text" name="no_hp" value="{{ old('no_hp') }}" class="mt-1 block w-full rounded-xl border-gray-300 shadow-xs border p-2.5 text-sm" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Alamat</label>
                            <input type="text" name="alamat" value="{{ old('alamat') }}" class="mt-1 block w-full rounded-xl border-gray-300 shadow-xs border p-2.5 text-sm" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Tanggal Bon</label>
                            <input type="date" name="tanggal_bon" value="{{ old('tanggal_bon', date('Y-m-d')) }}" class="mt-1 block w-full rounded-xl border-gray-300 shadow-xs border p-2.5 text-sm" required />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Jatuh Tempo</label>
                            <input type="date" name="jatuh_tempo" value="{{ old('jatuh_tempo', date('Y-m-d', strtotime('+30 days'))) }}" class="mt-1 block w-full rounded-xl border-gray-300 shadow-xs border p-2.5 text-sm" required />
                        </div>
                    </div>

                    <h3 class="text-lg font-semibold mb-3 text-gray-700">Daftar Barang</h3>
                    <div class="flex flex-col md:flex-row gap-4 mb-4 items-end bg-gray-50 p-4 rounded-xl">
                        <div class="flex-1">
                            @php
                                $bonItemOptions = $masterItems->map(fn ($item) => [
                                    'value' => (string) $item->id,
                                    'label' => ($item->nama_barang ?? $item->nama) . ' (Stok: ' . $item->stok . ') - Rp ' . number_format($item->harga_jual ?? 0, 0, ',', '.'),
                                    'disabled' => $item->stok <= 0,
                                ])->values()->all();
                            @endphp
                            <x-ui.select
                                x-model="selectedItemId"
                                label="Pilih Barang"
                                placeholder="-- Pilih Barang Toko --"
                                :options="$bonItemOptions"
                                :searchable="true"
                            />
                        </div>
                        <div class="w-32">
                            <label class="block text-sm font-medium text-gray-700">Jumlah</label>
                            <input type="number" x-model="jumlah" min="1" class="mt-1 block w-full rounded-xl border-gray-300 shadow-xs border p-2.5 text-sm" />
                        </div>
                        <div>
                            <button type="button" x-on:click="tambahItem()" class="bg-blue-600 text-white px-4 py-2.5 rounded-xl hover:bg-blue-700 transition text-sm font-semibold cursor-pointer">Tambah Item</button>
                        </div>
                    </div>

                    <div class="overflow-x-auto mb-6">
                        <table class="min-w-full divide-y divide-gray-200 border rounded-xl overflow-hidden">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="px-4 py-2.5 text-left text-xs font-medium text-gray-500 uppercase">Nama Barang</th>
                                    <th class="px-4 py-2.5 text-center text-xs font-medium text-gray-500 uppercase">Jumlah</th>
                                    <th class="px-4 py-2.5 text-right text-xs font-medium text-gray-500 uppercase">Harga Satuan</th>
                                    <th class="px-4 py-2.5 text-right text-xs font-medium text-gray-500 uppercase">Subtotal</th>
                                    <th class="px-4 py-2.5 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <template x-if="items.length === 0">
                                    <tr>
                                        <td colspan="5" class="px-4 py-4 text-center text-gray-500 text-sm">Belum ada barang yang ditambahkan ke keranjang bon.</td>
                                    </tr>
                                </template>
                                <template x-for="(cartItem, idx) in items" :key="cartItem.barang_id">
                                    <tr>
                                        <td class="px-4 py-2.5 text-sm text-gray-900" x-text="cartItem.nama_barang"></td>
                                        <td class="px-4 py-2.5 text-sm text-gray-900 text-center" x-text="cartItem.jumlah"></td>
                                        <td class="px-4 py-2.5 text-sm text-gray-900 text-right" x-text="formatRp(cartItem.harga_satuan)"></td>
                                        <td class="px-4 py-2.5 text-sm text-gray-900 text-right" x-text="formatRp(cartItem.subtotal)"></td>
                                        <td class="px-4 py-2.5 text-sm text-center">
                                            <button type="button" x-on:click="hapusItem(idx)" class="text-red-600 hover:text-red-900 font-semibold cursor-pointer">Hapus</button>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                        @error('items') <span class="text-red-500 text-xs block mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" x-bind:disabled="items.length === 0" class="bg-primary text-white px-6 py-2.5 rounded-xl hover:bg-primary-deep transition font-semibold shadow disabled:opacity-50 cursor-pointer">Simpan Bon & Piutang</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.app>
