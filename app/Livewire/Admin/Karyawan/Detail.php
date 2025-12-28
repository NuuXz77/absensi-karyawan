<?php

namespace App\Livewire\Admin\Karyawan;

use Livewire\Component;
use App\Models\Karyawan;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('components.layouts.app')]
class Detail extends Component
{
    public Karyawan $karyawan;

    public function mount($id)
    {
        $this->karyawan = Karyawan::with(['user', 'jabatan.departemen', 'departemen', 'wajah'])
            ->withCount(['absensi', 'izin', 'cuti'])
            ->findOrFail($id);
    }

    #[Title('Detail Karyawan')]
    public function render()
    {
        return view('livewire.admin.karyawan.detail');
    }
}
