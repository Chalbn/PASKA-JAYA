# PASKA JAYA — Progress Tracker

> **Last updated**: 14 November 2026 · PIC pembaruan: Jona (System Analyst/QA)

## Summary
| Modul | PIC | Progress |
|---|---|---|
| Dokumentasi & desain (P0) | Tim | 100% |
| Setup & Database (P1) | Vascha, Patricia | 50% |
| Fondasi UI & Auth (P2) | Jeli, Vascha, Chyntia | 0% |
| Inventori & Penjualan (P3) | Patricia, Jeli, Chyntia | 0% |
| Bon & Pengiriman (P4) | Jona, Chyntia | 0% |
| Operasional & Panel (P5) | Chyntia, Jona, Patricia, Vascha, Jeli | 0% |
| QA & Rilis (P6) | Jona + semua | 0% |
| **OVERALL** | | **±10%** |

## Phase Checklist
### P1 Setup & Database
- [✅] Setup project (PROMPT_SETUP_PROJECT)
- [✅] 22 migration (PROMPT_MIGRATIONS)
- [ ] View, trigger T1–T8, procedure P1–P5, privilege (PROMPT_DATABASE_ROUTINES)
- [ ] Model, enum, command `app:buat-admin` (PROMPT_MODELS)
### P2 Fondasi UI & Auth
- [ ] Logo & brand + foto toko (PROMPT_LOGO)
- [ ] Design system & layout (PROMPT_FRONTEND)
- [ ] Auth + Profil (PROMPT_AUTH)
### P3 Inventori & Penjualan
- [ ] Master Item (PROMPT_INVENTORY)
- [ ] Beranda Kasir & Barang Keluar (PROMPT_KASIR_PENJUALAN)
- [ ] Saldo, Grafik, Riwayat (PROMPT_KASIR_SALDO)
### P4 Bon & Pengiriman
- [ ] Bon Kasir & Owner (PROMPT_BON)
- [ ] Pengantaran Karyawan (PROMPT_KARYAWAN_PANEL)
### P5 Operasional & Panel
- [ ] Bahan Harian & Kehadiran (PROMPT_OPERASIONAL)
- [ ] Panel Owner (PROMPT_OWNER_PANEL)
- [ ] Panel Admin & Tim (PROMPT_ADMIN_PANEL)
### P6 QA & Rilis
- [ ] QA, UAT, deploy (PROMPT_QA_DEPLOY)

## Checklist E2E (mulai dari database kosong)
0. Semua halaman render tanpa error pada database kosong + empty state
1. `app:buat-admin` → login admin; admin membuat akun Owner, Kasir, Karyawan
2. Register publik → login (tanpa demo account); foto toko tampil di Login & Register
3. Kasir tambah barang (kategori baru) → kategori muncul di filter
4. Barang Keluar cash 2 item → stok turun, saldo cash naik, kode TRX berurutan
5. Stok kurang ditolak tanpa mengubah data
6. Catat Bon → Daftar Bon (Belum Dikirim, stok belum berubah)
7. Kirim ke Karyawan → stok turun; karyawan melihat tugas + badge Baru
8. Karyawan: Sedang Disiapkan → Siap Berangkat → Selesai (foto wajib)
9. Kasir Bayar Sebagian → Tandai Lunas → saldo laci bertambah; status otomatis Lunas
10. Batalkan pengiriman (belum selesai) → stok kembali
11. Owner buka Daftar Bon → detail + timeline + foto; aksi tulis = 403
12. Owner: grafik 7 hari terisi dari transaksi nyata; Laporan filter + CSV
13. Reset laci manual & otomatis tercatat di Riwayat Saldo Laci; ekspor Excel/PDF
14. Kehadiran: Kasir CRUD; Owner read-only
15. Admin ubah role / nonaktifkan user → tidak bisa login; admin terakhir tidak bisa dinonaktifkan
16. activity_logs tercatat dan tidak bisa diubah/hapus
17. Foto bukti pengiriman tidak bisa dibuka tanpa login/izin

## Session Log
### Session 1 — 14 November 2026 (Claude + Kelompok 7) — Dokumentasi
- Dibuat: dokumentasi lengkap (PRD, 01–07), 22 tabel + objek DB, 13 ADR, 16 prompt + master + logo.
- Disesuaikan dengan laporan MSBD (Livewire, 14 entitas) dan contoh struktur dari tim.
- Next: Vascha mulai PROMPT_SETUP_PROJECT.
