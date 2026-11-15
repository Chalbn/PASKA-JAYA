# ADR-012: Tanpa data dummy; database dimulai kosong

**Status**: Accepted · **Tanggal**: 14 November 2026 · **Diputuskan oleh**: Kelompok 7 (PASKA JAYA)

## Context
Tim menegaskan seluruh data harus asli dari database yang akan dibuat; contoh angka di PDF hanya tampilan.

## Decision
Tidak ada seeder demo. Seeder hanya `RoleSeeder` (4 role) dan `ConfigSeeder` (konfigurasi sistem). Admin pertama dibuat dengan `php artisan app:buat-admin`. Semua halaman wajib punya empty state. Factory hanya untuk test.

## Consequences
Setup awal butuh input data oleh tim. UI harus tahan data kosong (grafik 0, kartu 0).

## Alternatives Considered
- Seeder demo (ditolak oleh tim).
