<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\Lokasi;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AbsensiController extends Controller
{
    /**
     * Get list absensi karyawan
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $karyawan = $request->user()->karyawan;
            
            if (!$karyawan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data karyawan tidak ditemukan'
                ], 404);
            }

            // Get query parameters
            $bulan = $request->query('bulan', Carbon::now()->month);
            $tahun = $request->query('tahun', Carbon::now()->year);

            $absensi = Absensi::where('karyawan_id', $karyawan->id)
                ->whereMonth('tanggal', $bulan)
                ->whereYear('tanggal', $tahun)
                ->with('lokasi')
                ->orderBy('tanggal', 'desc')
                ->get()
                ->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'tanggal' => $item->tanggal,
                        'jam_masuk' => $item->jam_masuk,
                        'jam_pulang' => $item->jam_pulang,
                        'status' => $item->status,
                        'lokasi' => $item->lokasi ? [
                            'id' => $item->lokasi->id,
                            'nama' => $item->lokasi->nama_lokasi,
                        ] : null,
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $absensi
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get absensi hari ini
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function today(Request $request)
    {
        try {
            $karyawan = $request->user()->karyawan;
            
            if (!$karyawan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data karyawan tidak ditemukan'
                ], 404);
            }

            $today = Carbon::now()->format('Y-m-d');
            $absensi = Absensi::where('karyawan_id', $karyawan->id)
                ->whereRaw('DATE(tanggal) = ?', [$today])
                ->with('lokasi')
                ->first();

            if (!$absensi) {
                return response()->json([
                    'success' => false,
                    'message' => 'Belum ada absensi hari ini',
                    'data' => null
                ], 200);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $absensi->id,
                    'tanggal' => Carbon::parse($absensi->tanggal)->format('Y-m-d'),
                    'jam_masuk' => $absensi->jam_masuk,
                    'jam_pulang' => $absensi->jam_pulang,
                    'status' => $absensi->status,
                    'lokasi' => $absensi->lokasi ? [
                        'id' => $absensi->lokasi->id,
                        'nama' => $absensi->lokasi->nama_lokasi,
                    ] : null,
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Clock in (absen masuk)
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function clockIn(Request $request)
    {
        try {
            $karyawan = $request->user()->karyawan;
            
            if (!$karyawan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data karyawan tidak ditemukan'
                ], 404);
            }

            $validated = $request->validate([
                'lokasi_id' => 'required|exists:lokasi,id',
                'latitude' => 'required|numeric',
                'longitude' => 'required|numeric',
                'foto_masuk' => 'nullable|string', // Base64 image
            ]);

            // Check if already clocked in today
            $today = Carbon::now()->format('Y-m-d');
            $existingAbsensi = Absensi::where('karyawan_id', $karyawan->id)
                ->whereRaw('DATE(tanggal) = ?', [$today])
                ->first();

            if ($existingAbsensi) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda sudah melakukan absen masuk hari ini'
                ], 400);
            }

            // Verify location
            $lokasi = Lokasi::find($validated['lokasi_id']);
            $distance = $this->calculateDistance(
                $validated['latitude'],
                $validated['longitude'],
                $lokasi->latitude,
                $lokasi->longitude
            );

            if ($distance > $lokasi->radius_meter) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda berada di luar radius lokasi absensi'
                ], 400);
            }

            // Create absensi
            $now = Carbon::now();
            $absensi = Absensi::create([
                'karyawan_id' => $karyawan->id,
                'lokasi_id' => $validated['lokasi_id'],
                'tanggal' => $now->format('Y-m-d'),
                'jam_masuk' => $now->format('H:i:s'),
                'lat_masuk' => $validated['latitude'],
                'long_masuk' => $validated['longitude'],
                'foto_masuk' => $validated['foto_masuk'] ?? null,
                'status' => 'tepat_waktu',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Absen masuk berhasil',
                'data' => [
                    'id' => $absensi->id,
                    'tanggal' => $absensi->tanggal,
                    'jam_masuk' => $absensi->jam_masuk,
                    'status' => $absensi->status,
                ]
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Clock out (absen keluar)
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function clockOut(Request $request)
    {
        try {
            $karyawan = $request->user()->karyawan;
            
            if (!$karyawan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data karyawan tidak ditemukan'
                ], 404);
            }

            $validated = $request->validate([
                'latitude' => 'required|numeric',
                'longitude' => 'required|numeric',
                'foto_keluar' => 'nullable|string', // Base64 image
            ]);

            // Check if already clocked in today
            $today = Carbon::now()->format('Y-m-d');
            $absensi = Absensi::where('karyawan_id', $karyawan->id)
                ->whereRaw('DATE(tanggal) = ?', [$today])
                ->first();

            if (!$absensi) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda belum melakukan absen masuk hari ini'
                ], 400);
            }

            if ($absensi->jam_pulang) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda sudah melakukan absen keluar hari ini'
                ], 400);
            }

            // Verify location
            $lokasi = $absensi->lokasi;
            $distance = $this->calculateDistance(
                $validated['latitude'],
                $validated['longitude'],
                $lokasi->latitude,
                $lokasi->longitude
            );

            if ($distance > $lokasi->radius_meter) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda berada di luar radius lokasi absensi'
                ], 400);
            }

            // Update absensi
            $now = Carbon::now();
            $absensi->update([
                'jam_pulang' => $now->format('H:i:s'),
                'lat_keluar' => $validated['latitude'],
                'long_keluar' => $validated['longitude'],
                'foto_keluar' => $validated['foto_keluar'] ?? null,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Absen keluar berhasil',
                'data' => [
                    'id' => $absensi->id,
                    'tanggal' => $absensi->tanggal,
                    'jam_masuk' => $absensi->jam_masuk,
                    'jam_pulang' => $absensi->jam_pulang,
                    'status' => $absensi->status,
                ]
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get available locations
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function locations(Request $request)
    {
        try {
            $lokasi = Lokasi::where('status', 'aktif')->get()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'nama' => $item->nama_lokasi,
                    'latitude' => $item->latitude,
                    'longitude' => $item->longitude,
                    'radius_meter' => $item->radius_meter,
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $lokasi
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Calculate distance between two coordinates (in meters)
     * 
     * @param float $lat1
     * @param float $lon1
     * @param float $lat2
     * @param float $lon2
     * @return float
     */
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000; // in meters

        $lat1 = deg2rad($lat1);
        $lon1 = deg2rad($lon1);
        $lat2 = deg2rad($lat2);
        $lon2 = deg2rad($lon2);

        $latDelta = $lat2 - $lat1;
        $lonDelta = $lon2 - $lon1;

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
            cos($lat1) * cos($lat2) *
            sin($lonDelta / 2) * sin($lonDelta / 2);
        
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    /**
     * Get jadwal kerja hari ini
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function todaySchedule(Request $request)
    {
        try {
            $karyawan = $request->user()->karyawan;
            
            if (!$karyawan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data karyawan tidak ditemukan'
                ], 404);
            }

            // Get jadwal untuk hari ini - use Y-m-d format
            $today = Carbon::now()->format('Y-m-d');
            
            // Cari jadwal kerja karyawan untuk hari ini
            $jadwal = \App\Models\JadwalKerja::where('karyawan_id', $karyawan->id)
                ->whereRaw('DATE(tanggal) = ?', [$today])
                ->with('shift')
                ->first();

            if (!$jadwal || !$jadwal->shift) {
                return response()->json([
                    'success' => true,
                    'message' => 'Tidak ada jadwal hari ini',
                    'data' => null
                ], 200);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $jadwal->id,
                    'tanggal' => Carbon::parse($jadwal->tanggal)->format('Y-m-d'),
                    'jam_masuk' => $jadwal->shift->jam_masuk,
                    'jam_keluar' => $jadwal->shift->jam_pulang,
                    'shift' => [
                        'id' => $jadwal->shift->id,
                        'nama' => $jadwal->shift->nama_shift,
                        'toleransi_menit' => $jadwal->shift->toleransi_menit,
                    ]
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get absensi statistics
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function statistics(Request $request)
    {
        try {
            $karyawan = $request->user()->karyawan;
            
            if (!$karyawan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data karyawan tidak ditemukan'
                ], 404);
            }

            $bulan = $request->query('bulan', Carbon::now()->month);
            $tahun = $request->query('tahun', Carbon::now()->year);

            $totalHadir = Absensi::where('karyawan_id', $karyawan->id)
                ->whereMonth('tanggal', $bulan)
                ->whereYear('tanggal', $tahun)
                ->where('status', 'tepat_waktu')
                ->count();

            $totalTerlambat = Absensi::where('karyawan_id', $karyawan->id)
                ->whereMonth('tanggal', $bulan)
                ->whereYear('tanggal', $tahun)
                ->where('status', 'terlambat')
                ->count();

            $totalAlpha = Absensi::where('karyawan_id', $karyawan->id)
                ->whereMonth('tanggal', $bulan)
                ->whereYear('tanggal', $tahun)
                ->where('status', 'alpha')
                ->count();

            return response()->json([
                'success' => true,
                'data' => [
                    'bulan' => $bulan,
                    'tahun' => $tahun,
                    'total_hadir' => $totalHadir,
                    'total_terlambat' => $totalTerlambat,
                    'total_alpha' => $totalAlpha,
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}
