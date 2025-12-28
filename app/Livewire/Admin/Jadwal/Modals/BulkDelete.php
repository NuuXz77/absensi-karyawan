<?php

namespace App\Livewire\Admin\Jadwal\Modals;

use App\Models\JadwalKerja;
use App\Models\Departemen;
use Livewire\Component;
use Carbon\Carbon;

class BulkDelete extends Component
{
    public $tanggal_mulai = '';
    public $tanggal_selesai = '';
    public $departemen_id = '';
    public $confirmText = '';

    public $showSuccess = false;
    public $showError = false;
    public $successMessage = '';
    public $errorMessage = '';

    protected $listeners = ['open-bulk-delete-modal' => 'openModal'];

    protected $rules = [
        'tanggal_mulai' => 'required|date',
        'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
    ];

    protected $messages = [
        'tanggal_mulai.required' => 'Tanggal mulai wajib diisi',
        'tanggal_selesai.required' => 'Tanggal selesai wajib diisi',
        'tanggal_selesai.after_or_equal' => 'Tanggal selesai harus setelah atau sama dengan tanggal mulai',
    ];

    public function openModal()
    {
        $this->tanggal_mulai = now()->startOfMonth()->format('Y-m-d');
        $this->tanggal_selesai = now()->endOfMonth()->format('Y-m-d');
        $this->departemen_id = '';
        $this->confirmText = '';
        $this->resetValidation();
        $this->showSuccess = false;
        $this->showError = false;
        
        $this->dispatch('open-bulk-delete-modal-dialog');
    }

    public function getPreviewCount()
    {
        $this->validate();

        $query = JadwalKerja::whereBetween('tanggal', [$this->tanggal_mulai, $this->tanggal_selesai]);

        if ($this->departemen_id) {
            $query->whereHas('karyawan', function($q) {
                $q->where('departemen_id', $this->departemen_id);
            });
        }

        return $query->count();
    }

    public function delete()
    {
        $this->showSuccess = false;
        $this->showError = false;

        $this->validate();

        if (strtoupper($this->confirmText) !== 'HAPUS') {
            $this->showError = true;
            $this->errorMessage = 'Ketik "HAPUS" untuk konfirmasi';
            return;
        }

        try {
            $query = JadwalKerja::whereBetween('tanggal', [$this->tanggal_mulai, $this->tanggal_selesai]);

            if ($this->departemen_id) {
                $query->whereHas('karyawan', function($q) {
                    $q->where('departemen_id', $this->departemen_id);
                });
            }

            $count = $query->count();
            $query->delete();

            $this->showSuccess = true;
            $this->successMessage = "Berhasil menghapus {$count} jadwal!";
            
            $this->reset(['tanggal_mulai', 'tanggal_selesai', 'departemen_id', 'confirmText']);
            $this->resetValidation();

            $this->dispatch('jadwal-deleted');
            $this->dispatch('close-bulk-delete-modal');
            
        } catch (\Exception $e) {
            $this->showError = true;
            $this->showSuccess = false;
            $this->errorMessage = 'Gagal menghapus jadwal: ' . $e->getMessage();
        }
    }

    public function closeModal()
    {
        $this->reset(['tanggal_mulai', 'tanggal_selesai', 'departemen_id', 'confirmText', 'showError', 'errorMessage']);
        $this->resetValidation();
        $this->dispatch('close-bulk-delete-modal');
    }

    public function render()
    {
        $departemens = Departemen::where('status', 'active')
            ->orderBy('nama_departemen')
            ->get();

        $previewCount = 0;
        if ($this->tanggal_mulai && $this->tanggal_selesai) {
            try {
                $previewCount = $this->getPreviewCount();
            } catch (\Exception $e) {
                $previewCount = 0;
            }
        }

        return view('livewire.admin.jadwal.modals.bulk-delete', [
            'departemens' => $departemens,
            'previewCount' => $previewCount,
        ]);
    }
}
