<?php

namespace App\Livewire\Karyawan\Jadwal;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\JadwalKerja;
use App\Models\Cuti;
use App\Models\Izin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

#[Layout('components.layouts.app')]
class Index extends Component
{
    #[Title('Jadwal - Karyawan')]
    
    public $selectedMonth;
    public $selectedYear;
    public $viewMode = 'month'; // month or week
    
    public function mount()
    {
        $this->selectedMonth = Carbon::now()->month;
        $this->selectedYear = Carbon::now()->year;
    }
    
    public function previousMonth()
    {
        $date = Carbon::create($this->selectedYear, $this->selectedMonth, 1)->subMonth();
        $this->selectedMonth = $date->month;
        $this->selectedYear = $date->year;
    }
    
    public function nextMonth()
    {
        $date = Carbon::create($this->selectedYear, $this->selectedMonth, 1)->addMonth();
        $this->selectedMonth = $date->month;
        $this->selectedYear = $date->year;
    }
    
    public function today()
    {
        $this->selectedMonth = Carbon::now()->month;
        $this->selectedYear = Carbon::now()->year;
    }
    
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
    
    public function render()
    {
        $karyawan = Auth::user()->karyawan;
        $today = Carbon::today();
        
        // Jadwal hari ini
        $jadwalHariIni = JadwalKerja::with(['shift', 'lokasi'])
            ->where('karyawan_id', $karyawan->id)
            ->whereDate('tanggal', $today)
            ->first();
        
        // Cek cuti/izin hari ini
        $cutiHariIni = Cuti::where('karyawan_id', $karyawan->id)
            ->where('status', 'disetujui')
            ->whereDate('tanggal_mulai', '<=', $today)
            ->whereDate('tanggal_selesai', '>=', $today)
            ->first();
            
        $izinHariIni = Izin::where('karyawan_id', $karyawan->id)
            ->where('status', 'disetujui')
            ->whereDate('tanggal_mulai', '<=', $today)
            ->whereDate('tanggal_selesai', '>=', $today)
            ->first();
        
        // Calendar data
        $startOfMonth = Carbon::create($this->selectedYear, $this->selectedMonth, 1)->startOfMonth();
        $endOfMonth = Carbon::create($this->selectedYear, $this->selectedMonth, 1)->endOfMonth();
        
        // Adjust to start from Monday
        $startOfCalendar = $startOfMonth->copy()->startOfWeek(Carbon::MONDAY);
        $endOfCalendar = $endOfMonth->copy()->endOfWeek(Carbon::SUNDAY);
        
        // Get jadwal for this month
        $jadwals = JadwalKerja::with(['shift', 'lokasi'])
            ->where('karyawan_id', $karyawan->id)
            ->whereBetween('tanggal', [$startOfCalendar, $endOfCalendar])
            ->get();
        
        // Get cuti/izin for this month
        $cutisInMonth = Cuti::where('karyawan_id', $karyawan->id)
            ->where('status', 'disetujui')
            ->where(function($query) use ($startOfMonth, $endOfMonth) {
                $query->whereBetween('tanggal_mulai', [$startOfMonth, $endOfMonth])
                      ->orWhereBetween('tanggal_selesai', [$startOfMonth, $endOfMonth])
                      ->orWhere(function($q) use ($startOfMonth, $endOfMonth) {
                          $q->where('tanggal_mulai', '<=', $startOfMonth)
                            ->where('tanggal_selesai', '>=', $endOfMonth);
                      });
            })
            ->get();
            
        $izinsInMonth = Izin::where('karyawan_id', $karyawan->id)
            ->where('status', 'disetujui')
            ->where(function($query) use ($startOfMonth, $endOfMonth) {
                $query->whereBetween('tanggal_mulai', [$startOfMonth, $endOfMonth])
                      ->orWhereBetween('tanggal_selesai', [$startOfMonth, $endOfMonth])
                      ->orWhere(function($q) use ($startOfMonth, $endOfMonth) {
                          $q->where('tanggal_mulai', '<=', $startOfMonth)
                            ->where('tanggal_selesai', '>=', $endOfMonth);
                      });
            })
            ->get();
        
        // Get hari libur nasional from API
        $hariLiburNasional = $this->getHariLiburNasional($this->selectedYear);
        
        // Ringkasan bulan ini
        $totalHariKerja = $jadwals->where('tanggal', '>=', $startOfMonth)
            ->where('tanggal', '<=', $endOfMonth)
            ->count();
        
        // Hitung total hari dalam bulan
        $totalHariBulanIni = $endOfMonth->diffInDays($startOfMonth) + 1;
        
        // Hitung hari libur (weekend + libur nasional) - hanya yang tidak ada jadwal kerja
        $totalLibur = 0;
        $currentDate = $startOfMonth->copy();
        while ($currentDate <= $endOfMonth) {
            $dateString = $currentDate->format('Y-m-d');
            $isWeekend = $currentDate->isWeekend();
            $isNationalHoliday = isset($hariLiburNasional[$dateString]);
            $hasJadwal = $jadwals->where('tanggal', $currentDate)->count() > 0;
            
            // Hitung sebagai libur jika weekend atau libur nasional dan tidak ada jadwal
            if (($isWeekend || $isNationalHoliday) && !$hasJadwal) {
                $totalLibur++;
            }
            
            $currentDate->addDay();
        }
        
        $totalCuti = $cutisInMonth->sum(function($cuti) use ($startOfMonth, $endOfMonth) {
            $start = Carbon::parse($cuti->tanggal_mulai)->max($startOfMonth);
            $end = Carbon::parse($cuti->tanggal_selesai)->min($endOfMonth);
            return $start->diffInDays($end) + 1;
        });
        
        $totalIzin = $izinsInMonth->sum(function($izin) use ($startOfMonth, $endOfMonth) {
            $start = Carbon::parse($izin->tanggal_mulai)->max($startOfMonth);
            $end = Carbon::parse($izin->tanggal_selesai)->min($endOfMonth);
            return $start->diffInDays($end) + 1;
        });
        
        // Riwayat jadwal (30 hari terakhir)
        $riwayatJadwal = JadwalKerja::with(['shift', 'lokasi'])
            ->where('karyawan_id', $karyawan->id)
            ->where('tanggal', '<=', $today)
            ->orderBy('tanggal', 'desc')
            ->take(30)
            ->get();
        
        return view('livewire.karyawan.jadwal.index', [
            'jadwalHariIni' => $jadwalHariIni,
            'cutiHariIni' => $cutiHariIni,
            'izinHariIni' => $izinHariIni,
            'jadwals' => $jadwals,
            'cutisInMonth' => $cutisInMonth,
            'izinsInMonth' => $izinsInMonth,
            'startOfCalendar' => $startOfCalendar,
            'endOfCalendar' => $endOfCalendar,
            'startOfMonth' => $startOfMonth,
            'endOfMonth' => $endOfMonth,
            'totalHariKerja' => $totalHariKerja,
            'totalLibur' => $totalLibur,
            'totalCuti' => $totalCuti,
            'totalIzin' => $totalIzin,
            'riwayatJadwal' => $riwayatJadwal,
            'hariLiburNasional' => $hariLiburNasional,
        ]);
    }
}
