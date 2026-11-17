# F02 — Admin — Ringkasan Sistem

| Atribut | Isi |
|---|---|
| Role | admin |
| URL | `/admin` |
| Komponen Livewire | `Admin\Dashboard` |
| Tabel/Objek DB | users, barang, penjualan, pengiriman_barang, absensi_karyawan, penggunaan_bahan, system_configs |
| ADR | ADR-007 |
| PIC | Vascha |
| Referensi desain | `07_extras/referensi_desain/uiux_paska_jaya.pdf` (lihat `04_plans/SCREEN_MAPPING.md`) |

Layar: Role Admin → Fitur 1. Menu Admin: Ringkasan Sistem, Manajemen Pengguna, Tim, Keluar.
- Judul "Ringkasan Sistem" — "Gambaran data dan pengguna di seluruh sistem PASKA JAYA." Tombol kanan **Kelola Pengguna** → langsung ke Manajemen Pengguna (F03).
- 4 kartu: **Total pengguna** (n, "x aktif"), **Master item** (n, "x stok menipis"), **Transaksi keluar** (n, "x data bon"), **Pengantaran** (n, "x berjalan").
- Kartu **Pengguna per Role**: Admin / Owner / Kasir / Karyawan → jumlah akun + progress bar proporsional.
- Kartu **Status Sistem**: Layanan aplikasi (badge Normal), Basis data (Terhubung — cek koneksi DB), Pencadangan terakhir (Hari ini, 02:00 dari `settings.last_backup_at`), Catatan kehadiran (n data), Catatan bahan harian (n data).
- Semua angka dari database (SystemStatusService).
## Acceptance
- [ ] Angka sesuai DB. - [ ] Tombol Kelola Pengguna navigasi benar. - [ ] Hanya admin (403 role lain).


## Data & Empty State
Semua data dari database (tanpa dummy). Saat tabel kosong tampilkan empty state, kartu bernilai 0, dan grafik bernilai 0 — tanpa error (ADR-012).
