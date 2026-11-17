# F03 — Admin — Manajemen Pengguna

| Atribut | Isi |
|---|---|
| Role | admin |
| URL | `/admin/pengguna` |
| Komponen Livewire | `Admin\Pengguna\Index` |
| Tabel/Objek DB | users, roles, activity_logs |
| ADR | ADR-009 |
| PIC | Vascha |
| Referensi desain | `07_extras/referensi_desain/uiux_paska_jaya.pdf` (lihat `04_plans/SCREEN_MAPPING.md`) |

Judul "Manajemen Pengguna" — "Tambah, ubah role, nonaktifkan, atau hapus akun." Tombol **+ Tambah Pengguna**.
- Pencarian "Cari nama atau email…".
- Tabel: **PENGGUNA** (avatar inisial, nama, email; "(Anda)" untuk diri sendiri) · **ROLE** (dropdown Admin/Owner/Kasir/Karyawan, ubah langsung simpan) · **DIBUAT** (5 Januari 2024) · **STATUS** (badge Aktif/Nonaktif) · **AKSI**: Edit, Nonaktifkan/Aktifkan, Hapus (ikon tempat sampah).
- Modal Tambah/Edit: nama, email, HP, role, password (tambah wajib; edit opsional).
- Konfirmasi hapus. Akun sendiri: Nonaktifkan, hapus, dan ubah role **disabled**. Minimal 1 admin aktif tersisa.
- Hapus = soft delete; riwayat transaksi tetap utuh.
## Acceptance
- [ ] CRUD + toggle status + ubah role. - [ ] Proteksi akun sendiri & admin terakhir. - [ ] Pencarian server-side. - [ ] Test.


## Data & Empty State
Semua data dari database (tanpa dummy). Saat tabel kosong tampilkan empty state, kartu bernilai 0, dan grafik bernilai 0 — tanpa error (ADR-012).
