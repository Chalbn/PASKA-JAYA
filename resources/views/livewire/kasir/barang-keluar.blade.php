<div class="space-y-6">
    {{-- Header Halaman Sesuai Gambar Referensi --}}
    <div>
        <h1 class="text-2xl sm:text-3xl font-extrabold text-[#0F172A] tracking-tight">
            Barang Keluar
        </h1>
        <p class="mt-1 text-xs sm:text-sm text-slate-500">
            Catat penjualan: harga dan total terisi otomatis, stok berkurang setelah disimpan.
        </p>
    </div>

    {{-- Grid Layout: Form Barang Keluar (Kiri) & Riwayat Barang Keluar (Kanan) --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        {{-- SISI KIRI (7 Kolom): 1. Pilih Barang, 2. Keranjang, 3. Pembayaran --}}
        <div class="lg:col-span-7 space-y-5">
            {{-- 1. PILIH BARANG --}}
            <div class="bg-white rounded-2xl p-5 sm:p-6 shadow-sm border border-slate-100">
                <h2 class="text-sm font-bold text-slate-800 mb-3.5">
                    1. Pilih barang
                </h2>

                <div class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
                    <div class="sm:col-span-7">
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5">
                            Barang
                        </label>
                        <select 
                            wire:model="selectedBarangId"
                            class="w-full text-xs sm:text-sm rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-slate-700 focus:border-[#1D39A8] focus:ring-1 focus:ring-[#1D39A8] transition shadow-xs"
                        >
                            <option value="">— Pilih dari master item —</option>
                            @foreach($this->masterItems as $barang)
                                <option value="{{ $barang->id }}" @disabled($barang->stok <= 0)>
                                    {{ $barang->nama_barang }} ({{ $barang->satuan }}) - {{ rupiah($barang->harga_jual) }} [Stok: {{ $barang->stok }}]
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5">
                            Jumlah
                        </label>
                        <input 
                            type="number" 
                            min="1" 
                            wire:model="jumlah"
                            wire:keydown.enter.prevent="tambahItem"
                            class="w-full text-xs sm:text-sm rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-center text-slate-700 font-semibold tabular-nums focus:border-[#1D39A8] focus:ring-1 focus:ring-[#1D39A8] transition shadow-xs"
                        >
                    </div>

                    <div class="sm:col-span-3">
                        <button 
                            type="button" 
                            wire:click="tambahItem"
                            class="w-full inline-flex items-center justify-center gap-1.5 px-4 py-2.5 rounded-xl bg-[#1D39A8] hover:bg-[#162d4d] text-white text-xs sm:text-sm font-semibold transition active:scale-95 shadow-xs cursor-pointer select-none"
                        >
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                            </svg>
                            <span>Tambah</span>
                        </button>
                    </div>
                </div>

                @error('selectedBarangId')
                    <p class="mt-1.5 text-xs text-rose-500 font-medium">{{ $message }}</p>
                @enderror
                @error('jumlah')
                    <p class="mt-1.5 text-xs text-rose-500 font-medium">{{ $message }}</p>
                @enderror
            </div>

            {{-- 2. KERANJANG --}}
            <div class="bg-white rounded-2xl p-5 sm:p-6 shadow-sm border border-slate-100">
                <h2 class="text-sm font-bold text-slate-800 mb-3.5">
                    2. Keranjang
                </h2>

                @if(empty($keranjang))
                    <div class="rounded-xl border border-dashed border-slate-200 bg-slate-50/60 py-9 px-4 text-center">
                        <p class="text-xs sm:text-sm text-slate-400 font-normal">
                            Belum ada barang. Pilih barang lalu klik Tambah.
                        </p>
                    </div>
                @else
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
                                @foreach($keranjang as $item)
                                    <tr class="hover:bg-slate-50/50 transition">
                                        <td class="px-3.5 py-2.5 font-medium text-slate-800">
                                            <div class="font-semibold text-slate-800 leading-tight">
                                                {{ $item['nama_barang'] }}
                                            </div>
                                            <span class="text-[11px] text-slate-400 font-normal">
                                                {{ $item['satuan'] }} · Stok {{ $item['stok_tersedia'] }}
                                            </span>
                                        </td>
                                        <td class="px-3.5 py-2.5 text-right text-slate-600 tabular-nums">
                                            {{ rupiah($item['harga_satuan']) }}
                                        </td>
                                        <td class="px-3.5 py-2.5 text-center">
                                            <div class="inline-flex items-center gap-1 bg-slate-100 rounded-lg p-0.5">
                                                <button 
                                                    type="button" 
                                                    wire:click="ubahJumlah({{ $item['barang_id'] }}, -1)"
                                                    class="w-6 h-6 flex items-center justify-center rounded bg-white text-slate-600 hover:bg-slate-200 transition text-xs font-bold shadow-xs cursor-pointer select-none"
                                                    title="Kurangi"
                                                >−</button>
                                                <span class="w-8 text-center font-bold text-slate-800 tabular-nums text-xs">
                                                    {{ $item['jumlah'] }}
                                                </span>
                                                <button 
                                                    type="button" 
                                                    wire:click="ubahJumlah({{ $item['barang_id'] }}, 1)"
                                                    class="w-6 h-6 flex items-center justify-center rounded bg-white text-slate-600 hover:bg-slate-200 transition text-xs font-bold shadow-xs cursor-pointer select-none"
                                                    title="Tambah"
                                                >+</button>
                                            </div>
                                        </td>
                                        <td class="px-3.5 py-2.5 text-right font-bold text-slate-900 tabular-nums">
                                            {{ rupiah($item['subtotal']) }}
                                        </td>
                                        <td class="px-2 py-2.5 text-center">
                                            <button 
                                                type="button" 
                                                wire:click="hapusItem({{ $item['barang_id'] }})"
                                                class="p-1 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition cursor-pointer"
                                                title="Hapus dari keranjang"
                                            >
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            {{-- 3. PEMBAYARAN --}}
            <div class="bg-white rounded-2xl p-5 sm:p-6 shadow-sm border border-slate-100">
                <h2 class="text-sm font-bold text-slate-800 mb-3.5">
                    3. Pembayaran
                </h2>

                <div class="space-y-4">
                    {{-- Nama Pelanggan --}}
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5">
                            Nama pelanggan (opsional)
                        </label>
                        <input 
                            type="text" 
                            wire:model="namaPelanggan"
                            placeholder="Umum" 
                            class="w-full text-xs sm:text-sm rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-slate-700 placeholder-slate-400 focus:border-[#1D39A8] focus:ring-1 focus:ring-[#1D39A8] transition shadow-xs"
                        >
                    </div>

                    {{-- Pilihan Metode Pembayaran --}}
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5">
                            Metode pembayaran
                        </label>
                        <div class="grid grid-cols-2 gap-3">
                            <button 
                                type="button" 
                                wire:click="setMetodeBayar('cash')"
                                class="flex items-center justify-center gap-2 py-3 px-4 rounded-xl font-bold text-xs sm:text-sm transition cursor-pointer select-none {{ $metodeBayar === 'cash' ? 'bg-[#162d4d] text-white shadow-sm ring-2 ring-[#162d4d]/20' : 'border border-slate-200 text-slate-600 bg-white hover:bg-slate-50' }}"
                            >
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                <span>CASH</span>
                            </button>

                            <button 
                                type="button" 
                                wire:click="setMetodeBayar('transfer')"
                                class="flex items-center justify-center gap-2 py-3 px-4 rounded-xl font-bold text-xs sm:text-sm transition cursor-pointer select-none {{ $metodeBayar === 'transfer' ? 'bg-[#162d4d] text-white shadow-sm ring-2 ring-[#162d4d]/20' : 'border border-slate-200 text-slate-600 bg-white hover:bg-slate-50' }}"
                            >
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                </svg>
                                <span>TRANSFER</span>
                            </button>
                        </div>
                    </div>

                    {{-- Kotak Total Bayar (Navy / Deep Blue) --}}
                    <div class="bg-[#162d4d] rounded-2xl p-4 sm:p-5 flex items-center justify-between text-white shadow-md">
                        <span class="text-xs sm:text-sm font-medium text-slate-300">
                            Total bayar
                        </span>
                        <span class="text-xl sm:text-2xl font-black tracking-tight tabular-nums text-white">
                            {{ rupiah($this->totalBayar) }}
                        </span>
                    </div>

                    {{-- Tombol Simpan Transaksi --}}
                    <button 
                        type="button" 
                        wire:click="simpanTransaksi"
                        wire:loading.attr="disabled"
                        @disabled(empty($keranjang))
                        class="w-full inline-flex items-center justify-center gap-2 py-3.5 px-4 rounded-xl bg-[#1D39A8] hover:bg-[#162d4d] disabled:opacity-50 disabled:cursor-not-allowed text-white font-bold text-sm sm:text-base transition active:scale-[0.99] shadow-sm cursor-pointer select-none"
                    >
                        <span wire:loading.remove wire:target="simpanTransaksi">
                            Simpan Transaksi
                        </span>
                        <span wire:loading wire:target="simpanTransaksi" class="inline-flex items-center gap-2">
                            <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span>Menyimpan...</span>
                        </span>
                    </button>
                </div>
            </div>
        </div>

        {{-- SISI KANAN (5 Kolom): Riwayat Barang Keluar --}}
        <div class="lg:col-span-5 bg-white rounded-2xl p-5 sm:p-6 shadow-sm border border-slate-100 flex flex-col">
            <div class="mb-4 pb-3 border-b border-slate-100">
                <h2 class="text-base sm:text-lg font-bold text-slate-800">
                    Riwayat Barang Keluar
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">
                    @if($this->totalRiwayat > 0)
                        {{ $this->totalRiwayat }} transaksi
                    @else
                        Belum ada transaksi
                    @endif
                </p>
            </div>

            @if($this->riwayatPenjualan->isEmpty())
                <div class="py-16 text-center text-slate-400">
                    <svg class="w-12 h-12 mx-auto mb-3 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    <p class="text-sm font-semibold text-slate-600">Belum ada transaksi</p>
                    <p class="text-xs text-slate-400 mt-1">Transaksi penjualan langsung akan muncul di sini</p>
                </div>
            @else
                <div class="divide-y divide-slate-100 space-y-3.5">
                    @foreach($this->riwayatPenjualan as $trx)
                        @php
                            $metode = ($trx->metode_bayar instanceof \BackedEnum) 
                                ? $trx->metode_bayar->value 
                                : (string) $trx->metode_bayar;
                            $dt = $trx->waktu_transaksi ? \Carbon\Carbon::parse($trx->waktu_transaksi)->timezone('Asia/Jakarta') : null;
                            $waktuStr = $dt ? tanggal_id($dt, false, false) . ', ' . $dt->format('H.i') : '-';
                            $summaryItems = $trx->detailPenjualan->map(fn($d) => $d->nama_barang . ' x' . $d->jumlah)->implode(', ');
                        @endphp
                        <div class="pt-3.5 first:pt-0">
                            {{-- Baris 1: Kode TRX & Badge Metode --}}
                            <div class="flex items-center justify-between gap-2">
                                <span class="text-xs sm:text-sm font-bold text-slate-900 tracking-tight">
                                    {{ $trx->kode_transaksi }}
                                </span>
                                @if(strtolower($metode) === 'transfer')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-[#EBF2FF] text-[#1D39A8]">
                                        TRANSFER
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-[#162d4d] text-white">
                                        CASH
                                    </span>
                                @endif
                            </div>

                            {{-- Baris 2: Nama Pelanggan --}}
                            <div class="mt-1 text-xs sm:text-sm font-bold text-slate-800">
                                {{ $trx->nama_pelanggan ?: 'Umum' }}
                            </div>

                            {{-- Baris 3: Ringkasan Item --}}
                            <div class="mt-0.5 text-[11px] sm:text-xs text-slate-500 line-clamp-2">
                                {{ $summaryItems ?: '-' }}
                            </div>

                            {{-- Baris 4: Waktu & Total --}}
                            <div class="mt-2 flex items-center justify-between gap-2 text-xs">
                                <span class="text-[11px] text-slate-400">
                                    {{ $waktuStr }}
                                </span>
                                <span class="font-extrabold text-slate-900 tabular-nums">
                                    {{ rupiah($trx->total) }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
