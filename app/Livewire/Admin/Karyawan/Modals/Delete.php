<?php

namespace App\Livewire\Admin\Karyawan\Modals;

use Livewire\Component;
use App\Models\Karyawan;
use Illuminate\Support\Facades\Storage;

class Delete extends Component
{
    public $karyawan;
    public $showSuccess = false;
    public $showError = false;
    public $errorMessage = '';

    protected $listeners = ['open-delete-modal' => 'confirmDelete'];

    public function confirmDelete($karyawanId)
    {
        $this->karyawan = Karyawan::with(['jabatan', 'departemen', 'user'])
            ->withCount(['absensi', 'izin', 'cuti'])
            ->find($karyawanId);
    }

    public function delete()
    {
        try {
            if (!$this->karyawan) {
                throw new \Exception('Data karyawan tidak ditemukan');
            }

            // Cek apakah ada data terkait
            $hasRelatedData = $this->karyawan->absensi_count > 0 || 
                             $this->karyawan->izin_count > 0 || 
                             $this->karyawan->cuti_count > 0;

            if ($hasRelatedData) {
                $this->showError = true;
                $this->errorMessage = 'Tidak dapat menghapus karyawan karena masih memiliki data absensi/izin/cuti!';
                return;
            }

            // Delete foto if exists
            if ($this->karyawan->foto_karyawan) {
                Storage::disk('public')->delete($this->karyawan->foto_karyawan);
            }

            // Delete wajah data
            if ($this->karyawan->wajah) {
                $this->karyawan->wajah->delete();
            }

            // Delete user account
            if ($this->karyawan->user) {
                $this->karyawan->user->delete();
            }

            // Delete karyawan
            $this->karyawan->delete();

            $this->showSuccess = true;
            $this->dispatch('karyawan-deleted');
            $this->dispatch('close-delete-modal');
            
            $this->reset(['karyawan', 'showError', 'errorMessage']);

        } catch (\Exception $e) {
            $this->showError = true;
            $this->errorMessage = $e->getMessage();
        }
    }

    public function closeModal()
    {
        $this->reset(['karyawan', 'showSuccess', 'showError', 'errorMessage']);
        $this->dispatch('close-delete-modal');
    }

    public function render()
    {
        return view('livewire.admin.karyawan.modals.delete');
    }
}
