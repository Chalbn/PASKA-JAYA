<x-layouts.app title="Laporan Barang Keluar" role="owner">
    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-text-on-surface tracking-tight">Laporan Barang Keluar</h1>
                <p class="mt-1 text-xs sm:text-sm text-text-muted">Rekap transaksi penjualan langsung toko PASKA JAYA.</p>
            </div>
        </div>

        <form method="GET" action="{{ route('owner.laporan') }}" class="bg-surface-card border border-surface-border rounded-2xl p-4 shadow-xs flex flex-wrap items-end gap-3">
            <div>
                <label class="block text-xs font-semibold text-text-muted mb-1">Dari Tanggal</label>
                <input type="date" name="dari" value="{{ request('dari') }}" class="rounded-xl border border-surface-border bg-surface-canvas px-3 py-2 text-xs text-text-slate" />
            </div>
            <div>
                <label class="block text-xs font-semibold text-text-muted mb-1">Sampai Tanggal</label>
                <input type="date" name="sampai" value="{{ request('sampai') }}" class="rounded-xl border border-surface-border bg-surface-canvas px-3 py-2 text-xs text-text-slate" />
            </div>
            <div>
                <label class="block text-xs font-semibold text-text-muted mb-1">Metode Bayar</label>
                <select name="metode" class="rounded-xl border border-surface-border bg-surface-canvas px-3 py-2 text-xs text-text-slate">
                    <option value="semua" @selected(request('metode', 'semua') === 'semua')>Semua Metode</option>
                    <option value="cash" @selected(request('metode') === 'cash')>Cash</option>
                    <option value="transfer" @selected(request('metode') === 'transfer')>Transfer</option>
                </select>
            </div>
            <x-ui.button type="submit" variant="primary" size="sm">Terapkan Filter</x-ui.button>
        </form>

        <x-ui.card padding="p-0">
            @if(empty($daftar))
                <div class="p-8">
                    <x-ui.empty-state title="Belum ada transaksi" description="Tidak ada data penjualan pada rentang waktu yang dipilih." />
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead class="bg-surface-canvas border-b border-surface-border text-[11px] uppercase tracking-wider text-text-muted font-bold">
                            <tr>
                                <th class="py-3 px-4">Kode Transaksi</th>
                                <th class="py-3 px-4">Waktu</th>
                                <th class="py-3 px-4">Pelanggan</th>
                                <th class="py-3 px-4">Metode</th>
                                <th class="py-3 px-4 text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-surface-border">
                            @foreach($daftar as $row)
                                <tr>
                                    <td class="py-3 px-4 font-mono font-bold text-primary">{{ $row->kode_transaksi ?? '-' }}</td>
                                    <td class="py-3 px-4 text-text-muted">{{ isset($row->waktu_transaksi) ? tanggal_id($row->waktu_transaksi, false, true) : '-' }}</td>
                                    <td class="py-3 px-4 font-semibold text-text-slate">{{ $row->nama_pelanggan ?? 'Umum' }}</td>
                                    <td class="py-3 px-4 uppercase font-bold">{{ $row->metode_bayar ?? '-' }}</td>
                                    <td class="py-3 px-4 text-right font-bold text-text-slate tabular-nums">{{ rupiah($row->total ?? 0) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </x-ui.card>
    </div>
</x-layouts.app>
