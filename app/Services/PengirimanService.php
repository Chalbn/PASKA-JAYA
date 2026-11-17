<?php

namespace App\Services;

use App\Enums\StatusPengiriman;
use App\Models\LogStatusPengiriman;
use App\Models\PengirimanBarang;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class PengirimanService
{
    /**
     * Status berikutnya dalam alur pengiriman karyawan.
     * Sedang Disiapkan → Siap Berangkat → Selesai
     */
    public function statusBerikutnya(StatusPengiriman|string $status): ?StatusPengiriman
    {
        $current = $status instanceof StatusPengiriman ? $status : StatusPengiriman::from($status);

        return match ($current) {
            StatusPengiriman::DikirimKeKaryawan => StatusPengiriman::SedangDisiapkan,
            StatusPengiriman::SedangDisiapkan => StatusPengiriman::SiapBerangkat,
            StatusPengiriman::SiapBerangkat => StatusPengiriman::Selesai,
            default => null,
        };
    }

    /**
     * Label tombol untuk status berikutnya.
     */
    public function labelStatusBerikutnya(StatusPengiriman|string $status): string
    {
        $next = $this->statusBerikutnya($status);

        return $next ? 'Ubah Status → '.$next->label() : '';
    }

    /**
     * Ubah status pengiriman dan catat log.
     * Trigger T6 akan menjaga aturan foto wajib saat selesai.
     *
     * @throws \Exception
     */
    public function ubahStatus(PengirimanBarang $pengiriman, StatusPengiriman|string $statusBaru, User $user, ?string $fotoBuktiPath = null): void
    {
        $statusEnum = $statusBaru instanceof StatusPengiriman ? $statusBaru : StatusPengiriman::from($statusBaru);

        DB::transaction(function () use ($pengiriman, $statusEnum, $user, $fotoBuktiPath) {
            $data = ['status' => $statusEnum->value];

            if ($fotoBuktiPath !== null) {
                $data['foto_bukti'] = $fotoBuktiPath;
            }

            if ($statusEnum === StatusPengiriman::Selesai && empty($pengiriman->waktu_selesai)) {
                $data['waktu_selesai'] = now();
            }

            $pengiriman->update($data);

            LogStatusPengiriman::create([
                'bon_id' => $pengiriman->bon_id,
                'pengiriman_id' => $pengiriman->id,
                'status' => $statusEnum->value,
                'user_id' => $user->id,
                'catatan' => 'Status diubah ke '.$statusEnum->label().' oleh '.$user->nama,
            ]);
        });
    }

    /**
     * Tandai pengiriman sudah dibaca (hapus badge "Baru").
     */
    public function tandaiDibaca(PengirimanBarang $pengiriman): void
    {
        if ($pengiriman->dibaca_pada === null) {
            $pengiriman->update(['dibaca_pada' => now()]);
        }
    }

    /**
     * Hitung jumlah pengiriman baru (belum dibaca) untuk karyawan.
     */
    public function jumlahBaru(int $karyawanId): int
    {
        return PengirimanBarang::where('karyawan_id', $karyawanId)
            ->whereNull('dibaca_pada')
            ->whereNotIn('status', [
                StatusPengiriman::Selesai->value,
                StatusPengiriman::Dibatalkan->value,
            ])
            ->count();
    }
}
