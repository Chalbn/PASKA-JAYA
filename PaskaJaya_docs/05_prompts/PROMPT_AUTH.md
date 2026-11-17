# PROMPT: Auth & Profil

| Atribut | Isi |
|---|---|
| PIC | Vascha (+Chyntia profil) |
| Branch | `feat/auth, feat/profile` |
| Bergantung pada | Database Routines, Models, Frontend |
| Dokumen terkait | `F01_AUTH_SYSTEM.md`, `F10_PROFILE.md`, `SECURITY_GUIDELINES.md` |

## PROMPT
````
Kamu adalah senior Laravel developer. Proyek: **PASKA JAYA** — sistem manajemen Toko Bahan Bangunan (Laravel 13 + Livewire 3 + Tailwind CSS + MySQL 8, dev di Laragon).
BACA DULU: `PRD_PASKAJAYA.md`, `02_architecture/DATABASE_SCHEMA.md` + `ADR/`, fitur terkait di `03_features/`, `01_guides/AGENT_GUIDELINES.md`, dan PDF desain `07_extras/referensi_desain/uiux_paska_jaya.pdf`.
ATURAN KERAS:
- GIT: JANGAN jalankan perintah git apa pun (add, commit, push, branch, checkout, PR). Saya yang commit sendiri. Abaikan semua instruksi branch/commit/PR di file prompt. Jangan menambahkan "Co-Authored-By" atau atribusi AI di file mana pun.
- Database dimulai KOSONG. JANGAN membuat seeder/data dummy. Nama & angka di PDF hanya contoh tampilan; semua data diambil dari database.
- Nama tabel/kolom persis `DATABASE_SCHEMA.md`. Jangan ubah migration yang sudah merge.
- Logika kritis stok/kas/status bon mengikuti trigger & stored procedure (ADR-007).
- Semua halaman wajib punya empty state; kartu/grafik bernilai 0 saat data kosong.
- Otorisasi di server (middleware `role:`, Policy), bukan hanya menyembunyikan menu.
- UI hanya Tailwind + komponen `x-ui.*`; teks Bahasa Indonesia sesuai PDF; uang `Rp 1.820.000`; zona Asia/Jakarta.
FORMAT LAPORAN AKHIR: ✅ Selesai · 📁 File · 🧪 Test · ⚠️ Asumsi · ➡️ Berikutnya.

## TUGAS: Auth (F01) & Profil (F10)
1. `Auth\Login`: field "Email or phone number" + "Password" (show/hide), "Forgot password?", tombol Login, "Don't have an account? Register". **Tanpa kotak demo accounts.** Login memakai email ATAU `no_hp`. Rate limit 5/menit. Akun `nonaktif` ditolak dengan pesan jelas. Redirect per role.
2. `Auth\Register`: Full Name, Email, Phone Number, Password, Confirm Password, checkbox Terms & Privacy (wajib), tombol Register disabled sampai valid, tombol ← ke Login. Role awal `karyawan`, status aktif.
3. OTP 6 digit (`AUTH_OTP_ENABLED`): hash di `otp_codes`, kedaluwarsa 10 menit, kirim ulang cooldown 60 dtk.
4. Lupa password (reset via email standar Laravel).
5. Middleware `PastikanRole` (alias `role`) dan `PastikanAktif`; grup route per role sesuai `API_ROUTES.md`. Logout via POST.
6. Catat `login`/`logout` ke `activity_logs`.
7. Profil (`Shared\Profil`, semua role): Data Akun (read-only), Hapus Akun (konfirmasi password; admin terakhir tidak boleh), Data Profil (foto, nama, HP, alamat), Ubah Password.
8. Tampilan sesuai PDF; foto toko baru di kiri halaman.
## ACCEPTANCE
Test: login email/HP, salah password, nonaktif, rate limit, register valid/invalid, OTP, 403 lintas role, redirect per role, profil validasi, hapus akun aman.
````
