<?php

namespace App\Livewire\Admin\Departemen\Modals;

use App\Models\Departemen;
use Livewire\Component;

class Delete extends Component
{
    public $departemenId;
    public $departemen;

    public $showSuccess = false;
    public $showError = false;
    public $errorMessage = '';

    protected $listeners = ['confirm-delete' => 'loadDepartemen'];

    public function loadDepartemen($id)
    {
        $this->departemenId = $id;
        $this->departemen = Departemen::withCount('karyawans')->findOrFail($id);
        
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
        $this->reset(['departemenId', 'departemen', 'showError', 'errorMessage']);
        $this->dispatch('close-delete-modal');
    }

    public function render()
    {
        return view('livewire.admin.departemen.modals.delete');
    }
}
