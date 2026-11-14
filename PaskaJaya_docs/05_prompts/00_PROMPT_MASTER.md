# PROMPT MASTER — Bangun Web PASKA JAYA (satu kali tempel)

> Tempel seluruh isi file ini ke AI coding agent (Claude Code / Cursor / dll.). Lampirkan PDF desain `07_extras/referensi_desain/uiux_paska_jaya.pdf` dan screenshot desain. Jika folder `PaskaJaya_docs` tersedia di repo, agent boleh membaca `03_features/*` untuk detail tambahan.

## Peran
Kamu senior full-stack engineer. Bangun aplikasi web **PASKA JAYA — Toko Bahan Bangunan** (tagline: "Every sack, bar, and delivery in one place.") yang **identik dengan desain PDF/foto yang dilampirkan**, lengkap semua layar dan fungsinya, bukan prototipe statis.

## Stack
Laravel 11 (PHP 8.3) + Inertia.js + React 18 + TypeScript + Tailwind CSS (font Plus Jakarta Sans) + MySQL (SQLite untuk dev) + Recharts + lucide-react + maatwebsite/excel + dompdf + Pest. Uang = integer rupiah. Zona waktu Asia/Jakarta. UI Bahasa Indonesia (Login/Register mengikuti desain, berbahasa Inggris).

## Gaya visual
Sidebar kiri gradien biru (navy→biru), logo kotak putih "PJ" + "PASKA JAYA / TOKO BAHAN BANGUNAN", label menu ("MENU ADMIN/OWNER/KASIR/KARYAWAN"), item aktif pil putih, tombol Keluar di bawah. Topbar gradien biru berisi tanggal ("Sabtu, 14 November 2026"), judul halaman, avatar inisial + nama + role. Konten latar biru sangat muda, kartu putih kaca radius besar, badge pil (Lunas navy ✓, Belum Lunas kuning, Selesai navy ✓, Kritis merah, CASH navy, TRANSFER biru muda). Modal rapi (Batal/Simpan; hapus = "Ya, hapus" merah). Responsif.

## Role
`admin`, `owner`, `kasir`, `karyawan`. Hak akses ditegakkan di server (middleware + policy), bukan hanya menu.

## 1. Auth
- **Login**: split-screen (foto toko "Toko Bangunan Indosatu" + overlay biru + headline "Every sack, bar, and delivery in one place."), kartu "Welcome back": Email or phone number, Password (show/hide), Forgot password?, Login, "Don't have an account? Register". **TIDAK ADA kotak demo accounts** (user wajib register dulu).
- **Register**: tombol ←, "Create Account" ("We'll verify your account with a one-time code."), Full Name, Email, Phone Number, Password, Confirm Password, checkbox Terms & Privacy, tombol Register (disabled sampai valid). OTP 6 digit via flag `AUTH_OTP_ENABLED`. Akun baru berrole `karyawan`.
- Login via email ATAU HP; akun nonaktif ditolak; redirect per role.

## 2. ADMIN
1. **Ringkasan Sistem**: tombol "Kelola Pengguna" (→ Manajemen Pengguna). 4 kartu (Total pengguna + aktif, Master item + stok menipis, Transaksi keluar + data bon, Pengantaran + berjalan). "Pengguna per Role" dengan progress bar. "Status Sistem": Layanan aplikasi Normal, Basis data Terhubung, Pencadangan terakhir (Hari ini 02:00), Catatan kehadiran n data, Catatan bahan harian n data.
2. **Manajemen Pengguna**: cari nama/email; tabel Pengguna (avatar, nama, email, "(Anda)"), Role (dropdown), Dibuat, Status, Aksi (Edit, Nonaktifkan/Aktifkan, Hapus); tombol "+ Tambah Pengguna". Admin tak bisa menonaktifkan/menghapus/ubah role diri sendiri; selalu ≥1 admin aktif.
3. **Tim**: kartu anggota tim pembuat web (inisial, nama, jabatan, tombol edit): Rudi Hartono–Administrator Sistem, Dewi Anggraini–Manajer Operasional, Bagas Pratama–Kepala Gudang, Sri Wahyuni–Staf Keuangan, Yusuf Maulana–Koordinator Pengantaran. CRUD oleh admin.
(+ Profil admin)

## 3. OWNER
1. **Ringkasan Toko**: kartu Penjualan hari ini, Saldo cash (laci) + transfer, Stok menipis, Bon belum lunas (Σ sisa + jumlah pelanggan). **Grafik "Penjualan 7 Hari Terakhir" harus berisi data nyata** (bar chart). Panel Stok Menipis (link Kelola), Bon Belum Lunas (jatuh tempo + sisa), Transaksi Terbaru (link Laporan).
2. **Laporan Barang Keluar**: filter Dari/Sampai tanggal + Metode (Semua/Cash/Transfer); kartu Total penjualan/Cash/Transfer; tabel ID, Waktu, Pelanggan, Metode, Total, tombol Rincian (baris meluas menampilkan item, tombol jadi Tutup); tombol Unduh CSV.
3. **Daftar Bon (hanya lihat)**: filter Semua/Belum lunas/Lunas; kolom Pelanggan (jatuh tempo, badge Lewat tempo), Ringkasan barang (chip), Total (+Sisa), Status Pembayaran, Status Pengantaran (+nama karyawan), Lihat Detail → modal Detail Bon (alamat, tabel barang, Total/Terbayar/Sisa, karyawan, jadwal, catatan, timeline "Alur Status" 5 langkah dengan jam; bila Selesai tampil foto bukti). Aksi tulis → 403.
4. **Master Item** (akses penuh, sama dengan Kasir): 4 kartu (Total Barang, Nilai Stok berdasarkan harga beli, Stok Menipis, Stok Aman); pencarian + dropdown kategori otomatis dari data (kategori baru ikut muncul saat produk baru) + toggle Kartu/Tabel; kartu produk (foto, nama, kode, badge stok/"Kritis", harga jual, ikon hapus); tabel (No, Kode, Nama, Kategori, Satuan, Harga beli, Harga jual, Stok, Tanggal masuk, Aksi pensil/hapus); klik kartu/pensil → modal "Restock / Edit Barang" (kode, nama, kategori, satuan, harga beli/jual, batas stok menipis, "Tambah stok barang baru datang" + "Stok setelah disimpan", foto); "+ Tambah Barang" → modal (kode otomatis BRG-018, nama, kategori, satuan, harga beli/jual, stok awal, batas stok menipis default 10, foto); hapus → dialog "Hapus barang?".
5. **Kehadiran** (hanya lihat): cari, filter karyawan, filter tanggal, tabel Nama, Tanggal, Pekerjaan (chip).
6. **Profil**: Data Akun, Hapus Akun, Data Profil (foto, nama, HP, alamat), Ubah Password.

## 4. KASIR
Menu: Beranda, Barang Keluar, Lihat Saldo, Grafik Mingguan, Riwayat Saldo Laci, Catat Bon, Daftar Bon, Bahan Harian, Kehadiran, Master Item, Profil.
1. **Beranda Kasir**: kartu biru "SALDO LACI HARI INI" (reset otomatis 00:00, tombol Reset Manual, Saldo Cash, Saldo Transfer), Transaksi Terbaru, kartu Transaksi hari ini/Stok menipis/Bon belum lunas, quick link: Barang Keluar→Barang Keluar, Catat Bon→Catat Bon, Bahan Harian→Bahan Harian, Kehadiran→Kehadiran.
2. **Barang Keluar**: (1) pilih barang dari master item + jumlah + Tambah, (2) keranjang (kosong: "Belum ada barang. Pilih barang lalu klik Tambah."), (3) pembayaran: nama pelanggan opsional (default "Umum"), metode CASH/TRANSFER, kotak Total bayar, "Simpan Transaksi". Kanan: Riwayat Barang Keluar. Simpan = transaksi DB: kurangi stok, buat TRX-####, tambah saldo laci.
3. **Lihat Saldo**: kartu saldo + transaksi terbaru + "Riwayat 7 Hari Terakhir" (Tanggal, Cash, Transfer, Total, Direset oleh) + Lihat Semua.
4. **Grafik Mingguan**: dropdown Minggu ini/Minggu lalu/Pilih tanggal; kartu Total penjualan, Hari tertinggi, Dibanding minggu lalu (%); stacked bar Cash+Transfer per hari.
5. **Riwayat Saldo Laci**: filter tanggal, tombol Excel & PDF, tabel (Tanggal, Saldo Cash, Saldo Transfer, Total, Waktu reset, Direset oleh, Detail) → modal "Rincian {tanggal}" (Waktu, Keterangan, Metode, Nominal).
6. **Catat Bon**: Data bon (nama pelanggan, alamat pengantaran opsional, tanggal, jatuh tempo); Tambah barang (dari master item, jumlah, harga satuan otomatis & editable, subtotal); Keranjang bon (tabel editable, hapus); TOTAL BON + Simpan Bon. Status awal Belum Lunas & Belum Dikirim.
7. **Daftar Bon** (penuh): filter Semua/Belum lunas/Lunas; aksi Kirim ke Karyawan (pilih karyawan, jadwal, catatan; stok berkurang), Ganti Karyawan, Batalkan Pengiriman (stok kembali), Tandai Lunas, Bayar Sebagian, Detail (modal seperti owner), Hapus. Status dari karyawan otomatis tampil.
8. **Bahan Harian**: form Tambah bahan (nama, jumlah, harga, tanggal, keterangan; error "Nama bahan, jumlah, dan harga wajib diisi.") + daftar bahan (Tanggal, Bahan, Jumlah, Harga, Keterangan, Aksi edit/hapus) + total pengeluaran.
9. **Kehadiran** (CRUD): "+ Tambah Kehadiran" (modal: nama karyawan, tanggal, pekerjaan banyak dengan "+ Tambah pekerjaan"), pensil → modal Edit, hapus → "Hapus baris kehadiran?".
10. **Master Item**: sama seperti Owner.
11. **Profil**: sama seperti Owner.

## 5. KARYAWAN
Menu: Pengantaran (badge merah = jumlah tugas baru), Profil.
1. **Pengantaran**: kartu Pengantaran baru / Sedang berjalan / Selesai; tab "Akan diantar (n)" & "Selesai (n)"; kartu bon (badge BERIKUTNYA / Baru, status, pelanggan, alamat, DAFTAR BARANG + qty, jadwal, "Catatan kasir", tombol "Detail & Ubah Status →" / "Lihat Detail →"). Hanya bon yang ditugaskan kepadanya. **Detail**: ← Kembali, Informasi bon, Status pengantaran (stepper Sedang Disiapkan → Siap Berangkat/Sedang Diantar → Selesai), **foto bukti wajib** (Pilih / ambil foto) sebelum Selesai, tombol "Ubah Status → …".
2. **Profil**: seperti role lain.

## Alur status bon
Belum Dikirim → Dikirim ke Karyawan → Sedang Disiapkan → Siap Berangkat/Sedang Diantar → Selesai (tiap perubahan tercatat di `bon_status_logs` dengan jam; tampil di timeline Kasir & Owner). Sisa = total − terbayar; Lunas bila terbayar ≥ total; Lewat tempo bila belum lunas dan lewat jatuh tempo. Pembayaran bon masuk saldo laci sesuai metode.

## Data & skema
Tabel: users, otp_codes, categories, items, stock_movements, sales, sale_items, bons, bon_items, bon_payments, bon_status_logs, drawer_entries, drawer_closings, daily_materials, attendances, team_members, settings (detail di `02_architecture/DATABASE_SCHEMA.md`). Seeder demo (hanya local) dari `07_extras/DEMO_DATA.md` (7 user password `password`, 17 item, 9 kategori, transaksi TRX-1006…1012, 6 bon, dll.).

## Aturan kerja
1. Kerjakan berurutan: setup → migrasi/model/seeder → design system & layout → auth → master item & profil → kasir penjualan/saldo → bon → pengantaran → bahan harian/kehadiran → owner → admin → QA.
2. Semua angka dari database; tidak ada data hardcode di UI.
3. Operasi stok/uang dalam DB transaction; tolak stok kurang.
4. Otorisasi server-side untuk semua rute (matriks role di PRD).
5. Tulis test Pest untuk service & akses role; jalankan lint/test sebelum menyatakan selesai.
6. Bila ada ambiguitas, ikuti desain PDF; catat asumsi di akhir.

## Definisi selesai
Semua 33 layar PDF berfungsi dan tampil sesuai desain; checklist E2E di `06_reports/PROGRESS_TRACKER.md` lulus; tidak ada celah akses lintas role.
