<?php

namespace App\Livewire\Admin\Jabatan\Modals;

use App\Models\Jabatan;
use App\Models\Departemen;
use Livewire\Component;

class Create extends Component
{
    public $kode_jabatan = '';
    public $nama_jabatan = '';
    public $departemen_id = '';
    public $status = 'active';

    public $showSuccess = false;
    public $showError = false;
    public $errorMessage = '';

    protected $rules = [
        'kode_jabatan' => 'required|string|max:50|unique:jabatan,kode_jabatan',
        'nama_jabatan' => 'required|string|max:255|unique:jabatan,nama_jabatan',
        'departemen_id' => 'required|exists:departemen,id',
    ];

    protected $messages = [
        'kode_jabatan.required' => 'Kode jabatan wajib diisi',
        'kode_jabatan.unique' => 'Kode jabatan sudah digunakan',
        'nama_jabatan.required' => 'Nama jabatan wajib diisi',
        'nama_jabatan.unique' => 'Nama jabatan sudah digunakan',
        'departemen_id.required' => 'Departemen wajib dipilih',
        'departemen_id.exists' => 'Departemen tidak valid',
    ];

    public function save()
    {
        // Reset toast state setiap kali save dipanggil
        $this->showSuccess = false;
        $this->showError = false;

        // Validasi input
        $this->validate();

        try {
            Jabatan::create([
                'kode_jabatan' => strtoupper($this->kode_jabatan),
                'nama_jabatan' => $this->nama_jabatan,
                'departemen_id' => $this->departemen_id,
                'status' => $this->status,
            ]);

            sleep(3);

            // Tampilkan toast success
            $this->showSuccess = true;
            
            // Reset form
            $this->reset(['kode_jabatan', 'nama_jabatan', 'departemen_id']);
            $this->status = 'active';
            $this->resetValidation();

            // Refresh parent component
            $this->dispatch('jabatan-created');
            
            // Close modal dengan event yang konsisten
            $this->dispatch('close-create-modal');
            
        } catch (\Exception $e) {
            // Jika gagal
            $this->showError = true;
            $this->errorMessage = 'Gagal menyimpan data: ' . $e->getMessage();
            
            // Tetap tutup modal jika diperlukan
            $this->dispatch('close-create-modal');
        }
    }

    public function closeModal()
    {
        $this->reset(['kode_jabatan', 'nama_jabatan', 'departemen_id', 'showError', 'errorMessage', 'showSuccess']);
        $this->status = 'active';
        $this->resetValidation();
        $this->dispatch('close-create-modal');
    }

    public function render()
    {
        $departemens = Departemen::where('status', 'active')
            ->orderBy('nama_departemen')
            ->get();

        return view('livewire.admin.jabatan.modals.create', [
            'departemens' => $departemens
        ]);
    }
}
