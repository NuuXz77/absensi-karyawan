<?php

namespace App\Livewire\Admin\Absensi;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\Absensi;

#[Layout('components.layouts.app')]
class Detail extends Component
{
    public $absensiId;
    public $absensi;

    public function mount($id)
    {
        $this->absensiId = $id;
        $this->absensi = Absensi::with(['karyawan.departemen', 'karyawan.jabatan', 'lokasi'])
            ->findOrFail($id);
    }

    #[Title('Detail Absensi')]
    public function render()
    {
        return view('livewire.admin.absensi.detail');
    }
}
