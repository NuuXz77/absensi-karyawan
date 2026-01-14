<?php

namespace App\Livewire\Admin\Jadwal\Modals;

use App\Models\JadwalKerja;
use App\Models\Karyawan;
use App\Models\Shift;
use App\Models\Lokasi;
use Livewire\Component;

class Edit extends Component
{
    public $jadwalId;
    public $karyawan_id = '';
    public $shift_id = '';
    public $lokasi_id = '';
    public $tanggal = '';
    public $status = 'aktif';
    public $keterangan = '';

    public $showSuccess = false;
    public $showError = false;
    public $errorMessage = '';

    protected $listeners = ['edit-jadwal' => 'edit'];

    protected $rules = [
        'karyawan_id' => 'required|exists:karyawan,id',
        'shift_id' => 'required|exists:shift,id',
        'lokasi_id' => 'required|exists:lokasi,id',
        'tanggal' => 'required|date',
    ];

    protected $messages = [
        'karyawan_id.required' => 'Karyawan wajib dipilih',
        'shift_id.required' => 'Shift wajib dipilih',
        'lokasi_id.required' => 'Lokasi wajib dipilih',
        'tanggal.required' => 'Tanggal wajib diisi',
    ];

    public function edit($id)
    {
        $jadwal = JadwalKerja::findOrFail($id);
        
        $this->jadwalId = $jadwal->id;
        $this->karyawan_id = $jadwal->karyawan_id;
        $this->shift_id = $jadwal->shift_id;
        $this->lokasi_id = $jadwal->lokasi_id;
        $this->tanggal = $jadwal->tanggal;
        $this->status = $jadwal->status ?? 'aktif';
        $this->keterangan = $jadwal->keterangan;

        $this->resetValidation();
        $this->showSuccess = false;
        $this->showError = false;
        
        $this->dispatch('open-edit-modal');
    }

    public function update()
    {
        $this->showSuccess = false;
        $this->showError = false;

        $this->validate();

        try {
            $jadwal = JadwalKerja::findOrFail($this->jadwalId);

            // Check if jadwal already exists (except current)
            $exists = JadwalKerja::where('karyawan_id', $this->karyawan_id)
                ->where('tanggal', $this->tanggal)
                ->where('id', '!=', $this->jadwalId)
                ->exists();

            if ($exists) {
                $this->showError = true;
                $this->errorMessage = 'Jadwal untuk karyawan ini pada tanggal tersebut sudah ada!';
                return;
            }

            $jadwal->update([
                'karyawan_id' => $this->karyawan_id,
                'shift_id' => $this->shift_id,
                'lokasi_id' => $this->lokasi_id,
                'tanggal' => $this->tanggal,
                'status' => $this->status,
                'keterangan' => $this->keterangan,
            ]);

            $this->showSuccess = true;
            $this->showError = false;

            $this->dispatch('jadwal-updated');
            $this->dispatch('close-edit-modal');
            
        } catch (\Exception $e) {
            $this->showError = true;
            $this->showSuccess = false;
            $this->errorMessage = 'Gagal memperbarui jadwal: ' . $e->getMessage();
        }
    }

    public function closeModal()
    {
        $this->reset(['jadwalId', 'karyawan_id', 'shift_id', 'lokasi_id', 'tanggal', 'keterangan', 'showError', 'errorMessage']);
        $this->status = 'aktif';
        $this->resetValidation();
        $this->dispatch('close-edit-modal');
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

        $lokasis = Lokasi::where('status', 'active')
            ->orderBy('nama_lokasi')
            ->get();

        return view('livewire.admin.jadwal.modals.edit', [
            'karyawans' => $karyawans,
            'shifts' => $shifts,
            'lokasis' => $lokasis,
        ]);
    }
}
