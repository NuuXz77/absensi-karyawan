<?php

namespace App\Livewire\Admin\Cuti\Modals;

use App\Models\Cuti;
use App\Models\SaldoCutiDanIzin;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Delete extends Component
{
    public $cutiId;
    public $cuti;

    public $showSuccess = false;
    public $showError = false;
    public $errorMessage = '';

    protected $listeners = ['confirm-delete-cuti' => 'loadCuti'];

    public function loadCuti($id)
    {
        $this->cutiId = $id;
        $this->cuti = Cuti::with('karyawan')->findOrFail($id);
        
        $this->showSuccess = false;
        $this->showError = false;
    }

    public function delete()
    {
        $this->showSuccess = false;
        $this->showError = false;

        if (!$this->cuti) {
            return;
        }

        DB::beginTransaction();
        try {
            // Jika cuti yang dihapus statusnya sudah disetujui dan jenis tahunan, kembalikan saldo
            if ($this->cuti->status === 'disetujui' && $this->cuti->jenis_cuti === 'tahunan') {
                $tahunCuti = date('Y', strtotime($this->cuti->tanggal_mulai));
                
                $saldoCuti = SaldoCutiDanIzin::where('karyawan_id', $this->cuti->karyawan_id)
                    ->where('tahun', $tahunCuti)
                    ->first();

                if ($saldoCuti) {
                    // Kembalikan saldo cuti
                    $saldoCuti->sisa_cuti += $this->cuti->jumlah_hari;
                    $saldoCuti->save();
                }
            }

            // Delete photo if exists
            if ($this->cuti->bukti_foto && Storage::disk('public')->exists($this->cuti->bukti_foto)) {
                Storage::disk('public')->delete($this->cuti->bukti_foto);
            }

            $this->cuti->delete();
            
            DB::commit();
            $this->showSuccess = true;
            $this->dispatch('cuti-deleted');
            $this->dispatch('close-delete-modal');
            
        } catch (\Exception $e) {
            DB::rollBack();
            $this->showError = true;
            $this->errorMessage = 'Terjadi kesalahan: ' . $e->getMessage();
        }
    }

    public function closeModal()
    {
        $this->reset(['cutiId', 'showError', 'errorMessage']);
        $this->dispatch('close-delete-modal');
    }

    public function render()
    {
        return view('livewire.admin.cuti.modals.delete');
    }
}
