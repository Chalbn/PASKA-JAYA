<x-layouts.app title="Ringkasan Toko" role="owner">
    <div class="space-y-6">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-text-on-surface tracking-tight">Ringkasan Toko</h1>
            <p class="mt-1 text-xs sm:text-sm text-text-muted">Pantau omzet, stok kritis, dan piutang PASKA JAYA.</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <x-ui.stat-card
                title="Penjualan Hari Ini"
                :value="rupiah($ringkasanToko->penjualan_hari_ini ?? 0)"
                :subtitle="($ringkasanToko->transaksi_hari_ini ?? 0) . ' transaksi'"
                accent="primary"
            />
            <x-ui.stat-card
                title="Penjualan Bulan Ini"
                :value="rupiah($ringkasanToko->penjualan_bulan_ini ?? 0)"
                accent="secondary"
            />
            <x-ui.stat-card
                title="Total Piutang Aktif"
                :value="rupiah($ringkasanToko->total_piutang ?? 0)"
                :subtitle="($ringkasanToko->jumlah_bon_aktif ?? 0) . ' bon belum lunas'"
                accent="tertiary"
                :href="route('owner.bon')"
                actionText="Lihat daftar bon"
            />
            <x-ui.stat-card
                title="Stok Menipis"
                :value="count($stokMenipis ?? []) . ' barang'"
                accent="danger"
                :href="route('owner.master-item')"
                actionText="Cek master item"
            />
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <x-ui.card title="Stok Barang Menipis" subtitle="Barang yang berada pada atau di bawah stok minimum.">
                @if(empty($stokMenipis) || count($stokMenipis) === 0)
                    <x-ui.empty-state title="Stok Aman" description="Seluruh stok barang berada di atas batas minimum." />
                @else
                    <div class="overflow-x-auto max-h-[340px] overflow-y-auto custom-scrollbar">
                        <table class="w-full text-left text-xs border-collapse">
                            <thead>
                                <tr class="border-b border-surface-border text-[11px] uppercase tracking-wider text-text-muted font-bold">
                                    <th class="py-2.5 px-3">Barang</th>
                                    <th class="py-2.5 px-3 text-right">Stok</th>
                                    <th class="py-2.5 px-3 text-right">Minimum</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-surface-border">
                                @foreach($stokMenipis as $item)
                                    <tr>
                                        <td class="py-2.5 px-3 font-semibold text-text-slate">{{ $item->nama_barang }}</td>
                                        <td class="py-2.5 px-3 text-right font-bold text-status-danger tabular-nums">{{ $item->stok }} {{ $item->satuan }}</td>
                                        <td class="py-2.5 px-3 text-right text-text-muted tabular-nums">{{ $item->stok_minimum }} {{ $item->satuan }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </x-ui.card>

            <x-ui.card title="Piutang Aktif" subtitle="Daftar bon pelanggan yang belum lunas.">
                @if(empty($piutangAktif) || count($piutangAktif) === 0)
                    <x-ui.empty-state title="Tidak Ada Piutang" description="Belum ada catatan piutang bon yang aktif." />
                @else
                    <div class="overflow-x-auto max-h-[340px] overflow-y-auto custom-scrollbar">
                        <table class="w-full text-left text-xs border-collapse">
                            <thead>
                                <tr class="border-b border-surface-border text-[11px] uppercase tracking-wider text-text-muted font-bold">
                                    <th class="py-2.5 px-3">Kode Bon</th>
                                    <th class="py-2.5 px-3">Pelanggan</th>
                                    <th class="py-2.5 px-3 text-right">Sisa Tagihan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-surface-border">
                                @foreach($piutangAktif as $bon)
                                    <tr>
                                        <td class="py-2.5 px-3 font-mono font-bold text-primary">{{ $bon->kode_bon }}</td>
                                        <td class="py-2.5 px-3 font-semibold text-text-slate">{{ $bon->nama_pelanggan ?? '-' }}</td>
                                        <td class="py-2.5 px-3 text-right font-bold text-status-danger tabular-nums">{{ rupiah($bon->sisa_tagihan ?? ($bon->total_tagihan - $bon->total_terbayar)) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </x-ui.card>
        </div>
    </div>
</x-layouts.app>
