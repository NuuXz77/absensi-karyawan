<?php

namespace App\Livewire\Admin\Departemen\Modals;

use App\Models\Departemen;
use Livewire\Component;

class Create extends Component
{
    public $nama_departemen = '';
    public $kode_departemen = '';
    public $status = 'active';

    public $showSuccess = false;
    public $showError = false;
    public $errorMessage = '';

    protected $rules = [
        'nama_departemen' => 'required|string|max:255|unique:departemen,nama_departemen',
        'kode_departemen' => 'required|string|max:50|unique:departemen,kode_departemen',
    ];

    protected $messages = [
        'nama_departemen.required' => 'Nama departemen wajib diisi',
        'nama_departemen.unique' => 'Nama departemen sudah digunakan',
        'kode_departemen.required' => 'Kode departemen wajib diisi',
        'kode_departemen.unique' => 'Kode departemen sudah digunakan',
    ];

    public function openModal()
    {
        $this->resetValidation();
        $this->showSuccess = false;
        $this->showError = false;
        $this->dispatch('open-create-modal');
    }

    public function save()
    {
        // Reset toast state setiap kali login dipanggil
        $this->showSuccess = false;
        $this->showError = false;

        // Validasi input
        $this->validate();

        try {
            Departemen::create([
                'nama_departemen' => $this->nama_departemen,
                'kode_departemen' => strtoupper($this->kode_departemen),
                'status' => $this->status,
            ]);

            // Tampilkan toast success
            $this->showSuccess = true;
            $this->showError = false;
            
            $this->reset(['nama_departemen', 'kode_departemen']);
            $this->status = 'active';
            $this->resetValidation();

            // Refresh parent component
            $this->dispatch('departemen-created');
            
            // Close modal
            $this->dispatch('close-create-modal');
            
        } catch (\Exception $e) {
            // Jika gagal
            $this->showError = true;
            $this->showSuccess = false;
            $this->errorMessage = 'Gagal menyimpan data: ' . $e->getMessage();
        }
    }

    public function closeModal()
    {
        $this->reset(['nama_departemen', 'kode_departemen', 'showError', 'errorMessage']);
        $this->status = 'active';
        $this->resetValidation();
        $this->dispatch('close-create-modal');
    }

    public function render()
    {
        return view('livewire.admin.departemen.modals.create');
    }
}
