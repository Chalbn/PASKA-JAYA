# PROMPT 10 — Bahan Harian & Kehadiran (F13, F09)

> **Konteks wajib**: Proyek PASKA JAYA (toko bahan bangunan). Stack Laravel 11 + Inertia + React/TS + Tailwind (lihat `02_architecture/TECH_STACK.md`). Ikuti `01_guides/*`, desain PDF `07_extras/referensi_desain/uiux_paska_jaya.pdf`, dan spesifikasi fitur di `03_features/`. UI berbahasa Indonesia. Uang integer rupiah. Selesai → jalankan test, update `06_reports/PROGRESS_TRACKER.md`, commit.

## Tugas
1. **Bahan Harian** (`Kasir/DailyMaterials`): form Tambah bahan (nama, jumlah+satuan, harga, tanggal, keterangan; error "Nama bahan, jumlah, dan harga wajib diisi."), daftar kanan dengan total pengeluaran, edit & hapus.
2. **Kehadiran** (`Kasir/Attendance` CRUD; `Owner/Attendance` read-only): filter cari/karyawan/tanggal, tabel chip pekerjaan, modal Tambah (pilih karyawan, tanggal, pekerjaan dinamis "+ Tambah pekerjaan"), modal Edit, dialog Hapus ("Catatan {nama} tanggal {tanggal} akan dihapus"). Unik (employee, tanggal).
## Acceptance
CRUD berfungsi; owner tidak punya tombol tulis dan POST/PUT/DELETE → 403; test.
