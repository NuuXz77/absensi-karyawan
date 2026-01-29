<?php

namespace App\Livewire\Admin\Departemen\Modals;

use App\Models\Departemen;
use Livewire\Component;

class Delete extends Component
{
    public $departemenId;
    public $nama_departemen = '';
    public $kode_departemen = '';
    public $status = '';
    public $karyawan_count = 0;

    public $showSuccess = false;
    public $showError = false;
    public $errorMessage = '';

    protected $listeners = ['confirm-delete' => 'loadDepartemen'];

    public function loadDepartemen($id)
    {
        $this->departemenId = $id;
        $departemen = Departemen::withCount('karyawans')->findOrFail($id);
        
        $this->nama_departemen = $departemen->nama_departemen;
        $this->kode_departemen = $departemen->kode_departemen;
        $this->status = ucfirst($departemen->status);
        $this->karyawan_count = $departemen->karyawans_count ?? 0;
        
        $this->showSuccess = false;
        $this->showError = false;
    }

    public function delete()
    {
        // Reset toast state
        $this->showSuccess = false;
        $this->showError = false;

        try {
            $departemen = Departemen::findOrFail($this->departemenId);
            
            // Optional: Check if has karyawan
            if ($departemen->karyawans()->count() > 0) {
                $this->showError = true;
                $this->errorMessage = 'Tidak dapat menghapus departemen yang masih memiliki karyawan!';
                return;
            }
            
            $departemen->delete();

            // Tampilkan toast success
            $this->showSuccess = true;
            $this->showError = false;
            
            // Refresh parent component
            $this->dispatch('departemen-deleted');
            
            // Close modal
            $this->dispatch('close-delete-modal');
            
        } catch (\Exception $e) {
            // Jika gagal
            $this->showError = true;
            $this->showSuccess = false;
            $this->errorMessage = 'Gagal menghapus data: ' . $e->getMessage();
        }
    }

    public function closeModal()
    {
        $this->reset(['departemenId', 'nama_departemen', 'kode_departemen', 'status', 'karyawan_count', 'showError', 'errorMessage']);
        $this->dispatch('close-delete-modal');
    }

    public function render()
    {
        return view('livewire.admin.departemen.modals.delete');
    }
}
