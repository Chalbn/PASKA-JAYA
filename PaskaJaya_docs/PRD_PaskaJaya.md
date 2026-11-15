# PRD — PASKA JAYA (Product Requirements Document)

> Sumber: laporan MSBD Kelompok 7 (USU) dan desain UI/UX PDF. Dokumen hidup — perubahan dicatat di `CHANGELOG.md`.

## 1. Ringkasan Eksekutif
**PASKA JAYA** adalah sistem manajemen Toko Bangunan "Paska Jaya" berbasis web: stok barang, kasir (Point of Sale), bon/piutang pelanggan, pengantaran barang oleh karyawan, bahan harian, kehadiran, dan pelaporan real-time dalam satu aplikasi multi-peran (Admin, Owner, Kasir, Karyawan). Tagline: **"Every sack, bar, and delivery in one place."** Tujuan utama: mengganti pencatatan manual berbasis kertas yang menyebabkan selisih stok, kesalahan hitung, piutang tidak teratur, dan pengiriman sulit dipantau.

## 2. Latar Belakang & Masalah
Toko Bangunan Paska Jaya (UMKM penyedia bahan bangunan & material konstruksi) mengelola banyak jenis barang berputar cepat, melayani penjualan tunai dan kredit, tetapi masih memakai buku tulis. Masalah teridentifikasi (laporan §1.2, §2.1):
| # | Masalah | Dampak |
|---|---|---|
| 1 | Pencatatan keuangan & omzet manual | Lama, human error |
| 2 | Stok tidak akurat (stock discrepancy) | Selisih catatan vs fisik gudang |
| 3 | Piutang (bon) tidak teratur | Sulit memantau sisa utang & jatuh tempo |
| 4 | Pengiriman sulit dipantau | Hanya bon fisik dibawa supir; data tercerai-berai |
| 5 | Absensi & pencatatan kerja tidak efektif | Data kehadiran rawan hilang |

## 3. Tujuan
1. Basis data terpusat untuk meminimalkan selisih stok dan menyajikan stok real-time.
2. Modul kasir (POS) berbasis web untuk mempercepat transaksi & mengurangi human error.
3. Pengelolaan piutang + pelacakan pengiriman barang oleh karyawan (dengan foto bukti).
4. Hak akses berbasis peran (Multi-Role Access Control) demi keamanan & akuntabilitas.
5. Dashboard pemilik (omzet, bon, stok kritis) yang dapat dipantau kapan saja.
6. Absensi & pencatatan pekerjaan harian terintegrasi.
**Metrik sukses**: transaksi kasir < 60 detik; angka dashboard = laporan = database; 0 selisih stok pada uji alur; seluruh 33 layar PDF berfungsi; UAT mitra lulus.

## 4. Lingkup Produk
### 4.1 In Scope (MVP)
- F01 Auth (login email/HP, register, OTP, lupa password)
- F02–F04 Admin: Ringkasan Sistem, Manajemen Pengguna, Tim
- F05–F06 Owner: Ringkasan Toko (grafik 7 hari), Laporan Barang Keluar (CSV)
- F07 Daftar Bon (Owner read-only · Kasir penuh) + pengiriman & pembayaran
- F08 Master Item (kartu/tabel, kategori otomatis, restock)
- F09 Kehadiran (Kasir CRUD · Owner read-only)
- F10 Profil semua role
- F11–F13 Kasir: Beranda, Barang Keluar, Saldo Laci, Grafik Mingguan, Riwayat (Excel/PDF), Catat Bon, Bahan Harian
- F14 Karyawan: Pengantaran + foto bukti
- Database: 22 tabel, view, trigger, stored procedure, hak akses MySQL; audit log
### 4.2 Out of Scope (v1)
Payment gateway, multi-cabang, integrasi akuntansi, aplikasi mobile native, notifikasi WhatsApp/SMS, manajemen pemasok & pembelian, pelacakan GPS pengemudi.

## 5. Persona / Role
| Role | Siapa | Tujuan utama |
|---|---|---|
| Admin | Tim pembuat/pengelola sistem | Kelola pengguna, tim, status sistem |
| Owner | Pemilik toko | Pantau omzet, stok kritis, piutang, kehadiran |
| Kasir | Petugas kasir | Transaksi, bon, saldo laci, bahan harian, kehadiran |
| Karyawan | Pengantar/pekerja gudang | Mengantar barang, upload bukti, profil |

## 6. Hak Akses (Matriks)
| Fitur | Admin | Owner | Kasir | Karyawan |
|---|:-:|:-:|:-:|:-:|
| Ringkasan Sistem, Manajemen Pengguna, Tim | ✅ | – | – | – |
| Ringkasan Toko, Laporan Barang Keluar | – | ✅ | – | – |
| Daftar Bon | – | 👁 | ✅ | – |
| Master Item | – | ✅ | ✅ | – |
| Kehadiran | – | 👁 | ✅ | – |
| Barang Keluar, Saldo, Grafik, Riwayat Laci, Catat Bon, Bahan Harian | – | – | ✅ | – |
| Pengantaran (ubah status + foto bukti) | – | – | – | ✅ (miliknya) |
| Profil sendiri | ✅ | ✅ | ✅ | ✅ |
(👁 = hanya lihat; aksi tulis ditolak 403 di server)

## 7. Kebutuhan Fungsional (ringkas) — detail di `03_features/`
### 7.1 Auth
Halaman Login (email atau no HP), Register (Full Name, Email, Phone, Password, Confirm, Terms), OTP (opsional), Lupa password. **Tanpa demo account.** Foto toko pada panel kiri Login & Register memakai foto toko Paska Jaya (`07_extras/aset/foto_toko.jpg`).
### 7.2 Admin
Ringkasan Sistem (4 kartu, pengguna per role, status sistem) · Manajemen Pengguna (CRUD, ubah role, aktif/nonaktif) · Tim (profil tim pembuat web).
### 7.3 Owner
Ringkasan (kartu + grafik 7 hari data nyata + stok menipis + bon belum lunas + transaksi terbaru) · Laporan Barang Keluar (filter, rincian, CSV) · Daftar Bon (detail + timeline + foto bukti) · Master Item · Kehadiran · Profil.
### 7.4 Kasir
Beranda · Barang Keluar · Lihat Saldo · Grafik Mingguan · Riwayat Saldo Laci (detail, Excel, PDF) · Catat Bon · Daftar Bon (kirim, ganti, batal, lunas, sebagian, hapus) · Bahan Harian · Kehadiran · Master Item · Profil.
### 7.5 Karyawan
Pengantaran (daftar + detail + stepper + foto bukti wajib) · Profil.

## 8. Alur Bisnis Utama
1. **Penjualan langsung**: pilih barang → keranjang → Cash/Transfer → simpan → stok turun (trigger) → kas laci naik (trigger).
2. **Bon**: Catat Bon (Belum Lunas, Belum Dikirim) → Kirim ke Karyawan (stok turun) → karyawan: Sedang Disiapkan → Siap Berangkat/Sedang Diantar → Selesai (foto bukti) → pembayaran bertahap/lunas (masuk kas laci).
3. **Laci**: saldo aktif = kas laci; reset otomatis 00:00 / manual; riwayat per penutupan.
4. **Restock**: tambah stok barang baru datang → stok & tanggal masuk diperbarui + mutasi stok.

## 9. Data
- **Tidak ada data dummy.** Database dimulai kosong; seeder hanya 4 role + konfigurasi sistem. Nama/angka pada PDF desain hanya contoh tampilan.
- Admin pertama: `php artisan app:buat-admin`. Data barang, pengguna, tim, transaksi diinput tim lewat aplikasi.
- Skema lengkap: `02_architecture/DATABASE_SCHEMA.md` (14 entitas laporan + 8 pendukung).

## 10. Kebutuhan Non-Fungsional
| Aspek | Ketentuan |
|---|---|
| Keamanan | Hash password, rate limit, CSRF, validasi server, bukti pengiriman privat, audit log append-only, privilege MySQL berlapis, HTTPS |
| Integritas data | Trigger (stok ≥ 0, status bon otomatis, kas laci), stored procedure transaksi, CHECK & UNIQUE constraint |
| Performa | Indeks sesuai skema; agregasi via view; paginasi; eager loading |
| Ketersediaan | Backup otomatis 02:00; restore teruji |
| Usability | Antarmuka sederhana (user-friendly), responsif (HP untuk karyawan), empty state |
| Aksesibilitas & i18n | Locale `id`, zona Asia/Jakarta, kontras AA |
| Pengujian | Pest: unit, feature, database; coverage Service ≥ 80% |
| Kode | PSR-12 (Pint), Conventional Commits, PR ke `develop` |
| Biaya | Rp 0 (open source; sesuai laporan Tabel 2.1) |

## 11. Terminologi Kunci (lihat `GLOSSARY.md`)
Barang Keluar = penjualan langsung · Bon = penjualan kredit + piutang · Sisa = tagihan − terbayar · Saldo Laci = kas laci aktif · Stok menipis = stok ≤ stok_minimum · Alur Status = timeline pengiriman.

## 12. Status & Roadmap
| Phase | Cakupan | Status |
|---|---|---|
| P0 Analisis & Desain | Wawancara, ERD, UI/UX, dokumentasi | ✅ Done |
| P1 Setup & Database | Laravel+Livewire, 22 migration, view/trigger/procedure | ☐ |
| P2 Fondasi UI & Auth | Logo, design system, auth, profil | ☐ |
| P3 Inventori & Penjualan | Master Item, Barang Keluar, laci | ☐ |
| P4 Bon & Pengiriman | Bon, pengantaran | ☐ |
| P5 Operasional & Panel | Bahan harian, kehadiran, Owner, Admin | ☐ |
| P6 Pengujian & Evaluasi | QA, UAT, deploy | ☐ |
Tracking aktual: `06_reports/PROGRESS_TRACKER.md`. Rencana waktu mengikuti laporan Tabel 2.2 (±10 minggu, metode Waterfall).

## 13. Risiko & Asumsi
- **Register terbuka**: akun baru berrole Karyawan; Admin meninjau di Manajemen Pengguna (opsi lanjutan: persetujuan admin).
- **Stok awal akurat**: butuh stock opname sebelum go-live agar laporan benar.
- **Logika terbagi PHP & DB**: wajib test database; perubahan trigger = migration baru (ADR-007).
- **Bon tanpa pengantaran**: v1 menganggap semua bon melalui alur kirim; bon ambil-sendiri dapat ditambahkan sebagai ADR berikutnya.
- **Laporan Barang Keluar** hanya menampilkan penjualan langsung; bon dipantau di Daftar Bon (ADR-005).
- **Foto toko resolusi rendah** (973×547) — ganti dengan foto asli ≥ 1920 px.
- **Kinerja tim mahasiswa**: jadwal mengikuti kalender kuliah; sprint 1 minggu.

*PRD ini merangkum dokumentasi; detail teknis ada di `02_architecture/` dan `03_features/`.*
