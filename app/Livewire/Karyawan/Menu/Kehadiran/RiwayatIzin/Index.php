<?php

namespace App\Livewire\Karyawan\Menu\Kehadiran\RiwayatIzin;

use App\Models\Izin;
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
    public $filterJenisIzin = '';
    public $filterBulan = '';
    public $filterTahun = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'filterStatus' => ['except' => ''],
        'filterJenisIzin' => ['except' => ''],
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

    public function updatingFilterJenisIzin()
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
        $this->reset(['search', 'filterStatus', 'filterJenisIzin', 'filterBulan', 'filterTahun']);
        $this->filterBulan = date('m');
        $this->filterTahun = date('Y');
        $this->resetPage();
    }

    #[Title('Riwayat Izin')]
    public function render()
    {
        $karyawanId = Auth::user()->karyawan->id;

        $izin = Izin::with(['disetujuiOleh'])
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
            ->when($this->filterJenisIzin, function ($query) {
                $query->where('jenis_izin', $this->filterJenisIzin);
            })
            ->when($this->filterBulan, function ($query) {
                $query->whereMonth('tanggal_mulai', $this->filterBulan);
            })
            ->when($this->filterTahun, function ($query) {
                $query->whereYear('tanggal_mulai', $this->filterTahun);
            })
            ->orderBy('tanggal_mulai', 'desc')
            ->paginate(10);

        // Ambil saldo izin
        $saldoIzin = SaldoCutiDanIzin::where('karyawan_id', $karyawanId)
            ->where('tahun', date('Y'))
            ->first();

        // Statistik
        $totalIzin = Izin::where('karyawan_id', $karyawanId)
            ->whereMonth('tanggal_mulai', $this->filterBulan)
            ->whereYear('tanggal_mulai', $this->filterTahun)
            ->count();

        $totalDiterima = Izin::where('karyawan_id', $karyawanId)
            ->whereMonth('tanggal_mulai', $this->filterBulan)
            ->whereYear('tanggal_mulai', $this->filterTahun)
            ->where('status', 'disetujui')
            ->count();

        $totalDitolak = Izin::where('karyawan_id', $karyawanId)
            ->whereMonth('tanggal_mulai', $this->filterBulan)
            ->whereYear('tanggal_mulai', $this->filterTahun)
            ->where('status', 'ditolak')
            ->count();

        return view('livewire.karyawan.menu.kehadiran.riwayat-izin.index', [
            'izin' => $izin,
            'saldoIzin' => $saldoIzin,
            'totalIzin' => $totalIzin,
            'totalDiterima' => $totalDiterima,
            'totalDitolak' => $totalDitolak,
        ]);
    }
}
