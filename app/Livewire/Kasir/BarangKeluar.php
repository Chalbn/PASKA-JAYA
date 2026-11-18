<?php

declare(strict_types=1);

namespace App\Livewire\Kasir;

use App\Models\Barang;
use App\Models\Penjualan;
use App\Services\PenjualanService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Barang Keluar')]
class BarangKeluar extends Component
{
    public ?int $selectedBarangId = null;

    public int $jumlah = 1;

    /**
     * @var array<int, array{
     *     barang_id: int,
     *     nama_barang: string,
     *     kode_barang: string,
     *     satuan: string,
     *     harga_satuan: int,
     *     jumlah: int,
     *     subtotal: int,
     *     stok_tersedia: int
     * }>
     */
    public array $keranjang = [];

    public string $namaPelanggan = 'Umum';

    public string $metodeBayar = 'cash';

    public function tambahItem(): void
    {
        $this->validate([
            'selectedBarangId' => ['required', 'integer', 'exists:barang,id'],
            'jumlah' => ['required', 'integer', 'min:1'],
        ], [
            'selectedBarangId.required' => 'Silakan pilih barang terlebih dahulu.',
            'selectedBarangId.exists' => 'Barang yang dipilih tidak valid.',
            'jumlah.min' => 'Jumlah minimal 1.',
        ]);

        $barang = Barang::find($this->selectedBarangId);
        if (! $barang) {
            return;
        }

        // Cari jika barang sudah ada di keranjang
        $existingIndex = null;
        $currentQty = 0;
        foreach ($this->keranjang as $index => $item) {
            if ($item['barang_id'] === $barang->id) {
                $existingIndex = $index;
                $currentQty = $item['jumlah'];
                break;
            }
        }

        $totalQty = $currentQty + $this->jumlah;

        if ($barang->stok < $totalQty) {
            $this->dispatch('toast', [
                'message' => "Stok {$barang->nama_barang} tidak cukup (tersisa {$barang->stok})",
                'type' => 'error',
            ]);

            return;
        }

        if ($existingIndex !== null) {
            $this->keranjang[$existingIndex]['jumlah'] = $totalQty;
            $this->keranjang[$existingIndex]['subtotal'] = $totalQty * (int) $barang->harga_jual;
            $this->keranjang[$existingIndex]['stok_tersedia'] = (int) $barang->stok;
        } else {
            $this->keranjang[] = [
                'barang_id' => $barang->id,
                'nama_barang' => $barang->nama_barang,
                'kode_barang' => (string) $barang->kode_barang,
                'satuan' => (string) $barang->satuan,
                'harga_satuan' => (int) $barang->harga_jual,
                'jumlah' => $this->jumlah,
                'subtotal' => $this->jumlah * (int) $barang->harga_jual,
                'stok_tersedia' => (int) $barang->stok,
            ];
        }

        $this->selectedBarangId = null;
        $this->jumlah = 1;
    }

    public function ubahJumlah(int $barangId, int $delta): void
    {
        foreach ($this->keranjang as $index => $item) {
            if ($item['barang_id'] === $barangId) {
                $newQty = $item['jumlah'] + $delta;
                if ($newQty <= 0) {
                    $this->hapusItem($barangId);

                    return;
                }

                if ($newQty > $item['stok_tersedia']) {
                    $this->dispatch('toast', [
                        'message' => "Stok {$item['nama_barang']} tidak cukup (tersisa {$item['stok_tersedia']})",
                        'type' => 'error',
                    ]);

                    return;
                }

                $this->keranjang[$index]['jumlah'] = $newQty;
                $this->keranjang[$index]['subtotal'] = $newQty * $item['harga_satuan'];
                break;
            }
        }
    }

    public function updateJumlahInput(int $barangId, mixed $qty): void
    {
        $qtyInt = (int) $qty;
        if ($qtyInt <= 0) {
            $this->hapusItem($barangId);

            return;
        }

        foreach ($this->keranjang as $index => $item) {
            if ($item['barang_id'] === $barangId) {
                if ($qtyInt > $item['stok_tersedia']) {
                    $this->dispatch('toast', [
                        'message' => "Stok {$item['nama_barang']} tidak cukup (tersisa {$item['stok_tersedia']})",
                        'type' => 'error',
                    ]);
                    $qtyInt = $item['stok_tersedia'];
                }

                $this->keranjang[$index]['jumlah'] = $qtyInt;
                $this->keranjang[$index]['subtotal'] = $qtyInt * $item['harga_satuan'];
                break;
            }
        }
    }

    public function hapusItem(int $barangId): void
    {
        $this->keranjang = array_values(array_filter(
            $this->keranjang,
            fn ($item) => $item['barang_id'] !== $barangId
        ));
    }

    public function setMetodeBayar(string $metode): void
    {
        $this->metodeBayar = in_array($metode, ['cash', 'transfer'], true) ? $metode : 'cash';
    }

    public function simpanTransaksi(PenjualanService $service): void
    {
        if (empty($this->keranjang)) {
            $this->dispatch('toast', [
                'message' => 'Keranjang masih kosong. Pilih barang terlebih dahulu.',
                'type' => 'error',
            ]);

            return;
        }

        $nama = trim($this->namaPelanggan);
        if ($nama === '') {
            $nama = 'Umum';
        }

        $payload = [
            'items' => array_map(fn ($item) => [
                'barang_id' => $item['barang_id'],
                'jumlah' => $item['jumlah'],
            ], $this->keranjang),
            'metode_bayar' => $this->metodeBayar,
            'nama_pelanggan' => $nama,
        ];

        try {
            $user = Auth::user();
            $penjualan = $service->simpanLangsung($payload, $user);

            $this->keranjang = [];
            $this->namaPelanggan = 'Umum';
            $this->metodeBayar = 'cash';
            $this->selectedBarangId = null;
            $this->jumlah = 1;

            $this->dispatch('toast', [
                'message' => "Transaksi {$penjualan->kode_transaksi} berhasil disimpan.",
                'type' => 'success',
            ]);
        } catch (\Throwable $e) {
            $this->dispatch('toast', [
                'message' => $e->getMessage(),
                'type' => 'error',
            ]);
        }
    }

    #[Computed]
    public function masterItems()
    {
        return Barang::query()->orderBy('nama_barang')->get();
    }

    #[Computed]
    public function totalBayar(): int
    {
        return (int) array_sum(array_column($this->keranjang, 'subtotal'));
    }

    #[Computed]
    public function riwayatPenjualan()
    {
        return Penjualan::query()
            ->langsung()
            ->with('detailPenjualan')
            ->latest('waktu_transaksi')
            ->latest('id')
            ->take(10)
            ->get();
    }

    #[Computed]
    public function totalRiwayat(): int
    {
        return Penjualan::query()->langsung()->count();
    }

    public function render(): View
    {
        return view('livewire.kasir.barang-keluar');
    }
}
