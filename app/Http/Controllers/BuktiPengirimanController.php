<?php

namespace App\Http\Controllers;

use App\Models\PengirimanBarang;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class BuktiPengirimanController extends Controller
{
    /**
     * Sajikan foto bukti pengiriman dari disk privat.
     * Policy: owner, kasir, atau karyawan pemilik.
     */
    public function __invoke(int $id): Response
    {
        $pengiriman = PengirimanBarang::findOrFail($id);

        Gate::authorize('lihatBukti', $pengiriman);

        if (! $pengiriman->foto_bukti || ! Storage::disk('local')->exists($pengiriman->foto_bukti)) {
            abort(404, 'Foto bukti tidak ditemukan.');
        }

        return Storage::disk('local')->response($pengiriman->foto_bukti);
    }
}

