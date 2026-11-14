# F14 — Karyawan: Pengantaran
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
- Setiap perubahan → `bon_status_logs` + tampil di Daftar Bon Kasir/Owner. Saat Selesai: `delivered_at`, foto tersimpan.
- Bon baru dibuka pertama kali menghapus penanda "Baru".
## Acceptance: [ ] hanya bon miliknya [ ] foto wajib [ ] status sinkron ke kasir/owner [ ] badge menu akurat.
