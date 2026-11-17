# PASKA JAYA — Agent Guidelines

> Panduan wajib untuk AI coding agent (Claude Code, Cursor, Copilot, dll.) dan anggota tim. Baca sebelum menulis kode.

## 1. Identitas Proyek
| Item | Isi |
|---|---|
| Nama | PASKA JAYA — Sistem Manajemen Toko Bahan Bangunan |
| Tagline | "Every sack, bar, and delivery in one place." |
| Tim | Kelompok 7 (USU, Prodi Teknologi Informasi) |
| Stack | Laravel 13 + Livewire 3 + Tailwind + MySQL 8 (Laragon) |
| Role | admin · owner · kasir · karyawan |
| Bahasa UI | Indonesia (Login/Register mengikuti desain berbahasa Inggris) |

## 2. Dokumen Wajib Dibaca (urut)
1. `PRD_PASKAJAYA.md` → 2. `02_architecture/DATABASE_SCHEMA.md` + `ADR/` → 3. `03_features/Fxx` yang dikerjakan → 4. `07_extras/referensi_desain/uiux_paska_jaya.pdf` → 5. `01_guides/*` → 6. `05_prompts/PROMPT_*.md` yang relevan.

## 3. Prinsip Utama
- **Desain PDF = kebenaran tampilan.** Teks, urutan, badge, warna mengikuti PDF.
- **Database dimulai kosong, tanpa dummy** (ADR-012). Data PDF hanyalah contoh tampilan.
- **Laporan MSBD = kebenaran database.** Nama tabel/kolom ikuti `DATABASE_SCHEMA.md`.
- Semua angka dashboard/laporan dari database (view, query agregasi), bukan hardcode.

## 4. Konvensi Teknis
### 4.1 Laravel
- Eloquent + relasi; hindari raw query kecuali memanggil stored procedure/view.
- Controller hanya untuk logout/unduhan berkas; halaman = komponen Livewire.
- Validasi lewat Livewire Form Object / `rules()`; otorisasi via Policy + middleware `role`.
- Model memakai `$table` eksplisit untuk tabel berbahasa Indonesia.
### 4.2 Livewire
- Satu halaman = satu komponen full-page di `app/Livewire/<Role>/`.
- State publik minimal; data besar via `#[Computed]`; gunakan `wire:model.live.debounce.300ms` untuk pencarian.
- Aksi kritis (simpan transaksi, kirim bon, bayar, hapus) selalu lewat `Service` dan `try/catch` → tampilkan pesan error dari trigger/procedure.
- Event UI: `$this->dispatch('toast', ...)`; modal dengan Alpine + `wire:model`.
### 4.3 Blade/Tailwind
- Hanya Tailwind + komponen Blade di `resources/views/components/ui`. Dilarang CSS custom ad-hoc.
- Warna dari token (lihat UI_UX_GUIDELINES). Format uang `Rp 1.820.000` lewat helper `rupiah()`.
### 4.4 Security Rules
- JANGAN percayakan menu tersembunyi sebagai otorisasi; cek di server.
- SELALU `auth` + `aktif` + `role:` pada grup route.
- JANGAN simpan password/kunci di kode; gunakan `.env`.
- JANGAN ubah `activity_logs` (append-only).
- Detail: `SECURITY_GUIDELINES.md`.
### 4.5 Media Upload Rule (PENTING)
- Foto barang/profil/tim → disk `public`. **Foto bukti pengiriman → disk privat**, ditampilkan via route ber-Policy (ADR-011).
- `public/images/` HANYA aset brand statis (logo, favicon, foto toko).

## 5. Business Logic Kritis
### 5.1 Barang Keluar (penjualan langsung)
Kasir pilih barang → keranjang → Cash/Transfer → simpan. Insert `penjualan(tipe='langsung')` + `detail_penjualan` → trigger T2 potong stok & `mutasi_stok`, T3 masuk `kas_laci`. Stok kurang → exception "Stok tidak mencukupi".
### 5.2 Bon & Piutang
Catat Bon = `penjualan(tipe='bon')` + `detail_penjualan` + `bon_piutang` + `log_status_pengiriman('belum_dikirim')`. Status bayar otomatis (T4). Pembayaran: insert `pembayaran_bon` → T5 update total_terbayar & kas laci. Sisa = total_tagihan − total_terbayar. Lewat tempo = belum lunas & jatuh_tempo < hari ini.
### 5.3 Pengiriman
`CALL sp_kirim_bon` → buat `pengiriman_barang` + `detail_pengiriman`, potong stok. Karyawan ubah status: Sedang Disiapkan → Siap Berangkat/Sedang Diantar → Selesai (foto bukti wajib, T6). Batalkan = `sp_batalkan_pengiriman` (stok kembali; tidak bisa bila selesai).
### 5.4 Saldo Laci
Saldo aktif = `kas_laci.penutupan_id IS NULL`. Reset otomatis 00:00 (`laci:reset`) / manual (`sp_reset_laci`). Direset oleh NULL = "Sistem".
### 5.5 Status Pengantaran (ditampilkan)
Belum Dikirim → Dikirim ke Karyawan → Sedang Disiapkan → Siap Berangkat/Sedang Diantar → Selesai.
### 5.6 Hak Akses Ringkas
Owner hanya-lihat untuk Daftar Bon & Kehadiran. Kasir CRUD Kehadiran, kelola Bon. Karyawan hanya melihat pengiriman miliknya. Admin kelola pengguna & Tim.

## 6. STRICT CONSTRAINTS (NON-NEGOTIABLE)
1. JANGAN mengubah migration yang sudah di-merge; buat migration baru.
2. JANGAN menambah package composer/npm baru tanpa izin Tech Lead.
3. JANGAN mengubah nama tabel/kolom dari `DATABASE_SCHEMA.md` tanpa ADR.
4. JANGAN membuat seeder/data dummy atau commit data asli.
5. JANGAN menulis logika potong stok/kas/status bon di PHP yang bertentangan dengan trigger & procedure (ADR-007).
6. SELALU jalankan `php artisan migrate:status` sebelum mengusulkan perubahan DB.
7. JANGAN commit `.env`, password, atau dump database.
8. SELALU kerja di branch fitur jatah masing-masing; PR ke `develop`.
9. SELALU update `04_plans/TASK_DISTRIBUTION.md` dan `06_reports/PROGRESS_TRACKER.md` setelah selesai.
10. JANGAN memakai library chart/CSS selain Chart.js & Tailwind.
11. JANGAN menyimpan foto bukti pengiriman di disk public.
12. SELALU sediakan empty state & penanganan error di setiap halaman.

## 7. Format Komunikasi AI Agent
Saat melaporkan progres gunakan:
```
✅ Selesai: <fitur/prompt>
📁 File: <daftar file dibuat/diubah>
🧪 Test: <hasil Pest>
⚠️ Catatan/Asumsi: <jika ada>
➡️ Berikutnya: <langkah selanjutnya>
```

## 8. Cara Update Progress & Laporan
1. Update `04_plans/TASK_DISTRIBUTION.md` (centang `☐ → ✅`).
2. Update `06_reports/PROGRESS_TRACKER.md`: timestamp, summary table (% modul & OVERALL), phase checklist, Session Log.
3. Untuk fitur besar, buat `06_reports/REPORT_YYYY-MM-DD_<TOPIK>.md`.
4. Commit dengan Conventional Commits; buat PR ke `develop`, jangan push langsung ke `main`.

## 9. Peta File Proyek
| Lokasi | Isi | Source of Truth |
|---|---|---|
| `PRD_PASKAJAYA.md` | Kebutuhan produk | Produk |
| `02_architecture/DATABASE_SCHEMA.md` | Skema DB | Database |
| `07_extras/database_sql/` | View/trigger/procedure/privilege | Database lanjutan |
| `03_features/` | Spesifikasi per fitur | Perilaku fitur |
| `07_extras/referensi_desain/` | PDF UI/UX | Tampilan |
| `07_extras/referensi_laporan/` | Laporan MSBD | Latar & kebutuhan akademik |
| `05_prompts/` | Prompt siap pakai | Eksekusi |
