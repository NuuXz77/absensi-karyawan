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

    public $search = '';
    public $filterStatus = '';
    public $sortField = 'nama_shift';
    public $sortDirection = 'asc';
    public $perPage = 10;

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

    public function resetFilters()
    {
        $this->search = '';
        $this->filterStatus = '';
    }

    #[Title('Data Shift')]
    public function render()
    {
        $shifts = Shift::query()
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
