# AGENT GUIDELINES (untuk AI coding agent)

1. **Baca dulu**: `PRD_PaskaJaya.md`, `02_architecture/*`, fitur terkait di `03_features/`, dan PDF desain di `07_extras/referensi_desain/`.
2. **Desain = kebenaran**. Tampilan harus mengikuti PDF (layout, label Bahasa Indonesia, warna biru gradien, kartu kaca). Jangan menambah/menghapus fitur tanpa instruksi.
3. **Bahasa UI**: Indonesia (kecuali halaman Login/Register yang di desain berbahasa Inggris — tetap ikuti desain).
4. **Satu prompt = satu unit kerja**. Selesaikan, jalankan test, update `06_reports/PROGRESS_TRACKER.md`, lalu commit.
5. **Jangan hardcode** angka dashboard; semua dari database.
6. **Otorisasi di server** (Policy/Middleware), bukan hanya menyembunyikan menu.
7. **Transaksi DB** untuk operasi yang mengubah stok/uang.
8. **Jangan commit** `.env`, file upload, atau kredensial.
9. Bila instruksi ambigu: ikuti bagian "Asumsi" di PRD, catat di report, lanjutkan.
10. Format uang `Rp 1.820.000`; tanggal `14 November 2026`; zona waktu `Asia/Jakarta`.
