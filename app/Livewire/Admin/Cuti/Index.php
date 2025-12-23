<?php

namespace App\Livewire\Admin\Cuti;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\Cuti;
use App\Models\Karyawan;
use Illuminate\Support\Facades\Auth;

#[Layout('components.layouts.app')]
class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $filterStatus = '';
    public $filterKaryawan = '';
    public $sortField = 'created_at';
    public $sortDirection = 'desc';
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
        $this->filterKaryawan = '';
    }

    public function approve($id)
    {
        $cuti = Cuti::findOrFail($id);
        $cuti->update([
            'status' => 'disetujui',
            'disetujui_oleh' => Auth::id(),
        ]);

        session()->flash('success', 'Cuti berhasil disetujui!');
    }

    public function reject($id)
    {
        $cuti = Cuti::findOrFail($id);
        $cuti->update([
            'status' => 'ditolak',
            'disetujui_oleh' => Auth::id(),
        ]);

        session()->flash('success', 'Cuti ditolak!');
    }

    #[Title('Pengajuan Cuti')]
    public function render()
    {
        $cutis = Cuti::with(['karyawan', 'disetujuiOleh'])
            ->when($this->search, function($query) {
                $query->whereHas('karyawan', function($q) {
                    $q->where('nama_lengkap', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->filterStatus, function($query) {
                $query->where('status', $this->filterStatus);
            })
            ->when($this->filterKaryawan, function($query) {
                $query->where('karyawan_id', $this->filterKaryawan);
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        $karyawans = Karyawan::where('status', 'active')->get();

        return view('livewire.admin.cuti.index', [
            'cutis' => $cutis,
            'karyawans' => $karyawans,
        ]);
    }
}
