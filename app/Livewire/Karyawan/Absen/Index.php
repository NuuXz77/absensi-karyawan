<?php

namespace App\Livewire\Karyawan\Absen;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\Absensi;
use App\Models\JadwalKerja;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

#[Layout('components.layouts.app')]
class Index extends Component
{
    #[Title('Absen - Karyawan')]
    
    public $latitude;
    public $longitude;
    public $locationEnabled = false;

    
    public function render()
    {
        $karyawan = Auth::user()->karyawan;
        $now = Carbon::now();
        $today = Carbon::today();
        
        // Ambil jadwal hari ini
        $jadwalHariIni = JadwalKerja::with(['shift', 'karyawan', 'lokasi'])
            ->where('karyawan_id', $karyawan->id)
            ->whereDate('tanggal', $today)
            ->first();
        
        // Ambil absensi hari ini
        $absensiHariIni = Absensi::where('karyawan_id', $karyawan->id)
            ->whereDate('tanggal', $today)
            ->first();
        
        // Validasi waktu absen masuk
        $canAbsenMasuk = false;
        $absenMasukMessage = '';
        $waktuBatasAbsenMasuk = null;
        
        if ($jadwalHariIni && $jadwalHariIni->shift) {
            // Parse jam masuk dari shift
            $jamMasukShift = Carbon::parse($today->format('Y-m-d') . ' ' . $jadwalHariIni->shift->jam_masuk);
            $toleransiMenit = $jadwalHariIni->shift->toleransi_menit ?? 0;
            
            // Waktu batas absen masuk (jam masuk + toleransi)
            $waktuBatasAbsenMasuk = $jamMasukShift->copy()->addMinutes($toleransiMenit);
            
            // Untuk shift malam yang melewati tengah malam
            // Jika jam masuk > 12:00 (malam), batas absen bisa sampai besok
            if ($jamMasukShift->hour >= 18) {
                // Shift malam/sore
                // Boleh absen mulai 2 jam sebelum shift sampai jam masuk + toleransi
                $waktuMulaiAbsen = $jamMasukShift->copy()->subHours(2);
                
                // Jika sekarang sudah lewat tengah malam tapi masih dalam window absen
                if ($now->format('H:i') < '06:00') {
                    // Masih dalam window shift malam kemarin
                    $jadwalKemarin = JadwalKerja::with(['shift'])
                        ->where('karyawan_id', $karyawan->id)
                        ->whereDate('tanggal', $today->copy()->subDay())
                        ->first();
                    
                    if ($jadwalKemarin && $jadwalKemarin->shift) {
                        $jamMasukKemarin = Carbon::parse($today->copy()->subDay()->format('Y-m-d') . ' ' . $jadwalKemarin->shift->jam_masuk);
                        if ($jamMasukKemarin->hour >= 18) {
                            $waktuBatasKemarin = $jamMasukKemarin->copy()->addMinutes($jadwalKemarin->shift->toleransi_menit ?? 0);
                            
                            // Cek apakah masih dalam batas waktu shift kemarin
                            if ($now->lessThanOrEqualTo($waktuBatasKemarin->addDay())) {
                                $canAbsenMasuk = false;
                                $absenMasukMessage = 'Waktu absen untuk shift kemarin sudah terlewat (Batas: ' . $waktuBatasKemarin->format('H:i') . ')';
                            }
                        }
                    }
                } else {
                    // Belum tengah malam, cek apakah sudah boleh absen
                    if ($now->greaterThanOrEqualTo($waktuMulaiAbsen) && $now->lessThanOrEqualTo($waktuBatasAbsenMasuk)) {
                        $canAbsenMasuk = true;
                    } elseif ($now->greaterThan($waktuBatasAbsenMasuk)) {
                        $canAbsenMasuk = false;
                        $absenMasukMessage = 'Waktu absen masuk sudah terlewat (Batas: ' . $waktuBatasAbsenMasuk->format('H:i') . ')';
                    } else {
                        $canAbsenMasuk = false;
                        $absenMasukMessage = 'Belum saatnya absen (Mulai: ' . $waktuMulaiAbsen->format('H:i') . ')';
                    }
                }
            } else {
                // Shift pagi/siang
                // Boleh absen mulai 2 jam sebelum shift sampai jam masuk + toleransi
                $waktuMulaiAbsen = $jamMasukShift->copy()->subHours(2);
                
                if ($now->greaterThanOrEqualTo($waktuMulaiAbsen) && $now->lessThanOrEqualTo($waktuBatasAbsenMasuk)) {
                    $canAbsenMasuk = true;
                } elseif ($now->greaterThan($waktuBatasAbsenMasuk)) {
                    $canAbsenMasuk = false;
                    $absenMasukMessage = 'Waktu absen masuk sudah terlewat (Batas: ' . $waktuBatasAbsenMasuk->format('H:i') . ')';
                } else {
                    $canAbsenMasuk = false;
                    $absenMasukMessage = 'Belum saatnya absen (Mulai: ' . $waktuMulaiAbsen->format('H:i') . ')';
                }
            }
        } else {
            $absenMasukMessage = 'Tidak ada jadwal shift untuk hari ini';
        }
        
        // Jika sudah absen masuk, tidak bisa absen masuk lagi
        if ($absensiHariIni && $absensiHariIni->jam_masuk) {
            $canAbsenMasuk = false;
        }
        
        // Validasi waktu absen pulang
        $canAbsenPulang = false;
        $absenPulangMessage = '';
        
        if ($jadwalHariIni && $jadwalHariIni->shift && $absensiHariIni && $absensiHariIni->jam_masuk) {
            $jamPulangShift = Carbon::parse($today->format('Y-m-d') . ' ' . $jadwalHariIni->shift->jam_pulang);
            
            // Untuk shift yang jam pulangnya besok (melewati tengah malam)
            if ($jamPulangShift->lessThan($jamMasukShift)) {
                $jamPulangShift->addDay();
            }
            
            // Boleh absen pulang 1 jam sebelum jam pulang
            $waktuMulaiAbsenPulang = $jamPulangShift->copy()->subHour();
            
            if ($now->greaterThanOrEqualTo($waktuMulaiAbsenPulang)) {
                $canAbsenPulang = true;
            } else {
                $absenPulangMessage = 'Belum saatnya absen pulang (Mulai: ' . $waktuMulaiAbsenPulang->format('H:i') . ')';
            }
        }
        
        // Jika sudah absen pulang, tidak bisa absen pulang lagi
        if ($absensiHariIni && $absensiHariIni->jam_pulang) {
            $canAbsenPulang = false;
        }
        
        // Ambil 5 absensi terbaru
        $riwayatAbsensi = Absensi::with(['lokasi'])
            ->where('karyawan_id', $karyawan->id)
            ->orderBy('tanggal', 'desc')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
        
        // Data lokasi kantor untuk map
        $lokasiKantor = null;
        if ($jadwalHariIni && $jadwalHariIni->lokasi) {
            $lokasiKantor = [
                'nama' => $jadwalHariIni->lokasi->nama_lokasi,
                'latitude' => $jadwalHariIni->lokasi->latitude,
                'longitude' => $jadwalHariIni->lokasi->longitude,
                'radius' => $jadwalHariIni->lokasi->radius_meter,
            ];
        }
        
        return view('livewire.karyawan.absen.index', [
            'jadwalHariIni' => $jadwalHariIni,
            'absensiHariIni' => $absensiHariIni,
            'riwayatAbsensi' => $riwayatAbsensi,
            'canAbsenMasuk' => $canAbsenMasuk,
            'absenMasukMessage' => $absenMasukMessage,
            'canAbsenPulang' => $canAbsenPulang,
            'absenPulangMessage' => $absenPulangMessage,
            'waktuBatasAbsenMasuk' => $waktuBatasAbsenMasuk,
            'now' => $now,
            'lokasiKantor' => $lokasiKantor,
        ]);
    }
}
