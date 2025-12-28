<?php

namespace App\Livewire\Admin\Jadwal\Modals;

use App\Models\JadwalKerja;
use App\Models\Karyawan;
use App\Models\Shift;
use App\Models\Departemen;
use Livewire\Component;
use Carbon\Carbon;

class AutoGenerate extends Component
{
    public $tanggal_mulai;
    public $tanggal_selesai;
    public $departemen_id = '';
    public $shift_pattern = 'single'; // single, rotating
    public $selected_shifts = [];
    public $include_weekends = false;
    public $karyawan_ids = [];

    public $showSuccess = false;
    public $showError = false;
    public $errorMessage = '';
    public $successMessage = '';

    protected $rules = [
        'tanggal_mulai' => 'required|date',
        'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
        'departemen_id' => 'required|exists:departemen,id',
        'shift_pattern' => 'required|in:single,rotating',
        'selected_shifts' => 'required|array|min:1',
    ];

    protected $messages = [
        'tanggal_mulai.required' => 'Tanggal mulai wajib diisi',
        'tanggal_selesai.required' => 'Tanggal selesai wajib diisi',
        'tanggal_selesai.after_or_equal' => 'Tanggal selesai harus setelah tanggal mulai',
        'departemen_id.required' => 'Departemen wajib dipilih',
        'shift_pattern.required' => 'Pola shift wajib dipilih',
        'selected_shifts.required' => 'Minimal pilih 1 shift',
        'selected_shifts.min' => 'Minimal pilih 1 shift',
    ];

    public function openModal()
    {
        // Set default dates (current month)
        $this->tanggal_mulai = Carbon::now()->startOfMonth()->format('Y-m-d');
        $this->tanggal_selesai = Carbon::now()->endOfMonth()->format('Y-m-d');
        
        $this->resetValidation();
        $this->showSuccess = false;
        $this->showError = false;
    }

    public function updatedDepartemenId()
    {
        $this->loadKaryawan();
    }

    public function loadKaryawan()
    {
        if ($this->departemen_id) {
            $this->karyawan_ids = Karyawan::where('departemen_id', $this->departemen_id)
                ->where('status', 'active')
                ->pluck('id')
                ->toArray();
        } else {
            $this->karyawan_ids = [];
        }
    }

    public function generate()
    {
        $this->showSuccess = false;
        $this->showError = false;

        $this->validate();

        try {
            $this->loadKaryawan();

            if (empty($this->karyawan_ids)) {
                $this->showError = true;
                $this->errorMessage = 'Tidak ada karyawan aktif di departemen ini!';
                return;
            }

            $startDate = Carbon::parse($this->tanggal_mulai);
            $endDate = Carbon::parse($this->tanggal_selesai);
            $totalGenerated = 0;
            $totalSkipped = 0;

            $currentDate = $startDate->copy();
            $shiftIndex = 0;

            while ($currentDate <= $endDate) {
                // Skip weekends if not included
                if (!$this->include_weekends && $currentDate->isWeekend()) {
                    $currentDate->addDay();
                    continue;
                }

                foreach ($this->karyawan_ids as $karyawanId) {
                    // Check if jadwal already exists
                    $exists = JadwalKerja::where('karyawan_id', $karyawanId)
                        ->where('tanggal', $currentDate->format('Y-m-d'))
                        ->exists();

                    if ($exists) {
                        $totalSkipped++;
                        continue;
                    }

                    // Determine shift based on pattern
                    if ($this->shift_pattern === 'single') {
                        $shiftId = $this->selected_shifts[0];
                    } else {
                        // Rotating pattern
                        $shiftId = $this->selected_shifts[$shiftIndex % count($this->selected_shifts)];
                    }

                    // Create jadwal
                    JadwalKerja::create([
                        'karyawan_id' => $karyawanId,
                        'shift_id' => $shiftId,
                        'tanggal' => $currentDate->format('Y-m-d'),
                        'status' => 'aktif',
                    ]);

                    $totalGenerated++;
                }

                if ($this->shift_pattern === 'rotating') {
                    $shiftIndex++;
                }

                $currentDate->addDay();
            }

            $this->showSuccess = true;
            $this->successMessage = "Berhasil generate {$totalGenerated} jadwal!";
            if ($totalSkipped > 0) {
                $this->successMessage .= " ({$totalSkipped} jadwal di-skip karena sudah ada)";
            }
            
            $this->dispatch('jadwals-generated');
            
            // Reset form
            $this->reset(['departemen_id', 'selected_shifts', 'karyawan_ids']);
            $this->shift_pattern = 'single';
            $this->include_weekends = false;
            
        } catch (\Exception $e) {
            $this->showError = true;
            $this->showSuccess = false;
            $this->errorMessage = 'Gagal generate jadwal: ' . $e->getMessage();
        }
    }

    public function closeModal()
    {
        $this->reset([
            'tanggal_mulai', 
            'tanggal_selesai', 
            'departemen_id', 
            'selected_shifts', 
            'karyawan_ids',
            'showError', 
            'errorMessage'
        ]);
        $this->shift_pattern = 'single';
        $this->include_weekends = false;
        $this->resetValidation();
        $this->dispatch('close-auto-generate-modal');
    }

    public function render()
    {
        $departemens = Departemen::where('status', 'active')
            ->orderBy('nama_departemen')
            ->get();

        $shifts = Shift::where('status', 'active')
            ->orderBy('nama_shift')
            ->get();

        return view('livewire.admin.jadwal.modals.auto-generate', [
            'departemens' => $departemens,
            'shifts' => $shifts,
        ]);
    }
}
