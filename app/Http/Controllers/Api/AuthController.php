<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Login API
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        try {
            $request->validate([
                'username' => 'required|string',
                'password' => 'required|string',
            ]);

            $user = User::where('username', $request->username)->first();

            if (!$user || !Hash::check($request->password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Username atau password salah'
                ], 401);
            }

            // Check if user role is karyawan
            if ($user->role !== 'karyawan') {
                return response()->json([
                    'success' => false,
                    'message' => 'Hanya karyawan yang dapat login melalui aplikasi mobile'
                ], 403);
            }

            // Check if user is active
            if ($user->status !== 'active') {
                return response()->json([
                    'success' => false,
                    'message' => 'Akun Anda tidak aktif'
                ], 403);
            }

            // Create token
            $token = $user->createToken('api-token')->plainTextToken;

            // Load karyawan data
            $user->load(['karyawan.jabatan', 'karyawan.departemen']);

            return response()->json([
                'success' => true,
                'message' => 'Login berhasil',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'username' => $user->username,
                        'role' => $user->role,
                        'status' => $user->status,
                        'harus_mengganti_password' => $user->harus_mengganti_password,
                    ],
                    'karyawan' => $user->karyawan ? [
                        'id' => $user->karyawan->id,
                        'nip' => $user->karyawan->nip,
                        'nama_lengkap' => $user->karyawan->nama_lengkap,
                        'email' => $user->karyawan->email,
                        'foto_karyawan' => $user->karyawan->foto_karyawan ? asset('storage/' . $user->karyawan->foto_karyawan) : null,
                        'no_telepon' => $user->karyawan->no_telepon,
                        'jenis_kelamin' => $user->karyawan->jenis_kelamin,
                        'jabatan' => $user->karyawan->jabatan ? [
                            'id' => $user->karyawan->jabatan->id,
                            'nama' => $user->karyawan->jabatan->nama_jabatan,
                        ] : null,
                        'departemen' => $user->karyawan->departemen ? [
                            'id' => $user->karyawan->departemen->id,
                            'nama' => $user->karyawan->departemen->nama_departemen,
                        ] : null,
                    ] : null,
                    'token' => $token,
                ]
            ], 200);

        } catch (ValidationException $e) {
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
     * Logout API
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        try {
            // Revoke current token
            $request->user()->currentAccessToken()->delete();

            return response()->json([
                'success' => true,
                'message' => 'Logout berhasil'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get authenticated user info
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function me(Request $request)
    {
        try {
            $user = $request->user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User tidak terautentikasi'
                ], 401);
            }

            // Load relasi dengan eager loading
            $user->load(['karyawan.jabatan', 'karyawan.departemen']);

            $responseData = [
                'success' => true,
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'username' => $user->username,
                        'role' => $user->role,
                        'status' => $user->status,
                    ],
                    'karyawan' => null,
                ]
            ];

            // Check jika karyawan data ada
            if ($user->karyawan) {
                $karyawan = $user->karyawan;
                $responseData['data']['karyawan'] = [
                    'id' => $karyawan->id,
                    'nip' => $karyawan->nip,
                    'nama_lengkap' => $karyawan->nama_lengkap,
                    'email' => $karyawan->email,
                    'foto_karyawan' => $karyawan->foto_karyawan ? asset('storage/' . $karyawan->foto_karyawan) : null,
                    'no_telepon' => $karyawan->no_telepon ?? null,
                    'jenis_kelamin' => $karyawan->jenis_kelamin ?? null,
                    'tanggal_lahir' => $karyawan->tanggal_lahir ?? null,
                    'alamat' => $karyawan->alamat ?? null,
                    'jabatan' => null,
                    'departemen' => null,
                ];

                // Check jabatan
                if ($karyawan->jabatan) {
                    $responseData['data']['karyawan']['jabatan'] = [
                        'id' => $karyawan->jabatan->id,
                        'nama' => $karyawan->jabatan->nama_jabatan,
                    ];
                }

                // Check departemen
                if ($karyawan->departemen) {
                    $responseData['data']['karyawan']['departemen'] = [
                        'id' => $karyawan->departemen->id,
                        'nama' => $karyawan->departemen->nama_departemen,
                    ];
                }
            }

            return response()->json($responseData, 200);

        } catch (\Exception $e) {
            \Log::error('Me API Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}
