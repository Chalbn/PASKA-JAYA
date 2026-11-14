# PROMPT 05 — Auth (F01)

> **Konteks wajib**: Proyek PASKA JAYA (toko bahan bangunan). Stack Laravel 11 + Inertia + React/TS + Tailwind (lihat `02_architecture/TECH_STACK.md`). Ikuti `01_guides/*`, desain PDF `07_extras/referensi_desain/uiux_paska_jaya.pdf`, dan spesifikasi fitur di `03_features/`. UI berbahasa Indonesia. Uang integer rupiah. Selesai → jalankan test, update `06_reports/PROGRESS_TRACKER.md`, commit.

## Tugas
Implementasikan `03_features/F01_AUTH_SYSTEM.md`:
- Login (email ATAU nomor HP + password, show/hide password, Forgot password), Register (Full Name, Email, Phone, Password, Confirm, checkbox Terms), OTP (`AUTH_OTP_ENABLED`), logout, reset password.
- **Tanpa kotak demo accounts.**
- Middleware `EnsureRole`, `EnsureActive`; redirect per role; rate limit login; akun nonaktif ditolak.
- Register → role `karyawan`.
- Teks dan layout persis desain (Welcome back / Create Account, tombol ← di Register, tombol Register disabled sampai valid).
## Acceptance
Test: login email, login HP, salah password, akun nonaktif, register valid/invalid, 403 lintas role, redirect tiap role.
