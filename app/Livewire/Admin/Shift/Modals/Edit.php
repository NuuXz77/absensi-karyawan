<?php

namespace App\Livewire\Admin\Shift\Modals;

use App\Models\Shift;
use Livewire\Component;

class Edit extends Component
{
    public $shiftId;
    public $nama_shift = '';
    public $jam_masuk = '';
    public $jam_pulang = '';
    public $toleransi_menit = 15;
    public $status = 'active';

    public $showSuccess = false;
    public $showError = false;
    public $errorMessage = '';

    protected $messages = [
        'nama_shift.required' => 'Nama shift wajib diisi',
        'nama_shift.unique' => 'Nama shift sudah digunakan',
        'jam_masuk.required' => 'Jam masuk wajib diisi',
        'jam_masuk.date_format' => 'Format jam masuk tidak valid',
        'jam_pulang.required' => 'Jam pulang wajib diisi',
        'jam_pulang.date_format' => 'Format jam pulang tidak valid',
        'jam_pulang.after' => 'Jam pulang harus setelah jam masuk',
        'toleransi_menit.required' => 'Toleransi keterlambatan wajib diisi',
        'toleransi_menit.min' => 'Toleransi minimal 0 menit',
        'toleransi_menit.max' => 'Toleransi maksimal 60 menit',
    ];

    protected $listeners = ['open-edit-modal' => 'loadShift'];

    protected function rules()
    {
        return [
            'nama_shift' => 'required|string|max:255|unique:shift,nama_shift,' . $this->shiftId,
            'jam_masuk' => 'required|date_format:H:i',
            'jam_pulang' => 'required|date_format:H:i|after:jam_masuk',
            'toleransi_menit' => 'required|integer|min:0|max:60',
        ];
    }

    public function loadShift($id)
    {
        $this->shiftId = $id;
        $shift = Shift::findOrFail($id);
        
        $this->nama_shift = $shift->nama_shift;
        $this->jam_masuk = \Carbon\Carbon::parse($shift->jam_masuk)->format('H:i');
        $this->jam_pulang = \Carbon\Carbon::parse($shift->jam_pulang)->format('H:i');
        $this->toleransi_menit = $shift->toleransi_menit;
        $this->status = $shift->status;
        
        $this->resetValidation();
        $this->showSuccess = false;
        $this->showError = false;
    }

    public function update()
    {
        $this->showSuccess = false;
        $this->showError = false;

        $this->validate();

        try {
            $shift = Shift::findOrFail($this->shiftId);
            
            $shift->update([
                'nama_shift' => $this->nama_shift,
                'jam_masuk' => $this->jam_masuk,
                'jam_pulang' => $this->jam_pulang,
                'toleransi_menit' => $this->toleransi_menit,
                'status' => $this->status,
            ]);

            $this->showSuccess = true;
            $this->showError = false;
            
            $this->dispatch('shift-updated');
            $this->dispatch('close-edit-modal');
            
        } catch (\Exception $e) {
            $this->showError = true;
            $this->showSuccess = false;
            $this->errorMessage = 'Gagal mengupdate data: ' . $e->getMessage();
        }
    }

    public function closeModal()
    {
        $this->reset(['nama_shift', 'jam_masuk', 'jam_pulang', 'shiftId', 'showError', 'errorMessage']);
        $this->toleransi_menit = 15;
        $this->status = 'active';
        $this->resetValidation();
        $this->dispatch('close-edit-modal');
    }

    public function render()
    {
        return view('livewire.admin.shift.modals.edit');
    }
}
