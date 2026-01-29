<?php

namespace App\Livewire\Karyawan\Menu\Kehadiran\RiwayatCuti;

use App\Models\Cuti;
use App\Models\SaldoCutiDanIzin;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $filterStatus = '';
    public $filterJenisCuti = '';
    public $filterBulan = '';
    public $filterTahun = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'filterStatus' => ['except' => ''],
        'filterJenisCuti' => ['except' => ''],
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

    public function updatingFilterJenisCuti()
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

    public function resetFilters()
    {
        $this->reset(['search', 'filterStatus', 'filterJenisCuti', 'filterBulan', 'filterTahun']);
        $this->filterBulan = date('m');
        $this->filterTahun = date('Y');
        $this->resetPage();
    }

    #[Title('Riwayat Cuti')]
    public function render()
    {
        $karyawanId = Auth::user()->karyawan->id;

        $cuti = Cuti::with(['disetujuiOleh'])
            ->where('karyawan_id', $karyawanId)
            ->when($this->search, function ($query) {
                $query->where(function($q) {
                    $q->where('tanggal_mulai', 'like', '%' . $this->search . '%')
                      ->orWhere('tanggal_selesai', 'like', '%' . $this->search . '%')
                      ->orWhere('keterangan', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->filterStatus, function ($query) {
                $query->where('status', $this->filterStatus);
            })
            ->when($this->filterJenisCuti, function ($query) {
                $query->where('jenis_cuti', $this->filterJenisCuti);
            })
            ->when($this->filterBulan, function ($query) {
                $query->whereMonth('tanggal_mulai', $this->filterBulan);
            })
            ->when($this->filterTahun, function ($query) {
                $query->whereYear('tanggal_mulai', $this->filterTahun);
            })
            ->orderBy('tanggal_mulai', 'desc')
            ->paginate(10);

        // Ambil saldo cuti
        $saldoCuti = SaldoCutiDanIzin::where('karyawan_id', $karyawanId)
            ->where('tahun', date('Y'))
            ->first();

        // Statistik
        $totalCuti = Cuti::where('karyawan_id', $karyawanId)
            ->whereMonth('tanggal_mulai', $this->filterBulan)
            ->whereYear('tanggal_mulai', $this->filterTahun)
            ->count();

        $totalDiterima = Cuti::where('karyawan_id', $karyawanId)
            ->whereMonth('tanggal_mulai', $this->filterBulan)
            ->whereYear('tanggal_mulai', $this->filterTahun)
            ->where('status', 'disetujui')
            ->count();

        $totalDitolak = Cuti::where('karyawan_id', $karyawanId)
            ->whereMonth('tanggal_mulai', $this->filterBulan)
            ->whereYear('tanggal_mulai', $this->filterTahun)
            ->where('status', 'ditolak')
            ->count();

        return view('livewire.karyawan.menu.kehadiran.riwayat-cuti.index', [
            'cuti' => $cuti,
            'saldoCuti' => $saldoCuti,
            'totalCuti' => $totalCuti,
            'totalDiterima' => $totalDiterima,
            'totalDitolak' => $totalDitolak,
        ]);
    }
}
