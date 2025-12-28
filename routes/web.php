<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


Require __DIR__.'/auth.php';
Require __DIR__.'/karyawan.php';