# PROMPT 03 — Models, Factories, Seeders

> **Konteks wajib**: Proyek PASKA JAYA (toko bahan bangunan). Stack Laravel 11 + Inertia + React/TS + Tailwind (lihat `02_architecture/TECH_STACK.md`). Ikuti `01_guides/*`, desain PDF `07_extras/referensi_desain/uiux_paska_jaya.pdf`, dan spesifikasi fitur di `03_features/`. UI berbahasa Indonesia. Uang integer rupiah. Selesai → jalankan test, update `06_reports/PROGRESS_TRACKER.md`, commit.

## Tugas
1. Model + relasi + cast + scope: `Item::lowStock()` (`stock <= min_stock`), `Bon::unpaid()`, `Bon::overdue()`, `Bon::remaining` accessor, `Sale::today()`.
2. Services skeleton: StockService (`decrease`, `increase`, mencatat stock_movements, lempar exception stok kurang), SaleService, BonService, DrawerService, ReportService.
3. Factory untuk semua model.
4. Seeder dari `07_extras/DEMO_DATA.md`: 7 user (password `password`), 5 anggota tim, 9 kategori, 17 item, ±12 sale (kode TRX-1001…1012) tersebar 7+ hari, 6 bon beserta item/pembayaran/status log, kehadiran, bahan harian, drawer_entries & drawer_closings 7 hari. Seeder hanya jalan di environment `local`/`testing`.
## Acceptance
`php artisan migrate:fresh --seed` OK; 5 item menipis; Σ sisa bon belum lunas ≈ Rp 7.806.000 (boleh disesuaikan); test unit StockService hijau.
