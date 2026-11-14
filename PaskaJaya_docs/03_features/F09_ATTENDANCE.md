# F09 — Kehadiran Karyawan (Kasir: CRUD · Owner: hanya lihat)
Judul "Kehadiran Karyawan". Owner: "Daftar kehadiran karyawan. Pengisian kehadiran hanya dapat dilakukan oleh Kasir." + badge **Hanya Lihat**. Kasir: "Catat kehadiran dan pekerjaan yang dilakukan setiap karyawan." + tombol **+ Tambah Kehadiran**.
- Filter: cari nama/pekerjaan, dropdown **Semua karyawan**, tanggal (`mm/dd/yyyy`). Penghitung "11 catatan".
- Tabel: NO, NAMA KARYAWAN, TANGGAL, PEKERJAAN YANG DILAKUKAN (chip: "Bongkar muat semen", "Menata stok gudang", "Mengantar pesanan", …), AKSI (Kasir: pensil + hapus).
- **Tambah Kehadiran** modal: Nama karyawan (select "Pilih atau ketik nama" dari user karyawan aktif), Tanggal (default hari ini), Pekerjaan yang dilakukan (input "Pekerjaan 1" + **+ Tambah pekerjaan** untuk banyak), Batal/Simpan.
- **Edit** modal sama, terisi. **Hapus**: "Hapus baris kehadiran? Catatan Agus Prasetyo tanggal 14 November 2026 akan dihapus."
- Satu karyawan boleh >1 catatan per hari? Tidak — unik (employee, tanggal); tambah pekerjaan lewat edit.
## Acceptance: [ ] CRUD kasir [ ] owner 403 untuk tulis [ ] filter [ ] chip pekerjaan.
