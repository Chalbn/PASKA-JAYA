# PASKA JAYA — Security Guidelines

## 1. Authentication & Authorization
| Aturan | Implementasi |
|---|---|
| Login email **atau** no HP | Komponen `Auth\Login`; rate limit 5/menit per IP+identifier |
| Password | Hash (bcrypt/argon), min. 8 karakter; ubah password butuh password lama |
| Akun nonaktif | Ditolak login (`PastikanAktif` + cek saat login) |
| Role | `role:<nama>` pada grup route + Policy |
| Owner read-only | `BonPiutangPolicy`/`AbsensiPolicy` menolak tulis → 403 |
| Karyawan | hanya pengiriman miliknya (`karyawan_id = auth()->id()`) |
| Admin terakhir | dijaga trigger T7 + validasi aplikasi |
| Hapus akun (Profil) | konfirmasi password; hanya jika bukan admin terakhir |
Forbidden: jangan mengandalkan menu tersembunyi; jangan simpan role di cookie/klien.

## 2. Input Validation
Semua input divalidasi (Livewire `rules()`/Form Object): angka ≥ 0, tanggal valid, enum whitelist, panjang maksimum sesuai kolom. Server menghitung ulang `subtotal`/`total` — jangan percaya nilai dari klien. Properti Livewire sensitif (id, harga, total) bertanda `#[Locked]`.

## 3. CSRF
Livewire/Blade memakai token CSRF otomatis. Tidak ada route yang mengecualikan CSRF.

## 4. SQL Injection
Eloquent/Query Builder dengan binding. Panggilan procedure memakai placeholder `?`. Dilarang concatenation input ke SQL.

## 5. XSS
Blade `{{ }}` (escaped). Dilarang `{!! !!}` untuk input pengguna. Nama pelanggan/catatan ditampilkan escaped.

## 6. File Upload Security
- Validasi `mimes:jpg,jpeg,png,webp`, `max:4096`.
- Nama file acak (`hashName()`), jangan pakai nama asli.
- **Foto bukti pengiriman di disk privat**; akses lewat `/berkas/bukti-pengiriman/{id}` + Policy (ADR-011).
- Hapus berkas lama saat diganti/dihapus.

## 7. Audit Trail (`activity_logs`)
Catat: login/logout, tambah/ubah/hapus barang (termasuk harga), kirim/batal pengiriman, pembayaran bon, reset laci, ubah role/status pengguna. Tabel append-only (trigger T8). Simpan `data_lama`/`data_baru` JSON, IP, user agent. Jangan simpan password/token di log.

## 8. Session Security
`SESSION_LIFETIME=120`, `SESSION_SECURE_COOKIE=true` (produksi/HTTPS), `SESSION_SAME_SITE=lax`, regenerate session saat login, invalidate saat logout.

## 9. Rate Limiting
Login & OTP: 5/menit. Kirim ulang OTP: cooldown 60 dtk. Unduhan laporan: 20/menit.

## 10. Hak Akses Database (ADR-013)
| Akun MySQL | Hak | Dipakai oleh |
|---|---|---|
| `pj_app` | SELECT/INSERT/UPDATE/DELETE/EXECUTE | Laravel runtime |
| `pj_migrator` | ALL | `php artisan migrate` (deploy) |
| `pj_report` | SELECT pada view | laporan/BI |
| `pj_backup` | SELECT, LOCK TABLES, SHOW VIEW, TRIGGER | `backup:run` |
Skrip: `07_extras/database_sql/04_privileges.sql`. Password ke `.env` server saja.

## 11. Register Terbuka (Risiko & Mitigasi)
Register publik membuat role `karyawan`. Mitigasi: Admin meninjau pengguna baru di Manajemen Pengguna; opsi lanjutan: status `menunggu` + persetujuan admin (catat sebagai ADR bila diadopsi).

## 12. Environment Separation
Local: `APP_DEBUG=true`, mailer `log`. Produksi: `APP_DEBUG=false`, HTTPS, OTP aktif, mailer nyata, DB akun berbeda.

## Pre-Deploy Checklist
- [ ] `APP_DEBUG=false`, `APP_ENV=production`, `APP_KEY` baru
- [ ] Akun MySQL berlapis dibuat, password kuat
- [ ] `php artisan migrate --force` (akun migrator) sukses; trigger/procedure terpasang
- [ ] `storage:link`; folder private tidak terekspos web
- [ ] HTTPS aktif; cookie secure
- [ ] Backup 02:00 jalan & pernah diuji restore
- [ ] Admin pertama dibuat (`app:buat-admin`), tanpa password default
- [ ] Uji akses lintas role (matriks PRD §6)
