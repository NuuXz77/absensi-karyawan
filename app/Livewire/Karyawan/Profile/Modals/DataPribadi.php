<?php

namespace App\Livewire\Karyawan\Profile\Modals;

use Livewire\Component;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Auth;

class DataPribadi extends Component
{
    public $karyawan;

    public function mount()
    {
        $user = Auth::user();
        $this->karyawan = $user->karyawan()->with(['jabatan', 'departemen'])->first();
    }

    public function closeModal()
    {
        $this->dispatch('close-data-pribadi-modal');
    }

    public function render()
    {
        return view('livewire.karyawan.profile.modals.data-pribadi');
    }
}
