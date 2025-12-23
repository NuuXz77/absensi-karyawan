<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Absensi extends Model
{
    protected $table = "absensi";
    
    protected $fillable = [
        'karyawan_id',
        'lokasi_id',
        'tanggal',
        'waktu_masuk',
        'waktu_keluar',
        'status',
        'keterangan',
    ];

    // relasi ke table karyawans
    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class);
    }

    // relasi ke table lokasis
    public function lokasi()
    {
        return $this->belongsTo(Lokasi::class);
    }
}
