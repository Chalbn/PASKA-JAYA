# PASKA JAYA — Git Workflow

## Branch Strategy
```
main      ← stabil, siap deploy (dilindungi)
 └─ develop   ← integrasi semua fitur (dilindungi)
     ├─ feat/<nama-fitur>      ← 1 branch = 1 pemilik
     ├─ fix/<nama-bug>
     ├─ release/1.0.0          ← QA + UAT dengan mitra
     └─ hotfix/<nama>          ← dari main bila bug kritis di produksi
```
### Branch Rules
| Branch | Siapa yang merge | Aturan |
|---|---|---|
| `main` | Tech Lead (Vascha) | Hanya dari `release/*` atau `hotfix/*`; tag `vX.Y.Z` |
| `develop` | Reviewer via PR | Wajib PR, ≥1 review, test hijau |
| `feat/*`, `fix/*` | Pemilik branch | Dibuat dari `develop`; hapus setelah merge |

### Branch Fitur yang Sudah Ditentukan
| Branch | Pemilik | Isi |
|---|---|---|
| `feat/setup-project` | Vascha | Setup Laravel, Livewire, Tailwind, Vite, Pest |
| `feat/database-schema` | Vascha, Patricia | Migration 22 tabel, model, enum |
| `feat/database-routines` | Vascha | View, trigger, stored procedure, privilege |
| `feat/design-system-layout` | Jeli | Komponen UI, layout per role, logo, brand |
| `feat/auth` | Vascha | Login, register, OTP, middleware role |
| `feat/profile` | Chyntia | Profil semua role |
| `feat/admin-panel` | Vascha | Ringkasan Sistem, Manajemen Pengguna |
| `feat/admin-team` | Jeli | Halaman Tim |
| `feat/master-item` | Patricia (+Jeli UI) | Master Item kartu/tabel/restock |
| `feat/kasir-sales` | Patricia (+Chyntia UI) | Beranda kasir, Barang Keluar |
| `feat/kasir-balance` | Patricia (+Chyntia UI) | Saldo, grafik mingguan, riwayat laci |
| `feat/bon-kasir` | Jona (+Chyntia UI) | Catat Bon, Daftar Bon kasir |
| `feat/bon-owner-readonly` | Jona | Daftar Bon Owner |
| `feat/delivery-karyawan` | Chyntia (+Jona logic) | Pengantaran + foto bukti |
| `feat/daily-materials` | Chyntia | Bahan Harian |
| `feat/attendance` | Jona (+Chyntia UI) | Kehadiran |
| `feat/owner-dashboard` | Patricia (+Jeli UI) | Ringkasan Owner + grafik 7 hari |
| `feat/owner-sales-report` | Patricia | Laporan Barang Keluar + CSV |
| `feat/qa-polish` | Jona | QA, perbaikan, empty state |

Pekerjaan di luar daftar → buat branch baru dari `develop`.

## Commit Convention (Conventional Commits)
| Prefix | Dipakai untuk |
|---|---|
| `feat` | fitur baru |
| `fix` | perbaikan bug |
| `docs` | dokumentasi |
| `refactor` | refactor tanpa ubah perilaku |
| `test` | menambah test |
| `chore` | konfigurasi/tooling |
| `db` | migration/trigger/procedure |
### Contoh
```
feat(kasir): barang keluar memotong stok dan menambah kas laci
db(trigger): tambah trg_bon_piutang_bu_status
fix(bon): hitung sisa dan status lewat tempo
docs: update progress tracker
```

## Workflow Harian
### Memulai Task
```bash
git checkout develop && git pull origin develop
git checkout feat/kasir-sales
git merge develop        # ambil perubahan terbaru
```
### Menyelesaikan Task
```bash
./vendor/bin/pint && php artisan test
git add . && git commit -m "feat(kasir): ..."
git push origin feat/kasir-sales
```
Lalu buat **Pull Request ke `develop`**.
### PR Title
`feat(kasir): barang keluar` — ringkas, pakai prefix.
### PR Description Template
```
## Ringkasan
## Fitur terkait (Fxx) & Screen
## Perubahan DB (migration/trigger/procedure)?
## Cara uji
## Checklist
- [ ] Test hijau  - [ ] Pint  - [ ] Empty state  - [ ] Akses role diuji  - [ ] Tracker diupdate
```

## Review Turnaround
Reviewer menanggapi ≤ 24 jam; bila macet, ping Tech Lead di grup. Minimal 1 reviewer dari anggota lain; perubahan DB wajib direview Vascha/Patricia.

## Resolving Conflicts
`git fetch origin && git merge origin/develop` di branch Anda → selesaikan konflik di VS Code (Accept Current/Incoming/Both) → jalankan test → commit. Migration bentrok: ubah timestamp nama file, jangan edit milik orang lain.

## Initial Repository Setup (Tech Lead Only)
1. Buat repo **private** di GitHub. 2. `main` + `develop`. 3. Branch protection (PR wajib, review, status check). 4. Undang anggota sebagai collaborator. 5. Tambahkan `.gitignore` (`.env`, `vendor`, `node_modules`, `storage/app/private`, `*.sql` dump).

## Git Hooks (Opsional)
`pre-commit`: Pint + test cepat.

## FAQ
**Kenapa ada `develop`?** Agar `main` selalu stabil dan siap demo/dosen.
**Kapan hotfix?** Bug kritis di produksi (mis. stok salah) — dari `main`, merge ke `main` dan `develop`.
**Siapa boleh push ke `main`?** Hanya Tech Lead lewat release/hotfix.
**Perlu data dummy untuk test?** Pakai factory di test saja; jangan di database bersama.
