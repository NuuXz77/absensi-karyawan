<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WajahKaryawan extends Model
{
    protected $table = 'wajah_karyawan';

    protected $fillable = [
        'karyawan_id',
        'foto_path',
        'face_embedding',
    ];

    // relasi ke table karyawans
    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class);
    }
}
