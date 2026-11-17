# PROMPT: Panel Admin: Ringkasan Sistem, Pengguna, Tim

| Atribut | Isi |
|---|---|
| PIC | Vascha |
| Branch | `feat/admin-panel, feat/admin-team` |
| Bergantung pada | Auth |
| Dokumen terkait | `F02_ADMIN_DASHBOARD.md`, `F03_USER_MANAGEMENT.md`, `F04_TEAM.md` |

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

## TUGAS: Admin (F02–F04)
1. **Ringkasan Sistem**: tombol "Kelola Pengguna" → Manajemen Pengguna. 4 kartu: Total pengguna (+aktif), Master item (+stok menipis), Transaksi keluar (+data bon), Pengantaran (+berjalan). "Pengguna per Role" (Admin/Owner/Kasir/Karyawan + progress bar). "Status Sistem": Layanan aplikasi (Normal), Basis data (cek koneksi → Terhubung), Pencadangan terakhir (`system_configs.backup_terakhir`), Catatan kehadiran (n data), Catatan bahan harian (n data). Semua via `StatusSistemService`.
2. **Manajemen Pengguna**: pencarian nama/email; tabel Pengguna (avatar, nama, email, "(Anda)"), Role (dropdown langsung simpan), Dibuat, Status, Aksi (Edit, Nonaktifkan/Aktifkan, Hapus); "+ Tambah Pengguna" (modal: nama, email, HP, role, password). Akun sendiri: nonaktifkan/hapus/ubah role disabled; admin aktif terakhir dijaga (T7 + validasi). Hapus = soft delete. Catat `activity_logs`.
3. **Tim** (`anggota_tim`): grid kartu (inisial otomatis, nama, jabatan, tombol edit); tambah/edit/hapus. Data awal kosong → empty state "Belum ada anggota tim".
4. Command `backup:run` (mysqldump via akun `pj_backup`, tulis `backup_terakhir`) dijadwalkan 02:00.
## ACCEPTANCE
Role berubah langsung efektif; user nonaktif tidak bisa login; test proteksi admin terakhir; admin-only (403 role lain).
````
