<?php

namespace App\Livewire\Admin\Jadwal;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use App\Models\JadwalKerja;
use App\Models\Karyawan;
use App\Models\Shift;
use App\Models\Departemen;
use Carbon\Carbon;

#[Layout('components.layouts.app')]
class Detail extends Component
{
    #[Url]
    public $tanggal;
    
    #[Url]
    public $shift_id;
    
    #[Url]
    public $departemen_id;
    
    public $shift;
    public $departemen;
    public $jadwals;

    // Edit mode
    public $editingJadwalId = null;
    public $editShiftId = null;

    public function mount()
    {
        if (!$this->tanggal || !$this->shift_id || !$this->departemen_id) {
            return redirect()->route('admin.jadwal.index');
        }

        $this->loadData();
    }

    public function loadData()
    {
        $this->shift = Shift::find($this->shift_id);
        $this->departemen = Departemen::find($this->departemen_id);

        $this->jadwals = JadwalKerja::with(['karyawan.departemen', 'karyawan.jabatan', 'shift'])
            ->whereDate('tanggal', $this->tanggal)
            ->where('shift_id', $this->shift_id)
            ->whereHas('karyawan', function($q) {
                $q->where('departemen_id', $this->departemen_id);
            })
            ->get();
    }

    public function editJadwal($jadwalId)
    {
        $jadwal = JadwalKerja::find($jadwalId);
        if ($jadwal) {
            $this->editingJadwalId = $jadwalId;
            $this->editShiftId = $jadwal->shift_id;
        }
    }

    public function cancelEdit()
    {
        $this->editingJadwalId = null;
        $this->editShiftId = null;
    }

    public function updateJadwal($jadwalId)
    {
        $this->validate([
            'editShiftId' => 'required|exists:shifts,id',
        ]);

        try {
            $jadwal = JadwalKerja::find($jadwalId);
            if ($jadwal) {
                $jadwal->update([
                    'shift_id' => $this->editShiftId,
                ]);

                session()->flash('success', 'Jadwal berhasil diupdate!');
                $this->loadData();
                $this->cancelEdit();
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal mengupdate jadwal: ' . $e->getMessage());
        }
    }

    public function deleteJadwal($jadwalId)
    {
        try {
            $jadwal = JadwalKerja::find($jadwalId);
            if ($jadwal) {
                $jadwal->delete();
                session()->flash('success', 'Jadwal berhasil dihapus!');
                $this->loadData();
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal menghapus jadwal: ' . $e->getMessage());
        }
    }

    #[Title('Detail Jadwal')]
    public function render()
    {
        $allShifts = Shift::where('status', 'active')->orderBy('nama_shift')->get();

        return view('livewire.admin.jadwal.detail', [
            'allShifts' => $allShifts,
            'formattedDate' => Carbon::parse($this->tanggal)->locale('id')->isoFormat('dddd, D MMMM YYYY'),
        ]);
    }
}
