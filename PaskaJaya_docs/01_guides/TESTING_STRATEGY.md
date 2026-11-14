# Testing Strategy (Pest)
- **Feature test** wajib: login/register, akses per role (403), SaleService (stok berkurang, saldo laci), BonService (kirim → stok, bayar sebagian, lunas), drawer reset, status pengantaran + foto wajib, CRUD master item (restock), kategori otomatis.
- **Unit test**: StockService, perhitungan sisa/lewat tempo, format uang.
- **Skenario E2E manual** (checklist di `06_reports/PROGRESS_TRACKER.md`): Kasir catat bon → kirim ke karyawan → karyawan selesai + foto → owner lihat detail dengan foto.
- Target coverage service ≥ 80%.
