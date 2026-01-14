<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\KaryawanController;
use App\Http\Controllers\Api\JadwalController;
use App\Http\Controllers\Api\CutiIzinController;
use App\Http\Controllers\Api\AbsensiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public routes
Route::prefix('v1')->group(function () {
    // Test endpoint
    Route::get('/ping', function () {
        return response()->json([
            'success' => true,
            'message' => 'pong',
            'timestamp' => now()->toDateTimeString(),
        ]);
    });

    // Auth routes
    Route::post('/login', [AuthController::class, 'login']);
});

// Protected routes
Route::prefix('v1')
    ->middleware('auth:sanctum')
    ->group(function () {
        // Auth routes
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);

        // Karyawan routes
        Route::prefix('karyawan')->group(function () {
            Route::get('/profile', [KaryawanController::class, 'profile']);
            Route::put('/profile', [KaryawanController::class, 'updateProfile']);
        });

        // Jadwal routes
        Route::prefix('jadwal')->group(function () {
            Route::get('/', [JadwalController::class, 'index']);
            Route::get('/today', [JadwalController::class, 'today']);
            Route::get('/shifts', [JadwalController::class, 'shifts']);
        });

        // Cuti routes
        Route::prefix('cuti')->group(function () {
            Route::get('/', [CutiIzinController::class, 'indexCuti']);
            Route::post('/', [CutiIzinController::class, 'storeCuti']);
            Route::get('/saldo', [CutiIzinController::class, 'saldoCuti']);
        });

        // Izin routes
        Route::prefix('izin')->group(function () {
            Route::get('/', [CutiIzinController::class, 'indexIzin']);
            Route::post('/', [CutiIzinController::class, 'storeIzin']);
        });

        // Absensi routes
        Route::prefix('absensi')->group(function () {
            Route::get('/', [AbsensiController::class, 'index']);

            Route::get('/absensi/today-schedule', [AbsensiController::class, 'todaySchedule']);
            Route::get('/today', [AbsensiController::class, 'today']);
            Route::post('/clock-in', [AbsensiController::class, 'clockIn']);
            Route::post('/clock-out', [AbsensiController::class, 'clockOut']);
            Route::get('/locations', [AbsensiController::class, 'locations']);
            Route::get('/statistics', [AbsensiController::class, 'statistics']);
        });
    });
