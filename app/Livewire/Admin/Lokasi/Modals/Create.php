<?php

namespace App\Livewire\Admin\Lokasi\Modals;

use Livewire\Component;
use App\Models\Lokasi;

class Create extends Component
{
    public $nama_lokasi;
    public $latitude;
    public $longitude;
    public $radius_meter = 100;
    public $status = 'active';
    
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

    public function openModal()
    {
        $this->resetValidation();
        $this->showSuccess = false;
        $this->showError = false;
        $this->dispatch('openCreateModal');
    }

    public function closeModal()
    {
        $this->reset(['nama_lokasi', 'latitude', 'longitude', 'radius_meter', 'showError', 'errorMessage']);
        $this->status = 'active';
        $this->resetValidation();
        $this->dispatch('closeCreateModal');
    }

    public function save()
    {
        // Reset toast state
        $this->showSuccess = false;
        $this->showError = false;
        
        // Get status from checkbox
        $this->status = request()->input('status') ? 'active' : 'inactive';
        
        // Validasi input
        $this->validate();

        try {
            Lokasi::create([
                'nama_lokasi' => $this->nama_lokasi,
                'latitude' => $this->latitude,
                'longitude' => $this->longitude,
                'radius_meter' => $this->radius_meter,
                'status' => $this->status,
            ]);

            // Tampilkan toast success
            $this->showSuccess = true;
            $this->showError = false;
            
            $this->reset(['nama_lokasi', 'latitude', 'longitude', 'radius_meter']);
            $this->status = 'active';
            $this->resetValidation();

            // Refresh parent component
            $this->dispatch('lokasi-created');
            
            // Close modal
            $this->dispatch('closeCreateModal');
            
        } catch (\Exception $e) {
            // Jika gagal
            $this->showError = true;
            $this->showSuccess = false;
            $this->errorMessage = 'Gagal menambahkan lokasi: ' . $e->getMessage();
        }
    }

    public function render()
    {
        return view('livewire.admin.lokasi.modals.create');
    }
}
