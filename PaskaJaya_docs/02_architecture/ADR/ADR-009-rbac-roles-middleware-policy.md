# ADR-009: RBAC: tabel roles + middleware + Policy; Owner read-only

**Status**: Accepted · **Tanggal**: 14 November 2026 · **Diputuskan oleh**: Kelompok 7 (PASKA JAYA)

## Context
Empat role tetap: admin, owner, kasir, karyawan (laporan tujuan #4: Multi-Role Access Control).

## Decision
`users.role_id → roles`. Middleware `role:<nama>` pada grup route; Policy untuk aksi sensitif. Owner hanya-lihat pada Bon & Kehadiran (403 di server). Admin tidak dapat menonaktifkan/mengubah/menghapus dirinya; admin aktif terakhir dijaga trigger T7.

## Consequences
Tidak memakai paket permission pihak ketiga. Menambah role = migration + penyesuaian middleware/menu.

## Alternatives Considered
- spatie/laravel-permission (ditolak: berlebihan untuk 4 role tetap).
