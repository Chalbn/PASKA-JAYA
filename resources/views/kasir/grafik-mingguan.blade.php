<x-layouts.app title="Grafik Penjualan Mingguan" role="kasir">
<div class="space-y-6" x-data="penjualanMingguanChart({
    labels: {{ Js::from(collect($data['hari'])->pluck('nama_hari')) }},
    cashData: {{ Js::from(collect($data['hari'])->pluck('total_cash')) }},
    transferData: {{ Js::from(collect($data['hari'])->pluck('total_transfer')) }}
})">
    {{-- Header Halaman & Filter Rentang Tanggal --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-text-slate tracking-tight">Grafik Penjualan Mingguan</h1>
            <p class="text-xs sm:text-sm text-text-muted mt-0.5">
                Perbandingan transaksi Cash dan Transfer per hari (Senin – Minggu).
            </p>
        </div>

        {{-- Dropdown Rentang Waktu & Custom Date --}}
        <form method="GET" action="{{ route('kasir.grafik') }}" class="flex flex-wrap items-center gap-2">
            @if(($mode ?? 'minggu_ini') === 'pilih_tanggal')
                <div class="w-40">
                    <input
                        type="date"
                        name="customTanggal"
                        value="{{ $customTanggal ?? now()->toDateString() }}"
                        onchange="this.form.submit()"
                        class="w-full text-xs font-semibold rounded-xl border border-surface-border bg-surface-card text-text-slate px-3 py-2 shadow-xs focus:ring-2 focus:ring-primary focus:outline-none"
                    />
                </div>
            @endif

            <div class="relative">
                <select
                    name="mode"
                    onchange="this.form.submit()"
                    class="text-xs font-semibold rounded-xl border border-surface-border bg-surface-card text-text-slate pl-3 pr-8 py-2 shadow-xs focus:ring-2 focus:ring-primary focus:outline-none cursor-pointer"
                >
                    <option value="minggu_ini" @selected(($mode ?? 'minggu_ini') === 'minggu_ini')>Minggu Ini</option>
                    <option value="minggu_lalu" @selected(($mode ?? '') === 'minggu_lalu')>Minggu Lalu</option>
                    <option value="pilih_tanggal" @selected(($mode ?? '') === 'pilih_tanggal')>Pilih Tanggal</option>
                </select>
            </div>

            <a 
                href="{{ route('kasir.saldo') }}"
                class="inline-flex items-center justify-center font-medium rounded-xl transition duration-150 ease-in-out border border-surface-border bg-surface-card hover:bg-surface-container-low text-text-slate text-xs px-3.5 py-2 shadow-xs gap-1.5"
            >
                <svg class="w-4 h-4 text-text-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                </svg>
                <span>Lihat Saldo</span>
            </a>
        </form>
    </div>

    {{-- 3 Kartu Statistik Ringkasan --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        {{-- Kartu 1: Total Penjualan --}}
        <div class="bg-surface-card border border-surface-border rounded-2xl p-5 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-text-muted uppercase tracking-wider">Total Penjualan</span>
                <span class="p-2 rounded-xl bg-primary/10 text-primary">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </span>
            </div>
            <div class="mt-3">
                <div class="text-2xl font-black text-text-slate tracking-tight tabular-nums">
                    {{ rupiah($data['total_penjualan']) }}
                </div>
                <div class="flex items-center gap-2 mt-1 text-xs text-text-muted">
                    <span>Rentang: {{ $data['rentang_label'] }}</span>
                </div>
            </div>
        </div>

        {{-- Kartu 2: Hari Tertinggi --}}
        <div class="bg-surface-card border border-surface-border rounded-2xl p-5 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-text-muted uppercase tracking-wider">Hari Tertinggi</span>
                <span class="p-2 rounded-xl bg-status-success-bg text-status-success">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                    </svg>
                </span>
            </div>
            <div class="mt-3">
                <div class="text-2xl font-black text-text-slate tracking-tight">
                    @if($data['hari_tertinggi'])
                        {{ $data['hari_tertinggi']['hari'] }}
                    @else
                        -
                    @endif
                </div>
                <div class="mt-1 text-xs text-text-muted font-medium">
                    @if($data['hari_tertinggi'])
                        <span class="font-bold text-status-success tabular-nums">{{ rupiah($data['hari_tertinggi']['total']) }}</span>
                        <span>({{ $data['hari_tertinggi']['tanggal'] }})</span>
                    @else
                        <span>Belum ada transaksi</span>
                    @endif
                </div>
            </div>
        </div>

        {{-- Kartu 3: Dibanding Minggu Lalu --}}
        <div class="bg-surface-card border border-surface-border rounded-2xl p-5 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-text-muted uppercase tracking-wider">Dibanding Minggu Lalu</span>
                @if($data['dibanding_minggu_lalu']['tren'] === 'meningkat')
                    <span class="p-2 rounded-xl bg-status-success-bg text-status-success">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" />
                        </svg>
                    </span>
                @elseif($data['dibanding_minggu_lalu']['tren'] === 'menurun')
                    <span class="p-2 rounded-xl bg-status-danger-bg text-status-danger">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                        </svg>
                    </span>
                @else
                    <span class="p-2 rounded-xl bg-surface-canvas text-text-muted">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                        </svg>
                    </span>
                @endif
            </div>
            <div class="mt-3">
                <div class="text-2xl font-black text-text-slate tracking-tight tabular-nums flex items-center gap-2">
                    @if($data['dibanding_minggu_lalu']['tren'] === 'meningkat')
                        <span class="text-status-success">+{{ $data['dibanding_minggu_lalu']['persen'] }}%</span>
                    @elseif($data['dibanding_minggu_lalu']['tren'] === 'menurun')
                        <span class="text-status-danger">-{{ $data['dibanding_minggu_lalu']['persen'] }}%</span>
                    @else
                        <span class="text-text-muted">0%</span>
                    @endif
                </div>
                <div class="mt-1 text-xs">
                    @if($data['dibanding_minggu_lalu']['tren'] === 'meningkat')
                        <span class="font-bold text-status-success">Penjualan meningkat</span>
                        <span class="text-text-muted">dari {{ rupiah($data['dibanding_minggu_lalu']['total_lalu']) }}</span>
                    @elseif($data['dibanding_minggu_lalu']['tren'] === 'menurun')
                        <span class="font-bold text-status-danger">Penjualan menurun</span>
                        <span class="text-text-muted">dari {{ rupiah($data['dibanding_minggu_lalu']['total_lalu']) }}</span>
                    @else
                        <span class="text-text-muted">Penjualan stabil</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Kartu Stacked Bar Chart --}}
    <div class="bg-surface-card border border-surface-border rounded-2xl p-5 sm:p-6 shadow-xs space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-4 border-b border-surface-border">
            <div>
                <h2 class="text-base font-bold text-text-slate">Perbandingan Penjualan Harian</h2>
                <p class="text-xs text-text-muted">Distribusi transaksi Tunai (Cash) dan Transfer Bank per hari.</p>
            </div>
            {{-- Legenda Kustom --}}
            <div class="flex items-center gap-4 text-xs font-semibold">
                <div class="flex items-center gap-2">
                    <span class="w-3.5 h-3.5 rounded-md bg-[#1E3A8A]"></span>
                    <span class="text-text-slate">Cash (Tunai): <strong class="tabular-nums">{{ rupiah($data['total_cash']) }}</strong></span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-3.5 h-3.5 rounded-md bg-[#60A5FA]"></span>
                    <span class="text-text-slate">Transfer: <strong class="tabular-nums">{{ rupiah($data['total_transfer']) }}</strong></span>
                </div>
            </div>
        </div>

        {{-- Area Canvas Chart.js --}}
        <div class="relative w-full h-80 sm:h-96">
            <canvas x-ref="canvas"></canvas>
        </div>

        {{-- Tabel Ringkasan Rinci di Bawah Grafik --}}
        <div class="pt-4 border-t border-surface-border overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="text-[11px] uppercase tracking-wider text-text-muted font-bold border-b border-surface-border">
                        <th class="py-2.5 px-3">Hari & Tanggal</th>
                        <th class="py-2.5 px-3 text-right">Cash</th>
                        <th class="py-2.5 px-3 text-right">Transfer</th>
                        <th class="py-2.5 px-3 text-right">Total Harian</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-surface-border">
                    @foreach($data['hari'] as $item)
                        <tr class="hover:bg-surface-container-low/50 transition">
                            <td class="py-2.5 px-3 font-semibold text-text-slate whitespace-nowrap">
                                {{ $item['label'] }}
                            </td>
                            <td class="py-2.5 px-3 text-right text-text-muted tabular-nums whitespace-nowrap">
                                {{ rupiah($item['total_cash']) }}
                            </td>
                            <td class="py-2.5 px-3 text-right text-text-muted tabular-nums whitespace-nowrap">
                                {{ rupiah($item['total_transfer']) }}
                            </td>
                            <td class="py-2.5 px-3 text-right font-bold text-primary tabular-nums whitespace-nowrap">
                                {{ rupiah($item['total']) }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('penjualanMingguanChart', (initialData) => ({
        chart: null,
        init() {
            this.$nextTick(() => {
                this.renderChart(initialData);
            });

            window.addEventListener('chart-data-updated', (e) => {
                const chartData = e.detail.chartData;
                if (!chartData || !this.chart) return;

                const labels = chartData.hari.map(h => h.nama_hari);
                const cash = chartData.hari.map(h => h.total_cash);
                const transfer = chartData.hari.map(h => h.total_transfer);

                this.chart.data.labels = labels;
                this.chart.data.datasets[0].data = cash;
                this.chart.data.datasets[1].data = transfer;
                this.chart.update();
            });
        },
        renderChart(data) {
            const ctx = this.$refs.canvas.getContext('2d');
            if (this.chart) {
                this.chart.destroy();
            }

            const formatRupiah = (value) => {
                return 'Rp ' + Number(value).toLocaleString('id-ID');
            };

            this.chart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: data.labels,
                    datasets: [
                        {
                            label: 'Cash (Tunai)',
                            data: data.cashData,
                            backgroundColor: '#1E3A8A', // Navy primary
                            borderRadius: 6,
                            stack: 'penjualan',
                        },
                        {
                            label: 'Transfer Bank',
                            data: data.transferData,
                            backgroundColor: '#60A5FA', // Biru muda
                            borderRadius: 6,
                            stack: 'penjualan',
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false,
                    },
                    plugins: {
                        legend: {
                            display: false, // Digantikan legenda kustom HTML di atas
                        },
                        tooltip: {
                            backgroundColor: '#0F172A',
                            titleFont: { size: 12, weight: 'bold' },
                            bodyFont: { size: 12 },
                            padding: 12,
                            cornerRadius: 10,
                            callbacks: {
                                label: function(context) {
                                    return context.dataset.label + ': ' + formatRupiah(context.parsed.y);
                                },
                                footer: function(tooltipItems) {
                                    let sum = 0;
                                    tooltipItems.forEach(item => {
                                        sum += item.parsed.y;
                                    });
                                    return 'Total: ' + formatRupiah(sum);
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            stacked: true,
                            grid: {
                                display: false
                            },
                            ticks: {
                                font: {
                                    size: 11,
                                    weight: '600'
                                },
                                color: '#64748B'
                            }
                        },
                        y: {
                            stacked: true,
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Nominal dalam Rupiah',
                                color: '#64748B',
                                font: {
                                    size: 11,
                                    weight: 'bold'
                                }
                            },
                            grid: {
                                color: '#F1F5F9'
                            },
                            ticks: {
                                font: {
                                    size: 11
                                },
                                color: '#64748B',
                                callback: function(value) {
                                    return formatRupiah(value);
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

</x-layouts.app>
