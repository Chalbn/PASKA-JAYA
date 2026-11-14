# PROMPT 09 — Pengantaran Karyawan + Owner Read-only Bon (F14, F07)

> **Konteks wajib**: Proyek PASKA JAYA (toko bahan bangunan). Stack Laravel 11 + Inertia + React/TS + Tailwind (lihat `02_architecture/TECH_STACK.md`). Ikuti `01_guides/*`, desain PDF `07_extras/referensi_desain/uiux_paska_jaya.pdf`, dan spesifikasi fitur di `03_features/`. UI berbahasa Indonesia. Uang integer rupiah. Selesai → jalankan test, update `06_reports/PROGRESS_TRACKER.md`, commit.

## Tugas
1. **Karyawan/Deliveries**: 3 kartu (baru/berjalan/selesai), tab Akan diantar/Selesai, kartu bon (badge BERIKUTNYA/Baru, alamat, daftar barang, jadwal, catatan kasir, tombol Detail & Ubah Status). Hanya bon miliknya, urut jadwal terdekat. Badge merah di menu = jumlah tugas baru.
2. **Karyawan/DeliveryShow**: informasi bon + stepper (Sedang Disiapkan → Siap Berangkat/Sedang Diantar → Selesai), **foto bukti wajib** untuk Selesai (kamera/galeri), tombol Ubah Status; catat `bon_status_logs`; set `delivered_at`.
3. **Owner/Bons** (read-only): halaman Daftar Bon + modal Detail + timeline + foto bukti; alert "Mode baca saja", badge Hanya Lihat; semua aksi tulis → 403.
## Acceptance
Perubahan status karyawan langsung terlihat di Kasir & Owner; selesai tanpa foto ditolak; karyawan lain tidak bisa akses bon bukan miliknya (403); test.
