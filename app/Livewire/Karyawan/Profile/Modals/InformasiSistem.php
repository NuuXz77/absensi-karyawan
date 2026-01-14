<?php

namespace App\Livewire\Karyawan\Profile\Modals;

use Livewire\Component;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Auth;

class InformasiSistem extends Component
{
    public $user;

    public function mount()
    {
        $this->user = Auth::user();
    }

    public function closeModal()
    {
        $this->dispatch('close-informasi-sistem-modal');
    }

    public function render()
    {
        return view('livewire.karyawan.profile.modals.informasi-sistem');
    }
}
