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
use App\Models\Lokasi;
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
    
    public $filterShift = ''; // Filter untuk shift
    public $filterLokasi = ''; // Filter untuk lokasi
    
    public $shift;
    public $departemen;
    public $jadwals;
    public $allShifts;
    public $allLokasis;

    // Edit mode
    public $editingJadwalId = null;
    public $editShiftId = null;

    public function mount()
    {
        if (!$this->tanggal || !$this->departemen_id) {
            return redirect()->route('admin.jadwal.index');
        }

        $this->loadData();
    }

    public function loadData()
    {
        $this->departemen = Departemen::find($this->departemen_id);
        $this->allShifts = Shift::where('status', 'active')->orderBy('nama_shift')->get();
        $this->allLokasis = Lokasi::where('status', 'active')->orderBy('nama_lokasi')->get();

        $query = JadwalKerja::with(['karyawan.departemen', 'karyawan.jabatan', 'shift', 'lokasi'])
            ->whereDate('tanggal', $this->tanggal)
            ->whereHas('karyawan', function($q) {
                $q->where('departemen_id', $this->departemen_id);
            });

        // Apply shift filter if set
        if ($this->filterShift) {
            $query->where('shift_id', $this->filterShift);
        }

        // Apply lokasi filter if set
        if ($this->filterLokasi) {
            $query->where('lokasi_id', $this->filterLokasi);
        }

        $this->jadwals = $query->get();

        // Get unique shifts from jadwals for display
        $this->shift = $this->jadwals->pluck('shift')->unique('id')->first();
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
        return view('livewire.admin.jadwal.detail', [
            'formattedDate' => Carbon::parse($this->tanggal)->locale('id')->isoFormat('dddd, D MMMM YYYY'),
        ]);
    }

    public function updatedFilterShift()
    {
        $this->loadData();
    }

    public function updatedFilterLokasi()
    {
        $this->loadData();
    }
}
