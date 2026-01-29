<?php

namespace App\Livewire\Admin\Izin\Modals;

use App\Models\Izin;
use App\Models\SaldoCutiDanIzin;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Delete extends Component
{
    public $izinId;
    public $izin;

    public $showSuccess = false;
    public $showError = false;
    public $errorMessage = '';

    protected $listeners = ['confirm-delete-izin' => 'loadIzin'];

    public function loadIzin($id)
    {
        $this->izinId = $id;
        $this->izin = Izin::with('karyawan')->findOrFail($id);
        
        $this->showSuccess = false;
        $this->showError = false;
    }

    public function delete()
    {
        $this->showSuccess = false;
        $this->showError = false;

        if (!$this->izin) {
            return;
        }

        DB::beginTransaction();
        try {
            // Jika izin yang dihapus statusnya sudah disetujui, kembalikan saldo
            if ($this->izin->status === 'disetujui') {
                $tanggalMulai = \Carbon\Carbon::parse($this->izin->tanggal_mulai);
                $tanggalSelesai = \Carbon\Carbon::parse($this->izin->tanggal_selesai);
                $jumlahHari = $tanggalMulai->diffInDays($tanggalSelesai) + 1;
                
                $tahunIzin = date('Y', strtotime($this->izin->tanggal_mulai));
                
                $saldoIzin = SaldoCutiDanIzin::where('karyawan_id', $this->izin->karyawan_id)
                    ->where('tahun', $tahunIzin)
                    ->first();

                if ($saldoIzin) {
                    // Kembalikan saldo izin
                    $saldoIzin->sisa_izin += $jumlahHari;
                    $saldoIzin->save();
                }
            }

            // Delete photo if exists
            if ($this->izin->bukti_foto && Storage::disk('public')->exists($this->izin->bukti_foto)) {
                Storage::disk('public')->delete($this->izin->bukti_foto);
            }

            $this->izin->delete();
            
            DB::commit();
            $this->showSuccess = true;
            $this->dispatch('izin-deleted');
            $this->dispatch('close-delete-modal');
            
        } catch (\Exception $e) {
            DB::rollBack();
            $this->showError = true;
            $this->errorMessage = 'Terjadi kesalahan: ' . $e->getMessage();
        }
    }

    public function closeModal()
    {
        $this->reset(['izinId', 'showError', 'errorMessage']);
        $this->dispatch('close-delete-modal');
    }

    public function render()
    {
        return view('livewire.admin.izin.modals.delete');
    }
}
