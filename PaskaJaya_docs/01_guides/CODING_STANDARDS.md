# Coding Standards
## PHP / Laravel
- PSR-12, `declare(strict_types=1)`, Laravel Pint.
- Controller tipis → logika di `app/Services` (SaleService, BonService, StockService, DrawerService).
- Validasi lewat FormRequest. Otorisasi via Policy + middleware `role:`.
- Uang: kolom `unsignedBigInteger` (rupiah utuh, tanpa desimal). Jangan float.
- Enum PHP 8.1 untuk role, metode bayar, status bon, status kirim.
- Query dashboard: gunakan agregasi SQL, hindari N+1 (`with()`).
## React / TypeScript
- TypeScript strict. Komponen fungsional, hooks.
- Struktur: `Pages/{Role}/...`, `Components/ui`, `Components/domain`, `Layouts`, `lib` (format, hooks).
- Form: `useForm` dari Inertia. Error validasi tampil di bawah field.
- Format uang/tanggal lewat `lib/format.ts` (`formatRupiah`, `formatTanggal`).
- Tidak ada `any`. Props Inertia bertipe di `types/`.
## Penamaan
- DB & PHP: snake_case / PascalCase class. React: PascalCase komponen, camelCase fungsi.
- Kode barang `BRG-001`, transaksi `TRX-1012`, bon `BON-0001`.
