<?php

namespace App\Livewire\Kasir;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Barang;
use App\Services\BonService;
use Illuminate\Support\Facades\DB;

#[Layout('layouts.app')]
class CatatBon extends Component
{
    public $nama_pelanggan = '';
    public $alamat = '';
    public $kontak = '';
    public $tanggal;
    public $jatuh_tempo;

    // Keranjang item bon
    public $items = [];
    
    // Input sementara untuk form tambah item
    public $selected_item_id = '';
    public $jumlah = 1;
    public $harga_satuan = 0;

    protected $rules = [
        'nama_pelanggan' => 'required|string|max:255',
        'tanggal' => 'required|date',
        'jatuh_tempo' => 'required|date|after_or_equal:tanggal',
        'items' => 'required|array|min:1',
    ];

    public function mount()
    {
        $this->tanggal = date('Y-m-d');
        $this->jatuh_tempo = date('Y-m-d', strtotime('+30 days'));
    }

    // DIPERBAIKI: Nama hook harus sesuai dengan camelCase dari $selected_item_id
    public function updatedSelectedItemId($value)
    {
        if ($value) {
            $barang = Barang::find($value);
            if ($barang) {
                $this->harga_satuan = $barang->harga_jual ?? 0;
            }
        }
    }

    public function tambahItem()
    {
        $this->validate([
            'selected_item_id' => 'required|exists:barang,id',
            'jumlah' => 'required|integer|min:1',
            'harga_satuan' => 'required|numeric|min:0',
        ]);

        $itemMaster = Barang::find($this->selected_item_id);

        // Cek apakah item sudah ada di keranjang, jika ada tambahkan jumlahnya
        $exists = false;
        foreach ($this->items as &$cartItem) {
            if ($cartItem['master_item_id'] == $this->selected_item_id) {
                $cartItem['jumlah'] += $this->jumlah;
                $exists = true;
                break;
            }
        }

        if (!$exists) {
            $this->items[] = [
                'master_item_id' => $itemMaster->id,
                'nama_barang' => $itemMaster->nama_barang ?? $itemMaster->nama,
                'jumlah' => $this->jumlah,
                'harga_satuan' => $this->harga_satuan,
                'subtotal' => $this->jumlah * $this->harga_satuan,
            ];
        }

        // Reset input sementara
        $this->selected_item_id = '';
        $this->jumlah = 1;
        $this->harga_satuan = 0;
    }

    public function hapusItem($index)
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
    }

    public function simpanBon(BonService $bonService)
    {
        $this->validate();

        try {
            $bonService->buat([
                'nama_pelanggan' => $this->nama_pelanggan,
                'alamat' => $this->alamat,
                'kontak' => $this->kontak,
                'tanggal' => $this->tanggal,
                'jatuh_tempo' => $this->jatuh_tempo,
                'items' => $this->items,
            ]);

            session()->flash('success', 'Bon berhasil dicatat! Status awal: Belum Dikirim.');
            return redirect()->route('kasir.daftar-bon');

        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan saat menyimpan bon: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $masterItems = Barang::all();

        return view('livewire.kasir.catat-bon', [
            'masterItems' => $masterItems
        ]);
    }
} 
