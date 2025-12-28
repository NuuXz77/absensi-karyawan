<?php

namespace App\Livewire\Admin\Jadwal\Modals;

use App\Models\JadwalKerja;
use Livewire\Component;

class Delete extends Component
{
    public $jadwalId;
    public $jadwalInfo;

    public $showSuccess = false;
    public $showError = false;
    public $errorMessage = '';

    protected $listeners = ['confirm-delete-jadwal' => 'confirmDelete'];

    public function confirmDelete($id)
    {
        $jadwal = JadwalKerja::with(['karyawan.departemen', 'shift'])->findOrFail($id);
        
        $this->jadwalId = $jadwal->id;
        $this->jadwalInfo = [
            'karyawan' => $jadwal->karyawan->nama_lengkap,
            'departemen' => $jadwal->karyawan->departemen->nama_departemen ?? '-',
            'tanggal' => \Carbon\Carbon::parse($jadwal->tanggal)->format('d M Y'),
            'shift' => $jadwal->shift->nama_shift,
        ];

        $this->showSuccess = false;
        $this->showError = false;
        
        $this->dispatch('open-delete-modal');
    }

    public function delete()
    {
        $this->showSuccess = false;
        $this->showError = false;

        try {
            $jadwal = JadwalKerja::findOrFail($this->jadwalId);
            $jadwal->delete();

            $this->showSuccess = true;
            $this->showError = false;

            $this->dispatch('jadwal-deleted');
            $this->dispatch('close-delete-modal');
            
        } catch (\Exception $e) {
            $this->showError = true;
            $this->showSuccess = false;
            $this->errorMessage = 'Gagal menghapus jadwal: ' . $e->getMessage();
        }
    }

    public function closeModal()
    {
        $this->reset(['jadwalId', 'jadwalInfo', 'showError', 'errorMessage']);
        $this->dispatch('close-delete-modal');
    }

    public function render()
    {
        return view('livewire.admin.jadwal.modals.delete');
    }
}
