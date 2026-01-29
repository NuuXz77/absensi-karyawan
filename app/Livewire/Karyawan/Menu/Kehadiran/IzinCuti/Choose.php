<?php

namespace App\Livewire\Karyawan\Menu\Kehadiran\IzinCuti;

use Livewire\Component;

class Choose extends Component
{
    public $showSuccess = false;
    public $showError = false;
    public $successMessage = '';
    public $errorMessage = '';

    public function openModal()
    {
        $this->dispatch('open-choose-modal');
    }

    public function closeModal()
    {
        $this->dispatch('close-choose-modal');
    }

    public function goToIzin()
    {
        $this->closeModal();
        return $this->redirect(route('karyawan.izin.create'), navigate: true);
    }

    public function goToCuti()
    {
        $this->closeModal();
        return $this->redirect(route('karyawan.cuti.create'), navigate: true);
    }

    public function render()
    {
        return view('livewire.karyawan.menu.kehadiran.izin-cuti.choose');
    }
}
