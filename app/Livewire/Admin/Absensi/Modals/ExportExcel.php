<?php

namespace App\Livewire\Admin\Absensi\Modals;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\Karyawan;
use App\Models\Absensi;
use App\Exports\Admin\Absensi\ExportExcel as AbsensiExport;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class ExportExcel extends Component
{
    public $karyawan_id = '';
    public $nama_file = '';
    public $tipe_periode = 'tanggal'; // tanggal atau jangka_waktu
    
    // Untuk tipe tanggal
    public $tanggal_awal;
    public $tanggal_akhir = '';
    
    // Untuk tipe jangka waktu
    public $jangka_waktu = 'mingguan'; // mingguan, bulanan, tahunan
    
    // Mingguan
    public $minggu_awal;
    public $minggu_akhir;
    public $tahun_minggu;
    public $bulan_minggu;
    
    // Bulanan
    public $bulan_awal;
    public $bulan_akhir;
    public $tahun_bulan;
    
    // Tahunan
    public $tahun_awal;
    public $tahun_akhir;
    
    public $totalAbsensi = 0;
    public $periodeText = '';

    public function mount()
    {
        $this->tanggal_awal = Carbon::today()->format('Y-m-d');
        $this->tahun_minggu = Carbon::now()->year;
        $this->bulan_minggu = Carbon::now()->month;
        $this->minggu_awal = 1;
        $this->minggu_akhir = 1;
        
        $this->tahun_bulan = Carbon::now()->year;
        $this->bulan_awal = Carbon::now()->month;
        $this->bulan_akhir = Carbon::now()->month;
        
        $this->tahun_awal = Carbon::now()->year;
        $this->tahun_akhir = Carbon::now()->year;
        
        $this->generateFilename();
        $this->calculateTotal();
    }

    public function updatedKaryawanId()
    {
        $this->generateFilename();
        $this->calculateTotal();
    }
    
    public function updatedTipePeriode()
    {
        $this->generateFilename();
        $this->calculateTotal();
    }
    
    public function updatedJangkaWaktu()
    {
        $this->generateFilename();
        $this->calculateTotal();
    }

    public function updatedTanggalAwal()
    {
        $this->validateDates();
        $this->generateFilename();
        $this->calculateTotal();
    }

    public function updatedTanggalAkhir()
    {
        $this->validateDates();
        $this->generateFilename();
        $this->calculateTotal();
    }
    
    public function updatedBulanMinggu()
    {
        $this->bulan_minggu = (int) $this->bulan_minggu;
        $this->generateFilename();
        $this->calculateTotal();
    }
    
    public function updatedMingguAwal()
    {
        $this->minggu_awal = (int) $this->minggu_awal;
        if ($this->minggu_awal < 1) $this->minggu_awal = 1;
        if ($this->minggu_awal > 5) $this->minggu_awal = 5;
        
        if ($this->minggu_akhir < $this->minggu_awal) {
            $this->minggu_akhir = $this->minggu_awal;
        }
        $this->generateFilename();
        $this->calculateTotal();
    }
    
    public function updatedMingguAkhir()
    {
        $this->minggu_akhir = (int) $this->minggu_akhir;
        if ($this->minggu_akhir < $this->minggu_awal) {
            $this->minggu_akhir = $this->minggu_awal;
        }
        if ($this->minggu_akhir > 5) $this->minggu_akhir = 5;
        $this->generateFilename();
        $this->calculateTotal();
    }
    
    public function updatedTahunMinggu()
    {
        $this->tahun_minggu = (int) $this->tahun_minggu;
        $this->generateFilename();
        $this->calculateTotal();
    }
    
    public function updatedBulanAwal()
    {
        $this->bulan_awal = (int) $this->bulan_awal;
        if ($this->bulan_awal < 1) $this->bulan_awal = 1;
        if ($this->bulan_awal > 12) $this->bulan_awal = 12;
        
        if ($this->bulan_akhir < $this->bulan_awal) {
            $this->bulan_akhir = $this->bulan_awal;
        }
        $this->generateFilename();
        $this->calculateTotal();
    }
    
    public function updatedBulanAkhir()
    {
        $this->bulan_akhir = (int) $this->bulan_akhir;
        if ($this->bulan_akhir < $this->bulan_awal) {
            $this->bulan_akhir = $this->bulan_awal;
        }
        if ($this->bulan_akhir > 12) $this->bulan_akhir = 12;
        $this->generateFilename();
        $this->calculateTotal();
    }
    
    public function updatedTahunBulan()
    {
        $this->tahun_bulan = (int) $this->tahun_bulan;
        $this->generateFilename();
        $this->calculateTotal();
    }
    
    public function updatedTahunAwal()
    {
        $this->tahun_awal = (int) $this->tahun_awal;
        if ($this->tahun_akhir < $this->tahun_awal) {
            $this->tahun_akhir = $this->tahun_awal;
        }
        $this->generateFilename();
        $this->calculateTotal();
    }
    
    public function updatedTahunAkhir()
    {
        $this->tahun_akhir = (int) $this->tahun_akhir;
        if ($this->tahun_akhir < $this->tahun_awal) {
            $this->tahun_akhir = $this->tahun_awal;
        }
        $this->generateFilename();
        $this->calculateTotal();
    }

    public function validateDates()
    {
        if ($this->tanggal_awal && $this->tanggal_akhir) {
            if ($this->tanggal_awal > $this->tanggal_akhir) {
                $this->tanggal_akhir = $this->tanggal_awal;
            }
        }
    }
    
    public function getDateRange()
    {
        if ($this->tipe_periode === 'tanggal') {
            $start = $this->tanggal_awal;
            $end = $this->tanggal_akhir ?: $this->tanggal_awal;
            $this->periodeText = Carbon::parse($start)->format('d/m/Y') . ' - ' . Carbon::parse($end)->format('d/m/Y');
            return [$start, $end];
        }
        
        if ($this->jangka_waktu === 'mingguan') {
            $tahun = (int) $this->tahun_minggu;
            $bulan = (int) $this->bulan_minggu;
            $mingguAwal = (int) $this->minggu_awal;
            $mingguAkhir = (int) $this->minggu_akhir;
            
            // Ambil tanggal awal bulan
            $startOfMonth = Carbon::create($tahun, $bulan, 1)->startOfMonth();
            
            // Hitung minggu ke-n dalam bulan (setiap minggu = 7 hari)
            $start = $startOfMonth->copy()->addWeeks($mingguAwal - 1)->startOfWeek();
            $end = $startOfMonth->copy()->addWeeks($mingguAkhir - 1)->endOfWeek();
            
            // Pastikan tidak keluar dari bulan yang dipilih
            $endOfMonth = Carbon::create($tahun, $bulan, 1)->endOfMonth();
            if ($end->greaterThan($endOfMonth)) {
                $end = $endOfMonth;
            }
            
            $namaBulan = Carbon::create($tahun, $bulan)->locale('id')->monthName;
            $this->periodeText = 'Minggu ' . $mingguAwal . '-' . $mingguAkhir . ' ' . $namaBulan . ' ' . $tahun;
        } elseif ($this->jangka_waktu === 'bulanan') {
            $tahun = (int) $this->tahun_bulan;
            $bulanAwal = (int) $this->bulan_awal;
            $bulanAkhir = (int) $this->bulan_akhir;
            
            $start = Carbon::create($tahun, $bulanAwal, 1)->startOfMonth();
            $end = Carbon::create($tahun, $bulanAkhir, 1)->endOfMonth();
            $namaBulanAwal = Carbon::create($tahun, $bulanAwal)->locale('id')->monthName;
            $namaBulanAkhir = Carbon::create($tahun, $bulanAkhir)->locale('id')->monthName;
            $this->periodeText = $namaBulanAwal . ' - ' . $namaBulanAkhir . ' ' . $tahun;
        } else { // tahunan
            $tahunAwal = (int) $this->tahun_awal;
            $tahunAkhir = (int) $this->tahun_akhir;
            
            $start = Carbon::create($tahunAwal, 1, 1)->startOfYear();
            $end = Carbon::create($tahunAkhir, 12, 31)->endOfYear();
            $this->periodeText = 'Tahun ' . $tahunAwal . ' - ' . $tahunAkhir;
        }
        
        return [$start->format('Y-m-d'), $end->format('Y-m-d')];
    }
    
    public function generateFilename()
    {
        $karyawan = '';
        if ($this->karyawan_id) {
            $karyawanModel = Karyawan::find($this->karyawan_id);
            if ($karyawanModel) {
                $karyawan = str_replace(' ', '_', $karyawanModel->nama_lengkap) . '_';
            }
        }
        
        list($start, $end) = $this->getDateRange();
        
        if ($this->tipe_periode === 'tanggal') {
            $periode = Carbon::parse($start)->format('d-m-Y');
            if ($end && $end !== $start) {
                $periode .= '_sampai_' . Carbon::parse($end)->format('d-m-Y');
            }
        } else {
            if ($this->jangka_waktu === 'mingguan') {
                $mingguAwal = (int) $this->minggu_awal;
                $mingguAkhir = (int) $this->minggu_akhir;
                $bulan = (int) $this->bulan_minggu;
                $tahun = (int) $this->tahun_minggu;
                $periode = 'Minggu_' . $mingguAwal . '-' . $mingguAkhir . '_Bulan_' . $bulan . '_' . $tahun;
            } elseif ($this->jangka_waktu === 'bulanan') {
                $bulanAwal = (int) $this->bulan_awal;
                $bulanAkhir = (int) $this->bulan_akhir;
                $tahun = (int) $this->tahun_bulan;
                $periode = 'Bulan_' . $bulanAwal . '-' . $bulanAkhir . '_' . $tahun;
            } else {
                $tahunAwal = (int) $this->tahun_awal;
                $tahunAkhir = (int) $this->tahun_akhir;
                $periode = 'Tahun_' . $tahunAwal . '-' . $tahunAkhir;
            }
        }
        
        $this->nama_file = 'Absensi_' . $karyawan . $periode;
    }

    public function calculateTotal()
    {
        list($start, $end) = $this->getDateRange();
        
        $query = Absensi::query();

        if ($this->karyawan_id) {
            $query->where('karyawan_id', $this->karyawan_id);
        }

        $query->whereBetween('tanggal', [$start, $end]);

        $this->totalAbsensi = $query->count();
    }

    public function resetFilters()
    {
        $this->karyawan_id = '';
        $this->tipe_periode = 'tanggal';
        $this->tanggal_awal = Carbon::today()->format('Y-m-d');
        $this->tanggal_akhir = '';
        $this->jangka_waktu = 'mingguan';
        
        $this->tahun_minggu = Carbon::now()->year;
        $this->minggu_awal = Carbon::now()->weekOfYear;
        $this->minggu_akhir = Carbon::now()->weekOfYear;
        
        $this->tahun_bulan = Carbon::now()->year;
        $this->bulan_awal = Carbon::now()->month;
        $this->bulan_akhir = Carbon::now()->month;
        
        $this->tahun_awal = Carbon::now()->year;
        $this->tahun_akhir = Carbon::now()->year;
        
        $this->generateFilename();
        $this->calculateTotal();
    }

    public function export()
    {
        if ($this->totalAbsensi == 0) {
            $this->dispatch('show-toast', type: 'error', message: 'Tidak ada data absensi untuk diekspor');
            return;
        }
        
        list($start, $end) = $this->getDateRange();

        $filename = $this->nama_file . '.xlsx';

        $this->dispatch('show-toast', type: 'success', message: 'Data sedang diekspor...');
        
        return Excel::download(
            new AbsensiExport(
                $this->karyawan_id,
                $start,
                $end,
                $this->periodeText
            ),
            $filename
        );
    }

    public function render()
    {
        $karyawans = Karyawan::where('status', 'active')
            ->orderBy('nama_lengkap')
            ->get();

        return view('livewire.admin.absensi.modals.export-excel', [
            'karyawans' => $karyawans,
        ]);
    }
}
