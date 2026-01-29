<?php

namespace App\Livewire\Admin\Saldo\Modals;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\SaldoCutiDanIzin;

class Delete extends Component
{
    public $saldoId;
    public $saldoInfo;

    public $showSuccess = false;
    public $showError = false;
    public $errorMessage = '';

    #[On('open-delete-modal')]
    public function openModal($id)
    {
        $this->saldoId = $id;
        $saldo = SaldoCutiDanIzin::with('karyawan')->findOrFail($id);
        
        $this->saldoInfo = [
            'karyawan' => $saldo->karyawan->nama_lengkap ?? '-',
            'nip' => $saldo->karyawan->nip ?? '-',
            'tahun' => $saldo->tahun,
        ];

        $this->dispatch('show-delete-modal');
    }

    public function closeModal()
    {
        $this->reset(['saldoId', 'saldoInfo', 'showSuccess', 'showError', 'errorMessage']);
        $this->dispatch('hide-delete-modal');
    }

    public function delete()
    {
        try {
            $saldo = SaldoCutiDanIzin::findOrFail($this->saldoId);
            $saldo->delete();

            $this->showSuccess = true;
            $this->dispatch('saldo-deleted');
            $this->closeModal();
        } catch (\Exception $e) {
            $this->showError = true;
            $this->errorMessage = 'Gagal menghapus data: ' . $e->getMessage();
        }
    }

    public function render()
    {
        return view('livewire.admin.saldo.modals.delete');
    }
}
