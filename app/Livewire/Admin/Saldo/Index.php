<?php

namespace App\Livewire\Admin\Saldo;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\On;
use App\Models\SaldoCutiDanIzin;

#[Layout('components.layouts.app')]
class Index extends Component
{
    use WithPagination;

    #[Title('Saldo Cuti & Izin - Admin')]

    // Table properties
    public $search = '';
    public $perPage = 10;
    public $sortField = 'tahun';
    public $sortDirection = 'desc';
    public $filterTahun = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'sortField' => ['except' => 'tahun'],
        'sortDirection' => ['except' => 'desc'],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingPerPage()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }
        $this->sortField = $field;
    }

    public function resetFilters()
    {
        $this->reset(['search', 'filterTahun', 'sortField', 'sortDirection']);
        $this->sortField = 'tahun';
        $this->sortDirection = 'desc';
    }

    #[On('saldo-created')]
    #[On('saldo-updated')]
    #[On('saldo-deleted')]
    public function refreshSaldos()
    {
        // Refresh component
    }

    public function edit($id)
    {
        $this->dispatch('open-edit-modal', id: $id);
    }

    public function confirmDelete($id)
    {
        $this->dispatch('open-delete-modal', id: $id);
    }

    public function render()
    {
        $saldos = SaldoCutiDanIzin::with('karyawan')
            ->when($this->search, function ($query) {
                $query->whereHas('karyawan', function ($q) {
                    $q->where('nama_lengkap', 'like', '%' . $this->search . '%')
                      ->orWhere('nip', 'like', '%' . $this->search . '%');
                })->orWhere('tahun', 'like', '%' . $this->search . '%');
            })
            ->when($this->filterTahun, function ($query) {
                $query->where('tahun', $this->filterTahun);
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        // Get available years for filter
        $years = SaldoCutiDanIzin::select('tahun')
            ->distinct()
            ->orderBy('tahun', 'desc')
            ->pluck('tahun');

        return view('livewire.admin.saldo.index', [
            'saldos' => $saldos,
            'years' => $years,
        ]);
    }
}
