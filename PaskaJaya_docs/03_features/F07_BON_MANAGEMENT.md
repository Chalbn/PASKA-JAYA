# F07 — Daftar Bon (Owner read-only · Kasir penuh)

| Atribut | Isi |
|---|---|
| Role | owner, kasir |
| URL | `/owner/daftar-bon`, `/kasir/daftar-bon` |
| Komponen Livewire | `Owner\DaftarBon`, `Kasir\DaftarBon` |
| Tabel/Objek DB | bon_piutang, penjualan, detail_penjualan, pembayaran_bon, pengiriman_barang, log_status_pengiriman |
| ADR | ADR-005, ADR-006 |
| PIC | Jona (Lead) · Chyntia (UI) |
| Referensi desain | `07_extras/referensi_desain/uiux_paska_jaya.pdf` (lihat `04_plans/SCREEN_MAPPING.md`) |

## Umum
- Judul "Daftar Bon". Filter pil **Semua | Belum lunas | Lunas**.
- Alert: Owner — "Mode baca saja: pembayaran dan pengiriman bon hanya dapat dilakukan oleh Kasir." + badge **Hanya Lihat**; info "Perubahan status dari Karyawan otomatis tampil di kolom Status Pengantaran."
- Kolom: NO, PELANGGAN (+ "Jatuh tempo …" + badge **Lewat tempo** bila perlu; kontraktor ditulis "Pak Hendra (Kontraktor)"), RINGKASAN BARANG (chip "Semen Gresik 50kg ×20"; maks 3 chip lalu "+n"), TOTAL (+ "Sisa Rp …" bila belum lunas), STATUS PEMBAYARAN (Belum Lunas kuning / ✓ Lunas navy), STATUS PENGANTARAN (badge + "Budi Santoso" di bawah; "Belum Dikirim"), aksi.
## Owner
- Tombol **Lihat Detail** → modal **Detail Bon**: header pelanggan + badge status kirim, alamat (ikon pin), tabel BARANG/JML/SUBTOTAL, ringkasan Total bon / Terbayar / Sisa, kotak Karyawan, Jadwal, Catatan, dan kolom kanan **ALUR STATUS** (timeline: Belum Dikirim → Dikirim ke Karyawan → Sedang Disiapkan → Siap Berangkat/Sedang Diantar → Selesai, tiap langkah dengan tanggal+jam).
- Bila Selesai: tampil badge ✓ Selesai dan **Foto bukti selesai diantar**.
## Kasir (aksi per baris)
- **Kirim ke Karyawan** (modal pilih karyawan aktif, jadwal, catatan) — aktif bila Belum Dikirim; memanggil `sp_kirim_bon` (buat `pengiriman_barang` + `detail_pengiriman`, potong stok, catat `log_status_pengiriman`; ADR-006).
- **Ganti Karyawan** (bila sudah dikirim, belum selesai) · **Batalkan Pengiriman** (kembali Belum Dikirim, stok kembali).
- **Tandai Lunas** (pilih metode → insert `pembayaran_bon` sebesar sisa → trigger T5 update `total_terbayar` & masuk `kas_laci`) · **Bayar Sebagian** (nominal ≤ sisa + metode).
- **Detail** (modal sama dengan Owner) · **Hapus** (konfirmasi; hanya bila belum dikirim & belum ada pembayaran).
- Tombol non-relevan tampil pudar/disabled (mis. Tandai Lunas pada bon Lunas).
- Alert Kasir: "Bon hanya terlihat oleh Kasir dan Owner. Bon baru otomatis berstatus Belum Dikirim."
## Acceptance
- [ ] Owner 403 untuk semua POST aksi. - [ ] Sisa/lewat tempo benar. - [ ] Stok & saldo laci konsisten. - [ ] Timeline lengkap dengan jam. - [ ] Foto bukti tampil.


## Data & Empty State
Semua data dari database (tanpa dummy). Saat tabel kosong tampilkan empty state, kartu bernilai 0, dan grafik bernilai 0 — tanpa error (ADR-012).
