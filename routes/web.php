<?php

use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });
Route::get('/', function () {
    return redirect()->route('login');
});

Require __DIR__.'/auth.php';
Require __DIR__.'/api.php';
Require __DIR__.'/karyawan.php';