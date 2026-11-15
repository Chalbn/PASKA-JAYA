# F04 — Admin — Tim

| Atribut | Isi |
|---|---|
| Role | admin |
| URL | `/admin/tim` |
| Komponen Livewire | `Admin\Tim\Index` |
| Tabel/Objek DB | anggota_tim |
| ADR | ADR-011 |
| PIC | Jeli (Lead) · Vascha (review) |
| Referensi desain | `07_extras/referensi_desain/uiux_paska_jaya.pdf` (lihat `04_plans/SCREEN_MAPPING.md`) |

Halaman profil tim pembuat web. Judul "Tim" — "Anggota tim PASKA JAYA."
- Grid kartu: kotak inisial besar, nama, jabatan, tombol edit (pensil).
- Data awal **kosong**; Admin menambah anggota tim sendiri (inisial otomatis dari nama).
- Empty state: "Belum ada anggota tim. Klik + Tambah Anggota."
- Admin dapat edit (modal nama, jabatan, foto opsional), tambah, hapus anggota.
## Acceptance: [ ] tampil sesuai desain [ ] edit tersimpan [ ] hanya admin.


## Data & Empty State
Semua data dari database (tanpa dummy). Saat tabel kosong tampilkan empty state, kartu bernilai 0, dan grafik bernilai 0 — tanpa error (ADR-012).
