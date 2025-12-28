<?php

namespace App\Livewire\Admin\Shift;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\Shift;

#[Layout('components.layouts.app')]
class Index extends Component
{
    use WithPagination;

    #[Title('Data Shift')]

    // Search & Filter
    public $search = '';
    public $filterStatus = '';

    // Sorting
    public $sortField = 'nama_shift';
    public $sortDirection = 'asc';

    // Per page
    public $perPage = 10;

    protected $queryString = [
        'search' => ['except' => ''],
        'filterStatus' => ['except' => ''],
        'sortField' => ['except' => 'nama_shift'],
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

    public function edit($id)
    {
        $this->dispatch('open-edit-modal', id: $id);
    }

    public function confirmDelete($id)
    {
        $this->dispatch('confirm-delete', id: $id);
    }

    protected $listeners = [
        'shift-created' => '$refresh',
        'shift-updated' => '$refresh',
        'shift-deleted' => '$refresh',
    ];

    public function render()
    {
        $shifts = Shift::query()
            ->withCount('jadwalKerja')
            ->when($this->search, function($query) {
                $query->where('nama_shift', 'like', '%' . $this->search . '%');
            })
            ->when($this->filterStatus, function($query) {
                $query->where('status', $this->filterStatus);
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view('livewire.admin.shift.index', [
            'shifts' => $shifts
        ]);
    }
}
