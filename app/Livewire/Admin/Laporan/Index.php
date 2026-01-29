<?php

namespace App\Livewire\Admin\Laporan;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\Karyawan;
use App\Models\Absensi;
use App\Models\Cuti;
use App\Models\Izin;
use App\Models\Departemen;
use App\Models\Jabatan;
use App\Models\Lokasi;
use App\Models\Shift;
use Carbon\Carbon;

#[Layout('components.layouts.app')]
class Index extends Component
{
    // Filter properties
    public $tanggal_awal;
    public $tanggal_akhir;
    public $departemen_id = '';
    public $jabatan_id = '';
    
    // Statistics
    public $totalKaryawan = 0;
    public $totalAbsensi = 0;
    public $totalCuti = 0;
    public $totalIzin = 0;
    public $totalDepartemen = 0;
    public $totalJabatan = 0;
    public $totalLokasi = 0;
    public $totalShift = 0;
    
    // Detailed statistics
    public $karyawanAktif = 0;
    public $karyawanNonAktif = 0;
    public $absensiHariIni = 0;
    public $cutiDiajukan = 0;
    public $cutiDisetujui = 0;
    public $izinDiajukan = 0;
    public $izinDisetujui = 0;

    public function mount()
    {
        // Set default filter ke bulan ini
        $this->tanggal_awal = Carbon::now()->startOfMonth()->format('Y-m-d');
        $this->tanggal_akhir = Carbon::now()->endOfMonth()->format('Y-m-d');
        
        $this->calculateStatistics();
    }

    public function updatedTanggalAwal()
    {
        $this->validateDates();
        $this->calculateStatistics();
    }

    public function updatedTanggalAkhir()
    {
        $this->validateDates();
        $this->calculateStatistics();
    }

    public function updatedDepartemenId()
    {
        $this->calculateStatistics();
    }

    public function updatedJabatanId()
    {
        $this->calculateStatistics();
    }

    public function validateDates()
    {
        if ($this->tanggal_awal && $this->tanggal_akhir) {
            if ($this->tanggal_awal > $this->tanggal_akhir) {
                $this->tanggal_akhir = $this->tanggal_awal;
            }
        }
    }

    public function calculateStatistics()
    {
        // Base query untuk karyawan dengan filter
        $karyawanQuery = Karyawan::query();
        
        if ($this->departemen_id) {
            $karyawanQuery->where('departemen_id', $this->departemen_id);
        }
        
        if ($this->jabatan_id) {
            $karyawanQuery->where('jabatan_id', $this->jabatan_id);
        }
        
        // Total Karyawan
        $this->totalKaryawan = $karyawanQuery->count();
        $this->karyawanAktif = (clone $karyawanQuery)->where('status', 'active')->count();
        $this->karyawanNonAktif = (clone $karyawanQuery)->where('status', '!=', 'active')->count();
        
        // Get karyawan IDs untuk filter data lain
        $karyawanIds = (clone $karyawanQuery)->pluck('id')->toArray();
        
        // Total Absensi dengan filter tanggal dan karyawan
        $absensiQuery = Absensi::query();
        
        if (!empty($karyawanIds)) {
            $absensiQuery->whereIn('karyawan_id', $karyawanIds);
        }
        
        if ($this->tanggal_awal && $this->tanggal_akhir) {
            $absensiQuery->whereBetween('tanggal', [$this->tanggal_awal, $this->tanggal_akhir]);
        }
        
        $this->totalAbsensi = $absensiQuery->count();
        $this->absensiHariIni = Absensi::whereDate('tanggal', Carbon::today())
            ->when(!empty($karyawanIds), fn($q) => $q->whereIn('karyawan_id', $karyawanIds))
            ->count();
        
        // Total Cuti dengan filter
        $cutiQuery = Cuti::query();
        
        if (!empty($karyawanIds)) {
            $cutiQuery->whereIn('karyawan_id', $karyawanIds);
        }
        
        if ($this->tanggal_awal && $this->tanggal_akhir) {
            $cutiQuery->where(function($q) {
                $q->whereBetween('tanggal_mulai', [$this->tanggal_awal, $this->tanggal_akhir])
                  ->orWhereBetween('tanggal_selesai', [$this->tanggal_awal, $this->tanggal_akhir]);
            });
        }
        
        $this->totalCuti = $cutiQuery->count();
        $this->cutiDiajukan = (clone $cutiQuery)->where('status', 'pending')->count();
        $this->cutiDisetujui = (clone $cutiQuery)->where('status', 'approved')->count();
        
        // Total Izin dengan filter
        $izinQuery = Izin::query();
        
        if (!empty($karyawanIds)) {
            $izinQuery->whereIn('karyawan_id', $karyawanIds);
        }
        
        if ($this->tanggal_awal && $this->tanggal_akhir) {
            $izinQuery->where(function($q) {
                $q->whereBetween('tanggal_mulai', [$this->tanggal_awal, $this->tanggal_akhir])
                  ->orWhereBetween('tanggal_selesai', [$this->tanggal_awal, $this->tanggal_akhir]);
            });
        }
        
        $this->totalIzin = $izinQuery->count();
        $this->izinDiajukan = (clone $izinQuery)->where('status', 'pending')->count();
        $this->izinDisetujui = (clone $izinQuery)->where('status', 'approved')->count();
        
        // Total data master (tidak terpengaruh filter tanggal)
        $this->totalDepartemen = Departemen::count();
        $this->totalJabatan = Jabatan::count();
        $this->totalLokasi = Lokasi::count();
        $this->totalShift = Shift::count();
    }

    public function resetFilters()
    {
        $this->tanggal_awal = Carbon::now()->startOfMonth()->format('Y-m-d');
        $this->tanggal_akhir = Carbon::now()->endOfMonth()->format('Y-m-d');
        $this->departemen_id = '';
        $this->jabatan_id = '';
        
        $this->calculateStatistics();
    }

    public function exportAllReports()
    {
        $this->dispatch('show-toast', type: 'info', message: 'Fitur export semua laporan akan segera hadir');
    }

    #[Title('Laporan Keseluruhan Data')]
    public function render()
    {
        $departemens = Departemen::orderBy('nama_departemen')->get();
        $jabatans = Jabatan::orderBy('nama_jabatan')->get();
        
        return view('livewire.admin.laporan.index', [
            'departemens' => $departemens,
            'jabatans' => $jabatans,
        ]);
    }
}
