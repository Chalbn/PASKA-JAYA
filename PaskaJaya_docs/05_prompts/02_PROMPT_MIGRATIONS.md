# PROMPT 02 — Migrations

> **Konteks wajib**: Proyek PASKA JAYA (toko bahan bangunan). Stack Laravel 11 + Inertia + React/TS + Tailwind (lihat `02_architecture/TECH_STACK.md`). Ikuti `01_guides/*`, desain PDF `07_extras/referensi_desain/uiux_paska_jaya.pdf`, dan spesifikasi fitur di `03_features/`. UI berbahasa Indonesia. Uang integer rupiah. Selesai → jalankan test, update `06_reports/PROGRESS_TRACKER.md`, commit.

## Tugas
Buat semua migration persis sesuai `02_architecture/DATABASE_SCHEMA.md`: users (+role, status, phone, address, avatar_path, softDeletes), otp_codes, categories, items, stock_movements, sales, sale_items, bons, bon_items, bon_payments, bon_status_logs, drawer_entries, drawer_closings, daily_materials, attendances, team_members, settings.
- Gunakan enum PHP di `app/Enums` (Role, UserStatus, PaymentMethod, PaymentStatus, DeliveryStatus) dan cast di model.
- FK + index sesuai skema. `unsignedBigInteger` untuk uang.
## Acceptance
`php artisan migrate:fresh` sukses; rollback sukses; test skema dasar hijau.
