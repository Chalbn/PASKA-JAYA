<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AnggotaTim;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class TimController extends Controller
{
    public function index(Request $request): View|JsonResponse
    {
        Gate::authorize('viewAny', AnggotaTim::class);

        $daftarKartu = AnggotaTim::query()
            ->orderBy('urutan')
            ->orderBy('id')
            ->get()
            ->map(function (AnggotaTim $baris, int $index): array {
                $slot = (int) ($baris->urutan ?: ($index + 1));
                $fotoUrl = null;

                if ($baris->foto) {
                    $fotoUrl = str_starts_with($baris->foto, 'images/')
                        ? asset($baris->foto)
                        : Storage::disk('public')->url($baris->foto);
                } else {
                    foreach (['jpg', 'jpeg', 'png', 'webp'] as $ext) {
                        $rel = "images/tim/tim-{$slot}.{$ext}";
                        if (file_exists(public_path($rel))) {
                            $fotoUrl = asset($rel);
                            break;
                        }
                    }
                }

                return [
                    'slot' => $slot,
                    'nama' => $baris->nama,
                    'jabatan' => $baris->jabatan,
                    'inisial' => $baris->inisial ?: inisial($baris->nama),
                    'foto' => $baris->foto,
                    'foto_url' => $fotoUrl,
                ];
            })
            ->all();

        $fileFotoTim = collect([
            'images/foto-tim-bersama.jpg',
            'images/foto-tim-bersama.png',
            'images/foto-tim-bersama.jpeg',
            'images/foto-tim.jpg',
            'images/foto-tim.png',
        ])->first(fn (string $path) => file_exists(public_path($path)));

        $fotoBersamaUrl = $fileFotoTim ? asset($fileFotoTim) : null;
        $fotoBersama = $fileFotoTim;
        $anggota = collect($daftarKartu);

        if ($request->wantsJson()) {
            return response()->json([
                'anggota' => $anggota,
                'foto_bersama' => $fotoBersama,
                'daftar_kartu' => $daftarKartu,
                'foto_bersama_url' => $fotoBersamaUrl,
            ]);
        }

        return view('admin.tim.index', compact(
            'daftarKartu',
            'fotoBersamaUrl',
            'anggota',
            'fotoBersama'
        ));
    }
}