# Rute (Inertia, `routes/web.php`)
Middleware: `auth`, `active`, `role:<x>`. Redirect setelah login: admin→`/admin`, owner→`/owner`, kasir→`/kasir`, karyawan→`/karyawan/pengantaran`.

## Auth (guest)
GET/POST `/login` · GET/POST `/register` · GET/POST `/verify-otp` (+ `/verify-otp/resend`) · POST `/logout`

## Admin — prefix `/admin`
GET `/` Ringkasan Sistem · GET `/pengguna` · POST `/pengguna` · PUT `/pengguna/{user}` (nama, email, HP, role) · PATCH `/pengguna/{user}/role` · PATCH `/pengguna/{user}/status` · DELETE `/pengguna/{user}` · GET `/tim` · POST/PUT/DELETE `/tim/{member}` · GET/PUT `/profil`

## Owner — prefix `/owner`
GET `/` Ringkasan (props: stats, sales7days, lowStock, unpaidBons, recentSales)
GET `/laporan-barang-keluar?from&to&method` · GET `/laporan-barang-keluar/export` (CSV)
GET `/daftar-bon?filter=semua|belum_lunas|lunas` · GET `/daftar-bon/{bon}` (detail JSON/modal)
GET `/master-item` + CRUD (lihat Shared Items) · GET `/kehadiran` (read-only) · GET/PUT `/profil`

## Kasir — prefix `/kasir`
GET `/` Beranda · POST `/saldo/reset`
GET/POST `/barang-keluar` · GET `/saldo` · GET `/grafik-mingguan?range=minggu_ini|minggu_lalu|tanggal&date`
GET `/riwayat-saldo?date` · GET `/riwayat-saldo/{closing}` (detail) · GET `/riwayat-saldo/export?format=xlsx|pdf`
GET/POST `/catat-bon`
GET `/daftar-bon?filter` · POST `/daftar-bon/{bon}/kirim` (employee_id, scheduled_at, note) · POST `/{bon}/ganti-karyawan` · POST `/{bon}/batalkan-pengiriman` · POST `/{bon}/lunas` (method) · POST `/{bon}/bayar-sebagian` (amount, method) · GET `/{bon}` detail · DELETE `/{bon}`
GET/POST `/bahan-harian` · PUT/DELETE `/bahan-harian/{m}`
GET/POST `/kehadiran` · PUT/DELETE `/kehadiran/{a}`
Master Item & Profil (Shared)

## Karyawan — prefix `/karyawan`
GET `/pengantaran?tab=akan_diantar|selesai` · GET `/pengantaran/{bon}` · POST `/pengantaran/{bon}/status` (status, proof photo wajib untuk `selesai`) · GET/PUT `/profil`

## Shared (owner+kasir) `/master-item`
GET `?q&category&view=kartu|tabel` · POST (tambah) · PUT `/{item}` (edit + restock `add_stock`) · DELETE `/{item}`
## Shared Profil (semua role)
PUT `/profil` (nama, HP, alamat, foto) · PUT `/profil/password` · DELETE `/profil` (konfirmasi password)
