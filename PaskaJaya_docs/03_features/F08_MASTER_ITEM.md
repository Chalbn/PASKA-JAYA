# F08 — Master Item (Owner & Kasir, akses penuh)
Judul "Master Item" — "Katalog bahan & barang — restock barang baru datang, edit harga, dan pantau stok." Tombol **+ Tambah Barang**.
- 4 kartu: **Total Barang** (n, "n kategori") · **Nilai Stok** (Σ stok×harga beli, "Berdasarkan harga beli") · **Stok Menipis** (n) · **Stok Aman** (n, "di atas batas minimum").
- Pencarian "Cari bahan/barang atau kode…" + **dropdown kategori** (Semua + kategori otomatis dari data; produk baru berkategori baru ikut muncul — ADR-007) + toggle **Kartu | Tabel**. Info "17 barang · 5 stok menipis" (merah).
- **Kartu**: foto thumbnail, nama, kode kecil, ikon hapus kanan atas, badge stok ("Stok: 142" biru / "Kritis: 4" merah), harga jual kanan. Klik kartu → modal Restock/Edit.
- **Tabel**: NO, KODE BARANG, NAMA BARANG, KATEGORI, SATUAN, HARGA BELI, HARGA JUAL, STOK (badge; "4 · Menipis" merah), TANGGAL MASUK, AKSI (pensil, hapus).
- **Modal Restock / Edit Barang**: Kode barang, Nama barang, Kategori, Satuan, Harga beli (Rp), Harga jual (Rp), Batas stok menipis, kotak biru "Tambah stok barang baru datang (stok saat ini: N)" input angka + teks "Stok setelah disimpan: N+x", Foto barang (opsional, Unggah foto), Batal/Simpan. Menambah stok → stock_movement `restock` + update `last_restocked_at`.
- **Modal Tambah Barang**: kode otomatis (BRG-018, bisa diedit, unik), nama (placeholder "mis. Semen Gresik 50kg"), kategori (combobox, bisa ketik kategori baru), satuan ("Sak, Batang, Dus…"), harga beli/jual, stok awal, batas stok menipis (default 10), foto.
- **Hapus**: dialog "Hapus barang? Barang 'X' akan dihapus dari master item." Batal / **Ya, hapus** (soft delete; transaksi lama tetap aman).
- Validasi: jual ≥ beli (peringatan), angka ≥ 0, kode unik.
## Acceptance: [ ] kartu & tabel [ ] kategori otomatis [ ] restock benar [ ] unggah foto [ ] Owner & Kasir sama.
