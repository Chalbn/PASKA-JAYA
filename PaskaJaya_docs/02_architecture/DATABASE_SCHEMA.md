# Database Schema
Uang = `unsignedBigInteger` (rupiah). Semua tabel punya `timestamps`.

**users**: id, name, email (unique), phone (unique, nullable), password, role (admin|owner|kasir|karyawan), status (aktif|nonaktif), address (text null), avatar_path (null), email_verified_at, remember_token, softDeletes.
**otp_codes**: id, user_id, code_hash, expires_at, used_at.
**categories**: id, name (unique), slug (unique).
**items**: id, code (unique, BRG-###), name, category_id FK, unit, buy_price, sell_price, stock (int), min_stock (int default 10), photo_path (null), last_restocked_at (null = tanggal masuk), softDeletes.
**stock_movements**: id, item_id, type (initial|restock|sale|bon_sent|bon_cancel|adjustment), qty (+/-), stock_after, ref_type, ref_id, user_id, note.
**sales** (barang keluar): id, code (TRX-####, unique), customer_name (default 'Umum'), payment_method (cash|transfer), total, user_id (kasir), sold_at.
**sale_items**: id, sale_id, item_id, item_name, unit, qty, unit_price, subtotal.
**bons**: id, code (BON-####), customer_name, delivery_address (null), bon_date, due_date, total, paid_amount (default 0), payment_status (belum_lunas|lunas), delivery_status (belum_dikirim|dikirim_ke_karyawan|sedang_disiapkan|siap_berangkat|selesai), assigned_to FK users null, scheduled_at null, delivery_note text null, delivery_proof_path null, delivered_at null, created_by FK, softDeletes.
**bon_items**: id, bon_id, item_id, item_name, unit, qty, unit_price, subtotal.
**bon_payments**: id, bon_id, amount, method (cash|transfer), paid_at, user_id.
**bon_status_logs**: id, bon_id, status, user_id, note null, created_at (dipakai "Alur Status" + jam).
**drawer_entries**: id, entry_date, occurred_at, description ("Penjualan TRX-1004 - Pak Hendra"), method (cash|transfer), amount, source_type, source_id.
**drawer_closings**: id, closing_date (unique), cash_total, transfer_total, total, reset_at (time 00:00), reset_by FK users null (null = "Sistem").
**daily_materials**: id, spent_on (date), name, qty (decimal 10,2), unit, price (total rupiah), note null, user_id.
**attendances**: id, employee_id FK users, work_date, tasks (json array string), created_by FK.
**team_members**: id, initials, name, title, photo_path null, sort_order.
**settings**: key (unique), value (json) — mis. `last_backup_at`.

Indeks: items(code, category_id), sales(sold_at), bons(payment_status, delivery_status, due_date), drawer_entries(entry_date), attendances(work_date, employee_id).

## Aturan bisnis
- `items.stock >= 0` (tolak transaksi jika stok kurang; pesan "Stok {nama} tidak cukup (tersisa N)").
- Stok menipis: `stock <= min_stock`.
- Bon: `Sisa = total - paid_amount`; `Lunas` bila `paid_amount >= total`. Lewat tempo bila belum lunas & due_date < hari ini.
- Setiap sale / pembayaran bon membuat `drawer_entries`.
- Saldo laci hari ini = Σ drawer_entries hari ini per metode.
- Reset (otomatis 00:00 / manual) → buat `drawer_closings` untuk tanggal yang ditutup.
