<?php

namespace App\Livewire\Karyawan\Dashboard;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('components.layouts.app')]
class Index extends Component
{
#[Title('Dashboard')]
    public function render()
    {
        return view('livewire.karyawan.dashboard.index');
    }
}
