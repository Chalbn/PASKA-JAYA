# PASKA JAYA — Task Distribution

> Pembagian tugas per anggota Kelompok 7 (Prodi Teknologi Informasi, USU — Dosen: Dedy Arisandi, S.T., M.Kom.). Disesuaikan dengan web PASKA JAYA (4 role).
> ⚠️ Dokumen ini memuat nomor kontak anggota — simpan repository sebagai **private**.

## Summary Per Anggota

| Anggota | NIM | Kontak | Role | Area Lead | Collaborator Role |
|---|---|---|---|---|---|
| **Vascha U. Lumbantoruan** | 251402125 | 0878-5625-1735 | Backend & DB Engineer (Tech Lead) | Setup, database (migration, trigger, procedure, privilege), Auth, Admin panel, review & deploy | Review PR semua anggota |
| **Patricia Putri Josephine Situmeang** | 251402119 | 0821-6336-7083 | Backend & DB Engineer | Master Item, Barang Keluar, saldo laci, grafik, riwayat, laporan Owner, ekspor | Model & migration bersama Vascha |
| **Jona Vebrian Gultom** | 251402084 | 0823-7298-8845 | System Analyst, QA & Frontend | Bon (Catat/Daftar, Kasir & Owner), Kehadiran, test plan, UAT, tracker, laporan akhir | Logika pengiriman bersama Chyntia |
| **Jeli Alvonzo Laia** | 251402101 | 0821-6497-9728 | UI/UX Designer & Frontend | Logo & brand, design system, layout per role, Login/Register (foto toko), Tim, UI Owner & Master Item, QA tampilan vs PDF | UI semua panel |
| **Chyntia R. A. Hutabarat** | 251402022 | 0813-7748-9793 | Frontend Developer | UI Kasir, Bahan Harian, Pengantaran Karyawan, Profil, UI Pengguna | UI Bon & Kehadiran |

> **Aset desain**: `07_extras/referensi_desain/uiux_paska_jaya.pdf` (33 layar). Pemetaan: `04_plans/SCREEN_MAPPING.md`.
> **Urutan pengerjaan & prompt**: `04_plans/URUTAN_KERJA.md`, `05_prompts/`.
> **Data**: database kosong, tanpa dummy (ADR-012). Lihat `SETUP_GUIDE.md` Step 9.

## Konvensi "Lead vs Collaborator"
- **Lead** = pemilik fitur (logika Livewire/Service/DB). Memastikan spec `03_features/` terpenuhi dan membuat PR utama.
- **Collaborator (UI)** = membuat Blade/Tailwind & interaksi tampilan atas permintaan Lead, memakai komponen `x-ui.*` dari Jeli. Bukan pemilik fitur.
- Satu branch = satu pemilik (lihat `GIT_WORKFLOW.md`).

## Detail Per Anggota

### 1. Vascha U. Lumbantoruan (Backend & DB Engineer — Tech Lead)
```
Branch: feat/setup-project, feat/database-schema, feat/database-routines, feat/auth, feat/admin-panel
✅ Init Laravel 13 + Livewire 3 + Tailwind + Vite + Pest; repo GitHub private + branch protection
✅ Migration 22 tabel sesuai DATABASE_SCHEMA.md (bersama Patricia)
✅ View v_stok_menipis, v_piutang_aktif, v_penjualan_harian
☐ Trigger T1–T8 + stored procedure P1–P5 (migration DB::unprepared) + test database
☐ Skrip privilege MySQL (pj_app, pj_migrator, pj_report, pj_backup)
☐ Command app:buat-admin, laci:reset, backup:run + scheduler
✅ Auth: login (email/HP), register, OTP, lupa password, middleware role & aktif
☐ AuditObserver → activity_logs
☐ Admin: Ringkasan Sistem (status sistem, pengguna per role)
☐ Admin: Manajemen Pengguna (CRUD, ubah role, aktif/nonaktif, proteksi admin terakhir)
☐ Code review semua PR (khusus perubahan DB)
☐ Release/deploy (HTTPS, APP_DEBUG=false, backup teruji)
```

### 2. Patricia Putri Josephine Situmeang (Backend & DB Engineer)
```
Branch: feat/master-item, feat/kasir-sales, feat/kasir-balance, feat/owner-dashboard, feat/owner-sales-report
☐ Model & relasi (bersama Vascha): Barang, KategoriBarang, Penjualan, DetailPenjualan, KasLaci, PenutupanLaci
☐ Master Item: kartu & tabel, 4 kartu statistik, pencarian, kategori otomatis
☐ Tambah Barang (kode BRG-### otomatis, foto), Restock/Edit (sp_restock_barang), Hapus (soft delete)
☐ Beranda Kasir (saldo laci, transaksi terbaru, 3 kartu, quick link)
☐ Barang Keluar: pilih barang → keranjang → Cash/Transfer → simpan (PenjualanService)
☐ Riwayat Barang Keluar (panel kanan)
☐ Lihat Saldo + Reset Manual (LaciService, sp_reset_laci)
☐ Grafik Mingguan (Minggu ini/lalu/pilih tanggal; Cash + Transfer; % vs minggu lalu)
☐ Riwayat Saldo Laci + modal Detail + ekspor Excel & PDF
☐ Owner: Ringkasan Toko (4 kartu, grafik 7 hari DATA NYATA, stok menipis, bon belum lunas, transaksi terbaru)
☐ Owner: Laporan Barang Keluar (filter, rincian expand, Unduh CSV; sp_laporan_penjualan)
Collaborator: Jeli (UI Owner & Master Item), Chyntia (UI Kasir)
```

### 3. Jona Vebrian Gultom (System Analyst, QA & Frontend)
```
Branch: feat/bon-kasir, feat/bon-owner-readonly, feat/attendance, feat/qa-polish
☐ Model & relasi: BonPiutang, PembayaranBon, PengirimanBarang, LogStatusPengiriman, Pelanggan, AbsensiKaryawan
☐ Catat Bon: data bon, tambah barang (harga editable), keranjang editable, total, simpan
☐ Daftar Bon Kasir: filter Semua/Belum lunas/Lunas, kolom lengkap, Lewat tempo
☐ Aksi: Kirim ke Karyawan (sp_kirim_bon), Ganti Karyawan, Batalkan Pengiriman, Tandai Lunas, Bayar Sebagian, Hapus
☐ Modal Detail Bon + timeline Alur Status + foto bukti (privat)
☐ Daftar Bon Owner (read-only, aksi tulis = 403)
☐ Kehadiran: Kasir CRUD (modal tambah/edit/hapus, pekerjaan banyak) & Owner read-only
☐ Penyusun test plan, matriks hak akses, skrip UAT mitra
☐ Update PROGRESS_TRACKER.md tiap sprint; laporan akhir MSBD
☐ QA: checklist E2E, uji database kosong, uji lintas role
Collaborator: Chyntia (UI Bon & Kehadiran), Chyntia (logika pengiriman)
```

### 4. Jeli Alvonzo Laia (UI/UX Designer & Frontend)
```
Branch: feat/design-system-layout, feat/admin-team
✅ Logo (PROMPT_LOGO): logo.svg, logo-mark.svg, favicon; letakkan di public/images
✅ Foto toko (07_extras/aset/foto_toko.jpg) → public/images/foto-toko.jpg; dipakai di Login & Register
✅ Design system x-ui.*: button, card, stat-card, badge, modal, confirm, input, select/combobox, table, pill-filter, toggle, timeline, empty-state, upload, toast
✅ Layout app per role (sidebar gradien + topbar) + menu per role + drawer mobile
✅ Layout auth split-screen + halaman Login & Register (TANPA demo account)
☐ Admin — Tim (CRUD kartu anggota, inisial otomatis)
☐ UI Owner (Ringkasan, Laporan) & UI Master Item (kartu/tabel/modal)
✅ Grafik Chart.js helper (bar & stacked bar)
☐ QA tampilan semua layar vs PDF (SCREEN_MAPPING.md)
Collaborator: semua anggota (komponen UI bersama)
```

### 5. Chyntia R. A. Hutabarat (Frontend Developer)
```
Branch: feat/profile, feat/daily-materials, feat/delivery-karyawan
✅ Profil Saya (semua role): data akun, foto, ubah password, hapus akun
☐ Bahan Harian: form tambah (validasi), daftar + total pengeluaran, edit/hapus
☐ Pengantaran Karyawan: daftar (3 kartu, tab, badge BERIKUTNYA/Baru), detail, stepper status
☐ Foto bukti wajib (kamera/galeri) + route berkas privat
☐ Badge merah jumlah tugas baru di menu
☐ UI Kasir: Beranda, Barang Keluar, Lihat Saldo, Grafik, Riwayat Saldo Laci
☐ UI Bon (Catat Bon, Daftar Bon) & Kehadiran
☐ UI Manajemen Pengguna
☐ Responsif mobile halaman Karyawan
Collaborator: Patricia (UI Kasir), Jona (UI Bon & Kehadiran), Vascha (UI Pengguna)
```

## Alokasi Tugas per Fase
```
Fase 1 — Setup & Database (Vascha + Patricia)
☐ Setup, migration, model, view/trigger/procedure/privilege            (urutan 1–4)
Fase 2 — Fondasi UI & Auth (Jeli + Vascha + Chyntia)
✅ Logo, design system, layout, login/register, profil                   (urutan 5–7)
Fase 3 — Inventori & Penjualan (Patricia + Jeli + Chyntia)
☐ Master Item, Barang Keluar, saldo laci, grafik, riwayat               (urutan 8–10)
Fase 4 — Bon & Pengiriman (Jona + Chyntia)
☐ Catat/Daftar Bon, pengantaran + foto bukti                            (urutan 11–12)
Fase 5 — Operasional & Panel (Chyntia + Jona + Patricia + Vascha + Jeli)
☐ Bahan Harian, Kehadiran, Owner, Admin, Tim                            (urutan 13–15)
Fase 6 — QA & Rilis (Jona + semua)
☐ Checklist E2E, UAT mitra, deploy, laporan akhir                       (urutan 16)
```
> Jangan mulai fase berikutnya sebelum acceptance fase sebelumnya hijau dan `06_reports/PROGRESS_TRACKER.md` diperbarui.

## Task Status Legend
☐ belum · 🔄 berjalan · ✅ selesai · ⛔ blocked

## Aturan Kolaborasi
1. Sebelum mulai task baru → `git pull origin develop`.
2. Setelah selesai → push, buat PR, minta review.
3. Jika blocked → tulis di PROGRESS_TRACKER dan kabari Tech Lead.
4. Perlu mengubah file orang lain → koordinasi dulu di grup.
5. Perubahan struktur DB → hanya lewat migration baru + review Vascha/Patricia.


---
*Pembagian tugas bisa berubah sesuai progress. Last updated: 14 November 2026.*
