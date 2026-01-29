<?php

namespace App\Livewire\Admin\Cuti\Modals;

use App\Models\Cuti;
use App\Models\SaldoCutiDanIzin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\Attributes\Computed;

class Confirm extends Component
{
    public $cutiId;
    public $confirmAction = ''; // 'approve' or 'reject'

    public $showSuccess = false;
    public $showError = false;
    public $errorMessage = '';

    protected $listeners = ['open-confirm-modal' => 'openModal'];

    #[Computed]
    public function cuti()
    {
        return $this->cutiId ? Cuti::with('karyawan.departemen')->find($this->cutiId) : null;
    }

    public function openModal($id, $action)
    {
        $this->cutiId = $id;
        $this->confirmAction = $action;
        $this->showSuccess = false;
        $this->showError = false;
    }

    public function confirmActionSubmit()
    {
        $this->showSuccess = false;
        $this->showError = false;

        $cuti = $this->cuti;
        if (!$cuti) {
            return;
        }

        DB::beginTransaction();
        try {
            if ($this->confirmAction === 'approve') {
                // Update status cuti
                $cuti->update([
                    'status' => 'disetujui',
                    'disetujui_oleh' => Auth::id(),
                ]);

                // Update saldo cuti untuk cuti tahunan
                if ($cuti->jenis_cuti === 'tahunan') {
                    $tahunCuti = date('Y', strtotime($cuti->tanggal_mulai));
                    
                    $saldoCuti = SaldoCutiDanIzin::where('karyawan_id', $cuti->karyawan_id)
                        ->where('tahun', $tahunCuti)
                        ->first();

                    if ($saldoCuti) {
                        // Cek apakah sisa cuti mencukupi
                        if ($saldoCuti->sisa_cuti >= $cuti->jumlah_hari) {
                            // Kurangi sisa_cuti
                            $saldoCuti->sisa_cuti -= $cuti->jumlah_hari;
                            $saldoCuti->save();
                        } else {
                            DB::rollBack();
                            $this->showError = true;
                            $this->errorMessage = 'Saldo cuti tidak mencukupi! Sisa: ' . $saldoCuti->sisa_cuti . ' hari';
                            return;
                        }
                    } else {
                        DB::rollBack();
                        $this->showError = true;
                        $this->errorMessage = 'Saldo cuti untuk tahun ' . $tahunCuti . ' tidak ditemukan!';
                        return;
                    }
                }

                DB::commit();
                $this->showSuccess = true;
                $this->dispatch('cuti-approved');
                $this->dispatch('close-confirm-modal');
                
            } elseif ($this->confirmAction === 'reject') {
                $cuti->update([
                    'status' => 'ditolak',
                    'disetujui_oleh' => Auth::id(),
                ]);
                
                DB::commit();
                $this->showSuccess = true;
                $this->dispatch('cuti-rejected');
                $this->dispatch('close-confirm-modal');
            }
            
        } catch (\Exception $e) {
            DB::rollBack();
            $this->showError = true;
            $this->errorMessage = 'Terjadi kesalahan: ' . $e->getMessage();
        }
    }

    public function closeModal()
    {
        $this->reset(['cutiId', 'confirmAction', 'showError', 'errorMessage']);
        $this->dispatch('close-confirm-modal');
    }

    public function render()
    {
        return view('livewire.admin.cuti.modals.confirm');
    }
}
