<?php

namespace App\Livewire\Karyawan\Menu;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('components.layouts.app')]
class Index extends Component
{
    #[Title('Menu - Karyawan')]
    public function render()
    {
        return view('livewire.karyawan.menu.index');
    }
}
