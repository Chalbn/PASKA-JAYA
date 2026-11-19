<x-layouts.app title="Daftar Bon" role="owner">
    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-text-on-surface tracking-tight">Daftar Bon</h1>
                <p class="mt-1 text-xs sm:text-sm text-text-muted">Pantau seluruh piutang bon pelanggan secara real-time (mode baca saja).</p>
            </div>
            <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-bold bg-slate-100 text-slate-700 border border-slate-200/90">
                Hanya Lihat
            </span>
        </div>

        <x-ui.card padding="p-0">
            @if(empty($bon) || count($bon) === 0)
                <div class="p-8">
                    <x-ui.empty-state title="Belum ada data bon" description="Catatan piutang bon pelanggan akan tampil di halaman ini." />
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead class="bg-surface-canvas border-b border-surface-border text-[11px] uppercase tracking-wider text-text-muted font-bold">
                            <tr>
                                <th class="py-3 px-4">Kode Bon</th>
                                <th class="py-3 px-4">Pelanggan</th>
                                <th class="py-3 px-4">Jatuh Tempo</th>
                                <th class="py-3 px-4 text-right">Total Tagihan</th>
                                <th class="py-3 px-4 text-right">Sisa</th>
                                <th class="py-3 px-4 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-surface-border">
                            @foreach($bon as $row)
                                <tr>
                                    <td class="py-3 px-4 font-mono font-bold text-primary">{{ $row->kode_bon }}</td>
                                    <td class="py-3 px-4 font-semibold text-text-slate">{{ $row->nama_pelanggan ?? '-' }}</td>
                                    <td class="py-3 px-4 text-text-muted">{{ isset($row->jatuh_tempo) ? tanggal_id($row->jatuh_tempo) : '-' }}</td>
                                    <td class="py-3 px-4 text-right font-bold tabular-nums">{{ rupiah($row->total_tagihan ?? 0) }}</td>
                                    <td class="py-3 px-4 text-right font-bold text-status-danger tabular-nums">{{ rupiah(($row->total_tagihan ?? 0) - ($row->total_terbayar ?? 0)) }}</td>
                                    <td class="py-3 px-4 text-center">
                                        <x-ui.badge :variant="($row->status_bayar ?? '') === 'lunas' ? 'lunas' : 'belum-lunas'">
                                            {{ ($row->status_bayar ?? '') === 'lunas' ? 'Lunas' : 'Belum Lunas' }}
                                        </x-ui.badge>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </x-ui.card>
    </div>
</x-layouts.app>
