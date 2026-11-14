# ADR-006 Role via enum + middleware

**Status**: Accepted · 2026-11-14

## Konteks
Hanya 4 role tetap.

## Keputusan
Kolom users.role (enum) + middleware EnsureRole + Policy.

## Konsekuensi
Tidak perlu paket permission.
