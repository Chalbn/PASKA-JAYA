# PROMPT: View, Trigger, Stored Procedure & Privilege

| Atribut | Isi |
|---|---|
| PIC | Vascha |
| Branch | `feat/database-routines` |
| Bergantung pada | Migrations |
| Dokumen terkait | `07_extras/database_sql/*.sql`, ADR-007, ADR-013 |

## PROMPT
````
Kamu adalah senior Laravel developer. Proyek: **PASKA JAYA** — sistem manajemen Toko Bahan Bangunan (Laravel 13 + Livewire 3 + Tailwind CSS + MySQL 8, dev di Laragon).
BACA DULU: `PRD_PASKAJAYA.md`, `02_architecture/DATABASE_SCHEMA.md` + `ADR/`, fitur terkait di `03_features/`, `01_guides/AGENT_GUIDELINES.md`, dan PDF desain `07_extras/referensi_desain/uiux_paska_jaya.pdf`.
ATURAN KERAS:
- GIT: JANGAN jalankan perintah git apa pun (add, commit, push, branch, checkout, PR). Saya yang commit sendiri. Abaikan semua instruksi branch/commit/PR di file prompt. Jangan menambahkan "Co-Authored-By" atau atribusi AI di file mana pun.
- Database dimulai KOSONG. JANGAN membuat seeder/data dummy. Nama & angka di PDF hanya contoh tampilan; semua data diambil dari database.
- Nama tabel/kolom persis `DATABASE_SCHEMA.md`. Jangan ubah migration yang sudah merge.
- Logika kritis stok/kas/status bon mengikuti trigger & stored procedure (ADR-007).
- Semua halaman wajib punya empty state; kartu/grafik bernilai 0 saat data kosong.
- Otorisasi di server (middleware `role:`, Policy), bukan hanya menyembunyikan menu.
- UI hanya Tailwind + komponen `x-ui.*`; teks Bahasa Indonesia sesuai PDF; uang `Rp 1.820.000`; zona Asia/Jakarta.
FORMAT LAPORAN AKHIR: ✅ Selesai · 📁 File · 🧪 Test · ⚠️ Asumsi · ➡️ Berikutnya.

## TUGAS: Objek Database Lanjutan
1. Migration `buat_view_*`: `v_stok_menipis`, `v_piutang_aktif`, `v_penjualan_harian` (referensi `01_views.sql`).
2. Migration `buat_trigger_*`: T1–T8 (referensi `02_triggers.sql`). Gunakan `DB::unprepared()` per trigger tanpa `DELIMITER`; `down()` men-drop.
3. Migration `buat_procedure_*`: `sp_restock_barang`, `sp_kirim_bon`, `sp_batalkan_pengiriman`, `sp_reset_laci`, `sp_laporan_penjualan` (referensi `03_stored_procedures.sql`).
4. Verifikasi SQL referensi berjalan di MySQL 8; perbaiki bila ada error sintaks, dokumentasikan perubahan di `CHANGELOG.md`.
5. Skrip privilege `04_privileges.sql` → panduan pemakaian di `SETUP_GUIDE.md`.
6. Test database (`tests/Database`): T1 stok negatif ditolak; T2 penjualan langsung memotong stok + mutasi; T3 kas laci terisi; T4 status bon otomatis & kelebihan bayar ditolak; T5 pembayaran menambah total_terbayar + kas; T6 selesai tanpa foto ditolak; T7 admin terakhir terjaga; T8 activity_logs tidak bisa update/delete; semua procedure happy path + error.
## ACCEPTANCE
`migrate:fresh` memasang semua objek; seluruh test database hijau; akun `pj_app` tidak dapat DDL.
````
