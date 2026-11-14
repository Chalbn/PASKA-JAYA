# PROMPT 12 — Admin (F02, F03, F04)

> **Konteks wajib**: Proyek PASKA JAYA (toko bahan bangunan). Stack Laravel 11 + Inertia + React/TS + Tailwind (lihat `02_architecture/TECH_STACK.md`). Ikuti `01_guides/*`, desain PDF `07_extras/referensi_desain/uiux_paska_jaya.pdf`, dan spesifikasi fitur di `03_features/`. UI berbahasa Indonesia. Uang integer rupiah. Selesai → jalankan test, update `06_reports/PROGRESS_TRACKER.md`, commit.

## Tugas
- **Admin/Dashboard**: 4 kartu, Pengguna per Role (progress bar), Status Sistem (Layanan Normal, Basis data Terhubung, Pencadangan terakhir, jumlah catatan kehadiran & bahan harian), tombol Kelola Pengguna → Manajemen Pengguna. Command `backup:run` 02:00 menulis `settings.last_backup_at`.
- **Admin/Users**: pencarian, tabel (role dropdown langsung simpan, status, Edit/Nonaktifkan/Aktifkan/Hapus), modal Tambah/Edit, proteksi akun sendiri & admin terakhir.
- **Admin/Team**: grid kartu tim + edit/tambah/hapus.
## Acceptance
Role berubah langsung efektif; user nonaktif tidak bisa login; test proteksi admin terakhir.
