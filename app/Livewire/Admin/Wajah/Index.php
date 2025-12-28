<?php

namespace App\Livewire\Admin\Wajah;

use App\Models\WajahKaryawan;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
class Index extends Component
{
    use WithPagination;

    #[Title('Data Wajah Karyawan')]

    // Search & Filter
    public $search = '';
    public $filterDepartemen = '';
    public $filterJabatan = '';

    // Sorting
    public $sortField = 'created_at';
    public $sortDirection = 'desc';

    // Per page
    public $perPage = 10;

    protected $queryString = [
        'search' => ['except' => ''],
        'filterDepartemen' => ['except' => ''],
        'filterJabatan' => ['except' => ''],
        'sortField' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterDepartemen()
    {
        $this->resetPage();
    }

    public function updatingFilterJabatan()
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
        $this->reset(['search', 'filterDepartemen', 'filterJabatan']);
        $this->resetPage();
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

    protected $listeners = [
        'wajah-deleted' => '$refresh',
    ];

    public function render()
    {
        $wajahKaryawans = WajahKaryawan::query()
            ->with(['karyawan.jabatan.departemen', 'karyawan.departemen'])
            ->when($this->search, function ($query) {
                $query->whereHas('karyawan', function($q) {
                    $q->where('nama_lengkap', 'like', '%' . $this->search . '%')
                        ->orWhere('nip', 'like', '%' . $this->search . '%')
                        ->orWhere('id_card', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->filterDepartemen, function ($query) {
                $query->whereHas('karyawan', function($q) {
                    $q->where('departemen_id', $this->filterDepartemen);
                });
            })
            ->when($this->filterJabatan, function ($query) {
                $query->whereHas('karyawan', function($q) {
                    $q->where('jabatan_id', $this->filterJabatan);
                });
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        // Get departemen dan jabatan untuk filter dropdown
        $departemens = \App\Models\Departemen::where('status', 'active')
            ->orderBy('nama_departemen')
            ->get();
            
        $jabatans = \App\Models\Jabatan::with('departemen')
            ->where('status', 'active')
            ->orderBy('nama_jabatan')
            ->get();

        return view('livewire.admin.wajah.index', [
            'wajahKaryawans' => $wajahKaryawans,
            'departemens' => $departemens,
            'jabatans' => $jabatans
        ]);
    }
}
