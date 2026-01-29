<?php

namespace App\Livewire\Admin\Saldo\Modals;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\SaldoCutiDanIzin;
use App\Models\Karyawan;

class Edit extends Component
{
    public $saldoId;
    public $karyawan_id = '';
    public $tahun;
    public $total_izin;
    public $sisa_izin;
    public $total_cuti;
    public $sisa_cuti;

    public $showSuccess = false;
    public $showError = false;
    public $errorMessage = '';

    public $karyawans = [];

    public function mount()
    {
        $this->loadKaryawans();
    }

    public function loadKaryawans()
    {
        $this->karyawans = Karyawan::where('status', 'active')
            ->with(['departemen', 'jabatan'])
            ->orderBy('nama_lengkap')
            ->get();
    }

    #[On('open-edit-modal')]
    public function openModal($id)
    {
        $this->saldoId = $id;
        $saldo = SaldoCutiDanIzin::findOrFail($id);
        
        $this->karyawan_id = $saldo->karyawan_id;
        $this->tahun = $saldo->tahun;
        $this->total_izin = $saldo->total_izin;
        $this->sisa_izin = $saldo->sisa_izin;
        $this->total_cuti = $saldo->total_cuti;
        $this->sisa_cuti = $saldo->sisa_cuti;

        $this->dispatch('show-edit-modal');
    }

    public function closeModal()
    {
        $this->reset(['saldoId', 'karyawan_id', 'tahun', 'total_izin', 'sisa_izin', 'total_cuti', 'sisa_cuti', 'showSuccess', 'showError', 'errorMessage']);
        $this->dispatch('hide-edit-modal');
    }

    protected function rules()
    {
        return [
            'karyawan_id' => 'required|exists:karyawan,id',
            'tahun' => 'required|integer|min:2020|max:2100',
            'total_izin' => 'required|integer|min:0|max:365',
            'sisa_izin' => 'required|integer|min:0|max:365|lte:total_izin',
            'total_cuti' => 'required|integer|min:0|max:365',
            'sisa_cuti' => 'required|integer|min:0|max:365|lte:total_cuti',
        ];
    }

    protected $messages = [
        'karyawan_id.required' => 'Karyawan wajib dipilih',
        'sisa_izin.lte' => 'Sisa izin tidak boleh lebih dari total izin',
        'sisa_cuti.lte' => 'Sisa cuti tidak boleh lebih dari total cuti',
    ];

    public function update()
    {
        $this->validate();

        try {
            $saldo = SaldoCutiDanIzin::findOrFail($this->saldoId);
            
            // Check if combination already exists (except current record)
            $exists = SaldoCutiDanIzin::where('karyawan_id', $this->karyawan_id)
                ->where('tahun', $this->tahun)
                ->where('id', '!=', $this->saldoId)
                ->exists();

            if ($exists) {
                $this->showError = true;
                $this->errorMessage = 'Saldo untuk karyawan ini di tahun ' . $this->tahun . ' sudah ada!';
                return;
            }

            $saldo->update([
                'karyawan_id' => $this->karyawan_id,
                'tahun' => $this->tahun,
                'total_izin' => $this->total_izin,
                'sisa_izin' => $this->sisa_izin,
                'total_cuti' => $this->total_cuti,
                'sisa_cuti' => $this->sisa_cuti,
            ]);

            $this->showSuccess = true;
            $this->dispatch('saldo-updated');
            $this->closeModal();
        } catch (\Exception $e) {
            $this->showError = true;
            $this->errorMessage = 'Gagal mengupdate data: ' . $e->getMessage();
        }
    }

    public function render()
    {
        return view('livewire.admin.saldo.modals.edit');
    }
}
