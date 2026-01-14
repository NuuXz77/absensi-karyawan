<?php

namespace App\Livewire\Karyawan\Absen;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\Absensi as AbsensiModel;
use App\Models\JadwalKerja;
use App\Models\WajahKaryawan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

#[Layout('components.layouts.app')]
class AbsensiKeluar extends Component
{
    #[Title('Absen Keluar')]
    
    // Properties
    public $step = 'camera'; // camera, preview, success
    
    // Camera & Photo
    public $photoData = null;
    public $capturedPhoto = null;
    
    // Location
    public $latitude = null;
    public $longitude = null;
    public $isLocationValid = false;
    public $locationDistance = null;
    
    // Face Recognition
    public $isFaceDetected = false;
    public $faceConfidence = 0;
    public $faceMatchResult = null;
    
    // Validation Status
    public $isTimeValid = false;
    public $statusMessage = '';
    
    // Result
    public $absensiResult = null;

    public function mount()
    {
        // Validasi apakah sudah absen masuk dan belum absen keluar
        $karyawan = Auth::user()->karyawan;
        $today = Carbon::today();
        
        $absensiHariIni = AbsensiModel::where('karyawan_id', $karyawan->id)
            ->whereDate('tanggal', $today)
            ->first();
        
        if (!$absensiHariIni || !$absensiHariIni->jam_masuk) {
            session()->flash('error', 'Anda belum melakukan absen masuk');
            return redirect()->route('karyawan.absen.index');
        }
        
        if ($absensiHariIni && $absensiHariIni->jam_pulang) {
            session()->flash('error', 'Anda sudah melakukan absen keluar hari ini');
            return redirect()->route('karyawan.absen.index');
        }
    }

    public function validateLocation($lat, $lng)
    {
        $this->latitude = $lat;
        $this->longitude = $lng;
        
        $karyawan = Auth::user()->karyawan;
        $today = Carbon::today();
        
        // Ambil jadwal hari ini
        $jadwal = JadwalKerja::with('lokasi')
            ->where('karyawan_id', $karyawan->id)
            ->whereDate('tanggal', $today)
            ->first();
        
        if (!$jadwal || !$jadwal->lokasi) {
            $this->isLocationValid = false;
            $this->locationDistance = null;
            return;
        }
        
        // Hitung jarak menggunakan Haversine formula
        $lokasiLat = $jadwal->lokasi->latitude;
        $lokasiLng = $jadwal->lokasi->longitude;
        $radius = $jadwal->lokasi->radius ?? 100;
        
        $earthRadius = 6371000; // dalam meter
        
        $latFrom = deg2rad($lokasiLat);
        $lonFrom = deg2rad($lokasiLng);
        $latTo = deg2rad($lat);
        $lonTo = deg2rad($lng);
        
        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;
        
        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
            cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
        
        $distance = $angle * $earthRadius;
        
        $this->locationDistance = round($distance, 2);
        $this->isLocationValid = $distance <= $radius;
    }

    public function validateTime()
    {
        $karyawan = Auth::user()->karyawan;
        $now = Carbon::now();
        $today = Carbon::today();
        
        $jadwal = JadwalKerja::with('shift')
            ->where('karyawan_id', $karyawan->id)
            ->whereDate('tanggal', $today)
            ->first();
        
        if (!$jadwal || !$jadwal->shift) {
            $this->isTimeValid = false;
            return;
        }
        
        $jamPulang = Carbon::parse($today->format('Y-m-d') . ' ' . $jadwal->shift->jam_pulang);
        $mulaiAbsenPulang = $jamPulang->copy()->subHour();
        
        $this->isTimeValid = $now->greaterThanOrEqualTo($mulaiAbsenPulang);
    }

    public function capturePhoto($photoDataUrl)
    {
        $this->photoData = $photoDataUrl;
        $this->step = 'preview';
    }

    public function retakePhoto()
    {
        $this->photoData = null;
        $this->capturedPhoto = null;
        $this->faceMatchResult = null;
        $this->isFaceDetected = false;
        $this->faceConfidence = 0;
        $this->step = 'camera';
        
        $this->dispatch('restartCamera');
    }

    public function validateFace($embedding)
    {
        try {
            \Log::info('validateFace called', ['embedding_length' => count($embedding)]);
            
            $karyawan = Auth::user()->karyawan;
            
            $wajahKaryawan = WajahKaryawan::where('karyawan_id', $karyawan->id)->first();
            
            if (!$wajahKaryawan) {
                \Log::warning('No face data found for karyawan', ['karyawan_id' => $karyawan->id]);
                $this->isFaceDetected = false;
                $this->faceConfidence = 0;
                return;
            }
            
            $storedEmbedding = json_decode($wajahKaryawan->face_embedding, true);
            
            if (!is_array($storedEmbedding) || count($storedEmbedding) !== count($embedding)) {
                \Log::error('Embedding mismatch', [
                    'stored_count' => is_array($storedEmbedding) ? count($storedEmbedding) : 'not array',
                    'received_count' => count($embedding)
                ]);
                $this->isFaceDetected = false;
                $this->faceConfidence = 0;
                return;
            }
            
            $distance = $this->calculateEuclideanDistance($embedding, $storedEmbedding);
            $threshold = 1.2;
            $confidence = max(0, min(100, (1 - ($distance / $threshold)) * 100));
            
            \Log::info('Face validation result', [
                'distance' => round($distance, 4),
                'threshold' => $threshold,
                'confidence' => round($confidence, 2),
                'match' => $confidence >= 60,
                'status' => $confidence >= 60 ? 'MATCH' : 'NO MATCH'
            ]);
            
            $this->isFaceDetected = true;
            $this->faceConfidence = $confidence;
            $this->faceMatchResult = [
                'match' => $confidence >= 60,
                'confidence' => $confidence,
                'distance' => $distance
            ];
        } catch (\Exception $e) {
            $this->isFaceDetected = false;
            $this->faceConfidence = 0;
            \Log::error('Face validation error: ' . $e->getMessage());
        }
    }

    public function confirmAbsen()
    {
        try {
            $karyawan = Auth::user()->karyawan;
            $now = Carbon::now();
            $today = Carbon::today();
            
            // Validasi final
            if (!$this->isLocationValid) {
                session()->flash('error', 'Lokasi Anda tidak valid');
                return;
            }
            
            if (!$this->isFaceDetected || $this->faceConfidence < 60) {
                session()->flash('error', 'Wajah tidak terdeteksi atau tidak cocok');
                return;
            }
            
            // Ambil jadwal
            $jadwal = JadwalKerja::with(['shift', 'lokasi'])
                ->where('karyawan_id', $karyawan->id)
                ->whereDate('tanggal', $today)
                ->first();
            
            // Simpan foto
            $image = str_replace('data:image/jpeg;base64,', '', $this->photoData);
            $image = str_replace(' ', '+', $image);
            $imageData = base64_decode($image);
            
            $filename = 'absensi/' . $karyawan->id . '_' . $now->format('Y-m-d_His') . '_keluar.jpg';
            Storage::disk('public')->put($filename, $imageData);
            
            // Absen keluar
            $absensi = AbsensiModel::where('karyawan_id', $karyawan->id)
                ->whereDate('tanggal', $today)
                ->first();
            
            if ($absensi) {
                $absensi->update([
                    'jam_pulang' => $now->format('H:i:s'),
                    'lat_keluar' => $this->latitude,
                    'long_keluar' => $this->longitude,
                    'foto_keluar' => $filename,
                ]);
                
                $this->statusMessage = 'Absen keluar berhasil!';
                
                $this->absensiResult = [
                    'status' => $absensi->status,
                    'jam_masuk' => $absensi->jam_masuk,
                    'jam_pulang' => $now->format('H:i:s'),
                    'lokasi' => $jadwal->lokasi->nama_lokasi,
                    'confidence' => $this->faceConfidence,
                ];
                
                $this->step = 'success';
            } else {
                session()->flash('error', 'Data absensi masuk tidak ditemukan');
            }
            
        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
            \Log::error('Absensi keluar error: ' . $e->getMessage());
        }
    }

    private function calculateEuclideanDistance($embedding1, $embedding2)
    {
        $sum = 0;
        for ($i = 0; $i < count($embedding1); $i++) {
            $diff = $embedding1[$i] - $embedding2[$i];
            $sum += $diff * $diff;
        }
        return sqrt($sum);
    }

    public function render()
    {
        $karyawan = Auth::user()->karyawan;
        $today = Carbon::today();
        $now = Carbon::now();
        
        // Ambil jadwal hari ini
        $jadwal = JadwalKerja::with(['shift', 'lokasi'])
            ->where('karyawan_id', $karyawan->id)
            ->whereDate('tanggal', $today)
            ->first();
        
        return view('livewire.karyawan.absen.absensi-keluar', [
            'jadwal' => $jadwal,
            'now' => $now,
            'today' => $today,
            'karyawan' => $karyawan,
        ]);
    }
}

