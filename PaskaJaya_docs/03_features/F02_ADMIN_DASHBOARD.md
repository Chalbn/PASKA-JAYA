# F02 — Admin: Ringkasan Sistem
Layar: Role Admin → Fitur 1. Menu Admin: Ringkasan Sistem, Manajemen Pengguna, Tim, Keluar.
- Judul "Ringkasan Sistem" — "Gambaran data dan pengguna di seluruh sistem PASKA JAYA." Tombol kanan **Kelola Pengguna** → langsung ke Manajemen Pengguna (F03).
- 4 kartu: **Total pengguna** (n, "x aktif"), **Master item** (n, "x stok menipis"), **Transaksi keluar** (n, "x data bon"), **Pengantaran** (n, "x berjalan").
- Kartu **Pengguna per Role**: Admin / Owner / Kasir / Karyawan → jumlah akun + progress bar proporsional.
- Kartu **Status Sistem**: Layanan aplikasi (badge Normal), Basis data (Terhubung — cek koneksi DB), Pencadangan terakhir (Hari ini, 02:00 dari `settings.last_backup_at`), Catatan kehadiran (n data), Catatan bahan harian (n data).
- Semua angka dari database (SystemStatusService).
## Acceptance
- [ ] Angka sesuai DB. - [ ] Tombol Kelola Pengguna navigasi benar. - [ ] Hanya admin (403 role lain).
