<?php

namespace App\Livewire\Admin\Lokasi\Modals;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\Lokasi;

class Detail extends Component
{
    public $lokasiId;
    public $nama_lokasi;
    public $latitude;
    public $longitude;
    public $radius_meter;
    public $status;

    #[On('view-lokasi')]
    public function view($id)
    {
        $lokasi = Lokasi::findOrFail($id);

        $this->lokasiId = $lokasi->id;
        $this->nama_lokasi = $lokasi->nama_lokasi;
        $this->latitude = $lokasi->latitude;
        $this->longitude = $lokasi->longitude;
        $this->radius_meter = $lokasi->radius_meter;
        $this->status = $lokasi->status;

        $this->dispatch('openDetailModal', ['latitude' => $lokasi->latitude, 'longitude' => $lokasi->longitude]);
    }

    public function closeModal()
    {
        $this->reset(['lokasiId', 'nama_lokasi', 'latitude', 'longitude', 'radius_meter', 'status']);
        $this->dispatch('closeDetailModal');
    }

    public function render()
    {
        return view('livewire.admin.lokasi.modals.detail');
    }
}
