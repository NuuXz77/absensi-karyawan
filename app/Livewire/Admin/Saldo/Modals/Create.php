<?php

namespace App\Livewire\Admin\Saldo\Modals;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\SaldoCutiDanIzin;
use App\Models\Karyawan;

class Create extends Component
{
    public $karyawan_id = '';
    public $tahun;
    public $sisa_izin = 0;
    public $sisa_cuti = 0;

    public $showSuccess = false;
    public $showError = false;
    public $errorMessage = '';

    public $karyawans = [];

    public function mount()
    {
        $this->tahun = date('Y');
        $this->loadKaryawans();
    }

    public function loadKaryawans()
    {
        $this->karyawans = Karyawan::where('status', 'active')
            ->with(['departemen', 'jabatan'])
            ->orderBy('nama_lengkap')
            ->get();
    }

    protected function rules()
    {
        return [
            'karyawan_id' => 'required|exists:karyawan,id',
            'tahun' => 'required|integer|min:2020|max:2100',
            'sisa_izin' => 'required|integer|min:0|max:365',
            'sisa_cuti' => 'required|integer|min:0|max:365',
        ];
    }

    protected $messages = [
        'karyawan_id.required' => 'Karyawan wajib dipilih',
        'karyawan_id.exists' => 'Karyawan tidak ditemukan',
        'tahun.required' => 'Tahun wajib diisi',
        'tahun.integer' => 'Tahun harus berupa angka',
        'tahun.min' => 'Tahun minimal 2020',
        'tahun.max' => 'Tahun maksimal 2100',
        'sisa_izin.required' => 'Sisa izin wajib diisi',
        'sisa_cuti.required' => 'Sisa cuti wajib diisi',
    ];

    public function openModal()
    {
        $this->dispatch('open-create-modal');
    }

    public function closeModal()
    {
        $this->reset(['karyawan_id', 'sisa_izin', 'sisa_cuti', 'showSuccess', 'showError', 'errorMessage']);
        $this->tahun = date('Y');
        $this->sisa_izin = 0;
        $this->sisa_cuti = 0;
        $this->dispatch('close-create-modal');
    }

    public function save()
    {
        $this->validate();

        try {
            // Check if saldo already exists for this karyawan and year
            $exists = SaldoCutiDanIzin::where('karyawan_id', $this->karyawan_id)
                ->where('tahun', $this->tahun)
                ->exists();

            if ($exists) {
                $this->showError = true;
                $this->errorMessage = 'Saldo untuk karyawan ini di tahun ' . $this->tahun . ' sudah ada!';
                return;
            }

            SaldoCutiDanIzin::create([
                'karyawan_id' => $this->karyawan_id,
                'tahun' => $this->tahun,
                'total_izin' => 0, // Default 0, belum ada pengajuan
                'sisa_izin' => $this->sisa_izin,
                'total_cuti' => 0, // Default 0, belum ada pengajuan
                'sisa_cuti' => $this->sisa_cuti,
            ]);

            $this->showSuccess = true;
            $this->dispatch('saldo-created');
            
            $this->reset(['karyawan_id', 'sisa_izin', 'sisa_cuti']);
            $this->tahun = date('Y');
            $this->sisa_izin = 0;
            $this->sisa_cuti = 0;

            $this->dispatch('close-create-modal');
        } catch (\Exception $e) {
            $this->showError = true;
            $this->errorMessage = 'Gagal menyimpan data: ' . $e->getMessage();
        }
    }

    public function render()
    {
        return view('livewire.admin.saldo.modals.create');
    }
}
