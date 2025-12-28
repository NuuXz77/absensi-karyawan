<?php

namespace App\Livewire\Admin\Shift\Modals;

use App\Models\Shift;
use Livewire\Component;

class Delete extends Component
{
    public $shiftId;
    public $shift;

    public $showSuccess = false;
    public $showError = false;
    public $errorMessage = '';

    protected $listeners = ['confirm-delete' => 'loadShift'];

    public function loadShift($id)
    {
        $this->shiftId = $id;
        $this->shift = Shift::withCount('jadwalKerja')
            ->findOrFail($id);
        
        $this->showSuccess = false;
        $this->showError = false;
    }

    public function delete()
    {
        $this->showSuccess = false;
        $this->showError = false;

        try {
            $shift = Shift::findOrFail($this->shiftId);
            
            // Check if has jadwal kerja
            if ($shift->jadwalKerja()->count() > 0) {
                $this->showError = true;
                $this->errorMessage = 'Tidak dapat menghapus shift yang masih digunakan dalam jadwal kerja!';
                return;
            }
            
            $shift->delete();

            $this->showSuccess = true;
            $this->showError = false;
            
            $this->dispatch('shift-deleted');
            $this->dispatch('close-delete-modal');
            
        } catch (\Exception $e) {
            $this->showError = true;
            $this->showSuccess = false;
            $this->errorMessage = 'Gagal menghapus data: ' . $e->getMessage();
        }
    }

    public function closeModal()
    {
        $this->reset(['shiftId', 'shift', 'showError', 'errorMessage']);
        $this->dispatch('close-delete-modal');
    }

    public function render()
    {
        return view('livewire.admin.shift.modals.delete');
    }
}
