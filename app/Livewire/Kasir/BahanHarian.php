<?php

namespace App\Livewire\Kasir;

use App\Models\PenggunaanBahan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Bahan Harian')]
class BahanHarian extends Component
{
    public string $nama_bahan = '';
    public string|float $jumlah = '';
    public string $satuan = 'liter';
    public string|int $harga = '';
    public string $tanggal = '';
    public ?string $keterangan = null;

    public ?int $editId = null;
    public ?int $hapusId = null;
    public bool $showDeleteModal = false;

    public function mount(): void
    {
        $this->authorize('viewAny', PenggunaanBahan::class);
        $this->tanggal = now()->toDateString();
    }

    #[Computed]
    public function daftarBahan()
    {
        return PenggunaanBahan::with('user')
            ->orderBy('tanggal', 'desc')
            ->orderBy('id', 'desc')
            ->get();
    }

    #[Computed]
    public function totalPengeluaran(): int
    {
        return (int) PenggunaanBahan::sum('harga');
    }

    #[Computed]
    public function bahanYangDihapus(): ?PenggunaanBahan
    {
        return $this->hapusId ? PenggunaanBahan::find($this->hapusId) : null;
    }

    public function simpan(): void
    {
        $this->resetErrorBag();

        if ($this->editId) {
            $bahan = PenggunaanBahan::findOrFail($this->editId);
            $this->authorize('update', $bahan);
        } else {
            $this->authorize('create', PenggunaanBahan::class);
        }

        $pesanWajib = 'Nama bahan, jumlah, dan harga wajib diisi.';

        $validator = Validator::make([
            'nama_bahan' => $this->nama_bahan,
            'jumlah' => $this->jumlah,
            'satuan' => $this->satuan,
            'harga' => $this->harga,
            'tanggal' => $this->tanggal,
            'keterangan' => $this->keterangan,
        ], [
            'nama_bahan' => ['required', 'string', 'max:150'],
            'jumlah' => ['required', 'numeric', 'gt:0'],
            'satuan' => ['required', 'string', 'max:30'],
            'harga' => ['required', 'integer', 'gte:0'],
            'tanggal' => ['required', 'date'],
            'keterangan' => ['nullable', 'string', 'max:255'],
        ], [
            'nama_bahan.required' => $pesanWajib,
            'jumlah.required' => $pesanWajib,
            'harga.required' => $pesanWajib,
            'jumlah.gt' => 'Jumlah bahan harus lebih dari 0.',
            'harga.gte' => 'Harga tidak boleh negatif.',
        ]);

        if ($validator->fails()) {
            if ($validator->errors()->has('nama_bahan') || $validator->errors()->has('jumlah') || $validator->errors()->has('harga')) {
                $this->addError('error_wajib', $pesanWajib);
            }

            foreach ($validator->errors()->messages() as $field => $messages) {
                foreach ($messages as $msg) {
                    $this->addError($field, $msg);
                }
            }

            return;
        }

        $validated = $validator->validated();

        if ($this->editId) {
            $bahan->update([
                'tanggal' => $validated['tanggal'],
                'nama_bahan' => $validated['nama_bahan'],
                'jumlah' => $validated['jumlah'],
                'satuan' => $validated['satuan'],
                'harga' => $validated['harga'],
                'keterangan' => $validated['keterangan'] ?? null,
            ]);
            $this->dispatch('toast', type: 'success', message: 'Bahan harian berhasil diperbarui.');
        } else {
            PenggunaanBahan::create([
                'tanggal' => $validated['tanggal'],
                'nama_bahan' => $validated['nama_bahan'],
                'jumlah' => $validated['jumlah'],
                'satuan' => $validated['satuan'],
                'harga' => $validated['harga'],
                'keterangan' => $validated['keterangan'] ?? null,
                'user_id' => Auth::id(),
            ]);
            $this->dispatch('toast', type: 'success', message: 'Bahan harian berhasil ditambahkan.');
        }

        $this->batal();
    }

    public function edit(int $id): void
    {
        $this->resetErrorBag();
        $bahan = PenggunaanBahan::findOrFail($id);
        $this->authorize('update', $bahan);

        $this->editId = $bahan->id;
        $this->nama_bahan = $bahan->nama_bahan;
        $this->jumlah = (float) $bahan->jumlah;
        $this->satuan = $bahan->satuan;
        $this->harga = $bahan->harga;
        $this->tanggal = $bahan->tanggal ? $bahan->tanggal->format('Y-m-d') : now()->toDateString();
        $this->keterangan = $bahan->keterangan;
    }

    public function batal(): void
    {
        $this->reset(['nama_bahan', 'jumlah', 'harga', 'keterangan', 'editId']);
        $this->satuan = 'liter';
        $this->tanggal = now()->toDateString();
        $this->resetErrorBag();
    }

    public function konfirmasiHapus(int $id): void
    {
        $bahan = PenggunaanBahan::findOrFail($id);
        $this->authorize('delete', $bahan);
        $this->hapusId = $bahan->id;
        $this->showDeleteModal = true;
    }

    public function batalHapus(): void
    {
        $this->hapusId = null;
        $this->showDeleteModal = false;
    }

    public function hapus(): void
    {
        if (! $this->hapusId) {
            return;
        }

        $bahan = PenggunaanBahan::findOrFail($this->hapusId);
        $this->authorize('delete', $bahan);
        $bahan->delete();

        if ($this->editId === $this->hapusId) {
            $this->batal();
        }

        $this->hapusId = null;
        $this->showDeleteModal = false;
        $this->dispatch('toast', type: 'success', message: 'Bahan harian berhasil dihapus.');
    }

    public function render()
    {
        return view('livewire.kasir.bahan-harian');
    }
}
