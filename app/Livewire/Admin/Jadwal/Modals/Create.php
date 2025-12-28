<?php

namespace App\Livewire\Admin\Jadwal\Modals;

use App\Models\JadwalKerja;
use App\Models\Karyawan;
use App\Models\Shift;
use Livewire\Component;

class Create extends Component
{
    public $karyawan_id = '';
    public $shift_id = '';
    public $tanggal = '';
    public $status = 'aktif';
    public $keterangan = '';

    public $showSuccess = false;
    public $showError = false;
    public $errorMessage = '';

    protected $rules = [
        'karyawan_id' => 'required|exists:karyawan,id',
        'shift_id' => 'required|exists:shift,id',
        'tanggal' => 'required|date',
    ];

    protected $messages = [
        'karyawan_id.required' => 'Karyawan wajib dipilih',
        'shift_id.required' => 'Shift wajib dipilih',
        'tanggal.required' => 'Tanggal wajib diisi',
    ];

    public function openModal()
    {
        $this->tanggal = now()->format('Y-m-d');
        $this->resetValidation();
        $this->showSuccess = false;
        $this->showError = false;
    }

    public function save()
    {
        $this->showSuccess = false;
        $this->showError = false;

        $this->validate();

        try {
            // Check if jadwal already exists
            $exists = JadwalKerja::where('karyawan_id', $this->karyawan_id)
                ->where('tanggal', $this->tanggal)
                ->exists();

            if ($exists) {
                $this->showError = true;
                $this->errorMessage = 'Jadwal untuk karyawan ini pada tanggal tersebut sudah ada!';
                return;
            }

            JadwalKerja::create([
                'karyawan_id' => $this->karyawan_id,
                'shift_id' => $this->shift_id,
                'tanggal' => $this->tanggal,
                'status' => $this->status,
                'keterangan' => $this->keterangan,
            ]);

            $this->showSuccess = true;
            $this->showError = false;
            
            $this->reset(['karyawan_id', 'shift_id', 'keterangan']);
            $this->status = 'aktif';
            $this->resetValidation();

            $this->dispatch('jadwal-created');
            $this->dispatch('close-create-modal');
            
        } catch (\Exception $e) {
            $this->showError = true;
            $this->showSuccess = false;
            $this->errorMessage = 'Gagal menyimpan jadwal: ' . $e->getMessage();
        }
    }

    public function closeModal()
    {
        $this->reset(['karyawan_id', 'shift_id', 'tanggal', 'keterangan', 'showError', 'errorMessage']);
        $this->status = 'aktif';
        $this->resetValidation();
        $this->dispatch('close-create-modal');
    }

    public function render()
    {
        $karyawans = Karyawan::where('status', 'active')
            ->with('departemen')
            ->orderBy('nama_lengkap')
            ->get();

        $shifts = Shift::where('status', 'active')
            ->orderBy('nama_shift')
            ->get();

        return view('livewire.admin.jadwal.modals.create', [
            'karyawans' => $karyawans,
            'shifts' => $shifts,
        ]);
    }
}
