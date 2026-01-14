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
        'jam_masuk',
        'jam_pulang',
        'lat_masuk',
        'long_masuk',
        'lat_keluar',
        'long_keluar',
        'foto_masuk',
        'foto_keluar',
        'status',
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
