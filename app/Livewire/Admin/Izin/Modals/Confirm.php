<?php

namespace App\Livewire\Admin\Izin\Modals;

use App\Models\Izin;
use App\Models\SaldoCutiDanIzin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Confirm extends Component
{
    public $izinId;
    public $confirmAction = ''; // 'approve' or 'reject'

    // Data izin
    public $nama_lengkap;
    public $nama_departemen;
    public $tanggal_mulai;
    public $tanggal_selesai;
    public $durasi;
    public $jenis_izin;
    public $alasan;

    public $showSuccess = false;
    public $showError = false;
    public $errorMessage = '';

    protected $listeners = ['open-confirm-modal' => 'openModal'];

    public function openModal($id, $action)
    {
        $this->izinId = $id;
        $this->confirmAction = $action;
        $this->showSuccess = false;
        $this->showError = false;

        // Load data izin
        $izin = Izin::with('karyawan.departemen')->find($id);
        
        if ($izin) {
            $this->nama_lengkap = $izin->karyawan->nama_lengkap ?? '-';
            $this->nama_departemen = $izin->karyawan->departemen->nama_departemen ?? '-';
            $this->tanggal_mulai = \Carbon\Carbon::parse($izin->tanggal_mulai)->locale('id')->isoFormat('D MMM Y');
            $this->tanggal_selesai = \Carbon\Carbon::parse($izin->tanggal_selesai)->locale('id')->isoFormat('D MMM Y');
            $this->durasi = \Carbon\Carbon::parse($izin->tanggal_mulai)->diffInDays(\Carbon\Carbon::parse($izin->tanggal_selesai)) + 1;
            $this->jenis_izin = match($izin->jenis_izin) {
                'sakit' => 'Sakit',
                'keperluan_pribadi' => 'Keperluan Pribadi',
                'dinas' => 'Dinas',
                'lainnya' => 'Lainnya',
                default => ucfirst($izin->jenis_izin)
            };
            $this->alasan = $izin->alasan;
        }
    }

    public function confirmActionSubmit()
    {
        $this->showSuccess = false;
        $this->showError = false;

        $izin = Izin::with('karyawan')->find($this->izinId);
        if (!$izin) {
            return;
        }

        DB::beginTransaction();
        try {
            if ($this->confirmAction === 'approve') {
                // Update status izin
                $izin->update([
                    'status' => 'disetujui',
                    'disetujui_oleh' => Auth::id(),
                ]);

                // Hitung jumlah hari izin
                $tanggalMulai = \Carbon\Carbon::parse($izin->tanggal_mulai);
                $tanggalSelesai = \Carbon\Carbon::parse($izin->tanggal_selesai);
                $jumlahHari = $tanggalMulai->diffInDays($tanggalSelesai) + 1;

                // Update saldo izin
                $tahunIzin = date('Y', strtotime($izin->tanggal_mulai));
                
                $saldoIzin = SaldoCutiDanIzin::where('karyawan_id', $izin->karyawan_id)
                    ->where('tahun', $tahunIzin)
                    ->first();

                if ($saldoIzin) {
                    // Cek apakah sisa izin mencukupi
                    if ($saldoIzin->sisa_izin >= $jumlahHari) {
                        // Kurangi sisa_izin
                        $saldoIzin->sisa_izin -= $jumlahHari;
                        $saldoIzin->save();
                    } else {
                        DB::rollBack();
                        $this->showError = true;
                        $this->errorMessage = 'Saldo izin tidak mencukupi! Sisa: ' . $saldoIzin->sisa_izin . ' hari';
                        return;
                    }
                } else {
                    DB::rollBack();
                    $this->showError = true;
                    $this->errorMessage = 'Saldo izin untuk tahun ' . $tahunIzin . ' tidak ditemukan!';
                    return;
                }

                DB::commit();
                $this->showSuccess = true;
                $this->dispatch('izin-approved');
                $this->dispatch('close-confirm-modal');
                
            } elseif ($this->confirmAction === 'reject') {
                $izin->update([
                    'status' => 'ditolak',
                    'disetujui_oleh' => Auth::id(),
                ]);
                
                DB::commit();
                $this->showSuccess = true;
                $this->dispatch('izin-rejected');
                $this->dispatch('close-confirm-modal');
            }
            
        } catch (\Exception $e) {
            DB::rollBack();
            $this->showError = true;
            $this->errorMessage = 'Terjadi kesalahan: ' . $e->getMessage();
        }
    }

    public function closeModal()
    {
        $this->reset(['izinId', 'confirmAction', 'showError', 'errorMessage', 'nama_lengkap', 'nama_departemen', 'tanggal_mulai', 'tanggal_selesai', 'durasi', 'jenis_izin', 'alasan']);
        $this->dispatch('close-confirm-modal');
    }

    public function render()
    {
        return view('livewire.admin.izin.modals.confirm');
    }
}
