<?php

namespace App\Livewire\Admin\Lokasi\Modals;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\Lokasi;

class Delete extends Component
{
    public $lokasiId;
    public $nama_lokasi;
    
    public $showSuccess = false;
    public $showError = false;
    public $errorMessage = '';

    #[On('delete-lokasi')]
    public function confirmDelete($id)
    {
        $lokasi = Lokasi::findOrFail($id);
        
        $this->lokasiId = $lokasi->id;
        $this->nama_lokasi = $lokasi->nama_lokasi;
        
        $this->showSuccess = false;
        $this->showError = false;

        $this->dispatch('openDeleteModal');
    }

    public function closeModal()
    {
        $this->reset(['lokasiId', 'nama_lokasi', 'showError', 'errorMessage']);
        $this->dispatch('closeDeleteModal');
    }

    public function delete()
    {
        // Reset toast state
        $this->showSuccess = false;
        $this->showError = false;
        
        try {
            $lokasi = Lokasi::findOrFail($this->lokasiId);
            $lokasi->delete();

            // Tampilkan toast success
            $this->showSuccess = true;
            $this->showError = false;

            // Refresh parent component
            $this->dispatch('lokasi-deleted');
            
            // Close modal
            $this->dispatch('closeDeleteModal');
            
        } catch (\Exception $e) {
            // Jika gagal
            $this->showError = true;
            $this->showSuccess = false;
            $this->errorMessage = 'Gagal menghapus lokasi: ' . $e->getMessage();
        }
    }

    public function render()
    {
        return view('livewire.admin.lokasi.modals.delete');
    }
}
