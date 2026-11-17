# F06 — Owner — Laporan Barang Keluar

| Atribut | Isi |
|---|---|
| Role | owner |
| URL | `/owner/laporan-barang-keluar` |
| Komponen Livewire | `Owner\LaporanBarangKeluar` |
| Tabel/Objek DB | penjualan, detail_penjualan (sp_laporan_penjualan) |
| ADR | ADR-005 |
| PIC | Jeli |
| Referensi desain | `07_extras/referensi_desain/uiux_paska_jaya.pdf` (lihat `04_plans/SCREEN_MAPPING.md`) |

Judul "Laporan Barang Keluar" — "Filter berdasarkan tanggal dan metode pembayaran." Tombol **Unduh CSV**.
- Filter: Dari tanggal, Sampai tanggal (default 7 hari terakhir), Metode pembayaran (Semua/Cash/Transfer).
- 3 kartu ringkasan: Total penjualan (n transaksi), Cash, Transfer — mengikuti filter.
- Tabel: ID (TRX-1012), Waktu, Pelanggan, Metode (badge), Total, tombol **Rincian**.
- **Rincian** = baris meluas di bawah: daftar barang "Cat Tembok White 5kg · 4 kaleng × Rp 135.000 = …" + subtotal kanan; tombol berubah **Tutup**.
- CSV: id, waktu, pelanggan, metode, total (+ item). Nama file `laporan-barang-keluar-YYYYMMDD.csv`.
## Acceptance: [ ] filter [ ] expand rincian [ ] CSV sesuai filter [ ] paginasi.


## Data & Empty State
Semua data dari database (tanpa dummy). Saat tabel kosong tampilkan empty state, kartu bernilai 0, dan grafik bernilai 0 — tanpa error (ADR-012).
