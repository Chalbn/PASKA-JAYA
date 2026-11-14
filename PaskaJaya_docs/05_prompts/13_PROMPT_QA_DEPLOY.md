# PROMPT 13 — QA, Polish, Deploy

> **Konteks wajib**: Proyek PASKA JAYA (toko bahan bangunan). Stack Laravel 11 + Inertia + React/TS + Tailwind (lihat `02_architecture/TECH_STACK.md`). Ikuti `01_guides/*`, desain PDF `07_extras/referensi_desain/uiux_paska_jaya.pdf`, dan spesifikasi fitur di `03_features/`. UI berbahasa Indonesia. Uang integer rupiah. Selesai → jalankan test, update `06_reports/PROGRESS_TRACKER.md`, commit.

## Tugas
1. Jalankan seluruh checklist E2E di `06_reports/PROGRESS_TRACKER.md`; perbaiki bug.
2. Audit otorisasi: setiap route diuji untuk 4 role (matriks di PRD §3).
3. Audit N+1, indeks, empty state, loading, error state, responsif mobile (terutama Karyawan).
4. Bandingkan tiap layar dengan PDF (`04_plans/SCREEN_MAPPING.md`) — samakan teks, urutan, warna, badge.
5. Deploy: `.env` produksi (`APP_DEBUG=false`, OTP aktif), `storage:link`, cron scheduler, backup, HTTPS.
6. Tulis laporan `06_reports/REPORT_<tanggal>_RELEASE.md`.
## Acceptance
Semua checklist lulus; coverage service ≥ 80%; tidak ada celah akses lintas role.
