<?php

namespace App\Livewire\Admin\Jadwal;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\JadwalKerja;
use App\Models\Karyawan;
use App\Models\Shift;
use Carbon\Carbon;

#[Layout('components.layouts.app')]
class Index extends Component
{
    public $currentDate;
    public $selectedMonth;
    public $selectedYear;
    public $filterKaryawan = '';
    public $filterShift = '';
    public $jadwals = [];

    public function mount()
    {
        $this->currentDate = Carbon::now();
        $this->selectedMonth = $this->currentDate->month;
        $this->selectedYear = $this->currentDate->year;
        $this->loadJadwals();
    }

    public function previousMonth()
    {
        $this->currentDate = $this->currentDate->subMonth();
        $this->selectedMonth = $this->currentDate->month;
        $this->selectedYear = $this->currentDate->year;
        $this->loadJadwals();
    }

    public function nextMonth()
    {
        $this->currentDate = $this->currentDate->addMonth();
        $this->selectedMonth = $this->currentDate->month;
        $this->selectedYear = $this->currentDate->year;
        $this->loadJadwals();
    }

    public function today()
    {
        $this->currentDate = Carbon::now();
        $this->selectedMonth = $this->currentDate->month;
        $this->selectedYear = $this->currentDate->year;
        $this->loadJadwals();
    }

    public function loadJadwals()
    {
        $startOfMonth = Carbon::create($this->selectedYear, $this->selectedMonth, 1)->startOfMonth();
        $endOfMonth = Carbon::create($this->selectedYear, $this->selectedMonth, 1)->endOfMonth();

        $this->jadwals = JadwalKerja::with(['karyawan', 'shift'])
            ->whereBetween('tanggal', [$startOfMonth, $endOfMonth])
            ->when($this->filterKaryawan, function($query) {
                $query->where('karyawan_id', $this->filterKaryawan);
            })
            ->when($this->filterShift, function($query) {
                $query->where('shift_id', $this->filterShift);
            })
            ->get();
    }

    public function resetFilters()
    {
        $this->filterKaryawan = '';
        $this->filterShift = '';
        $this->loadJadwals();
    }

    #[Title('Jadwal Kerja')]
    public function render()
    {
        $karyawans = Karyawan::where('status', 'active')->get();
        $shifts = Shift::where('status', 'active')->get();
        
        $startOfMonth = Carbon::create($this->selectedYear, $this->selectedMonth, 1)->startOfMonth();
        $endOfMonth = Carbon::create($this->selectedYear, $this->selectedMonth, 1)->endOfMonth();
        $startOfCalendar = $startOfMonth->copy()->startOfWeek();
        $endOfCalendar = $endOfMonth->copy()->endOfWeek();

        return view('livewire.admin.jadwal.index', [
            'karyawans' => $karyawans,
            'shifts' => $shifts,
            'startOfCalendar' => $startOfCalendar,
            'endOfCalendar' => $endOfCalendar,
            'startOfMonth' => $startOfMonth,
            'endOfMonth' => $endOfMonth,
        ]);
    }
}
