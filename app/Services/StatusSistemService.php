<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\RolePengguna;
use App\Enums\StatusPengguna;
use App\Enums\StatusPengiriman;
use App\Enums\TipePenjualan;
use App\Models\AbsensiKaryawan;
use App\Models\Barang;
use App\Models\BonPiutang;
use App\Models\PenggunaanBahan;
use App\Models\PengirimanBarang;
use App\Models\Penjualan;
use App\Models\Role;
use App\Models\SystemConfig;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * Sumber seluruh angka pada Ringkasan Sistem Admin (F02).
 * Semua nilai dibaca dari database; database kosong menghasilkan 0 / "Belum pernah".
 */
class StatusSistemService
{
    /**
     * Empat kartu ringkasan: total + keterangan pendamping.
     *
     * @return array{
     *     pengguna: array{total: int, aktif: int},
     *     master_item: array{total: int, menipis: int},
     *     transaksi_keluar: array{total: int, bon: int},
     *     pengantaran: array{total: int, berjalan: int}
     * }
     */
    public function kartuRingkasan(): array
    {
        return [
            'pengguna' => [
                'total' => User::query()->count(),
                'aktif' => User::query()->where('status', StatusPengguna::Aktif->value)->count(),
            ],
            'master_item' => [
                'total' => Barang::query()->count(),
                'menipis' => Barang::query()->stokMenipis()->count(),
            ],
            // "Transaksi keluar" = penjualan langsung (Barang Keluar); bon dihitung terpisah (ADR-005).
            'transaksi_keluar' => [
                'total' => Penjualan::query()->where('tipe', TipePenjualan::Langsung->value)->count(),
                'bon' => BonPiutang::query()->count(),
            ],
            'pengantaran' => [
                'total' => PengirimanBarang::query()
                    ->where('status', '<>', StatusPengiriman::Dibatalkan->value)
                    ->count(),
                'berjalan' => PengirimanBarang::query()
                    ->whereIn('status', [
                        StatusPengiriman::DikirimKeKaryawan->value,
                        StatusPengiriman::SedangDisiapkan->value,
                        StatusPengiriman::SiapBerangkat->value,
                    ])
                    ->count(),
            ],
        ];
    }

    /**
     * Jumlah akun per role beserta lebar progress bar (relatif terhadap role terbanyak).
     *
     * @return list<array{nama: string, label: string, jumlah: int, persen: int}>
     */
    public function penggunaPerRole(): array
    {
        $idRole = Role::query()->pluck('id', 'nama');

        $jumlahPerRoleId = User::query()
            ->selectRaw('role_id, COUNT(*) AS jumlah')
            ->groupBy('role_id')
            ->pluck('jumlah', 'role_id');

        $baris = [];

        foreach (RolePengguna::cases() as $role) {
            $id = $idRole[$role->value] ?? null;

            $baris[] = [
                'nama' => $role->value,
                'label' => $role->label(),
                'jumlah' => (int) ($id !== null ? ($jumlahPerRoleId[$id] ?? 0) : 0),
                'persen' => 0,
            ];
        }

        $terbanyak = max(array_column($baris, 'jumlah'));

        foreach ($baris as &$item) {
            $item['persen'] = $terbanyak > 0 ? (int) round($item['jumlah'] / $terbanyak * 100) : 0;
        }

        return $baris;
    }

    /**
     * Status sistem: layanan, koneksi basis data, pencadangan terakhir, dan jumlah catatan.
     *
     * @return array{
     *     layanan: array{label: string, normal: bool},
     *     basis_data: array{label: string, terhubung: bool},
     *     pencadangan: string,
     *     kehadiran: int,
     *     bahan_harian: int
     * }
     */
    public function statusSistem(): array
    {
        $terhubung = $this->basisDataTerhubung();

        return [
            'layanan' => ['label' => 'Normal', 'normal' => true],
            'basis_data' => [
                'label' => $terhubung ? 'Terhubung' : 'Terputus',
                'terhubung' => $terhubung,
            ],
            'pencadangan' => $this->pencadanganTerakhir(),
            'kehadiran' => $terhubung ? AbsensiKaryawan::query()->count() : 0,
            'bahan_harian' => $terhubung ? PenggunaanBahan::query()->count() : 0,
        ];
    }

    /**
     * Cek koneksi basis data dengan query ringan.
     */
    public function basisDataTerhubung(): bool
    {
        try {
            DB::select('SELECT 1');

            return true;
        } catch (Throwable) {
            return false;
        }
    }

    /**
     * Label pencadangan terakhir dari `system_configs.backup_terakhir`
     * (mis. "Hari ini, 02.00", "Kemarin, 02.00", atau tanggal lengkap).
     */
    public function pencadanganTerakhir(): string
    {
        try {
            $nilai = trim((string) SystemConfig::nilai('backup_terakhir', ''));
        } catch (Throwable) {
            return 'Belum pernah';
        }

        if ($nilai === '') {
            return 'Belum pernah';
        }

        try {
            $waktu = Carbon::parse($nilai, 'Asia/Jakarta');
        } catch (Throwable) {
            return 'Belum pernah';
        }

        $jam = $waktu->format('H.i');

        return match (true) {
            $waktu->isToday() => "Hari ini, {$jam}",
            $waktu->isYesterday() => "Kemarin, {$jam}",
            default => tanggal_id($waktu).", {$jam}",
        };
    }
}
