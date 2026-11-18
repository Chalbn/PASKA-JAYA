<?php

namespace App\Livewire\Kasir;

use App\Models\BonPiutang;
use App\Models\LogStatusPengiriman;
use App\Models\PembayaranBon; // 1. Tambahkan use attribute Layout
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')] // 2. Daftarkan layout utama aplikasi di sini
class DaftarBon extends Component
{
    use WithPagination;

    public $filterStatus = 'semua'; // semua, belum_lunas, lunas

    public $search = '';

    // Properti untuk Modal / Aksi
    public $selectedBonId = null;

    public $modalDetailOpen = false;

    public $modalKirimOpen = false;

    public $modalBayarOpen = false;

    // Input form kirim ke karyawan
    public $karyawan_id;

    public $jadwal_pengiriman;

    public $catatan_pengiriman;

    // Input form pembayaran
    public $nominal_bayar = 0;

    public $metode_pembayaran = 'tunai';

    protected $updatesQueryString = ['filterStatus', 'search'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterStatus()
    {
        $this->resetPage();
    }

    // Aksi Kasir: Buka modal kirim ke karyawan
    public function bukaModalKirim($bonId)
    {
        $this->selectedBonId = $bonId;
        $this->modalKirimOpen = true;
    }

    // Aksi Kasir: Eksekusi Kirim ke Karyawan (memanggil Stored Procedure / Logika Kirim)
    public function kirimKeKaryawan()
    {
        $this->validate([
            'karyawan_id' => 'required|exists:users,id',
            'jadwal_pengiriman' => 'required|date',
        ]);

        DB::transaction(function () {
            $bon = BonPiutang::with('penjualan.detailPenjualan.masterItem')->findOrFail($this->selectedBonId);

            // 1. Potong stok barang di master_items (sesuai aturan stok berkurang saat bon dikirim)
            foreach ($bon->penjualan->detailPenjualan as $detail) {
                $item = $detail->masterItem;
                if ($item) {
                    $item->stok -= $detail->jumlah;
                    $item->save();
                }
            }

            // 2. Catat pengiriman barang (atau update status)
            LogStatusPengiriman::create([
                'penjualan_id' => $bon->penjualan_id,
                'status' => 'sedang_diantar',
                'keterangan' => 'Bon dikirim ke karyawan ID: '.$this->karyawan_id.'. Jadwal: '.$this->jadwal_pengiriman,
                'user_id' => Auth::id(),
            ]);
        });

        session()->flash('success', 'Bon berhasil dikirim ke karyawan dan stok inventaris telah dikurangi.');
        $this->modalKirimOpen = false;
        $this->reset(['karyawan_id', 'jadwal_pengiriman', 'catatan_pengiriman', 'selectedBonId']);
    }

    // Aksi Kasir: Tandai Lunas / Bayar Sebagian
    public function bayarBon()
    {
        $this->validate([
            'nominal_bayar' => 'required|numeric|min:1',
            'metode_pembayaran' => 'required|string',
        ]);

        DB::transaction(function () {
            $bon = BonPiutang::findOrFail($this->selectedBonId);
            $sisaTagihan = $bon->total_nominal - $bon->total_terbayar;

            $bayar = min($this->nominal_bayar, $sisaTagihan);

            // Insert ke tabel pembayaran_bon
            PembayaranBon::create([
                'bon_piutang_id' => $bon->id,
                'jumlah_bayar' => $bayar,
                'metode' => $this->metode_pembayaran,
                'user_id' => Auth::id(),
            ]);

            // Update total terbayar & status pembayaran
            $bon->total_terbayar += $bayar;
            if ($bon->total_terbayar >= $bon->total_nominal) {
                $bon->status_pembayaran = 'lunas';
            }
            $bon->save();
        });

        session()->flash('success', 'Pembayaran bon berhasil dicatat dan masuk ke kas laci.');
        $this->modalBayarOpen = false;
        $this->reset(['nominal_bayar', 'selectedBonId']);
    }

    public function render()
    {
        $query = BonPiutang::with(['penjualan.pelanggan', 'penjualan.detailPenjualan.masterItem']);

        // Filter berdasarkan status pembayaran
        if ($this->filterStatus === 'belum_lunas') {
            $query->where('status_pembayaran', 'belum_lunas');
        } elseif ($this->filterStatus === 'lunas') {
            $query->where('status_pembayaran', 'lunas');
        }

        // Pencarian berdasarkan nama pelanggan atau kode bon
        if (! empty($this->search)) {
            $query->whereHas('penjualan.pelanggan', function ($q) {
                $q->where('nama', 'like', '%'.$this->search.'%');
            })->orWhere('kode_bon', 'like', '%'.$this->search.'%');
        }

        $daftarBon = $query->latest()->paginate(10);

        return view('livewire.kasir.daftar-bon', [
            'daftarBon' => $daftarBon,
        ]);
    }
}
