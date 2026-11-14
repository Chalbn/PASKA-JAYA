# F05 — Owner: Ringkasan Toko
Menu Owner: Ringkasan, Laporan Barang Keluar, Daftar Bon, Master Item, Kehadiran, Profil, Keluar. Judul "Ringkasan Toko" — "Kondisi penjualan, kas, stok, dan piutang hari ini."
- 4 kartu: **Penjualan hari ini** (Rp, n transaksi) · **Saldo cash (laci)** (Rp, "Transfer Rp …") · **Stok menipis** (n barang, "n barang di bawah batas minimum") · **Bon belum lunas** (Σ sisa, "n pelanggan").
- **Penjualan 7 Hari Terakhir** — "Total barang keluar per hari": **bar chart nyata** (Recharts) 7 hari terakhir (label Sab, Min, Sen, …; nilai tooltip Rp). *Catatan PDF: grafik harus ada datanya.* Hari tanpa transaksi = 0.
- **Stok Menipis** (link **Kelola** → Master Item): 5 item terendah, nama + badge "6 Dus", "12 Batang"… (stok + satuan).
- **Bon Belum Lunas**: pelanggan, "Jatuh tempo 9 November 2026", sisa Rp.
- **Transaksi Terbaru** (link **Laporan**): pelanggan, waktu, badge metode, nominal.
## Acceptance: [ ] semua dari DB [ ] grafik tampil [ ] link berfungsi.
