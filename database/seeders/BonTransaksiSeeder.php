<?php

namespace Database\Seeders;

use App\Models\Barang;
use App\Models\Pelanggan;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BonTransaksiSeeder extends Seeder
{
    public function run(): void
    {
        if (DB::table('penjualan')->count() > 0) {
            return;
        }

        $kasir = User::whereHas('role', fn ($q) => $q->where('nama', 'kasir'))->firstOrFail();
        $pelangganMap = Pelanggan::pluck('id', 'nama');
        $barangMap = Barang::all()->keyBy('nama_barang');

        $bonList = [
            // Bon Lunas
            [
                'pelanggan' => 'HKBP Hutanamora (Lestari Nababan)',
                'alamat' => 'Hutanamora',
                'tanggal' => '2026-11-09',
                'jatuh_tempo' => '2026-12-09',
                'lunas' => true,
                'metode' => 'transfer',
                'catatan' => 'Transfer BNI - Lestari Nababan',
                'items' => [
                    ['nama' => 'Semen Garuda 40kg', 'qty' => 10, 'harga' => 85000],
                    ['nama' => 'Pasir', 'qty' => 16, 'harga' => 160000],
                    ['nama' => 'Semen Padang 50kg', 'qty' => 33, 'harga' => 90000],
                    ['nama' => 'Batu Padas', 'qty' => 7, 'harga' => 270000],
                ],
            ],
            [
                'pelanggan' => 'A. Adi',
                'alamat' => 'Lumban Ina-ina',
                'tanggal' => '2026-11-12',
                'jatuh_tempo' => '2026-12-12',
                'lunas' => true,
                'metode' => 'cash',
                'catatan' => 'Bon lunas (Diantar hari ini)',
                'items' => [
                    ['nama' => 'Batu Bata', 'qty' => 1000, 'harga' => 900],
                    ['nama' => 'Semen Garuda 40kg', 'qty' => 10, 'harga' => 85000],
                    ['nama' => 'Pasir', 'qty' => 3, 'harga' => 170000],
                    ['nama' => 'Seng 7/11', 'qty' => 15, 'harga' => 70000],
                    ['nama' => 'Paku Seng', 'qty' => 2, 'harga' => 45000],
                    ['nama' => 'Paku 3"', 'qty' => 3, 'harga' => 25000],
                    ['nama' => 'Paku 4"', 'qty' => 1, 'harga' => 25000],
                    ['nama' => 'Paku 2"', 'qty' => 2, 'harga' => 25000],
                    ['nama' => 'Batu Padas', 'qty' => 2, 'harga' => 240000],
                ],
            ],
            [
                'pelanggan' => 'Eda Yasmin',
                'alamat' => 'Sipultak Dolok',
                'tanggal' => '2026-11-12',
                'jatuh_tempo' => '2026-12-12',
                'lunas' => true,
                'metode' => 'cash',
                'catatan' => 'Bon lunas faktur Semen Padang',
                'items' => [
                    ['nama' => 'Canal', 'qty' => 8, 'harga' => 125000],
                    ['nama' => 'Reng Baja', 'qty' => 5, 'harga' => 80000],
                    ['nama' => 'Seng Deck 3.5m', 'qty' => 8, 'harga' => 323000],
                    ['nama' => 'Semen Garuda 40kg', 'qty' => 30, 'harga' => 85000],
                    ['nama' => 'Batu Bata', 'qty' => 300, 'harga' => 900],
                    ['nama' => 'Pasir', 'qty' => 7, 'harga' => 160000],
                    ['nama' => 'Besi 10 mili', 'qty' => 8, 'harga' => 85000],
                    ['nama' => 'Besi 5 mili', 'qty' => 5, 'harga' => 30000],
                    ['nama' => 'Kawat Ikat', 'qty' => 1, 'harga' => 30000],
                    ['nama' => 'Paku 3"', 'qty' => 1, 'harga' => 25000],
                ],
            ],
            // Bon Piutang (Belum Lunas)
            [
                'pelanggan' => 'Op. Ayu',
                'alamat' => 'Lumban Julu',
                'tanggal' => '2026-10-27',
                'jatuh_tempo' => '2026-11-27',
                'lunas' => false,
                'metode' => null,
                'catatan' => null,
                'items' => [
                    ['nama' => 'Cat Avitex Putih', 'qty' => 2, 'harga' => 1500000],
                    ['nama' => 'Pasir', 'qty' => 8, 'harga' => 170000],
                ],
            ],
            [
                'pelanggan' => 'Op. Ayu',
                'alamat' => 'Lumban Julu',
                'tanggal' => '2026-11-04',
                'jatuh_tempo' => '2026-12-04',
                'lunas' => false,
                'metode' => null,
                'catatan' => null,
                'items' => [
                    ['nama' => 'Bohlam 5W', 'qty' => 2, 'harga' => 30000],
                    ['nama' => 'Box MCB', 'qty' => 1, 'harga' => 30000],
                    ['nama' => 'Keramik 50x50', 'qty' => 92, 'harga' => 100000],
                ],
            ],
            [
                'pelanggan' => 'Ka Lilis',
                'alamat' => 'Lumban Julu',
                'tanggal' => '2026-10-01',
                'jatuh_tempo' => '2026-11-01',
                'lunas' => false,
                'metode' => null,
                'catatan' => null,
                'items' => [
                    ['nama' => 'Besi 5 mili', 'qty' => 10, 'harga' => 30000],
                    ['nama' => 'Kawat Ikat', 'qty' => 2, 'harga' => 30000],
                    ['nama' => 'Semen Garuda 40kg', 'qty' => 20, 'harga' => 80000],
                    ['nama' => 'Besi 8 mili', 'qty' => 20, 'harga' => 59000],
                ],
            ],
            [
                'pelanggan' => 'Ka Lilis',
                'alamat' => 'Lumban Julu',
                'tanggal' => '2026-10-21',
                'jatuh_tempo' => '2026-11-21',
                'lunas' => false,
                'metode' => null,
                'catatan' => null,
                'items' => [
                    ['nama' => 'Semen Garuda 40kg', 'qty' => 20, 'harga' => 85000],
                    ['nama' => 'Pasir', 'qty' => 7, 'harga' => 130000],
                    ['nama' => 'Keramik 50x50', 'qty' => 63, 'harga' => 100000],
                    ['nama' => 'Seng Deck 6m', 'qty' => 14, 'harga' => 402857],
                ],
            ],
        ];

        foreach ($bonList as $b) {
            $total = 0;
            foreach ($b['items'] as $it) {
                $total += $it['qty'] * $it['harga'];
            }

            $penjualanId = DB::table('penjualan')->insertGetId([
                'tipe' => 'bon',
                'pelanggan_id' => $pelangganMap[$b['pelanggan']],
                'nama_pelanggan' => $b['pelanggan'],
                'metode_bayar' => null,
                'total' => $total,
                'user_id' => $kasir->id,
                'waktu_transaksi' => $b['tanggal'] . ' 10:00:00',
            ]);

            foreach ($b['items'] as $it) {
                $brg = $barangMap[$it['nama']];
                DB::table('detail_penjualan')->insert([
                    'penjualan_id' => $penjualanId,
                    'barang_id' => $brg->id,
                    'nama_barang' => $brg->nama_barang,
                    'satuan' => $brg->satuan,
                    'jumlah' => $it['qty'],
                    'harga_satuan' => $it['harga'],
                    'subtotal' => $it['qty'] * $it['harga'],
                ]);
            }

            $bonId = DB::table('bon_piutang')->insertGetId([
                'penjualan_id' => $penjualanId,
                'pelanggan_id' => $pelangganMap[$b['pelanggan']],
                'alamat_pengantaran' => $b['alamat'],
                'tanggal_bon' => $b['tanggal'],
                'jatuh_tempo' => $b['jatuh_tempo'],
                'total_tagihan' => $total,
                'total_terbayar' => 0,
                'status_bayar' => 'belum_lunas',
                'dibuat_oleh' => $kasir->id,
            ]);

            if ($b['lunas']) {
                DB::table('pembayaran_bon')->insert([
                    'bon_id' => $bonId,
                    'jumlah' => $total,
                    'metode' => $b['metode'],
                    'waktu_bayar' => $b['tanggal'] . ' 16:00:00',
                    'user_id' => $kasir->id,
                    'catatan' => $b['catatan'],
                ]);
            }
        }
    }
}