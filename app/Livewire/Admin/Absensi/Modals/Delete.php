<?php

namespace App\Livewire\Admin\Absensi\Modals;

use Livewire\Component;
use App\Models\Absensi;
use Livewire\Attributes\On;

class Delete extends Component
{
    public $absensiId;
    public $showModal = false;
    public $absensi = null;

    #[On('open-delete-modal')]
    public function openModal($absensiId)
    {
        $this->absensiId = $absensiId;
        $this->absensi = Absensi::with(['karyawan', 'lokasi'])->find($absensiId);
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->absensiId = null;
        $this->absensi = null;
    }

    public function delete()
    {
        try {
            $absensi = Absensi::findOrFail($this->absensiId);
            
            // Hapus foto jika ada
            if ($absensi->foto_masuk && \Storage::disk('public')->exists($absensi->foto_masuk)) {
                \Storage::disk('public')->delete($absensi->foto_masuk);
            }
            if ($absensi->foto_keluar && \Storage::disk('public')->exists($absensi->foto_keluar)) {
                \Storage::disk('public')->delete($absensi->foto_keluar);
            }
            
            $absensi->delete();

            session()->flash('success', 'Data absensi berhasil dihapus!');
            
            $this->closeModal();
            $this->dispatch('absensi-deleted');
            
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal menghapus data absensi: ' . $e->getMessage());
            $this->closeModal();
        }
    }

    public function render()
    {
        return view('livewire.admin.absensi.modals.delete');
    }
}
