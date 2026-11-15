# PASKA JAYA — Screen Mapping (PDF → Rute → Komponen → Fitur)

> Sumber: `07_extras/referensi_desain/uiux_paska_jaya.pdf`. Pemetaan tetap seperti versi sebelumnya; kolom komponen disesuaikan ke Livewire.

| Layar PDF | Rute | Komponen Livewire | Fitur |
|---|---|---|---|
| Login | /login | Auth\Login | F01 |
| Register | /register | Auth\Register | F01 |
| Admin 1 Ringkasan Sistem | /admin | Admin\Dashboard | F02 |
| Admin 2 Manajemen Pengguna | /admin/pengguna | Admin\Pengguna\Index | F03 |
| Admin 3 Tim | /admin/tim | Admin\Tim\Index | F04 |
| Owner 1 Ringkasan | /owner | Owner\Dashboard | F05 |
| Owner 2 Laporan (+rincian) | /owner/laporan-barang-keluar | Owner\LaporanBarangKeluar | F06 |
| Owner 3 Daftar Bon (semua/belum lunas/lunas + detail) | /owner/daftar-bon | Owner\DaftarBon | F07 |
| Owner 4 Master Item (kartu/tabel/edit/tambah/filter/hapus) | /owner/master-item | Shared\MasterItem\Index | F08 |
| Owner 5 Kehadiran | /owner/kehadiran | Owner\Kehadiran | F09 |
| Owner 6 Profil | /owner/profil | Shared\Profil | F10 |
| Kasir 1 Beranda | /kasir | Kasir\Beranda | F11 |
| Kasir 2 Barang Keluar | /kasir/barang-keluar | Kasir\BarangKeluar | F11 |
| Kasir 3 Lihat Saldo | /kasir/saldo | Kasir\LihatSaldo | F12 |
| Kasir 4 Grafik Mingguan | /kasir/grafik-mingguan | Kasir\GrafikMingguan | F12 |
| Kasir 5 Riwayat Saldo Laci (+detail) | /kasir/riwayat-saldo | Kasir\RiwayatSaldoLaci | F12 |
| Kasir 6 Catat Bon | /kasir/catat-bon | Kasir\CatatBon | F13 |
| Kasir 7 Daftar Bon (+detail, 3 filter) | /kasir/daftar-bon | Kasir\DaftarBon | F07 |
| Kasir 8 Bahan Harian | /kasir/bahan-harian | Kasir\BahanHarian | F13 |
| Kasir 9 Kehadiran (+edit/tambah/hapus) | /kasir/kehadiran | Kasir\Kehadiran | F09 |
| Kasir 10 Master Item | /kasir/master-item | Shared\MasterItem\Index | F08 |
| Kasir 11 Profil | /kasir/profil | Shared\Profil | F10 |
| Karyawan 1 Pengantaran (+detail) | /karyawan/pengantaran | Karyawan\Pengantaran\Index, Detail | F14 |
| Karyawan 2 Profil | /karyawan/profil | Shared\Profil | F10 |

## Catatan dari PDF yang berlaku
- Login: **tanpa demo account**; foto toko diganti `foto-toko.jpg` (Login & Register).
- Ringkasan Sistem → "Kelola Pengguna" langsung ke Manajemen Pengguna.
- Owner Ringkasan: grafik 7 hari harus menampilkan data nyata.
- Master Item: filter kategori otomatis; pilihan barang di Catat Bon dari Master Item.
- Beranda Kasir: Barang Keluar → Barang Keluar, Catat Bon → Catat Bon, Bahan Harian → Bahan Harian, Kehadiran → Kehadiran.
- Tim = profil tim pembuat web (admin).
