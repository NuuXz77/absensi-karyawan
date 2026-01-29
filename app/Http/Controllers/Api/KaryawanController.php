<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Karyawan;
use Illuminate\Http\Request;

class KaryawanController extends Controller
{
    /**
     * Get karyawan profile
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function profile(Request $request)
    {
        try {
            $karyawan = $request->user()->karyawan;
            
            if (!$karyawan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data karyawan tidak ditemukan'
                ], 404);
            }

            $karyawan->load(['jabatan', 'departemen']);

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $karyawan->id,
                    'user_id' => $karyawan->user_id,
                    'id_card' => $karyawan->id_card,
                    'nip' => $karyawan->nip,
                    'nama_lengkap' => $karyawan->nama_lengkap,
                    'email' => $karyawan->email,
                    'tanggal_lahir' => $karyawan->tanggal_lahir,
                    'foto_karyawan' => $karyawan->foto_karyawan ? asset('storage/' . $karyawan->foto_karyawan) : null,
                    'no_telepon' => $karyawan->no_telepon,
                    'jenis_kelamin' => $karyawan->jenis_kelamin,
                    'alamat' => $karyawan->alamat,
                    'status' => $karyawan->status,
                    'jabatan' => $karyawan->jabatan ? [
                        'id' => $karyawan->jabatan->id,
                        'nama' => $karyawan->jabatan->nama_jabatan,
                        'deskripsi' => $karyawan->jabatan->deskripsi,
                    ] : null,
                    'departemen' => $karyawan->departemen ? [
                        'id' => $karyawan->departemen->id,
                        'nama' => $karyawan->departemen->nama_departemen,
                        'deskripsi' => $karyawan->departemen->deskripsi,
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
     * Update karyawan profile
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateProfile(Request $request)
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
                'email' => 'nullable|email|unique:karyawan,email,' . $karyawan->id,
                'no_telepon' => 'nullable|string|max:20',
                'alamat' => 'nullable|string',
                'foto_karyawan' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
            ]);

            if ($request->hasFile('foto_karyawan')) {
                // Delete old photo if exists
                if ($karyawan->foto_karyawan && \Storage::exists('public/' . $karyawan->foto_karyawan)) {
                    \Storage::delete('public/' . $karyawan->foto_karyawan);
                }
                
                $path = $request->file('foto_karyawan')->store('karyawan', 'public');
                $validated['foto_karyawan'] = $path;
            }

            $karyawan->update($validated);
            $karyawan->load(['jabatan', 'departemen']);

            return response()->json([
                'success' => true,
                'message' => 'Profile berhasil diupdate',
                'data' => [
                    'id' => $karyawan->id,
                    'nip' => $karyawan->nip,
                    'nama_lengkap' => $karyawan->nama_lengkap,
                    'email' => $karyawan->email,
                    'foto_karyawan' => $karyawan->foto_karyawan ? asset('storage/' . $karyawan->foto_karyawan) : null,
                    'no_telepon' => $karyawan->no_telepon,
                    'alamat' => $karyawan->alamat,
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
}
