# F09 — Kehadiran Karyawan (Kasir CRUD · Owner read-only)

| Atribut | Isi |
|---|---|
| Role | kasir, owner |
| URL | `/kasir/kehadiran`, `/owner/kehadiran` |
| Komponen Livewire | `Kasir\Kehadiran`, `Owner\Kehadiran` |
| Tabel/Objek DB | absensi_karyawan, detail_pekerjaan_absensi, users |
| ADR | ADR-009 |
| PIC | Jona (Lead) · Chyntia (UI) |
| Referensi desain | `07_extras/referensi_desain/uiux_paska_jaya.pdf` (lihat `04_plans/SCREEN_MAPPING.md`) |

Judul "Kehadiran Karyawan". Owner: "Daftar kehadiran karyawan. Pengisian kehadiran hanya dapat dilakukan oleh Kasir." + badge **Hanya Lihat**. Kasir: "Catat kehadiran dan pekerjaan yang dilakukan setiap karyawan." + tombol **+ Tambah Kehadiran**.
- Filter: cari nama/pekerjaan, dropdown **Semua karyawan**, tanggal (`mm/dd/yyyy`). Penghitung "11 catatan".
- Tabel: NO, NAMA KARYAWAN, TANGGAL, PEKERJAAN YANG DILAKUKAN (chip: "Bongkar muat semen", "Menata stok gudang", "Mengantar pesanan", …), AKSI (Kasir: pensil + hapus).
- **Tambah Kehadiran** modal: Nama karyawan (select "Pilih atau ketik nama" dari user karyawan aktif), Tanggal (default hari ini), Pekerjaan yang dilakukan (input "Pekerjaan 1" + **+ Tambah pekerjaan** untuk banyak), Batal/Simpan.
- **Edit** modal sama, terisi. **Hapus**: "Hapus baris kehadiran? Catatan Agus Prasetyo tanggal 14 November 2026 akan dihapus."
- Satu karyawan boleh >1 catatan per hari? Tidak — unik `(karyawan_id, tanggal)`; kolom `shift` opsional (pagi/siang/full); pekerjaan disimpan di `detail_pekerjaan_absensi`; tambah pekerjaan lewat edit.
## Acceptance: [ ] CRUD kasir [ ] owner 403 untuk tulis [ ] filter [ ] chip pekerjaan.


## Data & Empty State
Semua data dari database (tanpa dummy). Saat tabel kosong tampilkan empty state, kartu bernilai 0, dan grafik bernilai 0 — tanpa error (ADR-012).
