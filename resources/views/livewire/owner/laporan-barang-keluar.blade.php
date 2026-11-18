<div class="space-y-6">
    {{-- 1. HEADER HALAMAN --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">
                Laporan Barang Keluar
            </h1>
            <p class="mt-1 text-sm text-slate-500">
                Filter berdasarkan tanggal dan metode pembayaran.
            </p>
        </div>

        <div class="shrink-0">
            <button
                type="button"
                wire:click="unduhCsv"
                wire:loading.attr="disabled"
                class="bg-white hover:bg-slate-50 border border-slate-200/90 text-slate-700 font-semibold text-xs sm:text-sm rounded-xl px-4 py-2.5 flex items-center gap-2 shadow-2xs transition cursor-pointer select-none disabled:opacity-60"
            >
                <svg class="w-4 h-4 text-slate-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
                <span wire:loading.remove wire:target="unduhCsv">Unduh CSV</span>
                <span wire:loading wire:target="unduhCsv">Mengunduh...</span>
            </button>
        </div>
    </div>

    {{-- 2. BARIS FILTER --}}
    <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-2xs">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label for="filter-dari-tanggal" class="block text-xs font-semibold text-slate-600 mb-1.5">
                    Dari tanggal
                </label>
                <input
                    id="filter-dari-tanggal"
                    type="date"
                    wire:model.live="dariTanggal"
                    class="w-full text-xs sm:text-sm rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-slate-700 tabular-nums focus:border-[#1D3FA3] focus:ring-1 focus:ring-[#1D3FA3] transition shadow-2xs"
                />
            </div>

            <div>
                <label for="filter-sampai-tanggal" class="block text-xs font-semibold text-slate-600 mb-1.5">
                    Sampai tanggal
                </label>
                <input
                    id="filter-sampai-tanggal"
                    type="date"
                    wire:model.live="sampaiTanggal"
                    class="w-full text-xs sm:text-sm rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-slate-700 tabular-nums focus:border-[#1D3FA3] focus:ring-1 focus:ring-[#1D3FA3] transition shadow-2xs"
                />
            </div>

            <div>
                <label for="filter-metode-bayar" class="block text-xs font-semibold text-slate-600 mb-1.5">
                    Metode pembayaran
                </label>
                <select
                    id="filter-metode-bayar"
                    wire:model.live="metode"
                    class="w-full text-xs sm:text-sm rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-slate-700 focus:border-[#1D3FA3] focus:ring-1 focus:ring-[#1D3FA3] transition shadow-2xs"
                >
                    <option value="semua">Semua</option>
                    <option value="cash">Cash</option>
                    <option value="transfer">Transfer</option>
                </select>
            </div>
        </div>
    </div>

    {{-- 3. BARIS 3 KARTU RINGKASAN FILTER --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        {{-- Kartu 1: Total penjualan --}}
        <div class="bg-sky-50/90 border border-sky-200/80 rounded-2xl p-4 flex items-center gap-3.5 shadow-2xs">
            <div class="p-2.5 rounded-xl bg-sky-100 text-[#1D3FA3] shrink-0">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                </svg>
            </div>
            <div class="min-w-0">
                <p class="text-xs font-medium text-slate-500">Total penjualan</p>
                <p class="text-lg sm:text-xl font-extrabold text-slate-900 tabular-nums leading-tight mt-0.5 whitespace-nowrap">
                    {{ rupiah($ringkasan['total_penjualan']) }}
                </p>
                <p class="text-[11px] font-medium text-slate-500 tabular-nums mt-0.5">
                    {{ $ringkasan['jumlah_transaksi'] }} transaksi
                </p>
            </div>
        </div>

        {{-- Kartu 2: Cash --}}
        <div class="bg-white border border-slate-200/80 rounded-2xl p-4 flex items-center gap-3.5 shadow-2xs">
            <div class="p-2.5 rounded-xl bg-blue-50 text-[#1D3FA3] shrink-0">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
            </div>
            <div class="min-w-0">
                <p class="text-xs font-medium text-slate-500">Cash</p>
                <p class="text-lg sm:text-xl font-extrabold text-slate-900 tabular-nums leading-tight mt-0.5 whitespace-nowrap">
                    {{ rupiah($ringkasan['total_cash']) }}
                </p>
            </div>
        </div>

        {{-- Kartu 3: Transfer --}}
        <div class="bg-white border border-slate-200/80 rounded-2xl p-4 flex items-center gap-3.5 shadow-2xs">
            <div class="p-2.5 rounded-xl bg-blue-50 text-[#1D3FA3] shrink-0">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                </svg>
            </div>
            <div class="min-w-0">
                <p class="text-xs font-medium text-slate-500">Transfer</p>
                <p class="text-lg sm:text-xl font-extrabold text-slate-900 tabular-nums leading-tight mt-0.5 whitespace-nowrap">
                    {{ rupiah($ringkasan['total_transfer']) }}
                </p>
            </div>
        </div>
    </div>

    {{-- 4. TABEL LAPORAN TRANSAKSI & INTERAKSI TOMBOL RINCIAN --}}
    <div class="bg-white rounded-2xl border border-slate-200/80 overflow-hidden shadow-2xs">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs sm:text-sm border-collapse">
                <thead class="bg-slate-50/90 border-b border-slate-200/80 text-[11px] font-bold uppercase tracking-wider text-slate-500">
                    <tr>
                        <th class="px-5 py-3.5 whitespace-nowrap">ID</th>
                        <th class="px-5 py-3.5 whitespace-nowrap">WAKTU</th>
                        <th class="px-5 py-3.5 whitespace-nowrap">PELANGGAN</th>
                        <th class="px-5 py-3.5 whitespace-nowrap">METODE</th>
                        <th class="px-5 py-3.5 text-right whitespace-nowrap">TOTAL</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($transaksi as $row)
                        @php
                            $penjualanId = (int) $row->penjualan_id;
                            $isExpanded = ! empty($expandedRows[$penjualanId]);
                            $metodeVal = strtolower((string) ($row->metode_bayar ?? 'cash'));
                            $dt = $row->waktu_transaksi
                                ? \Carbon\Carbon::parse($row->waktu_transaksi)->timezone('Asia/Jakarta')
                                : null;
                            $waktuStr = $dt ? tanggal_id($dt).', '.$dt->format('H.i') : '-';
                            $items = $detailsByPenjualanId->get($penjualanId, collect());
                        @endphp

                        <tr wire:key="trx-row-{{ $penjualanId }}" class="hover:bg-slate-50/60 transition">
                            <td class="px-5 py-4 font-bold text-[#1D3FA3] whitespace-nowrap">
                                {{ $row->kode_transaksi }}
                            </td>
                            <td class="px-5 py-4 text-slate-600 tabular-nums whitespace-nowrap">
                                {{ $waktuStr }}
                            </td>
                            <td class="px-5 py-4 font-semibold text-slate-800 whitespace-nowrap">
                                {{ $row->nama_pelanggan ?: 'Umum' }}
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap">
                                @if($metodeVal === 'transfer')
                                    <span class="inline-flex items-center bg-blue-100 text-blue-800 rounded-full px-3 py-1 text-[11px] font-bold">
                                        TRANSFER
                                    </span>
                                @else
                                    <span class="inline-flex items-center bg-[#1E3F99] text-white rounded-full px-3 py-1 text-[11px] font-bold">
                                        CASH
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-right whitespace-nowrap">
                                <div class="inline-flex items-center justify-end gap-3">
                                    <span class="font-bold text-slate-900 tabular-nums">
                                        {{ rupiah($row->total) }}
                                    </span>
                                    <button
                                        type="button"
                                        wire:click="toggleRincian({{ $penjualanId }})"
                                        class="bg-white hover:bg-slate-50 border border-slate-200 text-slate-700 text-xs font-semibold rounded-lg px-3 py-1.5 transition cursor-pointer select-none"
                                    >
                                        {{ $isExpanded ? 'Tutup' : 'Rincian' }}
                                    </button>
                                </div>
                            </td>
                        </tr>

                        @if($isExpanded)
                            <tr wire:key="trx-detail-{{ $penjualanId }}">
                                <td colspan="5" class="bg-slate-50/90 border-t border-b border-slate-200/70 px-6 py-4">
                                    <div class="space-y-2.5">
                                        @forelse($items as $item)
                                            <div class="flex items-center justify-between gap-4 text-xs sm:text-sm">
                                                <span class="text-slate-700">
                                                    <span class="font-semibold text-slate-800">{{ $item->nama_barang }}</span>
                                                    <span class="text-slate-500">
                                                        · {{ $item->jumlah }} {{ $item->satuan }} × {{ rupiah($item->harga_satuan) }}
                                                    </span>
                                                </span>
                                                <span class="font-bold text-slate-900 tabular-nums whitespace-nowrap">
                                                    {{ rupiah($item->subtotal) }}
                                                </span>
                                            </div>
                                        @empty
                                            <p class="text-xs text-slate-500">Tidak ada rincian barang.</p>
                                        @endforelse

                                        <div class="pt-2 border-t border-slate-200/70 text-[11px] sm:text-xs text-slate-500">
                                            Kasir: {{ $row->kasir ?? '-' }}
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-14 text-center">
                                <div class="w-12 h-12 rounded-2xl bg-slate-50 text-slate-400 flex items-center justify-center mx-auto mb-3">
                                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                </div>
                                <p class="text-sm font-semibold text-slate-700">Belum ada transaksi barang keluar</p>
                                <p class="text-xs text-slate-400 mt-1">
                                    Tidak ditemukan transaksi penjualan pada rentang tanggal dan metode pembayaran yang dipilih.
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($transaksi->hasPages())
            <div class="px-5 py-4 border-t border-slate-100 bg-white">
                {{ $transaksi->links() }}
            </div>
        @endif
    </div>
</div>
