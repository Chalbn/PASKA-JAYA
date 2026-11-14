# F13 — Kasir: Catat Bon & Bahan Harian
## Catat Bon
"Catat utang pelanggan dengan banyak barang sekaligus." Alert: "Bon yang dibuat otomatis berstatus Belum Dikirim, dan hanya terlihat oleh Kasir dan Owner. Stok barang berkurang saat bon dikirim."
1. **Data bon**: Nama pelanggan (mis. Pak Hendra), Alamat pengantaran (opsional, "isi jika barang perlu diantar"), Tanggal (default hari ini), Jatuh tempo (date).
2. **Tambah barang**: Barang (select dari master item: "BRG-001 · Semen Gresik 50kg (stok 142)"), Jumlah, Harga satuan (otomatis dari harga jual, **boleh diubah**), Subtotal, **+ Tambah Barang**.
3. **Keranjang bon** (n item): NO, NAMA BARANG (+kode), JUMLAH (editable + satuan), HARGA SATUAN, SUBTOTAL, AKSI (hapus). Footer biru **TOTAL BON Rp 1.300.000** + **Simpan Bon**.
- Simpan: buat bon + items, status Belum Lunas / Belum Dikirim, log status awal. Validasi: ≥1 item, jatuh tempo ≥ tanggal, qty ≤ stok.
## Bahan Harian
"Catat pembelian bahan operasional harian." Kartu kiri **Tambah bahan**: Nama bahan, Jumlah (placeholder "5 gulung" → angka + satuan), Harga (Rp), Tanggal, Keterangan, error merah "Nama bahan, jumlah, dan harga wajib diisi.", tombol **Tambah**.
Kartu kanan **Daftar Bahan Harian** ("Total pengeluaran Rp …"): TANGGAL, BAHAN, JUMLAH, HARGA, KETERANGAN, AKSI (edit, hapus).
## Acceptance: [ ] total bon benar [ ] bon baru tampil di Daftar Bon [ ] CRUD bahan harian [ ] total pengeluaran akurat.
