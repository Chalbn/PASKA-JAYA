# F01 — Auth (Login & Register)
Layar PDF: Halaman Login, Halaman Register.
## Login
- Split-screen (lihat UI_UX_GUIDELINES). Kartu "Welcome back" — "Log in to continue to your dashboard".
- Field: **Email or phone number** (placeholder `name@toko.com or 0812...`), **Password** (ikon mata show/hide), link **Forgot password?**, tombol **Login**, "Don't have an account? Register".
- **TANPA kotak "Demo accounts"** (catatan PDF: user harus register dulu).
- Login bisa pakai email ATAU nomor HP. Akun nonaktif ditolak ("Akun Anda dinonaktifkan. Hubungi admin.").
- Setelah sukses redirect sesuai role.
## Register
- Tombol kembali (←) ke Login. Judul "Create Account" — "Fill in your details to get started. We'll verify your account with a one-time code."
- Field: Full Name, Email, Phone Number (`0812 3456 7890`), Password, Confirm Password, checkbox "I agree to the Terms & Conditions and Privacy Policy" (wajib). Tombol **Register** disabled sampai valid. "Already have an account? Login".
- Validasi: nama wajib; email valid unik; HP unik format 08xx/+62; password ≥ 8 & cocok.
- Akun baru: role `karyawan`, status aktif (admin bisa ubah role).
- OTP (jika `AUTH_OTP_ENABLED=true`): halaman 6 digit, kedaluwarsa 10 menit, kirim ulang (cooldown 60 dtk); sukses → login.
## Forgot password
Alur reset standar Laravel via email (halaman sederhana bergaya sama).
## Acceptance
- [ ] Tidak ada demo account di UI. - [ ] Login by email & HP. - [ ] Rate limit. - [ ] Redirect per role. - [ ] Test feature auth hijau.
