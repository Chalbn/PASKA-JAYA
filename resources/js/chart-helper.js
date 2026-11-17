/**
 * Helper Chart.js & Alpine.js untuk PASKA JAYA
 * Sesuai panduan UI/UX: Bar & Stacked Bar dengan palet Police Blue (#2E4365) & Marigold (#E59D2C)
 * Tooltip & Sumbu berformat Rupiah (Rp 1.820.000). Data kosong tetap menggambar sumbu bernilai 0.
 */

export function formatRupiah(num) {
    if (num === null || num === undefined || isNaN(num)) return 'Rp 0';
    return 'Rp ' + Math.round(Number(num)).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
}

/**
 * Bar Chart Component untuk Penjualan 7 Hari
 * Warna: Police Blue (#2E4365)
 */
export function paskaChartBar(config = {}) {
    return {
        chart: null,
        labels: config.labels && config.labels.length ? config.labels : ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'],
        values: config.values && config.values.length ? config.values : [0, 0, 0, 0, 0, 0, 0],
        title: config.title || 'Total Penjualan',

        init() {
            this.$nextTick(() => {
                this.renderChart();
            });
        },

        renderChart() {
            if (typeof window.Chart === 'undefined') {
                console.warn('Chart.js belum dimuat.');
                return;
            }

            const ctx = this.$refs.canvas;
            if (!ctx) return;

            if (this.chart) {
                this.chart.destroy();
            }

            const dataValues = this.values.map(v => Number(v) || 0);

            this.chart = new window.Chart(ctx, {
                type: 'bar',
                data: {
                    labels: this.labels,
                    datasets: [{
                        label: this.title,
                        data: dataValues,
                        backgroundColor: '#2E4365', // Police Blue
                        hoverBackgroundColor: '#162D4D', // Deep Navy
                        borderRadius: 6,
                        borderSkipped: false,
                        maxBarThickness: 42,
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
                            backgroundColor: '#162D4D',
                            titleColor: '#FFFFFF',
                            bodyColor: '#D6E3FF',
                            padding: 10,
                            cornerRadius: 8,
                            callbacks: {
                                label: function(context) {
                                    return ' ' + context.dataset.label + ': ' + formatRupiah(context.parsed.y);
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
                                    weight: 600,
                                    size: 11
                                }
                            }
                        },
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: '#E2E8F0',
                                strokeDash: [4, 4]
                            },
                            ticks: {
                                color: '#64748B',
                                font: {
                                    family: "'Plus Jakarta Sans', sans-serif",
                                    size: 11
                                },
                                callback: function(value) {
                                    if (value >= 1000000) {
                                        return 'Rp ' + (value / 1000000) + ' jt';
                                    } else if (value >= 1000) {
                                        return 'Rp ' + (value / 1000) + ' rb';
                                    }
                                    return 'Rp ' + value;
                                }
                            }
                        }
                    }
                }
            });
        },

        updateData(newValues, newLabels) {
            this.values = newValues || [0, 0, 0, 0, 0, 0, 0];
            if (newLabels) this.labels = newLabels;
            this.renderChart();
        },

        destroy() {
            if (this.chart) {
                this.chart.destroy();
                this.chart = null;
            }
        }
    };
}

/**
 * Stacked Bar Chart Component untuk Saldo/Kas Mingguan (Cash + Transfer)
 * Warna: Cash (#2E4365 - Police Blue) + Transfer (#E59D2C - Marigold)
 */
export function paskaChartStackedBar(config = {}) {
    return {
        chart: null,
        labels: config.labels && config.labels.length ? config.labels : ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'],
        cashData: config.cash && config.cash.length ? config.cash : [0, 0, 0, 0, 0, 0, 0],
        transferData: config.transfer && config.transfer.length ? config.transfer : [0, 0, 0, 0, 0, 0, 0],

        init() {
            this.$nextTick(() => {
                this.renderChart();
            });
        },

        renderChart() {
            if (typeof window.Chart === 'undefined') {
                console.warn('Chart.js belum dimuat.');
                return;
            }

            const ctx = this.$refs.canvas;
            if (!ctx) return;

            if (this.chart) {
                this.chart.destroy();
            }

            const cleanCash = this.cashData.map(v => Number(v) || 0);
            const cleanTransfer = this.transferData.map(v => Number(v) || 0);

            this.chart = new window.Chart(ctx, {
                type: 'bar',
                data: {
                    labels: this.labels,
                    datasets: [
                        {
                            label: 'Cash (Laci)',
                            data: cleanCash,
                            backgroundColor: '#2E4365', // Police Blue
                            hoverBackgroundColor: '#162D4D',
                            borderRadius: { topLeft: 0, topRight: 0, bottomLeft: 4, bottomRight: 4 },
                            maxBarThickness: 38,
                        },
                        {
                            label: 'Transfer Bank',
                            data: cleanTransfer,
                            backgroundColor: '#E59D2C', // Marigold
                            hoverBackgroundColor: '#FEB240',
                            borderRadius: { topLeft: 4, topRight: 4, bottomLeft: 0, bottomRight: 0 },
                            maxBarThickness: 38,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                            align: 'end',
                            labels: {
                                boxWidth: 12,
                                boxHeight: 12,
                                usePointStyle: true,
                                pointStyle: 'circle',
                                color: '#1E293B',
                                font: {
                                    family: "'Plus Jakarta Sans', sans-serif",
                                    weight: 600,
                                    size: 12
                                }
                            }
                        },
                        tooltip: {
                            backgroundColor: '#162D4D',
                            titleColor: '#FFFFFF',
                            bodyColor: '#D6E3FF',
                            padding: 10,
                            cornerRadius: 8,
                            callbacks: {
                                label: function(context) {
                                    return ' ' + context.dataset.label + ': ' + formatRupiah(context.parsed.y);
                                },
                                footer: function(tooltipItems) {
                                    let total = 0;
                                    tooltipItems.forEach(function(tooltipItem) {
                                        total += tooltipItem.parsed.y;
                                    });
                                    return 'Total: ' + formatRupiah(total);
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
                                color: '#64748B',
                                font: {
                                    family: "'Plus Jakarta Sans', sans-serif",
                                    weight: 600,
                                    size: 11
                                }
                            }
                        },
                        y: {
                            stacked: true,
                            beginAtZero: true,
                            grid: {
                                color: '#E2E8F0',
                                strokeDash: [4, 4]
                            },
                            ticks: {
                                color: '#64748B',
                                font: {
                                    family: "'Plus Jakarta Sans', sans-serif",
                                    size: 11
                                },
                                callback: function(value) {
                                    if (value >= 1000000) {
                                        return 'Rp ' + (value / 1000000) + ' jt';
                                    } else if (value >= 1000) {
                                        return 'Rp ' + (value / 1000) + ' rb';
                                    }
                                    return 'Rp ' + value;
                                }
                            }
                        }
                    }
                }
            });
        },

        destroy() {
            if (this.chart) {
                this.chart.destroy();
                this.chart = null;
            }
        }
    };
}
