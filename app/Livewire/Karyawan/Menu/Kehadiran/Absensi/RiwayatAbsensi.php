<?php

namespace App\Livewire\Karyawan\Menu\Kehadiran\Absensi;

use App\Models\Absensi;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class RiwayatAbsensi extends Component
{
    use WithPagination;

    public $search = '';
    public $filterStatus = '';
    public $filterBulan = '';
    public $filterTahun = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'filterStatus' => ['except' => ''],
        'filterBulan' => ['except' => ''],
        'filterTahun' => ['except' => ''],
    ];

    public function mount()
    {
        $this->filterBulan = date('m');
        $this->filterTahun = date('Y');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterStatus()
    {
        $this->resetPage();
    }

    public function updatingFilterBulan()
    {
        $this->resetPage();
    }

    public function updatingFilterTahun()
    {
        $this->resetPage();
    }

    #[Title('Riwayat Absensi')]
    public function render()
    {
        $karyawanId = Auth::user()->karyawan->id;

        $absensi = Absensi::with(['lokasi'])
            ->where('karyawan_id', $karyawanId)
            ->when($this->search, function ($query) {
                $query->where('tanggal', 'like', '%' . $this->search . '%');
            })
            ->when($this->filterStatus, function ($query) {
                $query->where('status', $this->filterStatus);
            })
            ->when($this->filterBulan, function ($query) {
                $query->whereMonth('tanggal', $this->filterBulan);
            })
            ->when($this->filterTahun, function ($query) {
                $query->whereYear('tanggal', $this->filterTahun);
            })
            ->orderBy('tanggal', 'desc')
            ->paginate(10);

        // Statistik
        $totalHadir = Absensi::where('karyawan_id', $karyawanId)
            ->whereMonth('tanggal', $this->filterBulan)
            ->whereYear('tanggal', $this->filterTahun)
            ->whereIn('status', ['hadir', 'tepat_waktu', 'terlambat'])
            ->count();

        $totalTerlambat = Absensi::where('karyawan_id', $karyawanId)
            ->whereMonth('tanggal', $this->filterBulan)
            ->whereYear('tanggal', $this->filterTahun)
            ->where('status', 'terlambat')
            ->count();

        $totalIzin = Absensi::where('karyawan_id', $karyawanId)
            ->whereMonth('tanggal', $this->filterBulan)
            ->whereYear('tanggal', $this->filterTahun)
            ->where('status', 'izin')
            ->count();

        $totalCuti = Absensi::where('karyawan_id', $karyawanId)
            ->whereMonth('tanggal', $this->filterBulan)
            ->whereYear('tanggal', $this->filterTahun)
            ->where('status', 'cuti')
            ->count();

        $totalAlpha = Absensi::where('karyawan_id', $karyawanId)
            ->whereMonth('tanggal', $this->filterBulan)
            ->whereYear('tanggal', $this->filterTahun)
            ->where('status', 'alpha')
            ->count();

        return view('livewire.karyawan.menu.kehadiran.absensi.riwayat-absensi', [
            'absensi' => $absensi,
            'totalHadir' => $totalHadir,
            'totalTerlambat' => $totalTerlambat,
            'totalIzin' => $totalIzin,
            'totalCuti' => $totalCuti,
            'totalAlpha' => $totalAlpha,
        ]);
    }
}
