<?php

namespace App\Livewire\Karyawan\Dashboard;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\Absensi;
use App\Models\JadwalKerja;
use App\Models\Izin;
use App\Models\Cuti;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

#[Layout('components.layouts.app')]
class Index extends Component
{
    #[Title('Dashboard')]
    
    public function render()
    {
        $karyawan = Auth::user()->karyawan;
        $today = Carbon::today();
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();
        
        // Ambil jadwal hari ini
        $jadwalHariIni = JadwalKerja::with(['shift', 'lokasi'])
            ->where('karyawan_id', $karyawan->id)
            ->whereDate('tanggal', $today)
            ->first();
        
        // Ambil absensi hari ini
        $absensiHariIni = Absensi::where('karyawan_id', $karyawan->id)
            ->whereDate('tanggal', $today)
            ->first();
        
        // Hitung keterlambatan bulan ini
        $keterlambatanBulanIni = Absensi::where('karyawan_id', $karyawan->id)
            ->whereMonth('tanggal', Carbon::now()->month)
            ->whereYear('tanggal', Carbon::now()->year)
            ->where('status', 'terlambat')
            ->get();
        
        $totalKeterlambatan = $keterlambatanBulanIni->count();
        $totalMenitTerlambat = 0;
        
        foreach ($keterlambatanBulanIni as $absensi) {
            if ($absensi->jam_masuk) {
                $jadwal = JadwalKerja::with('shift')
                    ->where('karyawan_id', $karyawan->id)
                    ->whereDate('tanggal', $absensi->tanggal)
                    ->first();
                    
                if ($jadwal && $jadwal->shift) {
                    $jamMasukSeharusnya = Carbon::parse($absensi->tanggal . ' ' . $jadwal->shift->jam_masuk);
                    $jamMasukAktual = Carbon::parse($absensi->tanggal . ' ' . $absensi->jam_masuk);
                    
                    if ($jamMasukAktual->gt($jamMasukSeharusnya)) {
                        $totalMenitTerlambat += $jamMasukAktual->diffInMinutes($jamMasukSeharusnya);
                    }
                }
            }
        }
        
        // Ringkasan absensi bulan ini
        $absensiHadir = Absensi::where('karyawan_id', $karyawan->id)
            ->whereMonth('tanggal', Carbon::now()->month)
            ->whereYear('tanggal', Carbon::now()->year)
            ->whereIn('status', ['hadir', 'terlambat'])
            ->count();
        
        $izinBulanIni = Izin::where('karyawan_id', $karyawan->id)
            ->where('status', 'disetujui')
            ->where(function($query) use ($startOfMonth, $endOfMonth) {
                $query->whereBetween('tanggal_mulai', [$startOfMonth, $endOfMonth])
                      ->orWhereBetween('tanggal_selesai', [$startOfMonth, $endOfMonth])
                      ->orWhere(function($q) use ($startOfMonth, $endOfMonth) {
                          $q->where('tanggal_mulai', '<=', $startOfMonth)
                            ->where('tanggal_selesai', '>=', $endOfMonth);
                      });
            })
            ->get()
            ->sum(function($izin) use ($startOfMonth, $endOfMonth) {
                $start = Carbon::parse($izin->tanggal_mulai)->max($startOfMonth);
                $end = Carbon::parse($izin->tanggal_selesai)->min($endOfMonth);
                return $start->diffInDays($end) + 1;
            });
        
        $cutiBulanIni = Cuti::where('karyawan_id', $karyawan->id)
            ->where('status', 'disetujui')
            ->where(function($query) use ($startOfMonth, $endOfMonth) {
                $query->whereBetween('tanggal_mulai', [$startOfMonth, $endOfMonth])
                      ->orWhereBetween('tanggal_selesai', [$startOfMonth, $endOfMonth])
                      ->orWhere(function($q) use ($startOfMonth, $endOfMonth) {
                          $q->where('tanggal_mulai', '<=', $startOfMonth)
                            ->where('tanggal_selesai', '>=', $endOfMonth);
                      });
            })
            ->get()
            ->sum(function($cuti) use ($startOfMonth, $endOfMonth) {
                $start = Carbon::parse($cuti->tanggal_mulai)->max($startOfMonth);
                $end = Carbon::parse($cuti->tanggal_selesai)->min($endOfMonth);
                return $start->diffInDays($end) + 1;
            });
        
        // Hitung alpha (hari kerja - hadir - izin - cuti)
        $hariKerjaBulanIni = JadwalKerja::where('karyawan_id', $karyawan->id)
            ->whereMonth('tanggal', Carbon::now()->month)
            ->whereYear('tanggal', Carbon::now()->year)
            ->count();
        
        $alphaBulanIni = max(0, $hariKerjaBulanIni - $absensiHadir - $izinBulanIni - $cutiBulanIni);
        
        // Ambil notifikasi (izin/cuti yang baru disetujui/ditolak dalam 7 hari terakhir)
        $notifikasiIzin = Izin::where('karyawan_id', $karyawan->id)
            ->whereIn('status', ['disetujui', 'ditolak'])
            ->where('updated_at', '>=', Carbon::now()->subDays(7))
            ->orderBy('updated_at', 'desc')
            ->take(3)
            ->get();
        
        $notifikasiCuti = Cuti::where('karyawan_id', $karyawan->id)
            ->whereIn('status', ['disetujui', 'ditolak'])
            ->where('updated_at', '>=', Carbon::now()->subDays(7))
            ->orderBy('updated_at', 'desc')
            ->take(3)
            ->get();
        
        // Cek absensi kemarin (lupa absen pulang)
        $absensiKemarin = Absensi::where('karyawan_id', $karyawan->id)
            ->whereDate('tanggal', Carbon::yesterday())
            ->first();
        
        return view('livewire.karyawan.dashboard.index', [
            'jadwalHariIni' => $jadwalHariIni,
            'absensiHariIni' => $absensiHariIni,
            'totalKeterlambatan' => $totalKeterlambatan,
            'totalMenitTerlambat' => $totalMenitTerlambat,
            'absensiHadir' => $absensiHadir,
            'izinBulanIni' => $izinBulanIni,
            'cutiBulanIni' => $cutiBulanIni,
            'alphaBulanIni' => $alphaBulanIni,
            'notifikasiIzin' => $notifikasiIzin,
            'notifikasiCuti' => $notifikasiCuti,
            'absensiKemarin' => $absensiKemarin,
        ]);
    }
}
