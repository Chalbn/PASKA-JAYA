# PASKA JAYA — Master Roadmap

> Metodologi: **Waterfall** (laporan §2.4). Rencana waktu mengikuti laporan Tabel 2.2: pengumpulan data 1 minggu → desain database 2 minggu → implementasi 5 minggu → pengujian 1 minggu → evaluasi 1 minggu (total ±10 minggu).

## Ringkasan Phase
| Phase | Cakupan | Estimasi | Prompt | Status |
|---|---|---|---|---|
| P0 Analisis & Desain | Wawancara mitra, kebutuhan, ERD, UI/UX (PDF), dokumentasi (zip ini) | Minggu 1–3 | – | ✅ Done (dokumentasi) |
| P1 Setup & Database | Setup Laravel+Livewire, 22 migration, model, view/trigger/procedure/privilege | Minggu 4 | 01–04 | ☐ |
| P2 Fondasi UI & Auth | Brand, design system, layout per role, auth, profil | Minggu 5 | 05–07 | ☐ |
| P3 Inventori & Penjualan | Master Item, Barang Keluar, saldo laci, grafik, riwayat | Minggu 6–7 | 08–10 | ☐ |
| P4 Bon & Pengiriman | Catat Bon, Daftar Bon, pembayaran, pengantaran + foto bukti | Minggu 7–8 | 11–12 | ☐ |
| P5 Operasional & Panel | Bahan Harian, Kehadiran, panel Owner & Admin | Minggu 8–9 | 13–15 | ☐ |
| P6 Pengujian & Evaluasi | QA, UAT mitra, perbaikan, deploy, dokumentasi akhir | Minggu 10 | 16 | ☐ |

## Detailed Milestones
### Phase 1: Setup & Database (Hari 1–7)
| Task | PIC |
|---|---|
| Repo GitHub + branch protection | Vascha |
| Setup Laravel 13, Livewire 3, Tailwind, Vite, Pest | Vascha |
| 22 migration + model + enum | Vascha, Patricia |
| View, trigger T1–T8, stored procedure P1–P5, privilege | Vascha |
| Test database (trigger/procedure) | Patricia, Jona |
**Definition of Done**: `php artisan migrate` bersih; `php artisan app:buat-admin` membuat 1 admin; test trigger & procedure hijau; database kosong tanpa dummy.

### Phase 2: Fondasi UI & Auth (Hari 8–14)
| Task | PIC |
|---|---|
| Logo & brand (PROMPT_LOGO), foto toko di Login/Register | Jeli |
| Design system `x-ui.*`, layout per role, menu | Jeli, Chyntia |
| Auth: login (email/HP), register, OTP, lupa password, middleware role | Vascha |
| Profil semua role | Chyntia |
**DoD**: login/register tampil sesuai PDF dengan foto toko baru; redirect per role; 403 lintas role.

### Phase 3: Inventori & Penjualan (Hari 15–28)
| Task | PIC |
|---|---|
| Master Item (kartu/tabel, kategori otomatis, restock, tambah, hapus) | Patricia, Jeli |
| Barang Keluar + Beranda Kasir | Patricia, Chyntia |
| Lihat Saldo, Grafik Mingguan, Riwayat Saldo Laci, reset otomatis/manual, ekspor | Patricia, Chyntia |
**DoD**: penjualan memotong stok & menambah kas laci; reset tercatat; grafik 3 rentang.

### Phase 4: Bon & Pengiriman (Hari 22–35)
| Task | PIC |
|---|---|
| Catat Bon + Daftar Bon Kasir (kirim, ganti, batal, lunas, sebagian, detail, hapus) | Jona, Chyntia |
| Daftar Bon Owner (read-only) | Jona |
| Pengantaran Karyawan + foto bukti privat | Chyntia, Jona |
**DoD**: alur bon → kirim → selesai (foto) → lunas konsisten di Kasir & Owner.

### Phase 5: Operasional & Panel (Hari 36–45)
| Task | PIC |
|---|---|
| Bahan Harian | Chyntia |
| Kehadiran (Kasir CRUD, Owner read-only) | Jona, Chyntia |
| Owner: Ringkasan (grafik 7 hari nyata) + Laporan + CSV | Patricia, Jeli |
| Admin: Ringkasan Sistem, Manajemen Pengguna, Tim, backup | Vascha, Jeli |
**DoD**: semua 33 layar PDF berfungsi.

### Phase 6: Pengujian & Evaluasi (Hari 46–56)
QA checklist E2E, uji hak akses, UAT di toko, perbaikan bug, deploy, finalisasi dokumentasi & laporan. **Pemeliharaan**: evaluasi pasca-implementasi (laporan §2.4 tahap 5).

## Checkpoints (Kriteria Terukur)
| Checkpoint | Kriteria |
|---|---|
| CP1 (akhir P1) | 22 tabel + objek DB lulus test |
| CP2 (akhir P2) | Login semua role, foto toko & logo terpasang |
| CP3 (akhir P3) | Transaksi kasir end-to-end, stok & laci akurat |
| CP4 (akhir P4) | Bon → pengantaran → pelunasan end-to-end |
| CP5 (akhir P5) | Seluruh fitur PDF + empty state |
| CP6 (rilis) | UAT mitra lulus, tidak ada celah akses lintas role |
