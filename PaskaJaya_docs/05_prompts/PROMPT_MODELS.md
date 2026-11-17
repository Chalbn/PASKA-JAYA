# PROMPT: Models, Enum, Command Admin Pertama

| Atribut | Isi |
|---|---|
| PIC | Patricia |
| Branch | `feat/database-schema` |
| Bergantung pada | Migrations |
| Dokumen terkait | `FOLDER_STRUCTURE.md`, `CODING_STANDARDS.md` |

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

## TUGAS: Model & Service Dasar
1. 22 model dengan `$table`, `$fillable`, `casts()`, relasi dua arah, SoftDeletes sesuai skema.
2. Enum PHP: `RolePengguna`, `StatusPengguna`, `MetodeBayar`, `TipePenjualan`, `StatusBayar`, `StatusPengiriman`.
3. Scope: `Barang::stokMenipis()`, `BonPiutang::belumLunas()/lewatTempo()`, `Penjualan::langsung()`, `KasLaci::aktif()`. Accessor `BonPiutang::sisa`.
4. Service skeleton: PenjualanService, BonService (membungkus `CALL sp_*`), LaciService, LaporanService, StatusSistemService, FileService. Terjemahkan `QueryException` dari SIGNAL menjadi `DomainException` berpesan ramah.
5. `AuditObserver` mencatat `activity_logs` (aksi, modul, data_lama/data_baru).
6. Command `php artisan app:buat-admin` (interaktif: nama, email, no HP, password min. 12 karakter; role admin, status aktif, email terverifikasi). Tolak email duplikat. **Tidak ada password default.**
7. Factory HANYA untuk test.
## ACCEPTANCE
Relasi & scope diuji; service aman pada database kosong (hasil 0/array kosong); `app:buat-admin` membuat tepat 1 admin.
````
