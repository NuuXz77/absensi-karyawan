<?php

namespace App\Livewire\Admin\Cuti\Modals;

use App\Models\Cuti;
use Livewire\Component;
use Livewire\Attributes\Computed;

class Detail extends Component
{
    public $cutiId;
    
    // Karyawan fields
    public $nama_lengkap;
    public $nip;
    public $nama_departemen;
    public $nama_jabatan;
    
    // Cuti fields
    public $tanggal_mulai;
    public $tanggal_selesai;
    public $jenis_cuti;
    public $jumlah_hari;
    public $keterangan;
    public $bukti_foto;
    public $status;
    
    // Approval fields
    public $diproses_oleh;
    public $tanggal_diproses;
    public $created_at;

    protected $listeners = ['open-detail-modal' => 'openModal'];

    #[Computed]
    public function cuti()
    {
        return $this->cutiId ? Cuti::with(['karyawan.departemen', 'karyawan.jabatan', 'disetujuiOleh.karyawan'])
            ->find($this->cutiId) : null;
    }

    public function openModal($id)
    {
        $this->cutiId = $id;
        $this->loadCutiData();
    }
    
    public function loadCutiData()
    {
        $cuti = $this->cuti;
        
        if ($cuti) {
            $this->nama_lengkap = $cuti->karyawan->nama_lengkap ?? '-';
            $this->nip = $cuti->karyawan->nip ?? '-';
            $this->nama_departemen = $cuti->karyawan->departemen->nama_departemen ?? '-';
            $this->nama_jabatan = $cuti->karyawan->jabatan->nama_jabatan ?? '-';
            
            $this->tanggal_mulai = \Carbon\Carbon::parse($cuti->tanggal_mulai)->locale('id')->isoFormat('dddd, D MMMM Y');
            $this->tanggal_selesai = \Carbon\Carbon::parse($cuti->tanggal_selesai)->locale('id')->isoFormat('dddd, D MMMM Y');
            
            $this->jenis_cuti = match($cuti->jenis_cuti) {
                'tahunan' => 'Cuti Tahunan',
                'khusus' => 'Cuti Khusus',
                default => ucfirst($cuti->jenis_cuti)
            };
            
            $this->jumlah_hari = $cuti->jumlah_hari . ' hari';
            $this->keterangan = $cuti->keterangan ?? 'Tidak ada keterangan';
            $this->bukti_foto = $cuti->bukti_foto;
            $this->status = $cuti->status;
            
            if ($cuti->status !== 'pending') {
                $this->diproses_oleh = $cuti->disetujuiOleh->karyawan->nama_lengkap ?? 'Admin';
                $this->tanggal_diproses = \Carbon\Carbon::parse($cuti->updated_at)->locale('id')->isoFormat('D MMMM Y, HH:mm');
            }
            
            $this->created_at = \Carbon\Carbon::parse($cuti->created_at)->locale('id')->isoFormat('dddd, D MMMM Y HH:mm');
        }
    }

    public function closeModal()
    {
        $this->reset([
            'cutiId', 'nama_lengkap', 'nip', 'nama_departemen', 'nama_jabatan',
            'tanggal_mulai', 'tanggal_selesai', 'jenis_cuti', 'jumlah_hari',
            'keterangan', 'bukti_foto', 'status', 'diproses_oleh', 'tanggal_diproses', 'created_at'
        ]);
        $this->dispatch('close-detail-modal');
    }

    public function render()
    {
        return view('livewire.admin.cuti.modals.detail');
    }
}
