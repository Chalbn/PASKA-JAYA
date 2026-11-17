# PROMPT: Migrations 22 Tabel

| Atribut | Isi |
|---|---|
| PIC | Vascha, Patricia |
| Branch | `feat/database-schema` |
| Bergantung pada | Setup Project |
| Dokumen terkait | `DATABASE_SCHEMA.md`, ADR-002/003/004/005/006/008 |

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

## TUGAS: Buat Migration PROMPT_MIGRATIONS.md
Buat migration untuk 22 tabel pada `DATABASE_SCHEMA.md` dengan urutan FK yang benar (bagian "Urutan Migration"):
roles, users (+ tabel bawaan Laravel), system_configs, activity_logs, kategori_barang, barang, pelanggan, penjualan, detail_penjualan, bon_piutang, pembayaran_bon, pengiriman_barang, detail_pengiriman, log_status_pengiriman, mutasi_stok, penutupan_laci, kas_laci, penggunaan_bahan, absensi_karyawan, detail_pekerjaan_absensi, anggota_tim, otp_codes.
Detail wajib:
- Uang `unsignedBigInteger`; enum sesuai skema; softDeletes pada users, barang, bon_piutang.
- CHECK constraint: `barang.stok >= 0`, `bon_piutang.total_terbayar <= total_tagihan`, `jatuh_tempo >= tanggal_bon`.
- UNIQUE: `detail_penjualan(penjualan_id, barang_id)`, `absensi_karyawan(karyawan_id, tanggal)`, `bon_piutang.penjualan_id`.
- `pengiriman_barang.aktif_key` = `unsignedBigInteger()->virtualAs("IF(status='dibatalkan', NULL, bon_id)")->nullable()->unique()`.
- FK eksplisit (`restrictOnDelete`/`cascadeOnDelete`/`nullOnDelete` sesuai skema). Index sesuai skema.
- `RoleSeeder` (4 baris: admin, owner, kasir, karyawan) dan `ConfigSeeder` (kunci konfigurasi: auto_backup, jam_backup=02:00, jam_reset_laci=00:00, nama_toko) — HANYA data referensi sistem. `DatabaseSeeder` memanggil keduanya saja.
- DILARANG seeder pengguna/barang/transaksi.
## ACCEPTANCE
`php artisan migrate:fresh --seed` sukses; hanya tabel `roles` (4 baris) dan `system_configs` yang berisi; `migrate:rollback` sukses; test skema (kolom, FK, unique, check) hijau.
````
