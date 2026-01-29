<?php

namespace App\Livewire\Admin\Lokasi\Modals;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\Lokasi;

class Edit extends Component
{
    public $lokasiId;
    public $nama_lokasi;
    public $latitude;
    public $longitude;
    public $radius_meter;
    public $status;

    public $showSuccess = false;
    public $showError = false;
    public $errorMessage = '';

    protected $rules = [
        'nama_lokasi' => 'required|string|max:255',
        'latitude' => 'required|numeric|between:-90,90',
        'longitude' => 'required|numeric|between:-180,180',
        'radius_meter' => 'required|integer|min:1',
        'status' => 'required|in:active,inactive',
    ];

    protected $messages = [
        'nama_lokasi.required' => 'Nama lokasi wajib diisi',
        'latitude.required' => 'Latitude wajib diisi',
        'longitude.required' => 'Longitude wajib diisi',
        'radius_meter.required' => 'Radius wajib diisi',
        'radius_meter.min' => 'Radius minimal 1 meter',
    ];

    #[On('edit-lokasi')]
    public function edit($id)
    {
        $lokasi = Lokasi::findOrFail($id);

        $this->lokasiId = $lokasi->id;
        $this->nama_lokasi = $lokasi->nama_lokasi;
        $this->latitude = $lokasi->latitude;
        $this->longitude = $lokasi->longitude;
        $this->radius_meter = $lokasi->radius_meter;
        $this->status = $lokasi->status;

        $this->resetValidation();
        $this->showSuccess = false;
        $this->showError = false;

        $this->dispatch('openEditModal', ['latitude' => $lokasi->latitude, 'longitude' => $lokasi->longitude]);
    }

    public function closeModal()
    {
        $this->reset(['lokasiId', 'nama_lokasi', 'latitude', 'longitude', 'radius_meter', 'status', 'showError', 'errorMessage']);
        $this->resetValidation();
        $this->dispatch('closeEditModal');
    }

    public function update()
    {
        // Reset toast state
        $this->showSuccess = false;
        $this->showError = false;

        // Status sudah dalam format 'active' atau 'inactive' dari blade
        // Tidak perlu konversi lagi

        // Validasi input
        $this->validate();

        try {
            $lokasi = Lokasi::findOrFail($this->lokasiId);

            $lokasi->update([
                'nama_lokasi' => $this->nama_lokasi,
                'latitude' => $this->latitude,
                'longitude' => $this->longitude,
                'radius_meter' => $this->radius_meter,
                'status' => $this->status,
            ]);

            // Tampilkan toast success
            $this->showSuccess = true;
            $this->showError = false;

            $this->resetValidation();

            // Refresh parent component
            $this->dispatch('lokasi-updated');

            // Close modal
            $this->dispatch('closeEditModal');
        } catch (\Exception $e) {
            // Jika gagal
            $this->showError = true;
            $this->showSuccess = false;
            $this->errorMessage = 'Gagal mengupdate lokasi: ' . $e->getMessage();
        }
    }

    public function render()
    {
        return view('livewire.admin.lokasi.modals.edit');
    }
}
