<x-layouts.app title="Barang Keluar" role="kasir">
    <div
        class="space-y-6"
        x-data="{
            masterItems: @js($masterItems->map(fn($b) => [
                'id' => $b->id,
                'nama_barang' => $b->nama_barang,
                'kode_barang' => (string) $b->kode_barang,
                'satuan' => (string) $b->satuan,
                'harga_jual' => (int) $b->harga_jual,
                'stok' => (int) $b->stok,
            ])->values()),
            selectedBarangId: '',
            jumlah: 1,
            keranjang: [],
            namaPelanggan: 'Umum',
            metodeBayar: 'cash',
            formatRp(val) {
                return 'Rp ' + Math.round(Number(val) || 0).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            },
            get totalBayar() {
                return this.keranjang.reduce((sum, item) => sum + item.subtotal, 0);
            },
            tambahItem() {
                const id = parseInt(this.selectedBarangId);
                const qty = parseInt(this.jumlah) || 1;
                if (!id || qty < 1) return;
                const barang = this.masterItems.find(b => b.id === id);
                if (!barang) return;

                const idx = this.keranjang.findIndex(k => k.barang_id === id);
                const currentQty = idx >= 0 ? this.keranjang[idx].jumlah : 0;
                const totalQty = currentQty + qty;

                if (barang.stok < totalQty) {
                    window.dispatchEvent(new CustomEvent('toast', {
                        detail: { message: `Stok ${barang.nama_barang} tidak cukup (tersisa ${barang.stok})`, type: 'error' }
                    }));
                    return;
                }

                if (idx >= 0) {
                    this.keranjang[idx].jumlah = totalQty;
                    this.keranjang[idx].subtotal = totalQty * barang.harga_jual;
                } else {
                    this.keranjang.push({
                        barang_id: barang.id,
                        nama_barang: barang.nama_barang,
                        satuan: barang.satuan,
                        harga_satuan: barang.harga_jual,
                        jumlah: qty,
                        subtotal: qty * barang.harga_jual,
                        stok_tersedia: barang.stok,
                    });
                }

                this.selectedBarangId = '';
                this.jumlah = 1;
            },
            ubahJumlah(barangId, delta) {
                const idx = this.keranjang.findIndex(k => k.barang_id === barangId);
                if (idx < 0) return;
                const item = this.keranjang[idx];
                const newQty = item.jumlah + delta;
                if (newQty <= 0) {
                    this.keranjang.splice(idx, 1);
                    return;
                }
                if (newQty > item.stok_tersedia) {
                    window.dispatchEvent(new CustomEvent('toast', {
                        detail: { message: `Stok ${item.nama_barang} tidak cukup (tersisa ${item.stok_tersedia})`, type: 'error' }
                    }));
                    return;
                }
                item.jumlah = newQty;
                item.subtotal = newQty * item.harga_satuan;
            },
            hapusItem(barangId) {
                this.keranjang = this.keranjang.filter(k => k.barang_id !== barangId);
            }
        }"
    >
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-[#0F172A] tracking-tight">Barang Keluar</h1>
            <p class="mt-1 text-xs sm:text-sm text-slate-500">
                Catat penjualan: harga dan total terisi otomatis, stok berkurang setelah disimpan.
            </p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            <form method="POST" action="{{ route('kasir.barang-keluar.store') }}" class="lg:col-span-7 space-y-5">
                @csrf

                <template x-for="(item, idx) in keranjang" :key="item.barang_id">
                    <div>
                        <input type="hidden" x-bind:name="`items[${idx}][barang_id]`" x-bind:value="item.barang_id" />
                        <input type="hidden" x-bind:name="`items[${idx}][jumlah]`" x-bind:value="item.jumlah" />
                    </div>
                </template>
                <input type="hidden" name="metode_bayar" x-bind:value="metodeBayar" />

                {{-- 1. PILIH BARANG --}}
                <div class="bg-white rounded-2xl p-5 sm:p-6 shadow-sm border border-slate-100">
                    <h2 class="text-sm font-bold text-slate-800 mb-3.5">1. Pilih barang</h2>

                    <div class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
                        <div class="sm:col-span-7">
                            <label class="block text-xs font-semibold text-slate-600 mb-1.5">Barang</label>
                            @php
                                $barangOptions = $masterItems->map(fn ($barang) => [
                                    'value' => (string) $barang->id,
                                    'label' => $barang->nama_barang . ' (' . $barang->satuan . ') - ' . rupiah($barang->harga_jual) . ' [Stok: ' . $barang->stok . ']',
                                    'disabled' => $barang->stok <= 0,
                                ])->values()->all();
                            @endphp
                            <x-ui.select
                                x-model="selectedBarangId"
                                placeholder="— Pilih dari master item —"
                                :options="$barangOptions"
                                :searchable="true"
                            />
                        </div>

                        <div class="sm:col-span-2">
                            <label class="block text-xs font-semibold text-slate-600 mb-1.5">Jumlah</label>
                            <input
                                type="number"
                                min="1"
                                x-model="jumlah"
                                x-on:keydown.enter.prevent="tambahItem()"
                                class="w-full text-xs sm:text-sm rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-center text-slate-700 font-semibold tabular-nums focus:border-[#1D39A8] focus:ring-1 focus:ring-[#1D39A8] transition shadow-xs"
                            />
                        </div>

                        <div class="sm:col-span-3">
                            <button
                                type="button"
                                x-on:click="tambahItem()"
                                class="w-full inline-flex items-center justify-center gap-1.5 px-4 py-2.5 rounded-xl bg-[#1D39A8] hover:bg-[#162d4d] text-white text-xs sm:text-sm font-semibold transition active:scale-95 shadow-xs cursor-pointer select-none"
                            >
                                <span>+ Tambah</span>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- 2. KERANJANG --}}
                <div class="bg-white rounded-2xl p-5 sm:p-6 shadow-sm border border-slate-100">
                    <h2 class="text-sm font-bold text-slate-800 mb-3.5">2. Keranjang</h2>

                    <template x-if="keranjang.length === 0">
                        <div class="rounded-xl border border-dashed border-slate-200 bg-slate-50/60 py-9 px-4 text-center">
                            <p class="text-xs sm:text-sm text-slate-400 font-normal">
                                Belum ada barang. Pilih barang lalu klik Tambah.
                            </p>
                        </div>
                    </template>

                    <template x-if="keranjang.length > 0">
                        <div class="overflow-x-auto rounded-xl border border-slate-100">
                            <table class="w-full text-left text-xs sm:text-sm">
                                <thead class="bg-slate-50 text-[11px] font-bold uppercase tracking-wider text-slate-500 border-b border-slate-100">
                                    <tr>
                                        <th class="px-3.5 py-2.5">Barang</th>
                                        <th class="px-3.5 py-2.5 text-right">Harga</th>
                                        <th class="px-3.5 py-2.5 text-center">Jumlah</th>
                                        <th class="px-3.5 py-2.5 text-right">Subtotal</th>
                                        <th class="px-2 py-2.5 text-center w-8"></th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 bg-white">
                                    <template x-for="item in keranjang" :key="item.barang_id">
                                        <tr class="hover:bg-slate-50/50 transition">
                                            <td class="px-3.5 py-2.5 font-medium text-slate-800">
                                                <div class="font-semibold text-slate-800 leading-tight" x-text="item.nama_barang"></div>
                                                <span class="text-[11px] text-slate-400 font-normal" x-text="`${item.satuan} · Stok ${item.stok_tersedia}`"></span>
                                            </td>
                                            <td class="px-3.5 py-2.5 text-right text-slate-600 tabular-nums" x-text="formatRp(item.harga_satuan)"></td>
                                            <td class="px-3.5 py-2.5 text-center">
                                                <div class="inline-flex items-center gap-1 bg-slate-100 rounded-lg p-0.5">
                                                    <button type="button" x-on:click="ubahJumlah(item.barang_id, -1)" class="w-6 h-6 flex items-center justify-center rounded bg-white text-slate-600 hover:bg-slate-200 transition text-xs font-bold shadow-xs cursor-pointer">−</button>
                                                    <span class="w-8 text-center font-bold text-slate-800 tabular-nums text-xs" x-text="item.jumlah"></span>
                                                    <button type="button" x-on:click="ubahJumlah(item.barang_id, 1)" class="w-6 h-6 flex items-center justify-center rounded bg-white text-slate-600 hover:bg-slate-200 transition text-xs font-bold shadow-xs cursor-pointer">+</button>
                                                </div>
                                            </td>
                                            <td class="px-3.5 py-2.5 text-right font-bold text-slate-900 tabular-nums" x-text="formatRp(item.subtotal)"></td>
                                            <td class="px-2 py-2.5 text-center">
                                                <button type="button" x-on:click="hapusItem(item.barang_id)" class="p-1 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition cursor-pointer">✕</button>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </template>
                </div>

                {{-- 3. PEMBAYARAN --}}
                <div class="bg-white rounded-2xl p-5 sm:p-6 shadow-sm border border-slate-100">
                    <h2 class="text-sm font-bold text-slate-800 mb-3.5">3. Pembayaran</h2>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1.5">Nama pelanggan (opsional)</label>
                            <input
                                type="text"
                                name="nama_pelanggan"
                                x-model="namaPelanggan"
                                placeholder="Umum"
                                class="w-full text-xs sm:text-sm rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-slate-700 placeholder-slate-400 focus:border-[#1D39A8] focus:ring-1 focus:ring-[#1D39A8] transition shadow-xs"
                            />
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1.5">Metode pembayaran</label>
                            <div class="grid grid-cols-2 gap-3">
                                <button
                                    type="button"
                                    x-on:click="metodeBayar = 'cash'"
                                    x-bind:class="metodeBayar === 'cash' ? 'bg-[#162d4d] text-white shadow-sm ring-2 ring-[#162d4d]/20' : 'border border-slate-200 text-slate-600 bg-white hover:bg-slate-50'"
                                    class="flex items-center justify-center gap-2 py-3 px-4 rounded-xl font-bold text-xs sm:text-sm transition cursor-pointer select-none"
                                >
                                    <span>CASH</span>
                                </button>

                                <button
                                    type="button"
                                    x-on:click="metodeBayar = 'transfer'"
                                    x-bind:class="metodeBayar === 'transfer' ? 'bg-[#162d4d] text-white shadow-sm ring-2 ring-[#162d4d]/20' : 'border border-slate-200 text-slate-600 bg-white hover:bg-slate-50'"
                                    class="flex items-center justify-center gap-2 py-3 px-4 rounded-xl font-bold text-xs sm:text-sm transition cursor-pointer select-none"
                                >
                                    <span>TRANSFER</span>
                                </button>
                            </div>
                        </div>

                        <div class="bg-[#162d4d] rounded-2xl p-4 sm:p-5 flex items-center justify-between text-white shadow-md">
                            <span class="text-xs sm:text-sm font-medium text-slate-300">Total bayar</span>
                            <span class="text-xl sm:text-2xl font-black tracking-tight tabular-nums text-white" x-text="formatRp(totalBayar)"></span>
                        </div>

                        <button
                            type="submit"
                            x-bind:disabled="keranjang.length === 0"
                            class="w-full inline-flex items-center justify-center gap-2 py-3.5 px-4 rounded-xl bg-[#1D39A8] hover:bg-[#162d4d] disabled:opacity-50 disabled:cursor-not-allowed text-white font-bold text-sm sm:text-base transition active:scale-[0.99] shadow-sm cursor-pointer select-none"
                        >
                            <span>Simpan Transaksi</span>
                        </button>
                    </div>
                </div>
            </form>

            {{-- SISI KANAN: Riwayat Barang Keluar --}}
            <div class="lg:col-span-5 bg-white rounded-2xl p-5 sm:p-6 shadow-sm border border-slate-100 flex flex-col">
                <div class="mb-4 pb-3 border-b border-slate-100">
                    <h2 class="text-base sm:text-lg font-bold text-slate-800">Riwayat Barang Keluar</h2>
                    <p class="text-xs text-slate-500 mt-0.5">
                        @if(($totalRiwayat ?? 0) > 0)
                            {{ $totalRiwayat }} transaksi
                        @else
                            Belum ada transaksi
                        @endif
                    </p>
                </div>

                @if(($riwayatPenjualan ?? collect())->isEmpty())
                    <div class="py-16 text-center text-slate-400">
                        <p class="text-sm font-semibold text-slate-600">Belum ada transaksi</p>
                        <p class="text-xs text-slate-400 mt-1">Transaksi penjualan langsung akan muncul di sini</p>
                    </div>
                @else
                    <div class="divide-y divide-slate-100 space-y-3.5">
                        @foreach($riwayatPenjualan as $trx)
                            @php
                                $metode = ($trx->metode_bayar instanceof \BackedEnum)
                                    ? $trx->metode_bayar->value
                                    : (string) $trx->metode_bayar;
                                $dt = $trx->waktu_transaksi ? \Carbon\Carbon::parse($trx->waktu_transaksi)->timezone('Asia/Jakarta') : null;
                                $waktuStr = $dt ? tanggal_id($dt, false, false) . ', ' . $dt->format('H.i') : '-';
                                $summaryItems = $trx->detailPenjualan->map(fn($d) => $d->nama_barang . ' x' . $d->jumlah)->implode(', ');
                            @endphp
                            <div class="pt-3.5 first:pt-0">
                                <div class="flex items-center justify-between gap-2">
                                    <span class="text-xs sm:text-sm font-bold text-slate-900 tracking-tight">{{ $trx->kode_transaksi }}</span>
                                    @if(strtolower($metode) === 'transfer')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-[#EBF2FF] text-[#1D39A8]">TRANSFER</span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-[#162d4d] text-white">CASH</span>
                                    @endif
                                </div>
                                <div class="mt-1 text-xs sm:text-sm font-bold text-slate-800">{{ $trx->nama_pelanggan ?: 'Umum' }}</div>
                                <div class="mt-0.5 text-[11px] sm:text-xs text-slate-500 line-clamp-2">{{ $summaryItems ?: '-' }}</div>
                                <div class="mt-2 flex items-center justify-between gap-2 text-xs">
                                    <span class="text-[11px] text-slate-400">{{ $waktuStr }}</span>
                                    <span class="font-extrabold text-slate-900 tabular-nums">{{ rupiah($trx->total) }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-layouts.app>
