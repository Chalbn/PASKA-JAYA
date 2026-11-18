<?php

namespace App\Livewire\Kasir;

use App\Models\AbsensiKaryawan;
use App\Services\AbsensiService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use Throwable;

#[Layout('components.layouts.app')]
class Kehadiran extends Component
{
    use WithPagination;

    // Filter
    public $search = '';

    public $filterKaryawan = '';

    public $filterTanggal = '';

    // Modal
    public $modalOpen = false;

    public $modalHapusOpen = false;

    public $editingId = null;

    public $hapusId = null;

    // Form kehadiran
    public $karyawan_id = '';

    public $tanggal = '';

    public $shift = '';

    public $pekerjaan = [''];

    protected function rules(): array
    {
        return [
            'karyawan_id' => 'required|exists:users,id',
            'tanggal' => 'required|date',
            'shift' => 'nullable|in:pagi,siang,full',
            'pekerjaan' => 'required|array|min:1',
            'pekerjaan.*' => 'required|string|max:150',
        ];
    }

    protected $messages = [
        'karyawan_id.required' => 'Nama karyawan wajib dipilih.',
        'tanggal.required' => 'Tanggal kehadiran wajib diisi.',
        'pekerjaan.required' => 'Minimal satu pekerjaan harus diisi.',
        'pekerjaan.*.required' => 'Deskripsi pekerjaan tidak boleh kosong.',
        'pekerjaan.*.max' => 'Pekerjaan maksimal 150 karakter.',
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilterKaryawan(): void
    {
        $this->resetPage();
    }

    public function updatingFilterTanggal(): void
    {
        $this->resetPage();
    }

    public function bukaModalTambah(): void
    {
        $this->resetForm();
        $this->tanggal = now()->toDateString();
        $this->modalOpen = true;
    }

    public function bukaModalEdit(int $id): void
    {
        $absensi = AbsensiKaryawan::with('detailPekerjaan')
            ->findOrFail($id);

        $this->editingId = $absensi->id;
        $this->karyawan_id = $absensi->karyawan_id;
        $this->tanggal = $absensi->tanggal->format('Y-m-d');
        $this->shift = $absensi->shift ?? '';

        $this->pekerjaan = $absensi->detailPekerjaan
            ->sortBy('urutan')
            ->pluck('deskripsi')
            ->values()
            ->toArray();

        if (empty($this->pekerjaan)) {
            $this->pekerjaan = [''];
        }

        $this->resetValidation();
        $this->modalOpen = true;
    }

    public function tambahPekerjaan(): void
    {
        $this->pekerjaan[] = '';
    }

    public function hapusPekerjaan(int $index): void
    {
        if (count($this->pekerjaan) <= 1) {
            return;
        }

        unset($this->pekerjaan[$index]);

        $this->pekerjaan = array_values($this->pekerjaan);
    }

    public function simpan(AbsensiService $service): void
    {
        $data = $this->validate();

        try {
            if ($this->editingId) {
                $service->update($this->editingId, $data);

                session()->flash(
                    'success',
                    'Data kehadiran berhasil diperbarui.'
                );
            } else {
                $service->simpan($data, Auth::id());

                session()->flash(
                    'success',
                    'Kehadiran berhasil ditambahkan.'
                );
            }

            $this->tutupModal();
            $this->resetPage();

        } catch (ValidationException $exception) {
            throw $exception;
        } catch (Throwable $exception) {
            report($exception);

            session()->flash(
                'error',
                'Terjadi kesalahan saat menyimpan kehadiran.'
            );
        }
    }

    public function konfirmasiHapus(int $id): void
    {
        $this->hapusId = $id;
        $this->modalHapusOpen = true;
    }

    public function hapus(AbsensiService $service): void
    {
        if (! $this->hapusId) {
            return;
        }

        try {
            $service->hapus($this->hapusId);

            session()->flash(
                'success',
                'Catatan kehadiran berhasil dihapus.'
            );

            $this->modalHapusOpen = false;
            $this->hapusId = null;

        } catch (Throwable $exception) {
            report($exception);

            session()->flash(
                'error',
                'Terjadi kesalahan saat menghapus kehadiran.'
            );
        }
    }

    public function tutupModal(): void
    {
        $this->modalOpen = false;
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->reset([
            'editingId',
            'karyawan_id',
            'tanggal',
            'shift',
            'pekerjaan',
        ]);

        $this->pekerjaan = [''];

        $this->resetValidation();
    }

    public function render(AbsensiService $service)
    {
        return view('livewire.kasir.kehadiran', [
            'daftarAbsensi' => $service->getDaftarAbsensi([
                'cari' => $this->search,
                'karyawan_id' => $this->filterKaryawan,
                'tanggal' => $this->filterTanggal,
            ]),
            'daftarKaryawan' => $service->getDaftarKaryawanAktif(),
        ]);
    }
}
