<?php

namespace App\Livewire\Admin\Jabatan;

use App\Models\Jabatan;
use App\Models\Departemen;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
class Index extends Component
{
    use WithPagination;

    #[Title('Data Jabatan')]

    // Search & Filter
    public $search = '';
    public $filterStatus = '';
    public $filterDepartemen = '';

    // Sorting
    public $sortField = 'nama_jabatan';
    public $sortDirection = 'asc';

    // Per page
    public $perPage = 10;

    protected $queryString = [
        'search' => ['except' => ''],
        'filterStatus' => ['except' => ''],
        'filterDepartemen' => ['except' => ''],
        'sortField' => ['except' => 'nama_jabatan'],
        'sortDirection' => ['except' => 'asc'],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterStatus()
    {
        $this->resetPage();
    }

    public function updatingFilterDepartemen()
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
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function resetFilters()
    {
        $this->reset(['search', 'filterStatus', 'filterDepartemen']);
        $this->resetPage();
    }

    public function create()
    {
        $this->dispatch('open-create-modal');
    }

    public function view($id)
    {
        $this->dispatch('open-view-modal', id: $id);
    }

    public function edit($id)
    {
        $this->dispatch('open-edit-modal', id: $id);
    }

    public function confirmDelete($id)
    {
        $this->dispatch('confirm-delete', id: $id);
    }

    public function render()
    {
        $jabatans = Jabatan::query()
            ->with('departemen')
            ->withCount('karyawans')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('nama_jabatan', 'like', '%' . $this->search . '%')
                        ->orWhere('deskripsi', 'like', '%' . $this->search . '%')
                        ->orWhere('level', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->filterStatus, function ($query) {
                $query->where('status', $this->filterStatus);
            })
            ->when($this->filterDepartemen, function ($query) {
                $query->where('departemen_id', $this->filterDepartemen);
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        $departemens = Departemen::where('status', 'aktif')
            ->orderBy('nama_departemen')
            ->get();

        return view('livewire.admin.jabatan.index', [
            'jabatans' => $jabatans,
            'departemens' => $departemens
        ]);
    }
}
