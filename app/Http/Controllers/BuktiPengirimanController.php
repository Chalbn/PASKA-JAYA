<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class BuktiPengirimanController extends Controller
{
    /**
     * Mengunduh/menampilkan foto bukti pengiriman privat (ADR-011).
     * Mengakses view v_pengantaran_karyawan agar sesuai izin pj_app.
     */
    public function __invoke(Request $request, int $id): BinaryFileResponse
    {
        $pengiriman = DB::table('pengiriman_barang')
            ->select(['id', 'karyawan_id', 'foto_bukti'])
            ->where('id', $id)
            ->first();

        abort_if($pengiriman === null || empty($pengiriman->foto_bukti), 404, 'Bukti pengiriman tidak ditemukan.');

        $user = Auth::user();
        abort_if($user === null, 403);

        $role = DB::table('roles')->where('id', $user->role_id)->value('nama');
        $boleh = in_array($role, ['owner', 'kasir', 'admin'], true)
            || ($role === 'karyawan' && (int) $pengiriman->karyawan_id === (int) $user->id);

        abort_unless($boleh, 403, 'Anda tidak memiliki akses ke berkas ini.');

        $disk = Storage::disk('local');
        abort_unless($disk->exists($pengiriman->foto_bukti), 404, 'Berkas tidak ditemukan di penyimpanan.');

        return response()->file($disk->path($pengiriman->foto_bukti));
    }
}
