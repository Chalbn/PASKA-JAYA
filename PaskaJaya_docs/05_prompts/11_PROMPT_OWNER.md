# PROMPT 11 — Owner (F05, F06)

> **Konteks wajib**: Proyek PASKA JAYA (toko bahan bangunan). Stack Laravel 11 + Inertia + React/TS + Tailwind (lihat `02_architecture/TECH_STACK.md`). Ikuti `01_guides/*`, desain PDF `07_extras/referensi_desain/uiux_paska_jaya.pdf`, dan spesifikasi fitur di `03_features/`. UI berbahasa Indonesia. Uang integer rupiah. Selesai → jalankan test, update `06_reports/PROGRESS_TRACKER.md`, commit.

## Tugas
- **Owner/Dashboard**: 4 kartu, **grafik Penjualan 7 Hari Terakhir berisi data nyata** (bar, Recharts, hari tanpa transaksi = 0), Stok Menipis (+ link Kelola), Bon Belum Lunas, Transaksi Terbaru (+ link Laporan). Semua dari ReportService.
- **Owner/SalesReport**: filter dari/sampai/metode, 3 kartu ringkasan, tabel dengan Rincian expand/Tutup, Unduh CSV.
## Acceptance
Angka dashboard = angka Laporan; CSV sesuai filter; test ReportService.
