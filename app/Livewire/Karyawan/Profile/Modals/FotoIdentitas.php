<?php

namespace App\Livewire\Karyawan\Profile\Modals;

use Livewire\Component;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Auth;

class FotoIdentitas extends Component
{
    public $karyawan;

    public function mount()
    {
        $user = Auth::user();
        $this->karyawan = $user->karyawan()->first();
    }

    public function closeModal()
    {
        $this->dispatch('close-foto-identitas-modal');
    }

    public function render()
    {
        return view('livewire.karyawan.profile.modals.foto-identitas');
    }
}
