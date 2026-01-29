<?php

namespace App\Livewire\Admin\Absensi;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\On;
use App\Models\Absensi;
use App\Models\Karyawan;
use App\Models\Lokasi;
use Carbon\Carbon;
use App\Exports\AbsensiExport;
use Maatwebsite\Excel\Facades\Excel;

#[Layout('components.layouts.app')]
class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $filterStatus = '';
    public $filterKaryawan = '';
    public $filterLokasi = '';
    public $filterTanggal = '';
    public $sortField = 'tanggal';
    public $sortDirection = 'desc';
    public $perPage = 10;

    public function mount()
    {
        $this->filterTanggal = Carbon::today()->format('Y-m-d');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function exportEx()
    {
        return Excel::download(new AbsensiExport(), 'absensi.xlsx');
    }

    public function exportPdf()
    {
        return Excel::download(new AbsensiExport(), 'absensi.pdf', \Maatwebsite\Excel\Excel::DOMPDF);
    }

    public function resetFilters()
    {
        $this->search = '';
        $this->filterStatus = '';
        $this->filterKaryawan = '';
        $this->filterLokasi = '';
        $this->filterTanggal = Carbon::today()->format('Y-m-d');
    }

    #[On('absensi-deleted')]
    public function refreshData()
    {
        // Refresh data setelah hapus
        $this->resetPage();
    }

    #[Title('Rekap Absensi')]
    public function render()
    {
        $absensis = Absensi::with(['karyawan', 'lokasi'])
            ->when($this->search, function ($query) {
                $query->whereHas('karyawan', function ($q) {
                    $q->where('nama_lengkap', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->filterStatus, function ($query) {
                $query->where('status', $this->filterStatus);
            })
            ->when($this->filterKaryawan, function ($query) {
                $query->where('karyawan_id', $this->filterKaryawan);
            })
            ->when($this->filterLokasi, function ($query) {
                $query->where('lokasi_id', $this->filterLokasi);
            })
            ->when($this->filterTanggal, function ($query) {
                $query->whereDate('tanggal', $this->filterTanggal);
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        $karyawans = Karyawan::where('status', 'active')->get();
        $lokasis = Lokasi::where('status', 'active')->get();

        return view('livewire.admin.absensi.index', [
            'absensis' => $absensis,
            'karyawans' => $karyawans,
            'lokasis' => $lokasis,
        ]);
    }
}
