<?php

namespace App\Livewire\Admin\Dashboard;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\Karyawan;
use App\Models\Absensi;
use App\Models\Izin;
use App\Models\Cuti;
use Carbon\Carbon;

#[Layout('components.layouts.app')]
class Index extends Component
{
    public $totalKaryawan;
    public $karyawanAktif;
    public $hadirHariIni;
    public $izinHariIni;
    public $cutiAktif;
    public $recentAbsensi;
    public $pendingIzin;
    public $pendingCuti;

    public function mount()
    {
        $this->loadStatistics();
    }

    public function loadStatistics()
    {
        $today = Carbon::today();

        // Total statistics
        $this->totalKaryawan = Karyawan::count();
        $this->karyawanAktif = Karyawan::where('status', 'active')->count();
        
        // Today's attendance
        $this->hadirHariIni = Absensi::whereDate('tanggal', $today)
            ->where('status', 'hadir')
            ->count();
        
        $this->izinHariIni = Izin::whereDate('tanggal_mulai', '<=', $today)
            ->whereDate('tanggal_selesai', '>=', $today)
            ->where('status', 'disetujui')
            ->count();
        
        $this->cutiAktif = Cuti::whereDate('tanggal_mulai', '<=', $today)
            ->whereDate('tanggal_selesai', '>=', $today)
            ->where('status', 'disetujui')
            ->count();

        // Recent data
        $this->recentAbsensi = Absensi::with('karyawan')
            ->latest()
            ->take(5)
            ->get();

        $this->pendingIzin = Izin::where('status', 'pending')->count();
        $this->pendingCuti = Cuti::where('status', 'pending')->count();
    }

    #[Title('Dashboard Admin')]
    public function render()
    {
        return view('livewire.admin.dashboard.index');
    }
}
