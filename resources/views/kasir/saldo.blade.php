<x-layouts.app title="Lihat Saldo" role="kasir">
<div class="space-y-6" x-data="{ modalResetOpen: false }">
    {{-- Header Halaman --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-text-slate tracking-tight">Lihat Saldo</h1>
            <p class="text-xs sm:text-sm text-text-muted mt-0.5">
                Pantau saldo kasir aktif saat ini dan riwayat penutupan laci harian.
            </p>
        </div>
        <div class="flex items-center gap-2">
            <a 
                href="{{ route('kasir.riwayat-laci') }}"
                class="inline-flex items-center justify-center font-medium rounded-xl transition duration-150 ease-in-out border border-surface-border bg-surface-card hover:bg-surface-container-low text-text-slate text-xs px-3.5 py-2 shadow-xs gap-1.5"
            >
                <svg class="w-4 h-4 text-text-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>Riwayat Saldo Lengkap</span>
            </a>
            <a 
                href="{{ route('kasir.grafik') }}"
                class="inline-flex items-center justify-center font-medium rounded-xl transition duration-150 ease-in-out border border-surface-border bg-surface-card hover:bg-surface-container-low text-text-slate text-xs px-3.5 py-2 shadow-xs gap-1.5"
            >
                <svg class="w-4 h-4 text-text-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z" />
                </svg>
                <span>Grafik Mingguan</span>
            </a>
        </div>
    </div>

    {{-- Kartu Saldo Laci Aktif --}}
    <div class="bg-surface-card border border-surface-border rounded-2xl p-5 sm:p-6 shadow-xs relative overflow-hidden">
        {{-- Latar aksen gradasi halus khas Toko Bangunan --}}
        <div class="absolute top-0 right-0 -mr-16 -mt-16 w-64 h-64 rounded-full bg-primary/5 pointer-events-none blur-2xl"></div>

        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4 pb-5 border-b border-surface-border">
            <div>
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-primary/10 text-primary uppercase tracking-wider">
                        <span class="w-2 h-2 rounded-full bg-status-success animate-pulse"></span>
                        Saldo Aktif Saat Ini
                    </span>
                    <span class="text-xs text-text-muted">
                        • Sesi kasir berjalan
                    </span>
                </div>
                <div class="mt-2">
                    <span class="text-3xl sm:text-4xl font-black text-text-slate tracking-tight tabular-nums">
                        {{ rupiah($saldo['saldo_total']) }}
                    </span>
                </div>
            </div>

            <div class="shrink-0">
                <button
                    type="button"
                    x-on:click="modalResetOpen = true"
                    class="inline-flex items-center justify-center font-semibold rounded-xl transition duration-150 ease-in-out bg-primary hover:bg-primary-container text-primary-on px-4 py-2.5 text-xs shadow-xs gap-2 select-none"
                >
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    <span>Reset Manual Laci</span>
                </button>
            </div>
        </div>

        {{-- Breakdown Cash vs Transfer --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-5">
            <div class="p-4 rounded-xl bg-surface-canvas border border-surface-border flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-status-success-bg text-status-success flex items-center justify-center font-bold">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <div>
                        <span class="text-xs font-semibold text-text-muted block">Saldo Uang Tunai (Cash)</span>
                        <span class="text-base sm:text-lg font-bold text-text-slate tabular-nums">
                            {{ rupiah($saldo['saldo_cash']) }}
                        </span>
                    </div>
                </div>
                <x-ui.badge variant="success" size="sm">Tunai</x-ui.badge>
            </div>

            <div class="p-4 rounded-xl bg-surface-canvas border border-surface-border flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-primary/10 text-primary flex items-center justify-center font-bold">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                        </svg>
                    </div>
                    <div>
                        <span class="text-xs font-semibold text-text-muted block">Saldo Transfer Bank</span>
                        <span class="text-base sm:text-lg font-bold text-text-slate tabular-nums">
                            {{ rupiah($saldo['saldo_transfer']) }}
                        </span>
                    </div>
                </div>
                <x-ui.badge variant="primary" size="sm">Non-Tunai</x-ui.badge>
            </div>
        </div>

        {{-- Footnote Transaksi Aktif --}}
        <div class="mt-4 pt-3 border-t border-surface-border flex items-center justify-between text-xs text-text-muted">
            <span class="inline-flex items-center gap-1.5">
                <svg class="w-4 h-4 text-text-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ $saldo['jumlah_transaksi'] }} transaksi tercatat pada saldo aktif saat ini.</span>
            </span>
            <span class="text-[11px] text-text-muted hidden sm:inline">
                Reset otomatis dijadwalkan setiap pukul 00:00 WIB
            </span>
        </div>
    </div>

    {{-- Grid 2 Kolom: Transaksi Aktif Terbaru & Riwayat 7 Hari --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Kolom Kiri: Transaksi Terbaru pada Saldo Aktif --}}
        <div class="bg-surface-card border border-surface-border rounded-2xl p-5 shadow-xs flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between pb-3 border-b border-surface-border mb-3">
                    <div>
                        <h2 class="text-sm font-bold text-text-slate">Transaksi Terbaru</h2>
                        <p class="text-[11px] text-text-muted">Transaksi kas masuk pada sesi saldo laci aktif.</p>
                    </div>
                    <x-ui.badge variant="neutral" size="sm">
                        {{ $transaksiTerbaru->count() }} Transaksi
                    </x-ui.badge>
                </div>

                @if($transaksiTerbaru->isEmpty())
                    <x-ui.empty-state
                        title="Belum ada transaksi aktif"
                        description="Belum ada transaksi penjualan langsung atau pelunasan bon pada sesi laci saat ini."
                    />
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs border-collapse">
                            <thead>
                                <tr class="border-b border-surface-border text-[11px] uppercase tracking-wider text-text-muted font-bold">
                                    <th class="py-2.5 px-3">Waktu</th>
                                    <th class="py-2.5 px-3">Keterangan</th>
                                    <th class="py-2.5 px-3">Metode</th>
                                    <th class="py-2.5 px-3 text-right">Nominal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-surface-border">
                                @foreach($transaksiTerbaru as $trx)
                                    <tr class="hover:bg-surface-container-low/50 transition">
                                        <td class="py-2.5 px-3 text-text-muted font-mono whitespace-nowrap">
                                            {{ \Carbon\Carbon::parse($trx->waktu)->timezone('Asia/Jakarta')->format('H:i') }}
                                        </td>
                                        <td class="py-2.5 px-3 font-medium text-text-slate max-w-[200px] truncate" title="{{ $trx->keterangan }}">
                                            {{ $trx->keterangan }}
                                        </td>
                                        <td class="py-2.5 px-3 whitespace-nowrap">
                                            @if(($trx->metode->value ?? $trx->metode) === 'cash')
                                                <x-ui.badge variant="success" size="sm">Cash</x-ui.badge>
                                            @else
                                                <x-ui.badge variant="primary" size="sm">Transfer</x-ui.badge>
                                            @endif
                                        </td>
                                        <td class="py-2.5 px-3 text-right font-bold text-text-slate tabular-nums whitespace-nowrap">
                                            {{ rupiah($trx->jumlah) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

        {{-- Kolom Kanan: Riwayat 7 Hari Terakhir --}}
        <div class="bg-surface-card border border-surface-border rounded-2xl p-5 shadow-xs flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between pb-3 border-b border-surface-border mb-3">
                    <div>
                        <h2 class="text-sm font-bold text-text-slate">Riwayat 7 Hari Terakhir</h2>
                        <p class="text-[11px] text-text-muted">Hasil penutupan laci kasir harian sebelumnya.</p>
                    </div>
                    <a 
                        href="{{ route('kasir.riwayat-laci') }}" 
                        class="text-xs font-bold text-primary hover:underline inline-flex items-center gap-1"
                    >
                        <span>Lihat Semua</span>
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </div>

                @if($riwayat7Hari->isEmpty())
                    <x-ui.empty-state
                        title="Belum ada riwayat"
                        description="Belum ada data penutupan laci sebelumnya."
                    />
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs border-collapse">
                            <thead>
                                <tr class="border-b border-surface-border text-[11px] uppercase tracking-wider text-text-muted font-bold">
                                    <th class="py-2.5 px-3">Tanggal</th>
                                    <th class="py-2.5 px-3 text-right">Cash</th>
                                    <th class="py-2.5 px-3 text-right">Transfer</th>
                                    <th class="py-2.5 px-3 text-right">Total</th>
                                    <th class="py-2.5 px-3 text-center">Direset Oleh</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-surface-border">
                                @foreach($riwayat7Hari as $rwt)
                                    <tr class="hover:bg-surface-container-low/50 transition">
                                        <td class="py-2.5 px-3 font-medium text-text-slate whitespace-nowrap">
                                            {{ tanggal_id($rwt->tanggal) }}
                                        </td>
                                        <td class="py-2.5 px-3 text-right text-text-muted tabular-nums whitespace-nowrap">
                                            {{ rupiah($rwt->total_cash) }}
                                        </td>
                                        <td class="py-2.5 px-3 text-right text-text-muted tabular-nums whitespace-nowrap">
                                            {{ rupiah($rwt->total_transfer) }}
                                        </td>
                                        <td class="py-2.5 px-3 text-right font-bold text-primary tabular-nums whitespace-nowrap">
                                            {{ rupiah($rwt->total) }}
                                        </td>
                                        <td class="py-2.5 px-3 text-center whitespace-nowrap">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-medium {{ $rwt->direset_oleh === 'Sistem' ? 'bg-surface-canvas text-text-muted border border-surface-border' : 'bg-primary/10 text-primary font-semibold' }}">
                                                {{ $rwt->direset_oleh }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Modal Konfirmasi Reset Manual Laci --}}
    <div x-show="modalResetOpen" style="display: none;"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-xs">
            <div 
                x-data 
                x-on:click.outside="modalResetOpen = false"
                class="bg-surface-card border border-surface-border rounded-2xl max-w-md w-full p-6 shadow-xl space-y-4 animate-in fade-in zoom-in-95 duration-150"
            >
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-primary/10 text-primary flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-base font-bold text-text-slate">Reset Saldo Laci Kasir?</h3>
                        <p class="text-xs text-text-muted mt-1 leading-relaxed">
                            Seluruh transaksi aktif saat ini sebesar <strong class="text-text-slate font-bold">{{ rupiah($saldo['saldo_total']) }}</strong> akan dibukukan ke riwayat penutupan laci. Saldo laci kasir aktif akan dimulai kembali dari <strong class="text-text-slate">Rp 0</strong>.
                        </p>
                    </div>
                </div>

                <div class="p-3 bg-surface-canvas rounded-xl border border-surface-border text-xs space-y-1">
                    <div class="flex justify-between">
                        <span class="text-text-muted">Total Tunai (Cash):</span>
                        <span class="font-bold text-text-slate">{{ rupiah($saldo['saldo_cash']) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-text-muted">Total Transfer Bank:</span>
                        <span class="font-bold text-text-slate">{{ rupiah($saldo['saldo_transfer']) }}</span>
                    </div>
                    <div class="flex justify-between pt-1 border-t border-surface-border">
                        <span class="font-semibold text-text-muted">Total yang Ditutup:</span>
                        <span class="font-bold text-primary">{{ rupiah($saldo['saldo_total']) }}</span>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2.5 pt-2">
                    <button
                        type="button"
                        x-on:click="modalResetOpen = false"
                        class="px-4 py-2 text-xs font-semibold rounded-xl border border-surface-border bg-surface-card hover:bg-surface-container-low text-text-slate transition"
                    >
                        Batal
                    </button>
                    <form method="POST" action="{{ route('kasir.saldo.reset') }}" class="inline">
                        @csrf
                        <button
                            type="submit"
                            class="px-4 py-2 text-xs font-semibold rounded-xl bg-primary hover:bg-primary-container text-primary-on shadow-xs transition cursor-pointer"
                        >
                            Ya, Reset Saldo
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

</x-layouts.app>
