# PROMPT 07 — Kasir: Beranda, Barang Keluar, Saldo, Grafik, Riwayat (F11, F12)

> **Konteks wajib**: Proyek PASKA JAYA (toko bahan bangunan). Stack Laravel 11 + Inertia + React/TS + Tailwind (lihat `02_architecture/TECH_STACK.md`). Ikuti `01_guides/*`, desain PDF `07_extras/referensi_desain/uiux_paska_jaya.pdf`, dan spesifikasi fitur di `03_features/`. UI berbahasa Indonesia. Uang integer rupiah. Selesai → jalankan test, update `06_reports/PROGRESS_TRACKER.md`, commit.

## Tugas
Implementasikan `F11_KASIR_SALES.md` dan `F12_KASIR_BALANCE.md`:
- **Beranda Kasir** (kartu saldo laci + Reset Manual, transaksi terbaru, 3 kartu, 4 quick link: Barang Keluar→/kasir/barang-keluar, Catat Bon→/kasir/catat-bon, Bahan Harian→/kasir/bahan-harian, Kehadiran→/kasir/kehadiran).
- **Barang Keluar**: 3 langkah (pilih barang → keranjang → pembayaran Cash/Transfer) + riwayat kanan. `SaleService::create` dalam DB transaction: sale, sale_items snapshot, kurangi stok, drawer_entry, kode TRX berurutan.
- **Lihat Saldo**, **Grafik Mingguan** (Recharts stacked bar, dropdown Minggu ini/Minggu lalu/Pilih tanggal, 3 kartu ringkasan, % vs minggu lalu), **Riwayat Saldo Laci** (filter tanggal, tabel, modal Detail, ekspor Excel & PDF).
- `DrawerService`: `currentBalance()`, `reset(?User)`; command `drawer:reset` dijadwalkan 00:00 (direset oleh "Sistem"); reset manual oleh kasir.
## Acceptance
Penjualan menurunkan stok & menaikkan saldo sesuai metode; reset membuat riwayat; grafik minggu lalu/ini benar; ekspor menghasilkan file; test SaleService & DrawerService.
