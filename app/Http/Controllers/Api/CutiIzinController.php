<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cuti;
use App\Models\Izin;
use App\Models\SaldoCuti;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CutiIzinController extends Controller
{
    /**
     * Get list cuti karyawan
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function indexCuti(Request $request)
    {
        try {
            $karyawan = $request->user()->karyawan;
            
            if (!$karyawan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data karyawan tidak ditemukan'
                ], 404);
            }

            $status = $request->query('status'); // pending, disetujui, ditolak

            $query = Cuti::where('karyawan_id', $karyawan->id)
                ->with('disetujuiOleh.karyawan');

            if ($status) {
                $query->where('status', $status);
            }

            $cuti = $query->orderBy('created_at', 'desc')->get()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'tanggal_mulai' => $item->tanggal_mulai,
                    'tanggal_selesai' => $item->tanggal_selesai,
                    'jumlah_hari' => $item->jumlah_hari,
                    'jenis_cuti' => $item->jenis_cuti,
                    'alasan' => $item->alasan,
                    'status' => $item->status,
                    'keterangan_ditolak' => $item->keterangan_ditolak,
                    'disetujui_oleh' => $item->disetujuiOleh && $item->disetujuiOleh->karyawan ? [
                        'nama' => $item->disetujuiOleh->karyawan->nama_lengkap,
                    ] : null,
                    'tanggal_disetujui' => $item->tanggal_disetujui,
                    'created_at' => $item->created_at->format('Y-m-d H:i:s'),
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $cuti
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Submit pengajuan cuti
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeCuti(Request $request)
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
                'tanggal_mulai' => 'required|date',
                'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
                'jenis_cuti' => 'required|in:tahunan,sakit,darurat,lainnya',
                'alasan' => 'required|string',
            ]);

            // Calculate jumlah hari
            $tanggalMulai = Carbon::parse($validated['tanggal_mulai']);
            $tanggalSelesai = Carbon::parse($validated['tanggal_selesai']);
            $jumlahHari = $tanggalMulai->diffInDays($tanggalSelesai) + 1;

            // Check saldo cuti if jenis_cuti is tahunan
            if ($validated['jenis_cuti'] === 'tahunan') {
                $saldo = SaldoCuti::where('karyawan_id', $karyawan->id)
                    ->where('tahun', Carbon::now()->year)
                    ->first();

                if (!$saldo || $saldo->sisa_cuti < $jumlahHari) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Saldo cuti tidak mencukupi'
                    ], 400);
                }
            }

            $cuti = Cuti::create([
                'karyawan_id' => $karyawan->id,
                'tanggal_mulai' => $validated['tanggal_mulai'],
                'tanggal_selesai' => $validated['tanggal_selesai'],
                'jumlah_hari' => $jumlahHari,
                'jenis_cuti' => $validated['jenis_cuti'],
                'alasan' => $validated['alasan'],
                'status' => 'pending',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Pengajuan cuti berhasil dikirim',
                'data' => [
                    'id' => $cuti->id,
                    'tanggal_mulai' => $cuti->tanggal_mulai,
                    'tanggal_selesai' => $cuti->tanggal_selesai,
                    'jumlah_hari' => $cuti->jumlah_hari,
                    'jenis_cuti' => $cuti->jenis_cuti,
                    'status' => $cuti->status,
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
     * Get saldo cuti karyawan
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function saldoCuti(Request $request)
    {
        try {
            $karyawan = $request->user()->karyawan;
            
            if (!$karyawan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data karyawan tidak ditemukan'
                ], 404);
            }

            $tahun = $request->query('tahun', Carbon::now()->year);

            $saldo = SaldoCuti::where('karyawan_id', $karyawan->id)
                ->where('tahun', $tahun)
                ->first();

            if (!$saldo) {
                // Create default saldo if not exists
                $saldo = SaldoCuti::create([
                    'karyawan_id' => $karyawan->id,
                    'tahun' => $tahun,
                    'jatah_cuti' => 12, // default 12 hari
                    'cuti_terpakai' => 0,
                    'sisa_cuti' => 12,
                ]);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'tahun' => $saldo->tahun,
                    'jatah_cuti' => $saldo->jatah_cuti,
                    'cuti_terpakai' => $saldo->cuti_terpakai,
                    'sisa_cuti' => $saldo->sisa_cuti,
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
     * Get list izin karyawan
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function indexIzin(Request $request)
    {
        try {
            $karyawan = $request->user()->karyawan;
            
            if (!$karyawan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data karyawan tidak ditemukan'
                ], 404);
            }

            $status = $request->query('status'); // pending, disetujui, ditolak

            $query = Izin::where('karyawan_id', $karyawan->id)
                ->with('disetujuiOleh.karyawan');

            if ($status) {
                $query->where('status', $status);
            }

            $izin = $query->orderBy('created_at', 'desc')->get()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'tanggal' => $item->tanggal,
                    'jenis_izin' => $item->jenis_izin,
                    'keterangan' => $item->keterangan,
                    'dokumen' => $item->dokumen ? asset('storage/' . $item->dokumen) : null,
                    'status' => $item->status,
                    'keterangan_ditolak' => $item->keterangan_ditolak,
                    'disetujui_oleh' => $item->disetujuiOleh && $item->disetujuiOleh->karyawan ? [
                        'nama' => $item->disetujuiOleh->karyawan->nama_lengkap,
                    ] : null,
                    'tanggal_disetujui' => $item->tanggal_disetujui,
                    'created_at' => $item->created_at->format('Y-m-d H:i:s'),
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $izin
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Submit pengajuan izin
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeIzin(Request $request)
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
                'tanggal' => 'required|date',
                'jenis_izin' => 'required|in:sakit,keperluan_pribadi,dinas_luar,lainnya',
                'keterangan' => 'required|string',
                'dokumen' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            ]);

            $data = [
                'karyawan_id' => $karyawan->id,
                'tanggal' => $validated['tanggal'],
                'jenis_izin' => $validated['jenis_izin'],
                'keterangan' => $validated['keterangan'],
                'status' => 'pending',
            ];

            if ($request->hasFile('dokumen')) {
                $path = $request->file('dokumen')->store('izin', 'public');
                $data['dokumen'] = $path;
            }

            $izin = Izin::create($data);

            return response()->json([
                'success' => true,
                'message' => 'Pengajuan izin berhasil dikirim',
                'data' => [
                    'id' => $izin->id,
                    'tanggal' => $izin->tanggal,
                    'jenis_izin' => $izin->jenis_izin,
                    'status' => $izin->status,
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
}
