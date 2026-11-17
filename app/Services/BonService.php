<?php

namespace App\Services;

use App\Models\Penjualan;
use App\Models\DetailPenjualan;
use App\Models\BonPiutang;
use App\Models\LogStatusPengiriman;
use App\Models\Pelanggan;
use App\Models\Barang;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class BonService
{
    public function buat(array $data)
    {
        return DB::transaction(function () use ($data) {
            // 1. Cari atau buat data pelanggan berdasarkan nama
            $pelanggan = Pelanggan::firstOrCreate(
                ['nama' => $data['nama_pelanggan']],
                [
                    'alamat' => $data['alamat'] ?? null,
                    'kontak' => $data['kontak'] ?? null,
                ]
            );

            // 2. Generate kode bon unik
            $tanggal = $data['tanggal'] ?? Carbon::now()->toDateString();
            $jumlahBonHariIni = BonPiutang::whereDate('created_at', Carbon::today())->count() + 1;
            $kodeBon = 'BON-' . Carbon::parse($tanggal)->format('Ymd') . '-' . str_pad($jumlahBonHariIni, 4, '0', STR_PAD_LEFT);

            // 3. Hitung total nominal
            $totalNominal = collect($data['items'])->sum(function ($item) {
                return $item['jumlah'] * $item['harga_satuan'];
            });

            // 4. Insert ke tabel penjualan (tipe = 'bon')
            $penjualan = Penjualan::create([
                'tipe' => 'bon',
                'pelanggan_id' => $pelanggan->id,
                'user_id' => Auth::id(),
                'total' => $totalNominal,
                'tanggal' => $tanggal,
                'waktu_transaksi' => now(),
            ]);

            // 5. Insert detail penjualan dengan menyertakan satuan
            foreach ($data['items'] as $item) {
                $barangModel = Barang::find($item['master_item_id']);

                DetailPenjualan::create([
                    'penjualan_id' => $penjualan->id,
                    'barang_id' => $item['master_item_id'],
                    'nama_barang' => $item['nama_barang'],
                    'satuan' => $barangModel ? $barangModel->satuan : 'sak',
                    'jumlah' => $item['jumlah'],
                    'harga_satuan' => $item['harga_satuan'],
                    'subtotal' => $item['jumlah'] * $item['harga_satuan'],
                ]);
            }

            // 6. Insert ke tabel bon_piutang
            $jatuhTempo = $data['jatuh_tempo'] ?? Carbon::parse($tanggal)->addDays(30)->toDateString();
            $bonPiutang = BonPiutang::create([
                'penjualan_id' => $penjualan->id,
                'pelanggan_id' => $pelanggan->id,
                'kode_bon' => $kodeBon,
                'tanggal_bon' => $tanggal,
                'jatuh_tempo' => $jatuhTempo,
                'total_nominal' => $totalNominal,
                'total_tagihan' => $totalNominal,
                'total_terbayar' => 0,
                'status_pembayaran' => 'belum_lunas',
                'dibuat_oleh' => Auth::id(),
            ]);

            // 7. Catat log status pengiriman awal (Menyertakan bon_id)
            LogStatusPengiriman::create([
                'bon_id' => $bonPiutang->id, // Ditambahkan agar tidak error field 'bon_id'
                'penjualan_id' => $penjualan->id,
                'status' => 'belum_dikirim',
                'keterangan' => 'Bon berhasil dicatat, menunggu pengiriman oleh kasir.',
                'user_id' => Auth::id(),
            ]);

            return $bonPiutang;
        });
    }
}