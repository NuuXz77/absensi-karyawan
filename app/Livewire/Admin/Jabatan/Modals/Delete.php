<?php

namespace App\Livewire\Admin\Jabatan\Modals;

use App\Models\Jabatan;
use Livewire\Component;

class Delete extends Component
{
    public $jabatanId;
    public $jabatan;

    public $showSuccess = false;
    public $showError = false;
    public $errorMessage = '';

    protected $listeners = ['confirm-delete' => 'loadJabatan'];

    public function loadJabatan($id)
    {
        $this->jabatanId = $id;
        $this->jabatan = Jabatan::with('departemen')
            ->withCount('karyawans')
            ->findOrFail($id);
        
        $this->showSuccess = false;
        $this->showError = false;
    }

    public function delete()
    {
        $this->showSuccess = false;
        $this->showError = false;

        try {
            $jabatan = Jabatan::findOrFail($this->jabatanId);
            
            // Check if has karyawan
            if ($jabatan->karyawans()->count() > 0) {
                $this->showError = true;
                $this->errorMessage = 'Tidak dapat menghapus jabatan yang masih memiliki karyawan!';
                return;
            }
            
            $jabatan->delete();

            $this->showSuccess = true;
            $this->showError = false;
            
            $this->dispatch('jabatan-deleted');
            $this->dispatch('close-delete-modal');
            
        } catch (\Exception $e) {
            $this->showError = true;
            $this->showSuccess = false;
            $this->errorMessage = 'Gagal menghapus data: ' . $e->getMessage();
        }
    }

    public function closeModal()
    {
        $this->reset(['jabatanId', 'jabatan', 'showError', 'errorMessage']);
        $this->dispatch('close-delete-modal');
    }

    public function render()
    {
        return view('livewire.admin.jabatan.modals.delete');
    }
}
