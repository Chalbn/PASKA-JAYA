@php
    $grafikLabels = collect($ringkasan['grafik_7_hari'])->pluck('hari')->values()->all();
    $grafikTotals = collect($ringkasan['grafik_7_hari'])->pluck('total')->values()->all();
@endphp

<div
    class="space-y-6"
    x-data="ownerPenjualan7HariChart({
        labels: {{ Js::from($grafikLabels) }},
        values: {{ Js::from($grafikTotals) }}
    })"
>
    {{-- 1. HEADER HALAMAN --}}
    <div>
        <h1 class="text-2xl font-bold text-slate-900 tracking-tight">
            Ringkasan Toko
        </h1>
        <p class="mt-1 text-sm text-slate-500">
            Kondisi penjualan, kas, stok, dan piutang hari ini
        </p>
    </div>

    {{-- 2. BARIS 1 — 4 KARTU STATISTIK UTAMA SEJAJAR --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- Kartu 1: Penjualan hari ini --}}
        <div class="bg-sky-50/90 border border-sky-200/80 rounded-2xl p-4 flex items-center gap-3.5 shadow-2xs">
            <div class="p-2.5 rounded-xl bg-sky-100 text-[#1D3FA3] shrink-0">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                </svg>
            </div>
            <div class="min-w-0">
                <p class="text-xs font-medium text-slate-500">Penjualan hari ini</p>
                <p class="text-lg sm:text-xl font-extrabold text-slate-900 tabular-nums leading-tight mt-0.5 whitespace-nowrap">
                    {{ rupiah($ringkasan['penjualan_hari_ini']) }}
                </p>
                <p class="text-[11px] font-medium text-slate-500 tabular-nums mt-0.5">
                    {{ $ringkasan['transaksi_hari_ini'] }} transaksi
                </p>
            </div>
        </div>

        {{-- Kartu 2: Saldo cash (laci) --}}
        <div class="bg-white border border-slate-200/80 rounded-2xl p-4 flex items-center gap-3.5 shadow-2xs">
            <div class="p-2.5 rounded-xl bg-blue-50 text-[#1D3FA3] shrink-0">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                </svg>
            </div>
            <div class="min-w-0">
                <p class="text-xs font-medium text-slate-500">Saldo cash (laci)</p>
                <p class="text-lg sm:text-xl font-extrabold text-slate-900 tabular-nums leading-tight mt-0.5 whitespace-nowrap">
                    {{ rupiah($ringkasan['saldo_cash']) }}
                </p>
                <p class="text-[11px] font-medium text-slate-500 tabular-nums mt-0.5 truncate">
                    Transfer: {{ rupiah($ringkasan['saldo_transfer']) }}
                </p>
            </div>
        </div>

        {{-- Kartu 3: Stok menipis --}}
        <div class="bg-white border border-slate-200/80 rounded-2xl p-4 flex items-center gap-3.5 shadow-2xs">
            <div class="p-2.5 rounded-xl bg-amber-50 text-amber-600 shrink-0">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            <div class="min-w-0">
                <p class="text-xs font-medium text-slate-500">Stok menipis</p>
                <p class="text-lg sm:text-xl font-extrabold text-slate-900 tabular-nums leading-tight mt-0.5">
                    {{ $ringkasan['stok_menipis_count'] }} barang
                </p>
                <p class="text-[11px] font-medium text-slate-500 tabular-nums mt-0.5 truncate">
                    {{ $ringkasan['stok_menipis_count'] }} barang di bawah batas minimum
                </p>
            </div>
        </div>

        {{-- Kartu 4: Bon belum lunas --}}
        <div class="bg-orange-50/60 border border-orange-200/80 rounded-2xl p-4 flex items-center gap-3.5 shadow-2xs">
            <div class="p-2.5 rounded-xl bg-orange-100/90 text-orange-700 shrink-0">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
            </div>
            <div class="min-w-0">
                <p class="text-xs font-medium text-slate-500">Bon belum lunas</p>
                <p class="text-lg sm:text-xl font-extrabold text-slate-900 tabular-nums leading-tight mt-0.5 whitespace-nowrap">
                    {{ rupiah($ringkasan['bon_belum_lunas_total']) }}
                </p>
                <p class="text-[11px] font-medium text-slate-500 tabular-nums mt-0.5">
                    {{ $ringkasan['bon_belum_lunas_pelanggan_count'] }} pelanggan
                </p>
            </div>
        </div>
    </div>

    {{-- 3. BARIS 2 — GRAFIK 7 HARI & PANEL STOK MENIPIS --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5">
        {{-- Kolom Kiri (lg:col-span-8): Kartu Penjualan 7 Hari Terakhir --}}
        <div class="lg:col-span-8 bg-white rounded-2xl border border-slate-200/80 p-6 shadow-2xs flex flex-col justify-between">
            <div>
                <div class="mb-4">
                    <h2 class="text-base font-bold text-slate-900 tracking-tight">
                        Penjualan 7 Hari Terakhir
                    </h2>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Total barang keluar per hari
                    </p>
                </div>

                {{-- Canvas Bar Chart (Chart.js) --}}
                <div class="relative w-full h-56" wire:ignore>
                    <canvas x-ref="canvas"></canvas>
                </div>
            </div>

            {{-- Ringkasan Nilai Singkat Per Hari (Sen–Min) di Bawah Batang --}}
            <div class="grid grid-cols-7 gap-2 mt-4 pt-3 border-t border-slate-100 text-center">
                @foreach($ringkasan['grafik_7_hari'] as $hariItem)
                    <div class="px-1">
                        <p class="text-[11px] font-semibold text-slate-500">{{ $hariItem['hari'] }}</p>
                        <p class="text-xs font-bold text-slate-800 tabular-nums mt-0.5">{{ $hariItem['label_singkat'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Kolom Kanan (lg:col-span-4): Kartu Stok Menipis --}}
        <div class="lg:col-span-4 bg-white rounded-2xl border border-slate-200/80 p-6 shadow-2xs flex flex-col">
            <div class="flex items-center justify-between gap-2 mb-4">
                <h2 class="text-base font-bold text-slate-900 tracking-tight">
                    Stok Menipis
                </h2>
                <a
                    href="{{ route('owner.master-item') }}"
                    class="text-xs font-semibold text-[#1D3FA3] hover:underline"
                >
                    Kelola
                </a>
            </div>

            @if($ringkasan['stok_menipis_daftar']->isEmpty())
                <div class="flex-1 flex flex-col items-center justify-center py-8 text-center">
                    <div class="w-11 h-11 rounded-2xl bg-slate-50 text-slate-400 flex items-center justify-center mb-2.5">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                    </div>
                    <p class="text-xs font-semibold text-slate-700">Semua stok aman</p>
                    <p class="text-[11px] text-slate-400 mt-1">Belum ada barang di bawah batas minimum.</p>
                </div>
            @else
                <div class="divide-y divide-slate-100">
                    @foreach($ringkasan['stok_menipis_daftar'] as $item)
                        <div class="py-3 first:pt-0 last:pb-0 flex items-center justify-between gap-3">
                            <span class="text-xs sm:text-sm font-medium text-slate-700 truncate">
                                {{ $item->nama_barang }}
                            </span>
                            <span class="bg-rose-50 text-rose-600 rounded-full px-2.5 py-0.5 text-xs font-semibold tabular-nums shrink-0 whitespace-nowrap">
                                {{ $item->stok }} {{ $item->satuan }}
                            </span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- 4. BARIS 3 — DUA PANEL DAFTAR BAWAH --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
        {{-- Kolom Kiri: Bon Belum Lunas --}}
        <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-2xs flex flex-col">
            <div class="mb-4">
                <h2 class="text-base font-bold text-slate-900 tracking-tight">
                    Bon Belum Lunas
                </h2>
            </div>

            @if($ringkasan['bon_belum_lunas_daftar']->isEmpty())
                <div class="flex-1 flex flex-col items-center justify-center py-8 text-center">
                    <div class="w-11 h-11 rounded-2xl bg-slate-50 text-slate-400 flex items-center justify-center mb-2.5">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <p class="text-xs font-semibold text-slate-700">Belum ada bon belum lunas</p>
                    <p class="text-[11px] text-slate-400 mt-1">Piutang pelanggan yang belum lunas akan tampil di sini.</p>
                </div>
            @else
                <div class="divide-y divide-slate-100">
                    @foreach($ringkasan['bon_belum_lunas_daftar'] as $bon)
                        <div class="py-3 first:pt-0 last:pb-0 flex items-center justify-between gap-3">
                            <div class="min-w-0">
                                <p class="text-xs sm:text-sm font-bold text-slate-800 truncate">
                                    {{ $bon->nama_pelanggan }}
                                </p>
                                <p class="text-[11px] text-slate-400 mt-0.5">
                                    Jatuh tempo {{ tanggal_id($bon->jatuh_tempo) }}
                                </p>
                            </div>
                            <span class="text-xs sm:text-sm font-bold text-slate-900 tabular-nums whitespace-nowrap shrink-0">
                                {{ rupiah($bon->sisa) }}
                            </span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Kolom Kanan: Transaksi Terbaru --}}
        <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-2xs flex flex-col">
            <div class="flex items-center justify-between gap-2 mb-4">
                <h2 class="text-base font-bold text-slate-900 tracking-tight">
                    Transaksi Terbaru
                </h2>
                <a
                    href="{{ route('owner.laporan') }}"
                    class="text-xs font-semibold text-[#1D3FA3] hover:underline"
                >
                    Laporan
                </a>
            </div>

            @if($ringkasan['transaksi_terbaru']->isEmpty())
                <div class="flex-1 flex flex-col items-center justify-center py-8 text-center">
                    <div class="w-11 h-11 rounded-2xl bg-slate-50 text-slate-400 flex items-center justify-center mb-2.5">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                    <p class="text-xs font-semibold text-slate-700">Belum ada transaksi terbaru</p>
                    <p class="text-[11px] text-slate-400 mt-1">Transaksi barang keluar terbaru akan muncul di sini.</p>
                </div>
            @else
                <div class="divide-y divide-slate-100">
                    @foreach($ringkasan['transaksi_terbaru'] as $trx)
                        @php
                            $metodeVal = strtolower((string) ($trx->metode_bayar ?? 'cash'));
                            $dt = $trx->waktu_transaksi
                                ? \Carbon\Carbon::parse($trx->waktu_transaksi)->timezone('Asia/Jakarta')
                                : null;
                            $waktuStr = $dt ? tanggal_id($dt).', '.$dt->format('H.i') : '-';
                        @endphp
                        <div class="py-3 first:pt-0 last:pb-0 flex items-center justify-between gap-3">
                            <div class="min-w-0">
                                <p class="text-xs sm:text-sm font-bold text-slate-800 truncate">
                                    {{ $trx->nama_pelanggan ?: 'Umum' }}
                                </p>
                                <p class="text-[11px] text-slate-400 mt-0.5 tabular-nums">
                                    {{ $waktuStr }}
                                </p>
                            </div>
                            <div class="flex items-center gap-2.5 shrink-0">
                                @if($metodeVal === 'transfer')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-blue-800">
                                        TRANSFER
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-[#1E3F99] text-white">
                                        CASH
                                    </span>
                                @endif
                                <span class="text-xs sm:text-sm font-bold text-slate-900 tabular-nums whitespace-nowrap">
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

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('ownerPenjualan7HariChart', (initialData) => ({
        chart: null,
        init() {
            this.$nextTick(() => {
                this.renderChart(initialData);
            });
        },
        renderChart(data) {
            if (typeof window.Chart === 'undefined' || !this.$refs.canvas) {
                return;
            }

            const ctx = this.$refs.canvas.getContext('2d');
            if (this.chart) {
                this.chart.destroy();
            }

            const values = (data.values || []).map(v => Number(v) || 0);
            const maxVal = Math.max(...values, 0);

            const formatRupiah = (value) => {
                return 'Rp ' + Math.round(Number(value) || 0).toLocaleString('id-ID');
            };

            this.chart = new window.Chart(ctx, {
                type: 'bar',
                data: {
                    labels: data.labels || [],
                    datasets: [{
                        label: 'Penjualan',
                        data: values,
                        backgroundColor: '#1D3FA3',
                        hoverBackgroundColor: '#162D4D',
                        borderRadius: 6,
                        borderSkipped: false,
                        maxBarThickness: 44,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: '#0F172A',
                            titleFont: { size: 12, weight: 'bold' },
                            bodyFont: { size: 12 },
                            padding: 10,
                            cornerRadius: 8,
                            callbacks: {
                                label: function(context) {
                                    return ' Total: ' + formatRupiah(context.parsed.y);
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                color: '#64748B',
                                font: {
                                    family: "'Plus Jakarta Sans', sans-serif",
                                    weight: '600',
                                    size: 11
                                }
                            }
                        },
                        y: {
                            beginAtZero: true,
                            suggestedMax: maxVal === 0 ? 100000 : undefined,
                            grid: {
                                color: '#F1F5F9'
                            },
                            ticks: {
                                color: '#64748B',
                                font: {
                                    family: "'Plus Jakarta Sans', sans-serif",
                                    size: 11
                                },
                                callback: function(value) {
                                    if (value >= 1000000) {
                                        return 'Rp ' + (value / 1000000).toLocaleString('id-ID') + ' jt';
                                    }
                                    if (value >= 1000) {
                                        return 'Rp ' + (value / 1000).toLocaleString('id-ID') + ' rb';
                                    }
                                    return 'Rp ' + value;
                                }
                            }
                        }
                    }
                }
            });
        }
    }));
});
</script>
