<?php

namespace App\Livewire\Admin\Jabatan\Modals;

use App\Models\Jabatan;
use App\Models\Departemen;
use Livewire\Component;

class Edit extends Component
{
    public $jabatanId;
    public $kode_jabatan = '';
    public $nama_jabatan = '';
    public $departemen_id = '';
    public $status = 'active';

    public $showSuccess = false;
    public $showError = false;
    public $errorMessage = '';

    protected $messages = [
        'kode_jabatan.required' => 'Kode jabatan wajib diisi',
        'kode_jabatan.unique' => 'Kode jabatan sudah digunakan',
        'nama_jabatan.required' => 'Nama jabatan wajib diisi',
        'nama_jabatan.unique' => 'Nama jabatan sudah digunakan',
        'departemen_id.required' => 'Departemen wajib dipilih',
        'departemen_id.exists' => 'Departemen tidak valid',
    ];

    protected $listeners = ['open-edit-modal' => 'loadJabatan'];

    protected function rules()
    {
        return [
            'kode_jabatan' => 'required|string|max:50|unique:jabatan,kode_jabatan,' . $this->jabatanId,
            'nama_jabatan' => 'required|string|max:255|unique:jabatan,nama_jabatan,' . $this->jabatanId,
            'departemen_id' => 'required|exists:departemen,id',
        ];
    }

    public function loadJabatan($id)
    {
        $this->jabatanId = $id;
        $jabatan = Jabatan::findOrFail($id);
        
        $this->kode_jabatan = $jabatan->kode_jabatan;
        $this->nama_jabatan = $jabatan->nama_jabatan;
        $this->departemen_id = $jabatan->departemen_id;
        $this->status = $jabatan->status;
        
        $this->resetValidation();
        $this->showSuccess = false;
        $this->showError = false;
    }

    public function update()
    {
        $this->showSuccess = false;
        $this->showError = false;

        $this->validate();

        try {
            $jabatan = Jabatan::findOrFail($this->jabatanId);
            
            $jabatan->update([
                'kode_jabatan' => strtoupper($this->kode_jabatan),
                'nama_jabatan' => $this->nama_jabatan,
                'departemen_id' => $this->departemen_id,
                'status' => $this->status,
            ]);

            $this->showSuccess = true;
            $this->showError = false;
            
            $this->dispatch('jabatan-updated');
            $this->dispatch('close-edit-modal');
            
        } catch (\Exception $e) {
            $this->showError = true;
            $this->showSuccess = false;
            $this->errorMessage = 'Gagal mengupdate data: ' . $e->getMessage();
        }
    }

    public function closeModal()
    {
        $this->reset(['kode_jabatan', 'nama_jabatan', 'departemen_id', 'jabatanId', 'showError', 'errorMessage']);
        $this->status = 'active';
        $this->resetValidation();
        $this->dispatch('close-edit-modal');
    }

    public function render()
    {
        $departemens = Departemen::where('status', 'active')
            ->orderBy('nama_departemen')
            ->get();

        return view('livewire.admin.jabatan.modals.edit', [
            'departemens' => $departemens
        ]);
    }
}
