# UI/UX Guidelines (diturunkan dari desain PDF)
## Layout aplikasi
- **Sidebar kiri** gradien biru (navy `#1E3A8A` → biru `#3B5BDB`), lebar ±220px: logo "PJ" kotak putih + teks **PASKA JAYA / TOKO BAHAN BANGUNAN**, label menu kecil kapital ("MENU ADMIN", "MENU OWNER", "MENU KASIR", "MENU KARYAWAN"), item aktif = pil putih teks biru, tombol **Keluar** di bawah.
- **Topbar** gradien biru: ikon halaman, tanggal ("Sabtu, 14 November 2026") + judul halaman; kanan: avatar inisial + nama + role.
- **Konten**: latar putih-kebiruan (`#EEF3FF` → putih), judul halaman bold navy + subjudul abu.
- **Kartu**: putih/kaca, radius 16–20px, bayangan lembut, border tipis biru muda.
## Token
- Warna: primary `#1E40AF`/`#2F4FD8`, aksen cyan muda untuk kartu statistik, kuning (belum lunas), hijau (selesai), merah (kritis/hapus `#C81E1E`).
- Font: **Plus Jakarta Sans** (fallback DM Sans, system-ui).
- Badge: pil; "Lunas" = biru tua + ✓; "Belum Lunas" = kuning; "Selesai" = biru tua + ✓; "Kritis"/"Menipis" = merah muda; "Lewat tempo" = merah.
- Metode: `CASH` pil navy solid; `TRANSFER` pil biru muda.
## Pola
- Kartu statistik 4 kolom di atas halaman.
- Modal: judul kiri, X kanan, tombol **Batal** (outline) + **Simpan** (primary); konfirmasi hapus: tombol merah "Ya, hapus".
- Tabel: header kapital kecil abu, baris zebra halus, aksi ikon (pensil, tempat sampah).
- Toggle **Kartu | Tabel** (Master Item). Filter pil **Semua | Belum lunas | Lunas** (Bon).
- Timeline vertikal untuk "Alur Status" (lingkaran ✓ biru, garis penghubung).
- Empty state: teks abu di dalam kotak putus-putus ("Belum ada barang. Pilih barang lalu klik Tambah.").
- Badge merah di menu Pengantaran (karyawan) = jumlah tugas baru.
- Responsif: sidebar jadi drawer < 1024px; tabel scroll horizontal.
- Aksesibilitas: kontras AA, fokus terlihat, label field jelas.
## Halaman Auth
Split-screen: kiri foto toko (Toko Bangunan Indosatu) overlay biru + headline **"Every sack, bar, and delivery in one place."** + subteks "Stock, cashier, and delivery tracking for your building-material store, all in a single system."; kanan kartu form (Welcome back / Create Account). Tanpa kotak demo account.
