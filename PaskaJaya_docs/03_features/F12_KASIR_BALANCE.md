# F12 — Kasir: Saldo Laci, Grafik, Riwayat
## Lihat Saldo
Kartu saldo (sama Beranda) + Reset Manual, kartu Transaksi Terbaru, tabel **Riwayat 7 Hari Terakhir**: TANGGAL, CASH, TRANSFER, TOTAL, DIRESET OLEH (Sistem / nama kasir), link **Lihat Semua** → Riwayat Saldo Laci. Footnote "n transaksi tercatat pada saldo aktif hari ini."
## Grafik Mingguan
"Grafik Penjualan Mingguan — Perbandingan transaksi Cash dan Transfer per hari." Dropdown kanan atas: **Minggu ini / Minggu lalu / Pilih tanggal**.
- 3 kartu: Total penjualan (rentang tanggal), Hari tertinggi (nama hari + Rp), Dibanding minggu lalu (± %, "Penjualan meningkat/menurun").
- **Stacked bar chart** per hari (Sen–Min): segmen Cash (navy) + Transfer (biru muda); legenda; tooltip nominal; sumbu "Nominal dalam Rupiah". Minggu mulai Senin.
## Riwayat Saldo Laci
"Catatan reset otomatis pukul 00:00 dan reset manual." Tombol **Excel** & **PDF** (ekspor sesuai filter). Filter tanggal. Tabel: TANGGAL, SALDO CASH, SALDO TRANSFER, TOTAL, WAKTU RESET, DIRESET OLEH, **Detail**.
- **Detail** modal "Rincian 11 November 2026 — n transaksi · Direset oleh …": WAKTU, KETERANGAN ("Penjualan TRX-1004 - Pak Hendra"), METODE, NOMINAL.
## Reset
- Otomatis 00:00 (`drawer:reset`, direset oleh "Sistem"). Manual: konfirmasi → buat closing hari berjalan, saldo aktif kembali 0, "Direset oleh {Kasir}".
## Acceptance: [ ] angka cocok transaksi [ ] ekspor Excel/PDF [ ] reset auto+manual [ ] grafik 3 rentang.
