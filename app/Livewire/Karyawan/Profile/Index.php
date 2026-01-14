<?php

namespace App\Livewire\Karyawan\Profile;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Auth;

#[Layout('components.layouts.app')]
class Index extends Component
{
    public $karyawan;
    public $user;

    public function mount()
    {
        $this->user = Auth::user();
        $this->karyawan = $this->user->karyawan()->with(['jabatan', 'departemen'])->first();
    }

    public function openModal($modalType)
    {
        $this->dispatch('open-' . $modalType . '-modal');
    }

    public function logout()
    {
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();
        
        return redirect()->route('login');
    }

    #[Title('Profile - Karyawan')]
    public function render()
    {
        return view('livewire.karyawan.profile.index');
    }
}
