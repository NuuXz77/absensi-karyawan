<?php

namespace App\Livewire\Admin\Karyawan;

use App\Models\Karyawan;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
class Index extends Component
{
    use WithPagination;

    #[Title('Data Karyawan')]

    // Search & Filter
    public $search = '';
    public $filterStatus = '';
    public $filterDepartemen = '';
    public $filterJabatan = '';

    // Sorting
    public $sortField = 'nama_lengkap';
    public $sortDirection = 'asc';

    // Per page
    public $perPage = 10;

    // Modal state
    public $showCreateModal = false;

    protected $queryString = [
        'search' => ['except' => ''],
        'filterStatus' => ['except' => ''],
        'filterDepartemen' => ['except' => ''],
        'filterJabatan' => ['except' => ''],
        'sortField' => ['except' => 'nama_lengkap'],
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
        $this->reset(['search', 'filterStatus', 'filterDepartemen', 'filterJabatan']);
        $this->resetPage();
    }

    public function create()
    {
        $this->showCreateModal = true;
    }

    protected $listeners = [
        'karyawan-created' => 'handleKaryawanCreated',
        'karyawan-deleted' => '$refresh',
        'modal-closed' => 'handleModalClosed',
    ];

    public function handleKaryawanCreated($data)
    {
        $this->showCreateModal = false;
        
        session()->flash('success', $data['message']);
        
        if ($data['hasPhoto']) {
            session()->flash('info', 'Foto sedang diproses untuk face recognition. Proses ini mungkin membutuhkan beberapa saat.');
        }
    }

    public function handleModalClosed()
    {
        $this->showCreateModal = false;
    }

    public function view($id)
    {
        // Redirect ke halaman detail atau buka modal
        $this->dispatch('open-view-modal', id: $id);
    }

    public function edit($id)
    {
        // Redirect ke halaman edit atau buka modal
        $this->dispatch('open-edit-modal', id: $id);
    }

    public function confirmDelete($id)
    {
        // Tampilkan konfirmasi delete
        $this->dispatch('confirm-delete', id: $id);
    }

    public function render()
    {
        $karyawans = Karyawan::query()
            ->with(['user', 'jabatan.departemen', 'departemen'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('nama_lengkap', 'like', '%' . $this->search . '%')
                        ->orWhere('nip', 'like', '%' . $this->search . '%')
                        ->orWhere('id_card', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%')
                        ->orWhereHas('jabatan', function($q) {
                            $q->where('nama_jabatan', 'like', '%' . $this->search . '%');
                        })
                        ->orWhereHas('departemen', function($q) {
                            $q->where('nama_departemen', 'like', '%' . $this->search . '%');
                        });
                });
            })
            ->when($this->filterStatus, function ($query) {
                $query->where('status', $this->filterStatus);
            })
            ->when($this->filterDepartemen, function ($query) {
                $query->where('departemen_id', $this->filterDepartemen);
            })
            ->when($this->filterJabatan, function ($query) {
                $query->where('jabatan_id', $this->filterJabatan);
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

        return view('livewire.admin.karyawan.index', [
            'karyawans' => $karyawans,
            'departemens' => $departemens,
            'jabatans' => $jabatans
        ]);
    }
}
