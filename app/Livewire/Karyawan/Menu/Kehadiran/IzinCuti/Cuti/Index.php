<?php

namespace App\Livewire\Karyawan\Menu\Kehadiran\IzinCuti\Cuti;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\Cuti;
use App\Models\SaldoCutiDanIzin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

#[Layout('components.layouts.app')]
class Index extends Component
{
    use WithFileUploads;

    #[Title('Pengajuan Cuti - Karyawan')]

    public $tanggal_mulai;
    public $tanggal_selesai;
    public $jumlah_hari = 0;
    public $jenis_cuti = '';
    public $keterangan;
    public $bukti_foto;
    public $sisaSaldoCuti;
    public $totalCuti;

    public $showSuccess = false;
    public $showError = false;
    public $errorMessage = '';

    public function mount()
    {
        $this->loadSaldoCuti();
    }

    public function loadSaldoCuti()
    {
        $karyawan = Auth::user()->karyawan;
        if ($karyawan) {
            $saldoCuti = SaldoCutiDanIzin::where('karyawan_id', $karyawan->id)
                ->where('tahun', date('Y'))
                ->first();
            
            if ($saldoCuti) {
                $this->sisaSaldoCuti = $saldoCuti->sisa_cuti;
                $this->totalCuti = $saldoCuti->total_cuti;
            } else {
                // Jika belum ada saldo, create default (biasanya 12 hari per tahun)
                $saldoBaru = SaldoCutiDanIzin::create([
                    'karyawan_id' => $karyawan->id,
                    'tahun' => date('Y'),
                    'total_izin' => 12, // default 12 izin per tahun
                    'sisa_izin' => 12,
                    'total_cuti' => 12, // default 12 cuti per tahun
                    'sisa_cuti' => 12,
                ]);
                $this->sisaSaldoCuti = 12;
                $this->totalCuti = 12;
            }
        }
    }

    public function updatedTanggalMulai()
    {
        $this->calculateJumlahHari();
    }

    public function updatedTanggalSelesai()
    {
        $this->calculateJumlahHari();
    }

    public function calculateJumlahHari()
    {
        if ($this->tanggal_mulai && $this->tanggal_selesai) {
            try {
                $start = Carbon::parse($this->tanggal_mulai);
                $end = Carbon::parse($this->tanggal_selesai);
                
                // Hitung selisih hari + 1 (termasuk hari pertama)
                $this->jumlah_hari = $start->diffInDays($end) + 1;
            } catch (\Exception $e) {
                $this->jumlah_hari = 0;
            }
        } else {
            $this->jumlah_hari = 0;
        }
    }

    protected function rules()
    {
        return [
            'tanggal_mulai' => 'required|date|after_or_equal:today',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'jenis_cuti' => 'required|in:tahunan,khusus',
            'keterangan' => 'required|string|min:10|max:1000',
            'bukti_foto' => 'nullable|image|max:2048', // max 2MB
        ];
    }

    protected $messages = [
        'tanggal_mulai.required' => 'Tanggal mulai wajib diisi',
        'tanggal_mulai.after_or_equal' => 'Tanggal mulai tidak boleh kurang dari hari ini',
        'tanggal_selesai.required' => 'Tanggal selesai wajib diisi',
        'tanggal_selesai.after_or_equal' => 'Tanggal selesai tidak boleh kurang dari tanggal mulai',
        'jenis_cuti.required' => 'Jenis cuti wajib dipilih',
        'jenis_cuti.in' => 'Jenis cuti tidak valid',
        'keterangan.required' => 'Keterangan wajib diisi',
        'keterangan.min' => 'Keterangan minimal 10 karakter',
        'keterangan.max' => 'Keterangan maksimal 1000 karakter',
        'bukti_foto.image' => 'File harus berupa gambar',
        'bukti_foto.max' => 'Ukuran file maksimal 2MB',
    ];

    public function save()
    {
        $this->validate();

        try {
            $karyawan = Auth::user()->karyawan;

            if (!$karyawan) {
                $this->showError = true;
                $this->errorMessage = 'Data karyawan tidak ditemukan!';
                return;
            }

            // Validasi maksimal 2 hari cuti per bulan
            $bulanIni = Carbon::parse($this->tanggal_mulai)->format('Y-m');
            $totalCutiDiBulanIni = Cuti::where('karyawan_id', $karyawan->id)
                ->where('status', '!=', 'ditolak')
                ->where(function($query) use ($bulanIni) {
                    $query->whereRaw("DATE_FORMAT(tanggal_mulai, '%Y-%m') = ?", [$bulanIni])
                          ->orWhereRaw("DATE_FORMAT(tanggal_selesai, '%Y-%m') = ?", [$bulanIni]);
                })
                ->sum('jumlah_hari');

            if (($totalCutiDiBulanIni + $this->jumlah_hari) > 2) {
                $this->showError = true;
                $this->errorMessage = 'Maksimal cuti dalam 1 bulan adalah 2 hari! Anda sudah menggunakan ' . $totalCutiDiBulanIni . ' hari bulan ini.';
                return;
            }

            // Validasi saldo cuti untuk cuti tahunan
            if ($this->jenis_cuti === 'tahunan') {
                if ($this->sisaSaldoCuti < $this->jumlah_hari) {
                    $this->showError = true;
                    $this->errorMessage = 'Saldo cuti tidak mencukupi! Sisa: ' . $this->sisaSaldoCuti . ' hari';
                    return;
                }
            }

            $buktiPath = null;
            if ($this->bukti_foto) {
                $buktiPath = $this->bukti_foto->store('cuti/bukti', 'public');
            }

            Cuti::create([
                'karyawan_id' => $karyawan->id,
                'tanggal_mulai' => $this->tanggal_mulai,
                'tanggal_selesai' => $this->tanggal_selesai,
                'jumlah_hari' => $this->jumlah_hari,
                'jenis_cuti' => $this->jenis_cuti,
                'keterangan' => $this->keterangan,
                'bukti_foto' => $buktiPath,
                'status' => 'pending',
            ]);

            session()->flash('success', 'Pengajuan cuti berhasil dikirim!');
            return $this->redirect(route('karyawan.kehadiran.riwayat-cuti'), navigate: true);

        } catch (\Exception $e) {
            $this->showError = true;
            $this->errorMessage = 'Gagal mengirim pengajuan: ' . $e->getMessage();
        }
    }

    public function render()
    {
        return view('livewire.karyawan.menu.kehadiran.izin-cuti.cuti.index');
    }
}
