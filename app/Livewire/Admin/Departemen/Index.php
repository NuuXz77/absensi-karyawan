<?php

namespace App\Livewire\Admin\Departemen;

use App\Models\Departemen;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
class Index extends Component
{
    use WithPagination;

    #[Title('Data Departemen')]

    // Search & Filter
    public $search = '';
    public $filterStatus = '';

    // Sorting
    public $sortField = 'nama_departemen';
    public $sortDirection = 'asc';

    // Per page
    public $perPage = 10;

    protected $queryString = [
        'search' => ['except' => ''],
        'filterStatus' => ['except' => ''],
        'sortField' => ['except' => 'nama_departemen'],
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
        $this->reset(['search', 'filterStatus']);
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

    protected $listeners = ['departemen-created' => '$refresh'];

    public function render()
    {
        $departemens = Departemen::query()
            ->withCount('karyawans')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('nama_departemen', 'like', '%' . $this->search . '%')
                        ->orWhere('kode_departemen', 'like', '%' . $this->search . '%')
                        ->orWhere('deskripsi', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->filterStatus, function ($query) {
                $query->where('status', $this->filterStatus);
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view('livewire.admin.departemen.index', [
            'departemens' => $departemens
        ]);
    }
}
