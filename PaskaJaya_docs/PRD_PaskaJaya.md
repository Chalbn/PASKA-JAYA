# PRD — PASKA JAYA (Toko Bahan Bangunan)

## 1. Latar belakang
Toko bangunan "Indosatu / PASKA JAYA" membutuhkan satu sistem untuk mencatat penjualan tunai/transfer, utang pelanggan (bon) beserta pengantarannya, stok bahan, pengeluaran bahan harian, dan kehadiran karyawan. Tagline: **"Every sack, bar, and delivery in one place."**

## 2. Tujuan
- Kasir mencatat penjualan & bon cepat, stok berkurang otomatis.
- Owner memantau penjualan, stok menipis, bon belum lunas, kehadiran (read-only).
- Karyawan mengantar barang dan mengunggah foto bukti.
- Admin mengelola pengguna dan melihat status sistem.

## 3. Role & hak akses
| Fitur | Admin | Owner | Kasir | Karyawan |
|---|:-:|:-:|:-:|:-:|
| Ringkasan Sistem, Manajemen Pengguna, Tim | ✅ | – | – | – |
| Ringkasan Toko, Laporan Barang Keluar | – | ✅ | – | – |
| Daftar Bon | – | 👁 hanya lihat | ✅ penuh | – |
| Master Item | – | ✅ penuh | ✅ penuh | – |
| Kehadiran | – | 👁 hanya lihat | ✅ CRUD | – |
| Barang Keluar, Saldo Laci, Grafik, Riwayat Laci, Catat Bon, Bahan Harian | – | – | ✅ | – |
| Pengantaran (ubah status + foto bukti) | – | – | – | ✅ |
| Profil sendiri | ✅ | ✅ | ✅ | ✅ |

## 4. Ruang lingkup (33 layar di PDF)
- **Auth**: Login, Register (tanpa akun demo — user wajib register dulu).
- **Admin (3 fitur)**: Ringkasan Sistem, Manajemen Pengguna, Tim.
- **Owner (6)**: Ringkasan (grafik 7 hari), Laporan Barang Keluar, Daftar Bon, Master Item, Kehadiran, Profil.
- **Kasir (11)**: Beranda, Barang Keluar, Lihat Saldo, Grafik Mingguan, Riwayat Saldo Laci, Catat Bon, Daftar Bon, Bahan Harian, Kehadiran, Master Item, Profil.
- **Karyawan (2)**: Pengantaran (+ detail & ubah status), Profil.

## 5. Di luar ruang lingkup (v1)
Payment gateway, multi-cabang, integrasi akuntansi, aplikasi mobile native, notifikasi WhatsApp.

## 6. Catatan keputusan dari PDF
- Demo account **ditiadakan** di halaman login.
- Klik "Kelola Pengguna" (Ringkasan Sistem) → langsung ke Manajemen Pengguna.
- Grafik "Penjualan 7 Hari Terakhir" Owner **harus berisi data nyata** (di desain kosong).
- Filter kategori Master Item **dibuat otomatis** dari kategori produk; bertambah saat produk baru dengan kategori baru dibuat.
- Pilihan barang di Catat Bon / Barang Keluar = isi Master Item.
- Quick link Beranda Kasir: Barang Keluar→Barang Keluar, Catat Bon→Catat Bon, Bahan Harian→Bahan Harian, Kehadiran→Kehadiran.
- Halaman Tim = profil tim pembuat web (dikelola admin).

## 6b. Asumsi (bisa diubah)
1. Akun hasil Register berperan `karyawan`, aktif; Admin mengubah role.
2. OTP verifikasi (sesuai teks desain Register) dikontrol `AUTH_OTP_ENABLED` (off di local).
3. Stok berkurang saat Barang Keluar disimpan, dan untuk Bon saat **Kirim ke Karyawan**; dibatalkan bila pengiriman dibatalkan.
4. Pembayaran bon (Tandai Lunas / Bayar Sebagian) masuk ke saldo laci hari itu sesuai metode.
5. Saldo laci direset otomatis 00:00 (scheduler) atau manual oleh Kasir; keduanya tercatat di Riwayat.

## 7. Metrik sukses
Transaksi kasir < 60 detik; semua angka dashboard cocok dengan laporan; 0 selisih stok pada uji alur; semua layar PDF terimplementasi.
