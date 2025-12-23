<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lokasi extends Model
{
    protected $table = 'lokasi';
    protected $fillable = ['nama_lokasi', 'latitude', 'longitude', 'radius_meter', 'status'];

    public function absensi()
    {
        return $this->hasMany(Absensi::class);
    }
}
