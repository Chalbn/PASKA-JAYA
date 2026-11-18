<?php

namespace App\Services;

use App\Enums\RolePengguna;
use App\Enums\StatusPengguna;
use App\Models\AbsensiKaryawan;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AbsensiService
{
    /**
     * Mengambil daftar karyawan aktif.
     */
    public function getDaftarKaryawanAktif()
    {
        return User::query()
            ->whereHas('role', function ($query) {
                $query->where('nama', RolePengguna::Karyawan->value);
            })
            ->where('status', StatusPengguna::Aktif->value)
            ->orderBy('nama')
            ->get(['id', 'nama']);
    }

    /**
     * Mengambil daftar kehadiran dengan filter.
     */
    public function getDaftarAbsensi(array $filter = []): LengthAwarePaginator
    {
        return AbsensiKaryawan::query()
            ->with(['karyawan', 'detailPekerjaan'])
            ->when($filter['cari'] ?? null, function ($query, $cari) {
                $query->where(function ($subQuery) use ($cari) {
                    $subQuery->whereHas('karyawan', function ($userQuery) use ($cari) {
                        $userQuery->where('nama', 'like', "%{$cari}%");
                    })->orWhereHas('detailPekerjaan', function ($detailQuery) use ($cari) {
                        $detailQuery->where('deskripsi', 'like', "%{$cari}%");
                    });
                });
            })
            ->when($filter['karyawan_id'] ?? null, function ($query, $karyawanId) {
                $query->where('karyawan_id', $karyawanId);
            })
            ->when($filter['tanggal'] ?? null, function ($query, $tanggal) {
                $query->whereDate('tanggal', $tanggal);
            })
            ->orderByDesc('tanggal')
            ->orderByDesc('id')
            ->paginate(10);
    }

    /**
     * Menyimpan kehadiran beserta detail pekerjaan.
     */
    public function simpan(array $data, int $pembuatId): AbsensiKaryawan
    {
        return DB::transaction(function () use ($data, $pembuatId) {
            $this->pastikanKaryawanAktif((int) $data['karyawan_id']);

            $sudahAda = AbsensiKaryawan::query()
                ->where('karyawan_id', $data['karyawan_id'])
                ->whereDate('tanggal', $data['tanggal'])
                ->exists();

            if ($sudahAda) {
                throw ValidationException::withMessages([
                    'karyawan_id' => 'Karyawan tersebut sudah memiliki catatan kehadiran pada tanggal ini.',
                ]);
            }

            $absensi = AbsensiKaryawan::create([
                'karyawan_id' => $data['karyawan_id'],
                'tanggal' => $data['tanggal'],
                'shift' => $data['shift'] ?? null,
                'dibuat_oleh' => $pembuatId,
            ]);

            foreach ($data['pekerjaan'] as $index => $deskripsi) {
                $absensi->detailPekerjaan()->create([
                    'deskripsi' => trim($deskripsi),
                    'urutan' => $index + 1,
                ]);
            }

            return $absensi->load(['karyawan', 'detailPekerjaan']);
        });
    }

    /**
     * Memperbarui kehadiran beserta seluruh detail pekerjaan.
     */
    public function update(int $id, array $data): AbsensiKaryawan
    {
        return DB::transaction(function () use ($id, $data) {
            $absensi = AbsensiKaryawan::query()
                ->lockForUpdate()
                ->findOrFail($id);

            $this->pastikanKaryawanAktif((int) $data['karyawan_id']);

            $sudahAda = AbsensiKaryawan::query()
                ->where('karyawan_id', $data['karyawan_id'])
                ->whereDate('tanggal', $data['tanggal'])
                ->where('id', '!=', $id)
                ->exists();

            if ($sudahAda) {
                throw ValidationException::withMessages([
                    'karyawan_id' => 'Karyawan tersebut sudah memiliki catatan kehadiran pada tanggal ini.',
                ]);
            }

            $absensi->update([
                'karyawan_id' => $data['karyawan_id'],
                'tanggal' => $data['tanggal'],
                'shift' => $data['shift'] ?? null,
            ]);

            $absensi->detailPekerjaan()->delete();

            foreach ($data['pekerjaan'] as $index => $deskripsi) {
                $absensi->detailPekerjaan()->create([
                    'deskripsi' => trim($deskripsi),
                    'urutan' => $index + 1,
                ]);
            }

            return $absensi->load(['karyawan', 'detailPekerjaan']);
        });
    }

    /**
     * Menghapus catatan kehadiran.
     */
    public function hapus(int $id): void
    {
        DB::transaction(function () use ($id) {
            $absensi = AbsensiKaryawan::query()
                ->lockForUpdate()
                ->findOrFail($id);

            $absensi->delete();
        });
    }

    /**
     * Memastikan user merupakan karyawan aktif.
     */
    private function pastikanKaryawanAktif(int $karyawanId): void
    {
        $valid = User::query()
            ->whereKey($karyawanId)
            ->whereHas('role', function ($query) {
                $query->where('nama', RolePengguna::Karyawan->value);
            })
            ->where('status', StatusPengguna::Aktif->value)
            ->exists();

        if (! $valid) {
            throw ValidationException::withMessages([
                'karyawan_id' => 'Karyawan tidak ditemukan atau sudah tidak aktif.',
            ]);
        }
    }
}
