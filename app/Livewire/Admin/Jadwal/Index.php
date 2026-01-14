<?php

namespace App\Livewire\Admin\Jadwal;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\JadwalKerja;
use App\Models\Karyawan;
use App\Models\Shift;
use App\Models\Cuti;
use App\Models\Izin;
use App\Models\Departemen;
use App\Models\Lokasi;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

#[Layout('components.layouts.app')]
class Index extends Component
{
    public $currentDate;
    public $selectedMonth;
    public $selectedYear;
    public $filterKaryawan = '';
    public $filterLokasi = '';
    public $filterShift = '';
    public $filterDepartemen = '';
    public $viewMode = 'calendar'; // calendar atau list

    protected $queryString = ['filterLokasi', 'filterDepartemen', 'filterShift'];

    // Modal states
    public $showCreateModal = false;
    public $showAutoGenerateModal = false;

    public function mount()
    {
        $this->currentDate = Carbon::now();
        $this->selectedMonth = $this->currentDate->month;
        $this->selectedYear = $this->currentDate->year;
    }

    public function previousMonth()
    {
        $this->currentDate = $this->currentDate->subMonth();
        $this->selectedMonth = $this->currentDate->month;
        $this->selectedYear = $this->currentDate->year;
    }

    public function nextMonth()
    {
        $this->currentDate = $this->currentDate->addMonth();
        $this->selectedMonth = $this->currentDate->month;
        $this->selectedYear = $this->currentDate->year;
    }

    public function today()
    {
        $this->currentDate = Carbon::now();
        $this->selectedMonth = $this->currentDate->month;
        $this->selectedYear = $this->currentDate->year;
    }

    public function resetFilters()
    {
        $this->filterKaryawan = '';
        $this->filterLokasi = '';
        $this->filterShift = '';
        $this->filterDepartemen = '';
    }

    public function openCreateModal()
    {
        $this->dispatch('open-create-modal');
    }

    public function openAutoGenerateModal()
    {
        $this->dispatch('open-auto-generate-modal');
    }

    public function edit($id)
    {
        $this->dispatch('open-edit-modal', id: $id);
    }

    public function confirmDelete($id)
    {
        $this->dispatch('confirm-delete', id: $id);
    }

    // Drag and drop handler
    public function updateJadwal($jadwalId, $newDate, $newShiftId)
    {
        try {
            $jadwal = JadwalKerja::find($jadwalId);
            if ($jadwal) {
                $jadwal->update([
                    'tanggal' => $newDate,
                    'shift_id' => $newShiftId,
                ]);
                session()->flash('success', 'Jadwal berhasil diupdate!');
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal mengupdate jadwal: ' . $e->getMessage());
        }
    }

    // Get cuti/izin untuk tanggal tertentu
    public function getCutiIzinForDate($date)
    {
        $cutis = Cuti::with('karyawan')
            ->where('status', 'disetujui')
            ->whereDate('tanggal_mulai', '<=', $date)
            ->whereDate('tanggal_selesai', '>=', $date)
            ->get();

        $izins = Izin::with('karyawan')
            ->where('status', 'disetujui')
            ->whereDate('tanggal_mulai', '<=', $date)
            ->whereDate('tanggal_selesai', '>=', $date)
            ->get();

        return [
            'cutis' => $cutis,
            'izins' => $izins,
        ];
    }

    protected $listeners = [
        'jadwal-created' => '$refresh',
        'jadwal-updated' => '$refresh',
        'jadwal-deleted' => '$refresh',
        'jadwals-generated' => '$refresh',
    ];

    /**
     * Get Indonesian national holidays from API
     * Using https://api-harilibur.vercel.app/
     */
    public function getHariLiburNasional($year)
    {
        try {
            // Cache hasil API selama 30 hari untuk mengurangi request
            return Cache::remember("hari_libur_{$year}", now()->addDays(30), function () use ($year) {
                $response = Http::timeout(10)->get("https://api-harilibur.vercel.app/api", [
                    'year' => $year
                ]);

                if ($response->successful()) {
                    $holidays = collect($response->json());
                    
                    // Format data: date => [name, is_cuti]
                    return $holidays->mapWithKeys(function ($item) {
                        return [
                            $item['holiday_date'] => [
                                'name' => $item['holiday_name'],
                                'is_cuti' => $item['is_national_holiday'] ?? false
                            ]
                        ];
                    })->toArray();
                }

                return [];
            });
        } catch (\Exception $e) {
            \Log::error('Error fetching hari libur: ' . $e->getMessage());
            return [];
        }
    }

    #[Title('Jadwal Kerja')]
    public function render()
    {
        $karyawans = Karyawan::where('status', 'active')
            ->with('departemen')
            ->orderBy('nama_lengkap')
            ->get();
            
        $shifts = Shift::where('status', 'active')
            ->orderBy('nama_shift')
            ->get();

        $departemens = Departemen::where('status', 'active')
            ->orderBy('nama_departemen')
            ->get();
        
        $startOfMonth = Carbon::create($this->selectedYear, $this->selectedMonth, 1);
        $endOfMonth = Carbon::create($this->selectedYear, $this->selectedMonth, 1)->endOfMonth();
        $startOfCalendar = $startOfMonth->copy()->startOfWeek();
        $endOfCalendar = $endOfMonth->copy()->endOfWeek();

        // Load jadwals for current month with filters
        // Lokasi and Departemen filter are MANDATORY
        if (!$this->filterLokasi || !$this->filterDepartemen) {
            $jadwals = collect(); // Empty collection if no lokasi or departemen selected
        } else {
            $query = JadwalKerja::with(['karyawan.departemen', 'shift', 'lokasi'])
                ->whereBetween('tanggal', [$startOfMonth, $endOfMonth])
                ->where('lokasi_id', $this->filterLokasi)
                ->whereHas('karyawan', function($q) {
                    $q->where('departemen_id', $this->filterDepartemen);
                });

            if ($this->filterShift) {
                $query->where('shift_id', $this->filterShift);
            }

            $jadwals = $query->get();
        }

        // Get all cuti and izin for the month
        $cutisInMonth = Cuti::where('status', 'disetujui')
            ->where(function($query) use ($startOfMonth, $endOfMonth) {
                $query->whereBetween('tanggal_mulai', [$startOfMonth, $endOfMonth])
                    ->orWhereBetween('tanggal_selesai', [$startOfMonth, $endOfMonth])
                    ->orWhere(function($q) use ($startOfMonth, $endOfMonth) {
                        $q->where('tanggal_mulai', '<=', $startOfMonth)
                          ->where('tanggal_selesai', '>=', $endOfMonth);
                    });
            })
            ->with('karyawan')
            ->get();

        $izinsInMonth = Izin::where('status', 'disetujui')
            ->where(function($query) use ($startOfMonth, $endOfMonth) {
                $query->whereBetween('tanggal_mulai', [$startOfMonth, $endOfMonth])
                    ->orWhereBetween('tanggal_selesai', [$startOfMonth, $endOfMonth])
                    ->orWhere(function($q) use ($startOfMonth, $endOfMonth) {
                        $q->where('tanggal_mulai', '<=', $startOfMonth)
                          ->where('tanggal_selesai', '>=', $endOfMonth);
                    });
            })
            ->with('karyawan')
            ->get();

        // Get hari libur nasional from API
        $hariLiburNasional = $this->getHariLiburNasional($this->selectedYear);

        $lokasis = Lokasi::where('status', 'active')
            ->orderBy('nama_lokasi')
            ->get();

        return view('livewire.admin.jadwal.index', [
            'karyawans' => $karyawans,
            'shifts' => $shifts,
            'departemens' => $departemens,
            'lokasis' => $lokasis,
            'jadwals' => $jadwals,
            'startOfCalendar' => $startOfCalendar,
            'endOfCalendar' => $endOfCalendar,
            'startOfMonth' => $startOfMonth,
            'endOfMonth' => $endOfMonth,
            'cutisInMonth' => $cutisInMonth,
            'izinsInMonth' => $izinsInMonth,
            'hariLiburNasional' => $hariLiburNasional,
        ]);
    }
}
