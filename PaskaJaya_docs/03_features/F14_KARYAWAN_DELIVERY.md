# F14 — Karyawan — Pengantaran

| Atribut | Isi |
|---|---|
| Role | karyawan |
| URL | `/karyawan/pengantaran`, `/karyawan/pengantaran/{id}` |
| Komponen Livewire | `Karyawan\Pengantaran\Index`, `Detail` |
| Tabel/Objek DB | pengiriman_barang, detail_pengiriman, log_status_pengiriman |
| ADR | ADR-006, ADR-011 |
| PIC | Chyntia (Lead) · Jona (logic) |
| Referensi desain | `07_extras/referensi_desain/uiux_paska_jaya.pdf` (lihat `04_plans/SCREEN_MAPPING.md`) |

Menu Karyawan: **Pengantaran** (badge merah = jumlah tugas baru), Profil, Keluar.
## Daftar Pengantaran
"Pengantaran — Bon yang dikirim kasir, diurutkan berdasarkan jadwal terdekat."
- 3 kartu: **Pengantaran baru** (n), **Sedang berjalan** (n), **Selesai** (n).
- Tab **Akan diantar (n)** / **Selesai (n)**.
- Kartu bon: badge **BERIKUTNYA** (jadwal terdekat) atau **Baru** (merah, belum dibuka), badge status; nama pelanggan, alamat (ikon pin), **DAFTAR BARANG** (nama ... qty satuan), jadwal, **Catatan kasir**, tombol **Detail & Ubah Status →**. Tab Selesai: tombol **Lihat Detail →**.
- Karyawan hanya melihat bon yang ditugaskan kepadanya.
## Detail Pengantaran
Tombol **← Kembali**; judul "Detail Pengantaran — Bon {pelanggan} · Rp total"; status kanan.
- Kartu **Informasi bon**: Pelanggan, Alamat, Jadwal, Catatan kasir, tabel BARANG/JUMLAH.
- Kartu **Status pengantaran**: stepper 3 langkah **Sedang Disiapkan → Siap Berangkat/Sedang Diantar → Selesai**; **Foto bukti selesai diantar (wajib)** area unggah ("Belum ada foto") + **Pilih / ambil foto** (kamera mobile); tombol **Ubah Status → {langkah berikut}**, disabled sampai foto ada saat menuju Selesai; teks "Unggah foto bukti terlebih dahulu untuk menyelesaikan."
- Setiap perubahan → `log_status_pengiriman` + tampil di Daftar Bon Kasir/Owner. Saat Selesai: `delivered_at`, foto tersimpan.
- Bon baru dibuka pertama kali menghapus penanda "Baru".
## Acceptance: [ ] hanya bon miliknya [ ] foto wajib [ ] status sinkron ke kasir/owner [ ] badge menu akurat.


## Data & Empty State
Semua data dari database (tanpa dummy). Saat tabel kosong tampilkan empty state, kartu bernilai 0, dan grafik bernilai 0 — tanpa error (ADR-012).
