# PROMPT 08 — Bon: Catat & Daftar Bon Kasir (F13, F07)

> **Konteks wajib**: Proyek PASKA JAYA (toko bahan bangunan). Stack Laravel 11 + Inertia + React/TS + Tailwind (lihat `02_architecture/TECH_STACK.md`). Ikuti `01_guides/*`, desain PDF `07_extras/referensi_desain/uiux_paska_jaya.pdf`, dan spesifikasi fitur di `03_features/`. UI berbahasa Indonesia. Uang integer rupiah. Selesai → jalankan test, update `06_reports/PROGRESS_TRACKER.md`, commit.

## Tugas
1. **Catat Bon** (`Kasir/BonCreate`): Data bon (pelanggan, alamat opsional, tanggal, jatuh tempo), tambah barang (select master item, jumlah, harga satuan default harga jual & editable, subtotal), keranjang tabel editable, footer TOTAL BON + Simpan Bon. Status awal Belum Lunas + Belum Dikirim + log.
2. **Daftar Bon Kasir** (`Kasir/Bons`) sesuai `F07_BON_MANAGEMENT.md`: filter Semua/Belum lunas/Lunas, kolom lengkap (chip barang, Sisa, Lewat tempo, status bayar & antar), aksi: Kirim ke Karyawan (modal karyawan+jadwal+catatan; stok berkurang), Ganti Karyawan, Batalkan Pengiriman (stok kembali), Tandai Lunas, Bayar Sebagian (nominal ≤ sisa; masuk drawer), Detail (modal + Alur Status timeline + foto bukti bila selesai), Hapus.
3. `BonService`: sendToEmployee, changeEmployee, cancelDelivery, pay(partial/full), delete guard; semua dalam transaksi + status log.
## Acceptance
Perhitungan sisa/lewat tempo benar; stok & saldo laci konsisten; tombol disabled sesuai status; test BonService lengkap.
