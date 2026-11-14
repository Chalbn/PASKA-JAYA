# F03 — Admin: Manajemen Pengguna
Judul "Manajemen Pengguna" — "Tambah, ubah role, nonaktifkan, atau hapus akun." Tombol **+ Tambah Pengguna**.
- Pencarian "Cari nama atau email…".
- Tabel: **PENGGUNA** (avatar inisial, nama, email; "(Anda)" untuk diri sendiri) · **ROLE** (dropdown Admin/Owner/Kasir/Karyawan, ubah langsung simpan) · **DIBUAT** (5 Januari 2024) · **STATUS** (badge Aktif/Nonaktif) · **AKSI**: Edit, Nonaktifkan/Aktifkan, Hapus (ikon tempat sampah).
- Modal Tambah/Edit: nama, email, HP, role, password (tambah wajib; edit opsional).
- Konfirmasi hapus. Akun sendiri: Nonaktifkan, hapus, dan ubah role **disabled**. Minimal 1 admin aktif tersisa.
- Hapus = soft delete; riwayat transaksi tetap utuh.
## Acceptance
- [ ] CRUD + toggle status + ubah role. - [ ] Proteksi akun sendiri & admin terakhir. - [ ] Pencarian server-side. - [ ] Test.
