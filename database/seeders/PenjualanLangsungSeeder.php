<?php

namespace Database\Seeders;

use App\Models\Barang;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PenjualanLangsungSeeder extends Seeder
{
    public function run(): void
    {
        $kasir = User::whereHas('role', fn ($q) => $q->where('nama', 'kasir'))->firstOrFail();
        $barangMap = Barang::all()->keyBy('nama_barang');
        $seninLalu = now('Asia/Jakarta')->startOfWeek(\Carbon\Carbon::MONDAY)->subWeek();
        $hariIni = now('Asia/Jakarta');

        $transaksiList = [
            // Minggu 09/11 - 15/11/2026
            [
                'pelanggan' => 'Pak Maruli',
                'metode' => 'cash',
                'waktu' => '2026-11-09 09:15:00',
                'items' => [
                    ['nama' => 'Seng 6/11', 'qty' => 6],
                    ['nama' => 'Paku Seng', 'qty' => 2],
                ],
            ],
            [
                'pelanggan' => 'Ibu Dewi',
                'metode' => 'transfer',
                'waktu' => '2026-11-09 14:20:00',
                'items' => [
                    ['nama' => 'Pipa 4"', 'qty' => 2],
                    ['nama' => 'Pipa 2 1/2"', 'qty' => 4],
                ],
            ],
            [
                'pelanggan' => 'Pak Sitorus',
                'metode' => 'cash',
                'waktu' => '2026-11-10 10:30:00',
                'items' => [
                    ['nama' => 'Batu Bata', 'qty' => 200],
                    ['nama' => 'Pasir', 'qty' => 1],
                ],
            ],
            [
                'pelanggan' => 'Bang Rudi',
                'metode' => 'transfer',
                'waktu' => '2026-11-11 11:45:00',
                'items' => [
                    ['nama' => 'Besi 10 mili', 'qty' => 3],
                    ['nama' => 'Besi 5 mili', 'qty' => 4],
                ],
            ],
            [
                'pelanggan' => 'Pak Gultom',
                'metode' => 'cash',
                'waktu' => '2026-11-12 13:10:00',
                'items' => [
                    ['nama' => 'Semen Padang 50kg', 'qty' => 2],
                    ['nama' => 'Batu Cor 2/3', 'qty' => 2],
                ],
            ],
            [
                'pelanggan' => 'Ibu Lidia',
                'metode' => 'transfer',
                'waktu' => '2026-11-13 15:05:00',
                'items' => [
                    ['nama' => 'Seng 8/11', 'qty' => 5],
                    ['nama' => 'Rabung 6k', 'qty' => 4],
                ],
            ],
            [
                'pelanggan' => 'Pak Simbolon',
                'metode' => 'cash',
                'waktu' => '2026-11-14 09:50:00',
                'items' => [
                    ['nama' => 'Besi 8 mili', 'qty' => 3],
                    ['nama' => 'Kawat Ikat', 'qty' => 2],
                    ['nama' => 'Paku 3"', 'qty' => 2],
                ],
            ],
            [
                'pelanggan' => 'Umum (Minggu)',
                'metode' => 'transfer',
                'waktu' => '2026-11-15 12:15:00',
                'items' => [
                    ['nama' => 'Bohlam 5W', 'qty' => 4],
                    ['nama' => 'Box MCB', 'qty' => 2],
                    ['nama' => 'Paku 2"', 'qty' => 2],
                ],
            ],

            // Minggu lalu: Senin (16/11) s.d. Minggu (22/11)
            [
                'pelanggan' => 'Pak Anton',
                'metode' => 'cash',
                'waktu' => $seninLalu->copy()->setTime(9, 20)->format('Y-m-d H:i:s'),
                'items' => [
                    ['nama' => 'Seng 6/11', 'qty' => 8],
                    ['nama' => 'Paku Seng', 'qty' => 2],
                ],
            ],
            [
                'pelanggan' => 'Ibu Rina',
                'metode' => 'transfer',
                'waktu' => $seninLalu->copy()->setTime(14, 10)->format('Y-m-d H:i:s'),
                'items' => [
                    ['nama' => 'Pipa 4"', 'qty' => 3],
                    ['nama' => 'Pipa 3"', 'qty' => 2],
                ],
            ],
            [
                'pelanggan' => 'Bang Jek',
                'metode' => 'cash',
                'waktu' => $seninLalu->copy()->addDays(1)->setTime(10, 45)->format('Y-m-d H:i:s'),
                'items' => [
                    ['nama' => 'Batu Bata', 'qty' => 300],
                    ['nama' => 'Pasir', 'qty' => 2],
                ],
            ],
            [
                'pelanggan' => 'Pak Budi',
                'metode' => 'transfer',
                'waktu' => $seninLalu->copy()->addDays(2)->setTime(13, 15)->format('Y-m-d H:i:s'),
                'items' => [
                    ['nama' => 'Besi 10 mili', 'qty' => 4],
                    ['nama' => 'Kawat Ikat', 'qty' => 2],
                ],
            ],
            [
                'pelanggan' => 'Pak Hendro',
                'metode' => 'cash',
                'waktu' => $seninLalu->copy()->addDays(3)->setTime(11, 0)->format('Y-m-d H:i:s'),
                'items' => [
                    ['nama' => 'Semen Padang 50kg', 'qty' => 3],
                    ['nama' => 'Batu Padas', 'qty' => 2],
                ],
            ],
            [
                'pelanggan' => 'Ibu Sari',
                'metode' => 'transfer',
                'waktu' => $seninLalu->copy()->addDays(4)->setTime(15, 30)->format('Y-m-d H:i:s'),
                'items' => [
                    ['nama' => 'Seng 8/11', 'qty' => 6],
                    ['nama' => 'Rabung 8k', 'qty' => 2],
                ],
            ],
            [
                'pelanggan' => 'Pak Darwin',
                'metode' => 'cash',
                'waktu' => $seninLalu->copy()->addDays(5)->setTime(9, 40)->format('Y-m-d H:i:s'),
                'items' => [
                    ['nama' => 'Besi 8 mili', 'qty' => 4],
                    ['nama' => 'Paku 3"', 'qty' => 3],
                ],
            ],
            [
                'pelanggan' => 'Umum',
                'metode' => 'transfer',
                'waktu' => $seninLalu->copy()->addDays(6)->setTime(12, 20)->format('Y-m-d H:i:s'),
                'items' => [
                    ['nama' => 'Bohlam 5W', 'qty' => 5],
                    ['nama' => 'Box MCB', 'qty' => 2],
                    ['nama' => 'Paku 2"', 'qty' => 2],
                ],
            ],
            // Tambahan Cash hari ini (Senin 23/11)
            [
                'pelanggan' => 'Pak Tono',
                'metode' => 'cash',
                'waktu' => $hariIni->copy()->setTime(10, 5)->format('Y-m-d H:i:s'),
                'items' => [
                    ['nama' => 'Rabung 7k', 'qty' => 4],
                    ['nama' => 'Paku 4"', 'qty' => 2],
                ],
            ],
        ];

        foreach ($transaksiList as $trx) {
            $tgl = substr($trx['waktu'], 0, 10);
            $sudahAda = DB::table('penjualan')
                ->where('tipe', 'langsung')
                ->where('nama_pelanggan', $trx['pelanggan'])
                ->whereDate('waktu_transaksi', $tgl)
                ->exists();

            if ($sudahAda) {
                continue;
            }

            $total = 0;
            foreach ($trx['items'] as $it) {
                $brg = $barangMap[$it['nama']] ?? null;
                if ($brg) {
                    $total += $it['qty'] * (int) $brg->harga_jual;
                }
            }

            if ($total === 0) {
                continue;
            }

            $penjualanId = DB::table('penjualan')->insertGetId([
                'tipe' => 'langsung',
                'pelanggan_id' => null,
                'nama_pelanggan' => $trx['pelanggan'],
                'metode_bayar' => $trx['metode'],
                'total' => $total,
                'user_id' => $kasir->id,
                'waktu_transaksi' => $trx['waktu'],
            ]);

            foreach ($trx['items'] as $it) {
                $brg = $barangMap[$it['nama']] ?? null;
                if (! $brg) {
                    continue;
                }
                DB::table('detail_penjualan')->insert([
                    'penjualan_id' => $penjualanId,
                    'barang_id' => $brg->id,
                    'nama_barang' => $brg->nama_barang,
                    'satuan' => $brg->satuan,
                    'jumlah' => $it['qty'],
                    'harga_satuan' => (int) $brg->harga_jual,
                    'subtotal' => $it['qty'] * (int) $brg->harga_jual,
                ]);
            }
        }

        // Tutup kas_laci tanggal sebelum hari ini ke penutupan_laci agar Riwayat Saldo Laci terisi per hari
        $hariIniStr = $hariIni->toDateString();
        $tanggalBelumTutup = DB::table('kas_laci')
            ->whereNull('penutupan_id')
            ->whereDate('tanggal', '<', $hariIniStr)
            ->selectRaw("tanggal, COALESCE(SUM(IF(metode = 'cash', jumlah, 0)), 0) as total_cash, COALESCE(SUM(IF(metode = 'transfer', jumlah, 0)), 0) as total_transfer")
            ->groupBy('tanggal')
            ->orderBy('tanggal')
            ->get();

        foreach ($tanggalBelumTutup as $row) {
            $penutupanId = DB::table('penutupan_laci')->insertGetId([
                'tanggal' => $row->tanggal,
                'total_cash' => (int) $row->total_cash,
                'total_transfer' => (int) $row->total_transfer,
                'total' => (int) $row->total_cash + (int) $row->total_transfer,
                'waktu_reset' => $row->tanggal . ' 23:59:59',
                'direset_oleh' => null,
                'tipe_reset' => 'otomatis',
            ]);

            DB::table('kas_laci')
                ->whereNull('penutupan_id')
                ->where('tanggal', $row->tanggal)
                ->update(['penutupan_id' => $penutupanId]);
        }
    }
}