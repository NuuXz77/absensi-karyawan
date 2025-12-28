<?php

namespace App\Livewire\Admin\Departemen\Modals;

use App\Models\Departemen;
use Livewire\Component;

class Edit extends Component
{
    public $departemenId;
    public $nama_departemen = '';
    public $kode_departemen = '';
    public $status = 'active';

    public $showSuccess = false;
    public $showError = false;
    public $errorMessage = '';

    protected $messages = [
        'nama_departemen.required' => 'Nama departemen wajib diisi',
        'nama_departemen.unique' => 'Nama departemen sudah digunakan',
        'kode_departemen.required' => 'Kode departemen wajib diisi',
        'kode_departemen.unique' => 'Kode departemen sudah digunakan',
    ];

    protected $listeners = ['open-edit-modal' => 'loadDepartemen'];

    protected function rules()
    {
        return [
            'nama_departemen' => 'required|string|max:255|unique:departemen,nama_departemen,' . $this->departemenId,
            'kode_departemen' => 'required|string|max:50|unique:departemen,kode_departemen,' . $this->departemenId,
        ];
    }

    public function loadDepartemen($id)
    {
        $this->departemenId = $id;
        $departemen = Departemen::findOrFail($id);
        
        $this->nama_departemen = $departemen->nama_departemen;
        $this->kode_departemen = $departemen->kode_departemen;
        $this->status = $departemen->status;
        
        $this->resetValidation();
        $this->showSuccess = false;
        $this->showError = false;
    }

    public function update()
    {
        // Reset toast state
        $this->showSuccess = false;
        $this->showError = false;

        // Validasi input
        $this->validate();

        try {
            $departemen = Departemen::findOrFail($this->departemenId);
            
            $departemen->update([
                'nama_departemen' => $this->nama_departemen,
                'kode_departemen' => strtoupper($this->kode_departemen),
                'status' => $this->status,
            ]);

            // Tampilkan toast success
            $this->showSuccess = true;
            $this->showError = false;
            
            // Refresh parent component
            $this->dispatch('departemen-updated');
            
            // Close modal
            $this->dispatch('close-edit-modal');
            
        } catch (\Exception $e) {
            // Jika gagal
            $this->showError = true;
            $this->showSuccess = false;
            $this->errorMessage = 'Gagal mengupdate data: ' . $e->getMessage();
        }
    }

    public function closeModal()
    {
        $this->reset(['nama_departemen', 'kode_departemen', 'departemenId', 'showError', 'errorMessage']);
        $this->status = 'active';
        $this->resetValidation();
        $this->dispatch('close-edit-modal');
    }

    public function render()
    {
        return view('livewire.admin.departemen.modals.edit');
    }
}
