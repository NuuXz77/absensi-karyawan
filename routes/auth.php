<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Livewire\Auth\Login;

Route::middleware('guest')->group(function () {
    Route::get('/login', Login::class)->name('login');
});

Route::middleware('auth', 'role:admin')->group(function () {
    Route::get('/admin/dashboard', \App\Livewire\Admin\Dashboard\Index::class)->name('admin.dashboard.index');

    // Absensi Routes
    Route::get('/admin/karyawan', \App\Livewire\Admin\Karyawan\Index::class)->name('admin.karyawan.index');
    Route::get('/admin/karyawan/create', \App\Livewire\Admin\Karyawan\Create::class)->name('admin.karyawan.create');
    Route::get('/admin/karyawan/{id}/detail', \App\Livewire\Admin\Karyawan\Detail::class)->name('admin.karyawan.detail');
    Route::get('/admin/karyawan/{id}/edit', \App\Livewire\Admin\Karyawan\Edit::class)->name('admin.karyawan.edit');

    // Wajah Karyawan Routes
    Route::get('/admin/wajah-karyawan', \App\Livewire\Admin\Wajah\Index::class)->name('admin.wajah-karyawan.index');

    // Lokasi Routes
    Route::get('/admin/lokasi', \App\Livewire\Admin\Lokasi\Index::class)->name('admin.lokasi.index');

    // Shift Routes
    Route::get('/admin/shift', \App\Livewire\Admin\Shift\Index::class)->name('admin.shift.index');

    // Jadwal Routes
    Route::get('/admin/jadwal', \App\Livewire\Admin\Jadwal\Index::class)->name('admin.jadwal.index');
    Route::get('/admin/jadwal/detail', \App\Livewire\Admin\Jadwal\Detail::class)->name('admin.jadwal.detail');

    // Absensi Routes
    Route::get('/admin/absensi', \App\Livewire\Admin\Absensi\Index::class)->name('admin.absensi.index');
    Route::get('/admin/absensi/{id}/detail', \App\Livewire\Admin\Absensi\Detail::class)->name('admin.absensi.detail');

    // Izin Routes
    Route::get('/admin/izin', \App\Livewire\Admin\Izin\Index::class)->name('admin.izin.index');

    // Cuti Routes
    Route::get('/admin/cuti', \App\Livewire\Admin\Cuti\Index::class)->name('admin.cuti.index');

    // Departemen Routes
    Route::get('/admin/departemen', \App\Livewire\Admin\Departemen\Index::class)->name('admin.departemen.index');

    // Jabatan Routes
    Route::get('/admin/jabatan', \App\Livewire\Admin\Jabatan\Index::class)->name('admin.jabatan.index');

    // Saldo Cuti & Izin Routes
    Route::get('/admin/saldo', \App\Livewire\Admin\Saldo\Index::class)->name('admin.saldo.index');

    // Laporan Routes
    Route::get('/admin/laporan', \App\Livewire\Admin\Laporan\Index::class)->name('admin.laporan.index');
});

Route::middleware('auth', 'role:karyawan')->group(function () {
    // Karyawan Dashboard
    Route::get('/dashboard', \App\Livewire\Karyawan\Dashboard\Index::class)->name('karyawan.dashboard.index');

    // Karyawan Absensi
    Route::get('/absen', \App\Livewire\Karyawan\Absen\Index::class)->name('karyawan.absen.index');
    Route::get('/absen/absensi', \App\Livewire\Karyawan\Absen\Absensi::class)->name('karyawan.absen.absensi');

    // Karyawan Jadwal
    Route::get('/jadwal', \App\Livewire\Karyawan\Jadwal\Index::class)->name('karyawan.jadwal.index');

    // Karyawan Menu
    Route::get('/menu', \App\Livewire\Karyawan\Menu\Index::class)->name('karyawan.menu.index');
    Route::get('/menu/kehadiran/riwayat-absensi', \App\Livewire\Karyawan\Menu\Kehadiran\Absensi\RiwayatAbsensi::class)->name('karyawan.kehadiran.riwayat-absensi');
    Route::get('/menu/kehadiran/riwayat-absensi/{id}', \App\Livewire\Karyawan\Menu\Kehadiran\Absensi\Detail\RiwayatAbsensi::class)->name('karyawan.kehadiran.riwayat-absensi.detail');
    Route::get('/menu/kehadiran/riwayat-cuti', \App\Livewire\Karyawan\Menu\Kehadiran\RiwayatCuti\Index::class)->name('karyawan.kehadiran.riwayat-cuti');
    Route::get('/menu/kehadiran/riwayat-izin', \App\Livewire\Karyawan\Menu\Kehadiran\RiwayatIzin\Index::class)->name('karyawan.kehadiran.riwayat-izin');
    // Karyawan Izin & Cuti
    Route::get('/menu/izin/create', \App\Livewire\Karyawan\Menu\Kehadiran\IzinCuti\Izin\Index::class)->name('karyawan.izin.create');
    Route::get('/menu/cuti/create', \App\Livewire\Karyawan\Menu\Kehadiran\IzinCuti\Cuti\Index::class)->name('karyawan.cuti.create');

    // Karyawan Profile
    Route::get('/profile', \App\Livewire\Karyawan\Profile\Index::class)->name('karyawan.profile.index');
});
