<div class="space-y-6">
    {{-- Header Halaman --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-text-slate tracking-tight">Riwayat Saldo Laci</h1>
            <p class="text-xs sm:text-sm text-text-muted mt-0.5">
                Catatan reset otomatis pukul 00:00 dan reset manual.
            </p>
        </div>

        {{-- Tombol Aksi Ekspor Excel & PDF --}}
        <div class="flex flex-wrap items-center gap-2">
            <button
                type="button"
                wire:click="exportExcel"
                class="inline-flex items-center justify-center font-medium rounded-xl transition duration-150 ease-in-out border border-surface-border bg-surface-card hover:bg-surface-container-low text-text-slate text-xs px-3.5 py-2 shadow-xs gap-1.5"
                title="Ekspor ke spreadsheet Excel"
            >
                <svg class="w-4 h-4 text-status-success" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <span>Excel</span>
            </button>

            <button
                type="button"
                wire:click="exportPdf"
                class="inline-flex items-center justify-center font-medium rounded-xl transition duration-150 ease-in-out border border-surface-border bg-surface-card hover:bg-surface-container-low text-text-slate text-xs px-3.5 py-2 shadow-xs gap-1.5"
                title="Cetak format dokumen PDF"
            >
                <svg class="w-4 h-4 text-status-danger" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                </svg>
                <span>PDF</span>
            </button>

            <a 
                href="{{ route('kasir.saldo') }}"
                class="inline-flex items-center justify-center font-medium rounded-xl transition duration-150 ease-in-out bg-primary hover:bg-primary-container text-primary-on text-xs px-3.5 py-2 shadow-xs gap-1.5"
            >
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                </svg>
                <span>Lihat Saldo Aktif</span>
            </a>
        </div>
    </div>

    {{-- Filter Rentang Tanggal --}}
    <div class="bg-surface-card border border-surface-border rounded-2xl p-4 shadow-xs">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="flex flex-wrap items-center gap-3">
                <div class="flex items-center gap-2">
                    <label class="text-xs font-semibold text-text-muted">Dari:</label>
                    <input 
                        type="date" 
                        wire:model.live="tanggalAwal"
                        class="text-xs rounded-xl border border-surface-border bg-surface-canvas text-text-slate px-3 py-1.5 focus:ring-2 focus:ring-primary focus:outline-none"
                    />
                </div>

                <div class="flex items-center gap-2">
                    <label class="text-xs font-semibold text-text-muted">Sampai:</label>
                    <input 
                        type="date" 
                        wire:model.live="tanggalAkhir"
                        class="text-xs rounded-xl border border-surface-border bg-surface-canvas text-text-slate px-3 py-1.5 focus:ring-2 focus:ring-primary focus:outline-none"
                    />
                </div>

                @if($tanggalAwal || $tanggalAkhir)
                    <button
                        type="button"
                        wire:click="resetFilter"
                        class="text-xs text-text-muted hover:text-status-danger underline transition font-medium"
                    >
                        Reset Filter
                    </button>
                @endif
            </div>

            <div class="text-xs text-text-muted">
                Menampilkan <strong class="text-text-slate font-bold">{{ $riwayat->total() }}</strong> catatan penutupan
            </div>
        </div>
    </div>

    {{-- Tabel Riwayat Penutupan Laci --}}
    @if($riwayat->isEmpty())
        <x-ui.empty-state
            title="Belum ada riwayat"
            description="Tidak ada catatan penutupan laci yang ditemukan sesuai filter tanggal yang dipilih."
        />
    @else
        <div class="bg-surface-card border border-surface-border rounded-2xl overflow-hidden shadow-xs">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs border-collapse">
                    <thead class="bg-surface-canvas border-b border-surface-border text-[11px] uppercase tracking-wider text-text-muted font-bold select-none">
                        <tr>
                            <th class="py-3 px-4">Tanggal</th>
                            <th class="py-3 px-4 text-right">Saldo Cash</th>
                            <th class="py-3 px-4 text-right">Saldo Transfer</th>
                            <th class="py-3 px-4 text-right">Total</th>
                            <th class="py-3 px-4 text-center">Waktu Reset</th>
                            <th class="py-3 px-4">Direset Oleh</th>
                            <th class="py-3 px-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-surface-border">
                        @foreach($riwayat as $item)
                            <tr class="hover:bg-surface-container-low/50 transition">
                                <td class="py-3 px-4 font-semibold text-text-slate whitespace-nowrap">
                                    {{ tanggal_id($item->tanggal) }}
                                </td>
                                <td class="py-3 px-4 text-right text-text-muted tabular-nums whitespace-nowrap">
                                    {{ rupiah($item->total_cash) }}
                                </td>
                                <td class="py-3 px-4 text-right text-text-muted tabular-nums whitespace-nowrap">
                                    {{ rupiah($item->total_transfer) }}
                                </td>
                                <td class="py-3 px-4 text-right font-black text-primary tabular-nums whitespace-nowrap">
                                    {{ rupiah($item->total) }}
                                </td>
                                <td class="py-3 px-4 text-center text-text-muted font-mono whitespace-nowrap">
                                    {{ \Carbon\Carbon::parse($item->waktu_reset)->timezone('Asia/Jakarta')->format('H:i') }} WIB
                                </td>
                                <td class="py-3 px-4 whitespace-nowrap">
                                    <div class="flex items-center gap-1.5">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-medium {{ $item->direset_oleh === 'Sistem' ? 'bg-surface-canvas text-text-muted border border-surface-border' : 'bg-primary/10 text-primary font-semibold' }}">
                                            {{ $item->direset_oleh }}
                                        </span>
                                        <span class="text-[10px] text-text-muted capitalize">({{ $item->tipe_reset }})</span>
                                    </div>
                                </td>
                                <td class="py-3 px-4 text-center whitespace-nowrap">
                                    <button
                                        type="button"
                                        wire:click="bukaModalDetail({{ $item->penutupan_id }})"
                                        class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold text-primary hover:bg-primary/10 transition"
                                    >
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        <span>Detail</span>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination Links --}}
            @if($riwayat->hasPages())
                <div class="p-4 border-t border-surface-border">
                    {{ $riwayat->links() }}
                </div>
            @endif
        </div>
    @endif

    {{-- Modal Detail Rincian Sesi Penutupan Laci --}}
    @if($modalDetailOpen && $detailData)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-xs">
            <div 
                x-data 
                x-on:click.outside="$wire.tutupModalDetail()"
                class="bg-surface-card border border-surface-border rounded-2xl max-w-2xl w-full p-6 shadow-xl space-y-5 animate-in fade-in zoom-in-95 duration-150"
            >
                {{-- Header Modal --}}
                <div class="flex items-start justify-between pb-4 border-b border-surface-border">
                    <div>
                        <h3 class="text-base font-bold text-text-slate">
                            Rincian {{ tanggal_id($detailData['penutupan']->tanggal) }}
                        </h3>
                        <p class="text-xs text-text-muted mt-0.5">
                            {{ $detailData['jumlah_transaksi'] }} transaksi • Direset oleh <strong>{{ $detailData['penutupan']->direset_oleh }}</strong> pada pukul {{ \Carbon\Carbon::parse($detailData['penutupan']->waktu_reset)->timezone('Asia/Jakarta')->format('H:i') }} WIB ({{ ucfirst($detailData['penutupan']->tipe_reset) }})
                        </p>
                    </div>
                    <button
                        type="button"
                        wire:click="tutupModalDetail"
                        class="p-1 rounded-lg text-text-muted hover:text-text-slate hover:bg-surface-container-low transition"
                    >
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                {{-- Ringkasan Penutupan --}}
                <div class="grid grid-cols-3 gap-3 p-3 bg-surface-canvas rounded-xl border border-surface-border text-center">
                    <div>
                        <span class="text-[11px] font-semibold text-text-muted block">Cash</span>
                        <span class="text-sm font-bold text-text-slate tabular-nums">{{ rupiah($detailData['penutupan']->total_cash) }}</span>
                    </div>
                    <div>
                        <span class="text-[11px] font-semibold text-text-muted block">Transfer</span>
                        <span class="text-sm font-bold text-text-slate tabular-nums">{{ rupiah($detailData['penutupan']->total_transfer) }}</span>
                    </div>
                    <div>
                        <span class="text-[11px] font-semibold text-text-muted block">Total Ditutup</span>
                        <span class="text-sm font-black text-primary tabular-nums">{{ rupiah($detailData['penutupan']->total) }}</span>
                    </div>
                </div>

                {{-- Tabel Daftar Transaksi Rinci --}}
                <div class="space-y-2">
                    <h4 class="text-xs font-bold text-text-slate uppercase tracking-wider">Daftar Transaksi Kas</h4>
                    <div class="max-h-64 overflow-y-auto border border-surface-border rounded-xl">
                        @if($detailData['transaksi']->isEmpty())
                            <div class="p-6 text-center text-xs text-text-muted">
                                Tidak ada transaksi kas pada sesi penutupan ini (reset saat saldo kosong).
                            </div>
                        @else
                            <table class="w-full text-left text-xs border-collapse">
                                <thead class="bg-surface-canvas text-[11px] uppercase tracking-wider text-text-muted font-bold sticky top-0 border-b border-surface-border">
                                    <tr>
                                        <th class="py-2 px-3">Waktu</th>
                                        <th class="py-2 px-3">Keterangan</th>
                                        <th class="py-2 px-3">Metode</th>
                                        <th class="py-2 px-3 text-right">Nominal</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-surface-border">
                                    @foreach($detailData['transaksi'] as $tx)
                                        <tr class="hover:bg-surface-container-low/40">
                                            <td class="py-2 px-3 text-text-muted font-mono whitespace-nowrap">
                                                {{ \Carbon\Carbon::parse($tx->waktu)->timezone('Asia/Jakarta')->format('H:i') }}
                                            </td>
                                            <td class="py-2 px-3 text-text-slate font-medium">
                                                {{ $tx->keterangan }}
                                            </td>
                                            <td class="py-2 px-3 whitespace-nowrap">
                                                @if(($tx->metode->value ?? $tx->metode) === 'cash')
                                                    <x-ui.badge variant="success" size="sm">Cash</x-ui.badge>
                                                @else
                                                    <x-ui.badge variant="primary" size="sm">Transfer</x-ui.badge>
                                                @endif
                                            </td>
                                            <td class="py-2 px-3 text-right font-bold text-text-slate tabular-nums whitespace-nowrap">
                                                {{ rupiah($tx->jumlah) }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endif
                    </div>
                </div>

                {{-- Footer Modal --}}
                <div class="flex justify-end pt-2">
                    <button
                        type="button"
                        wire:click="tutupModalDetail"
                        class="px-4 py-2 text-xs font-semibold rounded-xl bg-surface-canvas hover:bg-surface-container-low text-text-slate border border-surface-border transition"
                    >
                        Tutup Rincian
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
