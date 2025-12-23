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

    // Lokasi Routes
    Route::get('/admin/lokasi', \App\Livewire\Admin\Lokasi\Index::class)->name('admin.lokasi.index');

    // Shift Routes
    Route::get('/admin/shift', \App\Livewire\Admin\Shift\Index::class)->name('admin.shift.index');

    // Jadwal Routes
    Route::get('/admin/jadwal', \App\Livewire\Admin\Jadwal\Index::class)->name('admin.jadwal.index');

    // Absensi Routes
    Route::get('/admin/absensi', \App\Livewire\Admin\Absensi\Index::class)->name('admin.absensi.index');

    // Izin Routes
    Route::get('/admin/izin', \App\Livewire\Admin\Izin\Index::class)->name('admin.izin.index');

    // Cuti Routes
    Route::get('/admin/cuti', \App\Livewire\Admin\Cuti\Index::class)->name('admin.cuti.index');

    // Departemen Routes
    Route::get('/admin/departemen', \App\Livewire\Admin\Departemen\Index::class)->name('admin.departemen.index');

    // Jabatan Routes
    Route::get('/admin/jabatan', \App\Livewire\Admin\Jabatan\Index::class)->name('admin.jabatan.index');
});

Route::middleware('auth', 'role:karyawan')->group(function () {
    Route::get('/dashboard', \App\Livewire\Karyawan\Dashboard\Index::class)->name('karyawan.dashboard.index');
    // Tambahkan route karyawan lainnya di sini tanpa prefix /admin/
});