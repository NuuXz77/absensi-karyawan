<?php

namespace App\Livewire\Admin\Izin\Modals;

use App\Models\Izin;
use Livewire\Component;

class Detail extends Component
{
    public $izinId;
    
    // Data karyawan
    public $nama_lengkap;
    public $nip;
    public $nama_departemen;
    public $nama_jabatan;
    
    // Data izin
    public $tanggal_mulai;
    public $tanggal_selesai;
    public $jenis_izin;
    public $durasi;
    public $alasan;
    public $bukti_foto;
    public $status;
    
    // Data persetujuan
    public $disetujui_oleh;
    public $tanggal_diproses;
    public $tanggal_diajukan;

    protected $listeners = ['open-detail-modal' => 'openModal'];

    public function openModal($id)
    {
        $this->izinId = $id;
        
        // Load data izin
        $izin = Izin::with(['karyawan.departemen', 'karyawan.jabatan', 'disetujuiOleh.karyawan'])
            ->find($id);
        
        if ($izin) {
            // Data karyawan
            $this->nama_lengkap = $izin->karyawan->nama_lengkap ?? '-';
            $this->nip = $izin->karyawan->nip ?? '-';
            $this->nama_departemen = $izin->karyawan->departemen->nama_departemen ?? '-';
            $this->nama_jabatan = $izin->karyawan->jabatan->nama_jabatan ?? '-';
            
            // Data izin
            $this->tanggal_mulai = \Carbon\Carbon::parse($izin->tanggal_mulai)->locale('id')->isoFormat('dddd, D MMMM Y');
            $this->tanggal_selesai = \Carbon\Carbon::parse($izin->tanggal_selesai)->locale('id')->isoFormat('dddd, D MMMM Y');
            $this->jenis_izin = match($izin->jenis_izin) {
                'sakit' => 'Sakit',
                'keperluan_pribadi' => 'Keperluan Pribadi',
                'dinas' => 'Dinas',
                'lainnya' => 'Lainnya',
                default => ucfirst($izin->jenis_izin)
            };
            $this->durasi = \Carbon\Carbon::parse($izin->tanggal_mulai)->diffInDays(\Carbon\Carbon::parse($izin->tanggal_selesai)) + 1;
            $this->alasan = $izin->alasan;
            $this->bukti_foto = $izin->bukti_foto;
            $this->status = $izin->status;
            
            // Data persetujuan
            if ($izin->status !== 'pending') {
                $this->disetujui_oleh = $izin->disetujuiOleh->karyawan->nama_lengkap ?? 'Admin';
                $this->tanggal_diproses = \Carbon\Carbon::parse($izin->updated_at)->locale('id')->isoFormat('D MMMM Y, HH:mm');
            }
            
            $this->tanggal_diajukan = \Carbon\Carbon::parse($izin->created_at)->locale('id')->isoFormat('dddd, D MMMM Y HH:mm');
        }
    }

    public function closeModal()
    {
        $this->reset([
            'izinId', 'nama_lengkap', 'nip', 'nama_departemen', 'nama_jabatan',
            'tanggal_mulai', 'tanggal_selesai', 'jenis_izin', 'durasi', 'alasan',
            'bukti_foto', 'status', 'disetujui_oleh', 'tanggal_diproses', 'tanggal_diajukan'
        ]);
        $this->dispatch('close-detail-modal');
    }

    public function render()
    {
        return view('livewire.admin.izin.modals.detail');
    }
}
