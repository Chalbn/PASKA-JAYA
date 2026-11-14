# F06 — Owner: Laporan Barang Keluar
Judul "Laporan Barang Keluar" — "Filter berdasarkan tanggal dan metode pembayaran." Tombol **Unduh CSV**.
- Filter: Dari tanggal, Sampai tanggal (default 7 hari terakhir), Metode pembayaran (Semua/Cash/Transfer).
- 3 kartu ringkasan: Total penjualan (n transaksi), Cash, Transfer — mengikuti filter.
- Tabel: ID (TRX-1012), Waktu, Pelanggan, Metode (badge), Total, tombol **Rincian**.
- **Rincian** = baris meluas di bawah: daftar barang "Cat Tembok White 5kg · 4 kaleng × Rp 135.000 = …" + subtotal kanan; tombol berubah **Tutup**.
- CSV: id, waktu, pelanggan, metode, total (+ item). Nama file `laporan-barang-keluar-YYYYMMDD.csv`.
## Acceptance: [ ] filter [ ] expand rincian [ ] CSV sesuai filter [ ] paginasi.
