import { paskaChartBar, paskaChartStackedBar, formatRupiah } from './chart-helper';

// Daftarkan ke window agar dapat dipanggil langsung dari x-data Alpine.js
window.paskaChartBar = paskaChartBar;
window.paskaChartStackedBar = paskaChartStackedBar;
window.formatRupiah = formatRupiah;

// Daftarkan juga ke Alpine jika event alpine:init terpanggil
document.addEventListener('alpine:init', () => {
    if (window.Alpine) {
        window.Alpine.data('chartBar', paskaChartBar);
        window.Alpine.data('chartStackedBar', paskaChartStackedBar);
    }
});
