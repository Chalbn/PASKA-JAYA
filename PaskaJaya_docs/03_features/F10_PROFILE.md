# F10 — Profil Saya

| Atribut | Isi |
|---|---|
| Role | semua |
| URL | `/{role}/profil` |
| Komponen Livewire | `Shared\Profil` |
| Tabel/Objek DB | users |
| ADR | ADR-011 |
| PIC | Chyntia |
| Referensi desain | `07_extras/referensi_desain/uiux_paska_jaya.pdf` (lihat `04_plans/SCREEN_MAPPING.md`) |

Judul "Profil Saya" — "Kelola data akun, profil, dan keamanan."
- **Data Akun**: avatar inisial, nama, badge role; Email, Role, Tanggal dibuat, Status akun (Aktif) — read-only.
- **Hapus Akun** (kartu merah muda): "Tindakan ini permanen dan tidak dapat dibatalkan." tombol merah **Hapus Akun** → dialog konfirmasi + password. Admin terakhir tidak boleh hapus.
- **Data Profil**: "Perbarui foto, nama, nomor HP, dan alamat." Ganti foto (unggah), Nama lengkap, Nomor HP, Alamat (textarea), **Simpan Perubahan**.
- **Ubah Password**: Password lama, Password baru, Konfirmasi password baru ("Minimal 8 karakter"), **Ubah Password**.
- Sama untuk Admin, Owner, Kasir, Karyawan. Avatar topbar ikut berubah.
## Acceptance: [ ] validasi [ ] password lama benar [ ] foto tersimpan [ ] hapus akun aman.


## Data & Empty State
Semua data dari database (tanpa dummy). Saat tabel kosong tampilkan empty state, kartu bernilai 0, dan grafik bernilai 0 — tanpa error (ADR-012).
