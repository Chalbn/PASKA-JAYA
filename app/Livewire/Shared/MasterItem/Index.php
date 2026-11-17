<?php

declare(strict_types=1);

namespace App\Livewire\Shared\MasterItem;

use App\Models\Barang;
use App\Models\KategoriBarang;
use App\Services\FileService;
use App\Services\PenjualanService;
use DomainException;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
#[Title('Master Item')]
class Index extends Component
{
    use WithFileUploads, WithPagination;

    #[Url]
    public string $search = '';

    #[Url]
    public string $kategori = '';

    #[Url]
    public string $view = 'cards'; // 'cards' atau 'table'

    // ── Form State: Tambah Barang ──
    public bool $showTambahModal = false;

    public string $kode_barang = '';

    public string $nama_barang = '';

    public string $kategori_nama = '';

    public string $satuan = 'Sak';

    public ?int $harga_beli = null;

    public ?int $harga_jual = null;

    public int $stok_awal = 0;

    public int $stok_minimum = 10;

    public $foto = null;

    // ── Form State: Restock / Edit Barang ──
    public bool $showEditModal = false;

    public ?int $editBarangId = null;

    public string $edit_kode_barang = '';

    public string $edit_nama_barang = '';

    public string $edit_kategori_nama = '';

    public string $edit_satuan = '';

    public ?int $edit_harga_beli = null;

    public ?int $edit_harga_jual = null;

    public int $edit_stok_minimum = 10;

    public int $edit_stok_saat_ini = 0;

    public int $tambah_stok = 0;

    public ?string $edit_foto_lama = null;

    public $edit_foto_baru = null;

    // ── Dialog Hapus ──
    public bool $showDeleteModal = false;

    public ?int $deleteBarangId = null;

    public string $deleteBarangNama = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedKategori(): void
    {
        $this->resetPage();
    }

    public function setView(string $mode): void
    {
        $this->view = in_array($mode, ['cards', 'table'], true) ? $mode : 'cards';
    }

    // ── Ringkasan Statistik ──
    #[Computed]
    public function stats(): array
    {
        $totalBarang = Barang::count();
        $jumlahKategori = KategoriBarang::whereHas('barang')->count();
        $nilaiStok = (int) (Barang::selectRaw('COALESCE(SUM(stok * harga_beli), 0) as total')->value('total') ?? 0);
        $stokMenipis = Barang::stokMenipis()->count();
        $stokAman = Barang::whereColumn('stok', '>', 'stok_minimum')->count();

        return [
            'total_barang' => $totalBarang,
            'jumlah_kategori' => $jumlahKategori,
            'nilai_stok' => $nilaiStok,
            'stok_menipis' => $stokMenipis,
            'stok_aman' => $stokAman,
        ];
    }

    // ── Daftar Kategori Otomatis (ADR-010) ──
    #[Computed]
    public function kategoriList()
    {
        return KategoriBarang::whereHas('barang')
            ->orderBy('nama')
            ->get();
    }

    // ── Semua Nama Kategori (untuk autocomplete/combobox) ──
    #[Computed]
    public function allKategoriSuggestions(): array
    {
        return KategoriBarang::orderBy('nama')->pluck('nama')->all();
    }

    // ── Data Barang Terpaginasi ──
    #[Computed]
    public function barangList()
    {
        $query = Barang::with('kategori')
            ->when($this->search !== '', function ($q) {
                $term = '%'.trim($this->search).'%';
                $q->where(function ($sub) use ($term) {
                    $sub->where('nama_barang', 'like', $term)
                        ->orWhere('kode_barang', 'like', $term);
                });
            })
            ->when($this->kategori !== '', function ($q) {
                $q->whereHas('kategori', function ($sub) {
                    $sub->where('slug', $this->kategori)
                        ->orWhere('nama', $this->kategori);
                });
            })
            ->orderBy('id', 'desc');

        return $query->paginate(12);
    }

    // ── Modal Tambah Barang ──
    public function openTambahModal(): void
    {
        $this->resetErrorBag();
        $this->reset(['nama_barang', 'kategori_nama', 'harga_beli', 'harga_jual', 'stok_awal', 'foto']);
        $this->satuan = 'Sak';
        $this->stok_minimum = 10;
        $this->stok_awal = 0;

        // Ambil kode barang berikutnya dari database
        try {
            $next = DB::selectOne('SELECT fn_kode_barang_berikutnya() AS kode');
            $this->kode_barang = $next?->kode ?? 'BRG-001';
        } catch (\Throwable) {
            $max = Barang::count() + 1;
            $this->kode_barang = 'BRG-'.str_pad((string) $max, 3, '0', STR_PAD_LEFT);
        }

        $this->showTambahModal = true;
    }

    public function simpanTambah(PenjualanService $penjualanService, FileService $fileService): void
    {
        $this->validate([
            'kode_barang' => ['required', 'string', 'max:20', 'unique:barang,kode_barang'],
            'nama_barang' => ['required', 'string', 'max:150'],
            'kategori_nama' => ['required', 'string', 'max:80'],
            'satuan' => ['required', 'string', 'max:30'],
            'harga_beli' => ['required', 'integer', 'min:0'],
            'harga_jual' => ['required', 'integer', 'min:0'],
            'stok_awal' => ['required', 'integer', 'min:0'],
            'stok_minimum' => ['required', 'integer', 'min:0'],
            'foto' => ['nullable', 'image', 'max:4096'],
        ], [
            'kode_barang.required' => 'Kode barang wajib diisi.',
            'kode_barang.unique' => 'Kode barang sudah digunakan.',
            'nama_barang.required' => 'Nama barang wajib diisi.',
            'kategori_nama.required' => 'Kategori barang wajib dipilih atau diketik.',
            'satuan.required' => 'Satuan barang wajib diisi.',
            'harga_beli.required' => 'Harga beli wajib diisi.',
            'harga_jual.required' => 'Harga jual wajib diisi.',
        ]);

        try {
            $fotoPath = null;
            if ($this->foto) {
                $fotoPath = $fileService->simpanFotoBarang($this->foto);
            }

            $penjualanService->tambahBarang(
                kodeBarang: trim($this->kode_barang),
                kategori: trim($this->kategori_nama),
                namaBarang: trim($this->nama_barang),
                satuan: trim($this->satuan),
                hargaBeli: (int) $this->harga_beli,
                hargaJual: (int) $this->harga_jual,
                stokAwal: (int) $this->stok_awal,
                stokMinimum: (int) $this->stok_minimum,
                foto: $fotoPath,
                userId: Auth::id()
            );

            $this->showTambahModal = false;
            $this->dispatch('toast', tipe: 'sukses', pesan: 'Barang '.trim($this->nama_barang).' berhasil ditambahkan.');
        } catch (DomainException $e) {
            $this->addError('general', $e->getMessage());
        } catch (\Throwable $e) {
            $this->addError('general', 'Terjadi kesalahan saat menyimpan barang: '.$e->getMessage());
        }
    }

    // ── Modal Restock / Edit Barang ──
    public function openEditModal(int $barangId): void
    {
        $this->resetErrorBag();
        $barang = Barang::with('kategori')->findOrFail($barangId);

        $this->editBarangId = $barang->id;
        $this->edit_kode_barang = $barang->kode_barang;
        $this->edit_nama_barang = $barang->nama_barang;
        $this->edit_kategori_nama = $barang->kategori?->nama ?? '';
        $this->edit_satuan = $barang->satuan;
        $this->edit_harga_beli = (int) $barang->harga_beli;
        $this->edit_harga_jual = (int) $barang->harga_jual;
        $this->edit_stok_minimum = (int) $barang->stok_minimum;
        $this->edit_stok_saat_ini = (int) $barang->stok;
        $this->edit_foto_lama = $barang->foto;
        $this->edit_foto_baru = null;
        $this->tambah_stok = 0;

        $this->showEditModal = true;
    }

    public function simpanEdit(PenjualanService $penjualanService, FileService $fileService): void
    {
        $this->validate([
            'edit_kode_barang' => ['required', 'string', 'max:20', 'unique:barang,kode_barang,'.$this->editBarangId],
            'edit_nama_barang' => ['required', 'string', 'max:150'],
            'edit_kategori_nama' => ['required', 'string', 'max:80'],
            'edit_satuan' => ['required', 'string', 'max:30'],
            'edit_harga_beli' => ['required', 'integer', 'min:0'],
            'edit_harga_jual' => ['required', 'integer', 'min:0'],
            'edit_stok_minimum' => ['required', 'integer', 'min:0'],
            'tambah_stok' => ['required', 'integer', 'min:0'],
            'edit_foto_baru' => ['nullable', 'image', 'max:4096'],
        ], [
            'edit_kode_barang.required' => 'Kode barang wajib diisi.',
            'edit_kode_barang.unique' => 'Kode barang sudah digunakan oleh barang lain.',
            'edit_nama_barang.required' => 'Nama barang wajib diisi.',
            'edit_kategori_nama.required' => 'Kategori barang wajib diisi.',
            'edit_satuan.required' => 'Satuan barang wajib diisi.',
            'tambah_stok.min' => 'Jumlah penambahan stok minimal 0.',
        ]);

        try {
            $barang = Barang::findOrFail($this->editBarangId);

            // Simpan foto baru jika diunggah
            if ($this->edit_foto_baru) {
                $fotoPath = $fileService->simpanFotoBarang($this->edit_foto_baru);
                $barang->foto = $fotoPath;
            }

            // Pastikan kategori ada / dibuat jika baru
            $kategori = KategoriBarang::firstOrCreate(
                ['nama' => trim($this->edit_kategori_nama)],
                ['slug' => Str::slug($this->edit_kategori_nama)]
            );

            // Jika ada penambahan stok, panggil sp_restock_barang
            if ($this->tambah_stok > 0) {
                $penjualanService->restockBarang(
                    barangId: $barang->id,
                    jumlahTambah: (int) $this->tambah_stok,
                    hargaBeli: (int) $this->edit_harga_beli,
                    hargaJual: (int) $this->edit_harga_jual,
                    stokMinimum: (int) $this->edit_stok_minimum,
                    userId: Auth::id()
                );
            }

            // Update atribut lainnya
            $barang->kode_barang = trim($this->edit_kode_barang);
            $barang->nama_barang = trim($this->edit_nama_barang);
            $barang->kategori_id = $kategori->id;
            $barang->satuan = trim($this->edit_satuan);
            $barang->harga_beli = (int) $this->edit_harga_beli;
            $barang->harga_jual = (int) $this->edit_harga_jual;
            $barang->stok_minimum = (int) $this->edit_stok_minimum;
            $barang->save();

            $this->showEditModal = false;
            $this->dispatch('toast', tipe: 'sukses', pesan: 'Perubahan barang '.$barang->nama_barang.' berhasil disimpan.');
        } catch (DomainException $e) {
            $this->addError('general', $e->getMessage());
        } catch (\Throwable $e) {
            $this->addError('general', 'Terjadi kesalahan saat memperbarui barang: '.$e->getMessage());
        }
    }

    // ── Hapus Barang (Soft Delete) ──
    public function confirmDelete(int $barangId): void
    {
        $barang = Barang::findOrFail($barangId);
        $this->deleteBarangId = $barang->id;
        $this->deleteBarangNama = $barang->nama_barang;
        $this->showDeleteModal = true;
    }

    public function deleteBarang(): void
    {
        if (! $this->deleteBarangId) {
            return;
        }

        try {
            $barang = Barang::findOrFail($this->deleteBarangId);
            $nama = $barang->nama_barang;
            $barang->delete();

            $this->showDeleteModal = false;
            $this->deleteBarangId = null;
            $this->deleteBarangNama = '';

            $this->dispatch('toast', tipe: 'sukses', pesan: "Barang '{$nama}' berhasil dihapus dari master item.");
        } catch (\Throwable $e) {
            $this->addError('general', 'Gagal menghapus barang: '.$e->getMessage());
        }
    }

    public function render(): View
    {
        return view('livewire.shared.master-item.index', [
            'barangPaginated' => $this->barangList,
            'kategoriList' => $this->kategoriList,
            'stats' => $this->stats,
            'allKategori' => $this->allKategoriSuggestions,
        ]);
    }
}
