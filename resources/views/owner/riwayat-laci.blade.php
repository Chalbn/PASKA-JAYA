<x-layouts.app title="Riwayat Saldo Laci" role="owner">
    <div class="space-y-6">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-text-on-surface tracking-tight">Riwayat Saldo Laci</h1>
            <p class="mt-1 text-xs sm:text-sm text-text-muted">Catatan penutupan laci harian kasir.</p>
        </div>

        <x-ui.card padding="p-0">
            @if(empty($riwayat) || count($riwayat) === 0)
                <div class="p-8">
                    <x-ui.empty-state title="Belum ada riwayat laci" description="Catatan penutupan laci kasir akan muncul di sini." />
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead class="bg-surface-canvas border-b border-surface-border text-[11px] uppercase tracking-wider text-text-muted font-bold">
                            <tr>
                                <th class="py-3 px-4">Tanggal</th>
                                <th class="py-3 px-4 text-right">Cash</th>
                                <th class="py-3 px-4 text-right">Transfer</th>
                                <th class="py-3 px-4 text-right">Total</th>
                                <th class="py-3 px-4">Direset Oleh</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-surface-border">
                            @foreach($riwayat as $item)
                                <tr>
                                    <td class="py-3 px-4 font-semibold text-text-slate">{{ tanggal_id($item->tanggal) }}</td>
                                    <td class="py-3 px-4 text-right tabular-nums">{{ rupiah($item->total_cash) }}</td>
                                    <td class="py-3 px-4 text-right tabular-nums">{{ rupiah($item->total_transfer) }}</td>
                                    <td class="py-3 px-4 text-right font-bold text-primary tabular-nums">{{ rupiah($item->total) }}</td>
                                    <td class="py-3 px-4">{{ $item->direset_oleh ?? 'Sistem' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </x-ui.card>
    </div>
</x-layouts.app>
