# F11 — Kasir: Beranda & Barang Keluar
## Beranda Kasir
Menu Kasir: Beranda, Barang Keluar, Lihat Saldo, Grafik Mingguan, Riwayat Saldo Laci, Catat Bon, Daftar Bon, Bahan Harian, Kehadiran, Master Item, Profil, Keluar.
- "Beranda Kasir" — "Ringkasan shift dan akses cepat."
- Kartu biru besar **SALDO LACI HARI INI** Rp (cash+transfer), teks "Reset otomatis setiap hari 00:00", tombol **Reset Manual**, sub-kartu SALDO CASH & SALDO TRANSFER.
- **Transaksi Terbaru** (sumber pemasukan saldo hari ini): "Penjualan TRX-1012 · Bu Lestari 10:35" + badge metode + nominal.
- 3 kartu: Transaksi hari ini (n, Rp), Stok menipis (n barang), Bon belum lunas (n bon, Σ sisa).
- 4 quick link: **Barang Keluar** → F11; **Catat Bon** → F13; **Bahan Harian** → F13; **Kehadiran** → F09.
## Barang Keluar
"Catat penjualan. Harga dan total terisi otomatis, stok berkurang setelah disimpan."
- Kiri: **1. Pilih barang** (select "— Pilih dari master item —", Jumlah, **+ Tambah**) → **2. Keranjang** (kosong: "Belum ada barang. Pilih barang lalu klik Tambah."; baris: nama, qty editable, harga, subtotal, hapus) → **3. Pembayaran** (Nama pelanggan opsional default "Umum", Metode **CASH | TRANSFER**, kotak "Total bayar Rp 0", **Simpan Transaksi**).
- Kanan: **Riwayat Barang Keluar** (n transaksi): kartu TRX-xxxx, badge metode, pelanggan, ringkasan item, waktu, total.
- Simpan = DB transaction: buat sale + items, kurangi stok, drawer_entry. Gagal bila stok kurang.
## Acceptance: [ ] stok berkurang [ ] saldo laci naik sesuai metode [ ] kode TRX berurutan [ ] validasi stok.
