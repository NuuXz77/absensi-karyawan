<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\JadwalKerja;
use App\Models\Shift;
use Illuminate\Http\Request;
use Carbon\Carbon;

class JadwalController extends Controller
{
    /**
     * Get jadwal kerja karyawan
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

            $jadwal = JadwalKerja::where('karyawan_id', $karyawan->id)
                ->whereMonth('tanggal', $bulan)
                ->whereYear('tanggal', $tahun)
                ->with('shift')
                ->orderBy('tanggal', 'asc')
                ->get()
                ->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'tanggal' => Carbon::parse($item->tanggal)->format('Y-m-d'),
                        'hari' => Carbon::parse($item->tanggal)->locale('id')->translatedFormat('l'),
                        'shift' => $item->shift ? [
                            'id' => $item->shift->id,
                            'nama' => $item->shift->nama_shift,
                            'jam_masuk' => $item->shift->jam_masuk,
                            'jam_pulang' => $item->shift->jam_pulang,
                            'toleransi_menit' => $item->shift->toleransi_menit,
                        ] : null,
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $jadwal
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get jadwal hari ini
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
            $jadwal = JadwalKerja::where('karyawan_id', $karyawan->id)
                ->whereRaw('DATE(tanggal) = ?', [$today])
                ->with('shift')
                ->first();

            if (!$jadwal) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada jadwal untuk hari ini'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $jadwal->id,
                    'tanggal' => Carbon::parse($jadwal->tanggal)->format('Y-m-d'),
                    'hari' => Carbon::parse($jadwal->tanggal)->locale('id')->translatedFormat('l'),
                    'shift' => $jadwal->shift ? [
                        'id' => $jadwal->shift->id,
                        'nama' => $jadwal->shift->nama_shift,
                        'jam_masuk' => $jadwal->shift->jam_masuk,
                        'jam_pulang' => $jadwal->shift->jam_pulang,
                        'toleransi_menit' => $jadwal->shift->toleransi_menit,
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
     * Get all shifts
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function shifts(Request $request)
    {
        try {
            $shifts = Shift::all()->map(function ($shift) {
                return [
                    'id' => $shift->id,
                    'nama' => $shift->nama_shift,
                    'jam_masuk' => $shift->jam_masuk,
                    'jam_pulang' => $shift->jam_pulang,
                    'toleransi_menit' => $shift->toleransi_menit,
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $shifts
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}
