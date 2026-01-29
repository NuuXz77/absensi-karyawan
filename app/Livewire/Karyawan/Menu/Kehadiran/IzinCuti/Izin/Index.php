<?php

namespace App\Livewire\Karyawan\Menu\Kehadiran\IzinCuti\Izin;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\Izin;
use App\Models\SaldoCutiDanIzin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

#[Layout('components.layouts.app')]
class Index extends Component
{
    use WithFileUploads;

    #[Title('Pengajuan Izin - Karyawan')]

    public $tanggal_mulai;
    public $tanggal_selesai;
    public $jenis_izin = '';
    public $keterangan;
    public $bukti_foto;
    public $sisaSaldoIzin;
    public $totalIzin;

    public $showSuccess = false;
    public $showError = false;
    public $errorMessage = '';

    public function mount()
    {
        $this->loadSaldoIzin();
    }

    public function loadSaldoIzin()
    {
        $karyawan = Auth::user()->karyawan;
        if ($karyawan) {
            $saldoIzin = SaldoCutiDanIzin::where('karyawan_id', $karyawan->id)
                ->where('tahun', date('Y'))
                ->first();
            
            if ($saldoIzin) {
                $this->sisaSaldoIzin = $saldoIzin->sisa_izin;
                $this->totalIzin = $saldoIzin->total_izin;
            } else {
                // Jika belum ada saldo, create default
                $saldoBaru = SaldoCutiDanIzin::create([
                    'karyawan_id' => $karyawan->id,
                    'tahun' => date('Y'),
                    'total_izin' => 12,
                    'sisa_izin' => 12,
                    'total_cuti' => 12,
                    'sisa_cuti' => 12,
                ]);
                $this->sisaSaldoIzin = 12;
                $this->totalIzin = 12;
            }
        }
    }

    protected function rules()
    {
        return [
            'tanggal_mulai' => 'required|date|after_or_equal:today',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'jenis_izin' => 'required|in:sakit,keperluan_pribadi,dinas,lainnya',
            'keterangan' => 'required|string|min:10|max:1000',
            'bukti_foto' => 'nullable|image|max:2048', // max 2MB
        ];
    }

    protected $messages = [
        'tanggal_mulai.required' => 'Tanggal mulai wajib diisi',
        'tanggal_mulai.after_or_equal' => 'Tanggal mulai tidak boleh kurang dari hari ini',
        'tanggal_selesai.required' => 'Tanggal selesai wajib diisi',
        'tanggal_selesai.after_or_equal' => 'Tanggal selesai tidak boleh kurang dari tanggal mulai',
        'jenis_izin.required' => 'Jenis izin wajib dipilih',
        'jenis_izin.in' => 'Jenis izin tidak valid',
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

            // Hitung jumlah hari izin
            $start = Carbon::parse($this->tanggal_mulai);
            $end = Carbon::parse($this->tanggal_selesai);
            $jumlahHari = $start->diffInDays($end) + 1;

            // Validasi maksimal 2 hari izin per bulan
            $bulanIni = Carbon::parse($this->tanggal_mulai)->format('Y-m');
            $totalIzinDiBulanIni = Izin::where('karyawan_id', $karyawan->id)
                ->where('status', '!=', 'ditolak')
                ->where(function($query) use ($bulanIni) {
                    $query->whereRaw("DATE_FORMAT(tanggal_mulai, '%Y-%m') = ?", [$bulanIni])
                          ->orWhereRaw("DATE_FORMAT(tanggal_selesai, '%Y-%m') = ?", [$bulanIni]);
                })
                ->get()
                ->sum(function($izin) {
                    return Carbon::parse($izin->tanggal_mulai)->diffInDays(Carbon::parse($izin->tanggal_selesai)) + 1;
                });

            if (($totalIzinDiBulanIni + $jumlahHari) > 2) {
                $this->showError = true;
                $this->errorMessage = 'Maksimal izin dalam 1 bulan adalah 2 hari! Anda sudah menggunakan ' . $totalIzinDiBulanIni . ' hari bulan ini.';
                return;
            }

            $buktiPath = null;
            if ($this->bukti_foto) {
                $buktiPath = $this->bukti_foto->store('izin/bukti', 'public');
            }

            Izin::create([
                'karyawan_id' => $karyawan->id,
                'tanggal_mulai' => $this->tanggal_mulai,
                'tanggal_selesai' => $this->tanggal_selesai,
                'jenis_izin' => $this->jenis_izin,
                'keterangan' => $this->keterangan,
                'bukti_foto' => $buktiPath,
                'status' => 'pending',
            ]);

            session()->flash('success', 'Pengajuan izin berhasil dikirim!');
            return $this->redirect(route('karyawan.kehadiran.riwayat-izin'), navigate: true);

        } catch (\Exception $e) {
            $this->showError = true;
            $this->errorMessage = 'Gagal mengirim pengajuan: ' . $e->getMessage();
        }
    }

    public function render()
    {
        return view('livewire.karyawan.menu.kehadiran.izin-cuti.izin.index');
    }
}
