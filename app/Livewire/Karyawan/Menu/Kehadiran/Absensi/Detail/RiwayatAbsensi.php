<?php

namespace App\Livewire\Karyawan\Menu\Kehadiran\Absensi\Detail;

use App\Models\Absensi;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class RiwayatAbsensi extends Component
{
    public $absensiId;
    public $absensi;

    public function mount($id)
    {
        $this->absensiId = $id;
        
        // Ambil data absensi dengan memastikan milik user yang login
        $this->absensi = Absensi::with(['lokasi', 'karyawan.departemen', 'karyawan.jabatan'])
            ->where('id', $id)
            ->whereHas('karyawan', function ($query) {
                $query->where('user_id', Auth::id());
            })
            ->firstOrFail();
    }

    public function render()
    {
        return view('livewire.karyawan.menu.kehadiran.absensi.detail.riwayat-absensi');
    }
}
