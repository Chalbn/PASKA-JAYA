# Architecture Decision Records

| ADR | Judul |
|---|---|
| [ADR-001](ADR-001-stack-laravel-livewire.md) | Stack: Laravel + Livewire (bukan React/Inertia) |
| [ADR-002](ADR-002-database-mysql-penamaan-indonesia.md) | Database MySQL, penamaan Indonesia, 14 entitas laporan + 8 pendukung |
| [ADR-003](ADR-003-uang-integer-rupiah.md) | Nominal uang disimpan sebagai integer rupiah |
| [ADR-004](ADR-004-snapshot-detail-transaksi.md) | Snapshot nama, satuan, dan harga pada detail transaksi |
| [ADR-005](ADR-005-penjualan-terpadu-bon.md) | Penjualan terpadu: tipe langsung & bon, bon_piutang 1:1 |
| [ADR-006](ADR-006-stok-berkurang-saat-bon-dikirim.md) | Stok bon berkurang saat dikirim; pengiriman entitas sendiri |
| [ADR-007](ADR-007-logika-kritis-di-database.md) | Logika kritis di database: trigger, stored procedure, view |
| [ADR-008](ADR-008-kas-laci-dan-penutupan.md) | Saldo laci berbasis kas_laci + penutupan_laci |
| [ADR-009](ADR-009-rbac-roles-middleware-policy.md) | RBAC: tabel roles + middleware + Policy; Owner read-only |
| [ADR-010](ADR-010-kategori-otomatis.md) | Kategori barang otomatis |
| [ADR-011](ADR-011-penyimpanan-berkas.md) | Penyimpanan berkas: bukti pengiriman privat |
| [ADR-012](ADR-012-tanpa-data-dummy.md) | Tanpa data dummy; database dimulai kosong |
| [ADR-013](ADR-013-hak-akses-mysql.md) | Hak akses MySQL berlapis (least privilege) |

Format: Status · Tanggal · Context · Decision · Consequences · Alternatives. ADR baru wajib ditambahkan di sini dan dirujuk dari dokumen terkait.
